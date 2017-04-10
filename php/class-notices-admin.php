<?php
/**
 * Class Notices_Admin
 *
 * @package mkdo\binder
 */

namespace mkdo\binder;

/**
 * If the plugin needs attention, here is where the notices are set.
 *
 * You should place warnings such as plugin dependancies here.
 */
class Notices_Admin {

	/**
	 * Constructor
	 */
	function __construct() {}

	/**
	 * Do Work
	 */
	public function run() {
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
	}

	/**
	 * Do Admin Notifications
	 */
	public function admin_notices() {

		// Shortcake.
		if ( ! function_exists( 'shortcode_ui_register_for_shortcode' ) ) {
			$install_url = admin_url( '/plugin-install.php?s=Shortcode%20UI&tab=search&type=term' );
			$warning     = sprintf( __( 'The %1$sBinder%2$s plugin works much better when you %3$sinstall and activate the Shortcode UI plugin%4$s.', 'binder' ), '<strong>', '</strong>', '<a href="' . esc_url( $install_url ) . '" target="_blank">', '</a>' );
			?>
			<div class="notice notice-warning is-dismissible">
			<p>
			<?php
				echo wp_kses(
					$warning,
					array(
						'a' => array(
							'href'   => array(),
							'target' => array(),
						),
						'strong'   => array(),
						'em' => array(),
					)
				);
			?>
			</p>
			</div>
			<?php
		}
	}
}
