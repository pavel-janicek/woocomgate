<?php
if (!class_exists('Clvr_Woo_Comgate')){

    class Clvr_Woo_Comgate extends WC_Payment_Gateway{
        const PAID = 'PAID';
        const CANCELLED = 'CANCELLED';
        const PENDING = 'PENDING';
        const AUTHORIZED = 'AUTHORIZED';

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
            
            add_action('check_comgate', array($this, 'check_response'),10,1);

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
            
            $this->client  = new ondrs\Comgate\AgmoPaymentsSimpleProtocol($this->PAYMENTS_URL,$this->merchantId,$isTest,$this->password);
            return $this->client;
        }

        public function process_payment($order_id){
            global $woocommerce;
            $order = new WC_Order( $order_id );
            $create_order = $this->getClient()->createTransaction(
                'CZ', //country
                $order->get_total(), //price
                $order->get_currency(),//currency
                $this->setProdName('Obj. ' .$order_id. ' '.get_bloginfo('name')), //label, max 16 characters
                $order_id, //merchants payment identifier
                $order->get_billing_first_name() .' ' .$order->get_billing_last_name(), // payer identifier
                null, //one of VATs PL from Agmo Payments system parameter is required only for MPAY_PL method
                null, //product category identifier parameter is required only for MPAY_CZ and SMS_CZ methods
                'ALL', //method identifier or 'ALL' value
                null, //Identifier of Merchant’s bank account to which AGMO transfers the money. If the parameter is empty, the default Merchant’s account will be used.
                $order->get_billing_email(), //cliens email address (optional)
                null, //clients phone number (optional)
                'Obj. ' .$order_id. ' '.get_bloginfo('name'), // product identifier (optional)
                null, //language identifier (optional)
                false, //$preauth
                false, //is Recurring
                null //$reccurringId
            );
            $redirectUrl = $this->getClient()->getRedirectUrl();
            $order->reduce_order_stock();
			$woocommerce->cart->empty_cart();
			return array(
            'result' => 'success',
            'redirect' => $redirectUrl
            );    
        }
        
        public function setProdName($string,$length=16,$dots='…'){
            //https://stackoverflow.com/a/3161830/855636
            return (strlen($string) > $length) ? substr($string, 0, $length - strlen($dots)) . $dots : $string;
        }

        public function check_response($data){
            //$data = $_POST;

            $order_id = $data['refId'];
            $order = new WC_Order($order_id);
            if (isset($_GET['cmg-status'])){
                
                wp_redirect($order->get_checkout_order_received_url());
                exit;
            }
            try{
                $result = $this->getClient()->checkTransactionStatus($data);
            }catch(Exception $e){
                $order->update_status('failed');
                $order->add_order_note('Chyba objednávky');
                $order->add_order_note($e->getMessage());
                echo 'code=1&message='.urlencode($e->getMessage());
                print_r($e);
                echo '<br>';
                print_r($data);
                print_r($_POST);
                die();
            }
            if ($data['status'] == self::PAID){
                $order->payment_complete();
				$order->add_order_note('Objednávka úspěšně zaplacena platební branou Comgate');
            }
            echo 'code=0&message=OK';
            die();
        }

        
    
    } // end class

}//endif