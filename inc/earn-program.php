<?php
/**
 * Earn center: ledger, referral month stats, payout requests.
 *
 * @package Free_Backlinks_Generator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Ledger table name.
 *
 * @return string
 */
function fbg_earn_ledger_table() {
	global $wpdb;
	return $wpdb->prefix . 'fbg_earn_ledger';
}

/**
 * Payout requests table name.
 *
 * @return string
 */
function fbg_earn_payout_requests_table() {
	global $wpdb;
	return $wpdb->prefix . 'fbg_payout_requests';
}

/**
 * Create earn / payout tables.
 */
function fbg_create_earn_tables() {
	global $wpdb;
	$charset = $wpdb->get_charset_collate();
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	$ledger  = fbg_earn_ledger_table();
	$payouts = fbg_earn_payout_requests_table();

	$sql1 = "CREATE TABLE {$ledger} (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		user_id bigint(20) unsigned NOT NULL DEFAULT 0,
		entry_type varchar(40) NOT NULL DEFAULT '',
		amount_usd decimal(12,4) NOT NULL DEFAULT 0,
		slots_delta int NOT NULL DEFAULT 0,
		context text,
		created_at datetime NOT NULL,
		PRIMARY KEY  (id),
		KEY user_created (user_id, created_at),
		KEY entry_type (entry_type)
	) {$charset};";

	$sql2 = "CREATE TABLE {$payouts} (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		user_id bigint(20) unsigned NOT NULL DEFAULT 0,
		amount_usd decimal(12,4) NOT NULL DEFAULT 0,
		status varchar(20) NOT NULL DEFAULT 'pending',
		user_note text,
		admin_note text,
		payment_ref varchar(190) NOT NULL DEFAULT '',
		processed_by bigint(20) unsigned DEFAULT NULL,
		created_at datetime NOT NULL,
		updated_at datetime NOT NULL,
		PRIMARY KEY  (id),
		KEY user_status (user_id, status),
		KEY status_created (status, created_at)
	) {$charset};";

	dbDelta( $sql1 );
	dbDelta( $sql2 );
}

/**
 * Minimum USD before requesting payout (Customizer).
 *
 * @return float
 */
function fbg_earn_min_payout_usd() {
	$n = (float) get_theme_mod( 'fbg_earn_min_payout_usd', 25 );
	return min( 10000, max( 1, $n ) );
}

/**
 * Bump per-month referral count JSON for charts.
 *
 * @param int $user_id User ID.
 * @return void
 */
function fbg_earn_bump_referral_month_stat( $user_id ) {
	$user_id = (int) $user_id;
	if ( $user_id < 1 ) {
		return;
	}
	$ym = gmdate( 'Y-m' );
	$raw = get_user_meta( $user_id, '_fbg_aff_referrals_by_month', true );
	$map = array();
	if ( is_array( $raw ) ) {
		$map = $raw;
	} elseif ( is_string( $raw ) && $raw !== '' ) {
		$decoded = json_decode( $raw, true );
		if ( is_array( $decoded ) ) {
			$map = $decoded;
		}
	}
	if ( ! isset( $map[ $ym ] ) ) {
		$map[ $ym ] = 0;
	}
	++$map[ $ym ];
	// Keep last 24 month keys max.
	ksort( $map );
	if ( count( $map ) > 24 ) {
		$map = array_slice( $map, -24, null, true );
	}
	update_user_meta( $user_id, '_fbg_aff_referrals_by_month', wp_json_encode( $map ) );
}

/**
 * Last N months of referral counts for chart (newest last).
 *
 * @param int $user_id User ID.
 * @param int $months  Number of months.
 * @return array<string, array{label:string,count:int}>
 */
function fbg_earn_get_referral_chart_series( $user_id, $months = 6 ) {
	$user_id = (int) $user_id;
	$months  = max( 1, min( 24, $months ) );
	$raw     = get_user_meta( $user_id, '_fbg_aff_referrals_by_month', true );
	$map     = array();
	if ( is_array( $raw ) ) {
		$map = $raw;
	} elseif ( is_string( $raw ) && $raw !== '' ) {
		$d = json_decode( $raw, true );
		if ( is_array( $d ) ) {
			$map = $d;
		}
	}
	$out   = array();
	$start = new DateTime( 'first day of this month', wp_timezone() );
	$start->modify( '-' . ( $months - 1 ) . ' months' );
	for ( $i = 0; $i < $months; $i++ ) {
		$key = $start->format( 'Y-m' );
		$cnt = isset( $map[ $key ] ) ? (int) $map[ $key ] : 0;
		$out[] = array(
			'key'   => $key,
			'label' => $start->format( 'M' ),
			'count' => $cnt,
		);
		$start->modify( '+1 month' );
	}
	return $out;
}

/**
 * Insert ledger row.
 *
 * @param int    $user_id    User ID.
 * @param string $entry_type Type slug.
 * @param float  $amount_usd Amount (negative for payout debit).
 * @param int    $slots_delta Slots change.
 * @param string $context    Optional note / JSON.
 * @return void
 */
function fbg_earn_ledger_insert( $user_id, $entry_type, $amount_usd = 0.0, $slots_delta = 0, $context = '' ) {
	global $wpdb;
	$user_id = (int) $user_id;
	if ( $user_id < 1 ) {
		return;
	}
	$table = fbg_earn_ledger_table();
	// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name from prefix.
	$wpdb->insert(
		$table,
		array(
			'user_id'     => $user_id,
			'entry_type'  => sanitize_key( $entry_type ),
			'amount_usd'  => round( (float) $amount_usd, 4 ),
			'slots_delta' => (int) $slots_delta,
			'context'     => is_string( $context ) ? wp_kses_post( $context ) : '',
			'created_at'  => current_time( 'mysql', true ),
		),
		array( '%d', '%s', '%f', '%d', '%s', '%s' )
	);
}

/**
 * After affiliate engagement: monthly stat + optional ledger for cash/slots.
 *
 * @param int   $aff_id       Affiliate user ID.
 * @param float $usd_credit   USD added this event.
 * @param int   $slots_credit Slots added this event.
 * @return void
 */
function fbg_earn_on_affiliate_rewards( $aff_id, $usd_credit, $slots_credit ) {
	$aff_id = (int) $aff_id;
	if ( $aff_id < 1 || ! fbg_is_user_affiliate_partner( $aff_id ) ) {
		return;
	}
	fbg_earn_bump_referral_month_stat( $aff_id );
	if ( $usd_credit > 0 ) {
		fbg_earn_ledger_insert(
			$aff_id,
			'organic_milestone_usd',
			$usd_credit,
			0,
			__( 'Organic search referral milestone bonus', 'free-backlinks-generator' )
		);
	}
	if ( $slots_credit > 0 ) {
		fbg_earn_ledger_insert(
			$aff_id,
			'referral_slots',
			0,
			$slots_credit,
			__( 'Bonus guest-post slots from referral traffic milestones', 'free-backlinks-generator' )
		);
	}
}
add_action( 'fbg_affiliate_rewards_credited', 'fbg_earn_on_affiliate_rewards', 10, 3 );

/**
 * Recent ledger rows for dashboard.
 *
 * @param int $user_id User ID.
 * @param int $limit   Max rows.
 * @return array<int, object>
 */
function fbg_earn_get_user_ledger( $user_id, $limit = 25 ) {
	global $wpdb;
	$user_id = (int) $user_id;
	$limit   = max( 1, min( 100, $limit ) );
	$table   = fbg_earn_ledger_table();
	// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	return $wpdb->get_results(
		$wpdb->prepare(
			"SELECT * FROM {$table} WHERE user_id = %d ORDER BY id DESC LIMIT %d",
			$user_id,
			$limit
		)
	);
}

/**
 * Sum of pending payout request amounts for user.
 *
 * @param int $user_id User ID.
 * @return float
 */
function fbg_earn_user_pending_payout_sum( $user_id ) {
	global $wpdb;
	$user_id = (int) $user_id;
	$table   = fbg_earn_payout_requests_table();
	// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$sum = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT COALESCE(SUM(amount_usd),0) FROM {$table} WHERE user_id = %d AND status = %s",
			$user_id,
			'pending'
		)
	);
	return (float) $sum;
}

/**
 * Wallet balance available for new payout requests (balance minus pending).
 *
 * @param int $user_id User ID.
 * @return float
 */
function fbg_earn_user_spendable_balance( $user_id ) {
	$bal = (float) get_user_meta( $user_id, '_fbg_aff_balance_usd', true );
	return max( 0, $bal - fbg_earn_user_pending_payout_sum( $user_id ) );
}

/**
 * Lifetime total paid out (user meta).
 *
 * @param int $user_id User ID.
 * @return float
 */
function fbg_earn_user_lifetime_paid( $user_id ) {
	return max( 0, (float) get_user_meta( $user_id, '_fbg_aff_lifetime_paid_usd', true ) );
}

/**
 * AJAX: request payout.
 */
function fbg_ajax_earn_request_payout() {
	check_ajax_referer( 'fbg_dashboard_nonce', 'nonce' );
	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => __( 'Not logged in.', 'free-backlinks-generator' ) ) );
	}
	$uid = get_current_user_id();
	if ( ! fbg_is_user_affiliate_partner( $uid ) ) {
		wp_send_json_error( array( 'message' => __( 'Only approved partners can request payouts.', 'free-backlinks-generator' ) ) );
	}
	$amount = isset( $_POST['amount'] ) ? (float) wp_unslash( $_POST['amount'] ) : 0;
	$amount = round( $amount, 2 );
	$note   = isset( $_POST['note'] ) ? sanitize_textarea_field( wp_unslash( $_POST['note'] ) ) : '';
	$min    = fbg_earn_min_payout_usd();
	if ( $amount < $min ) {
		wp_send_json_error(
			array(
				'message' => sprintf(
					/* translators: %s: minimum USD */
					__( 'Minimum payout is %s.', 'free-backlinks-generator' ),
					'$' . number_format_i18n( $min, 2 )
				),
			)
		);
	}
	$max = fbg_earn_user_spendable_balance( $uid );
	if ( $amount > $max + 0.0001 ) {
		wp_send_json_error( array( 'message' => __( 'That amount exceeds your available balance (minus pending requests).', 'free-backlinks-generator' ) ) );
	}
	global $wpdb;
	$table = fbg_earn_payout_requests_table();
	$now   = current_time( 'mysql', true );
	$ins = $wpdb->insert(
		$table,
		array(
			'user_id'      => $uid,
			'amount_usd'   => $amount,
			'status'       => 'pending',
			'user_note'    => $note,
			'admin_note'   => '',
			'payment_ref'  => '',
			'processed_by' => 0,
			'created_at'   => $now,
			'updated_at'   => $now,
		),
		array( '%d', '%f', '%s', '%s', '%s', '%s', '%d', '%s', '%s' )
	);
	if ( false === $ins ) {
		wp_send_json_error( array( 'message' => __( 'Could not save request. Try again or contact support.', 'free-backlinks-generator' ) ) );
	}
	if ( function_exists( 'fbg_create_notification' ) ) {
		fbg_create_notification(
			$uid,
			'earn_payout_requested',
			sprintf(
				/* translators: %s: formatted amount */
				__( 'Payout request submitted for %s. We will review and pay per program terms.', 'free-backlinks-generator' ),
				'$' . number_format_i18n( $amount, 2 )
			),
			null
		);
	}
	wp_send_json_success( array( 'message' => __( 'Payout request submitted.', 'free-backlinks-generator' ) ) );
}
add_action( 'wp_ajax_fbg_earn_request_payout', 'fbg_ajax_earn_request_payout' );

/**
 * Mark payout request paid: deduct wallet, ledger, lifetime paid, notify user.
 *
 * @param int    $request_id  Row ID.
 * @param int    $admin_id    Admin user ID.
 * @param string $payment_ref Reference shown to member.
 * @param string $admin_note  Internal / member-visible note.
 * @return true|WP_Error
 */
function fbg_earn_mark_request_paid( $request_id, $admin_id, $payment_ref, $admin_note ) {
	global $wpdb;
	$request_id = (int) $request_id;
	$admin_id   = (int) $admin_id;
	$table      = fbg_earn_payout_requests_table();
	// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $request_id ) );
	if ( ! $row || 'pending' !== $row->status ) {
		return new WP_Error( 'fbg_payout_bad', __( 'Invalid or non-pending payout request.', 'free-backlinks-generator' ) );
	}
	$uid    = (int) $row->user_id;
	$amount = (float) $row->amount_usd;
	$bal    = (float) get_user_meta( $uid, '_fbg_aff_balance_usd', true );
	if ( $amount > $bal + 0.0001 ) {
		return new WP_Error( 'fbg_payout_funds', __( 'User wallet balance is lower than this request. Adjust balance or reject the request.', 'free-backlinks-generator' ) );
	}
	$new_bal = round( $bal - $amount, 4 );
	update_user_meta( $uid, '_fbg_aff_balance_usd', $new_bal );
	$life = fbg_earn_user_lifetime_paid( $uid ) + $amount;
	update_user_meta( $uid, '_fbg_aff_lifetime_paid_usd', $life );

	fbg_earn_ledger_insert(
		$uid,
		'payout_paid',
		-1 * abs( $amount ),
		0,
		sprintf(
			/* translators: 1: payment ref, 2: request id */
			__( 'Payout #%2$d paid (%1$s)', 'free-backlinks-generator' ),
			sanitize_text_field( $payment_ref ),
			$request_id
		)
	);

	$now = current_time( 'mysql', true );
	$wpdb->update(
		$table,
		array(
			'status'       => 'paid',
			'payment_ref'  => sanitize_text_field( $payment_ref ),
			'admin_note'   => sanitize_textarea_field( $admin_note ),
			'processed_by' => $admin_id,
			'updated_at'   => $now,
		),
		array( 'id' => $request_id ),
		array( '%s', '%s', '%s', '%d', '%s' ),
		array( '%d' )
	);

	if ( function_exists( 'fbg_create_notification' ) ) {
		fbg_create_notification(
			$uid,
			'earn_payout_paid',
			sprintf(
				/* translators: 1: amount, 2: reference */
				__( 'Your payout of %1$s was marked paid. Reference: %2$s', 'free-backlinks-generator' ),
				'$' . number_format_i18n( $amount, 2 ),
				sanitize_text_field( $payment_ref )
			),
			null
		);
	}

	return true;
}

/**
 * Reject payout request (no balance change).
 *
 * @param int    $request_id Request ID.
 * @param int    $admin_id   Admin user ID.
 * @param string $admin_note Reason.
 * @return true|WP_Error
 */
function fbg_earn_mark_request_rejected( $request_id, $admin_id, $admin_note ) {
	global $wpdb;
	$request_id = (int) $request_id;
	$admin_id   = (int) $admin_id;
	$table      = fbg_earn_payout_requests_table();
	// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $request_id ) );
	if ( ! $row || 'pending' !== $row->status ) {
		return new WP_Error( 'fbg_payout_bad', __( 'Invalid or non-pending payout request.', 'free-backlinks-generator' ) );
	}
	$uid = (int) $row->user_id;
	$now = current_time( 'mysql', true );
	$wpdb->update(
		$table,
		array(
			'status'       => 'rejected',
			'admin_note'   => sanitize_textarea_field( $admin_note ),
			'processed_by' => $admin_id,
			'updated_at'   => $now,
		),
		array( 'id' => $request_id ),
		array( '%s', '%s', '%d', '%s' ),
		array( '%d' )
	);
	if ( function_exists( 'fbg_create_notification' ) ) {
		fbg_create_notification(
			$uid,
			'earn_payout_rejected',
			__( 'Your payout request was not approved. Check the note from our team or contact support.', 'free-backlinks-generator' ),
			null
		);
	}
	return true;
}

/**
 * Pending payout requests (admin list).
 *
 * @return array<int, object>
 */
function fbg_earn_get_pending_requests() {
	global $wpdb;
	$table = fbg_earn_payout_requests_table();
	// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	return $wpdb->get_results( "SELECT * FROM {$table} WHERE status = 'pending' ORDER BY created_at ASC" );
}

/**
 * Partner balances snapshot for admin table.
 *
 * @param int $limit Max users.
 * @return array<int, array<string, mixed>>
 */
/**
 * Human label for ledger entry_type.
 *
 * @param string $type Type slug.
 * @return string
 */
function fbg_earn_ledger_type_label( $type ) {
	$labels = array(
		'organic_milestone_usd' => __( 'Organic milestone (USD)', 'free-backlinks-generator' ),
		'referral_slots'        => __( 'Bonus writing slots (referrals)', 'free-backlinks-generator' ),
		'payout_paid'           => __( 'Payout sent', 'free-backlinks-generator' ),
	);
	return isset( $labels[ $type ] ) ? $labels[ $type ] : $type;
}

/**
 * @param int $limit Max users.
 * @return array<int, array<string, mixed>>
 */
function fbg_earn_admin_partner_balances( $limit = 200 ) {
	$q = new WP_User_Query(
		array(
			'meta_key'     => '_fbg_affiliate_active',
			'meta_value'   => 'yes',
			'number'       => $limit,
			'orderby'      => 'ID',
			'order'        => 'ASC',
			'count_total'  => false,
			'fields'       => 'all',
		)
	);
	$out = array();
	foreach ( $q->get_results() as $u ) {
		$uid = (int) $u->ID;
		$out[] = array(
			'user'    => $u,
			'balance' => (float) get_user_meta( $uid, '_fbg_aff_balance_usd', true ),
			'pending' => fbg_earn_user_pending_payout_sum( $uid ),
			'paid'    => fbg_earn_user_lifetime_paid( $uid ),
			'slots'   => fbg_get_user_bonus_post_slots( $uid ),
			'refs'    => (int) get_user_meta( $uid, '_fbg_aff_referral_total', true ),
		);
	}
	return $out;
}
