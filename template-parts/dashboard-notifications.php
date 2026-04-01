<?php
/**
 * Dashboard tab: notifications.
 *
 * @package Free_Backlinks_Generator
 */
$list = fbg_get_notifications( get_current_user_id(), 100 );
?>
<section class="fbg-dash-panel" id="tab-notifications" data-panel="notifications" hidden tabindex="-1">
	<h2><?php esc_html_e( 'Notifications', 'free-backlinks-generator' ); ?></h2>
	<p><button type="button" class="btn-ghost" id="fbg-notify-read-all"><?php esc_html_e( 'Mark all as read', 'free-backlinks-generator' ); ?></button></p>
	<ul class="fbg-notify-list" id="fbg-notify-list">
		<?php if ( empty( $list ) ) : ?>
			<li><?php esc_html_e( 'No notifications yet.', 'free-backlinks-generator' ); ?></li>
		<?php else : ?>
			<?php foreach ( $list as $row ) : ?>
				<?php
				$nurl = ( $row->post_id && get_post( $row->post_id ) ) ? get_permalink( (int) $row->post_id ) : '';
				?>
				<li class="<?php echo $row->is_read ? 'is-read' : 'is-unread'; ?>">
					<button type="button" class="fbg-notify-item" data-id="<?php echo esc_attr( (string) $row->id ); ?>" data-url="<?php echo esc_url( $nurl ); ?>">
						<?php echo esc_html( wp_strip_all_tags( $row->message ) ); ?>
						<span class="fbg-notify-time"><?php echo esc_html( human_time_diff( strtotime( $row->created_at ), time() ) ); ?> <?php esc_html_e( 'ago', 'free-backlinks-generator' ); ?></span>
					</button>
				</li>
			<?php endforeach; ?>
		<?php endif; ?>
	</ul>
</section>
