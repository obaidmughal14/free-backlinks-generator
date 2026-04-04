<?php
/**
 * Peer reading time (unlock guest-post slots) + affiliate referral tracking.
 *
 * @package Free_Backlinks_Generator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Cookie name for affiliate attribution. */
const FBG_AFF_COOKIE = 'fbg_aff_ref';

/** Seconds of active reading required per community post. */
const FBG_READ_SECONDS_REQUIRED = 120;

/** Initial free guest posts for new members (before read bonuses). */
const FBG_BASE_POST_SLOTS = 1;

/** Peer posts fully read per +1 slot (after base). */
const FBG_READS_PER_SLOT_PAIR = 2;

/**
 * Count guest posts that count toward the member cap (non-trash).
 *
 * @param int $user_id User ID.
 * @return int
 */
function fbg_count_user_guest_posts_for_cap( $user_id ) {
	$q = new WP_Query(
		array(
			'post_type'              => 'fbg_post',
			'author'                 => (int) $user_id,
			'post_status'            => array( 'publish', 'pending', 'draft', 'future', 'private' ),
			'posts_per_page'         => -1,
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache'   => false,
		)
	);
	return (int) $q->found_posts;
}

/**
 * Post IDs this user has fully read (>= required active seconds each).
 *
 * @param int $user_id User ID.
 * @return int[]
 */
function fbg_get_user_completed_peer_reads( $user_id ) {
	$raw = get_user_meta( $user_id, '_fbg_read_completed', true );
	if ( ! is_array( $raw ) ) {
		return array();
	}
	return array_values( array_unique( array_map( 'absint', $raw ) ) );
}

/**
 * Bonus slots from affiliates / admin grants.
 *
 * @param int $user_id User ID.
 * @return int
 */
function fbg_get_user_bonus_post_slots( $user_id ) {
	return max( 0, (int) get_user_meta( $user_id, '_fbg_bonus_post_slots', true ) );
}

/**
 * Max guest posts (draft + pending + published…) this user may have at once.
 *
 * @param int $user_id User ID.
 * @return int
 */
function fbg_get_user_post_slot_limit( $user_id ) {
	$user = get_userdata( $user_id );
	if ( ! $user ) {
		return 0;
	}
	if ( user_can( $user_id, 'manage_options' ) ) {
		return 999;
	}
	if ( 'pro' === get_user_meta( $user_id, '_fbg_membership', true ) ) {
		return 999;
	}
	$n_reads = count( fbg_get_user_completed_peer_reads( $user_id ) );
	$from_reads = (int) floor( $n_reads / FBG_READS_PER_SLOT_PAIR );
	return FBG_BASE_POST_SLOTS + $from_reads + fbg_get_user_bonus_post_slots( $user_id );
}

/**
 * Whether the user may create another guest post.
 *
 * @param int $user_id User ID.
 * @return bool
 */
function fbg_user_can_create_guest_post( $user_id ) {
	return fbg_count_user_guest_posts_for_cap( $user_id ) < fbg_get_user_post_slot_limit( $user_id );
}

/**
 * Referrer looks like organic search.
 *
 * @return bool
 */
function fbg_request_is_organic_referrer() {
	if ( empty( $_SERVER['HTTP_REFERER'] ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		return false;
	}
	$ref = esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$host = wp_parse_url( $ref, PHP_URL_HOST );
	if ( ! is_string( $host ) || '' === $host ) {
		return false;
	}
	$host = strtolower( preg_replace( '/^www\./', '', $host ) );
	$needles = array( 'google.', 'bing.', 'yahoo.', 'duckduckgo.', 'yandex.', 'baidu.', 'ecosia.', 'startpage.' );
	foreach ( $needles as $n ) {
		if ( str_contains( $host, $n ) ) {
			return true;
		}
	}
	return false;
}

/**
 * Store ?fbg_ref= user id in cookie (90 days).
 *
 * @return void
 */
function fbg_capture_affiliate_ref() {
	if ( is_admin() || wp_doing_ajax() || wp_doing_cron() ) {
		return;
	}
	if ( empty( $_GET['fbg_ref'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}
	$id = absint( wp_unslash( $_GET['fbg_ref'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( $id < 1 || ! get_userdata( $id ) ) {
		return;
	}
	$path   = ( defined( 'COOKIEPATH' ) && COOKIEPATH ) ? COOKIEPATH : '/';
	$domain = ( defined( 'COOKIE_DOMAIN' ) && COOKIE_DOMAIN ) ? COOKIE_DOMAIN : '';
	setcookie(
		FBG_AFF_COOKIE,
		(string) $id,
		array(
			'expires'  => time() + 90 * DAY_IN_SECONDS,
			'path'     => $path,
			'domain'   => $domain,
			'secure'   => is_ssl(),
			'httponly' => true,
			'samesite' => 'Lax',
		)
	);
	// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
	$_COOKIE[ FBG_AFF_COOKIE ] = (string) $id;
}
add_action( 'init', 'fbg_capture_affiliate_ref', 1 );

/**
 * Whether this user is allowed to earn affiliate referrals (admin-approved partner).
 *
 * @param int $user_id User ID.
 * @return bool
 */
function fbg_is_user_affiliate_partner( $user_id ) {
	$user_id = (int) $user_id;
	if ( $user_id < 1 || ! get_userdata( $user_id ) ) {
		return false;
	}
	$status = get_user_meta( $user_id, '_fbg_affiliate_active', true );
	if ( 'no' === $status ) {
		return false;
	}
	if ( 'yes' === $status ) {
		return true;
	}
	// Legacy: already had attributed traffic before partner flag existed.
	return (int) get_user_meta( $user_id, '_fbg_aff_referral_total', true ) > 0;
}

/**
 * Affiliate ID from cookie.
 *
 * @return int
 */
function fbg_get_affiliate_id_from_cookie() {
	if ( empty( $_COOKIE[ FBG_AFF_COOKIE ] ) ) {
		return 0;
	}
	return absint( wp_unslash( $_COOKIE[ FBG_AFF_COOKIE ] ) );
}

/**
 * Public referral URL for a partner (?fbg_ref= user id, sets attribution cookie).
 *
 * @param int $user_id Affiliate WordPress user ID.
 * @return string
 */
function fbg_get_user_affiliate_referral_url( $user_id ) {
	$uid = absint( $user_id );
	if ( $uid < 1 ) {
		return home_url( '/' );
	}
	return add_query_arg( 'fbg_ref', $uid, home_url( '/' ) );
}

/**
 * Same as referral URL plus UTM parameters for analytics (unique campaign per partner).
 *
 * @param int $user_id Affiliate WordPress user ID.
 * @return string
 */
function fbg_get_user_affiliate_utm_url( $user_id ) {
	$uid = absint( $user_id );
	if ( $uid < 1 ) {
		return home_url( '/' );
	}
	$base = fbg_get_user_affiliate_referral_url( $uid );
	return add_query_arg(
		array(
			'utm_source'   => 'fbg_partner',
			'utm_medium'   => 'referral',
			'utm_campaign' => 'fbg_ref_' . $uid,
		),
		$base
	);
}

/**
 * Credit affiliate for a referred engagement (blog view, contact, etc.).
 *
 * @param int  $aff_id  Affiliate user ID.
 * @param bool $organic Whether this hit counts as organic search traffic.
 * @return void
 */
function fbg_affiliate_add_engagement( $aff_id, $organic ) {
	$aff_id = (int) $aff_id;
	if ( $aff_id < 1 || ! get_userdata( $aff_id ) || ! fbg_is_user_affiliate_partner( $aff_id ) ) {
		return;
	}

	$usd_credit   = 0.0;
	$slots_credit = 0;

	$total = (int) get_user_meta( $aff_id, '_fbg_aff_referral_total', true );
	++$total;
	update_user_meta( $aff_id, '_fbg_aff_referral_total', $total );

	if ( $organic ) {
		$org = (int) get_user_meta( $aff_id, '_fbg_aff_referral_organic', true );
		++$org;
		update_user_meta( $aff_id, '_fbg_aff_referral_organic', $org );

		$paid_blocks = (int) get_user_meta( $aff_id, '_fbg_aff_organic_blocks_paid', true );
		$blocks      = (int) floor( $org / 1000 );
		if ( $blocks > $paid_blocks ) {
			$add   = $blocks - $paid_blocks;
			$owed  = (float) get_user_meta( $aff_id, '_fbg_aff_balance_usd', true );
			$owed += 2.0 * $add;
			update_user_meta( $aff_id, '_fbg_aff_balance_usd', $owed );
			update_user_meta( $aff_id, '_fbg_aff_organic_blocks_paid', $blocks );
			$usd_credit = 2.0 * $add;
			if ( function_exists( 'fbg_create_notification' ) ) {
				fbg_create_notification(
					$aff_id,
					'affiliate_earn',
					sprintf(
						/* translators: 1: USD amount, 2: organic referral count */
						__( 'Affiliate: $%1$s credited for organic referral milestones (%2$d organic visits logged).', 'free-backlinks-generator' ),
						number_format_i18n( 2 * $add, 2 ),
						$org
					),
					null
				);
			}
		}
	}

	$slot_blocks_paid = (int) get_user_meta( $aff_id, '_fbg_aff_slot_blocks_paid', true );
	$tblocks          = (int) floor( $total / 1000 );
	if ( $tblocks > $slot_blocks_paid ) {
		$add_slots = ( $tblocks - $slot_blocks_paid ) * 10;
		$bonus     = fbg_get_user_bonus_post_slots( $aff_id );
		update_user_meta( $aff_id, '_fbg_bonus_post_slots', $bonus + $add_slots );
		update_user_meta( $aff_id, '_fbg_aff_slot_blocks_paid', $tblocks );
		$slots_credit = $add_slots;
		if ( function_exists( 'fbg_create_notification' ) ) {
			fbg_create_notification(
				$aff_id,
				'affiliate_slots',
				sprintf(
					/* translators: %d: bonus slots */
					__( 'You earned %d bonus guest-post writing slots from referred visits (read or contact).', 'free-backlinks-generator' ),
					$add_slots
				),
				null
			);
		}
	}

	/**
	 * Log earn-program ledger + monthly chart (usd_credit, slots_credit may be zero).
	 *
	 * @param int   $aff_id       Affiliate user ID.
	 * @param float $usd_credit   USD added this hit’s processing.
	 * @param int   $slots_credit Slots added this hit’s processing.
	 */
	do_action( 'fbg_affiliate_rewards_credited', $aff_id, $usd_credit, $slots_credit );
}

/**
 * Log one referred view of a community post (deduped per IP + post + day).
 *
 * @return void
 */
function fbg_affiliate_track_referred_view() {
	if ( ! is_singular( 'fbg_post' ) || is_preview() || is_feed() ) {
		return;
	}
	$aff_id = fbg_get_affiliate_id_from_cookie();
	if ( $aff_id < 1 ) {
		return;
	}
	$post_id = (int) get_queried_object_id();
	if ( $post_id < 1 || 'publish' !== get_post_status( $post_id ) ) {
		return;
	}
	$viewer = get_current_user_id();
	if ( $viewer === $aff_id ) {
		return;
	}

	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '0';
	$key = 'fbg_aff_view_' . md5( $aff_id . '_' . $post_id . '_' . $ip );
	if ( get_transient( $key ) ) {
		return;
	}
	set_transient( $key, 1, DAY_IN_SECONDS );

	fbg_affiliate_add_engagement( $aff_id, fbg_request_is_organic_referrer() );
}
add_action( 'template_redirect', 'fbg_affiliate_track_referred_view', 20 );
