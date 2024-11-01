<?php
/**
 * The file that defines the stock notifier class
 *
 * @link       http://sproutient.com
 * @since      1.0.0
 *
 * @package    zypento
 * @subpackage zypento/includes
 */

namespace Zypento;

/**
 * The stock notifier class.
 *
 * @since      1.0.0
 * @package    zypento
 * @subpackage zypento/includes
 * @author     Sproutient <dev@sproutient.com>
 */
class Stock_Notifier {

	/**
	 * Initialize stock notifier object.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$plugin_path = trailingslashit( WP_PLUGIN_DIR ) . 'woocommerce/woocommerce.php';

		if (
			! in_array( $plugin_path, wp_get_active_and_valid_plugins(), true )
		) {
			return;
		}

		add_filter( 'zypento_admin_js_variables', array( $this, 'admin_variables' ) );
		add_filter( 'zypento_js_variables', array( $this, 'public_variables' ) );
		add_filter( 'zypento_post_types', array( $this, 'add_post_type' ) );

		add_action( 'woocommerce_product_meta_start', array( $this, 'form' ) );
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'admin_init', array( $this, 'add_caps' ) );
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
		add_action( 'woocommerce_product_set_stock', array( $this, 'stock_updated' ) );
		add_action( 'woocommerce_variation_set_stock', array( $this, 'stock_updated' ) );

	}

	/**
	 * Hooks.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function hooks() {

		return array(
			array(
				'type'     => 'action',
				'name'     => 'woocommerce_product_meta_start',
				'target'   => 'form',
				'priority' => 10,
				'args'     => 0,
			),
			array(
				'type'     => 'action',
				'name'     => 'init',
				'target'   => 'register_post_type',
				'priority' => 12,
				'args'     => 0,
			),
			array(
				'type'     => 'action',
				'name'     => 'admin_init',
				'target'   => 'add_caps',
				'priority' => 12,
				'args'     => 0,
			),
			array(
				'type'     => 'action',
				'name'     => 'rest_api_init',
				'target'   => 'register_routes',
				'priority' => 10,
				'args'     => 0,
			),
			array(
				'type'     => 'action',
				'name'     => 'woocommerce_product_set_stock',
				'target'   => 'stock_updated',
				'priority' => 10,
				'args'     => 1,
			),
			array(
				'type'     => 'action',
				'name'     => 'woocommerce_variation_set_stock',
				'target'   => 'stock_updated',
				'priority' => 10,
				'args'     => 1,
			),
		);

	}

	/**
	 * Register post type.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function register_post_type() {

		$labels = array(
			'name'                  => esc_html_x( 'Notify', 'Post type general name', 'zypento' ),
			'singular_name'         => esc_html_x( 'Notify', 'Post type singular name', 'zypento' ),
			'menu_name'             => esc_html_x( 'Notify', 'Admin Menu text', 'zypento' ),
			'name_admin_bar'        => esc_html_x( 'Notify', 'Add New on Toolbar', 'zypento' ),
			'add_new'               => esc_html__( 'Add New Notify', 'zypento' ),
			'add_new_item'          => esc_html__( 'Add New Notify', 'zypento' ),
			'new_item'              => esc_html__( 'New Notify', 'zypento' ),
			'edit_item'             => esc_html__( 'Edit Notify', 'zypento' ),
			'view_item'             => esc_html__( 'View Notify', 'zypento' ),
			'all_items'             => esc_html__( 'All Notify', 'zypento' ),
			'search_items'          => esc_html__( 'Search Notify', 'zypento' ),
			'parent_item_colon'     => esc_html__( 'Parent Notify:', 'zypento' ),
			'not_found'             => esc_html__( 'No Notify found.', 'zypento' ),
			'not_found_in_trash'    => esc_html__( 'No Notify found in Trash.', 'zypento' ),
			'featured_image'        => esc_html_x( 'Notify Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'zypento' ),
			'set_featured_image'    => esc_html_x( 'Set Notify image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'zypento' ),
			'remove_featured_image' => esc_html_x( 'Remove Notify image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'zypento' ),
			'use_featured_image'    => esc_html_x( 'Use as Notify image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'zypento' ),
			'archives'              => esc_html_x( 'Notify archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'zypento' ),
			'insert_into_item'      => esc_html_x( 'Insert into Notify', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'zypento' ),
			'uploaded_to_this_item' => esc_html_x( 'Uploaded to this Notify', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'zypento' ),
			'filter_items_list'     => esc_html_x( 'Filter Notify', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'zypento' ),
			'items_list_navigation' => esc_html_x( 'Notify navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Notify navigation”. Added in 4.4', 'zypento' ),
			'items_list'            => esc_html_x( 'Notify', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'zypento' ),
		);

		$args = array(

			'labels'              => $labels,
			'public'              => false,
			'show_ui'             => false,
			'show_in_menu'        => false,
			'query_var'           => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => false,
			'rewrite'             => array( 'slug' => 'notify' ),
			'has_archive'         => true,
			'hierarchical'        => true,
			'menu_position'       => null,
			'supports'            => array( 'title' ),
			'show_in_rest'        => false,
			'capability_type'     => 'post',
			'capabilities'        => array(
				'edit_post'              => 'edit_notify',
				'read_post'              => 'read_notify',
				'delete_post'            => 'delete_notify',
				'create_post'            => 'create_notify',
				'delete_others_posts'    => 'delete_others_notify',
				'delete_private_posts'   => 'delete_private_notify',
				'delete_published_posts' => 'delete_published_notify',
				'edit_posts'             => 'edit_notify',
				'edit_others_posts'      => 'edit_others_notify',
				'edit_private_posts'     => 'edit_private_notify',
				'edit_published_posts'   => 'edit_published_notify',
				'publish_posts'          => 'publish_notify',
				'read_private_posts'     => 'read_private_notify',
			),
			'taxonomies'          => array(),
			'map_meta_cap'        => false,
		);

		register_post_type( 'zyp-notify', $args );

	}

	/**
	 * Give admins and listing managers ability to edit listing's.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function add_caps() {

		$admins = get_role( 'administrator' );

		$admins->add_cap( 'edit_notify' );
		$admins->add_cap( 'read_notify' );
		$admins->add_cap( 'delete_notify' );
		$admins->add_cap( 'create_notify' );
		$admins->add_cap( 'delete_notify' );
		$admins->add_cap( 'delete_others_notify' );
		$admins->add_cap( 'delete_private_notify' );
		$admins->add_cap( 'delete_published_notify' );
		$admins->add_cap( 'edit_notify' );
		$admins->add_cap( 'edit_others_notify' );
		$admins->add_cap( 'edit_private_notify' );
		$admins->add_cap( 'edit_published_notify' );
		$admins->add_cap( 'publish_notify' );
		$admins->add_cap( 'read_private_notify' );

	}

	/**
	 * Add notification form.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function form() {

		include ZYPENTO_PLUGIN_PATH . 'partials/notifications/back-in-stock-form.php';

	}

	/**
	 * Add JS variables.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @param string $variable Admin JS variable.
	 */
	public function admin_variables( $variable ) {

		$variable['api']['admin']['addNotification'] = get_rest_url( null, 'zypento/v1/add-notification' );
		return $variable;

	}

	/**
	 * Add Public JS variables.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @param string $variables Public JS variable.
	 */
	public function public_variables( $variables ) {

		if ( ! array_key_exists( 'api', $variables ) ) {

			$variables['api'] = array();

		}

		if ( ! array_key_exists( 'woo', $variables['api'] ) ) {

			$variables['api']['woo'] = array();

		}

		$variables['api']['woo']['addNotification'] = get_rest_url( null, 'zypento/v1/add-notification' );

		if ( ! array_key_exists( 'features', $variables ) ) {

			$variables['features'] = array();

		}

		$variables['features']['stockNotifier'] = '';

		return $variables;

	}

	/**
	 * Add Post Type to post types array.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @param string $post_types Post type variable.
	 */
	public function add_post_type( $post_types ) {

		$post_types[] = 'zyp-notify';
		return $post_types;

	}

	/**
	 * Register REST Routes.
	 */
	public function register_routes() {

		global $zypento_namespace, $zypento_objects;

		register_rest_route(
			$zypento_namespace,
			'/add-notification',
			array(

				// Here we register the readable endpoint for collections.
				array(
					'methods'             => 'GET, POST',
					'callback'            => array( $this, 'process_notification' ),
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
	 * Process notification.
	 *
	 * @param WP_REST_Request $request Current request.
	 */
	public function process_notification( $request ) {

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

			if ( 'add' === $action ) {

				$data = array_merge( $data, $this->add_notification( $request, $data ) );

			}

			if ( 'remove' === $action ) {

				$data = array_merge( $data, $this->remove_notification( $request, $data ) );

			}
		} else {

			$data['data']['reason'] = esc_html__( 'unAuthorized', 'zypento' );

		}

		$response = $zypento_objects['rest_aux']->prepare( $data, $request );

		// Return all of our post response data.
		return $response;

	}

	/**
	 * Add notification.
	 *
	 * @param WP_REST_Request $request Current request.
	 * @param Array           $data Data to manipulate and output.
	 */
	public function add_notification( $request, $data ) {

		$value   = '';
		$product = '';
		$email   = '';
		$name    = '';

		$data['data']['error']['reason'] = esc_html__( 'Something went wrong, Please try again.', 'zypento' );

		if ( isset( $request['value'] ) ) {
			$value = $request['value'];
		}

		$user = (int) get_current_user_id();

		$data['data']['aux']['$value'] = $value;

		if ( $value ) {

			$value = json_decode( wp_unslash( $value ), true );

			if ( is_array( $value ) && array_key_exists( 'product', $value ) && $value['product'] ) {
				$product = (int) sanitize_text_field( $value['product'] );
			} else {
				$data['data']['error']['product'] = esc_html__( 'Product is required.', 'zypento' );
			}

			if ( is_array( $value ) && array_key_exists( 'email', $value ) && $value['email'] ) {
				$email = sanitize_text_field( $value['email'] );
				if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
					$email                           = '';
					$data['data']['error']['reason'] = esc_html__( 'Email is invalid.', 'zypento' );
				}
			} else {
				$data['data']['error']['reason'] = esc_html__( 'Email is required.', 'zypento' );
			}

			$data['data']['aux']['email'] = $email;

			if ( is_array( $value ) && array_key_exists( 'name', $value ) && $value['name'] ) {
				$name = sanitize_text_field( $value['name'] );
			}

			if ( ! empty( $product ) && ( $user || ! empty( $email ) ) ) {

				$args = array(
					'post_type'   => 'zyp-notify',
					'post_status' => 'publish',
					'fields'      => 'ids',
					'meta_query'  => array( // phpcs:ignore slow query ok
						'relation' => 'AND',
						array(
							'key'     => 'zyp_product',
							'value'   => $product,
							'compare' => '=',
						),
						array(
							'key'     => 'zyp_email',
							'value'   => $email,
							'compare' => '=',
						),
					),
				);

				$posts = get_posts( $args );

				if ( 0 < count( $posts ) ) {
					$data['result'] = true;
					return $data;
				}

				$post_data = array(
					'post_title'   => 'Notify',
					'post_content' => '',
					'post_type'    => 'zyp-notify',
					'post_status'  => 'publish',
					'meta_input'   => array(
						'zyp_product' => esc_html( $product ),
						'zyp_user'    => $user,
						'zyp_email'   => $email,
						'zyp_name'    => $name,
					),
				);

				$post_id = wp_insert_post( $post_data );

				if ( $post_id ) {

					$data['result'] = true;

				}
			}
		}

		return $data;

	}

	/**
	 * Remove notification.
	 *
	 * @param WP_REST_Request $request Current request.
	 * @param Array           $data Data to manipulate and output.
	 */
	public function remove_notification( $request, $data ) {

		$value   = '';
		$product = '';
		$email   = '';

		$data['data']['error']['reason'] = esc_html__( 'Something went wrong, Please try again.', 'zypento' );

		if ( isset( $request['value'] ) ) {
			$value = $request['value'];
		}

		$user = (int) get_current_user_id();

		$data['data']['aux']['$value'] = $value;

		if ( $value ) {

			$value = json_decode( wp_unslash( $value ), true );

			if ( is_array( $value ) && array_key_exists( 'product', $value ) && $value['product'] ) {
				$product = (int) sanitize_text_field( $value['product'] );
			} else {
				$data['data']['error']['product'] = esc_html__( 'Product is required.', 'zypento' );
			}

			if ( is_array( $value ) && array_key_exists( 'email', $value ) && $value['email'] ) {
				$email = sanitize_text_field( $value['email'] );
			}

			if ( ! empty( $product ) && ( $user || ! empty( $email ) ) ) {

				$args = array(
					'post_type'   => 'zyp-notify',
					'post_status' => 'publish',
					'fields'      => 'ids',
					'meta_query'  => array( // phpcs:ignore slow query ok
						'relation' => 'AND',
						array(
							'key'     => 'zyp_product',
							'value'   => $product,
							'compare' => '=',
						),
						array(
							'key'     => 'zyp_user',
							'value'   => $user,
							'compare' => '=',
						),
					),
				);

				$posts = get_posts( $args );

				foreach ( $posts as $post ) {
					wp_delete_post( $post, true );
				}

				$data['result'] = true;

			}
		}

		return $data;

	}

	/**
	 * Get user status.
	 *
	 * @param string $product Product.
	 * @param string $user User.
	 */
	public static function get_status( $product, $user = '' ) {

		$status = '';

		if ( '' === $user ) {
			$user = get_current_user_id();
		}

		if ( 0 === $user ) {
			return $status;
		}

		$args = array(
			'post_type'   => 'zyp-notify',
			'post_status' => 'publish',
			'fields'      => 'ids',
			'meta_query'  => array( // phpcs:ignore slow query ok
				'relation' => 'AND',
				array(
					'key'     => 'zyp_product',
					'value'   => $product,
					'compare' => '=',
				),
				array(
					'key'     => 'zyp_user',
					'value'   => $user,
					'compare' => '=',
				),
			),
		);

		$posts = get_posts( $args );

		if ( count( $posts ) ) {
			$status = 'enabled';
		}

		return $status;

	}

	/**
	 * Log notification for background processor to process.
	 *
	 * @param Object $product Product.
	 */
	public static function stock_updated( $product ) {

		$posts = 0;

		$product_id = $product->get_id();

		if (
			0 < $product->get_stock_quantity()
		) {

			$args = array(
				'post_type'   => 'zyp-notify',
				'post_status' => 'publish',
				'fields'      => 'ids',
				'meta_query'  => array( // phpcs:ignore slow query ok
					array(
						'key'     => 'zyp_product',
						'value'   => $product_id,
						'compare' => '=',
					),
				),
			);

			$posts = count( get_posts( $args ) );

		}

		if ( 0 === $posts ) {
			return;
		}

		$post_data = array(
			'post_title'   => 'BG task for ' . $product_id,
			'post_content' => '',
			'post_type'    => 'zyp-bg-task',
			'post_status'  => 'publish',
			'meta_input'   => array(
				'zyp_product'   => $product_id,
				'zyp_task_type' => 'back-in-stock-notification',
			),
		);

		$post_id = wp_insert_post( $post_data );

		Background_Process::repeat();

	}

}
