<?php
/**
 * Class Binder_Document
 *
 * @package mkdo\binder
 */

namespace mkdo\binder;

/**
 * The Binder Document
 */
class Binder_Document {

	/**
	 * The ID of the document.
	 *
	 * @var 	bool
	 * @access	private
	 * @since	0.1.0
	 */
	public $binder_id;

	/**
	 * The document upload date.
	 *
	 * @var 	bool
	 * @access	private
	 * @since	0.1.0
	 */
	public $upload_date;

	/**
	 * The post ID associated with the document.
	 *
	 * @var 	bool
	 * @access	private
	 * @since	0.1.0
	 */
	public $post_id;

	/**
	 * The user that uploaded the document.
	 *
	 * @var 	bool
	 * @access	private
	 * @since	0.1.0
	 */
	public $user_id;

	/**
	 * The extension of the document.
	 *
	 * @var 	bool
	 * @access	private
	 * @since	0.1.0
	 */
	public $type;

	/**
	 * The status of the document / type of entry.
	 *
	 * @var 	bool
	 * @access	private
	 * @since	0.1.0
	 */
	public $status;

	/**
	 * The version of the document.
	 *
	 * @var 	bool
	 * @access	private
	 * @since	0.1.0
	 */
	public $version;

	/**
	 * The original file name of the document.
	 *
	 * @var 	bool
	 * @access	private
	 * @since	0.1.0
	 */
	public $name;

	/**
	 * The document description / entry comment.
	 *
	 * @var 	bool
	 * @access	private
	 * @since	0.1.0
	 */
	public $description;

	/**
	 * The folder the document is stored in.
	 *
	 * @var 	bool
	 * @access	private
	 * @since	0.1.0
	 */
	public $folder;

	/**
	 * The document file name.
	 *
	 * @var 	bool
	 * @access	private
	 * @since	0.1.0
	 */
	public $file;

	/**
	 * The size of the document.
	 *
	 * @var 	bool
	 * @access	private
	 * @since	0.1.0
	 */
	public $size;

	/**
	 * The path to the auto-created thumbnail.
	 *
	 * @var 	bool
	 * @access	private
	 * @since	0.1.0
	 */
	public $thumb;

	/**
	 * The mime type of the document.
	 *
	 * @var 	bool
	 * @access	private
	 * @since	0.1.0
	 */
	public $mime_type;

	/**
	 * Constructor
	 */
	function __construct() {}

	/**
	 * Get the Thumbnail
	 *
	 * @param  int    $binder_id The document ID.
	 * @param  string $size      The Image Size.
	 * @return string            The image URL
	 *
	 * @since	0.1.0
	 */
	public function get_thumbnail( $binder_id, $size ) {
		$image_url = '';

		if ( $this->binder_id === $binder_id ) {
			$document = $this;
		} else {
			$document = Binder::get_document( $binder_id );
		}

		if ( ! empty( $document->thumb ) ) {
			$thumb  = '';
			$thumbs = array();

			if ( is_serialized( $document->thumb ) ) {
				$thumbs = unserialize( $document->thumb );
			} else {
				return $thumb;
			}

			if ( isset( $thumbs[ $size ] ) ) {
				$thumb = $thumbs[ $size ];
			} elseif ( isset( $thumbs['default'] ) ) {
				$thumb = $thumbs['default'];
			}
			if ( ! empty( $thumb ) ) {
				$base      = apply_filters( MKDO_BINDER_PREFIX . '_document_base', WP_CONTENT_DIR . '/uploads/binder/' );
				$base      = str_replace( WP_CONTENT_DIR, '', $base );
				$image_url = content_url() . $base . $document->folder . '/' . $thumb;
			}
		}

		return $image_url;
	}
}
