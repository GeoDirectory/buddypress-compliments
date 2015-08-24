<?php
/**
 * Functions hooked to this action will be processed before displaying compliments page content.
 *
 * @since 0.0.1
 * @package BuddyPress_Compliments
 *
 * @global object $bp BuddyPress instance.
 */
do_action('bp_before_member_' . bp_current_action() . '_content'); ?>

<div class="bp-compliments-wrap">
    <?php
    $c_id = false;
    $count_args = array(
        'user_id' => bp_displayed_user_id()
    );
    $count_array = bp_compliments_total_counts($count_args);
    $total = (int)$count_array['received'];

    $comp_per_page_value = esc_attr( get_option('bp_comp_per_page'));
    $items_per_page = $comp_per_page_value ? (int) $comp_per_page_value : 5;

    $bp_compliment_can_see_others_comp_value = esc_attr( get_option('bp_compliment_can_see_others_comp'));
    $bp_compliment_can_see_others_comp = $bp_compliment_can_see_others_comp_value ? $bp_compliment_can_see_others_comp_value : 'yes';
    if (bp_displayed_user_id() == bp_loggedin_user_id()) {
        $bp_compliment_can_see_others_comp = 'yes';
    } elseif (current_user_can( 'manage_options' )) {
        $bp_compliment_can_see_others_comp = 'yes';
    }

    $page = isset($_GET['cpage']) ? abs((int)$_GET['cpage']) : 1;
    $offset = ($page * $items_per_page) - $items_per_page;
    $args = array(
        'offset' => $offset,
        'limit' => $items_per_page
    );
    if (isset($_GET['c_id'])) {
        $c_id = (int) strip_tags(esc_sql($_GET['c_id']));
        if ($c_id) {
            $args['c_id'] = $c_id;
        }

    }
    $compliments = bp_compliments_get_compliments($args);
    $start = $offset ? $offset : 1;
    $end = $offset + $items_per_page;
    $end = ($end > $total) ? $total : $end;

    if (isset($_GET['c_id'])) {
        foreach ($compliments as $comp) {
            $author_id = $comp->sender_id;
            if ($author_id == bp_loggedin_user_id()) {
                $bp_compliment_can_see_others_comp = 'yes';
            }
        }
    }

    if ($compliments && ($bp_compliment_can_see_others_comp == 'yes')) {
        ?>
        <div class="comp-user-content">
            <ul class="comp-user-ul">
                <?php
                foreach ($compliments as $comp) {
                    $t_id = $comp->term_id;
                    $term = get_term_by('id', $t_id, 'compliment');
                    $term_meta = get_option("taxonomy_$t_id");
                    ?>
                    <li>
                        <div class="comp-user-header">
        <span>
            <img style="height: 20px; width: 20px; vertical-align:middle"
                 src='<?php echo esc_attr($term_meta['compliments_icon']) ? esc_attr($term_meta['compliments_icon']) : ''; ?>'
                 class='preview-upload'/>
            <?php echo $term->name; ?>
        </span>
                            <em>
                                <?php echo date_i18n(get_option('date_format'), strtotime($comp->created_at)); ?>
                            </em>
                            <?php
                            global $bp;
                            $bp_compliment_can_delete_value = esc_attr( get_option('bp_compliment_can_delete'));
                            $bp_compliment_can_delete = $bp_compliment_can_delete_value ? $bp_compliment_can_delete_value : 'yes';
                            if (is_user_logged_in() && ($bp->loggedin_user->id == $bp->displayed_user->id) && ($bp_compliment_can_delete != 'no')) {
                                $receiver_url    = bp_core_get_userlink( $comp->receiver_id, false, true );
                                $compliment_url = $receiver_url . $bp->compliments->id . '/?c_id='.$comp->id.'&action=delete';
                                ?>
                                <a href="<?php echo $compliment_url; ?>" class="button item-button confirm" style="float: right;"><?php echo __('Delete', BP_COMP_TEXTDOMAIN); ?></a>
                            <?php } ?>
                        </div>
                        <div class="comp-user-msg-wrap">
                            <div class="comp-user-message">
                                <?php $author_id = $comp->sender_id; ?>
                                <div class="comp-user">
                                    <div class="comp-user-avatar">
                                        <?php
                                        $user = get_user_by('id', $author_id);
                                        $name = $user->display_name;
                                        $user_link = bp_core_get_user_domain($author_id);
                                        ?>
                                        <?php echo get_avatar($author_id, 60); ?>
                                        <div class="comp-username">
                                            <a href="<?php echo $user_link; ?>" class="url"><?php echo $name; ?></a>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                echo stripcslashes($comp->message); ?>
                            </div>
                        </div>
                    </li>
                <?php
                } ?>
            </ul>
        </div>
        <?php
        if (($total > $items_per_page) && !$c_id) { ?>
            <div id="pag-top" class="pagination">
                <div class="pag-count" id="member-dir-count-top">
                    <?php echo sprintf(_n('1 of 1', '%1$s to %2$s of %3$s', $total, BP_COMP_TEXTDOMAIN), $start, $end, $total); ?>
                </div>
                <div class="pagination-links">
                    <span class="bp-comp-pagination-text"><?php echo __('Go to Page', BP_COMP_TEXTDOMAIN) ?></span>
                    <?php
                    echo paginate_links(array(
                        'base' => esc_url(add_query_arg('cpage', '%#%')),
                        'format' => '',
                        'prev_next' => false,
                        'total' => ceil($total / $items_per_page),
                        'current' => $page
                    ));
                    ?>
                </div>
            </div>
        <?php }
    } elseif($bp_compliment_can_see_others_comp == 'no') {
        ?>
        <div id="message" class="bp-no-compliments info">
            <p><?php echo __('You don\'t have permission to access this page.', BP_COMP_TEXTDOMAIN); ?></p>
        </div>
    <?php
    } else {
        if (bp_displayed_user_id() == bp_loggedin_user_id()) {
            ?>
            <div id="message" class="bp-no-compliments info">
                <p><?php echo sprintf( __( 'Aw, you have no %1$s yet. To get some try sending %1$s to others.', BP_COMP_TEXTDOMAIN ), strtolower(BP_COMP_PLURAL_NAME) ); ?></p>
            </div>
        <?php
        } else {
            ?>
            <div id="message" class="bp-no-compliments info">
                <p><?php echo sprintf( __( 'Sorry, no %1$s just yet.', BP_COMP_TEXTDOMAIN ), strtolower(BP_COMP_PLURAL_NAME) ); ?></p>
            </div>
        <?php
        }
    }
    ?>
</div>

<?php
/**
 * Functions hooked to this action will be processed after displaying compliments page content.
 *
 * @since 0.0.1
 * @package BuddyPress_Compliments
 */
do_action('bp_after_member_' . bp_current_action() . '_content'); ?>