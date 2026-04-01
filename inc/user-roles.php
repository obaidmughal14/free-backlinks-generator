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
