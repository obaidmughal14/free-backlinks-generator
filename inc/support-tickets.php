<?php
/**
 * Support tickets (contact page + admin).
 *
 * @package Free_Backlinks_Generator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return string
 */
function fbg_support_tickets_table() {
	global $wpdb;
	return $wpdb->prefix . 'fbg_support_tickets';
}

/**
 * @return string
 */
function fbg_support_ticket_replies_table() {
	global $wpdb;
	return $wpdb->prefix . 'fbg_support_ticket_replies';
}

/**
 * Create ticket tables.
 */
function fbg_create_support_ticket_tables() {
	global $wpdb;
	$charset = $wpdb->get_charset_collate();
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	$tickets = fbg_support_tickets_table();
	$replies = fbg_support_ticket_replies_table();

	$sql1 = "CREATE TABLE {$tickets} (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		public_id varchar(24) NOT NULL DEFAULT '',
		user_id bigint(20) unsigned NOT NULL DEFAULT 0,
		requester_email varchar(190) NOT NULL DEFAULT '',
		requester_name varchar(190) NOT NULL DEFAULT '',
		subject varchar(255) NOT NULL DEFAULT '',
		category varchar(50) NOT NULL DEFAULT 'general',
		priority varchar(20) NOT NULL DEFAULT 'normal',
		status varchar(20) NOT NULL DEFAULT 'open',
		body longtext NOT NULL,
		admin_notes longtext,
		assigned_to bigint(20) unsigned DEFAULT NULL,
		created_at datetime NOT NULL,
		updated_at datetime NOT NULL,
		PRIMARY KEY  (id),
		UNIQUE KEY public_id (public_id),
		KEY status_created (status, created_at),
		KEY requester_email (requester_email)
	) {$charset};";

	$sql2 = "CREATE TABLE {$replies} (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		ticket_id bigint(20) unsigned NOT NULL,
		author_user_id bigint(20) unsigned NOT NULL DEFAULT 0,
		is_staff tinyint(1) NOT NULL DEFAULT 1,
		body text NOT NULL,
		created_at datetime NOT NULL,
		PRIMARY KEY  (id),
		KEY ticket_id (ticket_id)
	) {$charset};";

	dbDelta( $sql1 );
	dbDelta( $sql2 );
}

/**
 * Create ticket from POST (AJAX).
 */
function fbg_ajax_support_ticket_create() {
	check_ajax_referer( 'fbg_support_ticket', 'nonce' );

	$ip_key = 'fbg_ticket_create_' . md5( isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'x' );
	$hit    = (int) get_transient( $ip_key );
	if ( $hit >= 8 ) {
		wp_send_json_error( array( 'message' => __( 'Too many tickets from this network. Try again later.', 'free-backlinks-generator' ) ) );
	}
	set_transient( $ip_key, $hit + 1, HOUR_IN_SECONDS );

	$name    = isset( $_POST['requester_name'] ) ? sanitize_text_field( wp_unslash( $_POST['requester_name'] ) ) : '';
	$email   = isset( $_POST['requester_email'] ) ? sanitize_email( wp_unslash( $_POST['requester_email'] ) ) : '';
	$subject = isset( $_POST['subject'] ) ? sanitize_text_field( wp_unslash( $_POST['subject'] ) ) : '';
	$body    = isset( $_POST['body'] ) ? sanitize_textarea_field( wp_unslash( $_POST['body'] ) ) : '';
	$cat     = isset( $_POST['category'] ) ? sanitize_key( wp_unslash( $_POST['category'] ) ) : 'general';
	$pri     = isset( $_POST['priority'] ) ? sanitize_key( wp_unslash( $_POST['priority'] ) ) : 'normal';

	$allowed_cat = array( 'general', 'account', 'billing', 'technical', 'moderation', 'partnership', 'other' );
	$allowed_pri = array( 'low', 'normal', 'high', 'urgent' );
	if ( ! in_array( $cat, $allowed_cat, true ) ) {
		$cat = 'general';
	}
	if ( ! in_array( $pri, $allowed_pri, true ) ) {
		$pri = 'normal';
	}

	if ( is_user_logged_in() ) {
		$u = wp_get_current_user();
		if ( strlen( $name ) < 2 ) {
			$name = $u->display_name;
		}
		if ( ! is_email( $email ) ) {
			$email = $u->user_email;
		}
	}

	if ( strlen( $name ) < 2 || ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => __( 'Please enter your name and a valid email.', 'free-backlinks-generator' ) ) );
	}
	if ( strlen( $subject ) < 4 || strlen( $body ) < 20 ) {
		wp_send_json_error( array( 'message' => __( 'Subject and description are required (description at least 20 characters).', 'free-backlinks-generator' ) ) );
	}

	$uid = is_user_logged_in() ? get_current_user_id() : 0;
	$now = current_time( 'mysql' );
	global $wpdb;
	$table     = fbg_support_tickets_table();
	$tmp_public = 'tmp-' . wp_generate_password( 12, false, false );

	$wpdb->insert(
		$table,
		array(
			'public_id'       => $tmp_public,
			'user_id'         => (int) $uid,
			'requester_email' => $email,
			'requester_name'  => substr( $name, 0, 190 ),
			'subject'         => substr( $subject, 0, 255 ),
			'category'        => $cat,
			'priority'        => $pri,
			'status'          => 'open',
			'body'            => $body,
			'admin_notes'     => '',
			'created_at'      => $now,
			'updated_at'      => $now,
		),
		array( '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
	);
	$ticket_db_id = (int) $wpdb->insert_id;
	if ( ! $ticket_db_id ) {
		wp_send_json_error( array( 'message' => __( 'Could not create ticket.', 'free-backlinks-generator' ) ) );
	}
	$public_id = 'FBG-' . $ticket_db_id;
	$wpdb->update(
		$table,
		array( 'public_id' => $public_id ),
		array( 'id' => $ticket_db_id ),
		array( '%s' ),
		array( '%d' )
	);

	$admin = get_option( 'admin_email' );
	if ( is_email( $admin ) && function_exists( 'fbg_wp_mail_html' ) && function_exists( 'fbg_email_html_layout' ) ) {
		$url  = admin_url( 'admin.php?page=fbg-support-tickets&ticket=' . $ticket_db_id );
		$subj = sprintf( __( '[%s] New support ticket %s', 'free-backlinks-generator' ), get_bloginfo( 'name' ), $public_id );
		$html = fbg_email_html_layout(
			__( 'New ticket submitted', 'free-backlinks-generator' ),
			$public_id . ' — ' . esc_html( $subject ),
			'<p><strong>' . esc_html__( 'From', 'free-backlinks-generator' ) . ':</strong> ' . esc_html( $name ) . ' &lt;' . esc_html( $email ) . '&gt;<br>'
			. '<strong>' . esc_html__( 'Category', 'free-backlinks-generator' ) . ':</strong> ' . esc_html( $cat ) . '<br>'
			. '<strong>' . esc_html__( 'Priority', 'free-backlinks-generator' ) . ':</strong> ' . esc_html( $pri ) . '</p>'
			. '<p style="white-space:pre-wrap;">' . esc_html( $body ) . '</p>',
			__( 'View ticket', 'free-backlinks-generator' ),
			$url,
			null
		);
		fbg_wp_mail_html( $admin, $subj, $html );
	}

	$confirm_subj = sprintf( __( '[%s] Ticket %s received', 'free-backlinks-generator' ), get_bloginfo( 'name' ), $public_id );
	$confirm_body = fbg_email_html_layout(
		__( 'We received your request', 'free-backlinks-generator' ),
		sprintf( /* translators: %s ticket id */ __( 'Ticket %s', 'free-backlinks-generator' ), esc_html( $public_id ) ),
		'<p>' . esc_html__( 'Thanks for contacting us. Our team will review your ticket and reply by email.', 'free-backlinks-generator' ) . '</p>'
		. '<p><strong>' . esc_html__( 'Subject', 'free-backlinks-generator' ) . ':</strong> ' . esc_html( $subject ) . '</p>'
		. '<p style="white-space:pre-wrap;color:#475569;">' . esc_html( wp_trim_words( $body, 80 ) ) . '</p>',
		'',
		'',
		null
	);
	if ( function_exists( 'fbg_wp_mail_html' ) ) {
		fbg_wp_mail_html( $email, $confirm_subj, $confirm_body );
	}

	wp_send_json_success(
		array(
			'public_id' => $public_id,
			'message'   => __( 'Ticket created successfully. Check your email for confirmation.', 'free-backlinks-generator' ),
		)
	);
}
add_action( 'wp_ajax_nopriv_fbg_support_ticket_create', 'fbg_ajax_support_ticket_create' );
add_action( 'wp_ajax_fbg_support_ticket_create', 'fbg_ajax_support_ticket_create' );

/**
 * Fetch single ticket by id.
 *
 * @param int $id DB id.
 * @return object|null
 */
function fbg_support_ticket_get( $id ) {
	global $wpdb;
	return $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . fbg_support_tickets_table() . ' WHERE id = %d', (int) $id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
}

/**
 * Ticket replies.
 *
 * @param int $ticket_id Ticket DB id.
 * @return array
 */
function fbg_support_ticket_replies( $ticket_id ) {
	global $wpdb;
	return $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . fbg_support_ticket_replies_table() . ' WHERE ticket_id = %d ORDER BY id ASC', (int) $ticket_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
}

/**
 * Admin: add staff reply + email requester.
 */
function fbg_ajax_support_ticket_reply() {
	check_ajax_referer( 'fbg_support_ticket_admin', 'nonce' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => __( 'Not allowed.', 'free-backlinks-generator' ) ) );
	}
	$ticket_id = isset( $_POST['ticket_id'] ) ? absint( $_POST['ticket_id'] ) : 0;
	$body       = isset( $_POST['reply'] ) ? sanitize_textarea_field( wp_unslash( $_POST['reply'] ) ) : '';
	if ( $ticket_id < 1 || strlen( $body ) < 5 ) {
		wp_send_json_error( array( 'message' => __( 'Enter a reply (at least 5 characters).', 'free-backlinks-generator' ) ) );
	}
	$ticket = fbg_support_ticket_get( $ticket_id );
	if ( ! $ticket ) {
		wp_send_json_error();
	}
	$uid = get_current_user_id();
	global $wpdb;
	$now = current_time( 'mysql' );
	$wpdb->insert(
		fbg_support_ticket_replies_table(),
		array(
			'ticket_id'       => $ticket_id,
			'author_user_id'  => $uid,
			'is_staff'        => 1,
			'body'            => $body,
			'created_at'      => $now,
		),
		array( '%d', '%d', '%d', '%s', '%s' )
	);
	$wpdb->update(
		fbg_support_tickets_table(),
		array(
			'status'     => 'pending',
			'updated_at' => $now,
		),
		array( 'id' => $ticket_id ),
		array( '%s', '%s' ),
		array( '%d' )
	);
	$to = $ticket->requester_email;
	if ( is_email( $to ) && function_exists( 'fbg_wp_mail_html' ) && function_exists( 'fbg_email_html_layout' ) ) {
		$subj = sprintf(
			/* translators: 1: ticket id, 2: subject */
			__( '[%1$s] Re: %2$s — %3$s', 'free-backlinks-generator' ),
			get_bloginfo( 'name' ),
			$ticket->public_id,
			$ticket->subject
		);
		$html = fbg_email_html_layout(
			__( 'Support replied to your ticket', 'free-backlinks-generator' ),
			__( 'New reply', 'free-backlinks-generator' ),
			'<p style="white-space:pre-wrap;">' . esc_html( $body ) . '</p>',
			__( 'Visit website', 'free-backlinks-generator' ),
			home_url( '/contact/' ),
			null
		);
		fbg_wp_mail_html( $to, $subj, $html );
	}
	wp_send_json_success( array( 'message' => __( 'Reply sent.', 'free-backlinks-generator' ) ) );
}
add_action( 'wp_ajax_fbg_support_ticket_reply', 'fbg_ajax_support_ticket_reply' );

/**
 * Admin: update ticket status.
 */
function fbg_ajax_support_ticket_update() {
	check_ajax_referer( 'fbg_support_ticket_admin', 'nonce' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error();
	}
	$ticket_id = isset( $_POST['ticket_id'] ) ? absint( $_POST['ticket_id'] ) : 0;
	$status    = isset( $_POST['status'] ) ? sanitize_key( wp_unslash( $_POST['status'] ) ) : '';
	$allowed   = array( 'open', 'pending', 'resolved', 'closed' );
	if ( $ticket_id < 1 || ! in_array( $status, $allowed, true ) ) {
		wp_send_json_error();
	}
	global $wpdb;
	$wpdb->update(
		fbg_support_tickets_table(),
		array(
			'status'     => $status,
			'updated_at' => current_time( 'mysql' ),
		),
		array( 'id' => $ticket_id ),
		array( '%s', '%s' ),
		array( '%d' )
	);
	wp_send_json_success();
}
add_action( 'wp_ajax_fbg_support_ticket_update', 'fbg_ajax_support_ticket_update' );

/**
 * Admin: save internal notes only.
 */
function fbg_ajax_support_ticket_notes() {
	check_ajax_referer( 'fbg_support_ticket_admin', 'nonce' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error();
	}
	$ticket_id = isset( $_POST['ticket_id'] ) ? absint( $_POST['ticket_id'] ) : 0;
	$notes     = isset( $_POST['admin_notes'] ) ? sanitize_textarea_field( wp_unslash( $_POST['admin_notes'] ) ) : '';
	if ( $ticket_id < 1 ) {
		wp_send_json_error();
	}
	global $wpdb;
	$wpdb->update(
		fbg_support_tickets_table(),
		array(
			'admin_notes' => $notes,
			'updated_at'  => current_time( 'mysql' ),
		),
		array( 'id' => $ticket_id ),
		array( '%s', '%s' ),
		array( '%d' )
	);
	wp_send_json_success();
}
add_action( 'wp_ajax_fbg_support_ticket_notes', 'fbg_ajax_support_ticket_notes' );
