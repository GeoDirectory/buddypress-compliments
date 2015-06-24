<?php
/**
 * Functions related to frontend content display.
 *
 * @since 0.0.1
 * @package BuddyPress_Compliments
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 *
 * @since 0.0.1
 * @package BuddyPress_Compliments
 *
 * @global object $bp BuddyPress instance.
 */
function bp_compliments_screen_compliments() {
    global $bp;

    do_action( 'bp_compliments_screen_compliments' );
    bp_core_load_template( 'members/single/compliments' );
}

/**
 *
 * @since 0.0.1
 * @package BuddyPress_Compliments
 *
 * @global object $bp BuddyPress instance.
 * @param $found_template
 * @param $templates
 * @return mixed|string|void
 */
function bp_compliments_load_template_filter( $found_template, $templates ) {
    global $bp;

    // Only filter the template location when we're on the compliments component pages.
    if ( ! bp_is_current_component( $bp->compliments->compliments->slug ) )
        return $found_template;

    if ( empty( $found_template ) ) {

        bp_register_template_stack( 'bp_compliments_get_template_directory', 14 );

        // locate_template() will attempt to find the plugins.php template in the
        // child and parent theme and return the located template when found
        //
        // plugins.php is the preferred template to use, since all we'd need to do is
        // inject our content into BP
        //
        // note: this is only really relevant for bp-default themes as theme compat
        // will kick in on its own when this template isn't found
        $found_template = locate_template( 'members/single/plugins.php', false, false );

        // add our hook to inject content into BP
        // note the new template name for our template part
        add_action( 'bp_template_content', create_function( '', "
			bp_get_template_part( 'members/single/compliments' );
		" ) );
    }

    return apply_filters( 'bp_compliments_load_template_filter', $found_template );
}
add_filter( 'bp_located_template', 'bp_compliments_load_template_filter', 10, 2 );

/**
 * @since 0.0.1
 * @package BuddyPress_Compliments
 *
 * @return mixed|void
 */
function bp_compliments_get_template_directory() {
    return apply_filters( 'bp_compliments_get_template_directory', constant( 'BP_COMPLIMENTS_DIR' ) . '/includes/templates' );
}