<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * This file *may be moved above the wp-includes directory* per wp-load.php. 
 *
 * @package WordPress
 */
define( 'WEBS_ROOT_URL', 'creinteractive.com' ); //*** set site root url ***///
define( 'WEBS_HOST', 'http://' ); //*** set host type (e.g. 'http://') ***///

// ** Is this the local site? ** //
if ( file_exists( dirname( __FILE__ ) . '/local-config.php' ) ) {
	define( 'WP_LOCAL_DEV', true ); // Set dev vars?
	include ( dirname( __FILE__ ) . '/local-config.php' );
} else { // ** MySQL settings - You can get this info from your web host ** //

	define('DB_NAME', 'dbwp_creinteractive_com');/** The name of the database for WordPress */
	define('DB_USER', 'creinteractive');/** MySQL database username */
	define('DB_PASSWORD', 'dbcreisql04');/** MySQL database password */
	define('DB_HOST', 'mysql.creinteractive.com');/** MySQL hostname */
	define('DB_CHARSET', 'utf8');/** Database Charset to use in creating database tables. */
	define('DB_COLLATE', '');/** The Database Collate type. Don't change this if in doubt. */
	
	// ** Is this the staging site? ** //
	if ( !defined( 'WP_LOCAL_DEV' ) && file_exists( dirname( __FILE__ ) . '/stage-config.php' ) ) {
		define( 'WP_STAGE_DEV', true ); //*** set path for staging url ***///
		include ( dirname( __FILE__ ) . '/stage-config.php' );
	} else {
		
		define( 'WEBS_URI', WEBS_HOST . WEBS_ROOT_URL );/*** REMOTE PATH SETTINGS ***/

	}
}

define( 'WP_SITEURL', WEBS_URI . '/wp' ); //location of wp-includes
define( 'WP_HOME', WEBS_URI ); //frontpg location
// define('RELOCATE', true); //use when changing the above constants, after the dbase has already been set
# http://codex.wordpress.org/Changing_The_Site_URL#Relocate_method

/*** WP-CONTENT PATH SETTINGS ***/
# http://codex.wordpress.org/Hardening_WordPress#Securing_wp-config.php 
# http://codex.wordpress.org/Installing_WordPress_With_Clean_Subversion_Repositories
define( 'WP_CONTENT_DIR', dirname( __FILE__ ) . '/site-content' ); //local path wp-content dir
define( 'WP_CONTENT_URL', WEBS_URI . '/site-content' ); //URI of wp-content directory

define( 'WP_PLUGIN_DIR', dirname( __FILE__ ) . '/site-content/plugins' );
define( 'WP_PLUGIN_URL', WEBS_URI . '/site-content/plugins');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '|6G,,;|]=%cb+J:{#9_n079YLAm-5;}A96Q]+$n2OOZ3H/<9`fF9UtN:p%nZC^uL');
define('SECURE_AUTH_KEY',  '{R2q<qc4c9YPk7xXRvc%l[bwA|-2v(`FrU RAQs@Tw]we3Wkwx4-T8wpw+df_Q]8');
define('LOGGED_IN_KEY',    'lUUSSU+hc|{6-9RX9Xy>rN~/A!`]}m:={-fq&{PbxAF[[SYL2GfXl(Ju$4<+;~U+');
define('NONCE_KEY',        '<diQzM^JLry};ld?nC%TH-![g@ME.cW4aMdWe$G9lI7nGg*wdCK.*+mCl=kaS3#1');
define('AUTH_SALT',        'HQ:g-tjj0+y~inu<hyu)l,u+b-RdjdA Nfm9m+p!6g7-=eb@d)]l;v-/tL<v~{3_');
define('SECURE_AUTH_SALT', 'Da>g3g(4}gCu]v%ci of{o!Ly:p-qZ +W. <DPw^ZfHluXJmn:Z-]-vvBSH`+qD3');
define('LOGGED_IN_SALT',   'p:qO0hW?i5:5b>t&dX5b(1y:[Y-HZf$|v^J.Jn4:t(`R]H2kF?g{+NQ`6}CC_Z=V');
define('NONCE_SALT',       '}B{rhY %^r?OQac%oj @2Z_rnxjuE<U^i,js/,[/)6+X|8f>aR|g@KbPHop=aI+|');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
if ( !defined( 'WP_LOCAL_DEV' ) && !isset ( $table_prefix ) )
$table_prefix  = 'wp_9q0ig_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define( 'WPLANG', '' );

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
if ( !defined( 'WP_DEBUG' ) )
	define( 'WP_DEBUG', false );

if ( !defined( 'SCRIPT_DEBUG' ) ) //for modifying some of WordPress' built-in JavaScript
	define( 'SCRIPT_DEBUG', false );

### http://codex.wordpress.org/Editing_wp-config.php#Enable_Multisite_.2F_Network_Ability
//define('WP_ALLOW_MULTISITE', true); //Enable Multisite / Network Ability

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined( 'ABSPATH' ) )
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );

/** Sets up WordPress vars and included files. */
require_once ( ABSPATH . 'wp-settings.php' );

/**
 * Custom wp-config.php settings
 * 
 */
# Add Dev Notice on Admin Bar in Yellow, if on local server
if ( current_user_can( 'add_users' ) && ( defined( 'WP_LOCAL_DEV' ) || defined( 'WP_STAGE_DEV' ) ) ) {
	add_action( 'wp_head', 'admin_bar_dev_notice' );
	add_action( 'admin_head', 'admin_bar_dev_notice' );
}