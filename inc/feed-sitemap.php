<?php
/**
 * RSS feed enhancements and XML sitemap support for public content.
 *
 * @package Free_Backlinks_Generator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Human-readable RSS in browsers (XSL transform).
 *
 * @param string $feed_type Feed type slug.
 */
function fbg_feed_rss_stylesheet( $feed_type ) {
	if ( 'rss2' !== $feed_type ) {
		return;
	}
	$xsl = get_theme_file_uri( 'assets/rss/rss.xsl' );
	echo '<?xml-stylesheet type="text/xsl" href="' . esc_url( $xsl ) . '"?>' . "\n";
}
add_action( 'rss_tag_pre', 'fbg_feed_rss_stylesheet', 1 );

/**
 * MRSS namespace for thumbnails in RSS 2.0.
 */
function fbg_rss2_media_namespace() {
	echo 'xmlns:media="http://search.yahoo.com/mrss/"' . "\n";
}
add_action( 'rss2_ns', 'fbg_rss2_media_namespace' );

/**
 * Optional featured image per feed item (posts + guest posts).
 */
function fbg_rss2_item_media_thumbnail() {
	global $post;
	if ( ! $post instanceof WP_Post ) {
		return;
	}
	if ( ! in_array( $post->post_type, array( 'post', 'fbg_post' ), true ) ) {
		return;
	}
	if ( ! has_post_thumbnail( $post ) ) {
		return;
	}
	$url = get_the_post_thumbnail_url( $post, 'medium_large' );
	if ( ! $url ) {
		return;
	}
	printf( '<media:content url="%s" medium="image" />' . "\n", esc_url( $url ) );
}
add_action( 'rss2_item', 'fbg_rss2_item_media_thumbnail', 3 );

/**
 * Include published guest posts in the main site feed (updates as soon as posts are published).
 *
 * @param WP_Query $query Query.
 */
function fbg_feed_query_post_types( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_feed() ) {
		return;
	}
	if ( $query->is_tax() || $query->is_category() || $query->is_tag() || $query->is_author() || $query->is_post_type_archive() || $query->is_search() ) {
		return;
	}
	$pt = $query->get( 'post_type' );
	$merge = false;
	if ( empty( $pt ) ) {
		$merge = true;
	} elseif ( 'post' === $pt ) {
		$merge = true;
	} elseif ( is_array( $pt ) && 1 === count( $pt ) && in_array( 'post', $pt, true ) ) {
		$merge = true;
	}
	if ( $merge ) {
		$query->set( 'post_type', array( 'post', 'fbg_post' ) );
		$query->set( 'post_status', 'publish' );
	}
}
add_action( 'pre_get_posts', 'fbg_feed_query_post_types' );

/**
 * Richer feed title prefix for guest posts.
 *
 * @param string $title Post title.
 * @return string
 */
function fbg_feed_item_title( $title ) {
	if ( ! is_feed() ) {
		return $title;
	}
	$post = get_post();
	if ( ! $post || 'fbg_post' !== $post->post_type ) {
		return $title;
	}
	return sprintf( '[%s] %s', __( 'Guest post', 'free-backlinks-generator' ), $title );
}
add_filter( 'the_title_rss', 'fbg_feed_item_title' );

/**
 * Ensure guest posts appear in WordPress XML sitemaps (core updates with publish).
 *
 * @param string[] $post_types Post type names.
 * @return string[]
 */
function fbg_sitemaps_include_guest_posts( $post_types ) {
	if ( post_type_exists( 'fbg_post' ) && ! in_array( 'fbg_post', $post_types, true ) ) {
		$post_types[] = 'fbg_post';
	}
	return $post_types;
}
add_filter( 'wp_sitemaps_post_types', 'fbg_sitemaps_include_guest_posts' );

/**
 * Slightly higher priority for fresh community content in sitemap (optional SEO signal).
 *
 * @param array<string, mixed> $entry     Entry data.
 * @param string               $post_type Post type.
 * @param WP_Post              $post      Post object.
 * @return array<string, mixed>
 */
function fbg_sitemaps_fbg_post_priority( $entry, $post_type, $post ) {
	if ( 'fbg_post' === $post_type && is_array( $entry ) ) {
		$entry['priority'] = 0.75;
	}
	return $entry;
}
add_filter( 'wp_sitemaps_posts_entry', 'fbg_sitemaps_fbg_post_priority', 10, 3 );
