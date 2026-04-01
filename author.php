<?php
/**
 * Author archive (guest posts).
 *
 * @package Free_Backlinks_Generator
 */

get_header();
$author = get_queried_object();
?>
<main id="main-content" class="fbg-main fbg-container fbg-section">
	<h1 class="fbg-page-title">
		<?php
		printf(
			/* translators: %s author name */
			esc_html__( 'Posts by %s', 'free-backlinks-generator' ),
			esc_html( $author->display_name ?? '' )
		);
		?>
	</h1>
	<div class="fbg-post-grid">
		<?php
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/blog', 'card' );
			endwhile;
			the_posts_navigation();
		else :
			?>
			<p><?php esc_html_e( 'No published guest posts yet.', 'free-backlinks-generator' ); ?></p>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
