<?php
/**
 * Class Activator
 *
 * @since	0.1.0
 *
 * @package mkdo\binder
 */

namespace mkdo\binder;

/**
 * Carry out actions when the plugin is activated.
 */
class Activator {

	/**
	 * Constructor.
	 *
	 * @since	0.1.0
	 */
	public function __construct() {}

	/**
	 * Go.
	 *
	 * @since	0.1.0
	 */
	public function run() {
		// Register the activation callback.
		register_activation_hook( MKDO_BINDER_ROOT, array( $this, 'activate' ) );
		add_action( 'plugins_loaded', array( $this, 'upgrade' ) );
	}

	/**
	 * Activate the plugin.
	 *
	 * @since	0.1.0
	 */
	public function activate() {

		global $wpdb;

		$table_name      = MKDO_BINDER_HISTORY_TABLE;
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			binder_id   BIGINT(20)   NOT NULL AUTO_INCREMENT,
			upload_date DATETIME     DEFAULT '0000-00-00 00:00:00' NOT NULL,
			post_id     BIGINT(20)   NOT NULL,
			user_id     BIGINT(20)   NOT NULL,
			type        VARCHAR(20)  DEFAULT '' NOT NULL,
			status      VARCHAR(20)  DEFAULT '' NOT NULL,
			version     VARCHAR(20)  DEFAULT '',
			name        VARCHAR(200) DEFAULT '',
			description LONGTEXT     DEFAULT '',
			folder      VARCHAR(200) DEFAULT '',
			file        VARCHAR(200) DEFAULT '',
			size        VARCHAR(20)  DEFAULT '',
			thumb       LONGTEXT     DEFAULT '',
			mime_type   VARCHAR(200) DEFAULT '',
			PRIMARY KEY (binder_id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		// Add the version to the options table.
		update_option( 'mkdo_binder_version', MKDO_BINDER_VERSION );

		// TODO:
		//
		// Create a file and check for:
		//
		// - Thumbnail generator support
		// - PDF extension support
		// - Get File Contents Suport
		// - File permissions.
		$support = 'TODO';
	}

	/**
	 * Upgrade the plugin.
	 *
	 * @since	0.1.0
	 */
	public function upgrade() {
		$version = get_option( 'mkdo_binder_version' );
		if ( MKDO_BINDER_VERSION !== $version ) {
			// Upgrade the version number.
			update_option( 'mkdo_binder_version', MKDO_BINDER_VERSION );
		}
	}
}
