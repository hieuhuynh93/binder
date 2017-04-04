<?php
/**
 * Class Controller_Main
 *
 * @since	0.1.0
 *
 * @package mkdo\binder
 */

namespace mkdo\binder;

/**
 * The main loader for this plugin
 */
class Controller_Main {

	/**
	 * Activator.
	 *
	 * @var 	object
	 * @access	private
	 * @since	0.1.0
	 */
	private $activator;

	/**
	 * Uninstaller.
	 *
	 * @var 	object
	 * @access	private
	 * @since	0.1.0
	 */
	private $uninstaller;

	/**
	 * Enqueue the public and admin assets.
	 *
	 * @var 	object
	 * @access	private
	 * @since	0.1.0
	 */
	private $controller_assets;

	/**
	 * Define the settings page.
	 *
	 * @var 	object
	 * @access	private
	 * @since	0.1.0
	 */
	private $settings;

	/**
	 * Notices on the admin screens.
	 *
	 * @var 	object
	 * @access	private
	 * @since	0.1.0
	 */
	private $notices_admin;

	/**
	 * The Binder Document Post Type.
	 *
	 * @var 	object
	 * @access	private
	 * @since	0.1.0
	 */
	private $post_binder;

	/**
	 * Constructor.
	 *
	 * @param Activator         $activator         Activator.
	 * @param Uninstaller       $uninstaller       Uninstaller.
	 * @param Settings          $settings          Define the settings page.
	 * @param Controller_Assets $controller_assets Enqueue the public and admin assets.
	 * @param Notices_Admin     $notices_admin     Notices on the admin screens.
	 * @param Post_Binder       $post_binder       The Binder Document Post Type.
	 *
	 * @since 0.1.0
	 */
	public function __construct(
		Activator $activator,
		Uninstaller $uninstaller,
		Settings $settings,
		Controller_Assets $controller_assets,
		Notices_Admin $notices_admin,
		Post_Binder $post_binder
	) {
		$this->activator          = $activator;
		$this->uninstaller        = $uninstaller;
		$this->settings           = $settings;
		$this->controller_assets  = $controller_assets;
		$this->notices_admin      = $notices_admin;
		$this->post_binder        = $post_binder;
	}

	/**
	 * Go.
	 *
	 * @since		0.1.0
	 */
	public function run() {
		load_plugin_textdomain(
			'binder',
			false,
			MKDO_BINDER_ROOT . '\languages'
		);

		$this->activator->run();
		// $this->uninstaller->run();
		$this->settings->run();
		$this->controller_assets->run();
		$this->notices_admin->run();
		$this->post_binder->run();
	}
}
