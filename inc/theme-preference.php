<?php
/**
 * Dark / light theme: early script to reduce flash, body class hint.
 *
 * @package Free_Backlinks_Generator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Inline script in head: apply saved or system preference before paint.
 */
function fbg_theme_preference_head_script() {
	?>
	<script>
	(function(){
		try {
			var k = 'fbg-theme';
			var d = document.documentElement;
			var s = localStorage.getItem(k);
			var dark = s === 'dark' || (s !== 'light' && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
			if (dark) { d.setAttribute('data-theme', 'dark'); }
			else { d.removeAttribute('data-theme'); }
		} catch (e) {}
	})();
	</script>
	<?php
}
add_action( 'wp_head', 'fbg_theme_preference_head_script', 1 );
