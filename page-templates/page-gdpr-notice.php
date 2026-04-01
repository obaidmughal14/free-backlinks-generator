<?php
/**
 * Template Name: GDPR Notice
 *
 * @package Free_Backlinks_Generator
 */

get_header();
$site   = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
$admin  = get_option( 'admin_email' );
$domain = wp_parse_url( home_url(), PHP_URL_HOST );
?>
<main id="main-content" class="fbg-main">
	<section class="fbg-mkt-hero" aria-labelledby="fbg-gdpr-title">
		<div class="fbg-container">
			<h1 id="fbg-gdpr-title"><?php esc_html_e( 'GDPR & data protection notice', 'free-backlinks-generator' ); ?></h1>
			<p class="fbg-mkt-hero__lead">
				<?php
				printf(
					/* translators: %s site name */
					esc_html__( 'This notice describes how %s processes personal data when you use our website and community features, in line with the EU General Data Protection Regulation (GDPR) and similar laws.', 'free-backlinks-generator' ),
					esc_html( $site )
				);
				?>
			</p>
			<p class="fbg-mkt-hero__lead" style="margin-top: var(--space-md); font-size: 0.95rem; opacity: 0.9;">
				<?php
				printf(
					esc_html__( 'Last updated: %s', 'free-backlinks-generator' ),
					esc_html( gmdate( 'F j, Y' ) )
				);
				?>
			</p>
		</div>
	</section>

	<section class="fbg-mkt-section fbg-container">
		<h2><?php esc_html_e( 'Data controller', 'free-backlinks-generator' ); ?></h2>
		<div class="fbg-mkt-card" style="max-width: 640px; margin: var(--space-lg) auto 0;">
			<p style="margin: 0 0 var(--space-sm);"><strong><?php echo esc_html( $site ); ?></strong></p>
			<p style="margin: 0; color: var(--color-text-secondary);">
				<?php esc_html_e( 'Website:', 'free-backlinks-generator' ); ?> <span class="mono"><?php echo esc_html( (string) $domain ); ?></span><br>
				<?php esc_html_e( 'Contact email:', 'free-backlinks-generator' ); ?>
				<?php if ( is_email( $admin ) ) : ?>
					<a href="mailto:<?php echo esc_attr( $admin ); ?>"><?php echo esc_html( $admin ); ?></a>
				<?php else : ?>
					<?php esc_html_e( 'See Contact page', 'free-backlinks-generator' ); ?>
				<?php endif; ?>
			</p>
		</div>
	</section>

	<section class="fbg-mkt-section fbg-mkt-section--alt">
		<div class="fbg-container">
			<h2><?php esc_html_e( 'What we process', 'free-backlinks-generator' ); ?></h2>
			<p class="fbg-mkt-section__sub"><?php esc_html_e( 'Depending on how you use the platform, we may process:', 'free-backlinks-generator' ); ?></p>
			<div class="fbg-mkt-grid fbg-mkt-grid--3">
				<div class="fbg-mkt-card">
					<div class="fbg-mkt-card__icon" aria-hidden="true">👤</div>
					<h3><?php esc_html_e( 'Account data', 'free-backlinks-generator' ); ?></h3>
					<p><?php esc_html_e( 'Name, email, password hash, profile fields, website URL, and membership tier.', 'free-backlinks-generator' ); ?></p>
				</div>
				<div class="fbg-mkt-card">
					<div class="fbg-mkt-card__icon" aria-hidden="true">📝</div>
					<h3><?php esc_html_e( 'Content you submit', 'free-backlinks-generator' ); ?></h3>
					<p><?php esc_html_e( 'Guest posts, excerpts, backlinks, images, and moderation history.', 'free-backlinks-generator' ); ?></p>
				</div>
				<div class="fbg-mkt-card">
					<div class="fbg-mkt-card__icon" aria-hidden="true">📡</div>
					<h3><?php esc_html_e( 'Technical data', 'free-backlinks-generator' ); ?></h3>
					<p><?php esc_html_e( 'IP address, device/browser type, timestamps, security logs, and cookies as described in our Cookie Policy.', 'free-backlinks-generator' ); ?></p>
				</div>
			</div>
		</div>
	</section>

	<section class="fbg-mkt-section fbg-container">
		<h2><?php esc_html_e( 'Legal bases (GDPR Art. 6)', 'free-backlinks-generator' ); ?></h2>
		<div class="fbg-mkt-grid fbg-mkt-grid--2">
			<div class="fbg-mkt-card">
				<h3><?php esc_html_e( 'Contract', 'free-backlinks-generator' ); ?></h3>
				<p><?php esc_html_e( 'Providing accounts, guest posting, dashboards, and paid features you request.', 'free-backlinks-generator' ); ?></p>
			</div>
			<div class="fbg-mkt-card">
				<h3><?php esc_html_e( 'Legitimate interests', 'free-backlinks-generator' ); ?></h3>
				<p><?php esc_html_e( 'Security, fraud prevention, product analytics, and improving the service — balanced against your rights.', 'free-backlinks-generator' ); ?></p>
			</div>
			<div class="fbg-mkt-card">
				<h3><?php esc_html_e( 'Consent', 'free-backlinks-generator' ); ?></h3>
				<p><?php esc_html_e( 'Optional marketing emails and non-essential cookies where we ask you to opt in.', 'free-backlinks-generator' ); ?></p>
			</div>
			<div class="fbg-mkt-card">
				<h3><?php esc_html_e( 'Legal obligation', 'free-backlinks-generator' ); ?></h3>
				<p><?php esc_html_e( 'Records we must keep for tax, abuse reports, or lawful requests from authorities.', 'free-backlinks-generator' ); ?></p>
			</div>
		</div>
	</section>

	<section class="fbg-mkt-section fbg-mkt-section--alt">
		<div class="fbg-container">
			<h2><?php esc_html_e( 'Your rights', 'free-backlinks-generator' ); ?></h2>
			<p class="fbg-mkt-section__sub"><?php esc_html_e( 'If GDPR applies, you may have the following rights (some exceptions apply):', 'free-backlinks-generator' ); ?></p>
			<div class="fbg-mkt-rights">
				<div class="fbg-mkt-right">
					<span class="fbg-mkt-right__emoji" aria-hidden="true">🔍</span>
					<div><strong><?php esc_html_e( 'Access', 'free-backlinks-generator' ); ?></strong><p style="margin: var(--space-xs) 0 0; font-size: 0.9rem; color: var(--color-text-secondary);"><?php esc_html_e( 'Request a copy of personal data we hold about you.', 'free-backlinks-generator' ); ?></p></div>
				</div>
				<div class="fbg-mkt-right">
					<span class="fbg-mkt-right__emoji" aria-hidden="true">✏️</span>
					<div><strong><?php esc_html_e( 'Rectification', 'free-backlinks-generator' ); ?></strong><p style="margin: var(--space-xs) 0 0; font-size: 0.9rem; color: var(--color-text-secondary);"><?php esc_html_e( 'Correct inaccurate profile or post metadata.', 'free-backlinks-generator' ); ?></p></div>
				</div>
				<div class="fbg-mkt-right">
					<span class="fbg-mkt-right__emoji" aria-hidden="true">🗑️</span>
					<div><strong><?php esc_html_e( 'Erasure', 'free-backlinks-generator' ); ?></strong><p style="margin: var(--space-xs) 0 0; font-size: 0.9rem; color: var(--color-text-secondary);"><?php esc_html_e( 'Ask us to delete data where no overriding legal ground applies.', 'free-backlinks-generator' ); ?></p></div>
				</div>
				<div class="fbg-mkt-right">
					<span class="fbg-mkt-right__emoji" aria-hidden="true">📦</span>
					<div><strong><?php esc_html_e( 'Portability', 'free-backlinks-generator' ); ?></strong><p style="margin: var(--space-xs) 0 0; font-size: 0.9rem; color: var(--color-text-secondary);"><?php esc_html_e( 'Export machine-readable data where processing is based on contract or consent.', 'free-backlinks-generator' ); ?></p></div>
				</div>
				<div class="fbg-mkt-right">
					<span class="fbg-mkt-right__emoji" aria-hidden="true">⛔</span>
					<div><strong><?php esc_html_e( 'Object / restrict', 'free-backlinks-generator' ); ?></strong><p style="margin: var(--space-xs) 0 0; font-size: 0.9rem; color: var(--color-text-secondary);"><?php esc_html_e( 'Object to certain processing or ask us to pause processing in specific cases.', 'free-backlinks-generator' ); ?></p></div>
				</div>
				<div class="fbg-mkt-right">
					<span class="fbg-mkt-right__emoji" aria-hidden="true">📮</span>
					<div><strong><?php esc_html_e( 'Complaint', 'free-backlinks-generator' ); ?></strong><p style="margin: var(--space-xs) 0 0; font-size: 0.9rem; color: var(--color-text-secondary);"><?php esc_html_e( 'Lodge a complaint with your local supervisory authority.', 'free-backlinks-generator' ); ?></p></div>
				</div>
			</div>
			<p class="fbg-mkt-section__sub" style="margin-top: var(--space-xl);">
				<?php
				printf(
					wp_kses_post( __( 'Logged-in members can export much of their data from <a href="%s">Dashboard → Settings</a>. For other requests, use the Contact page.', 'free-backlinks-generator' ) ),
					esc_url( home_url( '/dashboard/#settings' ) )
				);
				?>
			</p>
		</div>
	</section>

	<section class="fbg-mkt-section fbg-container">
		<h2><?php esc_html_e( 'Retention & transfers', 'free-backlinks-generator' ); ?></h2>
		<div class="fbg-mkt-timeline">
			<p><?php esc_html_e( 'We keep data only as long as needed for the purposes above: active accounts until deletion, backups on a rolling schedule, and legal/tax records as required.', 'free-backlinks-generator' ); ?></p>
			<p><?php esc_html_e( 'Our hosting and email providers may process data in the EU, UK, US, or other regions. Where required, we use appropriate safeguards such as Standard Contractual Clauses.', 'free-backlinks-generator' ); ?></p>
		</div>
	</section>
</main>
<?php
get_footer();
