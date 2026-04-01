<?php
/**
 * Search results.
 *
 * @package Free_Backlinks_Generator
 */

get_header();
?>
<main id="main-content" class="fbg-main fbg-container fbg-section">
	<h1 class="fbg-page-title">
		<?php
		printf(
			/* translators: %s search query */
			esc_html__( 'Search results for "%s"', 'free-backlinks-generator' ),
			esc_html( get_search_query() )
		);
		?>
	</h1>
	<?php if ( have_posts() ) : ?>
		<ul class="fbg-search-list">
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
				<?php
			endwhile;
			?>
		</ul>
		<?php the_posts_navigation(); ?>
	<?php else : ?>
		<p><?php esc_html_e( 'No results found.', 'free-backlinks-generator' ); ?></p>
	<?php endif; ?>
</main>
<?php
get_footer();
