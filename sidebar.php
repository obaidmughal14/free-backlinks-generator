<?php
/**
 * Sidebar (optional).
 *
 * @package Free_Backlinks_Generator
 */

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}
?>
<aside class="fbg-sidebar widget-area" role="complementary">
	<?php dynamic_sidebar( 'sidebar-1' ); ?>
</aside>
