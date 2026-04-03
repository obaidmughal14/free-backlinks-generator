<?php
/**
 * Long-form legal copy for Privacy Policy and Terms of Service templates.
 *
 * @package Free_Backlinks_Generator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Echo privacy policy sections (plain language; customize for your entity).
 */
function fbg_render_privacy_policy_body() {
	$site = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
	$mail = antispambot( get_option( 'admin_email' ) );
	?>
	<section class="fbg-mkt-section fbg-container">
		<div class="fbg-mkt-prose">
			<h2><?php esc_html_e( '1. Introduction', 'free-backlinks-generator' ); ?></h2>
			<p><?php esc_html_e( 'We respect your privacy. This policy explains what information we collect when you use our website, member dashboard, and community guest-post features; how we use it; and the choices you have. By using the service, you agree to this policy together with our Terms of Service and Cookie Policy.', 'free-backlinks-generator' ); ?></p>

			<h2><?php esc_html_e( '2. Who we are', 'free-backlinks-generator' ); ?></h2>
			<p><?php echo esc_html( $site ); ?> <?php esc_html_e( 'operates this platform as described on our About page. For privacy-related requests, contact us using the email address listed at the end of this policy.', 'free-backlinks-generator' ); ?></p>

			<h2><?php esc_html_e( '3. Information we collect', 'free-backlinks-generator' ); ?></h2>
			<ul>
				<li><strong><?php esc_html_e( 'Account data:', 'free-backlinks-generator' ); ?></strong> <?php esc_html_e( 'name, email address, username, password hash, website URL you provide for your profile, and optional billing or payout details if you participate in paid or affiliate programs.', 'free-backlinks-generator' ); ?></li>
				<li><strong><?php esc_html_e( 'Content you submit:', 'free-backlinks-generator' ); ?></strong> <?php esc_html_e( 'guest posts, comments, messages to support, attachments, and metadata (e.g. niche categories, submission timestamps).', 'free-backlinks-generator' ); ?></li>
				<li><strong><?php esc_html_e( 'Usage and technical data:', 'free-backlinks-generator' ); ?></strong> <?php esc_html_e( 'IP address, browser type, device identifiers, approximate location derived from IP, pages viewed, referring URLs, and diagnostic logs used for security and performance.', 'free-backlinks-generator' ); ?></li>
				<li><strong><?php esc_html_e( 'Cookies:', 'free-backlinks-generator' ); ?></strong> <?php esc_html_e( 'see our Cookie Policy for categories, purposes, and controls.', 'free-backlinks-generator' ); ?></li>
			</ul>

			<h2><?php esc_html_e( '4. How we use information', 'free-backlinks-generator' ); ?></h2>
			<ul>
				<li><?php esc_html_e( 'Provide accounts, authentication, dashboards, and publishing workflows.', 'free-backlinks-generator' ); ?></li>
				<li><?php esc_html_e( 'Moderate submissions, enforce community guidelines, prevent fraud and abuse, and comply with law.', 'free-backlinks-generator' ); ?></li>
				<li><?php esc_html_e( 'Send service emails (e.g. approvals, password resets, security notices). Marketing emails only where permitted and with opt-out.', 'free-backlinks-generator' ); ?></li>
				<li><?php esc_html_e( 'Measure aggregate usage, improve performance, and develop features.', 'free-backlinks-generator' ); ?></li>
			</ul>

			<h2><?php esc_html_e( '5. Legal bases (EEA, UK, and similar jurisdictions)', 'free-backlinks-generator' ); ?></h2>
			<p><?php esc_html_e( 'Where GDPR-style laws apply, we rely on: performance of a contract (providing the service you signed up for); legitimate interests (security, analytics, product improvement) balanced against your rights; consent where required (e.g. certain cookies or marketing); and legal obligation where we must retain or disclose data.', 'free-backlinks-generator' ); ?></p>

			<h2><?php esc_html_e( '6. Sharing and processors', 'free-backlinks-generator' ); ?></h2>
			<p><?php esc_html_e( 'We do not sell your personal information. We share data with subprocessors who help us run the site (e.g. hosting, email delivery, analytics) under contracts that require protection and limited use. We may disclose information if required by law or to protect rights, safety, and integrity of members and the public.', 'free-backlinks-generator' ); ?></p>

			<h2><?php esc_html_e( '7. Public content', 'free-backlinks-generator' ); ?></h2>
			<p><?php esc_html_e( 'Guest posts you publish may be visible to the public, indexed by search engines, and distributed via RSS or similar feeds. Do not include sensitive personal data in posts unless necessary and lawful.', 'free-backlinks-generator' ); ?></p>

			<h2><?php esc_html_e( '8. Retention', 'free-backlinks-generator' ); ?></h2>
			<p><?php esc_html_e( 'We keep data as long as your account is active and as needed for the purposes above, including backups, audit, and legal compliance. You may request deletion subject to exceptions (e.g. fraud prevention, ongoing disputes).', 'free-backlinks-generator' ); ?></p>

			<h2><?php esc_html_e( '9. Your rights', 'free-backlinks-generator' ); ?></h2>
			<p><?php esc_html_e( 'Depending on your location, you may have rights to access, correct, delete, restrict, or object to certain processing, and to data portability. You may withdraw consent where processing is consent-based. To exercise rights, contact us using the email below. You may lodge a complaint with your local supervisory authority.', 'free-backlinks-generator' ); ?></p>

			<h2><?php esc_html_e( '10. Security', 'free-backlinks-generator' ); ?></h2>
			<p><?php esc_html_e( 'We use industry-standard measures (encryption in transit, access controls, monitoring). No method of transmission or storage is 100% secure; use a strong, unique password for your account.', 'free-backlinks-generator' ); ?></p>

			<h2><?php esc_html_e( '11. Children', 'free-backlinks-generator' ); ?></h2>
			<p><?php esc_html_e( 'The service is not directed to children under 16 (or the age required in your country). We do not knowingly collect their personal data.', 'free-backlinks-generator' ); ?></p>

			<h2><?php esc_html_e( '12. International transfers', 'free-backlinks-generator' ); ?></h2>
			<p><?php esc_html_e( 'If we transfer data across borders, we use appropriate safeguards such as standard contractual clauses where required.', 'free-backlinks-generator' ); ?></p>

			<h2><?php esc_html_e( '13. Changes', 'free-backlinks-generator' ); ?></h2>
			<p><?php esc_html_e( 'We may update this policy from time to time. We will post the revised version with a new “Last updated” date and, where appropriate, notify you by email or dashboard notice.', 'free-backlinks-generator' ); ?></p>

			<h2><?php esc_html_e( '14. Contact', 'free-backlinks-generator' ); ?></h2>
			<p>
				<?php esc_html_e( 'Questions about privacy:', 'free-backlinks-generator' ); ?>
				<a href="mailto:<?php echo esc_attr( $mail ); ?>"><?php echo esc_html( $mail ); ?></a>
			</p>
		</div>
	</section>
	<?php
}

/**
 * Echo terms of service sections.
 */
function fbg_render_terms_of_service_body() {
	$site = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
	$mail = antispambot( get_option( 'admin_email' ) );
	?>
	<section class="fbg-mkt-section fbg-container">
		<div class="fbg-mkt-prose">
			<h2><?php esc_html_e( '1. Agreement', 'free-backlinks-generator' ); ?></h2>
			<p><?php esc_html_e( 'These Terms of Service (“Terms”) govern your access to and use of our website, member accounts, and community guest-posting features. By registering, submitting content, or using the service, you agree to these Terms and our Privacy Policy and Cookie Policy.', 'free-backlinks-generator' ); ?></p>

			<h2><?php esc_html_e( '2. The service', 'free-backlinks-generator' ); ?></h2>
			<p><?php printf( /* translators: %s site name */ esc_html__( '%s provides a community where members may submit editorial content for review, publication, and mutual backlink opportunities. Features, limits, and availability may change; we do not guarantee uninterrupted access.', 'free-backlinks-generator' ), esc_html( $site ) ); ?></p>

			<h2><?php esc_html_e( '3. Eligibility and accounts', 'free-backlinks-generator' ); ?></h2>
			<p><?php esc_html_e( 'You must provide accurate registration information and keep your credentials secure. You are responsible for activity under your account. Notify us promptly of unauthorized use.', 'free-backlinks-generator' ); ?></p>

			<h2><?php esc_html_e( '4. Content you submit', 'free-backlinks-generator' ); ?></h2>
			<ul>
				<li><?php esc_html_e( 'You retain ownership of your content. You grant us a worldwide, non-exclusive license to host, reproduce, distribute, display, and adapt your content as needed to operate, promote, and improve the service (including RSS, sitemaps, and previews).', 'free-backlinks-generator' ); ?></li>
				<li><?php esc_html_e( 'You represent that you have all rights necessary to submit the content and that it does not infringe third-party rights.', 'free-backlinks-generator' ); ?></li>
				<li><?php esc_html_e( 'Published posts may remain public even if you close your account, except where we agree otherwise or law requires removal.', 'free-backlinks-generator' ); ?></li>
			</ul>

			<h2><?php esc_html_e( '5. Editorial standards and moderation', 'free-backlinks-generator' ); ?></h2>
			<p><?php esc_html_e( 'We may approve, reject, edit for formatting, or remove content at our discretion to enforce quality, legal compliance, and our Community Guidelines. We are not obligated to publish any submission.', 'free-backlinks-generator' ); ?></p>

			<h2><?php esc_html_e( '6. Prohibited conduct', 'free-backlinks-generator' ); ?></h2>
			<p><?php esc_html_e( 'You must not: break the law; distribute malware; scrape or overload systems; impersonate others; harass members; post spam, spun, or AI-generated filler without disclosure where required; build private blog networks or paid link schemes through the platform; or circumvent technical or editorial limits.', 'free-backlinks-generator' ); ?></p>

			<h2><?php esc_html_e( '7. Backlinks and SEO', 'free-backlinks-generator' ); ?></h2>
			<p><?php esc_html_e( 'Links should be editorial and relevant. Search engines independently decide how to index content. We do not guarantee rankings, traffic, domain authority changes, or SEO outcomes.', 'free-backlinks-generator' ); ?></p>

			<h2><?php esc_html_e( '8. Third-party sites', 'free-backlinks-generator' ); ?></h2>
			<p><?php esc_html_e( 'The service may link to third-party websites. We are not responsible for their content, policies, or practices.', 'free-backlinks-generator' ); ?></p>

			<h2><?php esc_html_e( '9. Disclaimers', 'free-backlinks-generator' ); ?></h2>
			<p><?php esc_html_e( 'THE SERVICE IS PROVIDED “AS IS” AND “AS AVAILABLE.” TO THE MAXIMUM EXTENT PERMITTED BY LAW, WE DISCLAIM IMPLIED WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE, AND NON-INFRINGEMENT.', 'free-backlinks-generator' ); ?></p>

			<h2><?php esc_html_e( '10. Limitation of liability', 'free-backlinks-generator' ); ?></h2>
			<p><?php esc_html_e( 'TO THE MAXIMUM EXTENT PERMITTED BY LAW, WE WILL NOT BE LIABLE FOR INDIRECT, INCIDENTAL, SPECIAL, CONSEQUENTIAL, OR PUNITIVE DAMAGES, OR LOSS OF PROFITS, DATA, OR GOODWILL. OUR AGGREGATE LIABILITY FOR CLAIMS RELATING TO THE SERVICE SHALL NOT EXCEED THE GREATER OF AMOUNTS YOU PAID US IN THE TWELVE MONTHS BEFORE THE CLAIM OR FIFTY U.S. DOLLARS (OR LOCAL EQUIVALENT).', 'free-backlinks-generator' ); ?></p>

			<h2><?php esc_html_e( '11. Indemnity', 'free-backlinks-generator' ); ?></h2>
			<p><?php esc_html_e( 'You will defend and indemnify us against claims arising from your content, your use of the service, or your breach of these Terms.', 'free-backlinks-generator' ); ?></p>

			<h2><?php esc_html_e( '12. Suspension and termination', 'free-backlinks-generator' ); ?></h2>
			<p><?php esc_html_e( 'We may suspend or terminate access for violations or risk to the community. You may stop using the service at any time. Provisions that by nature should survive will survive termination.', 'free-backlinks-generator' ); ?></p>

			<h2><?php esc_html_e( '13. Governing law', 'free-backlinks-generator' ); ?></h2>
			<p><?php esc_html_e( 'Unless mandatory local law requires otherwise, these Terms are governed by the laws of the jurisdiction where the operator is established, without regard to conflict-of-law rules. Courts in that jurisdiction have exclusive venue, subject to non-waivable rights you may have.', 'free-backlinks-generator' ); ?></p>

			<h2><?php esc_html_e( '14. Changes', 'free-backlinks-generator' ); ?></h2>
			<p><?php esc_html_e( 'We may modify these Terms. We will post the updated Terms with a new effective date. Continued use after changes constitutes acceptance where allowed by law.', 'free-backlinks-generator' ); ?></p>

			<h2><?php esc_html_e( '15. Contact', 'free-backlinks-generator' ); ?></h2>
			<p>
				<?php esc_html_e( 'Legal or account questions:', 'free-backlinks-generator' ); ?>
				<a href="mailto:<?php echo esc_attr( $mail ); ?>"><?php echo esc_html( $mail ); ?></a>
			</p>
		</div>
	</section>
	<?php
}
