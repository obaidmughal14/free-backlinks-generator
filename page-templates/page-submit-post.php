<?php
/**
 * Template Name: Submit Guest Post
 *
 * @package Free_Backlinks_Generator
 */

if ( ! is_user_logged_in() ) {
	wp_safe_redirect( home_url( '/login/' ) );
	exit;
}

get_header();
$uid    = get_current_user_id();
$max_l  = fbg_get_user_link_limit( $uid );
$niches = fbg_niche_options();
$ctypes = fbg_content_type_options();
?>
<main id="main-content" class="fbg-submit fbg-submit-wrap">
	<section class="fbg-submit-hero" aria-labelledby="fbg-submit-title">
		<h1 id="fbg-submit-title"><?php esc_html_e( 'Submit a Guest Post', 'free-backlinks-generator' ); ?></h1>
		<p class="fbg-submit__sub"><?php esc_html_e( 'Write original content in your niche with your backlinks embedded naturally. Posts are reviewed by our team within 24–48 hours before going live.', 'free-backlinks-generator' ); ?></p>
	</section>

	<div class="fbg-submit-card">
		<div class="fbg-info-bar"><?php printf( esc_html__( 'Your plan allows up to %d backlinks per post. Use them naturally inside the article.', 'free-backlinks-generator' ), (int) $max_l ); ?></div>
		<div class="fbg-alert fbg-alert--error" id="fbg-submit-alert" role="alert" tabindex="-1" hidden></div>
		<form id="fbg-submit-form" class="fbg-form fbg-submit-form" novalidate>
			<div class="fbg-field">
				<label for="fbg-post-title"><?php esc_html_e( 'Post Title', 'free-backlinks-generator' ); ?> *</label>
				<input type="text" id="fbg-post-title" name="title" required maxlength="120" autocomplete="off">
				<small id="fbg-title-preview" class="mono"></small>
				<span id="fbg-title-count" class="fbg-char-count" aria-live="polite">0/70</span>
			</div>
			<div class="fbg-field">
				<label for="fbg-post-niche"><?php esc_html_e( 'Niche / Category', 'free-backlinks-generator' ); ?> *</label>
				<select id="fbg-post-niche" name="niche" required>
					<option value=""><?php esc_html_e( 'Select…', 'free-backlinks-generator' ); ?></option>
					<?php foreach ( $niches as $slug => $label ) : ?>
						<option value="<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="fbg-field">
				<label for="fbg-post-type"><?php esc_html_e( 'Content Type', 'free-backlinks-generator' ); ?> *</label>
				<select id="fbg-post-type" name="content_type" required>
					<?php foreach ( $ctypes as $slug => $label ) : ?>
						<option value="<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="fbg-field">
				<label for="fbg-featured-id"><?php esc_html_e( 'Featured Image', 'free-backlinks-generator' ); ?> *</label>
				<input type="hidden" name="featured_image_id" id="fbg-featured-id" value="">
				<button type="button" class="btn-ghost" id="fbg-featured-btn"><?php esc_html_e( 'Select image', 'free-backlinks-generator' ); ?></button>
				<div id="fbg-featured-preview"></div>
				<small><?php esc_html_e( 'Required for submission. Max 5MB. JPG, PNG, or WebP.', 'free-backlinks-generator' ); ?></small>
			</div>
			<div class="fbg-field">
				<label class="fbg-field-label-block"><?php esc_html_e( 'Guest Post Content', 'free-backlinks-generator' ); ?> *</label>
				<?php
				wp_editor(
					'',
					'fbg_post_content',
					array(
						'textarea_name' => 'content',
						'media_buttons' => false,
						'teeny'         => true,
						'quicktags'     => true,
						'textarea_rows' => 18,
					)
				);
				?>
				<span id="fbg-word-count" class="fbg-char-count" aria-live="polite">0 / 600 <?php esc_html_e( 'minimum', 'free-backlinks-generator' ); ?></span>
			</div>
			<div class="fbg-field">
				<label><?php esc_html_e( 'Your Backlinks', 'free-backlinks-generator' ); ?> *</label>
				<div id="fbg-backlinks-rows"></div>
				<button type="button" class="btn-ghost" id="fbg-add-link"><?php esc_html_e( '+ Add a Backlink', 'free-backlinks-generator' ); ?></button>
				<small><?php esc_html_e( 'Add at least one valid URL and anchor text when submitting for review.', 'free-backlinks-generator' ); ?></small>
			</div>
			<div class="fbg-field">
				<label for="fbg-excerpt"><?php esc_html_e( 'Meta Description / Excerpt', 'free-backlinks-generator' ); ?> *</label>
				<textarea id="fbg-excerpt" name="excerpt" rows="3" maxlength="200" required></textarea>
				<span id="fbg-excerpt-count" class="fbg-char-count" aria-live="polite">0/160</span>
			</div>
			<div class="fbg-submit-actions">
				<button type="submit" name="save" value="draft" class="btn-ghost" id="fbg-save-draft"><?php esc_html_e( 'Save as Draft', 'free-backlinks-generator' ); ?></button>
				<button type="submit" name="save" value="submit" class="btn-primary" id="fbg-submit-review"><?php esc_html_e( 'Submit for Review', 'free-backlinks-generator' ); ?> →</button>
			</div>
		</form>
	</div>
</main>
<?php
get_footer();
