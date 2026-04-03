<?php
/**
 * Template Name: About
 *
 * @package Free_Backlinks_Generator
 */

get_header();
$site = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
$ill  = get_theme_file_uri( 'assets/images/hero-illustration.svg' );
?>
<main id="main-content" class="fbg-main">
	<section class="fbg-mkt-hero fbg-mkt-hero--split" aria-labelledby="fbg-about-title">
		<div class="fbg-container fbg-mkt-hero-split">
			<div class="fbg-mkt-hero-split__text">
				<h1 id="fbg-about-title"><?php esc_html_e( 'About us', 'free-backlinks-generator' ); ?></h1>
				<p class="fbg-mkt-hero__lead" style="margin: 0;">
					<?php
					printf(
						/* translators: %s site name */
						esc_html__( '%s is a free community where bloggers, niche site owners, and SEO teams exchange high-quality guest posts and earn real editorial backlinks — without paid link schemes or shady networks.', 'free-backlinks-generator' ),
						esc_html( $site )
					);
					?>
				</p>
			</div>
			<div class="fbg-mkt-hero-split__visual">
				<img src="<?php echo esc_url( $ill ); ?>" width="420" height="294" alt="" loading="lazy" decoding="async" class="fbg-mkt-hero-split__img">
			</div>
		</div>
	</section>

	<section class="fbg-mkt-section fbg-container">
		<div class="fbg-mkt-stats fbg-mkt-stats--light">
			<div class="fbg-mkt-stat fbg-mkt-stat--light">
				<strong><?php echo esc_html( number_format_i18n( max( 1, (int) fbg_published_post_count() ) ) ); ?>+</strong>
				<span><?php esc_html_e( 'Community posts indexed', 'free-backlinks-generator' ); ?></span>
			</div>
			<div class="fbg-mkt-stat fbg-mkt-stat--light">
				<strong>24/7</strong>
				<span><?php esc_html_e( 'Member submissions accepted', 'free-backlinks-generator' ); ?></span>
			</div>
			<div class="fbg-mkt-stat fbg-mkt-stat--light">
				<strong>100%</strong>
				<span><?php esc_html_e( 'Editorial review before publish', 'free-backlinks-generator' ); ?></span>
			</div>
		</div>
	</section>

	<section class="fbg-mkt-section fbg-mkt-section--alt">
		<div class="fbg-container">
			<h2><?php esc_html_e( 'Our mission', 'free-backlinks-generator' ); ?></h2>
			<p class="fbg-mkt-section__sub"><?php esc_html_e( 'Make ethical link-building accessible: original content, transparent guidelines, and mutual value for readers — not automated spam.', 'free-backlinks-generator' ); ?></p>
			<div class="fbg-mkt-grid fbg-mkt-grid--3">
				<div class="fbg-mkt-card">
					<div class="fbg-mkt-card__icon" aria-hidden="true"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/icon-community.svg' ) ); ?>" width="40" height="40" alt=""></div>
					<h3><?php esc_html_e( 'Community-first', 'free-backlinks-generator' ); ?></h3>
					<p><?php esc_html_e( 'Members read each other’s work, unlock credits, and grow together — the platform rewards participation, not shortcuts.', 'free-backlinks-generator' ); ?></p>
				</div>
				<div class="fbg-mkt-card">
					<div class="fbg-mkt-card__icon" aria-hidden="true"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/icon-links.svg' ) ); ?>" width="40" height="40" alt=""></div>
					<h3><?php esc_html_e( 'Quality backlinks', 'free-backlinks-generator' ); ?></h3>
					<p><?php esc_html_e( 'Contextual links inside useful articles, relevant niches, and human moderation keep placements valuable for SEO and readers.', 'free-backlinks-generator' ); ?></p>
				</div>
				<div class="fbg-mkt-card">
					<div class="fbg-mkt-card__icon" aria-hidden="true"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/icon-dashboard.svg' ) ); ?>" width="40" height="40" alt=""></div>
					<h3><?php esc_html_e( 'Simple workflow', 'free-backlinks-generator' ); ?></h3>
					<p><?php esc_html_e( 'Dashboard, submissions, and notifications are built for creators and agencies who want clarity — not black-box “link packages”.', 'free-backlinks-generator' ); ?></p>
				</div>
			</div>
		</div>
	</section>

	<section class="fbg-mkt-section fbg-container" style="text-align: center;">
		<h2><?php esc_html_e( 'Join the community', 'free-backlinks-generator' ); ?></h2>
		<p class="fbg-mkt-section__sub"><?php esc_html_e( 'Create a free account, read the guidelines, and submit your first guest post when you are ready.', 'free-backlinks-generator' ); ?></p>
		<a class="btn-primary" href="<?php echo esc_url( home_url( '/register/' ) ); ?>"><?php esc_html_e( 'Get started', 'free-backlinks-generator' ); ?> →</a>
		<a class="btn-ghost" style="margin-inline-start: var(--space-sm);" href="<?php echo esc_url( get_post_type_archive_link( 'fbg_post' ) ); ?>"><?php esc_html_e( 'Browse posts', 'free-backlinks-generator' ); ?></a>
	</section>
</main>
<?php
get_footer();
