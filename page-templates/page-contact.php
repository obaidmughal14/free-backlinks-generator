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
$cu   = is_user_logged_in() ? wp_get_current_user() : null;
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

	<section class="fbg-mkt-section fbg-container fbg-ticket-section" id="support-ticket">
		<div class="fbg-sidebar-card fbg-sidebar-card--contact fbg-ticket-section__card">
			<h2 class="fbg-sidebar-card__title"><?php esc_html_e( 'Create a support ticket', 'free-backlinks-generator' ); ?></h2>
			<p class="fbg-sidebar-card__intro"><?php esc_html_e( 'For account issues, billing, technical problems, or moderation appeals, open a tracked ticket. You will receive a reference number (e.g. FBG-12) by email.', 'free-backlinks-generator' ); ?></p>
			<form id="fbg-support-ticket-form" class="fbg-aff-form fbg-ticket-form" novalidate>
				<?php if ( $cu ) : ?>
					<input type="hidden" name="requester_name" value="<?php echo esc_attr( $cu->display_name ); ?>">
					<input type="hidden" name="requester_email" value="<?php echo esc_attr( $cu->user_email ); ?>">
					<p class="fbg-ticket-form__logged"><?php esc_html_e( 'You are logged in — we will use your account name and email.', 'free-backlinks-generator' ); ?></p>
				<?php else : ?>
					<div class="fbg-field">
						<label for="fbg-ticket-name"><?php esc_html_e( 'Your name', 'free-backlinks-generator' ); ?></label>
						<input type="text" id="fbg-ticket-name" name="requester_name" required maxlength="190" autocomplete="name">
					</div>
					<div class="fbg-field">
						<label for="fbg-ticket-email"><?php esc_html_e( 'Email', 'free-backlinks-generator' ); ?></label>
						<input type="email" id="fbg-ticket-email" name="requester_email" required autocomplete="email">
					</div>
				<?php endif; ?>
				<div class="fbg-field">
					<label for="fbg-ticket-subject"><?php esc_html_e( 'Subject', 'free-backlinks-generator' ); ?></label>
					<input type="text" id="fbg-ticket-subject" name="subject" required maxlength="255" placeholder="<?php esc_attr_e( 'Short summary of the issue', 'free-backlinks-generator' ); ?>">
				</div>
				<div class="fbg-field fbg-field--inline">
					<label for="fbg-ticket-category"><?php esc_html_e( 'Category', 'free-backlinks-generator' ); ?></label>
					<select id="fbg-ticket-category" name="category" required>
						<option value="general"><?php esc_html_e( 'General', 'free-backlinks-generator' ); ?></option>
						<option value="account"><?php esc_html_e( 'Account & login', 'free-backlinks-generator' ); ?></option>
						<option value="billing"><?php esc_html_e( 'Billing / payouts', 'free-backlinks-generator' ); ?></option>
						<option value="technical"><?php esc_html_e( 'Technical / bug', 'free-backlinks-generator' ); ?></option>
						<option value="moderation"><?php esc_html_e( 'Moderation / appeal', 'free-backlinks-generator' ); ?></option>
						<option value="partnership"><?php esc_html_e( 'Partnership / press', 'free-backlinks-generator' ); ?></option>
						<option value="other"><?php esc_html_e( 'Other', 'free-backlinks-generator' ); ?></option>
					</select>
				</div>
				<div class="fbg-field fbg-field--inline">
					<label for="fbg-ticket-priority"><?php esc_html_e( 'Priority', 'free-backlinks-generator' ); ?></label>
					<select id="fbg-ticket-priority" name="priority" required>
						<option value="low"><?php esc_html_e( 'Low', 'free-backlinks-generator' ); ?></option>
						<option value="normal" selected><?php esc_html_e( 'Normal', 'free-backlinks-generator' ); ?></option>
						<option value="high"><?php esc_html_e( 'High', 'free-backlinks-generator' ); ?></option>
						<option value="urgent"><?php esc_html_e( 'Urgent', 'free-backlinks-generator' ); ?></option>
					</select>
				</div>
				<div class="fbg-field">
					<label for="fbg-ticket-body"><?php esc_html_e( 'Describe the issue', 'free-backlinks-generator' ); ?></label>
					<textarea id="fbg-ticket-body" name="body" required rows="6" maxlength="8000" placeholder="<?php esc_attr_e( 'Include steps to reproduce, links, and what you expected to happen.', 'free-backlinks-generator' ); ?>"></textarea>
				</div>
				<button type="submit" class="btn-primary" id="fbg-support-ticket-submit"><?php esc_html_e( 'Submit ticket', 'free-backlinks-generator' ); ?></button>
				<div id="fbg-support-ticket-feedback" class="fbg-ticket-form__feedback" hidden role="status"></div>
			</form>
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
