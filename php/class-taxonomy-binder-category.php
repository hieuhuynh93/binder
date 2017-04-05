<?php
/**
 * Class Taxonomy_Binder_Category
 *
 * @package mkdo\binder
 */

namespace mkdo\binder;

/**
 * Register Taxonomy for Document Post Type
 */
class Taxonomy_Binder_Category {

	/**
	 * Constructor
	 */
	function __construct() {
	}


	/**
	 * Do Work
	 */
	public function run() {
		add_action( 'init', array( $this, 'register_taxonomy' ) );
		add_action( 'wp', array( $this, 'template_redirect' ) );
	}

	/**
	 * Register Taxonomy
	 */
	public function register_taxonomy() {

		$labels = array(
			'name'              => _x( 'Document Categories', 'Taxonomy General Name', 'binder' ),
			'singular_name'     => _x( 'Document Category', 'Taxonomy General Name', 'binder' ),
			'menu_name'         => __( 'Document Categories', 'binder' ),
			'all_items'         => __( 'All Document Categories', 'binder' ),
			'edit_item'         => __( 'Edit Document Category', 'binder' ),
			'view_item'         => __( 'View Document Category', 'binder' ),
			'update_item'       => __( 'Update Document Category', 'binder' ),
			'add_new_item'      => __( 'Add New Document Category', 'binder' ),
			'new_item_name'     => __( 'New Document Category Name', 'binder' ),
			'parent_item'       => __( 'Parent Document Category', 'binder' ),
			'parent_item_colon' => __( 'Parent Document Category:', 'binder' ),
			'search_items'      => __( 'Search Document Categories', 'binder' ),
			'not_found'         => __( 'No categories found.', 'binder' ),
		);
		$args = array(
			'hierarchical'       => true,
			'labels'             => $labels,
			'show_ui'            => true,
			'show_admin_column'  => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => '/document/category' ),
			'public'             => true,
			'publicly_queryable' => true,
		);

		register_taxonomy( 'binder_category', array( 'binder' ), $args );
	}

	/**
	 * Because we share the path with the document single, we need to make
	 * sure WP can find the archive
	 */
	function template_redirect() {

		global $post, $wp_query;

		$url = $_SERVER['REQUEST_URI'];

		if ( is_404() && stripos( $url, '/document/category/' ) === 0 ) {
	        $slug = $wp_query->query_vars['name'];

			$paged = 1;
			$url = str_replace( '/document/category/', '', $url );
			$url = preg_replace('/\?.*/', '', $url);

			if ( stripos( $url, 'page/' ) === 0 ) {
				$url   = str_replace( 'page/', '', $url );
				$paged = preg_replace( '/[^0-9 ]/', '', $url );
				if ( ! is_numeric( $paged ) ) {
					$paged = 1;
				}
			}

	        if ( ! is_admin() && ! empty( $slug ) ) {
	            status_header( 200 );
	            $wp_query->is_404          = false;
	            $args                      = array();
	            $args['binder_category'] = $slug;
	            $args['paged']             = $paged;
	            $args['suppress_filters']  = false;

	            $GLOBALS['wp_query']     = $wp_query = new \WP_Query( $args );
	            $GLOBALS['wp_the_query'] = $GLOBALS['wp_query'];
	        }
		}
	}
}
