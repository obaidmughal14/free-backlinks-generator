<?php
/**
 * Template Name: Privacy Policy
 *
 * @package Free_Backlinks_Generator
 */

get_header();
$site = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
?>
<main id="main-content" class="fbg-main">
	<section class="fbg-mkt-hero" aria-labelledby="fbg-privacy-title">
		<div class="fbg-container">
			<h1 id="fbg-privacy-title"><?php esc_html_e( 'Privacy Policy', 'free-backlinks-generator' ); ?></h1>
			<p class="fbg-mkt-hero__lead">
				<?php
				printf(
					/* translators: %s site name */
					esc_html__( 'How %s collects, uses, and protects personal data when you use our guest-post community and member tools.', 'free-backlinks-generator' ),
					esc_html( $site )
				);
				?>
			</p>
			<p class="fbg-mkt-hero__lead" style="margin-top: var(--space-md); font-size: 0.95rem; opacity: 0.9;">
				<?php
				printf(
					/* translators: %s date */
					esc_html__( 'Last updated: %s', 'free-backlinks-generator' ),
					esc_html( gmdate( 'F j, Y' ) )
				);
				?>
			</p>
		</div>
	</section>
	<section class="fbg-mkt-section fbg-container fbg-mkt-trust-strip" aria-label="<?php esc_attr_e( 'Privacy principles', 'free-backlinks-generator' ); ?>">
		<div class="fbg-mkt-trust-strip__grid">
			<div class="fbg-mkt-trust-strip__item">
				<img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/icon-dashboard.svg' ) ); ?>" width="48" height="48" alt="">
				<strong><?php esc_html_e( 'Transparency', 'free-backlinks-generator' ); ?></strong>
				<span><?php esc_html_e( 'We explain what we collect and why.', 'free-backlinks-generator' ); ?></span>
			</div>
			<div class="fbg-mkt-trust-strip__item">
				<img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/icon-community.svg' ) ); ?>" width="48" height="48" alt="">
				<strong><?php esc_html_e( 'Control', 'free-backlinks-generator' ); ?></strong>
				<span><?php esc_html_e( 'Rights and choices where the law allows.', 'free-backlinks-generator' ); ?></span>
			</div>
			<div class="fbg-mkt-trust-strip__item">
				<img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/icon-links.svg' ) ); ?>" width="48" height="48" alt="">
				<strong><?php esc_html_e( 'Security', 'free-backlinks-generator' ); ?></strong>
				<span><?php esc_html_e( 'Reasonable safeguards for accounts and data.', 'free-backlinks-generator' ); ?></span>
			</div>
		</div>
	</section>
	<?php fbg_render_privacy_policy_body(); ?>
	<section class="fbg-mkt-section fbg-mkt-section--alt">
		<div class="fbg-container" style="text-align: center;">
			<p class="fbg-mkt-section__sub" style="max-width: 36rem;">
				<?php esc_html_e( 'For cookie categories and controls, see our Cookie Policy. For EU/UK-specific disclosures, see the GDPR Notice.', 'free-backlinks-generator' ); ?>
			</p>
			<p style="margin-top: var(--space-md);">
				<a class="btn-primary" href="<?php echo esc_url( home_url( '/cookie-policy/' ) ); ?>"><?php esc_html_e( 'Cookie Policy', 'free-backlinks-generator' ); ?></a>
				<a class="btn-ghost" style="margin-inline-start: var(--space-sm);" href="<?php echo esc_url( home_url( '/gdpr-notice/' ) ); ?>"><?php esc_html_e( 'GDPR Notice', 'free-backlinks-generator' ); ?></a>
			</p>
		</div>
	</section>
</main>
<?php
get_footer();
