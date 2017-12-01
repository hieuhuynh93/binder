<?php
/**
 * Class Load_Binder_Document
 *
 * @package mkdo\binder
 */

namespace mkdo\binder;

/**
 * Handle the Document
 */
class Load_Binder_Document {

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
		add_filter( 'user_has_cap', array( $this, 'user_has_cap' ),	0, 3 );
	}

	/**
	 * Do the Redirect
	 */
	public function routing() {

		global $wp_query, $post;

		// Get the page slug.
		$slug      = Helper::page_slug_from_url();
		$file_name = '';
		$path      = '';

		// If we are:
		//
		// - Not on the admin screen
		// - The post is a binder document
		// - The current page is a 404
		// - The document name contains a '.'
		if ( ! is_admin() && (
				(
					is_object( $post ) &&
					'binder' === $post->post_type
				) ||
				(
					is_404() ||
					'.' === substr( $slug, -1 )
				)
			)
		) {
			// Split the documetn by its suffix.
			// This should still work if there is no suffix.
			$name_parts = explode( '.', $slug );

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
							return;
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

						// Meta cap check.
						if ( $load_document && ! current_user_can( 'read_post', $document->ID ) ) {
							$load_document = false;
						}

						// Add filters to do extra permission checks.
						$load_document = apply_filters( MKDO_BINDER_PREFIX . '_load_document', $load_document, $document );

						// We can load the document, lets load it.
						if ( $load_document ) {

							$latest_document = Binder::get_latest_document_by_post_id( $document->ID );
							$file_name       = $latest_document->file;
							$folder          = $latest_document->folder;
							$type            = $latest_document->type;
							$mime_type       = $latest_document->mime_type;
							$base            = apply_filters( MKDO_BINDER_PREFIX . '_document_base', WP_CONTENT_DIR . '/uploads/binder/' );
							$path            = $base . $folder;

							// If the version is set, return that.
							if ( isset( $_GET['v'] ) ) {
								$version_document = Binder::get_document_by_version( $document->ID, esc_attr( $_GET['v'] ) );
								$file_name        = $version_document->file;
								$folder           = $version_document->folder;
								$type             = $version_document->type;
								$mime_type        = $version_document->mime_type;
							}

							// If the file is a URL redirect to that.
							if ( false !== filter_var( $file_name, FILTER_VALIDATE_URL ) ) {
								status_header( 200 );
								header( 'Location:' . esc_url( $file_name ) );
								exit;
							}

							$permalink_support = get_option( MKDO_BINDER_PREFIX . '_permalink_support', false );

							if ( ! $permalink_support && false === strpos( $slug, '.' . $type ) ) {
								$slug = $slug . '.' . $type;
							}

							if ( ! empty( $file_name ) && file_exists( $path . '/' . $file_name ) ) {
								status_header( 200 );
								header( 'Content-type:' . $mime_type );
								header( "Content-Disposition:attachment;filename='" . $slug . "'" );
								readfile( $path . '/' . $file_name );
								exit;
							}
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

	/**
	 * Allow logged out users to view documetns by default
	 *
	 * @see https://speakerdeck.com/johnbillion/a-deep-dive-into-the-roles-and-capabilities-api
	 *
	 * @param  array $user_caps     The capabilities of the user.
	 * @param  array $required_caps The required capabilities.
	 * @param  array $args          The requrested capabilities.
	 * @return array                The modified user capabilities.
	 */
	public function user_has_cap( $user_caps, $required_caps, $args ) {

		if ( 'read_post' === $args[0] ) {

			// Get the post.
			$document = get_post( $args[2] );

			// Allow logout users to view documents by default.
			if ( current_user_can( 'exist' ) && ! is_user_logged_in() && 'binder' === $document->post_type ) {
				$user_caps[ $required_caps[0] ] = true;
			}
		}
		return $user_caps;
	}
}
