<?php
/**
 * Theme footer.
 *
 * @package Free_Backlinks_Generator
 */

if ( ! is_page_template( 'page-templates/page-signup.php' ) && ! is_page_template( 'page-templates/page-login.php' ) && ! is_page_template( 'page-templates/page-forgot-password.php' ) && ! is_page_template( 'page-templates/page-dashboard.php' ) ) {
	?>
	<footer class="fbg-footer" role="contentinfo">
		<div class="fbg-container fbg-footer__grid">
			<div class="fbg-footer__col">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="fbg-footer__logo">
					<img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/logo-white.svg' ) ); ?>" alt="<?php bloginfo( 'name' ); ?>" width="180" height="44" loading="lazy">
				</a>
				<p class="fbg-footer__tagline"><?php esc_html_e( 'Submit guest posts. Get free backlinks. Build domain authority together.', 'free-backlinks-generator' ); ?></p>
				<p class="fbg-footer__social">
					<a href="#" aria-label="Twitter">𝕏</a>
					<a href="#" aria-label="LinkedIn">in</a>
					<a href="<?php echo esc_url( get_bloginfo( 'rss2_url' ) ); ?>" aria-label="RSS">RSS</a>
				</p>
			</div>
			<div class="fbg-footer__col">
				<h4 class="fbg-footer__heading"><?php esc_html_e( 'Platform', 'free-backlinks-generator' ); ?></h4>
				<ul>
					<li><a href="<?php echo esc_url( home_url( '/#how-it-works' ) ); ?>"><?php esc_html_e( 'How It Works', 'free-backlinks-generator' ); ?></a></li>
					<li><a href="<?php echo esc_url( get_post_type_archive_link( 'fbg_post' ) ); ?>"><?php esc_html_e( 'Browse Guest Posts', 'free-backlinks-generator' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/submit-post/' ) ); ?>"><?php esc_html_e( 'Submit a Post', 'free-backlinks-generator' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/community-guidelines/' ) ); ?>"><?php esc_html_e( 'Community Guidelines', 'free-backlinks-generator' ); ?></a></li>
				</ul>
			</div>
			<div class="fbg-footer__col">
				<h4 class="fbg-footer__heading"><?php esc_html_e( 'Company', 'free-backlinks-generator' ); ?></h4>
				<ul>
					<li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'About Us', 'free-backlinks-generator' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact', 'free-backlinks-generator' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/affiliate-program/' ) ); ?>"><?php esc_html_e( 'Affiliate Program', 'free-backlinks-generator' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>"><?php esc_html_e( 'Blog', 'free-backlinks-generator' ); ?></a></li>
				</ul>
			</div>
			<div class="fbg-footer__col">
				<h4 class="fbg-footer__heading"><?php esc_html_e( 'Legal', 'free-backlinks-generator' ); ?></h4>
				<ul>
					<li><a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>"><?php esc_html_e( 'Privacy Policy', 'free-backlinks-generator' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/terms-of-service/' ) ); ?>"><?php esc_html_e( 'Terms of Service', 'free-backlinks-generator' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/cookie-policy/' ) ); ?>"><?php esc_html_e( 'Cookie Policy', 'free-backlinks-generator' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/gdpr-notice/' ) ); ?>"><?php esc_html_e( 'GDPR Notice', 'free-backlinks-generator' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/wp-sitemap.xml' ) ); ?>"><?php esc_html_e( 'Sitemap', 'free-backlinks-generator' ); ?></a></li>
				</ul>
			</div>
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
