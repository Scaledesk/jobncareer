<?php

/**
 *  File Type: Pre Bank Transfer
 *
 */
if (!class_exists('CS_PRE_BANK_TRANSFER')) {

    class CS_PRE_BANK_TRANSFER {

        public function __construct() {
            global $cs_gateway_options;
            $cs_gateway_options = get_option('cs_plugin_options');
        }
     
        // Start function for Bank Transfer setting 
        
        public function settings($cs_gateways_id = '') {
            global $post;

            $cs_rand_id = CS_FUNCTIONS()->cs_rand_id();

            $on_off_option = array("show" => __("on", "jobhunt"), "hide" => __("off", "jobhunt"));



            $cs_settings[] = array("name" => __("Bank Transfer Settings", "jobhunt"),
                "id" => "tab-heading-options",
                "std" => __("Bank Transfer Settings", "jobhunt"),
                "type" => "section",
                "parrent_id" => "$cs_gateways_id",
                "active" => false,
            );
            
            

            $cs_settings[] = array("name" => __("Custom Logo", "jobhunt"),
                "desc" => "",
                "hint_text" => "",
                "id" => "pre_bank_transfer_logo",
                "std" => wp_jobhunt::plugin_url() . 'payments/images/bank.png',
                "display" => "none",
                "type" => "upload logo"
            );

            $cs_settings[] = array("name" => __("Default Status", "jobhunt"),
                "desc" => "",
                "hint_text" =>__("If this switch will be OFF, no payment will be processed via Bank Transfer.","jobhunt"),
                "id" => "pre_bank_transfer_status",
                "std" => "on",
                "type" => "checkbox",
                "options" => $on_off_option
            );
            $cs_settings[] = array("name" => __("Bank Information", "jobhunt"),
                "desc" => "",
                "hint_text" => __("Add information of your bank (Bank Name).", "jobhunt"),
                "id" => "bank_information",
                "std" => "",
                "type" => "text"
            );
            $cs_settings[] = array("name" => __("Account Number", "jobhunt"),
                "desc" => "",
                "hint_text" => __("Add your bank account Number where you want receive payment.", "jobhunt"),
                "id" => "bank_account_id",
                "std" => "",
                "type" => "text"
            );
            $cs_settings[] = array("name" => __("Other Information", "jobhunt"),
                "desc" => "",
                "hint_text" => __("In this text box, you can add any help text whatever you want to show on front end for assistance of users regarding bank payment.", "jobhunt"),
                "id" => "other_information",
                "std" => "",
                "type" => "textarea"
            );



            return $cs_settings;
        }

        // Start function for process request 
        
        public function cs_proress_request($params = '') {
            global $post, $cs_plugin_options, $cs_gateway_options, $current_user;

            extract($params);
			
			$cs_emp_funs = new cs_employer_functions();
			
			$cs_emp_id = $cs_trans_user;
            $args = array(
                'posts_per_page' => "1",
                'post_type' => 'employer',
                'post_status' => 'publish',
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key' => 'cs_user',
                        'value' => $current_user->ID,
                        'compare' => '=',
                    ),
                ),
            );

            $custom_query = new WP_Query($args);

            if ($custom_query->post_count > 0) {
				while ($custom_query->have_posts()) : $custom_query->the_post();
                	$cs_emp_id = get_the_id();
				endwhile;
            }
			
			$cs_frst_name = get_post_meta($cs_emp_id, 'cs_first_name', true);
			$cs_usr_email = get_post_meta($cs_emp_id, 'cs_email', true);
			$cs_lst_name = get_post_meta($cs_emp_id, 'cs_last_name', true);
			$cs_user_adres = get_post_meta($cs_emp_id, 'cs_post_loc_address', true);
			
			$cs_trans_post_id = $cs_emp_funs->cs_get_post_id_by_meta_key("cs_transaction_id", $cs_trans_id);
			
			update_post_meta($cs_trans_post_id, 'cs_first_name', $cs_frst_name);
			update_post_meta($cs_trans_post_id, 'cs_last_name', $cs_lst_name);
			update_post_meta($cs_trans_post_id, 'cs_summary_email', $cs_usr_email);
			update_post_meta($cs_trans_post_id, 'cs_full_address', $cs_user_adres);
			
            $cs_feature_amount = isset($cs_plugin_options['cs_job_feat_price']) ? $cs_plugin_options['cs_job_feat_price'] : '';

            $cs_totl_amount = 0;
            $cs_detail = '<ul>';
            $cs_currency_sign = isset($cs_plugin_options['cs_currency_sign']) ? $cs_plugin_options['cs_currency_sign'] : '$';

            if ($cs_trans_package <> '') {
                $cs_trans_pkg_title = isset($cs_trans_pkg) && $cs_trans_pkg <> '' ? $cs_emp_funs->get_pkg_field($cs_trans_pkg) : '';
                $cs_trans_pkg_price = isset($cs_trans_pkg) && $cs_trans_pkg <> '' ? $cs_emp_funs->get_pkg_field($cs_trans_pkg, 'package_price') : '';
                $cs_detail .= '<li>' . __('Package : ', 'jobhunt'). $cs_trans_pkg_title . ' - ' . $cs_currency_sign . $cs_trans_pkg_price . '</li>';
                $cs_totl_amount += CS_FUNCTIONS()->cs_num_format($cs_trans_pkg_price);
            }

            if (isset($cs_trans_featured) && $cs_trans_featured == 'on') {

                $cs_detail .= '<li>' . __('Featured - ', 'jobhunt') . $cs_currency_sign . CS_FUNCTIONS()->cs_num_format($cs_feature_amount) . '</li>';
                $cs_totl_amount += CS_FUNCTIONS()->cs_num_format($cs_feature_amount);
            }

            $cs_totl_amount = CS_FUNCTIONS()->cs_num_format($cs_totl_amount);

            $cs_detail .= '<li>' . __('Total Charges: ', 'jobhunt') . $cs_currency_sign . $cs_totl_amount . '</li>';

            $cs_payment_vat = isset($cs_plugin_options['cs_payment_vat']) ? $cs_plugin_options['cs_payment_vat'] : '';

            if ($cs_payment_vat <> '' && $cs_payment_vat > 0) {

                $cs_tax_amount = $cs_totl_amount * ($cs_payment_vat / 100);
                $cs_tax_amount = CS_FUNCTIONS()->cs_num_format($cs_tax_amount);

                $cs_totl_amount = $cs_totl_amount + $cs_tax_amount;
                $cs_totl_amount = CS_FUNCTIONS()->cs_num_format($cs_totl_amount);

                $cs_detail .= '<li>' . sprintf(__('%s&#37; VAT :', 'jobhunt'), $cs_payment_vat) . ' - ' . $cs_currency_sign . $cs_tax_amount . '</li>';
                $cs_detail .= '<li>' . __('Gross Charges: ', 'jobhunt') . $cs_currency_sign . $cs_totl_amount . '</li>';
            }

            $cs_detail .= '</ul>';

            $cs_bank_transfer = '<div class="cs-bank-transfer">';
            $cs_bank_transfer .= '<h2>' . __('Order detail', 'jobhunt') . '</h2>';

            $cs_bank_transfer .= '<ul class="list-group">';
            $cs_bank_transfer .= '<li class="list-group-item">';
            $cs_bank_transfer .= '<span class="badge">#' . $cs_trans_id . '</span>';
            $cs_bank_transfer .= __('Order ID','jobhunt');
            $cs_bank_transfer .= '</li>';

            $cs_bank_transfer .= '<ul class="list-group">';
            $cs_bank_transfer .= '<h2>' . __('Bank detail', 'jobhunt') . '</h2>';
            $cs_bank_transfer .= '<p>' . __('Please transfer amount To this account, After payment Received we will process your Order', 'jobhunt') . '</p1>';

            if (isset($cs_gateway_options['cs_bank_information']) && $cs_gateway_options['cs_bank_information'] != '') {
                $cs_bank_transfer .= '<li class="list-group-item">';
                $cs_bank_transfer .= '<span class="badge">' . $cs_gateway_options['cs_bank_information'] . '</span>';
                $cs_bank_transfer .= __('Bank Information', 'jobhunt');
                $cs_bank_transfer .= '</li>';
            }

            if (isset($cs_gateway_options['cs_bank_account_id']) && $cs_gateway_options['cs_bank_account_id'] != '') {
                $cs_bank_transfer .= '<li class="list-group-item">';
                $cs_bank_transfer .= '<span class="badge">' . $cs_gateway_options['cs_bank_account_id'] . '</span>';
                $cs_bank_transfer .= __('Account No', 'jobhunt');
                $cs_bank_transfer .= '</li>';
            }

            if (isset($cs_gateway_options['cs_other_information']) && $cs_gateway_options['cs_other_information'] != '') {
                $cs_bank_transfer .= '<li class="list-group-item">';
                $cs_bank_transfer .= '<span>' . $cs_gateway_options['cs_other_information'] . '</span>';
                $cs_bank_transfer .= '</li>';
            }

            $cs_bank_transfer .= '</ul>';
            $cs_bank_transfer .= '</div>';

            return force_balance_tags($cs_bank_transfer);
        }

    }

}