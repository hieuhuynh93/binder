<?php
/**
 * Class Load_Binder
 *
 * @package mkdo\binder
 */

namespace mkdo\binder;

/**
 * Handle the Document
 */
class Load_Binder {

	/**
	 * Constructor
	 */
	function __construct() {}

	/**
	 * Do Work
	 */
	public function run() {
		// We need to hook in really late, as we want to do
		// permission checks first.
		add_action( 'wp', array( $this, 'routing' ), 9999 );
	}

	/**
	 * Do the Redirect
	 */
	public function routing() {

		global $wp_query, $post;

		$name = '';

		// Get the document name.
		if ( isset( $wp_query->query['name'] ) ) {
			$name = $wp_query->query['name'];
		}

		// If we are:
		//
		// - Not on the admin screen
		// - The current page is a 404
		// - The document name contains a '.'
		//
		// TODO:
		// - Need to factor in servers with no handling for document permalinks.
		if (
			! is_admin() &&
			(
				is_404() ||
				'.' === substr( $name, -1 )
			)
		) {
			// Split the documetn by its suffix.
			// This should still work if there is no suffix.
			$name_parts = explode( '.', $name );

			// If the name_parts is now an array.
			if ( count( $name_parts ) >= 1 ) {

				// Get the document object.
				$document = get_page_by_path( $name_parts[0], OBJECT, 'binder' );

				// If the documet is an object.
				if ( is_object( $document ) ) {

					$load_document = false;

					// Make sure the document is a binder document.
					if ( 'binder' === $document->post_type ) {

						// Post password checking.
						if (
							empty( $document->post_password ) ||
							(
								! empty( $document->post_password ) &&
								false === post_password_required( $document )
							)
					 	) {
							// If the post is passworded, and the password has not been entered,
							// don't load it this time.
							$load_document = true;
						} elseif (
							! empty( $document->post_password ) &&
							true === post_password_required( $document )
						) {
							// Redirect to the password page if the document is
							// passworded.
							$args = array(
		                        'post_type'        => 'document',
		                        'p'                => $document->ID,
		                        'suppress_filters' => false
		                    );

							// Setup everything that we need to load the default
							// template.
							$GLOBALS['post']         = $document;
							$GLOBALS['post_id']      = $document->ID;
							$GLOBALS['the_post']     = $document;
		                    $GLOBALS['wp_query']     = $wp_query = new \WP_Query( $args );
		                    $GLOBALS['wp_the_query'] = $GLOBALS['wp_query'];
						}

						// Check if this is a private document, and if we are the
						// owner (or have permissions to view) if so.
						if (
							'private' === $document->post_status &&
							! is_user_logged_in()
						) {
							$load_document = false;
						} elseif (
							'private' === $document->post_status &&
							is_user_logged_in() &&
							! (
								current_user_can( 'read_private_posts' ) ||
								current_user_can( 'read', $document->ID ) ||
								$document->post_author === get_current_user_id()
							)
						) {
							$load_document = false;
						}

						// Add filters to do extra permission checks.
						$load_document = apply_filters( MKDO_BINDER_PREFIX . '_load_document', $load_document, $document );

						// We can load the document, lets load it.
						if ( $load_document ) {
							$file_name      = get_post_meta( $document->ID, MKDO_BINDER_PREFIX . '_latest', true );
							$folder         = get_post_meta( $document->ID, MKDO_BINDER_PREFIX . '_folder', true );
							$type           = get_post_meta( $document->ID, MKDO_BINDER_PREFIX . '_type', true );
							$mime_type      = get_post_meta( $document->ID, MKDO_BINDER_PREFIX . '_mime_type', true );
							$base           = apply_filters( MKDO_BINDER_PREFIX . '_document_base', WP_CONTENT_DIR . '/uploads/binder/' );
							$path           = $base . $folder;

							// If the version is set, return that.
							if ( isset( $_GET['v'] ) ) {
								$get_version = esc_attr( $_GET['v'] );
								$history = get_post_meta( $document->ID, MKDO_BINDER_PREFIX . '_history', true );
								if ( ! is_array( $history ) ) {
									$history = array();
								}
								foreach ( $history as $version ) {
									if ( $get_version === $version['version'] ) {
										$file_name = esc_attr( $version['file'] );
										$type      = esc_attr( $version['type'] );
										$mime_type = esc_attr( $version['mime_type'] );
										break;
									}
								}
							}

							// TODO:
							// If $name does not have a suffix, we will need to add one here.
							if ( ! empty( $file_name ) && file_exists( $path . '/' . $file_name ) ) {
								status_header( 200 );
								header( 'Content-type:' . $mime_type );
								header( "Content-Disposition:attachment;filename='" . $name . "'" );
								readfile( $path . '/' . $file_name );
								exit;
							}

							// Action before we return a 404.
							do_action( MKDO_BINDER_PREFIX . '_before_404', $file_name, $path );

							// We cannot access the document. Lets 404 instead.
							status_header( 400 );
							$wp_query->is_404 = true;
							$GLOBALS['wp_query']     = $wp_query;
		                    $GLOBALS['wp_the_query'] = $GLOBALS['wp_query'];
						}
					}
				}
			}
		}
	}
}
