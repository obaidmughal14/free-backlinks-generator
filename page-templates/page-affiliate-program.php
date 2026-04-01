<?php
/**
 * Template Name: Affiliate Program
 *
 * @package Free_Backlinks_Generator
 */

get_header();
?>
<main id="main-content" class="fbg-main">
	<section class="fbg-mkt-hero" aria-labelledby="fbg-aff-title">
		<div class="fbg-container">
			<div class="fbg-mkt-hero__badges">
				<span class="fbg-mkt-badge"><?php esc_html_e( 'Recurring commissions', 'free-backlinks-generator' ); ?></span>
				<span class="fbg-mkt-badge"><?php esc_html_e( '90-day attribution', 'free-backlinks-generator' ); ?></span>
				<span class="fbg-mkt-badge"><?php esc_html_e( 'Monthly payouts', 'free-backlinks-generator' ); ?></span>
			</div>
			<h1 id="fbg-aff-title"><?php esc_html_e( 'Earn with the Free Backlinks Generator partner program', 'free-backlinks-generator' ); ?></h1>
			<p class="fbg-mkt-hero__lead">
				<?php esc_html_e( 'Promote a platform that helps SEOs and bloggers exchange quality guest posts. You earn a share of every paid upgrade from traffic you refer — with transparent tracking and real-time stats in your partner dashboard (issued after approval).', 'free-backlinks-generator' ); ?>
			</p>
			<div class="fbg-mkt-stats" role="group" aria-label="<?php esc_attr_e( 'Program highlights', 'free-backlinks-generator' ); ?>">
				<div class="fbg-mkt-stat">
					<strong>30%</strong>
					<span><?php esc_html_e( 'Recurring on Pro subscriptions', 'free-backlinks-generator' ); ?></span>
				</div>
				<div class="fbg-mkt-stat">
					<strong>90</strong>
					<span><?php esc_html_e( 'Day cookie window', 'free-backlinks-generator' ); ?></span>
				</div>
				<div class="fbg-mkt-stat">
					<strong>$50</strong>
					<span><?php esc_html_e( 'Minimum payout threshold', 'free-backlinks-generator' ); ?></span>
				</div>
			</div>
		</div>
	</section>

	<section class="fbg-mkt-section fbg-container">
		<h2><?php esc_html_e( 'Commission structure', 'free-backlinks-generator' ); ?></h2>
		<p class="fbg-mkt-section__sub"><?php esc_html_e( 'Rates apply to net subscription revenue after discounts. Payouts are processed monthly via PayPal or bank transfer where supported.', 'free-backlinks-generator' ); ?></p>
		<div class="fbg-mkt-table-wrap">
			<table class="fbg-mkt-table">
				<thead>
					<tr>
						<th scope="col"><?php esc_html_e( 'Product', 'free-backlinks-generator' ); ?></th>
						<th scope="col"><?php esc_html_e( 'First payment', 'free-backlinks-generator' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Recurring', 'free-backlinks-generator' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><?php esc_html_e( 'Pro — monthly', 'free-backlinks-generator' ); ?></td>
						<td class="fbg-mkt-highlight">40%</td>
						<td class="fbg-mkt-highlight">30%</td>
					</tr>
					<tr>
						<td><?php esc_html_e( 'Pro — annual', 'free-backlinks-generator' ); ?></td>
						<td class="fbg-mkt-highlight">25%</td>
						<td><?php esc_html_e( 'N/A (prepaid)', 'free-backlinks-generator' ); ?></td>
					</tr>
					<tr>
						<td><?php esc_html_e( 'Agency / volume (custom)', 'free-backlinks-generator' ); ?></td>
						<td colspan="2"><?php esc_html_e( 'Negotiated — contact partnerships', 'free-backlinks-generator' ); ?></td>
					</tr>
				</tbody>
			</table>
		</div>
	</section>

	<section class="fbg-mkt-section fbg-mkt-section--alt">
		<div class="fbg-container">
			<h2><?php esc_html_e( 'How it works', 'free-backlinks-generator' ); ?></h2>
			<p class="fbg-mkt-section__sub"><?php esc_html_e( 'Four steps from application to your first commission.', 'free-backlinks-generator' ); ?></p>
			<ol class="fbg-mkt-steps">
				<li>
					<strong><?php esc_html_e( 'Apply', 'free-backlinks-generator' ); ?></strong>
					<?php esc_html_e( 'Submit the form below. We review audience fit, content quality, and compliance with our advertising guidelines.', 'free-backlinks-generator' ); ?>
				</li>
				<li>
					<strong><?php esc_html_e( 'Get your links', 'free-backlinks-generator' ); ?></strong>
					<?php esc_html_e( 'Approved partners receive a unique tracking ID, deep links, and optional creatives (banners + copy).', 'free-backlinks-generator' ); ?>
				</li>
				<li>
					<strong><?php esc_html_e( 'Share & convert', 'free-backlinks-generator' ); ?></strong>
					<?php esc_html_e( 'Referrals are attributed for 90 days from the first click. Self-referrals and coupon abuse are void.', 'free-backlinks-generator' ); ?>
				</li>
				<li>
					<strong><?php esc_html_e( 'Get paid', 'free-backlinks-generator' ); ?></strong>
					<?php esc_html_e( 'Balances over $50 are paid by the 15th of each month. You can export CSV statements from the partner portal.', 'free-backlinks-generator' ); ?>
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
					<p><?php esc_html_e( 'After approval you receive dashboard access with clicks, trials, conversions, and earnings in near real time.', 'free-backlinks-generator' ); ?></p>
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
