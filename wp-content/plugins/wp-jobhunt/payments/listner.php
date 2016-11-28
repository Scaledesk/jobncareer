<?php

/*
 * File : Listener file for payment post in transaction custom post
 */

include_once('../../../../wp-load.php');


if (isset($_REQUEST['payment_source']) && $_REQUEST['payment_source'] == 'wooC') {
    if (isset($_REQUEST['order_id']) && isset($_REQUEST['payment_status'])) {

        $order_id = $_REQUEST['order_id'];
        $order = new WC_Order($order_id);
        $rcv_parameters = get_post_meta($order_id, '_rcv_parameters', true);
        $user_info = get_userdata(get_post_meta($order_id, '_customer_user', true));
        $transaction_id = get_post_meta($order_id, '_transaction_id', true);
        $mc_currency = get_post_meta($order_id, '_order_currency', true);
        $payer_email = $user_info->data->user_email;
        $payment_gross = get_post_meta($order_id, '_order_total', true);
        $payment_method_title = get_post_meta($order_id, '_payment_method', true);
        $item_number = '';
        $cs_trans_id = $rcv_parameters['custom_var']['cs_order_id'];

        $return_url = $rcv_parameters['redirect_url'];
		
		$user_id	= get_current_user_id();
		
        $transaction_array = array();
        $transaction_array['cs_trans_id'] = esc_attr($transaction_id);
        $transaction_array['cs_transaction_status'] = $_REQUEST['payment_status'];
        $transaction_array['cs_transaction_pay_method'] = $payment_method_title;
        $transaction_array['cs_trans_currency'] = esc_attr($mc_currency);
        $transaction_array['cs_summary_email'] = esc_attr($payer_email);
        $transaction_array['cs_transaction_amount'] = esc_attr($payment_gross);


        $transaction_array['cs_job_id'] = $rcv_parameters['custom_var']['cs_job_id'];

        cs_update_transaction($transaction_array, $cs_trans_id);
        if ($_REQUEST['payment_status'] == 'approved') {
            cs_update_post($transaction_array['cs_job_id'], $cs_trans_id);
        }
    }
}

// Payment transaction custom transaction post

function cs_update_transaction($cs_trans_array = array(), $cs_trans_id) {
    foreach ($cs_trans_array as $trans_key => $trans_val) {
        update_post_meta($cs_trans_id, "$trans_key", $trans_val);
    }
}

// Payment transaction custom post update 

function cs_update_post($id = '', $cs_trans_id = '') {
    global $cs_plugin_options;
    $cs_trans_type = get_post_meta($cs_trans_id, 'cs_transaction_type', true);

    if ($cs_trans_type != 'cv_trans') {
        $cs_transaction_id = get_post_meta($cs_trans_id, 'cs_transaction_id', true);
        $cs_trans_feat = get_post_meta($cs_trans_id, 'cs_transaction_feature', true);
        $cs_trans_pkg = get_post_meta($cs_trans_id, 'cs_transaction_package', true);
        if ($id == $cs_trans_pkg) {
            update_post_meta($cs_trans_id, 'cs_job_id', '');
        }
        if (isset($cs_plugin_options['cs_jobs_review_option']) && $cs_plugin_options['cs_jobs_review_option'] != 'on') {
            
            $old_job_status = get_post_meta( $id, 'cs_job_status', true ); // getting job status
            update_post_meta((int) $id, 'cs_job_status', 'active'); // automated active job if admin didn't set this option
            do_action( 'jobhunt_job_status_changed', $id, $old_job_status ); //job status approved/not approved email hook
            
        } else {
            update_post_meta((int) $id, 'cs_job_status', 'awaiting-activation');    // wait for approval if admin set this option
            do_action('jobhunt_job_waiting_approval', $id);
        }

        update_post_meta((int) $id, 'cs_job_featured', $cs_trans_feat);
        if ($cs_trans_pkg != '') {
            update_post_meta((int) $id, 'cs_trans_id', $cs_transaction_id);
            update_post_meta((int) $id, 'cs_job_package', $cs_trans_pkg);
        }
    } else {
        update_post_meta($cs_trans_id, 'cs_resume_ids', '');
    }
}

$postback = 'cmd=_notify-validate';

// go through each of the posted vars and add them to the postback variable

foreach ($_POST as $key => $value) {
    $value = urlencode(stripslashes($value));
    $postback .= "&$key=$value";
}

/*
 * Paypal Gateway Listner
 */

if (isset($_POST['payment_status']) && $_POST['payment_status'] == 'Completed') {

    $job_id = $_POST['item_number'];
    if (isset($job_id) && $job_id != '') {
        if (isset($_POST['txn_id']) && $_POST['txn_id'] <> '') {

            $transaction_array = array();

            $transaction_array['cs_trans_id'] = esc_attr($_POST['txn_id']);
            $transaction_array['cs_job_id'] = esc_attr($_POST['item_number']);
            $transaction_array['cs_transaction_status'] = 'approved';
            $transaction_array['cs_full_address'] = esc_attr($_POST['address_street']) . ' ' . esc_attr($_POST['address_city']) . ' ' . esc_attr($_POST['address_country']);
            $transaction_array['cs_transaction_amount'] = esc_attr($_POST['payment_gross']);
            $transaction_array['cs_transaction_pay_method'] = 'cs_paypal_gateway';
            $transaction_array['cs_trans_currency'] = esc_attr($_POST['mc_currency']);
            $transaction_array['cs_summary_email'] = esc_attr($_POST['payer_email']);
            $transaction_array['cs_first_name'] = esc_attr($_POST['first_name']);

            $transaction_array['cs_last_name'] = esc_attr($_POST['cs_last_name']);

            $cs_trans_id = isset($_POST['custom']) ? $_POST['custom'] : '';

            cs_update_transaction($transaction_array, $cs_trans_id);
            cs_update_post($job_id, $cs_trans_id);
        }
    }
}

/*
 * Authorize Gateway Listner
 */
if (isset($_POST['x_response_code']) && $_POST['x_response_code'] == '1') {

    $job_id = $_POST['x_cust_id'];

    if (isset($job_id) && $job_id != '') {
        $transaction_array = array();
        $transaction_array['cs_job_id'] = esc_attr($job_id);
        $transaction_array['transaction_purchase_on'] = date('Y-m-d H:i:s');

        $transaction_array['cs_transaction_status'] = 'approved';
        $transaction_array['cs_transaction_pay_method'] = 'cs_authorizedotnet_gateway';
        $transaction_array['order_id'] = esc_attr($_POST['x_po_num']);

        $transaction_array['summary_status'] = 'Completed';
        $transaction_array['cs_trans_id'] = esc_attr($_POST['x_trans_id']);
        $transaction_array['cs_transaction_amount'] = esc_attr($_POST['x_amount']);
        $transaction_array['cs_trans_currency'] = 'USD';

        $transaction_array['address_street'] = esc_attr($_POST['x_address']);
        $transaction_array['address_city'] = esc_attr($_POST['x_city']);
        $transaction_array['address_country'] = esc_attr($_POST['x_country']);
        $transaction_array['cs_full_address'] = esc_attr($_POST['x_address']) . ' ' . esc_attr($_POST['x_city']) . ' ' . esc_attr($_POST['x_country']);

        if (esc_attr($_POST['x_email'] == '')) {
            $transaction_array['cs_summary_email'] = cs_get_user_data($transaction_array['order_id'], 'email');
        } else {
            $transaction_array['cs_summary_email'] = esc_attr($_POST['x_email']);
        }

        if (esc_attr($_POST['x_first_name'] == '')) {
            $transaction_array['cs_first_name'] = cs_get_user_data($transaction_array['order_id'], 'first_name');
        } else {
            $transaction_array['cs_first_name'] = esc_attr($_POST['x_first_name']);
        }

        if (esc_attr($_POST['x_last_name'] == '')) {
            $transaction_array['cs_last_name'] = cs_get_user_data($transaction_array['order_id'], 'last_name');
        } else {
            $transaction_array['cs_last_name'] = esc_attr($_POST['x_last_name']);
        }

        $package_id = get_post_meta((int) $transaction_array['order_id'], 'transaction_package', true);

        $cs_trans_id = isset($_POST['x_po_num']) ? $_POST['x_po_num'] : '';

        cs_update_transaction($transaction_array, $cs_trans_id);
        cs_update_post($job_id, $cs_trans_id);
    }
}

/*
 * Skrill Gateway Listner
 */

if (isset($_POST['merchant_id'])) {
    // Validate the Moneybookers signature
    $concatFields = $_POST['merchant_id']
            . $_POST['order_id']
            . strtoupper(md5('Paste your secret word here'))
            . $_POST['mb_amount']
            . $_POST['mb_currency']
            . $_POST['status'];

    $cs_plugin_options = get_option('cs_plugin_options');

    $MBEmail = $cs_plugin_options['skrill_email'];

    if (isset($_POST['status']) && $_POST['status'] == '2' && trim($_POST['pay_to_email']) == trim($MBEmail)) {
        $data = explode('||', $_POST['transaction_id']);
        $order_id = $data[0];
        $job_id = $data[1];

        if (isset($job_id) && $job_id != '') {
            $transaction_array = array();
            $transaction_array['cs_job_id'] = esc_attr($job_id);
            $transaction_array['transaction_purchase_on'] = date('Y-m-d H:i:s');
            $transaction_array['cs_transaction_status'] = 'approved';
            $transaction_array['cs_transaction_pay_method'] = 'cs_skrill_gateway';
            $transaction_array['order_id'] = esc_attr($order_id);

            $transaction_array['summary_status'] = 'Completed';
            $transaction_array['cs_trans_id'] = esc_attr($_POST['mb_transaction_id']);
            $transaction_array['cs_transaction_amount'] = esc_attr($_POST['amount']);
            $transaction_array['cs_trans_currency'] = $_POST['currency'];
            $transaction_array['transaction_address'] = '';


            $package_id = get_post_meta((int) $transaction_array['order_id'], 'transaction_package', true);

            $user_id = get_post_meta((int) $transaction_array['order_id'], 'transaction_user', true);

            if ($user_id != '') {
                if ($_POST['summary_email'] == '') {
                    $transaction_array['cs_summary_email'] = cs_get_user_data($transaction_array['order_id'], 'email');
                }

                $transaction_array['cs_first_name'] = cs_get_user_data($transaction_array['order_id'], 'first_name');
                $transaction_array['cs_last_name'] = cs_get_user_data($transaction_array['order_id'], 'last_name');
                $transaction_array['cs_full_address'] = cs_get_user_data($transaction_array['order_id'], 'address');
            }

            $cs_trans_id = isset($order_id) ? $order_id : '';

            cs_update_transaction($transaction_array, $cs_trans_id);
            cs_update_post($job_id, $cs_trans_id);
        }
    } else {
        
    }
}

/*
 * start function for get user data
 */

if (!function_exists('cs_get_user_data')) {

    function cs_get_user_data($order_id = '', $key = '') {
        $user_id = get_post_meta((int) $order_id, 'transaction_user', true);
        if ($user_id != '') {
            if ($key != '') {
                return get_user_meta($user_id, $key, true);
            }
        }
        return;
    }

}