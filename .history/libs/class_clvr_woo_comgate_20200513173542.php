<?php
if (!class_exists('Clvr_Woo_Comgate')){

    class Clvr_Woo_Comgate extends WC_Payment_Gateway{

        public function __construct(){
            $this->id = 'woocomgate';
            $this->medthod_title = 'Comgate';
            $this->has_fields = false;

            $this->init_form_fields();
            $this->init_settings();

            $this->title = $this->settings['title'];
            $this->description = $this->settings['description'];
            $this->merchantId = $this->settings['merchantId'];
			$this->password = $this->settings['password'];
            $this->test_mode = $this->settings['test_mode'];
            
            add_action('check_comgate', array($this, 'check_response'));

            if ( version_compare( WOOCOMMERCE_VERSION, '2.0.0', '>=' ) ) {
                add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( &$this, 'process_admin_options' ) );
            } else {
                add_action( 'woocommerce_update_options_payment_gateways', array( $this, 'process_admin_options' ) );
            }
        }

        function init_form_fields(){

            $this -> form_fields = array(
                     'enabled' => array(
                         'title' => __('Povolit / Zakázat', 'woocomgate'),
                         'type' => 'checkbox',
                         'label' => __('Povolit platby přes Comgate', 'woocomgate'),
                         'default' => 'no'),
                     'title' => array(
                         'title' => __('Název:', 'woocomgate'),
                         'type'=> 'text',
                         'description' => __('Zde můžete změnit název brány zobrazovaný během nákupu', 'woocomgate'),
                         'default' => __('Comgate', 'woocomgate')),
                     'description' => array(
                         'title' => __('Popis:', 'woocomgate'),
                         'type' => 'textarea',
                         'description' => __('Zobrazí popis platební brány během nákupu', 'woocomgate'),
                         'default' => __('Zaplaťte rychle a snadno platební kartou.', 'woocomgate')),
                     'merchantId' => array(
                         'title' => __('Merchant ID', 'woocomgate'),
                         'type' => 'text',
                         'description' => __('Merchant ID:')),
                     'password' => array(
                         'title' => __('Heslo', 'woocomgate'),
                         'type' => 'text',
                         'description' =>  __('Heslo:', 'woocomgate'),
                     ),
                     'test_mode' => array(
                        'title' => __('Testovací mód?', 'woocomgate'),
                        'type' => 'checkbox',
                        'label' => __('Je brána v testovacím módu?', 'woothepay'),
                        'default' => 'no'
                                     )
                 );
         }

    } // end class

}//endif