<?php
/**
 * Main Compliments component class.
 *
 * @since 0.0.1
 * @package BuddyPress_Compliments
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class BP_Compliments_Component
 *
 * @since 0.0.1
 * @package BuddyPress_Compliments
 */
class BP_Compliments_Component extends BP_Component {

    /**
     * Initialize BP_Compliments_Component class.
     *
     * @since 0.0.1
     * @package BuddyPress_Compliments
     *
     * @global object $bp BuddyPress instance.
     */
    public function __construct() {
        global $bp;


        /**
         * Filters the value of compliment nav position.
         *
         * @since 0.0.1
         * @package BuddyPress_Compliments
         */
        $this->params = array(
            'adminbar_myaccount_order' => apply_filters( 'bp_compliments_nav_position', 71 )
        );

        parent::start(
            'compliments',
            __( 'Compliments', 'bp-compliments' ),
            constant( 'BP_COMPLIMENTS_DIR' ) . '/includes',
            $this->params
        );

        // include our files
        $this->includes();

        // setup hooks
        $this->setup_hooks();

        // register our component as an active component in BP
        $bp->active_components[$this->id] = '1';
    }

    /**
     * Include required files.
     *
     * @since 0.0.1
     * @package BuddyPress_Compliments
     */
    public function includes( $includes = array() ) {
        $bp_compliment_enable_activity_value = esc_attr( get_option('bp_compliment_enable_activity'));
        $bp_compliment_enable_activity = $bp_compliment_enable_activity_value ? $bp_compliment_enable_activity_value : 'yes';

        $bp_compliment_enable_notifications_value = esc_attr( get_option('bp_compliment_enable_notifications'));
        $bp_compliment_enable_notifications = $bp_compliment_enable_notifications_value ? $bp_compliment_enable_notifications_value : 'yes';

        // Include the Class that interact with the custom db table.
        require( $this->path . '/bp-compliments-classes.php' );
        // Functions related to compliment component.
        require( $this->path . '/bp-compliments-functions.php' );
        // Functions related to frontend content display.
        require( $this->path . '/bp-compliments-screens.php' );
        // Functions related to compliment buttons and template tags.
        require( $this->path . '/bp-compliments-templatetags.php' );
        // Functions related to handling user submitted data and actions.
        require( $this->path . '/bp-compliments-actions.php' );
        // Functions related to notification component.
        if ($bp_compliment_enable_notifications == 'yes') {
            require( $this->path . '/bp-compliments-notifications.php' );
        }
        // Functions related to activity component.
        if ($bp_compliment_enable_activity == 'yes') {
            require( $this->path . '/bp-compliments-activity.php' );
        }
        // Functions related to compliment forms.
        require( $this->path . '/bp-compliments-forms.php' );
        // Functions related to compliment settings.
        require( $this->path . '/bp-compliments-settings.php' );
        // Functions related to compliment types and icons.
        require( $this->path . '/bp-compliments-taxonomies.php' );
    }

    /**
     * Setup globals.
     *
     * @since 0.0.1
     * @package BuddyPress_Compliments
     *
     * @global object $bp BuddyPress instance.
     * @param array $args Not being used.
     */
    public function setup_globals( $args = array() ) {
        global $bp;

        // Set up the $globals array
        $globals = array(
            'notification_callback' => 'bp_compliments_format_notifications',
            'global_tables'         => array(
                'table_name' => BP_COMPLIMENTS_TABLE,
            )
        );

        // Let BP_Component::setup_globals() do its work.
        parent::setup_globals( $globals );

        // register other globals since BP isn't really flexible enough to add it
        // in the setup_globals() method
        //
        // would rather do away with this, but keeping it for backpat
        $bp->compliments->compliments = new stdClass;
        $bp->compliments->compliments->slug = constant( 'BP_COMPLIMENTS_SLUG' );

        // locally cache total count values for logged-in user
        if ( is_user_logged_in() ) {
            $bp->loggedin_user->total_compliment_counts = bp_compliments_total_counts( array(
                'user_id' => bp_loggedin_user_id()
            ) );
        }

        // locally cache total count values for displayed user
        if ( bp_is_user() && ( bp_loggedin_user_id() != bp_displayed_user_id() ) ) {
            $bp->displayed_user->total_compliment_counts = bp_compliments_total_counts( array(
                'user_id' => bp_displayed_user_id()
            ) );
        }

    }

    /**
     * Setup hooks.
     *
     * @since 0.0.1
     * @package BuddyPress_Compliments
     */
    public function setup_hooks() {
        // javascript hook
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 11 );
    }

    /**
     * Setup profile navigation.
     *
     * @since 0.0.1
     * @package BuddyPress_Compliments
     *
     * @global object $bp BuddyPress instance.
     * @param array $main_nav Not being used.
     * @param array $sub_nav Not being used.
     */
    public function setup_nav( $main_nav = array(), $sub_nav = array() ) {
        global $bp;

        /**
         * Functions hooked to this action will be processed before compliments navigation setup.
         *
         * @since 0.0.1
         * @package BuddyPress_Compliments
         */
        do_action( 'bp_compliments_before_setup_nav' );
        // Need to change the user ID, so if we're not on a member page, $counts variable is still calculated
        $user_id = bp_is_user() ? bp_displayed_user_id() : bp_loggedin_user_id();
        $counts  = bp_compliments_total_counts( array( 'user_id' => $user_id ) );

        $bp_compliment_can_see_others_comp_value = esc_attr( get_option('bp_compliment_can_see_others_comp'));
        $bp_compliment_can_see_others_comp = $bp_compliment_can_see_others_comp_value ? $bp_compliment_can_see_others_comp_value : 'yes';

        if ($bp_compliment_can_see_others_comp == 'members_choice') {
            $bp_compliment_can_see_your_comp_value = esc_attr( get_user_meta(bp_displayed_user_id(), 'bp_compliment_can_see_your_comp', true));
            $bp_compliment_can_see_others_comp = $bp_compliment_can_see_your_comp_value ? $bp_compliment_can_see_your_comp_value : 'yes';
        }
        
        if ($bp_compliment_can_see_others_comp == 'yes') {
            $show_for_displayed_user = true;
        } elseif ($bp_compliment_can_see_others_comp == 'members_only') {
            if (is_user_logged_in()) {
                $show_for_displayed_user = true;
            } else {
                $show_for_displayed_user = false;
            }
        } else {
            $show_for_displayed_user = false;
        }

        bp_core_new_nav_item( array(
            'name'                => BP_COMP_PLURAL_NAME." "."<span>".$counts['received']."</span>",
            'slug'                => $bp->compliments->compliments->slug,
            'position'            => $this->params['adminbar_myaccount_order'],
            'screen_function'     => 'bp_compliments_screen_compliments',
            'show_for_displayed_user' => $show_for_displayed_user,
            'default_subnav_slug' => 'compliments',
            'item_css_id'         => 'members-compliments'
        ) );

        /**
         * Functions hooked to this action will be processed after compliments navigation setup.
         *
         * @since 0.0.1
         * @package BuddyPress_Compliments
         */
        do_action( 'bp_compliments_after_setup_nav' );

    }


    /**
     * Enqueues the javascript.
     *
     * The JS is used to add AJAX functionality when clicking on the compliments button.
     *
     * @since 0.0.1
     * @package BuddyPress_Compliments
     */
    public function enqueue_scripts() {
        wp_enqueue_script( 'bp-compliments-js', constant( 'BP_COMPLIMENTS_URL' ) . 'js/bp-compliments.js', array( 'jquery' ) );
        wp_register_style( 'bp-compliments-css', constant( 'BP_COMPLIMENTS_URL' ) . 'css/bp-compliments.css' );
        wp_enqueue_style( 'bp-compliments-css' );
	    wp_enqueue_style( 'dashicons' );
    }

}

/**
 * Adds the Compliments component to BuddyPress.
 *
 * @since 0.0.1
 * @package BuddyPress_Compliments
 *
 * @global object $bp BuddyPress instance.
 */
function bp_compliments_setup_component() {
    global $bp;

    $bp->compliments = new BP_Compliments_Component;
}
add_action( 'bp_loaded', 'bp_compliments_setup_component' );