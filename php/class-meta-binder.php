<?php
/**
 * Class Meta_Binder
 *
 * @package mkdo\binder
 */

namespace mkdo\binder;

/**
 * Register the Binder Meta Boxes
 */
class Meta_Binder {

	/**
	 * Constructor
	 */
	function __construct() {}

	/**
	 * Do Work
	 */
	public function run() {
		add_action( 'post_edit_form_tag', array( $this, 'post_edit_form_tag' ) );
		// add_action( 'cmb2_admin_init', array( $this, 'add_cmb2_meta_boxes' ), 0 );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 0 );
		add_action( 'save_post', array( $this, 'save_meta' ), 9999 );
		// add_action( 'before_delete_post', array( $this, 'before_delete_post' ), 9999 );
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
	 * Add CMB2 Meta Boxes
	 */
	public function add_cmb2_meta_boxes() {

		$types_list = array();
		$types      = get_terms(
			'binder_type',
			array(
			    'hide_empty' => false,
			)
		);

		if ( is_array( $types ) ) {
			foreach ( $types as $type ) {
				$type_icon = get_term_meta( $type->term_id, 'mkdo_binder_binder_type_icon', true );
				if ( is_array( $type_icon ) ) {
					$type_icon = $type_icon[0];
				}
				$types_list[ $type->slug ] = '<i class="fa fa-' . $type_icon . '"></i> - ' . $type->name;
			}
		}

		$cmb = new_cmb2_box(
			array(
				'id'            => 'document_meta',
				'title'         => __( 'Document Type', 'binder' ),
				'object_types'  => array(
					'document',
				),
				'context'       => 'side',
				'priority'      => 'default',
				'show_names'    => false,
			)
		);

		$field1 = $cmb->add_field( array(
			'id'               => MKDO_BINDER_PREFIX . '_type',
			'type'             => 'radio',
			'show_option_none' => false,
			'options'          => $types_list,
			'attributes'       => array(
				'readonly' => 'readonly',
				'disabled' => 'disabled',
			),
		) );
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

		add_meta_box(
			'binder_version_control',
			'Vesion Control',
			array( $this, 'binder_version_control' ),
			'binder',
			'normal',
			'high'
		);
	}

	/**
	 * Add Entry
	 */
	public function binder_add_entry() {

		$document        = new Binder_Document();
		$current_version = $document->get_latest_version();
		$current_version = explode( '.', $current_version );
		$count_version   = count( $current_version );
		if ( is_numeric( $current_version[ $count_version - 1  ] ) ) {
			$current_version[ $count_version - 1 ] = $current_version[ $count_version - 1 ] + 1;
			$current_version = implode( '.', $current_version );
		}
		?>
		<div class="mkdo_meta_box">
			<div class="mkdo_meta_box__region mkdo_meta_box__region--add-file">
				<p class="mkdo_meta_box__item binder__status">
					<label for="<?php echo esc_attr( MKDO_BINDER_PREFIX );?>_draft">
						<input id="<?php echo esc_attr( MKDO_BINDER_PREFIX );?>_draft" name="<?php echo esc_attr( MKDO_BINDER_PREFIX );?>_draft" type="checkbox" value="draft"/>
						<?php esc_html_e( 'Document is draft', 'binder' );?>
					</label>
				</p>
				<p class="mkdo_meta_box__item binder__version">
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
				</p>
				<p class="mkdo_meta_box__item binder__comment">
					<label for="<?php echo esc_attr( MKDO_BINDER_PREFIX );?>_description">
						<?php esc_html_e( 'Comment', 'binder' );?>
					</label>
					<br/>
					<textarea
						id="<?php echo esc_attr( MKDO_BINDER_PREFIX );?>_description"
						name="<?php echo esc_attr( MKDO_BINDER_PREFIX );?>_description"
					/></textarea>
				</p>
				<p class="mkdo_meta_box__item binder__file">
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
		wp_nonce_field( MKDO_BINDER_PREFIX . 'add_entry', MKDO_BINDER_PREFIX . 'add_entry_nonce' );
	}

	/**
	 * Version Control
	 */
	public function binder_version_control() {
		$document = new Binder_Document();
		$history  = $document->get_history();
		$base     = WP_CONTENT_DIR . '/uploads/documents/';
		?>
		<div class="mkdo_meta_box">
			<table class="binder mkdo_table">
				<tr>
					<th class="binder__preview"><?php esc_html_e( 'Preview', 'binder' );?></th>
					<th class="binder__status"><?php esc_html_e( 'Status', 'binder' );?></th>
					<th class="binder__version"><?php esc_html_e( 'Version', 'binder' );?></th>
					<th class="binder__comment"><?php esc_html_e( 'Comment', 'binder' );?></th>
					<th class="binder__file"><?php esc_html_e( 'File', 'binder' );?></th>
				</tr>
			<?php

			foreach ( $history as $version ) {
				$author = get_userdata( $version->user_id );
				if ( 'comment' !== $version->type ) {
					$current_version = esc_attr( $version->version );
				}
				?>
				<tr>
					<td class="binder__preview" data-th="<?php esc_html_e( 'Preview', 'binder' );?>">
						<?php
						if ( ! empty( $version->thumb ) ) {
							$uploads    = wp_upload_dir();
							$thumb      = str_replace( $base, '', $version->thumb );
							$image_path = esc_url( $uploads['baseurl'] . '/documents/' . $thumb );
							?>
							<img src="<?php echo esc_attr( $image_path );?>" alt="File Preview"/>
							<?php
						} else {
							echo '-';
						}
						?>
					</td>
					<td class="binder__status" data-th="<?php esc_html_e( 'Status', 'binder' );?>">
						<?php
						if ( 'comment' !== $version->type ) {
							?>
							<label for="<?php echo esc_attr( MKDO_BINDER_PREFIX );?>_file">
								<input type="radio" id="<?php echo esc_attr( MKDO_BINDER_PREFIX );?>_file" name="<?php echo esc_attr( MKDO_BINDER_PREFIX );?>_file[]" value="<?php echo esc_attr( $version->file );?>" <?php checked( $version->status, 'latest', true );?> />
								<?php esc_html_e( 'Latest', 'binder' );?>
							</label>
							<?php
						} else {
							esc_html_e( 'Comment', 'binder' );
						}
						?>
					</td>
					<td class="binder__version" data-th="<?php esc_html_e( 'Version', 'binder' );?>">
						<label class="hidden" for="<?php echo esc_attr( MKDO_BINDER_PREFIX );?>_version">
							<?php esc_html_e( 'Version', 'binder' );?>
						</label>
						<?php echo esc_html( $version->version );?>
					</td>
					<td class="binder__comment" data-th="Comment">
						<label class="hidden" for="<?php echo esc_attr( MKDO_BINDER_PREFIX );?>_description">
							<?php esc_html_e( 'Comment', 'binder' );?>
						</label>
						<?php
						echo esc_html( $version->description );
						if ( ! empty( $version->description ) ) {
							echo '<br/><br/>';
						}
						if ( 'comment' !== $version->type ) {
							?>
							<strong><?php esc_html_e( 'File:', 'binder' );?></strong> <?php echo esc_html( $version->name );?>
							<br/>
							<strong><?php esc_html_e( 'Uploaded:', 'binder' );?></strong> <?php echo esc_html( $version->upload_date );?>
							<br/>
							<?php
						} else {
							?>
							<strong><?php esc_html_e( 'Date:', 'binder' );?></strong> <?php echo esc_html( $version->upload_date );?>
							<br/>
							<?php
						}
						?>
						<strong><?php esc_html_e( 'By:', 'binder' );?></strong> <?php echo esc_html( $author->user_nicename );?>
					</td>
					<td class="binder__file" data-th="<?php esc_html_e( 'Remove', 'binder' );?>">
						<label for="<?php echo esc_attr( MKDO_BINDER_PREFIX );?>_remove">
							<input id="<?php echo esc_attr( MKDO_BINDER_PREFIX);?>_remove" name="<?php echo esc_attr( MKDO_BINDER_PREFIX );?>_remove[]" value="<?php echo esc_attr( $version->file );?>" type="checkbox"/>
							<?php esc_html_e( 'Remove', 'binder' );?>
						</label>
					</td>
				</tr>
				<?php
			}
			?>
			</table>
		</div>
		<?php
		wp_nonce_field( MKDO_BINDER_PREFIX . 'version_control', MKDO_BINDER_PREFIX . 'version_control_nonce' );
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

		// Save the meta.
		// if ( isset( $_POST[ MKDO_BINDER_PREFIX . '_type' ] ) ) {
		// 	wp_set_object_terms( $post_id, array( $_POST[ MKDO_BINDER_PREFIX . '_type' ] ), 'binder_type', false );
		// } else {
		// 	// CMB2 loosing the type if not set, lets remedy this until such a
		// 	// time that we replace CMB2.
		// 	$type = get_post_meta( $post_id, MKDO_BINDER_PREFIX . '_type', true );
		// 	if ( empty( $type ) ) {
		// 		$history = get_post_meta( $post_id, MKDO_BINDER_PREFIX . '_history', true );
		// 		$latest  = get_post_meta( $post_id, MKDO_BINDER_PREFIX . '_latest', true );
		// 		if ( ! is_array( $history ) ) {
		// 			$history = array();
		// 		}
		// 		foreach ( $history as $version ) {
		// 			if ( $latest === $version['file'] ) {
		// 				$type = $version['type'];
		// 				update_post_meta( $post_id, MKDO_BINDER_PREFIX . '_type', $type );
		// 				break;
		// 			}
		// 		}
		// 	}
		// }

		// Remove selected files.
		// if ( isset( $_POST[ MKDO_BINDER_PREFIX . '_remove' ] ) ) {
		// 	$remove_posts = $_POST[ MKDO_BINDER_PREFIX . '_remove' ];
		// 	if ( is_array( $remove_posts ) ) {
		// 		$history_new  = array();
		// 		$base         = WP_CONTENT_DIR . '/uploads/documents/';
		// 		$history      = get_post_meta( $post_id, MKDO_BINDER_PREFIX . '_history', true );
		// 		if ( ! is_array( $history ) ) {
		// 			$history = array();
		// 		}
		// 		foreach ( $history as $version ) {
		// 			if ( in_array( $version['file'], $remove_posts, true ) ) {
		// 				$file = $base . '/' . $version['folder'] . '/' . $version['file'];
		// 				if ( file_exists( $file ) ) {
		// 					unlink( $file );
		// 				}
		// 				if ( file_exists( $version['thumb'] ) ) {
		// 					unlink( $version['thumb'] );
		// 				}
		// 			} else {
		// 				$history_new[] = $version;
		// 			}
		// 		}
		// 		$history = $history_new;
		// 		update_post_meta( $post_id, MKDO_BINDER_PREFIX . '_history', $history );
		// 	}
		// }

		// Change latest file.
		// if ( isset( $_POST[ MKDO_BINDER_PREFIX . '_file' ] ) ) {
		// 	$latest_file = $_POST[ MKDO_BINDER_PREFIX . '_file' ];
		// 	if ( is_array( $latest_file ) ) {
		// 		$latest_file = $latest_file[0];
		// 	}
		// 	if ( ! empty( $latest_file ) ) {
		// 		$latest  = get_post_meta( $post_id, MKDO_BINDER_PREFIX . '_latest', true );
		// 		$history = get_post_meta( $post_id, MKDO_BINDER_PREFIX . '_history', true );
		// 		if ( ! is_array( $history ) ) {
		// 			$history = array();
		// 		}
		// 		if ( $latest_file !== $latest ) {
		// 			foreach ( $history as &$version ) {
		// 				if ( $latest_file === $version['file'] ) {
		// 					$version['status'] = 'latest';
		// 					$output            = '';
		// 					$base              = WP_CONTENT_DIR . '/uploads/documents/';
		// 					$path              = $base . $version['folder'];
		//
		// 					if ( 'pdf' === $version['type'] ) {
		// 						$a = new \PDF2Text();
		// 						$a->setFilename( $path . '/' . $version['file'] );
		// 						$a->decodePDF();
		// 						$output = $a->output();
		//
		// 					} else {
		// 						$converter = new \DocxConversion(  $path . '/' . $version['file'] , $version['type']  );
		// 						$output    = $converter->convertToText();
		// 					}
		//
		// 					if ( ! empty( $output ) ) {
		// 						$document               = get_post( $post_id );
		// 						$document->post_content = apply_filters( 'the_content', $output );
		// 						remove_action( 'save_post', array( $this, 'save_meta' ), 9999 );
		// 						wp_update_post( $document );
		// 						add_action( 'save_post', array( $this, 'save_meta' ), 9999 );
		// 					}
		//
		// 					update_post_meta( $post_id, MKDO_BINDER_PREFIX . '_size', $version['size'] );
		// 					update_post_meta( $post_id, MKDO_BINDER_PREFIX . '_thumb', $version['thumb'] );
		// 					update_post_meta( $post_id, MKDO_BINDER_PREFIX . '_latest', $version['file'] );
		// 					update_post_meta( $post_id, MKDO_BINDER_PREFIX . '_type', $version['type'] );
		// 					update_post_meta( $post_id, MKDO_BINDER_PREFIX . '_mime_type', $version['mime_type'] );
		// 					update_post_meta( $post_id, MKDO_BINDER_PREFIX . '_uploaded', $version['uploaded'] );
		// 					wp_set_object_terms( $post_id, array( $version['type'] ), 'binder_type', false );
		// 				} elseif ( $latest === $version['file'] ) {
		// 					$version['status'] = 'archive';
		// 				} else if ( 'latest' === $version['status'] ) {
		// 					$version['status'] = 'archive';
		// 				}
		// 			}
		// 			update_post_meta( $post_id, MKDO_BINDER_PREFIX . '_history', $history );
		// 		}
		// 	}
		// }

		if ( isset( $_POST[ MKDO_BINDER_PREFIX . 'add_entry_nonce' ] ) && wp_verify_nonce( $_POST[ MKDO_BINDER_PREFIX . 'add_entry_nonce' ], MKDO_BINDER_PREFIX . 'add_entry' ) ) {

			// Make sure the file array isn't empty.
		    if ( ! empty( $_FILES[ MKDO_BINDER_PREFIX . '_file_upload' ]['name'] ) ) {

				$document        = new Binder_Document();
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

				// If the history is empty, set it.
				if ( ! is_array( $history ) ) {
					$history = array();
				}

				// Grab the document details.
				$original_name = $_FILES[ MKDO_BINDER_PREFIX . '_file_upload' ]['name'];
				$size          = $_FILES[ MKDO_BINDER_PREFIX . '_file_upload' ]['size'];
				$type          = pathinfo( $original_name, PATHINFO_EXTENSION );
				$file_name     = Helper::create_guid();
				$uploads_dir   = wp_upload_dir();
				$base          = WP_CONTENT_DIR . '/uploads/documents/';
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
					if ( 'draft' !== $status || 1 === count( $history ) ) {
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

						wp_set_object_terms( $post_id, array( $type ), 'binder_type', false );
					}
					$document->create();
		        }
		    }
		}

		// $do_comment = apply_filters( 'mkdo_binder_add_history', true, $post_id );
		//
		// if ( $do_comment && isset( $_POST[ MKDO_BINDER_PREFIX . '_description' ] ) && ! empty( $_POST[ MKDO_BINDER_PREFIX . '_description' ] ) ) {
		// 	$description = esc_html( $_POST[ MKDO_BINDER_PREFIX . '_description' ] );
		// 	$history     = get_post_meta( $post_id, MKDO_BINDER_PREFIX . '_history', true );
		// 	$history[]   = array(
		// 		'author'      => get_current_user_id(),
		// 		'folder'      => '',
		// 		'file'        => Helper::create_guid(),
		// 		'name'        => '',
		// 		'size'        => '',
		// 		'description' => $description,
		// 		'type'        => 'comment',
		// 		'status'      => 'comment',
		// 		'version'     => '-',
		// 		'thumb'       => '',
		// 		'mime_type'   => '',
		// 		'upload_date' => date( 'Y-m-d H:i:s' ),
		// 	);
		// 	update_post_meta( $post_id, MKDO_BINDER_PREFIX . '_history', $history );
		// }
	}

	/**
	 * Delete all documents when deleting a Document
	 *
	 * @param  int $post_id The post ID.
	 */
	public function before_delete_post( $post_id ) {
		$folder  = get_post_meta( $post_id, MKDO_BINDER_PREFIX . '_folder', true );

		$base          = WP_CONTENT_DIR . '/uploads/documents/';
		$path          = $base . $folder;

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
	}
}
