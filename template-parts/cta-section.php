<?php
/**
 * Homepage CTA with email capture (frontend only — does not create account).
 *
 * @package Free_Backlinks_Generator
 */
?>
<section class="fbg-cta-band">
	<div class="fbg-container fbg-cta-band__inner">
		<?php if ( is_user_logged_in() ) : ?>
			<h2><?php esc_html_e( 'Keep growing your backlinks', 'free-backlinks-generator' ); ?></h2>
			<p><?php esc_html_e( 'Read other members’ posts to unlock more guest slots, or open your dashboard to manage submissions.', 'free-backlinks-generator' ); ?></p>
			<div class="fbg-cta-form fbg-cta-form--logged-in">
				<a class="btn-dark" href="<?php echo esc_url( get_post_type_archive_link( 'fbg_post' ) ); ?>"><?php esc_html_e( 'Browse posts', 'free-backlinks-generator' ); ?> →</a>
				<a class="btn-ghost btn-ghost--on-dark" href="<?php echo esc_url( home_url( '/dashboard/' ) ); ?>"><?php esc_html_e( 'Dashboard', 'free-backlinks-generator' ); ?></a>
			</div>
			<p class="fbg-cta-foot"><?php esc_html_e( 'New members start with one guest post. Every two posts you read (2+ min each) unlock one more.', 'free-backlinks-generator' ); ?></p>
		<?php else : ?>
			<h2><?php esc_html_e( 'Ready to Start Building Free Backlinks?', 'free-backlinks-generator' ); ?></h2>
			<p><?php esc_html_e( 'Join the community. Submit your first guest post. Watch your domain authority grow — all for free.', 'free-backlinks-generator' ); ?></p>
			<form class="fbg-cta-form" action="<?php echo esc_url( home_url( '/register/' ) ); ?>" method="get">
				<label class="screen-reader-text" for="fbg-cta-email"><?php esc_html_e( 'Email', 'free-backlinks-generator' ); ?></label>
				<input type="email" id="fbg-cta-email" name="email" placeholder="<?php esc_attr_e( 'Enter your email address…', 'free-backlinks-generator' ); ?>">
				<button type="submit" class="btn-dark"><?php esc_html_e( 'Create My Free Account', 'free-backlinks-generator' ); ?> →</button>
			</form>
			<p class="fbg-cta-foot"><?php esc_html_e( '✓ Free forever  ✓ No spam  ✓ Cancel anytime', 'free-backlinks-generator' ); ?></p>
		<?php endif; ?>
	</div>
</section>
