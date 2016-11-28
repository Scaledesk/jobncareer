<?php

/**
 * @Generate Random String
 *
 *
 */
if ( ! function_exists('cs_generate_random_string') ) {

	function cs_generate_random_string($length = 3) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		for ( $i = 0; $i < $length; $i ++ ) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $randomString;
	}

}
/*
 *
 * Start Function  for if user exist using Ajax
 *
 */
if ( ! function_exists('ajax_login') ) :

	function ajax_login() {
		global $cs_plugin_options, $wpdb;
		$credentials = array();

		$cs_danger_html = '<div class="alert alert-danger"><button class="close" type="button" data-dismiss="alert" aria-hidden="true">&times;</button><p><i class="icon-warning4"></i>';

		$cs_success_html = '<div class="alert alert-success"><button class="close" aria-hidden="true" data-dismiss="alert" type="button">&times;</button><p><i class="icon-checkmark6"></i>';

		$cs_msg_html = '</p></div>';

		$credentials['user_login'] = esc_sql($_POST['user_login']);
		$credentials['user_password'] = esc_sql($_POST['user_pass']);
		if ( isset($_POST['rememberme']) ) {
			$remember = esc_sql($_POST['rememberme']);
		} else {
			$remember = '';
		}
		if ( $remember ) {
			$credentials['remember'] = true;
		} else {
			$credentials['remember'] = false;
		}
		if ( $credentials['user_login'] == '' ) {
			echo json_encode(array( 'loggedin' => false, 'message' => $cs_danger_html . __('User name should not be empty.', 'jobhunt') . $cs_msg_html ));
			exit();
		} elseif ( $credentials['user_password'] == '' ) {
			echo json_encode(array( 'loggedin' => false, 'message' => $cs_danger_html . __('Password should not be empty.', 'jobhunt') . $cs_msg_html ));
			exit();
		} else {
			$user_status = 'inactive';
			$user = get_user_by('login', $credentials['user_login']);
			if ( is_object($user) && isset($user->ID) ) {
				$user_id = $user->ID;
				$user_status = get_user_meta($user_id, 'cs_user_status', true);

				if ( $user && wp_check_password($credentials['user_password'], $user->data->user_pass, $user_id) ) {
					if ( $user_status == 'inactive' ) {
						echo json_encode(array( 'loggedin' => false, 'message' => $cs_danger_html . __('Your account is not activated yet.', 'jobhunt') . $cs_msg_html ));
						die;
					}
				} else {
					echo json_encode(array( 'loggedin' => false, 'message' => $cs_danger_html . __('Wrong username or password.', 'jobhunt') . $cs_msg_html ));
					die;
				}
			}

			$status = wp_signon($credentials, false);
			if ( is_wp_error($status) ) {
				echo json_encode(array( 'loggedin' => false, 'message' => $cs_danger_html . __('Wrong username or password.', 'jobhunt') . $cs_msg_html ));
			} else {
				$user_roles = isset($status->roles) ? $status->roles : '';
				$uid = $status->ID;
				$cs_user_name = $_POST['user_login'];
				$cs_login_user = get_user_by('login', $cs_user_name);
				$cs_page_id = '';
				$default_url = $_POST['redirect_to'];
				if ( ($user_roles != '' && in_array("cs_employer", $user_roles) ) ) {
					$cs_page_id = isset($cs_plugin_options['cs_emp_dashboard']) ? $cs_plugin_options['cs_emp_dashboard'] : $default_url;
				} elseif ( ($user_roles != '' && in_array("cs_candidate", $user_roles) ) ) {
					$cs_page_id = isset($cs_plugin_options['cs_js_dashboard']) ? $cs_plugin_options['cs_js_dashboard'] : $default_url;
				}
				// update user last activity
				update_user_meta($uid, 'cs_user_last_activity_date', strtotime(date('d-m-Y H:i:s')));
				$cs_redirect_url = '';
				if ( $cs_page_id != '' ) {
					$cs_redirect_url = get_permalink($cs_page_id);
				} else {
					$cs_redirect_url = $default_url;  // home URL if page not set
				}
				echo json_encode(array( 'redirecturl' => $cs_redirect_url, 'loggedin' => true, 'message' => $cs_success_html . __('Login Successfully...', 'jobhunt') . $cs_msg_html ));
			}
		}
		die();
	}

endif;
add_action('wp_ajax_ajax_login', 'ajax_login');
add_action('wp_ajax_nopriv_ajax_login', 'ajax_login');
/*
 *
 * Start Function  for  user registration validation 
 *
 */
if ( ! function_exists('cs_registration_validation') ) {

	function cs_registration_validation($atts = '') {

		global $wpdb, $cs_plugin_options, $cs_form_fields_frontend;

		$cs_danger_html = '<div class="alert alert-danger"><button class="close" type="button" data-dismiss="alert" aria-hidden="true">&times;</button><p><i class="icon-warning4"></i>';
		$cs_success_html = '<div class="alert alert-success"><button class="close" aria-hidden="true" data-dismiss="alert" type="button">&times;</button><p><i class="icon-checkmark6"></i>';
		$cs_msg_html = '</p></div>';

		$id = $_POST['id']; //rand id 
		$username = $_POST['user_login' . $id];
		$cs_user_role_type = (isset($_POST['cs_user_role_type' . $id]) and $_POST['cs_user_role_type' . $id] <> '') ? $_POST['cs_user_role_type' . $id] : '';
		$json = array();
		$cs_captcha_switch = isset($cs_plugin_options['cs_captcha_switch']) ? $cs_plugin_options['cs_captcha_switch'] : '';
		if ( empty($username) ) {
			$json['type'] = "error";
			$json['message'] = $cs_danger_html . __("User name should not be empty.", "jobhunt") . $cs_msg_html;
			echo json_encode($json);
			exit();
		} elseif ( ! preg_match('/^[a-zA-Z0-9_]{5,}$/', $username) ) { // for english chars + numbers only
			$json['type'] = "error";
			$json['message'] = $cs_danger_html . __(" Please enter a valid username. You can only enter alphanumeric value and only ( _ ) longer than or equals 5 chars", "jobhunt") . $cs_msg_html;
			echo json_encode($json);
			exit();
		}
		$email = esc_sql($_POST['cs_user_email' . $id]);
		if ( empty($email) ) {
			$json['type'] = "error";
			$json['message'] = $cs_danger_html . __("Email should not be empty.", "jobhunt") . $cs_msg_html;
			echo json_encode($json);
			exit();
		}
		if ( ! preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/", $email) ) {
			$json['type'] = "error";
			$json['message'] = $cs_danger_html . __("Please enter a valid email.", "jobhunt") . $cs_msg_html;
			echo json_encode($json);
			exit();
		}
		if ( $cs_captcha_switch == 'on' ) {
			cs_captcha_verify();
		}

		$random_password = wp_generate_password($length = 12, $include_standard_special_chars = false);

		$role = 'guest';
		$status = wp_create_user($username, $random_password, $email);
		if ( is_wp_error($status) ) {
			$json['type'] = "error";
			$json['message'] = $cs_danger_html . __("User already exists. Please try another one.", "jobhunt") . $cs_msg_html;
			echo json_encode($json);
			die;
		} else {
			global $wpdb;
			$signup_user_role = '';
			if ( $cs_user_role_type == 'employer' ) {
				$signup_user_role = 'cs_employer';
			} elseif ( $cs_user_role_type == 'candidate' ) {
				$signup_user_role = 'cs_candidate';
			}
			wp_update_user(array( 'ID' => esc_sql($status), 'role' => esc_sql($signup_user_role), 'user_status' => 1 ));
			$wpdb->update(
					$wpdb->prefix . 'users', array( 'user_status' => 1 ), array( 'ID' => esc_sql($status) )
			);
			update_user_meta($status, 'show_admin_bar_front', false);
			// send email to user

			$reg_user = get_user_by('ID', $status);
			// Employer Register email
			if ( isset($reg_user->roles) && in_array('cs_employer', $reg_user->roles) ) {
				// Site owner email hook
				do_action('jobhunt_new_user_notification_site_owner', $reg_user->data->user_login, $reg_user->data->user_email);
				do_action('jobhunt_employer_register', $reg_user, $random_password);

				if ( class_exists('jobhunt_employer_register_email_template') && isset(jobhunt_employer_register_email_template::$is_email_sent1) ) {
					$json['type'] = "success";
					$json['message'] = $cs_success_html . __("Please check your email for login details.", "jobhunt") . $cs_msg_html;
				} else {
					$json['type'] = "error";
					$json['message'] = $cs_danger_html . __("Something went wrong, Email could not be processed..", "jobhunt") . $cs_msg_html;
				}
				// Candidate Register email
			} elseif ( isset($reg_user->roles) && in_array('cs_candidate', $reg_user->roles) ) {
				// Site owner email hook
				do_action('jobhunt_new_user_notification_site_owner', $reg_user->data->user_login, $reg_user->data->user_email);
				do_action('jobhunt_candidate_register', $reg_user, $random_password);

				if ( class_exists('jobhunt_candidate_register_email_template') && isset(jobhunt_candidate_register_email_template::$is_email_sent1) ) {
					$json['type'] = "success";
					$json['message'] = $cs_success_html . __("Please check your email for login details.", "jobhunt") . $cs_msg_html;
				} else {
					$json['type'] = "error";
					$json['message'] = $cs_danger_html . __("Something went wrong, Email could not be processed..", "jobhunt") . $cs_msg_html;
				}

				// Normal Register email
			} else {
				// Site owner email hook
				do_action('jobhunt_new_user_notification_site_owner', $reg_user->data->user_login, $reg_user->data->user_email);
				do_action('jobhunt_user_register', $reg_user, $random_password);

				if ( class_exists('jobhunt_register_email_template') && isset(jobhunt_register_email_template::$is_email_sent1) ) {
					$json['type'] = "success";
					$json['message'] = $cs_success_html . __("Please check your email for login details.", "jobhunt") . $cs_msg_html;
				} else {
					$json['type'] = "error";
					$json['message'] = $cs_danger_html . __("Something went wrong, Email could not be processed..", "jobhunt") . $cs_msg_html;
				}
			}


			// update user meta by role
			if ( $cs_user_role_type == 'employer' ) {
				$cs_comp_name = $_POST['cs_organization_name' . $id];
				$cs_specialisms = $_POST['cs_employer_specialisms' . $id];
				$cs_phone_no = $_POST['cs_phone_no' . $id];
				wp_update_user(array( 'ID' => $status, 'display_name' => $cs_comp_name ));
				if ( isset($cs_plugin_options['cs_employer_review_option']) && $cs_plugin_options['cs_employer_review_option'] == 'on' ) {
					$wpdb->update(
							$wpdb->prefix . 'users', array( 'user_status' => 1 ), array( 'ID' => esc_sql($status) )
					);
					update_user_meta($status, 'cs_user_status', 'active');
				} else {
					$wpdb->update(
							$wpdb->prefix . 'users', array( 'user_status' => 0 ), array( 'ID' => esc_sql($status) )
					);
					update_user_meta($status, 'cs_user_status', 'inactive');
				}
			} elseif ( $cs_user_role_type == 'candidate' ) {
				$cs_phone_no = $_POST['cs_phone_no' . $id];
				$cs_specialisms = $_POST['cs_candidate_specialisms' . $id];
				if ( isset($cs_plugin_options['cs_candidate_review_option']) && $cs_plugin_options['cs_candidate_review_option'] == 'on' ) {
					$wpdb->update(
							$wpdb->prefix . 'users', array( 'user_status' => 1 ), array( 'ID' => esc_sql($status) )
					);
					update_user_meta($status, 'cs_user_status', 'active');
				} else {
					$wpdb->update(
							$wpdb->prefix . 'users', array( 'user_status' => 0 ), array( 'ID' => esc_sql($status) )
					);
					update_user_meta($status, 'cs_user_status', 'inactive');
				}
			}
			update_user_meta($status, 'cs_phone_number', $cs_phone_no);
			update_user_meta($status, 'cs_user_last_activity_date', strtotime(date('d-m-Y')));
			update_user_meta($status, 'cs_allow_search', 'yes');

			if ( ! empty($cs_specialisms) ) {
				update_user_meta($status, 'cs_specialisms', $cs_specialisms);
			}
			echo json_encode($json);
			die;
		}
		die();
	}

	add_action('wp_ajax_cs_registration_validation', 'cs_registration_validation');
	add_action('wp_ajax_nopriv_cs_registration_validation', 'cs_registration_validation');
}

if ( ! function_exists('cs_contact_validation') ) {

	function cs_contact_validation($atts = '') {
		global $wpdb, $cs_plugin_options, $cs_form_fields_frontend;
		$id = rand(10000000, 91564689); //rand id 
		$username = $_POST['user_login' . $id];
		$json = array();
		if ( $cs_captcha_switch == 'on' ) {
			cs_captcha_verify();
		}
		if ( is_wp_error($status) ) {
			$json['type'] = "error";
			$json['message'] = __("Currently there are and issue", "jobhunt");
			echo json_encode($json);
			die;
		} else {
			$json['type'] = "error";
			$json['message'] = __("Your account has been registered successfully, Please contact to site admin for password.", "jobhunt");
		}

		echo json_encode($json);
		die;
	}

	add_action('wp_ajax_cs_registration_validation', 'cs_registration_validation');
	add_action('wp_ajax_nopriv_cs_registration_validation', 'cs_registration_validation');
}

/*
 *
 * Start Function  for  create form  capatach
 *
 */
if ( ! function_exists('cs_captcha') ) {

	function cs_captcha($id = '') {
		global $cs_plugin_options;
		$cs_captcha_switch = isset($cs_plugin_options['cs_captcha_switch']) ? $cs_plugin_options['cs_captcha_switch'] : '';
		$cs_sitekey = isset($cs_plugin_options['cs_sitekey']) ? $cs_plugin_options['cs_sitekey'] : '';
		$cs_secretkey = isset($cs_plugin_options['cs_secretkey']) ? $cs_plugin_options['cs_secretkey'] : '';
		$output = '';
		if ( $cs_captcha_switch == 'on' ) {
			if ( $cs_sitekey <> '' && $cs_secretkey <> '' ) {

				$output .= '<div class="g-recaptcha" data-theme="light" id="' . $id . '" data-sitekey="' . $cs_sitekey . '" style="transform:scale(1.22);-webkit-transform:scale(1.22);transform-origin:0 0;-webkit-transform-origin:0 0;">'
						. '</div> <a class="recaptcha-reload-a" href="javascript:void(0);" onclick="captcha_reload(\'' . admin_url('admin-ajax.php') . '\', \'' . $id . '\');">'
						. '<i class="icon-refresh2"></i> ' . __('Reload', 'jobhunt') . '</a>';
			} else {
				$output = '<p>' . __('Please provide google captcha API keys', 'jobhunt') . '</p>';
			}
		}
		return $output;
	}

}
/*
 *
 * Start Function  for  create form validation/verify capatach
 *
 */
if ( ! function_exists('cs_captcha_verify') ) {

	function cs_captcha_verify($page = '') {
		global $cs_plugin_options;
		$cs_secretkey = isset($cs_plugin_options['cs_secretkey']) ? $cs_plugin_options['cs_secretkey'] : '';
		$cs_captcha = isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : '';
		$cs_captcha_switch = isset($cs_plugin_options['cs_captcha_switch']) ? $cs_plugin_options['cs_captcha_switch'] : '';

		if ( $cs_captcha_switch == 'on' ) {
			if ( $page == true ) {
				if ( empty($cs_captcha) ) {
					return true;
				}
			} else {
				$json = array();
				if ( empty($cs_captcha) ) {
					$json['type'] = "error";
					$json['message'] = __("Please select captcha field.", 'jobhunt');
					echo json_encode($json);
					exit();
				}
			}
		}
	}

}

/*
 *
 * Start Function  for  create form  capatach reload
 *
 */
if ( ! function_exists('captcha_reload') ) {

	function captcha_reload($atts = '') {
		global $cs_plugin_options;
		$captcha_id = $_REQUEST['captcha_id'];
		$cs_sitekey = isset($cs_plugin_options['cs_sitekey']) ? $cs_plugin_options['cs_sitekey'] : '';
		$return_str = "<script>
        var " . $captcha_id . ";
            " . $captcha_id . " = grecaptcha.render('" . $captcha_id . "', {
                'sitekey': '" . $cs_sitekey . "', //Replace this with your Site key
                'theme': 'light'
            });"
				. "</script>";
		$return_str .= cs_captcha($captcha_id);
		echo force_balance_tags($return_str);
		die();
	}

	add_action('wp_ajax_captcha_reload', 'captcha_reload');
	add_action('wp_ajax_nopriv_captcha_reload', 'captcha_reload');
}

add_action('user_register', 'jobcareer_registration_save', 10, 1);
if ( ! function_exists('jobcareer_registration_save') ) {

	function jobcareer_registration_save($user_id) {
		if ( isset($_REQUEST['action']) && $_REQUEST['action'] == 'register' ) {
			$random_password = wp_generate_password($length = 12, $include_standard_special_chars = false);
			wp_set_password($random_password, $user_id);
			$reg_user = get_user_by('ID', $user_id);
			if ( isset($reg_user->roles) && (in_array('subscriber', $reg_user->roles) || in_array('editor', $reg_user->roles) || in_array('author', $reg_user->roles)) ) {
				// Site owner email hook
				do_action('jobhunt_new_user_notification_site_owner', $reg_user->data->user_login, $reg_user->data->user_email);
				// normal user email hook
				do_action('jobhunt_user_register', $reg_user, $random_password);
			}
		}
	}

}
