<?php
/**
 * Template Name: Create support ticket
 *
 * @package Free_Backlinks_Generator
 */

get_header();
$cu  = is_user_logged_in() ? wp_get_current_user() : null;
$ill = get_theme_file_uri( 'assets/images/icon-dashboard.svg' );
?>
<main id="main-content" class="fbg-main fbg-ticket-page">
	<section class="fbg-mkt-hero fbg-mkt-hero--split fbg-ticket-page__hero" aria-labelledby="fbg-ticket-page-title">
		<div class="fbg-container fbg-mkt-hero-split">
			<div class="fbg-mkt-hero-split__text">
				<p class="fbg-ticket-page__eyebrow"><?php esc_html_e( 'Help desk', 'free-backlinks-generator' ); ?></p>
				<h1 id="fbg-ticket-page-title"><?php esc_html_e( 'Create a support ticket', 'free-backlinks-generator' ); ?></h1>
				<p class="fbg-mkt-hero__lead" style="margin: 0;">
					<?php esc_html_e( 'Open a tracked request for account issues, billing, technical problems, or moderation appeals. You will get a reference number (e.g. FBG-12) by email and our team will reply to your inbox.', 'free-backlinks-generator' ); ?>
				</p>
				<p class="fbg-ticket-page__meta">
					<span class="fbg-ticket-page__pill"><?php esc_html_e( 'Tracked in admin', 'free-backlinks-generator' ); ?></span>
					<span class="fbg-ticket-page__pill"><?php esc_html_e( 'Email updates', 'free-backlinks-generator' ); ?></span>
					<span class="fbg-ticket-page__pill"><?php esc_html_e( 'Typical reply 1–2 business days', 'free-backlinks-generator' ); ?></span>
				</p>
			</div>
			<div class="fbg-mkt-hero-split__visual fbg-ticket-page__visual">
				<img src="<?php echo esc_url( $ill ); ?>" width="200" height="200" alt="" loading="eager" decoding="async" class="fbg-ticket-page__hero-icon">
			</div>
		</div>
	</section>

	<section class="fbg-container fbg-ticket-page__body">
		<div class="fbg-ticket-page__shell">
			<form id="fbg-support-ticket-form" class="fbg-ticket-page__form fbg-aff-form" novalidate>
				<?php if ( $cu ) : ?>
					<input type="hidden" name="requester_name" value="<?php echo esc_attr( $cu->display_name ); ?>">
					<input type="hidden" name="requester_email" value="<?php echo esc_attr( $cu->user_email ); ?>">
					<div class="fbg-ticket-page__account-note">
						<strong><?php esc_html_e( 'Signed in', 'free-backlinks-generator' ); ?></strong>
						<?php
						printf(
							/* translators: %s display name */
							esc_html__( 'We will use %s and your account email on this ticket.', 'free-backlinks-generator' ),
							esc_html( $cu->display_name )
						);
						?>
					</div>
				<?php else : ?>
					<div class="fbg-ticket-page__grid2">
						<div class="fbg-field">
							<label for="fbg-ticket-name"><?php esc_html_e( 'Your name', 'free-backlinks-generator' ); ?></label>
							<input type="text" id="fbg-ticket-name" name="requester_name" required maxlength="190" autocomplete="name" class="fbg-ticket-page__input">
						</div>
						<div class="fbg-field">
							<label for="fbg-ticket-email"><?php esc_html_e( 'Email', 'free-backlinks-generator' ); ?></label>
							<input type="email" id="fbg-ticket-email" name="requester_email" required autocomplete="email" class="fbg-ticket-page__input">
						</div>
					</div>
				<?php endif; ?>

				<div class="fbg-field">
					<label for="fbg-ticket-subject"><?php esc_html_e( 'Subject', 'free-backlinks-generator' ); ?></label>
					<input type="text" id="fbg-ticket-subject" name="subject" required maxlength="255" placeholder="<?php esc_attr_e( 'Short summary of the issue', 'free-backlinks-generator' ); ?>" class="fbg-ticket-page__input">
				</div>

				<div class="fbg-ticket-page__grid2">
					<div class="fbg-field">
						<label for="fbg-ticket-category"><?php esc_html_e( 'Category', 'free-backlinks-generator' ); ?></label>
						<select id="fbg-ticket-category" name="category" required class="fbg-ticket-page__input">
							<option value="general"><?php esc_html_e( 'General', 'free-backlinks-generator' ); ?></option>
							<option value="account"><?php esc_html_e( 'Account & login', 'free-backlinks-generator' ); ?></option>
							<option value="billing"><?php esc_html_e( 'Billing / payouts', 'free-backlinks-generator' ); ?></option>
							<option value="technical"><?php esc_html_e( 'Technical / bug', 'free-backlinks-generator' ); ?></option>
							<option value="moderation"><?php esc_html_e( 'Moderation / appeal', 'free-backlinks-generator' ); ?></option>
							<option value="partnership"><?php esc_html_e( 'Partnership / press', 'free-backlinks-generator' ); ?></option>
							<option value="other"><?php esc_html_e( 'Other', 'free-backlinks-generator' ); ?></option>
						</select>
					</div>
					<div class="fbg-field">
						<label for="fbg-ticket-priority"><?php esc_html_e( 'Priority', 'free-backlinks-generator' ); ?></label>
						<select id="fbg-ticket-priority" name="priority" required class="fbg-ticket-page__input">
							<option value="low"><?php esc_html_e( 'Low', 'free-backlinks-generator' ); ?></option>
							<option value="normal" selected><?php esc_html_e( 'Normal', 'free-backlinks-generator' ); ?></option>
							<option value="high"><?php esc_html_e( 'High', 'free-backlinks-generator' ); ?></option>
							<option value="urgent"><?php esc_html_e( 'Urgent', 'free-backlinks-generator' ); ?></option>
						</select>
					</div>
				</div>

				<div class="fbg-field">
					<label for="fbg-ticket-body"><?php esc_html_e( 'Describe the issue', 'free-backlinks-generator' ); ?></label>
					<textarea id="fbg-ticket-body" name="body" required rows="8" maxlength="8000" placeholder="<?php esc_attr_e( 'Include steps to reproduce, links, screenshots descriptions, and what you expected.', 'free-backlinks-generator' ); ?>" class="fbg-ticket-page__textarea"></textarea>
				</div>

				<div class="fbg-ticket-page__submit-row">
					<button type="submit" class="btn-primary fbg-ticket-page__submit" id="fbg-support-ticket-submit"><?php esc_html_e( 'Submit ticket', 'free-backlinks-generator' ); ?></button>
					<a class="btn-ghost" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( '← Back to contact', 'free-backlinks-generator' ); ?></a>
				</div>
				<div id="fbg-support-ticket-feedback" class="fbg-ticket-form__feedback fbg-ticket-page__feedback" hidden role="status"></div>
			</form>
		</div>
	</section>
</main>
<?php
get_footer();
