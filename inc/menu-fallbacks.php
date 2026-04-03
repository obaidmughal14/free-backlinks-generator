<?php
/**
 * Default nav output when no menu is assigned to a location.
 *
 * @package Free_Backlinks_Generator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Normalize wp_nav_menu fallback args.
 *
 * @param array<string, mixed>|object $args Menu args.
 * @return object
 */
function fbg_nav_menu_args_object( $args ) {
	if ( is_array( $args ) ) {
		return (object) $args;
	}
	if ( is_object( $args ) ) {
		return $args;
	}
	return (object) array();
}

/**
 * Primary header / drawer menu fallback.
 *
 * @param array<string, mixed>|object $args Menu args.
 */
function fbg_primary_nav_fallback( $args ) {
	$a          = fbg_nav_menu_args_object( $args );
	$menu_class = isset( $a->menu_class ) && is_string( $a->menu_class ) ? $a->menu_class : 'nav-links';
	$comm       = get_post_type_archive_link( 'fbg_post' );
	$guidelines = home_url( '/community-guidelines/' );
	$items      = array(
		array(
			'url'   => home_url( '/#how-it-works' ),
			'label' => __( 'How It Works', 'free-backlinks-generator' ),
		),
		array(
			'url'   => home_url( '/#features' ),
			'label' => __( 'Features', 'free-backlinks-generator' ),
		),
		array(
			'url'   => $comm ? $comm : home_url( '/' ),
			'label' => __( 'Browse Posts', 'free-backlinks-generator' ),
		),
		array(
			'url'   => $guidelines,
			'label' => __( 'Guidelines', 'free-backlinks-generator' ),
		),
	);
	echo '<ul class="' . esc_attr( $menu_class ) . '">';
	foreach ( $items as $item ) {
		printf(
			'<li class="menu-item"><a href="%s">%s</a></li>',
			esc_url( $item['url'] ),
			esc_html( $item['label'] )
		);
	}
	echo '</ul>';
}

/**
 * Inner pages primary menu (How it works, Browse, Guidelines — no Features anchor).
 *
 * @param array<string, mixed>|object $args Menu args.
 */
function fbg_primary_nav_fallback_inner( $args ) {
	$a          = fbg_nav_menu_args_object( $args );
	$menu_class = isset( $a->menu_class ) && is_string( $a->menu_class ) ? $a->menu_class : 'nav-links';
	$comm       = get_post_type_archive_link( 'fbg_post' );
	$guidelines = home_url( '/community-guidelines/' );
	$items      = array(
		array(
			'url'   => home_url( '/#how-it-works' ),
			'label' => __( 'How It Works', 'free-backlinks-generator' ),
		),
		array(
			'url'   => $comm ? $comm : home_url( '/' ),
			'label' => __( 'Browse Posts', 'free-backlinks-generator' ),
		),
		array(
			'url'   => $guidelines,
			'label' => __( 'Guidelines', 'free-backlinks-generator' ),
		),
	);
	echo '<ul class="' . esc_attr( $menu_class ) . '">';
	foreach ( $items as $item ) {
		printf(
			'<li class="menu-item"><a href="%s">%s</a></li>',
			esc_url( $item['url'] ),
			esc_html( $item['label'] )
		);
	}
	echo '</ul>';
}

/**
 * Footer column menu fallback.
 *
 * @param array<string, mixed>|object $args Menu args.
 */
function fbg_footer_nav_fallback( $args ) {
	$a          = fbg_nav_menu_args_object( $args );
	$loc        = isset( $a->theme_location ) ? $a->theme_location : '';
	$menu_class = isset( $a->menu_class ) && is_string( $a->menu_class ) ? $a->menu_class : 'fbg-footer__menu';
	$comm       = get_post_type_archive_link( 'fbg_post' );

	$groups = array(
		'footer_1' => array(
			array( 'url' => home_url( '/#how-it-works' ), 'label' => __( 'How It Works', 'free-backlinks-generator' ) ),
			array( 'url' => $comm ? $comm : home_url( '/' ), 'label' => __( 'Browse Guest Posts', 'free-backlinks-generator' ) ),
			array( 'url' => home_url( '/submit-post/' ), 'label' => __( 'Submit a Post', 'free-backlinks-generator' ) ),
			array( 'url' => home_url( '/community-guidelines/' ), 'label' => __( 'Community Guidelines', 'free-backlinks-generator' ) ),
		),
		'footer_2' => array(
			array( 'url' => home_url( '/about/' ), 'label' => __( 'About Us', 'free-backlinks-generator' ) ),
			array( 'url' => home_url( '/contact/' ), 'label' => __( 'Contact', 'free-backlinks-generator' ) ),
			array( 'url' => home_url( '/affiliate-program/' ), 'label' => __( 'Affiliate Program', 'free-backlinks-generator' ) ),
			array( 'url' => home_url( '/blog/' ), 'label' => __( 'Blog', 'free-backlinks-generator' ) ),
		),
		'footer_3' => array(
			array( 'url' => home_url( '/privacy-policy/' ), 'label' => __( 'Privacy Policy', 'free-backlinks-generator' ) ),
			array( 'url' => home_url( '/terms-of-service/' ), 'label' => __( 'Terms of Service', 'free-backlinks-generator' ) ),
			array( 'url' => home_url( '/cookie-policy/' ), 'label' => __( 'Cookie Policy', 'free-backlinks-generator' ) ),
			array( 'url' => home_url( '/gdpr-notice/' ), 'label' => __( 'GDPR Notice', 'free-backlinks-generator' ) ),
			array( 'url' => home_url( '/wp-sitemap.xml' ), 'label' => __( 'Sitemap', 'free-backlinks-generator' ) ),
		),
	);

	$items = isset( $groups[ $loc ] ) ? $groups[ $loc ] : array();
	echo '<ul class="' . esc_attr( $menu_class ) . '">';
	foreach ( $items as $item ) {
		printf(
			'<li class="menu-item"><a href="%s">%s</a></li>',
			esc_url( $item['url'] ),
			esc_html( $item['label'] )
		);
	}
	echo '</ul>';
}
