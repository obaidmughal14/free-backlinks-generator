<?php
/**
 * Dashboard tab: my posts.
 *
 * @package Free_Backlinks_Generator
 */
$user_id    = get_current_user_id();
$can_submit = function_exists( 'fbg_user_can_create_guest_post' ) ? fbg_user_can_create_guest_post( $user_id ) : true;
$posts      = get_posts(
	array(
		'post_type'      => 'fbg_post',
		'author'         => $user_id,
		'post_status'    => array( 'publish', 'pending', 'draft' ),
		'posts_per_page' => -1,
		'orderby'        => 'date',
		'order'          => 'DESC',
	)
);
?>
<section class="fbg-dash-panel" id="tab-posts" data-panel="posts" hidden tabindex="-1">
	<h2><?php esc_html_e( 'My Posts', 'free-backlinks-generator' ); ?></h2>
	<div class="fbg-dash-toolbar">
		<label class="screen-reader-text" for="fbg-posts-search"><?php esc_html_e( 'Search', 'free-backlinks-generator' ); ?></label>
		<input type="search" id="fbg-posts-search" placeholder="<?php esc_attr_e( 'Search your posts…', 'free-backlinks-generator' ); ?>">
		<div class="fbg-filter-tabs" role="tablist">
			<button type="button" class="is-active" data-filter="all"><?php esc_html_e( 'All', 'free-backlinks-generator' ); ?> (<span id="fbg-count-all">0</span>)</button>
			<button type="button" data-filter="approved"><?php esc_html_e( 'Approved', 'free-backlinks-generator' ); ?> (<span id="fbg-count-approved">0</span>)</button>
			<button type="button" data-filter="pending"><?php esc_html_e( 'Pending', 'free-backlinks-generator' ); ?> (<span id="fbg-count-pending">0</span>)</button>
			<button type="button" data-filter="rejected"><?php esc_html_e( 'Rejected', 'free-backlinks-generator' ); ?> (<span id="fbg-count-rejected">0</span>)</button>
		</div>
	</div>
	<?php if ( empty( $posts ) ) : ?>
		<div class="fbg-empty">
			<h3><?php esc_html_e( 'No guest posts yet', 'free-backlinks-generator' ); ?></h3>
			<p><?php esc_html_e( 'Submit your first guest post and start earning free backlinks.', 'free-backlinks-generator' ); ?></p>
			<?php if ( $can_submit ) : ?>
				<a class="btn-primary" href="<?php echo esc_url( home_url( '/submit-post/' ) ); ?>"><?php esc_html_e( 'Submit Your First Post', 'free-backlinks-generator' ); ?> →</a>
			<?php else : ?>
				<a class="btn-primary" href="<?php echo esc_url( get_post_type_archive_link( 'fbg_post' ) ); ?>"><?php esc_html_e( 'Browse posts to unlock a slot', 'free-backlinks-generator' ); ?> →</a>
			<?php endif; ?>
		</div>
	<?php else : ?>
		<form id="fbg-posts-bulk" class="fbg-table-wrap">
			<table class="fbg-table" id="fbg-posts-table">
				<thead>
					<tr>
						<th><span class="screen-reader-text"><?php esc_html_e( 'Select', 'free-backlinks-generator' ); ?></span><input type="checkbox" id="fbg-select-all"></th>
						<th><?php esc_html_e( 'Title', 'free-backlinks-generator' ); ?></th>
						<th><?php esc_html_e( 'Niche', 'free-backlinks-generator' ); ?></th>
						<th><?php esc_html_e( 'Status', 'free-backlinks-generator' ); ?></th>
						<th><?php esc_html_e( 'Links', 'free-backlinks-generator' ); ?></th>
						<th><?php esc_html_e( 'Views', 'free-backlinks-generator' ); ?></th>
						<th><?php esc_html_e( 'Date', 'free-backlinks-generator' ); ?></th>
						<th><?php esc_html_e( 'Actions', 'free-backlinks-generator' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ( $posts as $p ) :
						$st = get_post_meta( $p->ID, '_fbg_content_status', true );
						if ( 'publish' === $p->post_status ) {
							$filter_st = 'approved';
						} elseif ( 'pending' === $p->post_status ) {
							$filter_st = 'pending';
						} elseif ( 'rejected' === $st ) {
							$filter_st = 'rejected';
						} elseif ( 'draft' === $p->post_status ) {
							$filter_st = 'draft';
						} else {
							$filter_st = 'pending';
						}
						$terms = get_the_terms( $p->ID, 'fbg_niche' );
						$niche = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : '';
						$reason = get_post_meta( $p->ID, '_fbg_rejection_reason', true );
						$status_label = array(
							'approved' => __( 'Live', 'free-backlinks-generator' ),
							'pending'  => __( 'Pending review', 'free-backlinks-generator' ),
							'rejected' => __( 'Needs revision', 'free-backlinks-generator' ),
							'draft'    => __( 'Draft', 'free-backlinks-generator' ),
						);
						$st_label = isset( $status_label[ $filter_st ] ) ? $status_label[ $filter_st ] : $filter_st;
						?>
						<tr data-status="<?php echo esc_attr( $filter_st ); ?>" data-title="<?php echo esc_attr( $p->post_title ); ?>">
							<td><input type="checkbox" name="ids[]" value="<?php echo esc_attr( (string) $p->ID ); ?>"></td>
							<td><a href="<?php echo esc_url( get_permalink( $p ) ); ?>"><?php echo esc_html( $p->post_title ); ?></a></td>
							<td><?php echo esc_html( $niche ); ?></td>
							<td><?php echo esc_html( $st_label ); ?></td>
							<td><?php echo esc_html( (string) (int) get_post_meta( $p->ID, '_fbg_backlink_count', true ) ); ?></td>
							<td><?php echo 'publish' === $p->post_status ? esc_html( number_format_i18n( (int) get_post_meta( $p->ID, '_fbg_view_count', true ) ) ) : '—'; ?></td>
							<td><?php echo esc_html( get_the_date( '', $p ) ); ?></td>
							<td>
								<a href="<?php echo esc_url( get_permalink( $p ) ); ?>"><?php esc_html_e( 'View', 'free-backlinks-generator' ); ?></a>
								<button type="button" class="fbg-link-delete" data-id="<?php echo esc_attr( (string) $p->ID ); ?>"><?php esc_html_e( 'Delete', 'free-backlinks-generator' ); ?></button>
							</td>
						</tr>
						<?php if ( $reason && 'rejected' === $filter_st ) : ?>
							<tr class="fbg-reject-row" data-status="<?php echo esc_attr( $filter_st ); ?>">
								<td colspan="8"><?php printf( esc_html__( 'Admin note: %s', 'free-backlinks-generator' ), esc_html( $reason ) ); ?></td>
							</tr>
						<?php endif; ?>
					<?php endforeach; ?>
				</tbody>
			</table>
			<button type="button" class="btn-ghost" id="fbg-bulk-delete"><?php esc_html_e( 'Delete selected', 'free-backlinks-generator' ); ?></button>
		</form>
	<?php endif; ?>
</section>
