<?php
/**
 * Class Activator
 *
 * @since	0.1.0
 *
 * @package mkdo\ground_control
 */

namespace mkdo\ground_control;

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
	 * Unleash Hell.
	 *
	 * @since	0.1.0
	 */
	public function run() {
		// Register the activation callback.
		register_activation_hook( MKDO_BINDER_ROOT, array( $this, 'activate' ) );
	}

	/**
	 * Activate the plugin.
	 *
	 * @since	0.1.0
	 */
	public function activate() {
		// Set a transient to confirm activation.
		set_transient( MKDO_BINDER_PREFIX . '_activated', true, 10 );
	}
}
