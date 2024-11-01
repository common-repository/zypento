<?php
/**
 * Admin page.
 *
 * @link       http://sproutient.com
 * @since      1.0.0
 *
 * @package    zypento
 * @subpackage zypento/includes
 */

	$zyp_features = array( 'stock_notifier', 'sale_countdown_timer', 'save_for_later' );

	$zypento_options = get_option( 'zypento_enabled_features', array() );

foreach ( $zyp_features as $feature ) {
	if ( ! array_key_exists( $feature, $zypento_options ) ) {
		$zypento_options[ $feature ] = 'yes';
	}
}

?>
<div class="zypento-admin-settings-container">

	<h3 class="zypento-admin-settings-header"><?php esc_html_e( 'Zypento Settings', 'zypento' ); ?></h3>

	<div class="zypento-admin-settings">

		<div class="zypento-admin-settings-overlay">
			<p><span class="zypento-admin-settings-spinner"></span></p>
			<p><?php esc_html_e( 'Please wait...', 'zypento' ); ?></p>
		</div>

		<div class="zypento-admin-settings-action">
			<span class="zypento-admin-settings-action-button"><?php esc_html_e( 'Save', 'zypento' ); ?></span>
		</div>

		<p data-zypento-type="" class="zypento-admin-settings-message"></p>

		<div class="zypento-admin-settings-content">

			<div class="zypento-admin-setting-container">
				<div class="zypento-admin-setting-content">
					<h4><?php esc_html_e( 'Back In Stock Notifications', 'zypento' ); ?></h4>
				</div>
				<div class="zypento-admin-setting-action">
					<span data-zyp-setting="stock-notifier" data-zyp-status="<?php echo esc_attr( $zypento_options['stock_notifier'] ); ?>" class="zypento-admin-setting-action-toggle">
						<span></span>
					</span>
				</div>
			</div>

			<div class="zypento-admin-setting-container">
				<div class="zypento-admin-setting-content">
					<h4><?php esc_html_e( 'Sale Countdown Timer', 'zypento' ); ?></h4>
				</div>
				<div class="zypento-admin-setting-action">
					<span data-zyp-setting="sale-countdown-timer" data-zyp-status="<?php echo esc_attr( $zypento_options['sale_countdown_timer'] ); ?>" class="zypento-admin-setting-action-toggle">
						<span></span>
					</span>
				</div>
			</div>

			<div class="zypento-admin-setting-container">
				<div class="zypento-admin-setting-content">
					<h4><?php esc_html_e( 'Save for Later', 'zypento' ); ?></h4>
				</div>
				<div class="zypento-admin-setting-action">
					<span data-zyp-setting="save-for-later" data-zyp-status="<?php echo esc_attr( $zypento_options['save_for_later'] ); ?>" class="zypento-admin-setting-action-toggle">
						<span></span>
					</span>
				</div>
			</div>

		</div>

	</div>

</div>
