<?php
/**
 * Contact form block (sidebar or full page). Optional vars from get_template_part() $args (extracted).
 *
 * @package Free_Backlinks_Generator
 */

if ( ! isset( $form_id ) ) {
	$form_id = 'fbg-sidebar-contact-form';
}
if ( ! isset( $feedback_id ) ) {
	$feedback_id = 'fbg-sc-feedback';
}
if ( ! isset( $name_id ) ) {
	$name_id = 'fbg-sc-name';
}
if ( ! isset( $email_id ) ) {
	$email_id = 'fbg-sc-email';
}
if ( ! isset( $message_id ) ) {
	$message_id = 'fbg-sc-msg';
}
if ( ! isset( $contact_form_title ) ) {
	$contact_form_title = __( 'Contact us', 'free-backlinks-generator' );
}
if ( ! isset( $contact_form_intro ) ) {
	$contact_form_intro = __( 'Questions about guest posts or partnerships? Send a message and we will get back to you.', 'free-backlinks-generator' );
}
if ( ! isset( $contact_wrap_class ) ) {
	$contact_wrap_class = 'fbg-sidebar-card fbg-sidebar-card--contact';
}
?>
<section class="<?php echo esc_attr( $contact_wrap_class ); ?>">
	<h2 class="fbg-sidebar-card__title"><?php echo esc_html( $contact_form_title ); ?></h2>
	<p class="fbg-sidebar-card__intro"><?php echo esc_html( $contact_form_intro ); ?></p>
	<form id="<?php echo esc_attr( $form_id ); ?>" class="fbg-sidebar-form" novalidate>
		<div class="fbg-sidebar-form__field">
			<label for="<?php echo esc_attr( $name_id ); ?>"><?php esc_html_e( 'Name', 'free-backlinks-generator' ); ?></label>
			<input type="text" id="<?php echo esc_attr( $name_id ); ?>" name="name" required maxlength="120" autocomplete="name">
		</div>
		<div class="fbg-sidebar-form__field">
			<label for="<?php echo esc_attr( $email_id ); ?>"><?php esc_html_e( 'Email', 'free-backlinks-generator' ); ?></label>
			<input type="email" id="<?php echo esc_attr( $email_id ); ?>" name="email" required autocomplete="email">
		</div>
		<div class="fbg-sidebar-form__field">
			<label for="<?php echo esc_attr( $message_id ); ?>"><?php esc_html_e( 'Message', 'free-backlinks-generator' ); ?></label>
			<textarea id="<?php echo esc_attr( $message_id ); ?>" name="message" required rows="4" maxlength="2000"></textarea>
		</div>
		<button type="submit" class="fbg-sidebar-form__submit"><?php esc_html_e( 'Send message', 'free-backlinks-generator' ); ?></button>
		<p id="<?php echo esc_attr( $feedback_id ); ?>" class="fbg-sidebar-form__feedback" role="status" hidden></p>
	</form>
</section>
