<?php
/**
 * Dashboard tab: settings.
 *
 * @package Free_Backlinks_Generator
 */
$uid = get_current_user_id();
?>
<section class="fbg-dash-panel" id="tab-settings" data-panel="settings" hidden tabindex="-1">
	<header class="fbg-dash-panel__head">
		<h2 class="fbg-dash-panel__title"><?php esc_html_e( 'Settings', 'free-backlinks-generator' ); ?></h2>
		<p class="fbg-dash-panel__lead"><?php esc_html_e( 'Choose how we reach you and what other members can see. Account security lives at the bottom.', 'free-backlinks-generator' ); ?></p>
	</header>

	<div class="fbg-alert fbg-alert--success" id="fbg-settings-toast" role="status" hidden></div>

	<form id="fbg-settings-form" class="fbg-dash-settings-form">
		<div class="fbg-dash-card">
			<h3 class="fbg-dash-card__title"><?php esc_html_e( 'Notifications', 'free-backlinks-generator' ); ?></h3>
			<p class="fbg-dash-card__sub"><?php esc_html_e( 'Email and product updates you want to receive.', 'free-backlinks-generator' ); ?></p>
			<div class="fbg-check-tiles">
				<label class="fbg-check-tile">
					<input type="checkbox" name="email_approved" value="1" <?php checked( '1', get_user_meta( $uid, '_fbg_pref_email_approved', true ) ); ?>>
					<span class="fbg-check-tile__text"><?php esc_html_e( 'When my guest post is approved', 'free-backlinks-generator' ); ?></span>
				</label>
				<label class="fbg-check-tile">
					<input type="checkbox" name="email_rejected" value="1" <?php checked( '1', get_user_meta( $uid, '_fbg_pref_email_rejected', true ) ); ?>>
					<span class="fbg-check-tile__text"><?php esc_html_e( 'When a post is rejected (with reviewer notes)', 'free-backlinks-generator' ); ?></span>
				</label>
				<label class="fbg-check-tile">
					<input type="checkbox" name="weekly_digest" value="1" <?php checked( '1', get_user_meta( $uid, '_fbg_pref_weekly_digest', true ) ); ?>>
					<span class="fbg-check-tile__text"><?php esc_html_e( 'Weekly backlinks digest', 'free-backlinks-generator' ); ?></span>
				</label>
				<label class="fbg-check-tile">
					<input type="checkbox" name="community_tips" value="1" <?php checked( '1', get_user_meta( $uid, '_fbg_pref_community_tips', true ) ); ?>>
					<span class="fbg-check-tile__text"><?php esc_html_e( 'Community tips & feature highlights', 'free-backlinks-generator' ); ?></span>
				</label>
			</div>
		</div>

		<div class="fbg-dash-card">
			<h3 class="fbg-dash-card__title"><?php esc_html_e( 'Privacy', 'free-backlinks-generator' ); ?></h3>
			<p class="fbg-dash-card__sub"><?php esc_html_e( 'Control your visibility in the community.', 'free-backlinks-generator' ); ?></p>
			<div class="fbg-check-tiles">
				<label class="fbg-check-tile">
					<input type="checkbox" name="dir_public" value="1" <?php checked( '1', get_user_meta( $uid, '_fbg_pref_dir_public', true ) ); ?>>
					<span class="fbg-check-tile__text"><?php esc_html_e( 'Show my profile in the public member directory', 'free-backlinks-generator' ); ?></span>
				</label>
				<label class="fbg-check-tile">
					<input type="checkbox" name="show_link_count" value="1" <?php checked( '1', get_user_meta( $uid, '_fbg_pref_show_link_count', true ) ); ?>>
					<span class="fbg-check-tile__text"><?php esc_html_e( 'Show my backlink count on my public profile', 'free-backlinks-generator' ); ?></span>
				</label>
				<label class="fbg-check-tile">
					<input type="checkbox" name="show_website" value="1" <?php checked( '1', get_user_meta( $uid, '_fbg_pref_show_website', true ) ); ?>>
					<span class="fbg-check-tile__text"><?php esc_html_e( 'Allow others to see my website URL', 'free-backlinks-generator' ); ?></span>
				</label>
			</div>
		</div>

		<div class="fbg-dash-form-actions">
			<button type="submit" class="btn-primary btn-primary--lg"><?php esc_html_e( 'Save settings', 'free-backlinks-generator' ); ?></button>
		</div>
	</form>

	<div class="fbg-dash-card fbg-dash-card--account">
		<h3 class="fbg-dash-card__title"><?php esc_html_e( 'Account & security', 'free-backlinks-generator' ); ?></h3>
		<div class="fbg-dash-account-actions">
			<button type="button" class="btn-ghost fbg-dash-account-btn" id="fbg-toggle-pass"><?php esc_html_e( 'Change password', 'free-backlinks-generator' ); ?> →</button>
			<a class="btn-ghost fbg-dash-account-btn" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'fbg_gdpr_export', '1', home_url( '/' ) ), 'fbg_gdpr' ) ); ?>"><?php esc_html_e( 'Download my data', 'free-backlinks-generator' ); ?></a>
		</div>
		<div id="fbg-pass-panel" class="fbg-dash-pass-panel" hidden>
			<div class="fbg-dash-profile-grid">
				<div class="fbg-field">
					<label for="fbg-cur-pass"><?php esc_html_e( 'Current password', 'free-backlinks-generator' ); ?></label>
					<input type="password" id="fbg-cur-pass" autocomplete="current-password">
				</div>
				<div class="fbg-field">
					<label for="fbg-new-pass"><?php esc_html_e( 'New password', 'free-backlinks-generator' ); ?></label>
					<input type="password" id="fbg-new-pass" autocomplete="new-password">
				</div>
				<div class="fbg-field">
					<label for="fbg-new-pass2"><?php esc_html_e( 'Confirm new password', 'free-backlinks-generator' ); ?></label>
					<input type="password" id="fbg-new-pass2" autocomplete="new-password">
				</div>
			</div>
			<button type="button" class="btn-primary" id="fbg-save-pass"><?php esc_html_e( 'Update password', 'free-backlinks-generator' ); ?></button>
		</div>
	</div>

	<div class="fbg-dash-card fbg-dash-card--danger">
		<h3 class="fbg-dash-card__title"><?php esc_html_e( 'Danger zone', 'free-backlinks-generator' ); ?></h3>
		<p class="fbg-dash-card__sub"><?php esc_html_e( 'Deleting your account removes your posts and cannot be undone.', 'free-backlinks-generator' ); ?></p>
		<button type="button" class="btn-danger" id="fbg-delete-open"><?php esc_html_e( 'Delete my account', 'free-backlinks-generator' ); ?></button>
	</div>

	<div id="fbg-delete-modal" class="fbg-modal" hidden>
		<div class="fbg-modal__box">
			<p><?php esc_html_e( 'This will permanently delete your account and all your submitted posts. Type DELETE to confirm.', 'free-backlinks-generator' ); ?></p>
			<input type="text" id="fbg-delete-confirm" autocomplete="off" aria-label="<?php esc_attr_e( 'Type DELETE', 'free-backlinks-generator' ); ?>">
			<div class="fbg-modal__actions">
				<button type="button" class="btn-danger" id="fbg-delete-do"><?php esc_html_e( 'Confirm delete', 'free-backlinks-generator' ); ?></button>
				<button type="button" class="btn-ghost" id="fbg-delete-cancel"><?php esc_html_e( 'Cancel', 'free-backlinks-generator' ); ?></button>
			</div>
		</div>
	</div>
</section>
