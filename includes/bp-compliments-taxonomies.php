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
    add_menu_page(
        __( 'Compliments', BP_COMP_TEXTDOMAIN ),
        __( 'Compliments', BP_COMP_TEXTDOMAIN ),
        'manage_options',
        'bp-compliment-settings',
        'bp_compliments_settings_page',
        plugins_url( 'buddypress-compliments/images/smiley-icon.png' ),
        85
    );
    add_submenu_page(
        'bp-compliment-settings',
        __( 'Compliment Types', BP_COMP_TEXTDOMAIN ),
        __( 'Compliment Types', BP_COMP_TEXTDOMAIN ),
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
        'name'              => _x( 'Compliments', 'taxonomy general name', BP_COMP_TEXTDOMAIN ),
        'singular_name'     => _x( 'Compliment', 'taxonomy singular name', BP_COMP_TEXTDOMAIN ),
        'search_items'      => __( 'Search Compliments', BP_COMP_TEXTDOMAIN ),
        'all_items'         => __( 'All Compliments', BP_COMP_TEXTDOMAIN ),
        'parent_item'       => __( 'Parent Compliment', BP_COMP_TEXTDOMAIN ),
        'parent_item_colon' => __( 'Parent Compliment:', BP_COMP_TEXTDOMAIN ),
        'edit_item'         => __( 'Edit Compliment', BP_COMP_TEXTDOMAIN ),
        'update_item'       => __( 'Update Compliment', BP_COMP_TEXTDOMAIN ),
        'add_new_item'      => __( 'Add New Compliment', BP_COMP_TEXTDOMAIN ),
        'new_item_name'     => __( 'New Compliment Name', BP_COMP_TEXTDOMAIN ),
        'menu_name'         => __( 'Compliment', BP_COMP_TEXTDOMAIN ),
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
        <label for="term_meta[compliments_icon]"><?php _e( 'Compliment Icon', BP_COMP_TEXTDOMAIN ); ?></label>
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
        <th scope="row" valign="top"><label for="term_meta[compliments_icon]"><?php _e( 'Compliment Icon', BP_COMP_TEXTDOMAIN ); ?></label></th>
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

add_action( 'admin_init', 'bp_compliments_register_settings' );
function bp_compliments_register_settings() {
    register_setting( 'bp-compliment-settings', 'bp_compliment_can_see_others_comp' );
    register_setting( 'bp-compliment-settings', 'bp_compliment_can_delete' );
    register_setting( 'bp-compliment-settings', 'bp_comp_per_page' );
    register_setting( 'bp-compliment-settings', 'bp_comp_custom_css' );
}

function bp_compliments_settings_page() {
    ?>
    <div class="wrap">
        <h2><?php echo __( 'BuddyPress Compliments - Settings', BP_COMP_TEXTDOMAIN ); ?></h2>

        <form method="post" action="options.php">
            <?php settings_fields( 'bp-compliment-settings' ); ?>
            <?php do_settings_sections( 'bp-compliment-settings' );

            $bp_compliment_can_see_others_comp_value = esc_attr( get_option('bp_compliment_can_see_others_comp'));
            $bp_compliment_can_see_others_comp = $bp_compliment_can_see_others_comp_value ? $bp_compliment_can_see_others_comp_value : 'yes';

            $bp_compliment_can_delete_value = esc_attr( get_option('bp_compliment_can_delete'));
            $bp_compliment_can_delete = $bp_compliment_can_delete_value ? $bp_compliment_can_delete_value : 'yes';

            $comp_per_page_value = esc_attr( get_option('bp_comp_per_page'));
            $comp_per_page = $comp_per_page_value ? (int) $comp_per_page_value : 5;

            $comp_custom_css_value = esc_attr( get_option('bp_comp_custom_css'));
            $comp_custom_css = $comp_custom_css_value ? $comp_custom_css_value : '';
            ?>
            <table class="widefat fixed" style="padding:10px;margin-top: 10px;">
                <tr valign="top">
                    <th scope="row"><?php echo __( 'Members can see other members compliment page?', BP_COMP_TEXTDOMAIN ); ?></th>
                    <td>
                        <select id="bp_compliment_can_see_others_comp" name="bp_compliment_can_see_others_comp">
                            <option value="yes" <?php selected( $bp_compliment_can_see_others_comp, 'yes' ); ?>><?php echo __( 'Yes', BP_COMP_TEXTDOMAIN ); ?></option>
                            <option value="no" <?php selected( $bp_compliment_can_see_others_comp, 'no' ); ?>><?php echo __( 'No', BP_COMP_TEXTDOMAIN ); ?></option>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php echo __( 'Members can delete compliments received?', BP_COMP_TEXTDOMAIN ); ?></th>
                    <td>
                        <select id="bp_compliment_can_delete" name="bp_compliment_can_delete">
                            <option value="yes" <?php selected( $bp_compliment_can_delete, 'yes' ); ?>><?php echo __( 'Yes', BP_COMP_TEXTDOMAIN ); ?></option>
                            <option value="no" <?php selected( $bp_compliment_can_delete, 'no' ); ?>><?php echo __( 'No', BP_COMP_TEXTDOMAIN ); ?></option>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php echo __( 'Number of Compliments to display per page?', BP_COMP_TEXTDOMAIN ); ?></th>
                    <td><input type="number" class="widefat" name="bp_comp_per_page" value="<?php echo $comp_per_page; ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php echo __( 'Custom CSS styles', BP_COMP_TEXTDOMAIN ); ?></th>
                    <td><textarea class="widefat" rows="5" name="bp_comp_custom_css"><?php echo $comp_custom_css; ?></textarea></td>
                </tr>
                <tr valign="top">
                    <th></th>
                    <td><?php submit_button(null, 'primary','submit',false); ?></td>
                </tr>
            </table>

        </form>
    </div>
<?php }

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