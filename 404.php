<?php
/**
 * 404 template.
 *
 * @package Free_Backlinks_Generator
 */

get_header();
?>
<main id="main-content" class="fbg-main fbg-container fbg-section fbg-404">
	<div class="fbg-404__inner">
		<img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/404-illustration.svg' ) ); ?>" alt="" width="280" height="200" loading="lazy">
		<h1><?php esc_html_e( 'Page not found', 'free-backlinks-generator' ); ?></h1>
		<p><?php esc_html_e( 'The page you are looking for does not exist or was moved.', 'free-backlinks-generator' ); ?></p>
		<a class="btn-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to home', 'free-backlinks-generator' ); ?></a>
	</div>
</main>
<?php
get_footer();
