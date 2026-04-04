<?php

/**

 * Compact header for guest post submission (logo + back to dashboard).

 *

 * @package Free_Backlinks_Generator

 */

if ( ! is_user_logged_in() ) {

	return;

}

$dash = home_url( '/dashboard/' );

?>

<header class="fbg-submit-header" role="banner">

	<div class="fbg-submit-header__inner fbg-container">

		<?php get_template_part( 'template-parts/site', 'logo', array( 'context' => 'submit' ) ); ?>

		<nav class="fbg-submit-header__actions" aria-label="<?php esc_attr_e( 'Submission', 'free-backlinks-generator' ); ?>">

			<?php get_template_part( 'template-parts/theme', 'toggle' ); ?>

			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="fbg-submit-header__home"><?php esc_html_e( 'Go to Homepage', 'free-backlinks-generator' ); ?></a>

			<a href="<?php echo esc_url( $dash ); ?>" class="fbg-submit-header__back">

				<span class="fbg-submit-header__back-icon" aria-hidden="true">←</span>

				<?php esc_html_e( 'Back to Dashboard', 'free-backlinks-generator' ); ?>

			</a>

			<a href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>" class="fbg-submit-header__logout"><?php esc_html_e( 'Log out', 'free-backlinks-generator' ); ?></a>

		</nav>

	</div>

</header>

