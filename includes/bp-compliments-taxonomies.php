<?php
/**
 * Functions related to compliment types and icons.
 *
 * @since 0.0.1
 * @package BuddyPress_Compliments
 */

add_action('admin_menu', 'register_compliments_submenu_page');

/**
 * Register Compliments menu below Settings menu.
 *
 * @since 0.0.1
 * @package BuddyPress_Compliments
 */
function register_compliments_submenu_page() {
    add_submenu_page(
        'bp-compliment-settings',
        sprintf( __( '%s Types', BP_COMP_TEXTDOMAIN ), BP_COMP_SINGULAR_NAME ),
        sprintf( __( '%s Types', BP_COMP_TEXTDOMAIN ), BP_COMP_SINGULAR_NAME ),
        'manage_options',
        'edit-tags.php?taxonomy=compliment'
    );
}
// hook into the init action and call create_book_taxonomies when it fires
add_action( 'init', 'create_compliment_taxonomies', 0 );

/**
 * Create compliment taxonomies for creating compliment types.
 *
 * @since 0.0.1
 * @package BuddyPress_Compliments
 */
function create_compliment_taxonomies() {
    // Add new taxonomy, make it hierarchical (like categories)
    $labels = array(
        'name'              => BP_COMP_PLURAL_NAME,
        'singular_name'     => BP_COMP_SINGULAR_NAME,
        'search_items'      => sprintf( __( 'Search %s', BP_COMP_TEXTDOMAIN ), BP_COMP_PLURAL_NAME ),
        'all_items'         => sprintf( __( 'All %s', BP_COMP_TEXTDOMAIN ), BP_COMP_PLURAL_NAME ),
        'parent_item'       => sprintf( __( 'Parent %s', BP_COMP_TEXTDOMAIN ), BP_COMP_SINGULAR_NAME ),
        'parent_item_colon' => sprintf( __( 'Parent %s:', BP_COMP_TEXTDOMAIN ), BP_COMP_SINGULAR_NAME ),
        'edit_item'         => sprintf( __( 'Edit %s', BP_COMP_TEXTDOMAIN ), BP_COMP_SINGULAR_NAME ),
        'update_item'       => sprintf( __( 'Update %s', BP_COMP_TEXTDOMAIN ), BP_COMP_SINGULAR_NAME ),
        'add_new_item'      => sprintf( __( 'Add New %s', BP_COMP_TEXTDOMAIN ), BP_COMP_SINGULAR_NAME ),
        'new_item_name'     => sprintf( __( 'New %s Name', BP_COMP_TEXTDOMAIN ), BP_COMP_SINGULAR_NAME ),
        'menu_name'         => BP_COMP_SINGULAR_NAME,
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
add_action( 'admin_enqueue_scripts', 'compliments_enqueue_admin_js' );
/**
 * Enqueue admin js for compliments plugin.
 *
 * @since 0.0.1
 * @package BuddyPress_Compliments
 *
 * @param string $hook_suffix Admin page suffix.
 */
function compliments_enqueue_admin_js( $hook_suffix ) {
    wp_enqueue_media();
    wp_enqueue_script( 'compliments-adminjs', constant( 'BP_COMPLIMENTS_URL' ) . 'js/admin.js', array(), false, true );
}

/**
 * Compliment icon upload form field.
 *
 * @since 0.0.1
 * @package BuddyPress_Compliments
 */
function compliments_taxonomy_add_new_meta_field() {
    ?>
    <div class="form-field form-required caticon-upload upload">
        <label for="term_meta[compliments_icon]"><?php echo sprintf( __( '%s Icon', BP_COMP_TEXTDOMAIN ), BP_COMP_SINGULAR_NAME ) ?></label>
        <img id="comp-icon-preview" class="image_preview" src="" style="display: none;" /><br/>
        <input id="comp-icon-value" style="position:absolute; left:-500px;width:50px;" class="image_data_field" type="text" name="term_meta[compliments_icon]" value=""/>
        <input id="comp-icon-upload" type="button" data-uploader_title="<?php echo __( 'Upload Icon' , BP_COMP_TEXTDOMAIN ); ?>" data-uploader_button_text="<?php echo __( 'Use Icon' , BP_COMP_TEXTDOMAIN ); ?>" class="image_upload_button button" value="<?php echo __( 'Upload new Icon' , BP_COMP_TEXTDOMAIN ); ?>" />
        <input id="comp-icon-delete" type="button" class="image_delete_button button" value="<?php echo __( 'Remove Icon' , BP_COMP_TEXTDOMAIN ); ?>" />
        <br/>
        <p><?php echo __( 'Recommended icon size: 20px x 20px' , BP_COMP_TEXTDOMAIN ); ?></p>
    </div>
<?php
    bp_compliments_taxonomy_highlight_js();
}
add_action( 'compliment_add_form_fields', 'compliments_taxonomy_add_new_meta_field', 10, 2 );
/**
 * Compliment icon upload form field for edit page.
 *
 * @since 0.0.1
 * @package BuddyPress_Compliments
 *
 * @param object $term The term object.
 */
function compliments_taxonomy_edit_meta_field($term) {
    $t_id = $term->term_id;
    $term_meta = get_option( "taxonomy_$t_id" ); ?>
    <tr class="form-field form-required">
        <th scope="row" valign="top"><label for="term_meta[compliments_icon]"><?php echo sprintf( __( '%s Icon', BP_COMP_TEXTDOMAIN ), BP_COMP_SINGULAR_NAME ) ?></label></th>
        <td>
		    <span class='caticon-upload upload'>
                <input id="comp-icon-value" style="position:absolute; left:-500px;width:50px;" class="image_data_field" type="hidden" name="term_meta[compliments_icon]" value="<?php echo esc_attr( $term_meta['compliments_icon'] ) ? esc_attr( $term_meta['compliments_icon'] ) : ''; ?>"/>
                <img id="comp-icon-preview" class="image_preview" src="<?php echo esc_attr( $term_meta['compliments_icon'] ) ? esc_attr( $term_meta['compliments_icon'] ) : ''; ?>" /><br/>
                <input id="comp-icon-upload" type="button" data-uploader_title="<?php echo __( 'Upload Icon' , BP_COMP_TEXTDOMAIN ); ?>" data-uploader_button_text="<?php echo __( 'Use Icon' , BP_COMP_TEXTDOMAIN ); ?>" class="image_upload_button button" value="<?php echo __( 'Upload new Icon' , BP_COMP_TEXTDOMAIN ); ?>" />
                <input id="comp-icon-delete" type="button" class="image_delete_button button" value="<?php echo __( 'Remove Icon' , BP_COMP_TEXTDOMAIN ); ?>" />
                <br/>
                <p><?php echo __( 'Recommended icon size: 20px x 20px' , BP_COMP_TEXTDOMAIN ); ?></p>
            </span>
        </td>
    </tr>
<?php
    bp_compliments_taxonomy_highlight_js();
}
add_action( 'compliment_edit_form_fields', 'compliments_taxonomy_edit_meta_field', 10, 2 );
/**
 * Save taxonomy custom meta.
 *
 * @since 0.0.1
 * @package BuddyPress_Compliments
 *
 * @param int $term_id The term ID.
 */
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
/**
 * Modify compliment page admin columns.
 *
 * @since 0.0.1
 * @package BuddyPress_Compliments
 *
 * @param array $columns The column array.
 * @return array Modified column array.
 */
function modify_compliment_columns($columns) {
    $new_columns = array(
        'cb' => '<input type="checkbox" />',
        'name' => __('Name', BP_COMP_TEXTDOMAIN),
        'icon' => __('Icon', BP_COMP_TEXTDOMAIN),
    );
    return $new_columns;
}

add_filter("manage_compliment_custom_column", 'manage_bp_compliment_columns', 10, 3);
/**
 * Modify compliment page admin column content.
 *
 * @since 0.0.1
 * @package BuddyPress_Compliments
 *
 * @param string $out The html output.
 * @param string $column_name The column name.
 * @param int $t_id The term ID.
 * @return string The modified html output.
 */
function manage_bp_compliment_columns($out, $column_name, $t_id) {
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
/**
 * Remove irrelevant fields from the compliment taxonomy form.
 *
 * @since 0.0.1
 * @package BuddyPress_Compliments
 */
function compliment_remove_parent_dropdown()
{
    if ( !isset($_GET['taxonomy']) OR ('compliment' != $_GET['taxonomy']) )
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

function bp_compliments_taxonomy_highlight_js() {
    ?>
    <script type="text/javascript">
        jQuery(document).ready( function($)
        {
            //remove higlighting from the posts menu
            var posts_menu = $('#menu-posts');
            posts_menu.removeClass('wp-has-current-submenu wp-menu-open');
            posts_menu.addClass('wp-not-current-submenu');
            posts_menu.children('a').removeClass('wp-has-current-submenu');

            // add highlighting to our compliments menu
            var comp_menu = $('#toplevel_page_bp-compliment-settings');
            comp_menu.removeClass('wp-not-current-submenu');
            comp_menu.children('a').removeClass('wp-not-current-submenu');
            comp_menu.addClass('wp-has-current-submenu wp-menu-open');
            comp_menu.children('a').addClass('wp-has-current-submenu');

        });
    </script>
    <?php
}

function bp_comp_custom_css() {
    $comp_custom_css_value = esc_attr( get_option('bp_comp_custom_css'));
    $comp_custom_css = $comp_custom_css_value ? $comp_custom_css_value : '';
    ?>
    <style type="text/css">
        <?php echo $comp_custom_css; ?>
    </style>
  <?php
}
add_action( 'wp_head', 'bp_comp_custom_css' );