<?php
/**
 * Class Binder
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
			$binder = new Binder();
			$document = $binder->get_document( $binder_id );
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

/**
 * The main Class
 */
class Binder {

	/**
	 * The binder table name.
	 *
	 * @var 	bool
	 * @access	private
	 * @since	0.1.0
	 */
	private $table_name;

	/**
	 * Constructor
	 */
	function __construct() {
		global $wpdb, $post;

		// Setup the binder table name.
		// Note: We could probably think about making this a CONST.
		$this->table_name = $wpdb->prefix . 'binder_history';
	}

	/**
	 * Get the latest document by post ID.
	 *
	 * @param  int $post_id The Post ID.
	 * @return object       A Binder Document.
	 *
	 * @since  0.1.0
	 */
	public function get_latest_document_by_post_id( $post_id ) {
		global $wpdb;

		// Attempt to get the latest version.
		$sql = "SELECT * FROM $this->table_name
			WHERE post_id = '$post_id'
			AND status = 'latest';";

		$document = $wpdb->get_row( $sql );

		// Looks like we have no 'latest versions', so lets try the
		// latest archive.
		if ( ! is_object( $document ) ) {
			$sql = "SELECT * FROM $this->table_name
				WHERE post_id = '$post_id'
				AND status = 'archive'
				ORDER BY upload_date DESC
				LIMIT 1;";

			$document = $wpdb->get_row( $sql );

			// We still havent got a document, so lets get the latest
			// draft.
			if ( ! is_object( $document ) ) {
				$sql = "SELECT * FROM $this->table_name
					WHERE post_id = '$post_id'
					AND status = 'draft'
					ORDER BY upload_date DESC
					LIMIT 1;";

				$document = $wpdb->get_row( $sql );
			}
		}

		$binder_document = new Binder_Document();

		// Set the Binder object.
		if ( is_object( $document ) ) {

			$binder_document->upload_date = $document->upload_date;
			$binder_document->post_id     = $document->post_id;
			$binder_document->user_id     = $document->user_id;
			$binder_document->type        = $document->type;
			$binder_document->status      = $document->status;
			$binder_document->version     = $document->version;
			$binder_document->name        = $document->name;
			$binder_document->description = $document->description;
			$binder_document->folder      = $document->folder;
			$binder_document->file        = $document->file;
			$binder_document->size        = $document->size;
			$binder_document->thumb       = $document->thumb;
			$binder_document->mime_type   = $document->mime_type;
		}

		return $binder_document;
	}

	/**
	 * Get the document by version
	 *
	 * @param  int    $post_id The Post ID.
	 * @param  string $version The Version.
	 * @return object          A Binder Document.
	 *
	 * @since  0.1.0
	 */
	public function get_document_by_version( $post_id, $version ) {
		global $wpdb;

		// Attempt to get the version.
		$sql = "SELECT * FROM $this->table_name
			WHERE post_id = '$post_id'
			AND version = '$version';";

		$document = $wpdb->get_row( $sql );

		// Looks like we have no 'versions', so lets try the
		// latest.
		if ( ! is_object( $document ) ) {
			$document = $this->get_latest_document_by_post_id( $post_id );
		}

		$binder_document = new Binder_Document();

		// Set the Binder object.
		if ( is_object( $document ) ) {

			$binder_document->upload_date = $document->upload_date;
			$binder_document->post_id     = $document->post_id;
			$binder_document->user_id     = $document->user_id;
			$binder_document->type        = $document->type;
			$binder_document->status      = $document->status;
			$binder_document->version     = $document->version;
			$binder_document->name        = $document->name;
			$binder_document->description = $document->description;
			$binder_document->folder      = $document->folder;
			$binder_document->file        = $document->file;
			$binder_document->size        = $document->size;
			$binder_document->thumb       = $document->thumb;
			$binder_document->mime_type   = $document->mime_type;
		}

		return $binder_document;
	}

	/**
	 * Get Document
	 *
	 * @param  int $binder_id The Binder ID.
	 * @return object         A Binder Document.
	 *
	 * @since  0.1.0
	 */
	public function get_document( $binder_id ) {
		global $wpdb;

		$sql = "SELECT * FROM $this->table_name
			WHERE binder_id = '$binder_id'
			AND status = 'latest';";

		$document = $wpdb->get_row( $sql );

		$binder_document = new Binder_Document();

		// Set the Binder object.
		if ( is_object( $document ) ) {

			$binder_document->upload_date = $document->upload_date;
			$binder_document->post_id     = $document->post_id;
			$binder_document->user_id     = $document->user_id;
			$binder_document->type        = $document->type;
			$binder_document->status      = $document->status;
			$binder_document->version     = $document->version;
			$binder_document->name        = $document->name;
			$binder_document->description = $document->description;
			$binder_document->folder      = $document->folder;
			$binder_document->file        = $document->file;
			$binder_document->size        = $document->size;
			$binder_document->thumb       = $document->thumb;
			$binder_document->mime_type   = $document->mime_type;
		}

		return $binder_document;
	}

	/**
	 * Create Document
	 *
	 * @param  object $document The Document to Create.
	 * @param  int    $post_id  The Post ID.
	 *
	 * @since  0.1.0
	 */
	public function add_entry( Binder_Document $document, $post_id ) {

		global $wpdb;

		// If we are creating the latest version, change the old version.
		if ( 'latest' === $document->status ) {
			$wpdb->update(
				$this->table_name,
				array(
					'status' => 'archive',
				),
				array(
					'post_id' => $post_id,
					'status'  => 'latest',
				)
			);
		}

		// Calculate the document size.
		$size = $document->size;
		if ( is_numeric( $size ) ) {
			$size = Helper::format_bytes( $size );
		}

		// Create the document.
		$result = $wpdb->insert(
			$this->table_name,
			array(
				'upload_date' => $document->upload_date,
				'post_id'     => $document->post_id,
				'user_id'     => $document->user_id,
				'type'        => $document->type,
				'status'      => $document->status,
				'version'     => $document->version,
				'name'        => $document->name,
				'description' => $document->description,
				'folder'      => $document->folder,
				'file'        => $document->file,
				'size'        => $size,
				'thumb'       => $document->thumb,
				'mime_type'   => $document->mime_type,
			)
		);
	}

	/**
	 * Get Latest Version Number by Post ID
	 *
	 * @param int $post_id  The Post ID.
	 * @return string       The Version Number.
	 *
	 * @since  0.1.0
	 */
	public function get_latest_version_by_post_id( $post_id ) {
		global $wpdb;

		$sql = "SELECT version FROM $this->table_name
			WHERE post_id = '$post_id'
			ORDER BY version DESC";

		$version = $wpdb->get_var( $sql );

		if ( empty( $version ) ) {
			return '0.0.0';
		}

		return $version;
	}

	/**
	 * Get History by Post ID
	 *
	 * @param  int $post_id The Post ID.
	 * @return array        An array of Binder Documents.
	 *
	 * @since  0.1.0
	 */
	public function get_history_by_post_id( $post_id ) {
		global $wpdb;

		$sql = "SELECT * FROM $this->table_name
			WHERE post_id = '$post_id'
			ORDER BY upload_date ASC";

		$history           = $wpdb->get_results( $sql );
		$history_documents = array();

		if ( empty( $history ) ) {
			return array();
		} else {
			foreach ( $history as $document ) {
				$binder_document = new Binder_Document();
				$binder_document->binder_id   = $document->binder_id;
				$binder_document->upload_date = $document->upload_date;
				$binder_document->post_id     = $document->post_id;
				$binder_document->user_id     = $document->user_id;
				$binder_document->type        = $document->type;
				$binder_document->status      = $document->status;
				$binder_document->version     = $document->version;
				$binder_document->name        = $document->name;
				$binder_document->description = $document->description;
				$binder_document->folder      = $document->folder;
				$binder_document->file        = $document->file;
				$binder_document->size        = $document->size;
				$binder_document->thumb       = $document->thumb;
				$binder_document->mime_type   = $document->mime_type;
				$history_documents[] = $binder_document;
			}
		}

		return $history_documents;
	}

	/**
	 * Delete Document by Post ID
	 *
	 * @param  int $post_id The Post ID.
	 *
	 * @since  0.1.0
	 */
	public function delete_document_history_by_post_id( $post_id ) {
		global $wpdb;

		$wpdb->delete(
			$this->table_name,
			array(
				'post_id' => $post_id,
			)
		);
	}

	/**
	 * Delete Document by History Item
	 *
	 * @param int $binder_id The Binder ID.
	 *
	 * @since  0.1.0
	 */
	public function delete_document_history_item( $binder_id ) {
		global $wpdb;

		$wpdb->delete(
			$this->table_name,
			array(
				'binder_id' => $binder_id,
			)
		);
	}

	/**
	 * Update History Item to Latest
	 *
	 * @param int $binder_id The Binder ID.
	 *
	 * @since  0.1.0
	 */
	public function update_document_history_item_to_latest( $binder_id ) {

		global $wpdb;

		$sql = "SELECT post_id FROM $this->table_name
			WHERE binder_id = '$binder_id'";

		$post_id = $wpdb->get_var( $sql );

		// Update all the old latest to archive.
		$wpdb->update(
			$this->table_name,
			array(
				'status' => 'archive',
			),
			array(
				'post_id' => $post_id,
				'status'  => 'latest',
			)
		);

		// Update this one to latest.
		$wpdb->update(
			$this->table_name,
			array(
				'status' => 'latest',
			),
			array(
				'binder_id' => $binder_id,
			)
		);
	}
}
