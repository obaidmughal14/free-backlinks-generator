<?php
/**
 * Template Name: About
 *
 * @package Free_Backlinks_Generator
 */

get_header();
$site = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
$ill  = get_theme_file_uri( 'assets/images/hero-illustration.svg' );
$ill2 = get_theme_file_uri( 'assets/images/404-illustration.svg' );
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
			<div class="fbg-mkt-grid fbg-mkt-grid--2" style="align-items: center;">
				<div>
					<h2><?php esc_html_e( 'Our story', 'free-backlinks-generator' ); ?></h2>
					<p class="fbg-mkt-section__sub" style="text-align: left; margin-bottom: var(--space-md);">
						<?php esc_html_e( 'We built this platform because small publishers deserve the same link opportunities as big brands — without buying risky “link packages” or joining opaque blog networks.', 'free-backlinks-generator' ); ?>
					</p>
					<p style="margin: 0 0 var(--space-md); line-height: 1.65; color: var(--color-text-secondary);">
						<?php esc_html_e( 'Every published article is meant to help readers first. Members earn placement through quality writing, peer reading, and clear rules — so backlinks stay contextual and search engines can treat them as editorial signals.', 'free-backlinks-generator' ); ?>
					</p>
					<p style="margin: 0; line-height: 1.65; color: var(--color-text-secondary);">
						<?php esc_html_e( 'Whether you run a niche blog, an affiliate site, or client SEO, you will find a straightforward workflow: submit, get reviewed, go live, and track what you have published.', 'free-backlinks-generator' ); ?>
					</p>
				</div>
				<figure class="fbg-mkt-promo-fig">
					<img src="<?php echo esc_url( $ill2 ); ?>" width="360" height="270" alt="" loading="lazy" decoding="async">
				</figure>
			</div>
		</div>
	</section>

	<section class="fbg-mkt-section fbg-container">
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
		<ul class="fbg-mkt-photo-row" aria-label="<?php esc_attr_e( 'Focus areas', 'free-backlinks-generator' ); ?>">
			<li>
				<img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/icon-posts.svg' ) ); ?>" width="72" height="72" alt="">
				<?php esc_html_e( 'Editorial guest posts', 'free-backlinks-generator' ); ?>
			</li>
			<li>
				<img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/icon-links.svg' ) ); ?>" width="72" height="72" alt="">
				<?php esc_html_e( 'Natural contextual links', 'free-backlinks-generator' ); ?>
			</li>
			<li>
				<img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/pattern-dots.svg' ) ); ?>" width="72" height="72" alt="">
				<?php esc_html_e( 'Scalable for teams', 'free-backlinks-generator' ); ?>
			</li>
		</ul>
	</section>

	<section class="fbg-mkt-section fbg-mkt-section--alt fbg-container">
		<h2><?php esc_html_e( 'What you can expect', 'free-backlinks-generator' ); ?></h2>
		<div class="fbg-mkt-grid fbg-mkt-grid--2">
			<div class="fbg-mkt-card">
				<h3><?php esc_html_e( 'For writers & publishers', 'free-backlinks-generator' ); ?></h3>
				<ul class="fbg-mkt-prose" style="margin: 0; padding-left: 1.2rem; color: var(--color-text-secondary); line-height: 1.65;">
					<li><?php esc_html_e( 'Clear submission checklist and niche tagging.', 'free-backlinks-generator' ); ?></li>
					<li><?php esc_html_e( 'Moderation feedback when something needs a fix.', 'free-backlinks-generator' ); ?></li>
					<li><?php esc_html_e( 'Public archives and RSS so your best work stays discoverable.', 'free-backlinks-generator' ); ?></li>
				</ul>
			</div>
			<div class="fbg-mkt-card">
				<h3><?php esc_html_e( 'For readers & the web', 'free-backlinks-generator' ); ?></h3>
				<ul class="fbg-mkt-prose" style="margin: 0; padding-left: 1.2rem; color: var(--color-text-secondary); line-height: 1.65;">
					<li><?php esc_html_e( 'No hidden paid-link networks posing as “editorial”.', 'free-backlinks-generator' ); ?></li>
					<li><?php esc_html_e( 'Community guidelines that discourage thin or misleading pages.', 'free-backlinks-generator' ); ?></li>
					<li><?php esc_html_e( 'Privacy and data practices documented in plain language.', 'free-backlinks-generator' ); ?></li>
				</ul>
			</div>
		</div>
	</section>

	<section class="fbg-mkt-section fbg-container" style="text-align: center;">
		<h2><?php esc_html_e( 'Join the community', 'free-backlinks-generator' ); ?></h2>
		<p class="fbg-mkt-section__sub"><?php esc_html_e( 'Create a free account, read the guidelines, and submit your first guest post when you are ready.', 'free-backlinks-generator' ); ?></p>
		<a class="btn-primary" href="<?php echo esc_url( home_url( '/register/' ) ); ?>"><?php esc_html_e( 'Get started', 'free-backlinks-generator' ); ?> →</a>
		<a class="btn-ghost" style="margin-inline-start: var(--space-sm);" href="<?php echo esc_url( get_post_type_archive_link( 'fbg_post' ) ); ?>"><?php esc_html_e( 'Browse posts', 'free-backlinks-generator' ); ?></a>
		<p style="margin-top: var(--space-lg);">
			<?php
			$posts_page_id = (int) get_option( 'page_for_posts' );
			$blog_href     = $posts_page_id ? get_permalink( $posts_page_id ) : home_url( '/' );
			?>
			<a class="fbg-mkt-inline-link" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact the team', 'free-backlinks-generator' ); ?></a>
			·
			<a class="fbg-mkt-inline-link" href="<?php echo esc_url( $blog_href ); ?>"><?php esc_html_e( 'Read our blog', 'free-backlinks-generator' ); ?></a>
		</p>
	</section>
</main>
<?php
get_footer();
