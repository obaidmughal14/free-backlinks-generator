<?php
/**
 * Single guest post.
 *
 * @package Free_Backlinks_Generator
 */

get_header();
while ( have_posts() ) :
	the_post();
	$niche_terms = get_the_terms( get_the_ID(), 'fbg_niche' );
	$type_terms  = get_the_terms( get_the_ID(), 'fbg_content_type' );
	$niche_name  = ( $niche_terms && ! is_wp_error( $niche_terms ) ) ? $niche_terms[0]->name : '';
	$type_name   = ( $type_terms && ! is_wp_error( $type_terms ) ) ? $type_terms[0]->name : '';
	$words       = str_word_count( wp_strip_all_tags( get_post_field( 'post_content', get_the_ID() ) ) );
	$read_mins   = max( 1, (int) ceil( $words / 200 ) );
	$link_count  = (int) get_post_meta( get_the_ID(), '_fbg_backlink_count', true );
	?>
	<main id="main-content" class="fbg-single-post">
		<div class="fbg-single-hero">
			<?php
			if ( has_post_thumbnail() ) {
				the_post_thumbnail( 'fbg_hero', array( 'class' => 'fbg-single-hero__img', 'loading' => 'eager' ) );
			}
			?>
			<div class="fbg-single-hero__overlay"></div>
			<div class="fbg-container fbg-single-hero__meta">
				<?php if ( $niche_name ) : ?>
					<span class="fbg-badge"><?php echo esc_html( $niche_name ); ?></span>
				<?php endif; ?>
				<?php if ( $type_name ) : ?>
					<span class="fbg-badge fbg-badge--muted"><?php echo esc_html( $type_name ); ?></span>
				<?php endif; ?>
				<h1><?php the_title(); ?></h1>
				<nav class="fbg-breadcrumb fbg-breadcrumb--hero" aria-label="<?php esc_attr_e( 'Breadcrumb', 'free-backlinks-generator' ); ?>">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'free-backlinks-generator' ); ?></a>
					<span aria-hidden="true"> → </span>
					<a href="<?php echo esc_url( get_post_type_archive_link( 'fbg_post' ) ); ?>"><?php esc_html_e( 'Community', 'free-backlinks-generator' ); ?></a>
					<?php if ( $niche_name ) : ?>
						<span aria-hidden="true"> → </span>
						<span><?php echo esc_html( $niche_name ); ?></span>
					<?php endif; ?>
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
					· 🔗 <?php echo esc_html( (string) $link_count ); ?> <?php esc_html_e( 'backlinks in this post', 'free-backlinks-generator' ); ?>
				</p>
			</div>
		</div>
		<div class="fbg-container fbg-single-layout">
			<div class="fbg-single-layout__main">
				<div class="fbg-single-content fbg-prose">
					<?php the_content(); ?>
				</div>
				<div class="fbg-single-aside">
					<?php get_template_part( 'template-parts/backlinks', 'list' ); ?>
					<?php get_template_part( 'template-parts/author', 'bio' ); ?>
				</div>
			</div>
			<?php get_template_part( 'template-parts/single', 'sidebar' ); ?>
		</div>
		<section class="fbg-related fbg-container">
			<h3>
				<?php
				if ( $niche_name ) {
					printf(
						/* translators: %s niche name */
						esc_html__( 'More from %s', 'free-backlinks-generator' ),
						esc_html( $niche_name )
					);
				} else {
					esc_html_e( 'More guest posts', 'free-backlinks-generator' );
				}
				?>
			</h3>
			<div class="fbg-post-grid fbg-post-grid--related">
				<?php
				$related_ids = fbg_get_related_fbg_post_ids( get_the_ID(), 3 );
				if ( ! empty( $related_ids ) ) {
					$related = new WP_Query(
						array(
							'post_type'      => 'fbg_post',
							'post_status'    => 'publish',
							'post__in'       => $related_ids,
							'orderby'        => 'post__in',
							'posts_per_page' => count( $related_ids ),
						)
					);
					if ( $related->have_posts() ) {
						while ( $related->have_posts() ) {
							$related->the_post();
							get_template_part( 'template-parts/blog', 'card' );
						}
						wp_reset_postdata();
					}
				}
				?>
			</div>
		</section>
		<section class="fbg-cta-inline fbg-container">
			<h3><?php esc_html_e( 'Want your backlinks featured here?', 'free-backlinks-generator' ); ?></h3>
			<p><?php esc_html_e( 'Submit a guest post — it’s free.', 'free-backlinks-generator' ); ?></p>
			<a class="btn-primary" href="<?php echo esc_url( home_url( '/register/' ) ); ?>"><?php esc_html_e( 'Join & Submit Your Post', 'free-backlinks-generator' ); ?> →</a>
		</section>
	</main>
	<?php
endwhile;
get_footer();
