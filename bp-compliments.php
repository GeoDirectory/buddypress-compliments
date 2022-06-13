<?php
/**
 * BuddyPress Compliments
 *
 * @package           BuddyPress_Compliments
 * @author            AyeCode Ltd
 * @copyright         2016 AyeCode Ltd
 * @license           GPLv3
 *
 * @wordpress-plugin
 * Plugin Name:       BuddyPress Compliments
 * Plugin URI:        https://wordpress.org/plugins/buddypress-compliments/
 * Description:       BuddyPress Compliments plugin adds a smart way for BuddyPress members to interact with each other via compliments.
 * Version:           1.1.1
 * Requires at least: 4.5
 * Tested up to:      6.0
 * Requires PHP:      5.6
 * Author:            AyeCode Ltd
 * Author URI:        https://ayecode.io
 * License:           GPLv3
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       bp-compliments
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! defined( 'BP_COMPLIMENTS_VER' ) ) {
	/**
	 * Plugin version.
	 */
	define( 'BP_COMPLIMENTS_VER', '1.1.1' );
}

if ( ! defined( 'BP_COMP_TEXTDOMAIN' ) ) {
	/**
	 * Plugin textdomain.
	 */
	define( 'BP_COMP_TEXTDOMAIN', 'bp-compliments' );
}

if ( ! defined( 'BP_COMP_SINGULAR_NAME' ) ) {
	/**
	 * Compliments singular name.
	 */
	define( 'BP_COMP_SINGULAR_NAME', trim( esc_attr( get_option( 'bp_compliment_singular_name', __( 'Compliment', 'bp-compliments' ) ) ) ) );
}

if ( ! defined( 'BP_COMP_PLURAL_NAME' ) ) {
	/**
	 * Compliments plural name.
	 */
	define( 'BP_COMP_PLURAL_NAME', trim( esc_attr( get_option( 'bp_compliment_plural_name', __( 'Compliments', 'bp-compliments' ) ) ) ) );
}

if ( ! defined( 'BP_COMPLIMENTS_SLUG' ) ) {
	/**
	 * Compliments slug.
	 */
	define( 'BP_COMPLIMENTS_SLUG', trim( strtolower( esc_attr( get_option('bp_compliment_slug', __( 'compliments', 'bp-compliments' ) ) ) ) ) );
}

/**
 * Only load the plugin code if BuddyPress is activated.
 *
 * @since 0.0.1
 * @package BuddyPress_Compliments
 *
 * @global object $bp BuddyPress instance.
 * @global object $wpdb WordPress db object.
 */
function bp_compliments_init() {
	global $wpdb, $bp;

	if ( !$table_prefix = $bp->table_prefix )
		/**
		 * Filters the value of BuddyPress table prefix.
		 *
		 * @since 0.0.1
		 * @package BuddyPress_Compliments
		 *
		 * @param string $wpdb->base_prefix WordPress table prefix.
		 */
		$table_prefix = apply_filters( 'bp_core_get_table_prefix', $wpdb->base_prefix );

	if ( ! defined( 'BP_COMPLIMENTS_DIR' ) ) {
		/**
		 * Compliments plugin directory.
		 */
		define( 'BP_COMPLIMENTS_DIR', plugin_dir_path( __FILE__ ) );
	}

	if ( ! defined( 'BP_COMPLIMENTS_URL' ) ) {
		/**
		 * Compliments plugin url.
		 */
		define( 'BP_COMPLIMENTS_URL', untrailingslashit( plugins_url( '/', __FILE__ ) ) . '/' );
	}

	if ( ! defined( 'BP_COMPLIMENTS_TABLE' ) ) {
		/**
		 * Compliments database table.
		 */
		define( 'BP_COMPLIMENTS_TABLE', $table_prefix . 'bp_compliments' );
	}

	if ( file_exists( BP_COMPLIMENTS_DIR . 'vendor/autoload.php' ) ) {
		require_once( BP_COMPLIMENTS_DIR . 'vendor/autoload.php' );
	}

	// Only supported in BP 1.5+
	if ( version_compare( BP_VERSION, '1.3', '>' ) ) {
		require( BP_COMPLIMENTS_DIR . 'bp-compliments-core.php' );
		// Show admin notice for users on BP 1.2.x
	} else {
		add_action( 'admin_notices', 'bp_compliments_older_version_notice' );
		return;
	}
}
add_action( 'bp_include', 'bp_compliments_init' );
add_action( 'init', 'bp_compliments_plugin_init' );

/**
 * Hook into actions and filters on site init.
 */
function bp_compliments_plugin_init() {
	add_action( 'tgmpa_register', 'bp_compliments_require_plugins' );
}

/**
 * Add required plugin check.
 */
function bp_compliments_require_plugins(){
	$plugins = array( /* The array to install plugins */ );
	$plugins = array(
		array(
			'name'      => 'BuddyPress',
			'slug'      => 'buddypress',
			'required'  => true, // this plugin is recommended
			'version'   => '1.5'
		)
	);
	$config = array(); /* The array to configure TGM Plugin Activation */
	tgmpa( $plugins, $config );
}

/**
 * Creates Custom table for BuddyPress compliments.
 *
 * @since 0.0.1
 * @package BuddyPress_Compliments
 *
 * @global object $bp BuddyPress instance.
 * @global object $wpdb WordPress db object.
 */
function bp_compliments_activate() {
    global $bp, $wpdb;
    $version = get_option( 'bp_compliments_version');

    if (!$version) {
        $charset_collate = !empty( $wpdb->charset ) ? "DEFAULT CHARACTER SET $wpdb->charset" : '';
        if ( !$table_prefix = $bp->table_prefix )
            /**
             * Filters the value of BuddyPress table prefix.
             *
             * @since 0.0.1
             * @package BuddyPress_Compliments
             *
             * @param string $wpdb->base_prefix WordPress table prefix.
             */
            $table_prefix = apply_filters( 'bp_core_get_table_prefix', $wpdb->base_prefix );

        $sql = "CREATE TABLE {$table_prefix}bp_compliments (
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
    
    add_option( 'bp_compliments_activation_redirect', 1 );
}

if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
    register_activation_hook( __FILE__, 'bp_compliments_activate' );
    register_deactivation_hook( __FILE__, 'bp_compliments_deactivate' );
    add_action( 'admin_init', 'bp_compliments_activation_redirect' );
}

/**
 * Plugin deactivation hook.
 *
 * @since 1.0.7
 */
function bp_compliments_deactivate() {
    // Plugin deactivation stuff.
}

/**
 * Redirects user to BuddyPress Compliments settings page after plugin activation.
 *
 * @since 1.0.7
 */
function bp_compliments_activation_redirect() {
    if ( get_option( 'bp_compliments_activation_redirect', false ) ) {
        delete_option( 'bp_compliments_activation_redirect' );
        if(class_exists('BuddyPress')){
            wp_redirect( admin_url( 'admin.php?page=bp-compliment-settings' ) );
            exit;
        }
    }
}

/**
 * Custom text domain loader.
 *
 * @since 0.0.1
 * @package BuddyPress_Compliments
 */
function bp_compliments_localization() {
	global $wp_version;

	$locale = $wp_version >= 4.7 ? get_user_locale() : get_locale();

	/**
	 * Filter the plugin locale.
	 *
	 * @since   1.0.0
	 * @package BuddyPress_Compliments
	 */
	$locale = apply_filters( 'plugin_locale', $locale, 'bp-compliments' );

	unload_textdomain( 'bp-compliments' );
	load_textdomain( 'bp-compliments', WP_LANG_DIR . '/bp-compliments/bp-compliments-' . $locale . '.mo' );
	load_plugin_textdomain( 'bp-compliments', false, basename( dirname( __FILE__ ) ) . '/languages/' );

}
add_action( 'plugins_loaded', 'bp_compliments_localization' );

function bp_compliments_older_version_notice() {
    $older_version_notice = __( "Hey! BP Compliments requires BuddyPress 1.5 or higher.", 'bp-compliments' );

    echo '<div class="error"><p>' . $older_version_notice . '</p></div>';
}