<?php
/**
 * Staging configurations for WordPress.
 *
 * http://codex.wordpress.org/Editing_wp-config.php
 * http://markjaquith.wordpress.com/2011/06/24/wordpress-local-dev-tips/
 * 
 */
/*** STAGING PATH SETTINGS ***/
define( 'WEBS_STAGE_PATH', 'dev.' ); //*** set relative path to dev directory ***///
	
define( 'WEBS_URI', WEBS_HOST . WEBS_STAGE_PATH . WEBS_ROOT_URL );

define( 'WP_DEBUG', true );
define( 'SCRIPT_DEBUG', true );

# Dev Notice on Admin Bar
function admin_bar_dev_notice() {
?>
<style>
li#wp-admin-bar-debug-bar, li#wp-admin-bar-debug-bar a { 
	color: #555 !important;
	background-color: #FF5200 !important; 
	background-image: none !important; 
	text-shadow: none !important;
	font-size: 1.1em !important;
}
</style>
<?php
}
