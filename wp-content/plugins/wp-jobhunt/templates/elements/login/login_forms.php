<?php
//=====================================================================
// Sign In With Social Media
//=====================================================================

if (!function_exists('jobcareer_pb_register')) {

    function jobcareer_pb_register($die = 0) {

        global $cs_form_fields2, $cs_html_fields;
        $shortcode_element = '';
        $filter_element = 'filterdrag';
        $shortcode_view = '';
        $output = array();
        $PREFIX = 'cs_register';
        $counter = $_POST['counter'];

        $cs_counter = $_POST['counter'];
        if (isset($_POST['action']) && !isset($_POST['shortcode_element_id'])) {
            $POSTID = '';
            $shortcode_element_id = '';
        } else {
            $parseObject = new ShortcodeParse();
            $POSTID = $_POST['POSTID'];
            $shortcode_element_id = $_POST['shortcode_element_id'];
            $shortcode_str = stripslashes($shortcode_element_id);
            $output = $parseObject->cs_shortcodes($output, $shortcode_str, true, $PREFIX);
        }
        $defaults = array('candidate_register_element_title' => '');
        if (isset($output['0']['atts'])) {
            $atts = $output['0']['atts'];
        } else {
            $atts = array();
        }
        if (isset($output['0']['content'])) {
            $atts_content = $output['0']['content'];
        } else {
            $atts_content = array();
        }
        $button_element_size = '100';
        foreach ($defaults as $key => $values) {
            if (isset($atts[$key])) {
                $$key = $atts[$key];
            } else {
                $$key = $values;
            }
        }
        $name = 'jobcareer_pb_register';

        $coloumn_class = 'column_' . $button_element_size;

        if (isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode') {
            $shortcode_element = 'shortcode_element_class';
            $shortcode_view = 'cs-pbwp-shortcode';
            $filter_element = 'ajax-drag';
            $coloumn_class = '';
        }

        $rand_id = rand(45, 897009);
        ?>

        <div id="<?php echo esc_attr($name . $cs_counter); ?>_del" class="column  parentdelete <?php echo esc_attr($coloumn_class); ?> <?php echo esc_attr($shortcode_view); ?>" item="register" data="<?php echo jobcareer_element_size_data_array_index($button_element_size) ?>" >
            <?php cs_element_setting($name, $cs_counter, $button_element_size, '', 'heart'); ?>
            <div class="cs-wrapp-class-<?php echo esc_attr($cs_counter) ?> <?php echo esc_attr($shortcode_element); ?>" id="<?php echo esc_attr($name . $cs_counter) ?>" data-shortcode-template="[cs_register {{attributes}}]" style="display: none;">
                <div class="cs-heading-area">

                    <h5><?php _e('JC: Register Options', 'jobhunt'); ?></h5>
                    <a href="javascript:removeoverlay('<?php echo esc_attr($name . $cs_counter) ?>','<?php echo esc_attr($filter_element); ?>')" class="cs-btnclose"><i class="icon-times"></i></a> 
                </div>
                <div class="cs-pbwp-content">
                    <div class="cs-wrapp-clone cs-shortcode-wrapp cs-pbwp-content">

                    </div>
                    <div class="cs-wrapp-clone cs-shortcode-wrapp">
                        <?php
                        $cs_opt_array = array(
                            'name' => __('Element Title', 'jobhunt'),
                            'desc' => '',
                            //'hint_text' => __("Enter title of Register", "jobhunt"),
                            'echo' => true,
                            'field_params' => array(
                                'std' => $candidate_register_element_title,
                                'id' => 'candidate_register_element_title',
                                'cust_name' => 'candidate_register_element_title[]',
                                'return' => true,
                            ),
                        );

                        $cs_html_fields->cs_text_field($cs_opt_array);
                        ?>
                    </div>
                    <?php if (isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode') {
                        ?>
                        <ul class="form-elements insert-bg">
                            <li class="to-field"> <a class="insert-btn cs-main-btn" onclick="javascript:Shortcode_tab_insert_editor('<?php echo esc_js(str_replace('jobcareer_pb_', '', $name)); ?>', '<?php echo esc_js($name . $cs_counter) ?>', '<?php echo esc_js($filter_element); ?>')" ><?php _e('Insert', 'jobhunt'); ?></a> </li>
                        </ul>
                        <div id="results-shortocde"></div>
                        <?php
                    } else {

                        $cs_opt_array = array(
                            'std' => __('register', 'jobhunt'),
                            'id' => '',
                            'before' => '',
                            'after' => '',
                            'classes' => '',
                            'extra_atr' => '',
                            'cust_id' => '',
                            'cust_name' => 'cs_orderby[]',
                            'return' => true,
                            'required' => false
                        );
                        echo $cs_form_fields2->cs_form_hidden_render($cs_opt_array);


                        $cs_opt_array = array(
                            'name' => '',
                            'desc' => '',
                            'hint_text' => '',
                            'echo' => true,
                            'field_params' => array(
                                'std' => __('Save', 'jobhunt'),
                                'cust_id' => '',
                                'cust_type' => 'button',
                                'classes' => 'cs-admin-btn',
                                'cust_name' => '',
                                'extra_atr' => 'onclick="javascript:_removerlay(jQuery(this))"',
                                'return' => true,
                            ),
                        );

                        $cs_html_fields->cs_text_field($cs_opt_array);
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php
        if ($die <> 1) {
            die();
        }
    }

    add_action('wp_ajax_jobcareer_pb_register', 'jobcareer_pb_register');
}

/*
 *
 * Start Function  how to login from social site(facebook, linkedin,twitter,etc)
 *
 */
if (!function_exists('cs_social_login_form')) {

    function cs_social_login_form($args = NULL) {

        require_once ('cs-social-login/linkedin/linkedin_function.php');
        global $cs_plugin_options, $cs_form_fields2;
        $display_label = false;
        // check for admin login form
        $admin_page = '0';
        if (in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'))) {
            $admin_page = '1';
        }
        if (get_option('users_can_register') && $admin_page == 0) {
            if ($args == NULL)
                $display_label = true;
            elseif (is_array($args))
                extract($args);
            if (!isset($images_url))
                $images_url = wp_jobhunt::plugin_url() . 'directory-login/cs-social-login/media/img/';
            $facebook_app_id = '';
            $facebook_secret = '';
            if (isset($cs_plugin_options['cs_dashboard'])) {
                $cs_dashboard_link = get_permalink($cs_plugin_options['cs_dashboard']);
            }
            $twitter_enabled = isset($cs_plugin_options['cs_twitter_api_switch']) ? $cs_plugin_options['cs_twitter_api_switch'] : '';
            $facebook_enabled = isset($cs_plugin_options['cs_facebook_login_switch']) ? $cs_plugin_options['cs_facebook_login_switch'] : '';
            $google_enabled = isset($cs_plugin_options['cs_google_login_switch']) ? $cs_plugin_options['cs_google_login_switch'] : '';
            $linkedin_enabled = isset($cs_plugin_options['cs_linkedin_login_switch']) ? $cs_plugin_options['cs_linkedin_login_switch'] : '';
            if (isset($cs_plugin_options['cs_facebook_app_id']))
                $facebook_app_id = $cs_plugin_options['cs_facebook_app_id'];
            if (isset($cs_plugin_options['cs_facebook_secret']))
                $facebook_secret = $cs_plugin_options['cs_facebook_secret'];
            if (isset($cs_plugin_options['cs_consumer_key']))
                $twitter_app_id = $cs_plugin_options['cs_consumer_key'];
            if (isset($cs_plugin_options['cs_google_client_id']))
                $google_app_id = $cs_plugin_options['cs_google_client_id'];
            if (isset($cs_plugin_options['cs_linkedin_app_id']))
                $linkedin_app_id = $cs_plugin_options['cs_linkedin_app_id'];
            if (isset($cs_plugin_options['cs_linkedin_secret']))
                $linkedin_secret = $cs_plugin_options['cs_linkedin_secret'];
            if ($twitter_enabled == 'on' || $facebook_enabled == 'on' || $google_enabled == 'on' || $linkedin_enabled == 'on') :
                $rand_id = rand(0, 98989899);
                $isRegistrationOn = get_option('users_can_register');
                if ($isRegistrationOn) {
                    ?>
                    <div class="footer-element comment-form-social-connect social_login_ui <?php if (strpos($_SERVER['REQUEST_URI'], 'wp-signup.php')) echo 'mu_signup'; ?>">
                        <div class="social_login_facebook_auth">
                            <?php
                            $cs_opt_array = array(
                                'id' => '',
                                'std' => esc_attr($facebook_app_id),
                                'cust_id' => "",
                                'cust_name' => "client_id",
                                'classes' => '',
                            );
                            $cs_form_fields2->cs_form_hidden_render($cs_opt_array);
                            $cs_opt_array = array(
                                'id' => '',
                                'std' => home_url('index.php?social-login=facebook-callback'),
                                'cust_id' => "",
                                'cust_name' => "redirect_uri",
                                'classes' => '',
                            );
                            $cs_form_fields2->cs_form_hidden_render($cs_opt_array);
                            ?>
                        </div>
                        <div class="social_login_twitter_auth">
                            <?php
                            $cs_opt_array = array(
                                'id' => '',
                                'std' => esc_attr($twitter_app_id),
                                'cust_id' => "",
                                'cust_name' => "client_id",
                                'classes' => '',
                            );
                            $cs_form_fields2->cs_form_hidden_render($cs_opt_array);
                            $cs_opt_array = array(
                                'id' => '',
                                'std' => home_url('index.php?social-login=twitter'),
                                'cust_id' => "",
                                'cust_name' => "redirect_uri",
                                'classes' => '',
                            );
                            $cs_form_fields2->cs_form_hidden_render($cs_opt_array);
                            ?>
                        </div>
                        <div class="social_login_google_auth">
                            <?php
                            $cs_opt_array = array(
                                'id' => '',
                                'std' => esc_attr($google_app_id),
                                'cust_id' => "",
                                'cust_name' => "client_id",
                                'classes' => '',
                            );
                            $cs_form_fields2->cs_form_hidden_render($cs_opt_array);
                            $cs_opt_array = array(
                                'id' => '',
                                'std' => cs_google_login_url() . (isset($_GET['redirect_to']) ? '&redirect=' . $_GET['redirect_to'] : ''),
                                'cust_id' => "",
                                'cust_name' => "redirect_uri",
                                'classes' => '',
                            );
                            $cs_form_fields2->cs_form_hidden_render($cs_opt_array);
                            ?>
                        </div>
                        <?php if ($linkedin_enabled == 'on') { ?>
                            <div class="social_login_linkedin_auth">
                                <?php
                                $cs_opt_array = array(
                                    'id' => '',
                                    'std' => 'initiate',
                                    'cust_id' => 'ltype',
                                    'cust_name' => 'ltype',
                                    'classes' => '',
                                );
                                $cs_form_fields2->cs_form_hidden_render($cs_opt_array);
                                $cs_opt_array = array(
                                    'id' => '',
                                    'std' => home_url('index.php?social-login=linkedin'),
                                    'cust_id' => "",
                                    'cust_name' => "redirect_uri",
                                    'classes' => '',
                                );
                                $cs_form_fields2->cs_form_hidden_render($cs_opt_array);
                                ?>
                            </div>
                        <?php } ?>
                        <div class="social-media">

                            <ul>	 
                                <?php
                                if (is_user_logged_in()) {

                                    // remove id from all links
                                    if ($facebook_enabled == 'on') :
                                        echo apply_filters('social_login_login_facebook', '<li><a onclick="javascript:show_alert_msg(\'' . __("Please logout first then try to login again", "jobhunt") . '\')" href="javascript:void(0);" title="Facebook" data-original-title="Facebook" class=" facebook"><span class="social-mess-top fb-social-login" style="display:none">' . __('Please set API key', 'jobhunt') . '</span><i class="icon-facebook2"></i></a></li>');
                                    endif;
                                    if ($twitter_enabled == 'on') :
                                        echo apply_filters('social_login_login_twitter', '<li><a onclick="javascript:show_alert_msg(\'' . __("Please logout first then try to login again", "jobhunt") . '\')" href="javascript:void(0);" title="Twitter" data-original-title="twitter" class="twitter"><span class="social-mess-top tw-social-login" style="display:none">' . __('Please set API key', 'jobhunt') . '</span><i class="icon-twitter2"></i></a></li>');
                                    endif;
                                    if ($google_enabled == 'on') :
                                        echo apply_filters('social_login_login_google', '<li><a onclick="javascript:show_alert_msg(\'' . __("Please logout first then try to login again", "jobhunt") . '\')" href="javascript:void(0);" rel="nofollow" title="google-plus" data-original-title="google-plus" class="gplus"><span class="social-mess-top gplus-social-login" style="display:none">' . __('Please set API key', 'jobhunt') . '</span><i class="icon-google-plus"></i></a></li>');
                                    endif;
                                    if ($linkedin_enabled == 'on') :
                                        echo apply_filters('social_login_login_linkedin', '<li><a onclick="javascript:show_alert_msg(\'' . __("Please logout first then try to login again", "jobhunt") . '\')" href="javascript:void(0);" rel="nofollow" title="linked-in" data-original-title="linked-in" class="linkedin" data-applyjobid=""><span class="social-mess-top linkedin-social-login" style="display:none">' . __('Please set API key', 'jobhunt') . '</span><i class="icon-linkedin2"></i></a></li>');
                                    endif;
                                } else {
                                    // remove id from all links
                                    if ($facebook_enabled == 'on') :
                                        echo apply_filters('social_login_login_facebook', '<li><a href="javascript:void(0);" title="Facebook" data-original-title="Facebook" class="social_login_login_facebook facebook"><span class="social-mess-top fb-social-login" style="display:none">' . __('Please set API key', 'jobhunt') . '</span><i class="icon-facebook2"></i></a></li>');
                                    endif;
                                    if ($twitter_enabled == 'on') :
                                        echo apply_filters('social_login_login_twitter', '<li><a href="javascript:void(0);" title="Twitter" data-original-title="twitter" class="social_login_login_twitter twitter"><span class="social-mess-top tw-social-login" style="display:none">' . __('Please set API key', 'jobhunt') . '</span><i class="icon-twitter2"></i></a></li>');
                                    endif;
                                    if ($google_enabled == 'on') :
                                        echo apply_filters('social_login_login_google', '<li><a  href="javascript:void(0);" rel="nofollow" title="google-plus" data-original-title="google-plus" class="social_login_login_google gplus"><span class="social-mess-top gplus-social-login" style="display:none">' . __('Please set API key', 'jobhunt') . '</span><i class="icon-google-plus"></i></a></li>');
                                    endif;
                                    if ($linkedin_enabled == 'on') :
                                        echo apply_filters('social_login_login_linkedin', '<li><a  href="javascript:void(0);" rel="nofollow" title="linked-in" data-original-title="linked-in" class="social_login_login_linkedin linkedin" data-applyjobid=""><span class="social-mess-top linkedin-social-login" style="display:none">' . __('Please set API key', 'jobhunt') . '</span><i class="icon-linkedin2"></i></a></li>');
                                    endif;
                                }

                                $social_login_provider = isset($_COOKIE['social_login_current_provider']) ? $_COOKIE['social_login_current_provider'] : '';

                                do_action('social_login_auth');
                                ?> 
                            </ul> 
                        </div>
                    </div>
                <?php } ?>

                <?php
            endif;
        }
    }

}
/*
 *
 * End Function  how to login from social site;
 *
 */

add_action('login_form', 'cs_social_login_form', 10);
add_action('social_form', 'cs_social_login_form', 10);
add_action('after_signup_form', 'cs_social_login_form', 10);
add_action('social_login_form', 'cs_social_login_form', 10);

/*
 *
 * Start Function  how to user  recover his  password
 *
 */
if (!function_exists('cs_recover_pass')) {

    function cs_recover_pass() {
        global $wpdb, $cs_plugin_options;

        $cs_danger_html = '<div class="alert alert-danger"><button class="close" type="button" data-dismiss="alert" aria-hidden="true">&times;</button><p><i class="icon-warning4"></i>';

        $cs_success_html = '<div class="alert alert-success"><button class="close" aria-hidden="true" data-dismiss="alert" type="button">&times;</button><p><i class="icon-checkmark6"></i>';

        $cs_msg_html = '</p></div>';
       
        $cs_msg = '';
        // check if we're in reset form
        if (isset($_POST['action']) && 'cs_recover_pass' == $_POST['action']) {
            $email = esc_sql(trim($_POST['user_input']));
            if (empty($email)) {
                $cs_msg = $cs_danger_html . __('Enter e-mail address..', 'jobhunt') . $cs_msg_html;
            } else if (!is_email($email)) {
                $cs_msg = $cs_danger_html . __('Invalid e-mail address.', 'jobhunt') . $cs_msg_html;
            } else if (!email_exists($email)) {
                $cs_msg = $cs_danger_html . __('There is no user registered with that email address.', 'jobhunt') . $cs_msg_html;
            } else {
                $random_password = wp_generate_password(12, false);
                $user = get_user_by('email', $email);
                $username = $user->user_login;
                $update_user = wp_update_user(array(
                    'ID' => $user->ID,
                    'user_pass' => $random_password
                        )
                );
              
                $template_data = array(
                    'user' =>$username,
                    'email'=> $email,
                    'password' => $random_password
                );
                if ($update_user) {
                    do_action('jobhunt_reset_password_email', $template_data);
                    if (class_exists('jobhunt_reset_password_email_template') && isset(jobhunt_reset_password_email_template::$is_email_sent1)) {
                        $cs_msg = $cs_success_html . __('Check your email address for you new password.', 'jobhunt') . $cs_msg_html;
                    } else {
                        $cs_msg = $cs_danger_html . __('Oops something went wrong updating your account.', 'jobhunt') . $cs_msg_html;
                    }
                }
            }
            //end else
        }
        // end if
        echo ($cs_msg);

        die;
    }

    add_action('wp_ajax_cs_recover_pass', 'cs_recover_pass');
    add_action('wp_ajax_nopriv_cs_recover_pass', 'cs_recover_pass');
}

/*
 *
 * End Function  how to user  recover his  password
 *
 */
/*
 *
 * Start Function how to user recover his lost password
 *
 */

if (!function_exists('cs_lost_pass')) {

    function cs_lost_pass($atts, $content = "") {
        global $cs_form_fields2;
        $cs_defaults = array(
            'cs_type' => '',
        );
        extract(shortcode_atts($cs_defaults, $atts));
        ob_start();
        $cs_rand = rand(12345678, 98765432);
        if ($cs_type == 'popup') {
            ?>
            <div class="modal-header">
                <h4><?php _e('Forgot Password', 'jobhunt') ?></h4>
                <a class="close" data-dismiss="modal">&times;</a>
            </div>
            <div id="cs-result-<?php echo absint($cs_rand) ?>"></div>
            <div class="login-form-id-<?php echo absint($cs_rand) ?>">
                <form class="user_form" id="wp_pass_lost_<?php echo absint($cs_rand) ?>" method="post">		
                    <div class="filed-border">
                        <div class="input-holder">
                            <i class="icon-envelope4"></i>
                            <?php
                            $cs_opt_array = array(
                                'id' => '',
                                'std' => '',
                                'cust_id' => "",
                                'cust_name' => "user_input",
                                'classes' => 'form-control',
                                'extra_atr' => 'placeholder="' . __('Enter email address...', 'jobhunt') . '"',
                            );
                            $cs_form_fields2->cs_form_text_render($cs_opt_array);
                            ?>
                        </div>
                    </div>
                    <label>
                        <?php
                        $cs_opt_array = array(
                            'id' => '',
                            'std' => __('Send Email', 'jobhunt'),
                            'cust_id' => "",
                            'cust_name' => "submit",
                            'classes' => 'reset_password cs-bgcolor',
                            'cust_type' => 'submit',
                        );
                        $cs_form_fields2->cs_form_text_render($cs_opt_array);
                        ?>
                    </label>

                    <a class="cs-bgcolor cs-login-switch"><?php _e('Login Here', 'jobhunt') ?></a>
                </form>
            </div>
            <?php
        } else {
            ?>
            <div class="scetion-title">
                <h4><?php _e('Forgot Password', 'jobhunt') ?></h4>
            </div>
            <div class="status status-message" id="cs-result-<?php echo absint($cs_rand) ?>"></div>
            <form class="user_form" id="wp_pass_lost_<?php echo absint($cs_rand) ?>" method="post">		
                <div class="row">
                    <div class="col-md-12">
                        <label><?php _e('Enter Email', 'jobhunt') ?></label>	
                        <?php
                        $cs_opt_array = array(
                            'id' => '',
                            'std' => '',
                            'cust_id' => "",
                            'cust_name' => "user_input",
                            'classes' => 'form-control',
                            'extra_atr' => 'onfocus="if (this.value == \'' . __('Enter email address...', 'jobhunt') . '\') {
                                                this.value = \'\';
                                            }" onblur="if (this.value == \'\') {
                                                this.value = \'' . __('Enter email address...', 'jobhunt') . '\' ;
                                            }"',
                        );
                        $cs_form_fields2->cs_form_text_render($cs_opt_array);
                        ?>
                    </div>
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-5">
                                <?php
                                $cs_opt_array = array(
                                    'id' => '',
                                    'std' => __('Send Email', 'jobhunt'),
                                    'cust_id' => "",
                                    'cust_name' => "submit",
                                    'classes' => 'reset_password user-submit backcolr cs-bgcolor acc-submit',
                                    'cust_type' => 'submit',
                                );
                                $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                ?>
                            </div>
                            <div class="col-md-7 login-section">
                                <a class="login-link-page" href="#"><?php _e('Login Here', 'jobhunt') ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <?php
        }
        ?>
        <script type="text/javascript">
            var $ = jQuery;
            $("#wp_pass_lost_<?php echo absint($cs_rand) ?>").submit(function () {
                $('#cs-result-<?php echo absint($cs_rand) ?>').html('<i class="icon-spinner8 icon-spin"></i>').fadeIn();
                var input_data = $('#wp_pass_lost_<?php echo absint($cs_rand) ?>').serialize() + '&action=cs_recover_pass';
                $.ajax({
                    type: "POST",
                    url: "<?php echo esc_url(admin_url('admin-ajax.php')) ?>",
                    data: input_data,
                    success: function (msg) {
                        $('#cs-result-<?php echo absint($cs_rand) ?>').html(msg);
                    }
                });
                return false;
            });
            $(document).on('click', '.cs-forgot-switch', function () {
                $('.cs-login-pbox').hide();
                $('.cs-forgot-pbox').show();
            });
            $(document).on('click', '.cs-login-switch', function () {
                $('.cs-forgot-pbox').hide();
                $('.cs-login-pbox').show();
            });
        </script>
        <?php
        $cs_html = ob_get_clean();

        return do_shortcode($cs_html);
    }

    add_shortcode('cs_forgot_password', 'cs_lost_pass');
}
/*
 *
 * End Function  how to user  recover his  lost password
 *
 */