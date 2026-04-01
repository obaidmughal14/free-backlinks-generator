<?php
/**
 * Template Name: Forgot password
 *
 * @package Free_Backlinks_Generator
 */

get_header();
$step = isset( $_GET['step'] ) ? sanitize_text_field( wp_unslash( $_GET['step'] ) ) : '';
$key  = isset( $_GET['key'] ) ? sanitize_text_field( wp_unslash( $_GET['key'] ) ) : '';
$login = isset( $_GET['login'] ) ? sanitize_text_field( wp_unslash( $_GET['login'] ) ) : '';
$email_sent = isset( $_GET['email'] ) ? sanitize_email( wp_unslash( $_GET['email'] ) ) : '';
?>
<div class="fbg-auth-full">
	<div class="fbg-auth-card">
		<a href="<?php echo esc_url( home_url( '/login/' ) ); ?>" class="fbg-auth-back">← <?php esc_html_e( 'Back to Login', 'free-backlinks-generator' ); ?></a>

		<?php if ( $key && $login ) : ?>
			<h2><?php esc_html_e( 'Create a New Password', 'free-backlinks-generator' ); ?></h2>
			<p class="fbg-auth-sub"><?php esc_html_e( 'Make it strong. You won’t see it again.', 'free-backlinks-generator' ); ?></p>
			<div class="fbg-alert fbg-alert--error" id="fbg-reset-alert" role="alert" hidden></div>
			<form id="fbg-reset-form" class="fbg-form">
				<input type="hidden" name="key" value="<?php echo esc_attr( $key ); ?>">
				<input type="hidden" name="login" value="<?php echo esc_attr( $login ); ?>">
				<div class="fbg-field">
					<label for="fbg-new-pass"><?php esc_html_e( 'New Password', 'free-backlinks-generator' ); ?> *</label>
					<input type="password" id="fbg-new-pass" name="password" required minlength="8" autocomplete="new-password">
				</div>
				<div class="fbg-field">
					<label for="fbg-new-pass2"><?php esc_html_e( 'Confirm New Password', 'free-backlinks-generator' ); ?> *</label>
					<input type="password" id="fbg-new-pass2" name="confirm_password" required autocomplete="new-password">
				</div>
				<button type="submit" class="btn-primary btn-block"><?php esc_html_e( 'Update My Password', 'free-backlinks-generator' ); ?> →</button>
			</form>

		<?php elseif ( 'sent' === $step ) : ?>
			<img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/favicon.svg' ) ); ?>" alt="" width="64" height="64" class="fbg-auth-icon">
			<h2><?php esc_html_e( 'Check Your Email', 'free-backlinks-generator' ); ?></h2>
			<p class="fbg-auth-sub">
				<?php
				printf(
					/* translators: %s email */
					esc_html__( 'We sent a password reset link to %s. It expires in 24 hours.', 'free-backlinks-generator' ),
					esc_html( $email_sent ? $email_sent : __( 'your address', 'free-backlinks-generator' ) )
				);
				?>
			</p>
			<p><a class="btn-ghost btn-block" href="<?php echo esc_url( home_url( '/forgot-password/' ) ); ?>"><?php esc_html_e( 'Use a different email', 'free-backlinks-generator' ); ?></a></p>

		<?php else : ?>
			<h2><?php esc_html_e( 'Reset Your Password', 'free-backlinks-generator' ); ?></h2>
			<p class="fbg-auth-sub"><?php esc_html_e( 'No worries. Enter your email and we’ll send you a reset link.', 'free-backlinks-generator' ); ?></p>
			<div class="fbg-alert fbg-alert--error" id="fbg-forgot-alert" role="alert" hidden></div>
			<form id="fbg-forgot-form" class="fbg-form">
				<div class="fbg-field">
					<label for="fbg-forgot-email"><?php esc_html_e( 'Email Address', 'free-backlinks-generator' ); ?> *</label>
					<input type="email" id="fbg-forgot-email" name="email" required autocomplete="email">
				</div>
				<button type="submit" class="btn-primary btn-block"><?php esc_html_e( 'Send Reset Link', 'free-backlinks-generator' ); ?> →</button>
			</form>
		<?php endif; ?>
	</div>
</div>
<?php
get_footer();
