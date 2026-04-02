<?php
/**
 * Testimonials carousel (home landing).
 *
 * @package Free_Backlinks_Generator
 */
$items = array(
	array(
		'quote'  => __( 'I was skeptical about free backlink platforms, but this community changed my mind. After twelve guest posts in three months, my domain authority climbed from 14 to 31 — all from real editorial links.', 'free-backlinks-generator' ),
		'author' => __( 'Sarah K.', 'free-backlinks-generator' ),
		'role'   => __( 'Lifestyle & travel blogger', 'free-backlinks-generator' ),
		'since'  => __( 'Member since January 2024', 'free-backlinks-generator' ),
		'stat'   => __( '87 backlinks placed', 'free-backlinks-generator' ),
	),
	array(
		'quote'  => __( 'As an SEO consultant I have tried every link-building tactic. The editorial review here means my clients get quality placements, not spam folders. It is now part of our standard playbook.', 'free-backlinks-generator' ),
		'author' => __( 'Marcus T.', 'free-backlinks-generator' ),
		'role'   => __( 'SEO consultant', 'free-backlinks-generator' ),
		'since'  => __( 'Member since March 2024', 'free-backlinks-generator' ),
		'stat'   => __( '214 backlinks placed', 'free-backlinks-generator' ),
	),
	array(
		'quote'  => __( 'I write about personal finance and this network has been a goldmine. I have built over 120 contextual backlinks and collaborated with writers who actually understand the niche.', 'free-backlinks-generator' ),
		'author' => __( 'Priya M.', 'free-backlinks-generator' ),
		'role'   => __( 'Personal finance writer', 'free-backlinks-generator' ),
		'since'  => __( 'Member since November 2023', 'free-backlinks-generator' ),
		'stat'   => __( '120 backlinks placed', 'free-backlinks-generator' ),
	),
	array(
		'quote'  => __( 'Our SaaS blog needed authority without a five-figure agency budget. Guest posts here gave us backlinks from relevant tech and marketing sites — Ahrefs shows a clean, steady upward trend.', 'free-backlinks-generator' ),
		'author' => __( 'Daniel R.', 'free-backlinks-generator' ),
		'role'   => __( 'Head of growth, B2B SaaS', 'free-backlinks-generator' ),
		'since'  => __( 'Member since February 2024', 'free-backlinks-generator' ),
		'stat'   => __( '56 referring domains', 'free-backlinks-generator' ),
	),
	array(
		'quote'  => __( 'I run a small agency and use this platform for clients in health and wellness. The niche categories keep links relevant, and turnaround for reviews is predictable.', 'free-backlinks-generator' ),
		'author' => __( 'Elena V.', 'free-backlinks-generator' ),
		'role'   => __( 'Digital marketing agency owner', 'free-backlinks-generator' ),
		'since'  => __( 'Member since June 2024', 'free-backlinks-generator' ),
		'stat'   => __( '34 live guest posts', 'free-backlinks-generator' ),
	),
	array(
		'quote'  => __( 'The reader unlock system pushed me to actually read other members’ articles — which made my own submissions stronger. Win-win for content quality.', 'free-backlinks-generator' ),
		'author' => __( 'Jason S.', 'free-backlinks-generator' ),
		'role'   => __( 'Lifestyle / tech blogger', 'free-backlinks-generator' ),
		'since'  => __( 'Member since August 2024', 'free-backlinks-generator' ),
		'stat'   => __( '19 posts published', 'free-backlinks-generator' ),
	),
	array(
		'quote'  => __( 'We are a local services brand competing with national chains. Community guest posts helped us show up for city + service keywords without shady PBNs.', 'free-backlinks-generator' ),
		'author' => __( 'Amir H.', 'free-backlinks-generator' ),
		'role'   => __( 'Local SEO lead', 'free-backlinks-generator' ),
		'since'  => __( 'Member since May 2024', 'free-backlinks-generator' ),
		'stat'   => __( '41 quality backlinks', 'free-backlinks-generator' ),
	),
	array(
		'quote'  => __( 'Transparent guidelines and a real human review process. I recommend this to every creator in my newsletter who asks how to build DA ethically.', 'free-backlinks-generator' ),
		'author' => __( 'Nina L.', 'free-backlinks-generator' ),
		'role'   => __( 'Newsletter publisher (12k subs)', 'free-backlinks-generator' ),
		'since'  => __( 'Member since April 2024', 'free-backlinks-generator' ),
		'stat'   => __( 'Affiliate + member', 'free-backlinks-generator' ),
	),
);
?>
<section class="fbg-section fbg-testimonials" aria-labelledby="fbg-testimonials-heading">
	<div class="fbg-container">
		<h2 id="fbg-testimonials-heading"><?php esc_html_e( 'Real results from real members', 'free-backlinks-generator' ); ?></h2>
		<p class="fbg-section__sub"><?php esc_html_e( 'Bloggers, agencies, and SEOs use our free guest-post community to earn contextual backlinks and grow domain authority — without private blog networks or paid link schemes.', 'free-backlinks-generator' ); ?></p>

		<div class="fbg-testimonials-carousel" id="fbg-testimonials-carousel" data-autoplay="7500" role="region" aria-roledescription="<?php esc_attr_e( 'Carousel', 'free-backlinks-generator' ); ?>" aria-label="<?php esc_attr_e( 'Member testimonials', 'free-backlinks-generator' ); ?>">
			<div class="fbg-testimonials-carousel__viewport" id="fbg-testimonials-viewport">
				<div class="fbg-testimonials-carousel__track">
					<?php foreach ( $items as $row ) : ?>
						<div class="fbg-testimonial-slide">
							<blockquote class="fbg-testimonial-card" cite="<?php echo esc_url( home_url( '/' ) ); ?>">
								<p><?php echo esc_html( $row['quote'] ); ?></p>
								<footer>
									<strong><?php echo esc_html( $row['author'] ); ?></strong>, <?php echo esc_html( $row['role'] ); ?>
									<br><span class="fbg-stars" aria-hidden="true">★★★★★</span>
									<span class="screen-reader-text"><?php esc_html_e( '5 out of 5 stars', 'free-backlinks-generator' ); ?></span>
									<br><?php echo esc_html( $row['since'] ); ?> · <?php echo esc_html( $row['stat'] ); ?>
								</footer>
							</blockquote>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
			<div class="fbg-tc-nav">
				<button type="button" class="fbg-tc-btn fbg-tc-prev" aria-controls="fbg-testimonials-viewport" aria-label="<?php esc_attr_e( 'Previous testimonials', 'free-backlinks-generator' ); ?>">‹</button>
				<div class="fbg-tc-dots" role="tablist" aria-label="<?php esc_attr_e( 'Testimonial slides', 'free-backlinks-generator' ); ?>"></div>
				<button type="button" class="fbg-tc-btn fbg-tc-next" aria-controls="fbg-testimonials-viewport" aria-label="<?php esc_attr_e( 'Next testimonials', 'free-backlinks-generator' ); ?>">›</button>
			</div>
		</div>
	</div>
</section>
