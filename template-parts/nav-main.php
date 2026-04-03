<?php

/**

 * Primary site navigation.

 *

 * @package Free_Backlinks_Generator

 */

$dash_url = home_url( '/dashboard/' );

$home_url = home_url( '/' );

?>

<nav class="fbg-nav fbg-nav--inner" id="main-nav" aria-label="<?php esc_attr_e( 'Primary', 'free-backlinks-generator' ); ?>">

	<div class="nav-container fbg-container">

		<?php get_template_part( 'template-parts/site', 'logo', array( 'context' => 'header' ) ); ?>

		<?php

		wp_nav_menu(

			array(

				'theme_location'  => 'primary',

				'container'       => false,

				'menu_class'      => 'nav-links',

				'depth'           => 1,

				'fallback_cb'     => 'fbg_primary_nav_fallback_inner',

			)

		);

		?>

		<div class="nav-actions">

			<?php if ( is_user_logged_in() ) : ?>

				<a href="<?php echo esc_url( $home_url ); ?>" class="btn-primary"><?php esc_html_e( 'Go to Homepage', 'free-backlinks-generator' ); ?> →</a>

				<a href="<?php echo esc_url( $dash_url ); ?>" class="btn-ghost"><?php esc_html_e( 'Dashboard', 'free-backlinks-generator' ); ?></a>

			<?php else : ?>

				<a href="<?php echo esc_url( home_url( '/login/' ) ); ?>" class="btn-ghost"><?php esc_html_e( 'Log In', 'free-backlinks-generator' ); ?></a>

				<a href="<?php echo esc_url( home_url( '/register/' ) ); ?>" class="btn-primary"><?php esc_html_e( 'Get Free Backlinks', 'free-backlinks-generator' ); ?> →</a>

			<?php endif; ?>

		</div>

		<button type="button" class="nav-hamburger" aria-expanded="false" aria-controls="fbg-nav-drawer" aria-label="<?php esc_attr_e( 'Open menu', 'free-backlinks-generator' ); ?>" data-label-open="<?php echo esc_attr__( 'Open menu', 'free-backlinks-generator' ); ?>" data-label-close="<?php echo esc_attr__( 'Close menu', 'free-backlinks-generator' ); ?>">☰</button>

	</div>

	<div class="fbg-nav-drawer" id="fbg-nav-drawer" hidden aria-hidden="true">

		<div class="fbg-nav-drawer__backdrop" data-fbg-drawer-close tabindex="-1" aria-hidden="true"></div>

		<div class="fbg-nav-drawer__panel" role="dialog" aria-modal="true" aria-labelledby="fbg-drawer-title-main">

			<div class="fbg-nav-drawer__head">

				<span id="fbg-drawer-title-main" class="fbg-nav-drawer__title"><?php esc_html_e( 'Menu', 'free-backlinks-generator' ); ?></span>

				<button type="button" class="fbg-nav-drawer__close" data-fbg-drawer-close aria-label="<?php esc_attr_e( 'Close menu', 'free-backlinks-generator' ); ?>"><span aria-hidden="true">×</span></button>

			</div>

			<div class="fbg-nav-drawer__body">

				<?php

				wp_nav_menu(

					array(

						'theme_location'  => 'primary',

						'container'       => false,

						'menu_class'      => 'fbg-nav-drawer__links',

						'depth'           => 1,

						'fallback_cb'     => 'fbg_primary_nav_fallback_inner',

					)

				);

				?>

				<div class="fbg-nav-drawer__actions">

					<?php if ( is_user_logged_in() ) : ?>

						<a href="<?php echo esc_url( $home_url ); ?>" class="btn-primary"><?php esc_html_e( 'Go to Homepage', 'free-backlinks-generator' ); ?> →</a>

						<a href="<?php echo esc_url( $dash_url ); ?>" class="btn-ghost btn-ghost--on-dark"><?php esc_html_e( 'Dashboard', 'free-backlinks-generator' ); ?></a>

					<?php else : ?>

						<a href="<?php echo esc_url( home_url( '/login/' ) ); ?>" class="btn-ghost"><?php esc_html_e( 'Log In', 'free-backlinks-generator' ); ?></a>

						<a href="<?php echo esc_url( home_url( '/register/' ) ); ?>" class="btn-primary"><?php esc_html_e( 'Get Free Backlinks', 'free-backlinks-generator' ); ?> →</a>

					<?php endif; ?>

				</div>

			</div>

		</div>

	</div>

</nav>

