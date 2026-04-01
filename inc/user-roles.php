<?php
/**
 * Custom user role: fbg_member.
 *
 * @package Free_Backlinks_Generator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add fbg_member role (subscriber + upload files for featured images).
 */
function fbg_register_member_role() {
	if ( get_role( 'fbg_member' ) ) {
		return;
	}
	$sub = get_role( 'subscriber' );
	$caps = $sub ? $sub->capabilities : array( 'read' => true );
	$caps['upload_files'] = true;
	add_role( 'fbg_member', __( 'FBG Member', 'free-backlinks-generator' ), $caps );
}
add_action( 'after_setup_theme', 'fbg_register_member_role', 1 );

/**
 * Redirect logged-in users away from login/register templates.
 */
function fbg_auth_redirect_logged_in() {
	if ( ! is_user_logged_in() ) {
		return;
	}
	if ( is_page_template( 'page-templates/page-login.php' ) || is_page_template( 'page-templates/page-signup.php' ) ) {
		wp_safe_redirect( home_url( '/dashboard/' ) );
		exit;
	}
}
add_action( 'template_redirect', 'fbg_auth_redirect_logged_in', 1 );

/**
 * Block wp-admin for FBG members (front-end dashboard only). Allows AJAX and media upload endpoints.
 */
function fbg_restrict_member_wp_admin() {
	if ( ! is_user_logged_in() ) {
		return;
	}
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		return;
	}
	if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
		return;
	}
	$user = wp_get_current_user();
	if ( ! in_array( 'fbg_member', (array) $user->roles, true ) ) {
		return;
	}
	if ( current_user_can( 'manage_options' ) ) {
		return;
	}

	global $pagenow;
	$allowed = array( 'admin-ajax.php', 'async-upload.php', 'media-upload.php' );
	if ( isset( $pagenow ) && in_array( $pagenow, $allowed, true ) ) {
		return;
	}

	wp_safe_redirect( home_url( '/dashboard/' ) );
	exit;
}
add_action( 'admin_init', 'fbg_restrict_member_wp_admin', 0 );

/**
 * Show the WordPress admin bar only to users with the Administrator role.
 */
function fbg_show_admin_bar_only_for_administrators( $show ) {
	if ( ! is_user_logged_in() ) {
		return $show;
	}
	$user = wp_get_current_user();
	$is_administrator = in_array( 'administrator', (array) $user->roles, true );
	$is_network_super = is_multisite() && is_super_admin( $user->ID );
	if ( $is_administrator || $is_network_super ) {
		return $show;
	}
	return false;
}
add_filter( 'show_admin_bar', 'fbg_show_admin_bar_only_for_administrators', 99 );

/**
 * After login, never send FBG members to wp-admin.
 *
 * @param string           $redirect_to URL.
 * @param string           $request     Requested redirect.
 * @param WP_User|WP_Error $user        User.
 * @return string
 */
function fbg_member_login_redirect( $redirect_to, $request, $user ) {
	if ( is_wp_error( $user ) || ! $user instanceof WP_User ) {
		return $redirect_to;
	}
	if ( in_array( 'fbg_member', (array) $user->roles, true ) && ! user_can( $user, 'manage_options' ) ) {
		if ( false !== strpos( (string) $redirect_to, 'wp-admin' ) ) {
			return home_url( '/dashboard/' );
		}
	}
	return $redirect_to;
}
add_filter( 'login_redirect', 'fbg_member_login_redirect', 10, 3 );
