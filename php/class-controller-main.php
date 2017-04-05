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
	 * Define the settings page.
	 *
	 * @var 	object
	 * @access	private
	 * @since	0.1.0
	 */
	private $settings;

	/**
	 * Enqueue the public and admin assets.
	 *
	 * @var 	object
	 * @access	private
	 * @since	0.1.0
	 */
	private $controller_assets;

	/**
	 * The Binder Document Add Entry Meta.
	 *
	 * @var 	object
	 * @access	private
	 * @since	0.1.0
	 */
	private $meta_binder_add_entry;

	/**
	 * The Binder Document Version Control Meta.
	 *
	 * @var 	object
	 * @access	private
	 * @since	0.1.0
	 */
	private $meta_binder_version_control;

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
	 * Taxonomy for Document Category
	 *
	 * @var object
	 */
	private $taxonomy_binder_category;

	/**
	 * Taxonomy for Document Tag
	 *
	 * @var object
	 */
	private $taxonomy_binder_tag;

	/**
	 * Taxonomy for Document Type
	 *
	 * @var object
	 */
	private $taxonomy_binder_type;

	/**
	 * Constructor.
	 *
	 * @param Activator                   $activator                   Activator.
	 * @param Settings                    $settings                    Define the settings page.
	 * @param Controller_Assets           $controller_assets           Enqueue the public and admin assets.
	 * @param Meta_Binder_Add_Entry       $meta_binder_add_entry       The Binder Document Add Entry Meta.
	 * @param Meta_Binder_Version_Control $meta_binder_version_control The Binder Version Control Meta.
	 * @param Notices_Admin               $notices_admin               Notices on the admin screens.
	 * @param Post_Binder                 $post_binder                 The Binder Document Post Type.
	 * @param Taxonomy_Binder_Category    $taxonomy_binder_category    Taxonomy for Document Category.
	 * @param Taxonomy_Binder_Tag         $taxonomy_binder_tag         Taxonomy for Document Tag.
	 * @param Taxonomy_Binder_Type        $taxonomy_binder_type        Taxonomy for Document Type.
	 *
	 * @since 0.1.0
	 */
	public function __construct(
		Activator $activator,
		Settings $settings,
		Controller_Assets $controller_assets,
		Meta_Binder_Add_Entry $meta_binder_add_entry,
		Meta_Binder_Version_Control $meta_binder_version_control,
		Notices_Admin $notices_admin,
		Post_Binder $post_binder,
		Taxonomy_Binder_Category $taxonomy_binder_category,
		Taxonomy_Binder_Tag $taxonomy_binder_tag,
		Taxonomy_Binder_Type $taxonomy_binder_type
	) {
		$this->activator                   = $activator;
		$this->settings                    = $settings;
		$this->controller_assets           = $controller_assets;
		$this->meta_binder_add_entry       = $meta_binder_add_entry;
		$this->meta_binder_version_control = $meta_binder_version_control;
		$this->notices_admin               = $notices_admin;
		$this->post_binder                 = $post_binder;
		$this->taxonomy_binder_category    = $taxonomy_binder_category;
		$this->taxonomy_binder_tag         = $taxonomy_binder_tag;
		$this->taxonomy_binder_type        = $taxonomy_binder_type;
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
		$this->settings->run();
		$this->controller_assets->run();
		$this->meta_binder_add_entry->run();
		$this->meta_binder_version_control->run();
		$this->notices_admin->run();
		$this->post_binder->run();
		$this->taxonomy_binder_category->run();
		$this->taxonomy_binder_tag->run();
		$this->taxonomy_binder_type->run();
	}
}
