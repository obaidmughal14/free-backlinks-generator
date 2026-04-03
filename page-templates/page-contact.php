<?php
/**
 * Template Name: Contact
 *
 * @package Free_Backlinks_Generator
 */

get_header();
$mail = antispambot( get_option( 'admin_email' ) );
$ill  = get_theme_file_uri( 'assets/images/icon-posts.svg' );
?>
<main id="main-content" class="fbg-main">
	<section class="fbg-mkt-hero" aria-labelledby="fbg-contact-title">
		<div class="fbg-container">
			<h1 id="fbg-contact-title"><?php esc_html_e( 'Contact us', 'free-backlinks-generator' ); ?></h1>
			<p class="fbg-mkt-hero__lead">
				<?php esc_html_e( 'Questions about your account, submissions, partnerships, or press? We read every message and usually reply within two business days.', 'free-backlinks-generator' ); ?>
			</p>
		</div>
	</section>

	<section class="fbg-mkt-section fbg-container">
		<div class="fbg-mkt-grid fbg-mkt-grid--2">
			<div class="fbg-mkt-card fbg-mkt-card--contact">
				<div class="fbg-mkt-card__icon" aria-hidden="true"><img src="<?php echo esc_url( $ill ); ?>" width="44" height="44" alt=""></div>
				<h2><?php esc_html_e( 'Email', 'free-backlinks-generator' ); ?></h2>
				<p><?php esc_html_e( 'Best for support, moderation appeals, and general inquiries.', 'free-backlinks-generator' ); ?></p>
				<p><a class="fbg-mkt-inline-link" href="mailto:<?php echo esc_attr( $mail ); ?>"><?php echo esc_html( $mail ); ?></a></p>
			</div>
			<div class="fbg-mkt-card fbg-mkt-card--contact">
				<div class="fbg-mkt-card__icon" aria-hidden="true">⏱</div>
				<h2><?php esc_html_e( 'Response time', 'free-backlinks-generator' ); ?></h2>
				<p><?php esc_html_e( 'Typical reply: 1–2 business days. Complex moderation reviews may take longer — you will see status updates in your dashboard when possible.', 'free-backlinks-generator' ); ?></p>
			</div>
			<div class="fbg-mkt-card fbg-mkt-card--contact">
				<div class="fbg-mkt-card__icon" aria-hidden="true">🛡</div>
				<h2><?php esc_html_e( 'Abuse & safety', 'free-backlinks-generator' ); ?></h2>
				<p><?php esc_html_e( 'Report spam, harassment, or policy violations via email with links and screenshots. We investigate and may suspend accounts that threaten the community.', 'free-backlinks-generator' ); ?></p>
			</div>
			<div class="fbg-mkt-card fbg-mkt-card--contact">
				<div class="fbg-mkt-card__icon" aria-hidden="true">🤝</div>
				<h2><?php esc_html_e( 'Partners & affiliates', 'free-backlinks-generator' ); ?></h2>
				<p><?php esc_html_e( 'Interested in our affiliate program?', 'free-backlinks-generator' ); ?> <a href="<?php echo esc_url( home_url( '/affiliate-program/' ) ); ?>"><?php esc_html_e( 'Read the partner page', 'free-backlinks-generator' ); ?></a>.</p>
			</div>
		</div>
	</section>
</main>
<?php
get_footer();
