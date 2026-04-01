<?php
/**
 * Custom post types and taxonomies.
 *
 * @package Free_Backlinks_Generator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register fbg_post and taxonomies.
 */
function fbg_register_post_types() {
	$labels = array(
		'name'               => __( 'Guest Posts', 'free-backlinks-generator' ),
		'singular_name'      => __( 'Guest Post', 'free-backlinks-generator' ),
		'add_new'            => __( 'Add New', 'free-backlinks-generator' ),
		'add_new_item'       => __( 'Add New Guest Post', 'free-backlinks-generator' ),
		'edit_item'          => __( 'Edit Guest Post', 'free-backlinks-generator' ),
		'new_item'           => __( 'New Guest Post', 'free-backlinks-generator' ),
		'view_item'          => __( 'View Guest Post', 'free-backlinks-generator' ),
		'search_items'       => __( 'Search Guest Posts', 'free-backlinks-generator' ),
		'not_found'          => __( 'No guest posts found', 'free-backlinks-generator' ),
		'not_found_in_trash' => __( 'No guest posts in trash', 'free-backlinks-generator' ),
		'menu_name'          => __( 'Guest Posts', 'free-backlinks-generator' ),
	);

	register_post_type(
		'fbg_post',
		array(
			'labels'              => $labels,
			'public'              => true,
			'has_archive'         => true,
			'rewrite'             => array( 'slug' => 'community' ),
			'menu_icon'           => 'dashicons-admin-links',
			'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'author', 'comments' ),
			'show_in_rest'        => true,
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
		)
	);

	register_taxonomy(
		'fbg_niche',
		'fbg_post',
		array(
			'label'             => __( 'Niche', 'free-backlinks-generator' ),
			'hierarchical'      => false,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'niche' ),
		)
	);

	register_taxonomy(
		'fbg_content_type',
		'fbg_post',
		array(
			'label'             => __( 'Content Type', 'free-backlinks-generator' ),
			'hierarchical'      => false,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'content-type' ),
		)
	);
}
add_action( 'init', 'fbg_register_post_types' );

/**
 * Default terms on activation (niches / types as slugs).
 */
function fbg_seed_taxonomy_terms() {
	$niches = fbg_niche_options();
	foreach ( $niches as $slug => $label ) {
		if ( ! term_exists( $slug, 'fbg_niche' ) ) {
			wp_insert_term( $label, 'fbg_niche', array( 'slug' => $slug ) );
		}
	}
	$types = fbg_content_type_options();
	foreach ( $types as $slug => $label ) {
		if ( ! term_exists( $slug, 'fbg_content_type' ) ) {
			wp_insert_term( $label, 'fbg_content_type', array( 'slug' => $slug ) );
		}
	}
}

/**
 * Admin list columns for fbg_post.
 *
 * @param array<string, string> $columns Columns.
 * @return array<string, string>
 */
function fbg_post_columns( $columns ) {
	$new = array();
	foreach ( $columns as $key => $label ) {
		$new[ $key ] = $label;
		if ( 'title' === $key ) {
			$new['fbg_status']       = __( 'FBG Status', 'free-backlinks-generator' );
			$new['fbg_backlinks']    = __( 'Backlinks', 'free-backlinks-generator' );
			$new['fbg_author_site']  = __( 'Author Website', 'free-backlinks-generator' );
		}
	}
	if ( ! isset( $new['fbg_status'] ) ) {
		$new['fbg_status']      = __( 'FBG Status', 'free-backlinks-generator' );
		$new['fbg_backlinks']   = __( 'Backlinks', 'free-backlinks-generator' );
		$new['fbg_author_site'] = __( 'Author Website', 'free-backlinks-generator' );
	}
	return $new;
}
add_filter( 'manage_fbg_post_posts_columns', 'fbg_post_columns' );

/**
 * Render custom columns.
 *
 * @param string $column Column key.
 * @param int    $post_id Post ID.
 */
function fbg_post_column_content( $column, $post_id ) {
	if ( 'fbg_status' === $column ) {
		$st = get_post_meta( $post_id, '_fbg_content_status', true );
		if ( ! $st ) {
			$st = get_post_status( $post_id ) === 'publish' ? 'approved' : 'pending';
		}
		echo esc_html( ucfirst( $st ) );
	} elseif ( 'fbg_backlinks' === $column ) {
		echo esc_html( (string) (int) get_post_meta( $post_id, '_fbg_backlink_count', true ) );
	} elseif ( 'fbg_author_site' === $column ) {
		$author_id = (int) get_post_field( 'post_author', $post_id );
		$url       = get_user_meta( $author_id, '_fbg_website_url', true );
		if ( $url ) {
			printf( '<a href="%s" target="_blank" rel="noopener">%s</a>', esc_url( $url ), esc_html( wp_parse_url( $url, PHP_URL_HOST ) ?: $url ) );
		} else {
			echo '—';
		}
	}
}
add_action( 'manage_fbg_post_posts_custom_column', 'fbg_post_column_content', 10, 2 );

/**
 * On publish: sync backlinks from content, update author stats, notify.
 *
 * @param string  $new New status.
 * @param string  $old Old status.
 * @param WP_Post $post Post.
 */
function fbg_transition_publish_guest_post( $new, $old, $post ) {
	if ( ! $post || 'fbg_post' !== $post->post_type ) {
		return;
	}
	if ( 'publish' !== $new || 'publish' === $old ) {
		return;
	}

	$content = $post->post_content;
	preg_match_all( '/<a[^>]+href=["\']([^"\']+)["\'][^>]*>([^<]+)<\/a>/i', $content, $matches );
	$links     = array();
	$home      = home_url();
	$stored    = get_post_meta( $post->ID, '_fbg_backlinks_array', true );
	if ( is_array( $stored ) && ! empty( $stored ) ) {
		$links = $stored;
	} else {
		foreach ( $matches[1] as $i => $url ) {
			if ( strpos( $url, $home ) === false ) {
				$links[] = array(
					'url'    => esc_url_raw( $url ),
					'anchor' => sanitize_text_field( wp_strip_all_tags( $matches[2][ $i ] ) ),
				);
			}
		}
	}

	update_post_meta( $post->ID, '_fbg_backlinks_array', $links );
	update_post_meta( $post->ID, '_fbg_backlink_count', count( $links ) );
	update_post_meta( $post->ID, '_fbg_content_status', 'approved' );

	$author_id = (int) $post->post_author;
	$old_links = (int) get_user_meta( $author_id, '_fbg_total_links', true );
	update_user_meta( $author_id, '_fbg_total_links', $old_links + count( $links ) );
	$old_posts = (int) get_user_meta( $author_id, '_fbg_total_posts', true );
	update_user_meta( $author_id, '_fbg_total_posts', $old_posts + 1 );

	fbg_recalculate_tier( $author_id );
	fbg_send_approval_email( $author_id, $post );
	fbg_create_notification(
		$author_id,
		'post_approved',
		sprintf(
			__( 'Your guest post "%s" has been approved and is now live!', 'free-backlinks-generator' ),
			$post->post_title
		),
		$post->ID
	);
}
add_action( 'transition_post_status', 'fbg_transition_publish_guest_post', 10, 3 );

/**
 * Remember when a guest post moves from pending to draft (admin rejection path).
 *
 * @param string  $new New status.
 * @param string  $old Old status.
 * @param WP_Post $post Post.
 */
function fbg_flag_pending_to_draft_for_rejection( $new, $old, $post ) {
	if ( ! $post || 'fbg_post' !== $post->post_type ) {
		return;
	}
	if ( 'pending' === $old && 'draft' === $new ) {
		set_transient( 'fbg_draft_from_pending_' . $post->ID, 1, 120 );
	}
}
add_action( 'transition_post_status', 'fbg_flag_pending_to_draft_for_rejection', 5, 3 );

/**
 * Meta box: optional rejection email when moving pending → draft.
 */
function fbg_add_rejection_metabox() {
	if ( ! current_user_can( 'edit_others_posts' ) && ! current_user_can( 'manage_options' ) ) {
		return;
	}
	add_meta_box(
		'fbg_rejection',
		__( 'Author notification (rejection)', 'free-backlinks-generator' ),
		'fbg_render_rejection_metabox',
		'fbg_post',
		'side',
		'high'
	);
}
add_action( 'add_meta_boxes', 'fbg_add_rejection_metabox' );

/**
 * Rejection meta box UI.
 *
 * @param WP_Post $post Post.
 */
function fbg_render_rejection_metabox( $post ) {
	if ( ! current_user_can( 'edit_others_posts' ) && ! current_user_can( 'manage_options' ) ) {
		echo '<p>' . esc_html__( 'Only moderators can use this box.', 'free-backlinks-generator' ) . '</p>';
		return;
	}
	wp_nonce_field( 'fbg_rejection_save', 'fbg_rejection_nonce' );
	?>
	<p style="margin-top:0;"><?php esc_html_e( 'When you change status from Pending to Draft, you can email the author with feedback.', 'free-backlinks-generator' ); ?></p>
	<p>
		<label>
			<input type="checkbox" name="fbg_rejection_notify" value="1">
			<?php esc_html_e( 'Email author about rejection', 'free-backlinks-generator' ); ?>
		</label>
	</p>
	<p>
		<label for="fbg_rejection_reason"><strong><?php esc_html_e( 'Message to author', 'free-backlinks-generator' ); ?></strong></label>
		<textarea name="fbg_rejection_reason" id="fbg_rejection_reason" rows="5" class="large-text" placeholder="<?php esc_attr_e( 'Explain what to improve so they can revise and resubmit…', 'free-backlinks-generator' ); ?>"></textarea>
	</p>
	<?php
}

/**
 * After save: send rejection email if pending → draft + checkbox + message.
 *
 * @param int     $post_id Post ID.
 * @param WP_Post $post    Post object.
 */
function fbg_save_rejection_notification( $post_id, $post ) {
	if ( ! $post || 'fbg_post' !== $post->post_type ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! isset( $_POST['fbg_rejection_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['fbg_rejection_nonce'] ) ), 'fbg_rejection_save' ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$key = 'fbg_draft_from_pending_' . $post_id;
	if ( ! get_transient( $key ) ) {
		return;
	}
	delete_transient( $key );

	if ( 'draft' !== $post->post_status ) {
		return;
	}

	$notify = ! empty( $_POST['fbg_rejection_notify'] );
	$reason = isset( $_POST['fbg_rejection_reason'] ) ? sanitize_textarea_field( wp_unslash( $_POST['fbg_rejection_reason'] ) ) : '';

	if ( ! $notify || '' === trim( $reason ) ) {
		return;
	}

	update_post_meta( $post_id, '_fbg_content_status', 'rejected' );
	update_post_meta( $post_id, '_fbg_rejection_reason', $reason );

	$author_id = (int) $post->post_author;
	fbg_send_rejection_email( $author_id, $post->post_title, $reason );
	fbg_create_notification(
		$author_id,
		'post_rejected',
		sprintf(
			/* translators: %s post title */
			__( 'Your guest post "%s" was not approved. Check your email for the reviewer\'s notes.', 'free-backlinks-generator' ),
			$post->post_title
		),
		$post_id
	);
}
add_action( 'save_post_fbg_post', 'fbg_save_rejection_notification', 25, 2 );

/**
 * Pending post: block single view for non-privileged users.
 */
function fbg_restrict_pending_single() {
	if ( ! is_singular( 'fbg_post' ) ) {
		return;
	}
	$post = get_post();
	if ( ! $post || 'publish' === $post->post_status ) {
		return;
	}
	if ( current_user_can( 'edit_post', $post->ID ) || current_user_can( 'edit_others_posts' ) ) {
		return;
	}
	global $wp_query;
	$wp_query->set_404();
	status_header( 404 );
	nocache_headers();
}
add_action( 'template_redirect', 'fbg_restrict_pending_single', 5 );

/**
 * Track single guest post views.
 */
function fbg_track_post_view() {
	if ( ! is_singular( 'fbg_post' ) ) {
		return;
	}
	$post = get_post();
	if ( ! $post || 'publish' !== $post->post_status ) {
		return;
	}
	$id = (int) $post->ID;
	$n  = (int) get_post_meta( $id, '_fbg_view_count', true );
	update_post_meta( $id, '_fbg_view_count', $n + 1 );
}
add_action( 'template_redirect', 'fbg_track_post_view', 20 );

/**
 * Author archive: show guest posts.
 *
 * @param WP_Query $query Query.
 */
function fbg_author_fbg_posts( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_author() ) {
		return;
	}
	$query->set( 'post_type', 'fbg_post' );
	$query->set( 'post_status', 'publish' );
}
add_action( 'pre_get_posts', 'fbg_author_fbg_posts' );

/**
 * Dashboard widget: pending posts.
 */
function fbg_register_dashboard_widget() {
	wp_add_dashboard_widget(
		'fbg_pending_posts',
		__( 'Pending Guest Posts', 'free-backlinks-generator' ),
		'fbg_render_pending_dashboard_widget'
	);
}
add_action( 'wp_dashboard_setup', 'fbg_register_dashboard_widget' );

/**
 * Output pending widget.
 */
function fbg_render_pending_dashboard_widget() {
	$pending = get_posts(
		array(
			'post_type'      => 'fbg_post',
			'post_status'    => 'pending',
			'posts_per_page' => 5,
		)
	);
	if ( ! $pending ) {
		echo '<p>' . esc_html__( 'No posts pending review.', 'free-backlinks-generator' ) . '</p>';
		return;
	}
	echo '<ul>';
	foreach ( $pending as $p ) {
		printf(
			'<li><a href="%s">%s</a> — %s</li>',
			esc_url( get_edit_post_link( $p->ID ) ),
			esc_html( $p->post_title ),
			esc_html( get_the_author_meta( 'display_name', $p->post_author ) )
		);
	}
	echo '</ul>';
	$total = count(
		get_posts(
			array(
				'post_type'      => 'fbg_post',
				'post_status'    => 'pending',
				'posts_per_page' => -1,
				'fields'         => 'ids',
			)
		)
	);
	printf(
		'<p><a href="%s">%s</a></p>',
		esc_url( admin_url( 'edit.php?post_type=fbg_post&post_status=pending' ) ),
		esc_html(
			sprintf(
				/* translators: %d count */
				__( 'View all %d pending posts →', 'free-backlinks-generator' ),
				$total
			)
		)
	);
}
