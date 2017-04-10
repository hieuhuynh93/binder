<?php
/**
 * Class Shortcode_Binder_Document
 *
 * @package mkdo\binder
 */

namespace mkdo\binder;

/**
 * The Binder Document Shortcode
 */
class Shortcode_Binder_Document {

	/**
	 * Constructor
	 */
	function __construct() {}

	/**
	 * Do Work
	 */
	public function run() {
		add_action( 'init', array( $this, 'register_shortcode' ) );
		add_action( 'wp_ajax_nopriv_mkdo_binder_get_document_versions', array( $this, 'get_document_versions' ) );
		add_action( 'wp_ajax_mkdo_binder_get_document_versions', array( $this, 'get_document_versions' ) );
		add_filter( 'mce_external_plugins', array( $this, 'mce_external_plugins' ) );
		add_filter( 'mce_buttons', array( $this, 'mce_buttons' ) );
	}

	/**
	 * Register the shortcode and shortcake UI
	 */
	public function register_shortcode() {

		global $post;

		// Render the shortcode.
		add_shortcode( 'binder_document', array( $this, 'render_shortcode' ) );

		// If we are not on an admin screen, return.
		if ( ! is_admin() ) {
			return;
		}

		// If shortcake doesn't exist,
		// or if the shortcake chosen extension dosn't exists,
		// exit.
		if ( ! function_exists( 'shortcode_ui_register_for_shortcode' ) ) {
			return;
		}

		$documents_list = array();
		$version_list   = array(
			'latest' => esc_html__( 'Latest', 'binder' ),
		);

		$documents = get_posts(
			array(
				'posts_per_page' => -1, // Bad practice, but we need to find any document.
				'post_type'      => 'binder',
			)
		);

		$documents_list[] = esc_html__( 'Please select...', 'binder' );

		foreach ( $documents as $document ) {
			$documents_list[ $document->ID ] = $document->post_title;
		}

		if ( is_array( $documents ) && ! empty( $documents ) ) {
			$binder  = new Binder();
			$history = $binder->get_history_by_post_id( $documents[0]->ID );
			if ( ! is_array( $history ) ) {
				$history = array();
			}
			foreach ( $history as $version ) {
				$version_list[ esc_attr( $version->version ) ] = esc_html( $version->version );
			}
		}

		$fields = array(
			array(
				'description'  => esc_html__( 'Choose a document to insert into your content.', 'binder' ),
				'type'         => 'text',
				'meta'         => array(
					'style' => 'display:none;', // Hacked a way of just showing a description.
				),
			),
			array(
				'label'        => esc_html__( 'Alternative Text', 'binder' ),
				'description'  => esc_html__( 'Use an alternative title for your document.', 'binder' ),
				'attr'         => 'alternative_text',
				'type'         => 'text',
			),
			array(
				'label'        => esc_html__( 'Select Document', 'binder' ),
				'description'  => esc_html__( 'Choose a document.', 'binder' ),
				'attr'         => 'document',
				'type'         => 'select',
				'options'      => $documents_list,
				'multiple'     => false,
				'meta'         => array(
					'placeholder'                         => esc_html__( 'Select Document', 'binder' ),
					'data-placeholder'                    => esc_html__( 'Select Document', 'binder' ),
					'data-js-mkdo-binder-list-document'   => 'binder',
					'data-js-select2'                     => 'select2',
				),
			),
			array(
				'label'        => esc_html__( 'Version', 'binder' ),
				'description'  => esc_html__( 'Choose a document version (default will always display the latest).', 'binder' ),
				'attr'         => 'version',
				'type'         => 'select',
				'options'      => $version_list,
				'multiple'     => false,
				'meta'         => array(
					'data-js-mkdo-binder-list-version' => 'version',
				),
			),
			array(
				'label'        => esc_html__( 'Show File Size', 'binder' ),
				'description'  => esc_html__( 'Show the file size in the list.', 'binder' ),
				'attr'         => 'file_size',
				'type'         => 'checkbox',
				'options'      => array(
					'true',
					'false',
				),
			),
			array(
				'label'        => esc_html__( 'Show Date', 'binder' ),
				'description'  => esc_html__( 'Show the files upload date in the list.', 'binder' ),
				'attr'         => 'date',
				'type'         => 'checkbox',
				'options'      => array(
					'true',
				),
			),
			array(
				'label'        => esc_html__( 'Show Extension', 'binder' ),
				'description'  => esc_html__( 'Show the file extension in the list.', 'binder' ),
				'attr'         => 'extension',
				'type'         => 'checkbox',
				'options'      => array(
					'true',
				),
			),
			array(
				'label'        => esc_html__( 'Show Version', 'binder' ),
				'description'  => esc_html__( 'Show the file version number in the list.', 'binder' ),
				'attr'         => 'show_version',
				'type'         => 'checkbox',
				'options'      => array(
					'true',
				),
			),
			array(
				'label'        => esc_html__( 'Show Icon', 'binder' ),
				'description'  => esc_html__( 'Show the file icon in the list.', 'binder' ),
				'attr'         => 'icon',
				'type'         => 'checkbox',
				'options'      => array(
					'true',
				),
			),
			// array(
			// 	'label'        => esc_html__( 'Show Image (card only)', 'binder' ),
			// 	'description'  => esc_html__( 'Show the image when presenting the document list as a card list or grid.', 'binder' ),
			// 	'attr'         => 'image',
			// 	'type'         => 'checkbox',
			// 	'options'      => array(
			// 		'true',
			// 	),
			// ),
			// array(
			// 	'label'        => esc_html__( 'Show Document Reader', 'binder' ),
			// 	'description'  => esc_html__( 'If this is displayed as a link it will add the reader text and link inline, otherwise it will display under the card.', 'binder' ),
			// 	'attr'         => 'document_reader',
			// 	'type'         => 'checkbox',
			// 	'options'      => array(
			// 		'true',
			// 	),
			// ),
		);

		// Display Types filter.
		$display_types = apply_filters(
			MKDO_BINDER_PREFIX . '_shortcode_binder_document_display_types',
			array(
				'link' => esc_html__( 'Link', 'binder' ),
			)
		);

		// Option to add more display types.
		if ( is_array( $display_types ) && count( $display_types ) > 1 ) {
			$fields[] = array(
				'label'        => esc_html__( 'Display Type', 'binder' ),
				'description'  => esc_html__( 'Choose how the documents should be displayed.', 'binder' ),
				'attr'         => 'display_type',
				'type'         => 'radio',
				'options'      => $display_types,
				'value'        => 'link',
			);
		}

		// Fields filter.
		$fields = apply_filters( MKDO_BINDER_PREFIX . '_shortcode_binder_document_fields', $fields );

		// Register the UI for the Shortcode.
		// Supported field types: text, checkbox, textarea, radio, select, email, url, number, and date.
		shortcode_ui_register_for_shortcode(
			'binder_document',
			array(
				'label'         => esc_html__( 'Document', 'binder' ),
				'description'   => esc_html__( 'Use the options below to insert a document.', 'binder' ),
				'listItemImage' => 'dashicons-media-document',
				'post_type'     => apply_filters( MKDO_BINDER_PREFIX . '_shortcode_document_supported_post_types', get_post_types() ),
				'attrs'         => $fields,
			)
		);
	}

	/**
	 * Render the shortcode
	 *
	 * @param array  $attr     Attributes.
	 * @param string $content The content.
	 */
	public function render_shortcode( $attr, $content = '' ) {

		$defaults = apply_filters(
			MKDO_BINDER_PREFIX . '_shortcode_binder_document_attributes',
			array(
				'document'         => '',
				'alternative_text' => '',
				'version'          => 'latest',
				'display_type'     => 'link',
				'file_size'        => 'false',
				'date'             => 'false',
				'extension'        => 'false',
				'show_version'     => 'true',
				'icon'             => 'false',
				'image'            => 'false',
				'document_reader'  => 'false',
			)
		);

		$attr = wp_parse_args( $attr, $defaults );

		$document_post = null;

		if ( ! empty( $attr['document'] ) ) {
			$document_ids = esc_attr( $attr['document'] );
			$document_ids = explode( ',', $document_ids );
			$count        = count( $document_ids );
			$manual_posts = get_posts(
				array(
					'post_type'      => 'binder',
					'posts_per_page' => $count,
					'post__in'       => $document_ids,
				)
			);
			if ( is_array( $manual_posts ) && ! empty( $manual_posts ) ) {
				$document_post = $manual_posts[0];
			}
		}

		ob_start();

		// Render the default view type.
		if ( 'link' === $attr['display_type'] ) {
			include Helper::render_view( 'view-binder-document' );
		}

		// Render additional view types.
		do_action( MKDO_BINDER_PREFIX . '_shortcode_binder_render_views' );

		return ob_get_clean();
	}

	/**
	 * Ajax Function to get document versions
	 */
	public function get_document_versions() {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			$document_id = $_POST['document_id'];
			$document    = get_post( $document_id );
			$binder      = new Binder();
			$history     = $binder->get_history_by_post_id( $document_id );
			if ( ! is_array( $history ) ) {
				$history = array();
			}
			echo '<option value="latest">' . esc_html__( 'Latest', 'binder' ) . '</option>';
			foreach ( $history as $version ) {
				echo '<option value="' . esc_attr( $version->version ) . '">' . esc_html( $version->version ) . '</option>';
			}
		}
		die();
	}

	/**
	 * Add our custom TinyMCE plugin
	 *
	 * @param  array $plugin_array An array of plugins.
	 * @return array               An array of plugins
	 */
	public function mce_external_plugins( $plugin_array ) {
		if ( ! function_exists( 'shortcode_ui_register_for_shortcode' ) ) {
			return $plugin_array; // Exit if shortcake isnt available.
		}
		$plugin_array[ MKDO_BINDER_PREFIX . '_document' ] = plugins_url( 'assets/js/buttons.js?v=1.0.0', MKDO_BINDER_ROOT );
		return $plugin_array;
	}

	/**
	 * Add the custom button to the editor.
	 *
	 * @param  array $buttons An array of buttons.
	 * @return array          An array of buttons.
	 */
	public function mce_buttons( $buttons ) {
		if ( ! function_exists( 'shortcode_ui_register_for_shortcode' ) ) {
			return $buttons; // Exit if shortcake isnt available.
		}
		array_push( $buttons, MKDO_BINDER_PREFIX . '_document' );
		return $buttons;
	}
}
