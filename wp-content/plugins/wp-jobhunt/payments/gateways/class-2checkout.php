<?php

/**
 *  File Type: 2Checkout Gateway
 *

 */
if (!class_exists('CS_2CHECKOUT_GATEWAY')) {

    class CS_2CHECKOUT_GATEWAY {

        public function __construct() {
            // Do Something
        }
         
        //start function for payment checkout setting gateways
        
        public function settings() {
            global $post;

            $cs_rand_id = CS_FUNCTIONS()->cs_rand_id();

            $on_off_option = array("show" => __("on", "jobhunt"), "hide" => __("off", "jobhunt"));

            $cs_settings[] = array("name" => __("Custom Logo", "jobhunt"),
                "desc" => "",
                "hint_text" => "",
                "id" => "2checkout_gateway_logo",
                "std" => "",
                "display" => "none",
                "type" => "logo"
            );

            $cs_settings[] = array("name" => __("Default Status", "jobhunt"),
                "desc" => "",
                "hint_text" => __("Show/Hide Gateway On Front End.", "jobhunt"),
                "id" => "2checkout_status",
                "std" => "on",
                "type" => "checkbox",
                "options" => $on_off_option
            );

            $cs_settings[] = array("name" => __("2CheckOut Sandbox", "jobhunt"),
                "desc" => "",
                "hint_text" => __("Only for Developer use.", "jobhunt"),
                "id" => "2checkout_sandbox",
                "std" => "on",
                "type" => "checkbox",
                "options" => $on_off_option
            );

            $cs_settings[] = array("name" => __("2CheckOut Business Email", "jobhunt"),
                "desc" => "",
                "hint_text" => "",
                "id" => "2checkout_email",
                "std" => "",
                "type" => "text"
            );

            $ipn_url = wp_jobhunt::plugin_url() . 'payments/gateways/class-2checkout.php';
            $cs_settings[] = array("name" => __("2CheckOut Ipn Url", "jobhunt"),
                "desc" => "",
                "hint_text" => __("Do not edit this Url", "jobhunt"),
                "id" => "dir_2checkout_ipn_url",
                "std" => $ipn_url,
                "type" => "text"
            );

            return $cs_settings;
        }

       //start function for generate form
        
        public function cs_generate_form() {
            global $post;
        }

    }

}