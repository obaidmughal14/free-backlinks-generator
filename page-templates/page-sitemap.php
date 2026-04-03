<?php
/**
 * Template Name: Site map (HTML)
 *
 * @package Free_Backlinks_Generator
 */

get_header();

$pages = get_pages(
	array(
		'sort_column' => 'post_title',
		'sort_order'  => 'ASC',
		'post_status' => 'publish',
	)
);

$posts = get_posts(
	array(
		'post_type'              => 'post',
		'post_status'            => 'publish',
		'posts_per_page'         => 40,
		'orderby'                => 'modified',
		'order'                  => 'DESC',
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
	)
);

$guest = get_posts(
	array(
		'post_type'              => 'fbg_post',
		'post_status'            => 'publish',
		'posts_per_page'         => 60,
		'orderby'                => 'modified',
		'order'                  => 'DESC',
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
	)
);

$xml_url = home_url( '/wp-sitemap.xml' );
?>
<main id="main-content" class="fbg-main">
	<section class="fbg-mkt-hero" aria-labelledby="fbg-sitemap-title">
		<div class="fbg-container">
			<h1 id="fbg-sitemap-title"><?php esc_html_e( 'Site map', 'free-backlinks-generator' ); ?></h1>
			<p class="fbg-mkt-hero__lead">
				<?php esc_html_e( 'Every link below reflects what is published right now. New posts and pages appear here as soon as they go live.', 'free-backlinks-generator' ); ?>
			</p>
			<p class="fbg-mkt-hero__badges">
				<a class="fbg-mkt-badge" href="<?php echo esc_url( get_bloginfo( 'rss2_url' ) ); ?>"><?php esc_html_e( 'RSS feed', 'free-backlinks-generator' ); ?></a>
				<a class="fbg-mkt-badge" href="<?php echo esc_url( $xml_url ); ?>"><?php esc_html_e( 'XML sitemap (search engines)', 'free-backlinks-generator' ); ?></a>
			</p>
		</div>
	</section>

	<section class="fbg-mkt-section fbg-container">
		<div class="fbg-sitemap-grid">
			<div class="fbg-sitemap-col">
				<h2><?php esc_html_e( 'Pages', 'free-backlinks-generator' ); ?></h2>
				<ul class="fbg-sitemap-list">
					<?php foreach ( $pages as $p ) : ?>
						<?php
						if ( in_array( $p->post_name, array( 'home' ), true ) ) {
							continue;
						}
						?>
						<li><a href="<?php echo esc_url( get_permalink( $p ) ); ?>"><?php echo esc_html( get_the_title( $p ) ); ?></a></li>
					<?php endforeach; ?>
					<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'free-backlinks-generator' ); ?></a></li>
				</ul>
			</div>
			<div class="fbg-sitemap-col">
				<h2><?php esc_html_e( 'Blog', 'free-backlinks-generator' ); ?></h2>
				<?php if ( $posts ) : ?>
					<ul class="fbg-sitemap-list">
						<?php foreach ( $posts as $p ) : ?>
							<li><a href="<?php echo esc_url( get_permalink( $p ) ); ?>"><?php echo esc_html( get_the_title( $p ) ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				<?php else : ?>
					<p class="fbg-sitemap-empty"><?php esc_html_e( 'No blog posts yet.', 'free-backlinks-generator' ); ?></p>
				<?php endif; ?>
			</div>
			<div class="fbg-sitemap-col">
				<h2><?php esc_html_e( 'Guest posts', 'free-backlinks-generator' ); ?></h2>
				<?php if ( $guest ) : ?>
					<ul class="fbg-sitemap-list">
						<?php foreach ( $guest as $p ) : ?>
							<li><a href="<?php echo esc_url( get_permalink( $p ) ); ?>"><?php echo esc_html( get_the_title( $p ) ); ?></a></li>
						<?php endforeach; ?>
					</ul>
					<p class="fbg-sitemap-note"><?php esc_html_e( 'Showing the most recently updated posts. The XML sitemap lists the full set for crawlers.', 'free-backlinks-generator' ); ?></p>
				<?php else : ?>
					<p class="fbg-sitemap-empty"><?php esc_html_e( 'No guest posts yet.', 'free-backlinks-generator' ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</section>
</main>
<?php
get_footer();
