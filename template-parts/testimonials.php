<?php

/**

 * Testimonials carousel (home landing) — from Testimonials admin or defaults.

 *

 * @package Free_Backlinks_Generator

 */



$rows = array();



if ( post_type_exists( 'fbg_testimonial' ) ) {

	$q = new WP_Query(

		array(

			'post_type'              => 'fbg_testimonial',

			'post_status'            => 'publish',

			'posts_per_page'         => -1,

			'orderby'                => array(

				'menu_order' => 'ASC',

				'title'      => 'ASC',

			),

			'no_found_rows'          => true,

			'update_post_meta_cache' => true,

		)

	);

	if ( $q->have_posts() ) {

		while ( $q->have_posts() ) {

			$q->the_post();

			$pid    = get_the_ID();

			$rating = (int) get_post_meta( $pid, '_fbg_testimonial_rating', true );

			if ( $rating < 1 || $rating > 5 ) {

				$rating = 5;

			}

			$content = get_post_field( 'post_content', $pid );

			$rows[]  = array(

				'quote_html' => wp_kses_post( wpautop( $content ) ),

				'author'     => get_the_title(),

				'role'       => (string) get_post_meta( $pid, '_fbg_testimonial_role', true ),

				'since'      => (string) get_post_meta( $pid, '_fbg_testimonial_since', true ),

				'stat'       => (string) get_post_meta( $pid, '_fbg_testimonial_stat', true ),

				'rating'     => $rating,

				'thumb_id'   => (int) get_post_thumbnail_id( $pid ),

			);

		}

		wp_reset_postdata();

	}

}



if ( ! $rows && function_exists( 'fbg_get_default_testimonials_seed' ) ) {

	foreach ( fbg_get_default_testimonials_seed() as $s ) {

		$rows[] = array(

			'quote_html' => '<p>' . esc_html( $s['quote'] ) . '</p>',

			'author'     => $s['author'],

			'role'       => $s['role'],

			'since'      => $s['since'],

			'stat'       => $s['stat'],

			'rating'     => 5,

			'thumb_id'   => 0,

		);

	}

}



if ( ! $rows ) {

	return;

}

?>

<section class="fbg-section fbg-testimonials" aria-labelledby="fbg-testimonials-heading">

	<div class="fbg-container">

		<h2 id="fbg-testimonials-heading"><?php esc_html_e( 'Real results from real members', 'free-backlinks-generator' ); ?></h2>

		<p class="fbg-section__sub"><?php esc_html_e( 'Bloggers, agencies, and SEOs use our free guest-post community to earn contextual backlinks and grow domain authority — without private blog networks or paid link schemes.', 'free-backlinks-generator' ); ?></p>



		<div class="fbg-testimonials-carousel" id="fbg-testimonials-carousel" data-autoplay="7500" role="region" aria-roledescription="<?php esc_attr_e( 'Carousel', 'free-backlinks-generator' ); ?>" aria-label="<?php esc_attr_e( 'Member testimonials', 'free-backlinks-generator' ); ?>">

			<div class="fbg-testimonials-carousel__viewport" id="fbg-testimonials-viewport">

				<div class="fbg-testimonials-carousel__track">

					<?php foreach ( $rows as $row ) : ?>

						<?php

						$rating = isset( $row['rating'] ) ? (int) $row['rating'] : 5;

						$rating = max( 1, min( 5, $rating ) );
						$stars  = str_repeat( '★', $rating );

						?>

					<div class="fbg-testimonial-slide">

						<blockquote class="fbg-testimonial-card" cite="<?php echo esc_url( home_url( '/' ) ); ?>">

							<div class="fbg-testimonial-card__inner">

								<div class="fbg-testimonial-card__media">

									<?php if ( ! empty( $row['thumb_id'] ) ) : ?>

										<?php echo wp_get_attachment_image( $row['thumb_id'], 'fbg_testimonial_avatar', false, array( 'class' => 'fbg-testimonial-card__avatar' ) ); ?>

									<?php else : ?>

										<span class="fbg-testimonial-card__initials" aria-hidden="true"><?php echo esc_html( fbg_testimonial_initials( $row['author'] ) ); ?></span>

									<?php endif; ?>

								</div>

								<div class="fbg-testimonial-card__body">

									<div class="fbg-testimonial-card__quote"><?php echo $row['quote_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built with wp_kses_post above. ?></div>

									<footer>

										<strong><?php echo esc_html( $row['author'] ); ?></strong><?php echo $row['role'] ? ', ' . esc_html( $row['role'] ) : ''; ?>

										<br><span class="fbg-stars" aria-hidden="true"><?php echo esc_html( $stars ); ?></span>

										<span class="screen-reader-text"><?php echo esc_html( sprintf( /* translators: %d star count */ __( '%d out of 5 stars', 'free-backlinks-generator' ), $rating ) ); ?></span>

										<?php if ( $row['since'] || $row['stat'] ) : ?>

											<br><?php echo esc_html( trim( $row['since'] . ( $row['since'] && $row['stat'] ? ' · ' : '' ) . $row['stat'] ) ); ?>

										<?php endif; ?>

									</footer>

								</div>

							</div>

						</blockquote>

					</div>

					<?php endforeach; ?>

				</div>

			</div>

			<div class="fbg-tc-nav">

				<button type="button" class="fbg-tc-btn fbg-tc-prev" aria-controls="fbg-testimonials-viewport" aria-label="<?php esc_attr_e( 'Previous testimonials', 'free-backlinks-generator' ); ?>">‹</button>

				<div class="fbg-tc-dots" role="tablist" aria-label="<?php esc_attr_e( 'Testimonial slides', 'free-backlinks-generator' ); ?>"></div>

				<button type="button" class="fbg-tc-btn fbg-tc-next" aria-controls="fbg-testimonials-viewport" aria-label="<?php esc_attr_e( 'Next testimonials', 'free-backlinks-generator' ); ?>">›</button>

			</div>

		</div>

	</div>

</section>

