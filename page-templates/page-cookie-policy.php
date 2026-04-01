<?php
/**
 * Template Name: Cookie Policy
 *
 * @package Free_Backlinks_Generator
 */

get_header();
$site = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
?>
<main id="main-content" class="fbg-main">
	<section class="fbg-mkt-hero" aria-labelledby="fbg-cookie-title">
		<div class="fbg-container">
			<h1 id="fbg-cookie-title"><?php esc_html_e( 'Cookie Policy', 'free-backlinks-generator' ); ?></h1>
			<p class="fbg-mkt-hero__lead">
				<?php
				printf(
					/* translators: %s site name */
					esc_html__( 'This page explains how %s uses cookies and similar technologies, what categories we use, and how you can control them.', 'free-backlinks-generator' ),
					esc_html( $site )
				);
				?>
			</p>
			<p class="fbg-mkt-hero__lead" style="margin-top: var(--space-md); font-size: 0.95rem; opacity: 0.9;">
				<?php
				printf(
					/* translators: %s last updated date */
					esc_html__( 'Last updated: %s', 'free-backlinks-generator' ),
					esc_html( gmdate( 'F j, Y' ) )
				);
				?>
			</p>
		</div>
	</section>

	<section class="fbg-mkt-section fbg-container">
		<h2><?php esc_html_e( 'What are cookies?', 'free-backlinks-generator' ); ?></h2>
		<p class="fbg-mkt-section__sub" style="text-align: left; max-width: none;">
			<?php esc_html_e( 'Cookies are small text files stored on your device when you visit a website. They help the site remember your preferences, keep you signed in, measure traffic, and improve performance. Similar technologies include local storage and pixels.', 'free-backlinks-generator' ); ?>
		</p>
		<div class="fbg-mkt-grid fbg-mkt-grid--2" style="margin-top: var(--space-xl);">
			<div class="fbg-mkt-card">
				<div class="fbg-mkt-card__icon" aria-hidden="true">🔒</div>
				<h3><?php esc_html_e( 'Strictly necessary', 'free-backlinks-generator' ); ?></h3>
				<p><?php esc_html_e( 'Required for security, login sessions, load balancing, and consent storage. These cannot be switched off in our systems without breaking core features.', 'free-backlinks-generator' ); ?></p>
			</div>
			<div class="fbg-mkt-card">
				<div class="fbg-mkt-card__icon" aria-hidden="true">⚙️</div>
				<h3><?php esc_html_e( 'Functional', 'free-backlinks-generator' ); ?></h3>
				<p><?php esc_html_e( 'Remember choices such as language, dashboard layout, and form drafts so you do not have to re-enter information.', 'free-backlinks-generator' ); ?></p>
			</div>
			<div class="fbg-mkt-card">
				<div class="fbg-mkt-card__icon" aria-hidden="true">📊</div>
				<h3><?php esc_html_e( 'Analytics', 'free-backlinks-generator' ); ?></h3>
				<p><?php esc_html_e( 'Help us understand how visitors use the site (pages viewed, approximate location, device type). We use aggregated statistics to improve content and speed.', 'free-backlinks-generator' ); ?></p>
			</div>
			<div class="fbg-mkt-card">
				<div class="fbg-mkt-card__icon" aria-hidden="true">🎯</div>
				<h3><?php esc_html_e( 'Marketing', 'free-backlinks-generator' ); ?></h3>
				<p><?php esc_html_e( 'Used only if you opt in — to measure ad performance and limit how often you see the same message. We do not sell your data to third parties for their own advertising.', 'free-backlinks-generator' ); ?></p>
			</div>
		</div>
	</section>

	<section class="fbg-mkt-section fbg-mkt-section--alt">
		<div class="fbg-container">
			<h2><?php esc_html_e( 'Cookies we commonly use', 'free-backlinks-generator' ); ?></h2>
			<p class="fbg-mkt-section__sub"><?php esc_html_e( 'Examples below may vary if you use plugins; your browser dev tools show the live list for your visit.', 'free-backlinks-generator' ); ?></p>
			<div class="fbg-mkt-table-wrap">
				<table class="fbg-mkt-table">
					<thead>
						<tr>
							<th scope="col"><?php esc_html_e( 'Name', 'free-backlinks-generator' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Purpose', 'free-backlinks-generator' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Typical duration', 'free-backlinks-generator' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><code class="mono">wordpress_*</code></td>
							<td><?php esc_html_e( 'Authentication and admin bar (for logged-in users).', 'free-backlinks-generator' ); ?></td>
							<td><?php esc_html_e( 'Session or 14 days', 'free-backlinks-generator' ); ?></td>
						</tr>
						<tr>
							<td><code class="mono">wp-settings-*</code></td>
							<td><?php esc_html_e( 'Editor and dashboard UI preferences.', 'free-backlinks-generator' ); ?></td>
							<td><?php esc_html_e( '1 year', 'free-backlinks-generator' ); ?></td>
						</tr>
						<tr>
							<td><code class="mono">fbg_*</code></td>
							<td><?php esc_html_e( 'Theme features: session security, form nonces, optional analytics consent flag.', 'free-backlinks-generator' ); ?></td>
							<td><?php esc_html_e( 'Session / 30 days', 'free-backlinks-generator' ); ?></td>
						</tr>
						<tr>
							<td><code class="mono">_ga / _gid</code></td>
							<td><?php esc_html_e( 'Google Analytics (only if enabled by the site owner).', 'free-backlinks-generator' ); ?></td>
							<td><?php esc_html_e( '2 years / 24 hours', 'free-backlinks-generator' ); ?></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</section>

	<section class="fbg-mkt-section fbg-container">
		<h2><?php esc_html_e( 'Your choices', 'free-backlinks-generator' ); ?></h2>
		<div class="fbg-mkt-timeline">
			<p><strong><?php esc_html_e( 'Browser settings:', 'free-backlinks-generator' ); ?></strong> <?php esc_html_e( 'You can block or delete cookies through your browser. Blocking all cookies may prevent login and some dashboard tools.', 'free-backlinks-generator' ); ?></p>
			<p><strong><?php esc_html_e( 'Opt-out links:', 'free-backlinks-generator' ); ?></strong> <?php esc_html_e( 'Industry tools such as the Network Advertising Initiative or your mobile device settings can limit ad tracking where available.', 'free-backlinks-generator' ); ?></p>
			<p><strong><?php esc_html_e( 'Contact:', 'free-backlinks-generator' ); ?></strong>
				<?php
				echo wp_kses_post(
					sprintf(
						/* translators: %s: contact page URL */
						__( 'Questions? Reach us via our <a href="%s">Contact</a> page.', 'free-backlinks-generator' ),
						esc_url( home_url( '/contact/' ) )
					)
				);
				?>
			</p>
		</div>
	</section>
</main>
<?php
get_footer();
