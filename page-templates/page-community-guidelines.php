<?php
/**
 * Template Name: Community Guidelines
 *
 * @package Free_Backlinks_Generator
 */

get_header();
$hero_img = get_theme_file_uri( 'assets/images/icon-community.svg' );
?>
<main id="main-content" class="fbg-main">
	<section class="fbg-mkt-hero fbg-mkt-hero--split" aria-labelledby="fbg-guide-title">
		<div class="fbg-container fbg-mkt-hero-split">
			<div class="fbg-mkt-hero-split__text">
				<h1 id="fbg-guide-title"><?php esc_html_e( 'Community guidelines', 'free-backlinks-generator' ); ?></h1>
				<p class="fbg-mkt-hero__lead" style="margin: 0;">
					<?php esc_html_e( 'These rules keep the platform useful for readers, fair for members, and sustainable for SEO. Violations may lead to rejection, editing, or account suspension.', 'free-backlinks-generator' ); ?>
				</p>
			</div>
			<div class="fbg-mkt-hero-split__visual">
				<img src="<?php echo esc_url( $hero_img ); ?>" width="200" height="200" alt="" loading="lazy" decoding="async" class="fbg-mkt-hero-split__img" style="max-width: 200px;">
			</div>
		</div>
	</section>

	<section class="fbg-mkt-section fbg-container fbg-mkt-trust-strip">
		<div class="fbg-mkt-trust-strip__grid">
			<div class="fbg-mkt-trust-strip__item">
				<img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/icon-posts.svg' ) ); ?>" width="48" height="48" alt="">
				<strong><?php esc_html_e( 'Original work', 'free-backlinks-generator' ); ?></strong>
				<span><?php esc_html_e( 'Write what you own or have rights to publish.', 'free-backlinks-generator' ); ?></span>
			</div>
			<div class="fbg-mkt-trust-strip__item">
				<img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/icon-links.svg' ) ); ?>" width="48" height="48" alt="">
				<strong><?php esc_html_e( 'Honest links', 'free-backlinks-generator' ); ?></strong>
				<span><?php esc_html_e( 'Editorial context — no hidden paid schemes.', 'free-backlinks-generator' ); ?></span>
			</div>
			<div class="fbg-mkt-trust-strip__item">
				<img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/icon-dashboard.svg' ) ); ?>" width="48" height="48" alt="">
				<strong><?php esc_html_e( 'Fair play', 'free-backlinks-generator' ); ?></strong>
				<span><?php esc_html_e( 'One person, honest use of credits and quotas.', 'free-backlinks-generator' ); ?></span>
			</div>
		</div>
	</section>

	<section class="fbg-mkt-section fbg-container">
		<div class="fbg-mkt-prose">
			<h2><?php esc_html_e( 'Content quality', 'free-backlinks-generator' ); ?></h2>
			<ul>
				<li><?php esc_html_e( 'Submit original articles you own or have rights to publish. No plagiarism, scraping, or unattributed quotes.', 'free-backlinks-generator' ); ?></li>
				<li><?php esc_html_e( 'Minimum length and depth apply — thin, duplicate, or keyword-stuffed pages are rejected.', 'free-backlinks-generator' ); ?></li>
				<li><?php esc_html_e( 'Disclose sponsored sections, affiliate links, and AI assistance where accuracy or ethics require it.', 'free-backlinks-generator' ); ?></li>
				<li><?php esc_html_e( 'Images and media should be licensed or created by you; credit sources when required.', 'free-backlinks-generator' ); ?></li>
			</ul>

			<h2><?php esc_html_e( 'Links and backlinks', 'free-backlinks-generator' ); ?></h2>
			<ul>
				<li><?php esc_html_e( 'Outbound links must be editorial and useful to readers — not hidden, misleading, or off-topic.', 'free-backlinks-generator' ); ?></li>
				<li><?php esc_html_e( 'Do not use the platform to build PBNs, automated link wheels, or paid link schemes.', 'free-backlinks-generator' ); ?></li>
				<li><?php esc_html_e( 'Respect reasonable limits on links per post based on your tier; stuffing triggers review.', 'free-backlinks-generator' ); ?></li>
				<li><?php esc_html_e( 'Anchor text should read naturally; excessive exact-match anchors may be edited or declined.', 'free-backlinks-generator' ); ?></li>
			</ul>

			<h2><?php esc_html_e( 'Behavior', 'free-backlinks-generator' ); ?></h2>
			<ul>
				<li><?php esc_html_e( 'Be respectful in comments and support tickets. Harassment, hate, or doxxing is prohibited.', 'free-backlinks-generator' ); ?></li>
				<li><?php esc_html_e( 'One person should not operate multiple accounts to game credits or quotas.', 'free-backlinks-generator' ); ?></li>
				<li><?php esc_html_e( 'Do not attempt to compromise the site, scrape private data, or bypass moderation.', 'free-backlinks-generator' ); ?></li>
				<li><?php esc_html_e( 'Impersonation of staff, brands, or other members is not allowed.', 'free-backlinks-generator' ); ?></li>
			</ul>

			<h2><?php esc_html_e( 'Advertising & promotions', 'free-backlinks-generator' ); ?></h2>
			<p><?php esc_html_e( 'Commercial mentions must be clearly labeled when they could confuse readers. Native advertising should match our disclosure standards and applicable law.', 'free-backlinks-generator' ); ?></p>

			<h2><?php esc_html_e( 'Moderation', 'free-backlinks-generator' ); ?></h2>
			<p><?php esc_html_e( 'Moderators may edit formatting, fix broken markup, or request revisions. Final publish decision rests with the team. Repeated low-quality submissions may slow or block future posting.', 'free-backlinks-generator' ); ?></p>

			<h2><?php esc_html_e( 'Enforcement & appeals', 'free-backlinks-generator' ); ?></h2>
			<p><?php esc_html_e( 'We may warn, reject, limit, or suspend accounts depending on severity and history. If you believe we made a mistake, reply calmly with links and context — we reopen cases when new information is relevant.', 'free-backlinks-generator' ); ?></p>

			<h2><?php esc_html_e( 'Copyright & DMCA', 'free-backlinks-generator' ); ?></h2>
			<p><?php esc_html_e( 'If you believe content infringes your rights, contact us with URL, description of the work, and a good-faith statement. We will respond per applicable law.', 'free-backlinks-generator' ); ?></p>
		</div>
	</section>

	<section class="fbg-mkt-section fbg-mkt-section--alt fbg-container" style="text-align: center;">
		<a class="btn-primary" href="<?php echo esc_url( home_url( '/submit-post/' ) ); ?>"><?php esc_html_e( 'Go to submit post', 'free-backlinks-generator' ); ?></a>
		<a class="btn-ghost" style="margin-inline-start: var(--space-sm);" href="<?php echo esc_url( home_url( '/terms-of-service/' ) ); ?>"><?php esc_html_e( 'Terms of Service', 'free-backlinks-generator' ); ?></a>
		<a class="btn-ghost" style="margin-inline-start: var(--space-sm);" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact', 'free-backlinks-generator' ); ?></a>
	</section>
</main>
<?php
get_footer();
