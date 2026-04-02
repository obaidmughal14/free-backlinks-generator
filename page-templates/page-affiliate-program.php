<?php
/**
 * Template Name: Affiliate Program
 *
 * @package Free_Backlinks_Generator
 */

get_header();
$uid         = is_user_logged_in() ? get_current_user_id() : 0;
$ref_home    = $uid ? add_query_arg( 'fbg_ref', $uid, home_url( '/' ) ) : '';
$is_partner  = $uid && function_exists( 'fbg_is_user_affiliate_partner' ) ? fbg_is_user_affiliate_partner( $uid ) : false;
?>
<main id="main-content" class="fbg-main">
	<section class="fbg-mkt-hero" aria-labelledby="fbg-aff-title">
		<div class="fbg-container">
			<div class="fbg-mkt-hero__badges">
				<span class="fbg-mkt-badge"><?php esc_html_e( 'Organic traffic rewards', 'free-backlinks-generator' ); ?></span>
				<span class="fbg-mkt-badge"><?php esc_html_e( '90-day cookie', 'free-backlinks-generator' ); ?></span>
				<span class="fbg-mkt-badge"><?php esc_html_e( 'Reads & messages count', 'free-backlinks-generator' ); ?></span>
			</div>
			<h1 id="fbg-aff-title"><?php esc_html_e( 'Affiliate program: earn from real readers', 'free-backlinks-generator' ); ?></h1>
			<p class="fbg-mkt-hero__lead">
				<?php esc_html_e( 'Share your link (see below). When someone arrives with your referral and reads community guest posts or uses the contact form on a post, we attribute that engagement to you. Organic search visits stack toward cash; all attributed visits also stack toward bonus guest-post slots.', 'free-backlinks-generator' ); ?>
			</p>
			<div class="fbg-mkt-stats" role="group" aria-label="<?php esc_attr_e( 'Program highlights', 'free-backlinks-generator' ); ?>">
				<div class="fbg-mkt-stat">
					<strong>$2</strong>
					<span><?php esc_html_e( 'Per 1,000 organic search visits attributed', 'free-backlinks-generator' ); ?></span>
				</div>
				<div class="fbg-mkt-stat">
					<strong>+10</strong>
					<span><?php esc_html_e( 'Bonus guest-post slots per 1,000 total attributed visits', 'free-backlinks-generator' ); ?></span>
				</div>
				<div class="fbg-mkt-stat">
					<strong>90</strong>
					<span><?php esc_html_e( 'Days referral cookie (first touch)', 'free-backlinks-generator' ); ?></span>
				</div>
			</div>
		</div>
	</section>

	<?php if ( is_user_logged_in() && $ref_home ) : ?>
		<section class="fbg-mkt-section fbg-container">
			<?php if ( ! $is_partner ) : ?>
				<div class="fbg-mkt-card" style="padding: var(--space-lg); border: 1px solid rgba(245, 166, 35, 0.45); border-radius: var(--radius-lg); background: rgba(245, 166, 35, 0.08); margin-bottom: var(--space-lg);">
					<h2 style="margin-top: 0;"><?php esc_html_e( 'Activation pending', 'free-backlinks-generator' ); ?></h2>
					<p class="fbg-mkt-section__sub" style="margin-bottom: 0;"><?php esc_html_e( 'An administrator must approve your account before referral visits earn credit. Submit the partner application below if you have not already. Until then, the link below will not accumulate rewards.', 'free-backlinks-generator' ); ?></p>
				</div>
			<?php endif; ?>
			<div class="fbg-mkt-card" style="padding: var(--space-lg); border: 1px solid var(--color-border); border-radius: var(--radius-lg);">
				<h2 style="margin-top: 0;"><?php esc_html_e( 'Your referral link', 'free-backlinks-generator' ); ?></h2>
				<p class="fbg-mkt-section__sub" style="margin-bottom: var(--space-md);"><?php esc_html_e( 'Add this parameter to any URL on the site (home, a guest post, or the register page). When visitors use it, reads and sidebar messages can credit your account once you are an approved partner.', 'free-backlinks-generator' ); ?></p>
				<p class="mono" style="word-break: break-all; font-size: 0.9rem; background: var(--color-bg); padding: var(--space-md); border-radius: var(--radius-md);"><?php echo esc_html( $ref_home ); ?></p>
				<p style="margin-bottom: 0; font-size: 0.9rem; color: var(--color-text-secondary);"><?php esc_html_e( 'Example: append ?fbg_ref=YOUR_USER_ID to links you share. Organic detection uses the visitor’s referrer (Google, Bing, DuckDuckGo, etc.).', 'free-backlinks-generator' ); ?></p>
			</div>
		</section>
	<?php endif; ?>

	<section class="fbg-mkt-section fbg-container">
		<h2><?php esc_html_e( 'How rewards add up', 'free-backlinks-generator' ); ?></h2>
		<p class="fbg-mkt-section__sub"><?php esc_html_e( 'Balances and slot bonuses are tracked automatically. Payouts of cash balances are processed according to your partner agreement after approval — contact us when you reach a balance you want settled.', 'free-backlinks-generator' ); ?></p>
		<div class="fbg-mkt-table-wrap">
			<table class="fbg-mkt-table">
				<thead>
					<tr>
						<th scope="col"><?php esc_html_e( 'Signal', 'free-backlinks-generator' ); ?></th>
						<th scope="col"><?php esc_html_e( 'What you earn', 'free-backlinks-generator' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><?php esc_html_e( '1,000 attributed visits from organic search (per visitor referrer)', 'free-backlinks-generator' ); ?></td>
						<td class="fbg-mkt-highlight"><?php esc_html_e( '$2 added to your affiliate balance (repeats every 1,000 organic)', 'free-backlinks-generator' ); ?></td>
					</tr>
					<tr>
						<td><?php esc_html_e( '1,000 total attributed engagements (any mix: reading posts, sidebar contact, etc.)', 'free-backlinks-generator' ); ?></td>
						<td class="fbg-mkt-highlight"><?php esc_html_e( '10 extra guest-post writing slots on your member account', 'free-backlinks-generator' ); ?></td>
					</tr>
					<tr>
						<td colspan="2"><?php esc_html_e( 'Self-clicks and bot traffic may be discarded. One view credit per post per IP per day to limit abuse.', 'free-backlinks-generator' ); ?></td>
					</tr>
				</tbody>
			</table>
		</div>
	</section>

	<section class="fbg-mkt-section fbg-mkt-section--alt">
		<div class="fbg-container">
			<h2><?php esc_html_e( 'How it works', 'free-backlinks-generator' ); ?></h2>
			<p class="fbg-mkt-section__sub"><?php esc_html_e( 'From link sharing to tracked engagement.', 'free-backlinks-generator' ); ?></p>
			<ol class="fbg-mkt-steps">
				<li>
					<strong><?php esc_html_e( 'Apply or get approved', 'free-backlinks-generator' ); ?></strong>
					<?php esc_html_e( 'After an admin activates your partner status, the link above starts earning credit. New partners usually apply with the form below; existing members may be added directly in the dashboard.', 'free-backlinks-generator' ); ?>
				</li>
				<li>
					<strong><?php esc_html_e( 'Visitors land with fbg_ref', 'free-backlinks-generator' ); ?></strong>
					<?php esc_html_e( 'We set a 90-day cookie so later visits can stay attributed when reasonable.', 'free-backlinks-generator' ); ?>
				</li>
				<li>
					<strong><?php esc_html_e( 'They read or message', 'free-backlinks-generator' ); ?></strong>
					<?php esc_html_e( 'Opening community posts (once per post per day per IP) or sending a sidebar message records engagement for you.', 'free-backlinks-generator' ); ?>
				</li>
				<li>
					<strong><?php esc_html_e( 'You collect balance + slots', 'free-backlinks-generator' ); ?></strong>
					<?php esc_html_e( 'Organic milestones pay into your USD balance; total milestones grant bonus guest-post slots. Check your dashboard for running totals.', 'free-backlinks-generator' ); ?>
				</li>
			</ol>
		</div>
	</section>

	<section class="fbg-mkt-section fbg-container">
		<h2><?php esc_html_e( 'Who we partner with', 'free-backlinks-generator' ); ?></h2>
		<div class="fbg-mkt-grid fbg-mkt-grid--3">
			<div class="fbg-mkt-card">
				<div class="fbg-mkt-card__icon" aria-hidden="true">📣</div>
				<h3><?php esc_html_e( 'Content creators', 'free-backlinks-generator' ); ?></h3>
				<p><?php esc_html_e( 'Newsletters, YouTube, and blogs in SEO, marketing, and online business with engaged audiences.', 'free-backlinks-generator' ); ?></p>
			</div>
			<div class="fbg-mkt-card">
				<div class="fbg-mkt-card__icon" aria-hidden="true">🧰</div>
				<h3><?php esc_html_e( 'Tool directories', 'free-backlinks-generator' ); ?></h3>
				<p><?php esc_html_e( 'Comparison sites and resource hubs that list legitimate growth and link-building tools.', 'free-backlinks-generator' ); ?></p>
			</div>
			<div class="fbg-mkt-card">
				<div class="fbg-mkt-card__icon" aria-hidden="true">🎓</div>
				<h3><?php esc_html_e( 'Educators', 'free-backlinks-generator' ); ?></h3>
				<p><?php esc_html_e( 'Course creators and agencies teaching SEO who want a vetted platform to recommend.', 'free-backlinks-generator' ); ?></p>
			</div>
		</div>
	</section>

	<section class="fbg-mkt-section fbg-mkt-section--alt">
		<div class="fbg-container">
			<h2><?php esc_html_e( 'FAQ', 'free-backlinks-generator' ); ?></h2>
			<div class="fbg-mkt-faq">
				<details>
					<summary><?php esc_html_e( 'Is there a cost to join?', 'free-backlinks-generator' ); ?></summary>
					<p><?php esc_html_e( 'No. The program is free. We only approve partners who align with our brand and community standards.', 'free-backlinks-generator' ); ?></p>
				</details>
				<details>
					<summary><?php esc_html_e( 'What traffic sources are allowed?', 'free-backlinks-generator' ); ?></summary>
					<p><?php esc_html_e( 'Organic content, email, and paid search/social are allowed if disclosed. No spyware, misleading claims, or trademark bidding on our brand without written permission.', 'free-backlinks-generator' ); ?></p>
				</details>
				<details>
					<summary><?php esc_html_e( 'How do I track performance?', 'free-backlinks-generator' ); ?></summary>
					<p><?php esc_html_e( 'Your dashboard shows referral visit totals, organic counts, USD balance, and notifications when you hit a milestone. Detailed exports can be requested from support.', 'free-backlinks-generator' ); ?></p>
				</details>
				<details>
					<summary><?php esc_html_e( 'What counts as “organic”?', 'free-backlinks-generator' ); ?></summary>
					<p><?php esc_html_e( 'We look at the browser referrer for major search engines (e.g. Google, Bing, DuckDuckGo). Direct or social traffic still counts toward the 1,000-visit bonus guest-post slots.', 'free-backlinks-generator' ); ?></p>
				</details>
			</div>
		</div>
	</section>

	<section class="fbg-mkt-section fbg-container" id="apply">
		<form id="fbg-affiliate-form" class="fbg-aff-form" novalidate>
			<h2><?php esc_html_e( 'Apply to become a partner', 'free-backlinks-generator' ); ?></h2>
			<p class="fbg-mkt-section__sub" style="margin-bottom: var(--space-lg);"><?php esc_html_e( 'We respond within two business days. All fields are required.', 'free-backlinks-generator' ); ?></p>
			<div class="fbg-field">
				<label for="fbg-aff-name"><?php esc_html_e( 'Full name', 'free-backlinks-generator' ); ?></label>
				<input type="text" id="fbg-aff-name" name="full_name" required maxlength="120" autocomplete="name">
			</div>
			<div class="fbg-field">
				<label for="fbg-aff-email"><?php esc_html_e( 'Email', 'free-backlinks-generator' ); ?></label>
				<input type="email" id="fbg-aff-email" name="email" required autocomplete="email">
			</div>
			<div class="fbg-field">
				<label for="fbg-aff-site"><?php esc_html_e( 'Primary website or channel URL', 'free-backlinks-generator' ); ?></label>
				<input type="url" id="fbg-aff-site" name="website" required placeholder="https://" autocomplete="url">
			</div>
			<div class="fbg-field">
				<label for="fbg-aff-audience"><?php esc_html_e( 'Monthly reach (estimate)', 'free-backlinks-generator' ); ?></label>
				<select id="fbg-aff-audience" name="audience" required>
					<option value=""><?php esc_html_e( 'Select…', 'free-backlinks-generator' ); ?></option>
					<option value="under-5k"><?php esc_html_e( 'Under 5,000', 'free-backlinks-generator' ); ?></option>
					<option value="5k-25k"><?php esc_html_e( '5,000 – 25,000', 'free-backlinks-generator' ); ?></option>
					<option value="25k-100k"><?php esc_html_e( '25,000 – 100,000', 'free-backlinks-generator' ); ?></option>
					<option value="100k-plus"><?php esc_html_e( '100,000+', 'free-backlinks-generator' ); ?></option>
				</select>
			</div>
			<div class="fbg-field">
				<label for="fbg-aff-niche"><?php esc_html_e( 'Primary niche', 'free-backlinks-generator' ); ?></label>
				<input type="text" id="fbg-aff-niche" name="niche" required maxlength="80" placeholder="<?php esc_attr_e( 'e.g. SEO, SaaS marketing', 'free-backlinks-generator' ); ?>">
			</div>
			<div class="fbg-field">
				<label for="fbg-aff-plan"><?php esc_html_e( 'How will you promote us?', 'free-backlinks-generator' ); ?></label>
				<textarea id="fbg-aff-plan" name="promo_plan" required maxlength="2000" placeholder="<?php esc_attr_e( 'Briefly describe your channels and content strategy.', 'free-backlinks-generator' ); ?>"></textarea>
			</div>
			<label class="fbg-check">
				<input type="checkbox" name="agree_terms" value="1" required>
				<span><?php esc_html_e( 'I agree to the partner terms, will disclose affiliate relationships where required by law, and will not use spam or misleading tactics.', 'free-backlinks-generator' ); ?></span>
			</label>
			<button type="submit" class="btn-primary" id="fbg-aff-submit"><?php esc_html_e( 'Submit application', 'free-backlinks-generator' ); ?></button>
			<div id="fbg-aff-message" class="fbg-aff-alert" hidden role="status"></div>
		</form>
	</section>
</main>
<?php
get_footer();
