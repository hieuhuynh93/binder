<?php
/**
 * Uninstall
 *
 * Code that runs when the plugin is uninstalled.
 *
 * @since	0.1.0
 *
 * @package mkdo\binder
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

global $wpdb;

// Drop the binder table if it exists.
$table_name = $wpdb->prefix . 'binder_history';
wp_die( print_r( $wpdb->query( "DROP TABLE IF EXISTS $table_name" ) ) );
delete_option( 'mkdo_plugin_version' );
