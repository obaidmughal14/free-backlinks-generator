<?php
/**
 * Dashboard tab: overview.
 *
 * @package Free_Backlinks_Generator
 */
$user_id = get_current_user_id();
$is_pro         = 'pro' === get_user_meta( $user_id, '_fbg_membership', true );
$slot_lim       = function_exists( 'fbg_get_user_post_slot_limit' ) ? fbg_get_user_post_slot_limit( $user_id ) : 999;
$slot_use       = function_exists( 'fbg_count_user_guest_posts_for_cap' ) ? fbg_count_user_guest_posts_for_cap( $user_id ) : 0;
$can_submit     = function_exists( 'fbg_user_can_create_guest_post' ) ? fbg_user_can_create_guest_post( $user_id ) : true;
$reads_done     = function_exists( 'fbg_get_user_completed_peer_reads' ) ? count( fbg_get_user_completed_peer_reads( $user_id ) ) : 0;
$aff_total      = (int) get_user_meta( $user_id, '_fbg_aff_referral_total', true );
$aff_org        = (int) get_user_meta( $user_id, '_fbg_aff_referral_organic', true );
$aff_balance    = (float) get_user_meta( $user_id, '_fbg_aff_balance_usd', true );
$aff_warn       = get_user_meta( $user_id, '_fbg_affiliate_warning', true );
$aff_warn       = is_string( $aff_warn ) ? trim( $aff_warn ) : '';
$approved       = get_posts(
	array(
		'post_type'      => 'fbg_post',
		'author'         => $user_id,
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);
$approved_count = count( $approved );
$pending        = get_posts(
	array(
		'post_type'      => 'fbg_post',
		'author'         => $user_id,
		'post_status'    => 'pending',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);
$pending_count = count( $pending );
$total_links   = 0;
foreach ( $approved as $pid ) {
	$total_links += (int) get_post_meta( $pid, '_fbg_backlink_count', true );
}
$total_views = fbg_author_total_views( $user_id );
$tier        = fbg_get_user_tier( $user_id );
$config      = fbg_tier_config();
$tier_label  = isset( $config[ $tier ] ) ? $config[ $tier ]['emoji'] . ' ' . $config[ $tier ]['label'] : $tier;
$next        = fbg_next_tier_info( $user_id );
$months      = fbg_author_posts_by_month( $user_id );
$max_bar     = max( 1, max( array_map( 'intval', $months ) ) );
?>
<section class="fbg-dash-panel is-active" id="tab-overview" data-panel="overview" tabindex="-1">
	<?php if ( isset( $_GET['welcome'] ) ) : ?>
		<div class="fbg-banner fbg-banner--welcome"><?php printf( esc_html__( 'Welcome, %s! 🎉 You’re in. Submit your first guest post to start earning backlinks.', 'free-backlinks-generator' ), esc_html( wp_get_current_user()->display_name ) ); ?></div>
	<?php endif; ?>
	<?php if ( isset( $_GET['submitted'] ) ) : ?>
		<div class="fbg-banner fbg-banner--success"><?php esc_html_e( 'Your post was submitted for review.', 'free-backlinks-generator' ); ?></div>
	<?php endif; ?>
	<?php if ( $aff_warn !== '' ) : ?>
		<div class="fbg-banner fbg-banner--welcome" style="border-color: rgba(229, 83, 75, 0.45); background: rgba(229, 83, 75, 0.08);">
			<strong><?php esc_html_e( 'Affiliate program notice', 'free-backlinks-generator' ); ?></strong>
			<p style="margin: 0.5em 0 0;"><?php echo esc_html( $aff_warn ); ?></p>
		</div>
	<?php endif; ?>
	<?php if ( $slot_lim < 900 ) : ?>
		<div class="fbg-banner fbg-banner--welcome">
			<?php
			printf(
				/* translators: 1: slots used, 2: slot max, 3: peer posts fully read */
				esc_html__( 'Guest-post slots: %1$d / %2$d in use. Community posts you have fully read (2+ min each): %3$d — every 2 unlock +1 slot.', 'free-backlinks-generator' ),
				(int) $slot_use,
				(int) $slot_lim,
				(int) $reads_done
			);
			?>
		</div>
	<?php endif; ?>
	<?php if ( $aff_total > 0 || $aff_balance > 0 ) : ?>
		<div class="fbg-banner fbg-banner--success">
			<?php
			printf(
				/* translators: 1: total referred hits, 2: organic hits, 3: USD balance */
				esc_html__( 'Affiliate referrals: %1$d visits logged (%2$d from organic search). Pending balance: $%3$s (see Affiliate Program page for payout terms).', 'free-backlinks-generator' ),
				(int) $aff_total,
				(int) $aff_org,
				esc_html( number_format_i18n( $aff_balance, 2 ) )
			);
			?>
		</div>
	<?php endif; ?>
	<div class="fbg-stat-cards">
		<div class="fbg-stat-card">
			<h4><?php esc_html_e( '📝 Guest Posts', 'free-backlinks-generator' ); ?></h4>
			<p class="fbg-stat-card__num"><?php echo esc_html( (string) $approved_count ); ?> <span><?php esc_html_e( 'approved', 'free-backlinks-generator' ); ?></span></p>
			<p class="fbg-stat-card__sub"><?php echo esc_html( (string) $pending_count ); ?> <?php esc_html_e( 'pending review', 'free-backlinks-generator' ); ?></p>
		</div>
		<div class="fbg-stat-card">
			<h4><?php esc_html_e( '🔗 Live Backlinks', 'free-backlinks-generator' ); ?></h4>
			<p class="fbg-stat-card__num"><?php echo esc_html( (string) $total_links ); ?> <span><?php esc_html_e( 'links live', 'free-backlinks-generator' ); ?></span></p>
		</div>
		<div class="fbg-stat-card">
			<h4><?php esc_html_e( '👁 Total Views', 'free-backlinks-generator' ); ?></h4>
			<p class="fbg-stat-card__num"><?php echo esc_html( number_format_i18n( $total_views ) ); ?></p>
		</div>
		<div class="fbg-stat-card">
			<h4><?php esc_html_e( '🏆 Community Tier', 'free-backlinks-generator' ); ?></h4>
			<p class="fbg-stat-card__num"><?php echo esc_html( $tier_label ); ?></p>
			<?php if ( $next ) : ?>
				<p class="fbg-stat-card__sub"><?php printf( esc_html__( 'Next: %1$s (%2$d more)', 'free-backlinks-generator' ), esc_html( $next['label'] ), (int) $next['remaining'] ); ?></p>
			<?php endif; ?>
		</div>
	</div>
	<h3><?php esc_html_e( 'Posts per month', 'free-backlinks-generator' ); ?></h3>
	<div class="fbg-bar-chart" role="img" aria-label="<?php esc_attr_e( 'Posts per month chart', 'free-backlinks-generator' ); ?>">
		<?php foreach ( $months as $ym => $c ) : ?>
			<?php
			$h = round( ( $c / $max_bar ) * 100 );
			$label = gmdate( 'M', strtotime( $ym . '-01' ) );
			?>
			<div class="fbg-bar-wrap">
				<div class="fbg-bar" style="height: <?php echo esc_attr( (string) max( 8, $h ) ); ?>%" title="<?php echo esc_attr( sprintf( /* translators: 1 count 2 month */ _n( '%1$d post in %2$s', '%1$d posts in %2$s', $c, 'free-backlinks-generator' ), $c, $label ) ); ?>"></div>
				<span class="fbg-bar-label"><?php echo esc_html( $label ); ?></span>
			</div>
		<?php endforeach; ?>
	</div>
	<h3><?php esc_html_e( 'Recent posts', 'free-backlinks-generator' ); ?></h3>
	<div class="fbg-table-wrap">
		<table class="fbg-table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Title', 'free-backlinks-generator' ); ?></th>
					<th><?php esc_html_e( 'Niche', 'free-backlinks-generator' ); ?></th>
					<th><?php esc_html_e( 'Status', 'free-backlinks-generator' ); ?></th>
					<th><?php esc_html_e( 'Links', 'free-backlinks-generator' ); ?></th>
					<th><?php esc_html_e( 'Views', 'free-backlinks-generator' ); ?></th>
					<th><?php esc_html_e( 'Date', 'free-backlinks-generator' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				$recent = new WP_Query(
					array(
						'post_type'      => 'fbg_post',
						'author'         => $user_id,
						'post_status'    => array( 'publish', 'pending', 'draft' ),
						'posts_per_page' => 5,
					)
				);
				if ( $recent->have_posts() ) :
					while ( $recent->have_posts() ) :
						$recent->the_post();
						$st = get_post_meta( get_the_ID(), '_fbg_content_status', true );
						if ( 'publish' === get_post_status() ) {
							$st = 'approved';
						} elseif ( 'pending' === get_post_status() ) {
							$st = 'pending';
						}
						$terms = get_the_terms( get_the_ID(), 'fbg_niche' );
						$niche = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : '—';
						?>
						<tr>
							<td><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></td>
							<td><?php echo esc_html( $niche ); ?></td>
							<td><?php echo esc_html( $st ); ?></td>
							<td><?php echo esc_html( (string) (int) get_post_meta( get_the_ID(), '_fbg_backlink_count', true ) ); ?></td>
							<td><?php echo 'publish' === get_post_status() ? esc_html( number_format_i18n( (int) get_post_meta( get_the_ID(), '_fbg_view_count', true ) ) ) : '—'; ?></td>
							<td><?php echo esc_html( get_the_date() ); ?></td>
						</tr>
						<?php
					endwhile;
					wp_reset_postdata();
				else :
					?>
					<tr><td colspan="6"><?php esc_html_e( 'No posts yet.', 'free-backlinks-generator' ); ?></td></tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
	<p class="fbg-quick-actions">
		<?php if ( $can_submit ) : ?>
			<a class="btn-primary" href="<?php echo esc_url( home_url( '/submit-post/' ) ); ?>"><?php esc_html_e( '+ Submit New Guest Post', 'free-backlinks-generator' ); ?></a>
		<?php else : ?>
			<a class="btn-primary" href="<?php echo esc_url( get_post_type_archive_link( 'fbg_post' ) ); ?>"><?php esc_html_e( 'Read posts to unlock slots', 'free-backlinks-generator' ); ?> →</a>
		<?php endif; ?>
		<a class="btn-ghost" href="#links" data-tab-trigger="links"><?php esc_html_e( '📋 View All My Links', 'free-backlinks-generator' ); ?></a>
		<a class="btn-ghost" href="#upgrade" data-tab-trigger="upgrade"><?php echo $is_pro ? esc_html__( '💎 Plans & billing', 'free-backlinks-generator' ) : esc_html__( '💎 Upgrade to Pro', 'free-backlinks-generator' ); ?></a>
		<a class="btn-ghost" href="#affiliate" data-tab-trigger="affiliate"><?php esc_html_e( '🤝 Affiliate tools', 'free-backlinks-generator' ); ?></a>
		<a class="btn-ghost" href="#earn" data-tab-trigger="earn"><?php esc_html_e( '💰 Earn & payouts', 'free-backlinks-generator' ); ?></a>
		<a class="btn-ghost" href="#profile" data-tab-trigger="profile"><?php esc_html_e( '👤 Edit Profile', 'free-backlinks-generator' ); ?></a>
	</p>
</section>
