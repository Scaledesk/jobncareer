<?php

// Packages start
// Adding columns start

/**
 * Start Function  how to Create colume in transactions 
 */
if (!function_exists('transactions_columns_add')) {
    add_filter('manage_cs-transactions_posts_columns', 'transactions_columns_add');

    function transactions_columns_add($columns) {
        unset($columns['title']);
        unset($columns['date']);
        $columns['p_title'] = __('Package Id', 'jobhunt');
        $columns['p_date'] = __('Date', 'jobhunt');
        $columns['users'] = __('User', 'jobhunt');
        $columns['package'] = __('Package Name', 'jobhunt');
        $columns['gateway'] = __('Payment Gateway', 'jobhunt');
        $columns['amount'] = __('Amount', 'jobhunt');
        return $columns;
    }

}

/**
 * Start Function  how to Show data in columns
 */
if (!function_exists('transactions_columns')) {
    add_action('manage_cs-transactions_posts_custom_column', 'transactions_columns', 10, 2);

    function transactions_columns($name) {
        global $post, $gateways, $cs_plugin_options;
        $general_settings = new CS_PAYMENTS();
        $currency_sign = isset($cs_plugin_options['cs_currency_sign']) ? $cs_plugin_options['cs_currency_sign'] : '$';
        $cs_emp_funs = new cs_employer_functions();
        $transaction_user = get_post_meta($post->ID, 'transaction_user', true);
        $transaction_amount = get_post_meta($post->ID, 'transaction_amount', true);
        $transaction_fee = get_post_meta($post->ID, 'transaction_fee', true);
        $transaction_status = get_post_meta($post->ID, 'transaction_status', true);
		
        // return payment gateway name
        switch ($name) {
            case 'p_title':
                echo get_the_title($post->ID);
                break;
            case 'p_date':
                echo get_the_date();
                break;
            case 'users':
                echo get_the_author_meta('display_name', (int) $transaction_user);
                break;
            case 'package':
                $cs_trans_type = get_post_meta(get_the_id(), "cs_transaction_type", true);

                if ($cs_trans_type == 'cv_trans') {
                    $cs_trans_pkg = get_post_meta(get_the_id(), "cs_transaction_cv_pkg", true);
                    $cs_trans_pkg_title = $cs_emp_funs->get_cv_pkg_field($cs_trans_pkg);
                } else {
                    $cs_trans_pkg = get_post_meta(get_the_id(), "cs_transaction_package", true);
                    $cs_trans_pkg_title = $cs_emp_funs->get_pkg_field($cs_trans_pkg);
                }
                if ($cs_trans_pkg_title != '') {
                    echo CS_FUNCTIONS()->cs_special_chars($cs_trans_pkg_title);
                } else {
                    echo '-';
                }
                break;
            case 'gateway':
                $cs_trans_gate = get_post_meta(get_the_id(), "cs_transaction_pay_method", true);
                if ($cs_trans_gate != '') {
					$cs_trans_gate = isset($gateways[strtoupper($cs_trans_gate)]) ? $gateways[strtoupper($cs_trans_gate)] : $cs_trans_gate;
					
					if( isset($cs_trans_gate ) && $cs_trans_gate != '' && $cs_trans_gate != 'cs_wooC_GATEWAY' ){
						if ( class_exists( 'WooCommerce' ) ) {
							$gateways	= WC()->payment_gateways->get_available_payment_gateways();
							if(isset($gateways[$cs_trans_gate]->title)){
								$cs_trans_gate = $gateways[$cs_trans_gate]->title;
							}
						}
					}
					$cs_trans_gate	= isset($cs_trans_gate)? $cs_trans_gate : __('Nill', 'jobhunt');
					$cs_trans_gate	= ($cs_trans_gate != 'cs_wooC_GATEWAY')? $cs_trans_gate : __('Nill', 'jobhunt');
					echo $cs_trans_gate;
                } else {
                    echo '-';
                }
                break;
            case 'amount':
                $cs_trans_amount = get_post_meta(get_the_id(), "cs_transaction_amount", true);
                if ($cs_trans_amount != '') {
                    echo esc_attr($currency_sign) . CS_FUNCTIONS()->cs_num_format($cs_trans_amount);
                } else {
                    echo '-';
                }
                break;
        }
    }

}

/**
 * Start Function  how to Row in columns
 */
if (!function_exists('remove_row_actions')) {
    add_filter('post_row_actions', 'remove_row_actions', 10, 1);

    function remove_row_actions($actions) {
        if (get_post_type() == 'cs-transactions') {
            unset($actions['view']);
            unset($actions['trash']);
            unset($actions['inline hide-if-no-js']);
        }
        return $actions;
    }

}

/**
 * Start Function  how configure gateway given dynamic gateway name
 */
if (!function_exists('cs_gateway_name')) {

    function cs_gateway_name($cs_post_id = '') {
        global $gateways;
        $transaction_method = '';
        $transaction_method = get_post_meta($cs_post_id, 'transaction_pay_method', true);
        $transaction_method = isset($gateways[strtoupper($transaction_method)]) ? $gateways[strtoupper($transaction_method)] : '';

        return $transaction_method;
    }

}

/**
 * Start Function  how configure package name dynamic gateway name
 */
if (!function_exists('cs_package_name')) {

    function cs_package_name($cs_post_id = '') {
        $transaction_package = get_post_meta($cs_post_id, 'transaction_package', true);
        $cs_plugin_options = get_option('cs_plugin_options');
        $cs_packages_options = $cs_plugin_options['cs_packages_options'];
        $package_title = isset($cs_packages_options[$transaction_package]['package_title']) ? $cs_packages_options[$transaction_package]['package_title'] : '';
        return $package_title;
    }

}

/**
 * Start Function  how create post type of transactions
 */
if (!class_exists('post_type_transactions')) {

    class post_type_transactions {

        // The Constructor
        public function __construct() {
            add_action('init', array(&$this, 'transactions_init'));
            add_action('admin_init', array(&$this, 'transactions_admin_init'));
        }

        public function transactions_init() {
            // Initialize Post Type
            $this->transactions_register();
        }

        public function transactions_register() {
            $labels = array(
                'name' => __('Packages', 'jobhunt'),
                'menu_name' => __('Packages', 'jobhunt'),
                'add_new_item' => __('Add New Packages', 'jobhunt'),
                'edit_item' => __('Edit Packages', 'jobhunt'),
                'new_item' => __('New Packages Item', 'jobhunt'),
                'add_new' => __('Add New Packages', 'jobhunt'),
                'view_item' => __('View Packages Item', 'jobhunt'),
                'search_items' => __('Search', 'jobhunt'),
                'not_found' => __('Nothing found', 'jobhunt'),
                'not_found_in_trash' => __('Nothing found in Trash', 'jobhunt'),
                'parent_item_colon' => ''
            );
            $args = array(
                'labels' => $labels,
                'public' => false,
                'publicly_queryable' => false,
                'show_ui' => true,
                'query_var' => false,
                'menu_icon' => 'dashicons-admin-post',
                'show_in_menu' => 'edit.php?post_type=jobs',
                'rewrite' => true,
                'capability_type' => 'post',
                'hierarchical' => false,
                'menu_position' => null,
                'supports' => array('')
            );
            register_post_type('cs-transactions', $args);
        }

        /**
         * End Function  how create post type of transactions
         */

        /**
         * Start Function  how create add meta boxes of transactions
         */
        public function transactions_admin_init() {
            // Add metaboxes
            add_action('add_meta_boxes', array(&$this, 'cs_meta_transactions_add'));
        }

        public function cs_meta_transactions_add() {
            add_meta_box('cs_meta_transactions', __('Packages Options', 'jobhunt'), array(&$this, 'cs_meta_transactions'), 'cs-transactions', 'normal', 'high');
        }

        public function cs_meta_transactions($post) {
            global $gateways, $cs_html_fields, $cs_form_fields2;
            $cs_users_list = array();
            $cs_users = get_users('orderby=nicename');

            foreach ($cs_users as $user) {
                $cs_users_list[$user->ID] = $user->display_name;
            }
            $cs_packages_list = $cs_cv_pkg_list = array();
            $cs_packages_options = get_option('cs_plugin_options');
            $cs_cv_pkgs_options = $cs_packages_options['cs_cv_pkgs_options'];
            $cs_packages_options = $cs_packages_options['cs_packages_options'];
            if (isset($cs_packages_options) && is_array($cs_packages_options) && count($cs_packages_options) > 0) {
                foreach ($cs_packages_options as $package_key => $package) {
                    if (isset($package_key) && $package_key <> '') {
                        $package_id = isset($package['package_id']) ? $package['package_id'] : '';
                        $package_title = isset($package['package_title']) ? $package['package_title'] : '';
                        $cs_packages_list[$package_id] = $package_title;
                    }
                }
            }
            if (isset($cs_cv_pkgs_options) && is_array($cs_cv_pkgs_options) && count($cs_cv_pkgs_options) > 0) {
                foreach ($cs_cv_pkgs_options as $cv_pkg_key => $cv_pkg) {
                    if (isset($cv_pkg_key) && $cv_pkg_key <> '') {
                        $cv_pkg_id = isset($cv_pkg['cv_pkg_id']) ? $cv_pkg['cv_pkg_id'] : '';
                        $cv_pkg_title = isset($cv_pkg['cv_pkg_title']) ? $cv_pkg['cv_pkg_title'] : '';
                        $cs_cv_pkg_list[$cv_pkg_id] = $cv_pkg_title;
                    }
                }
            }

            $object = new CS_PAYMENTS();
            $payment_geteways = array();
            $payment_geteways[''] = 'Select Payment Gateway';
            $cs_gateway_options = get_option('cs_plugin_options');
            foreach ($gateways as $key => $value) {
                $status = $cs_gateway_options[strtolower($key) . '_status'];
                if (isset($status) && $status == 'on') {
                    $payment_geteways[strtolower($key)] = $value;
                }
            }
			
			
			if( $cs_gateway_options['cs_use_woocommerce_gateway'] == 'on' ){
				if ( class_exists( 'WooCommerce' ) ) {
					$gateways	= WC()->payment_gateways->get_available_payment_gateways();				
					foreach ($gateways as $key => $value) {
						$payment_geteways[strtolower($key)] = $value->title;
					}
				}
			}

            $cs_trans_type = get_post_meta(get_the_id(), "cs_transaction_type", true);

            $transaction_meta = array();
            $transaction_meta['transaction_id'] = array(
                'name' => 'transaction_id',
                'type' => 'hidden_label',
                'title' => __('Transaction Id', 'jobhunt'),
                'description' => '',
            );
            $transaction_meta['transaction_user'] = array(
                'name' => 'transaction_user',
                'type' => 'select',
                'classes' => 'chosen-select',
                'title' => __('Package User', 'jobhunt'),
                'options' => $cs_users_list,
                'description' => '',
            );
            if ($cs_trans_type == 'cv_trans') {
                $transaction_meta['transaction_cv_pkg'] = array(
                    'name' => 'transaction_cv_pkg',
                    'type' => 'select',
                    'classes' => 'chosen-select-no-single',
                    'title' => __('Package', 'jobhunt'),
                    'options' => $cs_cv_pkg_list,
                    'description' => '',
                );
            } else {
                $transaction_meta['transaction_package'] = array(
                    'name' => 'transaction_package',
                    'type' => 'select',
                    'classes' => 'chosen-select-no-single',
                    'title' => __('Package', 'jobhunt'),
                    'options' => $cs_packages_list,
                    'description' => '',
                );
            }

            if ($cs_trans_type != 'cv_trans') {
                $transaction_meta['transaction_feature'] = array(
                    'name' => 'transaction_feature',
                    'type' => 'select',
                    'classes' => 'chosen-select-no-single',
                    'title' => __('Featured', 'jobhunt'),
                    'options' => array(
                        'no' => __('No', 'jobhunt'),
                        'yes' => __('Yes', 'jobhunt')
                    ),
                    'description' => '',
                );
            }
            $transaction_meta['transaction_amount'] = array(
                'name' => 'transaction_amount',
                'type' => 'text',
                'title' => __('Amount', 'jobhunt'),
                'description' => '',
            );
            $transaction_meta['transaction_pay_method'] = array(
                'name' => 'transaction_pay_method',
                'type' => 'select',
                'classes' => 'chosen-select-no-single',
                'title' => __('Payment Gateway', 'jobhunt'),
                'options' => $payment_geteways,
                'description' => '',
            );
            $transaction_meta['transaction_expiry_date'] = array(
                'name' => 'transaction_expiry_date',
                'type' => 'text',
                'title' => __('Package Expiry Date', 'jobhunt'),
                'description' => '',
            );
            if ($cs_trans_type == 'cv_trans') {
                $transaction_meta['transaction_listings'] = array(
                    'name' => 'transaction_listings',
                    'type' => 'text',
                    'title' => __('No. of CV\'s', 'jobhunt'),
                    'description' => '',
                );
            } else {
                $transaction_meta['transaction_listings'] = array(
                    'name' => 'transaction_listings',
                    'type' => 'text',
                    'title' => __('No. of Listings', 'jobhunt'),
                    'description' => '',
                );
            }

            if ($cs_trans_type != 'cv_trans') {
                $transaction_meta['transaction_listing_expiry'] = array(
                    'name' => 'transaction_listing_expiry',
                    'type' => 'text',
                    'title' => __('Listing Expiry', 'jobhunt'),
                    'description' => '',
                );
                $transaction_meta['transaction_listing_period'] = array(
                    'name' => 'transaction_listing_period',
                    'type' => 'select',
                    'classes' => 'chosen-select-no-single',
                    'title' => __('Listing Period', 'jobhunt'),
                    'options' => array('days' => __('Days', 'jobhunt'), 'months' => __('Months', 'jobhunt'), 'years' => __('Years', 'jobhunt')),
                    'description' => '',
                );
                $transaction_meta['transaction_ex_features'] = array(
                    'name' => 'transaction_ex_features',
                    'type' => 'extra_features',
                    'title' => __('Jobs', 'jobhunt'),
                    'description' => '',
                );
            }
            if ($cs_trans_type == 'cv_trans') {
                $transaction_meta['transaction_resumes'] = array(
                    'name' => 'transaction_resumes',
                    'type' => 'cv_resumes',
                    'title' => __('Resumes', 'jobhunt'),
                    'description' => '',
                );
            }

            $html = '<div class="page-wrap">
						<div class="option-sec" style="margin-bottom:0;">
							<div class="opt-conts">
								<div class="cs-review-wrap">
									<script type="text/javascript">
										jQuery(function(){
											jQuery("#transaction_expiry_date").datetimepicker({
												format:"d-m-Y",
												timepicker:false
											});
										});
									</script>';
            foreach ($transaction_meta as $key => $params) {
                $html .= cs_create_transactions_fields($key, $params);
            }

            $html .= '</div>
						</div>
					</div>';
            $cs_opt_array = array(
                'std' => '1',
                'id' => 'transactions_form',
                'cust_name' => 'transactions_form',
                'cust_type' => 'hidden',
                'return' => true,
            );
            $html .= $cs_form_fields2->cs_form_text_render($cs_opt_array);
            $html .= '
				<div class="clear"></div>
			</div>';
            echo force_balance_tags($html);
        }

    }

    /**
     * End Function  how create add meta boxes of transactions
     */
    return new post_type_transactions();
}