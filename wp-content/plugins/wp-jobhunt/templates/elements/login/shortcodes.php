<?php
/*
 *
 * Start Function  for shortcode of register for user
 *
 */
if (!function_exists('cs_register_shortcode')) {

    function cs_register_shortcode($atts, $content = "") {
        global $wpdb, $cs_plugin_options, $cs_form_fields_frontend, $cs_form_fields2, $cs_html_fields;
        cs_socialconnect_scripts(); // social login script
        $defaults = array('column_size' => '1/1', 'candidate_register_element_title' => '', 'register_title' => '', 'register_text' => '', 'register_role' => 'contributor', 'cs_register_class' => '', 'cs_register_animation' => '');
        extract(shortcode_atts($defaults, $atts));
        $column_size = isset($column_size) ? $column_size : '';
        //print_r($atts);
        $user_disable_text = __('User Registration is disabled', 'jobhunt');
        $cs_sitekey = isset($cs_plugin_options['cs_sitekey']) ? $cs_plugin_options['cs_sitekey'] : '';
        $cs_secretkey = isset($cs_plugin_options['cs_secretkey']) ? $cs_plugin_options['cs_secretkey'] : '';

        $cs_captcha_switch = isset($cs_plugin_options['cs_captcha_switch']) ? $cs_plugin_options['cs_captcha_switch'] : '';

        if ($cs_sitekey <> '' and $cs_secretkey <> '' and ! is_user_logged_in()) {
            cs_google_recaptcha_scripts();
            ?>
            <script>
                var recaptcha1;
                var recaptcha2;
                var recaptcha3;
                var recaptcha4;
                var cs_multicap = function () {
                    //Render the recaptcha1 on the element with ID "recaptcha1"
                    recaptcha1 = grecaptcha.render('recaptcha1', {
                        'sitekey': '<?php echo ($cs_sitekey); ?>', //Replace this with your Site key
                        'theme': 'light'
                    });
                    //Render the recaptcha2 on the element with ID "recaptcha2"
                    recaptcha2 = grecaptcha.render('recaptcha2', {
                        'sitekey': '<?php echo ($cs_sitekey); ?>', //Replace this with your Site key
                        'theme': 'light'
                    });
                    recaptcha3 = grecaptcha.render('recaptcha3', {
                        'sitekey': '<?php echo ($cs_sitekey); ?>', //Replace this with your Site key
                        'theme': 'light'
                    });
                    //Render the recaptcha2 on the element with ID "recaptcha2"
                    recaptcha4 = grecaptcha.render('recaptcha4', {
                        'sitekey': '<?php echo ($cs_sitekey); ?>', //Replace this with your Site key
                        'theme': 'light'
                    });
                };
            </script>
            <?php
        }

        // 
        $output = '';
        $registraion_div_rand_id = rand(5, 99999);
        $rand_id = rand(5, 99999);
        $rand_value = rand(0, 9999999);
        $role = $register_role;
        $output .='<div class="signup-form">';
        if (isset($candidate_register_element_title) && $candidate_register_element_title != '') {

            $output .= '<div class="cs-element-title">';
            $output .='<h4>' . $candidate_register_element_title . '</h4>';
            $output .= '</div>';
        }
        if (is_user_logged_in()) {
            $output .='<div class="alert alert-warning">' .
                    __('You have already logged in, Please logout to try again.', 'jobhunt') . '<a data-dismiss="alert" class="close" href="#">ï¿½</a>'
                    . '</div>';
        }
        $output .='<ul class="nav nav-tabs-page" role="tablist">';

        $output .='<li role="presentation" class="active">
                        <a href="#candidate' . $registraion_div_rand_id . '" onclick="javascript:cs_set_session(\'' . admin_url("admin-ajax.php") . '\',\'candidate\')" role="tab" data-toggle="tab" >
                        <i class="icon-user-add"></i>' . __('I am a Candidate', 'jobhunt') . '</a>
                    </li>';
        $output .='<li role="presentation" >
                        <a href="#employer' . $registraion_div_rand_id . '" onclick="javascript:cs_set_session(\'' . admin_url("admin-ajax.php") . '\',\'employer\')" role="tab" data-toggle="tab" ><i class="icon-briefcase4"></i>' . __('I am an Employer', 'jobhunt') . '</a></li>';

        $output .='</ul>';

        if (is_user_logged_in()) {

            $output .='<script>'
                    . 'jQuery("body").on("keypress", "input#user_login' . absint($rand_id) . ', input#user_pass' . absint($rand_id) . '", function (e) {
                    if (e.which == "13") {
                        show_alert_msg("' . __("Please logout first then try to login again", "jobhunt") . '");
                        return false;
                    }
                });'
                    . '</script>';
        } else {
            $output .='<script>'
                    . 'jQuery("body").on("keypress", "input#user_login' . absint($rand_id) . ', input#user_pass' . absint($rand_id) . '", function (e) {
                    if (e.which == "13") {
                        cs_user_authentication("' . esc_url(admin_url("admin-ajax.php")) . '", "' . absint($rand_id) . '");
                        return false;
                    }
                });'
                    . '</script>';
        }
        $output .= '<div class="input-info login-box login-from login-form-id-' . $rand_id . '">
                        <div class="scetion-title">
                            <h2>' . __('User Login', 'jobhunt') . '</h2>
                        </div>
                	<form method="post" class="wp-user-form webkit" id="ControlForm_' . $rand_id . '">
                            <div class="row">
                              <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                                    <label>' . __('Username', 'jobhunt') . '</label>';
        $cs_opt_array = array(
            'id' => '',
            'std' => __('Username', 'jobhunt'),
            'cust_id' => 'user_login_' . $rand_id,
            'cust_name' => 'user_login',
            'classes' => 'form-control',
            'extra_atr' => ' size="20" tabindex="11" onfocus="if(this.value ==\'Username\') { this.value = \'\'; }" onblur="if(this.value == \'\') { this.value =\'Username\'; }"',
            'return' => true,
        );
        $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);
        $output .= '
                              </div>
                              <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                                    <label>' . __('Password', 'jobhunt') . '</label>';
        $cs_opt_array = array(
            'id' => '',
            'std' => __('Password', 'jobhunt'),
            'cust_id' => 'user_pass' . $rand_id,
            'cust_name' => 'user_pass',
            'cust_type' => 'password',
            'classes' => 'form-control',
            'extra_atr' => ' size="20" tabindex="12" onfocus="if(this.value ==\'Username\') { this.value = \'\'; }" onblur="if(this.value == \'\') { this.value =\'Username\'; }"',
            'return' => true,
        );
        $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);
        $output .= '
                              </div>
                              <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                                <div class="row">
            ';
        if (is_user_logged_in()) {
            $output .= '<div class="col-md-6 col-lg-6 col-sm-12 col-xs-12">';

            $cs_opt_array = array(
                'id' => '',
                'std' => __('Log in', 'jobhunt'),
                'cust_id' => 'user-submit',
                'cust_name' => 'user-submit',
                'cust_type' => 'button',
                'extra_atr' => ' onclick="javascript:show_alert_msg(\'' . __("Please logout first then try to login again", "jobhunt") . '\')"',
                'classes' => 'user-submit backcolr cs-bgcolor acc-submit',
                'return' => true,
            );
            $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

            $output .= '
                   
            </div>';
        } else {
            $output .= '<div class="col-md-6 col-lg-6 col-sm-12 col-xs-12">';

            $cs_opt_array = array(
                'id' => '',
                'std' => __('Log in', 'jobhunt'),
                'cust_id' => 'user-submit',
                'cust_name' => 'user-submit',
                'cust_type' => 'button',
                'extra_atr' => ' onclick="javascript:cs_user_authentication(\'' . admin_url("admin-ajax.php") . '\',\'' . $rand_id . '\')"',
                'classes' => 'cs-bgcolor user-submit  backcolr  acc-submit',
                'return' => true,
            );
            $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

            $cs_opt_array = array(
                'std' => get_permalink(),
                'id' => 'redirect_to',
                'cust_name' => 'redirect_to',
                'cust_type' => 'hidden',
                'return' => true,
            );
            $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

            $cs_opt_array = array(
                'std' => '1',
                'id' => 'user_cookie',
                'cust_name' => 'user-cookie',
                'cust_type' => 'hidden',
                'return' => true,
            );
            $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

            $cs_opt_array = array(
                'id' => '',
                'std' => 'ajax_login',
                'cust_name' => 'action',
                'cust_type' => 'hidden',
                'return' => true,
            );
            $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

            $cs_opt_array = array(
                'std' => __('login', 'jobhunt'),
                'id' => 'login',
                'cust_name' => 'login',
                'cust_type' => 'hidden',
                'return' => true,
            );
            $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

            $output .= '
				<!--<span class="status status-message" style="display:none"></span>-->
				<a class="user-forgot-password-page" href="#">' . __(' Forgot Password?', 'jobhunt') . '</a>
            </div>';
        }
        $output .= '<div class="col-md-6 col-lg-6 col-sm-12 col-xs-12 login-section">
                        <i class="icon-user-add"></i>' . __('New to Us? ', 'jobhunt') . '  <a class="register-link-page" href="#">' . __('Register Here', 'jobhunt') . '</a>
                        </div>
                         <span class="status status-message" style="display:none"></span>
					</div>
					</div>
                <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                        <div class="form-bg">
                            <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">';
        /// Social login switche options
        $twitter_login = isset($cs_plugin_options['cs_twitter_api_switch']) ? $cs_plugin_options['cs_twitter_api_switch'] : '';
        $facebook_login = isset($cs_plugin_options['cs_facebook_login_switch']) ? $cs_plugin_options['cs_facebook_login_switch'] : '';
        $linkedin_login = isset($cs_plugin_options['cs_linkedin_login_switch']) ? $cs_plugin_options['cs_linkedin_login_switch'] : '';
        $google_login = isset($cs_plugin_options['cs_google_login_switch']) ? $cs_plugin_options['cs_google_login_switch'] : '';
        if ($twitter_login == 'on' || $facebook_login == 'on' || $linkedin_login == 'on' || $google_login == 'on') {
            ob_start();
            $output .='<h3>' . __('Signup / Signin with', 'jobhunt') . '</h3>';
            $output .= do_action('login_form');
            $output .= ob_get_clean();
        }
        $output .='</div>
						  	</div>
						</div>
					</div>
				</form>';
        $output .='</div>';







        $output .='<div class="input-info forgot-box login-from login-form-id-' . $rand_value . '" style="display:none;">';
        ob_start();
        $output .= do_shortcode('[cs_forgot_password]');
        $output .= ob_get_clean();
        $output .='</div>';
        $output .='<div class="tab-content tab-content-page">';
        $isRegistrationOn = get_option('users_can_register');
        if ($isRegistrationOn) {
            // registration page element
            $output .='<div id="employer' . $registraion_div_rand_id . '" role="tabpanel" class="tab-pane">';
            $output .='<div class="input-info">';
            $output .='<div class="row">';

            $output .='<script>'
                    . 'jQuery("body").on("keypress", "input#user_login' . absint($rand_value) . ', input#cs_user_email' . absint($rand_value) . ', input#cs_organization_name' . absint($rand_value) . ', input#cs_employer_specialisms' . absint($rand_value) . ', input#cs_phone_no' . absint($rand_value) . '", function (e) {
                    if (e.which == "13") {
                        cs_registration_validation("' . esc_url(admin_url("admin-ajax.php")) . '", "' . absint($rand_value) . '");
                        return false;
                    }
                });'
                    . '</script>';

            $output .= '<form method="post" class="wp-user-form " id="wp_signup_form_' . $rand_value . '" enctype="multipart/form-data">
                            <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">';

            $cs_opt_array = array(
                'id' => '',
                'std' => '',
                'cust_id' => 'user_login_' . $rand_value,
                'cust_name' => 'user_login' . $rand_value,
                'extra_atr' => ' size="20" tabindex="101" placeholder="' . __('Username', 'jobhunt') . '"',
                'classes' => 'form-control',
                'return' => true,
            );
            $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

            $output .= '	
                            </div>';
            $output .=$cs_form_fields_frontend->cs_form_text_render(
                    array('name' => __('Email', 'jobhunt'),
                        'id' => 'user_email' . $rand_value . '',
                        'classes' => 'col-md-12 col-lg-12 col-sm-12 col-xs-12',
                        'std' => '',
                        'description' => '',
                        'return' => true,
                        'hint' => ''
                    )
            );
            $output .=$cs_form_fields_frontend->cs_form_text_render(
                    array('name' => __('Organization Name', 'jobhunt'),
                        'id' => 'organization_name' . $rand_value . '',
                        'classes' => 'col-md-12 col-lg-12 col-sm-12 col-xs-12',
                        'std' => '',
                        'description' => '',
                        'return' => true,
                        'hint' => ''
                    )
            );
            $output .=$cs_form_fields_frontend->cs_form_hidden_render(
                    array('name' => __('Post Type', 'jobhunt'),
                        'id' => 'user_role_type' . $rand_value . '',
                        'classes' => 'col-md-12 col-lg-12 col-sm-12 col-xs-12',
                        'std' => 'employer',
                        'description' => '',
                        'return' => true,
                        'hint' => ''
                    )
            );
            $output .= '<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">';
            $output .='<div class="select-holder">';
            $output .= get_specialisms_dropdown('cs_employer_specialisms' . $rand_value, 'cs_employer_specialisms' . $rand_value, '', 'chosen-select form-control');
            $output .='</div>';
            $output .= '</div>';
            $output .= $cs_form_fields_frontend->cs_form_text_render(
                    array('name' => __('Phone Number', 'jobhunt'),
                        'id' => 'phone_no' . $rand_value . '',
                        'classes' => 'col-md-12 col-lg-12 col-sm-12 col-xs-12',
                        'std' => '',
                        'description' => '',
                        'return' => true,
                        'hint' => ''
                    )
            );
            $cs_rand_value = rand(54654, 99999965);
            if ($cs_captcha_switch == 'on' && (!is_user_logged_in())) {
                $output .='<div class="col-md-12 recaptcha-reload" id="recaptcha1_div">';
                $output .= cs_captcha('recaptcha1');
                $output .='</div>';
            }
            $output .= '<div class="upload-file">';
            $output .= '<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">'
                    . '<div class="row">';
            ob_start();
            $output .= do_action('register_form');
            $output .= ob_get_clean();
            if (is_user_logged_in()) {
                $output .= '<div class="col-md-6 col-lg-6 col-sm-12 col-xs-12">';
                $cs_opt_array = array(
                    'id' => '',
                    'std' => __('Create Account', 'jobhunt'),
                    'cust_id' => 'submitbtn' . $rand_value,
                    'cust_name' => 'user-submit',
                    'cust_type' => 'button',
                    'classes' => 'user-submit cs-bgcolor acc-submit',
                    'extra_atr' => ' tabindex="103" onclick="javascript:show_alert_msg(\'' . __("Please logout first then try to registration again", "jobhunt") . '\')"',
                    'return' => true,
                );
                $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);
            } else {
                $output .= '<div class="col-md-6 col-lg-6 col-sm-12 col-xs-12">';
                $cs_opt_array = array(
                    'id' => '',
                    'std' => __('Create Account', 'jobhunt'),
                    'cust_id' => 'submitbtn' . $rand_value,
                    'cust_name' => 'user-submit',
                    'cust_type' => 'button',
                    'classes' => 'cs-bgcolor user-submit acc-submit',
                    'extra_atr' => ' tabindex="103" onclick="javascript:cs_registration_validation(\'' . admin_url("admin-ajax.php") . '\',\'' . $rand_value . '\')"',
                    'return' => true,
                );
                $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

                $cs_opt_array = array(
                    'id' => '',
                    'std' => $role,
                    'cust_id' => 'register-role',
                    'cust_name' => 'role',
                    'cust_type' => 'hidden',
                    'return' => true,
                );
                $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

                $cs_opt_array = array(
                    'id' => '',
                    'std' => 'cs_registration_validation',
                    'cust_name' => 'action',
                    'cust_type' => 'hidden',
                    'return' => true,
                );
                $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);
            }
            $output .= '</div>';

            $output .= '<div class="col-md-6 col-lg-6 col-sm-12 col-xs-12 login-section">
			<i class="icon-user-add"></i>' . __(' Already have an account?', 'jobhunt') . ' <a href="#" class="login-link-page">' . __('Login Now', 'jobhunt') . '</a>';
            $output .= '</div>';

            $output .='</div>';
            $output .='<div id="result_' . $rand_value . '" class="status-message"><p class="status"></p></div>';
            $output .='</div>';
            $output .='</div>
                        </form>
                        <div class="register_content">' . do_shortcode($content . $register_text) . '</div>';
            $output .='</div>';
            $output .='</div>';
            $output .='</div>';
            // registration page element
            $output .='<div role="tabpanel" id="candidate' . $registraion_div_rand_id . '" class="tab-pane active">';
            $rand_id = rand(50, 99999);
            $output .='<div class="input-info">';
            if (is_user_logged_in()) {
                $output .='<script>'
                        . 'jQuery("body").on("keypress", "input#user_login' . absint($rand_id) . ', input#cs_user_email' . absint($rand_id) . ', input#cs_candidate_specialisms' . absint($rand_id) . ', input#cs_phone_no' . absint($rand_id) . '", function (e) {
                    if (e.which == "13") {
                        show_alert_msg("' . __("Please logout first then try to registration again", "jobhunt") . '");
                        return false;
                    }
                });'
                        . '</script>';
            } else {
                $output .='<script>'
                        . 'jQuery("body").on("keypress", "input#user_login' . absint($rand_id) . ', input#cs_user_email' . absint($rand_id) . ', input#cs_candidate_specialisms' . absint($rand_id) . ', input#cs_phone_no' . absint($rand_id) . '", function (e) {
                    if (e.which == "13") {
                        cs_registration_validation("' . esc_url(admin_url("admin-ajax.php")) . '", "' . absint($rand_id) . '");
                        return false;
                    }
                });'
                        . '</script>';
            }
            $output .='<div class="row">
                        <form method="post" class="wp-user-form " id="wp_signup_form_' . $rand_id . '" enctype="multipart/form-data">
                        
                    <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">';
            $cs_opt_array = array(
                'id' => '',
                'std' => '',
                'cust_id' => 'user_login_' . $rand_id,
                'cust_name' => 'user_login' . $rand_id,
                'classes' => 'form-control',
                'extra_atr' => ' size="20" tabindex="101" placeholder="' . __('Username', 'jobhunt') . '"',
                'return' => true,
            );
            $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

            $output .= '</div>';
            $output .=$cs_form_fields_frontend->cs_form_text_render(
                    array('name' => __('Email', 'jobhunt'),
                        'id' => 'user_email' . $rand_id . '',
                        'classes' => 'col-md-12 col-lg-12 col-sm-12 col-xs-12',
                        'std' => '',
                        'description' => '',
                        'return' => true,
                        'hint' => ''
                    )
            );
            $output .= '<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">';
            $output .='<div class="select-holder">';
            $output .= get_specialisms_dropdown('cs_candidate_specialisms' . $rand_id, 'cs_candidate_specialisms' . $rand_id, '', 'chosen-select form-control');
            $output .='</div>';
            $output .= '</div>';

            $output .=$cs_form_fields_frontend->cs_form_hidden_render(
                    array('name' => __('Post Type', 'jobhunt'),
                        'id' => 'user_role_type' . $rand_id . '',
                        'classes' => 'col-md-12 col-lg-12 col-sm-12 col-xs-12',
                        'std' => 'candidate',
                        'description' => '',
                        'return' => true,
                        'hint' => ''
                    )
            );
            $output .=$cs_form_fields_frontend->cs_form_text_render(
                    array('name' => __('Phone Number', 'jobhunt'),
                        'id' => 'phone_no' . $rand_id . '',
                        'classes' => 'col-md-12 col-lg-12 col-sm-12 col-xs-12',
                        'std' => '',
                        'description' => '',
                        'return' => true,
                        'hint' => ''
                    )
            );
            if ($cs_captcha_switch == 'on' && (!is_user_logged_in())) {
                $output .='<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12 recaptcha-reload" id="recaptcha2_div">';
                $output .= cs_captcha('recaptcha2');
                $output .='</div>';
            }
            $output .='<div class="upload-file">
                            <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                                <div class="row">';
            ob_start();
            $output .= do_action('register_form');
            $output .= ob_get_clean();

            if (is_user_logged_in()) {
                $output .= '<div class="col-md-6 col-lg-6 col-sm-12 col-xs-12">';

                $cs_opt_array = array(
                    'id' => '',
                    'std' => __('Create Account', 'jobhunt'),
                    'cust_id' => 'submitbtn' . $rand_id,
                    'cust_name' => 'user-submit',
                    'cust_type' => 'button',
                    'extra_atr' => ' tabindex="103" onclick="javascript:show_alert_msg(\'' . __("Please logout first then try to registration again", "jobhunt") . '\')"',
                    'classes' => 'cs-bgcolor user-submit  acc-submit',
                    'return' => true,
                );
                $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

                $output .= '
				<!--</div>-->
				</div>';
            } else {
                $output .= '<div class="col-md-6 col-lg-6 col-sm-12 col-xs-12">';

                $cs_opt_array = array(
                    'id' => '',
                    'std' => __('Create Account', 'jobhunt'),
                    'cust_id' => 'submitbtn' . $rand_id,
                    'cust_name' => 'user-submit',
                    'cust_type' => 'button',
                    'extra_atr' => ' tabindex="103" onclick="javascript:cs_registration_validation(\'' . admin_url("admin-ajax.php") . '\',\'' . $rand_id . '\')"',
                    'classes' => 'cs-bgcolor user-submit  acc-submit',
                    'return' => true,
                );
                $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

                $cs_opt_array = array(
                    'id' => '',
                    'std' => $role,
                    'cust_id' => 'login-role',
                    'cust_name' => 'role',
                    'cust_type' => 'hidden',
                    'return' => true,
                );
                $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

                $cs_opt_array = array(
                    'id' => '',
                    'std' => 'cs_registration_validation',
                    'cust_name' => 'action',
                    'cust_type' => 'hidden',
                    'return' => true,
                );
                $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

                $output .= '
                            </div>
                            ';
            }

            $output .='
                <div class="col-md-6 col-lg-6 col-sm-12 col-xs-12 login-section">
                                <i class="icon-user-add"></i> ' . __("Already have an account?", "jobhunt") . ' 
                                <a href="#" class="login-link-page">' . __('Login Now', 'jobhunt') . '</a>
                            </div>
                        </div>
                        </div>
                        <div id="result_' . $rand_id . '" class="status-message"><p class="status"></p></div>
                        </div>';
            $output .='</form>';
            $output .='</div>';

            /// Social login switche options
            $twitter_login = isset($cs_plugin_options['cs_twitter_api_switch']) ? $cs_plugin_options['cs_twitter_api_switch'] : '';
            $facebook_login = isset($cs_plugin_options['cs_facebook_login_switch']) ? $cs_plugin_options['cs_facebook_login_switch'] : '';
            $linkedin_login = isset($cs_plugin_options['cs_linkedin_login_switch']) ? $cs_plugin_options['cs_linkedin_login_switch'] : '';
            $google_login = isset($cs_plugin_options['cs_google_login_switch']) ? $cs_plugin_options['cs_google_login_switch'] : '';

            if ($twitter_login == 'on' || $facebook_login == 'on' || $linkedin_login == 'on' || $google_login == 'on') {
                $output .= '<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">';
                $output .= '<div class="form-bg">';
                $output .= '<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">';
                ob_start();
                if (class_exists('wp_jobhunt')) {
                    $output .='<h3>' . __('Signup / Signin with', 'jobhunt') . '</h3>';
                    $output .= do_action('login_form');
                }
                $output .= ob_get_clean();
                $output .= '</div>';
                $output .= '</div>';
                $output .= '</div>';
            }
            $output .= '
			</div></div>';

            $output .='<div class="register_content">' . do_shortcode($content . $register_text) . '</div>';
        } else {
            $output .='<div class="col-md-6 col-lg-6 col-sm-12 col-xs-12 register-page">';
            $output .='<div class="cs-user-register">
                        <div class="cs-element-title">
                            <h2>' . __('Register', 'jobhunt') . '</h2>
                        </div>
                        <p>' . $user_disable_text . '</p>';
            $output .='</div>';
            $output .='</div>';
        }
        $output .= '</div></div>';
        return $output;
    }

    add_shortcode('cs_register', 'cs_register_shortcode');
}
/*
 *
 * Start Function  for shortcode of user login
 * 
 *
 */

if (!function_exists('cs_user_login_shortcode')) {

    function cs_user_login_shortcode($atts, $content = "") {
        global $wpdb, $cs_plugin_options, $cs_form_fields_frontend, $cs_form_fields2;
        cs_socialconnect_scripts(); // social login script
        $defaults = array('column_size' => '1/1', 'register_title' => '', 'register_text' => '', 'register_role' => 'contributor', 'cs_type' => '', 'cs_login_txt' => '', 'login_btn_class' => '');
        extract(shortcode_atts($defaults, $atts));

        $user_disable_text = __('User Registration is disabled', 'jobhunt');
        $cs_sitekey = isset($cs_plugin_options['cs_sitekey']) ? $cs_plugin_options['cs_sitekey'] : '';
        $cs_secretkey = isset($cs_plugin_options['cs_secretkey']) ? $cs_plugin_options['cs_secretkey'] : '';
        $cs_captcha_switch = isset($cs_plugin_options['cs_captcha_switch']) ? $cs_plugin_options['cs_captcha_switch'] : '';

        $cs_demo_user_login_switch = isset($cs_plugin_options['cs_demo_user_login_switch']) ? $cs_plugin_options['cs_demo_user_login_switch'] : '';
        if ($cs_demo_user_login_switch == 'on') {
            $cs_job_demo_user_employer = isset($cs_plugin_options['cs_job_demo_user_employer']) ? $cs_plugin_options['cs_job_demo_user_employer'] : '';
            $cs_job_demo_user_candidate = isset($cs_plugin_options['cs_job_demo_user_candidate']) ? $cs_plugin_options['cs_job_demo_user_candidate'] : '';
        }
        $rand_id = rand(13243, 99999);
        cs_login_box_popup_scripts();
        if ($cs_sitekey <> '' and $cs_secretkey <> '' and ! is_user_logged_in()) {
            cs_google_recaptcha_scripts();
            ?>
            <script>
                var recaptcha1;
                var recaptcha2;
                var recaptcha3;
                var recaptcha4;
                var cs_multicap = function () {
                    //Render the recaptcha1 on the element with ID "recaptcha1"
                    recaptcha3 = grecaptcha.render('recaptcha3', {
                        'sitekey': '<?php echo ($cs_sitekey); ?>', //Replace this with your Site key
                        'theme': 'light'
                    });
                    //Render the recaptcha2 on the element with ID "recaptcha2"
                    recaptcha4 = grecaptcha.render('recaptcha4', {
                        'sitekey': '<?php echo ($cs_sitekey); ?>', //Replace this with your Site key
                        'theme': 'light'
                    });
                };

            </script>
            <?php
        }
        $output = '';
        if (is_user_logged_in()) {
            $output .= cs_profiletop_menu();
        } else {
            $role = $register_role;
            $cs_type = isset($cs_type) ? $cs_type : '';
            $cs_login_class = $cs_type == 'cv_elem' ? 'packge-login' : 'login';
            $output .='<div class="user-account">';
            $isRegistrationOn = get_option('users_can_register');
            if ($isRegistrationOn) {
                if ($cs_type != 'cv_elem') {
                    $output .='
            		<div class="join-us">';
                    $output .= '<i class="cs-color icon-pencil6"></i><a class="cs-color" data-target="#join-us" data-toggle="modal" href="#">' . __('Join Us', 'jobhunt') . '</a>';
                    $output .= '<div class="modal fade" id="join-us" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                  <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                      <div class="modal-header">
                                            <a class="close" data-dismiss="modal">&times;</a>
                                        <h4 id="myModalLabel" class="modal-title">' . __('Sign Up', 'jobhunt') . '</h4>
                                        </div>';
                    $output .='<div class="modal-body">';
                    $isRegistrationOn = get_option('users_can_register');
                    $popup_register_rand_divids = rand(0, 999999);
                    if ($isRegistrationOn) {
                        $output .='<ul class="nav nav-tabs" role="tablist">';
                        $output .='<li role="presentation" class="active">
                                    <a href="#candidate' . $popup_register_rand_divids . '" onclick="javascript:cs_set_session(\'' . admin_url("admin-ajax.php") . '\',\'candidate\')" role="tab" data-toggle="tab" ><i class="icon-user-add"></i>' . __('Candidate', 'jobhunt') . '</a>';
                        $output .='</li>';
                        $output .='<li role="presentation" >
                                        <a href="#employer' . $popup_register_rand_divids . '" onclick="javascript:cs_set_session(\'' . admin_url("admin-ajax.php") . '\',\'employer\')" 
                                        role="tab" data-toggle="tab" ><i class="icon-briefcase4"></i>' . __('Employer', 'jobhunt') . '</a>';
                        $output .='</li>';
                        $output .='</ul>';
                        $rand_ids = rand(0, 999999);

                        // popup registration forms
                        $output .='<div class="tab-content">';
                        // popup employer registration form
                        $output .='<div id="employer' . $popup_register_rand_divids . '" role="tabpanel" class="tab-pane">';
                        $output .= '<div id="result_' . $rand_ids . '" class="status-message"></div>';
                        $output .='<script>'
                                . 'jQuery("body").on("keypress", "input#user_login_3' . absint($rand_ids) . ', input#cs_user_email' . absint($rand_ids) . ', input#cs_organization_name' . absint($rand_ids) . ', input#cs_employer_specialisms' . absint($rand_ids) . ', input#cs_phone_no' . absint($rand_ids) . '", function (e) {
									if (e.which == "13") {
										cs_registration_validation("' . esc_url(admin_url("admin-ajax.php")) . '", "' . absint($rand_ids) . '");
										return false;
									}
									});'
                                . '</script>';
                        $output .='<form method="post" class="wp-user-form demo_test" id="wp_signup_form_' . $rand_ids . '" enctype="multipart/form-data">';
                        $output .='<label class="user">';

                        $cs_opt_array = array(
                            'id' => '',
                            'std' => '',
                            'cust_id' => 'user_login_3' . $rand_ids,
                            'cust_name' => 'user_login' . $rand_ids,
                            'extra_atr' => ' placeholder="' . __('Username', 'jobhunt') . '"',
                            'classes' => 'form-control',
                            'return' => true,
                        );
                        $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

                        $output .= '
                            </label>';

                        $output .='<label class="email">';
                        $output .=$cs_form_fields2->cs_form_text_render(
                                array('name' => __('Email', 'jobhunt'),
                                    'id' => 'user_email' . $rand_ids,
                                    'extra_atr' => ' placeholder="' . __('Email', 'jobhunt') . '"',
                                    'std' => '',
                                    'return' => true,
                                )
                        );
                        $output .= '</label>';
                        $output .= '<label class="orgniz">';
                        $output .= $cs_form_fields2->cs_form_text_render(
                                array('name' => __('Organization Name', 'jobhunt'),
                                    'id' => 'organization_name' . $rand_ids,
                                    'std' => '',
                                    'extra_atr' => ' placeholder="' . __('Organization Name', 'jobhunt') . '"',
                                    'return' => true,
                                )
                        );
                        $output .= '</label>';

                        $output .=$cs_form_fields_frontend->cs_form_hidden_render(
                                array('name' => 'user role type',
                                    'id' => 'user_role_type' . $rand_ids,
                                    'classes' => 'input-holder',
                                    'std' => 'employer',
                                    'description' => '',
                                    'return' => true,
                                    'hint' => '',
                                    'icon' => 'icon-user9'
                                )
                        );

                        $output .='<div class="side-by-side select-icon clearfix">';
                        $output .='<div class="select-holder">';
                        $output .= get_specialisms_dropdown('cs_employer_specialisms' . $rand_ids, 'cs_employer_specialisms' . $rand_ids, '', 'chosen-select form-control');
                        $output .='</div>';
                        $output .='</div>';
                        $output .='<label class="phone">';
                        $output .=$cs_form_fields2->cs_form_text_render(
                                array('name' => __('Phone Number', 'jobhunt'),
                                    'id' => 'phone_no' . $rand_ids,
                                    'std' => '',
                                    'extra_atr' => ' placeholder=" ' . __('Phone Number', 'jobhunt') . '"',
                                    'return' => true,
                                )
                        );
                        $output .='</label>';
                        if ($cs_captcha_switch == 'on' && (!is_user_logged_in())) {
                            $output .='<div class="recaptcha-reload" id="recaptcha3_div">';
                            $output .= cs_captcha('recaptcha3');
                            $output .='</div>';
                        }
                        $output .= '<div class="checks-holder">';
                        ob_start();
                        $output .= do_action('register_form');
                        $output .= ob_get_clean();
                        $cs_rand_id = rand(122, 1545464897);
                        $output .= '<label>';
                        $cs_opt_array = array(
                            'std' => __('Sign Up', 'jobhunt'),
                            'cust_id' => 'submitbtn' . $cs_rand_id,
                            'cust_name' => 'user-submit',
                            'cust_type' => 'button',
                            'classes' => 'user-submit cs-bgcolor acc-submit',
                            'extra_atr' => ' tabindex="103" onclick="javascript:cs_registration_validation(\'' . admin_url("admin-ajax.php") . '\',\'' . $rand_ids . '\')"',
                            'return' => true,
                        );
                        $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);
                        $cs_opt_array = array(
                            'id' => '',
                            'std' => $role,
                            'cust_id' => 'signin-role',
                            'cust_name' => 'role',
                            'cust_type' => 'hidden',
                            'return' => true,
                        );
                        $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

                        $cs_opt_array = array(
                            'id' => '',
                            'std' => 'cs_registration_validation',
                            'cust_name' => 'action',
                            'cust_type' => 'hidden',
                            'return' => true,
                        );
                        $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

                        $output .= '
                                    </label>';
                        $output .= '</div>';

                        $output .= '</form>
                                    <div class="register_content">' . do_shortcode($content . $register_text) . '</div>';
                        $output .='</div>';
                        // popup candidate registration form
                        $output .='<div role="tabpanel" class="tab-pane active" id="candidate' . $popup_register_rand_divids . '">';
                        $rand_ids = rand(0, 999999);
                        $rand_id = rand(0, 999999);
                        $output .= '<div id="result_' . $rand_id . '" class="status-message"></div>';

                        $output .='<script>'
                                . 'jQuery("body").on("keypress", "input#user_login4' . absint($rand_id) . ', input#cs_user_email' . absint($rand_id) . ', input#cs_candidate_specialisms' . absint($rand_id) . ', input#cs_phone_no' . absint($rand_id) . '", function (e) {
                                    if (e.which == "13") {
                                        cs_registration_validation("' . esc_url(admin_url("admin-ajax.php")) . '", "' . absint($rand_id) . '");
                                        return false;
                                    }
                                    });'
                                . '</script>';

                        $output .= '<div class="login-with">';
                        ob_start();
                        if (class_exists('wp_jobhunt')) {
                            $output .= do_action('login_form');
                        }
                        $output .= ob_get_clean();
                        $output .= '</div>';
                        $output .= '<div class="cs-separator"><span>' . __('Or', 'jobhunt') . '</span></div>';
                        $output .='<form method="post" class="wp-user-form" id="wp_signup_form_' . $rand_id . '" enctype="multipart/form-data">';
                        $output .= '<label class="user">';

                        $cs_opt_array = array(
                            'id' => '',
                            'std' => '',
                            'cust_id' => 'user_login4' . $rand_id,
                            'cust_name' => 'user_login' . $rand_id,
                            'extra_atr' => ' placeholder="' . __('Username', 'jobhunt') . '"',
                            'return' => true,
                        );
                        $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

                        $output .= '</label>';
                        $output .='<label class="email">';
                        $output .=$cs_form_fields2->cs_form_text_render(
                                array(
                                    'id' => 'user_email' . $rand_id,
                                    'std' => '',
                                    'extra_atr' => ' placeholder="' . __('Email', 'jobhunt') . '"',
                                    'return' => true,
                                )
                        );
                        $output .= '</label>';
                        $output .='<div class="side-by-side select-icon clearfix">';
                        $output .='<div class="select-holder">';
                        $output .= get_specialisms_dropdown('cs_candidate_specialisms' . $rand_id, 'cs_candidate_specialisms' . $rand_id, '', 'chosen-select form-control');
                        $output .='</div>';
                        $output .='</div>';
                        $output .=$cs_form_fields_frontend->cs_form_hidden_render(
                                array('name' => __('user role type', 'jobhunt'),
                                    'id' => 'user_role_type' . $rand_id,
                                    'classes' => 'input-holder',
                                    'std' => 'candidate',
                                    'description' => '',
                                    'return' => true,
                                    'hint' => ''
                                )
                        );
                        $output .='<label class="phone">';
                        $output .=$cs_form_fields2->cs_form_text_render(
                                array(
                                    'id' => 'phone_no' . $rand_id,
                                    'std' => '',
                                    'extra_atr' => ' placeholder="' . __('Phone Number', 'jobhunt') . '"',
                                    'return' => true,
                                )
                        );
                        $output .= '</label>';
                        if ($cs_captcha_switch == 'on' && (!is_user_logged_in())) {
                            $output .='<div class="input-holder recaptcha-reload" id="recaptcha4_div">';
                            $output .= cs_captcha('recaptcha4');
                            $output .='</div>';
                        }
                        ob_start();
                        $output .= do_action('register_form');
                        $output .= ob_get_clean();
                        $output .= '<div class="checks-holder">';
                        $cs_rand_id_value = rand(65454, 799845187);
                        $cs_rand_id_values = rand(65454, 799845187);
                        $output .= '<label>';

                        $cs_opt_array = array(
                            'std' => __('Sign Up', 'jobhunt'),
                            'cust_id' => 'submitbtn' . $cs_rand_id_value,
                            'cust_name' => 'user-submit',
                            'cust_type' => 'button',
                            'extra_atr' => ' tabindex="103" onclick="javascript:cs_registration_validation(\'' . admin_url("admin-ajax.php") . '\',\'' . $rand_id . '\')"',
                            'classes' => 'user-submit cs-bgcolor acc-submit',
                            'return' => true,
                        );

                        $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

                        $cs_opt_array = array(
                            'id' => '',
                            'std' => $role,
                            'cust_id' => 'signup-role',
                            'cust_name' => 'role',
                            'cust_type' => 'hidden',
                            'return' => true,
                        );
                        $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

                        $cs_opt_array = array(
                            'id' => '',
                            'std' => 'cs_registration_validation',
                            'cust_name' => 'action',
                            'cust_type' => 'hidden',
                            'return' => true,
                        );
                        $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

                        $output .= '
                                    </label>';
                        $output .= '</div>';

                        $output .= '</form>';
                        $output .= '<div class="register_content">' . do_shortcode($content . $register_text) . '</div>';

                        $output .='</div>';
                        $output .='</div>';
                    } else {
                        $output .='<div class="col-md-6 col-lg-6 col-sm-12 col-xs-12 register-page">
                                        <div class="cs-user-register">
                                            <div class="cs-element-title">
                                                   <h2>' . __('Register', 'jobhunt') . '</h2>
                                           </div>
                                           <p>' . $user_disable_text . '</p>
                                        </div>
                                    </div>
                            </div>';
                        $output .='</div>';
                    }
                    $output .= '</div>';
                    $output .= '</div>';

                    $output .= '
            	 	  </div>
				    </div>
			      </div>';
                }
            }
            $output .='
			<div class="login">';
            $login_btn_class_str = '';
            if ($login_btn_class != '') {
                $login_btn_class_str = 'class="' . $login_btn_class . '"';
            }
            if ($cs_type == 'cv_elem') {
                $cs_log_text = isset($cs_login_txt) && $cs_login_txt != '' ? $cs_login_txt : __('Buy Now', 'jobhunt');
                $output .='<a id="btn-header-main-login" data-target="#sign-in" class="cs-login-switch cs-bgcolor" data-toggle="modal" href="#">' . $cs_log_text . '</a>';
            } else {
                $output .='<a id="btn-header-main-login" data-target="#sign-in" data-toggle="modal" class="cs-login-switch cs-bgcolor" href="#"><i class="icon-login"></i>' . __('Sign in', 'jobhunt') . '</a>';
            }
            $output .='<div class="modal fade" id="sign-in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
					  <div class="modal-dialog" role="document">
					   <div class="modal-content">
						';

            $output .='<div class="modal-body">';
            $output .='<div class="login-form cs-login-pbox login-form-id-' . $rand_id . '">';
            $output .='<div class="modal-header">
                            <a class="close" data-dismiss="modal">&times;</a>
                            <h4 class="modal-title">' . __('User Login', 'jobhunt') . '</h4>
                     </div>'
                    . '<div class="status status-message"></div>';
            if (is_user_logged_in()) {
                $output .='<script>'
                        . 'jQuery("body").on("keypress", "input#user_login' . absint($rand_id) . ', input#user_pass' . absint($rand_id) . '", function (e) {
                                    if (e.which == "13") {
                                        show_alert_msg("' . __("Please logout first then try to login again", "jobhunt") . '");
                                        return false;
                                    }
                            });'
                        . '</script>';
            } else {
                $output .='<script>'
                        . 'jQuery("body").on("keypress", "input#user_login' . absint($rand_id) . ', input#user_pass' . absint($rand_id) . '", function (e) {
                                if (e.which == "13") {
                                    cs_user_authentication("' . esc_url(admin_url("admin-ajax.php")) . '", "' . absint($rand_id) . '");
                                    return false;
                                }
                            });'
                        . '</script>';
            }

            $output .='<form method="post" class="wp-user-form webkit" id="ControlForm_' . $rand_id . '">';
            if ($cs_demo_user_login_switch == 'on') {
                $demo_user_password = esc_html('demo123');
                $cs_job_demo_employer_detail = get_user_by('id', $cs_job_demo_user_employer);
                require_once( ABSPATH . 'wp-includes/class-phpass.php');
                $wp_hasher = new PasswordHash(8, TRUE);
                if (!(isset($cs_job_demo_employer_detail->user_pass) && $wp_hasher->CheckPassword($demo_user_password, $cs_job_demo_employer_detail->user_pass))) {
                    wp_set_password($demo_user_password, $cs_job_demo_user_employer);
                }
                $cs_job_demo_candidate_detail = get_user_by('id', $cs_job_demo_user_candidate);

                if (!(isset($cs_job_demo_candidate_detail->user_pass) && $wp_hasher->CheckPassword($demo_user_password, $cs_job_demo_candidate_detail->user_pass))) {
                    wp_set_password($demo_user_password, $cs_job_demo_user_candidate);
                }
                $cs_job_demo_candidate_detail_user = isset($cs_job_demo_candidate_detail->user_login) ? $cs_job_demo_candidate_detail->user_login : '';
                $cs_job_demo_employer_detail_user = isset($cs_job_demo_employer_detail->user_login) ? $cs_job_demo_employer_detail->user_login : '';

                $output .='<div class="cs-demo-login">';
                $output .='<div class="cs-demo-login-lable">' . __('Click to login with Demo User', 'jobhunt') . '</div>';
                $output .='<ul class="nav nav-tabs">';
                $output .='<li>'
                        . '<a href="javascript:void(0)" onclick="javascript:cs_demo_user_login(\'' . $cs_job_demo_candidate_detail_user . '\')" '
                        . '><i class="icon-user-add"></i>' . __('Candidate', 'jobhunt')
                        . '</a>';
                $output .='</li>';
                $output .='<li>'
                        . '<a href="javascript:void(0)" onclick="javascript:cs_demo_user_login(\'' . $cs_job_demo_employer_detail_user . '\')" '
                        . '><i class="icon-briefcase4"></i>' . __('Employer', 'jobhunt')
                        . '</a>';
                $output .='</li>';
                $output .='</ul>';
                $output .='</div>';
                $output .='<script>
                    function cs_demo_user_login(user) {
                        jQuery("#user_login' . $rand_id . '" ).val(user);
                        jQuery("#user_pass' . $rand_id . '" ).val("' . $demo_user_password . '");
                        cs_user_authentication(\'' . admin_url("admin-ajax.php") . '\',\'' . $rand_id . '\');
                    }
                </script>';
            }
            $output .='<label class="user">';

            $cs_opt_array = array(
                'id' => '',
                'std' => '',
                'cust_id' => 'user_login' . $rand_id,
                'cust_name' => 'user_login',
                'classes' => 'form-control',
                'extra_atr' => ' tabindex="11" placeholder="' . __('Username', 'jobhunt') . '"',
                'return' => true,
            );
            $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

            $output .='</label>';

            $output .='<label class="password">';

            $cs_opt_array = array(
                'id' => '',
                'std' => __('Password', 'jobhunt'),
                'cust_id' => 'user_pass' . $rand_id,
                'cust_name' => 'user_pass',
                'cust_type' => 'password',
                'classes' => 'form-control',
                'extra_atr' => ' tabindex="12" size="20" onfocus="if(this.value ==\'' . __('Password', 'jobhunt') . '\') { this.value = \'\'; }" onblur="if(this.value == \'\') { this.value =\'' . __('Password', 'jobhunt') . '\'; }"',
                'return' => true,
            );
            $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

            $output .='</label>';

            if (is_user_logged_in()) {
                $output .='<label>';
                $cs_opt_array = array(
                    'std' => __('Log in', 'jobhunt'),
                    'cust_name' => 'user-submit',
                    'cust_type' => 'button',
                    'classes' => 'cs-bgcolor',
                    'extra_atr' => ' onclick="javascript:show_alert_msg(\'' . __("Please logout first then try to login again", "jobhunt") . '\')"',
                    'return' => true,
                );
                $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

                $output .= '</label>';
            } else {
                $output .='<label>';
                $cs_opt_array = array(
                    'std' => __('Log in', 'jobhunt'),
                    'cust_name' => 'user-submit',
                    'cust_type' => 'button',
                    'classes' => 'cs-bgcolor',
                    'extra_atr' => ' onclick="javascript:cs_user_authentication(\'' . admin_url("admin-ajax.php") . '\',\'' . $rand_id . '\')"',
                    'return' => true,
                );
                $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

                $cs_opt_array = array(
                    'id' => '',
                    'std' => get_permalink(),
                    'cust_id' => 'redirect_to',
                    'cust_name' => 'redirect_to',
                    'cust_type' => 'hidden',
                    'return' => true,
                );
                $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

                $cs_opt_array = array(
                    'id' => '',
                    'std' => '1',
                    'cust_id' => 'user-cookie',
                    'cust_name' => 'user-cookie',
                    'cust_type' => 'hidden',
                    'return' => true,
                );
                $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

                $cs_opt_array = array(
                    'id' => '',
                    'std' => 'ajax_login',
                    'cust_name' => 'action',
                    'cust_type' => 'hidden',
                    'return' => true,
                );
                $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

                $cs_opt_array = array(
                    'id' => '',
                    'std' => 'login',
                    'cust_id' => 'login',
                    'cust_name' => 'login',
                    'cust_type' => 'hidden',
                    'return' => true,
                );
                $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

                $output .= '
                            
			</label>';
            }

            $output .='</form>';
            $output .='<div class="forget-password"><i class="icon-help"></i><a class="cs-forgot-switch">' . __('Forgot Password?', 'jobhunt') . '</a></div>';

            ob_start();
            $isRegistrationOn = get_option('users_can_register');
            /// Social login switche options
            $twitter_login = isset($cs_plugin_options['cs_twitter_api_switch']) ? $cs_plugin_options['cs_twitter_api_switch'] : '';
            $facebook_login = isset($cs_plugin_options['cs_facebook_login_switch']) ? $cs_plugin_options['cs_facebook_login_switch'] : '';
            $linkedin_login = isset($cs_plugin_options['cs_linkedin_login_switch']) ? $cs_plugin_options['cs_linkedin_login_switch'] : '';
            $google_login = isset($cs_plugin_options['cs_google_login_switch']) ? $cs_plugin_options['cs_google_login_switch'] : '';

            if ($isRegistrationOn && ($twitter_login == 'on' || $facebook_login == 'on' || $linkedin_login == 'on' || $google_login == 'on')) {
                $output .='<div class="cs-separator"><span>' . __('Or', 'jobhunt') . '</span></div>';
            }
            $output .= do_action('login_form');
            $output .= ob_get_clean();

            $output .= '</div>';
            $output .= '</div>';
            $output .= '<div class="content-style-form cs-forgot-pbox content-style-form-2" style="display:none;">';
            ob_start();
            $output .= do_shortcode('[cs_forgot_password cs_type="popup"]');
            $output .= ob_get_clean();
            $output .= '</div>';
            $output .= '</div>
                    </div>
               </div>';

            $output .='
            </div>';

            $output .='
        </div>';
        }
		return $output;
    }

    add_shortcode('cs_user_login', 'cs_user_login_shortcode');
}
