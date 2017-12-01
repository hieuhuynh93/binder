<?php
/**
 * Class Taxonomy_Binder_Type
 *
 * @package mkdo\binder
 */

namespace mkdo\binder;

/**
 * Register Taxonomy for Document Post Type
 */
class Taxonomy_Binder_Type {

	/**
	 * Font Awesome Icons
	 *
	 * @var array
	 */
	private $icons;

	/**
	 * Constructor
	 */
	function __construct() {
		$this->icons = array();
		$icons       = Helper::get_icons();

		// We only want the document icons.
		foreach ( $icons as $icon ) {
			if ( false !== strpos( $icon['id'], 'file' ) ) {
				$this->icons[] = $icon;
			}
		}

	}

	/**
	 * Do Work
	 */
	public function run() {
		add_action( 'init', array( $this, 'register_taxonomy' ), 0 );
		add_action( 'binder_type_add_form_fields', array( $this, 'add_form_fields' ), 10 );
		add_action( 'binder_type_edit_form_fields', array( $this, 'edit_form_fields' ), 10, 2 );
		add_action( 'edited_binder_type', array( $this, 'save_data' ), 10, 2 );
		add_action( 'created_binder_type', array( $this, 'save_data' ), 10, 2 );
		add_action( 'admin_menu', array( $this, 'remove_meta_boxes' ) );
		add_action( 'init', array( $this, 'populate_taxonomy' ), 9999 );
	}

	/**
	 * Register Taxonomy
	 */
	public function register_taxonomy() {

		$labels = array(
			'name'              => _x( 'Type', 'Taxonomy General Name', 'binder' ),
			'singular_name'     => _x( 'Type', 'Taxonomy General Name', 'binder' ),
			'menu_name'         => __( 'Types', 'binder' ),
			'all_items'         => __( 'All Types', 'binder' ),
			'edit_item'         => __( 'Edit Type', 'binder' ),
			'view_item'         => __( 'View Type', 'binder' ),
			'update_item'       => __( 'Update Type', 'binder' ),
			'add_new_item'      => __( 'Add New Type', 'binder' ),
			'new_item_name'     => __( 'New Type Name', 'binder' ),
			'parent_item'       => __( 'Parent Type', 'binder' ),
			'parent_item_colon' => __( 'Parent Type:', 'binder' ),
			'search_items'      => __( 'Search Types', 'binder' ),
			'not_found'         => __( 'No categories found.', 'binder' ),
		);
		$args = array(
			'hierarchical'       => true,
			'labels'             => $labels,
			'show_ui'            => true,
			'show_admin_column'  => true,
			'query_var'          => true,
			'public'             => false,
			'publicly_queryable' => true,
		);

		register_taxonomy( 'binder_type', array( 'binder' ), $args );
	}

	/**
	 * [add_form_fields description]
	 *
	 * @param  string $taxonomy The taxonomy.
	 */
	public function add_form_fields( $taxonomy ) {

		$value = '';

		?>
		<div class="form-field">
			<label for="<?php echo esc_attr( MKDO_BINDER_PREFIX . '_type_icon' );?>"><?php esc_html_e( 'Icon', 'binder' ); ?></label>
			<select name="<?php echo esc_attr( MKDO_BINDER_PREFIX . '_type_icon' );?>" id="<?php echo esc_attr( MKDO_BINDER_PREFIX . '_type_icon' );?>" class="fa-select">
				<?php
				foreach ( $this->icons as $icon ) {
					?>
					<option value="<?php echo esc_attr( $icon['id'] );?>" <?php selected( $value, $icon['id'], true ); ?>><?php echo esc_html( $icon['name'] );?></option>
					<?php
				}
				?>
			</select>
			<p style="clear:both;"><?php esc_html_e( 'Choose an icon associated with this tag', 'binder' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Add meta fields to taxonomy edit screen
	 *
	 * @param  object $term     The term.
	 * @param  string $taxonomy The taxonomy.
	 */
	public function edit_form_fields( $term, $taxonomy ) {
		$value = '';
		if ( isset( $term ) && is_object( $term ) ) {
			$value = get_term_meta( $term->term_id, MKDO_BINDER_PREFIX . '_type_icon', true );
		}
		?>
		<tr class="form-field">
			<th scope="row">
				<label for="<?php echo esc_attr( MKDO_BINDER_PREFIX . '_type_icon' );?>"><?php esc_html_e( 'Icon', 'binder' ); ?></label>
			</th>
			<td>
				<select pattern="#[a-f0-9]{6}" name="<?php echo esc_attr( MKDO_BINDER_PREFIX . '_type_icon' );?>" id="<?php echo esc_attr( MKDO_BINDER_PREFIX . '_type_icon' );?>" class="fa-select">
					<?php
					foreach ( $this->icons as $icon ) {
						?>
						<option value="<?php echo esc_attr( $icon['id'] );?>" <?php selected( $value, $icon['id'], true ); ?>><?php echo esc_html( $icon['name'] );?></option>
						<?php
					}
					?>
				</select>
				<p class="description" style="clear:both;"><?php esc_html_e( 'Choose an icon associated with this tag.', 'binder' ); ?></p>
			</td>
		</tr>
		<?php
	}

	/**
	 * Save the meta
	 *
	 * @param int $term_id Term ID.
	 * @param int $tt_id   Term Taxonomy ID.
	 */
	public function save_data( $term_id, $tt_id ) {

		if ( isset( $_POST[ MKDO_BINDER_PREFIX . '_type_icon' ] ) ) {
			$value = esc_html( $_POST[ MKDO_BINDER_PREFIX . '_type_icon' ] );
			update_term_meta( $term_id, MKDO_BINDER_PREFIX . '_type_icon', $value );
		}
	}

	/**
	 * Remove Meta Box
	 */
	public function remove_meta_boxes() {
		remove_meta_box( 'binder_typediv' , 'binder' , 'side' );
	}

	/**
	 * Populate the taxonomy.
	 *
	 * @since    1.0.0
	 */
	public function populate_taxonomy() {

		$taxonomy_name = 'binder_type';

		$terms                                   = array();
		$terms['Portable Document Format (pdf)'] = array( 'slug' => 'pdf',  'icon' => 'file-pdf-o' );
		$terms['Microsoft Word (docx)']          = array( 'slug' => 'docx', 'icon' => 'file-word-o' );
		$terms['Microsoft Word (doc)']           = array( 'slug' => 'doc',  'icon' => 'file-word-o' );
		$terms['Microsoft PowerPoint (pptx)']    = array( 'slug' => 'pptx', 'icon' => 'file-powerpoint-o' );
		$terms['Microsoft PowerPoint (ppt)']     = array( 'slug' => 'ppt',  'icon' => 'file-powerpoint-o' );
		$terms['Microsoft Excel (xlsx)']         = array( 'slug' => 'xlsx', 'icon' => 'file-excel-o' );
		$terms['Microsoft Excel (xls)']          = array( 'slug' => 'xls',  'icon' => 'file-excel-o' );
		$terms['Rich Text Format (rtf)']         = array( 'slug' => 'rtf',  'icon' => 'file-text-o' );
		$terms['Comma Separated Values (csv)']   = array( 'slug' => 'csv',  'icon' => 'file-code-o' );
		$terms['OpenDocument Text (odt)']        = array( 'slug' => 'odt',  'icon' => 'file-text-o' );

		foreach ( $terms as $term => $properties ) {

			$parent_id   = 0;
			$slug        = ( isset( $properties['slug'] ) ) ? $properties['slug'] : false;
			$parent      = ( isset( $properties['parent'] ) ) ? esc_attr( $properties['parent'] ) : 0;
			$description = ( isset( $properties['description'] ) ) ? esc_attr( $properties['description'] ) : false;

			if ( 0 !== $parent ) {

				$parent_object = get_term_by( 'name', $parent, $taxonomy_name  );

				if ( is_object( $parent_object ) ) {
					$parent_id = $parent_object->term_id;
				}
			}

			if ( ! term_exists( $term, $taxonomy_name ) ) {

				$result = wp_insert_term(
					$term,
					$taxonomy_name,
					array(
						'slug'        => $slug,
						'parent'      => $parent_id,
						'description' => $description,
					)
				);

				if ( is_array( $result ) && $result['term_id'] && isset( $properties['icon'] ) ) {
					update_term_meta( $result['term_id'], MKDO_BINDER_PREFIX . '_type_icon', $properties['icon'] );
				}
			}
		}
	}
}
