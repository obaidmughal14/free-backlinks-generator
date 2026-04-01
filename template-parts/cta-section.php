<?php
/**
 * Homepage CTA with email capture (frontend only — does not create account).
 *
 * @package Free_Backlinks_Generator
 */
?>
<section class="fbg-cta-band">
	<div class="fbg-container fbg-cta-band__inner">
		<h2><?php esc_html_e( 'Ready to Start Building Free Backlinks?', 'free-backlinks-generator' ); ?></h2>
		<p><?php esc_html_e( 'Join the community. Submit your first guest post. Watch your domain authority grow — all for free.', 'free-backlinks-generator' ); ?></p>
		<form class="fbg-cta-form" action="<?php echo esc_url( home_url( '/register/' ) ); ?>" method="get">
			<label class="screen-reader-text" for="fbg-cta-email"><?php esc_html_e( 'Email', 'free-backlinks-generator' ); ?></label>
			<input type="email" id="fbg-cta-email" name="email" placeholder="<?php esc_attr_e( 'Enter your email address…', 'free-backlinks-generator' ); ?>">
			<button type="submit" class="btn-dark"><?php esc_html_e( 'Create My Free Account', 'free-backlinks-generator' ); ?> →</button>
		</form>
		<p class="fbg-cta-foot"><?php esc_html_e( '✓ Free forever  ✓ No spam  ✓ Cancel anytime', 'free-backlinks-generator' ); ?></p>
	</div>
</section>
