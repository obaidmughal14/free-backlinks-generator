<?php
/**
 * Guest post archive — /community/
 *
 * @package Free_Backlinks_Generator
 */

get_header();
$count = fbg_published_post_count();
$niches = get_terms( array( 'taxonomy' => 'fbg_niche', 'hide_empty' => true ) );
?>
<main id="main-content" class="fbg-archive fbg-community">
	<section class="fbg-community-hero">
		<div class="fbg-container">
			<h1 class="fbg-community-hero__title"><?php esc_html_e( 'Free Guest Posts & Backlinks', 'free-backlinks-generator' ); ?></h1>
			<p class="fbg-community-hero__sub">
				<?php
				printf(
					/* translators: %s post count */
					esc_html__( 'Browse original posts submitted by our community of %s bloggers and SEO professionals. Every post contains real, contextual backlinks.', 'free-backlinks-generator' ),
					esc_html( number_format_i18n( $count ) )
				);
				?>
			</p>
		</div>
	</section>
	<div class="fbg-filter-bar fbg-container" id="fbg-filter-bar">
		<div class="fbg-filter-pills" role="tablist" aria-label="<?php esc_attr_e( 'Niche filters', 'free-backlinks-generator' ); ?>">
			<button type="button" class="fbg-pill is-active" data-niche="all"><?php esc_html_e( 'All Niches', 'free-backlinks-generator' ); ?></button>
			<?php foreach ( $niches as $term ) : ?>
				<button type="button" class="fbg-pill" data-niche="<?php echo esc_attr( $term->slug ); ?>"><?php echo esc_html( $term->name ); ?></button>
			<?php endforeach; ?>
		</div>
		<div class="fbg-filter-tools">
			<label class="screen-reader-text" for="fbg-order"><?php esc_html_e( 'Sort', 'free-backlinks-generator' ); ?></label>
			<select id="fbg-order" class="fbg-select" aria-label="<?php esc_attr_e( 'Sort order', 'free-backlinks-generator' ); ?>">
				<option value="newest"><?php esc_html_e( 'Newest', 'free-backlinks-generator' ); ?></option>
				<option value="oldest"><?php esc_html_e( 'Oldest', 'free-backlinks-generator' ); ?></option>
			</select>
			<label class="screen-reader-text" for="fbg-search-archive"><?php esc_html_e( 'Search posts', 'free-backlinks-generator' ); ?></label>
			<input type="search" id="fbg-search-archive" class="fbg-search-input" placeholder="<?php esc_attr_e( 'Search posts…', 'free-backlinks-generator' ); ?>">
		</div>
	</div>
	<div class="fbg-container">
		<div class="fbg-post-grid" id="fbg-post-grid" data-paged="1" data-max="1">
			<?php
			if ( have_posts() ) :
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/blog', 'card' );
				endwhile;
			else :
				?>
				<p><?php esc_html_e( 'No posts yet. Be the first to submit.', 'free-backlinks-generator' ); ?></p>
			<?php endif; ?>
		</div>
		<div class="fbg-load-more-wrap">
			<button type="button" class="btn-primary fbg-load-more" id="fbg-load-more"><?php esc_html_e( 'Load More Posts', 'free-backlinks-generator' ); ?></button>
			<p class="fbg-end-results" id="fbg-end-results" hidden>
				<?php esc_html_e( 'You’ve seen all posts.', 'free-backlinks-generator' ); ?>
				<a href="<?php echo esc_url( home_url( '/submit-post/' ) ); ?>"><?php esc_html_e( 'Submit your own guest post', 'free-backlinks-generator' ); ?> →</a>
			</p>
		</div>
	</div>
</main>
<?php
global $wp_query;
$fbg_max = $wp_query->max_num_pages;
?>
<script>
document.getElementById('fbg-post-grid')?.setAttribute('data-max', '<?php echo esc_js( (string) max( 1, (int) $fbg_max ) ); ?>');
</script>
<?php
get_footer();
