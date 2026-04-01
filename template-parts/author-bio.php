<?php
/**
 * Author bio card.
 *
 * @package Free_Backlinks_Generator
 */
$author_id = (int) get_the_author_meta( 'ID' );
$bio       = get_the_author_meta( 'description', $author_id );
$site      = get_user_meta( $author_id, '_fbg_website_url', true );
$niche     = get_user_meta( $author_id, '_fbg_niche', true );
$niches    = fbg_niche_options();
$niche_lbl = isset( $niches[ $niche ] ) ? $niches[ $niche ] : $niche;
?>
<div class="fbg-author-bio">
	<span class="author-avatar author-avatar--lg" aria-hidden="true"><?php echo esc_html( strtoupper( substr( get_the_author_meta( 'display_name', $author_id ), 0, 1 ) ) ); ?></span>
	<div>
		<strong class="fbg-author-bio__name"><?php echo esc_html( get_the_author_meta( 'display_name', $author_id ) ); ?></strong>
		<?php if ( $bio ) : ?>
			<p><?php echo esc_html( wp_trim_words( $bio, 40 ) ); ?></p>
		<?php endif; ?>
		<p class="fbg-author-bio__meta">
			<?php if ( $niche_lbl ) : ?>
				<span><?php esc_html_e( 'Niche:', 'free-backlinks-generator' ); ?> <?php echo esc_html( is_string( $niche_lbl ) ? $niche_lbl : '' ); ?></span>
			<?php endif; ?>
			<?php if ( $site ) : ?>
				<span><?php esc_html_e( 'Website:', 'free-backlinks-generator' ); ?> <a href="<?php echo esc_url( $site ); ?>" target="_blank" rel="noopener"><?php echo esc_html( wp_parse_url( $site, PHP_URL_HOST ) ?: $site ); ?></a></span>
			<?php endif; ?>
		</p>
		<a href="<?php echo esc_url( get_author_posts_url( $author_id ) ); ?>"><?php printf( esc_html__( 'View all posts by %s →', 'free-backlinks-generator' ), esc_html( get_the_author_meta( 'display_name', $author_id ) ) ); ?></a>
	</div>
</div>
