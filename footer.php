<?php
/**
 * Theme footer.
 *
 * @package Free_Backlinks_Generator
 */

if ( ! is_page_template( 'page-templates/page-signup.php' ) && ! is_page_template( 'page-templates/page-login.php' ) && ! is_page_template( 'page-templates/page-forgot-password.php' ) && ! is_page_template( 'page-templates/page-dashboard.php' ) ) {

	$fbg_footer_socials = array(
		array(
			'mod'   => 'fbg_social_facebook',
			'label' => __( 'Facebook', 'free-backlinks-generator' ),
			'svg'   => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3V2z"/></svg>',
		),
		array(
			'mod'   => 'fbg_social_x',
			'label' => __( 'X', 'free-backlinks-generator' ),
			'svg'   => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
		),
		array(
			'mod'   => 'fbg_social_instagram',
			'label' => __( 'Instagram', 'free-backlinks-generator' ),
			'svg'   => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 2h10a5 5 0 0 1 5 5v10a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5V7a5 5 0 0 1 5-5zm0 2a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V7a3 3 0 0 0-3-3H7zm5 3.5A5.5 5.5 0 1 1 6.5 13 5.5 5.5 0 0 1 12 7.5zm0 2A3.5 3.5 0 1 0 15.5 13 3.5 3.5 0 0 0 12 9.5zm5.25-3.75a1.25 1.25 0 1 1-1.25 1.25 1.25 1.25 0 0 1 1.25-1.25z"/></svg>',
		),
		array(
			'mod'   => 'fbg_social_tiktok',
			'label' => __( 'TikTok', 'free-backlinks-generator' ),
			'svg'   => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/></svg>',
		),
		array(
			'mod'   => 'fbg_social_linkedin',
			'label' => __( 'LinkedIn', 'free-backlinks-generator' ),
			'svg'   => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6.94 6.5a2 2 0 1 1-2 2 2 2 0 0 1 2-2zm-1.75 4.5h3.5V20h-3.5V11zm6.5 0h3.35v1.22h.05c.47-.9 1.6-1.85 3.3-1.85 3.53 0 4.18 2.32 4.18 5.34V20h-3.5v-4.52c0-1.08 0-2.47-1.5-2.47-1.5 0-1.73 1.17-1.73 2.39V20h-3.5V11z"/></svg>',
		),
		array(
			'mod'   => 'fbg_social_youtube',
			'label' => __( 'YouTube', 'free-backlinks-generator' ),
			'svg'   => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M23.5 6.2a3 3 0 0 0-2.1-2.1C19.5 3.5 12 3.5 12 3.5s-7.5 0-9.4.6A3 3 0 0 0 .5 6.2 31.5 31.5 0 0 0 0 12a31.5 31.5 0 0 0 .5 5.8 3 3 0 0 0 2.1 2.1c1.9.6 9.4.6 9.4.6s7.5 0 9.4-.6a3 3 0 0 0 2.1-2.1 31.5 31.5 0 0 0 .5-5.8 31.5 31.5 0 0 0-.5-5.8zM9.75 15.02V8.98L15.5 12l-5.75 3.02z"/></svg>',
		),
	);

	$fbg_has_social = false;
	foreach ( $fbg_footer_socials as $fs ) {
		$u = function_exists( 'fbg_social_url_or_empty' ) ? fbg_social_url_or_empty( get_theme_mod( $fs['mod'], '' ) ) : '';
		if ( '' !== $u ) {
			$fbg_has_social = true;
			break;
		}
	}

	$fbg_footer_titles = array(
		1 => get_theme_mod( 'fbg_footer_col1_title', __( 'Platform', 'free-backlinks-generator' ) ),
		2 => get_theme_mod( 'fbg_footer_col2_title', __( 'Company', 'free-backlinks-generator' ) ),
		3 => get_theme_mod( 'fbg_footer_col3_title', __( 'Legal', 'free-backlinks-generator' ) ),
	);
	?>
	<footer class="fbg-footer" role="contentinfo">
		<div class="fbg-container fbg-footer__grid">
			<div class="fbg-footer__col fbg-footer__col--brand">
				<?php get_template_part( 'template-parts/site', 'logo', array( 'context' => 'footer' ) ); ?>
				<p class="fbg-footer__tagline"><?php echo esc_html( get_bloginfo( 'description', 'display' ) ); ?></p>
				<div class="fbg-footer__social">
					<?php
					foreach ( $fbg_footer_socials as $row ) {
						$url = function_exists( 'fbg_social_url_or_empty' ) ? fbg_social_url_or_empty( get_theme_mod( $row['mod'], '' ) ) : '';
						if ( '' === $url ) {
							continue;
						}
						printf(
							'<a href="%1$s" class="fbg-footer__social-link" target="_blank" rel="noopener noreferrer" aria-label="%2$s">%3$s</a>',
							esc_url( $url ),
							esc_attr( $row['label'] ),
							$row['svg'] // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG markup fixed in theme.
						);
					}
					?>
					<a href="<?php echo esc_url( get_bloginfo( 'rss2_url' ) ); ?>" class="fbg-footer__social-link" aria-label="<?php esc_attr_e( 'RSS feed', 'free-backlinks-generator' ); ?>">
						<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6.18 15.64a2.18 2.18 0 0 0-2.18 2.18 2.18 2.18 0 0 0 4.36 0 2.18 2.18 0 0 0-2.18-2.18zM4 4.44v3.45a12.5 12.5 0 0 1 12.55 12.55h3.45A16 16 0 0 0 4 4.44zm0 7.72v3.45a4.82 4.82 0 0 1 4.82 4.82h3.45a8.28 8.28 0 0 0-8.27-8.27z"/></svg>
					</a>
				</div>
				<?php if ( current_user_can( 'manage_options' ) && ! $fbg_has_social ) : ?>
					<p class="fbg-footer__social-hint" style="font-size:0.8rem;opacity:0.65;margin-top:0.75rem;">
						<?php
						printf(
							/* translators: %s customizer URL */
							wp_kses_post( __( 'Add social links under <a href="%s">Appearance → Customize → Social & discoverability</a>.', 'free-backlinks-generator' ) ),
							esc_url( admin_url( 'customize.php?autofocus[section]=fbg_social_section' ) )
						);
						?>
					</p>
				<?php endif; ?>
			</div>
			<?php
			$fbg_footer_locations = array(
				1 => 'footer_1',
				2 => 'footer_2',
				3 => 'footer_3',
			);
			foreach ( $fbg_footer_locations as $idx => $location ) :
				?>
			<div class="fbg-footer__col">
				<h4 class="fbg-footer__heading"><?php echo esc_html( $fbg_footer_titles[ $idx ] ); ?></h4>
				<?php
				wp_nav_menu(
					array(
						'theme_location'  => $location,
						'container'       => false,
						'menu_class'      => 'fbg-footer__menu',
						'depth'           => 1,
						'fallback_cb'     => 'fbg_footer_nav_fallback',
					)
				);
				?>
			</div>
				<?php
			endforeach;
			?>
		</div>
		<div class="fbg-footer__bar">
			<div class="fbg-container fbg-footer__bar-inner">
				<p class="fbg-footer__copyright-line">&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'Free to join. Real backlinks. Real results.', 'free-backlinks-generator' ); ?></p>
				<p class="fbg-footer__credit"><?php esc_html_e( 'Designed and developed by', 'free-backlinks-generator' ); ?> <a href="https://devigontech.com" target="_blank" rel="noopener"><?php esc_html_e( 'Devigon Tech', 'free-backlinks-generator' ); ?></a></p>
			</div>
		</div>
	</footer>
	<?php
}
wp_footer();
?>
</body>
</html>
