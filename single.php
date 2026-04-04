<?php
/**
 * Single standard blog post (site news / articles).
 *
 * @package Free_Backlinks_Generator
 */

get_header();
while ( have_posts() ) :
	the_post();
	$words     = str_word_count( wp_strip_all_tags( get_post_field( 'post_content', get_the_ID() ) ) );
	$read_mins = max( 1, (int) ceil( $words / 200 ) );
	$cats      = get_the_category();
	$cat_name  = ( $cats && ! is_wp_error( $cats ) ) ? $cats[0]->name : '';
	$posts_page = (int) get_option( 'page_for_posts' );
	$blog_url   = $posts_page ? get_permalink( $posts_page ) : home_url( '/' );
	?>
	<main id="main-content" class="fbg-single-post fbg-blog-single">
		<div class="fbg-single-hero">
			<?php
			if ( has_post_thumbnail() ) {
				the_post_thumbnail( 'fbg_hero', array( 'class' => 'fbg-single-hero__img', 'loading' => 'eager' ) );
			} else {
				$pattern = get_theme_file_uri( 'assets/images/pattern-dots.svg' );
				echo '<img src="' . esc_url( $pattern ) . '" alt="" class="fbg-single-hero__img fbg-single-hero__img--pattern" width="800" height="400" loading="eager">';
			}
			?>
			<div class="fbg-single-hero__overlay"></div>
			<div class="fbg-container fbg-single-hero__meta">
				<div class="fbg-single-hero__inner">
				<?php if ( $cat_name ) : ?>
					<span class="fbg-badge"><?php echo esc_html( $cat_name ); ?></span>
				<?php endif; ?>
				<span class="fbg-badge fbg-badge--muted"><?php esc_html_e( 'Blog', 'free-backlinks-generator' ); ?></span>
				<h1><?php the_title(); ?></h1>
				<nav class="fbg-breadcrumb fbg-breadcrumb--hero" aria-label="<?php esc_attr_e( 'Breadcrumb', 'free-backlinks-generator' ); ?>">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'free-backlinks-generator' ); ?></a>
					<span aria-hidden="true"> → </span>
					<a href="<?php echo esc_url( $blog_url ); ?>"><?php esc_html_e( 'Blog', 'free-backlinks-generator' ); ?></a>
					<span aria-hidden="true"> → </span>
					<span class="fbg-breadcrumb__current"><?php the_title(); ?></span>
				</nav>
				<p class="fbg-single-byline">
					<span class="author-avatar"><?php echo esc_html( strtoupper( substr( get_the_author_meta( 'display_name' ), 0, 1 ) ) ); ?></span>
					<?php echo esc_html( get_the_author_meta( 'display_name' ) ); ?> ·
					<?php echo esc_html( get_the_date() ); ?> ·
					<?php
					printf(
						/* translators: %d minutes */
						esc_html__( '%d min read', 'free-backlinks-generator' ),
						$read_mins
					);
					?>
				</p>
				</div>
			</div>
		</div>
		<div class="fbg-container fbg-single-layout">
			<div class="fbg-single-layout__main">
				<article <?php post_class( 'fbg-single-content fbg-prose' ); ?>>
					<?php the_content(); ?>
					<?php
					wp_link_pages(
						array(
							'before' => '<nav class="fbg-post-pages" aria-label="' . esc_attr__( 'Page', 'free-backlinks-generator' ) . '"><p>' . esc_html__( 'Pages:', 'free-backlinks-generator' ) . '</p>',
							'after'  => '</nav>',
						)
					);
					?>
				</article>
				<?php if ( has_category() ) : ?>
					<p class="fbg-blog-single__tags">
						<?php esc_html_e( 'Filed under:', 'free-backlinks-generator' ); ?>
						<?php the_category( ', ' ); ?>
					</p>
				<?php endif; ?>
			</div>
			<?php get_template_part( 'template-parts/single', 'sidebar' ); ?>
		</div>
		<section class="fbg-related fbg-container">
			<h3><?php esc_html_e( 'More from the blog', 'free-backlinks-generator' ); ?></h3>
			<div class="fbg-post-grid fbg-post-grid--related">
				<?php
				$related_args = array(
					'post_type'           => 'post',
					'post_status'         => 'publish',
					'posts_per_page'      => 3,
					'post__not_in'        => array( get_the_ID() ),
					'ignore_sticky_posts' => true,
				);
				$related_cats = wp_get_post_categories( get_the_ID() );
				if ( ! empty( $related_cats ) ) {
					$related_args['category__in'] = $related_cats;
				}
				$related = new WP_Query( $related_args );
				if ( $related->have_posts() ) {
					while ( $related->have_posts() ) {
						$related->the_post();
						get_template_part( 'template-parts/content', 'post-card' );
					}
					wp_reset_postdata();
				}
				?>
			</div>
			<p class="fbg-blog-single__back">
				<a class="btn-ghost" href="<?php echo esc_url( $blog_url ); ?>"><?php esc_html_e( '← All blog posts', 'free-backlinks-generator' ); ?></a>
			</p>
		</section>
	</main>
	<?php
endwhile;
get_footer();
