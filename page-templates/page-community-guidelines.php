<?php
/**
 * Template Name: Community Guidelines
 *
 * @package Free_Backlinks_Generator
 */

get_header();
?>
<main id="main-content" class="fbg-main">
	<section class="fbg-mkt-hero" aria-labelledby="fbg-guide-title">
		<div class="fbg-container">
			<h1 id="fbg-guide-title"><?php esc_html_e( 'Community guidelines', 'free-backlinks-generator' ); ?></h1>
			<p class="fbg-mkt-hero__lead">
				<?php esc_html_e( 'These rules keep the platform useful for readers, fair for members, and sustainable for SEO. Violations may lead to rejection, editing, or account suspension.', 'free-backlinks-generator' ); ?>
			</p>
		</div>
	</section>

	<section class="fbg-mkt-section fbg-container">
		<div class="fbg-mkt-prose">
			<h2><?php esc_html_e( 'Content quality', 'free-backlinks-generator' ); ?></h2>
			<ul>
				<li><?php esc_html_e( 'Submit original articles you own or have rights to publish. No plagiarism, scraping, or unattributed quotes.', 'free-backlinks-generator' ); ?></li>
				<li><?php esc_html_e( 'Minimum length and depth apply — thin, duplicate, or keyword-stuffed pages are rejected.', 'free-backlinks-generator' ); ?></li>
				<li><?php esc_html_e( 'Disclose sponsored sections, affiliate links, and AI assistance where accuracy or ethics require it.', 'free-backlinks-generator' ); ?></li>
			</ul>

			<h2><?php esc_html_e( 'Links and backlinks', 'free-backlinks-generator' ); ?></h2>
			<ul>
				<li><?php esc_html_e( 'Outbound links must be editorial and useful to readers — not hidden, misleading, or off-topic.', 'free-backlinks-generator' ); ?></li>
				<li><?php esc_html_e( 'Do not use the platform to build PBNs, automated link wheels, or paid link schemes.', 'free-backlinks-generator' ); ?></li>
				<li><?php esc_html_e( 'Respect reasonable limits on links per post based on your tier; stuffing triggers review.', 'free-backlinks-generator' ); ?></li>
			</ul>

			<h2><?php esc_html_e( 'Behavior', 'free-backlinks-generator' ); ?></h2>
			<ul>
				<li><?php esc_html_e( 'Be respectful in comments and support tickets. Harassment, hate, or doxxing is prohibited.', 'free-backlinks-generator' ); ?></li>
				<li><?php esc_html_e( 'One person should not operate multiple accounts to game credits or quotas.', 'free-backlinks-generator' ); ?></li>
				<li><?php esc_html_e( 'Do not attempt to compromise the site, scrape private data, or bypass moderation.', 'free-backlinks-generator' ); ?></li>
			</ul>

			<h2><?php esc_html_e( 'Moderation', 'free-backlinks-generator' ); ?></h2>
			<p><?php esc_html_e( 'Moderators may edit formatting, fix broken markup, or request revisions. Final publish decision rests with the team. Repeated low-quality submissions may slow or block future posting.', 'free-backlinks-generator' ); ?></p>

			<h2><?php esc_html_e( 'Copyright & DMCA', 'free-backlinks-generator' ); ?></h2>
			<p><?php esc_html_e( 'If you believe content infringes your rights, contact us with URL, description of the work, and a good-faith statement. We will respond per applicable law.', 'free-backlinks-generator' ); ?></p>
		</div>
	</section>

	<section class="fbg-mkt-section fbg-mkt-section--alt fbg-container" style="text-align: center;">
		<a class="btn-primary" href="<?php echo esc_url( home_url( '/submit-post/' ) ); ?>"><?php esc_html_e( 'Go to submit post', 'free-backlinks-generator' ); ?></a>
		<a class="btn-ghost" style="margin-inline-start: var(--space-sm);" href="<?php echo esc_url( home_url( '/terms-of-service/' ) ); ?>"><?php esc_html_e( 'Terms of Service', 'free-backlinks-generator' ); ?></a>
	</section>
</main>
<?php
get_footer();
