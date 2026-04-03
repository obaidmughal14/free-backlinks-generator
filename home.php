<?php
/**
 * Blog posts index (Settings → Reading → Posts page).
 *
 * @package Free_Backlinks_Generator
 */

get_header();
$paged = max( 1, (int) get_query_var( 'paged' ) );
?>
<main id="main-content" class="fbg-archive fbg-blog-index">
	<section class="fbg-community-hero fbg-blog-index__hero">
		<div class="fbg-container">
			<h1 class="fbg-community-hero__title"><?php esc_html_e( 'Blog', 'free-backlinks-generator' ); ?></h1>
			<p class="fbg-community-hero__sub">
				<?php esc_html_e( 'Product updates, SEO tips, and community news. Subscribe via RSS for instant updates when we publish.', 'free-backlinks-generator' ); ?>
			</p>
			<p class="fbg-blog-index__rss">
				<a class="btn-ghost btn-ghost--on-dark" href="<?php echo esc_url( get_bloginfo( 'rss2_url' ) ); ?>"><?php esc_html_e( 'RSS feed', 'free-backlinks-generator' ); ?></a>
			</p>
		</div>
	</section>
	<div class="fbg-container fbg-section">
		<div class="fbg-post-grid">
			<?php
			if ( have_posts() ) :
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/content', 'post-card' );
				endwhile;
			else :
				?>
				<p><?php esc_html_e( 'No posts yet.', 'free-backlinks-generator' ); ?></p>
			<?php endif; ?>
		</div>
		<?php
		global $wp_query;
		if ( $wp_query->max_num_pages > 1 ) {
			$links = paginate_links(
				array(
					'mid_size'  => 2,
					'prev_text' => '← ' . __( 'Newer', 'free-backlinks-generator' ),
					'next_text' => __( 'Older', 'free-backlinks-generator' ) . ' →',
				)
			);
			if ( $links ) {
				echo '<nav class="fbg-pagination" aria-label="' . esc_attr__( 'Blog pagination', 'free-backlinks-generator' ) . '">';
				echo wp_kses_post( $links );
				echo '</nav>';
			}
		}
		?>
	</div>
</main>
<?php
get_footer();
