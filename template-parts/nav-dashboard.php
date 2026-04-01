<?php
/**
 * Dashboard sidebar navigation.
 *
 * @package Free_Backlinks_Generator
 */
if ( ! is_user_logged_in() ) {
	return;
}
$user    = wp_get_current_user();
$uid     = $user->ID;
$tier    = fbg_get_user_tier( $uid );
$config  = fbg_tier_config();
$tier_l  = isset( $config[ $tier ] ) ? $config[ $tier ]['label'] : $tier;
$tier_e  = isset( $config[ $tier ] ) ? $config[ $tier ]['emoji'] : '';
$site    = get_user_meta( $uid, '_fbg_website_url', true );
$unread  = fbg_unread_notification_count( $uid );
$initial = strtoupper( substr( $user->display_name, 0, 1 ) );
?>
<aside class="fbg-dash-sidebar" id="fbg-dash-sidebar">
	<div class="fbg-dash-user">
		<span class="fbg-dash-avatar" aria-hidden="true"><?php echo esc_html( $initial ); ?></span>
		<strong><?php echo esc_html( $user->display_name ); ?></strong>
		<span class="fbg-tier-pill"><?php echo esc_html( $tier_e . ' ' . $tier_l ); ?></span>
		<?php if ( $site ) : ?>
			<a class="fbg-dash-site mono" href="<?php echo esc_url( $site ); ?>" target="_blank" rel="noopener"><?php echo esc_html( wp_parse_url( $site, PHP_URL_HOST ) ?: $site ); ?></a>
		<?php endif; ?>
	</div>
	<nav class="fbg-dash-nav" aria-label="<?php esc_attr_e( 'Dashboard', 'free-backlinks-generator' ); ?>">
		<a href="#overview" class="is-active" data-tab="overview"><?php esc_html_e( '📊 Overview', 'free-backlinks-generator' ); ?></a>
		<a href="#posts" data-tab="posts"><?php esc_html_e( '📝 My Posts', 'free-backlinks-generator' ); ?></a>
		<a href="#links" data-tab="links"><?php esc_html_e( '🔗 My Links', 'free-backlinks-generator' ); ?></a>
		<a href="#profile" data-tab="profile"><?php esc_html_e( '👤 Profile', 'free-backlinks-generator' ); ?></a>
		<a href="#notifications" data-tab="notifications"><?php esc_html_e( '🔔 Notifications', 'free-backlinks-generator' ); ?> <?php if ( $unread > 0 ) : ?><span class="fbg-badge-count"><?php echo esc_html( (string) $unread ); ?></span><?php endif; ?></a>
		<a href="#settings" data-tab="settings"><?php esc_html_e( '⚙️ Settings', 'free-backlinks-generator' ); ?></a>
	</nav>
	<div class="fbg-dash-upgrade">
		<button type="button" class="btn-upgrade"><?php esc_html_e( '🚀 Upgrade to Pro', 'free-backlinks-generator' ); ?></button>
		<small><?php esc_html_e( 'Unlimited posts & links', 'free-backlinks-generator' ); ?></small>
	</div>
	<a class="fbg-dash-logout" href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>"><?php esc_html_e( 'Log Out', 'free-backlinks-generator' ); ?></a>
</aside>
