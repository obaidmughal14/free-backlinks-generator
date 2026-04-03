<?php
/**
 * Template Name: How It Works
 *
 * @package Free_Backlinks_Generator
 */

get_header();
?>
<main id="main-content" class="fbg-main">
	<section class="fbg-mkt-hero" aria-labelledby="fbg-hiw-title">
		<div class="fbg-container">
			<h1 id="fbg-hiw-title"><?php esc_html_e( 'How it works', 'free-backlinks-generator' ); ?></h1>
			<p class="fbg-mkt-hero__lead">
				<?php esc_html_e( 'Four simple steps from signup to live backlinks — built around reading, writing, and editorial quality.', 'free-backlinks-generator' ); ?>
			</p>
		</div>
	</section>

	<section class="fbg-mkt-section fbg-container">
		<ol class="fbg-mkt-steps">
			<li>
				<strong><?php esc_html_e( 'Create your free account', 'free-backlinks-generator' ); ?></strong>
				<?php esc_html_e( 'Register with a valid email, add your site URL, and confirm you accept our Terms and Privacy Policy.', 'free-backlinks-generator' ); ?>
			</li>
			<li>
				<strong><?php esc_html_e( 'Read community posts', 'free-backlinks-generator' ); ?></strong>
				<?php esc_html_e( 'Browse the guest-post archive by niche. Spend the required reading time on other members’ articles to unlock submission credits — this keeps the network fair.', 'free-backlinks-generator' ); ?>
			</li>
			<li>
				<strong><?php esc_html_e( 'Submit original content', 'free-backlinks-generator' ); ?></strong>
				<?php esc_html_e( 'Write a helpful article with contextual outbound links that follow our guidelines. Our team reviews for quality, relevance, and compliance.', 'free-backlinks-generator' ); ?>
			</li>
			<li>
				<strong><?php esc_html_e( 'Publish & earn backlinks', 'free-backlinks-generator' ); ?></strong>
				<?php esc_html_e( 'Once approved, your post goes live with your links. Track views and placements from your dashboard and keep contributing to climb tiers.', 'free-backlinks-generator' ); ?>
			</li>
		</ol>
	</section>

	<section class="fbg-mkt-section fbg-mkt-section--alt">
		<div class="fbg-container">
			<h2><?php esc_html_e( 'What makes us different', 'free-backlinks-generator' ); ?></h2>
			<div class="fbg-mkt-grid fbg-mkt-grid--2">
				<div class="fbg-mkt-card">
					<div class="fbg-mkt-card__icon" aria-hidden="true"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/icon-links.svg' ) ); ?>" width="40" height="40" alt=""></div>
					<h3><?php esc_html_e( 'Editorial bar', 'free-backlinks-generator' ); ?></h3>
					<p><?php esc_html_e( 'Automation assists; humans decide. Thin or manipulative content is declined with feedback so you can improve.', 'free-backlinks-generator' ); ?></p>
				</div>
				<div class="fbg-mkt-card">
					<div class="fbg-mkt-card__icon" aria-hidden="true"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/icon-community.svg' ) ); ?>" width="40" height="40" alt=""></div>
					<h3><?php esc_html_e( 'Reader unlock', 'free-backlinks-generator' ); ?></h3>
					<p><?php esc_html_e( 'Credits reward real engagement — not bulk submissions — which lifts content quality for everyone.', 'free-backlinks-generator' ); ?></p>
				</div>
			</div>
		</div>
	</section>

	<section class="fbg-mkt-section fbg-container" style="text-align: center;">
		<a class="btn-primary" href="<?php echo esc_url( home_url( '/register/' ) ); ?>"><?php esc_html_e( 'Create free account', 'free-backlinks-generator' ); ?> →</a>
	</section>
</main>
<?php
get_footer();
