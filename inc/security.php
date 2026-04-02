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

/**
 * Media library (modal): members only see their own uploads on the front end and in AJAX.
 * Administrators and users who can edit others’ posts keep full access.
 *
 * @param array<string, mixed> $query Query args for attachments.
 * @return array<string, mixed>
 */
function fbg_limit_media_library_to_current_user( $query ) {
	if ( ! is_user_logged_in() ) {
		return $query;
	}
	if ( current_user_can( 'manage_options' ) || current_user_can( 'edit_others_posts' ) ) {
		return $query;
	}
	$query['author'] = get_current_user_id();
	return $query;
}
add_filter( 'ajax_query_attachments_args', 'fbg_limit_media_library_to_current_user', 10, 1 );

/**
 * REST API media list: same restriction for the block/REST media picker.
 *
 * @param array<string, mixed>    $args    Query args.
 * @param WP_REST_Request         $request Request (unused).
 * @return array<string, mixed>
 */
function fbg_rest_limit_media_to_author( $args, $request ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
	if ( ! is_user_logged_in() ) {
		return $args;
	}
	if ( current_user_can( 'manage_options' ) || current_user_can( 'edit_others_posts' ) ) {
		return $args;
	}
	$args['author'] = get_current_user_id();
	return $args;
}
add_filter( 'rest_attachment_query', 'fbg_rest_limit_media_to_author', 10, 2 );
