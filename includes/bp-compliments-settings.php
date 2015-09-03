<?php
add_action('admin_menu', 'register_compliments_menu_page');

/**
 * Register Compliments menu below Settings menu.
 *
 * @since 0.0.1
 * @package BuddyPress_Compliments
 */
function register_compliments_menu_page() {
	add_menu_page(
		BP_COMP_PLURAL_NAME,
		BP_COMP_PLURAL_NAME,
		'manage_options',
		'bp-compliment-settings',
		'bp_compliments_settings_page',
		plugins_url( 'buddypress-compliments/images/smiley-icon.png' ),
		85
	);
}

add_action( 'admin_init', 'bp_compliments_register_settings' );
function bp_compliments_register_settings() {
	register_setting( 'bp-compliment-settings', 'bp_compliment_singular_name' );
	register_setting( 'bp-compliment-settings', 'bp_compliment_plural_name' );
	register_setting( 'bp-compliment-settings', 'bp_compliment_slug' );
	register_setting( 'bp-compliment-settings', 'bp_compliment_can_see_others_comp' );
	register_setting( 'bp-compliment-settings', 'bp_compliment_can_delete' );
	register_setting( 'bp-compliment-settings', 'bp_compliment_enable_activity' );
	register_setting( 'bp-compliment-settings', 'bp_compliment_enable_notifications' );
	register_setting( 'bp-compliment-settings', 'bp_comp_per_page' );
	register_setting( 'bp-compliment-settings', 'bp_comp_custom_css' );
}

function bp_compliments_settings_page() {
	?>
	<div class="wrap">
		<h2><?php echo sprintf( __( 'BuddyPress %s - Settings', 'bp-compliments' ), BP_COMP_PLURAL_NAME ); ?></h2>
		<form method="post" action="options.php">
			<?php settings_fields( 'bp-compliment-settings' ); ?>
			<?php do_settings_sections( 'bp-compliment-settings' );

			$bp_compliment_can_see_others_comp_value = esc_attr( get_option('bp_compliment_can_see_others_comp'));
			$bp_compliment_can_see_others_comp = $bp_compliment_can_see_others_comp_value ? $bp_compliment_can_see_others_comp_value : 'yes';

			$bp_compliment_can_delete_value = esc_attr( get_option('bp_compliment_can_delete'));
			$bp_compliment_can_delete = $bp_compliment_can_delete_value ? $bp_compliment_can_delete_value : 'yes';

			$bp_compliment_enable_activity_value = esc_attr( get_option('bp_compliment_enable_activity'));
			$bp_compliment_enable_activity = $bp_compliment_enable_activity_value ? $bp_compliment_enable_activity_value : 'yes';

			$bp_compliment_enable_notifications_value = esc_attr( get_option('bp_compliment_enable_notifications'));
			$bp_compliment_enable_notifications = $bp_compliment_enable_notifications_value ? $bp_compliment_enable_notifications_value : 'yes';

			$comp_per_page_value = esc_attr( get_option('bp_comp_per_page'));
			$comp_per_page = $comp_per_page_value ? (int) $comp_per_page_value : 5;

			$comp_custom_css_value = esc_attr( get_option('bp_comp_custom_css'));
			$comp_custom_css = $comp_custom_css_value ? $comp_custom_css_value : '';
			?>
			<table class="widefat fixed" style="padding:10px;margin-top: 10px;">
				<tr valign="top">
					<th scope="row"><?php echo __( 'Singlular name ( e.g. Gift. Default: Compliment )', 'bp-compliments' ); ?></th>
					<td><input type="text" class="widefat" name="bp_compliment_singular_name" value="<?php echo BP_COMP_SINGULAR_NAME; ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php echo __( 'Plural name ( e.g. Gifts. Default: Compliments )', 'bp-compliments' ); ?></th>
					<td><input type="text" class="widefat" name="bp_compliment_plural_name" value="<?php echo BP_COMP_PLURAL_NAME; ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php echo __( 'Slug ( e.g. gifts. Default: compliments. must be lowercase )', 'bp-compliments' ); ?></th>
					<td><input type="text" class="widefat" name="bp_compliment_slug" value="<?php echo BP_COMPLIMENTS_SLUG; ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php echo sprintf( __( 'Members can see other members %s page?', 'bp-compliments' ), strtolower(BP_COMP_SINGULAR_NAME) ); ?></th>
					<td>
						<select id="bp_compliment_can_see_others_comp" name="bp_compliment_can_see_others_comp">
							<option value="yes" <?php selected( $bp_compliment_can_see_others_comp, 'yes' ); ?>><?php echo __( 'Yes', 'bp-compliments' ); ?></option>
							<option value="no" <?php selected( $bp_compliment_can_see_others_comp, 'no' ); ?>><?php echo __( 'No', 'bp-compliments' ); ?></option>
						</select>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php echo sprintf( __( 'Members can delete %s received?', 'bp-compliments' ), strtolower(BP_COMP_PLURAL_NAME) ); ?></th>
					<td>
						<select id="bp_compliment_can_delete" name="bp_compliment_can_delete">
							<option value="yes" <?php selected( $bp_compliment_can_delete, 'yes' ); ?>><?php echo __( 'Yes', 'bp-compliments' ); ?></option>
							<option value="no" <?php selected( $bp_compliment_can_delete, 'no' ); ?>><?php echo __( 'No', 'bp-compliments' ); ?></option>
						</select>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php echo sprintf( __( 'Enable activity component for %s ?', 'bp-compliments' ), strtolower(BP_COMP_PLURAL_NAME) ); ?></th>
					<td>
						<select id="bp_compliment_enable_activity" name="bp_compliment_enable_activity">
							<option value="yes" <?php selected( $bp_compliment_enable_activity, 'yes' ); ?>><?php echo __( 'Yes', 'bp-compliments' ); ?></option>
							<option value="no" <?php selected( $bp_compliment_enable_activity, 'no' ); ?>><?php echo __( 'No', 'bp-compliments' ); ?></option>
						</select>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php echo sprintf( __( 'Enable notification component for %s ?', 'bp-compliments' ), strtolower(BP_COMP_PLURAL_NAME) ); ?></th>
					<td>
						<select id="bp_compliment_enable_notifications" name="bp_compliment_enable_notifications">
							<option value="yes" <?php selected( $bp_compliment_enable_notifications, 'yes' ); ?>><?php echo __( 'Yes', 'bp-compliments' ); ?></option>
							<option value="no" <?php selected( $bp_compliment_enable_notifications, 'no' ); ?>><?php echo __( 'No', 'bp-compliments' ); ?></option>
						</select>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php echo sprintf( __( 'Number of %s to display per page?', 'bp-compliments' ), BP_COMP_PLURAL_NAME ); ?></th>
					<td><input type="number" class="widefat" name="bp_comp_per_page" value="<?php echo $comp_per_page; ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php echo __( 'Custom CSS styles', 'bp-compliments' ); ?></th>
					<td><textarea class="widefat" rows="5" name="bp_comp_custom_css"><?php echo $comp_custom_css; ?></textarea></td>
				</tr>
				<tr valign="top">
					<th></th>
					<td><?php submit_button(null, 'primary','submit',false); ?></td>
				</tr>
			</table>

		</form>
	</div>
<?php }