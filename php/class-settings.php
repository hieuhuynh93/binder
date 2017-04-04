<?php
/**
 * Class Settings
 *
 * @since	0.1.0
 *
 * @package mkdo\binder
 */

namespace mkdo\binder;

/**
 * The main plugin settings page
 */
class Settings {

	/**
	 * Constructor.
	 *
	 * @since	0.1.0
	 */
	public function __construct() {}

	/**
	 * Do Work
	 *
	 * @since	0.1.0
	 */
	public function run() {
		add_action( 'admin_init', array( $this, 'init_settings_page' ) );
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'plugin_action_links_' . plugin_basename( MKDO_BINDER_ROOT ) , array( $this, 'add_setings_link' ) );
	}

	/**
	 * Initialise the Settings Page.
	 *
	 * @since	0.1.0
	 */
	public function init_settings_page() {

		// Register settings.
		register_setting( MKDO_BINDER_PREFIX . '_settings_group', MKDO_BINDER_PREFIX . '_example_setting' );

		// Add sections.
		add_settings_section( MKDO_BINDER_PREFIX . '_example_section',
			esc_html__( 'Example Section Heading', 'binder' ),
			array( $this, MKDO_BINDER_PREFIX . '_example_section_cb' ),
			MKDO_BINDER_PREFIX . '_settings'
		);

		// Add fields to a section.
		add_settings_field( MKDO_BINDER_PREFIX . '_example_field',
			esc_html__( 'Example Field Label:', 'binder' ),
			array( $this, MKDO_BINDER_PREFIX . '_example_field_cb' ),
			MKDO_BINDER_PREFIX . '_settings',
			MKDO_BINDER_PREFIX . '_example_section'
		);
	}

	/**
	 * Call back for the example section.
	 *
	 * @since	0.1.0
	 */
	public function mkdo_binder_example_section_cb() {
		echo '<p>' . esc_html( 'Example description for this section.', 'binder' ) . '</p>';
	}

	/**
	 * Call back for the example field.
	 *
	 * @since	0.1.0
	 */
	public function mkdo_binder_example_field_cb() {
		$example_option = get_option( MKDO_BINDER_PREFIX . '_example_option', 'Default text...' );
		?>

		<div class="field field-example">
			<p class="field-description">
				<?php esc_html_e( 'This is an example field.', 'binder' );?>
			</p>
			<ul class="field-input">
				<li>
					<label>
						<input type="text" name="<?php echo esc_attr( MKDO_BINDER_PREFIX . '_example_field' ); ?>" value="<?php echo esc_attr( $example_option ); ?>" />
					</label>
				</li>
			</ul>
		</div>

		<?php
	}

	/**
	 * Add the settings page.
	 *
	 * @since	0.1.0
	 */
	public function add_settings_page() {
		add_submenu_page( 'edit.php?post_type=binder',
			esc_html__( 'Settings', 'binder' ),
			esc_html__( 'Settings', 'binder' ),
			'manage_options',
			MKDO_BINDER_PREFIX,
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Render the settings page.
	 *
	 * @since	0.1.0
	 */
	public function render_settings_page() {
		?>
		<div class="wrap">
			<h2><?php esc_html_e( 'Binder', 'binder' );?></h2>

			<form action="settings.php" method="POST">
				<?php settings_fields( MKDO_BINDER_PREFIX . '_settings_group' ); ?>
				<?php do_settings_sections( MKDO_BINDER_PREFIX . '_settings' ); ?>
				<?php submit_button(); ?>
			</form>
		</div>
	<?php
	}

	/**
	 * Add 'Settings' action on installed plugin list.
	 *
	 * @param array $links An array of plugin action links.
	 *
	 * @since	0.1.0
	 */
	function add_setings_link( $links ) {
		array_unshift( $links, '<a href="edit.php?post_type=binder&page=' . esc_attr( MKDO_BINDER_PREFIX ) . '">' . esc_html__( 'Settings', 'binder' ) . '</a>' );

		return $links;
	}
}
