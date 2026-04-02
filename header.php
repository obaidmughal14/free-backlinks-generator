<?php
/**
 * Theme header.
 *
 * @package Free_Backlinks_Generator
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#main-content"><?php esc_html_e( 'Skip to content', 'free-backlinks-generator' ); ?></a>
<?php
if ( is_page_template( 'page-templates/page-submit-post.php' ) ) {
	get_template_part( 'template-parts/nav', 'submit-header' );
} elseif ( ! is_page_template( 'page-templates/page-home.php' ) && ! is_page_template( 'page-templates/page-signup.php' ) && ! is_page_template( 'page-templates/page-login.php' ) && ! is_page_template( 'page-templates/page-forgot-password.php' ) && ! is_page_template( 'page-templates/page-dashboard.php' ) ) {
	get_template_part( 'template-parts/nav', 'main' );
}
?>
