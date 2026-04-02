<?php
/**
 * Customizer: social profile URLs + optional site tagline for meta.
 *
 * @package Free_Backlinks_Generator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register social + SEO fields.
 *
 * @param WP_Customize_Manager $wp_customize Customizer.
 */
function fbg_customize_social_register( $wp_customize ) {
	$wp_customize->add_section(
		'fbg_social_section',
		array(
			'title'       => __( 'Social & discoverability', 'free-backlinks-generator' ),
			'description' => __( 'Add full URLs (https://). Empty fields hide that icon. Used in the footer and structured data.', 'free-backlinks-generator' ),
			'priority'    => 90,
		)
	);

	$networks = array(
		'fbg_social_facebook'  => __( 'Facebook URL', 'free-backlinks-generator' ),
		'fbg_social_x'         => __( 'X (Twitter) URL', 'free-backlinks-generator' ),
		'fbg_social_instagram' => __( 'Instagram URL', 'free-backlinks-generator' ),
		'fbg_social_tiktok'    => __( 'TikTok URL', 'free-backlinks-generator' ),
		'fbg_social_linkedin'  => __( 'LinkedIn URL', 'free-backlinks-generator' ),
		'fbg_social_youtube'   => __( 'YouTube URL', 'free-backlinks-generator' ),
	);

	foreach ( $networks as $setting_id => $label ) {
		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => '',
				'sanitize_callback' => 'esc_url_raw',
			)
		);
		$wp_customize->add_control(
			$setting_id,
			array(
				'label'   => $label,
				'section' => 'fbg_social_section',
				'type'    => 'url',
			)
		);
	}

	$wp_customize->add_setting(
		'fbg_home_meta_description',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_textarea_field',
		)
	);
	$wp_customize->add_control(
		'fbg_home_meta_description',
		array(
			'label'       => __( 'Homepage meta description', 'free-backlinks-generator' ),
			'description' => __( 'Optional. Shown in search snippets when the homepage has no other SEO plugin description (keep under ~160 characters, natural language).', 'free-backlinks-generator' ),
			'section'     => 'fbg_social_section',
			'type'        => 'textarea',
		)
	);
}
add_action( 'customize_register', 'fbg_customize_social_register' );

/**
 * Valid social URL for output.
 *
 * @param string $url URL.
 * @return string Empty if invalid.
 */
function fbg_social_url_or_empty( $url ) {
	$url = esc_url_raw( $url );
	if ( '' === $url || ! preg_match( '#^https?://#i', $url ) ) {
		return '';
	}
	return $url;
}
