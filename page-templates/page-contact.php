<?php
/**
 * Template Name: Contact
 *
 * @package Free_Backlinks_Generator
 */

get_header();
$mail = antispambot( get_option( 'admin_email' ) );
$ill  = get_theme_file_uri( 'assets/images/hero-illustration.svg' );
$icon = get_theme_file_uri( 'assets/images/icon-posts.svg' );
$ticket_page = get_page_by_path( 'support-ticket' );
$ticket_url  = $ticket_page ? get_permalink( $ticket_page ) : home_url( '/support-ticket/' );
?>
<main id="main-content" class="fbg-main">
	<section class="fbg-mkt-hero fbg-mkt-hero--split" aria-labelledby="fbg-contact-title">
		<div class="fbg-container fbg-mkt-hero-split">
			<div class="fbg-mkt-hero-split__text">
				<h1 id="fbg-contact-title"><?php esc_html_e( 'Contact us', 'free-backlinks-generator' ); ?></h1>
				<p class="fbg-mkt-hero__lead" style="margin: 0;">
					<?php esc_html_e( 'Questions about your account, submissions, partnerships, or press? Use the form or email us — we read every message and usually reply within two business days.', 'free-backlinks-generator' ); ?>
				</p>
			</div>
			<div class="fbg-mkt-hero-split__visual">
				<img src="<?php echo esc_url( $ill ); ?>" width="420" height="294" alt="" loading="lazy" decoding="async" class="fbg-mkt-hero-split__img">
			</div>
		</div>
	</section>

	<section class="fbg-mkt-section fbg-container">
		<div class="fbg-mkt-grid fbg-mkt-grid--2 fbg-contact-page__top">
			<div>
				<?php
				get_template_part(
					'template-parts/contact-form-block',
					null,
					array(
						'form_id'            => 'fbg-contact-page-form',
						'feedback_id'        => 'fbg-cp-feedback',
						'name_id'            => 'fbg-cp-name',
						'email_id'           => 'fbg-cp-email',
						'message_id'         => 'fbg-cp-msg',
						'contact_form_title' => __( 'Send us a message', 'free-backlinks-generator' ),
						'contact_form_intro' => __( 'Tell us how we can help. Include links to posts or tickets when relevant so we can respond faster.', 'free-backlinks-generator' ),
						'contact_wrap_class' => 'fbg-sidebar-card fbg-sidebar-card--contact fbg-contact-page__formwrap',
					)
				);
				?>
			</div>
			<div class="fbg-contact-page__quick">
				<div class="fbg-mkt-card fbg-mkt-card--contact">
					<div class="fbg-mkt-card__icon" aria-hidden="true"><img src="<?php echo esc_url( $icon ); ?>" width="44" height="44" alt=""></div>
					<h2><?php esc_html_e( 'Email', 'free-backlinks-generator' ); ?></h2>
					<p><?php esc_html_e( 'Best for attachments, legal notices, and long threads.', 'free-backlinks-generator' ); ?></p>
					<p><a class="fbg-mkt-inline-link" href="mailto:<?php echo esc_attr( $mail ); ?>"><?php echo esc_html( $mail ); ?></a></p>
				</div>
				<div class="fbg-mkt-card fbg-mkt-card--contact">
					<div class="fbg-mkt-card__icon" aria-hidden="true"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/icon-dashboard.svg' ) ); ?>" width="44" height="44" alt=""></div>
					<h2><?php esc_html_e( 'Response time', 'free-backlinks-generator' ); ?></h2>
					<p><?php esc_html_e( 'Typical reply: 1–2 business days. Complex moderation reviews may take longer — check your dashboard for status when available.', 'free-backlinks-generator' ); ?></p>
				</div>
			</div>
		</div>
	</section>

	<section class="fbg-mkt-section fbg-container fbg-contact-ticket-cta">
		<div class="fbg-mkt-card fbg-contact-ticket-cta__card">
			<div class="fbg-contact-ticket-cta__row">
				<div class="fbg-contact-ticket-cta__text">
					<h2 class="fbg-contact-ticket-cta__title"><?php esc_html_e( 'Need a tracked request?', 'free-backlinks-generator' ); ?></h2>
					<p class="fbg-contact-ticket-cta__desc"><?php esc_html_e( 'For billing, technical issues, or moderation appeals, open a support ticket. You will receive a reference number (e.g. FBG-12) and updates by email.', 'free-backlinks-generator' ); ?></p>
				</div>
				<div class="fbg-contact-ticket-cta__action">
					<a class="btn-primary fbg-contact-ticket-cta__btn" href="<?php echo esc_url( $ticket_url ); ?>"><?php esc_html_e( 'Create a support ticket', 'free-backlinks-generator' ); ?> →</a>
				</div>
			</div>
		</div>
	</section>

	<section class="fbg-mkt-section fbg-mkt-section--alt fbg-container">
		<div class="fbg-mkt-grid fbg-mkt-grid--2">
			<div class="fbg-mkt-card fbg-mkt-card--contact">
				<div class="fbg-mkt-card__icon" aria-hidden="true"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/icon-community.svg' ) ); ?>" width="44" height="44" alt=""></div>
				<h2><?php esc_html_e( 'Abuse & safety', 'free-backlinks-generator' ); ?></h2>
				<p><?php esc_html_e( 'Report spam, harassment, or policy violations via email with URLs and screenshots. We investigate and may suspend accounts that threaten the community.', 'free-backlinks-generator' ); ?></p>
			</div>
			<div class="fbg-mkt-card fbg-mkt-card--contact">
				<div class="fbg-mkt-card__icon" aria-hidden="true"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/icon-links.svg' ) ); ?>" width="44" height="44" alt=""></div>
				<h2><?php esc_html_e( 'Partners & affiliates', 'free-backlinks-generator' ); ?></h2>
				<p>
					<?php esc_html_e( 'Interested in our affiliate program?', 'free-backlinks-generator' ); ?>
					<a href="<?php echo esc_url( home_url( '/affiliate-program/' ) ); ?>"><?php esc_html_e( 'Read the partner page', 'free-backlinks-generator' ); ?></a>.
				</p>
			</div>
		</div>
	</section>
</main>
<?php
get_footer();
