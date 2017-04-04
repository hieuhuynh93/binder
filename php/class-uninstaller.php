<?php
/**
 * Class Uninstaller
 *
 * @since		0.1.0
 *
 * @package mkdo\binder
 */

namespace mkdo\binder;

/**
 * Carry out actions when the plugin is uninstalled.
 *
 * Things to consider:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 */
class Uninstaller {

	/**
	 * Constructor.
	 *
	 * @since		0.1.0
	 */
	public function __construct() {}

	/**
	 * Uninstall.
	 *
	 * @since		0.1.0
	 */
	public function run() {
		register_uninstall_hook( MKDO_BINDER_ROOT, 'mkdo_binder_uninstall' );

		/**
		 * Remove the Binder tables
		 */
		function mkdo_binder_uninstall() {

			global $wpdb;

			// Drop the binder table if it exists.
			$table_name = $wpdb->prefix . 'binder_history';
		    $wpdb->query( "DROP TABLE IF EXISTS $table_name" );
		    delete_option( 'mkdo_plugin_version' );
		}
	}
}
