<?php
/**
 * Main fallback template.
 *
 * @package Free_Backlinks_Generator
 */

get_header();
?>
<main id="main-content" class="fbg-main fbg-container fbg-section">
	<?php
	if ( have_posts() ) :
		while ( have_posts() ) :
			the_post();
			?>
			<article <?php post_class( 'fbg-prose' ); ?>>
				<h1><?php the_title(); ?></h1>
				<?php the_content(); ?>
			</article>
			<?php
		endwhile;
	else :
		?>
		<p><?php esc_html_e( 'Nothing found.', 'free-backlinks-generator' ); ?></p>
		<?php
	endif;
	?>
</main>
<?php
get_footer();
