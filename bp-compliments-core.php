<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class BP_Compliments_Component extends BP_Component {

    public function __construct() {
        global $bp;

        // setup misc parameters
        $this->params = array(
            'adminbar_myaccount_order' => apply_filters( 'bp_compliments_nav_position', 71 )
        );

        // let's start the show!
        parent::start(
            'compliments',
            __( 'Compliments', BP_COMP_TEXTDOMAIN ),
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
     * Includes.
     */
    public function includes( $includes = array() ) {
        require( $this->path . '/bp-compliments-classes.php' );
        require( $this->path . '/bp-compliments-functions.php' );
        require( $this->path . '/bp-compliments-taxonomies.php' );
        require( $this->path . '/bp-compliments-screens.php' );
        require( $this->path . '/bp-compliments-templatetags.php' );
        require( $this->path . '/bp-compliments-actions.php' );
        require( $this->path . '/bp-compliments-forms.php' );
    }

    /**
     * Setup globals.
     *
     * @global object $bp BuddyPress instance
     */
    public function setup_globals( $args = array() ) {
        global $bp;

        if ( ! defined( 'BP_COMPLIMENTS_SLUG' ) )
            define( 'BP_COMPLIMENTS_SLUG', 'compliments' );

        // Set up the $globals array
        $globals = array(
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
     */
    public function setup_hooks() {
        // javascript hook
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 11 );
    }

    /**
     * Setup profile / BuddyBar navigation
     */
    public function setup_nav( $main_nav = array(), $sub_nav = array() ) {
        global $bp;

        // Need to change the user ID, so if we're not on a member page, $counts variable is still calculated
        $user_id = bp_is_user() ? bp_displayed_user_id() : bp_loggedin_user_id();
        $counts  = bp_compliments_total_counts( array( 'user_id' => $user_id ) );
        
        bp_core_new_nav_item( array(
            'name'                => sprintf( __( 'Compliments <span>%d</span>', BP_COMP_TEXTDOMAIN ), $counts['senders'] ),
            'slug'                => $bp->compliments->compliments->slug,
            'position'            => $this->params['adminbar_myaccount_order'],
            'screen_function'     => 'bp_compliments_screen_compliments',
            'default_subnav_slug' => 'compliments',
            'item_css_id'         => 'members-compliments'
        ) );

        do_action( 'bp_compliments_setup_nav' );

    }


    /**
     * Enqueues the javascript.
     *
     * The JS is used to add AJAX functionality when clicking on the compliments button.
     */
    public function enqueue_scripts() {
        // Do not enqueue if no user is logged in
        if ( ! is_user_logged_in() ) {
            return;
        }

        // Do not enqueue on multisite if not on multiblog and not on root blog
        if( ! bp_is_multiblog_mode() && ! bp_is_root_blog() ) {
            return;
        }

        wp_enqueue_script( 'bp-compliments-js', constant( 'BP_COMPLIMENTS_URL' ) . 'js/bp-compliments.js', array( 'jquery' ) );
        wp_register_style( 'bp-compliments-css', constant( 'BP_COMPLIMENTS_URL' ) . 'css/bp-compliments.css' );
        wp_enqueue_style( 'bp-compliments-css' );
    }

}

function bp_compliments_setup_component() {
    global $bp;

    $bp->compliments = new BP_Compliments_Component;
}
add_action( 'bp_loaded', 'bp_compliments_setup_component' );