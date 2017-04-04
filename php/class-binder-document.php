<?php
/**
 * Class Binder_Document
 *
 * @package mkdo\binder
 */

namespace mkdo\binder;

/**
 * The main Document Class
 */
class Binder_Document {

	// TODO: Add declarations.
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

	private $table_name;

	/**
	 * Constructor
	 *
	 * @param integer $binder_id The ID of the document we wish to retrive.
	 */
	function __construct( $binder_id = 0 ) {
		global $wpdb, $post;

		$this->table_name = $wpdb->prefix . 'binder_history';

		// We did not request a document, so lets get the latest one associated
		// with this post.
		if ( 0 === $binder_id && is_object( $post ) ) {
			$this->get_document_by_post_id( $post->ID );
		} elseif( is_numeric( $binder_id ) ) {
			$this->get_document_by_binder_id( $binder_id );
		}
	}

	public function get_document_by_post_id( $post_id ) {
		global $wpdb;

		$sql = "SELECT * FROM $this->table_name
			WHERE post_id = '$post_id'
			AND status = 'latest';";

		$document = $wpdb->get_row( $sql );

		// Set the Binder_Document object.
		if ( is_object( $document ) ) {

			$this->upload_date = $document->upload_date;
			$this->post_id     = $document->post_id;
			$this->user_id     = $document->user_id;
			$this->type        = $document->type;
			$this->status      = $document->status;
			$this->version     = $document->version;
			$this->name        = $document->name;
			$this->description = $document->description;
			$this->folder      = $document->folder;
			$this->file        = $document->file;
			$this->size        = $document->size;
			$this->thumb       = $document->thumb;
			$this->mime_type   = $document->mime_type;
		}
	}

	public function get_document_by_binder_id( $binder_id ) {
		global $wpdb;

		$sql = "SELECT * FROM $this->table_name
			WHERE binder_id = '$binder_id'
			AND status = 'latest';";

		$document = $wpdb->get_row( $sql );

		// Set the Binder_Document object.
		if ( is_object( $document ) ) {

			$this->upload_date = $document->upload_date;
			$this->post_id     = $document->post_id;
			$this->user_id     = $document->user_id;
			$this->type        = $document->type;
			$this->status      = $document->status;
			$this->version     = $document->version;
			$this->name        = $document->name;
			$this->description = $document->description;
			$this->folder      = $document->folder;
			$this->file        = $document->file;
			$this->size        = $document->size;
			$this->thumb       = $document->thumb;
			$this->mime_type   = $document->mime_type;
		}
	}

	public function create() {

		global $wpdb, $post;

		// If we are creating the latest version, change the old version.
		if ( 'latest' === $this->status ) {
			$wpdb->update(
				$this->table_name,
				array(
					'status' => 'archive',
				),
				array(
					'post_id' => $post->ID,
					'status'  => 'latest',
				)
			);
		}

		// Create the document.
		$wpdb->insert(
			$this->table_name,
			array(
				'upload_date' => $this->upload_date,
				'post_id'     => $this->post_id,
				'user_id'     => $this->user_id,
				'type'        => $this->type,
				'status'      => $this->status,
				'version'     => $this->version,
				'name'        => $this->name,
				'description' => $this->description,
				'folder'      => $this->folder,
				'file'        => $this->file,
				'size'        => $this->size,
				'thumb'       => $this->thumb,
				'mime_type'   => $this->mime_type,
			)
		);
	}

	public function get_latest_version() {
		global $wpdb, $post;

		$sql = "SELECT version FROM $this->table_name
			WHERE post_id = '$post->ID'
			ORDER BY version DESC";

		$version = $wpdb->get_var( $sql );

		if ( empty( $version ) ) {
			return '0.0.0';
		}

		return $version;
	}

	public function get_history() {
		global $wpdb, $post;

		$sql = "SELECT * FROM $this->table_name
			WHERE post_id = '$post->ID'
			ORDER BY upload_date ASC";

		$history = $wpdb->get_results( $sql );

		if ( empty( $history ) ) {
			return array();
		}

		return $history;
	}
}
