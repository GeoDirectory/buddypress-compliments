<?php
/**
 * Functions related to handling user submitted data and actions.
 *
 * @since 0.0.1
 * @package BuddyPress_Compliments
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Handle submitted modal form data.
 *
 * @since 0.0.1
 * @package BuddyPress_Compliments
 */
function handle_compliments_form_data() {

    if (isset($_POST['comp-modal-form'])) {
        $nonce = wp_verify_nonce( $_POST['handle_compliments_nonce'], 'handle_compliments_form_data' );
        if (!$nonce) {
            return;
        }

        if ( bp_displayed_user_id() == bp_loggedin_user_id() ) {
            return;
        }


        $term_id = strip_tags($_POST['term_id']);
        // post id is required for geodirectory's whoop theme.
        $post_id = strip_tags($_POST['post_id']);
        $receiver_id = strip_tags($_POST['receiver_id']);
        $message = strip_tags($_POST['message']);
        $args = array(
            'term_id' => (int) $term_id,
            'post_id' => (int) $post_id,
            'message' => $message,
            'sender_id' => get_current_user_id()
        );
        if ($receiver_id) {
            $args['receiver_id'] = $receiver_id;
        }

        $receiver_name = bp_core_get_user_displayname($receiver_id);
        $redirect_url = bp_core_get_user_domain($receiver_id);

        if ( ! bp_compliments_start_compliment($args)) {
            bp_core_add_message( sprintf( __( 'There was a problem when trying to send compliment to %s, please contact administrator.', BP_COMP_TEXTDOMAIN ), $receiver_name ), 'error' );
        } else {
            bp_core_add_message( sprintf( __( 'Your compliment sent to %s.', BP_COMP_TEXTDOMAIN ), $receiver_name ) );
        }

        $redirect = $redirect_url.'compliments/';
        bp_core_redirect( $redirect );
    }
}
add_action( 'bp_actions', 'handle_compliments_form_data', 99 );

/**
 * Delete a single complement using compliment ID.
 *
 * @since 0.0.1
 * @package BuddyPress_Compliments
 */
function delete_single_complement() {
    if (!bp_is_user()) {
        return;
    }

    if ( bp_displayed_user_id() != bp_loggedin_user_id() ) {
        return;
    }

    if (!isset($_GET['c_id']) OR !isset($_GET['action']) ) {
        return;
    }

    $bp_compliment_can_delete_value = esc_attr( get_option('bp_compliment_can_delete'));
    $bp_compliment_can_delete = $bp_compliment_can_delete_value ? $bp_compliment_can_delete_value : 'yes';

    if ($bp_compliment_can_delete == 'no') {
        return;
    }

    $c_id = (int) strip_tags(esc_sql($_GET['c_id']));

    if (!$c_id) {
        return;
    }

    /**
     * Functions hooked to this action will be processed before deleting the complement.
     *
     * @since 0.0.1
     * @package BuddyPress_Compliments
     *
     * @param int $c_id The compliment ID.
     */
    do_action( 'bp_compliments_before_remove_compliment', $c_id );

    BP_Compliments::delete( $c_id );

    /**
     * Functions hooked to this action will be processed after deleting the complement.
     *
     * @since 0.0.1
     * @package BuddyPress_Compliments
     *
     * @param int $c_id The compliment ID.
     */
    do_action( 'bp_compliments_after_remove_compliment', $c_id );

    $redirect = bp_displayed_user_domain().'compliments/';
    bp_core_redirect( $redirect );
}
add_action( 'bp_actions', 'delete_single_complement');

