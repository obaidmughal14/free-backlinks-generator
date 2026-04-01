<?php
/**
 * Dashboard tab: settings.
 *
 * @package Free_Backlinks_Generator
 */
$uid = get_current_user_id();
?>
<section class="fbg-dash-panel" id="tab-settings" data-panel="settings" hidden tabindex="-1">
	<h2><?php esc_html_e( 'Settings', 'free-backlinks-generator' ); ?></h2>
	<div class="fbg-alert fbg-alert--success" id="fbg-settings-toast" role="status" hidden></div>
	<form id="fbg-settings-form" class="fbg-form">
		<fieldset>
			<legend><?php esc_html_e( 'Notification Preferences', 'free-backlinks-generator' ); ?></legend>
			<label class="fbg-field--check"><input type="checkbox" name="email_approved" value="1" <?php checked( '1', get_user_meta( $uid, '_fbg_pref_email_approved', true ) ); ?>> <?php esc_html_e( 'Email when my guest post is approved', 'free-backlinks-generator' ); ?></label>
			<label class="fbg-field--check"><input type="checkbox" name="email_rejected" value="1" <?php checked( '1', get_user_meta( $uid, '_fbg_pref_email_rejected', true ) ); ?>> <?php esc_html_e( 'Email when my guest post is rejected (with reviewer notes)', 'free-backlinks-generator' ); ?></label>
			<label class="fbg-field--check"><input type="checkbox" name="weekly_digest" value="1" <?php checked( '1', get_user_meta( $uid, '_fbg_pref_weekly_digest', true ) ); ?>> <?php esc_html_e( 'Weekly backlinks digest', 'free-backlinks-generator' ); ?></label>
			<label class="fbg-field--check"><input type="checkbox" name="community_tips" value="1" <?php checked( '1', get_user_meta( $uid, '_fbg_pref_community_tips', true ) ); ?>> <?php esc_html_e( 'Community tips', 'free-backlinks-generator' ); ?></label>
		</fieldset>
		<fieldset>
			<legend><?php esc_html_e( 'Privacy', 'free-backlinks-generator' ); ?></legend>
			<label class="fbg-field--check"><input type="checkbox" name="dir_public" value="1" <?php checked( '1', get_user_meta( $uid, '_fbg_pref_dir_public', true ) ); ?>> <?php esc_html_e( 'Show my profile in the public member directory', 'free-backlinks-generator' ); ?></label>
			<label class="fbg-field--check"><input type="checkbox" name="show_link_count" value="1" <?php checked( '1', get_user_meta( $uid, '_fbg_pref_show_link_count', true ) ); ?>> <?php esc_html_e( 'Show my backlink count on my public profile', 'free-backlinks-generator' ); ?></label>
			<label class="fbg-field--check"><input type="checkbox" name="show_website" value="1" <?php checked( '1', get_user_meta( $uid, '_fbg_pref_show_website', true ) ); ?>> <?php esc_html_e( 'Allow other members to see my website URL', 'free-backlinks-generator' ); ?></label>
		</fieldset>
		<button type="submit" class="btn-primary"><?php esc_html_e( 'Save Settings', 'free-backlinks-generator' ); ?></button>
	</form>
	<hr>
	<h3><?php esc_html_e( 'Account Actions', 'free-backlinks-generator' ); ?></h3>
	<button type="button" class="btn-ghost" id="fbg-toggle-pass"><?php esc_html_e( 'Change Password', 'free-backlinks-generator' ); ?> →</button>
	<div id="fbg-pass-panel" class="fbg-form" hidden>
		<div class="fbg-field"><label><?php esc_html_e( 'Current password', 'free-backlinks-generator' ); ?></label><input type="password" id="fbg-cur-pass"></div>
		<div class="fbg-field"><label><?php esc_html_e( 'New password', 'free-backlinks-generator' ); ?></label><input type="password" id="fbg-new-pass"></div>
		<div class="fbg-field"><label><?php esc_html_e( 'Confirm', 'free-backlinks-generator' ); ?></label><input type="password" id="fbg-new-pass2"></div>
		<button type="button" class="btn-primary" id="fbg-save-pass"><?php esc_html_e( 'Update password', 'free-backlinks-generator' ); ?></button>
	</div>
	<p><a class="btn-ghost" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'fbg_gdpr_export', '1', home_url( '/' ) ), 'fbg_gdpr' ) ); ?>"><?php esc_html_e( 'Download My Data', 'free-backlinks-generator' ); ?></a></p>
	<div class="fbg-danger">
		<h3><?php esc_html_e( 'Danger Zone', 'free-backlinks-generator' ); ?></h3>
		<button type="button" class="btn-danger" id="fbg-delete-open"><?php esc_html_e( 'Delete My Account', 'free-backlinks-generator' ); ?></button>
	</div>
	<div id="fbg-delete-modal" class="fbg-modal" hidden>
		<div class="fbg-modal__box">
			<p><?php esc_html_e( 'This will permanently delete your account and all your submitted posts. Type DELETE to confirm.', 'free-backlinks-generator' ); ?></p>
			<input type="text" id="fbg-delete-confirm" autocomplete="off" aria-label="<?php esc_attr_e( 'Type DELETE', 'free-backlinks-generator' ); ?>">
			<button type="button" class="btn-danger" id="fbg-delete-do"><?php esc_html_e( 'Confirm delete', 'free-backlinks-generator' ); ?></button>
			<button type="button" class="btn-ghost" id="fbg-delete-cancel"><?php esc_html_e( 'Cancel', 'free-backlinks-generator' ); ?></button>
		</div>
	</div>
</section>
