<?php
/**
 * Customizer: logo dimensions, homepage stats, footer column titles.
 *
 * @package Free_Backlinks_Generator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sanitize pixel dimension for logo bounds.
 *
 * @param mixed    $val Value.
 * @param int      $min Min.
 * @param int      $max Max.
 * @return int
 */
function fbg_sanitize_logo_px( $val, $min = 24, $max = 400 ) {
	$n = absint( $val );
	return min( $max, max( $min, $n ) );
}

/**
 * Register branding, stats, footer options.
 *
 * @param WP_Customize_Manager $wp_customize Customizer.
 */
function fbg_customize_theme_options_register( $wp_customize ) {
	$wp_customize->add_panel(
		'fbg_theme_panel',
		array(
			'title'       => __( 'FBG theme options', 'free-backlinks-generator' ),
			'description' => __( 'Logo sizing, homepage stats, Pro checkout URLs, and footer column headings. Assign menus under Appearance → Menus.', 'free-backlinks-generator' ),
			'priority'    => 35,
		)
	);

	$wp_customize->add_section(
		'fbg_logo_sizes',
		array(
			'title'       => __( 'Logo size (header & footer)', 'free-backlinks-generator' ),
			'description' => __( 'Max width and height in pixels. The image keeps its aspect ratio. Desktop is 1024px wide and up; tablet is 600–1023px; mobile is under 600px.', 'free-backlinks-generator' ),
			'panel'       => 'fbg_theme_panel',
			'priority'    => 10,
		)
	);

	$logo_dims = array(
		// Header.
		array( 'fbg_logo_header_w_desktop', __( 'Header — max width (desktop)', 'free-backlinks-generator' ), 180, 40, 320 ),
		array( 'fbg_logo_header_h_desktop', __( 'Header — max height (desktop)', 'free-backlinks-generator' ), 44, 24, 120 ),
		array( 'fbg_logo_header_w_tablet', __( 'Header — max width (tablet)', 'free-backlinks-generator' ), 160, 40, 280 ),
		array( 'fbg_logo_header_h_tablet', __( 'Header — max height (tablet)', 'free-backlinks-generator' ), 40, 24, 100 ),
		array( 'fbg_logo_header_w_mobile', __( 'Header — max width (mobile)', 'free-backlinks-generator' ), 140, 32, 260 ),
		array( 'fbg_logo_header_h_mobile', __( 'Header — max height (mobile)', 'free-backlinks-generator' ), 36, 22, 90 ),
		// Footer.
		array( 'fbg_logo_footer_w_desktop', __( 'Footer — max width (desktop)', 'free-backlinks-generator' ), 200, 40, 360 ),
		array( 'fbg_logo_footer_h_desktop', __( 'Footer — max height (desktop)', 'free-backlinks-generator' ), 48, 24, 140 ),
		array( 'fbg_logo_footer_w_tablet', __( 'Footer — max width (tablet)', 'free-backlinks-generator' ), 180, 40, 320 ),
		array( 'fbg_logo_footer_h_tablet', __( 'Footer — max height (tablet)', 'free-backlinks-generator' ), 44, 24, 120 ),
		array( 'fbg_logo_footer_w_mobile', __( 'Footer — max width (mobile)', 'free-backlinks-generator' ), 160, 32, 280 ),
		array( 'fbg_logo_footer_h_mobile', __( 'Footer — max height (mobile)', 'free-backlinks-generator' ), 40, 22, 100 ),
	);

	foreach ( $logo_dims as $row ) {
		list( $id, $label, $default, $min, $max ) = $row;
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $default,
				'sanitize_callback' => function ( $v ) use ( $min, $max ) {
					return fbg_sanitize_logo_px( $v, $min, $max );
				},
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'   => $label,
				'section' => 'fbg_logo_sizes',
				'type'    => 'number',
				'input_attrs' => array(
					'min' => $min,
					'max' => $max,
				),
			)
		);
	}

	$wp_customize->add_section(
		'fbg_home_stats',
		array(
			'title'       => __( 'Homepage — stats under hero', 'free-backlinks-generator' ),
			'description' => __( 'Four metrics shown on the landing template. Use plain numbers for animated counts (commas are optional). Add a suffix such as % or ★ in the suffix field.', 'free-backlinks-generator' ),
			'panel'       => 'fbg_theme_panel',
			'priority'    => 20,
		)
	);

	$stat_defaults = array(
		array(
			'label'   => __( 'Backlinks Shared', 'free-backlinks-generator' ),
			'value'   => '9328',
			'suffix'  => '',
		),
		array(
			'label'   => __( 'Active Members', 'free-backlinks-generator' ),
			'value'   => '440',
			'suffix'  => '',
		),
		array(
			'label'   => __( 'Approval Rate', 'free-backlinks-generator' ),
			'value'   => '17.2',
			'suffix'  => '%',
		),
		array(
			'label'   => __( 'Average DA Gain', 'free-backlinks-generator' ),
			'value'   => '0.91',
			'suffix'  => '★',
		),
	);

	for ( $i = 1; $i <= 4; $i++ ) {
		$d = $stat_defaults[ $i - 1 ];
		$wp_customize->add_setting(
			'fbg_home_stat_' . $i . '_label',
			array(
				'default'           => $d['label'],
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			'fbg_home_stat_' . $i . '_label',
			array(
				/* translators: %d stat number 1–4 */
				'label'   => sprintf( __( 'Stat %d — label', 'free-backlinks-generator' ), $i ),
				'section' => 'fbg_home_stats',
				'type'    => 'text',
			)
		);
		$wp_customize->add_setting(
			'fbg_home_stat_' . $i . '_value',
			array(
				'default'           => $d['value'],
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			'fbg_home_stat_' . $i . '_value',
			array(
				/* translators: %d stat number 1–4 */
				'label'   => sprintf( __( 'Stat %d — number', 'free-backlinks-generator' ), $i ),
				'section' => 'fbg_home_stats',
				'type'    => 'text',
			)
		);
		$wp_customize->add_setting(
			'fbg_home_stat_' . $i . '_suffix',
			array(
				'default'           => $d['suffix'],
				'sanitize_callback' => function ( $v ) {
					$v = is_string( $v ) ? trim( $v ) : '';
					return function_exists( 'mb_substr' ) ? mb_substr( $v, 0, 6 ) : substr( $v, 0, 6 );
				},
			)
		);
		$wp_customize->add_control(
			'fbg_home_stat_' . $i . '_suffix',
			array(
				/* translators: %d stat number 1–4 */
				'label'       => sprintf( __( 'Stat %d — suffix (optional)', 'free-backlinks-generator' ), $i ),
				'description' => __( 'Examples: % or ★ — shown after the animated number.', 'free-backlinks-generator' ),
				'section'     => 'fbg_home_stats',
				'type'        => 'text',
			)
		);
	}

	$wp_customize->add_section(
		'fbg_footer_titles',
		array(
			'title'       => __( 'Footer — column headings', 'free-backlinks-generator' ),
			'description' => __( 'Shown above each footer menu. Links come from Appearance → Menus (Footer column 1–3).', 'free-backlinks-generator' ),
			'panel'       => 'fbg_theme_panel',
			'priority'    => 30,
		)
	);

	$foot_titles = array(
		'fbg_footer_col1_title' => array(
			'label'   => __( 'Column 1 heading', 'free-backlinks-generator' ),
			'default' => __( 'Platform', 'free-backlinks-generator' ),
		),
		'fbg_footer_col2_title' => array(
			'label'   => __( 'Column 2 heading', 'free-backlinks-generator' ),
			'default' => __( 'Company', 'free-backlinks-generator' ),
		),
		'fbg_footer_col3_title' => array(
			'label'   => __( 'Column 3 heading', 'free-backlinks-generator' ),
			'default' => __( 'Legal', 'free-backlinks-generator' ),
		),
	);
	foreach ( $foot_titles as $setting_id => $cfg ) {
		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => $cfg['default'],
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			$setting_id,
			array(
				'label'   => $cfg['label'],
				'section' => 'fbg_footer_titles',
				'type'    => 'text',
			)
		);
	}

	$wp_customize->add_section(
		'fbg_pro_membership',
		array(
			'title'       => __( 'Pro upgrade — checkout URLs', 'free-backlinks-generator' ),
			'description' => __( 'Paste your Stripe Payment Links, WooCommerce product URLs, or any secure checkout page. These power the Upgrade tab in the member dashboard.', 'free-backlinks-generator' ),
			'panel'       => 'fbg_theme_panel',
			'priority'    => 25,
		)
	);

	$wp_customize->add_setting(
		'fbg_pro_checkout_url',
		array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	$wp_customize->add_control(
		'fbg_pro_checkout_url',
		array(
			'label'       => __( 'Primary checkout URL (e.g. monthly)', 'free-backlinks-generator' ),
			'description' => __( 'Required for the “Upgrade to Pro” checkout button.', 'free-backlinks-generator' ),
			'section'     => 'fbg_pro_membership',
			'type'        => 'url',
		)
	);

	$wp_customize->add_setting(
		'fbg_pro_checkout_url_annual',
		array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	$wp_customize->add_control(
		'fbg_pro_checkout_url_annual',
		array(
			'label'       => __( 'Annual checkout URL (optional)', 'free-backlinks-generator' ),
			'description' => __( 'Second button on the upgrade page; leave empty to hide.', 'free-backlinks-generator' ),
			'section'     => 'fbg_pro_membership',
			'type'        => 'url',
		)
	);

	$wp_customize->add_setting(
		'fbg_pro_billing_portal_url',
		array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	$wp_customize->add_control(
		'fbg_pro_billing_portal_url',
		array(
			'label'       => __( 'Customer billing portal URL (optional)', 'free-backlinks-generator' ),
			'description' => __( 'Stripe Customer Portal or “manage subscription” link shown to Pro members.', 'free-backlinks-generator' ),
			'section'     => 'fbg_pro_membership',
			'type'        => 'url',
		)
	);

	$wp_customize->add_setting(
		'fbg_pro_price_monthly',
		array(
			'default'           => '$19/mo',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'fbg_pro_price_monthly',
		array(
			'label'   => __( 'Displayed price — primary plan', 'free-backlinks-generator' ),
			'section' => 'fbg_pro_membership',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'fbg_pro_price_annual',
		array(
			'default'           => '$190/yr',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'fbg_pro_price_annual',
		array(
			'label'   => __( 'Displayed price — annual plan', 'free-backlinks-generator' ),
			'section' => 'fbg_pro_membership',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'fbg_earn_min_payout_usd',
		array(
			'default'           => 25,
			'sanitize_callback' => static function ( $v ) {
				$n = is_numeric( $v ) ? (float) $v : 25;
				return min( 10000, max( 1, $n ) );
			},
		)
	);
	$wp_customize->add_control(
		'fbg_earn_min_payout_usd',
		array(
			'label'       => __( 'Minimum payout (USD)', 'free-backlinks-generator' ),
			'description' => __( 'Partners must request at least this amount from the Earn tab.', 'free-backlinks-generator' ),
			'section'     => 'fbg_pro_membership',
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 1,
				'step' => 1,
			),
		)
	);
}
add_action( 'customize_register', 'fbg_customize_theme_options_register' );

/**
 * Pro checkout URL (primary).
 *
 * @return string
 */
function fbg_get_pro_checkout_url() {
	$url = get_theme_mod( 'fbg_pro_checkout_url', '' );
	return $url ? esc_url( $url ) : '';
}

/**
 * Pro checkout URL (annual).
 *
 * @return string
 */
function fbg_get_pro_checkout_url_annual() {
	$url = get_theme_mod( 'fbg_pro_checkout_url_annual', '' );
	return $url ? esc_url( $url ) : '';
}

/**
 * Stripe (or other) billing portal for existing subscribers.
 *
 * @return string
 */
function fbg_get_pro_billing_portal_url() {
	$url = get_theme_mod( 'fbg_pro_billing_portal_url', '' );
	return $url ? esc_url( $url ) : '';
}

/**
 * Numeric value for stat animation (strip commas etc.).
 *
 * @param string $value Raw value.
 * @return float
 */
function fbg_home_stat_animation_number( $value ) {
	$s = trim( (string) $value );
	$s = str_replace( ',', '', $s );
	if ( preg_match( '/^-?[0-9]*\.?[0-9]+$/', $s ) ) {
		return (float) $s;
	}
	return 0.0;
}

/**
 * Inline CSS for logo max dimensions.
 */
function fbg_print_logo_dimension_css() {
	$h_wd = (int) get_theme_mod( 'fbg_logo_header_w_desktop', 180 );
	$h_hd = (int) get_theme_mod( 'fbg_logo_header_h_desktop', 44 );
	$h_wt = (int) get_theme_mod( 'fbg_logo_header_w_tablet', 160 );
	$h_ht = (int) get_theme_mod( 'fbg_logo_header_h_tablet', 40 );
	$h_wm = (int) get_theme_mod( 'fbg_logo_header_w_mobile', 140 );
	$h_hm = (int) get_theme_mod( 'fbg_logo_header_h_mobile', 36 );

	$f_wd = (int) get_theme_mod( 'fbg_logo_footer_w_desktop', 200 );
	$f_hd = (int) get_theme_mod( 'fbg_logo_footer_h_desktop', 48 );
	$f_wt = (int) get_theme_mod( 'fbg_logo_footer_w_tablet', 180 );
	$f_ht = (int) get_theme_mod( 'fbg_logo_footer_h_tablet', 44 );
	$f_wm = (int) get_theme_mod( 'fbg_logo_footer_w_mobile', 160 );
	$f_hm = (int) get_theme_mod( 'fbg_logo_footer_h_mobile', 40 );

	$css = ':root{'
		. '--fbg-logo-header-w:' . $h_wd . 'px;'
		. '--fbg-logo-header-h:' . $h_hd . 'px;'
		. '--fbg-logo-footer-w:' . $f_wd . 'px;'
		. '--fbg-logo-footer-h:' . $f_hd . 'px;'
		. '}'
		. '.nav-logo img,.nav-logo .fbg-site-logo__img,.fbg-submit-header__logo img,.fbg-submit-header__logo .fbg-site-logo__img{max-width:var(--fbg-logo-header-w);max-height:var(--fbg-logo-header-h);width:auto;height:auto;object-fit:contain;}'
		. '.fbg-footer__logo img,.fbg-footer__logo .fbg-site-logo__img{max-width:var(--fbg-logo-footer-w);max-height:var(--fbg-logo-footer-h);width:auto;height:auto;object-fit:contain;}'
		. '@media (max-width:1023px){:root{--fbg-logo-header-w:' . $h_wt . 'px;--fbg-logo-header-h:' . $h_ht . 'px;--fbg-logo-footer-w:' . $f_wt . 'px;--fbg-logo-footer-h:' . $f_ht . 'px;}}'
		. '@media (max-width:599px){:root{--fbg-logo-header-w:' . $h_wm . 'px;--fbg-logo-header-h:' . $h_hm . 'px;--fbg-logo-footer-w:' . $f_wm . 'px;--fbg-logo-footer-h:' . $f_hm . 'px;}}';

	wp_add_inline_style( 'fbg-main', $css );
}
add_action( 'wp_enqueue_scripts', 'fbg_print_logo_dimension_css', 12 );
