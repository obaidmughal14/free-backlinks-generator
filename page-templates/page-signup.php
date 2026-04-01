<?php
/**
 * Template Name: Register / Signup
 *
 * @package Free_Backlinks_Generator
 */

get_header();
$niches = fbg_niche_options();
?>
<div class="fbg-auth-split">
	<div class="fbg-auth-split__brand">
		<a class="fbg-auth-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/logo-white.svg' ) ); ?>" alt="<?php bloginfo( 'name' ); ?>" width="200" height="48">
		</a>
		<blockquote class="fbg-auth-quote"><?php esc_html_e( '“The easiest way to build real backlinks is to create real value. That’s what this community is built on.”', 'free-backlinks-generator' ); ?></blockquote>
		<ul class="fbg-auth-trust">
			<li><?php esc_html_e( '✓ Your first backlinks can go live within 24 hours', 'free-backlinks-generator' ); ?></li>
			<li><?php esc_html_e( '✓ 2,400+ active members in 40+ niches', 'free-backlinks-generator' ); ?></li>
			<li><?php esc_html_e( '✓ Every post reviewed for quality — no spam neighbors', 'free-backlinks-generator' ); ?></li>
		</ul>
		<p class="fbg-auth-foot"><?php esc_html_e( 'Already helping 2,400+ link builders', 'free-backlinks-generator' ); ?></p>
	</div>
	<div class="fbg-auth-split__form">
		<p class="fbg-auth-switch"><?php esc_html_e( 'Already have an account?', 'free-backlinks-generator' ); ?> <a href="<?php echo esc_url( home_url( '/login/' ) ); ?>"><?php esc_html_e( 'Log in', 'free-backlinks-generator' ); ?> →</a></p>
		<h2><?php esc_html_e( 'Create Your Free Account', 'free-backlinks-generator' ); ?></h2>
		<p class="fbg-auth-sub"><?php esc_html_e( 'Start getting free backlinks today.', 'free-backlinks-generator' ); ?></p>
		<div class="fbg-alert fbg-alert--error" id="fbg-register-alert" role="alert" hidden></div>
		<form id="fbg-register-form" class="fbg-form" novalidate>
			<div class="fbg-field">
				<label for="fbg-reg-name"><?php esc_html_e( 'Full Name', 'free-backlinks-generator' ); ?> *</label>
				<input type="text" id="fbg-reg-name" name="full_name" required autocomplete="name">
			</div>
			<div class="fbg-field">
				<label for="fbg-reg-email"><?php esc_html_e( 'Email Address', 'free-backlinks-generator' ); ?> *</label>
				<input type="email" id="fbg-reg-email" name="email" required autocomplete="email">
			</div>
			<div class="fbg-field">
				<label for="fbg-reg-url"><?php esc_html_e( 'Website URL', 'free-backlinks-generator' ); ?> *</label>
				<input type="url" id="fbg-reg-url" name="website_url" required placeholder="https://" autocomplete="url">
			</div>
			<div class="fbg-field">
				<label for="fbg-reg-pass"><?php esc_html_e( 'Password', 'free-backlinks-generator' ); ?> *</label>
				<input type="password" id="fbg-reg-pass" name="password" required autocomplete="new-password" minlength="8">
				<meter id="fbg-pass-meter" min="0" max="4" value="0" aria-label="<?php esc_attr_e( 'Password strength', 'free-backlinks-generator' ); ?>"></meter>
				<span class="fbg-pass-label" id="fbg-pass-label"><?php esc_html_e( 'Strength', 'free-backlinks-generator' ); ?></span>
			</div>
			<div class="fbg-field">
				<label for="fbg-reg-pass2"><?php esc_html_e( 'Confirm Password', 'free-backlinks-generator' ); ?> *</label>
				<input type="password" id="fbg-reg-pass2" name="confirm_password" required autocomplete="new-password">
			</div>
			<div class="fbg-field">
				<label for="fbg-reg-niche"><?php esc_html_e( 'Your Niche', 'free-backlinks-generator' ); ?> *</label>
				<select id="fbg-reg-niche" name="niche" required>
					<option value=""><?php esc_html_e( 'Select your primary niche', 'free-backlinks-generator' ); ?></option>
					<?php foreach ( $niches as $slug => $label ) : ?>
						<option value="<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="fbg-field fbg-field--check">
				<input type="checkbox" id="fbg-reg-terms" name="terms" value="1" required>
				<label for="fbg-reg-terms"><?php esc_html_e( 'I agree to the Terms of Service and Privacy Policy', 'free-backlinks-generator' ); ?> *</label>
			</div>
			<div class="fbg-field fbg-field--check">
				<input type="checkbox" id="fbg-reg-tips" name="tips" value="1">
				<label for="fbg-reg-tips"><?php esc_html_e( 'Send me tips on getting more backlinks (optional)', 'free-backlinks-generator' ); ?></label>
			</div>
			<button type="submit" class="btn-primary btn-block"><?php esc_html_e( 'Create My Free Account', 'free-backlinks-generator' ); ?> →</button>
		</form>
	</div>
</div>
<?php
get_footer();
