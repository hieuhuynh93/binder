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
		add_action( 'init', array( $this, 'register_taxonomy' ) );
		add_action( 'binder_type_add_form_fields', array( $this, 'add_form_fields' ), 10 );
		add_action( 'binder_type_edit_form_fields', array( $this, 'edit_form_fields' ), 10, 2 );
		add_action( 'edited_binder_type', array( $this, 'save_data' ), 10, 2 );
		add_action( 'created_binder_type', array( $this, 'save_data' ), 10, 2 );
		add_action( 'admin_menu', array( $this, 'remove_meta_boxes' ) );
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
			<label for="mkdo_binder_binder_type_icon"><?php esc_html_e( 'Icon', 'binder' ); ?></label>
			<select name="mkdo_binder_binder_type_icon" id="mkdo_binder_binder_type_icon" class="fa-select">
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
				<label for="mkdo_binder_binder_type_icon"><?php esc_html_e( 'Icon', 'binder' ); ?></label>
			</th>
			<td>
				<select pattern="#[a-f0-9]{6}" name="mkdo_binder_binder_type_icon" id="mkdo_binder_binder_type_icon" class="fa-select">
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
		remove_meta_box( 'binder_typediv' , 'document' , 'side' );
	}
}
