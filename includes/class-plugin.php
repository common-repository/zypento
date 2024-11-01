<?php
/**
 * The file that defines the core plugin class
 *
 * @link       http://sproutient.com
 * @since      1.0.0
 *
 * @package    zypento
 * @subpackage zypento/includes
 */

namespace Zypento;

/**
 * The core plugin class.
 *
 * @since      1.0.0
 * @package    zypento
 * @subpackage zypento/includes
 * @author     Sproutient <dev@sproutient.com>
 */
class Plugin {

	/**
	 * Define the core functionality of the plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->set_locale();
		$this->loader();

	}

	/**
	 * Run on activation.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
			die( esc_html__( 'This plugin requires PHP version 7.4 or greater.  Sorry about that.', 'zypento' ) );
		}

		if ( ! wp_next_scheduled( 'zyp_bg_process', array() ) ) {
			wp_schedule_event( time(), 'hourly', 'zyp_bg_process', array() );
		}

	}

	/**
	 * Run on deactivation.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		add_action(
			'plugins_loaded',
			function() {

				load_plugin_textdomain(
					'zypento',
					false,
					ZYPENTO_PLUGIN_PATH . '/languages/'
				);

			}
		);

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function loader() {

		global $zypento_objects;

		$zypento_options = get_option( 'zypento_enabled_features', array() );

		$dir = ZYPENTO_PLUGIN_PATH . '/includes';

		$iterator = new \RecursiveIteratorIterator( new \RecursiveDirectoryIterator( $dir ) );

		foreach ( $iterator as $file ) {
			if ( $file->isFile() && $file->getPath() !== $dir ) {
				$filename       = pathinfo( $file->getFilename(), PATHINFO_FILENAME );
				$class_name     = str_replace( 'class-', '', $filename );
				$class_name     = str_replace( '-', ' ', $class_name );
				$class_name_key = $class_name;
				$class_name     = ucwords( $class_name );
				$class_name     = str_replace( ' ', '_', $class_name );

				$class_name_key = str_replace( ' ', '_', $class_name_key );

				$class_name = 'Zypento\\' . $class_name;

				if ( class_exists( $class_name ) ) {

					if ( strpos( $file->getPath(), 'includes/features' )
					) {
						if (
							! array_key_exists( $class_name_key, $zypento_options ) ||
							( array_key_exists( $class_name_key, $zypento_options ) &&
							'no' !== $zypento_options[ $class_name_key ] )
						) {
							$zypento_objects[ $class_name_key ] = new $class_name();
						}
					} else {
						$zypento_objects[ $class_name_key ] = new $class_name();
					}
				}
			}
		}

	}

}
