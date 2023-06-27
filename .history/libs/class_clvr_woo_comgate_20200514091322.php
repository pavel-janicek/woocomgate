<?php
if (!class_exists('Clvr_Woo_Comgate')){

    class Clvr_Woo_Comgate extends WC_Payment_Gateway{

        private $client;
        private $PAYMENTS_URL = 'https://payments.comgate.cz/v1.0/create';

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

        public function init_form_fields(){

            $this->form_fields = array(
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

         public function admin_options(){
            echo '<h3>'.__('Platební brána Comgate', 'woocomgate').'</h3>';
            echo '<p>'.__('Comgate je rychlá a spolehlivá platební brána pro příjem plateb kartou.').'</p>';
            echo '<table class="form-table">';
            // Generate the HTML For the settings form.
            $this->generate_settings_html();
            echo '</table>';
    
        }

        public function payment_fields(){
            if($this -> description) echo wpautop(wptexturize($this -> description));
    
        }

        public function getClient(){
            if (!empty($this->client)){
                return $this->client;
            }
            $isTest = ($this->test_mode=='yes');
            
            $this->client  = new AgmoPaymentsSimpleProtocol($this->PAYMENTS_URL,$this->merchantId,$isTest,$this->password);
            return $this->client;
        }

    } // end class

}//endif