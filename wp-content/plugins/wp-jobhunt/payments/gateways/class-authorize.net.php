<?php

/**
 *  File Type: Authorize.net Gateway

 */
if (!class_exists('CS_AUTHORIZEDOTNET_GATEWAY')) {

    class CS_AUTHORIZEDOTNET_GATEWAY extends CS_PAYMENTS {

        // Call a construct for objects 
        public function __construct() {
            // Do Something
            global $cs_gateway_options;
            $cs_gateway_options = get_option('cs_plugin_options');
            $cs_lister_url = '';
            if (isset($cs_gateway_options['dir_authorizenet_ipn_url'])) {
                $cs_lister_url = $cs_gateway_options['dir_authorizenet_ipn_url'];
            }
            if (isset($cs_gateway_options['cs_authorizenet_sandbox']) && $cs_gateway_options['cs_authorizenet_sandbox'] == 'on') {
                $this->gateway_url = "https://test.authorize.net/gateway/transact.dll";
            } else {
                $this->gateway_url = "https://secure.authorize.net/gateway/transact.dll";
            }
            $this->listner_url = $cs_lister_url;
        }

        // Start function for Authorize.net payment gateway
        
        public function settings($cs_gateways_id = '') {
            global $post;

            $cs_rand_id = CS_FUNCTIONS()->cs_rand_id();

            $on_off_option = array("show" => __("on", "jobhunt"), "hide" => __("off", "jobhunt"));




            $cs_settings[] = array(
                "name" => __("Authorize.net Settings", 'jobhunt'),
                "id" => "tab-heading-options",
                "std" => __("Authorize.net Settings", "jobhunt"),
                "type" => "section",
                "options" => "",
                "parrent_id" => "$cs_gateways_id",
                "active" => true,
            );




            $cs_settings[] = array("name" => __("Custom Logo", "jobhunt"),
                "desc" => "",
                "hint_text" => "",
                "id" => "authorizedotnet_gateway_logo",
                "std" => wp_jobhunt::plugin_url() . 'payments/images/athorizedotnet_.png',
                "display" => "none",
                "type" => "upload logo"
            );

            $cs_settings[] = array("name" => __("Default Status", "jobhunt"),
                "desc" => "",
                "hint_text" => __("If this switch will be OFF, no payment will be processed via Authorize.net.", "jobhunt"),
                "id" => "authorizedotnet_gateway_status",
                "std" => "on",
                "type" => "checkbox",
                "options" => $on_off_option
            );

            $cs_settings[] = array("name" => __("Authorize.net Sandbox", "jobhunt"),
                "desc" => "",
                "hint_text" => __("Control Authorize.net sandbox Account with this switch. If this switch is set to ON, payments will be  proceed with sandbox account.", "jobhunt"),
                "id" => "authorizenet_sandbox",
                "std" => "on",
                "type" => "checkbox",
                "options" => $on_off_option
            );

            $cs_settings[] = array("name" => __("Login Id", "jobhunt"),
                "desc" => "",
                "hint_text" => __("Add your Authorize.net login ID here. You will get it while signing up on Authorize.net.", "jobhunt"),
                "id" => "authorizenet_login",
                "std" => "",
                "type" => "text"
            );

            $cs_settings[] = array("name" => __("Transaction Key", "jobhunt"),
                "desc" => "",
                "hint_text" => __("Add your Authorize.net Transaction Key here. You will get this key while signing up on Authorize.net", "jobhunt"),
                "id" => "authorizenet_transaction_key",
                "std" => "",
                "type" => "text"
            );

            $ipn_url = wp_jobhunt::plugin_url() . 'payments/listner.php';
            $cs_settings[] = array("name" => __("Authorize.net Ipn Url", "jobhunt"),
                "desc" => $ipn_url,
                "hint_text" => __("Here you can add your Authorize.net IPN URL.", "jobhunt"),
                "id" => "dir_authorizenet_ipn_url",
                "std" => $ipn_url,
                "type" => "text"
            );



            return $cs_settings;
        }
            // Start function for process request Authorize.net payment gateway
        public function cs_proress_request($params = '') {
            global $post, $cs_gateway_options, $cs_form_fields2;
            extract($params);
            $cs_current_date = date('Y-m-d H:i:s');
            $output = '';
            $rand_id = $this->cs_get_string(5);
            $cs_login = '';
            if (isset($cs_gateway_options['cs_authorizenet_login'])) {
                $cs_login = $cs_gateway_options['cs_authorizenet_login'];
            }
            $transaction_key = '';
            if (isset($cs_gateway_options['cs_authorizenet_transaction_key'])) {
                $transaction_key = $cs_gateway_options['cs_authorizenet_transaction_key'];
            }
            if (isset($package)) {
                $package = $cs_gateway_options['cs_packages_options'][$cs_trans_pkg];
            }

            $timeStamp = time();
            $sequence = rand(1, 1000);

            if (phpversion() >= '5.1.2') {
                $fingerprint = hash_hmac("md5", $cs_login . "^" . $sequence . "^" . $timeStamp . "^" . $cs_trans_amount . "^", $transaction_key);
            } else {
                $fingerprint = bin2hex(mhash(MHASH_MD5, $cs_login . "^" . $sequence . "^" . $timeStamp . "^" . $cs_trans_amount . "^", $transaction_key));
            }

            $currency = isset($cs_gateway_options['cs_currency_type']) && $cs_gateway_options['cs_currency_type'] != '' ? $cs_gateway_options['cs_currency_type'] : 'USD';
            $user_ID = get_current_user_id();

            $cs_opt_hidden1_array = array(
                'id' => '',
                'std' => $cs_login,
                'cust_id' => "",
                'cust_name' => "x_login",
                'return' => true,
            );
            $cs_opt_hidden2_array = array(
                'id' => '',
                'std' => 'AUTH_CAPTURE',
                'cust_id' => "",
                'cust_name' => "x_type",
                'return' => true,
            );
            $cs_opt_hidden3_array = array(
                'id' => '',
                'std' => $cs_trans_amount,
                'cust_id' => "",
                'cust_name' => "x_amount",
                'return' => true,
            );
            $cs_opt_hidden4_array = array(
                'id' => '',
                'std' => $sequence,
                'cust_id' => "",
                'cust_name' => "x_fp_sequence",
                'return' => true,
            );
            $cs_opt_hidden5_array = array(
                'id' => '',
                'std' => $timeStamp,
                'cust_id' => "",
                'cust_name' => "x_fp_timestamp",
                'return' => true,
            );
            $cs_opt_hidden6_array = array(
                'id' => '',
                'std' => $fingerprint,
                'cust_id' => "",
                'cust_name' => "x_fp_hash",
                'return' => true,
            );
            $cs_opt_hidden7_array = array(
                'id' => '',
                'std' => 'PAYMENT_FORM',
                'cust_id' => "",
                'cust_name' => "x_show_form",
                'return' => true,
            );
            $cs_opt_hidden8_array = array(
                'id' => '',
                'std' => 'ORDER-' . sanitize_text_field($cs_order_id),
                'cust_id' => "",
                'cust_name' => "x_invoice_num",
                'return' => true,
            );
            $cs_opt_hidden9_array = array(
                'id' => '',
                'std' => sanitize_text_field($cs_order_id),
                'cust_id' => "",
                'cust_name' => "x_po_num",
                'return' => true,
            );
            $cs_opt_hidden10_array = array(
                'id' => '',
                'std' => sanitize_text_field($cs_job_id),
                'cust_id' => "",
                'cust_name' => "x_cust_id",
                'return' => true,
            );
            $cs_opt_hidden11_array = array(
                'id' => '',
                'std' => sanitize_text_field($cs_package_title),
                'cust_id' => "",
                'cust_name' => "x_description",
                'return' => true,
            );
            $cs_opt_hidden12_array = array(
                'id' => '',
                'std' => esc_url(get_permalink()),
                'cust_id' => "",
                'cust_name' => "x_cancel_url",
                'return' => true,
            );
            $cs_opt_hidden13_array = array(
                'id' => '',
                'std' => __('Cancel Order', 'jobhunt'),
                'cust_id' => "",
                'cust_name' => "x_cancel_url_text",
                'return' => true,
            );
            $cs_opt_hidden14_array = array(
                'id' => '',
                'std' => 'TRUE',
                'cust_id' => "",
                'cust_name' => "x_relay_response",
                'return' => true,
            );
            $cs_opt_hidden15_array = array(
                'id' => '',
                'std' => sanitize_text_field($this->listner_url),
                'cust_id' => "",
                'cust_name' => "x_relay_url",
                'return' => true,
            );
            $cs_opt_hidden16_array = array(
                'id' => '',
                'std' => 'false',
                'cust_id' => "",
                'cust_name' => "x_test_request",
                'return' => true,
            );
            $output .= '<form name="AuthorizeForm" id="direcotry-authorize-form" action="' . $this->gateway_url . '" method="post">  
			' . $cs_form_fields2->cs_form_hidden_render($cs_opt_hidden1_array) . '
                        ' . $cs_form_fields2->cs_form_hidden_render($cs_opt_hidden2_array) . '
                        ' . $cs_form_fields2->cs_form_hidden_render($cs_opt_hidden3_array) . '
                        ' . $cs_form_fields2->cs_form_hidden_render($cs_opt_hidden4_array) . '
                        ' . $cs_form_fields2->cs_form_hidden_render($cs_opt_hidden5_array) . '
                        ' . $cs_form_fields2->cs_form_hidden_render($cs_opt_hidden6_array) . '
                        ' . $cs_form_fields2->cs_form_hidden_render($cs_opt_hidden7_array) . '
                        ' . $cs_form_fields2->cs_form_hidden_render($cs_opt_hidden8_array) . '
                        ' . $cs_form_fields2->cs_form_hidden_render($cs_opt_hidden9_array) . '
                        ' . $cs_form_fields2->cs_form_hidden_render($cs_opt_hidden10_array) . '
                        ' . $cs_form_fields2->cs_form_hidden_render($cs_opt_hidden11_array) . '
                        ' . $cs_form_fields2->cs_form_hidden_render($cs_opt_hidden12_array) . '
                        ' . $cs_form_fields2->cs_form_hidden_render($cs_opt_hidden13_array) . '
                        ' . $cs_form_fields2->cs_form_hidden_render($cs_opt_hidden14_array) . '
                        ' . $cs_form_fields2->cs_form_hidden_render($cs_opt_hidden15_array) . '
                        ' . $cs_form_fields2->cs_form_hidden_render($cs_opt_hidden16_array) . '

			</form>';
            echo CS_FUNCTIONS()->cs_special_chars($output);
            echo '<script>
				    	jQuery("#direcotry-authorize-form").submit();
				      </script>';
            die;
        }

    }

}