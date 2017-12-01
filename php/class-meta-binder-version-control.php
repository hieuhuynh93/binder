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

		$document = Binder::get_latest_document_by_post_id( $post->ID );
		$history  = Binder::get_history_by_post_id( $post->ID );
		$base     = apply_filters( MKDO_BINDER_PREFIX . '_document_base', WP_CONTENT_DIR . '/uploads/binder/' );
		?>
		<div class="meta-box">
			<table class="binder-history">
				<thead>
					<tr>
						<th class="binder-history__column binder-history__column--version"><?php esc_html_e( 'Version', 'binder' );?></th>
						<th class="binder-history__column binder-history__column--type"><?php esc_html_e( 'Type', 'binder' );?></th>
						<th class="binder-history__column binder-history__column--size"><?php esc_html_e( 'Size', 'binder' );?></th>
						<th class="binder-history__column binder-history__column--date"><?php esc_html_e( 'Date', 'binder' );?></th>
						<th class="binder-history__column binder-history__column--author"><?php esc_html_e( 'Author', 'binder' );?></th>
						<th class="binder-history__column binder-history__column--comment"><?php esc_html_e( 'Comment', 'binder' );?></th>
						<th class="binder-history__column binder-history__column--latest"><?php esc_html_e( 'Latest', 'binder' );?></th>
						<th class="binder-history__column binder-history__column--delete"><?php esc_html_e( 'Delete', 'binder' );?></th>
						<th class="binder-history__column binder-history__column--downoad">
							<i class="fa fa-download">
								<span class="u-hidden-visually | sr-only">
									<?php esc_html_e( 'Download', 'binder' ); ?>
								</span>
							</i>
						</th>
					</tr>
				</thead>
				<tbody>
				<?php
				foreach ( $history as $version ) {
					$link     = get_the_permalink( $version->post_id );
					$author   = get_userdata( $version->user_id );
					$type     = '';
					$icon     = '';
					$term     = wp_get_object_terms( $version->post_id, 'binder_type' );

					if ( ! empty( $term ) ) {
						$term = $term[0];
						$type = $term->name;
						$icon = get_term_meta( $term->term_id, MKDO_BINDER_PREFIX . '_type_icon', true );
					}

					// Term fallback.
					if ( empty( $term ) ) {
						$type = $document->type;
						$term = get_term_by( 'slug', $type, 'binder_type' );
						if ( ! empty( $term ) ) {
							$term = $term;
							$type = $term->name;
							$icon = get_term_meta( $term->term_id, MKDO_BINDER_PREFIX . '_type_icon', true );
						}
					}
					if ( 'comment' !== $version->type ) {
						$current_version = esc_attr( $version->version );
					}
					?>
					<tr>
						<td
							class="binder-history__column binder-history__column--version"
							data-th="<?php esc_html_e( 'Version', 'binder' );?>"
						>
						<?php echo esc_html( $version->version );?>
						</td>
						<td
							class="binder-history__column binder-history__column--type"
							data-th="<?php esc_html_e( 'Type', 'binder' );?>"
						>
						<?php
						if ( 'comment' !== $version->type && false === filter_var( $version->file, FILTER_VALIDATE_URL ) ) {
							echo '<i class="fa fa-' . esc_attr( $icon ) . '"></i> ';
							echo esc_html( $type );
						} elseif ( 'comment' === $version->type ) {
							echo '<i class="fa fa-comment-o"></i> ';
							esc_html_e( 'Comment', 'binder' );
						} else {

							echo '<i class="fa fa-' . esc_attr( $icon ) . '"></i> ';
							echo esc_html( $type );
							echo ' (';
							echo '<i class="fa fa-link"></i> ';
							echo esc_html_e( 'Link', 'binder' );
							echo ')';
						}
						?>
						</td>
						<td
							class="binder-history__column binder-history__column--size"
							data-th="<?php esc_html_e( 'Size', 'binder' );?>"
						>
							<?php echo esc_html( $version->size );?>
						</td>
						<td
							class="binder-history__column binder-history__column--date"
							data-th="<?php esc_html_e( 'Date', 'binder' );?>"
						>
							<?php echo esc_html( $version->upload_date );?>
						</td>
						<td
							class="binder-history__column binder-history__column--author"
							data-th="<?php esc_html_e( 'Author', 'binder' );?>"
						>
							<?php echo esc_html( $author->user_nicename );?>
						</td>
						<td
							class="binder-history__column binder-history__column--comment"
							data-th="<?php esc_html_e( 'Comment', 'binder' );?>"
						>
							<?php echo esc_html( $version->description );?>
							<?php
							$thumbs = unserialize( $version->thumb );
							if ( ! empty( $thumbs ) ) {
								$uploads    = wp_upload_dir();
								$image_path = $version->get_thumbnail( $version->binder_id, $size = 'thumbnail' );
								?>
								<img src="<?php echo esc_attr( $image_path );?>" alt="File Preview"/>
								<?php
							}
							?>
						</td>
						<td
							class="binder-history__column binder-history__column--latest"
							data-th="<?php esc_html_e( 'Latest', 'binder' );?>"
						>
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
									<span class="u-hidden-visually | sr-only">
										<?php esc_html_e( 'Set to Latest', 'binder' );?>
									</span>
								</label>
								<?php
							}
							?>
						</td>
						<td
							class="binder-history__column binder-history__column--remove"
							data-th="<?php esc_html_e( 'Remove', 'binder' );?>"
						>
							<label for="<?php echo esc_attr( MKDO_BINDER_PREFIX );?>_remove">
								<input
									id="<?php echo esc_attr( MKDO_BINDER_PREFIX);?>_remove"
									name="<?php echo esc_attr( MKDO_BINDER_PREFIX );?>_remove[]"
									value="<?php echo esc_attr( $version->binder_id );?>"
									type="checkbox"
								/>
								<span class="u-hidden-visually | sr-only"><?php esc_html_e( 'Remove', 'binder' );?></span>
							</label>
						</td>
						<td
							class="binder-history__column binder-history__column--remove"
							data-th="<?php esc_html_e( 'Download', 'binder' );?>"
						>
						<?php
						if ( 'comment' !== $version->type ) {
							?>
							<a href="<?php echo esc_url( $link );?>?v=<?php echo esc_attr( $version->version );?>" target="_blank"><?php esc_html_e( 'Download', 'binder' );?></a>
							<?php
						}
						?>
						</td>
					</tr>
					<?php
				}
				?>
				</tbody>
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

			$documents = $_POST[ MKDO_BINDER_PREFIX . '_remove' ];
			if ( is_array( $documents ) ) {
				foreach ( $documents as $binder_id ) {
					Binder::delete_document_history_item( $binder_id );
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

				// Update the history.
				Binder::update_document_history_item_to_latest( $latest_file );

				// Get the document.
				$document = Binder::get_document( $latest_file );
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
					add_action( 'save_post', array( $this, 'save_meta' ), 9998 );
				}

				// Set the type.
				wp_set_object_terms( $post_id, array( $document->type ), 'binder_type', false );

			}
		}
	}
}
