<?php
/**
 * Homepage hero.
 *
 * @package Free_Backlinks_Generator
 */
?>
<section class="fbg-hero" id="top">
	<div class="fbg-container fbg-hero__grid">
		<div class="fbg-hero__copy">
			<p class="fbg-badge-pill">🔗 <?php esc_html_e( 'Trusted by 2,400+ Link Builders', 'free-backlinks-generator' ); ?></p>
			<h1><?php esc_html_e( 'Get Free Backlinks. Submit Guest Posts. Grow Your DA.', 'free-backlinks-generator' ); ?></h1>
			<p class="fbg-hero__lead">
				<?php esc_html_e( 'Free Backlinks Generator is the community where bloggers and SEO professionals publish original guest posts with their backlinks — and help each other rank higher. No payments. No private blog networks. Just real content, real links, real results.', 'free-backlinks-generator' ); ?>
			</p>
			<div class="fbg-hero__ctas">
				<a class="btn-primary" href="<?php echo esc_url( home_url( '/register/' ) ); ?>"><?php esc_html_e( 'Get My Free Backlinks', 'free-backlinks-generator' ); ?> →</a>
				<a class="btn-ghost btn-ghost--on-dark" href="#how-it-works"><?php esc_html_e( 'See How It Works', 'free-backlinks-generator' ); ?> ↓</a>
			</div>
			<ul class="fbg-hero__proof">
				<li><?php esc_html_e( 'Free to join', 'free-backlinks-generator' ); ?></li>
				<li><?php esc_html_e( 'No credit card', 'free-backlinks-generator' ); ?></li>
				<li><?php esc_html_e( '12,000+ backlinks shared', 'free-backlinks-generator' ); ?></li>
				<li><?php esc_html_e( 'Reviewed content only', 'free-backlinks-generator' ); ?></li>
			</ul>
		</div>
		<div class="fbg-hero__visual fbg-float" aria-hidden="true">
			<div class="fbg-mock-dashboard">
				<div class="fbg-mock-card">
					<strong><?php esc_html_e( 'My Backlinks', 'free-backlinks-generator' ); ?></strong>
					<span class="fbg-mock-stat">87 <?php esc_html_e( 'live', 'free-backlinks-generator' ); ?></span>
					<div class="fbg-mock-bars"><span></span><span></span><span></span><span></span></div>
				</div>
				<div class="fbg-mock-toast">✅ <?php esc_html_e( 'Your post was approved', 'free-backlinks-generator' ); ?></div>
				<div class="fbg-mock-pills">
					<span class="fbg-pill-anim">myblog.com → communitysite.com</span>
					<span class="fbg-pill-anim fbg-pill-anim--delay">yoursite.io → ournetwork.com</span>
				</div>
			</div>
		</div>
	</div>
</section>
