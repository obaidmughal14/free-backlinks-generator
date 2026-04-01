<?php
/**
 * Hardening: version, xmlrpc, REST user enumeration.
 *
 * @package Free_Backlinks_Generator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Remove WordPress version from head and feeds.
 */
function fbg_remove_version() {
	return '';
}
add_filter( 'the_generator', 'fbg_remove_version' );

/**
 * Strip version query args from scripts (cosmetic; full removal is complex).
 */
function fbg_hide_wp_version_strings( $src ) {
	if ( strpos( $src, 'ver=' ) !== false ) {
		$src = remove_query_arg( 'ver', $src );
	}
	return $src;
}
// Uncomment if desired site-wide; can break caching. Kept off by default.
// add_filter( 'script_loader_src', 'fbg_hide_wp_version_strings', 15 );
// add_filter( 'style_loader_src', 'fbg_hide_wp_version_strings', 15 );

/**
 * Block XML-RPC for unauthenticated GET (probe).
 */
function fbg_block_xmlrpc_get() {
	if ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST && isset( $_SERVER['REQUEST_METHOD'] ) && 'GET' === $_SERVER['REQUEST_METHOD'] ) {
		status_header( 403 );
		exit;
	}
}
add_action( 'init', 'fbg_block_xmlrpc_get', 0 );

/**
 * Restrict listing users via REST for non-admins.
 *
 * @param WP_REST_Response|WP_HTTP_Response|WP_Error|mixed $response Response.
 * @param WP_User                                          $user    User.
 * @param WP_REST_Request                                  $request Request.
 * @return WP_REST_Response|WP_Error
 */
function fbg_rest_user_visibility( $response, $user, $request ) {
	if ( ! current_user_can( 'list_users' ) ) {
		return new WP_Error(
			'rest_user_cannot_view',
			__( 'Sorry, you are not allowed to view users.', 'free-backlinks-generator' ),
			array( 'status' => 404 )
		);
	}
	return $response;
}
add_filter( 'rest_prepare_user', 'fbg_rest_user_visibility', 10, 3 );
