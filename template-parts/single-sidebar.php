<?php
/**
 * Single guest post sidebar: contact form + sidebar ads.
 *
 * @package Free_Backlinks_Generator
 */

$ads             = function_exists( 'fbg_sidebar_ads_for_template' ) ? fbg_sidebar_ads_for_template() : array();
$mode            = get_option( 'fbg_sidebar_ads_mode', 'slider' );
$autoplay        = max( 0, (int) get_option( 'fbg_sidebar_ads_autoplay', 5 ) );
$has_ads         = ! empty( $ads );
$use_slider      = $has_ads && 'slider' === $mode;
$show_slider_nav = count( $ads ) > 1;
$sidebar_class   = $has_ads ? 'fbg-post-sidebar fbg-post-sidebar--has-ads' : 'fbg-post-sidebar fbg-post-sidebar--contact-only';
?>
<aside class="<?php echo esc_attr( $sidebar_class ); ?>" aria-label="<?php esc_attr_e( 'Sidebar', 'free-backlinks-generator' ); ?>">
	<?php get_template_part( 'template-parts/contact-form-block' ); ?>

	<?php if ( ! empty( $ads ) ) : ?>
		<section class="fbg-sidebar-card fbg-sidebar-card--ads" aria-label="<?php esc_attr_e( 'Sponsored', 'free-backlinks-generator' ); ?>">
			<p class="fbg-sidebar-ads__label"><?php esc_html_e( 'Sponsored', 'free-backlinks-generator' ); ?></p>
			<?php if ( $use_slider ) : ?>
				<div class="fbg-ad-slider" data-autoplay="<?php echo esc_attr( (string) ( $autoplay * 1000 ) ); ?>">
					<div class="fbg-ad-slider__viewport" tabindex="0" data-multiple="<?php echo $show_slider_nav ? '1' : '0'; ?>">
							<?php
							foreach ( $ads as $ad ) :
								$url = get_post_meta( $ad->ID, '_fbg_ad_url', true );
								if ( ! $url ) {
									continue;
								}
								$img_id = get_post_thumbnail_id( $ad->ID );
								$img    = wp_get_attachment_image( $img_id, 'fbg_sidebar_ad', false, array( 'class' => 'fbg-ad-img', 'loading' => 'lazy' ) );
								if ( ! $img ) {
									continue;
								}
								?>
								<div class="fbg-ad-slider__slide">
									<a href="<?php echo esc_url( $url ); ?>" class="fbg-ad-link" target="_blank" rel="noopener sponsored"><?php echo $img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
								</div>
							<?php endforeach; ?>
					</div>
					<?php if ( $show_slider_nav ) : ?>
					<div class="fbg-ad-slider__nav">
						<button type="button" class="fbg-ad-slider__btn fbg-ad-slider__btn--prev" aria-label="<?php esc_attr_e( 'Previous ad', 'free-backlinks-generator' ); ?>">‹</button>
						<button type="button" class="fbg-ad-slider__btn fbg-ad-slider__btn--next" aria-label="<?php esc_attr_e( 'Next ad', 'free-backlinks-generator' ); ?>">›</button>
					</div>
					<?php endif; ?>
				</div>
			<?php else : ?>
				<div class="fbg-ad-stack">
					<?php
					foreach ( $ads as $ad ) :
						$url = get_post_meta( $ad->ID, '_fbg_ad_url', true );
						if ( ! $url ) {
							continue;
						}
						$img_id = get_post_thumbnail_id( $ad->ID );
						$img    = wp_get_attachment_image( $img_id, 'fbg_sidebar_ad', false, array( 'class' => 'fbg-ad-img', 'loading' => 'lazy' ) );
						if ( ! $img ) {
							continue;
						}
						?>
						<a href="<?php echo esc_url( $url ); ?>" class="fbg-ad-link fbg-ad-stack__item" target="_blank" rel="noopener sponsored"><?php echo $img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</section>
	<?php endif; ?>
</aside>
