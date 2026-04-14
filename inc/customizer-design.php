<?php
/**
 * Customizer: global design system (colors, typography, layout, behavior).
 *
 * @package Free_Backlinks_Generator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether the front-end dark / light toggle is shown.
 *
 * @return bool
 */
function fbg_show_theme_toggle() {
	return (bool) get_theme_mod( 'fbg_show_theme_toggle', true );
}

/**
 * Whether external links open in a new tab (content + menus).
 *
 * @return bool
 */
function fbg_external_links_new_tab_enabled() {
	return (bool) get_theme_mod( 'fbg_external_links_new_tab', true );
}

/**
 * Sanitize hex color; fall back if invalid.
 *
 * @param mixed  $color    Value.
 * @param string $fallback Valid hex.
 * @return string
 */
function fbg_sanitize_hex_color_or( $color, $fallback ) {
	$c = sanitize_hex_color( is_string( $color ) ? $color : '' );
	return $c ? $c : $fallback;
}

/**
 * Container max width (px).
 *
 * @param mixed $v Value.
 * @return int
 */
function fbg_sanitize_container_max( $v ) {
	$n = absint( $v );
	return min( 1320, max( 960, $n ) );
}

/**
 * Sanitize custom Google Fonts CSS2 URL (subset allowed).
 *
 * @param string $url URL.
 * @return string
 */
function fbg_sanitize_google_fonts_url( $url ) {
	$url = trim( (string) $url );
	if ( '' === $url ) {
		return '';
	}
	if ( ! preg_match( '#^https://fonts\.googleapis\.com/css2#i', $url ) ) {
		return '';
	}
	return esc_url_raw( $url );
}

/**
 * Safe font family token for CSS (letters, numbers, spaces, hyphen).
 *
 * @param mixed $name Raw.
 * @return string
 */
function fbg_sanitize_font_family_name( $name, $fallback = 'sans-serif' ) {
	$s = sanitize_text_field( is_string( $name ) ? $name : '' );
	$s = preg_replace( '/[^a-zA-Z0-9 \\-]/', '', $s );
	return '' !== $s ? $s : $fallback;
}

/**
 * Typography presets: Google Fonts URL + CSS stack names.
 *
 * @return array<string, array{label:string,url:string,display:string,body:string,mono:string}>
 */
function fbg_typography_presets() {
	return array(
		'default' => array(
			'label'   => __( 'Syne + DM Sans (default)', 'free-backlinks-generator' ),
			'url'     => 'https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&family=JetBrains+Mono:wght@400;500&display=swap',
			'display' => "'Syne', ui-sans-serif, system-ui, sans-serif",
			'body'    => "'DM Sans', ui-sans-serif, system-ui, sans-serif",
			'mono'    => "'JetBrains Mono', ui-monospace, monospace",
		),
		'outfit_inter' => array(
			'label'   => __( 'Outfit + Inter', 'free-backlinks-generator' ),
			'url'     => 'https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:ital,opsz,wght@0,14..32,300;0,14..32,400;0,14..32,500;0,14..32,600;1,14..32,400&family=JetBrains+Mono:wght@400;500&display=swap',
			'display' => "'Outfit', ui-sans-serif, system-ui, sans-serif",
			'body'    => "'Inter', ui-sans-serif, system-ui, sans-serif",
			'mono'    => "'JetBrains Mono', ui-monospace, monospace",
		),
		'playfair_source' => array(
			'label'   => __( 'Playfair Display + Source Sans 3', 'free-backlinks-generator' ),
			'url'     => 'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Source+Sans+3:ital,wght@0,400;0,600;0,700;1,400&family=JetBrains+Mono:wght@400;500&display=swap',
			'display' => "'Playfair Display', Georgia, 'Times New Roman', serif",
			'body'    => "'Source Sans 3', ui-sans-serif, system-ui, sans-serif",
			'mono'    => "'JetBrains Mono', ui-monospace, monospace",
		),
		'space_nunito' => array(
			'label'   => __( 'Space Grotesk + Nunito Sans', 'free-backlinks-generator' ),
			'url'     => 'https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Nunito+Sans:ital,opsz,wght@0,6..12,400;0,6..12,600;0,6..12,700;1,6..12,400&family=JetBrains+Mono:wght@400;500&display=swap',
			'display' => "'Space Grotesk', ui-sans-serif, system-ui, sans-serif",
			'body'    => "'Nunito Sans', ui-sans-serif, system-ui, sans-serif",
			'mono'    => "'JetBrains Mono', ui-monospace, monospace",
		),
		'fraunces_work' => array(
			'label'   => __( 'Fraunces + Work Sans', 'free-backlinks-generator' ),
			'url'     => 'https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,600;0,9..144,700;0,9..144,800;1,9..144,600&family=Work+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=JetBrains+Mono:wght@400;500&display=swap',
			'display' => "'Fraunces', Georgia, 'Times New Roman', serif",
			'body'    => "'Work Sans', ui-sans-serif, system-ui, sans-serif",
			'mono'    => "'JetBrains Mono', ui-monospace, monospace",
		),
	);
}

/**
 * Google Fonts stylesheet URL for current settings.
 *
 * @return string
 */
function fbg_get_google_fonts_stylesheet_url() {
	$preset = get_theme_mod( 'fbg_font_preset', 'default' );
	$all     = fbg_typography_presets();
	if ( 'custom' === $preset ) {
		$custom = fbg_sanitize_google_fonts_url( get_theme_mod( 'fbg_fonts_custom_url', '' ) );
		if ( $custom ) {
			return $custom;
		}
		return $all['default']['url'];
	}
	if ( isset( $all[ $preset ]['url'] ) ) {
		return $all[ $preset ]['url'];
	}
	return $all['default']['url'];
}

/**
 * @return array{display:string,body:string,mono:string}
 */
function fbg_get_active_typography_stacks() {
	$preset = get_theme_mod( 'fbg_font_preset', 'default' );
	$all     = fbg_typography_presets();
	if ( 'custom' === $preset ) {
		$custom = fbg_sanitize_google_fonts_url( get_theme_mod( 'fbg_fonts_custom_url', '' ) );
		if ( $custom ) {
			$disp = fbg_sanitize_font_family_name( get_theme_mod( 'fbg_font_custom_display', 'Syne' ), 'Syne' );
			$body = fbg_sanitize_font_family_name( get_theme_mod( 'fbg_font_custom_body', 'DM Sans' ), 'DM Sans' );
			return array(
				'display' => "'" . $disp . "', ui-sans-serif, system-ui, sans-serif",
				'body'    => "'" . $body . "', ui-sans-serif, system-ui, sans-serif",
				'mono'    => "'JetBrains Mono', ui-monospace, monospace",
			);
		}
		return array(
			'display' => $all['default']['display'],
			'body'    => $all['default']['body'],
			'mono'    => $all['default']['mono'],
		);
	}
	if ( isset( $all[ $preset ] ) ) {
		return array(
			'display' => $all[ $preset ]['display'],
			'body'    => $all[ $preset ]['body'],
			'mono'    => $all[ $preset ]['mono'],
		);
	}
	return array(
		'display' => $all['default']['display'],
		'body'    => $all['default']['body'],
		'mono'    => $all['default']['mono'],
	);
}

/**
 * Border radius map from preset key.
 *
 * @param string $preset compact|default|roomy.
 * @return array{sm:int,md:int,lg:int}
 */
function fbg_radius_preset_values( $preset ) {
	$map = array(
		'compact' => array( 'sm' => 4, 'md' => 8, 'lg' => 14 ),
		'default' => array( 'sm' => 6, 'md' => 12, 'lg' => 20 ),
		'roomy'   => array( 'sm' => 8, 'md' => 14, 'lg' => 24 ),
	);
	return isset( $map[ $preset ] ) ? $map[ $preset ] : $map['default'];
}

/**
 * Register design controls (same panel as other FBG options).
 *
 * @param WP_Customize_Manager $wp WP_Customize_Manager.
 */
function fbg_customizer_design_register( $wp ) {
	$wp->add_section(
		'fbg_design_colors_light',
		array(
			'title'       => __( 'Colors — light mode', 'free-backlinks-generator' ),
			'description' => __( 'These map to CSS variables on :root. Pickers use the theme defaults until you change them.', 'free-backlinks-generator' ),
			'panel'       => 'fbg_theme_panel',
			'priority'    => 12,
		)
	);

	$light = array(
		'fbg_color_primary'           => array( 'label' => __( 'Primary (dark surfaces, footer)', 'free-backlinks-generator' ), 'default' => '#0a0f1e' ),
		'fbg_color_accent'            => array( 'label' => __( 'Accent', 'free-backlinks-generator' ), 'default' => '#4f8ef7' ),
		'fbg_color_accent_dark'       => array( 'label' => __( 'Accent — hover / dark variant', 'free-backlinks-generator' ), 'default' => '#2d6fd4' ),
		'fbg_color_mint'              => array( 'label' => __( 'Mint (success highlights)', 'free-backlinks-generator' ), 'default' => '#00d4aa' ),
		'fbg_color_bg'                => array( 'label' => __( 'Page background', 'free-backlinks-generator' ), 'default' => '#f8f9ff' ),
		'fbg_color_surface'           => array( 'label' => __( 'Cards / surfaces', 'free-backlinks-generator' ), 'default' => '#ffffff' ),
		'fbg_color_border'            => array( 'label' => __( 'Borders', 'free-backlinks-generator' ), 'default' => '#e5e7f0' ),
		'fbg_color_text'              => array( 'label' => __( 'Body text', 'free-backlinks-generator' ), 'default' => '#0a0f1e' ),
		'fbg_color_text_secondary'    => array( 'label' => __( 'Secondary text', 'free-backlinks-generator' ), 'default' => '#6b7280' ),
	);

	foreach ( $light as $id => $cfg ) {
		$wp->add_setting(
			$id,
			array(
				'default'           => $cfg['default'],
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);
		$wp->add_control(
			new WP_Customize_Color_Control(
				$wp,
				$id,
				array(
					'label'   => $cfg['label'],
					'section' => 'fbg_design_colors_light',
				)
			)
		);
	}

	$wp->add_section(
		'fbg_design_colors_dark',
		array(
			'title'       => __( 'Colors — dark mode', 'free-backlinks-generator' ),
			'description' => __( 'Applied when visitors choose dark mode (or system preference). Primary stays dark for sidebars and footers.', 'free-backlinks-generator' ),
			'panel'       => 'fbg_theme_panel',
			'priority'    => 13,
		)
	);

	$dark = array(
		'fbg_color_dark_accent'         => array( 'label' => __( 'Accent', 'free-backlinks-generator' ), 'default' => '#6ba3ff' ),
		'fbg_color_dark_accent_dark'    => array( 'label' => __( 'Accent — hover', 'free-backlinks-generator' ), 'default' => '#4f8ef7' ),
		'fbg_color_dark_mint'           => array( 'label' => __( 'Mint', 'free-backlinks-generator' ), 'default' => '#2dd4bf' ),
		'fbg_color_dark_bg'             => array( 'label' => __( 'Page background', 'free-backlinks-generator' ), 'default' => '#0b1020' ),
		'fbg_color_dark_surface'        => array( 'label' => __( 'Cards / surfaces', 'free-backlinks-generator' ), 'default' => '#131b2e' ),
		'fbg_color_dark_border'         => array( 'label' => __( 'Borders', 'free-backlinks-generator' ), 'default' => '#2a3548' ),
		'fbg_color_dark_text'           => array( 'label' => __( 'Body text', 'free-backlinks-generator' ), 'default' => '#e8ebf4' ),
		'fbg_color_dark_text_secondary' => array( 'label' => __( 'Secondary text', 'free-backlinks-generator' ), 'default' => '#9ca8c0' ),
	);

	foreach ( $dark as $id => $cfg ) {
		$wp->add_setting(
			$id,
			array(
				'default'           => $cfg['default'],
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);
		$wp->add_control(
			new WP_Customize_Color_Control(
				$wp,
				$id,
				array(
					'label'   => $cfg['label'],
					'section' => 'fbg_design_colors_dark',
				)
			)
		);
	}

	$wp->add_section(
		'fbg_design_typography',
		array(
			'title'       => __( 'Typography & fonts', 'free-backlinks-generator' ),
			'description' => __( 'Loads Google Fonts on the front end. Custom URL must start with https://fonts.googleapis.com/css2', 'free-backlinks-generator' ),
			'panel'       => 'fbg_theme_panel',
			'priority'    => 14,
		)
	);

	$choices = array();
	foreach ( fbg_typography_presets() as $key => $row ) {
		$choices[ $key ] = $row['label'];
	}
	$choices['custom'] = __( 'Custom Google Fonts URL', 'free-backlinks-generator' );

	$wp->add_setting(
		'fbg_font_preset',
		array(
			'default'           => 'default',
			'sanitize_callback' => static function ( $v ) {
				$ok = array_keys( fbg_typography_presets() );
				$ok[] = 'custom';
				return in_array( (string) $v, $ok, true ) ? (string) $v : 'default';
			},
		)
	);
	$wp->add_control(
		'fbg_font_preset',
		array(
			'label'   => __( 'Font pairing', 'free-backlinks-generator' ),
			'section' => 'fbg_design_typography',
			'type'    => 'select',
			'choices' => $choices,
		)
	);

	$wp->add_setting(
		'fbg_fonts_custom_url',
		array(
			'default'           => '',
			'sanitize_callback' => 'fbg_sanitize_google_fonts_url',
		)
	);
	$wp->add_control(
		'fbg_fonts_custom_url',
		array(
			'label'       => __( 'Custom Google Fonts CSS URL', 'free-backlinks-generator' ),
			'description' => __( 'Only when “Custom” is selected. Paste the full css2?family=… link from Google Fonts.', 'free-backlinks-generator' ),
			'section'     => 'fbg_design_typography',
			'type'        => 'url',
		)
	);

	$wp->add_setting(
		'fbg_font_custom_display',
		array(
			'default'           => 'Syne',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp->add_control(
		'fbg_font_custom_display',
		array(
			'label'       => __( 'Custom — display font name', 'free-backlinks-generator' ),
			'description' => __( 'Family name as in Google Fonts (headings).', 'free-backlinks-generator' ),
			'section'     => 'fbg_design_typography',
			'type'        => 'text',
		)
	);

	$wp->add_setting(
		'fbg_font_custom_body',
		array(
			'default'           => 'DM Sans',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp->add_control(
		'fbg_font_custom_body',
		array(
			'label'       => __( 'Custom — body font name', 'free-backlinks-generator' ),
			'description' => __( 'Family name as in Google Fonts (paragraphs).', 'free-backlinks-generator' ),
			'section'     => 'fbg_design_typography',
			'type'        => 'text',
		)
	);

	$wp->add_section(
		'fbg_design_layout',
		array(
			'title'       => __( 'Layout', 'free-backlinks-generator' ),
			'description' => __( 'Content width and corner rounding.', 'free-backlinks-generator' ),
			'panel'       => 'fbg_theme_panel',
			'priority'    => 15,
		)
	);

	$wp->add_setting(
		'fbg_container_max_width',
		array(
			'default'           => 1120,
			'sanitize_callback' => 'fbg_sanitize_container_max',
		)
	);
	$wp->add_control(
		'fbg_container_max_width',
		array(
			'label'       => __( 'Max content width (px)', 'free-backlinks-generator' ),
			'description' => __( 'Between 960 and 1320. Used by .fbg-container and similar layouts.', 'free-backlinks-generator' ),
			'section'     => 'fbg_design_layout',
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 960,
				'max'  => 1320,
				'step' => 10,
			),
		)
	);

	$wp->add_setting(
		'fbg_radius_preset',
		array(
			'default'           => 'default',
			'sanitize_callback' => static function ( $v ) {
				return in_array( (string) $v, array( 'compact', 'default', 'roomy' ), true ) ? (string) $v : 'default';
			},
		)
	);
	$wp->add_control(
		'fbg_radius_preset',
		array(
			'label'   => __( 'Corner radius style', 'free-backlinks-generator' ),
			'section' => 'fbg_design_layout',
			'type'    => 'select',
			'choices' => array(
				'compact' => __( 'Compact', 'free-backlinks-generator' ),
				'default' => __( 'Balanced (default)', 'free-backlinks-generator' ),
				'roomy'   => __( 'Roomy', 'free-backlinks-generator' ),
			),
		)
	);

	$wp->add_section(
		'fbg_design_behavior',
		array(
			'title'       => __( 'Site behavior', 'free-backlinks-generator' ),
			'description' => __( 'Optional switches for marketplace buyers.', 'free-backlinks-generator' ),
			'panel'       => 'fbg_theme_panel',
			'priority'    => 16,
		)
	);

	$wp->add_setting(
		'fbg_show_theme_toggle',
		array(
			'default'           => true,
			'sanitize_callback' => static function ( $v ) {
				return (bool) $v;
			},
		)
	);
	$wp->add_control(
		'fbg_show_theme_toggle',
		array(
			'label'       => __( 'Show dark / light mode toggle', 'free-backlinks-generator' ),
			'description' => __( 'Turn off if you only want one color scheme.', 'free-backlinks-generator' ),
			'section'     => 'fbg_design_behavior',
			'type'        => 'checkbox',
		)
	);

	$wp->add_setting(
		'fbg_external_links_new_tab',
		array(
			'default'           => true,
			'sanitize_callback' => static function ( $v ) {
				return (bool) $v;
			},
		)
	);
	$wp->add_control(
		'fbg_external_links_new_tab',
		array(
			'label'       => __( 'Open external links in a new tab', 'free-backlinks-generator' ),
			'description' => __( 'Adds target="_blank" and rel noopener to off-site links in content and menus.', 'free-backlinks-generator' ),
			'section'     => 'fbg_design_behavior',
			'type'        => 'checkbox',
		)
	);

	$wp->add_section(
		'fbg_design_footer_text',
		array(
			'title'       => __( 'Footer text', 'free-backlinks-generator' ),
			'description' => __( 'Bottom bar on most pages. HTML is not allowed; plain text only.', 'free-backlinks-generator' ),
			'panel'       => 'fbg_theme_panel',
			'priority'    => 17,
		)
	);

	$wp->add_setting(
		'fbg_footer_tagline',
		array(
			'default'           => 'Free to join. Real backlinks. Real results.',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp->add_control(
		'fbg_footer_tagline',
		array(
			'label'       => __( 'Tagline (after site name)', 'free-backlinks-generator' ),
			'description' => __( 'Shown next to the copyright year and site title.', 'free-backlinks-generator' ),
			'section'     => 'fbg_design_footer_text',
			'type'        => 'text',
		)
	);

	$wp->add_setting(
		'fbg_footer_show_credit',
		array(
			'default'           => true,
			'sanitize_callback' => static function ( $v ) {
				return (bool) $v;
			},
		)
	);
	$wp->add_control(
		'fbg_footer_show_credit',
		array(
			'label'   => __( 'Show designer / credit line', 'free-backlinks-generator' ),
			'section' => 'fbg_design_footer_text',
			'type'    => 'checkbox',
		)
	);

	$wp->add_setting(
		'fbg_footer_credit_lead',
		array(
			'default'           => 'Designed and developed by',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp->add_control(
		'fbg_footer_credit_lead',
		array(
			'label'   => __( 'Credit — lead text', 'free-backlinks-generator' ),
			'section' => 'fbg_design_footer_text',
			'type'    => 'text',
		)
	);

	$wp->add_setting(
		'fbg_footer_credit_name',
		array(
			'default'           => 'Devigon Tech',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp->add_control(
		'fbg_footer_credit_name',
		array(
			'label'   => __( 'Credit — brand / author name', 'free-backlinks-generator' ),
			'section' => 'fbg_design_footer_text',
			'type'    => 'text',
		)
	);

	$wp->add_setting(
		'fbg_footer_credit_url',
		array(
			'default'           => 'https://devigontech.com',
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	$wp->add_control(
		'fbg_footer_credit_url',
		array(
			'label'   => __( 'Credit — link URL', 'free-backlinks-generator' ),
			'section' => 'fbg_design_footer_text',
			'type'    => 'url',
		)
	);
}
add_action( 'customize_register', 'fbg_customizer_design_register' );

/**
 * CSS variables from Customizer (front + block editor).
 *
 * @return string
 */
function fbg_get_design_inline_css() {
	$l = array(
		'primary'           => array( 'fbg_color_primary', '#0a0f1e' ),
		'accent'            => array( 'fbg_color_accent', '#4f8ef7' ),
		'accent-dark'       => array( 'fbg_color_accent_dark', '#2d6fd4' ),
		'mint'              => array( 'fbg_color_mint', '#00d4aa' ),
		'bg'                => array( 'fbg_color_bg', '#f8f9ff' ),
		'surface'           => array( 'fbg_color_surface', '#ffffff' ),
		'border'            => array( 'fbg_color_border', '#e5e7f0' ),
		'text'              => array( 'fbg_color_text', '#0a0f1e' ),
		'text-secondary'    => array( 'fbg_color_text_secondary', '#6b7280' ),
	);

	$parts = array( ':root{' );
	foreach ( $l as $var => $row ) {
		$hex     = fbg_sanitize_hex_color_or( get_theme_mod( $row[0], $row[1] ), $row[1] );
		$parts[] = '--color-' . $var . ':' . $hex . ';';
	}

	$stacks  = fbg_get_active_typography_stacks();
	$parts[] = '--font-display:' . $stacks['display'] . ';';
	$parts[] = '--font-body:' . $stacks['body'] . ';';
	$parts[] = '--font-mono:' . $stacks['mono'] . ';';

	$cw      = fbg_sanitize_container_max( get_theme_mod( 'fbg_container_max_width', 1120 ) );
	$parts[] = '--fbg-container-max:' . $cw . 'px;';

	$rad     = fbg_radius_preset_values( get_theme_mod( 'fbg_radius_preset', 'default' ) );
	$parts[] = '--radius-sm:' . (int) $rad['sm'] . 'px;';
	$parts[] = '--radius-md:' . (int) $rad['md'] . 'px;';
	$parts[] = '--radius-lg:' . (int) $rad['lg'] . 'px;';
	$parts[] = '}';

	$d = array(
		'primary'        => array( '', '#0a0f1e' ),
		'accent'         => array( 'fbg_color_dark_accent', '#6ba3ff' ),
		'accent-dark'    => array( 'fbg_color_dark_accent_dark', '#4f8ef7' ),
		'mint'           => array( 'fbg_color_dark_mint', '#2dd4bf' ),
		'bg'             => array( 'fbg_color_dark_bg', '#0b1020' ),
		'surface'        => array( 'fbg_color_dark_surface', '#131b2e' ),
		'border'         => array( 'fbg_color_dark_border', '#2a3548' ),
		'text'           => array( 'fbg_color_dark_text', '#e8ebf4' ),
		'text-secondary' => array( 'fbg_color_dark_text_secondary', '#9ca8c0' ),
	);

	$parts[] = 'html[data-theme="dark"]{';
	foreach ( $d as $var => $row ) {
		if ( '' === $row[0] ) {
			$hex = $row[1];
		} else {
			$hex = fbg_sanitize_hex_color_or( get_theme_mod( $row[0], $row[1] ), $row[1] );
		}
		$parts[] = '--color-' . $var . ':' . $hex . ';';
	}
	$parts[] = '--color-text-muted:#6b7a95;';
	$parts[] = '--color-amber:#fbbf24;';
	$parts[] = '--color-red:#f87171;';
	$parts[] = '}';

	return implode( '', $parts );
}

/**
 * Print CSS variables from Customizer (after static theme CSS).
 */
function fbg_print_design_customizer_css() {
	wp_add_inline_style( 'fbg-main', fbg_get_design_inline_css() );
}
add_action( 'wp_enqueue_scripts', 'fbg_print_design_customizer_css', 14 );

/**
 * Match block editor to Customizer colors and fonts.
 */
function fbg_enqueue_block_editor_design_css() {
	$fonts = fbg_get_google_fonts_stylesheet_url();
	if ( $fonts ) {
		wp_enqueue_style( 'fbg-editor-fonts', $fonts, array(), null );
	}
	$handle = 'fbg-editor-design-vars';
	wp_register_style( $handle, false, $fonts ? array( 'fbg-editor-fonts' ) : array(), FBG_VERSION );
	wp_enqueue_style( $handle );
	wp_add_inline_style( $handle, fbg_get_design_inline_css() );
}
add_action( 'enqueue_block_editor_assets', 'fbg_enqueue_block_editor_design_css', 20 );
