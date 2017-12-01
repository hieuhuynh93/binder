<?php
/**
 * Binder
 *
 * @link              https://github.com/mwtsn/binder
 * @package           mkdo\binder
 *
 * Plugin Name:       Binder
 * Plugin URI:        https://github.com/mwtsn/binder
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

global $wpdb;

// Constants.
define( 'MKDO_BINDER_ROOT', __FILE__ );
define( 'MKDO_BINDER_NAME', 'Binder' );
define( 'MKDO_BINDER_VERSION', '0.1.0' );
define( 'MKDO_BINDER_PREFIX', 'mkdo_binder' );
define( 'MKDO_BINDER_HISTORY_TABLE', $wpdb->prefix . 'binder_history' );

// Classes.
require_once 'vendor/class.pdf2text.php';
require_once 'vendor/DocxConversion.php';

require_once 'php/class-helper.php';
require_once 'php/class-binder.php';
require_once 'php/class-binder-document.php';

require_once 'php/class-activator.php';
require_once 'php/class-settings.php';
require_once 'php/class-controller-assets.php';
require_once 'php/class-controller-main.php';
require_once 'php/class-load-binder-document.php';
require_once 'php/class-meta-binder-add-entry.php';
require_once 'php/class-meta-binder-document-type.php';
require_once 'php/class-meta-binder-excerpt.php';
require_once 'php/class-meta-binder-version-control.php';
require_once 'php/class-notices-admin.php';
require_once 'php/class-post-binder.php';
require_once 'php/class-shortcode-binder-document.php';
require_once 'php/class-shortcode-binder-document-list.php';
require_once 'php/class-taxonomy-binder-category.php';
require_once 'php/class-taxonomy-binder-tag.php';
require_once 'php/class-taxonomy-binder-type.php';

// Namespaces
//
// Add references for each class here. If you add new classes be sure to include
// the namespace.
use mkdo\binder\Helper;
use mkdo\binder\Binder;
use mkdo\binder\Binder_Document;

use mkdo\binder\Activator;
use mkdo\binder\Settings;
use mkdo\binder\Controller_Assets;
use mkdo\binder\Controller_Main;
use mkdo\binder\Load_Binder_Document;
use mkdo\binder\Meta_Binder_Add_Entry;
use mkdo\binder\Meta_Binder_Document_Type;
use mkdo\binder\Meta_Binder_Excerpt;
use mkdo\binder\Meta_Binder_Version_Control;
use mkdo\binder\Notices_Admin;
use mkdo\binder\Post_Binder;
use mkdo\binder\Shortcode_Binder_Document;
use mkdo\binder\Shortcode_Binder_Document_List;
use mkdo\binder\Taxonomy_Binder_Category;
use mkdo\binder\Taxonomy_Binder_Tag;
use mkdo\binder\Taxonomy_Binder_Type;

// Instances.
$activator    			        = new Activator();
$settings                       = new Settings();
$controller_assets  	        = new Controller_Assets();
$load_binder_document  	        = new Load_Binder_Document();
$meta_binder_add_entry          = new Meta_Binder_Add_Entry();
$meta_binder_document_type      = new Meta_Binder_Document_Type();
$meta_binder_excerpt            = new Meta_Binder_Excerpt();
$meta_binder_version_control    = new Meta_Binder_Version_Control();
$notices_admin  	            = new Notices_Admin();
$post_binder  	                = new Post_Binder();
$shortcode_binder_document      = new Shortcode_Binder_Document();
$shortcode_binder_document_list = new Shortcode_Binder_Document_List();
$taxonomy_binder_category       = new Taxonomy_Binder_Category();
$taxonomy_binder_tag            = new Taxonomy_Binder_Tag();
$taxonomy_binder_type           = new Taxonomy_Binder_Type();
$controller_main                = new Controller_Main(
	$activator,
	$settings,
	$controller_assets,
	$load_binder_document,
	$meta_binder_add_entry,
	$meta_binder_document_type,
	$meta_binder_excerpt,
	$meta_binder_version_control,
	$notices_admin,
	$post_binder,
	$shortcode_binder_document,
	$shortcode_binder_document_list,
	$taxonomy_binder_category,
	$taxonomy_binder_tag,
	$taxonomy_binder_type
);

// Go.
$controller_main->run();

register_uninstall_hook( MKDO_BINDER_ROOT, 'mkdo_binder_uninstall' );
