<?php
/**
 * Class Binder
 *
 * @package mkdo\binder
 */

namespace mkdo\binder;

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
	 *
	 * @param integer $binder_id The ID of the document we wish to retrive.
	 */
	function __construct() {
		global $wpdb, $post;

		$this->table_name = $wpdb->prefix . 'binder_history';
	}

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

	public function create_document( Binder_Document $document, $post_id ) {

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

		// Create the document.
		$wpdb->insert(
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
				'size'        => $document->size,
				'thumb'       => $document->thumb,
				'mime_type'   => $document->mime_type,
			)
		);
	}

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

	public function get_history_by_post_id( $post_id ) {
		global $wpdb;

		$sql = "SELECT * FROM $this->table_name
			WHERE post_id = '$post_id'
			ORDER BY upload_date ASC";

		$history = $wpdb->get_results( $sql );

		if ( empty( $history ) ) {
			return array();
		}

		return $history;
	}

	public function delete_document_history_by_post_id( $post_id ) {
		global $wpdb;

		$wpdb->delete(
			$this->table_name,
			array(
				'post_id' => $post_id,
			)
		);
	}

	public function delete_document_history_item( $binder_id ) {
		global $wpdb;

		$wpdb->delete(
			$this->table_name,
			array(
				'binder_id' => $binder_id,
			)
		);
	}

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

/**
 * The Binder Document
 */
class Binder_Document {

	public $binder_id;
	public $upload_date;
	public $post_id;
	public $user_id;
	public $type;
	public $status;
	public $version;
	public $name;
	public $description;
	public $folder;
	public $file;
	public $size;
	public $thumb;
	public $mime_type;

	/**
	 * Constructor
	 */
	function __construct() {}
}
