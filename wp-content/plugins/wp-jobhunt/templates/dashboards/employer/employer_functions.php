<?php

/**
 * File Type: Employer Functions
 */
if ( ! class_exists('cs_employer_functions') ) {

	class cs_employer_functions {

		/**
		 * Start construct Functions
		 */
		public function __construct() {
			add_action('wp_ajax_cs_check_user_avail', array( &$this, 'cs_check_user_avail' ));
			add_action('wp_ajax_nopriv_cs_check_user_avail', array( &$this, 'cs_check_user_avail' ));
			add_action('wp_ajax_cs_job_delete', array( &$this, 'cs_job_delete' ));
			add_action('wp_ajax_cs_fav_resume_del', array( &$this, 'cs_fav_resume_del' ));
			add_action('wp_ajax_cs_emp_check', array( &$this, 'cs_emp_check' ));
			add_action('wp_ajax_nopriv_cs_emp_check', array( &$this, 'cs_emp_check' ));
			add_action('wp_ajax_cs_job_status_update', array( &$this, 'cs_job_status_update' ));
			add_action('wp_ajax_nopriv_cs_job_status_update', array( &$this, 'cs_job_status_update' ));
			add_action('wp_ajax_cs_unset_user_fav', array( &$this, 'cs_unset_user_fav' ));
			add_action('wp_ajax_ajax_employer_form_save', array( &$this, 'ajax_employer_form_save' ));
		}

		/**
		 * End construct Functions
		 */

		/**
		 * Start Function for checking the Availability and Registration for User
		 */
		public function cs_check_user_avail() {
			$cs_json = array();
			$cs_user_email = isset($_POST['emp_email']) ? $_POST['emp_email'] : '';
			$cs_username = isset($_POST['emp_username']) ? $_POST['emp_username'] : '';
			$cs_error = false;
			$cs_json['type'] = 'success';
			if ( email_exists($cs_user_email) ) {
				$cs_json['msg'] = __('Email Already in use.', 'jobhunt');
				$cs_json['type'] = 'error';
				$cs_error = true;
			} else if ( username_exists($cs_username) ) {
				$cs_json['msg'] = __('Username Already in use.', 'jobhunt');
				$cs_json['type'] = 'error';
				$cs_error = true;
			}
			if ( $cs_error == false ) {
				$cs_json['msg'] = __("You will recieve an email for login details after creating this job.", 'jobhunt');
				$cs_json['type'] = 'success';
			}
			echo json_encode($cs_json);
			die;
		}

		/**
		 * END Function for checking the Availability and Registration for User
		 */

		/**
		 * Start Function for Creating User Registration Form
		 */
		public function cs_create_user($cs_username, $cs_user_email) {
			global $wpdb;
			if ( ! username_exists($cs_username) && ! email_exists($cs_user_email) ) {
				$random_password = wp_generate_password($length = 12, $include_standard_special_chars = false);
				$role = 'cs_employer';
				$cs_user_email = sanitize_email($cs_user_email);
				$cs_register = wp_create_user($cs_username, $random_password, $cs_user_email);
				if ( ! is_wp_error($cs_register) ) {
					wp_update_user(array( 'ID' => esc_sql($cs_register), 'role' => esc_sql($role), 'user_status' => 1 ));
					$wpdb->update(
							$wpdb->prefix . 'users', array( 'user_status' => 1 ), array( 'ID' => esc_sql($cs_register) )
					);
					update_user_meta($cs_register, 'show_admin_bar_front', false);
					wp_new_user_notification(esc_sql($cs_register), $random_password);
					$get_user = get_user_by('email', $cs_user_email);

					if ( isset($cs_plugin_options['cs_employer_review_option']) && $cs_plugin_options['cs_employer_review_option'] == 'on' ) {
						$wpdb->update(
								$wpdb->prefix . 'users', array( 'user_status' => 1 ), array( 'ID' => esc_sql($get_user->ID) )
						);
						update_user_meta($get_user->ID, 'cs_user_status', 'active');
					} else {
						$wpdb->update(
								$wpdb->prefix . 'users', array( 'user_status' => 0 ), array( 'ID' => esc_sql($get_user->ID) )
						);
						update_user_meta($get_user->ID, 'cs_user_status', 'inactive');
					}

					return $get_user->ID;
				}
			}
		}

		/**
		 * End Function for Creating User Registration Form
		 */

		/**
		 * Start Function for Creating Jobs Custom Fields
		 */
		public function cs_custom_fields($cs_job_id = '') {
			global $cs_form_fields2;
			$cs_html = '';
			$cs_job_cus_fields = get_option("cs_job_cus_fields");
			if ( is_array($cs_job_cus_fields) && sizeof($cs_job_cus_fields) > 0 ) {
				foreach ( $cs_job_cus_fields as $cus_field ) {
					$cs_type = isset($cus_field['type']) ? $cus_field['type'] : '';
					switch ( $cs_type ) {
						case('text'):
							$cs_label = isset($cus_field['label']) ? $cus_field['label'] : '';
							$cs_meta_key = isset($cus_field['meta_key']) ? $cus_field['meta_key'] : '';
							$cs_default_val = isset($cus_field['default_value']) ? $cus_field['default_value'] : '';
							$cs_required = isset($cus_field['required']) && $cus_field['required'] == 'yes' ? ' required' : '';
							$cs_help_txt = isset($cus_field['help']) ? $cus_field['help'] : '';
							if ( $cs_job_id != '' ) {
								$cs_default_val = get_post_meta((int) $cs_job_id, "$cs_meta_key", true);
							}
							$cs_html .= '
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <label>' . esc_attr($cs_label) . '</label>';
							$cs_opt_array = array(
								'name' => isset($cus_field['label']) ? $cus_field['label'] : '',
								'desc' => '',
								'classes' => 'form-control',
								'hint_text' => isset($cus_field['help']) ? $cus_field['help'] : '',
								'std' => $cs_default_val,
								'id' => isset($cus_field['meta_key']) ? $cus_field['meta_key'] : '',
								'cus_field' => true,
								'return' => true,
							);

							if ( isset($cus_field['placeholder']) && $cus_field['placeholder'] != '' ) {
								$cs_opt_array['extra_atr'] = ' placeholder="' . $cus_field['placeholder'] . '"';
							}

							if ( isset($cus_field['required']) && $cus_field['required'] == 'yes' ) {
								$cs_opt_array['required'] = 'yes';
							}

							$cs_html .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

							if ( $cs_help_txt <> '' ) {
								$cs_html .= '<span class="cs-caption">' . $cs_help_txt . '</span>';
							}
							$cs_html .= '</div>';
							break;
						case('textarea'):
							$cs_label = isset($cus_field['label']) ? $cus_field['label'] : '';
							$cs_rows = isset($cus_field['rows']) ? $cus_field['rows'] : '';
							$cs_cols = isset($cus_field['cols']) ? $cus_field['cols'] : '';
							$cs_meta_key = isset($cus_field['meta_key']) ? $cus_field['meta_key'] : '';
							$cs_default_val = isset($cus_field['default_value']) ? $cus_field['default_value'] : '';
							$cs_required = isset($cus_field['required']) && $cus_field['required'] == 'yes' ? ' required' : '';
							$cs_help_txt = isset($cus_field['help']) ? $cus_field['help'] : '';
							if ( $cs_job_id != '' ) {
								$cs_default_val = get_post_meta((int) $cs_job_id, "$cs_meta_key", true);
							}
							$cs_html .= '
                                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                            <label>' . esc_attr($cs_label) . '</label>';

							$cs_opt_array = array(
								'name' => isset($cus_field['label']) ? $cus_field['label'] : '',
								'desc' => '',
								'classes' => 'form-control',
								'extra_atr' => 'rows="' . $cs_rows . '" cols="' . $cs_cols . '"',
								'hint_text' => isset($cus_field['help']) ? $cus_field['help'] : '',
								'std' => $cs_default_val,
								'id' => isset($cus_field['meta_key']) ? $cus_field['meta_key'] : '',
								'cus_field' => true,
								'return' => true,
							);

							if ( isset($cus_field['required']) && $cus_field['required'] == 'yes' ) {
								$cs_opt_array['required'] = 'yes';
							}

							$cs_html .= $cs_form_fields2->cs_form_textarea_render($cs_opt_array);

							if ( $cs_help_txt <> '' ) {
								$cs_html .= '<span class="cs-caption">' . $cs_help_txt . '</span>';
							}
							$cs_html .= '	
                                        </div>';
							break;
						case('dropdown'):
							$cs_label = isset($cus_field['label']) ? $cus_field['label'] : '';
							$cs_meta_key = isset($cus_field['meta_key']) ? $cus_field['meta_key'] : '';
							$cs_default_val = isset($cus_field['default_value']) ? $cus_field['default_value'] : '';
							$cs_required = isset($cus_field['required']) && $cus_field['required'] == 'yes' ? ' required' : '';
							$cs_help_txt = isset($cus_field['help']) ? $cus_field['help'] : '';

							if ( $cs_job_id != '' ) {
								$cs_default_val = get_post_meta((int) $cs_job_id, "$cs_meta_key", true);
							}
							$cs_dr_name = ' name="cs_cus_field[' . sanitize_html_class($cs_meta_key) . ']"';
							$cs_dr_mult = '';
							if ( isset($cus_field['post_multi']) && $cus_field['post_multi'] == 'yes' ) {
								$cs_dr_name = ' name="cs_cus_field[' . sanitize_html_class($cs_meta_key) . '][]"';
								$cs_dr_mult = ' multiple="multiple"';
							}

							$a_options = array();

							$cs_options_mark = '';

							if ( isset($cus_field['options']['value']) && is_array($cus_field['options']['value']) && sizeof($cus_field['options']['value']) > 0 ) {
								if ( isset($cus_field['first_value']) && $cus_field['first_value'] != '' ) {
									$cs_options_mark .= '<option value="">' . $cus_field['first_value'] . '</option>';
								}
								$cs_opt_counter = 0;
								foreach ( $cus_field['options']['value'] as $cs_option ) {

									if ( isset($cus_field['post_multi']) && $cus_field['post_multi'] == 'yes' ) {

										$cs_checkd = '';
										if ( is_array($cs_default_val) && in_array($cs_option, $cs_default_val) ) {
											$cs_checkd = ' selected="selected"';
										}
									} else {
										$cs_checkd = $cs_option == $cs_default_val ? ' selected="selected"' : '';
									}

									$cs_opt_label = $cus_field['options']['label'][$cs_opt_counter];
									$cs_options_mark .= '<option value="' . $cs_option . '"' . $cs_checkd . '>' . $cs_opt_label . '</option>';
									$cs_opt_counter ++;
								}
							}

							$cs_html .= '
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <label>' . esc_attr($cs_label) . '</label>
                            <div class="select-holder">';

							$cs_opt_array = array(
								'name' => isset($cus_field['label']) ? $cus_field['label'] : '',
								'desc' => '',
								'classes' => 'chosen-select form-control',
								'hint_text' => isset($cus_field['help']) ? $cus_field['help'] : '',
								'std' => isset($cus_field['default_value']) ? $cus_field['default_value'] : '',
								'id' => isset($cus_field['meta_key']) ? $cus_field['meta_key'] : '',
								'options' => $cs_options_mark,
								'options_markup' => true,
								'cus_field' => true,
								'return' => true,
							);

							if ( isset($cus_field['first_value']) && $cus_field['first_value'] != '' ) {
								$cs_opt_array['extra_atr'] = ' data-placeholder="' . $cus_field['first_value'] . '"';
							}

							if ( isset($cus_field['required']) && $cus_field['required'] == 'yes' ) {
								$cs_opt_array['required'] = 'yes';
							}
							if ( isset($cus_field['post_multi']) && $cus_field['post_multi'] == 'yes' ) {
								$cs_html .= $cs_form_fields2->cs_form_multiselect_render($cs_opt_array);
							} else {
								$cs_html .= $cs_form_fields2->cs_form_select_render($cs_opt_array);
							}


							if ( $cs_help_txt <> '' ) {
								$cs_html .= '<span class="cs-caption">' . $cs_help_txt . '</span>';
							}
							$cs_html .= '	
                            </div>
                            </div>';
							break;
						case('date'):
							$cs_label = isset($cus_field['label']) ? $cus_field['label'] : '';
							$cs_meta_key = isset($cus_field['meta_key']) ? $cus_field['meta_key'] : '';
							$cs_default_val = isset($cus_field['default_value']) ? $cus_field['default_value'] : '';
							$cs_required = isset($cus_field['required']) && $cus_field['required'] == 'yes' ? ' required' : '';
							$cs_format = isset($cus_field['date_format']) ? $cus_field['date_format'] : 'd-m-Y';
							$cs_help_txt = isset($cus_field['help']) ? $cus_field['help'] : '';
							if ( $cs_job_id != '' ) {
								$cs_default_val = get_post_meta((int) $cs_job_id, "$cs_meta_key", true);
							}

							$cs_html .= '
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <label>' . esc_attr($cs_label) . '</label>';

							$cs_opt_array = array(
								'name' => isset($cus_field['label']) ? $cus_field['label'] : '',
								'desc' => '',
								'classes' => 'form-control',
								'hint_text' => isset($cus_field['help']) ? $cus_field['help'] : '',
								'std' => $cs_default_val,
								'id' => isset($cus_field['meta_key']) ? $cus_field['meta_key'] : '',
								'cus_field' => true,
								'format' => $cs_format,
								'return' => true,
							);

							if ( isset($cus_field['placeholder']) && $cus_field['placeholder'] != '' ) {
								$cs_opt_array['extra_atr'] = ' placeholder="' . $cus_field['placeholder'] . '"';
							}

							if ( isset($cus_field['required']) && $cus_field['required'] == 'yes' ) {
								$cs_opt_array['required'] = 'yes';
							}

							$cs_html .= $cs_form_fields2->cs_form_date_render($cs_opt_array);

							if ( $cs_help_txt <> '' ) {
								$cs_html .= '<span class="cs-caption">' . $cs_help_txt . '</span>';
							}
							$cs_html .= '	
                            </div>';
							break;
						case('email'):
							$cs_label = isset($cus_field['label']) ? $cus_field['label'] : '';
							$cs_meta_key = isset($cus_field['meta_key']) ? $cus_field['meta_key'] : '';
							$cs_default_val = isset($cus_field['default_value']) ? $cus_field['default_value'] : '';
							$cs_required = isset($cus_field['required']) && $cus_field['required'] == 'yes' ? ' required' : '';
							$cs_help_txt = isset($cus_field['help']) ? $cus_field['help'] : '';
							if ( $cs_job_id != '' ) {
								$cs_default_val = get_post_meta((int) $cs_job_id, "$cs_meta_key", true);
							}
							$cs_html .= '
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <label>' . esc_attr($cs_label) . '</label>';
							$cs_opt_array = array(
								'name' => isset($cus_field['label']) ? $cus_field['label'] : '',
								'desc' => '',
								'classes' => 'form-control',
								'hint_text' => isset($cus_field['help']) ? $cus_field['help'] : '',
								'std' => $cs_default_val,
								'id' => isset($cus_field['meta_key']) ? $cus_field['meta_key'] : '',
								'cus_field' => true,
								'return' => true,
							);

							if ( isset($cus_field['placeholder']) && $cus_field['placeholder'] != '' ) {
								$cs_opt_array['extra_atr'] = ' placeholder="' . $cus_field['placeholder'] . '"';
							}

							if ( isset($cus_field['required']) && $cus_field['required'] == 'yes' ) {
								$cs_opt_array['required'] = 'yes';
							}

							$cs_html .= $cs_form_fields2->cs_form_text_render($cs_opt_array);
							if ( $cs_help_txt <> '' ) {
								$cs_html .= '<span class="cs-caption">' . $cs_help_txt . '</span>';
							}
							$cs_html .= '	
                            </div>';
							break;
						case('url'):
							$cs_label = isset($cus_field['label']) ? $cus_field['label'] : '';
							$cs_meta_key = isset($cus_field['meta_key']) ? $cus_field['meta_key'] : '';
							$cs_default_val = isset($cus_field['default_value']) ? $cus_field['default_value'] : '';
							$cs_required = isset($cus_field['required']) && $cus_field['required'] == 'yes' ? ' required' : '';
							$cs_help_txt = isset($cus_field['help']) ? $cus_field['help'] : '';
							if ( $cs_job_id != '' ) {
								$cs_default_val = get_post_meta((int) $cs_job_id, "$cs_meta_key", true);
							}
							$cs_html .= '
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <label>' . esc_attr($cs_label) . '</label>';

							$cs_opt_array = array(
								'name' => isset($cus_field['label']) ? $cus_field['label'] : '',
								'desc' => '',
								'classes' => 'form-control',
								'hint_text' => isset($cus_field['help']) ? $cus_field['help'] : '',
								'std' => $cs_default_val,
								'id' => isset($cus_field['meta_key']) ? $cus_field['meta_key'] : '',
								'cus_field' => true,
								'return' => true,
							);

							if ( isset($cus_field['placeholder']) && $cus_field['placeholder'] != '' ) {
								$cs_opt_array['extra_atr'] = ' placeholder="' . $cus_field['placeholder'] . '"';
							}

							if ( isset($cus_field['required']) && $cus_field['required'] == 'yes' ) {
								$cs_opt_array['required'] = 'yes';
							}

							$cs_html .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

							if ( $cs_help_txt <> '' ) {
								$cs_html .= '<span class="cs-caption">' . $cs_help_txt . '</span>';
							}
							$cs_html .= '	
					</div>';
							break;
						case('range'):
							$cs_label = isset($cus_field['label']) ? $cus_field['label'] : '';
							$cs_meta_key = isset($cus_field['meta_key']) ? $cus_field['meta_key'] : '';
							$cs_default_val = isset($cus_field['default_value']) ? $cus_field['default_value'] : '';
							$cs_required = isset($cus_field['required']) && $cus_field['required'] == 'yes' ? ' required' : '';
							$cs_help_txt = isset($cus_field['help']) ? $cus_field['help'] : '';
							if ( $cs_job_id != '' ) {
								$cs_default_val = get_post_meta((int) $cs_job_id, "$cs_meta_key", true);
							}

							$cs_html .= '
                                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                            <label>' . esc_attr($cs_label) . '</label>';

							$cs_opt_array = array(
								'name' => isset($cus_field['label']) ? $cus_field['label'] : '',
								'desc' => '',
								'classes' => 'form-control',
								'hint_text' => isset($cus_field['help']) ? $cus_field['help'] : '',
								'std' => $cs_default_val,
								'id' => isset($cus_field['meta_key']) ? $cus_field['meta_key'] : '',
								'cus_field' => true,
								'return' => true,
							);

							if ( isset($cus_field['placeholder']) && $cus_field['placeholder'] != '' ) {
								$cs_opt_array['extra_atr'] = ' placeholder="' . $cus_field['placeholder'] . '"';
							}

							if ( isset($cus_field['required']) && $cus_field['required'] == 'yes' ) {
								$cs_opt_array['required'] = 'yes';
							}

							$cs_html .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

							if ( $cs_help_txt <> '' ) {
								$cs_html .= '<span class="cs-caption">' . $cs_help_txt . '</span>';
							}
							$cs_html .= '	
                                        </div>';
							break;
					}
				}
			}
			return $cs_html;
		}

		/**
		 * End Function for Creating Jobs Custom Fields
		 */

		/**
		 * Start Function for Creating Job  Fields
		 */
		public function cs_add_job($values = array(), $cs_elem = false) {

			global $cs_plugin_options;
			extract($values);
			$cs_job_id = isset($cs_job_id) ? $cs_job_id : '';
			$cs_job_user = isset($cs_job_user) ? $cs_job_user : '';
			$cs_job_title = isset($cs_job_title) ? $cs_job_title : '';
			$cs_job_desc = isset($cs_job_desc) ? $cs_job_desc : '';
			$cs_job_types = isset($cs_job_types) ? $cs_job_types : '';
			$cs_job_specialisms = isset($cs_job_specialisms) ? $cs_job_specialisms : '';
			$cs_job_expire = isset($cs_job_expire) ? $cs_job_expire : '';
			$cs_job_pkg = isset($cs_job_pkg) ? $cs_job_pkg : '';
			$cs_job_status = isset($cs_job_status) ? $cs_job_status : '';
			$cs_job_custom = isset($cs_job_custom) ? $cs_job_custom : '';
			// location fields
			$cs_post_loc_country = isset($cs_post_loc_country) ? $cs_post_loc_country : '';
			$cs_post_loc_region = isset($cs_post_loc_region) ? $cs_post_loc_region : '';
			$cs_post_loc_city = isset($cs_post_loc_city) ? $cs_post_loc_city : '';
			$cs_post_loc_address = isset($cs_post_loc_address) ? $cs_post_loc_address : '';
			$cs_post_loc_latitude = isset($cs_post_loc_latitude) ? $cs_post_loc_latitude : '';
			$cs_post_loc_longitude = isset($cs_post_loc_longitude) ? $cs_post_loc_longitude : '';
			$cs_add_new_loc = isset($cs_add_new_loc) ? $cs_add_new_loc : '';
			$cs_post_loc_zoom = isset($cs_post_loc_zoom) ? $cs_post_loc_zoom : '';

			if ( $cs_elem == true ) {
				$cs_job_title = $cs_job_title;
			} else {
				$cs_job_title = cs_jobhunt_decrypt($cs_job_title);
			}

			if ( $cs_elem == true ) {
				$cs_job_desc = $cs_job_desc;
			} else {
				$cs_job_desc = cs_jobhunt_decrypt($cs_job_desc);
			}

			$job_post = array(
				'post_title' => $cs_job_title,
				'post_content' => $cs_job_desc,
				'post_status' => 'publish',
				'post_type' => 'jobs',
				'post_date' => current_time('Y-m-d H:i:s')
			);

			//insert job
			$job_id = wp_insert_post($job_post);

			$user_data = wp_get_current_user();
			do_action('jobhunt_job_add_email', $user_data, $job_id);

			// add cs_job_specialisms
			if ( ! empty($cs_job_specialisms) ) {
				wp_set_post_terms($job_id, array(), 'specialisms', FALSE);
				foreach ( $cs_job_specialisms as $cs_spec ) {
					$cs_spec = (int) $cs_spec;
					wp_set_post_terms($job_id, array( $cs_spec ), 'specialisms', true);
				}
			}
			// add cs_job_type
			if ( $cs_job_types != '' ) {
				wp_set_post_terms($job_id, array( $cs_job_types ), 'job_type', FALSE);
			}

			$cs_insert_array = array(
				'cs_job_id' => $cs_job_id,
				'cs_job_username' => $cs_job_user,
				'cs_job_posted' => strtotime(date('d-m-Y H:i:s')),
				'cs_job_expired' => strtotime($cs_job_expire),
				'cs_job_package' => $cs_job_pkg,
				'cs_job_status' => $cs_job_status,
			);
			// update location fiels
			if ( $cs_post_loc_zoom != '' ) {
				update_post_meta((int) $cs_job_id, "cs_post_loc_zoom", $cs_post_loc_zoom);
			}
			if ( is_array($cs_job_custom) && sizeof($cs_job_custom) > 0 ) {
				$cs_custom_array = array();
				foreach ( $cs_job_custom as $cus_key => $cs_val ) {
					$cs_custom_array[$cus_key] = $cs_val;
				}
				$cs_insert_array = array_merge($cs_insert_array, $cs_custom_array);
			}

			foreach ( $cs_insert_array as $job_key => $job_val ) {
				update_post_meta($job_id, "$job_key", $job_val);
			}

			if ( isset($_POST['cs_post_loc_country']) && $cs_elem != true ) {
				$cs_post_loc_country = cs_jobhunt_decrypt($_POST['cs_post_loc_country']);
				update_post_meta($job_id, "cs_post_loc_country", $cs_post_loc_country);
			}

			if ( isset($_POST['cs_post_loc_region']) && $cs_elem != true ) {
				$cs_post_loc_region = cs_jobhunt_decrypt($_POST['cs_post_loc_region']);
				update_post_meta($job_id, "cs_post_loc_region", $cs_post_loc_region);
			}

			if ( isset($_POST['cs_post_loc_city']) && $cs_elem != true ) {
				$cs_post_loc_city = cs_jobhunt_decrypt($_POST['cs_post_loc_city']);
				update_post_meta($job_id, "cs_post_loc_city", $cs_post_loc_city);
			}

			if ( isset($_POST['cs_post_loc_latitude']) && $cs_elem != true ) {
				$cs_post_loc_latitude = ($_POST['cs_post_loc_latitude']);
				update_post_meta($job_id, "cs_add_new_loc", $cs_post_loc_latitude);
			}

			if ( isset($_POST['cs_post_loc_longitude']) && $cs_elem != true ) {
				$cs_post_loc_longitude = ($_POST['cs_post_loc_longitude']);
				update_post_meta($job_id, "cs_post_loc_longitude", $cs_post_loc_longitude);
			}

			if ( isset($_POST['cs_add_new_loc']) && $cs_elem != true ) {
				$cs_add_new_loc = cs_jobhunt_decrypt($_POST['cs_add_new_loc']);
				update_post_meta($job_id, "cs_add_new_loc", $cs_add_new_loc);
			}

			if ( isset($_POST['cs_post_comp_address']) && $cs_elem != true ) {
				$cs_comp_address = cs_jobhunt_decrypt($_POST['cs_post_comp_address']);
				update_post_meta($job_id, "cs_post_comp_address", $cs_comp_address);
			}

			if ( isset($_POST['cs_post_loc_address']) && $cs_elem != true ) {
				$cs_post_loc_address = cs_jobhunt_decrypt($_POST['cs_post_loc_address']);
				update_post_meta($job_id, "cs_post_loc_address", $cs_post_loc_address);
			}

			if ( isset($_POST['cs_cus_field']) ) {
				if ( is_array($_POST['cs_cus_field']) && sizeof($_POST['cs_cus_field']) > 0 ) {
					foreach ( $_POST['cs_cus_field'] as $c_key => $c_val ) {
						
						if ( is_array($c_val) ) {
							$c_vll_array = array();
							foreach ( $c_val as $c_vll ) {
								$c_vll_array[] = cs_jobhunt_decrypt($c_vll);
							}
							update_post_meta($job_id, $c_key, $c_vll_array);
						} else {
							update_post_meta($job_id, $c_key, cs_jobhunt_decrypt($c_val));
						}
					}
				}
			}

			return $job_id;
		}

		/**
		 * End Function for Creating Job Fields
		 */

		/**
		 * Start Function for Creating pay Process
		 */
		public function cs_pay_process($values = array()) {

			$cs_transaction_fields = $values;
			extract($values);
			$cs_job_id = isset($cs_job_id) ? $cs_job_id : '';
			$cs_trans_id = isset($cs_trans_id) ? $cs_trans_id : '';
			$cs_trans_user = isset($cs_trans_user) ? $cs_trans_user : '';
			$cs_trans_pkg = isset($cs_trans_package) ? $cs_trans_package : '';
			$cs_trans_featured = isset($cs_trans_featured) && $cs_trans_featured == 'on' ? 'yes' : 'no';
			$cs_trans_amount = isset($cs_trans_amount) ? $cs_trans_amount : '';
			$cs_trans_pkg_expiry = isset($cs_trans_pkg_expiry) ? $cs_trans_pkg_expiry : '';
			$cs_trans_list_num = isset($cs_trans_list_num) ? $cs_trans_list_num : '';
			$cs_trans_list_expiry = isset($cs_trans_list_expiry) ? $cs_trans_list_expiry : '';
			$cs_trans_list_period = isset($cs_trans_list_period) ? $cs_trans_list_period : '';
			$post_author = $cs_trans_user;
			$transaction_post = array(
				'post_title' => '#' . $cs_trans_id,
				'post_status' => 'publish',
				'post_author' => $post_author,
				'post_type' => 'cs-transactions',
				'post_date' => current_time('Y-m-d H:i:s')
			);
			//insert the transaction
			$trans_id = wp_insert_post($transaction_post);

			$cs_trans_pay_method = isset($_POST['cs_payment_gateway']) ? $_POST['cs_payment_gateway'] : '';
			$cs_trans_array = array(
				'job_id' => $cs_job_id,
				'transaction_id' => $cs_trans_id,
				'transaction_user' => $cs_trans_user,
				'transaction_feature' => $cs_trans_featured,
				'transaction_package' => $cs_trans_pkg,
				'transaction_amount' => $cs_trans_amount,
				'transaction_pay_method' => $cs_trans_pay_method,
				'transaction_expiry_date' => $cs_trans_pkg_expiry,
				'transaction_listings' => $cs_trans_list_num,
				'transaction_listing_expiry' => $cs_trans_list_expiry,
				'transaction_listing_period' => $cs_trans_list_period,
			);

			if ( $cs_trans_amount <= 0 ) {
				$cs_trans_array['transaction_status'] = __('approved', 'jobhunt');
			}

			foreach ( $cs_trans_array as $trans_key => $trans_val ) {
				update_post_meta($trans_id, "cs_{$trans_key}", $trans_val);
			}
			// Make job ids clear if query direct from packages element
			if ( $cs_job_id == $cs_trans_pkg ) {
				update_post_meta($trans_id, 'cs_job_id', '');
			}
			$cs_transaction_fields['cs_order_id'] = $trans_id;

			if ( $cs_trans_amount > 0 ) {
				if ( isset($_POST['cs_payment_gateway']) && $_POST['cs_payment_gateway'] == 'cs_wooC_GATEWAY' && ! empty($cs_transaction_fields) ) {

					global $Payment_Processing;
					$payment_args = array(
						'package_id' => $cs_transaction_fields['cs_trans_id'],
						'package_name' => $cs_transaction_fields['cs_package_title'],
						'price' => $cs_transaction_fields['cs_trans_amount'],
						'custom_var' => array(
							'cs_order_id' => $cs_transaction_fields['cs_order_id'],
							'cs_job_id' => $cs_job_id,
						),
						'redirect_url' => get_option('wooC_current_page')
					);
					$Payment_Processing->processing_payment($payment_args);
				} else if ( isset($_POST['cs_payment_gateway']) && $_POST['cs_payment_gateway'] == 'CS_PAYPAL_GATEWAY' && ! empty($cs_transaction_fields) ) {
					$paypal_gateway = new CS_PAYPAL_GATEWAY();
					$paypal_gateway->cs_proress_request($cs_transaction_fields);
				} else if ( isset($_POST['cs_payment_gateway']) && $_POST['cs_payment_gateway'] == 'CS_AUTHORIZEDOTNET_GATEWAY' && ! empty($cs_transaction_fields) ) {
					$authorizedotnet = new CS_AUTHORIZEDOTNET_GATEWAY();
					$authorizedotnet->cs_proress_request($cs_transaction_fields);
				} else if ( isset($_POST['cs_payment_gateway']) && $_POST['cs_payment_gateway'] == 'CS_SKRILL_GATEWAY' && ! empty($cs_transaction_fields) ) {
					$skrill = new CS_SKRILL_GATEWAY();
					$skrill->cs_proress_request($cs_transaction_fields);
				} else if ( isset($_POST['cs_payment_gateway']) && $_POST['cs_payment_gateway'] == 'CS_PRE_BANK_TRANSFER' && ! empty($cs_transaction_fields) ) {
					$banktransfer = new CS_PRE_BANK_TRANSFER();
					return $banktransfer->cs_proress_request($cs_transaction_fields);
				} else {
					// Do Nothing
				}
			}
		}

		/**
		 * End Function for Creating pay Process
		 */

		/**
		 * Start Function for Cv  pay Process
		 */
		public function cs_cv_pay_process($values = array()) {
			$cs_transaction_fields = $values;
			extract($values);
			$cs_trans_id = isset($cs_trans_id) ? $cs_trans_id : '';
			$cs_trans_user = isset($cs_trans_user) ? $cs_trans_user : '';
			$cs_trans_pkg = isset($cs_trans_package) ? $cs_trans_package : '';
			$cs_trans_amount = isset($cs_trans_amount) ? $cs_trans_amount : '';
			$cs_trans_pkg_expiry = isset($cs_trans_pkg_expiry) ? $cs_trans_pkg_expiry : '';
			$cs_trans_cv_num = isset($cs_trans_cv_num) ? $cs_trans_cv_num : '';
			$post_author = $cs_trans_user;
			$transaction_post = array(
				'post_title' => '#' . $cs_trans_id,
				'post_status' => 'publish',
				'post_author' => $post_author,
				'post_type' => 'cs-transactions',
				'post_date' => current_time('Y-m-d H:i:s')
			);
			//insert the transaction
			$trans_id = wp_insert_post($transaction_post);
			$cs_trans_pay_method = '';
			//if (isset($_POST['cs_payment_gateway']) && $_POST['cs_payment_gateway'] == 'cs_pre_bank_transfer') {
			$cs_trans_pay_method = isset($_POST['cs_payment_gateway']) ? $_POST['cs_payment_gateway'] : '';
			//}
			$cs_trans_array = array(
				'transaction_id' => $cs_trans_id,
				'transaction_user' => $cs_trans_user,
				'transaction_cv_pkg' => $cs_trans_pkg,
				'transaction_amount' => $cs_trans_amount,
				'transaction_pay_method' => $cs_trans_pay_method,
				'transaction_expiry_date' => $cs_trans_pkg_expiry,
				'transaction_listings' => $cs_trans_cv_num,
				'transaction_type' => 'cv_trans',
			);
			foreach ( $cs_trans_array as $trans_key => $trans_val ) {
				update_post_meta($trans_id, "cs_{$trans_key}", $trans_val);
			}

			if ( $cs_trans_amount > 0 ) {
				$cs_transaction_fields['cs_order_id'] = $trans_id;

				if ( isset($_POST['cs_payment_gateway']) && $_POST['cs_payment_gateway'] == 'cs_wooC_GATEWAY' && ! empty($cs_transaction_fields) ) {

					global $Payment_Processing;
					$payment_args = array(
						'package_id' => $cs_transaction_fields['cs_trans_id'],
						'package_name' => $cs_transaction_fields['cs_package_title'],
						'price' => $cs_transaction_fields['cs_trans_amount'],
						'custom_var' => array(
							'cs_order_id' => $cs_transaction_fields['cs_order_id'],
						),
						'redirect_url' => get_option('wooC_current_page')
					);
					$Payment_Processing->processing_payment($payment_args);
				} else if ( isset($_POST['cs_payment_gateway']) && $_POST['cs_payment_gateway'] == 'CS_PAYPAL_GATEWAY' && ! empty($cs_transaction_fields) ) {
					$paypal_gateway = new CS_PAYPAL_GATEWAY();
					$paypal_gateway->cs_proress_request($cs_transaction_fields);
				} else if ( isset($_POST['cs_payment_gateway']) && $_POST['cs_payment_gateway'] == 'CS_AUTHORIZEDOTNET_GATEWAY' && ! empty($cs_transaction_fields) ) {
					$authorizedotnet = new CS_AUTHORIZEDOTNET_GATEWAY();
					$authorizedotnet->cs_proress_request($cs_transaction_fields);
				} else if ( isset($_POST['cs_payment_gateway']) && $_POST['cs_payment_gateway'] == 'CS_SKRILL_GATEWAY' && ! empty($cs_transaction_fields) ) {
					$skrill = new CS_SKRILL_GATEWAY();
					$skrill->cs_proress_request($cs_transaction_fields);
				} else if ( isset($_POST['cs_payment_gateway']) && $_POST['cs_payment_gateway'] == 'CS_PRE_BANK_TRANSFER' && ! empty($cs_transaction_fields) ) {
					$banktransfer = new CS_PRE_BANK_TRANSFER();
					return $banktransfer->cs_proress_request($cs_transaction_fields);
				} else {
					// Do Nothing
				}
			}
		}

		/**
		 * End Function for Cv  pay Process
		 */

		/**
		 * Start Function for Creating add Transactions
		 */
		public function cs_cv_add_trans($values = array()) {
			$cs_transaction_fields = $values;
			extract($values);
			$cs_trans_id = isset($cs_trans_id) ? $cs_trans_id : '';
			$cs_trans_user = isset($cs_trans_user) ? $cs_trans_user : '';
			$cs_trans_pkg = isset($cs_trans_package) ? $cs_trans_package : '';
			$cs_trans_amount = isset($cs_trans_amount) ? $cs_trans_amount : '';
			$cs_trans_pkg_expiry = isset($cs_trans_pkg_expiry) ? $cs_trans_pkg_expiry : '';
			$cs_trans_cv_num = isset($cs_trans_cv_num) ? $cs_trans_cv_num : '';
			$post_author = $cs_trans_user;
			$transaction_post = array(
				'post_title' => '#' . $cs_trans_id,
				'post_status' => 'publish',
				'post_author' => $post_author,
				'post_type' => 'cs-transactions',
				'post_date' => current_time('Y-m-d H:i:s')
			);
			//insert the transaction
			$trans_id = wp_insert_post($transaction_post);
			$cs_trans_array = array(
				'transaction_id' => $cs_trans_id,
				'transaction_user' => $cs_trans_user,
				'transaction_cv_pkg' => $cs_trans_pkg,
				'transaction_amount' => $cs_trans_amount,
				'transaction_pay_method' => '',
				'transaction_expiry_date' => $cs_trans_pkg_expiry,
				'transaction_listings' => $cs_trans_cv_num,
				'transaction_type' => 'cv_trans',
				'transaction_status' => 'approved',
			);
			foreach ( $cs_trans_array as $trans_key => $trans_val ) {
				update_post_meta($trans_id, "cs_{$trans_key}", $trans_val);
			}
		}

		/**
		 * End Function for Creating add Transactions
		 */

		/**
		 * Start Function for how to get package Fields
		 */
		public function get_pkg_field($cs_pkg_id = '', $cs_pkg_field = 'package_title') {
			global $cs_plugin_options;
			$cs_packages_options = isset($cs_plugin_options['cs_packages_options']) ? $cs_plugin_options['cs_packages_options'] : '';
			if ( is_array($cs_packages_options) && sizeof($cs_packages_options) > 0 ) {
				$cs_user_package = isset($cs_packages_options[$cs_pkg_id]) ? $cs_packages_options[$cs_pkg_id] : '';
				$cs_pkg_field = isset($cs_user_package[$cs_pkg_field]) ? $cs_user_package[$cs_pkg_field] : '';
				return $cs_pkg_field;
			}
		}

		/**
		 * End Function for how to get package Fields
		 */

		/**
		 * Start Function for how to get CV package Fields
		 */
		public function get_cv_pkg_field($cs_pkg_id = '', $cs_pkg_field = 'cv_pkg_title') {
			global $cs_plugin_options;
			$cs_packages_options = isset($cs_plugin_options['cs_cv_pkgs_options']) ? $cs_plugin_options['cs_cv_pkgs_options'] : '';
			if ( is_array($cs_packages_options) && sizeof($cs_packages_options) > 0 ) {
				$cs_user_package = isset($cs_packages_options[$cs_pkg_id]) ? $cs_packages_options[$cs_pkg_id] : '';
				$cs_pkg_field = isset($cs_user_package[$cs_pkg_field]) ? $cs_user_package[$cs_pkg_field] : '';
				return $cs_pkg_field;
			}
		}

		/**
		 * End Function for how to get CV package Fields
		 */

		/**
		 * Start Function for how to Date Duration posting
		 */
		public function cs_date_conv($cs_duration, $cs_format) {
			if ( $cs_format == "days" ) {
				$cs_adexp = date('Y-m-d H:i:s', strtotime("+" . $cs_duration . " days"));
			} elseif ( $cs_format == "months" ) {
				$cs_adexp = date('Y-m-d H:i:s', strtotime("+" . $cs_duration . " months"));
			} elseif ( $cs_format == "years" ) {
				$cs_adexp = date('Y-m-d H:i:s', strtotime("+" . $cs_duration . " years"));
			} else {
				$cs_adexp = '';
			}
			return $cs_adexp;
		}

		/**
		 * End Function for how to Date Duration posting
		 */

		/**
		 * Start Function how to get post id with the help of Meta key
		 */
		public function cs_get_post_id_by_meta_key($key, $value) {
			global $wpdb;
			$meta = $wpdb->get_results("SELECT * FROM `" . $wpdb->postmeta . "` WHERE meta_key='" . $key . "' AND meta_value='" . $value . "'");
			if ( is_array($meta) && ! empty($meta) && isset($meta[0]) ) {
				$meta = $meta[0];
			}
			if ( is_object($meta) ) {
				return $meta->post_id;
			} else {
				return false;
			}
		}

		/**
		 * End Function how to get post id with the help of Meta key
		 */

		/**
		 * Start Function how to get Ramaining Listing in Job Packages
		 */
		public function cs_pkg_remaining_listing($cs_job_pkg_names = '', $cs_transaction_id = 0) {
			global $post, $current_user, $cs_plugin_options;
			$cs_packages_options = isset($cs_plugin_options['cs_packages_options']) ? $cs_plugin_options['cs_packages_options'] : '';
			$html = '';
			$trans_post_id = $this->cs_get_post_id_by_meta_key("cs_transaction_id", $cs_transaction_id);
			$cs_job_post_ids = get_post_meta($trans_post_id, "cs_job_id", true);
			$cs_job_post_ids = explode(',', $cs_job_post_ids);
			if ( is_array($cs_packages_options) && sizeof($cs_packages_options) > 0 ) {
				if ( isset($cs_packages_options[$cs_job_pkg_names]) ) {
					$cs_user_package = $cs_packages_options[$cs_job_pkg_names];
					$cs_package = isset($cs_user_package['package_id']) ? $cs_user_package['package_id'] : '';
					$cs_pkg_lists = get_post_meta($trans_post_id, 'cs_transaction_listings', true);
					if ( isset($cs_job_post_ids[0]) && $cs_job_post_ids[0] == '' )
						unset($cs_job_post_ids[0]);
					if ( is_array($cs_job_post_ids) && sizeof($cs_job_post_ids) > 0 ) {
						if ( (int) $cs_pkg_lists > (int) sizeof($cs_job_post_ids) ) {
							return (int) $cs_pkg_lists - (int) sizeof($cs_job_post_ids);
						}
					} else {
						return (int) $cs_pkg_lists;
					}
				}
			}
			return 0;
		}

		/**
		 * End Function how to get Ramaining Listing in Job Packages
		 */

		/**
		 * Start Function how to get Ramaining CV's
		 */
		public function cs_pkg_remain_cvs($cs_job_pkg_names = '', $trans_post_id = 0) {
			global $post, $current_user, $cs_plugin_options;
			$cs_packages_options = isset($cs_plugin_options['cs_cv_pkgs_options']) ? $cs_plugin_options['cs_cv_pkgs_options'] : '';
			$html = '';
			$cs_job_post_ids = get_post_meta($trans_post_id, "cs_resume_ids", true);
			$cs_job_post_ids = explode(',', $cs_job_post_ids);
			if ( isset($cs_job_post_ids[0]) && $cs_job_post_ids[0] == '' )
				unset($cs_job_post_ids[0]);
			if ( is_array($cs_packages_options) && sizeof($cs_packages_options) > 0 ) {
				if ( isset($cs_packages_options[$cs_job_pkg_names]) ) {
					$cs_user_package = $cs_packages_options[$cs_job_pkg_names];
					$cs_package = isset($cs_user_package['cv_pkg_id']) ? $cs_user_package['cv_pkg_id'] : '';
					$cs_pkg_lists = get_post_meta($trans_post_id, 'cs_transaction_listings', true);
					if ( isset($cs_job_post_ids[0]) && $cs_job_post_ids[0] == '' )
						unset($cs_job_post_ids[0]);
					if ( is_array($cs_job_post_ids) && sizeof($cs_job_post_ids) > 0 ) {
						if ( (int) $cs_pkg_lists > (int) sizeof($cs_job_post_ids) ) {
							return (int) $cs_pkg_lists - (int) sizeof($cs_job_post_ids);
						}
					} else {
						return (int) $cs_pkg_lists;
					}
				}
			}
			return 0;
		}

		/**
		 * End Function how to get Ramaining CV's
		 */

		/**
		 * Start Function how update Submission CV's
		 */
		public function cs_update_pkg_subs($return_pkg = false, $cs_pckg = '') {
			global $cs_plugin_options;
			$cs_packages_options = isset($cs_plugin_options['cs_packages_options']) ? $cs_plugin_options['cs_packages_options'] : '';
			if ( $cs_pckg != '' && $this->cs_is_pkg_subscribed($cs_pckg) ) {
				if ( $return_pkg == true ) {
					$cs_trans_id = $this->cs_is_pkg_subscribed($cs_pckg, true);
					return array( 'pkg_id' => $cs_pckg, 'trans_id' => $cs_trans_id );
				}
				return true;
			}
			return false;
		}

		/**
		 * End Function how update Submission CV's
		 */

		/**
		 * Start Function how to find for Subscribers for Current User
		 */
		public function cs_is_pkg_subscribed($cs_package = '', $return_trans = false) {
			global $post, $current_user;
			$cs_emp_funs = new cs_employer_functions();
			$cs_current_date = strtotime(date('d-m-Y'));
			$args = array(
				'posts_per_page' => "-1",
				'post_type' => 'cs-transactions',
				'post_status' => 'publish',
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'key' => 'cs_transaction_package',
						'value' => $cs_package,
						'compare' => '=',
					),
					array(
						'key' => 'cs_transaction_user',
						'value' => $current_user->ID,
						'compare' => '=',
					),
					array(
						'key' => 'cs_transaction_expiry_date',
						'value' => $cs_current_date,
						'compare' => '>',
					),
					array(
						'key' => 'cs_transaction_status',
						'value' => 'approved',
						'compare' => '=',
					),
				),
			);
			$custom_query = new WP_Query($args);
			$cs_trans_count = $custom_query->post_count;
			$cs_trnasaction_id = 0;
			$cs_trans_counter = 0;
			if ( $cs_trans_count > 0 ) {
				while ( $custom_query->have_posts() ) : $custom_query->the_post();
					$cs_pkg_list_num = get_post_meta(get_the_id(), 'cs_transaction_listings', true);
					$cs_job_ids = get_post_meta(get_the_id(), 'cs_job_id', true);
					$cs_job_ids = explode(',', $cs_job_ids);
					$cs_ids_num = 0;
					if ( isset($cs_job_ids[0]) && $cs_job_ids[0] == '' )
						unset($cs_job_ids[0]);
					$cs_ids_num = sizeof($cs_job_ids);
					if ( (int) $cs_ids_num < (int) $cs_pkg_list_num ) {
						$cs_trnasaction_id = get_the_id();
					}
					$cs_trans_counter ++;
				endwhile;
			}
			if ( $cs_trans_counter > 0 && isset($cs_trnasaction_id) && $cs_trnasaction_id != '' ) {
				$cs_trnasaction_id = get_post_meta($cs_trnasaction_id, 'cs_transaction_id', true);
				if ( $this->cs_pkg_remaining_listing($cs_package, $cs_trnasaction_id) > 0 ) {
					if ( $return_trans == true ) {
						return $cs_trnasaction_id;
					}
					return true;
				}
			}
			return false;
		}

// Start function for cv package sub
		public function cs_is_cv_pkg_subs($cs_package = '', $return_trans = false) {
			global $post, $current_user;
			$cs_current_date = strtotime(date('d-m-Y'));
			$args = array(
				'posts_per_page' => "-1",
				'post_type' => 'cs-transactions',
				'post_status' => 'publish',
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'key' => 'cs_transaction_cv_pkg',
						'value' => $cs_package,
						'compare' => '=',
					),
					array(
						'key' => 'cs_transaction_user',
						'value' => $current_user->ID,
						'compare' => '=',
					),
					array(
						'key' => 'cs_transaction_expiry_date',
						'value' => $cs_current_date,
						'compare' => '>',
					),
					array(
						'key' => 'cs_transaction_type',
						'value' => 'cv_trans',
						'compare' => '=',
					),
					array(
						'key' => 'cs_transaction_status',
						'value' => 'approved',
						'compare' => '=',
					),
				),
			);
			$custom_query = new WP_Query($args);
			$cs_trans_count = $custom_query->post_count;
			$cs_trnasaction_id = 0;
			$cs_trans_counter = 0;

			if ( $cs_trans_count > 0 ) {
				while ( $custom_query->have_posts() ) : $custom_query->the_post();
					$cs_pkg_cv_num = get_post_meta(get_the_id(), 'cs_transaction_listings', true);
					$cs_resume_ids = get_post_meta(get_the_id(), 'cs_resume_ids', true);
					$cs_resume_ids = explode(',', $cs_resume_ids);
					if ( is_array($cs_resume_ids) && isset($cs_resume_ids[0]) && $cs_resume_ids[0] == '' )
						unset($cs_resume_ids[0]);
					$cs_ids_num = 0;
					$cs_ids_num = sizeof($cs_resume_ids);
					if ( (int) $cs_ids_num < (int) $cs_pkg_cv_num ) {
						$cs_trnasaction_id = get_the_id();
					}
					$cs_trans_counter ++;
				endwhile;
			}
			if ( $cs_trans_counter > 0 && isset($cs_trnasaction_id) && $cs_trnasaction_id != '' ) {
				if ( $this->cs_pkg_remain_cvs($cs_package, $cs_trnasaction_id) > 0 ) {
					if ( $return_trans == true ) {
						return $cs_trnasaction_id;
					}
					return true;
				}
			}
			return false;
		}

		/**
		 * End Function how to find for Subscribers for Current User
		 */

		/**
		 * Start Function how to find Expire Packages
		 */
		public function cs_expire_pkgs_id() {
			global $post, $current_user;
			$trans_array1 = $trans_array2 = array();
			$cs_emp_funs = new cs_employer_functions();
			$cs_current_date = strtotime(date('d-m-Y'));
			$args = array(
				'posts_per_page' => "-1",
				'post_type' => 'cs-transactions',
				'post_status' => 'publish',
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'key' => 'cs_transaction_package',
						'value' => '',
						'compare' => '!=',
					),
					array(
						'key' => 'cs_transaction_user',
						'value' => $current_user->ID,
						'compare' => '=',
					),
					array(
						'key' => 'cs_transaction_expiry_date',
						'value' => $cs_current_date,
						'compare' => '<=',
					),
					array(
						'key' => 'cs_transaction_status',
						'value' => 'approved',
						'compare' => '=',
					),
				),
			);
			$custom_query = new WP_Query($args);
			$cs_trans_count = $custom_query->post_count;
			if ( $cs_trans_count > 0 ) {
				while ( $custom_query->have_posts() ) : $custom_query->the_post();
					$trans_array1[] = get_the_id();
				endwhile;
				wp_reset_query();
			}
			$args = array(
				'posts_per_page' => "-1",
				'post_type' => 'cs-transactions',
				'post_status' => 'publish',
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'key' => 'cs_transaction_package',
						'value' => '',
						'compare' => '!=',
					),
					array(
						'key' => 'cs_transaction_user',
						'value' => $current_user->ID,
						'compare' => '=',
					),
					array(
						'key' => 'cs_transaction_expiry_date',
						'value' => $cs_current_date,
						'compare' => '>',
					),
					array(
						'key' => 'cs_transaction_status',
						'value' => 'approved',
						'compare' => '=',
					),
				),
			);
			$custom_query = new WP_Query($args);
			$cs_trans_count = $custom_query->post_count;
			if ( $cs_trans_count > 0 ) {
				while ( $custom_query->have_posts() ) : $custom_query->the_post();
					$cs_job_ids = get_post_meta(get_the_id(), 'cs_job_id', true);
					$cs_package = get_post_meta(get_the_id(), 'cs_transaction_package', true);
					$cs_pkg_list_num = get_post_meta(get_the_id(), 'cs_transaction_listings', true);
					$cs_job_ids = explode(',', $cs_job_ids);
					$cs_ids_num = 0;

					if ( isset($cs_job_ids[0]) && $cs_job_ids[0] == '' )
						unset($cs_job_ids[0]);
					$cs_ids_num = sizeof($cs_job_ids);
					if ( (int) $cs_ids_num == (int) $cs_pkg_list_num ) {
						$cs_trnasaction_id = get_the_id();
						if ( $cs_trnasaction_id != '' ) {
							$cs_trnasaction_id = get_post_meta($cs_trnasaction_id, 'cs_transaction_id', true);
							if ( $this->cs_pkg_remaining_listing($cs_package, $cs_trnasaction_id) == 0 ) {
								$trans_array2[] = get_the_id();
							}
						}
					}
				endwhile;
				wp_reset_query();
			}
			$trans_array = array_merge($trans_array1, $trans_array2);
			return $trans_array;
		}

		/**
		 * End Function how to find Expire Packages
		 */

		/**
		 * Start Function how to find Expire CV's
		 */
		public function cs_expire_cv_pkgs_id() {
			global $post, $current_user;
			$trans_array1 = $trans_array2 = array();
			$cs_emp_funs = new cs_employer_functions();
			$cs_current_date = strtotime(date('d-m-Y'));
			$args = array(
				'posts_per_page' => "-1",
				'post_type' => 'cs-transactions',
				'post_status' => 'publish',
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'key' => 'cs_transaction_cv_pkg',
						'value' => '',
						'compare' => '!=',
					),
					array(
						'key' => 'cs_transaction_user',
						'value' => $current_user->ID,
						'compare' => '=',
					),
					array(
						'key' => 'cs_transaction_expiry_date',
						'value' => $cs_current_date,
						'compare' => '<=',
					),
					array(
						'key' => 'cs_transaction_type',
						'value' => 'cv_trans',
						'compare' => '=',
					),
					array(
						'key' => 'cs_transaction_status',
						'value' => 'approved',
						'compare' => '=',
					),
				),
			);
			$custom_query = new WP_Query($args);
			$cs_trans_count = $custom_query->post_count;
			if ( $cs_trans_count > 0 ) {
				while ( $custom_query->have_posts() ) : $custom_query->the_post();
					$trans_array1[] = get_the_id();
				endwhile;
				wp_reset_query();
			}
			$args = array(
				'posts_per_page' => "-1",
				'post_type' => 'cs-transactions',
				'post_status' => 'publish',
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'key' => 'cs_transaction_cv_pkg',
						'value' => '',
						'compare' => '!=',
					),
					array(
						'key' => 'cs_transaction_user',
						'value' => $current_user->ID,
						'compare' => '=',
					),
					array(
						'key' => 'cs_transaction_expiry_date',
						'value' => $cs_current_date,
						'compare' => '>',
					),
					array(
						'key' => 'cs_transaction_type',
						'value' => 'cv_trans',
						'compare' => '=',
					),
					array(
						'key' => 'cs_transaction_status',
						'value' => 'approved',
						'compare' => '=',
					),
				),
			);
			$custom_query = new WP_Query($args);
			$cs_trans_count = $custom_query->post_count;
			if ( $cs_trans_count > 0 ) {
				while ( $custom_query->have_posts() ) : $custom_query->the_post();
					$cs_resume_ids = get_post_meta(get_the_id(), 'cs_resume_ids', true);
					$cs_package = get_post_meta(get_the_id(), 'cs_transaction_cv_pkg', true);
					$cs_pkg_list_num = get_post_meta(get_the_id(), 'cs_transaction_listings', true);
					$cs_resume_ids = explode(',', $cs_resume_ids);
					if ( isset($cs_resume_ids[0]) && $cs_resume_ids[0] == '' )
						unset($cs_resume_ids[0]);
					$cs_ids_num = sizeof($cs_resume_ids);
					if ( (int) $cs_ids_num == (int) $cs_pkg_list_num ) {
						$cs_trnasaction_id = get_the_id();
						if ( $cs_trnasaction_id != '' ) {
							$cs_trnasaction_id = get_post_meta($cs_trnasaction_id, 'cs_transaction_id', true);
							if ( $this->cs_pkg_remain_cvs($cs_package, get_the_id()) == 0 ) {
								$trans_array2[] = get_the_id();
							}
						}
					}
				endwhile;
				wp_reset_query();
			}
			$trans_array = array_merge($trans_array1, $trans_array2);
			return $trans_array;
		}

		/**
		 * Start Function how to find Expire CV's
		 */

		/**
		 * Start Function how to find Expire for Transaction
		 */
		public function cs_expire_pkgs($cs_trans) {
			global $cs_plugin_options;
			$cs_packages_options = isset($cs_plugin_options['cs_packages_options']) ? $cs_plugin_options['cs_packages_options'] : '';
			$cs_transac_id = get_post_meta($cs_trans, 'cs_transaction_id', true);
			$cs_transac_pkg = get_post_meta($cs_trans, 'cs_transaction_package', true);
			$cs_transac_expiry = get_post_meta($cs_trans, 'cs_transaction_expiry_date', true);
			$html = '';
			$cs_package_title = $this->get_pkg_field($cs_transac_pkg);
			$cs_user_package = isset($cs_packages_options[$cs_transac_pkg]) ? $cs_packages_options[$cs_transac_pkg] : '';
			$cs_pkg_listings = get_post_meta($cs_trans, 'cs_transaction_listings', true);
			$cs_package_type = isset($cs_user_package['package_type']) ? $cs_user_package['package_type'] : '';
			$cs_listing_left = $this->cs_pkg_remaining_listing($cs_transac_pkg, $cs_transac_id);
			$cs_ads_used = ($cs_pkg_listings > 0 && $cs_pkg_listings > $cs_listing_left) ? ($cs_pkg_listings - $cs_listing_left) : '0';
			if ( $cs_transac_expiry != '' ) {
				$cs_transac_expiry = date_i18n(get_option('date_format'), $cs_transac_expiry);
			}
			$html .= '<td>#' . $cs_transac_id . '</td>';
			$html .= '<td>' . $cs_package_title . '</td>';
			$html .= '<td>' . $cs_transac_expiry . '</td>';
			if ( $cs_package_type == 'subscription' ) {
				$html .= '<td>' . $cs_pkg_listings . '</td>';
				$html .= '<td>' . $cs_ads_used . '</td>';
				$html .= '<td>' . $cs_listing_left . '</td>';
			} else {
				$html .= '<td colspan="3">' . __('Single Submission', 'jobhunt') . '</td>';
			}
			$html .= '<td>' . __('Expire', 'jobhunt') . '</td>';
			return $html;
		}

		/**
		 * End Function how to find Expire for Transaction
		 */

		/**
		 * Start Function how to find Expire CV'S Transactions
		 */
		public function cs_cv_expire_pkgs($cs_trans) {
			global $cs_plugin_options;
			$cs_packages_options = isset($cs_plugin_options['cs_cv_pkgs_options']) ? $cs_plugin_options['cs_cv_pkgs_options'] : '';
			$cs_transac_id = get_post_meta($cs_trans, 'cs_transaction_id', true);
			$cs_transac_pkg = get_post_meta($cs_trans, 'cs_transaction_cv_pkg', true);
			$cs_transac_expiry = get_post_meta($cs_trans, 'cs_transaction_expiry_date', true);
			$html = '';
			$cs_package_title = $this->get_cv_pkg_field($cs_transac_pkg);
			$cs_user_package = isset($cs_packages_options[$cs_transac_pkg]) ? $cs_packages_options[$cs_transac_pkg] : '';
			$cs_pkg_listings = get_post_meta($cs_trans, 'cs_transaction_listings', true);
			$cs_listing_left = $this->cs_pkg_remain_cvs($cs_transac_pkg, $cs_trans);
			$cs_ads_used = ($cs_pkg_listings > 0 && $cs_pkg_listings > $cs_listing_left) ? ($cs_pkg_listings - $cs_listing_left) : '0';
			if ( $cs_transac_expiry != '' ) {
				$cs_transac_expiry = date_i18n(get_option('date_format'), $cs_transac_expiry);
			}
			$html .= '<td>#' . $cs_transac_id . '</td>';
			$html .= '<td>' . $cs_package_title . '</td>';
			$html .= '<td>' . $cs_transac_expiry . '</td>';
			$html .= '<td>' . $cs_pkg_listings . '</td>';
			$html .= '<td>' . $cs_ads_used . '</td>';
			$html .= '<td>' . $cs_listing_left . '</td>';
			$html .= '<td>' . __('Expire', 'jobhunt') . '</td>';
			return $html;
		}

		/**
		 * Start function to check cv package sub
		 */
		public function is_cv_pkg_subs($return_pkg = false) {
			global $cs_plugin_options;
			$cs_packages_options = isset($cs_plugin_options['cs_cv_pkgs_options']) ? $cs_plugin_options['cs_cv_pkgs_options'] : '';

			if ( is_array($cs_packages_options) && sizeof($cs_packages_options) > 0 ) {

				foreach ( $cs_packages_options as $pkg_key => $cv_pkg ) {

					if ( $this->cs_is_cv_pkg_subs($pkg_key) ) {
						if ( $return_pkg == true ) {
							$cs_trans_id = $this->cs_is_cv_pkg_subs($pkg_key, true);
							return array( 'pkg_id' => $pkg_key, 'trans_id' => $cs_trans_id );
						}
						return true;
					}
				}
			}

			return false;
		}

		/**
		 * Start Function how to find User pakcakge Details
		 */
		function cs_user_pkg_detail($cs_pkg, $cs_ad_expiry = '', $cs_profile = false) {
			global $cs_plugin_options;
			$cs_packages_options = isset($cs_plugin_options['cs_packages_options']) ? $cs_plugin_options['cs_packages_options'] : '';
			$html = '';
			if ( is_array($cs_pkg) && isset($cs_pkg['pkg_id']) && isset($cs_pkg['trans_id']) ) {
				if ( is_array($cs_packages_options) && sizeof($cs_packages_options) > 0 ) {
					$cs_listing_left = $this->cs_pkg_remaining_listing($cs_pkg['pkg_id'], $cs_pkg['trans_id']);
					$trans_post_id = $this->cs_get_post_id_by_meta_key("cs_transaction_id", $cs_pkg['trans_id']);
					$cs_transac_expiry = get_post_meta($trans_post_id, 'cs_transaction_expiry_date', true);
					$cs_user_package = $cs_packages_options[$cs_pkg['pkg_id']];
					$cs_package_title = isset($cs_user_package['package_title']) ? $cs_user_package['package_title'] : '';
					$cs_pkg_listings = get_post_meta($trans_post_id, 'cs_transaction_listings', true);
					$cs_package_type = isset($cs_user_package['package_type']) ? $cs_user_package['package_type'] : '';
					$cs_ads_used = ($cs_pkg_listings > 0 && $cs_pkg_listings > $cs_listing_left) ? ($cs_pkg_listings - $cs_listing_left) : '0';
					if ( $cs_transac_expiry != '' ) {
						$cs_transac_expiry = date_i18n(get_option('date_format'), $cs_transac_expiry);
					}
					if ( $cs_profile == true ) {
						$html .= '<td>#' . $cs_pkg['trans_id'] . '</td>';
						$html .= '<td>' . $cs_package_title . '</td>';
						$html .= '<td>' . $cs_transac_expiry . '</td>';
						if ( $cs_package_type == 'subscription' ) {
							$html .= '<td>' . $cs_pkg_listings . '</td>';
							$html .= '<td>' . $cs_ads_used . '</td>';
							$html .= '<td>' . $cs_listing_left . '</td>';
						} else {
							$html .= '<td colspan="3">' . __('Single Submission', 'jobhunt') . '</td>';
						}
						$html .= '<td>' . __('Active', 'jobhunt') . '</td>';
					} else {
						$html .= '<div class="cs-subscription-box">';
						$html .= '<div class="cs-subscription-head">
                                        <h4>' . __('Subscription Details', 'jobhunt') . '</h4>
                                  </div>';
						$html .= '<ul>';
						$html .= '<li><span class="subs-title">' . __('Transaction', 'jobhunt') . ' </span> <span class="subs-value">#' . $cs_pkg['trans_id'] . '</span></li>';
						$html .= '<li><span class="subs-title">' . __('Package', 'jobhunt') . ' </span> <span class="subs-value">' . $cs_package_title . '</span></li>';
						$html .= '<li><span class="subs-title">' . __('Expiry', 'jobhunt') . ' </span> <span class="subs-value">' . $cs_transac_expiry . '</span></li>';
						if ( $cs_package_type == 'subscription' ) {
							$html .= '<li><span class="subs-title">' . __('Total Jobs', 'jobhunt') . ' </span> <span class="subs-value">' . $cs_pkg_listings . '</span></li>';
							$html .= '<li><span class="subs-title">' . __('Used', 'jobhunt') . ' </span> <span class="subs-value">' . $cs_ads_used . '</span></li>';
							$html .= '<li><span class="subs-title">' . __('Remaining', 'jobhunt') . ' </span> <span class="subs-value">' . $cs_listing_left . '</span></li>';
						} else {
							$html .= '<li><span class="subs-title">' . __('Submission', 'jobhunt') . ' </span> <span class="subs-value">' . __('Single Submission', 'jobhunt') . '</span></li>';
						}
						$html .= '</ul>';
						$html .= '</div>';
					}
				}
			} else {
				if ( is_array($cs_packages_options) && sizeof($cs_packages_options) > 0 ) {
					$cs_user_package = $cs_packages_options[$cs_pkg];
					$cs_package_title = isset($cs_user_package['package_title']) ? $cs_user_package['package_title'] : '';
					if ( $cs_ad_expiry != '' ) {
						$cs_ad_expiry = date_i18n(get_option('date_format'), $cs_ad_expiry);
					}
					$html .= '<div class="cs-subscription-box">';
					$html .= '<div class="cs-subscription-head">
                                <h4>' . __('Subscription Details', 'jobhunt') . '</h4>
                            </div>';
					$html .= '<ul>';
					$html .= '<li><span class="subs-title">' . __('Package', 'jobhunt') . ' </span> <span class="subs-value">' . $cs_package_title . '</span></li>';
					$html .= '<li><span class="subs-title">' . __('Ad Expiry', 'jobhunt') . ' </span> <span class="subs-value">' . $cs_ad_expiry . '</span></li>';
					$html .= '</ul>';
					$html .= '</div>';
				}
			}
			return $html;
		}

		/**
		 * End Function how to find User pakcakge Details
		 */

		/**
		 * Start Function how to find User CV's pakcakge Details
		 */
		function user_cv_pkg_detail($cs_pkg, $cs_profile = false) {
			global $cs_plugin_options;
			$cs_packages_options = isset($cs_plugin_options['cs_cv_pkgs_options']) ? $cs_plugin_options['cs_cv_pkgs_options'] : '';
			$html = '';
			if ( is_array($cs_pkg) && isset($cs_pkg['pkg_id']) && isset($cs_pkg['trans_id']) ) {
				$cs_transac_id = get_post_meta($cs_pkg['trans_id'], 'cs_transaction_id', true);
				$cs_transac_expiry = get_post_meta($cs_pkg['trans_id'], 'cs_transaction_expiry_date', true);
				if ( is_array($cs_packages_options) && sizeof($cs_packages_options) > 0 ) {
					$cs_listing_left = $this->cs_pkg_remain_cvs($cs_pkg['pkg_id'], $cs_pkg['trans_id']);
					$cs_user_package = $cs_packages_options[$cs_pkg['pkg_id']];
					$cs_package_title = isset($cs_user_package['cv_pkg_title']) ? $cs_user_package['cv_pkg_title'] : '';
					$cs_pkg_listings = get_post_meta($cs_pkg['trans_id'], 'cs_transaction_listings', true);
					$cs_ads_used = ($cs_pkg_listings > 0 && $cs_pkg_listings > $cs_listing_left) ? ($cs_pkg_listings - $cs_listing_left) : '0';
					if ( $cs_transac_expiry != '' ) {
						$cs_transac_expiry = date_i18n(get_option('date_format'), $cs_transac_expiry);
					}
					if ( $cs_profile == true ) {
						$html .= '<td>#' . $cs_transac_id . '</td>';
						$html .= '<td>' . $cs_package_title . '</td>';
						$html .= '<td>' . $cs_transac_expiry . '</td>';
						$html .= '<td>' . $cs_pkg_listings . '</td>';
						$html .= '<td>' . $cs_ads_used . '</td>';
						$html .= '<td>' . $cs_listing_left . '</td>';
						$html .= '<td>' . __('Active', 'jobhunt') . '</td>';
					} else {
						$html .= '<div class="cs-subscription-box">';
						$html .= '<div class="cs-subscription-head">
                                    <h4>' . __('Subscription Details', 'jobhunt') . '</h4>
                                </div>';
						$html .= '<ul>';
						$html .= '<li><span class="subs-title">' . __('Transaction', 'jobhunt') . ' </span> <span class="subs-value">#' . $cs_transac_id . '</span></li>';
						$html .= '<li><span class="subs-title">' . __('Package', 'jobhunt') . ' </span> <span class="subs-value">' . $cs_package_title . '</span></li>';
						$html .= '<li><span class="subs-title">' . __('Total CV\'s', 'jobhunt') . ' </span> <span class="subs-value">' . $cs_pkg_listings . '</span></li>';
						$html .= '<li><span class="subs-title">' . __('Expiry', 'jobhunt') . ' </span> <span class="subs-value">' . $cs_transac_expiry . '</span></li>';
						$html .= '<li><span class="subs-title">' . __('Total used CV\'s', 'jobhunt') . ' </span> <span class="subs-value">' . $cs_ads_used . '</span></li>';
						$html .= '<li><span class="subs-title">' . __('Remaining', 'jobhunt') . ' </span> <span class="subs-value">' . $cs_listing_left . '</span></li>';
						$html .= '</ul>';
						$html .= '</div>';
					}
				}
			}
			return $html;
		}

		/**
		 * End Function how to find User CV's pakcakge Details
		 */

		/**
		 * Start Function how to find Subscriber Summary
		 */
		public function cs_subscribed_pkg_summary($cs_job_pkg_names = '') {
			$html = '';
			if ( $this->cs_is_pkg_subscribed($cs_job_pkg_names) ) {
				$cs_trans_id = $this->cs_is_pkg_subscribed($cs_job_pkg_names, true);
				$cs_pkg_title = $this->get_pkg_field($cs_job_pkg_names);
				$cs_trans_post_id = $this->cs_get_post_id_by_meta_key("cs_transaction_id", $cs_trans_id);
				$cs_pkg_listings = get_post_meta($cs_trans_post_id, 'cs_transaction_listings', true);
				$cs_listing_left = $this->cs_pkg_remaining_listing($cs_job_pkg_names, $cs_trans_id);
				$cs_package_type = $this->get_pkg_field($cs_job_pkg_names, 'package_type');
				$cs_used_listings = ($cs_pkg_listings > 0 && $cs_pkg_listings > $cs_listing_left) ? ($cs_pkg_listings - $cs_listing_left) : '0';
				$html .= '<div class="cs-subscription-box">';
				$html .= '<div class="cs-subscription-head">
                            <h4>' . __('Subscription Details', 'jobhunt') . '</h4>
                        </div>';
				$html .= '<ul>';
				$html .= '<li><span class="subs-title">' . __('Transaction', 'jobhunt') . ' </span> <span class="subs-value">#' . $cs_trans_id . '</span></li>';
				$html .= '<li><span class="subs-title">' . __('Package', 'jobhunt') . ' </span> <span class="subs-value">' . $cs_pkg_title . '</span></li>';
				if ( $cs_package_type == 'subscription' ) {
					$html .= '<li><span class="subs-title">' . __('Total Jobs', 'jobhunt') . ' </span> <span class="subs-value">' . $cs_pkg_listings . '</span></li>';
					$html .= '<li><span class="subs-title">' . __('Used', 'jobhunt') . ' </span> <span class="subs-value">' . $cs_used_listings . '</span></li>';
					$html .= '<li><span class="subs-title">' . __('Remaining', 'jobhunt') . ' </span> <span class="subs-value">' . $cs_listing_left . '</span></li>';
				} else {
					$html .= '<li><span class="subs-title">' . __('Submission', 'jobhunt') . ' </span> <span class="subs-value">' . __('Single Submission', 'jobhunt') . '</span></li>';
				}
				$html .= '</ul>';
				$html .= '</div>';
			}
			return $html;
		}

		/**
		 * End Function how to find Subscriber Summary
		 */

		/**
		 * Start Function how to find Job Expiry
		 */
		public function cs_job_expiry($cs_pkg_id = '') {
			if ( $cs_pkg_id != '' ) {
				$cs_list_expiry = $this->get_pkg_field($cs_pkg_id, 'package_submission_limit');
				$cs_list_dur = $this->get_pkg_field($cs_pkg_id, 'cs_list_dur');
				$cs_job_expiry = $this->cs_date_conv($cs_list_expiry, $cs_list_dur);
				return $cs_job_expiry;
			}
		}

		/**
		 * End Function how to find Job Expiry
		 */

		/**
		 * Start Function for Job Delete
		 */
		public function cs_job_delete() {
			global $current_user, $cs_plugin_options;
			$cs_jd_id = isset($_POST['u_id']) ? $_POST['u_id'] : '';

			$post_data = get_post($cs_jd_id);
			$job_title = $post_data->post_title;
			if ( $cs_jd_id != '' ) {
				$cs_jb_emplyr = get_post_meta((int) $cs_jd_id, 'cs_job_username', true);
				if ( $cs_jb_emplyr == (string) $current_user->ID ) {
					update_post_meta($cs_jd_id, 'cs_job_status', 'delete');
					update_post_meta($cs_jd_id, 'cs_job_expired', strtotime(date("d-m-Y", strtotime('-1 days'))));
				}
				echo '<i class="icon-trash"></i>';
				$cs_email_template_atts = array(
					'job_title' => $job_title,
					'user_name' => $current_user->display_name,
					'email' => $current_user->user_email,
				);

				do_action('delete_job_notification', $cs_email_template_atts);
			}
			die();
		}

		/**
		 * End Function for Job Delete
		 */

		/**
		 * Start Function for Job update Status
		 */
		public function cs_job_status_update() {
			global $current_user;
			$response = '';
			$cs_jd_id = isset($_POST['cs_jobid']) ? $_POST['cs_jobid'] : '';
			$cs_status = isset($_POST['cs_status']) ? $_POST['cs_status'] : '';
			if ( $cs_jd_id != '' && $cs_status != '' && ($cs_status == 'active' || $cs_status == 'inactive') ) {
				$cs_jb_emplyr = get_post_meta((int) $cs_jd_id, 'cs_job_username', true);
				$cs_job_status = get_post_meta((int) $cs_jd_id, 'cs_job_status', true);
				$cs_job_expired = get_post_meta((int) $cs_jd_id, "cs_job_expired", true);
				// check allow user to change status
				$job_status_link_allow = 1;
				if ( $cs_job_status != 'active' && $cs_job_status != 'inactive' ) // check staus diffrent 
					$job_status_link_allow = 0;
				if ( $cs_job_expired < time() ) // check job expire
					$job_status_link_allow = 0;
				if ( $cs_jb_emplyr != (string) $current_user->ID )
					$job_status_link_allow = 0;
				if ( $job_status_link_allow == 1 ) {
					update_post_meta($cs_jd_id, 'cs_job_status', $cs_status);
					if ( $cs_status == 'inactive' ) {
						$response['icon'] = '<i class="icon-eye-slash"></i>';
					} else if ( $cs_status == 'active' ) {
						$response['icon'] = '<i class="icon-eye3"></i>';
					}
					$response['error'] = 0;
					$response['message'] = __("You have changed job status successfully", "jobhunt");
				} else {
					$response['error'] = 1;
					$response['message'] = __("You are not authorized for this action", "jobhunt");
				}
			} else {
				$response['error'] = 1;
				$response['message'] = __("You are not authorized for this action", "jobhunt");
			}
			echo json_encode($response);
			die();
		}

		/**
		 * End Function for Job update Status
		 */

		/**
		 * Start Function for Resume Delete
		 */
		public function cs_fav_resume_del() {
			global $current_user, $cs_plugin_options;
			$cs_candidate_switch = isset($cs_plugin_options['cs_candidate_switch']) ? $cs_plugin_options['cs_candidate_switch'] : '';
			$cs_emp_funs = new cs_employer_functions();
			$cs_resume_id = isset($_POST['cs_id']) ? $_POST['cs_id'] : '';
			$cs_emp_id = $current_user->ID; // only get the login user data
			if ( $cs_candidate_switch == 'on' ) {
				if ( $cs_emp_id != '' && $cs_resume_id != '' ) {
					$cs_fav_resumes = get_user_meta($cs_emp_id, "cs_fav_resumes", true);
					if ( is_array($cs_fav_resumes) && sizeof($cs_fav_resumes) > 0 ) {
						foreach ( array_keys($cs_fav_resumes, $cs_resume_id, true) as $key ) {
							unset($cs_fav_resumes[$key]);
						}
						$cs_fav_resumes = array_unique($cs_fav_resumes);
						update_user_meta($cs_emp_id, "cs_fav_resumes", $cs_fav_resumes);
					}
					echo '<i class="icon-trash"></i>';
				}
			} else {
				// free wishlist
				cs_remove_from_user_meta_list($cs_resume_id, 'cs-user-resumes-wishlist', $current_user->ID);
				echo '<i class="icon-trash"></i>';
			}
			die();
		}

		/**
		 * End Function for Resume Delete
		 */

		/**
		 * Start Function for Checking Employeer Resume
		 */
		public function cs_check_emp_resume($id) {
			global $current_user;
			$cs_emp_id = $current_user->ID;
			$cs_fav_resumes = get_user_meta($cs_emp_id, "cs_fav_resumes", true);
			if ( ! is_array($cs_fav_resumes) ) {
				$cs_fav_resumes = array();
			}
			if ( in_array($id, $cs_fav_resumes) ) {
				return true;
			}
			return false;
		}

		/**
		 * End Function for Checking Employeer Resume
		 */

		/**
		 * Start Function for Checking Employeer
		 */
		public function cs_emp_check() {
			$cs_uid = isset($_POST['uid']) ? $_POST['uid'] : '';
			if ( $cs_uid != '' ) {
				if ( ! $this->is_employer() ) {
					_e('Become Employer to Subscribe', 'jobhunt');
				}
			}
			die();
		}

		/**
		 * end Function for Checking Employeer
		 */

		/**
		 * Start Function for Time elapsed
		 */
		public function cs_time_elapsed($ptime) {

			return human_time_diff($ptime, current_time('timestamp')) . __(' ago', 'jobhunt');

			$etime = time() - strtotime($ptime);
			if ( $etime < 1 ) {
				return '0 seconds';
			}
			$a = array( 365 * 24 * 60 * 60 => 'year',
				30 * 24 * 60 * 60 => 'month',
				24 * 60 * 60 => 'day',
				60 * 60 => 'hour',
				60 => 'minute',
				1 => 'second'
			);
			$a_plural = array( 'year' => 'years',
				'month' => 'months',
				'day' => 'days',
				'hour' => 'hours',
				'minute' => 'minutes',
				'second' => 'seconds'
			);
			foreach ( $a as $secs => $str ) {
				$d = $etime / $secs;
				if ( $d >= 1 ) {
					$r = round($d);
					return $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . __(' ago', 'jobhunt');
				}
			}
		}

		/**
		 * end Function for Time elapsed
		 */

		/**
		 * Start Function for posting jobs number
		 */
		public function posted_jobs_num($uid) {
			$args = array(
				'posts_per_page' => "-1",
				'post_type' => 'jobs',
				'post_status' => 'publish',
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'key' => 'cs_job_username',
						'value' => $uid,
						'compare' => '=',
					),
					array(
						'key' => 'cs_job_status',
						'value' => 'delete',
						'compare' => '!=',
					),
				),
				'orderby' => 'ID',
				'order' => 'DESC',
			);

			$custom_query = new WP_Query($args);
			return $custom_query->post_count;
		}

		/**
		 * end Function for posting jobs number
		 */

		/**
		 * Start Function for how many jobs are active
		 */
		public function active_jobs_num($uid) {
			$args = array(
				'posts_per_page' => "-1",
				'post_type' => 'jobs',
				'post_status' => 'publish',
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'key' => 'cs_job_username',
						'value' => $uid,
						'compare' => '=',
					),
					array(
						'key' => 'cs_job_status',
						'value' => 'active',
						'compare' => '=',
					),
				),
				'orderby' => 'ID',
				'order' => 'DESC',
			);

			$custom_query = new WP_Query($args);

			return $custom_query->post_count;
		}

		// Start function to check user role as a employer 

		public function is_employer() {

			global $current_user;
			$user_role = cs_get_loginuser_role();
			if ( isset($user_role) && $user_role <> '' && $user_role == 'cs_employer' ) {
				return true;
			}
			return false;
		}

		// start function to set user favourite 

		public function cs_set_user_fav($new_id = '') {

			global $current_user;

			$cs_fav_ids = 'cs_user_fav_' . $current_user->ID;

			if ( $new_id != '' ) {

				$exist_vals = '';
				if ( isset($_COOKIE[$cs_fav_ids]) ) {
					$exist_vals = $_COOKIE[$cs_fav_ids];
				}

				$ids_array = explode(',', $exist_vals);

				if ( is_array($ids_array) && ! in_array($new_id, $ids_array) ) {

					if ( $ids_array[0] != '' ) {
						$ids_array[] = $new_id;
					} else {
						$ids_array = array( $new_id );
					}
				}

				$ids_array = implode(',', $ids_array);

				if ( isset($_COOKIE[$cs_fav_ids]) ) {

					unset($_COOKIE[$cs_fav_ids]);
					setcookie($cs_fav_ids, null, -1, '/');
				}

				setcookie($cs_fav_ids, $ids_array, time() + 86400, '/');
			}
		}

		/**
		 * Start Function for doing unset user
		 */
		public function cs_unset_user_fav() {
			global $current_user;
			$cs_return = array();
			$rem_id = isset($_POST['cs_id']) && $_POST['cs_id'] != '' ? $_POST['cs_id'] : '';
			$cs_fav_ids = 'cs_user_fav_' . $current_user->ID;
			$cs_return['count'] = '';
			if ( $rem_id != '' ) {
				$exist_vals = '';
				if ( isset($_COOKIE[$cs_fav_ids]) ) {
					$exist_vals = $_COOKIE[$cs_fav_ids];
				}
				$ids_array = explode(',', $exist_vals);

				if ( is_array($ids_array) && in_array($rem_id, $ids_array) ) {

					if ( ( $key = array_search($rem_id, $ids_array) ) !== false ) {
						unset($ids_array[$key]);
					}
				}
				$cs_return['count'] = sizeof($ids_array);
				$ids_array = implode(',', $ids_array);
				if ( isset($_COOKIE[$cs_fav_ids]) ) {
					unset($_COOKIE[$cs_fav_ids]);
					setcookie($cs_fav_ids, null, -1, '/');
				}
				setcookie($cs_fav_ids, $ids_array, time() + 86400, '/');
			}
			echo json_encode($cs_return);
			die;
		}

		/**
		 * End Function for doing unset user
		 */

		/**
		 * Start Function finding header favorites
		 */
		public function cs_header_favorites() {
			//global $current_user;
			global $current_user, $post, $cs_plugin_options;
			if ( ! is_user_logged_in() ) {
				echo '<div class="wish-list"> 
                        <a><i class="icon-heart6"></i></a> <em class="cs-bgcolor" id="cs-fav-counts">0</em>
                    </div>';
			} else {
				candidate_header_wishlist(); // getting all list with HTML
			}
		}

		/**
		 * End Function finding header favorites
		 */

		/**
		 * Start Function for geting all job application
		 */
		public function all_jobs_apps($uid = '') {
			$args = array(
				'posts_per_page' => "-1",
				'post_type' => 'jobs',
				'post_status' => 'publish',
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'key' => 'cs_job_username',
						'value' => $uid,
						'compare' => '=',
					),
					array(
						'key' => 'cs_job_status',
						'value' => 'delete',
						'compare' => '!=',
					),
				),
				'orderby' => 'ID',
				'order' => 'DESC',
			);
			$custom_query = new WP_Query($args);

			$cs_apps = 0;
			if ( $custom_query->have_posts() ) {

				while ( $custom_query->have_posts() ) : $custom_query->the_post();

					// getting job' application count
					$cs_applicants = count_usermeta('cs-user-jobs-applied-list', serialize(strval(get_the_id())), 'LIKE', true);
					$cs_apps += count($cs_applicants);
				endwhile;
			}
			return $cs_apps;
		}

		/**
		 * End Function for geting all job application
		 */

		/**
		 * Start Function for how to initilize Editor
		 */
		public function cs_init_editor() {
			echo '<div style="display: none;">';
			wp_editor('', 'cs_comp_init_detail', array(
				'textarea_name' => 'cs_comp_init_detail',
				'editor_class' => 'text-input',
				'teeny' => true,
				'media_buttons' => false,
				'textarea_rows' => 4,
				'quicktags' => false
					)
			);
			echo '</div>';
			?>
			<script type="text/javascript">
				jQuery(document).ready(function (e) {
					if (typeof (et_tinyMCEPreInit) == 'undefined') {
						et_tinyMCEPreInit = JSON.stringify(tinyMCEPreInit);
					}
				});
			</script>
			<?php

		}

		/**
		 * End Function for how to initilize Editor
		 */

		/**
		 * Start Function for how to find employer custom fields
		 */
		function cs_employer_custom_fields($cs_user_id = '') {
			global $cs_form_fields2;
			$cs_html = '';
			$cs_job_cus_fields = get_option("cs_employer_cus_fields");
			if ( is_array($cs_job_cus_fields) && sizeof($cs_job_cus_fields) > 0 ) {
				foreach ( $cs_job_cus_fields as $cus_field ) {
					$cs_type = isset($cus_field['type']) ? $cus_field['type'] : '';
					switch ( $cs_type ) {
						case('text'):
							$cs_label = isset($cus_field['label']) ? $cus_field['label'] : '';
							$cs_meta_key = $cus_field['meta_key'];
							$cs_default_val = isset($cus_field['default_value']) ? $cus_field['default_value'] : '';
							$cs_required = isset($cus_field['required']) && $cus_field['required'] == 'yes' ? ' required' : '';
							$cs_help_txt = isset($cus_field['help']) ? $cus_field['help'] : '';
							if ( $cs_user_id != '' ) {
								$cs_default_val = get_user_meta((int) $cs_user_id, "$cs_meta_key", true);
							}
							$cs_html .= '
                                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                            <label>' . esc_attr($cs_label) . '</label>';
							$cs_opt_array = array(
								'name' => isset($cus_field['label']) ? $cus_field['label'] : '',
								'desc' => '',
								'classes' => 'form-control',
								'hint_text' => isset($cus_field['help']) ? $cus_field['help'] : '',
								'std' => isset($cs_default_val) ? $cs_default_val : '',
								'id' => isset($cus_field['meta_key']) ? $cus_field['meta_key'] : '',
								'cus_field' => true,
								'return' => true,
							);

							if ( isset($cus_field['placeholder']) && $cus_field['placeholder'] != '' ) {
								$cs_opt_array['extra_atr'] = ' placeholder="' . $cus_field['placeholder'] . '"';
							}

							if ( isset($cus_field['required']) && $cus_field['required'] == 'yes' ) {
								$cs_opt_array['required'] = 'yes';
							}

							$cs_html .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

							if ( $cs_help_txt <> '' ) {
								$cs_html .= '<span class="cs-caption">' . $cs_help_txt . '</span>';
							}
							$cs_html .= '	
                                        </div>';
							break;
						case('textarea'):
							$cs_label = isset($cus_field['label']) ? $cus_field['label'] : '';
							$cs_rows = isset($cus_field['rows']) ? $cus_field['rows'] : '';
							$cs_cols = isset($cus_field['cols']) ? $cus_field['cols'] : '';
							$cs_meta_key = $cus_field['meta_key'];
							$cs_default_val = isset($cus_field['default_value']) ? $cus_field['default_value'] : '';
							$cs_required = isset($cus_field['required']) && $cus_field['required'] == 'yes' ? ' required' : '';
							$cs_help_txt = isset($cus_field['help']) ? $cus_field['help'] : '';
							if ( $cs_user_id != '' ) {
								$cs_default_val = get_user_meta((int) $cs_user_id, "$cs_meta_key", true);
							}
							$cs_html .= '
                                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                            <label>' . esc_attr($cs_label) . '</label>';

							$cs_opt_array = array(
								'name' => isset($cus_field['label']) ? $cus_field['label'] : '',
								'desc' => '',
								'classes' => 'form-control',
								'extra_atr' => 'rows="' . $cs_rows . '" cols="' . $cs_cols . '"',
								'hint_text' => isset($cus_field['help']) ? $cus_field['help'] : '',
								'std' => isset($cs_default_val) ? $cs_default_val : '',
								'id' => isset($cus_field['meta_key']) ? $cus_field['meta_key'] : '',
								'cus_field' => true,
								'return' => true,
							);

							if ( isset($cus_field['required']) && $cus_field['required'] == 'yes' ) {
								$cs_opt_array['required'] = 'yes';
							}

							$cs_html .= $cs_form_fields2->cs_form_textarea_render($cs_opt_array);


							if ( $cs_help_txt <> '' ) {
								$cs_html .= '<span class="cs-caption">' . $cs_help_txt . '</span>';
							}
							$cs_html .= '	
                                        </div>';
							break;
						case('dropdown'):
							$cs_label = isset($cus_field['label']) ? $cus_field['label'] : '';
							$cs_meta_key = $cus_field['meta_key'];
							$cs_default_val = isset($cus_field['default_value']) ? $cus_field['default_value'] : '';
							$cs_required = isset($cus_field['required']) && $cus_field['required'] == 'yes' ? ' required' : '';
							$cs_help_txt = isset($cus_field['help']) ? $cus_field['help'] : '';

							if ( $cs_user_id != '' ) {
								$cs_default_val = get_user_meta((int) $cs_user_id, "$cs_meta_key", true);
							}
							$cs_dr_name = ' name="cs_cus_field[' . sanitize_html_class($cs_meta_key) . ']"';
							$cs_dr_mult = '';
							if ( isset($cus_field['post_multi']) && $cus_field['post_multi'] == 'yes' ) {
								$cs_dr_name = ' name="cs_cus_field[' . sanitize_html_class($cs_meta_key) . '][]"';
								$cs_dr_mult = ' multiple="multiple"';
							}

							$a_options = array();

							if ( is_array($cus_field['options']['value']) ) {
								$a_options = array_merge(array( '' => $cus_field['first_value'] ), $cus_field['options']['value']);
							}
							$cs_options = '';
							if ( isset($cus_field['first_value']) ) {
								$cs_options[''] = $cus_field['first_value'];
							}
							if ( isset($cus_field['options']) ) {
								$loop_counter = isset($cus_field['options']['value']) && is_array($cus_field['options']['value']) ? count($cus_field['options']['value']) : '0';
								$i = 0;
								if ( $loop_counter > 0 ) {
									while ( $loop_counter > $i ) {
										$cs_options[$cus_field['options']['value'][$i]] = $cus_field['options']['label'][$i];
										$i ++;
									}
								}
							}
							$a_options = $cs_html .= '
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                        <label>' . esc_attr($cs_label) . '</label>
                                        <div class="select-holder">';
							$cs_opt_array = array(
								'name' => isset($cus_field['label']) ? $cus_field['label'] : '',
								'desc' => '',
								'classes' => 'chosen-select form-control',
								'hint_text' => isset($cus_field['help']) ? $cus_field['help'] : '',
								'std' => isset($cs_default_val) ? $cs_default_val : '',
								'id' => isset($cus_field['meta_key']) ? $cus_field['meta_key'] : '',
								'options' => $cs_options,
								'cus_field' => true,
								'return' => true,
							);

							if ( isset($cus_field['required']) && $cus_field['required'] == 'yes' ) {
								$cs_opt_array['required'] = 'yes';
							}

							if ( isset($cus_field['first_value']) && $cus_field['first_value'] != '' ) {
								$cs_opt_array['extra_atr'] = ' data-placeholder="' . $cus_field['first_value'] . '"';
							}

							if ( isset($cus_field['post_multi']) && $cus_field['post_multi'] == 'yes' ) {
								$cs_html .= $cs_form_fields2->cs_form_multiselect_render($cs_opt_array);
							} else {
								$cs_html .= $cs_form_fields2->cs_form_select_render($cs_opt_array);
							}

							if ( $cs_help_txt <> '' ) {
								$cs_html .= '<span class="cs-caption">' . $cs_help_txt . '</span>';
							}
							$cs_html .= '	
								</div>
							</div>';
							break;
						case('date'):
							$cs_label = isset($cus_field['label']) ? $cus_field['label'] : '';
							$cs_meta_key = $cus_field['meta_key'];
							$cs_default_val = isset($cus_field['default_value']) ? $cus_field['default_value'] : '';
							$cs_required = isset($cus_field['required']) && $cus_field['required'] == 'yes' ? ' required' : '';
							$cs_format = isset($cus_field['date_format']) ? $cus_field['date_format'] : 'd-m-Y';
							$cs_help_txt = isset($cus_field['help']) ? $cus_field['help'] : '';
							if ( $cs_user_id != '' ) {
								$cs_default_val = get_user_meta((int) $cs_user_id, "$cs_meta_key", true);
							}

							$cs_html .= '
							<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
								<label>' . esc_attr($cs_label) . '</label>';

							$cs_opt_array = array(
								'name' => isset($cus_field['label']) ? $cus_field['label'] : '',
								'desc' => '',
								'classes' => 'form-control',
								'hint_text' => isset($cus_field['help']) ? $cus_field['help'] : '',
								'std' => isset($cs_default_val) ? $cs_default_val : '',
								'id' => isset($cus_field['meta_key']) ? $cus_field['meta_key'] : '',
								'cus_field' => true,
								'format' => $cs_format,
								'return' => true,
							);

							if ( isset($cus_field['placeholder']) && $cus_field['placeholder'] != '' ) {
								$cs_opt_array['extra_atr'] = ' placeholder="' . $cus_field['placeholder'] . '"';
							}

							if ( isset($cus_field['required']) && $cus_field['required'] == 'yes' ) {
								$cs_opt_array['required'] = 'yes';
							}

							$cs_html .= $cs_form_fields2->cs_form_date_render($cs_opt_array);

							if ( $cs_help_txt <> '' ) {
								$cs_html .= '<span class="cs-caption">' . $cs_help_txt . '</span>';
							}
							$cs_html .= '	
					</div>';
							break;
						case('email'):
							$cs_label = isset($cus_field['label']) ? $cus_field['label'] : '';
							$cs_meta_key = $cus_field['meta_key'];
							$cs_default_val = isset($cus_field['default_value']) ? $cus_field['default_value'] : '';
							$cs_required = isset($cus_field['required']) && $cus_field['required'] == 'yes' ? ' required' : '';
							$cs_help_txt = isset($cus_field['help']) ? $cus_field['help'] : '';
							if ( $cs_user_id != '' ) {
								$cs_default_val = get_user_meta((int) $cs_user_id, "$cs_meta_key", true);
							}
							$cs_html .= '
                                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                            <label>' . esc_attr($cs_label) . '</label>';
							$cs_opt_array = array(
								'name' => isset($cus_field['label']) ? $cus_field['label'] : '',
								'desc' => '',
								'classes' => 'form-control',
								'hint_text' => isset($cus_field['help']) ? $cus_field['help'] : '',
								'std' => isset($cs_default_val) ? $cs_default_val : '',
								'id' => isset($cus_field['meta_key']) ? $cus_field['meta_key'] : '',
								'cus_field' => true,
								'return' => true,
							);

							if ( isset($cus_field['placeholder']) && $cus_field['placeholder'] != '' ) {
								$cs_opt_array['extra_atr'] = ' placeholder="' . $cus_field['placeholder'] . '"';
							}

							if ( isset($cus_field['required']) && $cus_field['required'] == 'yes' ) {
								$cs_opt_array['required'] = 'yes';
							}

							$cs_html .= $cs_form_fields2->cs_form_text_render($cs_opt_array);
							if ( $cs_help_txt <> '' ) {
								$cs_html .= '<span class="cs-caption">' . $cs_help_txt . '</span>';
							}
							$cs_html .= '	
					</div>';
							break;
						case('url'):
							$cs_label = isset($cus_field['label']) ? $cus_field['label'] : '';
							$cs_meta_key = $cus_field['meta_key'];
							$cs_default_val = isset($cus_field['default_value']) ? $cus_field['default_value'] : '';
							$cs_required = isset($cus_field['required']) && $cus_field['required'] == 'yes' ? ' required' : '';
							$cs_help_txt = isset($cus_field['help']) ? $cus_field['help'] : '';
							if ( $cs_user_id != '' ) {
								$cs_default_val = get_user_meta((int) $cs_user_id, "$cs_meta_key", true);
							}
							$cs_html .= '
                                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                            <label>' . esc_attr($cs_label) . '</label>';

							$cs_opt_array = array(
								'name' => isset($cus_field['label']) ? $cus_field['label'] : '',
								'desc' => '',
								'classes' => 'form-control',
								'hint_text' => isset($cus_field['help']) ? $cus_field['help'] : '',
								'std' => isset($cs_default_val) ? $cs_default_val : '',
								'id' => isset($cus_field['meta_key']) ? $cus_field['meta_key'] : '',
								'cus_field' => true,
								'return' => true,
							);

							if ( isset($cus_field['placeholder']) && $cus_field['placeholder'] != '' ) {
								$cs_opt_array['extra_atr'] = ' placeholder="' . $cus_field['placeholder'] . '"';
							}

							if ( isset($cus_field['required']) && $cus_field['required'] == 'yes' ) {
								$cs_opt_array['required'] = 'yes';
							}

							$cs_html .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

							if ( $cs_help_txt <> '' ) {
								$cs_html .= '<span class="cs-caption">' . $cs_help_txt . '</span>';
							}
							$cs_html .= '	
					</div>';
							break;
						case('range'):
							$cs_label = isset($cus_field['label']) ? $cus_field['label'] : '';
							$cs_meta_key = $cus_field['meta_key'];
							$cs_default_val = isset($cus_field['default_value']) ? $cus_field['default_value'] : '';
							$cs_required = isset($cus_field['required']) && $cus_field['required'] == 'yes' ? ' required' : '';
							$cs_help_txt = isset($cus_field['help']) ? $cus_field['help'] : '';
							if ( $cs_user_id != '' ) {
								$cs_default_val = get_user_meta((int) $cs_user_id, "$cs_meta_key", true);
							}

							$cs_html .= '
                                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                            <label>' . esc_attr($cs_label) . '</label>';

							$cs_opt_array = array(
								'name' => isset($cus_field['label']) ? $cus_field['label'] : '',
								'desc' => '',
								'classes' => 'form-control',
								'hint_text' => isset($cus_field['help']) ? $cus_field['help'] : '',
								'std' => isset($cs_default_val) ? $cs_default_val : '',
								'id' => isset($cus_field['meta_key']) ? $cus_field['meta_key'] : '',
								'cus_field' => true,
								'return' => true,
							);

							if ( isset($cus_field['required']) && $cus_field['required'] == 'yes' ) {
								$cs_opt_array['required'] = 'yes';
							}

							if ( isset($cus_field['placeholder']) && $cus_field['placeholder'] != '' ) {
								$cs_opt_array['extra_atr'] = ' placeholder="' . $cus_field['placeholder'] . '"';
							}

							$cs_html .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

							if ( $cs_help_txt <> '' ) {
								$cs_html .= '<span class="cs-caption">' . $cs_help_txt . '</span>';
							}
							$cs_html .= '	
                                        </div>';
							break;
					}
				}
			}
			return $cs_html;
		}

		/**
		 * End Function for how to find employer custom fields
		 */

		/**
		 * Start Function for how Save form with the Help of Ajax
		 */
		function ajax_employer_form_save() {
			global $post, $current_user, $reset_date, $cs_options;
			if ( isset($_POST['cs_user']) && $_POST['cs_user'] <> '' ) {
				$user_id = $_POST['cs_user'];
				// check demo user check
				$get_demouser_info = get_user_by('id', $user_id);
				if ( isset($get_demouser_info->user_login) && $get_demouser_info->user_login == 'jobcareer-employer' ) {
					echo __("You don't have access to update in demo mode.", "jobhunt");
					die();
				}
				if ( ! current_user_can('edit_user', $user_id) ) {
					return false;
				}

				if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
					return;
				}

				$data = array();
				// update email
				if ( isset($_POST['user_email']) ) {
					$email_response = wp_update_user(array( 'ID' => $user_id, 'user_email' => $_POST['user_email'] ));
					if ( isset($email_response->errors) ) {
						echo __('Your given email address is already used or invalid, Please try with correct information', 'jobhunt');
						die();
					}
				}
				// update display name
				if ( isset($_POST['display_name']) ) {
					wp_update_user(array( 'ID' => $user_id, 'display_name' => $_POST['display_name'] ));
					$user_name = sanitize_title($_POST['display_name']);
					wp_update_user(array( 'ID' => $user_id, 'user_nicename' => $user_name ));
				}
				// update website url
				if ( isset($_POST['user_url']) ) {
					wp_update_user(array( 'ID' => $user_id, 'user_url' => $_POST['user_url'] ));
				}
				// update first name
				if ( isset($_POST['first_name']) ) {
					wp_update_user(array( 'ID' => $user_id, 'first_name' => $_POST['first_name'] ));
				}
				// update last name
				if ( isset($_POST['last_name']) ) {
					wp_update_user(array( 'ID' => $user_id, 'last_name' => $_POST['last_name'] ));
				}

				// description
				if ( isset($_POST['comp_detail']) ) {
					wp_update_user(array( 'ID' => $user_id, 'description' => $_POST['comp_detail'] ));
				}

				foreach ( $_POST as $key => $value ) {
					if ( strstr($key, 'cs_') ) {
						if ( $key == 'cs_transaction_expiry_date' || $key == 'cs_job_expired' || $key == 'cs_job_posted' || $key == 'cs_user_last_activity_date' ) {
							if ( $value == '' || $key == 'cs_user_last_activity_date' ) {
								$value = date('d-m-Y H:i:s');
							}
							$data[$key] = strtotime($value);
							update_user_meta($user_id, $key, strtotime($value));
						} else {
							if ( $key == 'cs_cus_field' ) {
								if ( is_array($value) && sizeof($value) > 0 ) {
									foreach ( $value as $c_key => $c_val ) {
										update_user_meta($user_id, $c_key, $c_val);
									}
								}
							} else {
								$data[$key] = $value;
								update_user_meta($user_id, $key, $value);
							}
						}
					}
				}

				update_user_meta($user_id, 'cs_array_data', $data);

				$cs_media_image = cs_user_avatar();

				if ( $cs_media_image == '' ) {
					$cs_media_image = $_POST['cs_employer_img'];
				} else {
					$cs_prev_img = get_user_meta($current_user->ID, 'user_img', true);
					cs_remove_img_url($cs_prev_img);
				}
				update_user_meta($current_user->ID, 'user_img', $cs_media_image);

				$cover_media_upload = cs_user_avatar('cover_media_upload');
				if ( $cover_media_upload == '' ) {
					$cover_media_upload = $_POST['cs_cover_employer_img'];
				} else {
					$cs_cover_prev_img = get_user_meta($current_user->ID, 'cover_user_img', true);
					cs_remove_img_url($cs_cover_prev_img);
				}
				update_user_meta($current_user->ID, 'cover_user_img', $cover_media_upload);

				echo __('Update Successfully', 'jobhunt');
			} else {
				echo __('Save Failed', 'jobhunt');
			}
			die();
		}

		/**
		 * End Function for how Save form with the Help of Ajax
		 */
	}

	$cs_emp_functions = new cs_employer_functions();
}
