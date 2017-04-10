<?php
/**
 * Class Meta_Binder_Add_Entry
 *
 * @package mkdo\binder
 */

namespace mkdo\binder;

/**
 * Register the Binder Add Entry Meta
 */
class Meta_Binder_Add_Entry {

	/**
	 * Constructor
	 */
	function __construct() {}

	/**
	 * Do Work
	 */
	public function run() {
		add_action( 'post_edit_form_tag', array( $this, 'post_edit_form_tag' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 0 );
		add_action( 'save_post', array( $this, 'save_meta' ), 9999 );
	}

	/**
	 * Update the form tag
	 */
	function post_edit_form_tag() {
		global $post;

		if ( is_object( $post ) && 'binder' === $post->post_type ) {
			echo ' enctype="multipart/form-data"';
		}
	}

	/**
	 * Add Meta Boxes
	 */
	public function add_meta_boxes() {

		add_meta_box(
			'binder_add_entry',
			'Add Entry',
			array( $this, 'binder_add_entry' ),
			'binder',
			'normal',
			'high'
		);
	}

	/**
	 * Add Entry
	 */
	public function binder_add_entry() {

		global $post;

		$binder          = new Binder();
		$document        = $binder->get_latest_document_by_post_id( $post->ID );
		$current_version = $binder->get_latest_version_by_post_id( $post->ID );
		$current_version = explode( '.', $current_version );
		$count_version   = count( $current_version );
		if ( is_numeric( $current_version[ $count_version - 1  ] ) ) {
			$current_version[ $count_version - 1 ] = $current_version[ $count_version - 1 ] + 1;
			$current_version = implode( '.', $current_version );
		}

		// Get binder history.
		$history = $binder->get_history_by_post_id( $post->ID );

		// Get the Type of Entry.
		$value = get_post_meta( $post->ID, esc_attr( MKDO_BINDER_PREFIX ) . '_entry_type', true );
		if ( empty( $value ) ) {
			$value = 'file';
		}

		// Setup entry types.
		$entry_options = apply_filters(
			MKDO_BINDER_PREFIX . '_entry_options',
			array(
				'comment' => 'Add Comment',
				'file'    => 'Upload File',
			)
		);
		?>
		<div class="meta-box">
			<div class="meta-box__region meta-box__region--entry-select">
				<div class="meta-box__item meta-box__item--entry-select">
					<p>
						<label>
							<?php esc_html_e( 'Type of Entry', 'binder' );?>
						</label>
					</p>
					<ul>
						<?php
						foreach ( $entry_options as $key => $option ) {
						?>
						<li>
							<label for="<?php echo esc_attr( MKDO_BINDER_PREFIX );?>_entry_type_<?php echo esc_attr( $key );?>">
								<input
									type="radio"
									id="<?php echo esc_attr( MKDO_BINDER_PREFIX );?>_entry_type_<?php echo esc_attr( $key );?>"
									name="<?php echo esc_attr( MKDO_BINDER_PREFIX );?>_entry_type"
									<?php checked( $value, $key, true );?>
								/>
								<?php echo esc_html( $option );?>
							</label>
						</li>
						<?php
						}
						?>
					</ul>
					<p class="description"><?php esc_html_e( 'Select the type of entry.', 'binder' );?></p>
				</div>
			</div>
			<div class="meta-box__region meta-box__region--add-file">
				<p>
					<span class="meta-box__item meta-box__item--version meta-box__item--version-select">
						<label for="<?php echo esc_attr( MKDO_BINDER_PREFIX );?>_version">
							<?php esc_html_e( 'Version', 'binder' );?>
						</label>
						<br/>
						<select
							id="<?php echo esc_attr( MKDO_BINDER_PREFIX );?>_version"
							name="<?php echo esc_attr( MKDO_BINDER_PREFIX );?>_version"
						>
						<?php
						foreach ( $history as $version ) {
							if ( 'latest' === $version->status || 'archive' === $version->status || 'draft' === $version->status ) {
							?>
							<option
								value="<?php echo esc_attr( $version->version );?>"
								<?php echo 'latest' === $version->status ? 'selected="selected"' : '';?>
							>
							<?php echo esc_html( $version->version );?>
							</option>
							<?php
							}
						}
						?>
						</select>
					</span>
					<span class="meta-box__item meta-box__item--version">
						<label for="<?php echo esc_attr( MKDO_BINDER_PREFIX );?>_version">
							<?php esc_html_e( 'Version', 'binder' );?>
						</label>
						<br/>
						<input
							type="text"
							id="<?php echo esc_attr( MKDO_BINDER_PREFIX );?>_version"
							name="<?php echo esc_attr( MKDO_BINDER_PREFIX );?>_version"
							pattern="^\d+(\.\d+)*$"
							value="<?php echo esc_attr( $current_version );?>"
						/>
					</span>
					<span class="meta-box__item meta-box__item--status">
						<label for="<?php echo esc_attr( MKDO_BINDER_PREFIX );?>_draft">
							<input id="<?php echo esc_attr( MKDO_BINDER_PREFIX );?>_draft" name="<?php echo esc_attr( MKDO_BINDER_PREFIX );?>_draft" type="checkbox" value="draft"/>
							<?php esc_html_e( 'Document is draft', 'binder' );?>
						</label>
					</span>
				</p>
				<div class="meta-box__item meta-box__item--comment">
					<p>
						<label for="<?php echo esc_attr( MKDO_BINDER_PREFIX );?>_description">
							<?php esc_html_e( 'Comment', 'binder' );?>
						</label>
						<br/>
						<!-- <textarea
							id="<?php echo esc_attr( MKDO_BINDER_PREFIX );?>_description"
							name="<?php echo esc_attr( MKDO_BINDER_PREFIX );?>_description"
						/></textarea> -->
						<?php
						wp_editor(
							'',
							esc_attr( MKDO_BINDER_PREFIX ) . '_description',
							array(
								'media_buttons' => false,
								'textarea_rows' => 5,
								'teeny'         => true,
								'quicktags'     => false,
							)
						);
						?>
					</p>
					<p class="description"><?php esc_html_e( 'Add a comment to this entry.', 'binder' );?></p>
				</div>
				<p class="meta-box__item meta-box__item--file">
					<label for="<?php echo esc_attr( MKDO_BINDER_PREFIX );?>_file_upload">
						<?php esc_html_e( 'Upload File', 'binder' );?>
					</label>
					<br/>
				    <input
						type="file"
						id="<?php echo esc_attr( MKDO_BINDER_PREFIX );?>_file_upload"
						name="<?php echo esc_attr( MKDO_BINDER_PREFIX );?>_file_upload"
						value="" size="25"
					/>
				</p>
			</div>
			<?php
				// Add additional Regions.
				do_action( MKDO_BINDER_PREFIX . '_add_entry_region' );
			?>
		</div>
		<?php
		wp_nonce_field( MKDO_BINDER_PREFIX . '_add_entry', MKDO_BINDER_PREFIX . '_add_entry_nonce' );
	}

	/**
	 * Save the Expiry Meta
	 *
	 * @param  int $post_id The Post ID.
	 */
	public function save_meta( $post_id ) {

		global $wp_roles, $wpdb, $_wp_additional_image_sizes;

		// If it is just a revision don't worry about it.
		if ( wp_is_post_revision( $post_id ) ) {
			return $post_id;
		}

		// Check it's not an auto save routine.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// Check that the current user has permission to edit the post.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		// Check the nonce is set.
		if ( ! isset( $_POST[ MKDO_BINDER_PREFIX . '_add_entry_nonce' ] ) || ! wp_verify_nonce( $_POST[ MKDO_BINDER_PREFIX . '_add_entry_nonce' ], MKDO_BINDER_PREFIX . '_add_entry' ) ) {
			return $post_id;
		}

		// Make sure the file array isn't empty.
	    if ( ! empty( $_FILES[ MKDO_BINDER_PREFIX . '_file_upload' ]['name'] ) ) {

			$binder          = new Binder();
			$document        = $binder->get_latest_document_by_post_id( $post_id );
			$description     = '';
			$status          = 'latest';
			$current_version = '0.0.1';
			$folder          = $document->folder;

			// Get the description.
			if ( isset( $_POST[ MKDO_BINDER_PREFIX . '_description' ] ) ) {
				$description = esc_html( $_POST[ MKDO_BINDER_PREFIX . '_description' ] );
			}

			// Get the draft status.
			if ( isset( $_POST[ MKDO_BINDER_PREFIX . '_draft' ] ) ) {
				$status = 'draft';
			}

			// Get the version.
			if ( isset( $_POST[ MKDO_BINDER_PREFIX . '_version' ] ) ) {
				$current_version = esc_html( $_POST[ MKDO_BINDER_PREFIX . '_version' ] );
			}

			// If the folder is empty, set it.
			if ( empty( $folder ) ) {
				$folder = Helper::create_guid();
				$document->folder = $folder;
			}

			// Grab the document details.
			$original_name = $_FILES[ MKDO_BINDER_PREFIX . '_file_upload' ]['name'];
			$size          = $_FILES[ MKDO_BINDER_PREFIX . '_file_upload' ]['size'];
			$type          = pathinfo( $original_name, PATHINFO_EXTENSION );
			$file_name     = Helper::create_guid();
			$uploads_dir   = wp_upload_dir();
			$base          = apply_filters( MKDO_BINDER_PREFIX . '_document_base', WP_CONTENT_DIR . '/uploads/binder/' );
			$path          = $base . $folder;

	        // Setup the array of supported file types.
	        $supported_types = array(
				'application/pdf',
				'application/msword',
				'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
				'application/vnd.ms-excel',
				'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
				'application/vnd.ms-powerpoint',
				'application/vnd.openxmlformats-officedocument.presentationml.presentation',
				'application/rtf',
				'text/csv',
				'application/vnd.oasis.opendocument.text',
			);

			// Filter the supported types.
			$supported_types = apply_filters( MKDO_BINDER_PREFIX . '_supported_mime_types', $supported_types );

	        // Get the file type of the upload.
	        $arr_file_type = wp_check_filetype( basename( $_FILES[ MKDO_BINDER_PREFIX . '_file_upload' ]['name'] ) );
	        $uploaded_type = $arr_file_type['type'];

	        // Check if the type is supported. If not, throw an error.
	        if ( in_array( $uploaded_type, $supported_types, true ) ) {

				// Create all the directories that we need.
				if ( ! is_dir( $base ) ) {
				    mkdir( $base );
				}
				if ( ! is_dir( $path ) ) {
				    mkdir( $path );
				}

				// Upload the file.
				$success = move_uploaded_file( $_FILES[ MKDO_BINDER_PREFIX . '_file_upload' ]['tmp_name'], $path . '/' . $file_name );

				// Generate an image for the document.
				$image_file = '';
				$image      = wp_get_image_editor( $path . '/' . $file_name, array( 'mime_type' => $uploaded_type ) );

				if ( ! is_wp_error( $image ) ) {

					$image->resize( 2048, 4096, false );
					$image_file = $image->generate_filename( '', $path . '/', 'jpg' );
					$image->save( $image_file, 'image/jpeg' );

					// TODO: Hooked in behaviour from GFS project, to be realised.
					//
					// do_action( MKDO_BINDER_PREFIX . '_generate_thumbnails', $path, $file_name, $uploaded_type, $_wp_additional_image_sizes, $post_id );
					//
					// $image_thumbs = array();
					// $image_thumbs = apply_filters( MKDO_BINDER_PREFIX . '_image_thumbs', $image_thumbs, $post_id );
				}

				$document->post_id     = $post_id;
				$document->upload_date = date( 'Y-m-d H:i:s' );
				$document->user_id     = get_current_user_id();
				$document->type        = $type;
				$document->status      = $status;
				$document->version     = $current_version;
				$document->name        = $original_name;
				$document->description = $description;
				$document->folder      = $folder;
				$document->file        = $file_name;
				$document->size        = $size;
				$document->thumb       = $image_file;
				$document->mime_type   = $uploaded_type;

				// Get the text from the file.
				if ( 'pdf' === $type ) {
					$a = new \PDF2Text();
					$a->setFilename( $path . '/' . $file_name );
					$a->decodePDF();
					$output = $a->output();
				} else {
					$converter = new \DocxConversion(  $path . '/' . $file_name, $type );
					$output    = $converter->convertToText();
				}

				// Update the post content.
				$binder  = new Binder();
				$history = $binder->get_history_by_post_id( $post_id );
				if ( 'draft' !== $status || 1 === count( $history ) ) {

					// Update the content.
					if ( ! empty( $output ) ) {
						$document_post               = get_post( $post_id );
						$document_post->post_content = apply_filters( 'the_content', $output );
						remove_action( 'save_post', array( $this, 'save_meta' ), 9999 );
						wp_update_post( $document_post );
						add_action( 'save_post', array( $this, 'save_meta' ), 9999 );
					}

					// TODO: Hooked in behaviour from GFS project, to be realised.
					//
					// foreach ( $image_thumbs as $size => $image_thumb_file ) {
					// 	update_post_meta( $post_id, MKDO_BINDER_PREFIX . '_thumb-' . $size, $image_thumb_file );
					// }

					// Update the type.
					wp_set_object_terms( $post_id, array( $type ), 'binder_type', false );
				}
				$binder->add_entry( $document, $post_id );
	        }
	    }

		// Other save actions.
		do_action( MKDO_BINDER_PREFIX . '_after_add_entry_save' );

		// Support for comments.
		if ( isset( $_POST[ MKDO_BINDER_PREFIX . '_description' ] ) && ! empty( $_POST[ MKDO_BINDER_PREFIX . '_description' ] ) ) {
			$description = esc_html( $_POST[ MKDO_BINDER_PREFIX . '_description' ] );
			$binder      = new Binder();
			$document    = new Binder_Document();
			$version     = $binder->get_latest_version_by_post_id( $post_id );

			if ( isset( $_POST[ MKDO_BINDER_PREFIX . '_version' ] ) ) {
				$version = esc_html( $_POST[ MKDO_BINDER_PREFIX . '_version' ] );
			}

			$document->post_id     = $post_id;
			$document->upload_date = date( 'Y-m-d H:i:s' );
			$document->user_id     = get_current_user_id();
			$document->type        = 'comment';
			$document->status      = 'comment';
			$document->version     = $version;
			$document->name        = '';
			$document->description = $description;
			$document->folder      = '';
			$document->file        = '';
			$document->size        = '';
			$document->thumb       = '';
			$document->mime_type   = '';

			$binder->add_entry( $document, $post_id );
		}
	}
}
