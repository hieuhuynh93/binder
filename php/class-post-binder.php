<?php
/**
 * Class Post_Binder
 *
 * @package mkdo\binder
 */

namespace mkdo\binder;

/**
 * Register the Binder Document Post Type.
 */
class Post_Binder {

	/**
	 * The Post Type.
	 *
	 * @var 	string
	 * @access	private
	 * @since	0.1.0
	 */
	private $post_type;

	/**
	 * Constructor
	 */
	function __construct() {
		$this->post_type = 'binder';
	}

	/**
	 * Do Work
	 */
	public function run() {
		add_action( 'init', array( $this, 'register_post_type' ), 0 );
		add_filter( 'gettext', array( $this, 'title_placeholder' ) );
		add_action( 'post_type_link', array( $this, 'post_type_link' ), 1, 3 );
		// add_filter( 'get_the_archive_title', array( $this, 'get_the_archive_title' ), 10, 1 );
		add_action( 'before_delete_post', array( $this, 'before_delete_post' ), 9999 );
	}

	/**
	 * Register Post Type
	 */
	public function register_post_type() {

		$labels = array(
			'name'                  => _x( 'Document', 'Post Type General Name', 'binder' ),
			'singular_name'         => _x( 'Document', 'Post Type Singular Name', 'binder' ),
			'menu_name'             => __( 'Documents', 'binder' ),
			'name_admin_bar'        => __( 'Documents', 'binder' ),
			'archives'              => __( 'Document Archives', 'binder' ),
			'parent_item_colon'     => __( 'Parent Document:', 'binder' ),
			'all_items'             => __( 'All Documents', 'binder' ),
			'add_new_item'          => __( 'Add New Document', 'binder' ),
			'add_new'               => __( 'Add New', 'binder' ),
			'new_item'              => __( 'New Document', 'binder' ),
			'edit_item'             => __( 'Edit Document', 'binder' ),
			'update_item'           => __( 'Update Document', 'binder' ),
			'view_item'             => __( 'View Document', 'binder' ),
			'search_items'          => __( 'Search Document', 'binder' ),
			'not_found'             => __( 'Not found', 'binder' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'binder' ),
			'featured_image'        => __( 'Featured Image', 'binder' ),
			'set_featured_image'    => __( 'Set featured image', 'binder' ),
			'remove_featured_image' => __( 'Remove featured image', 'binder' ),
			'use_featured_image'    => __( 'Use as featured image', 'binder' ),
			'insert_into_item'      => __( 'Insert into Document', 'binder' ),
			'uploaded_to_this_item' => __( 'Uploaded to this Document', 'binder' ),
			'items_list'            => __( 'Documents list', 'binder' ),
			'items_list_navigation' => __( 'Documents list navigation', 'binder' ),
			'filter_items_list'     => __( 'Filter Documents list', 'binder' ),
		);
		$args = array(
			'label'               => __( 'Document', 'binder' ),
			'description'         => __( 'Custom Post Type for Binder Documents', 'binder' ),
			'labels'              => $labels,
			'supports'            => array(
				'title',
				// 'editor',
				// 'author',
				'thumbnail',
				'excerpt',
				// 'trackbacks',
				// 'custom-fields',
				// 'comments',
				// 'revisions',
				// 'page-attributes',
				// 'post-formats',
			),
			'hierarchical'        => true,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 10,
			'menu_icon'           => 'dashicons-book-alt',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'show_in_rest'        => false,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
			'rewrite'             => array( 'slug' => _x( 'document', 'Documents URL', 'binder' ) ),
		);

		register_post_type( $this->post_type, $args );
	}

	/**
	 * Title Placeholder
	 *
	 * @param  string $input The placeholder text.
	 * @return string        The altered placeholder text.
	 */
	public function title_placeholder( $input ) {

		if ( is_admin() && 'Enter title here' === $input && $this->post_type === get_post_type( get_the_ID() ) ) {
			return __( 'Enter Document Title', 'binder' );
		}

		return $input;
	}

	/**
	 * Transform Post Type Link
	 *
	 * @param  string $link The original link.
	 * @param  object $post The post object.
	 * @return string       The transformed link
	 */
	public function post_type_link( $link, $post ) {

		$permalink_support = get_option( MKDO_BINDER_PREFIX . '_permalink_support', false );

		// Document
		//
		// Alter the permalink of the post type by changing the URL dynamically.
		if ( $this->post_type === $post->post_type && $permalink_support ) {

			// TODO:
			// - Do check to see if document permalink supported
			// - Add documentation and option to settings.
			$type = '';
			$types = wp_get_object_terms( $post->ID, 'binder_type', array( 'fields' => 'slugs' ) );
			if ( is_array( $types ) ) {
				$type = reset( $types );
				return rtrim( $link, '/' ) . '.' . $type;
			}
		}
		return $link;
	}

	/**
	 * Filter the Archive Title
	 *
	 * Will work with any archive title, it just needs filtering right.
	 * See https://developer.wordpress.org/reference/functions/get_the_archive_title/.
	 *
	 * @param  string $title The original title.
	 * @return string        The transformed title
	 */
	public function get_the_archive_title( $title ) {

	    if ( is_post_type_archive( $this->post_type ) ) {
			$title_prefix = esc_html__( 'Document', 'binder' );
	        $title        = sprintf( __( '%1$s: %2$s' ), $title_prefix, post_type_archive_title( '', false ) );
	    }

		return $title;
	}

	/**
	 * Delete all documents when deleting a Document
	 *
	 * @param  int $post_id The post ID.
	 */
	public function before_delete_post( $post_id ) {

		$document = Binder::get_latest_document_by_post_id( $post_id );
		$folder   = $document->folder;

		// Enabel the base to be filtered.
		$base     = apply_filters( MKDO_BINDER_PREFIX . '_document_base', apply_filters( MKDO_BINDER_PREFIX . '_document_base', WP_CONTENT_DIR . '/uploads/binder/' ) );
		$path     = $base . $folder;

		// Remove all document files.
		if ( ! empty( $folder ) && file_exists( $path ) ) {
			$iterator = new \RecursiveDirectoryIterator( $path, \RecursiveDirectoryIterator::SKIP_DOTS );
			$files = new \RecursiveIteratorIterator( $iterator, \RecursiveIteratorIterator::CHILD_FIRST );
			foreach ( $files as $file ) {
				if ( file_exists( $file->getRealPath() ) ) {
				    if ( $file->isDir() ) {
				        rmdir( $file->getRealPath() );
				    } else {
				        unlink( $file->getRealPath() );
				    }
				}
			}

			if ( file_exists( $path ) ) {
				rmdir( $path );
			}
		}

		// Remove the document.
		Binder::delete_document_history_by_post_id( $post_id );
	}
}
