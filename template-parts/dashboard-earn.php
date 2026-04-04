<?php
/**
 * Dashboard tab: earn, charts, payouts, free writing slots.
 *
 * @package Free_Backlinks_Generator
 */
$uid            = get_current_user_id();
$partner        = function_exists( 'fbg_is_user_affiliate_partner' ) && fbg_is_user_affiliate_partner( $uid );
$balance        = (float) get_user_meta( $uid, '_fbg_aff_balance_usd', true );
$pending_req    = function_exists( 'fbg_earn_user_pending_payout_sum' ) ? fbg_earn_user_pending_payout_sum( $uid ) : 0.0;
$spendable      = function_exists( 'fbg_earn_user_spendable_balance' ) ? fbg_earn_user_spendable_balance( $uid ) : max( 0, $balance - $pending_req );
$lifetime_paid  = function_exists( 'fbg_earn_user_lifetime_paid' ) ? fbg_earn_user_lifetime_paid( $uid ) : 0.0;
$bonus_slots    = function_exists( 'fbg_get_user_bonus_post_slots' ) ? fbg_get_user_bonus_post_slots( $uid ) : 0;
$reads_done     = function_exists( 'fbg_get_user_completed_peer_reads' ) ? count( fbg_get_user_completed_peer_reads( $uid ) ) : 0;
$from_reads     = function_exists( 'FBG_READS_PER_SLOT_PAIR' ) ? (int) floor( $reads_done / FBG_READS_PER_SLOT_PAIR ) : 0;
$base_slots     = function_exists( 'FBG_BASE_POST_SLOTS' ) ? (int) FBG_BASE_POST_SLOTS : 1;
$slot_limit     = function_exists( 'fbg_get_user_post_slot_limit' ) ? fbg_get_user_post_slot_limit( $uid ) : 1;
$slot_use       = function_exists( 'fbg_count_user_guest_posts_for_cap' ) ? fbg_count_user_guest_posts_for_cap( $uid ) : 0;
$min_payout     = function_exists( 'fbg_earn_min_payout_usd' ) ? fbg_earn_min_payout_usd() : 25;
$chart          = $partner && function_exists( 'fbg_earn_get_referral_chart_series' ) ? fbg_earn_get_referral_chart_series( $uid, 6 ) : array();
$max_chart      = 1;
foreach ( $chart as $c ) {
	$max_chart = max( $max_chart, (int) $c['count'] );
}
$ledger = ( $partner && function_exists( 'fbg_earn_get_user_ledger' ) ) ? fbg_earn_get_user_ledger( $uid, 20 ) : array();
$aff_total = (int) get_user_meta( $uid, '_fbg_aff_referral_total', true );
$aff_org   = (int) get_user_meta( $uid, '_fbg_aff_referral_organic', true );
?>
<section class="fbg-dash-panel" id="tab-earn" data-panel="earn" hidden tabindex="-1">
	<header class="fbg-dash-panel__head fbg-dash-panel__head--hero">
		<h2 class="fbg-dash-panel__title"><?php esc_html_e( 'Earn', 'free-backlinks-generator' ); ?></h2>
		<p class="fbg-dash-panel__lead"><?php esc_html_e( 'Track free writing capacity, partner earnings, and payout requests in one place.', 'free-backlinks-generator' ); ?></p>
	</header>

	<h3 class="fbg-dash-subhead"><?php esc_html_e( 'Free writing access', 'free-backlinks-generator' ); ?></h3>
	<div class="fbg-dash-earn-grid">
		<div class="fbg-dash-earn-card">
			<span class="fbg-dash-earn-card__label"><?php esc_html_e( 'Slots in use / max', 'free-backlinks-generator' ); ?></span>
			<p class="fbg-dash-earn-card__value"><?php echo esc_html( (string) (int) $slot_use ); ?> / <?php echo esc_html( (string) (int) $slot_limit ); ?></p>
			<p class="fbg-dash-earn-card__hint"><?php esc_html_e( 'Guest posts you can have at once (draft through published).', 'free-backlinks-generator' ); ?></p>
		</div>
		<div class="fbg-dash-earn-card">
			<span class="fbg-dash-earn-card__label"><?php esc_html_e( 'From reading peers', 'free-backlinks-generator' ); ?></span>
			<p class="fbg-dash-earn-card__value">+<?php echo esc_html( (string) (int) $from_reads ); ?></p>
			<p class="fbg-dash-earn-card__hint">
				<?php
				printf(
					/* translators: 1: posts read, 2: reads per slot pair */
					esc_html__( '%1$d posts fully read — every %2$d unlock +1 slot.', 'free-backlinks-generator' ),
					(int) $reads_done,
					(int) ( defined( 'FBG_READS_PER_SLOT_PAIR' ) ? FBG_READS_PER_SLOT_PAIR : 2 )
				);
				?>
			</p>
		</div>
		<div class="fbg-dash-earn-card fbg-dash-earn-card--accent">
			<span class="fbg-dash-earn-card__label"><?php esc_html_e( 'Bonus slots (referrals)', 'free-backlinks-generator' ); ?></span>
			<p class="fbg-dash-earn-card__value">+<?php echo esc_html( (string) (int) $bonus_slots ); ?></p>
			<p class="fbg-dash-earn-card__hint"><?php esc_html_e( 'Earned as an approved partner when referral traffic hits milestones.', 'free-backlinks-generator' ); ?></p>
		</div>
		<div class="fbg-dash-earn-card">
			<span class="fbg-dash-earn-card__label"><?php esc_html_e( 'Starter allowance', 'free-backlinks-generator' ); ?></span>
			<p class="fbg-dash-earn-card__value"><?php echo esc_html( (string) (int) $base_slots ); ?></p>
			<p class="fbg-dash-earn-card__hint"><?php esc_html_e( 'Base slots for every member before reads and bonuses.', 'free-backlinks-generator' ); ?></p>
		</div>
	</div>

	<?php if ( ! $partner ) : ?>
		<div class="fbg-dash-earn-upsell">
			<h3 class="fbg-dash-subhead"><?php esc_html_e( 'Partner earnings & payouts', 'free-backlinks-generator' ); ?></h3>
			<p><?php esc_html_e( 'USD wallet, referral charts, and payout requests unlock when you are an approved affiliate partner.', 'free-backlinks-generator' ); ?></p>
			<a class="btn-primary" href="#affiliate" data-tab-trigger="affiliate"><?php esc_html_e( 'Open Affiliate tab', 'free-backlinks-generator' ); ?> →</a>
		</div>
	<?php else : ?>
		<h3 class="fbg-dash-subhead"><?php esc_html_e( 'Partner wallet', 'free-backlinks-generator' ); ?></h3>
		<div class="fbg-dash-earn-grid fbg-dash-earn-grid--wallet">
			<div class="fbg-dash-earn-card fbg-dash-earn-card--wallet">
				<span class="fbg-dash-earn-card__label"><?php esc_html_e( 'Available balance', 'free-backlinks-generator' ); ?></span>
				<p class="fbg-dash-earn-card__value">$<?php echo esc_html( number_format_i18n( $balance, 2 ) ); ?></p>
				<p class="fbg-dash-earn-card__hint"><?php esc_html_e( 'Credited from organic-search referral milestones (per program rules).', 'free-backlinks-generator' ); ?></p>
			</div>
			<div class="fbg-dash-earn-card">
				<span class="fbg-dash-earn-card__label"><?php esc_html_e( 'Pending payout requests', 'free-backlinks-generator' ); ?></span>
				<p class="fbg-dash-earn-card__value">$<?php echo esc_html( number_format_i18n( $pending_req, 2 ) ); ?></p>
			</div>
			<div class="fbg-dash-earn-card">
				<span class="fbg-dash-earn-card__label"><?php esc_html_e( 'Requestable now', 'free-backlinks-generator' ); ?></span>
				<p class="fbg-dash-earn-card__value">$<?php echo esc_html( number_format_i18n( $spendable, 2 ) ); ?></p>
			</div>
			<div class="fbg-dash-earn-card">
				<span class="fbg-dash-earn-card__label"><?php esc_html_e( 'Lifetime paid out', 'free-backlinks-generator' ); ?></span>
				<p class="fbg-dash-earn-card__value">$<?php echo esc_html( number_format_i18n( $lifetime_paid, 2 ) ); ?></p>
			</div>
		</div>

		<div class="fbg-dash-earn-chart-block">
			<h3 class="fbg-dash-subhead"><?php esc_html_e( 'Referral visits by month', 'free-backlinks-generator' ); ?></h3>
			<p class="fbg-dash-earn-chart-desc"><?php esc_html_e( 'Each bar counts attributed referral hits (community post views from your links) in that calendar month.', 'free-backlinks-generator' ); ?></p>
			<div class="fbg-earn-bar-chart" role="img" aria-label="<?php esc_attr_e( 'Referrals per month', 'free-backlinks-generator' ); ?>">
				<?php foreach ( $chart as $c ) : ?>
					<?php
					$h = $max_chart > 0 ? round( ( (int) $c['count'] / $max_chart ) * 100 ) : 0;
					$h = max( 4, $h );
					?>
					<div class="fbg-earn-bar-wrap">
						<div class="fbg-earn-bar" style="height: <?php echo esc_attr( (string) $h ); ?>%;" title="<?php echo esc_attr( (string) (int) $c['count'] ); ?>"></div>
						<span class="fbg-earn-bar-label"><?php echo esc_html( $c['label'] ); ?></span>
						<span class="fbg-earn-bar-count"><?php echo esc_html( (string) (int) $c['count'] ); ?></span>
					</div>
				<?php endforeach; ?>
			</div>
			<p class="fbg-dash-earn-meta"><?php printf( esc_html__( 'All-time referral hits: %1$s — organic-tagged: %2$s', 'free-backlinks-generator' ), esc_html( number_format_i18n( $aff_total ) ), esc_html( number_format_i18n( $aff_org ) ) ); ?></p>
		</div>

		<div class="fbg-dash-earn-payout">
			<h3 class="fbg-dash-subhead"><?php esc_html_e( 'Request a payout', 'free-backlinks-generator' ); ?></h3>
			<p class="fbg-dash-earn-payout__hint">
				<?php
				printf(
					/* translators: %s: minimum USD */
					esc_html__( 'Minimum request: %s. We review against program terms; you will be notified when paid or if more information is needed.', 'free-backlinks-generator' ),
					'$' . esc_html( number_format_i18n( $min_payout, 2 ) )
				);
				?>
			</p>
			<div class="fbg-alert fbg-alert--success" id="fbg-earn-payout-toast" role="status" hidden></div>
			<form id="fbg-earn-payout-form" class="fbg-dash-earn-payout-form">
				<div class="fbg-field">
					<label for="fbg-earn-payout-amount"><?php esc_html_e( 'Amount (USD)', 'free-backlinks-generator' ); ?></label>
					<input type="number" id="fbg-earn-payout-amount" name="amount" min="<?php echo esc_attr( (string) $min_payout ); ?>" max="<?php echo esc_attr( (string) max( $min_payout, $spendable ) ); ?>" step="0.01" value="<?php echo esc_attr( $spendable >= $min_payout ? (string) round( $spendable, 2 ) : '' ); ?>" <?php echo $spendable < $min_payout ? 'disabled' : ''; ?> required>
				</div>
				<div class="fbg-field">
					<label for="fbg-earn-payout-note"><?php esc_html_e( 'Payment details / note (optional)', 'free-backlinks-generator' ); ?></label>
					<textarea id="fbg-earn-payout-note" name="note" rows="3" class="fbg-dash-textarea" placeholder="<?php esc_attr_e( 'PayPal email, bank reference preference, etc.', 'free-backlinks-generator' ); ?>"></textarea>
				</div>
				<button type="submit" class="btn-primary" <?php echo $spendable < $min_payout ? 'disabled' : ''; ?>><?php esc_html_e( 'Submit payout request', 'free-backlinks-generator' ); ?></button>
			</form>
			<?php if ( $spendable < $min_payout ) : ?>
				<p class="fbg-dash-earn-payout__warn"><?php esc_html_e( 'Balance is below the minimum payout threshold or held in pending requests.', 'free-backlinks-generator' ); ?></p>
			<?php endif; ?>
		</div>

		<h3 class="fbg-dash-subhead"><?php esc_html_e( 'Activity log', 'free-backlinks-generator' ); ?></h3>
		<div class="fbg-table-wrap fbg-dash-earn-ledger">
			<table class="fbg-table">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Date', 'free-backlinks-generator' ); ?></th>
						<th><?php esc_html_e( 'Type', 'free-backlinks-generator' ); ?></th>
						<th><?php esc_html_e( 'USD', 'free-backlinks-generator' ); ?></th>
						<th><?php esc_html_e( 'Slots', 'free-backlinks-generator' ); ?></th>
						<th><?php esc_html_e( 'Note', 'free-backlinks-generator' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php if ( empty( $ledger ) ) : ?>
						<tr><td colspan="5"><?php esc_html_e( 'No ledger entries yet — new credits appear after the next qualifying events.', 'free-backlinks-generator' ); ?></td></tr>
					<?php else : ?>
						<?php foreach ( $ledger as $row ) : ?>
							<tr>
								<td><?php echo esc_html( wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $row->created_at . ' UTC' ) ) ); ?></td>
								<td><?php echo esc_html( function_exists( 'fbg_earn_ledger_type_label' ) ? fbg_earn_ledger_type_label( $row->entry_type ) : $row->entry_type ); ?></td>
								<td><?php echo esc_html( number_format_i18n( (float) $row->amount_usd, 2 ) ); ?></td>
								<td>
									<?php
									$sd = (int) $row->slots_delta;
									echo 0 === $sd ? '—' : esc_html( sprintf( '%+d', $sd ) );
									?>
								</td>
								<td><?php echo $row->context ? esc_html( wp_trim_words( wp_strip_all_tags( (string) $row->context ), 12 ) ) : '—'; ?></td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	<?php endif; ?>
</section>
