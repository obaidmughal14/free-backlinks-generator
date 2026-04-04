<?php
/**
 * Dashboard tab: plans, Pro upgrade, checkout.
 *
 * @package Free_Backlinks_Generator
 */
$uid           = get_current_user_id();
$is_pro        = 'pro' === get_user_meta( $uid, '_fbg_membership', true );
$checkout      = function_exists( 'fbg_get_pro_checkout_url' ) ? fbg_get_pro_checkout_url() : '';
$checkout_ann  = function_exists( 'fbg_get_pro_checkout_url_annual' ) ? fbg_get_pro_checkout_url_annual() : '';
$portal        = function_exists( 'fbg_get_pro_billing_portal_url' ) ? fbg_get_pro_billing_portal_url() : '';
$price_m       = get_theme_mod( 'fbg_pro_price_monthly', '$19/mo' );
$price_a       = get_theme_mod( 'fbg_pro_price_annual', '$190/yr' );
$slot_lim      = function_exists( 'fbg_get_user_post_slot_limit' ) ? fbg_get_user_post_slot_limit( $uid ) : 1;
$is_limited    = $slot_lim < 900 && ! $is_pro;
?>
<section class="fbg-dash-panel" id="tab-upgrade" data-panel="upgrade" hidden tabindex="-1">
	<header class="fbg-dash-panel__head fbg-dash-panel__head--hero">
		<h2 class="fbg-dash-panel__title"><?php esc_html_e( 'Plans & Pro', 'free-backlinks-generator' ); ?></h2>
		<p class="fbg-dash-panel__lead"><?php esc_html_e( 'Compare Free and Pro, then complete checkout on our secure payment page when you are ready.', 'free-backlinks-generator' ); ?></p>
	</header>

	<?php if ( current_user_can( 'manage_options' ) && ! $checkout ) : ?>
		<div class="fbg-dash-callout fbg-dash-callout--admin">
			<p class="fbg-dash-callout__p">
				<strong><?php esc_html_e( 'Site admin:', 'free-backlinks-generator' ); ?></strong>
				<?php esc_html_e( 'Add your Stripe Payment Link or checkout URL under', 'free-backlinks-generator' ); ?>
				<a href="<?php echo esc_url( admin_url( 'customize.php?autofocus[section]=fbg_pro_membership&autofocus[panel]=fbg_theme_panel' ) ); ?>"><?php esc_html_e( 'Appearance → Customize → FBG theme options → Pro upgrade', 'free-backlinks-generator' ); ?></a>
				<?php esc_html_e( 'so members can complete payment from this tab.', 'free-backlinks-generator' ); ?>
			</p>
		</div>
	<?php endif; ?>

	<?php if ( $is_pro ) : ?>
		<div class="fbg-dash-plan-current fbg-dash-plan-current--pro">
			<div class="fbg-dash-plan-current__badge"><?php esc_html_e( 'Current plan', 'free-backlinks-generator' ); ?></div>
			<h3 class="fbg-dash-plan-current__name"><?php esc_html_e( 'Pro', 'free-backlinks-generator' ); ?></h3>
			<p class="fbg-dash-plan-current__desc"><?php esc_html_e( 'You have unlimited guest-post slots and the highest per-post link limits. Thank you for supporting the platform.', 'free-backlinks-generator' ); ?></p>
			<?php if ( $portal ) : ?>
				<a class="btn-primary" href="<?php echo esc_url( $portal ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Manage billing & subscription', 'free-backlinks-generator' ); ?> →</a>
			<?php else : ?>
				<p class="fbg-dash-plan-current__hint"><?php esc_html_e( 'For invoices or plan changes, contact support from the Contact page.', 'free-backlinks-generator' ); ?></p>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<div class="fbg-dash-plan-cards">
		<article class="fbg-dash-plan-card<?php echo $is_pro ? '' : ' fbg-dash-plan-card--active-free'; ?>">
			<div class="fbg-dash-plan-card__top">
				<h3 class="fbg-dash-plan-card__title"><?php esc_html_e( 'Free', 'free-backlinks-generator' ); ?></h3>
				<p class="fbg-dash-plan-card__price"><span class="fbg-dash-plan-card__amount"><?php esc_html_e( '$0', 'free-backlinks-generator' ); ?></span></p>
			</div>
			<ul class="fbg-dash-plan-card__list">
				<li><?php esc_html_e( 'Community tier system with growing link limits per post', 'free-backlinks-generator' ); ?></li>
				<li><?php esc_html_e( 'Guest-post slots from reading + community engagement', 'free-backlinks-generator' ); ?></li>
				<li><?php esc_html_e( 'Member directory & notifications', 'free-backlinks-generator' ); ?></li>
			</ul>
			<?php if ( ! $is_pro ) : ?>
				<p class="fbg-dash-plan-card__foot"><?php esc_html_e( 'Great for getting started.', 'free-backlinks-generator' ); ?></p>
			<?php endif; ?>
		</article>

		<article class="fbg-dash-plan-card fbg-dash-plan-card--pro<?php echo $is_pro ? ' fbg-dash-plan-card--highlight' : ''; ?>">
			<div class="fbg-dash-plan-card__ribbon"><?php esc_html_e( 'Pro', 'free-backlinks-generator' ); ?></div>
			<div class="fbg-dash-plan-card__top">
				<h3 class="fbg-dash-plan-card__title"><?php esc_html_e( 'Pro', 'free-backlinks-generator' ); ?></h3>
				<p class="fbg-dash-plan-card__price">
					<span class="fbg-dash-plan-card__amount"><?php echo esc_html( $price_m ); ?></span>
					<?php if ( $checkout_ann ) : ?>
						<span class="fbg-dash-plan-card__alt"><?php echo esc_html( $price_a ); ?></span>
					<?php endif; ?>
				</p>
			</div>
			<ul class="fbg-dash-plan-card__list">
				<li><?php esc_html_e( 'Unlimited active guest-post slots', 'free-backlinks-generator' ); ?></li>
				<li><?php esc_html_e( 'Up to 10 contextual backlinks per approved post (top tier)', 'free-backlinks-generator' ); ?></li>
				<li><?php esc_html_e( 'Priority consideration for reviews (where offered)', 'free-backlinks-generator' ); ?></li>
				<li><?php esc_html_e( 'Support the platform and unlock growth mode', 'free-backlinks-generator' ); ?></li>
			</ul>
			<?php if ( ! $is_pro ) : ?>
				<div class="fbg-dash-plan-card__cta">
					<?php if ( $checkout ) : ?>
						<a class="btn-primary fbg-dash-plan-card__btn" href="<?php echo esc_url( $checkout ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Checkout — primary plan', 'free-backlinks-generator' ); ?> →</a>
					<?php else : ?>
						<p class="fbg-dash-plan-card__pending"><?php esc_html_e( 'Checkout link is not configured yet. Please check back soon or contact the site owner.', 'free-backlinks-generator' ); ?></p>
					<?php endif; ?>
					<?php if ( $checkout_ann ) : ?>
						<a class="btn-ghost fbg-dash-plan-card__btn-secondary" href="<?php echo esc_url( $checkout_ann ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Annual checkout', 'free-backlinks-generator' ); ?> →</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</article>
	</div>

	<div class="fbg-dash-compare-wrap">
		<h3 class="fbg-dash-subhead"><?php esc_html_e( 'Side-by-side', 'free-backlinks-generator' ); ?></h3>
		<div class="fbg-dash-compare" role="region" aria-label="<?php esc_attr_e( 'Plan comparison', 'free-backlinks-generator' ); ?>">
			<table class="fbg-dash-compare-table">
				<thead>
					<tr>
						<th scope="col"><?php esc_html_e( 'Feature', 'free-backlinks-generator' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Free', 'free-backlinks-generator' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Pro', 'free-backlinks-generator' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><?php esc_html_e( 'Guest-post slots', 'free-backlinks-generator' ); ?></td>
						<td><?php esc_html_e( 'Earn more by reading peers', 'free-backlinks-generator' ); ?></td>
						<td><?php esc_html_e( 'Unlimited', 'free-backlinks-generator' ); ?></td>
					</tr>
					<tr>
						<td><?php esc_html_e( 'Links per post (at top tier)', 'free-backlinks-generator' ); ?></td>
						<td><?php esc_html_e( 'Up to 10 (tiered)', 'free-backlinks-generator' ); ?></td>
						<td><?php esc_html_e( 'Up to 10', 'free-backlinks-generator' ); ?></td>
					</tr>
					<tr>
						<td><?php esc_html_e( 'Analytics & dashboard', 'free-backlinks-generator' ); ?></td>
						<td><?php esc_html_e( 'Yes', 'free-backlinks-generator' ); ?></td>
						<td><?php esc_html_e( 'Yes', 'free-backlinks-generator' ); ?></td>
					</tr>
					<tr>
						<td><?php esc_html_e( 'Payment', 'free-backlinks-generator' ); ?></td>
						<td><?php esc_html_e( '—', 'free-backlinks-generator' ); ?></td>
						<td><?php esc_html_e( 'Secure checkout (Stripe, etc.)', 'free-backlinks-generator' ); ?></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>

	<?php if ( $is_limited && ! $is_pro ) : ?>
		<p class="fbg-dash-footnote"><?php esc_html_e( 'After payment, your account is upgraded to Pro on this site (site owners may need to confirm webhook or manual fulfillment depending on their payment setup).', 'free-backlinks-generator' ); ?></p>
	<?php endif; ?>
</section>
