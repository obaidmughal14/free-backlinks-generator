<?php
/**
 * Standard blog post card (posts index).
 *
 * @package Free_Backlinks_Generator
 */
$author_id = (int) get_the_author_meta( 'ID' );
$parts     = preg_split( '/\s+/', get_the_author_meta( 'display_name', $author_id ), 2 );
$initials  = strtoupper( substr( $parts[0], 0, 1 ) . ( isset( $parts[1] ) ? substr( $parts[1], 0, 1 ) : '' ) );
$cats      = get_the_category();
$cat_label = ( $cats && ! is_wp_error( $cats ) ) ? $cats[0]->name : '';
?>
<article <?php post_class( 'fbg-card fbg-card--blog' ); ?>>
	<a href="<?php the_permalink(); ?>" class="card-image-link">
		<?php
		if ( has_post_thumbnail() ) {
			the_post_thumbnail( 'fbg_card', array( 'alt' => esc_attr( get_the_title() ) ) );
		} else {
			echo '<div class="fbg-card__placeholder"></div>';
		}
		?>
		<?php if ( $cat_label ) : ?>
			<span class="card-niche-badge"><?php echo esc_html( $cat_label ); ?></span>
		<?php endif; ?>
	</a>
	<div class="card-body">
		<h3 class="card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<p class="card-excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 28 ) ); ?></p>
		<footer class="card-footer">
			<div class="card-author">
				<span class="author-avatar" aria-hidden="true"><?php echo esc_html( $initials ); ?></span>
				<span class="author-name"><?php echo esc_html( get_the_author_meta( 'display_name', $author_id ) ); ?></span>
			</div>
			<div class="card-meta">
				<span><?php echo esc_html( get_the_date() ); ?></span>
			</div>
		</footer>
	</div>
</article>
