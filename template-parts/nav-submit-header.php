<?php
/**
 * Compact header for guest post submission (logo + back to dashboard).
 *
 * @package Free_Backlinks_Generator
 */
if ( ! is_user_logged_in() ) {
	return;
}
$logo = get_theme_mod( 'custom_logo' ) ? wp_get_attachment_image_src( get_theme_mod( 'custom_logo' ), 'full' ) : null;
$logo_url = $logo ? $logo[0] : get_theme_file_uri( 'assets/images/logo.svg' );
$dash     = home_url( '/dashboard/' );
?>
<header class="fbg-submit-header" role="banner">
	<div class="fbg-submit-header__inner fbg-container">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="fbg-submit-header__logo">
			<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php bloginfo( 'name' ); ?>" width="180" height="44" loading="eager">
		</a>
		<nav class="fbg-submit-header__actions" aria-label="<?php esc_attr_e( 'Submission', 'free-backlinks-generator' ); ?>">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="fbg-submit-header__home"><?php esc_html_e( 'Go to Homepage', 'free-backlinks-generator' ); ?></a>
			<a href="<?php echo esc_url( $dash ); ?>" class="fbg-submit-header__back">
				<span class="fbg-submit-header__back-icon" aria-hidden="true">←</span>
				<?php esc_html_e( 'Back to Dashboard', 'free-backlinks-generator' ); ?>
			</a>
			<a href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>" class="fbg-submit-header__logout"><?php esc_html_e( 'Log out', 'free-backlinks-generator' ); ?></a>
		</nav>
	</div>
</header>
