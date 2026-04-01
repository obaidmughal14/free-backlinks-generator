<?php
/**
 * Dashboard tab: backlinks.
 *
 * @package Free_Backlinks_Generator
 */
$user_id = get_current_user_id();
$posts   = get_posts(
	array(
		'post_type'      => 'fbg_post',
		'author'         => $user_id,
		'post_status'    => array( 'publish', 'pending' ),
		'posts_per_page' => -1,
	)
);
$live_posts = count(
	get_posts(
		array(
			'post_type'      => 'fbg_post',
			'author'         => $user_id,
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids',
		)
	)
);
$link_total = 0;
foreach ( $posts as $p ) {
	if ( 'publish' === $p->post_status ) {
		$link_total += (int) get_post_meta( $p->ID, '_fbg_backlink_count', true );
	}
}
?>
<section class="fbg-dash-panel" id="tab-links" data-panel="links" hidden tabindex="-1">
	<h2><?php esc_html_e( 'My Links', 'free-backlinks-generator' ); ?></h2>
	<p class="fbg-links-summary">
		<?php
		echo esc_html(
			sprintf(
				/* translators: 1: link count, 2: post count */
				__( 'You have %1$s live backlinks across %2$s published posts.', 'free-backlinks-generator' ),
				(string) $link_total,
				(string) $live_posts
			)
		);
		?>
		<a class="btn-ghost" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'fbg_export_links', '1', home_url( '/' ) ), 'fbg_export_csv' ) ); ?>"><?php esc_html_e( '📥 Export as CSV', 'free-backlinks-generator' ); ?></a>
	</p>
	<div class="fbg-info-bar">
		<?php esc_html_e( '💡 You’re on the Free plan (max 3 links per post). Pro members get unlimited links + dofollow links by default.', 'free-backlinks-generator' ); ?>
		<button type="button" class="btn-upgrade-inline"><?php esc_html_e( 'Upgrade to Pro', 'free-backlinks-generator' ); ?> →</button>
	</div>
	<div class="fbg-table-wrap">
		<table class="fbg-table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Target URL', 'free-backlinks-generator' ); ?></th>
					<th><?php esc_html_e( 'Anchor', 'free-backlinks-generator' ); ?></th>
					<th><?php esc_html_e( 'In Post', 'free-backlinks-generator' ); ?></th>
					<th><?php esc_html_e( 'Status', 'free-backlinks-generator' ); ?></th>
					<th><?php esc_html_e( 'Since', 'free-backlinks-generator' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				$any = false;
				foreach ( $posts as $p ) :
					$links = get_post_meta( $p->ID, '_fbg_backlinks_array', true );
					if ( ! is_array( $links ) ) {
						continue;
					}
					foreach ( $links as $row ) :
						$any = true;
						$url = isset( $row['url'] ) ? $row['url'] : '';
						$an  = isset( $row['anchor'] ) ? $row['anchor'] : '';
						$st  = 'publish' === $p->post_status ? __( 'Live', 'free-backlinks-generator' ) : __( 'Pending', 'free-backlinks-generator' );
						?>
						<tr>
							<td class="mono fbg-truncate"><a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $url ); ?></a></td>
							<td><?php echo esc_html( $an ); ?></td>
							<td><a href="<?php echo esc_url( get_permalink( $p ) ); ?>"><?php echo esc_html( $p->post_title ); ?></a></td>
							<td><?php echo esc_html( $st ); ?></td>
							<td><?php echo esc_html( get_the_date( 'M j', $p ) ); ?></td>
						</tr>
						<?php
					endforeach;
				endforeach;
				if ( ! $any ) :
					?>
					<tr><td colspan="5"><?php esc_html_e( 'No backlinks recorded yet.', 'free-backlinks-generator' ); ?></td></tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</section>
