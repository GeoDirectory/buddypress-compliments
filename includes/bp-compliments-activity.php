<?php
/**
 * Functions related to activity component.
 *
 * @since 0.0.2
 * @package BuddyPress_Compliments
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Records activity when a compliment get posted.
 *
 * @since 0.0.2
 * @package BuddyPress_Compliments
 * 
 * @param array|string $args {
 *    Attributes of the $args.
 *
 *    @type int $user_id The user to record the activity for, can be false if this activity is not for a user.
 *    @type string $action The activity action - e.g. "Jon Doe posted an update".
 *    @type string $content Optional: The content of the activity item e.g. "BuddyPress is awesome guys!".
 *    @type string $primary_link Optional: The primary URL for this item in RSS feeds (defaults to activity permalink).
 *    @type string $component The name/ID of the component e.g. groups, profile, mycomponent.
 *    @type bool $type The activity type e.g. activity_update, profile_updated.
 *    @type bool $item_id Optional: The ID of the specific item being recorded, e.g. a blog_id.
 *    @type bool $secondary_item_id Optional: A second ID used to further filter e.g. a comment_id.
 *    @type string $recorded_time The GMT time that this activity was recorded.
 *    @type bool $hide_sitewide Should this be hidden on the sitewide activity stream?.
 *
 * }
 * @return bool|int The ID of the activity on success. False on error.
 */
function compliments_record_activity( $args = '' ) {

    if ( ! bp_is_active( 'activity' ) ) {
        return false;
    }

    $r = wp_parse_args( $args, array(
        'user_id'           => bp_loggedin_user_id(),
        'action'            => '',
        'content'           => '',
        'primary_link'      => '',
        'component'         => buddypress()->compliments->id,
        'type'              => false,
        'item_id'           => false,
        'secondary_item_id' => false,
        'recorded_time'     => bp_core_current_time(),
        'hide_sitewide'     => false
    ) );

    return bp_activity_add( $r );
}

/**
 * Records activity in two ways when a compliment get posted.
 *
 * @since 0.0.2
 * @package BuddyPress_Compliments
 * 
 * @param BP_Compliments $compliment The compliment object.
 */
function compliments_record_sent_received_activity( BP_Compliments $compliment ) {
    if ( ! bp_is_active( 'activity' ) ) {
        return;
    }

    // Record in activity streams for the sender
    compliments_record_activity( array(
        'user_id'           => $compliment->sender_id,
        'type'              => 'compliment_sent',
        'item_id'           => $compliment->id,
        'secondary_item_id' => $compliment->receiver_id
    ) );

    // Record in activity streams for the receiver
    compliments_record_activity( array(
        'user_id'           => $compliment->receiver_id,
        'type'              => 'compliment_received',
        'item_id'           => $compliment->id,
        'secondary_item_id' => $compliment->sender_id
    ) );
}
add_action( 'bp_compliments_start_compliment', 'compliments_record_sent_received_activity' );


/**
 * Register the activity actions for compliments.
 * 
 * @since 0.0.2
 * @package BuddyPress_Compliments
 */
function compliments_register_activity_actions() {

    if ( !bp_is_active( 'activity' ) ) {
        return false;
    }

    $bp = buddypress();

    bp_activity_set_action(
        $bp->compliments->id,
        'compliment_received',
        __( 'Compliment Received', BP_COMP_TEXTDOMAIN ),
        'compliments_format_activity_action_compliment_received',
        __( 'Compliments', BP_COMP_TEXTDOMAIN ),
        array( 'activity' )
    );

    bp_activity_set_action(
        $bp->compliments->id,
        'compliment_sent',
        __( 'Compliment Sent', BP_COMP_TEXTDOMAIN ),
        'compliments_format_activity_action_compliment_sent',
        __( 'Compliments', BP_COMP_TEXTDOMAIN ),
        array( 'activity' )
    );

    /**
     * Use this hook to register additional activity actions.
     *
     * @since 0.0.1
     * @package BuddyPress_Compliments
     */
    do_action( 'compliments_register_activity_actions' );
}
add_action( 'bp_register_activity_actions', 'compliments_register_activity_actions' );

/**
 * Format activity actions for 'compliment_received'.
 *
 * @since 0.0.2
 * @package BuddyPress_Compliments
 *
 * @global object $bp BuddyPress instance.
 * @param object $activity Activity data.
 * @return string $action Formatted activity action.
 */
function compliments_format_activity_action_compliment_received( $action, $activity ) {
    global $bp;

    $receiver_link = bp_core_get_userlink( $activity->user_id );
    $sender_link    = bp_core_get_userlink( $activity->secondary_item_id );
    $receiver_url    = bp_core_get_userlink( $activity->user_id, false, true );
    $compliment_url = $receiver_url . $bp->compliments->id . '/?c_id='.$activity->item_id;
    $compliment_link = '<a href="'.$compliment_url.'">'.__("compliment", BP_COMP_TEXTDOMAIN).'</a>';

    $bp_compliment_can_see_others_comp_value = esc_attr( get_option('bp_compliment_can_see_others_comp'));
    $bp_compliment_can_see_others_comp = $bp_compliment_can_see_others_comp_value ? $bp_compliment_can_see_others_comp_value : 'yes';

    if ($bp->loggedin_user->id == $bp->displayed_user->id) {
        $bp_compliment_can_see_others_comp = 'yes';
    }

    if ($bp_compliment_can_see_others_comp == 'yes') {
        $action = sprintf( __( '%1$s has received a %2$s from %3$s', BP_COMP_TEXTDOMAIN ), $receiver_link, $compliment_link, $sender_link );
    } else {
        $action = sprintf( __( '%1$s has received a compliment from %2$s', BP_COMP_TEXTDOMAIN ), $receiver_link, $sender_link );
    }


    /**
     * Filters the 'compliment_received' activity action format.
     *
     * @since 0.0.2
     *
     * @param string $action String text for the 'compliment_received' action.
     * @param object $activity Activity data.
     */
    return apply_filters( 'compliments_format_activity_action_compliment_received', $action, $activity );
}

/**
 * Format activity actions for 'compliment_sent'.
 *
 * @since 0.0.2
 * @package BuddyPress_Compliments
 *
 * @global object $bp BuddyPress instance.
 * @param string $action Static activity action.
 * @param object $activity Activity data.
 * @return string $action Formatted activity action.
 */
function compliments_format_activity_action_compliment_sent( $action, $activity ) {
    global $bp;
    $sender_link = bp_core_get_userlink( $activity->user_id );
    $receiver_link    = bp_core_get_userlink( $activity->secondary_item_id );
    $receiver_url    = bp_core_get_userlink( $activity->secondary_item_id, false, true );
    $compliment_url = $receiver_url . $bp->compliments->id . '/?c_id='.$activity->item_id;
    $compliment_link = '<a href="'.$compliment_url.'">'.__("compliment", BP_COMP_TEXTDOMAIN).'</a>';

    $bp_compliment_can_see_others_comp_value = esc_attr( get_option('bp_compliment_can_see_others_comp'));
    $bp_compliment_can_see_others_comp = $bp_compliment_can_see_others_comp_value ? $bp_compliment_can_see_others_comp_value : 'yes';

    if ($bp->loggedin_user->id == $bp->displayed_user->id) {
        $bp_compliment_can_see_others_comp = 'yes';
    }

    if ($bp_compliment_can_see_others_comp == 'yes') {
        $action = sprintf( __( '%1$s has sent a %2$s to %3$s', BP_COMP_TEXTDOMAIN ), $sender_link, $compliment_link, $receiver_link );
    } else {
        $action = sprintf( __( '%1$s has sent a compliment to %2$s', BP_COMP_TEXTDOMAIN ), $sender_link, $receiver_link );
    }

    /**
     * Filters the 'compliment_sent' activity action format.
     *
     * @since 0.0.2
     *
     * @param string $action String text for the 'compliment_sent' action.
     * @param object $activity Activity data.
     */
    return apply_filters( 'compliments_format_activity_action_compliment_sent', $action, $activity );
}


/**
 * Delete a compliment activity using compliment ID.
 *
 * @since 0.0.2
 * @package BuddyPress_Compliments
 * 
 * @param int $c_id Compliment ID.
 */
function compliments_delete_activity( $c_id ) {
    if ( ! bp_is_active( 'activity' ) ) {
        return;
    }

    bp_activity_delete( array(
        'component' => buddypress()->compliments->id,
        'item_id'   => $c_id
    ) );
}
add_action('bp_compliments_after_remove_compliment', 'compliments_delete_activity');

/**
 * Delete all compliment activities for user using user ID.
 *
 * @since 0.0.2
 * @package BuddyPress_Compliments
 * 
 * @param int $user_id User ID.
 */
function compliments_delete_activity_for_user( $user_id ) {
    if ( ! bp_is_active( 'activity' ) ) {
        return;
    }

    bp_activity_delete( array(
        'component' => buddypress()->compliments->id,
        'user_id'   => $user_id
    ) );

    bp_activity_delete( array(
        'component' => buddypress()->compliments->id,
        'secondary_item_id'   => $user_id
    ) );
}
add_action('bp_compliments_after_remove_data', 'compliments_delete_activity_for_user');