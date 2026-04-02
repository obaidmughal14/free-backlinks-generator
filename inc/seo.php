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
 * Whether a major SEO plugin is handling meta/OG.
 *
 * @return bool
 */
function fbg_seo_plugin_handles_meta() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' );
}

/**
 * Meta description string for current view (no HTML).
 *
 * @return string
 */
function fbg_meta_description_text() {
	if ( is_front_page() ) {
		$custom = get_theme_mod( 'fbg_home_meta_description', '' );
		if ( is_string( $custom ) && '' !== trim( $custom ) ) {
			return wp_strip_all_tags( $custom );
		}
		return wp_strip_all_tags( get_bloginfo( 'description', 'display' ) );
	}
	if ( is_singular() && has_excerpt() ) {
		return wp_strip_all_tags( get_the_excerpt() );
	}
	return wp_strip_all_tags( get_bloginfo( 'description', 'display' ) );
}

/**
 * Output meta description when no SEO plugin is active.
 */
function fbg_output_meta_description() {
	if ( is_admin() || fbg_seo_plugin_handles_meta() ) {
		return;
	}
	if ( ! is_front_page() && ! is_singular() ) {
		return;
	}
	$desc = fbg_meta_description_text();
	if ( '' === $desc ) {
		return;
	}
	printf( '<meta name="description" content="%s" />' . "\n", esc_attr( wp_html_excerpt( $desc, 320, '' ) ) );
}
add_action( 'wp_head', 'fbg_output_meta_description', 2 );

/**
 * Default OG tags when Yoast/RankMath not present.
 */
function fbg_default_og_tags() {
	if ( is_admin() || fbg_seo_plugin_handles_meta() ) {
		return;
	}
	if ( ! is_front_page() && ! is_singular() ) {
		return;
	}
	$title = wp_get_document_title();
	$desc  = fbg_meta_description_text();
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
 * Collect validated social URLs for sameAs.
 *
 * @return string[]
 */
function fbg_schema_same_as_urls() {
	if ( ! function_exists( 'fbg_social_url_or_empty' ) ) {
		return array();
	}
	$mods = array(
		'fbg_social_facebook',
		'fbg_social_x',
		'fbg_social_instagram',
		'fbg_social_tiktok',
		'fbg_social_linkedin',
		'fbg_social_youtube',
	);
	$out = array();
	foreach ( $mods as $mod ) {
		$u = fbg_social_url_or_empty( get_theme_mod( $mod, '' ) );
		if ( '' !== $u ) {
			$out[] = $u;
		}
	}
	return array_values( array_unique( $out ) );
}

/**
 * JSON-LD WebSite + Organization on front page.
 */
function fbg_schema_website() {
	if ( ! is_front_page() || is_admin() ) {
		return;
	}
	$home    = home_url( '/' );
	$org_id  = $home . '#organization';
	$site_id = $home . '#website';
	$name    = get_bloginfo( 'name' );

	$organization = array(
		'@type' => 'Organization',
		'@id'   => $org_id,
		'name'  => $name,
		'url'   => $home,
	);

	$logo_id = get_theme_mod( 'custom_logo' );
	if ( $logo_id ) {
		$logo_url = wp_get_attachment_image_url( (int) $logo_id, 'full' );
		if ( $logo_url ) {
			$organization['logo'] = array(
				'@type' => 'ImageObject',
				'url'   => $logo_url,
			);
		}
	}

	$same_as = fbg_schema_same_as_urls();
	if ( ! empty( $same_as ) ) {
		$organization['sameAs'] = $same_as;
	}

	$desc = fbg_meta_description_text();
	$website = array(
		'@type'       => 'WebSite',
		'@id'         => $site_id,
		'name'        => $name,
		'url'         => $home,
		'publisher'   => array( '@id' => $org_id ),
		'inLanguage'  => get_bloginfo( 'language' ),
	);
	if ( '' !== $desc ) {
		$website['description'] = wp_html_excerpt( $desc, 320, '' );
	}

	$data = array(
		'@context' => 'https://schema.org',
		'@graph'   => array(
			$organization,
			$website,
		),
	);
	echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
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
