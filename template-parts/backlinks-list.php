<?php
/**
 * Backlinks box on single post.
 *
 * @package Free_Backlinks_Generator
 */
$links = get_post_meta( get_the_ID(), '_fbg_backlinks_array', true );
if ( ! $links || ! is_array( $links ) ) {
	return;
}
?>
<div class="fbg-backlinks-box">
	<h3>🔗 <?php esc_html_e( 'Backlinks in This Post', 'free-backlinks-generator' ); ?></h3>
	<ul class="fbg-backlinks-box__list">
		<?php foreach ( $links as $link ) : ?>
			<?php
			$anchor = isset( $link['anchor'] ) ? $link['anchor'] : '';
			$url    = isset( $link['url'] ) ? $link['url'] : '';
			if ( ! $url ) {
				continue;
			}
			$rel = apply_filters( 'fbg_backlink_rel', 'nofollow ugc', get_the_ID(), $url );
			?>
			<li class="backlink-item">
				<span class="backlink-anchor"><?php echo esc_html( $anchor ); ?></span>
				<a href="<?php echo esc_url( $url ); ?>" rel="<?php echo esc_attr( $rel ); ?>" target="_blank" class="backlink-url mono"><?php echo esc_html( $url ); ?></a>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
