<?php
/**
 * Functions related to compliment component.
 *
 * @since 0.0.1
 * @package BuddyPress_Compliments
 */

/**
 * Start compliment.
 *
 * @since 0.0.1
 * @package BuddyPress_Compliments
 *
 * @global object $bp BuddyPress instance.
 * @param string|array $args {
 *    Attributes of the $args.
 *
 *    @type int $receiver_id Received ID.
 *    @type int $sender_id Sender ID.
 *    @type int $term_id Compliment Icon Term ID.
 *    @type int $post_id Post ID.
 *    @type string $message The compliment Message.
 *
 * }
 * @return bool
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

    /**
     * Functions hooked to this action will be processed after compliments data stored into the db.
     *
     * @since 0.0.1
     * @package BuddyPress_Compliments
     *
     * @param object $compliment The compliment data object.
     */
    do_action_ref_array( 'bp_compliments_start_compliment', array( &$compliment ) );

    return true;
}

/**
 * Get the total compliment counts for a user.
 *
 * @since 0.0.1
 * @package BuddyPress_Compliments
 *
 * @global object $bp BuddyPress instance.
 * @param string|array $args {
 *    Attributes of the $args.
 *
 *    @type int $user_id User ID.
 *
 * }
 * @return mixed|void
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

    /**
     * Filters the compliment count array.
     *
     * @since 0.0.1
     * @package BuddyPress_Compliments
     *
     * @param array $count {
     *    Attributes of the $count.
     *
     *    @type int $received Count of total compliments received.
     *    @type int $sent Count of total compliments sent.
     *
     * }
     * @param int $r['user_id'] User ID.
     */
    return apply_filters( 'bp_compliments_total_counts', $count, $r['user_id'] );
}

/**
 * Remove compliments data for the given user id.
 *
 * @since 0.0.1
 * @package BuddyPress_Compliments
 *
 * @param int $user_id The user ID.
 */
function bp_compliments_remove_data( $user_id ) {
    /**
     * Functions hooked to this action will be processed before deleting the user's complement data.
     *
     * @since 0.0.1
     * @package BuddyPress_Compliments
     *
     * @param int $user_id The User ID.
     */
    do_action( 'bp_compliments_before_remove_data', $user_id );

    BP_Compliments::delete_all_for_user( $user_id );

    /**
     * Functions hooked to this action will be processed after deleting the user's complement data.
     *
     * @since 0.0.1
     * @package BuddyPress_Compliments
     *
     * @param int $user_id The User ID.
     */
    do_action( 'bp_compliments_after_remove_data', $user_id );
}
add_action( 'wpmu_delete_user',	'bp_compliments_remove_data' );
add_action( 'delete_user',	'bp_compliments_remove_data' );
add_action( 'make_spam_user',	'bp_compliments_remove_data' );