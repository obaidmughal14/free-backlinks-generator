<?php
/**
 * SEO: robots, noindex auth pages, schema hooks.
 *
 * @package Free_Backlinks_Generator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * noindex for auth and dashboard templates.
 */
function fbg_noindex_private_templates() {
	if (
		is_page_template(
			array(
				'page-templates/page-login.php',
				'page-templates/page-signup.php',
				'page-templates/page-forgot-password.php',
				'page-templates/page-dashboard.php',
				'page-templates/page-submit-post.php',
			)
		)
	) {
		echo '<meta name="robots" content="noindex, nofollow" />' . "\n";
	}
}
add_action( 'wp_head', 'fbg_noindex_private_templates', 1 );

/**
 * Default OG tags when Yoast/RankMath not present.
 */
function fbg_default_og_tags() {
	if ( is_admin() || defined( 'WPSEO_VERSION' ) ) {
		return;
	}
	if ( ! is_front_page() && ! is_singular() ) {
		return;
	}
	$title = wp_get_document_title();
	$desc  = get_bloginfo( 'description', 'display' );
	if ( is_singular() && has_excerpt() ) {
		$desc = wp_strip_all_tags( get_the_excerpt() );
	}
	$img = get_theme_file_uri( 'assets/images/og-default.png' );
	if ( is_singular() && has_post_thumbnail() ) {
		$img = get_the_post_thumbnail_url( null, 'full' ) ?: $img;
	}
	printf( '<meta property="og:title" content="%s" />' . "\n", esc_attr( $title ) );
	printf( '<meta property="og:description" content="%s" />' . "\n", esc_attr( $desc ) );
	printf( '<meta property="og:url" content="%s" />' . "\n", esc_url( is_singular() ? get_permalink() : home_url( '/' ) ) );
	printf( '<meta property="og:image" content="%s" />' . "\n", esc_url( $img ) );
	printf( '<meta name="twitter:card" content="summary_large_image" />' . "\n" );
}
add_action( 'wp_head', 'fbg_default_og_tags', 5 );

/**
 * JSON-LD WebSite + Organization on front page.
 */
function fbg_schema_website() {
	if ( ! is_front_page() || is_admin() ) {
		return;
	}
	$data = array(
		'@context' => 'https://schema.org',
		'@graph'   => array(
			array(
				'@type' => 'WebSite',
				'name'  => get_bloginfo( 'name' ),
				'url'   => home_url( '/' ),
			),
		),
	);
	echo '<script type="application/ld+json">' . wp_json_encode( $data ) . '</script>' . "\n";
}
add_action( 'wp_head', 'fbg_schema_website', 99 );

/**
 * Article schema for single fbg_post.
 */
function fbg_schema_article() {
	if ( ! is_singular( 'fbg_post' ) ) {
		return;
	}
	$post = get_post();
	$data = array(
		'@context'    => 'https://schema.org',
		'@type'       => 'Article',
		'headline'    => get_the_title( $post ),
		'datePublished' => get_the_date( 'c', $post ),
		'author'      => array(
			'@type' => 'Person',
			'name'  => get_the_author_meta( 'display_name', $post->post_author ),
		),
	);
	if ( has_post_thumbnail( $post ) ) {
		$data['image'] = get_the_post_thumbnail_url( $post, 'full' );
	}
	echo '<script type="application/ld+json">' . wp_json_encode( $data ) . '</script>' . "\n";
}
add_action( 'wp_head', 'fbg_schema_article', 99 );

/**
 * robots.txt additions (only when virtual robots).
 *
 * @param string $output Output.
 * @param bool   $public Public blog flag.
 * @return string
 */
function fbg_robots_txt( $output, $public ) {
	if ( '0' === (string) $public ) {
		return $output;
	}
	$lines   = explode( "\n", $output );
	$extra   = array(
		'Disallow: /dashboard/',
		'Disallow: /login/',
		'Disallow: /register/',
		'Disallow: /forgot-password/',
		'Disallow: /submit-post/',
	);
	return $output . "\n" . implode( "\n", $extra ) . "\n";
}
add_filter( 'robots_txt', 'fbg_robots_txt', 10, 2 );
