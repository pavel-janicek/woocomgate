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

    } // end class

}//endif