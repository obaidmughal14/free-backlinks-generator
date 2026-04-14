<?php

/**

 * Animated stats bar (values from Customizer).

 *

 * @package Free_Backlinks_Generator

 */

$stat_defaults = array(

	array(

		'label'  => __( 'Backlinks Shared', 'free-backlinks-generator' ),

		'value'  => '9328',

		'suffix' => '',

	),

	array(

		'label'  => __( 'Active Members', 'free-backlinks-generator' ),

		'value'  => '440',

		'suffix' => '',

	),

	array(

		'label'  => __( 'Approval Rate', 'free-backlinks-generator' ),

		'value'  => '17.2',

		'suffix' => '%',

	),

	array(

		'label'  => __( 'Average DA Gain', 'free-backlinks-generator' ),

		'value'  => '0.91',

		'suffix' => '★',

	),

);



$stats = array();

for ( $i = 1; $i <= 4; $i++ ) {

	$d = $stat_defaults[ $i - 1 ];

	$stats[] = array(

		'label'  => get_theme_mod( 'fbg_home_stat_' . $i . '_label', $d['label'] ),

		'value'  => get_theme_mod( 'fbg_home_stat_' . $i . '_value', $d['value'] ),

		'suffix' => get_theme_mod( 'fbg_home_stat_' . $i . '_suffix', $d['suffix'] ),

	);

}



$targets = array();

foreach ( $stats as $s ) {

	$targets[] = function_exists( 'fbg_home_stat_animation_number' ) ? fbg_home_stat_animation_number( $s['value'] ) : (float) preg_replace( '/[^0-9.]/', '', (string) $s['value'] );

}

$data_stats = implode( ',', $targets );

?>

<section class="fbg-stats-bar" id="fbg-stats-bar" data-stats="<?php echo esc_attr( $data_stats ); ?>">

	<div class="fbg-container fbg-stats-bar__grid">

		<?php foreach ( $stats as $idx => $row ) : ?>

			<?php

			$target = $targets[ $idx ];

			$gid    = 'fbg-stat-g' . (int) $idx;

			// Unique gradient IDs per cell (valid SVG).

			$icons = array(

				'<svg class="fbg-stat__icon-svg" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><defs><linearGradient id="' . esc_attr( $gid ) . '-a" x1="8" y1="40" x2="44" y2="8" gradientUnits="userSpaceOnUse"><stop stop-color="#4f8ef7"/><stop offset="1" stop-color="#2dd4bf"/></linearGradient></defs><circle cx="24" cy="24" r="20" fill="url(#' . esc_attr( $gid ) . '-a)" opacity="0.2"/><path d="M18 30c-2.2 0-4-1.8-4-4s1.8-4 4-4h4m8-8c2.2 0 4 1.8 4 4s-1.8 4-4 4h-4m-8 4h12" stroke="url(#' . esc_attr( $gid ) . '-a)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>',

				'<svg class="fbg-stat__icon-svg" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><defs><linearGradient id="' . esc_attr( $gid ) . '-b" x1="10" y1="38" x2="42" y2="10" gradientUnits="userSpaceOnUse"><stop stop-color="#4f8ef7"/><stop offset="1" stop-color="#a78bfa"/></linearGradient></defs><circle cx="24" cy="24" r="20" fill="url(#' . esc_attr( $gid ) . '-b)" opacity="0.18"/><path d="M24 22a5 5 0 1 0 0-10 5 5 0 0 0 0 10Zm-9 14v-1c0-3.3 2.7-6 6-6h6c3.3 0 6 2.7 6 6v1M33 20a4 4 0 1 0 .01 0h-.01" stroke="url(#' . esc_attr( $gid ) . '-b)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',

				'<svg class="fbg-stat__icon-svg" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><defs><linearGradient id="' . esc_attr( $gid ) . '-c" x1="8" y1="36" x2="40" y2="12" gradientUnits="userSpaceOnUse"><stop stop-color="#2dd4bf"/><stop offset="1" stop-color="#4f8ef7"/></linearGradient></defs><circle cx="24" cy="24" r="20" fill="url(#' . esc_attr( $gid ) . '-c)" opacity="0.15"/><path d="M14 32V22m8 10V16m8 16v-8m8 12V20" stroke="url(#' . esc_attr( $gid ) . '-c)" stroke-width="2.5" stroke-linecap="round"/><path d="M14 32h22" stroke="url(#' . esc_attr( $gid ) . '-c)" stroke-width="1.5" opacity="0.5"/></svg>',

				'<svg class="fbg-stat__icon-svg" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><defs><linearGradient id="' . esc_attr( $gid ) . '-d" x1="12" y1="36" x2="38" y2="10" gradientUnits="userSpaceOnUse"><stop stop-color="#f59e0b"/><stop offset="1" stop-color="#f97316"/></linearGradient></defs><circle cx="24" cy="24" r="20" fill="url(#' . esc_attr( $gid ) . '-d)" opacity="0.16"/><path d="m16 28 6-6 4 4 8-10" stroke="url(#' . esc_attr( $gid ) . '-d)" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"/><path d="M28 16h8v8" stroke="url(#' . esc_attr( $gid ) . '-d)" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"/></svg>',

			);

			$icon = isset( $icons[ $idx ] ) ? $icons[ $idx ] : $icons[0];

			?>

		<div class="fbg-stat">

			<div class="fbg-stat__icon" aria-hidden="true"><?php echo $icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>

			<span class="fbg-stat__num" data-target="<?php echo esc_attr( (string) $target ); ?>" data-suffix="<?php echo esc_attr( $row['suffix'] ); ?>">0</span>

			<span class="fbg-stat__label"><?php echo esc_html( $row['label'] ); ?></span>

		</div>

		<?php endforeach; ?>

	</div>

</section>

