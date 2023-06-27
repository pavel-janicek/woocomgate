<?php
if (!class_exists('Clvr_Woo_Comgate')){

    class Clvr_Woo_Comgate extends WC_Payment_Gateway{

        public function __construct(){
            $this->id = 'thepay';
            $this->medthod_title = 'ThePay';
            $this->has_fields = false;

            $this->init_form_fields();
            $this->init_settings();

            $this->title = $this->settings['title'];
            $this->description = $this->settings['description'];
            $this->merchantId = $this->settings['merchantId'];
			$this->password = $this->settings['password'];
			$this->test_mode = $this->settings['test_mode'];
        }

    } // end class

}//endif