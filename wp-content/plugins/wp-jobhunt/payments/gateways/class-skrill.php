<?php

/**
 *  File Type: Skrill- Monery Booker Gateway
 *
 */
if (!class_exists('CS_SKRILL_GATEWAY')) {

    class CS_SKRILL_GATEWAY extends CS_PAYMENTS {

        
        // Start skrill gateway construct
        
        public function __construct() {
            global $cs_gateway_options;
            $cs_lister_url = '';
            if (isset($cs_gateway_options['cs_skrill_ipn_url'])) {
                $cs_lister_url = $cs_gateway_options['cs_skrill_ipn_url'];
            }



            $cs_gateway_options = get_option('cs_plugin_options');
            $this->gateway_url = "https://www.moneybookers.com/app/payment.pl";
            $this->listner_url = $cs_lister_url;
        }

        
        // Start function for skrill payment gateway setting 
        
        public function settings($cs_gateways_id = '') {
            global $post;

            $cs_rand_id = CS_FUNCTIONS()->cs_rand_id();

            $on_off_option = array("show" => __("on", "jobhunt"), "hide" => __("off", "jobhunt"));


            $cs_settings[] = array("name" => __("Skrill-MoneyBooker Settings", "jobhunt"),
                "id" => "tab-heading-options",
                "std" => __("Skrill-MoneyBooker Settings", "jobhunt"),
                "type" => "section",
                "id" => "$cs_rand_id",
                "parrent_id" => "$cs_gateways_id",
                "active" => false,
            );



            $cs_settings[] = array("name" => __("Custom Logo", "jobhunt"),
                "desc" => "",
                "hint_text" => "",
                "id" => "skrill_gateway_logo",
                "std" => wp_jobhunt::plugin_url() . 'payments/images/skrill.png',
                "display" => "none",
                "type" => "upload logo"
            );

            $cs_settings[] = array("name" => __("Default Status", "jobhunt"),
                "desc" => "",
                "hint_text" => __("If this switch will be OFF, no payment will be processed via Skrill-MoneyBooker.", "jobhunt"),
                "id" => "skrill_gateway_status",
                "std" => "on",
                "type" => "checkbox",
                "options" => $on_off_option
            );

            $cs_settings[] = array("name" => __("Skrill-MoneryBooker Business Email", "jobhunt"),
                "desc" => "",
                "hint_text" => __("Add your business Email address here to proceed Skrill-MoneryBooker payments..", "jobhunt"),
                "id" => "skrill_email",
                "std" => "",
                "type" => "text"
            );

            $ipn_url = wp_jobhunt::plugin_url() . 'payments/listner.php';
            $cs_settings[] = array("name" => __("Skrill-MoneryBooker Ipn Url", "jobhunt"),
                "desc" => $ipn_url,
                "hint_text" => __("Here you can add your Skrill-MoneryBooker IPN URL.", "jobhunt"),
                "id" => "skrill_ipn_url",
                "std" => $ipn_url,
                "type" => "text"
            );



            return $cs_settings;
        }
        
         // Start function for skrill payment gateway process request 

        public function cs_proress_request($params = '') {
            global $post, $cs_gateway_options, $cs_form_fields2;
            extract($params);

            $cs_current_date = date('Y-m-d H:i:s');
            $output = '';
            $rand_id = $this->cs_get_string(5);
            $business_email = $cs_gateway_options['cs_skrill_email'];

            $currency = isset($cs_gateway_options['cs_currency_type']) && $cs_gateway_options['cs_currency_type'] != '' ? $cs_gateway_options['cs_currency_type'] : 'USD';
            $user_ID = get_current_user_id();
            $cs_opt_hidden_array = array(
                'id' => '',
                'std' => sanitize_email($business_email),
                'cust_id' => "",
                'cust_name' => "pay_to_email",
                'return' => true,
            );
            $cs_opt_amount_array = array(
                'id' => '',
                'std' => $cs_trans_amount,
                'cust_id' => "",
                'cust_name' => "amount",
                'return' => true,
            );
            $cs_opt_language_array = array(
                'id' => '',
                'std' => 'EN',
                'cust_id' => "",
                'cust_name' => "language",
                'return' => true,
            );
            $cs_opt_currency_array = array(
                'id' => '',
                'std' => $currency,
                'cust_id' => "",
                'cust_name' => "currency",
                'return' => true,
            );
            $cs_opt_description_array = array(
                'id' => '',
                'std' => 'Package : ',
                'cust_id' => "",
                'cust_name' => "detail1_description",
                'return' => true,
            );
            $cs_opt_detail1_array = array(
                'id' => '',
                'std' => $cs_package_title,
                'cust_id' => "",
                'cust_name' => "detail1_text",
                'return' => true,
            );
            $cs_opt_detail2_description_array = array(
                'id' => '',
                'std' => 'Ad Title : ',
                'cust_id' => "",
                'cust_name' => "detail2_description",
                'return' => true,
            );
            $cs_opt_detail2_text_array = array(
                'id' => '',
                'std' => sanitize_text_field($cs_package_title),
                'cust_id' => "",
                'cust_name' => "detail2_text",
                'return' => true,
            );
            $cs_opt_detail3_description_array = array(
                'id' => '',
                'std' => "Ad ID : ",
                'cust_id' => "",
                'cust_name' => "detail3_description",
                'return' => true,
            );

            $cs_opt_detail3_text_array = array(
                'id' => '',
                'std' => sanitize_text_field($cs_order_id),
                'cust_id' => "",
                'cust_name' => "detail3_text",
                'return' => true,
            );
            $cs_opt_cancel_url_array = array(
                'id' => '',
                'std' => esc_url(get_permalink()),
                'cust_id' => "",
                'cust_name' => "cancel_url",
                'return' => true,
            );

            $cs_opt_status_url_array = array(
                'id' => '',
                'std' => sanitize_text_field($this->listner_url),
                'cust_id' => "",
                'cust_name' => "status_url",
                'return' => true,
            );

            $cs_opt_transaction_id_array = array(
                'id' => '',
                'std' => sanitize_text_field($cs_order_id) . '||' . sanitize_text_field($cs_order_id),
                'cust_id' => "",
                'cust_name' => "transaction_id",
                'return' => true,
            );

            $cs_opt_customer_number_array = array(
                'id' => '',
                'std' => $cs_order_id,
                'cust_id' => "",
                'cust_name' => "customer_number",
                'return' => true,
            );
            $cs_opt_return_url_array = array(
                'id' => '',
                'std' => esc_url(get_permalink()),
                'cust_id' => "",
                'cust_name' => "return_url",
                'return' => true,
            );

            $cs_opt_merchant_fields_array = array(
                'id' => '',
                'std' => $cs_order_id,
                'cust_id' => "",
                'cust_name' => "merchant_fields",
                'return' => true,
            );
            $output .= '<form name="SkrillForm" id="direcotry-skrill-form" action="' . $this->gateway_url . '" method="post">  
                        ' . $cs_form_fields2->cs_form_hidden_render($cs_opt_hidden_array) . '
                        ' . $cs_form_fields2->cs_form_hidden_render($cs_opt_amount_array) . '
                        ' . $cs_form_fields2->cs_form_hidden_render($cs_opt_language_array) . '
                        ' . $cs_form_fields2->cs_form_hidden_render($cs_opt_currency_array) . '
                        ' . $cs_form_fields2->cs_form_hidden_render($cs_opt_description_array) . '
                        ' . $cs_form_fields2->cs_form_hidden_render($cs_opt_detail1_array) . '
                        ' . $cs_form_fields2->cs_form_hidden_render($cs_opt_detail2_description_array) . '                    
                        ' . $cs_form_fields2->cs_form_hidden_render($cs_opt_detail2_text_array) . '  
                        ' . $cs_form_fields2->cs_form_hidden_render($cs_opt_detail3_description_array) . '  
                        ' . $cs_form_fields2->cs_form_hidden_render($cs_opt_detail3_text_array) . '  
                        ' . $cs_form_fields2->cs_form_hidden_render($cs_opt_cancel_url_array) . '  
                        ' . $cs_form_fields2->cs_form_hidden_render($cs_opt_status_url_array) . '  
                        ' . $cs_form_fields2->cs_form_hidden_render($cs_opt_transaction_id_array) . '  
                        ' . $cs_form_fields2->cs_form_hidden_render($cs_opt_customer_number_array) . '  
                        ' . $cs_form_fields2->cs_form_hidden_render($cs_opt_return_url_array) . '  
                        ' . $cs_form_fields2->cs_form_hidden_render($cs_opt_merchant_fields_array) . '  
                        </form>';

            echo CS_FUNCTIONS()->cs_special_chars($output);
            echo '<script>
				  	jQuery("#direcotry-skrill-form").submit();
				  </script>';
            die;
        }

    }

}