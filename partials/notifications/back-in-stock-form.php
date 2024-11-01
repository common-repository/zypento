<?php
/**
 * Notification form.
 *
 * @link       http://sproutient.com
 * @since      1.0.0
 *
 * @package    zypento
 * @subpackage zypento/includes
 */

	global $product;

if ( ! $product->managing_stock() ) {
	return;
}

	$zypento_user_id = (int) get_current_user_id();

	$zypento_user_logged = '';
if ( 0 !== $zypento_user_id ) {
	$zypento_user_logged = 'yes';
}

	$zypento_product_id = '';

	$zypento_notify_added = '';

	$zypento_style = '';

	$zypento_label = __( 'Notify Me', 'zypento' );

	$zypento_variants_added = array();

if ( $product->is_type( 'variable' ) ) {
	$zypento_style = 'display:none;';
	foreach ( $product->get_children( false ) as $child_id ) {
		$variation                                      = wc_get_product( $child_id );
		$zypento_variants_added[ $variation->get_id() ] = \Zypento\Stock_Notifier::get_status( $variation->get_id() );
	}
}

if ( ! $product->is_type( 'variable' ) ) {
	$zypento_product_id   = $product->get_id();
	$zypento_stock_number = $product->get_stock_quantity();
	$zypento_notify_added = \Zypento\Stock_Notifier::get_status( $product->get_id() );
	if ( 0 === $zypento_stock_number ) {
		$zypento_style = 'display:block;';
	}
	if ( 'enabled' === $zypento_notify_added ) {
		$zypento_label = __( 'Back InStock Notification Enabled', 'zypento' );
	}
}

?>

<div data-notify-member="<?php echo esc_attr( $zypento_user_logged ); ?>" data-notify-id="<?php echo esc_attr( $zypento_product_id ); ?>"  data-zypento-notify="<?php echo esc_attr( wp_json_encode( $zypento_variants_added ) ); ?>" style="<?php echo esc_attr( $zypento_style ); ?>" id="zypento-notify-product" class="zypento-notify-product-container">

	<?php if ( 0 !== $zypento_user_id ) : ?>

	<p class="zypento-notify-product-button">
		<span class="zypento-notify-product-button-text" data-notify-spinner="" data-notify-status="<?php echo esc_attr( $zypento_notify_added ); ?>"><?php echo esc_html( $zypento_label ); ?></span>
		<span class="zypento-notify-product-button-message"></span>
	</p>

	<?php endif; ?>

	<?php if ( 0 === $zypento_user_id ) : ?>

	<div data-bg="yes" class="zypento-notify-product-form">

		<div class="zypento-notify-product-form-item">
			<span><?php esc_html_e( 'Name', 'zypento' ); ?></span>
			<p><input name="zypento-notify-name" type="text" value="" /></p>
		</div>

		<div class="zypento-notify-product-form-item">
			<span><?php esc_html_e( 'Email', 'zypento' ); ?></span>
			<p><input name="zypento-notify-email" type="text" value="" /></p>
		</div>

		<p data-type="" class="zypento-notify-product-form-message"></p>

		<span data-notified="" data-notify-spinner="" class="zypento-notify-product-form-button"><?php esc_html_e( 'Notify Me', 'zypento' ); ?></span>

	</div>

	<?php endif; ?>

</div>
