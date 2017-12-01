# Binder

Document Management System (DMS) for WordPress.

## [Roadmap](#roadmap)
A bunch of features will be coming to this plugin, including:

- Release of Beta Version (fully migrate all legacy features)
- Tests and fallbacks for various server configs, including:
	- Thumbnail generator support
	- Extension support in permalinks
	- Get File Contents Support
	- File permissions
- Security on the binder folder
- Store documents as diffs
- Extension plugin - Aglets (Cards) for Binder
- Sort Version Control Column (JS Arrows)
- Better UI
- Regenerate Preview images
- Extension for DB Sync for documents
- Better Excerpt
- Auto Updater
- Better Refactor Meta, JS and CSS
- Clean orphan documents
- Clean orphan db entries
- On user delete, either remove a document, or reassign a document.

## [Basic Security](#security)

You can implement basic security for binder by hooking into `user_meta_cap` like so:

`function binder_user_has_cap( $user_caps, $required_caps, $args ) {

	if ( 'read_post' === $args[0] ) {

		// Get the post.
		$document = get_post( $args[2] );

		// If the user is logged out and they are trying to view a binder document.
		if ( current_user_can( 'exist' ) && ! is_user_logged_in() && 'binder' === $document->post_type ) {

			// Get meta from the document to indicate visibility.
			$visibility = get_post_meta( $document->ID, '_binder_document_visibility', true );

			// If the meta indicates the document is publicly visible continue.
			if ( 'public' === $visibility ) {
				$user_caps[ $required_caps[0] ] = true;
			} else {
				// If not redirect the user to the login screen.
				$user_caps[ $required_caps[0] ] = false;
				auth_redirect();
				exit;
			}
		}
	}
	return $user_caps;
}
add_filter( 'user_has_cap', 'binder_user_has_cap',	10, 3 );`

## [Credit](#credit)

Built using the [Ground Control](https://github.com/mwtsn/ground-control) plugin framework. A framework based on root composition principles, built by [Matt Watson](https://github.com/mwtsn/) and [Dave Green](https://github.com/davetgreen/), with thanks to [Make Do](https://www.makedo.net/).
