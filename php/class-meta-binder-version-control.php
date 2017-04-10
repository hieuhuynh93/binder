<?php
/**
 * Class Meta_Binder_Version_Control
 *
 * @package mkdo\binder
 */

namespace mkdo\binder;

/**
 * Register the Binder Version Control Meta
 */
class Meta_Binder_Version_Control {

	/**
	 * Constructor
	 */
	function __construct() {}

	/**
	 * Do Work
	 */
	public function run() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 0 );
		add_action( 'save_post', array( $this, 'save_meta' ), 9998 );
	}

	/**
	 * Add Meta Boxes
	 */
	public function add_meta_boxes() {

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
	 * Version Control
	 */
	public function binder_version_control() {

		global $post;

		$binder   = new Binder();
		$document = $binder->get_latest_document_by_post_id( $post->ID );
		$history  = $binder->get_history_by_post_id( $post->ID );
		$base     = apply_filters( MKDO_BINDER_PREFIX . '_document_base', WP_CONTENT_DIR . '/uploads/binder/' );
		?>
		<div class="meta-box">
			<table class="binder-history">
				<tr>
					<th class="binder-history__column binder-history__column--preview"><?php esc_html_e( 'Preview', 'binder' );?></th>
					<th class="binder-history__column binder-history__column--status"><?php esc_html_e( 'Status', 'binder' );?></th>
					<th class="binder-history__column binder-history__column--version"><?php esc_html_e( 'Version', 'binder' );?></th>
					<th class="binder-history__column binder-history__column--comment"><?php esc_html_e( 'Comment', 'binder' );?></th>
					<th class="binder-history__column binder-history__column--file"><?php esc_html_e( 'File', 'binder' );?></th>
				</tr>
			<?php

			foreach ( $history as $version ) {
				$author = get_userdata( $version->user_id );
				if ( 'comment' !== $version->type ) {
					$current_version = esc_attr( $version->version );
				}
				?>
				<tr>
					<td class="binder-history__column binder-history__column--preview" data-th="<?php esc_html_e( 'Preview', 'binder' );?>">
						<?php
						if ( ! empty( $version->thumb ) ) {
							$uploads    = wp_upload_dir();
							$thumb      = str_replace( $base, '', $version->thumb );
							$image_path = esc_url( $uploads['baseurl'] . '/binder/' . $thumb );
							?>
							<img src="<?php echo esc_attr( $image_path );?>" alt="File Preview"/>
							<?php
						} else {
							echo '-';
						}
						?>
					</td>
					<td class="binder-history__column binder-history__column--status" data-th="<?php esc_html_e( 'Status', 'binder' );?>">
						<?php
						if ( 'comment' !== $version->type ) {
							?>
							<label for="<?php echo esc_attr( MKDO_BINDER_PREFIX );?>_file">
								<input
									type="radio"
									id="<?php echo esc_attr( MKDO_BINDER_PREFIX );?>_file"
									name="<?php echo esc_attr( MKDO_BINDER_PREFIX );?>_file[]"
									value="<?php echo esc_attr( $version->binder_id );?>"
									<?php checked( $version->status, 'latest', true );?>
								/>
								<?php esc_html_e( 'Latest', 'binder' );?>
							</label>
							<?php
						} else {
							esc_html_e( 'Comment', 'binder' );
						}
						?>
					</td>
					<td class="binder-history__column binder-history__column--version" data-th="<?php esc_html_e( 'Version', 'binder' );?>">
						<label
							class="hidden"
							for="<?php echo esc_attr( MKDO_BINDER_PREFIX );?>_version"
						>
							<?php esc_html_e( 'Version', 'binder' );?>
						</label>
						<?php echo esc_html( $version->version );?>
					</td>
					<td class="binder-history__column binder-history__column--comment" data-th="Comment">
						<label
							class="hidden"
							for="<?php echo esc_attr( MKDO_BINDER_PREFIX );?>_description"
						>
							<?php esc_html_e( 'Comment', 'binder' );?>
						</label>
						<?php
						echo esc_html( $version->description );
						if ( ! empty( $version->description ) ) {
							echo '<br/><br/>';
						}
						if ( 'comment' !== $version->type ) {
							?>
							<strong>
								<?php esc_html_e( 'File:', 'binder' );?>
							</strong>
							<?php echo esc_html( $version->name );?>
							<br/>
							<strong>
								<?php esc_html_e( 'Uploaded:', 'binder' );?>
							</strong>
							<?php echo esc_html( $version->upload_date );?>
							<br/>
							<?php
						} else {
							?>
							<strong>
								<?php esc_html_e( 'Date:', 'binder' );?>
							</strong>
							<?php echo esc_html( $version->upload_date );?>
							<br/>
							<?php
						}
						?>
						<strong>
							<?php esc_html_e( 'By:', 'binder' );?>
						</strong>
						<?php echo esc_html( $author->user_nicename );?>
					</td>
					<td class="binder-history__column binder-history__column--file" data-th="<?php esc_html_e( 'Remove', 'binder' );?>">
						<label for="<?php echo esc_attr( MKDO_BINDER_PREFIX );?>_remove">
							<input
								id="<?php echo esc_attr( MKDO_BINDER_PREFIX);?>_remove"
								name="<?php echo esc_attr( MKDO_BINDER_PREFIX );?>_remove[]"
								value="<?php echo esc_attr( $version->binder_id );?>"
								type="checkbox"
							/>
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
		wp_nonce_field( MKDO_BINDER_PREFIX . '_version_control', MKDO_BINDER_PREFIX . '_version_control_nonce' );
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

		if ( ! isset( $_POST[ MKDO_BINDER_PREFIX . '_version_control_nonce' ] ) || ! wp_verify_nonce( $_POST[ MKDO_BINDER_PREFIX . '_version_control_nonce' ], MKDO_BINDER_PREFIX . '_version_control' ) ) {
			return $post_id;
		}


		// Remove selected files.
		if ( isset( $_POST[ MKDO_BINDER_PREFIX . '_remove' ] ) ) {
			$binder    = new Binder();
			$documents = $_POST[ MKDO_BINDER_PREFIX . '_remove' ];
			if ( is_array( $documents ) ) {
				foreach ( $documents as $binder_id ) {
					$binder->delete_document_history_item( $binder_id );
				}
			}
		}

		// Change latest file.
		if ( isset( $_POST[ MKDO_BINDER_PREFIX . '_file' ] ) ) {
			$latest_file = $_POST[ MKDO_BINDER_PREFIX . '_file' ];
			if ( is_array( $latest_file ) ) {
				$latest_file = $latest_file[0];
			}
			if ( ! empty( $latest_file ) ) {
				$binder = new Binder();

				// Update the history.
				$binder->update_document_history_item_to_latest( $latest_file );

				// Get the document.
				$document = $binder->get_document( $latest_file );
				$base     = apply_filters( MKDO_BINDER_PREFIX . '_document_base', WP_CONTENT_DIR . '/uploads/binder/' );
				$path     = $base . $document->folder;

				// Get the content.
				if ( 'pdf' === $document->type ) {
					$a = new \PDF2Text();
					$a->setFilename( $path . '/' . $document->file );
					$a->decodePDF();
					$output = $a->output();
				} else {
					$converter = new \DocxConversion(  $path . '/' . $document->file, $document->type );
					$output    = $converter->convertToText();
				}

				// Update the content.
				if ( ! empty( $output ) ) {
					$document_post               = get_post( $post_id );
					$document_post->post_content = apply_filters( 'the_content', $output );
					remove_action( 'save_post', array( $this, 'save_meta' ), 9998 );
					wp_update_post( $document_post );
					add_action( 'save_post', array( $this, 'save_meta' ), 9999 );
				}

				// TODO: Hooked in behaviour from GFS project, to be realised.
				//
				// foreach ( $image_thumbs as $size => $image_thumb_file ) {
				// 	update_post_meta( $post_id, MKDO_BINDER_PREFIX . '_thumb-' . $size, $image_thumb_file );
				// }

				// Set the type.
				wp_set_object_terms( $post_id, array( $document->type ), 'binder_type', false );

			}
		}
	}
}
