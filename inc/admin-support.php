<?php
/**
 * Admin: Live chat desk + support tickets.
 *
 * @package Free_Backlinks_Generator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Unassigned non-closed chat sessions.
 *
 * @return int
 */
function fbg_chat_queue_count() {
	global $wpdb;
	$table = fbg_chat_sessions_table();
	return (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table} WHERE status != 'closed' AND (assigned_agent_id IS NULL OR assigned_agent_id = 0)" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
}

/**
 * Open support tickets count.
 *
 * @return int
 */
function fbg_support_open_ticket_count() {
	global $wpdb;
	$table = fbg_support_tickets_table();
	return (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table} WHERE status IN ('open','pending')" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
}

/**
 * Register menus.
 */
function fbg_support_admin_menu() {
	add_menu_page(
		__( 'Support & Chat', 'free-backlinks-generator' ),
		__( 'Support', 'free-backlinks-generator' ),
		'fbg_live_chat_agent',
		'fbg-support-chat',
		'fbg_render_support_chat_page',
		'dashicons-format-chat',
		28
	);
	add_submenu_page(
		'fbg-support-chat',
		__( 'Live chat', 'free-backlinks-generator' ),
		__( 'Live chat', 'free-backlinks-generator' ),
		'fbg_live_chat_agent',
		'fbg-support-chat',
		'fbg_render_support_chat_page'
	);
	add_submenu_page(
		'fbg-support-chat',
		__( 'Support tickets', 'free-backlinks-generator' ),
		__( 'Tickets', 'free-backlinks-generator' ),
		'manage_options',
		'fbg-support-tickets',
		'fbg_render_support_tickets_page'
	);
}
add_action( 'admin_menu', 'fbg_support_admin_menu' );

/**
 * Menu badges.
 */
function fbg_support_admin_menu_badges() {
	global $menu;
	if ( ! is_array( $menu ) ) {
		return;
	}
	foreach ( $menu as $i => $item ) {
		if ( ! isset( $item[2] ) || 'fbg-support-chat' !== $item[2] ) {
			continue;
		}
		$parts = array();
		if ( function_exists( 'fbg_chat_queue_count' ) && current_user_can( 'fbg_live_chat_agent' ) ) {
			$q = fbg_chat_queue_count();
			if ( $q > 0 ) {
				$parts[] = '<span class="awaiting-mod" title="' . esc_attr__( 'Chats waiting for an agent', 'free-backlinks-generator' ) . '"><span class="pending-count">' . esc_html( (string) (int) $q ) . '</span></span>';
			}
		}
		if ( function_exists( 'fbg_support_open_ticket_count' ) && current_user_can( 'manage_options' ) ) {
			$t = fbg_support_open_ticket_count();
			if ( $t > 0 ) {
				$parts[] = '<span class="update-plugins count-' . (int) $t . '" title="' . esc_attr__( 'Open support tickets', 'free-backlinks-generator' ) . '"><span class="plugin-count">' . esc_html( (string) (int) $t ) . '</span></span>';
			}
		}
		if ( ! empty( $parts ) ) {
			$menu[ $i ][0] .= ' ' . implode( ' ', $parts ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		}
		break;
	}
}
add_action( 'admin_menu', 'fbg_support_admin_menu_badges', 999 );

/**
 * Admin notices for queue / tickets.
 */
function fbg_support_admin_notices() {
	if ( ! current_user_can( 'fbg_live_chat_agent' ) && ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( isset( $_GET['page'] ) && ( 'fbg-support-chat' === $_GET['page'] || 'fbg-support-tickets' === $_GET['page'] ) ) {
		return;
	}
	$bits = array();
	if ( current_user_can( 'fbg_live_chat_agent' ) && function_exists( 'fbg_chat_queue_count' ) ) {
		$q = fbg_chat_queue_count();
		if ( $q > 0 ) {
			$bits[] = '<a href="' . esc_url( admin_url( 'admin.php?page=fbg-support-chat&tab=queue' ) ) . '">' . sprintf( /* translators: %d count */ esc_html( _n( '%d chat in queue', '%d chats in queue', $q, 'free-backlinks-generator' ) ), (int) $q ) . '</a>';
		}
	}
	if ( current_user_can( 'manage_options' ) && function_exists( 'fbg_support_open_ticket_count' ) ) {
		$t = fbg_support_open_ticket_count();
		if ( $t > 0 ) {
			$bits[] = '<a href="' . esc_url( admin_url( 'admin.php?page=fbg-support-tickets' ) ) . '">' . sprintf( /* translators: %d */ esc_html( _n( '%d open ticket', '%d open tickets', $t, 'free-backlinks-generator' ) ), (int) $t ) . '</a>';
		}
	}
	if ( empty( $bits ) ) {
		return;
	}
	echo '<div class="notice notice-info is-dismissible"><p><strong>' . esc_html__( 'Support', 'free-backlinks-generator' ) . ':</strong> ' . wp_kses_post( implode( ' · ', $bits ) ) . '</p></div>';
}
add_action( 'admin_notices', 'fbg_support_admin_notices' );

/**
 * Enqueue chat admin script on chat page.
 */
function fbg_support_admin_assets() {
	$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';
	if ( 'fbg-support-chat' === $page ) {
		wp_enqueue_script(
			'fbg-chat-admin',
			FBG_URI . '/assets/js/fbg-chat-admin.js',
			array(),
			FBG_VERSION,
			true
		);
		wp_localize_script(
			'fbg-chat-admin',
			'fbgChatAdmin',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'fbg_chat_agent' ),
				'strings' => array(
					'loading' => __( 'Loading…', 'free-backlinks-generator' ),
					'send'    => __( 'Send', 'free-backlinks-generator' ),
					'claim'   => __( 'Take chat', 'free-backlinks-generator' ),
					'close'   => __( 'Close chat', 'free-backlinks-generator' ),
					'visitor' => __( 'Visitor', 'free-backlinks-generator' ),
					'you'     => __( 'You', 'free-backlinks-generator' ),
					'system'  => __( 'System', 'free-backlinks-generator' ),
					'empty'   => __( 'Select a conversation', 'free-backlinks-generator' ),
				),
			)
		);
		wp_enqueue_style( 'fbg-chat-admin', FBG_URI . '/assets/css/fbg-chat-admin.css', array(), FBG_VERSION );
	}
	if ( 'fbg-support-tickets' === $page && ! empty( $_GET['ticket'] ) ) {
		wp_enqueue_script(
			'fbg-tickets-admin',
			FBG_URI . '/assets/js/fbg-tickets-admin.js',
			array(),
			FBG_VERSION,
			true
		);
		wp_localize_script(
			'fbg-tickets-admin',
			'fbgTicketsAdmin',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'fbg_support_ticket_admin' ),
			)
		);
	}
}
add_action( 'admin_enqueue_scripts', 'fbg_support_admin_assets' );

/**
 * Live chat desk page.
 */
function fbg_render_support_chat_page() {
	if ( ! current_user_can( 'fbg_live_chat_agent' ) ) {
		wp_die( esc_html__( 'You do not have permission to access this page.', 'free-backlinks-generator' ) );
	}
	$tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'active';
	if ( ! in_array( $tab, array( 'active', 'queue', 'mine' ), true ) ) {
		$tab = 'active';
	}
	$preselect = isset( $_GET['session'] ) ? absint( $_GET['session'] ) : 0;
	$base      = admin_url( 'admin.php?page=fbg-support-chat' );
	?>
	<div class="wrap fbg-support-chat-wrap">
		<h1><?php esc_html_e( 'Live chat', 'free-backlinks-generator' ); ?></h1>
		<p class="description">
			<?php esc_html_e( 'Keep this page open while you are available — your browser will ping every 30 seconds so visitors see you as online. Unassigned conversations appear under Queue.', 'free-backlinks-generator' ); ?>
		</p>
		<h2 class="nav-tab-wrapper">
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'active', $base ) ); ?>" class="nav-tab <?php echo 'active' === $tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'All open', 'free-backlinks-generator' ); ?></a>
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'queue', $base ) ); ?>" class="nav-tab <?php echo 'queue' === $tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Queue', 'free-backlinks-generator' ); ?></a>
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'mine', $base ) ); ?>" class="nav-tab <?php echo 'mine' === $tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'My chats', 'free-backlinks-generator' ); ?></a>
		</h2>
		<div class="fbg-chat-desk" id="fbg-chat-desk" data-tab="<?php echo esc_attr( $tab ); ?>" data-preselect="<?php echo esc_attr( (string) $preselect ); ?>">
			<div class="fbg-chat-desk__list" id="fbg-chat-desk-list"></div>
			<div class="fbg-chat-desk__main">
				<div id="fbg-chat-desk-thread" class="fbg-chat-desk__thread"></div>
				<div class="fbg-chat-desk__actions" id="fbg-chat-desk-actions" hidden>
					<button type="button" class="button button-primary" id="fbg-chat-claim"><?php esc_html_e( 'Take chat', 'free-backlinks-generator' ); ?></button>
					<button type="button" class="button" id="fbg-chat-close"><?php esc_html_e( 'Close chat', 'free-backlinks-generator' ); ?></button>
				</div>
				<div class="fbg-chat-desk__composer" id="fbg-chat-desk-composer" hidden>
					<textarea id="fbg-chat-agent-input" rows="3" class="large-text" placeholder="<?php esc_attr_e( 'Type a reply…', 'free-backlinks-generator' ); ?>"></textarea>
					<p><button type="button" class="button button-primary" id="fbg-chat-agent-send"><?php esc_html_e( 'Send reply', 'free-backlinks-generator' ); ?></button></p>
				</div>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Tickets admin list / detail.
 */
function fbg_render_support_tickets_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to access this page.', 'free-backlinks-generator' ) );
	}
	$ticket_id = isset( $_GET['ticket'] ) ? absint( $_GET['ticket'] ) : 0;
	if ( $ticket_id > 0 ) {
		$ticket = fbg_support_ticket_get( $ticket_id );
		if ( ! $ticket ) {
			echo '<div class="wrap"><p>' . esc_html__( 'Ticket not found.', 'free-backlinks-generator' ) . '</p></div>';
			return;
		}
		$replies = fbg_support_ticket_replies( $ticket_id );
		?>
		<div class="wrap fbg-ticket-detail">
			<p><a href="<?php echo esc_url( admin_url( 'admin.php?page=fbg-support-tickets' ) ); ?>">&larr; <?php esc_html_e( 'All tickets', 'free-backlinks-generator' ); ?></a></p>
			<h1><?php echo esc_html( $ticket->public_id . ' — ' . $ticket->subject ); ?></h1>
			<p>
				<strong><?php esc_html_e( 'From', 'free-backlinks-generator' ); ?>:</strong>
				<?php echo esc_html( $ticket->requester_name ); ?> &lt;<?php echo esc_html( $ticket->requester_email ); ?>&gt;
				<?php if ( (int) $ticket->user_id ) : ?>
					<a href="<?php echo esc_url( get_edit_user_link( (int) $ticket->user_id ) ); ?>"><?php esc_html_e( 'WP profile', 'free-backlinks-generator' ); ?></a>
				<?php endif; ?>
			</p>
			<p>
				<strong><?php esc_html_e( 'Category', 'free-backlinks-generator' ); ?>:</strong> <?php echo esc_html( $ticket->category ); ?>
				&nbsp; <strong><?php esc_html_e( 'Priority', 'free-backlinks-generator' ); ?>:</strong> <?php echo esc_html( $ticket->priority ); ?>
				&nbsp; <strong><?php esc_html_e( 'Status', 'free-backlinks-generator' ); ?>:</strong> <?php echo esc_html( $ticket->status ); ?>
			</p>
			<div class="fbg-ticket-original">
				<h2><?php esc_html_e( 'Original message', 'free-backlinks-generator' ); ?></h2>
				<pre class="fbg-ticket-pre"><?php echo esc_html( $ticket->body ); ?></pre>
			</div>
			<?php if ( ! empty( $replies ) ) : ?>
				<h2><?php esc_html_e( 'Staff replies', 'free-backlinks-generator' ); ?></h2>
				<ul class="fbg-ticket-replies">
					<?php foreach ( $replies as $r ) : ?>
						<?php $author = $r->author_user_id ? get_userdata( (int) $r->author_user_id ) : null; ?>
						<li>
							<strong><?php echo esc_html( $author ? $author->display_name : __( 'Staff', 'free-backlinks-generator' ) ); ?></strong>
							<span class="description"><?php echo esc_html( $r->created_at ); ?></span>
							<pre class="fbg-ticket-pre"><?php echo esc_html( $r->body ); ?></pre>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<hr>
			<h2><?php esc_html_e( 'Reply to customer (email)', 'free-backlinks-generator' ); ?></h2>
			<textarea id="fbg-ticket-reply-body" class="large-text" rows="5"></textarea>
			<p><button type="button" class="button button-primary" id="fbg-ticket-send-reply" data-ticket-id="<?php echo esc_attr( (string) $ticket_id ); ?>"><?php esc_html_e( 'Send reply', 'free-backlinks-generator' ); ?></button> <span id="fbg-ticket-reply-msg"></span></p>
			<h2><?php esc_html_e( 'Update ticket', 'free-backlinks-generator' ); ?></h2>
			<p>
				<label><?php esc_html_e( 'Status', 'free-backlinks-generator' ); ?>
					<select id="fbg-ticket-status">
						<?php foreach ( array( 'open', 'pending', 'resolved', 'closed' ) as $st ) : ?>
							<option value="<?php echo esc_attr( $st ); ?>" <?php selected( $ticket->status, $st ); ?>><?php echo esc_html( $st ); ?></option>
						<?php endforeach; ?>
					</select>
				</label>
				<button type="button" class="button" id="fbg-ticket-save-status" data-ticket-id="<?php echo esc_attr( (string) $ticket_id ); ?>"><?php esc_html_e( 'Save status', 'free-backlinks-generator' ); ?></button>
			</p>
			<p>
				<label><?php esc_html_e( 'Internal notes (not emailed)', 'free-backlinks-generator' ); ?></label><br>
				<textarea id="fbg-ticket-admin-notes" class="large-text" rows="4"><?php echo esc_textarea( (string) $ticket->admin_notes ); ?></textarea><br>
				<button type="button" class="button" id="fbg-ticket-save-notes" data-ticket-id="<?php echo esc_attr( (string) $ticket_id ); ?>"><?php esc_html_e( 'Save notes', 'free-backlinks-generator' ); ?></button>
			</p>
		</div>
		<?php
		return;
	}

	global $wpdb;
	$table = fbg_support_tickets_table();
	$rows  = $wpdb->get_results( "SELECT * FROM {$table} ORDER BY updated_at DESC LIMIT 200" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Support tickets', 'free-backlinks-generator' ); ?></h1>
		<table class="widefat striped">
			<thead>
				<tr>
					<th><?php esc_html_e( 'ID', 'free-backlinks-generator' ); ?></th>
					<th><?php esc_html_e( 'Subject', 'free-backlinks-generator' ); ?></th>
					<th><?php esc_html_e( 'From', 'free-backlinks-generator' ); ?></th>
					<th><?php esc_html_e( 'Category', 'free-backlinks-generator' ); ?></th>
					<th><?php esc_html_e( 'Priority', 'free-backlinks-generator' ); ?></th>
					<th><?php esc_html_e( 'Status', 'free-backlinks-generator' ); ?></th>
					<th><?php esc_html_e( 'Updated', 'free-backlinks-generator' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( empty( $rows ) ) : ?>
					<tr><td colspan="7"><?php esc_html_e( 'No tickets yet.', 'free-backlinks-generator' ); ?></td></tr>
				<?php else : ?>
					<?php foreach ( $rows as $r ) : ?>
						<tr>
							<td><a href="<?php echo esc_url( admin_url( 'admin.php?page=fbg-support-tickets&ticket=' . (int) $r->id ) ); ?>"><?php echo esc_html( $r->public_id ); ?></a></td>
							<td><?php echo esc_html( $r->subject ); ?></td>
							<td><?php echo esc_html( $r->requester_email ); ?></td>
							<td><?php echo esc_html( $r->category ); ?></td>
							<td><?php echo esc_html( $r->priority ); ?></td>
							<td><?php echo esc_html( $r->status ); ?></td>
							<td><?php echo esc_html( $r->updated_at ); ?></td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
	<?php
}
