<?php
/*
Plugin Name: Disable plugins when doing local dev
Description: If the WP_LOCAL_DEV constant is true, disables plugins that you specify
Version: 0.1
License: GPL version 2 or any later version
Author: Mark Jaquith
Author URI: http://coveredwebservices.com/
Plugin URL: http://markjaquith.wordpress.com/2011/06/24/wordpress-local-dev-tips/
*/

class CWS_Disable_Plugins_When_Local_Dev {
	static $instance;
	private $disabled = array();

	/**
	 * Sets up the options filter, and optionally handles an array of plugins to disable
	 * @param array $disables Optional array of plugin filenames to disable
	 */
	public function __construct( Array $disables = NULL) {
		// Handle what was passed in
		if ( is_array( $disables ) ) {
			foreach ( $disables as $disable )
				$this->disable( $disable );
		}

		// Add the filter
		add_filter( 'option_active_plugins', array( $this, 'do_disabling' ) );

		// Allow other plugins to access this instance
		self::$instance = $this;
	}

	/**
	 * Adds a filename to the list of plugins to disable
	 */
	public function disable( $file ) {
		$this->disabled[] = $file;
	}

	/**
	 * Hooks in to the option_active_plugins filter and does the disabling
	 * @param array $plugins WP-provided list of plugin filenames
	 * @return array The filtered array of plugin filenames
	 */
	public function do_disabling( $plugins ) {
		if ( count( $this->disabled ) ) {
			foreach ( (array) $this->disabled as $plugin ) {
				$key = array_search( $plugin, $plugins );
				if ( false !== $key )
					unset( $plugins[$key] );
			}
		}
		return $plugins;
	}
}

/* Begin customization */

if ( defined( 'WP_LOCAL_DEV' ) && WP_LOCAL_DEV ) {
	new CWS_Disable_Plugins_When_Local_Dev( array( 'akismet/akismet.php' ) );
	new CWS_Disable_Plugins_When_Local_Dev( array( 'wp-stats/wp-stats.php' ) );
	new CWS_Disable_Plugins_When_Local_Dev( array( 'wp-db-manager/wp-db-manager.php' ) );
	new CWS_Disable_Plugins_When_Local_Dev( array( 'wp-super-cache/wp-super-cache.php' ) );
	//new CWS_Disable_Plugins_When_Local_Dev( array( 'wp-serverinfo/wp-serverinfo.php' ) );
	//new CWS_Disable_Plugins_When_Local_Dev( array( 'lightbox-2/lightbox2.php' ) );
	//new CWS_Disable_Plugins_When_Local_Dev( array( 'wp-quick-deploy/wp-quick-deploy.php' ) );
	//new CWS_Disable_Plugins_When_Local_Dev( array( 'vaultpress.php' ) );
	/*
		For programmatic disabling, you can initialize the object (e.g. as $_localdev) then do:
		$_localdev->disable( 'vaultpress.php' );
	*/
}