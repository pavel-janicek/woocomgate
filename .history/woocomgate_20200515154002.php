<?php
/*
Plugin Name: WooCommerce - Comgate
Plugin URL: https://cleverstart.cz
Description: Přidá možnost platby přes Comgate
Version: 0.0.9
Author: Pavel Janíček
Author URI: https://cleverstart.cz
*/

require __DIR__ . '/vendor/autoload.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://plugins.cleverstart.cz/?action=get_metadata&slug=woocomgate',
	__FILE__, //Full path to the main plugin file or functions.php.
	'woocomgate'
);

function woocommerce_add_cleverstart_comgate_gateway($methods) {
	require __DIR__ . '/libs/class_clvr_woo_comgate.php';
	require __DIR__ . '/vendor/autoload.php';
	$methods[] = 'Clvr_Woo_Comgate';
	return $methods;
}

add_filter('woocommerce_payment_gateways', 'woocommerce_add_cleverstart_comgate_gateway' );

add_action('init', 'woocommerce_comgate_gateway_pingback');



function woocommerce_comgate_gateway_pingback(){

	if ( isset( $_GET['listener'] ) && $_GET['listener'] == 'woocomgate' ) {
	woocomgate_debug_to_console('pingback post');
	woocomgate_debug_to_console($_POST);
	woocomgate_debug_to_console('pigback request');
	woocomgate_debug_to_console($_REQUEST);	
	 WC()->payment_gateways();
 	 do_action('check_comgate',$data);
  }
}

function woocomgate_debug_to_console( $data) {
      
	$logforreal = true;
	if(!$logforreal){
	  return;
	}
	$output = $data;
	if ( is_array( $output ) ){
		$output = implode(', ', array_map(
			function ($v, $k) {
				if (is_object($v)){
					$v = serialize($v);
				}
				return sprintf("%s='%s'", $k, $v);
			 },
			$output,
			array_keys($output)
		));
	}
	if (is_object($output)){
		$output = serialize($output);
	}

	error_log($output ."\n", 3, 'woocomgate.log');
	


}
