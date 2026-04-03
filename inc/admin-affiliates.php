<?php
/**
 * WP Admin: Affiliate Program — partners, applications, warnings, remove.
 *
 * @package Free_Backlinks_Generator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register admin menu + assets.
 */
function fbg_affiliate_admin_menu() {
	add_menu_page(
		__( 'Affiliate Program', 'free-backlinks-generator' ),
		__( 'Affiliates', 'free-backlinks-generator' ),
		'manage_options',
		'fbg-affiliate-program',
		'fbg_render_affiliate_admin_page',
		'dashicons-groups',
		27
	);
}
add_action( 'admin_menu', 'fbg_affiliate_admin_menu' );

/**
 * Pending-application count badge on Affiliates admin menu.
 *
 * @return void
 */
function fbg_affiliate_admin_menu_badge() {
	global $menu;
	if ( ! current_user_can( 'manage_options' ) || ! function_exists( 'fbg_affiliate_applications_queue_count' ) ) {
		return;
	}
	$count = fbg_affiliate_applications_queue_count();
	if ( $count < 1 || ! is_array( $menu ) ) {
		return;
	}
	foreach ( $menu as $i => $item ) {
		if ( isset( $item[2] ) && 'fbg-affiliate-program' === $item[2] ) {
			/* translators: %s: number of pending applications */
			$title = sprintf( __( 'Affiliates — %s pending applications', 'free-backlinks-generator' ), number_format_i18n( $count ) );
			$menu[ $i ][0] .= ' <span class="awaiting-mod count-' . (int) $count . '" title="' . esc_attr( $title ) . '"><span class="pending-count">' . esc_html( number_format_i18n( $count ) ) . '</span></span>'; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			break;
		}
	}
}
add_action( 'admin_menu', 'fbg_affiliate_admin_menu_badge', 999 );

/**
 * Dashboard notice when affiliate applications need review.
 *
 * @return void
 */
function fbg_affiliate_admin_pending_notice() {
	if ( ! current_user_can( 'manage_options' ) || ! function_exists( 'fbg_affiliate_applications_queue_count' ) ) {
		return;
	}
	$count = fbg_affiliate_applications_queue_count();
	if ( $count < 1 ) {
		return;
	}
	$url = admin_url( 'admin.php?page=fbg-affiliate-program&tab=applications' );
	printf(
		'<div class="notice notice-info is-dismissible"><p><strong>%1$s</strong> — %2$s <a href="%3$s">%4$s</a></p></div>',
		esc_html(
			sprintf(
				/* translators: %s number of applications */
				_n( '%s pending affiliate application', '%s pending affiliate applications', $count, 'free-backlinks-generator' ),
				number_format_i18n( $count )
			)
		),
		esc_html__( 'Review, approve, or reject from the Affiliates screen.', 'free-backlinks-generator' ),
		esc_url( $url ),
		esc_html__( 'Open applications', 'free-backlinks-generator' )
	);
}
add_action( 'admin_notices', 'fbg_affiliate_admin_pending_notice' );

/**
 * Enqueue admin styles for affiliate screens.
 *
 * @param string $hook_suffix Current admin page.
 */
function fbg_affiliate_admin_assets( $hook_suffix ) {
	if ( 'toplevel_page_fbg-affiliate-program' !== $hook_suffix ) {
		return;
	}
	wp_enqueue_style(
		'fbg-admin-affiliates',
		FBG_URI . '/assets/css/admin-affiliates.css',
		array(),
		FBG_VERSION
	);
}
add_action( 'admin_enqueue_scripts', 'fbg_affiliate_admin_assets' );

/**
 * Process POST actions (PRG).
 *
 * @return void
 */
function fbg_affiliate_admin_handle_post() {
	if ( 'POST' !== ( $_SERVER['REQUEST_METHOD'] ?? '' ) || empty( $_POST['fbg_aff_action'] ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'fbg_aff_admin' ) ) {
		return;
	}

	$action       = sanitize_key( wp_unslash( $_POST['fbg_aff_action'] ) );
	$redirect_tab = 'partners';
	if ( in_array( $action, array( 'approve_lead', 'dismiss_lead', 'reject_lead', 'reconsider_lead' ), true ) ) {
		$redirect_tab = 'applications';
	} elseif ( 'add_partner' === $action ) {
		$posted_tab = isset( $_POST['fbg_aff_tab'] ) ? sanitize_key( wp_unslash( $_POST['fbg_aff_tab'] ) ) : 'add';
		$redirect_tab = ( 'removed' === $posted_tab ) ? 'removed' : 'partners';
	}
	$args = array(
		'page' => 'fbg-affiliate-program',
		'tab'  => $redirect_tab,
	);

	switch ( $action ) {
		case 'add_partner':
			$uid  = isset( $_POST['user_id'] ) ? absint( $_POST['user_id'] ) : 0;
			$mail = isset( $_POST['user_email'] ) ? sanitize_email( wp_unslash( $_POST['user_email'] ) ) : '';
			if ( $uid < 1 && is_email( $mail ) ) {
				$u = get_user_by( 'email', $mail );
				if ( $u ) {
					$uid = (int) $u->ID;
				}
			}
			if ( $uid < 1 ) {
				$args['fbg_aff_err'] = 'no_user';
				break;
			}
			update_user_meta( $uid, '_fbg_affiliate_active', 'yes' );
			update_user_meta( $uid, '_fbg_affiliate_approved_at', time() );
			if ( function_exists( 'fbg_create_notification' ) ) {
				fbg_create_notification(
					$uid,
					'affiliate_approved',
					__( 'Your affiliate partner account is active. You can share your referral link from the Affiliate Program page.', 'free-backlinks-generator' ),
					null
				);
			}
			$args['fbg_aff_ok'] = 'added';
			break;

		case 'remove_partner':
			$uid = isset( $_POST['user_id'] ) ? absint( $_POST['user_id'] ) : 0;
			if ( $uid > 0 ) {
				update_user_meta( $uid, '_fbg_affiliate_active', 'no' );
				delete_user_meta( $uid, '_fbg_affiliate_warning' );
				if ( function_exists( 'fbg_create_notification' ) ) {
					fbg_create_notification(
						$uid,
						'affiliate_removed',
						__( 'Your access to the affiliate program has been removed by an administrator. Referral links will no longer earn new credit.', 'free-backlinks-generator' ),
						null
					);
				}
				$args['fbg_aff_ok'] = 'removed';
			}
			break;

		case 'warn_partner':
			$uid = isset( $_POST['user_id'] ) ? absint( $_POST['user_id'] ) : 0;
			$msg = isset( $_POST['warning_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['warning_message'] ) ) : '';
			if ( $uid > 0 && strlen( $msg ) > 2 ) {
				update_user_meta( $uid, '_fbg_affiliate_warning', $msg );
				if ( function_exists( 'fbg_create_notification' ) ) {
					fbg_create_notification(
						$uid,
						'affiliate_warn',
						sprintf(
							/* translators: %s warning text from admin */
							__( 'Affiliate program notice from the team: %s', 'free-backlinks-generator' ),
							$msg
						),
						null
					);
				}
				$args['fbg_aff_ok'] = 'warned';
			} else {
				$args['fbg_aff_err'] = 'warn_invalid';
			}
			break;

		case 'clear_warning':
			$uid = isset( $_POST['user_id'] ) ? absint( $_POST['user_id'] ) : 0;
			if ( $uid > 0 ) {
				delete_user_meta( $uid, '_fbg_affiliate_warning' );
				$args['fbg_aff_ok'] = 'warning_cleared';
			}
			break;

		case 'approve_lead':
			$idx   = isset( $_POST['lead_index'] ) ? absint( $_POST['lead_index'] ) : 99999;
			$leads = get_option( 'fbg_affiliate_leads', array() );
			if ( ! is_array( $leads ) || ! isset( $leads[ $idx ] ) ) {
				$args['fbg_aff_err'] = 'lead_missing';
				break;
			}
			$lead = $leads[ $idx ];
			$uid  = isset( $lead['user_id'] ) ? absint( $lead['user_id'] ) : 0;
			$em   = isset( $lead['email'] ) ? sanitize_email( $lead['email'] ) : '';
			$user = $uid ? get_userdata( $uid ) : ( is_email( $em ) ? get_user_by( 'email', $em ) : false );
			if ( ! $user ) {
				$args['fbg_aff_err'] = 'lead_no_wp_user';
				break;
			}
			update_user_meta( $user->ID, '_fbg_affiliate_active', 'yes' );
			update_user_meta( $user->ID, '_fbg_affiliate_approved_at', time() );
			delete_user_meta( $user->ID, '_fbg_affiliate_app_status' );
			delete_user_meta( $user->ID, '_fbg_affiliate_app_payload' );
			delete_user_meta( $user->ID, '_fbg_affiliate_app_submitted_at' );
			array_splice( $leads, $idx, 1 );
			update_option( 'fbg_affiliate_leads', $leads, false );
			$reset_url = function_exists( 'fbg_affiliate_password_reset_url_for_user' ) ? fbg_affiliate_password_reset_url_for_user( $user ) : '';
			if ( function_exists( 'fbg_send_affiliate_approved_email' ) ) {
				fbg_send_affiliate_approved_email( $user, $reset_url );
			}
			if ( function_exists( 'fbg_create_notification' ) ) {
				fbg_create_notification(
					$user->ID,
					'affiliate_approved',
					__( 'Your affiliate application was approved. Check your email for a link to set your password if needed, then share your referral link from the Affiliate Program page.', 'free-backlinks-generator' ),
					null
				);
			}
			$args['fbg_aff_ok'] = 'lead_approved';
			break;

		case 'reject_lead':
			$idx   = isset( $_POST['lead_index'] ) ? absint( $_POST['lead_index'] ) : 99999;
			$leads = get_option( 'fbg_affiliate_leads', array() );
			if ( ! is_array( $leads ) || ! isset( $leads[ $idx ] ) ) {
				$args['fbg_aff_err'] = 'lead_missing';
				break;
			}
			$lead = $leads[ $idx ];
			$uid  = isset( $lead['user_id'] ) ? absint( $lead['user_id'] ) : 0;
			$em   = isset( $lead['email'] ) ? sanitize_email( $lead['email'] ) : '';
			$user = $uid ? get_userdata( $uid ) : ( is_email( $em ) ? get_user_by( 'email', $em ) : false );
			if ( $user ) {
				update_user_meta( $user->ID, '_fbg_affiliate_app_status', 'rejected' );
				update_user_meta( $user->ID, '_fbg_affiliate_active', 'no' );
				delete_user_meta( $user->ID, '_fbg_affiliate_approved_at' );
				if ( function_exists( 'fbg_send_affiliate_rejected_email' ) ) {
					fbg_send_affiliate_rejected_email( $user );
				}
			}
			array_splice( $leads, $idx, 1 );
			update_option( 'fbg_affiliate_leads', $leads, false );
			$args['fbg_aff_ok'] = 'lead_rejected';
			break;

		case 'dismiss_lead':
			$idx   = isset( $_POST['lead_index'] ) ? absint( $_POST['lead_index'] ) : 99999;
			$leads = get_option( 'fbg_affiliate_leads', array() );
			if ( is_array( $leads ) && isset( $leads[ $idx ] ) ) {
				$row  = $leads[ $idx ];
				$duid = isset( $row['user_id'] ) ? absint( $row['user_id'] ) : 0;
				$dem  = isset( $row['email'] ) ? sanitize_email( $row['email'] ) : '';
				$dusr = $duid ? get_userdata( $duid ) : ( is_email( $dem ) ? get_user_by( 'email', $dem ) : false );
				if ( $dusr && 'pending' === get_user_meta( $dusr->ID, '_fbg_affiliate_app_status', true ) ) {
					delete_user_meta( $dusr->ID, '_fbg_affiliate_app_status' );
					delete_user_meta( $dusr->ID, '_fbg_affiliate_app_payload' );
					delete_user_meta( $dusr->ID, '_fbg_affiliate_app_submitted_at' );
				}
				array_splice( $leads, $idx, 1 );
				update_option( 'fbg_affiliate_leads', $leads, false );
				$args['fbg_aff_ok'] = 'lead_dismissed';
			}
			break;

		case 'reconsider_lead':
			$idx   = isset( $_POST['lead_index'] ) ? absint( $_POST['lead_index'] ) : 99999;
			$leads = get_option( 'fbg_affiliate_leads', array() );
			if ( ! is_array( $leads ) || ! isset( $leads[ $idx ] ) ) {
				$args['fbg_aff_err'] = 'lead_missing';
				break;
			}
			$lead = $leads[ $idx ];
			$em   = isset( $lead['email'] ) ? sanitize_email( $lead['email'] ) : '';
			if ( ! is_email( $em ) ) {
				$args['fbg_aff_err'] = 'reconsider_no_email';
				break;
			}
			$msg = isset( $_POST['reconsider_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['reconsider_message'] ) ) : '';
			if ( strlen( $msg ) < 15 ) {
				$args['fbg_aff_err'] = 'reconsider_short';
				break;
			}
			$disp = isset( $lead['name'] ) ? (string) $lead['name'] : '';
			if ( function_exists( 'fbg_send_affiliate_reconsideration_email' ) ) {
				fbg_send_affiliate_reconsideration_email( $em, $disp, $msg );
			}
			$uid  = isset( $lead['user_id'] ) ? absint( $lead['user_id'] ) : 0;
			$user = $uid ? get_userdata( $uid ) : get_user_by( 'email', $em );
			if ( $user ) {
				update_user_meta( $user->ID, '_fbg_affiliate_app_status', 'needs_info' );
				if ( function_exists( 'fbg_create_notification' ) ) {
					fbg_create_notification(
						$user->ID,
						'affiliate_needs_info',
						sprintf(
							/* translators: %s admin message excerpt */
							__( 'Your affiliate application needs more information: %s', 'free-backlinks-generator' ),
							wp_trim_words( $msg, 24 )
						),
						null
					);
				}
			}
			$args['fbg_aff_ok'] = 'lead_reconsider';
			break;

		default:
			break;
	}

	wp_safe_redirect( add_query_arg( $args, admin_url( 'admin.php' ) ) );
	exit;
}
add_action( 'admin_init', 'fbg_affiliate_admin_handle_post' );

/**
 * Render the Affiliates admin page.
 *
 * @return void
 */
function fbg_render_affiliate_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'partners';
	$allowed_tabs = array( 'partners', 'removed', 'add', 'applications', 'warn' );
	if ( ! in_array( $tab, $allowed_tabs, true ) ) {
		$tab = 'partners';
	}

	$warn_user = isset( $_GET['user'] ) ? absint( $_GET['user'] ) : 0;
	if ( 'warn' === $tab && $warn_user < 1 ) {
		$tab = 'partners';
	}

	// Admin notices from redirect.
	if ( ! empty( $_GET['fbg_aff_ok'] ) ) {
		$ok = sanitize_key( wp_unslash( $_GET['fbg_aff_ok'] ) );
		$messages = array(
			'added'            => __( 'Partner added successfully.', 'free-backlinks-generator' ),
			'removed'          => __( 'Partner removed from the program.', 'free-backlinks-generator' ),
			'warned'           => __( 'Warning saved and the user was notified.', 'free-backlinks-generator' ),
			'warning_cleared'  => __( 'Warning cleared.', 'free-backlinks-generator' ),
			'lead_approved'    => __( 'Application approved. The applicant was emailed with approval details and a link to set their password.', 'free-backlinks-generator' ),
			'lead_rejected'    => __( 'Application rejected. The applicant was notified by email.', 'free-backlinks-generator' ),
			'lead_dismissed'   => __( 'Application removed from the queue (no rejection email sent).', 'free-backlinks-generator' ),
			'lead_reconsider'  => __( 'Reconsideration request sent by email. The application remains in the queue.', 'free-backlinks-generator' ),
		);
		if ( isset( $messages[ $ok ] ) ) {
			printf( '<div class="notice notice-success is-dismissible"><p>%s</p></div>', esc_html( $messages[ $ok ] ) );
		}
	}
	if ( ! empty( $_GET['fbg_aff_err'] ) ) {
		$err = sanitize_key( wp_unslash( $_GET['fbg_aff_err'] ) );
		$errors = array(
			'no_user'         => __( 'No WordPress user found for that email or ID.', 'free-backlinks-generator' ),
			'warn_invalid'    => __( 'Enter a warning message (at least 3 characters).', 'free-backlinks-generator' ),
			'lead_missing'    => __( 'That application row no longer exists.', 'free-backlinks-generator' ),
			'lead_no_wp_user' => __( 'No WordPress account uses that email. Create the user first or add them manually on the Add tab.', 'free-backlinks-generator' ),
			'reconsider_short' => __( 'Enter a reconsideration message (at least 15 characters).', 'free-backlinks-generator' ),
			'reconsider_no_email' => __( 'This row has no valid email address.', 'free-backlinks-generator' ),
		);
		if ( isset( $errors[ $err ] ) ) {
			printf( '<div class="notice notice-error is-dismissible"><p>%s</p></div>', esc_html( $errors[ $err ] ) );
		}
	}

	$base = admin_url( 'admin.php?page=fbg-affiliate-program' );
	?>
	<div class="wrap fbg-aff-admin">
		<h1><?php esc_html_e( 'Affiliate Program', 'free-backlinks-generator' ); ?></h1>
		<p class="description"><?php esc_html_e( 'Partner applications create (or update) a member account. Use Contact to open your mail client, Approve to activate and email a password link, Reject to notify the applicant, Request reconsideration to email without removing the row, or Dismiss to remove the row quietly. Only approved partners earn new referral credit.', 'free-backlinks-generator' ); ?></p>

		<h2 class="nav-tab-wrapper wp-clearfix">
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'partners', $base ) ); ?>" class="nav-tab <?php echo 'partners' === $tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Active partners', 'free-backlinks-generator' ); ?></a>
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'applications', $base ) ); ?>" class="nav-tab <?php echo 'applications' === $tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Applications', 'free-backlinks-generator' ); ?></a>
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'add', $base ) ); ?>" class="nav-tab <?php echo 'add' === $tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Add partner', 'free-backlinks-generator' ); ?></a>
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'removed', $base ) ); ?>" class="nav-tab <?php echo 'removed' === $tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Removed', 'free-backlinks-generator' ); ?></a>
		</h2>

		<?php
		switch ( $tab ) {
			case 'partners':
				fbg_aff_admin_tab_partners();
				break;
			case 'removed':
				fbg_aff_admin_tab_removed();
				break;
			case 'add':
				fbg_aff_admin_tab_add();
				break;
			case 'applications':
				fbg_aff_admin_tab_applications();
				break;
			case 'warn':
				fbg_aff_admin_tab_warn( $warn_user );
				break;
		}
		?>
	</div>
	<?php
}

/**
 * Tab: active partners.
 *
 * @return void
 */
function fbg_aff_admin_tab_partners() {
	$q = new WP_User_Query(
		array(
			'meta_key'   => '_fbg_affiliate_active',
			'meta_value' => 'yes',
			'number'     => 300,
			'orderby'    => 'registered',
			'order'      => 'DESC',
		)
	);
	$users = $q->get_results();
	?>
	<table class="widefat striped fbg-aff-table">
		<thead>
			<tr>
				<th><?php esc_html_e( 'User', 'free-backlinks-generator' ); ?></th>
				<th><?php esc_html_e( 'Email', 'free-backlinks-generator' ); ?></th>
				<th><?php esc_html_e( 'Referrals', 'free-backlinks-generator' ); ?></th>
				<th><?php esc_html_e( 'Balance', 'free-backlinks-generator' ); ?></th>
				<th><?php esc_html_e( 'Warning', 'free-backlinks-generator' ); ?></th>
				<th><?php esc_html_e( 'Actions', 'free-backlinks-generator' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if ( empty( $users ) ) : ?>
				<tr><td colspan="6"><?php esc_html_e( 'No active partners yet. Add a user or approve an application.', 'free-backlinks-generator' ); ?></td></tr>
			<?php else : ?>
				<?php foreach ( $users as $user ) : ?>
					<?php
					$uid    = (int) $user->ID;
					$total  = (int) get_user_meta( $uid, '_fbg_aff_referral_total', true );
					$bal    = (float) get_user_meta( $uid, '_fbg_aff_balance_usd', true );
					$warn   = get_user_meta( $uid, '_fbg_affiliate_warning', true );
					$warn_s = is_string( $warn ) ? $warn : '';
					?>
					<tr>
						<td>
							<strong><a href="<?php echo esc_url( get_edit_user_link( $uid ) ); ?>"><?php echo esc_html( $user->display_name ); ?></a></strong>
							<br><span class="description">ID <?php echo esc_html( (string) $uid ); ?></span>
						</td>
						<td><?php echo esc_html( $user->user_email ); ?></td>
						<td><?php echo esc_html( number_format_i18n( $total ) ); ?></td>
						<td>$<?php echo esc_html( number_format_i18n( $bal, 2 ) ); ?></td>
						<td><?php echo $warn_s ? esc_html( wp_trim_words( $warn_s, 12 ) ) : '—'; ?></td>
						<td class="fbg-aff-actions">
							<a class="button button-small" href="<?php echo esc_url( add_query_arg( array( 'tab' => 'warn', 'user' => $uid ), admin_url( 'admin.php?page=fbg-affiliate-program' ) ) ); ?>"><?php esc_html_e( 'Warn', 'free-backlinks-generator' ); ?></a>
							<?php if ( $warn_s ) : ?>
								<form method="post" style="display:inline;" onsubmit="return confirm('<?php echo esc_js( __( 'Clear this warning?', 'free-backlinks-generator' ) ); ?>');">
									<?php wp_nonce_field( 'fbg_aff_admin' ); ?>
									<input type="hidden" name="fbg_aff_action" value="clear_warning">
									<input type="hidden" name="fbg_aff_tab" value="partners">
									<input type="hidden" name="user_id" value="<?php echo esc_attr( (string) $uid ); ?>">
									<button type="submit" class="button button-small"><?php esc_html_e( 'Clear warning', 'free-backlinks-generator' ); ?></button>
								</form>
							<?php endif; ?>
							<form method="post" style="display:inline;" onsubmit="return confirm('<?php echo esc_js( __( 'Remove this partner? Their links will stop earning new credit.', 'free-backlinks-generator' ) ); ?>');">
								<?php wp_nonce_field( 'fbg_aff_admin' ); ?>
								<input type="hidden" name="fbg_aff_action" value="remove_partner">
								<input type="hidden" name="fbg_aff_tab" value="partners">
								<input type="hidden" name="user_id" value="<?php echo esc_attr( (string) $uid ); ?>">
								<button type="submit" class="button button-small button-link-delete"><?php esc_html_e( 'Remove', 'free-backlinks-generator' ); ?></button>
							</form>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
	<?php
}

/**
 * Tab: removed partners.
 *
 * @return void
 */
function fbg_aff_admin_tab_removed() {
	$q = new WP_User_Query(
		array(
			'meta_key'   => '_fbg_affiliate_active',
			'meta_value' => 'no',
			'number'     => 300,
			'orderby'    => 'registered',
			'order'      => 'DESC',
		)
	);
	$users = $q->get_results();
	?>
	<table class="widefat striped fbg-aff-table">
		<thead>
			<tr>
				<th><?php esc_html_e( 'User', 'free-backlinks-generator' ); ?></th>
				<th><?php esc_html_e( 'Email', 'free-backlinks-generator' ); ?></th>
				<th><?php esc_html_e( 'Actions', 'free-backlinks-generator' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if ( empty( $users ) ) : ?>
				<tr><td colspan="3"><?php esc_html_e( 'No removed accounts.', 'free-backlinks-generator' ); ?></td></tr>
			<?php else : ?>
				<?php foreach ( $users as $user ) : ?>
					<?php $uid = (int) $user->ID; ?>
					<tr>
						<td><strong><a href="<?php echo esc_url( get_edit_user_link( $uid ) ); ?>"><?php echo esc_html( $user->display_name ); ?></a></strong></td>
						<td><?php echo esc_html( $user->user_email ); ?></td>
						<td>
							<form method="post" style="display:inline;" onsubmit="return confirm('<?php echo esc_js( __( 'Re-activate this partner?', 'free-backlinks-generator' ) ); ?>');">
								<?php wp_nonce_field( 'fbg_aff_admin' ); ?>
								<input type="hidden" name="fbg_aff_action" value="add_partner">
								<input type="hidden" name="fbg_aff_tab" value="removed">
								<input type="hidden" name="user_id" value="<?php echo esc_attr( (string) $uid ); ?>">
								<button type="submit" class="button button-small button-primary"><?php esc_html_e( 'Re-add', 'free-backlinks-generator' ); ?></button>
							</form>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
	<?php
}

/**
 * Tab: add partner form.
 *
 * @return void
 */
function fbg_aff_admin_tab_add() {
	?>
	<form method="post" class="fbg-aff-form">
		<?php wp_nonce_field( 'fbg_aff_admin' ); ?>
		<input type="hidden" name="fbg_aff_action" value="add_partner">
		<input type="hidden" name="fbg_aff_tab" value="add">
		<table class="form-table">
			<tr>
				<th scope="row"><label for="fbg_add_email"><?php esc_html_e( 'User email', 'free-backlinks-generator' ); ?></label></th>
				<td>
					<input type="email" class="regular-text" id="fbg_add_email" name="user_email" placeholder="name@example.com">
					<p class="description"><?php esc_html_e( 'Must match an existing WordPress user (e.g. a community member).', 'free-backlinks-generator' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="fbg_add_uid"><?php esc_html_e( 'Or user ID', 'free-backlinks-generator' ); ?></label></th>
				<td>
					<input type="number" class="small-text" id="fbg_add_uid" name="user_id" min="1" step="1" placeholder="12">
				</td>
			</tr>
		</table>
		<?php submit_button( __( 'Add as partner', 'free-backlinks-generator' ) ); ?>
	</form>
	<?php
}

/**
 * Tab: applications queue.
 *
 * @return void
 */
function fbg_aff_admin_tab_applications() {
	$leads = get_option( 'fbg_affiliate_leads', array() );
	if ( ! is_array( $leads ) ) {
		$leads = array();
	}
	?>
	<table class="widefat striped fbg-aff-table">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Date', 'free-backlinks-generator' ); ?></th>
				<th><?php esc_html_e( 'Name', 'free-backlinks-generator' ); ?></th>
				<th><?php esc_html_e( 'Email', 'free-backlinks-generator' ); ?></th>
				<th><?php esc_html_e( 'Website', 'free-backlinks-generator' ); ?></th>
				<th><?php esc_html_e( 'WP user', 'free-backlinks-generator' ); ?></th>
				<th><?php esc_html_e( 'Actions', 'free-backlinks-generator' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if ( empty( $leads ) ) : ?>
				<tr><td colspan="6"><?php esc_html_e( 'No pending applications in the queue.', 'free-backlinks-generator' ); ?></td></tr>
			<?php else : ?>
				<?php foreach ( $leads as $idx => $lead ) : ?>
					<?php
					$em    = isset( $lead['email'] ) ? sanitize_email( $lead['email'] ) : '';
					$wp_u  = is_email( $em ) ? get_user_by( 'email', $em ) : false;
					$ts    = isset( $lead['ts'] ) ? (int) $lead['ts'] : 0;
					$mail_subj = sprintf(
						/* translators: %s site name */
						__( 'Affiliate program — %s', 'free-backlinks-generator' ),
						wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES )
					);
					$mailto    = is_email( $em ) ? 'mailto:' . $em . '?subject=' . rawurlencode( $mail_subj ) : '';
					?>
					<tr>
						<td><?php echo esc_html( $ts ? wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $ts ) : '—' ); ?></td>
						<td><?php echo esc_html( isset( $lead['name'] ) ? (string) $lead['name'] : '' ); ?></td>
						<td><?php echo esc_html( $em ); ?></td>
						<td><?php echo isset( $lead['web'] ) ? '<a href="' . esc_url( $lead['web'] ) . '" target="_blank" rel="noopener">' . esc_html( (string) $lead['web'] ) . '</a>' : '—'; ?></td>
						<td>
							<?php
							if ( $wp_u ) {
								echo '<a href="' . esc_url( get_edit_user_link( $wp_u->ID ) ) . '">✓ ' . esc_html( $wp_u->user_login ) . '</a>';
							} else {
								esc_html_e( 'No match', 'free-backlinks-generator' );
							}
							?>
						</td>
						<td class="fbg-aff-actions">
							<div class="fbg-aff-actions-row">
								<?php if ( $mailto ) : ?>
									<a class="button button-small" href="<?php echo esc_url( $mailto ); ?>"><?php esc_html_e( 'Contact', 'free-backlinks-generator' ); ?></a>
								<?php endif; ?>
								<?php if ( $wp_u ) : ?>
									<form method="post" class="fbg-aff-inline-form">
										<?php wp_nonce_field( 'fbg_aff_admin' ); ?>
										<input type="hidden" name="fbg_aff_action" value="approve_lead">
										<input type="hidden" name="fbg_aff_tab" value="applications">
										<input type="hidden" name="lead_index" value="<?php echo esc_attr( (string) $idx ); ?>">
										<button type="submit" class="button button-small button-primary"><?php esc_html_e( 'Approve', 'free-backlinks-generator' ); ?></button>
									</form>
									<form method="post" class="fbg-aff-inline-form" onsubmit="return confirm('<?php echo esc_js( __( 'Reject this application? The applicant will receive an email.', 'free-backlinks-generator' ) ); ?>');">
										<?php wp_nonce_field( 'fbg_aff_admin' ); ?>
										<input type="hidden" name="fbg_aff_action" value="reject_lead">
										<input type="hidden" name="fbg_aff_tab" value="applications">
										<input type="hidden" name="lead_index" value="<?php echo esc_attr( (string) $idx ); ?>">
										<button type="submit" class="button button-small"><?php esc_html_e( 'Reject', 'free-backlinks-generator' ); ?></button>
									</form>
								<?php endif; ?>
								<form method="post" class="fbg-aff-inline-form" onsubmit="return confirm('<?php echo esc_js( __( 'Remove from the list without sending an email?', 'free-backlinks-generator' ) ); ?>');">
									<?php wp_nonce_field( 'fbg_aff_admin' ); ?>
									<input type="hidden" name="fbg_aff_action" value="dismiss_lead">
									<input type="hidden" name="fbg_aff_tab" value="applications">
									<input type="hidden" name="lead_index" value="<?php echo esc_attr( (string) $idx ); ?>">
									<button type="submit" class="button button-small"><?php esc_html_e( 'Dismiss', 'free-backlinks-generator' ); ?></button>
								</form>
							</div>
							<?php if ( is_email( $em ) ) : ?>
								<div class="fbg-aff-reconsider">
									<form method="post" class="fbg-aff-reconsider-form">
										<?php wp_nonce_field( 'fbg_aff_admin' ); ?>
										<input type="hidden" name="fbg_aff_action" value="reconsider_lead">
										<input type="hidden" name="fbg_aff_tab" value="applications">
										<input type="hidden" name="lead_index" value="<?php echo esc_attr( (string) $idx ); ?>">
										<label class="screen-reader-text" for="fbg-reconsider-<?php echo esc_attr( (string) $idx ); ?>"><?php esc_html_e( 'Message to applicant', 'free-backlinks-generator' ); ?></label>
										<textarea id="fbg-reconsider-<?php echo esc_attr( (string) $idx ); ?>" name="reconsider_message" rows="3" class="large-text" placeholder="<?php echo esc_attr__( 'Request more detail, clarification, or documents… (email sent to applicant; row stays in queue)', 'free-backlinks-generator' ); ?>" required minlength="15"></textarea>
										<p><button type="submit" class="button button-small"><?php esc_html_e( 'Request reconsideration', 'free-backlinks-generator' ); ?></button></p>
									</form>
								</div>
							<?php endif; ?>
							<?php if ( isset( $lead['plan'] ) && (string) $lead['plan'] !== '' ) : ?>
								<details class="fbg-aff-details">
									<summary><?php esc_html_e( 'Promotion plan', 'free-backlinks-generator' ); ?></summary>
									<pre class="fbg-aff-pre"><?php echo esc_html( (string) $lead['plan'] ); ?></pre>
								</details>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
	<?php
}

/**
 * Tab: warn single user.
 *
 * @param int $user_id User ID.
 * @return void
 */
function fbg_aff_admin_tab_warn( $user_id ) {
	$user = get_userdata( $user_id );
	if ( ! $user ) {
		echo '<p>' . esc_html__( 'User not found.', 'free-backlinks-generator' ) . '</p>';
		return;
	}
	$current = get_user_meta( $user_id, '_fbg_affiliate_warning', true );
	$current = is_string( $current ) ? $current : '';
	?>
	<p>
		<?php
		printf(
			/* translators: %s display name */
			esc_html__( 'Send a warning to %s. They will see it on their dashboard and in notifications.', 'free-backlinks-generator' ),
			esc_html( $user->display_name )
		);
		?>
	</p>
	<form method="post" class="fbg-aff-form">
		<?php wp_nonce_field( 'fbg_aff_admin' ); ?>
		<input type="hidden" name="fbg_aff_action" value="warn_partner">
		<input type="hidden" name="fbg_aff_tab" value="warn">
		<input type="hidden" name="user_id" value="<?php echo esc_attr( (string) $user_id ); ?>">
		<table class="form-table">
			<tr>
				<th scope="row"><label for="fbg_warn_msg"><?php esc_html_e( 'Warning message', 'free-backlinks-generator' ); ?></label></th>
				<td>
					<textarea class="large-text" rows="5" id="fbg_warn_msg" name="warning_message" required><?php echo esc_textarea( $current ); ?></textarea>
				</td>
			</tr>
		</table>
		<?php submit_button( __( 'Save warning', 'free-backlinks-generator' ) ); ?>
	</form>
	<p><a href="<?php echo esc_url( admin_url( 'admin.php?page=fbg-affiliate-program&tab=partners' ) ); ?>"><?php esc_html_e( '← Back to partners', 'free-backlinks-generator' ); ?></a></p>
	<?php
}
