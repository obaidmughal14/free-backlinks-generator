<?php
/**
 * Site logo: header, footer, or submit header.
 *
 * @package Free_Backlinks_Generator
 *
 * @param array<string, string> $args {
 *     @type string $context header|footer|submit
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$fbg_logo_ctx = 'header';
if ( isset( $args ) && is_array( $args ) && ! empty( $args['context'] ) ) {
	$fbg_logo_ctx = sanitize_key( $args['context'] );
} elseif ( isset( $context ) ) {
	$fbg_logo_ctx = sanitize_key( $context );
}

$logo_id = get_theme_mod( 'custom_logo' );
$alt     = get_bloginfo( 'name', 'display' );
$svg     = get_theme_file_uri( 'assets/images/logo.svg' );
$iclass  = 'fbg-site-logo__img';

if ( 'footer' === $fbg_logo_ctx ) {
	?>
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="fbg-footer__logo">
		<?php
		if ( $logo_id ) {
			echo wp_get_attachment_image(
				(int) $logo_id,
				'full',
				false,
				array(
					'class'    => $iclass,
					'alt'      => $alt,
					'loading'  => 'lazy',
					'decoding' => 'async',
				)
			);
		} else {
			printf(
				'<img src="%1$s" class="%2$s fbg-footer__logo-img--theme-svg" alt="%3$s" width="180" height="44" loading="lazy" decoding="async">',
				esc_url( $svg ),
				esc_attr( $iclass ),
				esc_attr( $alt )
			);
		}
		?>
	</a>
	<?php
} elseif ( 'submit' === $fbg_logo_ctx ) {
	?>
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="fbg-submit-header__logo">
		<?php
		if ( $logo_id ) {
			echo wp_get_attachment_image(
				(int) $logo_id,
				'full',
				false,
				array(
					'class'    => $iclass,
					'alt'      => $alt,
					'loading'  => 'eager',
					'decoding' => 'async',
				)
			);
		} else {
			printf(
				'<img src="%1$s" class="%2$s" alt="%3$s" width="180" height="44" loading="eager" decoding="async">',
				esc_url( $svg ),
				esc_attr( $iclass ),
				esc_attr( $alt )
			);
		}
		?>
	</a>
	<?php
} else {
	?>
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="nav-logo">
		<?php
		if ( $logo_id ) {
			echo wp_get_attachment_image(
				(int) $logo_id,
				'full',
				false,
				array(
					'class'    => $iclass,
					'alt'      => $alt,
					'loading'  => 'eager',
					'decoding' => 'async',
				)
			);
		} else {
			printf(
				'<img src="%1$s" class="%2$s" alt="%3$s" width="180" height="44" loading="eager" decoding="async">',
				esc_url( $svg ),
				esc_attr( $iclass ),
				esc_attr( $alt )
			);
		}
		?>
	</a>
	<?php
}
