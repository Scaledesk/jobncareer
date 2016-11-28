<?php

/**
 *  File Type: Paypal Gateway
 *
 */
if (!class_exists('CS_PAYPAL_GATEWAY')) {

    class CS_PAYPAL_GATEWAY extends CS_PAYMENTS {

        public function __construct() {
            global $cs_gateway_options;

            $cs_gateway_options = get_option('cs_plugin_options');

            $cs_lister_url = '';
            if (isset($cs_gateway_options['cs_dir_paypal_ipn_url'])) {
                $cs_lister_url = $cs_gateway_options['cs_dir_paypal_ipn_url'];
            }

            if (isset($cs_gateway_options['cs_paypal_sandbox']) && $cs_gateway_options['cs_paypal_sandbox'] == 'on') {
                $this->gateway_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
            } else {
                $this->gateway_url = "https://www.paypal.com/cgi-bin/webscr";
            }
            $this->listner_url = $cs_lister_url;
        }

        // Start function for paypal setting 
        
        public function settings($cs_gateways_id = '') {
            global $post;

            $cs_rand_id = CS_FUNCTIONS()->cs_rand_id();

            $on_off_option = array("show" => __("on","jobhunt"), "hide" => __("off","jobhunt"));



            $cs_settings[] = array(
                "name" => __("Paypal Settings", 'jobhunt'),
                "id" => "tab-heading-options",
                "std" => __("Paypal Settings", "jobhunt"),
                "type" => "section",
                "options" => "",
                "parrent_id" => "$cs_gateways_id",
                "active" => true,
            );



            $cs_settings[] = array("name" => __("Custom Logo ", "jobhunt"),
                "desc" => "",
                "hint_text" => "",
                "id" => "paypal_gateway_logo",
                "std" => wp_jobhunt::plugin_url() . 'payments/images/paypal.png',
                "display" => "none",
                "type" => "upload logo"
            );

            $cs_settings[] = array("name" => __("Default Status", "jobhunt"),
                "desc" => "",
                "hint_text" => __("If this switch will be OFF, no payment will be processed via Paypal. ", "jobhunt"),
                "id" => "paypal_gateway_status",
                "std" => "on",
                "type" => "checkbox",
                "options" => $on_off_option
            );

            $cs_settings[] = array("name" => __("Paypal Sandbox", "jobhunt"),
                "desc" => "",
                "hint_text" => __("Control PayPal sandbox Account with this switch. If this switch is set to ON, payments will be  proceed with sandbox account.", "jobhunt"),
                "id" => "paypal_sandbox",
                "std" => "on",
                "type" => "checkbox",
                "options" => $on_off_option
            );

            $cs_settings[] = array("name" => __("Paypal Business Email", "jobhunt"),
                "desc" => "",
                "hint_text" => __("Add your business Email address here to proceed PayPal payments.", "jobhunt"),
                "id" => "paypal_email",
                "std" => "",
                "type" => "text"
            );

            $ipn_url = wp_jobhunt::plugin_url() . 'payments/listner.php';
            $cs_settings[] = array("name" => __("Paypal Ipn Url", "jobhunt"),
                "desc" => $ipn_url,
                "hint_text" => __("Here you can add your PayPal IPN URL.", "jobhunt"),
                "id" => "dir_paypal_ipn_url",
                "std" => $ipn_url,
                "type" => "text"
            );



            return $cs_settings;
        }
        
        // Start function for paypal process request  

        public function cs_proress_request($params = '') {
            global $post, $cs_gateway_options, $cs_form_fields2;
            extract($params);

            $cs_current_date = date('Y-m-d H:i:s');
            $output = '';
            $rand_id = $this->cs_get_string(5);
            $business_email = $cs_gateway_options['cs_paypal_email'];


            $currency = isset($cs_gateway_options['cs_currency_type']) && $cs_gateway_options['cs_currency_type'] != '' ? $cs_gateway_options['cs_currency_type'] : 'USD';
            $cs_opt_hidden1_array = array(
                'id' => '',
                'std' => '_xclick',
                'cust_id' => "",
                'cust_name' => "cmd",
                'return' => true,
            );
            $cs_opt_hidden2_array = array(
                'id' => '',
                'std' => sanitize_email($business_email),
                'cust_id' => "",
                'cust_name' => "business",
                'return' => true,
            );
            $cs_opt_hidden3_array = array(
                'id' => '',
                'std' => $cs_trans_amount,
                'cust_id' => "",
                'cust_name' => "amount",
                'return' => true,
            );
            $cs_opt_hidden4_array = array(
                'id' => '',
                'std' => $currency,
                'cust_id' => "",
                'cust_name' => "currency_code",
                'return' => true,
            );
            $cs_opt_hidden5_array = array(
                'id' => '',
                'std' => $cs_package_title,
                'cust_id' => "",
                'cust_name' => "item_name",
                'return' => true,
            );
            $cs_opt_hidden6_array = array(
                'id' => '',
                'std' => sanitize_text_field($cs_job_id),
                'cust_id' => "",
                'cust_name' => "item_number",
                'return' => true,
            );
            $cs_opt_hidden7_array = array(
                'id' => '',
                'std' => '',
                'cust_id' => "",
                'cust_name' => "cancel_return",
                'return' => true,
            );
            $cs_opt_hidden8_array = array(
                'id' => '',
                'std' => '1',
                'cust_id' => "",
                'cust_name' => "no_note",
                'return' => true,
            );
            $cs_opt_hidden9_array = array(
                'id' => '',
                'std' => sanitize_text_field($cs_order_id),
                'cust_id' => "",
                'cust_name' => "invoice",
                'return' => true,
            );
            $cs_opt_hidden10_array = array(
                'id' => '',
                'std' => esc_url($this->listner_url),
                'cust_id' => "",
                'cust_name' => "notify_url",
                'return' => true,
            );
            $cs_opt_hidden11_array = array(
                'id' => '',
                'std' => '',
                'cust_id' => "",
                'cust_name' => "lc",
                'return' => true,
            );
            $cs_opt_hidden12_array = array(
                'id' => '',
                'std' => '2',
                'cust_id' => "",
                'cust_name' => "rm",
                'return' => true,
            );
            $cs_opt_hidden13_array = array(
                'id' => '',
                'std' => sanitize_text_field($cs_order_id),
                'cust_id' => "",
                'cust_name' => "custom",
                'return' => true,
            );
            $cs_opt_hidden14_array = array(
                'id' => '',
                'std' => esc_url(home_url('/')),
                'cust_id' => "",
                'cust_name' => "return",
                'return' => true,
            );
            
            $output .= '<form name="PayPalForm" id="direcotry-paypal-form" action="' . $this->gateway_url . '" method="post">  
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
                        </form>';
 

            $data = CS_FUNCTIONS()->cs_special_chars($output);
            $data .= '<script>
					  	  jQuery("#direcotry-paypal-form").submit();
					  </script>';
            echo CS_FUNCTIONS()->cs_special_chars($data);
        }

        public function cs_gateway_listner() {
            
        }

    }

}