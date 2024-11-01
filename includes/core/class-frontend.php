<?php
/**
 * Frontend features.
 *
 * @link       http://sproutient.com
 * @since      1.0.0
 *
 * @package    zypento
 * @subpackage zypento/includes
 */

namespace Zypento;

/**
 * Frontend features.
 *
 * @since      1.0.0
 * @package    zypento
 * @subpackage zypento/includes
 * @author     Sproutient <dev@sproutient.com>
 */
class Frontend {

	/**
	 * Data for frontend.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $public_variables    JS Variables.
	 */
	private $public_variables;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->public_variables = array();

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );

	}

	/**
	 * Register the JavaScript/CSS for the frontend.
	 *
	 * @since    1.0.0
	 * @param    string $hook current page.
	 */
	public function enqueue_assets( $hook ) {

		$this->public_variables['userLoggedIn'] = false;
		$this->public_variables['nonce']        = '';
		$this->public_variables['wpRestNonce']  = '';
		$this->public_variables['api']          = array();

		$this->public_variables['labels']          = array();
		$this->public_variables['labels']['prev']  = esc_html__( 'Prev', 'zypento' );
		$this->public_variables['labels']['next']  = esc_html__( 'Next', 'zypento' );
		$this->public_variables['labels']['wait']  = esc_html__( 'Please wait...', 'zypento' );
		$this->public_variables['labels']['error'] = esc_html__( 'Something went wrong, Please try again...', 'zypento' );

		if ( is_user_logged_in() && current_user_can( 'administrator' ) ) {

			$this->public_variables['userLoggedIn'] = true;

		}

		$this->public_variables['nonce']       = esc_html( wp_create_nonce( 'zypento' ) );
		$this->public_variables['wpRestNonce'] = esc_html( wp_create_nonce( 'wp_rest' ) );

		$this->public_variables['features'] = array();

		$variables = apply_filters( 'zypento_js_variables', $this->public_variables );

		wp_enqueue_style( ZYPENTO_NAME, ZYPENTO_PLUGIN_URL . '/assets/css/public.css', array(), ZYPENTO_VERSION, 'all' );
		wp_register_script( ZYPENTO_NAME, ZYPENTO_PLUGIN_URL . '/assets/js/public.js', array( 'jquery' ), ZYPENTO_VERSION, true );
		wp_localize_script( ZYPENTO_NAME, 'zypentoJsVariables', $variables );
		wp_enqueue_script( ZYPENTO_NAME );

	}

}
