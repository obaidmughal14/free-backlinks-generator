<?php
/**
 * Template Name: Login
 *
 * @package Free_Backlinks_Generator
 */

get_header();
?>
<div class="fbg-auth-full">
	<div class="fbg-auth-card">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="fbg-auth-card__logo">
			<img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/logo.svg' ) ); ?>" alt="<?php bloginfo( 'name' ); ?>" width="180" height="44">
		</a>
		<h2><?php esc_html_e( 'Welcome Back', 'free-backlinks-generator' ); ?></h2>
		<p class="fbg-auth-sub"><?php esc_html_e( 'Sign in to your Free Backlinks Generator account', 'free-backlinks-generator' ); ?></p>
		<?php if ( isset( $_GET['reset'] ) && 'success' === sanitize_text_field( wp_unslash( $_GET['reset'] ) ) ) : ?>
			<div class="fbg-alert fbg-alert--success" role="status"><?php esc_html_e( 'Password updated. Please sign in.', 'free-backlinks-generator' ); ?></div>
		<?php endif; ?>
		<div class="fbg-alert fbg-alert--error" id="fbg-login-alert" role="alert" hidden></div>
		<form id="fbg-login-form" class="fbg-form">
			<div class="fbg-field">
				<label for="fbg-login-email"><?php esc_html_e( 'Email Address', 'free-backlinks-generator' ); ?> *</label>
				<input type="email" id="fbg-login-email" name="email" required autocomplete="username">
			</div>
			<div class="fbg-field">
				<label for="fbg-login-pass"><?php esc_html_e( 'Password', 'free-backlinks-generator' ); ?> *</label>
				<input type="password" id="fbg-login-pass" name="password" required autocomplete="current-password">
			</div>
			<div class="fbg-field-row">
				<label class="fbg-field--check"><input type="checkbox" name="remember" value="1"> <?php esc_html_e( 'Remember me', 'free-backlinks-generator' ); ?></label>
				<a href="<?php echo esc_url( home_url( '/forgot-password/' ) ); ?>"><?php esc_html_e( 'Forgot password?', 'free-backlinks-generator' ); ?> →</a>
			</div>
			<button type="submit" class="btn-primary btn-block"><?php esc_html_e( 'Sign In', 'free-backlinks-generator' ); ?> →</button>
		</form>
		<p class="fbg-auth-divider"><span><?php esc_html_e( 'or', 'free-backlinks-generator' ); ?></span></p>
		<p class="fbg-auth-bottom"><a href="<?php echo esc_url( home_url( '/register/' ) ); ?>"><?php esc_html_e( 'Don’t have an account? Join free — it takes 60 seconds', 'free-backlinks-generator' ); ?> →</a></p>
	</div>
</div>
<?php
get_footer();
