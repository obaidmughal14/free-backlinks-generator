<?php
/**
 * External links: open in new tab; nav + post content.
 *
 * @package Free_Backlinks_Generator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether URL is http(s) and host differs from this site.
 *
 * @param string $url URL.
 * @return bool
 */
function fbg_is_external_href( $url ) {
	$url = trim( (string) $url );
	if ( '' === $url || '#' === $url[0] ) {
		return false;
	}
	if ( preg_match( '#^(mailto:|tel:|javascript:)#i', $url ) ) {
		return false;
	}
	$host = wp_parse_url( $url, PHP_URL_HOST );
	if ( ! is_string( $host ) || '' === $host ) {
		return false;
	}
	$site = wp_parse_url( home_url( '/' ), PHP_URL_HOST );
	if ( ! is_string( $site ) || '' === $site ) {
		return true;
	}
	$h  = strtolower( preg_replace( '/^www\./', '', $host ) );
	$s  = strtolower( preg_replace( '/^www\./', '', $site ) );
	return $h !== $s;
}

/**
 * Add target="_blank" and rel to anchor tags in HTML fragment.
 *
 * @param string $html HTML.
 * @return string
 */
function fbg_html_external_links_target_blank( $html ) {
	if ( '' === $html || false === strpos( $html, '<a ' ) ) {
		return $html;
	}
	if ( class_exists( 'WP_HTML_Tag_Processor' ) ) {
		$p = new WP_HTML_Tag_Processor( $html );
		while ( $p->next_tag( 'a' ) ) {
			$href = $p->get_attribute( 'href' );
			if ( ! $href || ! fbg_is_external_href( $href ) ) {
				continue;
			}
			if ( $p->get_attribute( 'target' ) ) {
				continue;
			}
			$p->set_attribute( 'target', '_blank' );
			$rel = (string) $p->get_attribute( 'rel' );
			$p->set_attribute( 'rel', trim( $rel . ' noopener noreferrer' ) );
		}
		return $p->get_updated_html();
	}
	return fbg_html_external_links_target_blank_dom( $html );
}

/**
 * DOM fallback for older WordPress.
 *
 * @param string $html HTML.
 * @return string
 */
function fbg_html_external_links_target_blank_dom( $html ) {
	if ( ! class_exists( 'DOMDocument' ) ) {
		return $html;
	}
	libxml_use_internal_errors( true );
	$doc = new DOMDocument();
	$wrapped = '<?xml encoding="UTF-8"><div id="fbg-link-root">' . $html . '</div>';
	$ok      = @$doc->loadHTML( $wrapped, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );
	if ( ! $ok ) {
		libxml_clear_errors();
		return $html;
	}
	$xpath = new DOMXPath( $doc );
	$home  = wp_parse_url( home_url( '/' ), PHP_URL_HOST );
	foreach ( $xpath->query( '//a[@href]' ) as $a ) {
		if ( ! $a instanceof DOMElement ) {
			continue;
		}
		$href = $a->getAttribute( 'href' );
		if ( ! fbg_is_external_href( $href ) ) {
			continue;
		}
		if ( $a->getAttribute( 'target' ) ) {
			continue;
		}
		$a->setAttribute( 'target', '_blank' );
		$rel = $a->getAttribute( 'rel' );
		$a->setAttribute( 'rel', trim( $rel . ' noopener noreferrer' ) );
	}
	$root = $doc->getElementById( 'fbg-link-root' );
	if ( ! $root ) {
		libxml_clear_errors();
		return $html;
	}
	$out = '';
	foreach ( $root->childNodes as $child ) {
		$out .= $doc->saveHTML( $child );
	}
	libxml_clear_errors();
	return $out;
}

/**
 * @param string $content Post content.
 * @return string
 */
function fbg_the_content_external_links( $content ) {
	if ( ! fbg_external_links_new_tab_enabled() ) {
		return $content;
	}
	if ( ! is_string( $content ) || '' === $content ) {
		return $content;
	}
	return fbg_html_external_links_target_blank( $content );
}
add_filter( 'the_content', 'fbg_the_content_external_links', 15 );

/**
 * Classic text widget.
 *
 * @param string $t Text.
 * @return string
 */
function fbg_widget_text_external_links( $t ) {
	if ( ! fbg_external_links_new_tab_enabled() ) {
		return $t;
	}
	return fbg_html_external_links_target_blank( $t );
}
add_filter( 'widget_text', 'fbg_widget_text_external_links', 15 );

/**
 * Block-based widget HTML.
 *
 * @param string $b Block content.
 * @return string
 */
function fbg_widget_block_external_links( $b ) {
	if ( ! fbg_external_links_new_tab_enabled() ) {
		return $b;
	}
	return fbg_html_external_links_target_blank( $b );
}
add_filter( 'widget_block_content', 'fbg_widget_block_external_links', 15 );

/**
 * Nav menu link attributes.
 *
 * @param array<string,string> $atts    Attributes.
 * @param WP_Post              $item    Menu item.
 * @param stdClass             $args    Menu args.
 * @return array<string,string>
 */
function fbg_nav_menu_link_target_blank( $atts, $item, $args ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed -- $item, $args required by filter signature.
	if ( ! fbg_external_links_new_tab_enabled() ) {
		return $atts;
	}
	if ( empty( $atts['href'] ) ) {
		return $atts;
	}
	if ( ! fbg_is_external_href( $atts['href'] ) ) {
		return $atts;
	}
	if ( ! empty( $atts['target'] ) ) {
		return $atts;
	}
	$atts['target'] = '_blank';
	$rel            = isset( $atts['rel'] ) ? $atts['rel'] : '';
	$atts['rel']    = trim( $rel . ' noopener noreferrer' );
	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'fbg_nav_menu_link_target_blank', 10, 3 );
