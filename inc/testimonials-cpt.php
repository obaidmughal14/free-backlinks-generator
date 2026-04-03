<?php
/**
 * Testimonials CPT (homepage carousel), meta, default seed.
 *
 * @package Free_Backlinks_Generator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register testimonial post type.
 */
function fbg_register_testimonial_cpt() {
	$labels = array(
		'name'               => __( 'Testimonials', 'free-backlinks-generator' ),
		'singular_name'      => __( 'Testimonial', 'free-backlinks-generator' ),
		'add_new'            => __( 'Add New', 'free-backlinks-generator' ),
		'add_new_item'       => __( 'Add New Testimonial', 'free-backlinks-generator' ),
		'edit_item'          => __( 'Edit Testimonial', 'free-backlinks-generator' ),
		'new_item'           => __( 'New Testimonial', 'free-backlinks-generator' ),
		'view_item'          => __( 'View Testimonial', 'free-backlinks-generator' ),
		'search_items'       => __( 'Search Testimonials', 'free-backlinks-generator' ),
		'not_found'          => __( 'No testimonials found', 'free-backlinks-generator' ),
		'not_found_in_trash' => __( 'No testimonials in trash', 'free-backlinks-generator' ),
		'menu_name'          => __( 'Testimonials', 'free-backlinks-generator' ),
	);

	register_post_type(
		'fbg_testimonial',
		array(
			'labels'              => $labels,
			'public'              => false,
			'publicly_queryable'  => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_icon'           => 'dashicons-format-quote',
			'menu_position'       => 21,
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
			'supports'            => array( 'title', 'editor', 'page-attributes' ),
			'has_archive'         => false,
			'rewrite'             => false,
			'show_in_rest'        => true,
		)
	);

	register_post_meta(
		'fbg_testimonial',
		'_fbg_testimonial_role',
		array(
			'type'              => 'string',
			'single'            => true,
			'sanitize_callback' => 'sanitize_text_field',
			'show_in_rest'      => false,
			'auth_callback'     => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);
	register_post_meta(
		'fbg_testimonial',
		'_fbg_testimonial_since',
		array(
			'type'              => 'string',
			'single'            => true,
			'sanitize_callback' => 'sanitize_text_field',
			'show_in_rest'      => false,
			'auth_callback'     => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);
	register_post_meta(
		'fbg_testimonial',
		'_fbg_testimonial_stat',
		array(
			'type'              => 'string',
			'single'            => true,
			'sanitize_callback' => 'sanitize_text_field',
			'show_in_rest'      => false,
			'auth_callback'     => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);
	register_post_meta(
		'fbg_testimonial',
		'_fbg_testimonial_rating',
		array(
			'type'              => 'integer',
			'single'            => true,
			'sanitize_callback' => function ( $v ) {
				$n = (int) $v;
				return min( 5, max( 1, $n ) );
			},
			'default'           => 5,
			'show_in_rest'      => false,
			'auth_callback'     => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);
}
add_action( 'init', 'fbg_register_testimonial_cpt' );

/**
 * Default testimonial rows for one-time seed.
 *
 * @return array<int, array<string, string>>
 */
function fbg_get_default_testimonials_seed() {
	return array(
		array(
			'author' => __( 'Sarah K.', 'free-backlinks-generator' ),
			'quote'  => __( 'I was skeptical about free backlink platforms, but this community changed my mind. After twelve guest posts in three months, my domain authority climbed from 14 to 31 — all from real editorial links.', 'free-backlinks-generator' ),
			'role'   => __( 'Lifestyle & travel blogger', 'free-backlinks-generator' ),
			'since'  => __( 'Member since January 2024', 'free-backlinks-generator' ),
			'stat'   => __( '87 backlinks placed', 'free-backlinks-generator' ),
		),
		array(
			'author' => __( 'Marcus T.', 'free-backlinks-generator' ),
			'quote'  => __( 'As an SEO consultant I have tried every link-building tactic. The editorial review here means my clients get quality placements, not spam folders. It is now part of our standard playbook.', 'free-backlinks-generator' ),
			'role'   => __( 'SEO consultant', 'free-backlinks-generator' ),
			'since'  => __( 'Member since March 2024', 'free-backlinks-generator' ),
			'stat'   => __( '214 backlinks placed', 'free-backlinks-generator' ),
		),
		array(
			'author' => __( 'Priya M.', 'free-backlinks-generator' ),
			'quote'  => __( 'I write about personal finance and this network has been a goldmine. I have built over 120 contextual backlinks and collaborated with writers who actually understand the niche.', 'free-backlinks-generator' ),
			'role'   => __( 'Personal finance writer', 'free-backlinks-generator' ),
			'since'  => __( 'Member since November 2023', 'free-backlinks-generator' ),
			'stat'   => __( '120 backlinks placed', 'free-backlinks-generator' ),
		),
		array(
			'author' => __( 'Daniel R.', 'free-backlinks-generator' ),
			'quote'  => __( 'Our SaaS blog needed authority without a five-figure agency budget. Guest posts here gave us backlinks from relevant tech and marketing sites — Ahrefs shows a clean, steady upward trend.', 'free-backlinks-generator' ),
			'role'   => __( 'Head of growth, B2B SaaS', 'free-backlinks-generator' ),
			'since'  => __( 'Member since February 2024', 'free-backlinks-generator' ),
			'stat'   => __( '56 referring domains', 'free-backlinks-generator' ),
		),
		array(
			'author' => __( 'Elena V.', 'free-backlinks-generator' ),
			'quote'  => __( 'I run a small agency and use this platform for clients in health and wellness. The niche categories keep links relevant, and turnaround for reviews is predictable.', 'free-backlinks-generator' ),
			'role'   => __( 'Digital marketing agency owner', 'free-backlinks-generator' ),
			'since'  => __( 'Member since June 2024', 'free-backlinks-generator' ),
			'stat'   => __( '34 live guest posts', 'free-backlinks-generator' ),
		),
		array(
			'author' => __( 'Jason S.', 'free-backlinks-generator' ),
			'quote'  => __( 'The reader unlock system pushed me to actually read other members’ articles — which made my own submissions stronger. Win-win for content quality.', 'free-backlinks-generator' ),
			'role'   => __( 'Lifestyle / tech blogger', 'free-backlinks-generator' ),
			'since'  => __( 'Member since August 2024', 'free-backlinks-generator' ),
			'stat'   => __( '19 posts published', 'free-backlinks-generator' ),
		),
		array(
			'author' => __( 'Amir H.', 'free-backlinks-generator' ),
			'quote'  => __( 'We are a local services brand competing with national chains. Community guest posts helped us show up for city + service keywords without shady PBNs.', 'free-backlinks-generator' ),
			'role'   => __( 'Local SEO lead', 'free-backlinks-generator' ),
			'since'  => __( 'Member since May 2024', 'free-backlinks-generator' ),
			'stat'   => __( '41 quality backlinks', 'free-backlinks-generator' ),
		),
		array(
			'author' => __( 'Nina L.', 'free-backlinks-generator' ),
			'quote'  => __( 'Transparent guidelines and a real human review process. I recommend this to every creator in my newsletter who asks how to build DA ethically.', 'free-backlinks-generator' ),
			'role'   => __( 'Newsletter publisher (12k subs)', 'free-backlinks-generator' ),
			'since'  => __( 'Member since April 2024', 'free-backlinks-generator' ),
			'stat'   => __( 'Affiliate + member', 'free-backlinks-generator' ),
		),
	);
}

/**
 * Insert default testimonials once (empty site).
 */
function fbg_maybe_seed_testimonials() {
	if ( '1' === get_option( 'fbg_testimonials_seeded_v1', '' ) ) {
		return;
	}
	$one = get_posts(
		array(
			'post_type'              => 'fbg_testimonial',
			'post_status'            => array( 'publish', 'draft', 'pending', 'private' ),
			'posts_per_page'         => 1,
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);
	if ( ! empty( $one ) ) {
		update_option( 'fbg_testimonials_seeded_v1', '1' );
		return;
	}

	$rows = fbg_get_default_testimonials_seed();
	$i    = 0;
	foreach ( $rows as $row ) {
		$post_id = wp_insert_post(
			array(
				'post_title'   => $row['author'],
				'post_content' => $row['quote'],
				'post_status'  => 'publish',
				'post_type'    => 'fbg_testimonial',
				'menu_order'   => $i,
			),
			true
		);
		if ( is_wp_error( $post_id ) || ! $post_id ) {
			continue;
		}
		update_post_meta( $post_id, '_fbg_testimonial_role', $row['role'] );
		update_post_meta( $post_id, '_fbg_testimonial_since', $row['since'] );
		update_post_meta( $post_id, '_fbg_testimonial_stat', $row['stat'] );
		update_post_meta( $post_id, '_fbg_testimonial_rating', 5 );
		++$i;
	}
	update_option( 'fbg_testimonials_seeded_v1', '1' );
}
add_action( 'init', 'fbg_maybe_seed_testimonials', 30 );

/**
 * Meta box: testimonial details.
 */
function fbg_testimonial_add_meta_box() {
	add_meta_box(
		'fbg_testimonial_details',
		__( 'Testimonial details', 'free-backlinks-generator' ),
		'fbg_testimonial_render_meta_box',
		'fbg_testimonial',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'fbg_testimonial_add_meta_box' );

/**
 * Meta box UI.
 *
 * @param WP_Post $post Post.
 */
function fbg_testimonial_render_meta_box( $post ) {
	wp_nonce_field( 'fbg_testimonial_save', 'fbg_testimonial_nonce' );
	$role   = get_post_meta( $post->ID, '_fbg_testimonial_role', true );
	$since  = get_post_meta( $post->ID, '_fbg_testimonial_since', true );
	$stat   = get_post_meta( $post->ID, '_fbg_testimonial_stat', true );
	$rating = (int) get_post_meta( $post->ID, '_fbg_testimonial_rating', true );
	if ( $rating < 1 || $rating > 5 ) {
		$rating = 5;
	}
	?>
	<p><strong><?php esc_html_e( 'Title field', 'free-backlinks-generator' ); ?></strong> — <?php esc_html_e( 'Member name (e.g. Sarah K.).', 'free-backlinks-generator' ); ?></p>
	<p><strong><?php esc_html_e( 'Editor', 'free-backlinks-generator' ); ?></strong> — <?php esc_html_e( 'Full testimonial quote.', 'free-backlinks-generator' ); ?></p>
	<p>
		<label for="fbg_testimonial_role"><strong><?php esc_html_e( 'Role / niche', 'free-backlinks-generator' ); ?></strong></label><br>
		<input type="text" class="large-text" id="fbg_testimonial_role" name="fbg_testimonial_role" value="<?php echo esc_attr( $role ); ?>" placeholder="<?php esc_attr_e( 'e.g. SEO consultant', 'free-backlinks-generator' ); ?>">
	</p>
	<p>
		<label for="fbg_testimonial_since"><strong><?php esc_html_e( 'Member since line', 'free-backlinks-generator' ); ?></strong></label><br>
		<input type="text" class="large-text" id="fbg_testimonial_since" name="fbg_testimonial_since" value="<?php echo esc_attr( $since ); ?>" placeholder="<?php esc_attr_e( 'e.g. Member since January 2024', 'free-backlinks-generator' ); ?>">
	</p>
	<p>
		<label for="fbg_testimonial_stat"><strong><?php esc_html_e( 'Highlight stat', 'free-backlinks-generator' ); ?></strong></label><br>
		<input type="text" class="large-text" id="fbg_testimonial_stat" name="fbg_testimonial_stat" value="<?php echo esc_attr( $stat ); ?>" placeholder="<?php esc_attr_e( 'e.g. 87 backlinks placed', 'free-backlinks-generator' ); ?>">
	</p>
	<p>
		<label for="fbg_testimonial_rating"><strong><?php esc_html_e( 'Star rating (1–5)', 'free-backlinks-generator' ); ?></strong></label><br>
		<select id="fbg_testimonial_rating" name="fbg_testimonial_rating">
			<?php for ( $s = 5; $s >= 1; $s-- ) : ?>
				<option value="<?php echo esc_attr( (string) $s ); ?>" <?php selected( $rating, $s ); ?>><?php echo esc_html( (string) $s ); ?></option>
			<?php endfor; ?>
		</select>
	</p>
	<p class="description"><?php esc_html_e( 'Use “Order” in the right column to control carousel order (lower = earlier).', 'free-backlinks-generator' ); ?></p>
	<?php
}

/**
 * Save testimonial meta.
 *
 * @param int $post_id Post ID.
 */
function fbg_testimonial_save_meta( $post_id ) {
	if ( ! isset( $_POST['fbg_testimonial_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['fbg_testimonial_nonce'] ) ), 'fbg_testimonial_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$post = get_post( $post_id );
	if ( ! $post || 'fbg_testimonial' !== $post->post_type ) {
		return;
	}
	if ( isset( $_POST['fbg_testimonial_role'] ) ) {
		update_post_meta( $post_id, '_fbg_testimonial_role', sanitize_text_field( wp_unslash( $_POST['fbg_testimonial_role'] ) ) );
	}
	if ( isset( $_POST['fbg_testimonial_since'] ) ) {
		update_post_meta( $post_id, '_fbg_testimonial_since', sanitize_text_field( wp_unslash( $_POST['fbg_testimonial_since'] ) ) );
	}
	if ( isset( $_POST['fbg_testimonial_stat'] ) ) {
		update_post_meta( $post_id, '_fbg_testimonial_stat', sanitize_text_field( wp_unslash( $_POST['fbg_testimonial_stat'] ) ) );
	}
	if ( isset( $_POST['fbg_testimonial_rating'] ) ) {
		$r = (int) $_POST['fbg_testimonial_rating'];
		$r = min( 5, max( 1, $r ) );
		update_post_meta( $post_id, '_fbg_testimonial_rating', $r );
	}
}
add_action( 'save_post_fbg_testimonial', 'fbg_testimonial_save_meta' );
