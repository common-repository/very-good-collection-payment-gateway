<?php
/*
	Plugin Name:			Very Good Collection Payment Gateway for WooCommerce
	Plugin URI: 			http://verygoodcollection.com/
	Description:            Very Good Collection payment gateway for WooCommerce
	Version:                1.1.2
	Author: 				Very Good Collection
	License:        		GPL-2.0+
	License URI:    		http://www.gnu.org/licenses/gpl-2.0.txt
	WC requires at least:   3.0.0
	WC tested up to:        5.3.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WAF_WC_VGC_MAIN_FILE', __FILE__ );

define( 'WAF_WC_VGC_URL', untrailingslashit( plugins_url( '/', __FILE__ ) ) );

define( 'WAF_WC_VGC_VERSION', '1.1.2' );

/**
 * Initialize Very Good Collection WooCommerce payment gateway.
 */
function waf_wc_vgc_init() {

	if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
		return;
	}
	require_once dirname( __FILE__ ) . '/includes/class-waf-wc-vgc-gateway.php';
	add_filter( 'woocommerce_payment_gateways', 'waf_wc_add_vgc_gateway' );
    add_filter( 'woocommerce_available_payment_gateways', 'conditionally_hide_waf_vgc_payment_gateways' );

}
add_action( 'plugins_loaded', 'waf_wc_vgc_init' );


/**
* Add Settings link to the plugin entry in the plugins menu
**/
function waf_wc_vgc_plugin_action_links( $links ) {

    $settings_link = array(
    	'settings' => '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=waf_vgc' ) . '" title="View Settings">Settings</a>'
    );
    return array_merge( $settings_link, $links );

}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'waf_wc_vgc_plugin_action_links' );


/**
* Add VGC Gateway to WC
**/
function waf_wc_add_vgc_gateway( $methods ) {
    $methods[] = 'Waf_WC_Vgc_Gateway';
	return $methods;

}

/**
 * @param $available_gateways
 * @return mixed
 * Hide Very Good Condition payment method if the currency is not one of the following: USD, GBP, EUR, GHS, NGN
 */
function conditionally_hide_waf_vgc_payment_gateways( $available_gateways ) {
    // Not in backend (admin)
    if( is_admin() ){
        return $available_gateways;
    }
    $vgc_api = new Waf_WC_Vgc_Gateway();
    $available_currencies = $vgc_api->get_supported_currencies();
    $currency = get_woocommerce_currency();
    if(array_search($currency, $available_currencies) === false){
        unset($available_gateways['waf_vgc']);
    }
    return $available_gateways;
}