<?php
/**
 * Template Name: Dashboard
 *
 * @package Free_Backlinks_Generator
 */

if ( ! is_user_logged_in() ) {
	wp_safe_redirect( home_url( '/login/' ) );
	exit;
}

get_header();
?>
<button type="button" class="fbg-dash-toggle" aria-expanded="false" aria-controls="fbg-dash-sidebar"><?php esc_html_e( 'Menu', 'free-backlinks-generator' ); ?></button>
<div class="fbg-dashboard" id="fbg-dashboard">
	<?php get_template_part( 'template-parts/nav', 'dashboard' ); ?>
	<div class="fbg-dash-main">
		<?php
		get_template_part( 'template-parts/dashboard', 'overview' );
		get_template_part( 'template-parts/dashboard', 'posts' );
		get_template_part( 'template-parts/dashboard', 'links' );
		get_template_part( 'template-parts/dashboard', 'profile' );
		get_template_part( 'template-parts/dashboard', 'notifications' );
		get_template_part( 'template-parts/dashboard', 'settings' );
		?>
	</div>
</div>
<?php
get_footer();
