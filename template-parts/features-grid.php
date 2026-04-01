<?php
/**
 * Features grid.
 *
 * @package Free_Backlinks_Generator
 */
?>
<section class="fbg-section fbg-features" id="features">
	<div class="fbg-container">
		<h2><?php esc_html_e( 'Everything You Need to Build Backlinks for Free', 'free-backlinks-generator' ); ?></h2>
		<p class="fbg-section__sub"><?php esc_html_e( 'Built by SEO professionals, for the community.', 'free-backlinks-generator' ); ?></p>
		<div class="fbg-features__grid">
			<?php
			$items = array(
				array( '🔗', __( 'Contextual Backlinks', 'free-backlinks-generator' ), __( 'Every backlink is embedded naturally within original content — exactly how Google wants to see links built. No link farms. No directories.', 'free-backlinks-generator' ) ),
				array( '📊', __( 'Backlink Dashboard', 'free-backlinks-generator' ), __( 'See every backlink you\'ve placed, which posts they\'re in, their live status, and your cumulative link count — all in one clean dashboard.', 'free-backlinks-generator' ) ),
				array( '✅', __( 'Editorial Review', 'free-backlinks-generator' ), __( 'Every guest post is reviewed by our team before publishing. We maintain strict quality standards so your backlinks live alongside credible content.', 'free-backlinks-generator' ) ),
				array( '🏆', __( 'Community Tiers', 'free-backlinks-generator' ), __( 'Rise from Seedling to Canopy as you contribute more. Higher tiers unlock more links per post, priority review, and community recognition.', 'free-backlinks-generator' ) ),
				array( '🔍', __( 'SEO-Optimized Posts', 'free-backlinks-generator' ), __( 'Every published post gets schema markup, canonical tags, Open Graph meta, and is included in our XML sitemap — maximizing your link visibility.', 'free-backlinks-generator' ) ),
				array( '🚀', __( 'Guest Posting Network', 'free-backlinks-generator' ), __( 'You\'re not just getting links — you\'re building relationships. Connect with 2,400+ bloggers and SEO pros in your niche.', 'free-backlinks-generator' ) ),
			);
			foreach ( $items as $row ) {
				list( $icon, $title, $desc ) = $row;
				echo '<article class="fbg-feature-card"><span class="fbg-feature-card__icon" aria-hidden="true">' . esc_html( $icon ) . '</span><h3>' . esc_html( $title ) . '</h3><p>' . esc_html( $desc ) . '</p></article>';
			}
			?>
		</div>
	</div>
</section>
