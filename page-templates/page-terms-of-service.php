<?php
/**
 * Template Name: Terms of Service
 *
 * @package Free_Backlinks_Generator
 */

get_header();
$site = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
?>
<main id="main-content" class="fbg-main">
	<section class="fbg-mkt-hero" aria-labelledby="fbg-terms-title">
		<div class="fbg-container">
			<h1 id="fbg-terms-title"><?php esc_html_e( 'Terms of Service', 'free-backlinks-generator' ); ?></h1>
			<p class="fbg-mkt-hero__lead">
				<?php
				printf(
					/* translators: %s site name */
					esc_html__( 'Rules for using %s — including accounts, guest posts, backlinks, moderation, and liability.', 'free-backlinks-generator' ),
					esc_html( $site )
				);
				?>
			</p>
			<p class="fbg-mkt-hero__lead" style="margin-top: var(--space-md); font-size: 0.95rem; opacity: 0.9;">
				<?php
				printf(
					esc_html__( 'Effective: %s', 'free-backlinks-generator' ),
					esc_html( gmdate( 'F j, Y' ) )
				);
				?>
			</p>
		</div>
	</section>
	<section class="fbg-mkt-section fbg-container fbg-mkt-trust-strip" aria-label="<?php esc_attr_e( 'Terms at a glance', 'free-backlinks-generator' ); ?>">
		<div class="fbg-mkt-trust-strip__grid">
			<div class="fbg-mkt-trust-strip__item">
				<img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/icon-posts.svg' ) ); ?>" width="48" height="48" alt="">
				<strong><?php esc_html_e( 'Your content', 'free-backlinks-generator' ); ?></strong>
				<span><?php esc_html_e( 'You keep ownership; we need a license to host and promote it.', 'free-backlinks-generator' ); ?></span>
			</div>
			<div class="fbg-mkt-trust-strip__item">
				<img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/icon-community.svg' ) ); ?>" width="48" height="48" alt="">
				<strong><?php esc_html_e( 'Fair use', 'free-backlinks-generator' ); ?></strong>
				<span><?php esc_html_e( 'Follow guidelines — moderation keeps quality high.', 'free-backlinks-generator' ); ?></span>
			</div>
			<div class="fbg-mkt-trust-strip__item">
				<img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/icon-links.svg' ) ); ?>" width="48" height="48" alt="">
				<strong><?php esc_html_e( 'No SEO guarantees', 'free-backlinks-generator' ); ?></strong>
				<span><?php esc_html_e( 'Search engines make independent indexing decisions.', 'free-backlinks-generator' ); ?></span>
			</div>
		</div>
	</section>
	<?php fbg_render_terms_of_service_body(); ?>
	<section class="fbg-mkt-section fbg-mkt-section--alt">
		<div class="fbg-container" style="text-align: center;">
			<a class="btn-primary" href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>"><?php esc_html_e( 'Privacy Policy', 'free-backlinks-generator' ); ?></a>
			<a class="btn-ghost" style="margin-inline-start: var(--space-sm);" href="<?php echo esc_url( home_url( '/community-guidelines/' ) ); ?>"><?php esc_html_e( 'Community Guidelines', 'free-backlinks-generator' ); ?></a>
		</div>
	</section>
</main>
<?php
get_footer();
