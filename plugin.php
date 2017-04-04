<?php
/**
 * Binder
 *
 * @link              https://github.com/mkdo/binder
 * @package           mkdo\binder
 *
 * Plugin Name:       Binder
 * Plugin URI:        https://github.com/mkdo/binder
 * Description:       Document Management System (DMS) for WordPress.
 * Version:           0.1.0
 * Author:            Make Do <hello@makedo.net>
 * Author URI:        https://makedo.net
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       binder
 * Domain Path:       /languages
 */

// Abort if this file is called directly.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Constants.
define( 'MKDO_BINDER_ROOT', __FILE__ );
define( 'MKDO_BINDER_NAME', 'Binder' );
define( 'MKDO_BINDER_VERSION', '0.1.0' );
define( 'MKDO_BINDER_PREFIX', 'mkdo_binder' );

// Classes.
require_once 'vendor/class.pdf2text.php';
require_once 'vendor/DocxConversion.php';

require_once 'php/class-helper.php';
require_once 'php/class-binder-document.php';

require_once 'php/class-activator.php';
require_once 'php/class-settings.php';
require_once 'php/class-controller-assets.php';
require_once 'php/class-controller-main.php';
require_once 'php/class-meta-binder.php';
require_once 'php/class-notices-admin.php';
require_once 'php/class-post-binder.php';

// Namespaces
//
// Add references for each class here. If you add new classes be sure to include
// the namespace.
use mkdo\binder\Helper;
use mkdo\binder\Binder_Document;

use mkdo\binder\Activator;
use mkdo\binder\Settings;
use mkdo\binder\Controller_Assets;
use mkdo\binder\Controller_Main;
use mkdo\binder\Meta_Binder;
use mkdo\binder\Notices_Admin;
use mkdo\binder\Post_Binder;

// Instances.
$activator    			  = new Activator();
$settings                 = new Settings();
$controller_assets  	  = new Controller_Assets();
$meta_binder  	          = new Meta_Binder();
$notices_admin  	      = new Notices_Admin();
$post_binder  	          = new Post_Binder();
$controller_main          = new Controller_Main(
	$activator,
	$settings,
	$controller_assets,
	$meta_binder,
	$notices_admin,
	$post_binder
);

// Go.
$controller_main->run();

register_uninstall_hook( MKDO_BINDER_ROOT, 'mkdo_binder_uninstall' );
