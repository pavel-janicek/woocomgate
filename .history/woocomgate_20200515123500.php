<?php
/*
Plugin Name: WooCommerce - Comgate
Plugin URL: https://cleverstart.cz
Description: Přidá možnost platby přes Comgate
Version: 0.0.1
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
	$methods[] = 'Clvr_Woo_Comgate';
	return $methods;
}

add_filter('woocommerce_payment_gateways', 'woocommerce_add_cleverstart_comgate_gateway' );