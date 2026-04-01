<?php
/**
 * Primary site navigation.
 *
 * @package Free_Backlinks_Generator
 */
$logo = get_theme_mod( 'custom_logo' ) ? wp_get_attachment_image_src( get_theme_mod( 'custom_logo' ), 'full' ) : null;
$logo_url = $logo ? $logo[0] : get_theme_file_uri( 'assets/images/logo.svg' );
?>
<nav class="fbg-nav fbg-nav--inner" id="main-nav" aria-label="<?php esc_attr_e( 'Primary', 'free-backlinks-generator' ); ?>">
	<div class="nav-container fbg-container">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="nav-logo">
			<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php bloginfo( 'name' ); ?>" width="180" height="44">
		</a>
		<ul class="nav-links">
			<li><a href="<?php echo esc_url( home_url( '/#how-it-works' ) ); ?>"><?php esc_html_e( 'How It Works', 'free-backlinks-generator' ); ?></a></li>
			<li><a href="<?php echo esc_url( get_post_type_archive_link( 'fbg_post' ) ); ?>"><?php esc_html_e( 'Browse Posts', 'free-backlinks-generator' ); ?></a></li>
			<li><a href="<?php echo esc_url( home_url( '/community-guidelines/' ) ); ?>"><?php esc_html_e( 'Guidelines', 'free-backlinks-generator' ); ?></a></li>
		</ul>
		<div class="nav-actions">
			<a href="<?php echo esc_url( home_url( '/login/' ) ); ?>" class="btn-ghost"><?php esc_html_e( 'Log In', 'free-backlinks-generator' ); ?></a>
			<a href="<?php echo esc_url( home_url( '/register/' ) ); ?>" class="btn-primary"><?php esc_html_e( 'Get Free Backlinks', 'free-backlinks-generator' ); ?> →</a>
		</div>
		<button type="button" class="nav-hamburger" aria-expanded="false" aria-controls="fbg-nav-drawer" aria-label="<?php esc_attr_e( 'Open menu', 'free-backlinks-generator' ); ?>">☰</button>
	</div>
	<div class="fbg-nav-drawer" id="fbg-nav-drawer" hidden>
		<ul class="fbg-nav-drawer__links">
			<li><a href="<?php echo esc_url( home_url( '/#how-it-works' ) ); ?>"><?php esc_html_e( 'How It Works', 'free-backlinks-generator' ); ?></a></li>
			<li><a href="<?php echo esc_url( get_post_type_archive_link( 'fbg_post' ) ); ?>"><?php esc_html_e( 'Browse Posts', 'free-backlinks-generator' ); ?></a></li>
			<li><a href="<?php echo esc_url( home_url( '/community-guidelines/' ) ); ?>"><?php esc_html_e( 'Guidelines', 'free-backlinks-generator' ); ?></a></li>
		</ul>
		<div class="fbg-nav-drawer__actions">
			<a href="<?php echo esc_url( home_url( '/login/' ) ); ?>" class="btn-ghost"><?php esc_html_e( 'Log In', 'free-backlinks-generator' ); ?></a>
			<a href="<?php echo esc_url( home_url( '/register/' ) ); ?>" class="btn-primary"><?php esc_html_e( 'Get Free Backlinks', 'free-backlinks-generator' ); ?> →</a>
		</div>
	</div>
</nav>
