<?php
/**
 * Child theme bootstrap (optional).
 *
 * @package Free_Backlinks_Generator_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Example: enqueue child stylesheet after parent (uncomment if you add rules to style.css).
 */
/*
add_action(
	'wp_enqueue_scripts',
	static function () {
		wp_enqueue_style(
			'fbg-child',
			get_stylesheet_directory_uri() . '/style.css',
			array( 'fbg-main' ),
			wp_get_theme()->get( 'Version' )
		);
	},
	20
);
*/
