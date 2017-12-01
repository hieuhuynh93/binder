<?php
/**
 * Class Shortcode_Binder_Document_List
 *
 * @package mkdo\binder
 */

namespace mkdo\binder;

/**
 * The Binder Document List Shortcode
 */
class Shortcode_Binder_Document_List {

	/**
	 * Constructor
	 */
	function __construct() {}

	/**
	 * Do Work
	 */
	public function run() {
		add_action( 'init', array( $this, 'register_shortcode' ), 20 );
	}

	/**
	 * Register the shortcode and shortcake UI
	 */
	public function register_shortcode() {

		// Render the shortcode.
		add_shortcode( 'binder_list', array( $this, 'render_shortcode' ) );

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

		$documents_list          = array();
		$document_tag_terms      = array();
		$document_category_terms = array();

		$fields   = array();
		$fields[] = array(
			'description'  => esc_html__( 'Use the options below to generate a list of documents. Each option you select will combine to produce a list with all of those options.', 'binder' ),
			'type'         => 'text',
			'meta'         => array(
				'style' => 'display:none;', // Hacked a way of just showing a description.
			),
		);
		$fields[] = array(
			'label'        => esc_html__( 'Search Terms', 'binder' ),
			'description'  => esc_html__( 'Create a list with a string of search terms.', 'binder' ),
			'attr'         => 'search_terms',
			'type'         => 'text',
		);

		$documents = get_posts(
			array(
				'posts_per_page' => -1, // Bad practice, but we need to find any document.
				'post_type'      => 'binder',
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);

		foreach ( $documents as $document ) {
			$documents_list[ $document->ID ] = $document->post_title;
		}

		$fields[] = array(
			'label'        => esc_html__( 'Select Documents', 'binder' ),
			'description'  => esc_html__( 'Create a list of documents by manually selecting them.', 'binder' ),
			'attr'         => 'documents',
			'type'         => 'select',
			'options'      => $documents_list,
			'multiple'     => true,
			'meta'         => array(
				'multiple'         => 'multiple',
				'placeholder'      => esc_html__( 'Select Documents', 'binder' ),
				'data-placeholder' => esc_html__( 'Select Documents', 'binder' ),
				'data-js-select2'  => 'select2',
			),
		);

		// List all taxonomies assocaited with Binder.
		$taxonomy_names = get_object_taxonomies( 'binder', 'objects' );
		foreach ( $taxonomy_names as $taxonomy ) {
			$taxonomy_terms_list = array();
			$taxonomy_terms      = get_terms(
				array(
					'taxonomy'   => $taxonomy->name,
					'hide_empty' => false,
				)
			);
			if ( ! is_wp_error( $taxonomy_terms ) && ! empty( $taxonomy_terms ) ) {
				foreach ( $taxonomy_terms as $term ) {
					$taxonomy_terms_list[ $term->term_id ] = $term->name;
				}
			} else {
				$taxonomy_terms_list[] = '';
			}

			$fields[] = array(
				'label'        => $taxonomy->label,
				'description'  => esc_html__( 'Choose terms to list documents by.', 'binder' ),
				'attr'         => $taxonomy->name,
				'type'         => 'select',
				'options'      => $taxonomy_terms_list,
				'multiple'     => true,
				'meta'         => array(
					'multiple'         => 'multiple',
					'placeholder'      => esc_html__( 'Select Terms', 'binder' ),
					'data-placeholder' => esc_html__( 'Select Terms', 'binder' ),
					'data-js-select2'  => 'select2',
				),
			);
		}

		$fields[] = array(
			'label'        => esc_html__( 'Combined or Filtered', 'binder' ),
			'description'  => esc_html__( 'Use the search criteria to perform a combined document search (e.g. document can be in category x or tag y),', 'binder' ) . '<br/>' . esc_html__( 'or a filtered search (e.g. document is in category x and tag y) ', 'binder' ),
			'attr'         => 'combined',
			'type'         => 'radio',
			'options'      => array(
				'combined' => esc_html__( 'Combined', 'binder' ),
				'filtered' => esc_html__( 'Filtered', 'binder' ),
			),
			'value' => 'combined',
		);
		$fields[] = array(
			'label'        => esc_html__( 'Show File Size', 'binder' ),
			'description'  => esc_html__( 'Show the file size in the list.', 'binder' ),
			'attr'         => 'file_size',
			'type'         => 'checkbox',
			'options'      => array(
				'true',
			),
		);
		$fields[] = array(
			'label'        => esc_html__( 'Show Date', 'binder' ),
			'description'  => esc_html__( 'Show the files upload date in the list.', 'binder' ),
			'attr'         => 'date',
			'type'         => 'checkbox',
			'options'      => array(
				'true',
			),
		);
		$fields[] = array(
			'label'        => esc_html__( 'Show Extension', 'binder' ),
			'description'  => esc_html__( 'Show the file extension in the list.', 'binder' ),
			'attr'         => 'extension',
			'type'         => 'checkbox',
			'options'      => array(
				'true',
			),
		);
		$fields[] = array(
			'label'        => esc_html__( 'Show Icon', 'binder' ),
			'description'  => esc_html__( 'Show the file icon in the list.', 'binder' ),
			'attr'         => 'icon',
			'type'         => 'checkbox',
			'options'      => array(
				'true',
			),
		);
		$fields[] = array(
			'label'        => esc_html__( 'Sort list by', 'binder' ),
			'description'  => esc_html__( 'Choose how you want to sort the list.', 'binder' ),
			'attr'         => 'sort_by',
			'type'         => 'radio',
			'options'      => array(
				'alphabet' => esc_html__( 'Alphabet', 'binder' ),
				'date'     => esc_html__( 'Date', 'binder' ),
				'size'     => esc_html__( 'Size', 'binder' ),
			),
			'value' => 'alphabet',
		);
		$fields[] = array(
			'label'        => esc_html__( 'Sort list order', 'binder' ),
			'description'  => esc_html__( 'Choose the order of the list sort.', 'binder' ),
			'attr'         => 'sort_order',
			'type'         => 'radio',
			'options'      => array(
				'ascending'  => esc_html__( 'Ascending', 'binder' ),
				'descending' => esc_html__( 'Descending', 'binder' ),
			),
			'value' => 'ascending',
		);

		// Display Types filter.
		$display_types = apply_filters(
			MKDO_BINDER_PREFIX . '_shortcode_binder_document_list_display_types',
			array(
				'link' => esc_html__( 'Unordered Link List', 'binder' ),
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
		$fields = apply_filters( MKDO_BINDER_PREFIX . '_shortcode_binder_document_list_fields', $fields );

		// Register the UI for the Shortcode.
		// Supported field types: text, checkbox, textarea, radio, select, email, url, number, and date.
		shortcode_ui_register_for_shortcode(
			'binder_list',
			array(
				'label'         => esc_html__( 'Document List', 'binder' ),
				'description'   => esc_html__( 'Use the options below to generate a list of documents.', 'binder' ),
				'listItemImage' => 'dashicons-list-view',
				'post_type'     => apply_filters( MKDO_BINDER_PREFIX . '_shortcode_document_list_supported_post_types', get_post_types() ),
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
			MKDO_BINDER_PREFIX . '_shortcode_document_list_attributes',
			array(
				'search_terms'    => '',
				'documents'       => '',
				'combined'        => 'combined',
				'display_type'    => 'list',
				'file_size'       => 'false',
				'date'            => 'false',
				'extension'       => 'false',
				'icon'            => 'false',
				'image'           => 'false',
				'document_reader' => 'false',
				'sort_by'         => 'alphabet',
				'sort_order'      => 'ascending',
			)
		);

		$attr = wp_parse_args( $attr, $defaults );

		$document_posts = array();
		$manual_posts   = array();
		$search_posts   = array();
		$tag_posts      = array();
		$category_posts = array();

		if ( ! empty( $attr['search_terms'] ) ) {
			$search_posts = get_posts(
				array(
					'post_type'        => 'binder',
					'posts_per_page'   => -1,
					's'                => esc_attr( $attr['search_terms'] ),
				)
			);
			$document_posts[] = $search_posts;
		}

		if ( ! empty( $attr['documents'] ) ) {
			$document_ids = esc_attr( $attr['documents'] );
			$document_ids = explode( ',', $document_ids );
			$count        = count( $document_ids );
			$manual_posts = get_posts(
				array(
					'post_type'      => 'binder',
					'posts_per_page' => $count,
					'post__in'       => $document_ids,
				)
			);
			$document_posts[] = $manual_posts;
		}

		$taxonomy_names = get_object_taxonomies( 'binder', 'objects' );
		foreach ( $taxonomy_names as $taxonomy ) {
			if ( ! empty( $attr[ $taxonomy->name ] ) ) {

				$term_ids  = esc_attr( $attr[ $taxonomy->name ] );
				$term_ids  = explode( ',', $term_ids );
				$term_posts = get_posts(
					array(
						'post_type'      => 'binder',
						'posts_per_page' => -1,
						'tax_query'      => array(
							array(
								'taxonomy' => $taxonomy->name,
								'field'    => 'id',
								'terms'    => $term_ids,
							),
						),
					)
				);
				$document_posts[] = $term_posts;
			}
		}

		// Filter for other list options.
		$document_posts = apply_filters( MKDO_BINDER_PREFIX . '_shortcode_document_list_include_posts', $document_posts, $attr );
		if ( 'combined' === $attr['combined'] && ! empty( $document_posts ) ) {
			// Merge all the posts into one array.
			$document_posts = call_user_func_array( 'array_merge', $document_posts );
			$document_posts = array_unique( $document_posts, SORT_REGULAR );
		} else {
			// Sort Posts that apprear in each array.
			$master     = array();
			$master_set = false;
			foreach ( $document_posts as $key => $array ) {
				if ( ! empty( $array ) && false === $master_set ) {
					$master     = $array;
					$master_set = true;
				} else if ( ! empty( $master ) && ! empty( $array ) && true === $master_set ) {
					$master = array_uintersect( $master, $array, function( $a, $b ) {
				        return $a->ID !== $b->ID;
				    } );
				}
			}
			$document_posts = $master;
		}

		ob_start();
		// Render the default view type.
		if ( 'link' === $attr['display_type'] ) {
			include Helper::render_view( 'view-binder-document-list' );
		}

		// Render additional view types.
		do_action( MKDO_BINDER_PREFIX . '_shortcode_binder_document_list_render_views', $attr, $document_posts );

		return ob_get_clean();
	}
}
