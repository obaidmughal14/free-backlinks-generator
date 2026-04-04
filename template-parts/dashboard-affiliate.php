<?php
/**
 * Dashboard tab: affiliate links, UTM guide (approved partners).
 *
 * @package Free_Backlinks_Generator
 */
$uid     = get_current_user_id();
$partner = function_exists( 'fbg_is_user_affiliate_partner' ) && fbg_is_user_affiliate_partner( $uid );
$ref_url = function_exists( 'fbg_get_user_affiliate_referral_url' ) ? fbg_get_user_affiliate_referral_url( $uid ) : home_url( '/' );
$utm_url = function_exists( 'fbg_get_user_affiliate_utm_url' ) ? fbg_get_user_affiliate_utm_url( $uid ) : $ref_url;
$aff_app = get_user_meta( $uid, '_fbg_affiliate_app_status', true );
$total   = (int) get_user_meta( $uid, '_fbg_aff_referral_total', true );
$organic = (int) get_user_meta( $uid, '_fbg_aff_referral_organic', true );
$balance = (float) get_user_meta( $uid, '_fbg_aff_balance_usd', true );
$apply   = home_url( '/affiliate-program/' );
?>
<section class="fbg-dash-panel" id="tab-affiliate" data-panel="affiliate" hidden tabindex="-1">
	<header class="fbg-dash-panel__head">
		<h2 class="fbg-dash-panel__title"><?php esc_html_e( 'Affiliate', 'free-backlinks-generator' ); ?></h2>
		<p class="fbg-dash-panel__lead"><?php esc_html_e( 'Share your link to earn referral credit when visitors read posts or engage. UTM links help you measure traffic in Google Analytics or other tools.', 'free-backlinks-generator' ); ?></p>
	</header>

	<?php if ( ! $partner ) : ?>
		<div class="fbg-dash-aff-gate">
			<div class="fbg-dash-aff-gate__icon" aria-hidden="true">🔗</div>
			<h3 class="fbg-dash-aff-gate__title"><?php esc_html_e( 'Partner program', 'free-backlinks-generator' ); ?></h3>
			<?php if ( 'pending' === $aff_app || 'needs_info' === $aff_app ) : ?>
				<p class="fbg-dash-aff-gate__text"><?php esc_html_e( 'Your application is being reviewed. We will email you when there is an update.', 'free-backlinks-generator' ); ?></p>
			<?php elseif ( 'rejected' === $aff_app ) : ?>
				<p class="fbg-dash-aff-gate__text"><?php esc_html_e( 'Your last application was not approved. You can reach out via Contact if you have questions.', 'free-backlinks-generator' ); ?></p>
			<?php else : ?>
				<p class="fbg-dash-aff-gate__text"><?php esc_html_e( 'Apply once to unlock your personal referral link, UTM builder, and payout-tracked stats on this tab.', 'free-backlinks-generator' ); ?></p>
			<?php endif; ?>
			<a class="btn-primary" href="<?php echo esc_url( $apply ); ?>"><?php esc_html_e( 'Open Affiliate Program page', 'free-backlinks-generator' ); ?> →</a>
		</div>
	<?php else : ?>
		<div class="fbg-dash-aff-stats">
			<div class="fbg-dash-aff-stat">
				<span class="fbg-dash-aff-stat__val"><?php echo esc_html( number_format_i18n( $total ) ); ?></span>
				<span class="fbg-dash-aff-stat__lab"><?php esc_html_e( 'Referral visits', 'free-backlinks-generator' ); ?></span>
			</div>
			<div class="fbg-dash-aff-stat">
				<span class="fbg-dash-aff-stat__val"><?php echo esc_html( number_format_i18n( $organic ) ); ?></span>
				<span class="fbg-dash-aff-stat__lab"><?php esc_html_e( 'From organic search', 'free-backlinks-generator' ); ?></span>
			</div>
			<div class="fbg-dash-aff-stat">
				<span class="fbg-dash-aff-stat__val">$<?php echo esc_html( number_format_i18n( $balance, 2 ) ); ?></span>
				<span class="fbg-dash-aff-stat__lab"><?php esc_html_e( 'Balance (USD)', 'free-backlinks-generator' ); ?></span>
			</div>
		</div>

		<div class="fbg-dash-copy-block">
			<label class="fbg-dash-copy-block__label" for="fbg-aff-ref-url"><?php esc_html_e( 'Your referral link', 'free-backlinks-generator' ); ?></label>
			<p class="fbg-dash-copy-block__hint"><?php esc_html_e( 'Anyone who opens this URL gets a 90-day cookie. Credit applies when they view community posts or take qualifying actions.', 'free-backlinks-generator' ); ?></p>
			<div class="fbg-dash-copy-row">
				<input type="text" id="fbg-aff-ref-url" class="fbg-dash-copy-input mono" readonly value="<?php echo esc_attr( $ref_url ); ?>">
				<button type="button" class="btn-primary fbg-dash-copy-btn" data-fbg-copy="#fbg-aff-ref-url"><?php esc_html_e( 'Copy', 'free-backlinks-generator' ); ?></button>
			</div>
		</div>

		<div class="fbg-dash-copy-block">
			<label class="fbg-dash-copy-block__label" for="fbg-aff-utm-url"><?php esc_html_e( 'Link with UTM tags (analytics)', 'free-backlinks-generator' ); ?></label>
			<p class="fbg-dash-copy-block__hint"><?php esc_html_e( 'Same attribution as above, plus standard UTM parameters so you can filter this traffic in Analytics.', 'free-backlinks-generator' ); ?></p>
			<div class="fbg-dash-copy-row">
				<input type="text" id="fbg-aff-utm-url" class="fbg-dash-copy-input mono" readonly value="<?php echo esc_attr( $utm_url ); ?>">
				<button type="button" class="btn-primary fbg-dash-copy-btn" data-fbg-copy="#fbg-aff-utm-url"><?php esc_html_e( 'Copy', 'free-backlinks-generator' ); ?></button>
			</div>
			<dl class="fbg-dash-utm-legend">
				<dt><code>utm_source</code></dt>
				<dd><code>fbg_partner</code> — <?php esc_html_e( 'identifies traffic from this program', 'free-backlinks-generator' ); ?></dd>
				<dt><code>utm_medium</code></dt>
				<dd><code>referral</code> — <?php esc_html_e( 'standard for partner links', 'free-backlinks-generator' ); ?></dd>
				<dt><code>utm_campaign</code></dt>
				<dd><code>fbg_ref_<?php echo esc_html( (string) (int) $uid ); ?></code> — <?php esc_html_e( 'unique to your account', 'free-backlinks-generator' ); ?></dd>
			</dl>
		</div>

		<details class="fbg-dash-aff-guide">
			<summary><?php esc_html_e( 'How to use the UTM link', 'free-backlinks-generator' ); ?></summary>
			<ol class="fbg-dash-aff-guide__list">
				<li><?php esc_html_e( 'Click Copy on the UTM link above.', 'free-backlinks-generator' ); ?></li>
				<li><?php esc_html_e( 'Paste it into emails, social bios, YouTube descriptions, or ad “destination URL” fields.', 'free-backlinks-generator' ); ?></li>
				<li><?php esc_html_e( 'In Google Analytics 4, use Traffic acquisition → Session campaign or add a filter for campaign name containing your ID.', 'free-backlinks-generator' ); ?></li>
				<li><?php esc_html_e( 'You can append more query parameters if needed (e.g. &utm_content=newsletter) — keep the existing fbg_ref and UTM keys.', 'free-backlinks-generator' ); ?></li>
			</ol>
		</details>

		<p class="fbg-dash-aff-more">
			<a class="btn-ghost" href="#earn" data-tab-trigger="earn"><?php esc_html_e( 'Earn tab — wallet, charts & payouts', 'free-backlinks-generator' ); ?> →</a>
			<a class="btn-ghost" href="<?php echo esc_url( $apply ); ?>"><?php esc_html_e( 'Program details & terms', 'free-backlinks-generator' ); ?> →</a>
		</p>
	<?php endif; ?>
</section>
