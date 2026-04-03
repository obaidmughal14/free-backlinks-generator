<?php
/**
 * Live chat: sessions, messages, agent presence (polling), offline queue.
 *
 * @package Free_Backlinks_Generator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Chat sessions table.
 *
 * @return string
 */
function fbg_chat_sessions_table() {
	global $wpdb;
	return $wpdb->prefix . 'fbg_chat_sessions';
}

/**
 * Chat messages table.
 *
 * @return string
 */
function fbg_chat_messages_table() {
	global $wpdb;
	return $wpdb->prefix . 'fbg_chat_messages';
}

/**
 * Create chat tables.
 */
function fbg_create_chat_tables() {
	global $wpdb;
	$charset = $wpdb->get_charset_collate();
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	$sessions = fbg_chat_sessions_table();
	$messages = fbg_chat_messages_table();

	$sql1 = "CREATE TABLE {$sessions} (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		user_id bigint(20) unsigned NOT NULL DEFAULT 0,
		guest_key varchar(64) NOT NULL DEFAULT '',
		visitor_name varchar(190) NOT NULL DEFAULT '',
		visitor_email varchar(190) NOT NULL DEFAULT '',
		assigned_agent_id bigint(20) unsigned DEFAULT NULL,
		status varchar(20) NOT NULL DEFAULT 'open',
		created_at datetime NOT NULL,
		updated_at datetime NOT NULL,
		PRIMARY KEY  (id),
		UNIQUE KEY guest_key (guest_key),
		KEY status_updated (status, updated_at),
		KEY assigned (assigned_agent_id),
		KEY user_id (user_id)
	) {$charset};";

	$sql2 = "CREATE TABLE {$messages} (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		session_id bigint(20) unsigned NOT NULL,
		sender varchar(20) NOT NULL,
		sender_user_id bigint(20) unsigned NOT NULL DEFAULT 0,
		body text NOT NULL,
		created_at datetime NOT NULL,
		PRIMARY KEY  (id),
		KEY session_msg (session_id, id)
	) {$charset};";

	dbDelta( $sql1 );
	dbDelta( $sql2 );
}

/**
 * Whether user can act as live chat agent.
 *
 * @param int $user_id User ID.
 * @return bool
 */
function fbg_user_can_answer_live_chat( $user_id ) {
	$user = get_userdata( $user_id );
	if ( ! $user ) {
		return false;
	}
	if ( user_can( $user, 'manage_options' ) ) {
		return true;
	}
	return user_can( $user, 'fbg_live_chat_agent' );
}

/**
 * Record agent heartbeat (considers agent online if pinged in last 60s).
 *
 * @param int $user_id Agent user ID.
 */
function fbg_chat_agent_heartbeat_store( $user_id ) {
	$key  = 'fbg_chat_agents_ts';
	$data = get_option( $key, array() );
	if ( ! is_array( $data ) ) {
		$data = array();
	}
	$now = time();
	foreach ( $data as $uid => $ts ) {
		if ( $now - (int) $ts > 120 ) {
			unset( $data[ $uid ] );
		}
	}
	$data[ (string) (int) $user_id ] = $now;
	update_option( $key, $data, false );
}

/**
 * Any support agent currently online?
 *
 * @return bool
 */
function fbg_chat_any_agent_online() {
	$data = get_option( 'fbg_chat_agents_ts', array() );
	if ( ! is_array( $data ) || empty( $data ) ) {
		return false;
	}
	$now = time();
	foreach ( $data as $uid => $ts ) {
		if ( $now - (int) $ts <= 60 && fbg_user_can_answer_live_chat( (int) $uid ) ) {
			return true;
		}
	}
	return false;
}

/**
 * Get or create chat session for visitor.
 *
 * @param string $guest_key Stable key from client.
 * @param string $name      Visitor name.
 * @param string $email     Visitor email.
 * @return array{session:object|null, created:bool}|WP_Error
 */
function fbg_chat_get_or_create_session( $guest_key, $name = '', $email = '' ) {
	global $wpdb;
	$table = fbg_chat_sessions_table();
	$key = preg_replace( '/[^a-zA-Z0-9_-]/', '', $guest_key );
	if ( strlen( $key ) < 2 ) {
		return new WP_Error( 'bad_key', __( 'Invalid session.', 'free-backlinks-generator' ) );
	}

	$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE guest_key = %s LIMIT 1", $key ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	if ( $row ) {
		return array( 'session' => $row, 'created' => false );
	}

	$uid   = is_user_logged_in() ? get_current_user_id() : 0;
	$u     = $uid ? wp_get_current_user() : null;
	$name  = $name ? sanitize_text_field( $name ) : ( $u ? $u->display_name : '' );
	$email = $email ? sanitize_email( $email ) : ( $u ? $u->user_email : '' );

	$now = current_time( 'mysql' );
	$wpdb->insert(
		$table,
		array(
			'user_id'       => (int) $uid,
			'guest_key'     => $key,
			'visitor_name'  => substr( $name, 0, 190 ),
			'visitor_email' => substr( $email, 0, 190 ),
			'status'        => 'open',
			'created_at'    => $now,
			'updated_at'    => $now,
		),
		array( '%d', '%s', '%s', '%s', '%s', '%s', '%s' )
	);
	if ( ! $wpdb->insert_id ) {
		return new WP_Error( 'db', __( 'Could not start chat.', 'free-backlinks-generator' ) );
	}

	$session = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $wpdb->insert_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared

	if ( ! fbg_chat_any_agent_online() && function_exists( 'fbg_wp_mail_html' ) && function_exists( 'fbg_email_html_layout' ) ) {
		$admin = get_option( 'admin_email' );
		if ( is_email( $admin ) ) {
			$subj = sprintf( __( '[%s] New live chat (offline queue)', 'free-backlinks-generator' ), get_bloginfo( 'name' ) );
			$url  = admin_url( 'admin.php?page=fbg-support-chat&tab=queue' );
			$html = fbg_email_html_layout(
				__( 'Visitor started chat while no agent was online', 'free-backlinks-generator' ),
				__( 'Chat queued', 'free-backlinks-generator' ),
				'<p>' . esc_html__( 'A visitor opened live chat. No agent was marked online — review the queue in Support → Live chat.', 'free-backlinks-generator' ) . '</p>'
				. '<p><strong>' . esc_html__( 'Name', 'free-backlinks-generator' ) . ':</strong> ' . esc_html( $name ?: '—' ) . '<br>'
				. '<strong>' . esc_html__( 'Email', 'free-backlinks-generator' ) . ':</strong> ' . esc_html( $email ?: '—' ) . '</p>',
				__( 'Open chat queue', 'free-backlinks-generator' ),
				$url,
				null
			);
			fbg_wp_mail_html( $admin, $subj, $html );
		}
	}

	return array( 'session' => $session, 'created' => true );
}

/**
 * Fetch messages for session after message id.
 *
 * @param int $session_id Session ID.
 * @param int $after_id   Last seen message id.
 * @return array<int, object>
 */
function fbg_chat_messages_since( $session_id, $after_id = 0 ) {
	global $wpdb;
	$table = fbg_chat_messages_table();
	$sid   = (int) $session_id;
	$after = (int) $after_id;
	return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} WHERE session_id = %d AND id > %d ORDER BY id ASC", $sid, $after ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
}

/**
 * Insert chat message.
 *
 * @param int    $session_id Session ID.
 * @param string $sender     visitor|agent|system.
 * @param int    $sender_uid WordPress user id for agent.
 * @param string $body       Message body.
 * @return int|false Message id.
 */
function fbg_chat_insert_message( $session_id, $sender, $sender_uid, $body ) {
	global $wpdb;
	$table = fbg_chat_messages_table();
	$body  = wp_kses_post( $body );
	if ( '' === trim( wp_strip_all_tags( $body ) ) ) {
		return false;
	}
	$now = current_time( 'mysql' );
	$wpdb->insert(
		$table,
		array(
			'session_id'      => (int) $session_id,
			'sender'          => sanitize_key( $sender ),
			'sender_user_id'  => (int) $sender_uid,
			'body'            => $body,
			'created_at'      => $now,
		),
		array( '%d', '%s', '%d', '%s', '%s' )
	);
	$mid = (int) $wpdb->insert_id;
	if ( $mid ) {
		$wpdb->update(
			fbg_chat_sessions_table(),
			array( 'updated_at' => $now ),
			array( 'id' => (int) $session_id ),
			array( '%s' ),
			array( '%d' )
		);
	}
	return $mid ? $mid : false;
}

/**
 * Public: init or resume session.
 */
function fbg_ajax_chat_init() {
	check_ajax_referer( 'fbg_chat_public', 'nonce' );
	$guest_key = isset( $_POST['guest_key'] ) ? sanitize_text_field( wp_unslash( $_POST['guest_key'] ) ) : '';
	$name      = isset( $_POST['visitor_name'] ) ? sanitize_text_field( wp_unslash( $_POST['visitor_name'] ) ) : '';
	$email     = isset( $_POST['visitor_email'] ) ? sanitize_email( wp_unslash( $_POST['visitor_email'] ) ) : '';
	$res       = fbg_chat_get_or_create_session( $guest_key, $name, $email );
	if ( is_wp_error( $res ) ) {
		wp_send_json_error( array( 'message' => $res->get_error_message() ) );
	}
	/** @var object $session */
	$session = $res['session'];
	$msgs    = fbg_chat_messages_since( (int) $session->id, 0 );
	wp_send_json_success(
		array(
			'session_id'     => (int) $session->id,
			'guest_key'      => $session->guest_key,
			'status'         => $session->status,
			'agent_online'   => fbg_chat_any_agent_online(),
			'assigned_agent' => (int) $session->assigned_agent_id,
			'messages'       => array_map( 'fbg_chat_message_to_array', $msgs ),
		)
	);
}
add_action( 'wp_ajax_nopriv_fbg_chat_init', 'fbg_ajax_chat_init' );
add_action( 'wp_ajax_fbg_chat_init', 'fbg_ajax_chat_init' );

/**
 * @param object $m Message row.
 * @return array<string, mixed>
 */
function fbg_chat_message_to_array( $m ) {
	return array(
		'id'             => (int) $m->id,
		'sender'         => $m->sender,
		'sender_user_id' => (int) $m->sender_user_id,
		'body'           => $m->body,
		'created_at'     => $m->created_at,
	);
}

/**
 * Public: poll new messages.
 */
function fbg_ajax_chat_poll() {
	check_ajax_referer( 'fbg_chat_public', 'nonce' );
	$session_id = isset( $_POST['session_id'] ) ? absint( $_POST['session_id'] ) : 0;
	$guest_key  = isset( $_POST['guest_key'] ) ? sanitize_text_field( wp_unslash( $_POST['guest_key'] ) ) : '';
	$after      = isset( $_POST['after_id'] ) ? absint( $_POST['after_id'] ) : 0;
	if ( $session_id < 1 || strlen( $guest_key ) < 2 ) {
		wp_send_json_error( array( 'message' => __( 'Invalid session.', 'free-backlinks-generator' ) ) );
	}
	global $wpdb;
	$table = fbg_chat_sessions_table();
	$s     = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d AND guest_key = %s", $session_id, $guest_key ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	if ( ! $s ) {
		wp_send_json_error( array( 'message' => __( 'Session not found.', 'free-backlinks-generator' ) ) );
	}
	$msgs = fbg_chat_messages_since( $session_id, $after );
	wp_send_json_success(
		array(
			'messages'       => array_map( 'fbg_chat_message_to_array', $msgs ),
			'status'         => $s->status,
			'assigned_agent' => (int) $s->assigned_agent_id,
			'agent_online'   => fbg_chat_any_agent_online(),
		)
	);
}
add_action( 'wp_ajax_nopriv_fbg_chat_poll', 'fbg_ajax_chat_poll' );
add_action( 'wp_ajax_fbg_chat_poll', 'fbg_ajax_chat_poll' );

/**
 * Public: send visitor message.
 */
function fbg_ajax_chat_send_visitor() {
	check_ajax_referer( 'fbg_chat_public', 'nonce' );
	$ip_key = 'fbg_chat_send_' . md5( isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'x' );
	$hit    = (int) get_transient( $ip_key );
	if ( $hit > 40 ) {
		wp_send_json_error( array( 'message' => __( 'Slow down — too many messages.', 'free-backlinks-generator' ) ) );
	}
	set_transient( $ip_key, $hit + 1, HOUR_IN_SECONDS );

	$session_id = isset( $_POST['session_id'] ) ? absint( $_POST['session_id'] ) : 0;
	$guest_key  = isset( $_POST['guest_key'] ) ? sanitize_text_field( wp_unslash( $_POST['guest_key'] ) ) : '';
	$body       = isset( $_POST['message'] ) ? wp_kses_post( wp_unslash( $_POST['message'] ) ) : '';
	if ( $session_id < 1 || strlen( $guest_key ) < 2 || strlen( wp_strip_all_tags( $body ) ) < 1 ) {
		wp_send_json_error( array( 'message' => __( 'Message cannot be empty.', 'free-backlinks-generator' ) ) );
	}
	global $wpdb;
	$table = fbg_chat_sessions_table();
	$s     = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d AND guest_key = %s", $session_id, $guest_key ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	if ( ! $s || 'closed' === $s->status ) {
		wp_send_json_error( array( 'message' => __( 'This chat is closed.', 'free-backlinks-generator' ) ) );
	}
	$mid = fbg_chat_insert_message( $session_id, 'visitor', is_user_logged_in() ? get_current_user_id() : 0, $body );
	if ( ! $mid ) {
		wp_send_json_error( array( 'message' => __( 'Could not send.', 'free-backlinks-generator' ) ) );
	}
	if ( ! $s->assigned_agent_id && ! fbg_chat_any_agent_online() ) {
		$admin = get_option( 'admin_email' );
		if ( is_email( $admin ) && function_exists( 'fbg_wp_mail_html' ) && function_exists( 'fbg_email_html_layout' ) ) {
			$subj = sprintf( __( '[%s] Live chat: new visitor message (queue)', 'free-backlinks-generator' ), get_bloginfo( 'name' ) );
			$url  = admin_url( 'admin.php?page=fbg-support-chat&session=' . (int) $session_id );
			$html = fbg_email_html_layout(
				__( 'Visitor sent a message', 'free-backlinks-generator' ),
				__( 'Chat update', 'free-backlinks-generator' ),
				'<p>' . esc_html( wp_trim_words( wp_strip_all_tags( $body ), 40 ) ) . '</p>',
				__( 'View in admin', 'free-backlinks-generator' ),
				$url,
				null
			);
			fbg_wp_mail_html( $admin, $subj, $html );
		}
	}
	$m = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . fbg_chat_messages_table() . ' WHERE id = %d', $mid ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	wp_send_json_success( array( 'message' => fbg_chat_message_to_array( $m ) ) );
}
add_action( 'wp_ajax_nopriv_fbg_chat_send_visitor', 'fbg_ajax_chat_send_visitor' );
add_action( 'wp_ajax_fbg_chat_send_visitor', 'fbg_ajax_chat_send_visitor' );

/**
 * Agent: heartbeat.
 */
function fbg_ajax_chat_agent_pulse() {
	check_ajax_referer( 'fbg_chat_agent', 'nonce' );
	$uid = get_current_user_id();
	if ( ! $uid || ! fbg_user_can_answer_live_chat( $uid ) ) {
		wp_send_json_error( array( 'message' => __( 'Not allowed.', 'free-backlinks-generator' ) ) );
	}
	fbg_chat_agent_heartbeat_store( $uid );
	wp_send_json_success( array( 'online' => true ) );
}
add_action( 'wp_ajax_fbg_chat_agent_pulse', 'fbg_ajax_chat_agent_pulse' );

/**
 * Agent: list sessions.
 */
function fbg_ajax_chat_agent_sessions() {
	check_ajax_referer( 'fbg_chat_agent', 'nonce' );
	$uid = get_current_user_id();
	if ( ! $uid || ! fbg_user_can_answer_live_chat( $uid ) ) {
		wp_send_json_error( array( 'message' => __( 'Not allowed.', 'free-backlinks-generator' ) ) );
	}
	global $wpdb;
	$table = fbg_chat_sessions_table();
	$tab   = isset( $_POST['list'] ) ? sanitize_key( wp_unslash( $_POST['list'] ) ) : 'active';
	if ( 'queue' === $tab ) {
		$rows = $wpdb->get_results( "SELECT * FROM {$table} WHERE status != 'closed' AND (assigned_agent_id IS NULL OR assigned_agent_id = 0) ORDER BY updated_at DESC LIMIT 100" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	} elseif ( 'mine' === $tab ) {
		$rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} WHERE assigned_agent_id = %d AND status != 'closed' ORDER BY updated_at DESC LIMIT 50", $uid ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	} else {
		$rows = $wpdb->get_results( "SELECT * FROM {$table} WHERE status != 'closed' ORDER BY updated_at DESC LIMIT 100" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}
	$out = array();
	foreach ( $rows as $r ) {
		$out[] = fbg_chat_session_to_array( $r );
	}
	wp_send_json_success( array( 'sessions' => $out ) );
}
add_action( 'wp_ajax_fbg_chat_agent_sessions', 'fbg_ajax_chat_agent_sessions' );

/**
 * @param object $r Session row.
 * @return array<string, mixed>
 */
function fbg_chat_session_to_array( $r ) {
	return array(
		'id'                 => (int) $r->id,
		'user_id'            => (int) $r->user_id,
		'guest_key'          => $r->guest_key,
		'visitor_name'       => $r->visitor_name,
		'visitor_email'      => $r->visitor_email,
		'assigned_agent_id'  => (int) $r->assigned_agent_id,
		'status'             => $r->status,
		'created_at'         => $r->created_at,
		'updated_at'         => $r->updated_at,
	);
}

/**
 * Agent: claim session.
 */
function fbg_ajax_chat_agent_claim() {
	check_ajax_referer( 'fbg_chat_agent', 'nonce' );
	$uid = get_current_user_id();
	if ( ! $uid || ! fbg_user_can_answer_live_chat( $uid ) ) {
		wp_send_json_error( array( 'message' => __( 'Not allowed.', 'free-backlinks-generator' ) ) );
	}
	$sid = isset( $_POST['session_id'] ) ? absint( $_POST['session_id'] ) : 0;
	if ( $sid < 1 ) {
		wp_send_json_error( array( 'message' => __( 'Invalid session.', 'free-backlinks-generator' ) ) );
	}
	global $wpdb;
	$table = fbg_chat_sessions_table();
	$s     = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $sid ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	if ( ! $s || 'closed' === $s->status ) {
		wp_send_json_error( array( 'message' => __( 'Session unavailable.', 'free-backlinks-generator' ) ) );
	}
	$wpdb->update(
		$table,
		array(
			'assigned_agent_id' => $uid,
			'status'            => 'active',
			'updated_at'        => current_time( 'mysql' ),
		),
		array( 'id' => $sid ),
		array( '%d', '%s', '%s' ),
		array( '%d' )
	);
	$agent = wp_get_current_user();
	$name  = $agent ? $agent->display_name : __( 'Support', 'free-backlinks-generator' );
	fbg_chat_insert_message( $sid, 'system', 0, sprintf( /* translators: %s agent name */ __( '%s joined the conversation.', 'free-backlinks-generator' ), esc_html( $name ) ) );
	wp_send_json_success( array( 'ok' => true ) );
}
add_action( 'wp_ajax_fbg_chat_agent_claim', 'fbg_ajax_chat_agent_claim' );

/**
 * Agent: poll messages for session (no guest_key required).
 */
function fbg_ajax_chat_agent_poll() {
	check_ajax_referer( 'fbg_chat_agent', 'nonce' );
	$uid = get_current_user_id();
	if ( ! $uid || ! fbg_user_can_answer_live_chat( $uid ) ) {
		wp_send_json_error( array( 'message' => __( 'Not allowed.', 'free-backlinks-generator' ) ) );
	}
	$sid   = isset( $_POST['session_id'] ) ? absint( $_POST['session_id'] ) : 0;
	$after = isset( $_POST['after_id'] ) ? absint( $_POST['after_id'] ) : 0;
	if ( $sid < 1 ) {
		wp_send_json_error( array( 'message' => __( 'Invalid session.', 'free-backlinks-generator' ) ) );
	}
	global $wpdb;
	$table = fbg_chat_sessions_table();
	$s     = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $sid ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	if ( ! $s ) {
		wp_send_json_error( array( 'message' => __( 'Not found.', 'free-backlinks-generator' ) ) );
	}
	if ( (int) $s->assigned_agent_id && (int) $s->assigned_agent_id !== $uid && ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => __( 'Another agent is handling this chat.', 'free-backlinks-generator' ) ) );
	}
	$msgs = fbg_chat_messages_since( $sid, $after );
	wp_send_json_success(
		array(
			'messages' => array_map( 'fbg_chat_message_to_array', $msgs ),
			'session'  => fbg_chat_session_to_array( $s ),
		)
	);
}
add_action( 'wp_ajax_fbg_chat_agent_poll', 'fbg_ajax_chat_agent_poll' );

/**
 * Agent: send reply.
 */
function fbg_ajax_chat_agent_send() {
	check_ajax_referer( 'fbg_chat_agent', 'nonce' );
	$uid = get_current_user_id();
	if ( ! $uid || ! fbg_user_can_answer_live_chat( $uid ) ) {
		wp_send_json_error( array( 'message' => __( 'Not allowed.', 'free-backlinks-generator' ) ) );
	}
	$sid  = isset( $_POST['session_id'] ) ? absint( $_POST['session_id'] ) : 0;
	$body = isset( $_POST['message'] ) ? wp_kses_post( wp_unslash( $_POST['message'] ) ) : '';
	if ( $sid < 1 || strlen( wp_strip_all_tags( $body ) ) < 1 ) {
		wp_send_json_error( array( 'message' => __( 'Message cannot be empty.', 'free-backlinks-generator' ) ) );
	}
	global $wpdb;
	$table = fbg_chat_sessions_table();
	$s     = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $sid ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	if ( ! $s || 'closed' === $s->status ) {
		wp_send_json_error( array( 'message' => __( 'Session closed.', 'free-backlinks-generator' ) ) );
	}
	if ( (int) $s->assigned_agent_id && (int) $s->assigned_agent_id !== $uid && ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => __( 'Claim this chat first.', 'free-backlinks-generator' ) ) );
	}
	if ( ! $s->assigned_agent_id ) {
		$wpdb->update(
			$table,
			array(
				'assigned_agent_id' => $uid,
				'status'            => 'active',
				'updated_at'        => current_time( 'mysql' ),
			),
			array( 'id' => $sid ),
			array( '%d', '%s', '%s' ),
			array( '%d' )
		);
	}
	$mid = fbg_chat_insert_message( $sid, 'agent', $uid, $body );
	if ( ! $mid ) {
		wp_send_json_error( array( 'message' => __( 'Could not send.', 'free-backlinks-generator' ) ) );
	}
	$m = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . fbg_chat_messages_table() . ' WHERE id = %d', $mid ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	wp_send_json_success( array( 'message' => fbg_chat_message_to_array( $m ) ) );
}
add_action( 'wp_ajax_fbg_chat_agent_send', 'fbg_ajax_chat_agent_send' );

/**
 * Agent: close session.
 */
function fbg_ajax_chat_agent_close() {
	check_ajax_referer( 'fbg_chat_agent', 'nonce' );
	$uid = get_current_user_id();
	if ( ! $uid || ! fbg_user_can_answer_live_chat( $uid ) ) {
		wp_send_json_error( array( 'message' => __( 'Not allowed.', 'free-backlinks-generator' ) ) );
	}
	$sid = isset( $_POST['session_id'] ) ? absint( $_POST['session_id'] ) : 0;
	if ( $sid < 1 ) {
		wp_send_json_error();
	}
	global $wpdb;
	$table = fbg_chat_sessions_table();
	$s     = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $sid ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	if ( ! $s ) {
		wp_send_json_error();
	}
	if ( (int) $s->assigned_agent_id && (int) $s->assigned_agent_id !== $uid && ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => __( 'Not your chat.', 'free-backlinks-generator' ) ) );
	}
	$wpdb->update(
		$table,
		array(
			'status'     => 'closed',
			'updated_at' => current_time( 'mysql' ),
		),
		array( 'id' => $sid ),
		array( '%s', '%s' ),
		array( '%d' )
	);
	fbg_chat_insert_message( $sid, 'system', 0, __( 'This conversation was closed by support. You can start a new chat anytime.', 'free-backlinks-generator' ) );
	wp_send_json_success();
}
add_action( 'wp_ajax_fbg_chat_agent_close', 'fbg_ajax_chat_agent_close' );

/**
 * Front: chat widget markup + localization.
 */
function fbg_chat_enqueue_front() {
	if ( is_admin() ) {
		return;
	}
	wp_enqueue_style( 'fbg-live-chat', FBG_URI . '/assets/css/fbg-live-chat.css', array(), FBG_VERSION );
	wp_enqueue_script( 'fbg-live-chat', FBG_URI . '/assets/js/fbg-live-chat.js', array(), FBG_VERSION, true );
	wp_localize_script(
		'fbg-live-chat',
		'fbgLiveChat',
		array(
			'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'fbg_chat_public' ),
			'agentUrl' => '', // not used on front.
			'strings'  => array(
				'title'       => __( 'Live support', 'free-backlinks-generator' ),
				'placeholder' => __( 'Type a message…', 'free-backlinks-generator' ),
				'send'        => __( 'Send', 'free-backlinks-generator' ),
				'start'       => __( 'Start chat', 'free-backlinks-generator' ),
				'offline'     => __( 'Team offline — your message is saved and we will follow up.', 'free-backlinks-generator' ),
				'error'       => __( 'Something went wrong. Please try again.', 'free-backlinks-generator' ),
				'connecting'  => __( 'Connecting…', 'free-backlinks-generator' ),
				'you'         => __( 'You', 'free-backlinks-generator' ),
				'support'     => __( 'Support', 'free-backlinks-generator' ),
				'system'      => __( 'Notice', 'free-backlinks-generator' ),
				'closed'      => __( 'This chat has ended.', 'free-backlinks-generator' ),
				'name'        => __( 'Your name', 'free-backlinks-generator' ),
				'email'       => __( 'Email (optional)', 'free-backlinks-generator' ),
				'begin'       => __( 'Begin', 'free-backlinks-generator' ),
			),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'fbg_chat_enqueue_front', 20 );

/**
 * Floating chat widget HTML.
 */
function fbg_chat_render_widget() {
	if ( is_admin() ) {
		return;
	}
	?>
	<div id="fbg-live-chat-root" class="fbg-live-chat" data-logged-in="<?php echo is_user_logged_in() ? '1' : '0'; ?>" data-user-key="<?php echo is_user_logged_in() ? esc_attr( 'u' . get_current_user_id() ) : ''; ?>">
		<button type="button" class="fbg-live-chat__toggle" id="fbg-live-chat-toggle" aria-expanded="false" aria-controls="fbg-live-chat-panel">
			<span class="fbg-live-chat__toggle-icon" aria-hidden="true">💬</span>
			<span class="screen-reader-text"><?php esc_html_e( 'Open live chat', 'free-backlinks-generator' ); ?></span>
		</button>
		<div class="fbg-live-chat__panel" id="fbg-live-chat-panel" role="dialog" aria-label="<?php esc_attr_e( 'Live support chat', 'free-backlinks-generator' ); ?>" hidden>
			<header class="fbg-live-chat__head">
				<strong class="fbg-live-chat__title"><?php esc_html_e( 'Live support', 'free-backlinks-generator' ); ?></strong>
				<button type="button" class="fbg-live-chat__close" id="fbg-live-chat-close" aria-label="<?php esc_attr_e( 'Close', 'free-backlinks-generator' ); ?>">×</button>
			</header>
			<div class="fbg-live-chat__preform" id="fbg-live-chat-preform">
				<p class="fbg-live-chat__hint"><?php esc_html_e( 'We typically reply in real time when agents are online. Otherwise your message is queued.', 'free-backlinks-generator' ); ?></p>
				<?php if ( ! is_user_logged_in() ) : ?>
					<label class="fbg-live-chat__label"><span><?php esc_html_e( 'Your name', 'free-backlinks-generator' ); ?></span>
						<input type="text" id="fbg-live-chat-name" class="fbg-live-chat__input" maxlength="120" autocomplete="name">
					</label>
					<label class="fbg-live-chat__label"><span><?php esc_html_e( 'Email', 'free-backlinks-generator' ); ?></span>
						<input type="email" id="fbg-live-chat-email" class="fbg-live-chat__input" maxlength="190" autocomplete="email">
					</label>
				<?php endif; ?>
				<button type="button" class="fbg-live-chat__btn fbg-live-chat__btn--primary" id="fbg-live-chat-start"><?php esc_html_e( 'Begin', 'free-backlinks-generator' ); ?></button>
			</div>
			<div class="fbg-live-chat__thread" id="fbg-live-chat-thread" hidden></div>
			<div class="fbg-live-chat__composer" id="fbg-live-chat-composer" hidden>
				<textarea id="fbg-live-chat-input" class="fbg-live-chat__textarea" rows="2" maxlength="4000" placeholder="<?php esc_attr_e( 'Type a message…', 'free-backlinks-generator' ); ?>"></textarea>
				<button type="button" class="fbg-live-chat__btn fbg-live-chat__btn--primary" id="fbg-live-chat-send"><?php esc_html_e( 'Send', 'free-backlinks-generator' ); ?></button>
			</div>
			<p class="fbg-live-chat__status" id="fbg-live-chat-status" role="status"></p>
		</div>
	</div>
	<?php
}
add_action( 'wp_footer', 'fbg_chat_render_widget', 5 );

/**
 * Register agent capability on activation.
 */
function fbg_support_register_capabilities() {
	$role = get_role( 'administrator' );
	if ( $role && ! $role->has_cap( 'fbg_live_chat_agent' ) ) {
		$role->add_cap( 'fbg_live_chat_agent' );
	}
	$role = get_role( 'editor' );
	if ( $role && ! $role->has_cap( 'fbg_live_chat_agent' ) ) {
		$role->add_cap( 'fbg_live_chat_agent' );
	}
}
