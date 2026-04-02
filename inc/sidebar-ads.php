<?php
/**
 * Sidebar portrait ads (single guest post) + Settings.
 *
 * @package Free_Backlinks_Generator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register CPT for sidebar advertisements.
 */
function fbg_register_sidebar_ad_cpt() {
	register_post_type(
		'fbg_sidebar_ad',
		array(
			'labels'              => array(
				'name'          => __( 'Sidebar ads', 'free-backlinks-generator' ),
				'singular_name' => __( 'Sidebar ad', 'free-backlinks-generator' ),
				'add_new_item'  => __( 'Add New Sidebar Ad', 'free-backlinks-generator' ),
				'edit_item'     => __( 'Edit Sidebar Ad', 'free-backlinks-generator' ),
				'menu_name'     => __( 'Sidebar ads', 'free-backlinks-generator' ),
			),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_icon'           => 'dashicons-megaphone',
			'menu_position'       => 26,
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
			'supports'            => array( 'title', 'thumbnail', 'page-attributes' ),
			'has_archive'         => false,
		)
	);
}
add_action( 'init', 'fbg_register_sidebar_ad_cpt' );

/**
 * Ad URL + active meta box.
 */
function fbg_sidebar_ad_metaboxes() {
	add_meta_box(
		'fbg_sidebar_ad_details',
		__( 'Ad link & status', 'free-backlinks-generator' ),
		'fbg_render_sidebar_ad_metabox',
		'fbg_sidebar_ad',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'fbg_sidebar_ad_metaboxes' );

/**
 * @param WP_Post $post Post.
 */
function fbg_render_sidebar_ad_metabox( $post ) {
	wp_nonce_field( 'fbg_sidebar_ad_save', 'fbg_sidebar_ad_nonce' );
	$url    = get_post_meta( $post->ID, '_fbg_ad_url', true );
	$active = get_post_meta( $post->ID, '_fbg_ad_active', true );
	if ( '' === $active ) {
		$active = '1';
	}
	?>
	<p>
		<label for="fbg_ad_url"><strong><?php esc_html_e( 'Click URL', 'free-backlinks-generator' ); ?></strong></label><br>
		<input type="url" name="fbg_ad_url" id="fbg_ad_url" class="large-text" value="<?php echo esc_attr( (string) $url ); ?>" placeholder="https://" required>
	</p>
	<p>
		<label>
			<input type="checkbox" name="fbg_ad_active" value="1" <?php checked( $active, '1' ); ?>>
			<?php esc_html_e( 'Active (show on site)', 'free-backlinks-generator' ); ?>
		</label>
	</p>
	<p class="description"><?php esc_html_e( 'Set a portrait featured image (recommended ~300×600px). Use the “Order” field in the sidebar to sort ads when not using random mode.', 'free-backlinks-generator' ); ?></p>
	<?php
}

/**
 * Save ad meta.
 *
 * @param int $post_id Post ID.
 */
function fbg_save_sidebar_ad_meta( $post_id ) {
	if ( ! isset( $_POST['fbg_sidebar_ad_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['fbg_sidebar_ad_nonce'] ) ), 'fbg_sidebar_ad_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$url = isset( $_POST['fbg_ad_url'] ) ? esc_url_raw( wp_unslash( $_POST['fbg_ad_url'] ) ) : '';
	update_post_meta( $post_id, '_fbg_ad_url', $url );
	$active = ! empty( $_POST['fbg_ad_active'] ) ? '1' : '0';
	update_post_meta( $post_id, '_fbg_ad_active', $active );
}
add_action( 'save_post_fbg_sidebar_ad', 'fbg_save_sidebar_ad_meta' );

/**
 * Settings: display mode.
 */
function fbg_sidebar_ads_register_settings() {
	register_setting(
		'fbg_sidebar_ads',
		'fbg_sidebar_ads_mode',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'fbg_sanitize_sidebar_ads_mode',
			'default'           => 'stack',
		)
	);
	register_setting(
		'fbg_sidebar_ads',
		'fbg_sidebar_ads_autoplay',
		array(
			'type'              => 'integer',
			'sanitize_callback' => 'absint',
			'default'           => 5,
		)
	);
}
add_action( 'admin_init', 'fbg_sidebar_ads_register_settings' );

/**
 * @param string $v Raw mode.
 * @return string
 */
function fbg_sanitize_sidebar_ads_mode( $v ) {
	$allowed = array( 'random', 'slider', 'stack' );
	return in_array( $v, $allowed, true ) ? $v : 'stack';
}

/**
 * Settings page under Settings.
 */
function fbg_sidebar_ads_settings_menu() {
	add_options_page(
		__( 'Guest post sidebar ads', 'free-backlinks-generator' ),
		__( 'Sidebar ads', 'free-backlinks-generator' ),
		'manage_options',
		'fbg-sidebar-ads',
		'fbg_render_sidebar_ads_settings_page'
	);
}
add_action( 'admin_menu', 'fbg_sidebar_ads_settings_menu' );

/**
 * Settings page HTML.
 */
function fbg_render_sidebar_ads_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$mode      = get_option( 'fbg_sidebar_ads_mode', 'stack' );
	$autoplay  = (int) get_option( 'fbg_sidebar_ads_autoplay', 5 );
	$edit_link = admin_url( 'edit.php?post_type=fbg_sidebar_ad' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Guest post sidebar ads', 'free-backlinks-generator' ); ?></h1>
		<p>
			<?php
			printf(
				/* translators: %s: URL to ads list */
				wp_kses_post( __( 'Create and manage portrait ads under <a href="%s">Sidebar ads</a>. They appear next to community post content.', 'free-backlinks-generator' ) ),
				esc_url( $edit_link )
			);
			?>
		</p>
		<form method="post" action="options.php">
			<?php settings_fields( 'fbg_sidebar_ads' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Display mode', 'free-backlinks-generator' ); ?></th>
					<td>
						<select name="fbg_sidebar_ads_mode" id="fbg_sidebar_ads_mode">
							<option value="stack" <?php selected( $mode, 'stack' ); ?>><?php esc_html_e( 'Stacked — show all active ads (portrait, top to bottom)', 'free-backlinks-generator' ); ?></option>
							<option value="random" <?php selected( $mode, 'random' ); ?>><?php esc_html_e( 'Random — pick one active ad per page load', 'free-backlinks-generator' ); ?></option>
							<option value="slider" <?php selected( $mode, 'slider' ); ?>><?php esc_html_e( 'Slider — rotate through all active ads', 'free-backlinks-generator' ); ?></option>
						</select>
						<p class="description"><?php esc_html_e( 'In stack mode, use the “Order” field on each ad to control sequence (lower = higher on the page).', 'free-backlinks-generator' ); ?></p>
					</td>
				</tr>
				<tr class="fbg-autoplay-row">
					<th scope="row"><?php esc_html_e( 'Slider autoplay', 'free-backlinks-generator' ); ?></th>
					<td>
						<input type="number" name="fbg_sidebar_ads_autoplay" value="<?php echo esc_attr( (string) $autoplay ); ?>" min="0" max="60" step="1" class="small-text">
						<p class="description"><?php esc_html_e( 'Seconds between slides (0 = no autoplay; manual prev/next still works).', 'free-backlinks-generator' ); ?></p>
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

/**
 * Active ads query.
 *
 * @return array<int, WP_Post>
 */
function fbg_get_active_sidebar_ads() {
	$q = new WP_Query(
		array(
			'post_type'      => 'fbg_sidebar_ad',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
			'meta_query'     => array(
				array(
					'key'   => '_fbg_ad_active',
					'value' => '1',
				),
			),
		)
	);
	return $q->posts;
}

/**
 * Ads to render for current view (respects display mode).
 *
 * @return array<int, WP_Post>
 */
function fbg_sidebar_ads_for_template() {
	$all = fbg_get_active_sidebar_ads();
	$all = array_values(
		array_filter(
			$all,
			function ( $p ) {
				$url = get_post_meta( $p->ID, '_fbg_ad_url', true );
				return $url && has_post_thumbnail( $p->ID );
			}
		)
	);
	if ( empty( $all ) ) {
		return array();
	}
	$mode = get_option( 'fbg_sidebar_ads_mode', 'stack' );
	if ( 'random' === $mode ) {
		return array( $all[ array_rand( $all ) ] );
	}
	return $all;
}
