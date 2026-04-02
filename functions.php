<?php
/**
 * Free Backlinks Generator theme bootstrap.
 *
 * @package Free_Backlinks_Generator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FBG_VERSION', '1.1.1' );
define( 'FBG_DIR', get_template_directory() );
define( 'FBG_URI', get_template_directory_uri() );

require_once FBG_DIR . '/inc/helpers.php';
require_once FBG_DIR . '/inc/reading-affiliate.php';
require_once FBG_DIR . '/inc/custom-post-types.php';
require_once FBG_DIR . '/inc/user-roles.php';
require_once FBG_DIR . '/inc/security.php';
require_once FBG_DIR . '/inc/seo.php';
require_once FBG_DIR . '/inc/ajax-handlers.php';
require_once FBG_DIR . '/inc/sidebar-ads.php';
if ( is_admin() ) {
	require_once FBG_DIR . '/inc/admin-affiliates.php';
}

/**
 * Theme setup.
 */
function fbg_theme_setup() {
	load_theme_textdomain( 'free-backlinks-generator', FBG_DIR . '/languages' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo', array( 'height' => 80, 'width' => 320, 'flex-height' => true, 'flex-width' => true ) );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/main.css' );

	register_nav_menus(
		array(
			'primary'   => __( 'Primary Menu', 'free-backlinks-generator' ),
			'footer'    => __( 'Footer Menu', 'free-backlinks-generator' ),
		)
	);

	add_image_size( 'fbg_card', 800, 450, true );
	add_image_size( 'fbg_hero', 1600, 700, true );
	add_image_size( 'fbg_sidebar_ad', 300, 600, true );
}
add_action( 'after_setup_theme', 'fbg_theme_setup' );

/**
 * Widget areas.
 */
function fbg_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Sidebar', 'free-backlinks-generator' ),
			'id'            => 'sidebar-1',
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		)
	);
}
add_action( 'widgets_init', 'fbg_widgets_init' );

/**
 * Enqueue front-end assets.
 */
function fbg_enqueue_assets() {
	wp_enqueue_style(
		'fbg-fonts',
		'https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&family=JetBrains+Mono:wght@400;500&display=swap',
		array(),
		null
	);
	wp_enqueue_style( 'fbg-main', FBG_URI . '/assets/css/main.css', array(), FBG_VERSION );

	if ( is_page_template( 'page-templates/page-signup.php' ) || is_page_template( 'page-templates/page-login.php' ) || is_page_template( 'page-templates/page-forgot-password.php' ) ) {
		wp_enqueue_style( 'fbg-auth', FBG_URI . '/assets/css/auth.css', array( 'fbg-main' ), FBG_VERSION );
		wp_enqueue_script( 'fbg-auth', FBG_URI . '/assets/js/auth.js', array(), FBG_VERSION, true );
		$auth_data = array(
			'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
			'loginNonce'    => wp_create_nonce( 'fbg_login_nonce' ),
			'registerNonce' => wp_create_nonce( 'fbg_register_nonce' ),
			'forgotNonce'   => wp_create_nonce( 'fbg_forgot_nonce' ),
			'resetNonce'    => wp_create_nonce( 'fbg_reset_nonce' ),
		);
		wp_localize_script( 'fbg-auth', 'fbgAuthData', $auth_data );
	}

	if ( is_page_template( 'page-templates/page-dashboard.php' ) ) {
		wp_enqueue_style( 'fbg-dashboard', FBG_URI . '/assets/css/dashboard.css', array( 'fbg-main' ), FBG_VERSION );
		wp_enqueue_script( 'fbg-dashboard', FBG_URI . '/assets/js/dashboard.js', array(), FBG_VERSION, true );
		wp_localize_script(
			'fbg-dashboard',
			'fbgDash',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'fbg_dashboard_nonce' ),
				'exportUrl' => wp_nonce_url( add_query_arg( 'fbg_export_links', '1', home_url( '/' ) ), 'fbg_export_csv' ),
			)
		);
	}

	if ( is_page_template( 'page-templates/page-submit-post.php' ) ) {
		wp_enqueue_editor();
		wp_enqueue_media();
		wp_enqueue_style( 'fbg-dashboard', FBG_URI . '/assets/css/dashboard.css', array( 'fbg-main' ), FBG_VERSION );
		wp_enqueue_script( 'fbg-submit', FBG_URI . '/assets/js/submit-post.js', array( 'jquery', 'editor' ), FBG_VERSION, true );
		wp_localize_script(
			'fbg-submit',
			'fbgSubmit',
			array(
				'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
				'nonce'        => wp_create_nonce( 'fbg_submit_nonce' ),
				'maxLinks'     => is_user_logged_in() ? fbg_get_user_link_limit( get_current_user_id() ) : 3,
				'anchorPh'     => __( 'Anchor text', 'free-backlinks-generator' ),
				'genericError' => __( 'We could not save your post. Please check the form and try again.', 'free-backlinks-generator' ),
				'parseError'   => __( 'The server returned an unexpected response. Try refreshing the page or logging in again.', 'free-backlinks-generator' ),
				'networkError' => __( 'Network error. Check your connection and try again.', 'free-backlinks-generator' ),
				'mediaError'   => __( 'The media library did not load. Refresh the page. If the problem continues, your account may need upload permission.', 'free-backlinks-generator' ),
			)
		);
	}

	if ( is_post_type_archive( 'fbg_post' ) || is_home() ) {
		wp_enqueue_style( 'fbg-blog', FBG_URI . '/assets/css/blog.css', array( 'fbg-main' ), FBG_VERSION );
	}

	if ( is_singular( 'fbg_post' ) ) {
		wp_enqueue_style( 'fbg-blog', FBG_URI . '/assets/css/blog.css', array( 'fbg-main' ), FBG_VERSION );
		wp_enqueue_script( 'fbg-single-sidebar', FBG_URI . '/assets/js/single-sidebar.js', array(), FBG_VERSION, true );
		wp_localize_script(
			'fbg-single-sidebar',
			'fbgSingleSidebar',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'fbg_sidebar_contact_nonce' ),
				'strings' => array(
					'sent'  => __( 'Thanks — your message was sent.', 'free-backlinks-generator' ),
					'error' => __( 'Could not send. Please try again.', 'free-backlinks-generator' ),
				),
			)
		);

		if ( is_user_logged_in() && function_exists( 'fbg_get_user_completed_peer_reads' ) ) {
			$read_pid = get_queried_object_id();
			$read_uid = get_current_user_id();
			if ( $read_pid && (int) get_post_field( 'post_author', $read_pid ) !== $read_uid ) {
				$read_done = in_array( $read_pid, fbg_get_user_completed_peer_reads( $read_uid ), true );
				if ( ! $read_done ) {
					wp_enqueue_script( 'fbg-reading', FBG_URI . '/assets/js/reading-tracker.js', array(), FBG_VERSION, true );
					wp_localize_script(
						'fbg-reading',
						'fbgReading',
						array(
							'ajaxUrl'         => admin_url( 'admin-ajax.php' ),
							'nonce'           => wp_create_nonce( 'fbg_read_progress' ),
							'postId'          => $read_pid,
							'intervalMs'      => 25000,
							'requiredSeconds' => (int) FBG_READ_SECONDS_REQUIRED,
							'strings'         => array(
								'progress' => __( 'Stay on this tab — about %d min of reading left to unlock credit for this post.', 'free-backlinks-generator' ),
								'done'     => __( 'This post counts toward your next guest-post slot. Thanks for reading!', 'free-backlinks-generator' ),
							),
						)
					);
				}
			}
		}
	}

	if (
		is_page_template(
			array(
				'page-templates/page-affiliate-program.php',
				'page-templates/page-cookie-policy.php',
				'page-templates/page-gdpr-notice.php',
			)
		)
	) {
		wp_enqueue_style( 'fbg-marketing', FBG_URI . '/assets/css/fbg-marketing.css', array( 'fbg-main' ), FBG_VERSION );
	}

	if ( is_page_template( 'page-templates/page-affiliate-program.php' ) ) {
		wp_enqueue_script( 'fbg-affiliate', FBG_URI . '/assets/js/affiliate.js', array(), FBG_VERSION, true );
		wp_localize_script(
			'fbg-affiliate',
			'fbgAffiliate',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'fbg_affiliate_nonce' ),
				'strings' => array(
					'sending' => __( 'Sending…', 'free-backlinks-generator' ),
					'sent'    => __( 'Application received. We will email you within 2 business days.', 'free-backlinks-generator' ),
					'error'   => __( 'Something went wrong. Please try again or email us from the Contact page.', 'free-backlinks-generator' ),
				),
			)
		);
	}

	$resp_deps = array( 'fbg-main' );
	if ( is_page_template( 'page-templates/page-signup.php' ) || is_page_template( 'page-templates/page-login.php' ) || is_page_template( 'page-templates/page-forgot-password.php' ) ) {
		$resp_deps[] = 'fbg-auth';
	}
	if ( is_page_template( 'page-templates/page-dashboard.php' ) || is_page_template( 'page-templates/page-submit-post.php' ) ) {
		$resp_deps[] = 'fbg-dashboard';
	}
	if ( is_post_type_archive( 'fbg_post' ) || is_home() || is_singular( 'fbg_post' ) ) {
		$resp_deps[] = 'fbg-blog';
	}
	if (
		is_page_template(
			array(
				'page-templates/page-affiliate-program.php',
				'page-templates/page-cookie-policy.php',
				'page-templates/page-gdpr-notice.php',
			)
		)
	) {
		$resp_deps[] = 'fbg-marketing';
	}
	wp_enqueue_style( 'fbg-responsive', FBG_URI . '/assets/css/fbg-responsive.css', $resp_deps, FBG_VERSION );

	wp_enqueue_script( 'fbg-main', FBG_URI . '/assets/js/main.js', array(), FBG_VERSION, true );
	wp_localize_script(
		'fbg-main',
		'fbgMain',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'fbg_archive_nonce' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'fbg_enqueue_assets' );

/**
 * Body classes for templates.
 *
 * @param array<string> $classes Classes.
 * @return array<string>
 */
function fbg_body_class( $classes ) {
	if ( is_page_template( 'page-templates/page-home.php' ) ) {
		$classes[] = 'fbg-home';
	}
	if (
		is_page_template(
			array(
				'page-templates/page-signup.php',
				'page-templates/page-login.php',
				'page-templates/page-forgot-password.php',
			)
		)
	) {
		$classes[] = 'fbg-auth-page';
	}
	if ( is_page_template( array( 'page-templates/page-dashboard.php', 'page-templates/page-submit-post.php' ) ) ) {
		$classes[] = 'fbg-dash-page';
	}
	if ( is_page_template( 'page-templates/page-submit-post.php' ) ) {
		$classes[] = 'fbg-submit-page';
	}
	if (
		is_page_template(
			array(
				'page-templates/page-affiliate-program.php',
				'page-templates/page-cookie-policy.php',
				'page-templates/page-gdpr-notice.php',
			)
		)
	) {
		$classes[] = 'fbg-marketing-page';
	}
	return $classes;
}
add_filter( 'body_class', 'fbg_body_class' );

/**
 * On theme activation: tables, terms, pages, flush rules.
 */
function fbg_theme_activation() {
	fbg_register_post_types();
	if ( function_exists( 'fbg_register_sidebar_ad_cpt' ) ) {
		fbg_register_sidebar_ad_cpt();
	}
	if ( ! get_role( 'fbg_member' ) ) {
		$sub  = get_role( 'subscriber' );
		$caps = $sub ? $sub->capabilities : array( 'read' => true );
		$caps['upload_files'] = true;
		add_role( 'fbg_member', 'FBG Member', $caps );
	}
	fbg_create_notifications_table();
	fbg_seed_taxonomy_terms();

	$pages = array(
		array(
			'title'    => 'Home',
			'slug'     => 'home',
			'template' => 'page-templates/page-home.php',
		),
		array(
			'title'    => 'Register',
			'slug'     => 'register',
			'template' => 'page-templates/page-signup.php',
		),
		array(
			'title'    => 'Login',
			'slug'     => 'login',
			'template' => 'page-templates/page-login.php',
		),
		array(
			'title'    => 'Forgot Password',
			'slug'     => 'forgot-password',
			'template' => 'page-templates/page-forgot-password.php',
		),
		array(
			'title'    => 'Dashboard',
			'slug'     => 'dashboard',
			'template' => 'page-templates/page-dashboard.php',
		),
		array(
			'title'    => 'Submit Post',
			'slug'     => 'submit-post',
			'template' => 'page-templates/page-submit-post.php',
		),
		array(
			'title' => 'About',
			'slug'  => 'about',
		),
		array(
			'title' => 'How It Works',
			'slug'  => 'how-it-works',
		),
		array(
			'title' => 'Community Guidelines',
			'slug'  => 'community-guidelines',
		),
		array(
			'title' => 'Contact',
			'slug'  => 'contact',
		),
		array(
			'title' => 'Privacy Policy',
			'slug'  => 'privacy-policy',
		),
		array(
			'title' => 'Terms of Service',
			'slug'  => 'terms-of-service',
		),
		array(
			'title'    => 'Affiliate Program',
			'slug'     => 'affiliate-program',
			'template' => 'page-templates/page-affiliate-program.php',
		),
		array(
			'title'    => 'Cookie Policy',
			'slug'     => 'cookie-policy',
			'template' => 'page-templates/page-cookie-policy.php',
		),
		array(
			'title'    => 'GDPR Notice',
			'slug'     => 'gdpr-notice',
			'template' => 'page-templates/page-gdpr-notice.php',
		),
	);

	foreach ( $pages as $p ) {
		$existing = get_page_by_path( $p['slug'] );
		if ( $existing ) {
			continue;
		}
		$post_id = wp_insert_post(
			array(
				'post_title'  => $p['title'],
				'post_name'   => $p['slug'],
				'post_status' => 'publish',
				'post_type'   => 'page',
				'post_content'=> isset( $p['content'] ) ? $p['content'] : '',
			)
		);
		if ( $post_id && ! is_wp_error( $post_id ) && ! empty( $p['template'] ) ) {
			update_post_meta( $post_id, '_wp_page_template', $p['template'] );
		}
	}

	$home = get_page_by_path( 'home' );
	if ( $home ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home->ID );
	}

	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'fbg_theme_activation' );

/**
 * Create affiliate & legal pages once for sites that activated the theme before these pages existed.
 */
function fbg_maybe_install_marketing_pages() {
	if ( ! current_user_can( 'manage_options' ) || get_option( 'fbg_marketing_pages_v1' ) ) {
		return;
	}
	$defs = array(
		array(
			'title'    => 'Affiliate Program',
			'slug'     => 'affiliate-program',
			'template' => 'page-templates/page-affiliate-program.php',
		),
		array(
			'title'    => 'Cookie Policy',
			'slug'     => 'cookie-policy',
			'template' => 'page-templates/page-cookie-policy.php',
		),
		array(
			'title'    => 'GDPR Notice',
			'slug'     => 'gdpr-notice',
			'template' => 'page-templates/page-gdpr-notice.php',
		),
	);
	foreach ( $defs as $p ) {
		if ( get_page_by_path( $p['slug'] ) ) {
			continue;
		}
		$post_id = wp_insert_post(
			array(
				'post_title'  => $p['title'],
				'post_name'   => $p['slug'],
				'post_status' => 'publish',
				'post_type'   => 'page',
			)
		);
		if ( $post_id && ! is_wp_error( $post_id ) ) {
			update_post_meta( $post_id, '_wp_page_template', $p['template'] );
		}
	}
	update_option( 'fbg_marketing_pages_v1', 1 );
}
add_action( 'admin_init', 'fbg_maybe_install_marketing_pages', 30 );
