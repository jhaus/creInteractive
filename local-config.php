<?php
/**
 * Local configurations of WordPress.
 *
 * http://codex.wordpress.org/Editing_wp-config.php
 * http://markjaquith.wordpress.com/2011/06/24/wordpress-local-dev-tips/
 * 
 */

// ** MySQL settings - You can get this info from your web host ** //
define('DB_NAME', 'dbwp_creinteractive_com');/** The name of the database for WordPress */
define('DB_USER', 'root');/** MySQL database username */
define('DB_PASSWORD', 'jfn08089');/** MySQL database password */
define('DB_HOST', 'localhost');/** MySQL hostname */
define('DB_CHARSET', 'utf8');/** Database Charset to use in creating database tables. */
define('DB_COLLATE', '');/** The Database Collate type. Don't change this if in doubt. */

/**
 * WordPress Local Constants.
 * via: http://localhost:8080/xampp/phpinfo.php
 * 
 * HTTP_HOST = localhost:8080
 * SERVER_NAME = localhost
 * SERVER_PORT = 8080
 * DOCUMENT_ROOT = C:/xampp/htdocs
 * include_path = .;C:\xampp\php\PEAR;C:\xampp\apps	
 * 
 */
/*** LOCAL PATH SETTINGS ***/
define( 'WEBS_LOCAL_PATH', 'localhost:8080/projects/webs/usr-creventures/' ); //*** set relative path to dev directory ***///

define( 'WEBS_URI', WEBS_HOST . WEBS_LOCAL_PATH . WEBS_ROOT_URL );

$table_prefix  = 'wp_9q0ig_';

define( 'WP_DEBUG', true );
define( 'SCRIPT_DEBUG', true );

# Dev Notice on Admin Bar
function admin_bar_dev_notice() {
?>
<style>
li#wp-admin-bar-debug-bar, li#wp-admin-bar-debug-bar a { 
	color: #555 !important;
	background-color: #FFFF77 !important; 
	background-image: none !important; 
	text-shadow: none !important;
	font-size: 1.1em !important;
}
</style>
<?php
}
/* That's all, stop editing! Happy blogging. */

/*** OLD LOCAL PATH SETTINGS ***/

/*define( 'WEBS_ROOT_URL', 'neuenhaus.org' ); //*** set site root url ***///
/*define( 'WEBS_REL_PATH', '/projects/webs/' ); //*** set relative path to dev directory ***///
/*
if ( $_SERVER['SERVER_NAME'] !== 'localhost' && $_SERVER['SERVER_NAME'] !== '127.0.0.1' ) { // check server configuration to set local site root URI constant
	define ( 'WEBS_URI', $_SERVER['SERVER_NAME'] ); // for production site
	} elseif ( $_SERVER['SERVER_NAME'] == 'localhost' && $_SERVER['SERVER_PORT'] == '80' ) {
	define( 'WEBS_URI', $_SERVER['SERVER_NAME'] . WEBS_REL_PATH . WEBS_ROOT_URL ); // for dev site with port 80
	} else {
	define( 'WEBS_URI', $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . WEBS_REL_PATH . WEBS_ROOT_URL ); // for dev site without port 80
}

### Hard code site settings for URL's
define('WP_SITEURL', 'http://' . WEBS_URI . '/wp'); //location of wp-includes
define('WP_HOME', 'http://' . WEBS_URI); //location of site homepage/root
// define('RELOCATE', true); //use when changing the above constants, after the dbase has already been set
*/


/*
if ( !defined('ABSPATH') ) //Absolute path to the WordPress directory.
	define('ABSPATH', dirname(__FILE__) . '/');

require_once(ABSPATH . 'wp-settings.php'); //Sets up WordPress vars and included files.
*/