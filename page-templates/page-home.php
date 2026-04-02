<?php
/**
 * Template Name: Home (Landing)
 *
 * @package Free_Backlinks_Generator
 */

get_header();
$logo     = get_theme_mod( 'custom_logo' ) ? wp_get_attachment_image_src( get_theme_mod( 'custom_logo' ), 'full' ) : null;
$logo_url = $logo ? $logo[0] : get_theme_file_uri( 'assets/images/logo.svg' );
$dash_url = home_url( '/dashboard/' );
$comm_url = get_post_type_archive_link( 'fbg_post' );
?>
<nav class="fbg-nav fbg-nav--home" id="main-nav" aria-label="<?php esc_attr_e( 'Primary', 'free-backlinks-generator' ); ?>">
	<div class="nav-container fbg-container">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="nav-logo">
			<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php bloginfo( 'name' ); ?>" width="180" height="44">
		</a>
		<ul class="nav-links">
			<li><a href="#how-it-works"><?php esc_html_e( 'How It Works', 'free-backlinks-generator' ); ?></a></li>
			<li><a href="#features"><?php esc_html_e( 'Features', 'free-backlinks-generator' ); ?></a></li>
			<li><a href="<?php echo esc_url( $comm_url ); ?>"><?php esc_html_e( 'Browse Posts', 'free-backlinks-generator' ); ?></a></li>
			<li><a href="<?php echo esc_url( home_url( '/community-guidelines/' ) ); ?>"><?php esc_html_e( 'Guidelines', 'free-backlinks-generator' ); ?></a></li>
			<?php if ( is_user_logged_in() ) : ?>
				<li><a href="<?php echo esc_url( $dash_url ); ?>"><?php esc_html_e( 'Dashboard', 'free-backlinks-generator' ); ?></a></li>
			<?php endif; ?>
		</ul>
		<div class="nav-actions">
			<?php if ( is_user_logged_in() ) : ?>
				<a href="<?php echo esc_url( $dash_url ); ?>" class="btn-primary"><?php esc_html_e( 'Dashboard', 'free-backlinks-generator' ); ?> →</a>
				<a href="<?php echo esc_url( $comm_url ); ?>" class="btn-ghost btn-ghost--on-dark"><?php esc_html_e( 'Browse posts', 'free-backlinks-generator' ); ?></a>
			<?php else : ?>
				<a href="<?php echo esc_url( home_url( '/login/' ) ); ?>" class="btn-ghost"><?php esc_html_e( 'Log In', 'free-backlinks-generator' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/register/' ) ); ?>" class="btn-primary"><?php esc_html_e( 'Get Free Backlinks', 'free-backlinks-generator' ); ?> →</a>
			<?php endif; ?>
		</div>
		<button type="button" class="nav-hamburger" aria-expanded="false" aria-controls="fbg-nav-drawer-home" aria-label="<?php esc_attr_e( 'Open menu', 'free-backlinks-generator' ); ?>">☰</button>
	</div>
	<div class="fbg-nav-drawer" id="fbg-nav-drawer-home" hidden>
		<ul class="fbg-nav-drawer__links">
			<li><a href="#how-it-works"><?php esc_html_e( 'How It Works', 'free-backlinks-generator' ); ?></a></li>
			<li><a href="#features"><?php esc_html_e( 'Features', 'free-backlinks-generator' ); ?></a></li>
			<li><a href="<?php echo esc_url( $comm_url ); ?>"><?php esc_html_e( 'Browse Posts', 'free-backlinks-generator' ); ?></a></li>
			<li><a href="<?php echo esc_url( home_url( '/community-guidelines/' ) ); ?>"><?php esc_html_e( 'Guidelines', 'free-backlinks-generator' ); ?></a></li>
			<?php if ( is_user_logged_in() ) : ?>
				<li><a href="<?php echo esc_url( $dash_url ); ?>"><?php esc_html_e( 'Dashboard', 'free-backlinks-generator' ); ?></a></li>
			<?php endif; ?>
		</ul>
		<div class="fbg-nav-drawer__actions">
			<?php if ( is_user_logged_in() ) : ?>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn-ghost btn-ghost--on-dark"><?php esc_html_e( 'Go to Homepage', 'free-backlinks-generator' ); ?></a>
				<a href="<?php echo esc_url( $dash_url ); ?>" class="btn-primary"><?php esc_html_e( 'Dashboard', 'free-backlinks-generator' ); ?> →</a>
			<?php else : ?>
				<a href="<?php echo esc_url( home_url( '/login/' ) ); ?>" class="btn-ghost"><?php esc_html_e( 'Log In', 'free-backlinks-generator' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/register/' ) ); ?>" class="btn-primary"><?php esc_html_e( 'Get Free Backlinks', 'free-backlinks-generator' ); ?> →</a>
			<?php endif; ?>
		</div>
	</div>
</nav>
<main id="main-content">
	<?php get_template_part( 'template-parts/hero' ); ?>
	<?php get_template_part( 'template-parts/stats', 'bar' ); ?>
	<?php get_template_part( 'template-parts/how-it', 'works' ); ?>
	<?php get_template_part( 'template-parts/features', 'grid' ); ?>
	<?php get_template_part( 'template-parts/testimonials' ); ?>
	<?php get_template_part( 'template-parts/cta', 'section' ); ?>
</main>
<?php
get_footer();
