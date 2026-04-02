<?php
/**
 * Helper functions: tiers, notifications, emails, stats.
 *
 * @package Free_Backlinks_Generator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Tier configuration.
 *
 * @return array<string, array<string, mixed>>
 */
function fbg_tier_config() {
	return array(
		'seedling' => array(
			'label' => __( 'Seedling', 'free-backlinks-generator' ),
			'emoji' => '🌱',
			'min_posts' => 0,
			'max_links_per_post' => 3,
		),
		'root'     => array(
			'label' => __( 'Root', 'free-backlinks-generator' ),
			'emoji' => '🌿',
			'min_posts' => 3,
			'max_links_per_post' => 4,
		),
		'branch'   => array(
			'label' => __( 'Branch', 'free-backlinks-generator' ),
			'emoji' => '🌳',
			'min_posts' => 8,
			'max_links_per_post' => 5,
		),
		'oak'      => array(
			'label' => __( 'Oak', 'free-backlinks-generator' ),
			'emoji' => '🌲',
			'min_posts' => 15,
			'max_links_per_post' => 6,
		),
		'canopy'   => array(
			'label' => __( 'Canopy', 'free-backlinks-generator' ),
			'emoji' => '🏆',
			'min_posts' => 30,
			'max_links_per_post' => 10,
		),
	);
}

/**
 * Approved post count for tier calculation.
 *
 * @param int $user_id User ID.
 * @return int
 */
function fbg_count_approved_posts_for_user( $user_id ) {
	$q = new WP_Query(
		array(
			'post_type'      => 'fbg_post',
			'post_status'    => 'publish',
			'author'         => (int) $user_id,
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'meta_query'     => array(
				array(
					'key'   => '_fbg_content_status',
					'value' => 'approved',
				),
			),
		)
	);
	return (int) $q->found_posts;
}

/**
 * Get user tier slug.
 *
 * @param int $user_id User ID.
 * @return string
 */
function fbg_get_user_tier( $user_id ) {
	$stored = get_user_meta( $user_id, '_fbg_tier', true );
	if ( $stored && is_string( $stored ) ) {
		return $stored;
	}
	return 'seedling';
}

/**
 * Recalculate tier from approved posts.
 *
 * @param int $user_id User ID.
 * @return string New tier slug.
 */
function fbg_recalculate_tier( $user_id ) {
	$count  = fbg_count_approved_posts_for_user( $user_id );
	$config = fbg_tier_config();
	$new    = 'seedling';
	$order  = array( 'canopy', 'oak', 'branch', 'root', 'seedling' );
	foreach ( $order as $slug ) {
		if ( isset( $config[ $slug ] ) && $count >= (int) $config[ $slug ]['min_posts'] ) {
			$new = $slug;
			break;
		}
	}
	$old = fbg_get_user_tier( $user_id );
	update_user_meta( $user_id, '_fbg_tier', $new );
	if ( $old !== $new ) {
		fbg_create_notification(
			$user_id,
			'tier_up',
			sprintf(
				/* translators: %s tier label */
				__( 'You reached a new community tier: %s', 'free-backlinks-generator' ),
				$config[ $new ]['label']
			),
			null
		);
	}
	return $new;
}

/**
 * Next tier progress.
 *
 * @param int $user_id User ID.
 * @return array{label:string,remaining:int,next_slug:string}|null
 */
function fbg_next_tier_info( $user_id ) {
	$current = fbg_get_user_tier( $user_id );
	$order   = array( 'seedling', 'root', 'branch', 'oak', 'canopy' );
	$idx     = array_search( $current, $order, true );
	if ( false === $idx || $idx >= count( $order ) - 1 ) {
		return null;
	}
	$next_slug = $order[ $idx + 1 ];
	$config    = fbg_tier_config();
	$count     = fbg_count_approved_posts_for_user( $user_id );
	$need      = (int) $config[ $next_slug ]['min_posts'];
	return array(
		'label'     => $config[ $next_slug ]['label'],
		'remaining' => max( 0, $need - $count ),
		'next_slug' => $next_slug,
	);
}

/**
 * Max backlinks per post for user (Pro override could hook here).
 *
 * @param int $user_id User ID.
 * @return int
 */
function fbg_get_user_link_limit( $user_id ) {
	$membership = get_user_meta( $user_id, '_fbg_membership', true );
	if ( 'pro' === $membership ) {
		return 999;
	}
	$tier   = fbg_get_user_tier( $user_id );
	$config = fbg_tier_config();
	return isset( $config[ $tier ] ) ? (int) $config[ $tier ]['max_links_per_post'] : 3;
}

/**
 * Notifications table name.
 *
 * @return string
 */
function fbg_notifications_table() {
	global $wpdb;
	return $wpdb->prefix . 'fbg_notifications';
}

/**
 * Create DB table.
 */
function fbg_create_notifications_table() {
	global $wpdb;
	$table   = fbg_notifications_table();
	$charset = $wpdb->get_charset_collate();
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$sql = "CREATE TABLE {$table} (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		user_id bigint(20) unsigned NOT NULL,
		type varchar(50) NOT NULL,
		message text NOT NULL,
		post_id bigint(20) unsigned DEFAULT NULL,
		is_read tinyint(1) DEFAULT 0,
		created_at datetime DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY  (id),
		KEY user_read (user_id, is_read),
		KEY created_at (created_at)
	) {$charset};";
	dbDelta( $sql );
}

/**
 * Insert notification.
 *
 * @param int         $user_id User ID.
 * @param string      $type    Type slug.
 * @param string      $message Message.
 * @param int|null    $post_id Post ID.
 * @return void
 */
function fbg_create_notification( $user_id, $type, $message, $post_id = null ) {
	global $wpdb;
	$table = fbg_notifications_table();
	$data  = array(
		'user_id'    => (int) $user_id,
		'type'       => sanitize_key( $type ),
		'message'    => wp_kses_post( $message ),
		'is_read'    => 0,
		'created_at' => current_time( 'mysql' ),
	);
	$format = array( '%d', '%s', '%s', '%d', '%s' );
	if ( null !== $post_id ) {
		$data['post_id'] = (int) $post_id;
		$format[]        = '%d';
	}
	$wpdb->insert( $table, $data, $format );
}

/**
 * Get notifications for user.
 *
 * @param int $user_id User ID.
 * @param int $limit   Limit.
 * @return array<int, object>
 */
function fbg_get_notifications( $user_id, $limit = 50 ) {
	global $wpdb;
	$table = fbg_notifications_table();
	// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name from trusted prefix.
	return $wpdb->get_results(
		$wpdb->prepare(
			"SELECT * FROM {$table} WHERE user_id = %d ORDER BY created_at DESC LIMIT %d",
			$user_id,
			$limit
		)
	);
}

/**
 * Unread notification count.
 *
 * @param int $user_id User ID.
 * @return int
 */
function fbg_unread_notification_count( $user_id ) {
	global $wpdb;
	$table = fbg_notifications_table();
	// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	return (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT COUNT(*) FROM {$table} WHERE user_id = %d AND is_read = 0",
			$user_id
		)
	);
}

/**
 * Unique username from email.
 *
 * @param string $email Email.
 * @return string
 */
function fbg_username_from_email( $email ) {
	$base = sanitize_user( current( explode( '@', $email ) ), true );
	if ( strlen( $base ) < 3 ) {
		$base = 'user';
	}
	$login = $base;
	$i     = 0;
	while ( username_exists( $login ) ) {
		++$i;
		$login = $base . $i;
	}
	return $login;
}

/**
 * Count words in HTML content (UTF-8 friendly).
 *
 * @param string $html HTML or text.
 * @return int
 */
function fbg_count_words_html( $html ) {
	$text = wp_strip_all_tags( (string) $html );
	$text = html_entity_decode( $text, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
	$text = preg_replace( '/[\p{Z}\s]+/u', ' ', $text );
	$text = trim( $text );
	if ( '' === $text ) {
		return 0;
	}
	$parts = preg_split( '/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY );
	return is_array( $parts ) ? count( $parts ) : 0;
}

/**
 * Whether user opted in for a notification email (default on).
 *
 * @param int    $user_id User ID.
 * @param string $key     Meta suffix: email_approved, email_rejected, etc.
 * @return bool
 */
function fbg_user_wants_email( $user_id, $key ) {
	$v = get_user_meta( (int) $user_id, '_fbg_pref_' . sanitize_key( $key ), true );
	return '0' !== $v;
}

/**
 * Wrap inner HTML in a branded email layout.
 *
 * @param string      $preheader Short preview line (hidden in inbox preview area).
 * @param string      $heading   Main headline.
 * @param string      $body_html Inner HTML (paragraphs, lists).
 * @param string      $btn_label Optional CTA label.
 * @param string      $btn_url   Optional CTA URL.
 * @param string|null $footer_note Optional line above site name.
 * @return string
 */
function fbg_email_html_layout( $preheader, $heading, $body_html, $btn_label = '', $btn_url = '', $footer_note = null ) {
	$blog   = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
	$home   = esc_url( home_url( '/' ) );
	$year   = gmdate( 'Y' );
	$pre    = esc_html( $preheader );
	$h      = esc_html( $heading );
	$btn_l  = esc_html( $btn_label );
	$btn_u  = esc_url( $btn_url );
	$note   = null !== $footer_note ? '<p style="margin:16px 0 0;font-size:13px;color:#64748b;">' . esc_html( $footer_note ) . '</p>' : '';

	$button_block = '';
	if ( $btn_label && $btn_url ) {
		$button_block = '<table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin:28px 0 8px;"><tr><td style="border-radius:10px;background:linear-gradient(135deg,#0d9488 0%,#6366f1 100%);"><a href="' . $btn_u . '" style="display:inline-block;padding:14px 28px;font-family:Segoe UI,Roboto,Helvetica,Arial,sans-serif;font-size:15px;font-weight:700;color:#ffffff;text-decoration:none;border-radius:10px;">' . $btn_l . '</a></td></tr></table>';
	}

	return '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width"><title>' . esc_html( $blog ) . '</title></head><body style="margin:0;padding:0;background:#f1f5f9;font-family:Segoe UI,Roboto,Helvetica,Arial,sans-serif;">
  <div style="display:none;max-height:0;overflow:hidden;">' . $pre . '</div>
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#f1f5f9;padding:32px 16px;">
    <tr><td align="center">
      <table role="presentation" width="100%" style="max-width:560px;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 12px 40px rgba(15,23,42,0.08);">
        <tr><td style="padding:28px 32px 20px;background:linear-gradient(135deg,#0f766e 0%,#312e81 100%);text-align:center;">
          <p style="margin:0;font-size:11px;letter-spacing:0.2em;text-transform:uppercase;color:rgba(255,255,255,0.75);">' . esc_html( $blog ) . '</p>
          <h1 style="margin:12px 0 0;font-size:22px;line-height:1.35;color:#ffffff;font-weight:800;">' . $h . '</h1>
        </td></tr>
        <tr><td style="padding:32px 32px 36px;color:#334155;font-size:16px;line-height:1.65;">
          ' . $body_html . '
          ' . $button_block . '
          ' . $note . '
          <p style="margin:28px 0 0;padding-top:24px;border-top:1px solid #e2e8f0;font-size:13px;color:#94a3b8;">&copy; ' . esc_html( $year ) . ' ' . esc_html( $blog ) . ' &middot; <a href="' . $home . '" style="color:#0d9488;">' . esc_html( $home ) . '</a></p>
        </td></tr>
      </table>
    </td></tr>
  </table>
</body></html>';
}

/**
 * Send HTML email with consistent headers.
 *
 * @param string|array<string> $to      Email(s).
 * @param string               $subject Subject line.
 * @param string               $html    HTML body.
 * @return bool
 */
function fbg_wp_mail_html( $to, $subject, $html ) {
	$headers = array( 'Content-Type: text/html; charset=UTF-8' );
	return wp_mail( $to, $subject, $html, $headers );
}

/**
 * Send welcome email.
 *
 * @param int    $user_id User ID.
 * @param string $name    Display name.
 * @param string $email   Email.
 */
function fbg_send_welcome_email( $user_id, $name, $email ) {
	$blogname = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
	$subject  = sprintf(
		/* translators: %1$s site name, %2$s user name */
		__( 'Welcome to %1$s, %2$s!', 'free-backlinks-generator' ),
		$blogname,
		$name
	);
	$submit    = home_url( '/submit-post/' );
	$community = home_url( '/community/' );
	$profile   = home_url( '/dashboard/#profile' );
	$body      = fbg_email_html_layout(
		__( 'You are in — start building backlinks.', 'free-backlinks-generator' ),
		__( 'You are officially part of the community', 'free-backlinks-generator' ),
		'<p style="margin:0 0 16px;">' . sprintf(
			/* translators: %s first name */
			esc_html__( 'Hi %s,', 'free-backlinks-generator' ),
			esc_html( $name )
		) . '</p>'
		. '<p style="margin:0 0 16px;">' . esc_html__( 'You can now submit guest posts, earn backlinks, and grow with other creators. Here is how to get started:', 'free-backlinks-generator' ) . '</p>'
		. '<ul style="margin:0;padding-left:20px;color:#475569;">'
		. '<li style="margin:8px 0;">' . esc_html__( 'Write your first guest post with contextual links to your site.', 'free-backlinks-generator' ) . '</li>'
		. '<li style="margin:8px 0;">' . esc_html__( 'Browse published posts in the community for inspiration.', 'free-backlinks-generator' ) . '</li>'
		. '<li style="margin:8px 0;">' . esc_html__( 'Complete your profile so other members can discover you.', 'free-backlinks-generator' ) . '</li>'
		. '</ul>'
		. '<p style="margin:20px 0 0;font-size:14px;color:#64748b;">' . esc_html__( 'Your starting tier: Seedling — keep publishing to level up.', 'free-backlinks-generator' ) . '</p>',
		__( 'Submit your first post', 'free-backlinks-generator' ),
		$submit,
		null
	);
	fbg_wp_mail_html( $email, $subject, $body );
}

/**
 * Notify admin of new submission.
 *
 * @param int    $post_id Post ID.
 * @param int    $user_id Author ID.
 * @param string $title   Title.
 */
function fbg_notify_admin_new_submission( $post_id, $user_id, $title ) {
	$admin = get_option( 'admin_email' );
	if ( ! is_email( $admin ) ) {
		return;
	}
	$edit   = admin_url( 'post.php?post=' . (int) $post_id . '&action=edit' );
	$author = get_userdata( $user_id );
	$aname  = $author ? $author->display_name : '#' . $user_id;
	$subj   = sprintf( __( '[%s] New guest post pending review', 'free-backlinks-generator' ), get_bloginfo( 'name' ) );
	$html   = fbg_email_html_layout(
		__( 'A new submission is waiting in wp-admin.', 'free-backlinks-generator' ),
		__( 'New guest post to review', 'free-backlinks-generator' ),
		'<p style="margin:0 0 12px;"><strong>' . esc_html__( 'Title', 'free-backlinks-generator' ) . ':</strong> ' . esc_html( $title ) . '</p>'
		. '<p style="margin:0 0 12px;"><strong>' . esc_html__( 'Author', 'free-backlinks-generator' ) . ':</strong> ' . esc_html( $aname ) . ' (ID ' . (int) $user_id . ')</p>'
		. '<p style="margin:0;">' . esc_html__( 'Review the post, then publish to approve or save as Draft and use the rejection box to notify the author.', 'free-backlinks-generator' ) . '</p>',
		__( 'Open in WordPress', 'free-backlinks-generator' ),
		$edit,
		null
	);
	fbg_wp_mail_html( $admin, $subj, $html );
}

/**
 * Approval email to author.
 *
 * @param int     $user_id User ID.
 * @param WP_Post $post    Post object.
 */
function fbg_send_approval_email( $user_id, $post ) {
	$user = get_userdata( $user_id );
	if ( ! $user ) {
		return;
	}
	if ( ! fbg_user_wants_email( $user_id, 'email_approved' ) ) {
		return;
	}
	$link = get_permalink( $post );
	$subj = sprintf(
		/* translators: %s post title */
		__( 'Approved: your guest post "%s" is live', 'free-backlinks-generator' ),
		$post->post_title
	);
	$html = fbg_email_html_layout(
		__( 'Great news — your post is published.', 'free-backlinks-generator' ),
		__( 'Your guest post was approved', 'free-backlinks-generator' ),
		'<p style="margin:0 0 16px;">' . sprintf(
			/* translators: %s display name */
			esc_html__( 'Hi %s,', 'free-backlinks-generator' ),
			esc_html( $user->display_name )
		) . '</p>'
		. '<p style="margin:0 0 16px;">' . esc_html__( 'Your submission passed review and is now live on the site. Thank you for contributing quality content to the community.', 'free-backlinks-generator' ) . '</p>'
		. '<p style="margin:0;font-size:15px;"><strong>' . esc_html( $post->post_title ) . '</strong></p>',
		__( 'View your live post', 'free-backlinks-generator' ),
		$link,
		__( 'You will also see this update in your dashboard notifications.', 'free-backlinks-generator' )
	);
	fbg_wp_mail_html( $user->user_email, $subj, $html );
}

/**
 * Rejection email.
 *
 * @param int    $user_id User ID.
 * @param string $title   Post title.
 * @param string $reason  Reason.
 */
function fbg_send_rejection_email( $user_id, $title, $reason ) {
	$user = get_userdata( $user_id );
	if ( ! $user ) {
		return;
	}
	if ( ! fbg_user_wants_email( $user_id, 'email_rejected' ) ) {
		return;
	}
	$dash = home_url( '/dashboard/#posts' );
	$subj = sprintf(
		/* translators: %s post title */
		__( 'Update on your guest post: %s', 'free-backlinks-generator' ),
		$title
	);
	$reason_html = '<div style="margin:16px 0;padding:16px 18px;background:#fff7ed;border-left:4px solid #ea580c;border-radius:0 8px 8px 0;font-size:15px;color:#9a3412;">' . esc_html( wp_strip_all_tags( $reason ) ) . '</div>';
	$html        = fbg_email_html_layout(
		__( 'Your submission needs a few changes.', 'free-backlinks-generator' ),
		__( 'Your guest post was not approved (yet)', 'free-backlinks-generator' ),
		'<p style="margin:0 0 16px;">' . sprintf(
			/* translators: %s display name */
			esc_html__( 'Hi %s,', 'free-backlinks-generator' ),
			esc_html( $user->display_name )
		) . '</p>'
		. '<p style="margin:0 0 12px;">' . esc_html__( 'Our team reviewed your submission and could not publish it in its current form. Please read the note below, revise your article, and submit again when you are ready.', 'free-backlinks-generator' ) . '</p>'
		. '<p style="margin:0 0 8px;font-weight:700;color:#0f172a;">' . esc_html__( 'Note from the reviewer', 'free-backlinks-generator' ) . '</p>'
		. $reason_html
		. '<p style="margin:20px 0 0;">' . esc_html__( 'Your draft is saved — open your dashboard to edit and resubmit.', 'free-backlinks-generator' ) . '</p>',
		__( 'Go to my posts', 'free-backlinks-generator' ),
		$dash,
		__( 'This message is also shown in your dashboard notifications.', 'free-backlinks-generator' )
	);
	fbg_wp_mail_html( $user->user_email, $subj, $html );
}

/**
 * Niches for forms.
 *
 * @return array<string, string>
 */
function fbg_niche_options() {
	return array(
		'technology'    => __( 'Technology', 'free-backlinks-generator' ),
		'seo-marketing' => __( 'SEO & Marketing', 'free-backlinks-generator' ),
		'finance'       => __( 'Finance & Money', 'free-backlinks-generator' ),
		'health'        => __( 'Health & Wellness', 'free-backlinks-generator' ),
		'lifestyle'     => __( 'Lifestyle & Travel', 'free-backlinks-generator' ),
		'food'          => __( 'Food & Recipes', 'free-backlinks-generator' ),
		'business'      => __( 'Business & Entrepreneurship', 'free-backlinks-generator' ),
		'education'     => __( 'Education', 'free-backlinks-generator' ),
		'sports'        => __( 'Sports & Fitness', 'free-backlinks-generator' ),
		'real-estate'   => __( 'Real Estate', 'free-backlinks-generator' ),
		'fashion'       => __( 'Fashion & Beauty', 'free-backlinks-generator' ),
		'other'         => __( 'Other', 'free-backlinks-generator' ),
	);
}

/**
 * Content types for forms.
 *
 * @return array<string, string>
 */
function fbg_content_type_options() {
	return array(
		'how-to'    => __( 'How-To Guide', 'free-backlinks-generator' ),
		'listicle'  => __( 'Listicle', 'free-backlinks-generator' ),
		'review'    => __( 'Review', 'free-backlinks-generator' ),
		'case-study'=> __( 'Case Study', 'free-backlinks-generator' ),
		'opinion'   => __( 'Opinion', 'free-backlinks-generator' ),
		'tutorial'  => __( 'Tutorial', 'free-backlinks-generator' ),
	);
}

/**
 * Published fbg_post count (archive hero).
 *
 * @return int
 */
/**
 * Sum view counts across author's published guest posts.
 *
 * @param int $user_id User ID.
 * @return int
 */
function fbg_author_total_views( $user_id ) {
	$ids = get_posts(
		array(
			'post_type'      => 'fbg_post',
			'author'         => (int) $user_id,
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids',
		)
	);
	$total = 0;
	foreach ( $ids as $pid ) {
		$total += (int) get_post_meta( $pid, '_fbg_view_count', true );
	}
	return $total;
}

/**
 * Post counts per month for bar chart (last 6 months).
 *
 * @param int $user_id User ID.
 * @return array<string, int> Keys like Y-m.
 */
function fbg_author_posts_by_month( $user_id ) {
	$out  = array();
	$base = strtotime( gmdate( 'Y-m-01' ) );
	for ( $i = 5; $i >= 0; $i-- ) {
		$key         = gmdate( 'Y-m', strtotime( '-' . $i . ' months', $base ) );
		$out[ $key ] = 0;
	}
	$oldest = array_key_first( $out );
	$from   = $oldest ? $oldest . '-01' : gmdate( 'Y-m-01' );
	$q      = new WP_Query(
		array(
			'post_type'      => 'fbg_post',
			'author'         => (int) $user_id,
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'date_query'     => array(
				array(
					'after'     => $from,
					'inclusive' => true,
				),
			),
		)
	);
	foreach ( $q->posts as $p ) {
		$key = get_post_time( 'Y-m', false, $p );
		if ( isset( $out[ $key ] ) ) {
			$out[ $key ]++;
		}
	}
	return $out;
}

function fbg_published_post_count() {
	$q = new WP_Query(
		array(
			'post_type'      => 'fbg_post',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'no_found_rows'  => false,
		)
	);
	return (int) $q->found_posts;
}

/**
 * Related published guest post IDs: same niche, then same author, then recent community posts.
 *
 * @param int $post_id Current post ID.
 * @param int $limit   Max posts.
 * @return array<int>
 */
function fbg_get_related_fbg_post_ids( $post_id, $limit = 3 ) {
	$post = get_post( $post_id );
	if ( ! $post || 'fbg_post' !== $post->post_type ) {
		return array();
	}
	$limit       = max( 1, (int) $limit );
	$exclude     = array( (int) $post_id );
	$found_ids   = array();
	$niche_terms = get_the_terms( $post_id, 'fbg_niche' );

	if ( $niche_terms && ! is_wp_error( $niche_terms ) ) {
		$q1 = new WP_Query(
			array(
				'post_type'      => 'fbg_post',
				'post_status'    => 'publish',
				'posts_per_page' => $limit,
				'post__not_in'   => $exclude,
				'fields'         => 'ids',
				'tax_query'      => array(
					array(
						'taxonomy' => 'fbg_niche',
						'field'    => 'term_id',
						'terms'    => (int) $niche_terms[0]->term_id,
					),
				),
				'orderby'        => 'date',
				'order'          => 'DESC',
			)
		);
		foreach ( $q1->posts as $id ) {
			$found_ids[] = (int) $id;
		}
		wp_reset_postdata();
	}

	if ( count( $found_ids ) < $limit ) {
		$q2 = new WP_Query(
			array(
				'post_type'      => 'fbg_post',
				'post_status'    => 'publish',
				'posts_per_page' => $limit - count( $found_ids ),
				'post__not_in'   => array_merge( $exclude, $found_ids ),
				'fields'         => 'ids',
				'author'         => (int) $post->post_author,
				'orderby'        => 'date',
				'order'          => 'DESC',
			)
		);
		foreach ( $q2->posts as $id ) {
			$found_ids[] = (int) $id;
		}
		wp_reset_postdata();
	}

	if ( count( $found_ids ) < $limit ) {
		$q3 = new WP_Query(
			array(
				'post_type'      => 'fbg_post',
				'post_status'    => 'publish',
				'posts_per_page' => $limit - count( $found_ids ),
				'post__not_in'   => array_merge( $exclude, $found_ids ),
				'fields'         => 'ids',
				'orderby'        => 'date',
				'order'          => 'DESC',
			)
		);
		foreach ( $q3->posts as $id ) {
			$found_ids[] = (int) $id;
		}
		wp_reset_postdata();
	}

	return array_slice( $found_ids, 0, $limit );
}
