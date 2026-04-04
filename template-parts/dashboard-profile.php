<?php
/**
 * Dashboard tab: profile.
 *
 * @package Free_Backlinks_Generator
 */
$u      = wp_get_current_user();
$uid    = $u->ID;
$bio    = get_user_meta( $uid, 'description', true );
$url    = get_user_meta( $uid, '_fbg_website_url', true );
$niche  = get_user_meta( $uid, '_fbg_niche', true );
$tw     = get_user_meta( $uid, '_fbg_twitter', true );
$li     = get_user_meta( $uid, '_fbg_linkedin', true );
$niches = fbg_niche_options();
?>
<section class="fbg-dash-panel" id="tab-profile" data-panel="profile" hidden tabindex="-1">
	<header class="fbg-dash-panel__head">
		<h2 class="fbg-dash-panel__title"><?php esc_html_e( 'Profile', 'free-backlinks-generator' ); ?></h2>
		<p class="fbg-dash-panel__lead"><?php esc_html_e( 'This information appears on your public author card and helps other members discover you.', 'free-backlinks-generator' ); ?></p>
	</header>

	<div class="fbg-alert fbg-alert--success" id="fbg-profile-toast" role="status" hidden></div>

	<form id="fbg-profile-form" class="fbg-dash-profile-form">
		<div class="fbg-dash-card fbg-dash-card--profile">
			<h3 class="fbg-dash-card__title"><?php esc_html_e( 'Account', 'free-backlinks-generator' ); ?></h3>
			<div class="fbg-dash-profile-grid">
				<div class="fbg-field">
					<label for="fbg-prof-name"><?php esc_html_e( 'Full name', 'free-backlinks-generator' ); ?></label>
					<input type="text" id="fbg-prof-name" name="display_name" value="<?php echo esc_attr( $u->display_name ); ?>" required autocomplete="name">
				</div>
				<div class="fbg-field">
					<label for="fbg-prof-email"><?php esc_html_e( 'Email', 'free-backlinks-generator' ); ?></label>
					<input type="email" id="fbg-prof-email" value="<?php echo esc_attr( $u->user_email ); ?>" disabled autocomplete="email">
					<small><?php esc_html_e( 'Contact support to change your email.', 'free-backlinks-generator' ); ?></small>
				</div>
			</div>
		</div>

		<div class="fbg-dash-card fbg-dash-card--profile">
			<h3 class="fbg-dash-card__title"><?php esc_html_e( 'Presence', 'free-backlinks-generator' ); ?></h3>
			<div class="fbg-dash-profile-grid">
				<div class="fbg-field fbg-field--full">
					<label for="fbg-prof-url"><?php esc_html_e( 'Website URL', 'free-backlinks-generator' ); ?></label>
					<input type="url" id="fbg-prof-url" name="website_url" value="<?php echo esc_attr( $url ); ?>" placeholder="https://" autocomplete="url">
				</div>
				<div class="fbg-field fbg-field--full">
					<label for="fbg-prof-bio"><?php esc_html_e( 'Bio', 'free-backlinks-generator' ); ?></label>
					<textarea id="fbg-prof-bio" name="bio" maxlength="280" rows="4" class="fbg-dash-textarea"><?php echo esc_textarea( $bio ); ?></textarea>
					<div class="fbg-dash-bio-meta">
						<span id="fbg-bio-count" class="fbg-char-count" aria-live="polite">0/280</span>
					</div>
				</div>
				<div class="fbg-field">
					<label for="fbg-prof-niche"><?php esc_html_e( 'Your niche', 'free-backlinks-generator' ); ?></label>
					<select id="fbg-prof-niche" name="niche">
						<?php foreach ( $niches as $slug => $label ) : ?>
							<option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $niche, $slug ); ?>><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="fbg-field">
					<label for="fbg-prof-tw"><?php esc_html_e( 'X (Twitter)', 'free-backlinks-generator' ); ?></label>
					<input type="text" id="fbg-prof-tw" name="twitter" value="<?php echo esc_attr( $tw ); ?>" placeholder="@handle" autocomplete="username">
				</div>
				<div class="fbg-field fbg-field--full">
					<label for="fbg-prof-li"><?php esc_html_e( 'LinkedIn', 'free-backlinks-generator' ); ?></label>
					<input type="url" id="fbg-prof-li" name="linkedin" value="<?php echo esc_attr( $li ); ?>" placeholder="https://linkedin.com/in/…" autocomplete="url">
				</div>
			</div>
		</div>

		<div class="fbg-dash-form-actions">
			<button type="submit" class="btn-primary btn-primary--lg"><?php esc_html_e( 'Save profile', 'free-backlinks-generator' ); ?></button>
		</div>
	</form>
</section>
