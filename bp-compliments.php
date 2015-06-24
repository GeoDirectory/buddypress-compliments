<?php
/*
Plugin Name: BuddyPress Compliments
Plugin URI: http://wpgeodirectory.com/
Description: Compliments module for BuddyPress.
Version: 0.1
Author: GeoDirectory
Author URI: http://wpgeodirectory.com
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

define( 'BP_COMP_TEXTDOMAIN', 'bp-compliments' );
/**
 * Only load the plugin code if BuddyPress is activated.
 */
function bp_compliments_init() {
    global $wpdb, $bp;
    // some pertinent defines
    define( 'BP_COMPLIMENTS_VER', "0.0.1" );
    define( 'BP_COMPLIMENTS_DIR', dirname( __FILE__ ) );
    define( 'BP_COMPLIMENTS_URL', plugin_dir_url( __FILE__ ) );
    if ( !$table_prefix = $bp->table_prefix )
        $table_prefix = apply_filters( 'bp_core_get_table_prefix', $wpdb->base_prefix );
    define( 'BP_COMPLIMENTS_TABLE', $table_prefix . 'bp_compliments' );

    // only supported in BP 1.5+
    if ( version_compare( BP_VERSION, '1.3', '>' ) ) {
        require( constant( 'BP_COMPLIMENTS_DIR' ) . '/bp-compliments-core.php' );

    // show admin notice for users on BP 1.2.x
    } else {
        $older_version_notice = __( "Hey! BP Compliments requires BuddyPress 1.5 or higher.", BP_COMP_TEXTDOMAIN );

        add_action( 'admin_notices', create_function( '', "
			echo '<div class=\"error\"><p>' . $older_version_notice . '</p></div>';
		" ) );

        return;
    }
}
add_action( 'bp_include', 'bp_compliments_init' );

function bp_compliments_activate() {
    global $bp, $wpdb;
    $version = get_option( 'bp_compliments_version');

    if (!$version) {
        $charset_collate = !empty( $wpdb->charset ) ? "DEFAULT CHARACTER SET $wpdb->charset" : '';
        if ( !$table_prefix = $bp->table_prefix )
            $table_prefix = apply_filters( 'bp_core_get_table_prefix', $wpdb->base_prefix );

        $sql[] = "CREATE TABLE IF NOT EXISTS {$table_prefix}bp_compliments (
			id bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			term_id int(10) NOT NULL,
			post_id int(10) NULL DEFAULT NULL,
			receiver_id bigint(20) NOT NULL,
			sender_id bigint(20) NOT NULL,
			message varchar(1000) NULL DEFAULT NULL,
			created_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		        KEY compliments (receiver_id, sender_id)
		) {$charset_collate};";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
        update_option( 'bp_compliments_version', BP_COMPLIMENTS_VER );
    }
}
register_activation_hook( __FILE__, 'bp_compliments_activate' );

/**
 * Custom text domain loader.
 *
 * Checks WP_LANG_DIR for the .mo file first, then the plugin's language folder.
 * Allows for a custom language file other than those packaged with the plugin.
 *
 * @uses load_textdomain() Loads a .mo file into WP
 */
function bp_compliments_localization() {
    $locale = apply_filters('plugin_locale', get_locale(), BP_COMP_TEXTDOMAIN);

    load_textdomain(BP_COMP_TEXTDOMAIN, WP_LANG_DIR . '/' . BP_COMP_TEXTDOMAIN . '/' . BP_COMP_TEXTDOMAIN . '-' . $locale . '.mo');
    load_plugin_textdomain(BP_COMP_TEXTDOMAIN, false, dirname( plugin_basename( __FILE__ ) ) . '/languages');

}
add_action( 'plugins_loaded', 'bp_compliments_localization' );

