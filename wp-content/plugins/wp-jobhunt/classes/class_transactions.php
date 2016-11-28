<?php

/**
 *  File Type: Transactions Class
 */
if (!class_exists('cs_transactions_options')) {

    class cs_transactions_options {

        /**
         * Start construct Functions
         */
        public function __construct() {
            add_action('wp_ajax_update_trans', array(&$this, 'update_trans'));
        }

        /**
         * End construct Functions
         */

        /**
         * Start Function for add submenu page in admin dashboard
         */
        public function cs_transactions_settings() {
            add_submenu_page('edit.php?post_type=jobs', __('Transactions', 'jobhunt'), __('Transactions', 'jobhunt'), 'manage_options', 'cs_transactions', array(&$this, 'cs_transactions_area'));
        }

        /**
         * End Function for add submenu page in admin dashboard
         */

        /**
         * Start Function for how to create transactions_area fields
         */
        public function cs_transactions_area() {
            global $post, $cs_plugin_options, $gateways, $cs_form_fields2;
            $currency_sign = isset($cs_plugin_options['cs_currency_sign']) ? $cs_plugin_options['cs_currency_sign'] : '$';

            $general_settings = new CS_PAYMENTS();

            $cs_emp_funs = new cs_employer_functions();

            wp_jobhunt::cs_data_table_style_script();

            $cs_html = '
			<div class="theme-wrap fullwidth">
				<div class="row">
					<form name="cs-booking-transactions" id="cs-booking-transactions" data-url="' . esc_js(admin_url('admin-ajax.php')) . '" method="post">
						<div class="cs-customers-area">';
            $args = array(
                'posts_per_page' => "-1",
                'post_type' => 'cs-transactions',
                'post_status' => 'publish',
                'orderby' => 'ID',
                'order' => 'DESC',
            );
            $custom_query = new WP_Query($args);
            if ($custom_query->have_posts()) {

                $cs_html .= '
                            <script type="text/javascript">
                            jQuery(document).ready(function() {
                                jQuery("#cs_custmr_data").DataTable();
                            });
                            </script>
                            <div class="cs-title"><h2>' . __('Transactions', 'jobhunt') . '</h2></div>
                            <div class="cs_table_data cs_loading">
                                <table id="cs_custmr_data" class="display" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>' . __('Package Id', 'jobhunt') . '</th>
                                            <th>' . __('Title', 'jobhunt') . '</th>
                                            <th>' . __('Payment Method', 'jobhunt') . '</th>
                                            <th>' . __('Amount', 'jobhunt') . '</th>
                                            <th>' . __('Email', 'jobhunt') . '</th>
                                            <th>' . __('First Name', 'jobhunt') . '</th>
                                            <th>' . __('Last Name', 'jobhunt') . '</th>
                                            <th>' . __('Address', 'jobhunt') . '</th>
                                            <th>' . __('Status', 'jobhunt') . '</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>' . __('Package Id', 'jobhunt') . '</th>
                                            <th>' . __('Title', 'jobhunt') . '</th>
                                            <th>' . __('Payment Method', 'jobhunt') . '</th>
                                            <th>' . __('Amount', 'jobhunt') . '</th>
                                            <th>' . __('Email', 'jobhunt') . '</th>
                                            <th>' . __('First Name', 'jobhunt') . '</th>
                                            <th>' . __('Last Name', 'jobhunt') . '</th>
                                            <th>' . __('Address', 'jobhunt') . '</th>
                                            <th>' . __('Status', 'jobhunt') . '</th>
                                        </tr>
                                    </tfoot>
                                        <tbody>';

                while ($custom_query->have_posts()) : $custom_query->the_post();
                    $cs_trans_id = get_post_meta(get_the_id(), "cs_transaction_id", true);
                    $cs_trans_gate = get_post_meta(get_the_id(), "cs_transaction_pay_method", true);
                    $cs_trans_amount = get_post_meta(get_the_id(), "cs_transaction_amount", true);
                    $cs_trans_status = get_post_meta(get_the_id(), "cs_transaction_status", true);
                    $summary_email = get_post_meta(get_the_id(), "cs_summary_email", true);
                    $first_name = get_post_meta(get_the_id(), "cs_first_name", true);
                    $last_name = get_post_meta(get_the_id(), "cs_last_name", true);
                    $full_address = get_post_meta(get_the_id(), "cs_full_address", true);
                    $cs_trans_amount = $cs_trans_amount != '' && $cs_trans_amount > 0 ? $cs_trans_amount : 0;
                    $summary_email = isset($summary_email) && $summary_email != '' ? $summary_email : __('Nill', 'jobhunt');
                    $first_name = isset($first_name) && $first_name != '' ? $first_name : __('Nill', 'jobhunt');
                    $last_name = isset($last_name) && $last_name != '' ? $last_name : __('Nill', 'jobhunt');
                    $full_address = isset($full_address) && $full_address != '' ? $full_address : __('Nill', 'jobhunt');
                    $cs_trans_type = get_post_meta(get_the_id(), "cs_transaction_type", true);
                    if ($cs_trans_type == 'cv_trans') {
                        $cs_trans_pkg = get_post_meta(get_the_id(), "cs_transaction_cv_pkg", true);
                        $cs_trans_pkg_title = $cs_emp_funs->get_cv_pkg_field($cs_trans_pkg);

                        if ($cs_trans_pkg_title != '') {
                            $cs_trans_pkg_title = __('CV Search', 'jobhunt') . ' - ' . $cs_trans_pkg_title;
                        }
                    } else {
                        $cs_trans_pkg = get_post_meta(get_the_id(), "cs_transaction_package", true);
                        $cs_trans_pkg_title = $cs_emp_funs->get_pkg_field($cs_trans_pkg);

                        if ($cs_trans_pkg_title != '') {
                            $cs_trans_pkg_title = __('Advertise job', 'jobhunt') . ' - ' . $cs_trans_pkg_title;
                        }
                    }
                    if ($cs_trans_pkg_title == '') {
                        if ($cs_trans_type != 'cv_trans') {
                            $cs_trans_job = get_post_meta(get_the_id(), "cs_job_id", true);
                            $cs_trans_pkg_title = __('Featured Job', 'jobhunt') . ' <a href="' . add_query_arg(array('post' => $cs_trans_job, 'action' => 'edit'), admin_url('post.php')) . '">' . get_the_title($cs_trans_job) . '</a>';
                        } else {
                            $cs_trans_pkg_title = __('Featured Job', 'jobhunt');
                        }
                    }
                    $cs_trans_gate = isset($gateways[strtoupper($cs_trans_gate)]) ? $gateways[strtoupper($cs_trans_gate)] : $cs_trans_gate;

                    if (isset($cs_trans_gate) && $cs_trans_gate != '' && $cs_trans_gate != 'cs_wooC_GATEWAY') {
                        if (class_exists('WooCommerce')) {
                            $gateways = WC()->payment_gateways->get_available_payment_gateways();
                            if (isset($gateways[$cs_trans_gate]->title)) {
                                $cs_trans_gate = $gateways[$cs_trans_gate]->title;
                            }
                        }
                    }
                    $cs_trans_gate = isset($cs_trans_gate) ? $cs_trans_gate : __('Nill', 'jobhunt');
                    $cs_trans_gate = ($cs_trans_gate != 'cs_wooC_GATEWAY') ? $cs_trans_gate : __('Nill', 'jobhunt');

                    $cs_html .= '
                                <tr>
                                    <td>#' . $cs_trans_id . '</td>
                                    <td>' . $cs_trans_pkg_title . '</td>
                                    <td>' . $cs_trans_gate . '</td>
                                    <td>' . esc_attr($currency_sign) . CS_FUNCTIONS()->cs_num_format($cs_trans_amount) . '</td>
                                    <td>' . $summary_email . '</td>
                                    <td>' . $first_name . '</td>
                                    <td>' . $last_name . '</td>
                                    <td>' . $full_address . '</td>
                                    <td>
                                    <div id="cs-status-' . absint(get_the_id()) . '">';

                    $cs_opt_array = array(
                        'std' => $cs_trans_status,
                        'cust_id' => 'cs_update_trans',
                        'cust_name' => '',
                        'extra_atr' => ' onchange="cs_update_transaction_status(\'' . esc_js(admin_url('admin-ajax.php')) . '\',\'' . absint(get_the_id()) . '\',this.value)"',
                        'options' => array(
                            'pending' => __('Pending', 'jobhunt'),
                            'approved' => __('Approved', 'jobhunt'),
                            'cancelled' => __('Cancelled', 'jobhunt'),
                            'refunded' => __('Refunded', 'jobhunt'),
                        ),
                        'return' => true,
                    );
                    $cs_html .= $cs_form_fields2->cs_form_select_render($cs_opt_array);

                    $cs_html .= '
                                        <div class="cs-holder"></div>
                                    </div>
                                    </td>
                                </tr>';
                endwhile;
                $cs_html .= '	
                                    </tbody>
                                </table>
                            </div>';
            }
            $cs_html .= '	
                                </div>
                            </form>
                        </div>
			</div>';

            echo force_balance_tags($cs_html, true);
        }

        /**
         * Start Function for how to Update transactions_area fields
         */
        public function update_trans() {
            $cs_emp_funs = new cs_employer_functions();
            $cs_trans_id = isset($_POST['cs_id']) ? $_POST['cs_id'] : '';
            $cs_trans_val = isset($_POST['cs_val']) ? $_POST['cs_val'] : '';

            if ($cs_trans_id != '' && $cs_trans_val != '') {

                $cs_trans_type = get_post_meta($cs_trans_id, "cs_transaction_type", true);

                update_post_meta($cs_trans_id, 'cs_transaction_status', $cs_trans_val);

                if ($cs_trans_val == 'cancelled' || $cs_trans_val == 'refunded') {

                    if ($cs_trans_type == 'cv_trans') {

                        $cs_user_id = get_post_meta($cs_trans_id, "cs_transaction_user", true);

                        $cs_emp_id = $cs_emp_funs->cs_get_post_id_by_meta_key("cs_user", $cs_user_id);

                        $cs_resume_ids = get_post_meta($cs_trans_id, "cs_resume_ids", true);
                        $cs_resume_ids = explode(',', $cs_resume_ids);
                        if (isset($cs_resume_ids) && is_array($cs_resume_ids) && sizeof($cs_resume_ids) && $cs_resume_ids[0] != '') {
                            foreach ($cs_resume_ids as $cs_resume_id) {
                                if ($cs_emp_id != '' && $cs_resume_id != '') {

                                    $cs_fav_resumes = get_post_meta($cs_emp_id, "cs_fav_resumes", true);
                                    $cs_fav_resumes = unserialize($cs_fav_resumes);

                                    if (is_array($cs_fav_resumes) && sizeof($cs_fav_resumes) > 0 && $cs_fav_resumes[0] != '') {

                                        foreach (array_keys($cs_fav_resumes, $cs_resume_id, true) as $key) {
                                            unset($cs_fav_resumes[$key]);
                                        }

                                        $cs_fav_resumes = serialize($cs_fav_resumes);

                                        update_post_meta($cs_emp_id, "cs_fav_resumes", $cs_fav_resumes);
                                    }
                                }
                            }
                        }
                    } else {
                        $cs_job_id = get_post_meta($cs_trans_id, "cs_job_id", true);
                        $cs_job_id = explode(',', $cs_job_id);
                        if (isset($cs_job_id) && is_array($cs_job_id) && sizeof($cs_job_id) && $cs_job_id[0] != '') {
                            foreach ($cs_job_id as $id) {
                                update_post_meta($id, 'cs_job_status', 'delete');
                            }
                        }
                    }
                }
                echo ucfirst($cs_trans_val);
            }
            die();
        }

        /**
         * End Function for how to Update transactions_area fields
         */
    }

}

// Call hook for transactions options 
if (class_exists('cs_transactions_options')) {
    $cs_transactions_obj = new cs_transactions_options();
    add_action('admin_menu', array(&$cs_transactions_obj, 'cs_transactions_settings'));
}
