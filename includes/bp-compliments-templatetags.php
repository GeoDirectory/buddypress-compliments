<?php
/**
 * Functions related to compliment buttons and template tags.
 *
 * @since 0.0.1
 * @package BuddyPress_Compliments
 */

/**
 * Output a compliment button for a given user.
 *
 * @since 0.0.1
 * @package BuddyPress_Compliments
 */
function bp_compliments_add_compliment_button( $args = '' ) {
    echo bp_compliments_get_add_compliment_button( $args );
}

/**
 * Returns a compliment button for a given user.
 *
 * @since 0.0.1
 * @package BuddyPress_Compliments
 *
 * @param string $args
 * @return string
 * @global object $bp BuddyPress instance.
 */
function bp_compliments_get_add_compliment_button( $args = '' ) {
    global $bp, $members_template;

    $r = wp_parse_args( $args, array(
        'receiver_id'     => bp_displayed_user_id(),
        'sender_id'   => bp_loggedin_user_id(),
        'link_text'     => '',
        'link_title'    => '',
        'wrapper_class' => '',
        'link_class'    => 'compliments-popup',
        'wrapper'       => 'div'
    ) );

    if ( ! $r['receiver_id'] || ! $r['sender_id'] )
        return false;


    // if the logged-in user is the receiver, use already-queried variables
    if ( bp_loggedin_user_id() && $r['receiver_id'] == bp_loggedin_user_id() ) {
        $receiver_domain   = bp_loggedin_user_domain();
        $receiver_fullname = bp_get_loggedin_user_fullname();

        // else we do a lookup for the user domain and display name of the receiver
    } else {
        $receiver_domain   = bp_core_get_user_domain( $r['receiver_id'] );
        $receiver_fullname = bp_core_get_user_displayname( $r['receiver_id'] );
    }

    // setup some variables

    $id        = 'compliments';
    $action    = 'start';
    $class     = 'compliments';
    $link_text = sprintf( _x( 'Send Compliment', 'Button', BP_COMP_TEXTDOMAIN ), apply_filters( 'bp_compliments_receiver_name', bp_get_user_firstname( $receiver_fullname ), $r['receiver_id'] ) );

    if ( empty( $r['link_text'] ) ) {
        $r['link_text'] = $link_text;
    }


    $wrapper_class = 'compliments-button ' . $id;

    if ( ! empty( $r['wrapper_class'] ) ) {
        $wrapper_class .= ' '  . esc_attr( $r['wrapper_class'] );
    }

    $link_class = $class;

    if ( ! empty( $r['link_class'] ) ) {
        $link_class .= ' '  . esc_attr( $r['link_class'] );
    }

    // make sure we can view the button if a user is on their own page
    $block_self = empty( $members_template->member ) ? true : false;

    // if we're using AJAX and a user is on their own profile, we need to set
    // block_self to false so the button shows up
    if ( ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ) && bp_is_my_profile() ) {
        $block_self = false;
    }

    // setup the button arguments
    $button = array(
        'id'                => $id,
        'component'         => 'compliments',
        'must_be_logged_in' => true,
        'block_self'        => $block_self,
        'wrapper_class'     => $wrapper_class,
        'wrapper_id'        => 'compliments-button-' . (int) $r['receiver_id'],
        'link_href'         => wp_nonce_url( $receiver_domain . $bp->compliments->compliments->slug . '/' . $action .'/', $action . '_compliments' ),
        'link_text'         => esc_attr( $r['link_text'] ),
        'link_title'        => esc_attr( $r['link_title'] ),
        'link_id'           => $class . '-' . (int) $r['receiver_id'],
        'link_class'        => $link_class,
        'wrapper'           => ! empty( $r['wrapper'] ) ? esc_attr( $r['wrapper'] ) : false
    );

    // Filter and return the HTML button
    return bp_get_button( apply_filters( 'bp_compliments_get_add_compliment_button', $button, $r['receiver_id'], $r['sender_id'] ) );
}

/**
 * @since 0.0.1
 * @package BuddyPress_Compliments
 */
function bp_compliments_add_profile_compliment_button() {
    bp_compliments_add_compliment_button();
}
add_action( 'bp_member_header_actions', 'bp_compliments_add_profile_compliment_button' );

/**
 *
 * @since 0.0.1
 * @package BuddyPress_Compliments
 *
 * @param string $args
 * @return mixed|void
 */
function bp_compliments_get_compliments( $args = '' ) {
    $r = wp_parse_args( $args, array(
        'user_id' => bp_displayed_user_id(),
        'offset' => 0,
        'limit' => 100,
        'c_id' => false
    ) );

    return apply_filters( 'bp_compliments_get_compliments', BP_Compliments::get_compliments( $r['user_id'], $r['offset'], $r['limit'], $r['c_id'] ) );
}