<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

class BP_Compliments {
    /**
     * The compliments ID.
     */
    public $id = 0;

    /**
     * The user ID of receiver.
     */
    public $receiver_id;

    /**
     * The user ID of sender.
     */
    var $sender_id;

    /**
     * The compliment type term ID.
     */
    var $term_id;

    /**
     * The post ID.
     */
    var $post_id;

    /**
     * The compliment message.
     */
    var $message;


    /**
     * Constructor.
     *
     * @param int $receiver_id The user ID of the user you want to compliment.
     * @param int $sender_id The user ID initiating the compliment request.
     * @param int $term_id The term ID of the compliment type.
     * @param int $post_id Optional. The post ID. If the compliment is for a post.
     * @param string $message Optional. The compliment message.
     */
    public function __construct( $receiver_id = 0, $sender_id = 0, $term_id = 0, $post_id = 0, $message = '' ) {
        if ( ! empty( $receiver_id ) && ! empty( $sender_id ) ) {
            $this->receiver_id   = (int) $receiver_id;
            $this->sender_id = (int) $sender_id;
        }
        if ( ! empty( $term_id ) ) {
            $this->term_id   = (int) $term_id;
        }
        if ( ! empty( $post_id ) ) {
            $this->post_id   = (int) $post_id;
        }
        if ( ! empty( $message ) ) {
            $this->message   = $message;
        }
    }

    /**
     * Saves a compliment into the database.
     */
    public function save() {
        global $wpdb, $bp;
        $table_name = BP_COMPLIMENTS_TABLE;

        $this->receiver_id   = apply_filters( 'bp_compliments_receiver_id_before_save',   $this->receiver_id,   $this->id );
        $this->sender_id = apply_filters( 'bp_compliments_sender_id_before_save', $this->sender_id, $this->id );
        $this->term_id = apply_filters( 'bp_compliments_term_id_before_save', $this->term_id, $this->id );
        $this->post_id = apply_filters( 'bp_compliments_post_id_before_save', $this->post_id, $this->id );
        $this->message = apply_filters( 'bp_compliments_message_before_save', $this->message, $this->id );

        do_action_ref_array( 'bp_compliments_before_save', array( &$this ) );

        if (!$this->term_id OR !$this->receiver_id OR !$this->sender_id) {
            return false;
        }

        $result = $wpdb->query( $wpdb->prepare( "INSERT INTO {$table_name} ( receiver_id, sender_id, term_id, post_id, message, created_at ) VALUES ( %d, %d, %d, %d, %s, %s )", $this->receiver_id, $this->sender_id, $this->term_id, $this->post_id, $this->message, current_time( 'mysql' ) ) );

        $this->id = $wpdb->insert_id;

        do_action_ref_array( 'bp_compliments_after_save', array( &$this ) );

        return $result;
    }

    /**
     * Deletes a compliment from the database.
     */
    public function delete() {
        global $wpdb, $bp;
        $table_name = BP_COMPLIMENTS_TABLE;
        return $wpdb->query( $wpdb->prepare( "DELETE FROM {$table_name} WHERE id = %d", $this->id ) );
    }

    /**
     * Get the sender IDs for a given user.
     * @param $user_id
     * @param $offset
     * @param $limit
     * @return mixed
     */
    public static function get_compliments( $user_id, $offset, $limit ) {
        global $bp, $wpdb;
        $table_name = BP_COMPLIMENTS_TABLE;
        return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE receiver_id = %d ORDER BY created_at DESC LIMIT %d, %d", $user_id, $offset, $limit ) );
    }

    /**
     * Get the senders / receivers counts for a given user.
     * @param $user_id
     * @return array
     */
    public static function get_counts( $user_id ) {
        global $bp, $wpdb;
        $table_name = BP_COMPLIMENTS_TABLE;
        $senders = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(id) FROM {$table_name} WHERE receiver_id = %d", $user_id ) );
        $receivers = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(id) FROM {$table_name} WHERE sender_id = %d", $user_id ) );

        return array( 'senders' => $senders, 'receivers' => $receivers );
    }


    /**
     * Deletes all compliments for a given user.
     * @param $user_id
     */
    public static function delete_all_for_user( $user_id ) {
        global $bp, $wpdb;
        $table_name = BP_COMPLIMENTS_TABLE;
        $wpdb->query( $wpdb->prepare( "DELETE FROM {$table_name} WHERE receiver_id = %d OR sender_id = %d", $user_id, $user_id ) );
    }
}