<?php
/**
 * WP Admin: Earn center — payout queue + partner balances.
 *
 * @package Free_Backlinks_Generator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Submenu under Affiliates.
 */
function fbg_earn_admin_menu() {
	add_submenu_page(
		'fbg-affiliate-program',
		__( 'Earn & Payouts', 'free-backlinks-generator' ),
		__( 'Earn & Payouts', 'free-backlinks-generator' ),
		'manage_options',
		'fbg-earn-payouts',
		'fbg_render_earn_admin_page'
	);
}
add_action( 'admin_menu', 'fbg_earn_admin_menu', 11 );

/**
 * Process payout actions (PRG).
 */
function fbg_earn_admin_handle_post() {
	if ( 'POST' !== ( $_SERVER['REQUEST_METHOD'] ?? '' ) || empty( $_POST['fbg_earn_payout_action'] ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'fbg_earn_admin' ) ) {
		return;
	}
	$action = sanitize_key( wp_unslash( $_POST['fbg_earn_payout_action'] ) );
	$rid    = isset( $_POST['request_id'] ) ? absint( $_POST['request_id'] ) : 0;
	$args   = array(
		'page' => 'fbg-earn-payouts',
	);
	if ( $rid < 1 ) {
		$args['fbg_earn_err'] = 'bad_id';
		wp_safe_redirect( add_query_arg( $args, admin_url( 'admin.php' ) ) );
		exit;
	}
	$admin_id = get_current_user_id();
	if ( 'mark_paid' === $action ) {
		$ref  = isset( $_POST['payment_ref'] ) ? sanitize_text_field( wp_unslash( $_POST['payment_ref'] ) ) : '';
		$note = isset( $_POST['admin_note'] ) ? sanitize_textarea_field( wp_unslash( $_POST['admin_note'] ) ) : '';
		if ( strlen( $ref ) < 2 ) {
			$args['fbg_earn_err'] = 'need_ref';
		} else {
			$r = fbg_earn_mark_request_paid( $rid, $admin_id, $ref, $note );
			if ( is_wp_error( $r ) ) {
				$args['fbg_earn_err'] = 'pay_' . $r->get_error_code();
			} else {
				$args['fbg_earn_ok'] = 'paid';
			}
		}
	} elseif ( 'reject' === $action ) {
		$note = isset( $_POST['admin_note'] ) ? sanitize_textarea_field( wp_unslash( $_POST['admin_note'] ) ) : '';
		$r    = fbg_earn_mark_request_rejected( $rid, $admin_id, $note );
		if ( is_wp_error( $r ) ) {
			$args['fbg_earn_err'] = 'rej_' . $r->get_error_code();
		} else {
			$args['fbg_earn_ok'] = 'rejected';
		}
	}
	wp_safe_redirect( add_query_arg( $args, admin_url( 'admin.php' ) ) );
	exit;
}
add_action( 'admin_init', 'fbg_earn_admin_handle_post' );

/**
 * Admin page markup.
 */
function fbg_render_earn_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( ! empty( $_GET['fbg_earn_ok'] ) ) {
		$ok = sanitize_key( wp_unslash( $_GET['fbg_earn_ok'] ) );
		if ( 'paid' === $ok ) {
			echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Payout marked as paid. User wallet and ledger were updated.', 'free-backlinks-generator' ) . '</p></div>';
		} elseif ( 'rejected' === $ok ) {
			echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Request rejected.', 'free-backlinks-generator' ) . '</p></div>';
		}
	}
	if ( ! empty( $_GET['fbg_earn_err'] ) ) {
		$err = sanitize_key( wp_unslash( $_GET['fbg_earn_err'] ) );
		$map = array(
			'bad_id'            => __( 'Invalid request.', 'free-backlinks-generator' ),
			'need_ref'          => __( 'Enter a payment reference (e.g. PayPal transaction ID, Stripe id).', 'free-backlinks-generator' ),
			'pay_fbg_payout_bad'=> __( 'Could not mark paid (invalid state).', 'free-backlinks-generator' ),
			'pay_fbg_payout_funds'=> __( 'User balance is too low — sync wallet or reject the request.', 'free-backlinks-generator' ),
			'rej_fbg_payout_bad'=> __( 'Could not reject (invalid state).', 'free-backlinks-generator' ),
		);
		if ( isset( $map[ $err ] ) ) {
			echo '<div class="notice notice-error is-dismissible"><p>' . esc_html( $map[ $err ] ) . '</p></div>';
		}
	}

	$pending = function_exists( 'fbg_earn_get_pending_requests' ) ? fbg_earn_get_pending_requests() : array();
	$rows    = function_exists( 'fbg_earn_admin_partner_balances' ) ? fbg_earn_admin_partner_balances( 250 ) : array();
	$min     = function_exists( 'fbg_earn_min_payout_usd' ) ? fbg_earn_min_payout_usd() : 25;
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Earn & Payouts', 'free-backlinks-generator' ); ?></h1>
		<p class="description">
			<?php
			printf(
				/* translators: %s: minimum USD */
				esc_html__( 'Members request payouts from their Earn tab when balance meets the minimum (%s). Paying deducts their in-app wallet and logs a ledger entry.', 'free-backlinks-generator' ),
				'$' . esc_html( number_format_i18n( $min, 2 ) )
			);
			?>
			<?php
			printf(
				' <a href="%s">%s</a>',
				esc_url( admin_url( 'customize.php?autofocus[section]=fbg_pro_membership&autofocus[panel]=fbg_theme_panel' ) ),
				esc_html__( 'Change minimum in Customizer', 'free-backlinks-generator' )
			);
			?>
		</p>

		<h2><?php esc_html_e( 'Payout queue', 'free-backlinks-generator' ); ?></h2>
		<?php if ( empty( $pending ) ) : ?>
			<p><?php esc_html_e( 'No pending payout requests.', 'free-backlinks-generator' ); ?></p>
		<?php else : ?>
			<table class="widefat striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'ID', 'free-backlinks-generator' ); ?></th>
						<th><?php esc_html_e( 'User', 'free-backlinks-generator' ); ?></th>
						<th><?php esc_html_e( 'Amount', 'free-backlinks-generator' ); ?></th>
						<th><?php esc_html_e( 'Requested', 'free-backlinks-generator' ); ?></th>
						<th><?php esc_html_e( 'Member note', 'free-backlinks-generator' ); ?></th>
						<th><?php esc_html_e( 'Actions', 'free-backlinks-generator' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $pending as $p ) : ?>
						<?php
						$u = get_userdata( (int) $p->user_id );
						?>
						<tr>
							<td><?php echo esc_html( (string) (int) $p->id ); ?></td>
							<td>
								<?php if ( $u ) : ?>
									<a href="<?php echo esc_url( get_edit_user_link( $u->ID ) ); ?>"><?php echo esc_html( $u->user_login ); ?></a>
									<br><span class="description"><?php echo esc_html( $u->user_email ); ?></span>
								<?php else : ?>
									—
								<?php endif; ?>
							</td>
							<td><strong>$<?php echo esc_html( number_format_i18n( (float) $p->amount_usd, 2 ) ); ?></strong></td>
							<td><?php echo esc_html( wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $p->created_at . ' UTC' ) ) ); ?></td>
							<td><?php echo $p->user_note ? esc_html( wp_trim_words( (string) $p->user_note, 20 ) ) : '—'; ?></td>
							<td>
								<form method="post" style="margin-bottom:8px;" onsubmit="return confirm('<?php echo esc_js( __( 'Mark this payout as paid and deduct the wallet?', 'free-backlinks-generator' ) ); ?>');">
									<?php wp_nonce_field( 'fbg_earn_admin' ); ?>
									<input type="hidden" name="fbg_earn_payout_action" value="mark_paid">
									<input type="hidden" name="request_id" value="<?php echo esc_attr( (string) (int) $p->id ); ?>">
									<input type="text" name="payment_ref" class="regular-text" placeholder="<?php esc_attr_e( 'Payment reference / txn id', 'free-backlinks-generator' ); ?>" required>
									<textarea name="admin_note" rows="2" class="large-text" placeholder="<?php esc_attr_e( 'Optional note to member', 'free-backlinks-generator' ); ?>"></textarea>
									<?php submit_button( __( 'Mark paid', 'free-backlinks-generator' ), 'primary small', 'submit', false ); ?>
								</form>
								<form method="post" onsubmit="return confirm('<?php echo esc_js( __( 'Reject this request?', 'free-backlinks-generator' ) ); ?>');">
									<?php wp_nonce_field( 'fbg_earn_admin' ); ?>
									<input type="hidden" name="fbg_earn_payout_action" value="reject">
									<input type="hidden" name="request_id" value="<?php echo esc_attr( (string) (int) $p->id ); ?>">
									<textarea name="admin_note" rows="2" class="large-text" placeholder="<?php esc_attr_e( 'Reason (optional)', 'free-backlinks-generator' ); ?>"></textarea>
									<?php submit_button( __( 'Reject', 'free-backlinks-generator' ), 'delete small', 'submit', false ); ?>
								</form>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>

		<h2 style="margin-top:2em;"><?php esc_html_e( 'Partner wallets & writing access', 'free-backlinks-generator' ); ?></h2>
		<p class="description"><?php esc_html_e( 'Active partners: USD wallet (organic milestones), pending payout holds, lifetime paid out, and bonus guest-post slots from referral traffic.', 'free-backlinks-generator' ); ?></p>
		<table class="widefat striped">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Partner', 'free-backlinks-generator' ); ?></th>
					<th><?php esc_html_e( 'Wallet', 'free-backlinks-generator' ); ?></th>
					<th><?php esc_html_e( 'Pending payout', 'free-backlinks-generator' ); ?></th>
					<th><?php esc_html_e( 'Lifetime paid', 'free-backlinks-generator' ); ?></th>
					<th><?php esc_html_e( 'Bonus slots', 'free-backlinks-generator' ); ?></th>
					<th><?php esc_html_e( 'Referrals', 'free-backlinks-generator' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( empty( $rows ) ) : ?>
					<tr><td colspan="6"><?php esc_html_e( 'No active partners yet.', 'free-backlinks-generator' ); ?></td></tr>
				<?php else : ?>
					<?php foreach ( $rows as $row ) : ?>
						<?php $u = $row['user']; ?>
						<tr>
							<td>
								<a href="<?php echo esc_url( get_edit_user_link( $u->ID ) ); ?>"><?php echo esc_html( $u->user_login ); ?></a>
							</td>
							<td>$<?php echo esc_html( number_format_i18n( $row['balance'], 2 ) ); ?></td>
							<td>$<?php echo esc_html( number_format_i18n( $row['pending'], 2 ) ); ?></td>
							<td>$<?php echo esc_html( number_format_i18n( $row['paid'], 2 ) ); ?></td>
							<td><?php echo esc_html( (string) (int) $row['slots'] ); ?></td>
							<td><?php echo esc_html( number_format_i18n( $row['refs'] ) ); ?></td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
	<?php
}
