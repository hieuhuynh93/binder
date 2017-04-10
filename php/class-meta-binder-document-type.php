<?php
/**
 * Class Meta_Binder_Document_Type
 *
 * @package mkdo\binder
 */

namespace mkdo\binder;

/**
 * Register the Binder Document Type Meta Box
 */
class Meta_Binder_Document_Type {

	/**
	 * Constructor
	 */
	function __construct() {}

	/**
	 * Do Work
	 */
	public function run() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 0 );
		// add_action( 'save_post', array( $this, 'save_meta' ), 9999 );
	}

	/**
	 * Add Meta Boxes
	 */
	public function add_meta_boxes() {

		add_meta_box(
			'binder_document_type',
			'Document Type',
			array( $this, 'binder_document_type' ),
			'binder',
			'side',
			'default'
		);

	}

	/**
	 * Add Entry
	 */
	public function binder_document_type() {

		global $post;

		// Get the current value.
		$value = wp_get_object_terms( $post->ID, 'binder_type' );
		if ( is_array( $value ) && ! empty( $value ) && ! is_wp_error( $value ) ) {
			$value = $value[0]->slug;
		} else {
			$value = '';
		}

		// Get the document type list.
		$types_list = array();
		$types      = get_terms(
			'binder_type',
			array(
			    'hide_empty' => false,
			)
		);

		// Setup the document type list.
		if ( is_array( $types ) ) {
			foreach ( $types as $type ) {
				$type_icon = get_term_meta( $type->term_id, MKDO_BINDER_PREFIX . '_type_icon', true );
				if ( is_array( $type_icon ) ) {
					$type_icon = $type_icon[0];
				}
				$types_list[ $type->slug ] = '<i class="fa fa-' . $type_icon . '"></i> - ' . $type->name;
			}
		}

		?>
		<div class="meta-box">
			<div class="meta-box__region meta-box__region--document-type">
				<?php
				// Output the document type list.
				foreach ( $types_list as $key => $type ) {
				?>
				<label for="<?php echo esc_attr( MKDO_BINDER_PREFIX );?>_type_<?php echo esc_attr( $key );?>">
					<input
						type="radio"
						id="<?php echo esc_attr( MKDO_BINDER_PREFIX );?>_type_<?php echo esc_attr( $key );?>"
						name="<?php echo esc_attr( MKDO_BINDER_PREFIX );?>_type"
						<?php checked( $value, $key, true );?>
						readonly="readonly"
						disabled="disabled"
					/>
					<?php
					echo wp_kses(
						$type,
						array(
							'i' => array(
								'class' => array(),
							),
						)
					);
					?>
				</label>
				<br/>
				<?php
				}
				?>
			</div>
		</div>
		<?php
		wp_nonce_field( MKDO_BINDER_PREFIX . '_document_type', MKDO_BINDER_PREFIX . '_document_type_nonce' );
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
		if ( ! isset( $_POST[ MKDO_BINDER_PREFIX . '_document_type_nonce' ] ) || ! wp_verify_nonce( $_POST[ MKDO_BINDER_PREFIX . '_document_type_nonce' ], MKDO_BINDER_PREFIX . '_document_type' ) ) {
			return $post_id;
		}

		/**
		 * Save Meta
		 *
		 * We have no need to save the meta, meta should never be saved directly
		 * using this meta box.
		 */
	}
}
