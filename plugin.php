<?php
/**
 * The plugin bootstrap file
 *
 * @link              zypento.com
 * @since             1.0.0
 * @package           zypento
 *
 * @wordpress-plugin
 * Plugin Name:       Zypento
 * Plugin URI:        http://zypento.com
 * Description:       Multipurpose WooCommerce plugin.
 * Version:           1.0.1
 * Author:            Zypento
 * Author URI:        zypento.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       zypento
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Autoloader
 */
require_once 'vendor/autoload.php';

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'ZYPENTO_NAME', 'zypento' );
define( 'ZYPENTO_VERSION', '1.0.0' );
define( 'ZYPENTO_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'ZYPENTO_PLUGIN_URL', plugins_url( '', __FILE__ ) );

$zypento_post_types = array( 'product' );
$zypento_namespace  = 'zypento/v1';
$zypento_objects    = array();

register_activation_hook(
	__FILE__,
	function() {
		\Zypento\Plugin::activate();
	}
);
register_deactivation_hook(
	__FILE__,
	function() {
		\Zypento\Plugin::deactivate();
	}
);

/**
 * Begins execution of the plugin.
 */
new \Zypento\Plugin();

