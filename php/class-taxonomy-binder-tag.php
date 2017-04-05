<?php
/**
 * Class Taxonomy_Binder_Tag
 *
 * @package mkdo\binder
 */

namespace mkdo\binder;

/**
 * Register Taxonomy for Document Post Type
 */
class Taxonomy_Binder_Tag {

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
		$this->icons = Helper::get_icons();
	}

	/**
	 * Do Work
	 */
	public function run() {
		add_action( 'init', array( $this, 'register_taxonomy' ) );
		add_action( 'binder_tag_add_form_fields', array( $this, 'add_form_fields' ), 10 );
		add_action( 'binder_tag_edit_form_fields', array( $this, 'edit_form_fields' ), 10, 2 );
		add_action( 'edited_binder_tag', array( $this, 'save_data' ), 10, 2 );
		add_action( 'created_binder_tag', array( $this, 'save_data' ), 10, 2 );
		add_action( 'wp', array( $this, 'template_redirect' ) );
	}

	/**
	 * Register Taxonomy
	 */
	public function register_taxonomy() {

		$labels = array(
			'name'                       => _x( 'Document Tags', 'Taxonomy General Name', 'binder' ),
			'singular_name'              => _x( 'Document Tag', 'Taxonomy General Name', 'binder' ),
			'menu_name'                  => __( 'Document Tags', 'binder' ),
			'all_items'                  => __( 'All Document Tags', 'binder' ),
			'edit_item'                  => __( 'Edit Document Tag', 'binder' ),
			'view_item'                  => __( 'View Document Tag', 'binder' ),
			'update_item'                => __( 'Update Document Tag', 'binder' ),
			'add_new_item'               => __( 'Add New Document Tag', 'binder' ),
			'new_item_name'              => __( 'New Document Tag Name', 'binder' ),
			'search_items'               => __( 'Search Document Tags', 'binder' ),
			'popular_items'              => __( 'Popular Document Tags', 'binder' ),
			'separate_items_with_commas' => __( 'Separate tags with commas', 'binder' ),
			'add_or_remove_items'        => __( 'Add or remove tags', 'binder' ),
			'choose_from_most_used'      => __( 'Choose from the most used tags', 'binder' ),
			'not_found'                  => __( 'No tags found.', 'binder' ),
		);
		$args = array(
			'hierarchical'          => false,
			'labels'                => $labels,
			'show_ui'               => true,
			'show_admin_column'     => true,
			'query_var'             => true,
			'public'                => true,
			'publicly_queryable'    => true,
			'rewrite'               => array( 'slug' => '/document/tag' ),
			'public'                => true,
			'publicly_queryable'    => true,
		);

		register_taxonomy( 'binder_tag', 'binder', $args );
	}

	/**
	 * Add Form Fields
	 *
	 * @param  string $taxonomy The taxonomy.
	 */
	public function add_form_fields( $taxonomy ) {

		$value = '';

		?>
		<div class="form-field">
			<label for="mkdo_global_food_security_binder_tag_icon"><?php esc_html_e( 'Icon', 'binder' ); ?></label>
			<select name="mkdo_global_food_security_binder_tag_icon" id="mkdo_global_food_security_binder_tag_icon" class="fa-select">
				<?php
				foreach ( $this->icons as $icon ) {
					?>
					<option value="<?php echo esc_attr( $icon['id'] );?>" <?php selected( $value, $icon['id'], true ); ?>><?php echo esc_html( $icon['name'] );?></option>
					<?php
				}
				?>
			</select>
			<p style="clear:both;"><?php esc_html_e( 'Choose an icon associated with this tag.', 'binder' ); ?></p>
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
			$value = get_term_meta( $term->term_id, 'mkdo_global_food_security_binder_tag_icon', true );
		}
		?>
		<tr class="form-field">
			<th scope="row">
				<label for="mkdo_global_food_security_binder_tag_icon"><?php esc_html_e( 'Icon', 'binder' ); ?></label>
			</th>
			<td>
				<select name="mkdo_global_food_security_binder_tag_icon" id="mkdo_global_food_security_binder_tag_icon" class="fa-select">
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

		if ( isset( $_POST['mkdo_global_food_security_binder_tag_icon'] ) ) {
			$value = esc_html( $_POST['mkdo_global_food_security_binder_tag_icon'] );
			update_term_meta( $term_id, 'mkdo_global_food_security_binder_tag_icon', $value );
		}
	}

	/**
	 * Because we share the path with the document single, we need to make
	 * sure WP can find the archive
	 */
	function template_redirect() {

		global $post, $wp_query;

		$url = $_SERVER['REQUEST_URI'];

		if ( is_404() && stripos( $url, '/document/tag/' ) === 0 ) {
	        $slug = $wp_query->query_vars['name'];

			$paged = 1;
			$url = str_replace( '/document/tag/', '', $url );
			$url = preg_replace('/\?.*/', '', $url);

			if ( stripos( $url, 'page/' ) === 0 ) {
				$url   = str_replace( 'page/', '', $url );
				$paged = preg_replace( '/[^0-9 ]/', '', $url );
				if ( ! is_numeric( $paged ) ) {
					$paged = 1;
				}
			}

	        if ( ! is_admin() && ! empty( $slug ) ) {
	            status_header( 200 );
	            $wp_query->is_404         = false;
	            $args                     = array();
	            $args['binder_tag']     = $slug;
	            $args['paged']            = $paged;
	            $args['suppress_filters'] = false;

	            $GLOBALS['wp_query']     = $wp_query = new \WP_Query( $args );
	            $GLOBALS['wp_the_query'] = $GLOBALS['wp_query'];
	        }
		}
	}
}
