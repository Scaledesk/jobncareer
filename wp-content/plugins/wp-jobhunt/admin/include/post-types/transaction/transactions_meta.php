<?php

/**
 * Start Function  how to Create Transations Fields
 */
if (!function_exists('cs_create_transactions_fields')) {

    function cs_create_transactions_fields($key, $param) {
        global $post, $cs_html_fields, $cs_form_fields2, $cs_plugin_options;
        $cs_gateway_options = get_option('cs_plugin_options');
        $cs_currency_sign = isset($cs_gateway_options['currency_sign']) ? $cs_gateway_options['currency_sign'] : '$';
        $cs_value = $param['title'];
        $html = '';
        switch ($param['type']) {
            case 'text' :
                // prepare
                $cs_value = get_post_meta($post->ID, 'cs_' . $key, true);

                if (isset($cs_value) && $cs_value != '') {
                    if ($key == 'transaction_expiry_date') {
                        $cs_value = date_i18n('d-m-Y', $cs_value);
                    } else {
                        $cs_value = $cs_value;
                    }
                } else {
                    $cs_value = '';
                }

                
				$cs_opt_array = array(
                    'name' => $param['title'],
                    'desc' => '',
                    'hint_text' => '',
                    'field_params' => array(
                        'std' => $cs_value,
                        'id' => $key,
						'classes' => 'cs-form-text cs-input',
                        'return' => true,
						
                    ),
                );
				$output='';
				 $output .= $cs_html_fields->cs_text_field($cs_opt_array);
                $output .= '<span class="cs-form-desc">' . $param['description'] . '</span>' . "\n";
                

                $html .= $output;
                break;
            case 'textarea' :
                // prepare
                $cs_value = get_post_meta($post->ID, 'cs_' . $key, true);
                if (isset($cs_value) && $cs_value != '') {
                    $cs_value = $cs_value;
                } else {
                    $cs_value = '';
                }

                $cs_opt_array = array(
                    'name' => $param['title'],
                    'desc' => '',
                    'hint_text' => '',
                    'field_params' => array(
                        'std' => '',
                        'id' => $key,
                        'return' => true,
                    ),
                );

                $output = $cs_html_fields->cs_textarea_field($cs_opt_array);
                $html .= $output;
                break;
            case 'select' :
                $cs_value = get_post_meta($post->ID, 'cs_' . $key, true);
                if (isset($cs_value) && $cs_value != '') {
                    $cs_value = $cs_value;
                } else {
                    $cs_value = '';
                }
                $cs_classes = '';
                if (isset($param['classes']) && $param['classes'] != "") {
                    $cs_classes = $param['classes'];
                }
                $cs_opt_array = array(
                    'name' => $param['title'],
                    'desc' => '',
                    'hint_text' => '',
                    'field_params' => array(
                        'std' => '',
                        'id' => $key,
                        'classes' => $cs_classes,
                        'options' => $param['options'],
                        'return' => true,
                    ),
                );

                $output = $cs_html_fields->cs_select_field($cs_opt_array);
                // append
                $html .= $output;
                break;
            case 'hidden_label' :
                // prepare
                $cs_value = get_post_meta($post->ID, 'cs_' . $key, true);

                if (isset($cs_value) && $cs_value != '') {
                    $cs_value = $cs_value;
                } else {
                    $cs_value = '';
                }

                $cs_opt_array = array(
                    'name' => $param['title'],
                    'hint_text' => '',
                );
                $output = $cs_html_fields->cs_opening_field($cs_opt_array);

                $output .= '<span>#' . $cs_value . '</span>';

                $output .= $cs_form_fields2->cs_form_hidden_render(
                        array(
                            'name' => '',
                            'id' => $key,
                            'return' => true,
                            'classes' => '',
                            'std' => $cs_value,
                            'description' => '',
                            'hint' => ''
                        )
                );

                $cs_opt_array = array(
                    'desc' => '',
                );
                $output .= $cs_html_fields->cs_closing_field($cs_opt_array);
                $html .= $output;
                break;
            case 'summary' :
                // prepare
                global $gateways;
                $object = new CS_PAYMENTS();
                $cs_plugin_options = get_option('cs_plugin_options');
                $summary_status = get_post_meta($post->ID, "cs_summary_status", true);
                $summary_transection_id = get_post_meta($post->ID, "cs_summary_transection_id", true);
                $summary_amount = get_post_meta($post->ID, "cs_summary_amount", true);
                $summary_currency = get_post_meta($post->ID, "cs_summary_currency", true);
                $summary_email = get_post_meta($post->ID, "cs_summary_email", true);
                $first_name = get_post_meta($post->ID, "cs_first_name", true);
                $last_name = get_post_meta($post->ID, "cs_last_name", true);
                $full_address = get_post_meta($post->ID, "cs_full_address", true);
                $transaction_pay_method = get_post_meta($post->ID, "cs_transaction_pay_method", true);
                $gateway_type = 'NILL';
                $gateway_logo = '';
                if (isset($transaction_pay_method) && $transaction_pay_method != '') {
                    $gateway_type = $gateways[strtoupper($transaction_pay_method)];
                    $logo = $cs_plugin_options[strtolower($transaction_pay_method) . '_logo'];
                    if (isset($logo) && $logo != '') {
                        $gateway_logo = '<img src=' . esc_url($logo) . ' />';
                    }
                }
                $summary_status = $summary_status ? $summary_status : __('Pending', 'jobhunt');
                $summary_transection_id = $summary_transection_id ? $summary_transection_id : __('NILL', 'jobhunt');
                $summary_email = $summary_email ? $summary_email : __('NILL', 'jobhunt');
                $first_name = $first_name ? $first_name : __('NILL', 'jobhunt');
                $last_name = $last_name ? $last_name : __('NILL', 'jobhunt');
                $full_address = $full_address ? $full_address : __('NILL', 'jobhunt');

                $cs_opt_array = array(
                    'name' => __('Payment Summary', 'jobhunt'),
                    'hint_text' => '',
                );
                $output = $cs_html_fields->cs_opening_field($cs_opt_array);

                $cs_opt_array = array(
                    'name' => __('Payment Method', 'jobhunt'),
                    'hint_text' => '',
                );
                $output = $cs_html_fields->cs_opening_field($cs_opt_array);
                $output .= $gateway_logo . ' ' . $gateway_type;
                $cs_opt_array = array(
                    'desc' => '',
                );
                $output .= $cs_html_fields->cs_closing_field($cs_opt_array);

                $cs_opt_array = array(
                    'name' => __('Gateway Transaction Id', 'jobhunt'),
                    'hint_text' => '',
                );
                $output = $cs_html_fields->cs_opening_field($cs_opt_array);
                $output .= $summary_transection_id;
                $cs_opt_array = array(
                    'desc' => '',
                );
                $output .= $cs_html_fields->cs_closing_field($cs_opt_array);

                $cs_opt_array = array(
                    'name' => __('Status', 'jobhunt'),
                    'hint_text' => '',
                );
                $output = $cs_html_fields->cs_opening_field($cs_opt_array);
                $output .= $summary_status;
                $cs_opt_array = array(
                    'desc' => '',
                );
                $output .= $cs_html_fields->cs_closing_field($cs_opt_array);

                $cs_opt_array = array(
                    'name' => __('Email', 'jobhunt'),
                    'hint_text' => '',
                );
                $output = $cs_html_fields->cs_opening_field($cs_opt_array);
                $output .= $summary_email;
                $cs_opt_array = array(
                    'desc' => '',
                );
                $output .= $cs_html_fields->cs_closing_field($cs_opt_array);

                $cs_opt_array = array(
                    'name' => __('First Name', 'jobhunt'),
                    'hint_text' => '',
                );
                $output = $cs_html_fields->cs_opening_field($cs_opt_array);
                $output .= $first_name;
                $cs_opt_array = array(
                    'desc' => '',
                );
                $output .= $cs_html_fields->cs_closing_field($cs_opt_array);

                $cs_opt_array = array(
                    'name' => __('Last Name', 'jobhunt'),
                    'hint_text' => '',
                );
                $output = $cs_html_fields->cs_opening_field($cs_opt_array);
                $output .= $last_name;
                $cs_opt_array = array(
                    'desc' => '',
                );
                $output .= $cs_html_fields->cs_closing_field($cs_opt_array);

                $cs_opt_array = array(
                    'name' => __('Address', 'jobhunt'),
                    'hint_text' => '',
                );
                $output = $cs_html_fields->cs_opening_field($cs_opt_array);
                $output .= $full_address;
                $cs_opt_array = array(
                    'desc' => '',
                );
                $output .= $cs_html_fields->cs_closing_field($cs_opt_array);

                $cs_opt_array = array(
                    'desc' => '',
                );
                $output .= $cs_html_fields->cs_closing_field($cs_opt_array);

                $html .= $output;
                break;
            case 'extra_features' :
                // prepare
                $cs_job_id = get_post_meta($post->ID, "cs_job_id", true);
                $cs_job_ids = $cs_job_id;
                $cs_job_id = explode(',', $cs_job_id);
                $cs_post_data = '';
                $cs_saved_ads = array();
                if (isset($cs_job_id) && is_array($cs_job_id) && sizeof($cs_job_id) && $cs_job_id[0] != '') {
                    foreach ($cs_job_id as $id) {
                        $cs_permalink = get_edit_post_link($id);
                        $cs_title = get_the_title($id);
                        $cs_post = '<ul>';
                        $cs_post .= '<li>' . __('Job Id', 'jobhunt') . ' : #' . $id . '</li>';
                        $cs_post .= '<li>' . __('Job Title', 'jobhunt') . ' : <a target="_blank" href="' . esc_url($cs_permalink) . '">' . $cs_title . '</a></li>';
                        $cs_post .= '</ul>';
                        $cs_post_data .= '<span>' . $cs_post . '</span>';

                        if ($cs_permalink <> '') {
                            $cs_saved_ads[] = $id;
                        }
                    }
                } else {
                    $cs_post_data .= __('No Jobs used yet.', 'jobhunt');
                }
                if (is_array($cs_saved_ads) && sizeof($cs_saved_ads) > 0) {
                    $cs_job_ids = implode(',', $cs_saved_ads);
                }

                $cs_opt_array = array(
                    'name' => $param['title'],
                    'hint_text' => '',
                );
                $output = $cs_html_fields->cs_opening_field($cs_opt_array);

                $output .= $cs_post_data;

                $output .= $cs_form_fields2->cs_form_hidden_render(
                        array(
                            'name' => '',
                            'id' => 'job_id',
                            'return' => true,
                            'classes' => '',
                            'std' => $cs_job_ids,
                            'description' => '',
                            'hint' => ''
                        )
                );

                $cs_opt_array = array(
                    'desc' => '',
                );
                $output .= $cs_html_fields->cs_closing_field($cs_opt_array);

                $html .= $output;
                break;
            case 'cv_resumes' :
                // prepare
                $cs_job_id = get_post_meta($post->ID, "cs_resume_ids", true);
                $cs_job_ids = $cs_job_id;
                $cs_job_id = explode(',', $cs_job_id);
                $cs_post_data = '';
                $cs_saved_ads = array();
                if (is_array($cs_job_id) && $cs_job_id[0] == '')
                    unset($cs_job_id[0]);
                if (isset($cs_job_id) && is_array($cs_job_id) && sizeof($cs_job_id) && !empty($cs_job_id)) {
                    foreach ($cs_job_id as $id) {
                        $cs_user_data = get_userdata($id);
                        if (!empty($cs_user_data)) {
                            $cs_permalink = get_dashboard_url($id);
                            $cs_title = $cs_user_data->display_name;
                            $cs_post = '<ul>';
                            $cs_post .= '<li>' . __('Resume Id', 'jobhunt') . ' : #' . $id . '</li>';
                            $cs_post .= '<li>' . __('Resume Title', 'jobhunt') . ' : <a target="_blank" href="' . esc_url($cs_permalink) . '">' . $cs_title . '</a></li>';
                            $cs_post .= '</ul>';
                            $cs_post_data .= '<span>' . $cs_post . '</span>';
                        }
                        if ($cs_permalink <> '') {
                            $cs_saved_ads[] = $id;
                        }
                    }
                } else {
                    $cs_post_data .= __('No Resumes used yet', 'jobhunt');
                }
                if (is_array($cs_saved_ads) && sizeof($cs_saved_ads) > 0) {
                    $cs_job_ids = implode(',', $cs_saved_ads);
                }

                $cs_opt_array = array(
                    'name' => $param['title'],
                    'hint_text' => '',
                );
                $output = $cs_html_fields->cs_opening_field($cs_opt_array);

                $output .= $cs_post_data;

                $output .= $cs_form_fields2->cs_form_hidden_render(
                        array(
                            'name' => '',
                            'id' => 'resume_ids',
                            'return' => true,
                            'classes' => '',
                            'std' => $cs_job_ids,
                            'description' => '',
                            'hint' => ''
                        )
                );

                $cs_opt_array = array(
                    'desc' => '',
                );
                $output .= $cs_html_fields->cs_closing_field($cs_opt_array);

                $html .= $output;
                break;
            default :
                break;
        }
        return $html;
    }

}
/**
 * End Function  how to Create Transations Fields
 */