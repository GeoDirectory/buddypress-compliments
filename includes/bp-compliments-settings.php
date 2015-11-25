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
	register_setting( 'bp-compliment-settings', 'bp_compliment_enable_categories' );
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

			$bp_compliment_enable_categories_value = esc_attr( get_option('bp_compliment_enable_categories'));
			$bp_compliment_enable_categories = $bp_compliment_enable_categories_value ? $bp_compliment_enable_categories_value : 'no';

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
					<th scope="row"><?php echo sprintf( __( 'Who can see other members %s page?', 'bp-compliments' ), strtolower(BP_COMP_SINGULAR_NAME) ); ?></th>
					<td>
						<select id="bp_compliment_can_see_others_comp" name="bp_compliment_can_see_others_comp">
							<option value="yes" <?php selected( $bp_compliment_can_see_others_comp, 'yes' ); ?>><?php echo __( 'Anybody', 'bp-compliments' ); ?></option>
							<option value="no" <?php selected( $bp_compliment_can_see_others_comp, 'no' ); ?>><?php echo __( 'Nobody', 'bp-compliments' ); ?></option>
							<option value="members_only" <?php selected( $bp_compliment_can_see_others_comp, 'members_only' ); ?>><?php echo __( 'Members Only', 'bp-compliments' ); ?></option>
							<option value="members_choice" <?php selected( $bp_compliment_can_see_others_comp, 'members_choice' ); ?>><?php echo __( 'Let members take care of this setting', 'bp-compliments' ); ?></option>
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
					<th scope="row"><?php echo sprintf( __( 'Enable categories for %s ?', 'bp-compliments' ), strtolower(BP_COMP_PLURAL_NAME) ); ?></th>
					<td>
						<select id="bp_compliment_enable_categories" name="bp_compliment_enable_categories">
							<option value="yes" <?php selected( $bp_compliment_enable_categories, 'yes' ); ?>><?php echo __( 'Yes', 'bp-compliments' ); ?></option>
							<option value="no" <?php selected( $bp_compliment_enable_categories, 'no' ); ?>><?php echo __( 'No', 'bp-compliments' ); ?></option>
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

//BuddyPress user settings
function bp_comp_settings_submenu() {
	global $bp;

	$bp_compliment_can_see_others_comp_value = esc_attr( get_option('bp_compliment_can_see_others_comp'));
	$bp_compliment_can_see_others_comp = $bp_compliment_can_see_others_comp_value ? $bp_compliment_can_see_others_comp_value : 'yes';

	if ($bp_compliment_can_see_others_comp == 'members_choice') {
		if (!bp_is_active('settings')) {
			return;
		}

		if (bp_displayed_user_domain()) {
			$user_domain = bp_displayed_user_domain();
		} elseif (bp_loggedin_user_domain()) {
			$user_domain = bp_loggedin_user_domain();
		} else {
			$user_domain = null;
		}

		// Get the settings slug
		$settings_slug = bp_get_settings_slug();

		bp_core_new_subnav_item(array(
				'name' => __('Compliments', 'bp-compliments'),
				'slug' => 'compliments',
				'parent_url' => trailingslashit($user_domain . $settings_slug),
				'parent_slug' => $settings_slug,
				'screen_function' => 'bp_comp_settings_submenu_page_content',
				'position' => 20,
				'user_has_access' => bp_core_can_edit_settings()
		));
	}

}
add_action('bp_setup_nav', 'bp_comp_settings_submenu', 16);

function bp_comp_settings_submenu_page_content() {
	//add title and content here - last is to call the members plugin.php template
	//add_action( 'bp_template_title', 'bp_comp_settings_submenu_page_show_screen_title' );
	add_action( 'bp_template_content', 'bp_comp_settings_submenu_page_show_screen_content' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

//function bp_comp_settings_submenu_page_show_screen_title() {
//	echo __('Compliments Settings');
//}

function bp_comp_settings_submenu_page_show_screen_content() {
	if (bp_displayed_user_id()) {
		$user_id = bp_displayed_user_id();
	} elseif (bp_loggedin_user_id()) {
		$user_id = bp_loggedin_user_id();
	} else {
		$user_id = null;
	}

	if (isset( $_POST['bp-comp-settings-submit'] )) {
		if (! isset( $_POST['bp-compl-settings-field'] ) || ! wp_verify_nonce( $_POST['bp-compl-settings-field'], 'bp_compl_settings_action' )) {
			?>
			<div id="message" class="error">
				<p>
					<?php echo __('There was an error with your form. Please contact administrator.', 'bp-compliments'); ?>
				</p>
			</div>
			<?php
		} else {
			// process form data
			$bp_compliment_can_see_your_comp_form_value = $_POST['bp_compliment_can_see_your_comp'];
			update_user_meta($user_id, 'bp_compliment_can_see_your_comp', $bp_compliment_can_see_your_comp_form_value);
			?>
			<div id="message" class="updated">
				<p>
					<?php echo __('Setting updated successfully.', 'bp-compliments'); ?>
				</p>
			</div>
			<?php
		}
	}

	$bp_compliment_can_see_your_comp_value = esc_attr( get_user_meta($user_id, 'bp_compliment_can_see_your_comp', true));
	$bp_compliment_can_see_your_comp = $bp_compliment_can_see_your_comp_value ? $bp_compliment_can_see_your_comp_value : 'yes';
	?>
	<form method="post" class="standard-form">
		<table class="profile-settings">
			<thead>
			<tr>
				<th class="title" colspan="2"><?php echo __('Compliments Settings', 'bp-compliments'); ?></th>
			</tr>
			</thead>
			<tbody>
			<tr>
				<td class="field-name"><label><?php echo __('Who can see your compliment page?', 'bp-compliments'); ?></label></td>
				<td class="field-visibility">
					<select id="bp_compliment_can_see_your_comp" class="bp-xprofile-visibility" name="bp_compliment_can_see_your_comp">
						<option value="yes" <?php selected( $bp_compliment_can_see_your_comp, 'yes' ); ?>><?php echo __( 'Anybody', 'bp-compliments' ); ?></option>
						<option value="no" <?php selected( $bp_compliment_can_see_your_comp, 'no' ); ?>><?php echo __( 'Nobody', 'bp-compliments' ); ?></option>
						<option value="members_only" <?php selected( $bp_compliment_can_see_your_comp, 'members_only' ); ?>><?php echo __( 'Members Only', 'bp-compliments' ); ?></option>
					</select>
				</td>
			</tr>
			</tbody>
		</table>
		<div class="submit">
			<?php wp_nonce_field( 'bp_compl_settings_action', 'bp-compl-settings-field' ); ?>
			<input type="submit" name="bp-comp-settings-submit" value="Save Settings" class="auto">
		</div>
	</form>
<?php
}