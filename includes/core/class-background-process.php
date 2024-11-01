<?php
/**
 * The file that processes background tasks.
 *
 * @link       http://sproutient.com
 * @since      1.0.0
 *
 * @package    zypento
 * @subpackage zypento/includes
 */

namespace Zypento;

/**
 * The file that processes background tasks.
 *
 * @since      1.0.0
 * @package    zypento
 * @subpackage zypento/includes
 * @author     Sproutient <dev@sproutient.com>
 */
class Background_Process {

	/**
	 * Add hooks.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'admin_init', array( $this, 'add_caps' ) );
		add_action( 'wp_loaded', array( $this, 'process' ) );

		add_action( 'zyp_bg_process', 'Self::repeat' );
		add_filter( 'zypento_post_types', array( $this, 'add_post_type' ) );

	}

	/**
	 * Register post type.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function register_post_type() {

		$labels = array(
			'name'                  => esc_html_x( 'BG Task', 'Post type general name', 'zypento' ),
			'singular_name'         => esc_html_x( 'BG Task', 'Post type singular name', 'zypento' ),
			'menu_name'             => esc_html_x( 'BG Task', 'Admin Menu text', 'zypento' ),
			'name_admin_bar'        => esc_html_x( 'BG Task', 'Add New on Toolbar', 'zypento' ),
			'add_new'               => esc_html__( 'Add New BG Task', 'zypento' ),
			'add_new_item'          => esc_html__( 'Add New BG Task', 'zypento' ),
			'new_item'              => esc_html__( 'New BG Task', 'zypento' ),
			'edit_item'             => esc_html__( 'Edit BG Task', 'zypento' ),
			'view_item'             => esc_html__( 'View BG Task', 'zypento' ),
			'all_items'             => esc_html__( 'All BG Task', 'zypento' ),
			'search_items'          => esc_html__( 'Search BG Task', 'zypento' ),
			'parent_item_colon'     => esc_html__( 'Parent BG Task:', 'zypento' ),
			'not_found'             => esc_html__( 'No BG Task found.', 'zypento' ),
			'not_found_in_trash'    => esc_html__( 'No BG Task found in Trash.', 'zypento' ),
			'featured_image'        => esc_html_x( 'BG Task Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'zypento' ),
			'set_featured_image'    => esc_html_x( 'Set BG Task image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'zypento' ),
			'remove_featured_image' => esc_html_x( 'Remove BG Task image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'zypento' ),
			'use_featured_image'    => esc_html_x( 'Use as BG Task image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'zypento' ),
			'archives'              => esc_html_x( 'BG Task archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'zypento' ),
			'insert_into_item'      => esc_html_x( 'Insert into BG Task', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'zypento' ),
			'uploaded_to_this_item' => esc_html_x( 'Uploaded to this BG Task', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'zypento' ),
			'filter_items_list'     => esc_html_x( 'Filter BG Task', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'zypento' ),
			'items_list_navigation' => esc_html_x( 'BG Task navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”BG Task navigation”. Added in 4.4', 'zypento' ),
			'items_list'            => esc_html_x( 'BG Task', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'zypento' ),
		);

		$args = array(

			'labels'              => $labels,
			'public'              => false,
			'show_ui'             => false,
			'show_in_menu'        => false,
			'query_var'           => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => false,
			'rewrite'             => array( 'slug' => 'zyp-bg-task' ),
			'has_archive'         => true,
			'hierarchical'        => true,
			'menu_position'       => null,
			'supports'            => array( 'title' ),
			'show_in_rest'        => false,
			'capability_type'     => 'post',
			'capabilities'        => array(
				'edit_post'              => 'edit_zyp_bg_task',
				'read_post'              => 'read_zyp_bg_task',
				'delete_post'            => 'delete_zyp_bg_task',
				'create_post'            => 'create_zyp_bg_task',
				'delete_others_posts'    => 'delete_others_zyp_bg_task',
				'delete_private_posts'   => 'delete_private_zyp_bg_task',
				'delete_published_posts' => 'delete_published_zyp_bg_task',
				'edit_posts'             => 'edit_zyp_bg_task',
				'edit_others_posts'      => 'edit_others_zyp_bg_task',
				'edit_private_posts'     => 'edit_private_zyp_bg_task',
				'edit_published_posts'   => 'edit_published_zyp_bg_task',
				'publish_posts'          => 'publish_zyp_bg_task',
				'read_private_posts'     => 'read_private_zyp_bg_task',
			),
			'taxonomies'          => array(),
			'map_meta_cap'        => false,
		);

		register_post_type( 'zyp-bg-task', $args );

	}

	/**
	 * Add Post Type to post types array.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @param string $post_types Post type.
	 */
	public function add_post_type( $post_types ) {

		$post_types[] = 'zyp-bg-task';
		return $post_types;

	}

	/**
	 * Give admins and listing managers ability to edit listing's.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function add_caps() {

		$admins = get_role( 'administrator' );

		$admins->add_cap( 'edit_zyp_bg_task' );
		$admins->add_cap( 'read_zyp_bg_task' );
		$admins->add_cap( 'delete_zyp_bg_task' );
		$admins->add_cap( 'create_zyp_bg_task' );
		$admins->add_cap( 'delete_zyp_bg_task' );
		$admins->add_cap( 'delete_others_zyp_bg_task' );
		$admins->add_cap( 'delete_private_zyp_bg_task' );
		$admins->add_cap( 'delete_published_zyp_bg_task' );
		$admins->add_cap( 'edit_zyp_bg_task' );
		$admins->add_cap( 'edit_others_zyp_bg_task' );
		$admins->add_cap( 'edit_private_zyp_bg_task' );
		$admins->add_cap( 'edit_published_zyp_bg_task' );
		$admins->add_cap( 'publish_zyp_bg_task' );
		$admins->add_cap( 'read_private_zyp_bg_task' );

	}

	/**
	 * Process BG tasks.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function process() {

		if ( ! isset( $_REQUEST['zyp-bg-process'] ) || 'yes' !== $_REQUEST['zyp-bg-process'] ) {
			return;
		}

		$processing      = get_option( 'zyp_bg_processing', 'no' );
		$processing_time = (int) get_option( 'zyp_bg_processing_time', 100 );

		if ( ! ( 'no' === $processing || ( 'yes' === $processing && ( $processing_time + 600 < time() ) ) ) ) {
			exit;
		}

		update_option( 'zyp_bg_processing', 'yes' );
		update_option( 'zyp_bg_processing_time', time() );

		$tasks = $this->fetch_tasks();

		if ( ! ( $tasks && ! empty( $tasks ) && is_array( $tasks ) ) ) {

			update_option( 'zyp_bg_processing', 'no' );

			exit;

		}

		$task_id = $tasks[0];

		if ( ! $task_id ) {
			exit;
		}

		$task_type = get_post_meta( $task_id, 'zyp_task_type', true );

		if ( 'back-in-stock-notification' === $task_type ) {

			$this->back_in_stock_notification( $task_id );

		}

		update_option( 'zyp_bg_processing', 'no' );

		$this->should_repeat();

		exit;

	}

	/**
	 * Fetch tasks.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	private function fetch_tasks() {

		$args = array(
			'post_type'      => 'zyp-bg-task',
			'post_status'    => 'publish',
			'fields'         => 'ids',
			'posts_per_page' => 1,
		);

		return get_posts( $args );

	}

	/**
	 * Check if any tasks left.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	private function should_repeat() {

		$args = array(
			'post_type'      => 'zyp-bg-task',
			'post_status'    => 'publish',
			'fields'         => 'ids',
			'posts_per_page' => 1,
		);

		if ( 0 < count( get_posts( $args ) ) ) {

			$this->repeat();

		}
	}

	/**
	 * Repeat the loop.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public static function repeat() {

		$url = site_url( '/', 'https' );

		$response = wp_remote_request(
			$url,
			array(
				'method'   => 'POST',
				'blocking' => false,
				'body'     => array(
					'zyp-bg-process' => 'yes',
				),
			)
		);

	}

	/**
	 * Fetch notification tasks.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param Int $task_id Notification task ID.
	 */
	public function back_in_stock_notification( $task_id ) {

		$zyp_product = get_post_meta( $task_id, 'zyp_product', true );

		if ( ! $zyp_product ) {
			wp_delete_post( $task_id );
			return;
		}

		$args = array(
			'post_type'      => 'zyp-notify',
			'post_status'    => 'publish',
			'fields'         => 'ids',
			'posts_per_page' => 2,
			'orderby'        => 'date',
			'order'          => 'ASC',
			'meta_query'     => array(
				array(
					'key'     => 'zyp_product',
					'value'   => $zyp_product,
					'compare' => '=',
				),
			),
		);

		$posts = get_posts( $args );

		if ( 0 === count( $posts ) ) {

			wp_delete_post( $task_id );
			return;

		}

		foreach ( $posts as $post ) {

			$this->process_back_in_stock_notification( $post );

		}

	}

	/**
	 * Process notification task.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param Int $post Notification task ID.
	 */
	public function process_back_in_stock_notification( $post ) {

		$zyp_user    = get_post_meta( $post, 'zyp_user', true );
		$zyp_email   = get_post_meta( $post, 'zyp_email', true );
		$zyp_name    = get_post_meta( $post, 'zyp_name', true );
		$zyp_product = get_post_meta( $post, 'zyp_product', true );

		if ( ( ! $zyp_user || empty( $zyp_user ) ) && ( ! $zyp_email || empty( $zyp_email ) ) && ( ! $zyp_product || empty( $zyp_product ) ) ) {

			wp_delete_post( $post );
			return;

		}

		if ( ! $zyp_email || empty( $zyp_email ) ) {

			$user_data = get_userdata( $zyp_user );

			$zyp_email = $user_data->user_email;
			$zyp_name  = $user_info->first_name . ' ' . $user_info->last_name;

		}

		$zyp_product_details = wc_get_product( $zyp_product );

		if ( $zyp_product_details->is_type( 'variable' ) ) {

			$variation_product = new \WC_Product_Variation( $zyp_product );
			$zyp_product_name  = $variation_product->get_name();
		}

		if ( ! $zyp_product_details->is_type( 'variable' ) ) {
			$zyp_product_name = $zyp_product_details->get_name();
		}

		$zyp_email_subject = sprintf( '%1$s %2$s', esc_html__( 'Back InStock notification from', 'zypento' ), esc_html( get_bloginfo( 'name' ) ) );

		$zyp_email_subject = apply_filters( 'zypento_stock_notifier_email_subject', $zyp_email_subject, $zyp_product, $zyp_product_details, $zyp_name, $zyp_email );

		$zyp_message = sprintf( '<p>%1$s</p><p>%2$s %3$s <a href="%5$s">%4$s</a></p><p>%6$s,</p><p>%7$s,</p>', $zyp_name, $zyp_product_name, esc_html__( 'is back in stock, please purchase it ', 'zypento' ), esc_html__( 'here', 'zypento' ), esc_url( get_permalink( $zyp_product ) ), esc_html__( 'Thank you', 'zypento' ), esc_html( get_bloginfo( 'name' ) ) );

		$zyp_email_content = "
        <div>{$zyp_message}</div>
        ";

		$zyp_email_content = apply_filters( 'zypento_stock_notifier_email_content', $zyp_email_content, $zyp_product, $zyp_product_details, $zyp_name, $zyp_email );

		$this->send_email( $zyp_email, $zyp_email_subject, $zyp_email_content );

		wp_delete_post( $post );

	}

	/**
	 * Send notification email.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param String $zyp_email Email.
	 * @param String $zyp_email_subject Subject.
	 * @param String $zyp_email_content Content.
	 */
	public function send_email( $zyp_email, $zyp_email_subject, $zyp_email_content ) {

		$send_email = apply_filters( 'zypento_stock_send_notifier_email', true, $zyp_email, $zyp_email_content );

		if ( ! $send_email ) {
			return;
		}

		Mail::send( $zyp_email, $zyp_email_subject, $zyp_email_content );

	}

}
