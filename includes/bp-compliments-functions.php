<?php
/**
 * Start compliment.
 */
function bp_compliments_start_compliment( $args = '' ) {
    global $bp;

    $r = wp_parse_args( $args, array(
        'receiver_id'   => bp_displayed_user_id(),
        'sender_id' => bp_loggedin_user_id(),
        'term_id' => 0,
        'post_id' => 0,
        'message' => null
    ) );

    $compliment = new BP_Compliments( $r['receiver_id'], $r['sender_id'], $r['term_id'], $r['post_id'], $r['message'] );

    if ( ! $compliment->save() ) {
        return false;
    }

    do_action_ref_array( 'bp_compliments_start_compliment', array( &$compliment ) );

    return true;
}
/**
 * Get the total compliment counts for a user.
 */
function bp_compliments_total_counts( $args = '' ) {

    $r = wp_parse_args( $args, array(
        'user_id' => bp_loggedin_user_id()
    ) );

    $count = false;

    /* try to get locally-cached values first */

    // logged-in user
    if ( $r['user_id'] == bp_loggedin_user_id() && is_user_logged_in() ) {
        global $bp;

        if ( ! empty( $bp->loggedin_user->total_compliment_counts ) ) {
            $count = $bp->loggedin_user->total_compliment_counts;
        }

        // displayed user
    } elseif ( $r['user_id'] == bp_displayed_user_id() && bp_is_user() ) {
        global $bp;

        if ( ! empty( $bp->displayed_user->total_compliment_counts ) ) {
            $count = $bp->displayed_user->total_compliment_counts;
        }
    }

    // no cached value, so query for it
    if ( $count === false ) {
        $count = BP_Compliments::get_counts( $r['user_id'] );
    }

    return apply_filters( 'bp_compliments_total_counts', $count, $r['user_id'] );
}

function bp_compliments_remove_data( $user_id ) {
    do_action( 'bp_compliments_before_remove_data', $user_id );

    BP_Compliments::delete_all_for_user( $user_id );

    do_action( 'bp_compliments_after_remove_data', $user_id );
}
add_action( 'wpmu_delete_user',	'bp_compliments_remove_data' );
add_action( 'delete_user',	'bp_compliments_remove_data' );
add_action( 'make_spam_user',	'bp_compliments_remove_data' );