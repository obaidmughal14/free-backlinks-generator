<?php
/**
 * Guest post card (archive grid).
 *
 * @package Free_Backlinks_Generator
 */
$author_id   = (int) get_the_author_meta( 'ID' );
$initials    = '';
$parts       = preg_split( '/\s+/', get_the_author_meta( 'display_name', $author_id ), 2 );
$initials    = strtoupper( substr( $parts[0], 0, 1 ) . ( isset( $parts[1] ) ? substr( $parts[1], 0, 1 ) : '' ) );
$niche_terms = get_the_terms( get_the_ID(), 'fbg_niche' );
$niche_label = ( $niche_terms && ! is_wp_error( $niche_terms ) ) ? $niche_terms[0]->name : '';
$link_count  = (int) get_post_meta( get_the_ID(), '_fbg_backlink_count', true );
$views       = (int) get_post_meta( get_the_ID(), '_fbg_view_count', true );
?>
<article <?php post_class( 'fbg-card' ); ?>>
	<a href="<?php the_permalink(); ?>" class="card-image-link">
		<?php
		if ( has_post_thumbnail() ) {
			the_post_thumbnail( 'fbg_card', array( 'alt' => esc_attr( get_the_title() ) ) );
		} else {
			echo '<div class="fbg-card__placeholder"></div>';
		}
		?>
		<?php if ( $niche_label ) : ?>
			<span class="card-niche-badge"><?php echo esc_html( $niche_label ); ?></span>
		<?php endif; ?>
	</a>
	<div class="card-body">
		<h3 class="card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<p class="card-excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 25 ) ); ?></p>
		<footer class="card-footer">
			<div class="card-author">
				<span class="author-avatar" aria-hidden="true"><?php echo esc_html( $initials ); ?></span>
				<span class="author-name"><?php echo esc_html( get_the_author_meta( 'display_name', $author_id ) ); ?></span>
			</div>
			<div class="card-meta">
				<span>👁 <?php echo esc_html( number_format_i18n( $views ) ); ?></span>
				<span>🔗 <?php echo esc_html( (string) $link_count ); ?> <?php esc_html_e( 'links', 'free-backlinks-generator' ); ?></span>
				<span><?php echo esc_html( get_the_date() ); ?></span>
			</div>
		</footer>
	</div>
</article>
