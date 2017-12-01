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
	 * Load the Binder Document.
	 *
	 * @var 	object
	 * @access	private
	 * @since	0.1.0
	 */
	private $load_binder_document;

	/**
	 * The Binder Document Add Entry Meta.
	 *
	 * @var 	object
	 * @access	private
	 * @since	0.1.0
	 */
	private $meta_binder_add_entry;

	/**
	 * The Binder Document Type Meta.
	 *
	 * @var 	object
	 * @access	private
	 * @since	0.1.0
	 */
	private $meta_binder_document_type;

	/**
	 * Make the excerpt into the summary.
	 *
	 * @var 	object
	 * @access	private
	 * @since	0.1.0
	 */
	private $meta_binder_excerpt;

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
	 * The Binder Document Shortcode.
	 *
	 * @var 	object
	 * @access	private
	 * @since	0.1.0
	 */
	private $shortcode_binder_document;

	/**
	 * The Binder Document List Shortcode.
	 *
	 * @var 	object
	 * @access	private
	 * @since	0.1.0
	 */
	private $shortcode_binder_document_list;

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
	 * @param Activator                      $activator                      Activator.
	 * @param Settings                       $settings                       Define the settings page.
	 * @param Controller_Assets              $controller_assets              Enqueue the public and admin assets.
	 * @param Load_Binder_Document           $load_binder_document           Load Binder Document.
	 * @param Meta_Binder_Add_Entry          $meta_binder_add_entry          The Binder Document Add Entry Meta.
	 * @param Meta_Binder_Document_Type      $meta_binder_document_type      The Binder Document Type Meta.
	 * @param Meta_Binder_Excerpt            $meta_binder_excerpt            Make the excerpt into the summary.
	 * @param Meta_Binder_Version_Control    $meta_binder_version_control    The Binder Version Control Meta.
	 * @param Notices_Admin                  $notices_admin                  Notices on the admin screens.
	 * @param Post_Binder                    $post_binder                    The Binder Document Post Type.
	 * @param Shortcode_Binder_Document      $shortcode_binder_document      The Binder Document Shortcode.
	 * @param Shortcode_Binder_Document_List $shortcode_binder_document_list The Binder Document List Shortcode.
	 * @param Taxonomy_Binder_Category       $taxonomy_binder_category       Taxonomy for Document Category.
	 * @param Taxonomy_Binder_Tag            $taxonomy_binder_tag            Taxonomy for Document Tag.
	 * @param Taxonomy_Binder_Type           $taxonomy_binder_type           Taxonomy for Document Type.
	 *
	 * @since 0.1.0
	 */
	public function __construct(
		Activator $activator,
		Settings $settings,
		Controller_Assets $controller_assets,
		Load_Binder_Document $load_binder_document,
		Meta_Binder_Add_Entry $meta_binder_add_entry,
		Meta_Binder_Document_Type $meta_binder_document_type,
		Meta_Binder_Excerpt $meta_binder_excerpt,
		Meta_Binder_Version_Control $meta_binder_version_control,
		Notices_Admin $notices_admin,
		Post_Binder $post_binder,
		Shortcode_Binder_Document $shortcode_binder_document,
		Shortcode_Binder_Document_List $shortcode_binder_document_list,
		Taxonomy_Binder_Category $taxonomy_binder_category,
		Taxonomy_Binder_Tag $taxonomy_binder_tag,
		Taxonomy_Binder_Type $taxonomy_binder_type
	) {
		$this->activator                      = $activator;
		$this->settings                       = $settings;
		$this->controller_assets              = $controller_assets;
		$this->load_binder_document           = $load_binder_document;
		$this->meta_binder_add_entry          = $meta_binder_add_entry;
		$this->meta_binder_document_type      = $meta_binder_document_type;
		$this->meta_binder_excerpt            = $meta_binder_excerpt;
		$this->meta_binder_version_control    = $meta_binder_version_control;
		$this->notices_admin                  = $notices_admin;
		$this->post_binder                    = $post_binder;
		$this->shortcode_binder_document      = $shortcode_binder_document;
		$this->shortcode_binder_document_list = $shortcode_binder_document_list;
		$this->taxonomy_binder_category       = $taxonomy_binder_category;
		$this->taxonomy_binder_tag            = $taxonomy_binder_tag;
		$this->taxonomy_binder_type           = $taxonomy_binder_type;
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
		// $this->settings->run();
		$this->controller_assets->run();
		$this->load_binder_document->run();
		$this->meta_binder_add_entry->run();
		$this->meta_binder_document_type->run();
		$this->meta_binder_excerpt->run();
		$this->meta_binder_version_control->run();
		$this->notices_admin->run();
		$this->post_binder->run();
		$this->shortcode_binder_document->run();
		$this->shortcode_binder_document_list->run();
		$this->taxonomy_binder_category->run();
		$this->taxonomy_binder_tag->run();
		$this->taxonomy_binder_type->run();
	}
}
