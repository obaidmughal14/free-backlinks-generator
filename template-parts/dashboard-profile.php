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
	<h2><?php esc_html_e( 'Profile', 'free-backlinks-generator' ); ?></h2>
	<div class="fbg-alert fbg-alert--success" id="fbg-profile-toast" role="status" hidden></div>
	<form id="fbg-profile-form" class="fbg-form">
		<div class="fbg-field">
			<label for="fbg-prof-name"><?php esc_html_e( 'Full Name', 'free-backlinks-generator' ); ?></label>
			<input type="text" id="fbg-prof-name" name="display_name" value="<?php echo esc_attr( $u->display_name ); ?>" required>
		</div>
		<div class="fbg-field">
			<label for="fbg-prof-email"><?php esc_html_e( 'Email', 'free-backlinks-generator' ); ?></label>
			<input type="email" id="fbg-prof-email" value="<?php echo esc_attr( $u->user_email ); ?>" disabled>
			<small><?php esc_html_e( 'Contact support to change email.', 'free-backlinks-generator' ); ?></small>
		</div>
		<div class="fbg-field">
			<label for="fbg-prof-url"><?php esc_html_e( 'Website URL', 'free-backlinks-generator' ); ?></label>
			<input type="url" id="fbg-prof-url" name="website_url" value="<?php echo esc_attr( $url ); ?>" placeholder="https://">
		</div>
		<div class="fbg-field">
			<label for="fbg-prof-bio"><?php esc_html_e( 'Bio', 'free-backlinks-generator' ); ?></label>
			<textarea id="fbg-prof-bio" name="bio" maxlength="280" rows="4"><?php echo esc_textarea( $bio ); ?></textarea>
			<span id="fbg-bio-count" aria-live="polite">0/280</span>
		</div>
		<div class="fbg-field">
			<label for="fbg-prof-niche"><?php esc_html_e( 'Your Niche', 'free-backlinks-generator' ); ?></label>
			<select id="fbg-prof-niche" name="niche">
				<?php foreach ( $niches as $slug => $label ) : ?>
					<option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $niche, $slug ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="fbg-field">
			<label for="fbg-prof-tw"><?php esc_html_e( 'Twitter', 'free-backlinks-generator' ); ?></label>
			<input type="text" id="fbg-prof-tw" name="twitter" value="<?php echo esc_attr( $tw ); ?>" placeholder="@handle">
		</div>
		<div class="fbg-field">
			<label for="fbg-prof-li"><?php esc_html_e( 'LinkedIn', 'free-backlinks-generator' ); ?></label>
			<input type="url" id="fbg-prof-li" name="linkedin" value="<?php echo esc_attr( $li ); ?>">
		</div>
		<button type="submit" class="btn-primary"><?php esc_html_e( 'Save Profile Changes', 'free-backlinks-generator' ); ?></button>
	</form>
</section>
