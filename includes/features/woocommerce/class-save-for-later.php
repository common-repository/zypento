<?php
/**
 * The file that defines the sale countdown timer class
 *
 * @link       http://sproutient.com
 * @since      1.0.0
 *
 * @package    zypento
 * @subpackage zypento/includes
 */

namespace Zypento;

/**
 * The sale countdown timer class.
 *
 * @since      1.0.0
 * @package    zypento
 * @subpackage zypento/includes
 * @author     Sproutient <dev@sproutient.com>
 */
class Save_For_Later {

	/**
	 * Initialize save for later object.
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

		add_action( 'woocommerce_after_cart_table', array( $this, 'display' ) );
		add_action( 'woocommerce_cart_is_empty', array( $this, 'display' ) );
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'admin_init', array( $this, 'add_caps' ) );
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );

	}

	/**
	 * Add JS variables.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @param string $variable Current gutenberg status.
	 */
	public function admin_variables( $variable ) {

		return $variable;

	}

	/**
	 * Add Public JS variables.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @param string $variables Current gutenberg status.
	 */
	public function public_variables( $variables ) {

		if ( ! array_key_exists( 'api', $variables ) ) {

			$variables['api'] = array();

		}

		if ( ! array_key_exists( 'woo', $variables['api'] ) ) {

			$variables['api']['woo'] = array();

		}

		$variables['api']['woo']['saveLater'] = get_rest_url( null, 'zypento/v1/add-save-for-later' );

		if ( ! array_key_exists( 'features', $variables ) ) {

			$variables['features'] = array();

		}

		$variables['features']['saveLater'] = '';

		return $variables;

	}

	/**
	 * Display saved for later products.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function display() {

		$user_id = get_current_user_id();

		if ( 0 === $user_id ) {
			return;
		}

		?>

		<div class="zypento-save-for-later-display-container">

			<div data-zypento-type="" class="zypento-save-for-later-message">
				<p class="zypento-save-for-later-message-content"></p>
			</div>

			<div class="zypento-save-for-later-display">
				<h3 class="zypento-save-for-later-heading"><?php esc_html_e( 'Saved for Later', 'zypento' ); ?></h3>
				<div class="zypento-save-for-later-content">

					<div class="zypento-save-for-later-belt">
						<?php echo wp_kses_post( $this->display_saved_products() ); ?>
					</div>

				</div>
				<div class="zypento-save-for-later-overlay"></div>
				<span data-zyp-enabled="no" data-zyp-button-type="next" class="zypento-save-for-later-display-button"></span>
				<span data-zyp-enabled="no" data-zyp-button-type="prev" class="zypento-save-for-later-display-button"></span>
			</div>

		</div>

		<?php

	}

	/**
	 * Register post type.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function register_post_type() {

		$labels = array(
			'name'                  => esc_html_x( 'Save Later', 'Post type general name', 'zypento' ),
			'singular_name'         => esc_html_x( 'Save Later', 'Post type singular name', 'zypento' ),
			'menu_name'             => esc_html_x( 'Save Later', 'Admin Menu text', 'zypento' ),
			'name_admin_bar'        => esc_html_x( 'Save Later', 'Add New on Toolbar', 'zypento' ),
			'add_new'               => esc_html__( 'Add New Save Later', 'zypento' ),
			'add_new_item'          => esc_html__( 'Add New Save Later', 'zypento' ),
			'new_item'              => esc_html__( 'New Save Later', 'zypento' ),
			'edit_item'             => esc_html__( 'Edit Save Later', 'zypento' ),
			'view_item'             => esc_html__( 'View Save Later', 'zypento' ),
			'all_items'             => esc_html__( 'All Save Later', 'zypento' ),
			'search_items'          => esc_html__( 'Search Save Later', 'zypento' ),
			'parent_item_colon'     => esc_html__( 'Parent Save Later:', 'zypento' ),
			'not_found'             => esc_html__( 'No Save Later found.', 'zypento' ),
			'not_found_in_trash'    => esc_html__( 'No Save Later found in Trash.', 'zypento' ),
			'featured_image'        => esc_html_x( 'Save Later Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'zypento' ),
			'set_featured_image'    => esc_html_x( 'Set Save Later image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'zypento' ),
			'remove_featured_image' => esc_html_x( 'Remove Save Later image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'zypento' ),
			'use_featured_image'    => esc_html_x( 'Use as Save Later image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'zypento' ),
			'archives'              => esc_html_x( 'Save Later archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'zypento' ),
			'insert_into_item'      => esc_html_x( 'Insert into Save Later', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'zypento' ),
			'uploaded_to_this_item' => esc_html_x( 'Uploaded to this Save Later', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'zypento' ),
			'filter_items_list'     => esc_html_x( 'Filter Save Later', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'zypento' ),
			'items_list_navigation' => esc_html_x( 'Save Later navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Save Later navigation”. Added in 4.4', 'zypento' ),
			'items_list'            => esc_html_x( 'Save Later', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'zypento' ),
		);

		$args = array(

			'labels'              => $labels,
			'public'              => false,
			'show_ui'             => false,
			'show_in_menu'        => false,
			'query_var'           => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => false,
			'rewrite'             => array( 'slug' => 'zyp-save-later' ),
			'has_archive'         => true,
			'hierarchical'        => true,
			'menu_position'       => null,
			'supports'            => array( 'title' ),
			'show_in_rest'        => false,
			'capability_type'     => 'post',
			'capabilities'        => array(
				'edit_post'              => 'edit_zyp_save_later',
				'read_post'              => 'read_zyp_save_later',
				'delete_post'            => 'delete_zyp_save_later',
				'create_post'            => 'create_zyp_save_later',
				'delete_others_posts'    => 'delete_others_zyp_save_later',
				'delete_private_posts'   => 'delete_private_zyp_save_later',
				'delete_published_posts' => 'delete_published_zyp_save_later',
				'edit_posts'             => 'edit_zyp_save_later',
				'edit_others_posts'      => 'edit_others_zyp_save_later',
				'edit_private_posts'     => 'edit_private_zyp_save_later',
				'edit_published_posts'   => 'edit_published_zyp_save_later',
				'publish_posts'          => 'publish_zyp_save_later',
				'read_private_posts'     => 'read_private_zyp_save_later',
			),
			'taxonomies'          => array(),
			'map_meta_cap'        => false,
		);

		register_post_type( 'zyp-save-later', $args );

	}

	/**
	 * Give admins and listing managers ability to edit listing's.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function add_caps() {

		$admins = get_role( 'administrator' );

		$admins->add_cap( 'edit_zyp_save_later' );
		$admins->add_cap( 'read_zyp_save_later' );
		$admins->add_cap( 'delete_zyp_save_later' );
		$admins->add_cap( 'create_zyp_save_later' );
		$admins->add_cap( 'delete_zyp_save_later' );
		$admins->add_cap( 'delete_others_zyp_save_later' );
		$admins->add_cap( 'delete_private_zyp_save_later' );
		$admins->add_cap( 'delete_published_zyp_save_later' );
		$admins->add_cap( 'edit_zyp_save_later' );
		$admins->add_cap( 'edit_others_zyp_save_later' );
		$admins->add_cap( 'edit_private_zyp_save_later' );
		$admins->add_cap( 'edit_published_zyp_save_later' );
		$admins->add_cap( 'publish_zyp_save_later' );
		$admins->add_cap( 'read_private_zyp_save_later' );

	}

	/**
	 * Register REST Routes.
	 */
	public function register_routes() {

		global $zypento_namespace, $zypento_objects;

		register_rest_route(
			$zypento_namespace,
			'/add-save-for-later',
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

			if ( 'add' === $action ) {

				$data = array_merge( $data, $this->add( $request, $data ) );

			}

			if ( 'move' === $action ) {

				$data = array_merge( $data, $this->move( $request, $data ) );

			}

			if ( 'remove' === $action ) {

				$data = array_merge( $data, $this->remove( $request, $data ) );

			}
		} else {

			$data['data']['reason'] = esc_html__( 'unAuthorized', 'zypento' );

		}

		$response = $zypento_objects['rest_aux']->prepare( $data, $request );

		// Return all of our post response data.
		return $response;

	}

	/**
	 * Fetch initial Wishlists.
	 *
	 * @param WP_REST_Request $request Current request.
	 * @param Array           $data Data to manipulate and output.
	 */
	public function add( $request, $data ) {

		// Load cart functions which are loaded only on the front-end.
		include_once WC_ABSPATH . 'includes/wc-cart-functions.php';
		include_once WC_ABSPATH . 'includes/class-wc-cart.php';

		if ( is_null( \WC()->cart ) ) {
			wc_load_cart();
		}

		$value   = '';
		$product = '';
		$email   = '';
		$name    = '';
		$var_id  = 0;

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

			if ( is_array( $value ) && array_key_exists( 'key', $value ) && $value['key'] ) {
				$key          = sanitize_text_field( $value['key'] );
				$cart         = \WC()->cart->get_cart();
				$product_data = \WC()->cart->get_cart_item( $key );
				$var_id       = $product_data['variation_id'];
			} else {
				$data['data']['error']['product'] = esc_html__( 'key is required.', 'zypento' );
			}

			if ( $var_id ) {
				$product = $var_id;
			}

			if ( ! empty( $product ) && $user ) {

				$args = array(
					'post_type'   => 'zyp-save-later',
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

				if ( 0 < count( $posts ) ) {
					$data['result'] = true;
					return $data;
				}

				$post_data = array(
					'post_title'   => 'save later for ' . $product . ' - ' . $user,
					'post_content' => '',
					'post_type'    => 'zyp-save-later',
					'post_status'  => 'publish',
					'meta_input'   => array(
						'zyp_product' => esc_html( $product ),
						'zyp_user'    => $user,
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
	 * Fetch initial Wishlists.
	 *
	 * @param WP_REST_Request $request Current request.
	 * @param Array           $data Data to manipulate and output.
	 */
	public function move( $request, $data ) {

		$value   = '';
		$id      = '';
		$product = '';

		$data['data']['error']['reason'] = esc_html__( 'Something went wrong, Please try again.', 'zypento' );

		if ( isset( $request['value'] ) ) {
			$value = $request['value'];
		}

		$user = (int) get_current_user_id();

		$data['data']['aux']['$value'] = $value;

		if ( $value ) {

			$value = json_decode( wp_unslash( $value ), true );

			if ( is_array( $value ) && array_key_exists( 'id', $value ) && $value['id'] ) {
				$id = (int) sanitize_text_field( $value['id'] );
			} else {
				$data['data']['error']['id'] = esc_html__( 'Id is required.', 'zypento' );
			}

			if ( is_array( $value ) && array_key_exists( 'product', $value ) && $value['product'] ) {
				$product = (int) sanitize_text_field( $value['product'] );
			} else {
				$data['data']['error']['product'] = esc_html__( 'Product is required.', 'zypento' );
			}

			if ( ! empty( $id ) && $user && ! empty( $product ) ) {

				$auth_user = get_post_meta( $id, 'zyp_user', true );

				if ( $user === (int) $auth_user ) {

					// Load cart functions which are loaded only on the front-end.
					include_once WC_ABSPATH . 'includes/wc-cart-functions.php';
					include_once WC_ABSPATH . 'includes/class-wc-cart.php';

					if ( is_null( \WC()->cart ) ) {
						wc_load_cart();
					}

					$product_details = wc_get_product( $product );

					$parent_id = $product_details->get_parent_id();

					if ( $parent_id ) {

						$attributes = $product_details->get_attributes();

						$arr = array();

						if ( is_array( $attributes ) ) {

							foreach ( $attributes as $key => $value ) {
								$arr[ esc_html( $key ) ] = esc_html( $value );
							}
						}

						$cart = \WC()->cart->get_cart();
						\WC()->cart->add_to_cart( $product_details->get_parent_id(), 1, $product, $arr, null );

						$data['result'] = true;

					}

					if ( ! $parent_id ) {

						$cart = \WC()->cart->get_cart();
						\WC()->cart->add_to_cart( $product, 1 );
						$data['result'] = true;

					}

					$delete = wp_delete_post( $id, true );

				}

				if ( $user !== (int) $auth_user ) {
					$data['data']['error']['reason'] = esc_html__( 'UnAuthorized.', 'zypento' );
				}
			}
		}

		return $data;

	}

	/**
	 * Fetch initial Wishlists.
	 *
	 * @param WP_REST_Request $request Current request.
	 * @param Array           $data Data to manipulate and output.
	 */
	public function remove( $request, $data ) {

		$value = '';
		$id    = '';
		$email = '';

		$data['data']['error']['reason'] = esc_html__( 'Something went wrong, Please try again.', 'zypento' );

		if ( isset( $request['value'] ) ) {
			$value = $request['value'];
		}

		$user = (int) get_current_user_id();

		$data['data']['aux']['$value'] = $value;

		if ( $value ) {

			$value = json_decode( wp_unslash( $value ), true );

			if ( is_array( $value ) && array_key_exists( 'id', $value ) && $value['id'] ) {
				$id = (int) sanitize_text_field( $value['id'] );
			} else {
				$data['data']['error']['id'] = esc_html__( 'Id is required.', 'zypento' );
			}

			if ( ! empty( $id ) && $user ) {

				$auth_user = get_post_meta( $id, 'zyp_user', true );

				if ( $user === (int) $auth_user ) {
					$delete = wp_delete_post( $id, true );
				}

				if ( $user !== (int) $auth_user ) {
					$data['data']['error']['reason'] = esc_html__( 'UnAuthorized.', 'zypento' );
				}

				if ( $delete ) {
					$data['result'] = true;
				}
			}
		}

		return $data;

	}

	/**
	 * Display countdown timer.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function display_saved_products() {

		$html = '';

		$products = $this->fetch_saved_products();

		if ( ! is_array( $products ) || empty( $products ) ) {
			return $html;
		}

		$count = 0;

		$html .= '<div class="zypento-save-for-later-panel">';

		foreach ( $products as $product ) {

			if ( 0 !== $count && 0 === $count % 4 ) {
				$html .= '</div>';
				$html .= '<div class="zypento-save-for-later-panel">';
			}

			$html .= $this->render_product_details( $product );

			$count++;

		}

		$html .= '</div>';

		return $html;

	}

	/**
	 * Display countdown timer.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function fetch_saved_products() {

		$posts = array();

		$user = (int) get_current_user_id();

		if ( ! $user ) {
			return $posts;
		}

		$args = array(
			'post_type'      => 'zyp-save-later',
			'post_status'    => 'publish',
			'fields'         => 'ids',
			'posts_per_page' => -1,
			'meta_query'     => array( // phpcs:ignore slow query ok
				array(
					'key'     => 'zyp_user',
					'value'   => $user,
					'compare' => '=',
				),
			),
		);

		$posts = get_posts( $args );

		return $posts;

	}

	/**
	 * Display countdown timer.
	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 * @param Int $id Product ID.
	 */
	public function render_product_details( $id ) {

		$html = '';

		$product_id = get_post_meta( $id, 'zyp_product', true );
		$product    = wc_get_product( $product_id );

		if ( ! $product ) {
			return $html;
		}

		$image = $product->get_image();
		$name  = $product->get_name();
		$link  = get_permalink( $product_id );
		$cta   = __( 'Move to Cart', 'zypento' );

		$html = "
            <div data-zyp-product-id=\"{$product_id}\" data-zyp-id=\"{$id}\" class=\"zypento-saved-later-item\">
                {$image}
                <a href=\"{$link}\">{$name}</a>
                <p>
                    <span class=\"zypento-saved-later-move\">{$cta}</span>
                    <span class=\"zypento-saved-later-delete\"></span>
                </p>
            </div>
        ";

		return $html;

	}

}
