<?php
/**
 * Admin features.
 *
 * @link       http://sproutient.com
 * @since      1.0.0
 *
 * @package    zypento
 * @subpackage zypento/includes
 */

namespace Zypento;

/**
 * Admin features.
 *
 * @since      1.0.0
 * @package    zypento
 * @subpackage zypento/includes
 * @author     Sproutient <dev@sproutient.com>
 */
class Admin {

	/**
	 * Data for admin page.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $admin_variables    JS Variables.
	 */
	private $admin_variables;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->admin_variables = array();

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'admin_menu', array( $this, 'create_admin_pages' ) );
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );

	}

	/**
	 * Register the JavaScript/CSS for the admin area.
	 *
	 * @since    1.0.0
	 * @param    string $hook current page.
	 */
	public function enqueue_assets( $hook ) {

		$zypento_post_types = apply_filters( 'zypento_post_types', array( 'product' ) );

		if ( 'toplevel_page_zypento' !== $hook && 'post-new.php' !== $hook && 'post.php' !== $hook && ! in_array( get_post_type(), $zypento_post_types, true ) ) {
			return;
		}

		$this->admin_variables['userLoggedIn'] = false;
		$this->admin_variables['nonce']        = '';
		$this->admin_variables['wpRestNonce']  = '';
		$this->admin_variables['api']          = array();
		$this->admin_variables['api']['admin'] = array();

		$this->admin_variables['labels']          = array();
		$this->admin_variables['labels']['prev']  = esc_html__( 'Prev', 'zypento' );
		$this->admin_variables['labels']['next']  = esc_html__( 'Next', 'zypento' );
		$this->admin_variables['labels']['wait']  = esc_html__( 'Please wait...', 'zypento' );
		$this->admin_variables['labels']['error'] = esc_html__( 'Something went wrong, Please try again...', 'zypento' );

		$this->admin_variables['labels']['settingsSuccess'] = esc_html__( 'Settings saved.', 'zypento' );

		if ( is_user_logged_in() && current_user_can( 'administrator' ) ) {

			$this->admin_variables['userLoggedIn'] = true;
			$this->admin_variables['nonce']        = esc_html( wp_create_nonce( 'zypento' ) );
			$this->admin_variables['wpRestNonce']  = esc_html( wp_create_nonce( 'wp_rest' ) );

			$this->admin_variables['api']['admin']['settings'] = get_rest_url( null, 'zypento/v1/settings' );

		}

		$variables = apply_filters( 'zypento_admin_js_variables', $this->admin_variables );

		wp_enqueue_style( ZYPENTO_NAME, ZYPENTO_PLUGIN_URL . '/assets/css/admin.css', array(), ZYPENTO_VERSION, 'all' );
		wp_register_script( ZYPENTO_NAME, ZYPENTO_PLUGIN_URL . '/assets/js/admin.js', array( 'jquery' ), ZYPENTO_VERSION, true );
		wp_localize_script( ZYPENTO_NAME, 'zypentoAdminVariables', $variables );
		wp_enqueue_script( ZYPENTO_NAME );

	}

	/**
	 * Create admin pages.
	 *
	 * @since    1.0.0
	 */
	public function create_admin_pages() {

		if ( ! array_key_exists( 'zypento', $GLOBALS['admin_page_hooks'] ) ) {

			add_menu_page(
				__( 'Zypento', 'zypento' ),
				__( 'Zypento', 'zypento' ),
				'manage_options',
				'zypento',
				array( $this, 'admin_page_display' ),
				'dashicons-media-code',
				20
			);

		}

	}

	/**
	 * Display admin pagecontent.
	 *
	 * @since    1.0.0
	 */
	public function admin_page_display() {

		include ZYPENTO_PLUGIN_PATH . 'partials/admin-page.php';

	}

	/**
	 * Register REST Routes.
	 */
	public function register_routes() {

		global $zypento_namespace, $zypento_objects;

		register_rest_route(
			$zypento_namespace,
			'/settings',
			array(

				// Here we register the readable endpoint for collections.
				array(
					'methods'             => 'GET, POST',
					'callback'            => array( $this, 'process' ),
					'args'                => array(
						'action' => array(
							'description'       => esc_html__( 'Action.', 'zypento' ),
							'type'              => 'string',
							'validate_callback' => array( $zypento_objects['rest_aux'], 'validate_string' ),
							'sanitize_callback' => array( $zypento_objects['rest_aux'], 'sanitize_string' ),
							'required'          => true,
							'default'           => '',
						),
						'value'  => array(
							'description'       => esc_html__( 'Value.', 'zypento' ),
							'type'              => 'string',
							'validate_callback' => array( $zypento_objects['rest_aux'], 'validate_string' ),
							'sanitize_callback' => array( $zypento_objects['rest_aux'], 'sanitize_string' ),
							'required'          => false,
							'default'           => '',
						),
						'nonce'  => array(
							'description'       => esc_html__( 'Nonce.', 'zypento' ),
							'type'              => 'bool',
							'sanitize_callback' => function( $value ) {
								return (bool) $value;
							},
							'validate_callback' => function( $value ) {
								return wp_verify_nonce( $value, 'zypento' );
							},
							'required'          => true,
							'default'           => false,
						),
					),
					'permission_callback' => '',
				),
				// Register our schema callback.
				'schema' => array( $zypento_objects['rest_aux'], 'get_schema' ),

			)
		);

	}

	/**
	 * Get user details.
	 *
	 * @param WP_REST_Request $request Current request.
	 */
	public function process( $request ) {

		global $zypento_objects;

		$data['result']         = false;
		$data['data']           = array();
		$data['data']['error']  = array();
		$data['data']['reason'] = '';

		$data['data']['data'] = array();

		$nonce  = '';
		$action = '';
		$value  = '';

		if ( isset( $request['nonce'] ) ) {
			$nonce = $request['nonce'];
		}

		if ( isset( $request['action'] ) ) {
			$action = $request['action'];
		}

		if ( $nonce ) {

			if ( 'enabled-features' === $action ) {

				$data = array_merge( $data, $this->enabled_features( $request, $data ) );

			}
		} else {

			$data['data']['reason'] = esc_html__( 'unAuthorized', 'zypento' );

		}

		$response = $zypento_objects['rest_aux']->prepare( $data, $request );

		// Return all of our post response data.
		return $response;

	}

	/**
	 * Set enabled features.
	 *
	 * @param WP_REST_Request $request Current request.
	 * @param Array           $data Data to manipulate and output.
	 */
	public function enabled_features( $request, $data ) {

		$value = '';

		$data['data']['error']['reason'] = esc_html__( 'Something went wrong, Please try again.', 'zypento' );

		if ( isset( $request['value'] ) ) {
			$value = $request['value'];
		}

		$data['data']['aux']['$value'] = $value;

		if ( $value && current_user_can( 'administrator' ) ) {

			$value = sanitize_text_field( $value );
			$value = json_decode( wp_unslash( $value ), true );

			update_option( 'zypento_enabled_features', array() );

			$update = update_option( 'zypento_enabled_features', $value );

			if ( $update ) {
				$data['result'] = true;
			}
		}

		return $data;

	}

}
