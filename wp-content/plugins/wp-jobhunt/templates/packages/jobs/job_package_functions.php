<?php

/*
 *
 * @Shortcode Name : Job Package
 * Start Function how to create Shortcode of Job Package
 *
 */
if (!function_exists('cs_job_package_shortcode')) {

    function cs_job_package_shortcode($atts) {
        global $post, $current_user, $cs_form_fields2;
        $defaults = array(
            'column_size' => '',
            'job_package_title' => '',
            'job_package_style' => '',
            'job_pkges' => '',
            'job_packages_columns' => '4',
        );
        extract(shortcode_atts($defaults, $atts));
        $column_size = isset($column_size) ? $column_size : '';
        $column_class = '';
        if($column_size != ''){
        $column_class = jobcareer_custom_column_class($column_size);
        }
        if ($job_packages_columns <> '') {
            $grid_columns = 12 / $job_packages_columns;
        } else {
             $grid_columns = 3;
        }
        
        $cs_html = '';
         if($column_class != ''){
        $cs_html .= '<div class="'.$column_class.'">';
        }
        $cs_plugin_options = get_option('cs_plugin_options');
        if (class_exists('cs_employer_functions')) {
            $cs_emp_funs = new cs_employer_functions();
        }
        if ($job_package_title != '') {
            $cs_html .= '<div class="cs-element-title"><h2>' . $job_package_title . '</h2></div>';
        }
        $job_pkges = explode(',', $job_pkges);

        $currency_sign = isset($cs_plugin_options['cs_currency_sign']) ? $cs_plugin_options['cs_currency_sign'] : '$';
        $cs_emp_dashboard = isset($cs_plugin_options['cs_emp_dashboard']) ? $cs_plugin_options['cs_emp_dashboard'] : '';
        $cs_packages_options = isset($cs_plugin_options['cs_packages_options']) ? $cs_plugin_options['cs_packages_options'] : '';
        $user_role = cs_get_loginuser_role();
        if (is_user_logged_in() && !((isset($user_role) && $user_role <> '' && $user_role == 'cs_employer') )) {
            $cs_html .= '<div id="cs-not-emp" class="alert alert-warning" style="display:none;">' . __('Become an Employer first to Subscribe the Package.', 'jobhunt') . '<a href="#" class="close" data-dismiss="alert">&times;</a></div>';
        }
        $rand_id = rand(0, 9999999);
        $cs_html .= '<div class="cs-packeges" id="cs-cv-form' . $rand_id . '" data-ajaxurl="' . esc_url(admin_url('admin-ajax.php')) . '">';

        if ($job_package_style == 'basic') {
            $cs_html .= '<div class="price-tables">';
        }

        if ($job_package_style == 'classic' || $job_package_style == 'fancy' || $job_package_style == 'modern' || $job_package_style == 'advance') {
            $cs_html .= '<div class="row">';
        }
        if (is_array($cs_packages_options) && sizeof($cs_packages_options) > 0) {

            if ($job_package_style == 'simple') {
                $cs_html .= '<ul class="cs-pricetable">';
            }
            if ($job_package_style == 'modern') {
                $cs_html .= '<ul class="cs-pricetable fancy">';
            }

            $cs_pkg_counter = 0;
            foreach ($cs_packages_options as $package_key => $package) {
                if (isset($package_key) && $package_key <> '' && in_array($package_key, $job_pkges)) {
                    $cs_rand_id = rand(53445, 65765);
                    $package_id = isset($package['package_id']) ? $package['package_id'] : '';
                    $package_title = isset($package['package_title']) ? $package['package_title'] : '';
                    $package_price = isset($package['package_price']) ? $package['package_price'] : '';
                    $package_listings = isset($package['package_listings']) ? $package['package_listings'] : '';
                    $package_submission_limit = isset($package['package_submission_limit']) ? $package['package_submission_limit'] : '';
                    $cs_list_dur = isset($package['cs_list_dur']) ? $package['cs_list_dur'] : '';
                    $package_duration = isset($package['package_duration']) ? $package['package_duration'] : '';
                    $package_duration_period = isset($package['package_duration_period']) ? $package['package_duration_period'] : '';
                    $package_feature = isset($package['package_feature']) ? $package['package_feature'] : '';
                    $package_desc = isset($package['package_description']) ? $package['package_description'] : '';
                    $cs_package_type = isset($package['package_type']) ? $package['package_type'] : '';
                    if ($job_package_style == 'simple') {
                        $pkg_feat_class = $package_feature == 'yes' ? ' active' : '';

                        $cs_html .= '<li class="col-lg-'.$grid_columns.' col-md-'.$grid_columns.' col-sm-6 col-xs-12 " >';
                        $cs_html .= '<div style="background-color:#fff;" class="pricetable-holder ' . CS_FUNCTIONS()->cs_special_chars($pkg_feat_class) . '">';
                        $cs_html .= '<h2 class="cs-bgcolor" style="color:#fff; padding:55px 20px;">' . CS_FUNCTIONS()->cs_special_chars($package_title) . '</h2>';
                        $cs_html .= '<div class="price-holder">';
                        $cs_html .= '<div class="cs-price">';
                        $cs_html .= ' <p>' . CS_FUNCTIONS()->cs_special_chars($package_desc) . '</p>';
                        $cs_html .= ' <span class="cs-color"><em>' . esc_attr($currency_sign) . CS_FUNCTIONS()->cs_special_chars($package_price) . '</em><small>' . sprintf(__('%s %s', 'jobhunt'), $package_submission_limit, $cs_list_dur) . '</small></span>';
                        $cs_html .= ' </div>';
                        if (!is_user_logged_in()) {
                            $cs_html .= '<a class="cs-button" style="background:#f5f5f5; color:#999" onclick="trigger_func(\'#btn-header-main-login\');">' . __('Buy Now', 'jobhunt') . '</a>';
                        } else if (is_user_logged_in() && !((isset($user_role) && $user_role <> '' && $user_role == 'cs_employer') )) {
                            $cs_html .= '<a class="cs-button" style="background:#f5f5f5; color:#999" id="cs_emp_check_' . absint($cs_rand_id) . '">' . __('Buy Now', 'jobhunt') . '</a>';
                        } else {

                            $cs_opt_submit_array = array(
                                'id' => '',
                                'std' => __('Buy Now', 'jobhunt'),
                                'cust_id' => "",
                                'cust_name' => "",
                                'cust_type' => 'submit',
                                'classes' => 'cs-bgcolor slct-cv-pkg cs-buy',
                                'extra_atr' => '',
                                'return' => true,
                            );

                            $cs_opt_radio_array = array(
                                'id' => '',
                                'std' => absint($package_id),
                                'cust_id' => "",
                                'cust_name' => "cs_package",
                                'return' => true,
                                'extra_atr' => 'style="display:none; position:absolute;" ',
                            );

                            $cs_opt_hidden_array = array(
                                'id' => '',
                                'std' => '1',
                                'cust_id' => "",
                                'cust_name' => "cs_job_pkg_trans",
                                'return' => true,
                            );

                            $cs_html .= '<form method="post" action="' . add_query_arg(array('profile_tab' => 'packages'), get_permalink($cs_emp_dashboard)) . '">
                                ' . $cs_form_fields2->cs_form_text_render($cs_opt_submit_array) . $cs_form_fields2->cs_form_hidden_render($cs_opt_radio_array) . $cs_form_fields2->cs_form_hidden_render($cs_opt_hidden_array) . '
                                   </form>';
                        }
                        $cs_html .= '</div>';
                        $cs_html .= '</div>';
                        $cs_html .= ' </li>';
                    } else if ($job_package_style == 'classic' || $job_package_style == 'advance') {

                        $pkg_feat_class = $package_feature == 'yes' ? ' active' : '';
                        $pkg_feat_color = $package_feature == 'yes' ? ' class="cs-color"' : '';
                        $main_class = 'modren';
                        if ($job_package_style == 'advance') {
                            $main_class = 'advance';
                        }
                        $cs_html .= '
						<div class="col-lg-'.$grid_columns.' col-md-'.$grid_columns.' col-sm-6 col-xs-12">
							<div class="pricetable-holder ' . $main_class . ' ' . $pkg_feat_class . '">
								<h2' . $pkg_feat_color . '>' . CS_FUNCTIONS()->cs_special_chars($package_title) . '</h2> 
								<div class="price-holder ">
									<div class="cs-price">
										<span><small>' . esc_attr($currency_sign) . '</small>' . CS_FUNCTIONS()->cs_special_chars($package_price) . '<em>' . sprintf(__('%s %s', 'jobhunt'), $package_submission_limit, $cs_list_dur) . '</em></span>';
                        if ($package_desc != '') {
                            $package_desc = nl2br($package_desc);
                            $package_desc = str_replace('<br />', '</li><li>', $package_desc);
                            $cs_html .= '
											<ul>
												<li>' . $package_desc . '</li>
											</ul>';
                        }
                        $cs_html .= '
									</div>';
                        if (!is_user_logged_in()) {
                            $cs_html .= '<a class="cs-bgcolor" onclick="trigger_func(\'#btn-header-main-login\');">' . __('Buy Now', 'jobhunt') . '</a>';
                        } else if (is_user_logged_in() && !((isset($user_role) && $user_role <> '' && $user_role == 'cs_employer') )) {
                            $cs_html .= '<a id="cs_emp_check_' . absint($cs_rand_id) . '" class="cs-bgcolor">' . __('Buy Now', 'jobhunt') . '</a>';
                        } else {

                            $cs_html .= '
										<form method="post" action="' . add_query_arg(array('profile_tab' => 'packages'), get_permalink($cs_emp_dashboard)) . '">
										<input class="cs-bgcolor" type="submit" value="' . __('Buy Now', 'jobhunt') . '">
										<input type="hidden" name="cs_package" value="' . absint($package_id) . '" style="display:none; position:absolute;" />
										<input type="hidden" name="cs_job_pkg_trans" value="1">
										</form>';
                        }
                        $cs_html .= '
								</div>
							</div>
						</div>';
                    } else if ($job_package_style == 'fancy') {

                        $pkg_feat_class = $package_feature == 'yes' ? ' active' : '';
                        $pkg_feat_color = $package_feature == 'yes' ? ' class="cs-color"' : '';
                        $pkg_feat_bgcolor = $package_feature == 'yes' ? '' : ' class="cs-bgcolor"';
                        $cs_html .= '
						<div class="col-lg-'.$grid_columns.' col-md-'.$grid_columns.' col-sm-6 col-xs-12">
							<div class="pricetable-holder classic cs-border-top-color' . $pkg_feat_class . '">
								<h2' . $pkg_feat_color . '>' . CS_FUNCTIONS()->cs_special_chars($package_title) . '</h2> 
								<div class="price-holder">
									<div class="cs-price">
										<span><small>' . esc_attr($currency_sign) . '</small>' . CS_FUNCTIONS()->cs_special_chars($package_price) . '<em>' . sprintf(__('%s %s', 'jobhunt'), $package_submission_limit, $cs_list_dur) . '</em></span>';
                        if ($package_desc != '') {
                            $package_desc = nl2br($package_desc);
                            $package_desc = str_replace('<br />', '</li><li>', $package_desc);
                            $cs_html .= '
											<ul>
												<li>' . $package_desc . '</li>
											</ul>';
                        }
                        $cs_html .= '
									</div>';
                        if (!is_user_logged_in()) {
                            $cs_html .= '<a' . $pkg_feat_bgcolor . ' onclick="trigger_func(\'#btn-header-main-login\');">' . __('Buy Now', 'jobhunt') . '</a>';
                        } else if (is_user_logged_in() && !((isset($user_role) && $user_role <> '' && $user_role == 'cs_employer') )) {
                            $cs_html .= '<a id="cs_emp_check_' . absint($cs_rand_id) . '"' . $pkg_feat_bgcolor . '>' . __('Buy Now', 'jobhunt') . '</a>';
                        } else {

                            $cs_html .= '
										<form method="post" action="' . add_query_arg(array('profile_tab' => 'packages'), get_permalink($cs_emp_dashboard)) . '">
										<input' . $pkg_feat_bgcolor . ' type="submit" value="' . __('Buy Now', 'jobhunt') . '">
										<input type="hidden" name="cs_package" value="' . absint($package_id) . '" style="display:none; position:absolute;" />
										<input type="hidden" name="cs_job_pkg_trans" value="1">
										</form>';
                        }
                        $cs_html .= '
								</div>
							</div>
						</div>';
                    } else if ($job_package_style == 'modern') {

                        $pkg_feat_class = $package_feature == 'yes' ? ' active cs-border-top-color' : '';
                        $pkg_feat_color = $package_feature == 'yes' ? ' class="cs-color"' : '';
                        $cs_html .= '
						<li class="col-lg-'.$grid_columns.' col-md-'.$grid_columns.' col-sm-6 col-xs-12">
							<div class="pricetable-holder ' . $pkg_feat_class . '">
								<h2' . $pkg_feat_color . '>' . CS_FUNCTIONS()->cs_special_chars($package_title) . '</h2> 
								<div class="price-holder">
									<div class="cs-price">
										<span><small>' . esc_attr($currency_sign) . '</small>' . CS_FUNCTIONS()->cs_special_chars($package_price) . '<em>' . sprintf(__('%s %s', 'jobhunt'), $package_submission_limit, $cs_list_dur) . '</em></span>';
                        if ($package_desc != '') {
                            $package_desc = nl2br($package_desc);
                            $package_desc = str_replace('<br />', '</li><li>', $package_desc);
                            $cs_html .= '
											<ul>
												<li>' . $package_desc . '</li>
											</ul>';
                        }
                        $cs_html .= '
									</div>';
                        if (!is_user_logged_in()) {
                            $cs_html .= '<a onclick="trigger_func(\'#btn-header-main-login\');">' . __('Buy Now', 'jobhunt') . '</a>';
                        } else if (is_user_logged_in() && !((isset($user_role) && $user_role <> '' && $user_role == 'cs_employer') )) {
                            $cs_html .= '<a id="cs_emp_check_' . absint($cs_rand_id) . '">' . __('Buy Now', 'jobhunt') . '</a>';
                        } else {

                            $cs_html .= '
										<form method="post" action="' . add_query_arg(array('profile_tab' => 'packages'), get_permalink($cs_emp_dashboard)) . '">
										<input type="submit" value="' . __('Buy Now', 'jobhunt') . '">
										<input type="hidden" name="cs_package" value="' . absint($package_id) . '" style="display:none; position:absolute;" />
										<input type="hidden" name="cs_job_pkg_trans" value="1">
										</form>';
                        }
                        $cs_html .= '
								</div>
							</div>
						</li>';
                    } else {
                        $cs_html .= '<article class="col-md-'.$grid_columns.'">
                                        <span class="cs-bgcolor price"><sup>' . esc_attr($currency_sign) . '</sup>' . CS_FUNCTIONS()->cs_special_chars($package_price) . '<em>' . sprintf(__('%s %s', 'jobhunt'), $package_submission_limit, $cs_list_dur) . '</em></span>
                                        <h3>' . CS_FUNCTIONS()->cs_special_chars($package_title) . '</h3>
                                        <ul class="price-list">';
                        if ($cs_package_type == 'subscription') {
                            $cs_html .= '<li><span><b>' . CS_FUNCTIONS()->cs_special_chars($package_listings) . '</b> ' . sprintf(__('Maximum Jobs in %s %s', 'jobhunt'), $package_submission_limit, $cs_list_dur) . '</span></li>';
                        } else {
                            $cs_html .= '<li><span>' . __('Single Submission', 'jobhunt') . '</span></li>';
                        }
                        $cs_html .= '<li><span><b>' . CS_FUNCTIONS()->cs_special_chars($package_duration) . '</b> ' . sprintf(__('%s duration', 'jobhunt'), $package_duration_period) . '</span></li>
							<li>' . CS_FUNCTIONS()->cs_special_chars($package_desc) . '</li>
							</ul>';
                        if (!is_user_logged_in()) {
                            $cs_html .= '<a class="cs-color acc-submit" onclick="trigger_func(\'#btn-header-main-login\');">' . __('Buy Now', 'jobhunt') . '</a>';
                        } else if (is_user_logged_in() && !((isset($user_role) && $user_role <> '' && $user_role == 'cs_employer') )) {
                            $cs_html .= '<a id="cs_emp_check_' . absint($cs_rand_id) . '" class="cs-color acc-submit">' . __('Buy Now', 'jobhunt') . '</a>';
                        } else {

                            $cs_html .= '<form method="post" action="' . add_query_arg(array('profile_tab' => 'packages'), get_permalink($cs_emp_dashboard)) . '">
							<input class="slct-cv-pkg cs-color acc-submit" type="submit" value="' . __('Buy Now', 'jobhunt') . '">
							<input type="hidden" name="cs_package" value="' . absint($package_id) . '" style="display:none; position:absolute;" />
							<input type="hidden" name="cs_job_pkg_trans" value="1">
							</form>';
                        }
                        $cs_html .= '</article>';
                    }
                }
                $cs_pkg_counter++;
            }
            if ($job_package_style == 'fancy') {
                $cs_html .= '</ul>';
            }
            if ($job_package_style == 'modern') {
                $cs_html .= '</ul>';
            }
        }
        if ($job_package_style == 'classic' || $job_package_style == 'fancy' || $job_package_style == 'modern' || $job_package_style == 'advance') {
            $cs_html .= '</div>';
        }


        if ($job_package_style == 'basic') {
            $cs_html .= '</div>';
        }
        $cs_html .= '</div>';
         if($column_class != ''){
        $cs_html .= '</div>';
        }
        return do_shortcode($cs_html);
    }

    add_shortcode('cs_job_package', 'cs_job_package_shortcode');
}