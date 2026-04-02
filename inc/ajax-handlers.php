<?php
/**
 * AJAX handlers for auth, posts, dashboard, archive.
 *
 * @package Free_Backlinks_Generator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registration.
 */
function fbg_handle_registration() {
	check_ajax_referer( 'fbg_register_nonce', 'nonce' );

	if ( empty( $_POST['terms'] ) ) {
		wp_send_json_error( array( 'field' => 'terms', 'message' => __( 'Please accept the Terms of Service and Privacy Policy.', 'free-backlinks-generator' ) ) );
	}

	$name     = isset( $_POST['full_name'] ) ? sanitize_text_field( wp_unslash( $_POST['full_name'] ) ) : '';
	$email    = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$url      = isset( $_POST['website_url'] ) ? esc_url_raw( wp_unslash( $_POST['website_url'] ) ) : '';
	$password = isset( $_POST['password'] ) ? wp_unslash( $_POST['password'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$niche    = isset( $_POST['niche'] ) ? sanitize_text_field( wp_unslash( $_POST['niche'] ) ) : '';

	if ( ! is_email( $email ) ) {
		wp_send_json_error( array( 'field' => 'email', 'message' => __( 'Please enter a valid email address.', 'free-backlinks-generator' ) ) );
	}
	if ( email_exists( $email ) ) {
		wp_send_json_error( array( 'field' => 'email', 'message' => __( 'This email is already registered.', 'free-backlinks-generator' ) ) );
	}
	if ( ! filter_var( $url, FILTER_VALIDATE_URL ) || ! preg_match( '#^https?://#i', $url ) ) {
		wp_send_json_error( array( 'field' => 'website_url', 'message' => __( 'Please enter a valid website URL starting with https://', 'free-backlinks-generator' ) ) );
	}
	if ( strlen( $password ) < 8 ) {
		wp_send_json_error( array( 'field' => 'password', 'message' => __( 'Password must be at least 8 characters.', 'free-backlinks-generator' ) ) );
	}
	$confirm = isset( $_POST['confirm_password'] ) ? wp_unslash( $_POST['confirm_password'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	if ( $password !== $confirm ) {
		wp_send_json_error( array( 'field' => 'confirm_password', 'message' => __( 'Passwords do not match.', 'free-backlinks-generator' ) ) );
	}

	$username = fbg_username_from_email( $email );
	$user_id  = wp_create_user( $username, $password, $email );
	if ( is_wp_error( $user_id ) ) {
		wp_send_json_error( array( 'message' => $user_id->get_error_message() ) );
	}

	wp_update_user(
		array(
			'ID'           => $user_id,
			'display_name' => $name,
			'role'         => 'fbg_member',
		)
	);
	update_user_meta( $user_id, '_fbg_website_url', $url );
	update_user_meta( $user_id, '_fbg_niche', $niche );
	update_user_meta( $user_id, '_fbg_membership', 'free' );
	update_user_meta( $user_id, '_fbg_joined', time() );
	update_user_meta( $user_id, '_fbg_total_posts', 0 );
	update_user_meta( $user_id, '_fbg_total_links', 0 );
	update_user_meta( $user_id, '_fbg_tier', 'seedling' );

	wp_set_current_user( $user_id );
	wp_set_auth_cookie( $user_id, true );

	fbg_send_welcome_email( $user_id, $name, $email );
	fbg_create_notification(
		$user_id,
		'welcome',
		__( 'Welcome to Free Backlinks Generator! Complete your profile to get started.', 'free-backlinks-generator' ),
		null
	);

	wp_send_json_success( array( 'redirect' => home_url( '/dashboard/?welcome=1' ) ) );
}
add_action( 'wp_ajax_nopriv_fbg_register', 'fbg_handle_registration' );

/**
 * Login.
 */
function fbg_handle_login() {
	check_ajax_referer( 'fbg_login_nonce', 'nonce' );

	$email    = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$password = isset( $_POST['password'] ) ? wp_unslash( $_POST['password'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$remember = ! empty( $_POST['remember'] );

	$ip_key = 'fbg_login_' . md5( isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown' );
	$block  = get_transient( 'fbg_login_block_' . $ip_key );
	if ( $block ) {
		wp_send_json_error(
			array(
				'message' => __( 'Too many failed attempts. Try again later.', 'free-backlinks-generator' ),
				'locked'  => true,
			)
		);
	}

	$user = get_user_by( 'email', $email );
	if ( ! $user || ! wp_check_password( $password, $user->user_pass, $user->ID ) ) {
		$attempts = (int) get_transient( $ip_key );
		++$attempts;
		set_transient( $ip_key, $attempts, 15 * MINUTE_IN_SECONDS );
		if ( $attempts >= 5 ) {
			set_transient( 'fbg_login_block_' . $ip_key, 1, 15 * MINUTE_IN_SECONDS );
			delete_transient( $ip_key );
			wp_send_json_error(
				array(
					'message' => __( 'Too many failed attempts. Try again in 15 minutes.', 'free-backlinks-generator' ),
					'locked'  => true,
				)
			);
		}
		wp_send_json_error(
			array(
				'message' => sprintf(
					/* translators: %d attempts left */
					__( 'Incorrect email or password. %d attempts remaining.', 'free-backlinks-generator' ),
					max( 0, 5 - $attempts )
				),
			)
		);
	}

	delete_transient( $ip_key );
	delete_transient( 'fbg_login_block_' . $ip_key );
	wp_set_current_user( $user->ID );
	wp_set_auth_cookie( $user->ID, $remember );
	wp_send_json_success( array( 'redirect' => home_url( '/dashboard/' ) ) );
}
add_action( 'wp_ajax_nopriv_fbg_login', 'fbg_handle_login' );

/**
 * Submit guest post.
 */
function fbg_handle_post_submission() {
	check_ajax_referer( 'fbg_submit_nonce', 'nonce' );
	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => __( 'Please log in.', 'free-backlinks-generator' ) ) );
	}

	$user_id  = get_current_user_id();
	$is_draft = isset( $_POST['status'] ) && 'draft' === sanitize_text_field( wp_unslash( $_POST['status'] ) );

	$title   = isset( $_POST['title'] ) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : '';
	$content = isset( $_POST['content'] ) ? wp_kses_post( wp_unslash( $_POST['content'] ) ) : '';
	$niche   = isset( $_POST['niche'] ) ? sanitize_text_field( wp_unslash( $_POST['niche'] ) ) : '';
	$type    = isset( $_POST['content_type'] ) ? sanitize_text_field( wp_unslash( $_POST['content_type'] ) ) : '';
	$excerpt = isset( $_POST['excerpt'] ) ? sanitize_text_field( wp_unslash( $_POST['excerpt'] ) ) : '';
	$excerpt = substr( $excerpt, 0, 160 );

	$raw_links = isset( $_POST['backlinks'] ) && is_array( $_POST['backlinks'] ) ? wp_unslash( $_POST['backlinks'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$max_links = fbg_get_user_link_limit( $user_id );
	$links     = array();
	$i         = 0;
	foreach ( $raw_links as $link ) {
		if ( $i >= $max_links ) {
			break;
		}
		if ( ! is_array( $link ) ) {
			continue;
		}
		$anchor = isset( $link['anchor'] ) ? sanitize_text_field( $link['anchor'] ) : '';
		$url    = isset( $link['url'] ) ? esc_url_raw( $link['url'] ) : '';
		if ( $anchor && $url && filter_var( $url, FILTER_VALIDATE_URL ) && preg_match( '#^https?://#i', $url ) ) {
			$links[] = array( 'anchor' => $anchor, 'url' => $url );
			++$i;
		}
	}

	if ( '' === trim( $title ) ) {
		wp_send_json_error( array( 'message' => __( 'Please enter a post title.', 'free-backlinks-generator' ) ) );
	}
	if ( '' === $niche || ! term_exists( $niche, 'fbg_niche' ) ) {
		wp_send_json_error( array( 'message' => __( 'Please select a valid niche / category.', 'free-backlinks-generator' ) ) );
	}
	if ( '' === $type || ! term_exists( $type, 'fbg_content_type' ) ) {
		wp_send_json_error( array( 'message' => __( 'Please select a valid content type.', 'free-backlinks-generator' ) ) );
	}

	$thumb = isset( $_POST['featured_image_id'] ) ? absint( $_POST['featured_image_id'] ) : 0;

	if ( ! $is_draft ) {
		$word_count = fbg_count_words_html( $content );
		if ( $word_count < 200 ) {
			wp_send_json_error(
				array(
					'message' => sprintf(
						/* translators: %d: current word count */
						__( 'Your post must be at least 200 words before you can submit for review. Current length: %d words.', 'free-backlinks-generator' ),
						$word_count
					),
				)
			);
		}
		if ( strlen( trim( $excerpt ) ) < 40 ) {
			wp_send_json_error( array( 'message' => __( 'Please write a meta description / excerpt of at least 40 characters (max 160).', 'free-backlinks-generator' ) ) );
		}
		if ( empty( $links ) ) {
			wp_send_json_error( array( 'message' => __( 'Add at least one backlink with anchor text and a full URL (https://…).', 'free-backlinks-generator' ) ) );
		}
		if ( ! $thumb ) {
			wp_send_json_error( array( 'message' => __( 'Please select a featured image before submitting for review.', 'free-backlinks-generator' ) ) );
		}
		$att = get_post( $thumb );
		if ( ! $att || 'attachment' !== $att->post_type || ! str_starts_with( (string) $att->post_mime_type, 'image/' ) ) {
			wp_send_json_error( array( 'message' => __( 'Featured image must be a valid image file (JPG, PNG, or WebP).', 'free-backlinks-generator' ) ) );
		}
		if ( (int) $att->post_author !== $user_id && ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'You can only use images uploaded while logged in as your account.', 'free-backlinks-generator' ) ) );
		}
	}

	$post_status = $is_draft ? 'draft' : 'pending';

	$post_id = wp_insert_post(
		array(
			'post_title'   => $title,
			'post_content' => $content,
			'post_excerpt' => $excerpt,
			'post_status'  => $post_status,
			'post_type'    => 'fbg_post',
			'post_author'  => $user_id,
		),
		true
	);

	if ( is_wp_error( $post_id ) ) {
		wp_send_json_error( array( 'message' => $post_id->get_error_message() ) );
	}

	update_post_meta( $post_id, '_fbg_content_status', $is_draft ? 'draft' : 'pending' );
	update_post_meta( $post_id, '_fbg_backlinks_array', $links );
	update_post_meta( $post_id, '_fbg_backlink_count', count( $links ) );

	wp_set_object_terms( $post_id, array( $niche ), 'fbg_niche', false );
	wp_set_object_terms( $post_id, array( $type ), 'fbg_content_type', false );

	if ( $thumb && get_post( $thumb ) ) {
		set_post_thumbnail( $post_id, $thumb );
	}

	if ( ! $is_draft ) {
		fbg_notify_admin_new_submission( $post_id, $user_id, $title );
		fbg_create_notification(
			$user_id,
			'post_submitted',
			sprintf(
				/* translators: %s post title */
				__( 'Your guest post "%s" has been submitted and is waiting for administrator approval.', 'free-backlinks-generator' ),
				$title
			),
			$post_id
		);
	}

	wp_send_json_success(
		array(
			'redirect' => $is_draft ? home_url( '/dashboard/?draft=1' ) : home_url( '/dashboard/?submitted=1' ),
		)
	);
}
add_action( 'wp_ajax_fbg_submit_post', 'fbg_handle_post_submission' );

/**
 * Archive load more / filter.
 */
function fbg_archive_posts() {
	check_ajax_referer( 'fbg_archive_nonce', 'nonce' );

	$paged  = isset( $_POST['paged'] ) ? max( 1, absint( $_POST['paged'] ) ) : 1;
	$niche  = isset( $_POST['niche'] ) ? sanitize_text_field( wp_unslash( $_POST['niche'] ) ) : '';
	$search = isset( $_POST['s'] ) ? sanitize_text_field( wp_unslash( $_POST['s'] ) ) : '';
	$order  = isset( $_POST['order'] ) ? sanitize_text_field( wp_unslash( $_POST['order'] ) ) : 'newest';

	$args = array(
		'post_type'      => 'fbg_post',
		'post_status'    => 'publish',
		'posts_per_page' => 9,
		'paged'          => $paged,
		's'              => $search,
	);
	if ( 'oldest' === $order ) {
		$args['order'] = 'ASC';
	}

	if ( $niche && 'all' !== $niche ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'fbg_niche',
				'field'    => 'slug',
				'terms'    => $niche,
			),
		);
	}

	$q = new WP_Query( $args );
	ob_start();
	if ( $q->have_posts() ) {
		while ( $q->have_posts() ) {
			$q->the_post();
			get_template_part( 'template-parts/blog', 'card' );
		}
		wp_reset_postdata();
	}
	$html = ob_get_clean();

	wp_send_json_success(
		array(
			'html'        => $html,
			'max_pages'   => (int) $q->max_num_pages,
			'found_posts' => (int) $q->found_posts,
		)
	);
}
add_action( 'wp_ajax_nopriv_fbg_archive_posts', 'fbg_archive_posts' );
add_action( 'wp_ajax_fbg_archive_posts', 'fbg_archive_posts' );

/**
 * Profile save.
 */
function fbg_save_profile() {
	check_ajax_referer( 'fbg_dashboard_nonce', 'nonce' );
	if ( ! is_user_logged_in() ) {
		wp_send_json_error();
	}
	$user_id = get_current_user_id();
	$name    = isset( $_POST['display_name'] ) ? sanitize_text_field( wp_unslash( $_POST['display_name'] ) ) : '';
	$bio     = isset( $_POST['bio'] ) ? sanitize_textarea_field( wp_unslash( $_POST['bio'] ) ) : '';
	$url     = isset( $_POST['website_url'] ) ? esc_url_raw( wp_unslash( $_POST['website_url'] ) ) : '';
	$niche   = isset( $_POST['niche'] ) ? sanitize_text_field( wp_unslash( $_POST['niche'] ) ) : '';
	$tw      = isset( $_POST['twitter'] ) ? sanitize_text_field( wp_unslash( $_POST['twitter'] ) ) : '';
	$li      = isset( $_POST['linkedin'] ) ? esc_url_raw( wp_unslash( $_POST['linkedin'] ) ) : '';

	wp_update_user(
		array(
			'ID'           => $user_id,
			'display_name' => $name,
		)
	);
	update_user_meta( $user_id, 'description', $bio );
	update_user_meta( $user_id, '_fbg_website_url', $url );
	update_user_meta( $user_id, '_fbg_niche', $niche );
	update_user_meta( $user_id, '_fbg_twitter', $tw );
	update_user_meta( $user_id, '_fbg_linkedin', $li );

	if ( ! empty( $_POST['avatar_id'] ) ) {
		$aid = absint( $_POST['avatar_id'] );
		if ( $aid && get_post( $aid ) ) {
			update_user_meta( $user_id, '_fbg_avatar_id', $aid );
		}
	}

	wp_send_json_success( array( 'message' => __( 'Profile saved.', 'free-backlinks-generator' ) ) );
}
add_action( 'wp_ajax_fbg_save_profile', 'fbg_save_profile' );

/**
 * Mark notification read.
 */
function fbg_notification_read() {
	check_ajax_referer( 'fbg_dashboard_nonce', 'nonce' );
	if ( ! is_user_logged_in() ) {
		wp_send_json_error();
	}
	global $wpdb;
	$id      = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
	$user_id = get_current_user_id();
	$table   = fbg_notifications_table();
	$wpdb->update( $table, array( 'is_read' => 1 ), array( 'id' => $id, 'user_id' => $user_id ), array( '%d' ), array( '%d', '%d' ) );
	wp_send_json_success();
}
add_action( 'wp_ajax_fbg_notification_read', 'fbg_notification_read' );

/**
 * Mark all notifications read.
 */
function fbg_notifications_read_all() {
	check_ajax_referer( 'fbg_dashboard_nonce', 'nonce' );
	if ( ! is_user_logged_in() ) {
		wp_send_json_error();
	}
	global $wpdb;
	$table   = fbg_notifications_table();
	$user_id = get_current_user_id();
	// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$wpdb->query( $wpdb->prepare( "UPDATE {$table} SET is_read = 1 WHERE user_id = %d", $user_id ) );
	wp_send_json_success();
}
add_action( 'wp_ajax_fbg_notifications_read_all', 'fbg_notifications_read_all' );

/**
 * Save settings preferences.
 */
function fbg_save_settings() {
	check_ajax_referer( 'fbg_dashboard_nonce', 'nonce' );
	if ( ! is_user_logged_in() ) {
		wp_send_json_error();
	}
	$uid = get_current_user_id();
	$keys = array(
		'email_approved',
		'email_rejected',
		'weekly_digest',
		'community_tips',
		'dir_public',
		'show_link_count',
		'show_website',
	);
	foreach ( $keys as $k ) {
		$val = ! empty( $_POST[ $k ] ) ? '1' : '0';
		update_user_meta( $uid, '_fbg_pref_' . $k, $val );
	}
	wp_send_json_success( array( 'message' => __( 'Settings saved.', 'free-backlinks-generator' ) ) );
}
add_action( 'wp_ajax_fbg_save_settings', 'fbg_save_settings' );

/**
 * Change password.
 */
function fbg_change_password() {
	check_ajax_referer( 'fbg_dashboard_nonce', 'nonce' );
	if ( ! is_user_logged_in() ) {
		wp_send_json_error();
	}
	$user = wp_get_current_user();
	$cur  = isset( $_POST['current'] ) ? wp_unslash( $_POST['current'] ) : ''; // phpcs:ignore
	$new  = isset( $_POST['new_password'] ) ? wp_unslash( $_POST['new_password'] ) : ''; // phpcs:ignore
	$conf = isset( $_POST['confirm'] ) ? wp_unslash( $_POST['confirm'] ) : ''; // phpcs:ignore
	if ( ! wp_check_password( $cur, $user->user_pass, $user->ID ) ) {
		wp_send_json_error( array( 'message' => __( 'Current password is incorrect.', 'free-backlinks-generator' ) ) );
	}
	if ( strlen( $new ) < 8 || $new !== $conf ) {
		wp_send_json_error( array( 'message' => __( 'Invalid new password.', 'free-backlinks-generator' ) ) );
	}
	wp_set_password( $new, $user->ID );
	wp_set_auth_cookie( $user->ID );
	wp_send_json_success( array( 'message' => __( 'Password updated.', 'free-backlinks-generator' ) ) );
}
add_action( 'wp_ajax_fbg_change_password', 'fbg_change_password' );

/**
 * Delete user account and posts.
 */
function fbg_delete_account() {
	check_ajax_referer( 'fbg_dashboard_nonce', 'nonce' );
	if ( ! is_user_logged_in() ) {
		wp_send_json_error();
	}
	$confirm = isset( $_POST['confirm'] ) ? sanitize_text_field( wp_unslash( $_POST['confirm'] ) ) : '';
	if ( 'DELETE' !== $confirm ) {
		wp_send_json_error( array( 'message' => __( 'Confirmation text did not match.', 'free-backlinks-generator' ) ) );
	}
	$user_id = get_current_user_id();
	$posts   = get_posts(
		array(
			'post_type'      => 'fbg_post',
			'author'         => $user_id,
			'posts_per_page' => -1,
			'post_status'    => 'any',
			'fields'         => 'ids',
		)
	);
	foreach ( $posts as $pid ) {
		wp_delete_post( $pid, true );
	}
	require_once ABSPATH . 'wp-admin/includes/user.php';
	wp_logout();
	wp_delete_user( $user_id );
	wp_send_json_success( array( 'redirect' => home_url( '/' ) ) );
}
add_action( 'wp_ajax_fbg_delete_account', 'fbg_delete_account' );

/**
 * Delete single post (author).
 */
function fbg_delete_my_post() {
	check_ajax_referer( 'fbg_dashboard_nonce', 'nonce' );
	if ( ! is_user_logged_in() ) {
		wp_send_json_error();
	}
	$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
	$post    = get_post( $post_id );
	if ( ! $post || 'fbg_post' !== $post->post_type || (int) $post->post_author !== get_current_user_id() ) {
		wp_send_json_error();
	}
	wp_delete_post( $post_id, true );
	wp_send_json_success();
}
add_action( 'wp_ajax_fbg_delete_my_post', 'fbg_delete_my_post' );

/**
 * Bulk delete posts.
 */
function fbg_bulk_delete_posts() {
	check_ajax_referer( 'fbg_dashboard_nonce', 'nonce' );
	if ( ! is_user_logged_in() ) {
		wp_send_json_error();
	}
	$ids = isset( $_POST['post_ids'] ) ? array_map( 'absint', (array) wp_unslash( $_POST['post_ids'] ) ) : array();
	foreach ( $ids as $post_id ) {
		$post = get_post( $post_id );
		if ( $post && 'fbg_post' === $post->post_type && (int) $post->post_author === get_current_user_id() ) {
			wp_delete_post( $post_id, true );
		}
	}
	wp_send_json_success();
}
add_action( 'wp_ajax_fbg_bulk_delete_posts', 'fbg_bulk_delete_posts' );

/**
 * Lost password request (AJAX).
 */
function fbg_lost_password() {
	check_ajax_referer( 'fbg_forgot_nonce', 'nonce' );
	$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	if ( ! is_email( $email ) || ! email_exists( $email ) ) {
		// Do not reveal if email exists.
		wp_send_json_success( array( 'step' => 'sent', 'email' => $email ) );
	}
	$user = get_user_by( 'email', $email );
	if ( $user ) {
		retrieve_password( $user->user_login );
	}
	wp_send_json_success( array( 'step' => 'sent', 'email' => $email ) );
}
add_action( 'wp_ajax_nopriv_fbg_lost_password', 'fbg_lost_password' );

/**
 * Reset password via AJAX (key + login).
 */
function fbg_reset_password() {
	check_ajax_referer( 'fbg_reset_nonce', 'nonce' );
	$key   = isset( $_POST['key'] ) ? sanitize_text_field( wp_unslash( $_POST['key'] ) ) : '';
	$login = isset( $_POST['login'] ) ? sanitize_text_field( wp_unslash( $_POST['login'] ) ) : '';
	$pass  = isset( $_POST['password'] ) ? wp_unslash( $_POST['password'] ) : ''; // phpcs:ignore
	$conf  = isset( $_POST['confirm_password'] ) ? wp_unslash( $_POST['confirm_password'] ) : ''; // phpcs:ignore
	$user  = check_password_reset_key( $key, $login );
	if ( is_wp_error( $user ) ) {
		wp_send_json_error( array( 'message' => $user->get_error_message() ) );
	}
	if ( strlen( $pass ) < 8 || $pass !== $conf ) {
		wp_send_json_error( array( 'message' => __( 'Passwords must match and be at least 8 characters.', 'free-backlinks-generator' ) ) );
	}
	reset_password( $user, $pass );
	wp_send_json_success( array( 'redirect' => home_url( '/login/?reset=success' ) ) );
}
add_action( 'wp_ajax_nopriv_fbg_reset_password', 'fbg_reset_password' );

/**
 * Export user's backlinks as CSV (frontend download).
 */
function fbg_maybe_export_links_csv() {
	if ( ! isset( $_GET['fbg_export_links'] ) ) {
		return;
	}
	if ( ! is_user_logged_in() || ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'fbg_export_csv' ) ) {
		wp_die( esc_html__( 'Invalid request.', 'free-backlinks-generator' ) );
	}
	$user_id = get_current_user_id();
	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=fbg-links-' . $user_id . '.csv' );
	$out = fopen( 'php://output', 'w' );
	fputcsv( $out, array( 'Target URL', 'Anchor', 'Post', 'Status', 'Date' ) );
	$posts = get_posts(
		array(
			'post_type'      => 'fbg_post',
			'post_status'    => array( 'publish', 'pending' ),
			'author'         => $user_id,
			'posts_per_page' => -1,
		)
	);
	foreach ( $posts as $p ) {
		$links = get_post_meta( $p->ID, '_fbg_backlinks_array', true );
		if ( ! is_array( $links ) ) {
			continue;
		}
		$st = get_post_meta( $p->ID, '_fbg_content_status', true );
		if ( 'publish' === $p->post_status ) {
			$st = 'live';
		}
		foreach ( $links as $row ) {
			fputcsv(
				$out,
				array(
					isset( $row['url'] ) ? $row['url'] : '',
					isset( $row['anchor'] ) ? $row['anchor'] : '',
					$p->post_title,
					$st,
					get_the_date( 'Y-m-d', $p ),
				)
			);
		}
	}
	fclose( $out );
	exit;
}
add_action( 'template_redirect', 'fbg_maybe_export_links_csv', 1 );

/**
 * GDPR JSON export for current user.
 */
function fbg_maybe_export_gdpr() {
	if ( ! isset( $_GET['fbg_gdpr_export'] ) ) {
		return;
	}
	if ( ! is_user_logged_in() || ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'fbg_gdpr' ) ) {
		wp_die( esc_html__( 'Invalid request.', 'free-backlinks-generator' ) );
	}
	$user = wp_get_current_user();
	$posts = get_posts(
		array(
			'post_type'      => 'fbg_post',
			'author'         => $user->ID,
			'post_status'    => 'any',
			'posts_per_page' => -1,
		)
	);
	$export_posts = array();
	foreach ( $posts as $p ) {
		$export_posts[] = array(
			'id'      => $p->ID,
			'title'   => $p->post_title,
			'status'  => $p->post_status,
			'content' => wp_strip_all_tags( $p->post_content ),
			'meta'    => array(
				'backlinks' => get_post_meta( $p->ID, '_fbg_backlinks_array', true ),
			),
		);
	}
	$raw_meta = get_user_meta( $user->ID );
	$meta_out = array();
	foreach ( $raw_meta as $k => $vals ) {
		$meta_out[ $k ] = isset( $vals[0] ) ? maybe_unserialize( $vals[0] ) : '';
	}
	$payload = array(
		'user'  => array(
			'login'   => $user->user_login,
			'email'   => $user->user_email,
			'display' => $user->display_name,
		),
		'meta'  => $meta_out,
		'posts' => $export_posts,
	);
	nocache_headers();
	header( 'Content-Type: application/json; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=fbg-data-' . $user->ID . '.json' );
	echo wp_json_encode( $payload );
	exit;
}
add_action( 'template_redirect', 'fbg_maybe_export_gdpr', 1 );

/**
 * Public affiliate program application (rate-limited, stored + emailed).
 */
function fbg_handle_affiliate_apply() {
	check_ajax_referer( 'fbg_affiliate_nonce', 'nonce' );

	$ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '0';
	$key = 'fbg_affiliate_rate_' . md5( $ip );
	$hit = (int) get_transient( $key );
	if ( $hit >= 5 ) {
		wp_send_json_error( array( 'message' => __( 'Too many applications from this network. Please try again in an hour.', 'free-backlinks-generator' ) ) );
	}
	set_transient( $key, $hit + 1, HOUR_IN_SECONDS );

	$name  = isset( $_POST['full_name'] ) ? sanitize_text_field( wp_unslash( $_POST['full_name'] ) ) : '';
	$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$web   = isset( $_POST['website'] ) ? esc_url_raw( wp_unslash( $_POST['website'] ) ) : '';
	$aud   = isset( $_POST['audience'] ) ? sanitize_text_field( wp_unslash( $_POST['audience'] ) ) : '';
	$niche = isset( $_POST['niche'] ) ? sanitize_text_field( wp_unslash( $_POST['niche'] ) ) : '';
	$plan  = isset( $_POST['promo_plan'] ) ? sanitize_textarea_field( wp_unslash( $_POST['promo_plan'] ) ) : '';
	$agree = ! empty( $_POST['agree_terms'] );

	if ( strlen( $name ) < 2 || ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => __( 'Please enter a valid name and email.', 'free-backlinks-generator' ) ) );
	}
	if ( ! $web || ! filter_var( $web, FILTER_VALIDATE_URL ) || ! preg_match( '#^https?://#i', $web ) ) {
		wp_send_json_error( array( 'message' => __( 'Please enter a valid website URL (https://).', 'free-backlinks-generator' ) ) );
	}
	$allowed_aud = array( 'under-5k', '5k-25k', '25k-100k', '100k-plus' );
	if ( ! in_array( $aud, $allowed_aud, true ) ) {
		wp_send_json_error( array( 'message' => __( 'Please select monthly reach.', 'free-backlinks-generator' ) ) );
	}
	if ( strlen( $niche ) < 2 || strlen( $plan ) < 20 ) {
		wp_send_json_error( array( 'message' => __( 'Please describe your niche and how you will promote us (at least a short paragraph).', 'free-backlinks-generator' ) ) );
	}
	if ( ! $agree ) {
		wp_send_json_error( array( 'message' => __( 'You must agree to the partner terms.', 'free-backlinks-generator' ) ) );
	}

	$lead = array(
		'ts'    => time(),
		'ip'    => $ip,
		'name'  => $name,
		'email' => $email,
		'web'   => $web,
		'aud'   => $aud,
		'niche' => $niche,
		'plan'  => $plan,
	);
	$leads = get_option( 'fbg_affiliate_leads', array() );
	if ( ! is_array( $leads ) ) {
		$leads = array();
	}
	array_unshift( $leads, $lead );
	$leads = array_slice( $leads, 0, 100 );
	update_option( 'fbg_affiliate_leads', $leads, false );

	$admin = get_option( 'admin_email' );
	if ( is_email( $admin ) && function_exists( 'fbg_wp_mail_html' ) && function_exists( 'fbg_email_html_layout' ) ) {
		$subj = sprintf(
			/* translators: %s site name */
			__( '[%s] New affiliate application', 'free-backlinks-generator' ),
			get_bloginfo( 'name' )
		);
		$rows = '<table role="presentation" style="width:100%;font-size:15px;border-collapse:collapse;">'
			. '<tr><td style="padding:8px 0;border-bottom:1px solid #e2e8f0;"><strong>' . esc_html__( 'Name', 'free-backlinks-generator' ) . '</strong></td><td style="padding:8px 0;border-bottom:1px solid #e2e8f0;">' . esc_html( $name ) . '</td></tr>'
			. '<tr><td style="padding:8px 0;border-bottom:1px solid #e2e8f0;"><strong>' . esc_html__( 'Email', 'free-backlinks-generator' ) . '</strong></td><td style="padding:8px 0;border-bottom:1px solid #e2e8f0;">' . esc_html( $email ) . '</td></tr>'
			. '<tr><td style="padding:8px 0;border-bottom:1px solid #e2e8f0;"><strong>' . esc_html__( 'Website', 'free-backlinks-generator' ) . '</strong></td><td style="padding:8px 0;border-bottom:1px solid #e2e8f0;">' . esc_html( $web ) . '</td></tr>'
			. '<tr><td style="padding:8px 0;border-bottom:1px solid #e2e8f0;"><strong>' . esc_html__( 'Reach band', 'free-backlinks-generator' ) . '</strong></td><td style="padding:8px 0;border-bottom:1px solid #e2e8f0;">' . esc_html( $aud ) . '</td></tr>'
			. '<tr><td style="padding:8px 0;"><strong>' . esc_html__( 'Niche', 'free-backlinks-generator' ) . '</strong></td><td style="padding:8px 0;">' . esc_html( $niche ) . '</td></tr></table>'
			. '<p style="margin-top:20px;"><strong>' . esc_html__( 'Promotion plan', 'free-backlinks-generator' ) . '</strong></p><p style="white-space:pre-wrap;color:#475569;">' . esc_html( $plan ) . '</p>';
		$html = fbg_email_html_layout(
			__( 'New partner application', 'free-backlinks-generator' ),
			__( 'Affiliate application received', 'free-backlinks-generator' ),
			$rows,
			__( 'WordPress admin', 'free-backlinks-generator' ),
			admin_url(),
			__( 'Recent applications are stored in the option fbg_affiliate_leads (up to 100 entries).', 'free-backlinks-generator' )
		);
		fbg_wp_mail_html( $admin, $subj, $html );
	}

	wp_send_json_success();
}
add_action( 'wp_ajax_nopriv_fbg_affiliate_apply', 'fbg_handle_affiliate_apply' );
add_action( 'wp_ajax_fbg_affiliate_apply', 'fbg_handle_affiliate_apply' );

/**
 * Sidebar contact form on single guest post (public).
 */
function fbg_handle_sidebar_contact() {
	check_ajax_referer( 'fbg_sidebar_contact_nonce', 'nonce' );

	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '0';
	$key = 'fbg_sidebar_contact_' . md5( $ip );
	$hit = (int) get_transient( $key );
	if ( $hit >= 8 ) {
		wp_send_json_error( array( 'message' => __( 'Too many messages sent. Please try again later.', 'free-backlinks-generator' ) ) );
	}
	set_transient( $key, $hit + 1, HOUR_IN_SECONDS );

	$name = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$mail = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$msg  = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

	if ( strlen( $name ) < 2 || ! is_email( $mail ) || strlen( $msg ) < 10 ) {
		wp_send_json_error( array( 'message' => __( 'Please fill in all fields with a real email and a message of at least 10 characters.', 'free-backlinks-generator' ) ) );
	}

	$admin = get_option( 'admin_email' );
	if ( ! is_email( $admin ) ) {
		wp_send_json_error( array( 'message' => __( 'Message could not be sent. Please try the Contact page.', 'free-backlinks-generator' ) ) );
	}

	$subj = sprintf(
		/* translators: %s site name */
		__( '[%s] Sidebar contact form', 'free-backlinks-generator' ),
		get_bloginfo( 'name' )
	);
	$body = sprintf(
		"Name: %s\nEmail: %s\n\n%s\n",
		$name,
		$mail,
		$msg
	);
	$headers = array( 'Content-Type: text/plain; charset=UTF-8', 'Reply-To: ' . $mail );
	wp_mail( $admin, $subj, $body, $headers );

	wp_send_json_success();
}
add_action( 'wp_ajax_nopriv_fbg_sidebar_contact', 'fbg_handle_sidebar_contact' );
add_action( 'wp_ajax_fbg_sidebar_contact', 'fbg_handle_sidebar_contact' );
