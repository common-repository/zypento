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
class Sale_Countdown_Timer {

	/**
	 * Initialize sale countdown timer object.
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

		add_action( 'woocommerce_before_add_to_cart_form', array( $this, 'display' ) );
		add_action( 'woocommerce_products_general_settings', array( $this, 'add_setting' ) );

		add_filter( 'zypento_js_variables', array( $this, 'public_variables' ) );

	}

	/**
	 * Add Public JS variables.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @param string $variables Public JS variable.
	 */
	public function public_variables( $variables ) {

		if ( ! array_key_exists( 'features', $variables ) ) {

			$variables['features'] = array();

		}

		$variables['features']['saleCountdownTimer'] = '';
		return $variables;

	}

	/**
	 * Add JS variables.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @param string $settings Admin JS variable.
	 */
	public function add_setting( $settings ) {

		$variable['api']['admin']['addNotification'] = get_rest_url( null, 'zypento/v1/add-notification' );
		return $variable;

	}

	/**
	 * Display countdown timer.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function display() {

		global $post, $product;
		$product_id = $product->get_id();

		if ( ! $product->is_on_sale() ) {
			return;
		}

		if ( $product->is_type( 'variable' ) ) {
			$this->timer_for_variable_product( $product );
		}

		if ( ! $product->is_type( 'variable' ) ) {

			$this->timer_for_simple_product( $product );

		}

	}

	/**
	 * Display countdown timer for simple product.
	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 * @param string $product Current product.
	 */
	public function timer_for_simple_product( $product ) {

		if ( ! $product->is_on_sale() ) {
			return;
		}

		$sales_price_to = $product->get_date_on_sale_to();

		if ( ! $sales_price_to ) {
			return;
		}

		?>

		<div data-zypento-type="simple" id="zypento-variable-timer">
			<div class="zypento-variable-timer-content">
				<p class="zypento-variable-timer-heading"><?php esc_html_e( 'Sale ends in', 'zypento' ); ?></p>
				<p id="zypento-variable-timer-actual" data-variation-sale="<?php echo esc_attr( $sales_price_to->getTimestamp() ); ?>"></p>
			</div>
		</div>

		<?php

	}

	/**
	 * Display countdown timer for variable product.
	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 * @param string $product Current product.
	 */
	public function timer_for_variable_product( $product ) {

		$zypento_style    = 'display:none;';
		$zypento_variants = array();
		foreach ( $product->get_children( false ) as $child_id ) {

			$variation = wc_get_product( $child_id );

			$zypento_variants[ $child_id ] = '';
			$sales_price_to                = $variation->get_date_on_sale_to();
			if ( $variation->is_on_sale() && ! is_null( $sales_price_to ) ) {

				$zypento_variants[ $child_id ] = $sales_price_to->getTimestamp();

			}
		}

		?>
		<div data-zypento-type="" style="<?php echo esc_attr( $zypento_style ); ?>" id="zypento-variable-timer">
			<div class="zypento-variable-timer-content">
				<p class="zypento-variable-timer-heading"><?php esc_html_e( 'Sale ends in', 'zypento' ); ?></p>
				<p id="zypento-variable-timer-actual" data-variation-sale="<?php echo esc_attr( wp_json_encode( $zypento_variants ) ); ?>"></p>
			</div>
		</div>
		<?php

	}

}
