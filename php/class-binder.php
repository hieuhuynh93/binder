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
	 * Get the latest document by post ID.
	 *
	 * @param  int $post_id The Post ID.
	 * @return object       A Binder Document.
	 *
	 * @since  0.1.0
	 */
	public static function get_latest_document_by_post_id( $post_id ) {
		global $wpdb;

		// Get the table name.
		$table_name = MKDO_BINDER_HISTORY_TABLE;

		// Attempt to get the latest version.
		$sql = "SELECT * FROM $table_name
			WHERE post_id = '$post_id'
			AND status = 'latest';";

		$document = $wpdb->get_row( $sql );

		// Looks like we have no 'latest versions', so lets try the
		// latest archive.
		if ( ! is_object( $document ) ) {
			$sql = "SELECT * FROM $table_name
				WHERE post_id = '$post_id'
				AND status = 'archive'
				ORDER BY upload_date DESC
				LIMIT 1;";

			$document = $wpdb->get_row( $sql );

			// We still havent got a document, so lets get the latest
			// draft.
			if ( ! is_object( $document ) ) {
				$sql = "SELECT * FROM $table_name
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
	public static function get_document_by_version( $post_id, $version ) {
		global $wpdb;

		// Get the table name.
		$table_name = MKDO_BINDER_HISTORY_TABLE;

		// Attempt to get the version.
		$sql = "SELECT * FROM $table_name
			WHERE post_id = '$post_id'
			AND version = '$version';";

		$document = $wpdb->get_row( $sql );

		// Looks like we have no 'versions', so lets try the
		// latest.
		if ( ! is_object( $document ) ) {
			$document = self::get_latest_document_by_post_id( $post_id );
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
	public static function get_document( $binder_id ) {
		global $wpdb;

		// Get the table name.
		$table_name = MKDO_BINDER_HISTORY_TABLE;

		$sql = "SELECT * FROM $table_name;
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
	public static function add_entry( Binder_Document $document, $post_id ) {

		global $wpdb;

		// Get the table name.
		$table_name = MKDO_BINDER_HISTORY_TABLE;

		// If we are creating the latest version, change the old version.
		if ( 'latest' === $document->status ) {
			$wpdb->update(
				$table_name,
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
			$table_name,
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
	public static function get_latest_version_by_post_id( $post_id ) {
		global $wpdb;

		// Get the table name.
		$table_name = MKDO_BINDER_HISTORY_TABLE;

		$sql = "SELECT version FROM $table_name
			WHERE post_id = '$post_id'
			ORDER BY upload_date DESC";

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
	public static function get_history_by_post_id( $post_id ) {
		global $wpdb;

		// Get the table name.
		$table_name = MKDO_BINDER_HISTORY_TABLE;

		$sql = "SELECT * FROM $table_name
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
	public static function delete_document_history_by_post_id( $post_id ) {
		global $wpdb;

		// Get the table name.
		$table_name = MKDO_BINDER_HISTORY_TABLE;

		$wpdb->delete(
			$table_name,
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
	public static function delete_document_history_item( $binder_id ) {
		global $wpdb;

		// Get the table name.
		$table_name = MKDO_BINDER_HISTORY_TABLE;

		$wpdb->delete(
			$table_name,
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
	public static function update_document_history_item_to_latest( $binder_id ) {

		global $wpdb;

		// Get the table name.
		$table_name = MKDO_BINDER_HISTORY_TABLE;

		$sql = "SELECT post_id FROM $table_name
			WHERE binder_id = '$binder_id'";

		$post_id = $wpdb->get_var( $sql );

		// Update all the old latest to archive.
		$wpdb->update(
			$table_name,
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
			$table_name,
			array(
				'status' => 'latest',
			),
			array(
				'binder_id' => $binder_id,
			)
		);
	}
}
