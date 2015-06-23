<?php
add_action('admin_menu', 'register_my_custom_submenu_page');

function register_my_custom_submenu_page() {
    add_submenu_page( 'options-general.php', 'Compliments', 'Compliments', 'manage_options', 'edit-tags.php?taxonomy=compliment' );
}
// hook into the init action and call create_book_taxonomies when it fires
add_action( 'init', 'create_compliment_taxonomies', 0 );

// create two taxonomies, compliments and writers for the post type "compliment"
function create_compliment_taxonomies() {
    // Add new taxonomy, make it hierarchical (like categories)
    $labels = array(
        'name'              => _x( 'Compliments', 'taxonomy general name' ),
        'singular_name'     => _x( 'Compliment', 'taxonomy singular name' ),
        'search_items'      => __( 'Search Compliments' ),
        'all_items'         => __( 'All Compliments' ),
        'parent_item'       => __( 'Parent Compliment' ),
        'parent_item_colon' => __( 'Parent Compliment:' ),
        'edit_item'         => __( 'Edit Compliment' ),
        'update_item'       => __( 'Update Compliment' ),
        'add_new_item'      => __( 'Add New Compliment' ),
        'new_item_name'     => __( 'New Compliment Name' ),
        'menu_name'         => __( 'Compliment' ),
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'compliment' ),
    );

    register_taxonomy( 'compliment', array(), $args );
}

//compliment icons
add_action( 'admin_enqueue_scripts', 'compliments_enqueue_color_picker' );
function compliments_enqueue_color_picker( $hook_suffix ) {
    wp_enqueue_media();
    wp_enqueue_script( 'compliments-adminjs', constant( 'BP_COMPLIMENTS_URL' ) . '/js/admin.js', array(), false, true );
}
function compliments_taxonomy_add_new_meta_field() {
    ?>
    <div class="form-field form-required">
	<span class='caticon-upload upload'>
        <label for="term_meta[compliments_icon]"><?php _e( 'Compliment Icon', 'compliments' ); ?></label>
        <img id="comp-icon-preview" class="image_preview" src="" style="display: none;" /><br/>
        <input id="comp-icon-upload" type="button" data-uploader_title="<?php echo __( 'Upload Icon' , BP_COMP_TEXTDOMAIN ); ?>" data-uploader_button_text="<?php echo __( 'Use Icon' , BP_COMP_TEXTDOMAIN ); ?>" class="image_upload_button button" value="<?php echo __( 'Upload new Icon' , BP_COMP_TEXTDOMAIN ); ?>" />
        <input id="comp-icon-delete" type="button" class="image_delete_button button" value="<?php echo __( 'Remove Icon' , BP_COMP_TEXTDOMAIN ); ?>" />
        <input id="comp-icon-value" class="image_data_field" type="hidden" name="term_meta[compliments_icon]" value=""/><br/>
        <p><?php echo __( 'Recommended icon size: 20px x 20px' , BP_COMP_TEXTDOMAIN ); ?></p>
    </span>
    </div>
<?php
}
add_action( 'compliment_add_form_fields', 'compliments_taxonomy_add_new_meta_field', 10, 2 );
function compliments_taxonomy_edit_meta_field($term) {
    $t_id = $term->term_id;
    $term_meta = get_option( "taxonomy_$t_id" ); ?>
    <tr class="form-field form-required">
        <th scope="row" valign="top"><label for="term_meta[compliments_icon]"><?php _e( 'Compliment Icon', 'compliments' ); ?></label></th>
        <td>
		    <span class='caticon-upload upload'>
                <img id="comp-icon-preview" class="image_preview" src="<?php echo esc_attr( $term_meta['compliments_icon'] ) ? esc_attr( $term_meta['compliments_icon'] ) : ''; ?>" /><br/>
                <input id="comp-icon-upload" type="button" data-uploader_title="<?php echo __( 'Upload Icon' , BP_COMP_TEXTDOMAIN ); ?>" data-uploader_button_text="<?php echo __( 'Use Icon' , BP_COMP_TEXTDOMAIN ); ?>" class="image_upload_button button" value="<?php echo __( 'Upload new Icon' , BP_COMP_TEXTDOMAIN ); ?>" />
                <input id="comp-icon-delete" type="button" class="image_delete_button button" value="<?php echo __( 'Remove Icon' , BP_COMP_TEXTDOMAIN ); ?>" />
                <input id="comp-icon-value" class="image_data_field" type="hidden" name="term_meta[compliments_icon]" value=""/><br/>
                <p><?php echo __( 'Recommended icon size: 20px x 20px' , BP_COMP_TEXTDOMAIN ); ?></p>
            </span>
        </td>
    </tr>
<?php
}
add_action( 'compliment_edit_form_fields', 'compliments_taxonomy_edit_meta_field', 10, 2 );
function save_taxonomy_custom_meta( $term_id ) {
    if ( isset( $_POST['term_meta'] ) ) {
        $t_id = $term_id;
        $term_meta = get_option( "taxonomy_$t_id" );
        $cat_keys = array_keys( $_POST['term_meta'] );
        foreach ( $cat_keys as $key ) {
            if ( isset ( $_POST['term_meta'][$key] ) ) {
                $term_meta[$key] = $_POST['term_meta'][$key];
            }
        }
        // Save the option array.
        update_option( "taxonomy_$t_id", $term_meta );
    }
}
add_action( 'edited_compliment', 'save_taxonomy_custom_meta', 10, 2 );
add_action( 'create_compliment', 'save_taxonomy_custom_meta', 10, 2 );

add_filter("manage_edit-compliment_columns", 'modify_compliment_columns');
function modify_compliment_columns($columns) {
    $new_columns = array(
        'cb' => '<input type="checkbox" />',
        'name' => __('Name'),
        'icon' => __('Icon'),
    );
    return $new_columns;
}

add_filter("manage_compliment_custom_column", 'manage_theme_columns', 10, 3);
function manage_theme_columns($out, $column_name, $t_id) {
    $term_meta = get_option( "taxonomy_$t_id" );
    $term_icon = esc_attr( $term_meta['compliments_icon'] ) ? esc_attr( $term_meta['compliments_icon'] ) : "";
    switch ($column_name) {
        case 'icon':
            $out .= '<img src="'.$term_icon.'" />';
            break;

        default:
            break;
    }
    return $out;
}

add_action( 'admin_head-edit-tags.php', 'compliment_remove_parent_dropdown' );
function compliment_remove_parent_dropdown()
{
    if ( 'compliment' != $_GET['taxonomy'] )
        return;

    $parent = 'parent()';

    if ( isset( $_GET['action'] ) )
        $parent = 'parent().parent()';

    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($)
        {
            $('label[for=parent]').<?php echo $parent; ?>.remove();
            $('label[for=slug]').<?php echo $parent; ?>.remove();
            $('label[for=description]').<?php echo $parent; ?>.remove();
            $('label[for=tag-slug]').<?php echo $parent; ?>.remove();
            $('label[for=tag-description]').<?php echo $parent; ?>.remove();
        });
    </script>
<?php
}