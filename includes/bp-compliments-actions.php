<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

function handle_compliments_form_data() {

    if (isset($_POST['comp-modal-form'])) {
        $nonce = wp_verify_nonce( $_POST['handle_compliments_nonce'], 'handle_compliments_form_data' );
        if (!$nonce) {
            return;
        }

        if ( bp_displayed_user_id() == bp_loggedin_user_id() ) {
            return;
        }

        $term_id = strip_tags(esc_sql($_POST['term_id']));
        $post_id = strip_tags(esc_sql($_POST['post_id']));
        $receiver_id = strip_tags(esc_sql($_POST['receiver_id']));
        $message = strip_tags(esc_sql($_POST['message']));
        $args = array(
            'term_id' => (int) $term_id,
            'post_id' => (int) $post_id,
            'message' => $message,
            'sender_id' => get_current_user_id()
        );
        if ($receiver_id) {
            $args['receiver_id'] = $receiver_id;
        }

        if ( ! bp_compliments_start_compliment($args)) {
            bp_core_add_message( sprintf( __( 'There was a problem when trying to send compliment to %s, please contact administrator.', 'bp-follow' ), bp_get_displayed_user_fullname() ), 'error' );
        } else {
            bp_core_add_message( sprintf( __( 'Your compliment sent to %s.', BP_COMP_TEXTDOMAIN ), bp_get_displayed_user_fullname() ) );
        }

        $redirect = bp_displayed_user_domain().'compliments/';
        bp_core_redirect( $redirect );
    }
}
add_action( 'bp_actions', 'handle_compliments_form_data', 99 );