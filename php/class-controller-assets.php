<?php
/**
 * Class Controller_Assets
 *
 * @since	0.1.0
 *
 * @package mkdo\binder
 */

namespace mkdo\binder;

/**
 * Sets up the public and admin area JS and CSS needed for this plugin.
 *
 * These enqueues should exist here for reference, but we highly recommend that
 * the appropriate filters are used to deactivate these enqueues, and these are
 * concatenated and enqueued in your own theme workflow.
 */
class Controller_Assets {

	/**
	 * Is debug mode on
	 *
	 * @var 	bool
	 * @access	private
	 * @since	0.1.0
	 */
	private $debug_mode;

	/**
	 * Asset Suffix
	 *
	 * @var 	string
	 * @access	private
	 * @since	0.1.0
	 */
	private $asset_suffix;

	/**
	 * Constructor.
	 *
	 * @since	0.1.0
	 */
	public function __construct() {
		$this->debug_mode   = false;
		$this->asset_suffix = '.min';

		// Check if WordPress is in debug mode, if it is then we do not want to
		// load the minified assets.
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			$this->debug_mode   = true;
			$this->asset_suffix = '';
		}
	}

	/**
	 * Go.
	 *
	 * @since	0.1.0
	 */
	public function run() {
		add_action( 'wp_enqueue_scripts', array( $this, 'public_enqueue_scripts' ), 10 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 10 );
		add_action( 'customize_preview_init', array( $this, 'customize_preview_init' ), 10 );
	}

	/**
	 * Enqueue Public Scripts.
	 *
	 * @since	0.1.0
	 */
	public function public_enqueue_scripts() {

		$do_public_enqueue                 = apply_filters( MKDO_BINDER_PREFIX . '_do_public_enqueue', true );
		$do_public_css_enqueue             = apply_filters( MKDO_BINDER_PREFIX . '_do_public_css_enqueue', true );
		$do_public_js_enqueue              = apply_filters( MKDO_BINDER_PREFIX . '_do_public_js_enqueue', false );
		$do_vendor_font_awsome_css_enqueue = apply_filters( MKDO_BINDER_PREFIX . '_do_vendor_font_awsome_css_enqueue', true );

		/* CSS */
		if ( $do_public_enqueue && $do_vendor_font_awsome_css_enqueue ) {
			$font_awesome_css_url  = plugins_url( 'vendor/font-awesome/css/font-awesome.min.css', MKDO_BINDER_ROOT );
			$font_awesome_css_path = dirname( MKDO_BINDER_ROOT ) . '/vendor/font-awesome/css/font-awesome.min.css';
			wp_enqueue_style(
				MKDO_BINDER_PREFIX . '-vendor-font-awesome',
				$font_awesome_css_url,
				array(),
				filemtime( $font_awesome_css_path )
			);
		}
		if ( $do_public_enqueue && $do_public_css_enqueue ) {
			$plugin_css_url  = plugins_url( 'assets/css/plugin' . $this->asset_suffix . '.css', MKDO_BINDER_ROOT );
			$plugin_css_path = dirname( MKDO_BINDER_ROOT ) . '/assets/css/plugin' . $this->asset_suffix . '.css';
			wp_enqueue_style(
				MKDO_BINDER_PREFIX . '-plugin-css',
				$plugin_css_url,
				array(),
				filemtime( $plugin_css_path )
			);
		}

		/* JS */
		if ( $do_public_enqueue && $do_public_js_enqueue ) {
			$plugin_js_url   = plugins_url( 'assets/js/plugin' . $this->asset_suffix . '.js', MKDO_BINDER_ROOT );
			$plugin_js_path  = dirname( MKDO_BINDER_ROOT ) . '/assets/js/plugin' . $this->asset_suffix . '.js';
			wp_enqueue_script(
				MKDO_BINDER_PREFIX . '-plugin-js',
				$plugin_js_url,
				array( 'jquery' ),
				filemtime( $plugin_js_path ),
				true
			);
		}
	}

	/**
	 * Enqueue Admin Scripts.
	 *
	 * @since	0.1.0
	 */
	public function admin_enqueue_scripts() {

		$do_admin_enqueue                        = apply_filters( MKDO_BINDER_PREFIX . '_do_admin_enqueue', true );
		$do_admin_css_enqueue                    = apply_filters( MKDO_BINDER_PREFIX . '_do_admin_css_enqueue', true );
		$do_admin_editor_css_enqueue             = apply_filters( MKDO_BINDER_PREFIX . '_do_admin_editor_css_enqueue', true );
		$do_admin_js_enqueue                     = apply_filters( MKDO_BINDER_PREFIX . '_do_admin_js_enqueue', true );
		$do_admin_vendor_font_awsome_css_enqueue = apply_filters( MKDO_BINDER_PREFIX . '_do_admin_vendor_font_awsome_css_enqueue', true );
		$do_admin_vendor_select2_css_enqueue     = apply_filters( MKDO_BINDER_PREFIX . '_do_admin_vendor_select2_css_enqueue', true );
		$do_admin_vendor_select2_js_enqueue      = apply_filters( MKDO_BINDER_PREFIX . '_do_admin_vendor_select2_js_enqueue', true );

		// Main JS file dependencies.
		$admin_enqueue_dependencies = apply_filters(
			MKDO_BINDER_PREFIX . '_admin_enqueue_dependencies',
			array(
				'jquery',
				MKDO_BINDER_PREFIX . '-vendor-select2',
				'shortcode-ui',
			)
		);

		/* CSS */
		if ( $do_admin_enqueue && $do_admin_vendor_font_awsome_css_enqueue ) {
			$font_awesome_css_url  = plugins_url( 'vendor/font-awesome/css/font-awesome.min.css', MKDO_BINDER_ROOT );
			$font_awesome_css_path = dirname( MKDO_BINDER_ROOT ) . '/vendor/font-awesome/css/font-awesome.min.css';
			wp_enqueue_style(
				MKDO_BINDER_PREFIX . '-vendor-font-awesome',
				$font_awesome_css_url,
				array(),
				filemtime( $font_awesome_css_path )
			);
		}
		if ( $do_admin_enqueue && $do_admin_vendor_select2_css_enqueue ) {
			$select2_css_url  = plugins_url( 'vendor/select2/dist/css/select2.min.css', MKDO_BINDER_ROOT );
			$select2_css_path = dirname( MKDO_BINDER_ROOT ) . '/vendor/select2/dist/css/select2.min.css';
			wp_enqueue_style(
				MKDO_BINDER_PREFIX . '-vendor-select2',
				$select2_css_url,
				array(),
				filemtime( $select2_css_path )
			);
		}
		if ( $do_admin_enqueue && $do_admin_css_enqueue ) {
			$plugin_css_url  = plugins_url( 'assets/css/plugin-admin' . $this->asset_suffix . '.css', MKDO_BINDER_ROOT );
			$plugin_css_path = dirname( MKDO_BINDER_ROOT ) . '/assets/css/plugin-admin' . $this->asset_suffix . '.css';
			wp_enqueue_style(
				MKDO_BINDER_PREFIX . '-plugin-admin-css',
				$plugin_css_url,
				array(),
				filemtime( $plugin_css_path )
			);
		}

		/* Editor CSS */
		if ( $do_admin_enqueue && $do_admin_editor_css_enqueue ) {
			$editor_css_url  = plugins_url( 'assets/css/plugin-admin-editor' . $this->asset_suffix . '.css', MKDO_BINDER_ROOT );
			$editor_css_path = dirname( MKDO_BINDER_ROOT ) . '/assets/css/plugin-admin-editor' . $this->asset_suffix . '.css';
			add_editor_style( $editor_css_url . '?v=' . $editor_css_path );
		}

		/* JS */
		if ( $do_admin_enqueue && $do_admin_vendor_select2_js_enqueue ) {
			$select2_js_url  = plugins_url( 'vendor/select2/dist/js/select2.full.min.js', MKDO_BINDER_ROOT );
			$select2_js_path = dirname( MKDO_BINDER_ROOT ) . '/vendor/select2/dist/js/select2.full.min.js';
			wp_enqueue_script(
				MKDO_BINDER_PREFIX . '-vendor-select2',
				$select2_js_url,
				array( 'jquery' ),
				filemtime( $select2_js_path ),
				true
			);
		}
		if ( $do_admin_enqueue && $do_admin_js_enqueue ) {
			$plugin_js_url   = plugins_url( 'assets/js/plugin-admin' . $this->asset_suffix . '.js', MKDO_BINDER_ROOT );
			$plugin_js_path  = dirname( MKDO_BINDER_ROOT ) . '/assets/js/plugin-admin' . $this->asset_suffix . '.js';
			wp_enqueue_script(
				MKDO_BINDER_PREFIX . '-plugin-admin-js',
				$plugin_js_url,
				$admin_enqueue_dependencies,
				filemtime( $plugin_js_path ),
				true
			);
			wp_localize_script(
				MKDO_BINDER_PREFIX . '-plugin-admin-js',
				'binder_admin',
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
				)
			);
		}
	}

	/**
	 * Enqueue live preview JS handlers.
	 *
	 * @since	0.1.0
	 */
	public function customize_preview_init() {

		$do_customizer_enqueue = apply_filters( MKDO_BINDER_PREFIX . '_do_customizer_enqueue', false );

		if ( $do_customizer_enqueue ) {
			$customizer_js_url  = plugins_url( 'assets/js/customizer' . $this->asset_suffix . '.js', MKDO_BINDER_ROOT );
			$customizer_js_path = dirname( MKDO_BINDER_ROOT ) . '/assets/js/customizer.js';
			wp_enqueue_script(
				MKDO_BINDER_PREFIX . '-customizer',
				$customizer_js_url,
				array( 'customize-preview' ),
				filemtime( $customizer_js_path ),
				true
			);
		}
	}
}
