<?php
function bp_compliments_modal_form($pid = 0, $receiver_id = 0) {
    ?>
    <div class="comp-modal">
        <div class="comp-modal-content-wrap">
            <div class="comp-modal-title">
                <h2><?php echo __( 'Choose Your Compliment Type:', BP_COMP_TEXTDOMAIN ); ?></h2>
            </div>
            <div class="comp-modal-content">
                <form action="" method="post">
                    <?php
                    $args = array(
                        'hide_empty' => false,
                        'orderby'  => 'id'
                    );
                    $terms = get_terms( 'compliment', $args );
                    if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
                        echo '<ul class="comp-form-ul">';
                        $count = 0;
                        foreach ( $terms as $term ) {
                            $count++;
                            $t_id = $term->term_id;
                            $term_meta = get_option( "taxonomy_$t_id" );
                            ?>
                            <li>
                                <label>
                                    <input type="radio" name="term_id" value="<?php echo $term->term_id; ?>" <?php if ($count == 1) { echo 'checked="checked"'; } ?>>
                                <span>
                                    <img style="height: 20px; width: 20px; vertical-align:middle" src='<?php echo esc_attr( $term_meta['compliments_icon'] ) ? esc_attr( $term_meta['compliments_icon'] ) : ''; ?>' class='preview-upload'/>
                                    <?php echo $term->name; ?>
                                </span>
                                </label>
                            </li>
                        <?php
                        }
                        echo '</ul>';

                        ?>
                        <textarea name="message" maxchar="1000"></textarea>
                        <input type="hidden" name="post_id" value="<?php echo $pid; ?>"/>
                        <input type="hidden" name="receiver_id" value="<?php echo $receiver_id; ?>"/>
                        <?php wp_nonce_field( 'handle_compliments_form_data','handle_compliments_nonce' ); ?>
                        <div class="bp-comp-pop-buttons">
                            <button type="submit" class="comp-submit-btn" name="comp-modal-form" value="submit"><?php echo __( 'Send', BP_COMP_TEXTDOMAIN ); ?></button>
                            <a class="bp-comp-cancel" href="#"><?php echo __( 'Cancel', BP_COMP_TEXTDOMAIN ); ?></a>
                        </div>
                        <script type="text/javascript">
                            jQuery(document).ready(function() {
                                jQuery('a.bp-comp-cancel').click(function (e) {
                                    e.preventDefault();
                                    var mod_shadow = jQuery('#bp_compliments_modal_shadow');
                                    var container = jQuery('.comp-modal');
                                    container.hide();
                                    container.replaceWith("<div class='comp-modal' style='display: none;'><div class='comp-modal-content-wrap'><div class='comp-modal-title comp-loading-icon'><div class='bp-loading-icon'></div></div></div></div>");
                                    mod_shadow.hide();
                                });
                            });
                        </script>
                    <?php
                    }
                    ?>
                </form>
            </div>
        </div>
    </div>
<?php
}

function bp_compliments_modal_ajax()
{
    check_ajax_referer('bp-compliments-nonce', 'bp_compliments_nonce');
    bp_compliments_modal_form();
    wp_die();
}

function bp_compliments_modal_shadow(){ ?>
    <div id="bp_compliments_modal_shadow" style="display: none;"></div>
<?php }
add_action('wp_footer', 'bp_compliments_modal_shadow');

//Ajax functions
add_action('wp_ajax_bp_compliments_modal_ajax', 'bp_compliments_modal_ajax');

//Javascript
add_action('bp_after_member_home_content', 'bp_compliments_js');
function bp_compliments_js() {
    $ajax_nonce = wp_create_nonce("bp-compliments-nonce");
    ?>
    <div class="comp-modal" style="display: none;">
        <div class="comp-modal-content-wrap">
            <div class="comp-modal-title comp-loading-icon">
                <div class="bp-loading-icon"></div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        jQuery(document).ready(function() {
            jQuery('a.compliments-popup').click(function (e) {
                e.preventDefault();
                var mod_shadow = jQuery('#bp_compliments_modal_shadow');
                var container = jQuery('.comp-modal');
                mod_shadow.show();
                container.show();
                var data = {
                    'action': 'bp_compliments_modal_ajax',
                    'bp_compliments_nonce': '<?php echo $ajax_nonce; ?>'
                };

                jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', data, function (response) {
                    container.replaceWith(response);
                });
            });
        });
    </script>
<?php
}