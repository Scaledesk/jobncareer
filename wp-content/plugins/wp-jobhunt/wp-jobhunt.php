<?php

/*
  Plugin Name: WP JobHunt
  Plugin URI: http://themeforest.net/user/Chimpstudio/
  Description: JobHunt
  Version: 1.5
  Author: ChimpStudio
  Text Domain: jobhunt
  Author URI: http://themeforest.net/user/Chimpstudio/
  License: GPL2
  Copyright 2015  chimpgroup  (email : info@chimpstudio.co.uk)
  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.
  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.
  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, United Kingdom
 */
if ( ! class_exists( 'wp_jobhunt' ) ) {

    class wp_jobhunt {

        public $plugin_url;
        public $plugin_dir;

        // public $plugin_user_images_directory;

        /**
         * Start Function of Construct
         */
        public function __construct() {

            add_action( 'init', array( $this, 'load_plugin_textdomain' ), 0 );

            remove_filter( 'pre_user_description', 'wp_filter_kses' );
            add_filter( 'pre_user_description', 'wp_filter_post_kses' );

            // Add optinos in Email Template Settings
            add_filter( 'jobhunt_email_template_settings', array( $this, 'email_template_settings_callback' ), 0, 1 );

            $this->define_constants();
            $this->includes();
        }

        /**
         * Start Function how to Create WC Contants
         */
        private function define_constants() {

            global $post, $wp_query, $cs_plugin_options, $current_user, $cs_jh_scodes, $plugin_user_images_directory;
            $cs_plugin_options = get_option( 'cs_plugin_options' );
            $this->plugin_url = plugin_dir_url( __FILE__ );
            $this->plugin_dir = plugin_dir_path( __FILE__ );
            $plugin_user_images_directory = 'wp-jobhunt-users';
        }

        /**
         * End Function how to Create WC Contants
         */

        /**
         * Start Function how to add core files used in admin and theme
         */
        public function includes() {
            // Addons Manager.
            require_once 'classes/class-addons-manager.php';
            // Email Templates.
            require_once 'classes/email-templates/class-register-template.php';
            require_once 'classes/email-templates/class-reset-password-template.php';
            require_once 'classes/email-templates/class-job-add-template.php';
            require_once 'classes/email-templates/class-employer-contact-candidate-email-template.php';
            require_once 'classes/email-templates/class-candidate-contact-employer-email-template.php';
            require_once 'classes/email-templates/class-new-user-notification-site-owner-template.php';
            require_once 'classes/email-templates/class-job-apply-successfully.php';
            require_once 'classes/email-templates/class-job-applied-employer-notification.php';
            require_once 'classes/email-templates/class-job-update-email-template.php';
            require_once 'classes/email-templates/class-job-approved-email-template.php';
            require_once 'classes/email-templates/class-job-not-approved-email-template.php';
            require_once 'classes/email-templates/class-job-waiting-email-template.php';
            require_once 'classes/email-templates/class-job-delete-template.php';
            require_once 'classes/email-templates/class-approved-employer-profile-template.php';
            require_once 'classes/email-templates/class-not-approved-employer-profile-template.php';
            require_once 'classes/email-templates/class-employer-register-template.php';
            require_once 'classes/email-templates/class-candidate-register-template.php';

            require_once 'admin/classes/class-save-post-options.php';
            require_once 'templates/elements/shortcode_functions.php';
            require_once 'admin/include/form-fields/cs_form_fields.php';
            require_once 'admin/include/form-fields/cs_html_fields.php';
            require_once 'classes/class_transactions.php';
            require_once 'include/form-fields/form-fields-frontend.php';
            require_once 'include/form-fields/cs_html_fields_frontend.php';
            require_once 'helpers/notification-helper.php';
            require_once 'admin/settings/plugin_settings.php';
            require_once 'admin/settings/includes/plugin_options.php';
            require_once 'admin/settings/includes/plugin_options_fields.php';
            require_once 'admin/settings/includes/plugin_options_functions.php';
            require_once 'admin/settings/includes/plugin_options_array.php';
            require_once 'admin/settings/user-import/cs_import.php';
            require_once 'admin/include/post-types/jobs/job_custom_fields.php';
            require_once 'admin/include/post-types/candidate/candidate_custom_fields.php';
            require_once 'admin/include/post-types/employer/employer_custom_fields.php';
            // importer hooks
            require_once 'admin/include/importer_hooks.php';

            require_once 'classes/class_dashboards_templates.php';
            require_once 'templates/dashboards/candidate/templates_functions.php';
            require_once 'templates/dashboards/candidate/templates_ajax_functions.php';
            require_once 'templates/dashboards/employer/employer_functions.php';
            require_once 'templates/dashboards/employer/employer_templates.php';
            require_once 'templates/dashboards/employer/employer_ajax_templates.php';
            require_once 'payments/class-payments.php';
            require_once 'payments/custom-wooc-hooks.php';
            require_once 'payments/config.php';
            require_once 'admin/include/post-types/jobs/jobs.php';
            // move at user meta
            require_once 'admin/include/post-types/transaction/transaction.php';
            require_once 'admin/include/post-types/transaction/transactions_meta.php';
            require_once 'admin/include/post-types/jobs/jobs_meta.php';
            // user meta
            require_once 'admin/include/user-meta/cs_meta.php';
            require_once 'widgets/widgets.php';
            // Cv Package Files
            require_once 'templates/packages/cv/cv_package_elements.php';
            require_once 'templates/packages/cv/cv_package_functions.php';
            // Job Package Files
            require_once 'templates/packages/jobs/job_package_elements.php';
            require_once 'templates/packages/jobs/job_package_functions.php';
            // Job Post Files
            require_once 'templates/elements/job-post/job-post-elements.php';
            require_once 'templates/elements/job-post/job-post-functions.php';
            // Job specialisms Files
            require_once 'templates/elements/specialisms/elements.php';
            require_once 'templates/elements/specialisms/functions.php';
            require_once 'templates/functions.php';
            // employer element   
            require_once 'templates/listings/employer/employer_element.php';
            // Job element   
            require_once 'templates/listings/jobs/jobs_element.php';
            // Job search
            require_once 'templates/elements/jobs-search/jobs-search-element.php';
            // Candidate  
            require_once 'templates/listings/candidate/candidate_element.php';
            // for employer listing
            require_once 'templates/listings/employer/employer_template.php';
            // for job sesker listing
            require_once 'templates/listings/candidate/candidate_template.php';
            // for jobs listing
            require_once 'templates/listings/jobs/jobs_template.php';
            require_once 'templates/elements/jobs-search/jobs-search-template.php';
            // for login
            require_once 'templates/elements/login/login_functions.php';
            require_once 'templates/elements/login/login_forms.php';
            require_once 'templates/elements/login/shortcodes.php';
            require_once 'templates/elements/login/cs-social-login/cs_social_login.php';
            require_once 'templates/elements/login/cs-social-login/google/cs_google_connect.php';
            // linkedin login
            // recaptchas
            require_once 'templates/elements/login/recaptcha/autoload.php';
            // Location Checker
            require_once 'classes/class_location_check.php';
            add_filter( 'template_include', array( &$this, 'cs_single_template' ) );
            add_action( 'admin_enqueue_scripts', array( &$this, 'cs_defaultfiles_plugin_enqueue' ), 2 );
            add_action( 'wp_enqueue_scripts', array( &$this, 'cs_defaultfiles_plugin_enqueue' ), 2 );
            add_action( 'wp_enqueue_scripts', array( &$this, 'cs_enqueue_responsive_front_scripts' ), 3 );


            add_action( 'admin_init', array( $this, 'cs_all_scodes' ) );
            add_filter( 'body_class', array( $this, 'cs_boby_class_names' ) );
        }

        /**
         * End Function how to add core files used in admin and theme
         */

        /**
         * Start Function how to add Specific CSS Classes by filter
         */
        function cs_boby_class_names( $classes ) {
            $classes[] = 'wp-jobhunt';
            return $classes;
        }

        /**
         * End Function how to add Specific CSS Classes by filter
         */

        /**
         * Start Function how to access admin panel
         */
        public function prevent_admin_access() {
            if ( is_user_logged_in() ) {

                if ( strpos( strtolower( $_SERVER['REQUEST_URI'] ), '/wp-admin' ) !== false && (current_user_can( 'cs_employer' ) || current_user_can( 'cs_candidate' )) ) {
                    wp_redirect( get_option( 'siteurl' ) );
                    add_filter( 'show_admin_bar', '__return_false' );
                }
            }
        }
		
	

        /**
         * Start Function how to Add textdomain for translation
         */
        public function load_plugin_textdomain() {
            global $cs_plugin_options;
			
			$settings_language_file = isset($cs_plugin_options['cs_language_file']) ? $cs_plugin_options['cs_language_file'] : '';
	
            if ( session_id() == '' ) {
                session_start();
            }
	

            if ( function_exists( 'icl_object_id' ) ) {

                global $sitepress, $wp_filesystem;

                require_once ABSPATH . '/wp-admin/includes/file.php';

                $backup_url = '';

                if ( false === ($creds = request_filesystem_credentials( $backup_url, '', false, false, array() ) ) ) {

                    return true;
                }

                if ( ! WP_Filesystem( $creds ) ) {
                    request_filesystem_credentials( $backup_url, '', true, false, array() );
                    return true;
                }

                $cs_languages_dir = plugin_dir_path( __FILE__ ) . 'languages/';

                $cs_all_langs = $wp_filesystem->dirlist( $cs_languages_dir );

                $cs_mo_files = array();
                if ( is_array( $cs_all_langs ) && sizeof( $cs_all_langs ) > 0 ) {

                    foreach ( $cs_all_langs as $file_key => $file_val ) {

                        if ( isset( $file_val['name'] ) ) {

                            $cs_file_name = $file_val['name'];

                            $cs_ext = pathinfo( $cs_file_name, PATHINFO_EXTENSION );

                            if ( $cs_ext == 'mo' ) {
                                $cs_mo_files[] = $cs_file_name;
                            }
                        }
                    }
                }

                $cs_active_langs = $sitepress->get_current_language();

                foreach ( $cs_mo_files as $mo_file ) {
                    if ( strpos( $mo_file, $cs_active_langs ) !== false ) {
                        $cs_lang_mo_file = $mo_file;
                    }
                }
            }

            $locale = apply_filters( 'plugin_locale', get_locale(), 'jobhunt' );
            $dir = trailingslashit( WP_LANG_DIR );
            if ( isset( $cs_lang_mo_file ) && $cs_lang_mo_file != '' ) {
                load_textdomain( 'jobhunt', plugin_dir_path( __FILE__ ) . "languages/" . $cs_lang_mo_file );
            } else if ( $settings_language_file != '' ) {
                load_textdomain( 'jobhunt', plugin_dir_path( __FILE__ ) . "languages/" . $settings_language_file );
            } else {
                load_textdomain( 'jobhunt', plugin_dir_path( __FILE__ ) . "languages/jobhunt-" . $locale . '.mo' );
            }
        }

        /**
         * Fetch and return version of the current plugin
         *
         * @return	string	version of this plugin
         */
        public static function get_plugin_version() {
            $plugin_data = get_plugin_data( __FILE__ );
            return $plugin_data['Version'];
        }

        /**
         * Start Function how to Add User and custom Roles
         */
        public function cs_add_custom_role() {
            add_role( 'guest', 'Guest', array(
                'read' => true, // True allows that capability
                'edit_posts' => true,
                'delete_posts' => false, // Use false to explicitly deny
            ) );
        }

        /**
         * End Function how to Add User and custom Roles
         */

        /**
         * Start Function how to Add plugin urls
         */
        public static function plugin_url() {
            return plugin_dir_url( __FILE__ );
        }

        /**
         * End Function how to Add plugin urls
         */

        /**
         * Start Function how to Add image url for plugin directory
         */
        public static function plugin_img_url() {
            return plugin_dir_url( __FILE__ );
        }

        /**
         * End Function how to Add image url for plugin directory
         */

        /**
         * Start Function how to Create plugin Directory
         */
        public static function plugin_dir() {
            return plugin_dir_path( __FILE__ );
        }

        /**
         * End Function how to Create plugin Directory
         */

        /**
         * Start Function how to Activate the plugin
         */
        public static function activate() {
            global $plugin_user_images_directory;
            add_option( 'cs_jobhunt_plugin_activation', 'installed' );
            add_option( 'cs_jobhunt', '1' );
            // create user role for candidate and employer
            $result = add_role(
                    'cs_employer', __( 'Employer', 'jobhunt' ), array(
                'read' => false,
                'edit_posts' => false,
                'delete_posts' => false,
                    )
            );
            $result = add_role(
                    'cs_candidate', __( 'Candidate', 'jobhunt' ), array(
                'read' => false,
                'edit_posts' => false,
                'delete_posts' => false,
                    )
            );
            // create users images directory 
            $upload = wp_upload_dir();
            $upload_dir = $upload['basedir'];
            $upload_dir = $upload_dir . '/' . $plugin_user_images_directory;
            if ( ! is_dir( $upload_dir ) ) {
                mkdir( $upload_dir, 0777 );
            }
        }

        /**
         * Start Function how to DeActivate the plugin
         */
        static function deactivate() {
            delete_option( 'cs_jobhunt_plugin_activation' );
            delete_option( 'cs_jobhunt', false );
        }

        /**
         * Start Function how to Add Theme Templates
         */
        public function cs_single_template( $single_template ) {
            global $post;

            if ( get_post_type() == 'candidate' ) {
                if ( is_single() ) {
                    $single_template = plugin_dir_path( __FILE__ ) . 'templates/single_pages/single-candidate.php';
                }
            }
            if ( get_post_type() == 'employer' ) {
                if ( is_single() ) {
                    $single_template = plugin_dir_path( __FILE__ ) . 'templates/single_pages/single-employer.php';
                }
            }
            if ( get_post_type() == 'jobs' ) {
                if ( is_single() ) {
                    $single_template = plugin_dir_path( __FILE__ ) . 'templates/single_pages/single-jobs.php';
                }
            }
            return $single_template;
        }

        /**
         * Custom Css 
         */
        public function cs_custom_inline_styles_method() {

            $cs_plugin_options = get_option( 'cs_plugin_options' );
            wp_enqueue_style( 'custom-style-inline', plugins_url( '/assets/css/custom_script.css', __FILE__ ) );
            $cs_custom_css = isset( $cs_plugin_options['cs_style-custom-css'] ) ? $cs_plugin_options['cs_style-custom-css'] : 'sdfdsa';
            $custom_css = $cs_custom_css;
            wp_add_inline_style( 'custom-style-inline', $custom_css );
        }

        /**
         * Start Function how to Includes Default Scripts and Styles
         */
        public function cs_defaultfiles_plugin_enqueue() {
			global $cs_plugin_options;
            if ( is_admin() ) {
                wp_enqueue_media();
            }
            if ( ! is_admin() ) {
                wp_enqueue_style( 'cs_iconmoon_css', plugins_url( '/assets/icomoon/css/iconmoon.css', __FILE__ ) );
                wp_enqueue_style( 'cs_bootstrap_css', plugins_url( '/assets/css/bootstrap.css', __FILE__ ) );
                wp_enqueue_style( 'jobcareer_widgets_css', plugins_url( '/assets/css/widget.css', __FILE__ ) );
                $cs_plugin_options = get_option( 'cs_plugin_options' );
                $cs_default_css_option = isset( $cs_plugin_options['cs_common-elements-style'] ) ? $cs_plugin_options['cs_common-elements-style'] : '';
                //Common css Elements
                if ( $cs_default_css_option == 'on' ) {
                    wp_enqueue_style( 'cs_jobhunt_plugin_defalut_elements_css', plugins_url( '/assets/css/default-elements.css', __FILE__ ) );
                }
                wp_enqueue_style( 'cs_jobhunt_plugin_css', plugins_url( '/assets/css/cs-jobhunt-plugin.css', __FILE__ ) );

                wp_enqueue_script( 'cs_waypoints_min_js', plugins_url( '/assets/scripts/waypoints.min.js', __FILE__ ), '', '', true ); //?
            }
            wp_enqueue_script( 'job-editor-script', plugins_url( '/assets/scripts/jquery-te-1.4.0.min.js', __FILE__ ) );
            wp_enqueue_style( 'job-editor-style', plugins_url( '/assets/css/jquery-te-1.4.0.css', __FILE__ ) );
            wp_enqueue_style( 'cs_slicknav_css', plugins_url( '/assets/css/slicknav.css', __FILE__ ) );
            wp_enqueue_style( 'cs_datetimepicker_css', plugins_url( '/assets/css/jquery_datetimepicker.css', __FILE__ ) );
            wp_enqueue_style( 'cs_bootstrap_slider_css', plugins_url( '/assets/css/bootstrap-slider.css', __FILE__ ) );
            wp_enqueue_script( 'cs_bootstrap_slider_js', plugins_url( '/assets/scripts/bootstrap-slider.js', __FILE__ ), '', '', true );
            wp_enqueue_style( 'cs_chosen_css', plugins_url( '/assets/css/chosen.css', __FILE__ ) );


            // All JS files
            // wp_enqueue_script(array('jquery'));
            // temporary off
            wp_enqueue_script( 'cs_bootstrap_min_js', plugins_url( '/assets/scripts/bootstrap.min.js', __FILE__ ), array( 'jquery' ), '', true );
			$google_api_key = '?libraries=places';
			if ( isset( $cs_plugin_options['cs_google_api_key'] ) && $cs_plugin_options['cs_google_api_key'] != '' ) {
				$google_api_key = '?key=' . $cs_plugin_options['cs_google_api_key'] . '&libraries=places';
			}
            wp_enqueue_script( 'cs_google_autocomplete_script', 'https://maps.googleapis.com/maps/api/js' . $google_api_key );
            wp_enqueue_script( 'cs_map_info_js', plugins_url( '/assets/scripts/map_infobox.js', __FILE__ ), '', '', true );

            wp_enqueue_script( 'cs_my_upload_js', '', array( 'jquery', 'media-upload', 'thickbox', 'jquery-ui-droppable', 'jquery-ui-datepicker', 'jquery-ui-slider', 'wp-color-picker' ) );
            wp_enqueue_script( 'cs_chosen_jquery_js', plugins_url( '/assets/scripts/chosen.jquery.js', __FILE__ ), '', '', true );
            wp_enqueue_script( 'cs_scripts_js', plugins_url( '/assets/scripts/scripts.js', __FILE__ ), '', '', true );
            wp_enqueue_script( 'cs_isotope_min_js', plugins_url( '/assets/scripts/isotope.min.js', __FILE__ ), '', '', true ); //?
            wp_enqueue_script( 'cs_modernizr_min_js', plugins_url( '/assets/scripts/modernizr.min.js', __FILE__ ), '', '', '' );
            wp_enqueue_script( 'cs_browser_detect_js', plugins_url( '/assets/scripts/browser-detect.js', __FILE__ ), '', '', '' );
            wp_enqueue_script( 'cs_slick_js', plugins_url( '/assets/scripts/slick.js', __FILE__ ), '', '', true );
            wp_enqueue_script( 'cs_jquery_sticky_js', plugins_url( '/assets/scripts/jquery.sticky.js', __FILE__ ), '', '', true ); //?
            wp_enqueue_script( 'cs_jobhunt_functions_js', plugins_url( '/assets/scripts/jobhunt_functions.js', __FILE__ ), '', '', true );
            wp_enqueue_script( 'cs_exra_functions_js', plugins_url( '/assets/scripts/extra_functions.js', __FILE__ ), '', '', true );

            if ( ! is_admin() ) {
                wp_enqueue_script( 'cs_functions_js', plugins_url( '/assets/scripts/functions.js', __FILE__ ), '', '', true );
            }
             wp_enqueue_script('cs_datetimepicker_js', plugins_url('/assets/scripts/jquery_datetimepicker.js', __FILE__), '', '', true);

            if ( ! wp_is_mobile() ) {
                /* include only when not a mobile device */
                wp_enqueue_script( 'cs_custom_resolution_js', plugins_url( '/assets/scripts/custom-resolution.js', __FILE__ ), '', '', true );
            }

            /**
             *
             * @login popup script files
             */
            if ( ! function_exists( 'cs_range_slider_scripts' ) ) {

                function cs_range_slider_scripts() {
                    
                }

            }
            /**
             *
             * @login popup script files
             */
            if ( ! function_exists( 'cs_google_recaptcha_scripts' ) ) {

                function cs_google_recaptcha_scripts() {
                    wp_enqueue_script( 'cs_google_recaptcha_scripts', cs_server_protocol() . 'www.google.com/recaptcha/api.js?onload=cs_multicap_all_functions&amp;render=explicit', '', '' );
                }

            }
            /**
             *
             * @login popup script files
             */
            if ( ! function_exists( 'cs_login_box_popup_scripts' ) ) {

                function cs_login_box_popup_scripts() {
                    wp_enqueue_script( 'cs_uiMorphingButton_fixed_js', plugins_url( '/assets/scripts/uiMorphingButton_fixed.js', __FILE__ ), '', '', true );
                }

            }
            /**
             *
             * @datetime calender script files
             */
            if ( ! function_exists( 'cs_datetime_picker_scripts' ) ) {

                function cs_datetime_picker_scripts() {
                    
                }

            }
            /**
             *
             * @sidemenue effect script files
             */
            if ( ! function_exists( 'cs_visualnav_sidemenu' ) ) {

                function cs_visualnav_sidemenu() {
                    wp_enqueue_script( 'cs_jquery_easing_js', plugins_url( '/assets/scripts/jquery.easing.1.2.js', __FILE__ ), '', '', true );
                    wp_enqueue_script( 'cs_jquery_visualNav_js', plugins_url( '/assets/scripts/jquery.visualNav.js', __FILE__ ), '', '', true );
                    wp_enqueue_script( 'cs_jquery_smint_js', plugins_url( '/assets/scripts/jquery.smint.js', __FILE__ ), '', '', true ); //?
                }

            }

            if ( ! function_exists( 'cs_enqueue_count_nos' ) ) {

                function cs_enqueue_count_nos() {
                    wp_enqueue_script( 'cs_countTo_js', plugins_url( '/assets/scripts/jquery.countTo.js' ), '', '', true );
                    wp_enqueue_script( 'cs_inview_js', plugins_url( '/assets/scripts/jquery.inview.min.js' ), '', '', true );
                }

            }
            /**
             *
             * @Add this enqueue Script
             */
            if ( ! function_exists( 'cs_addthis_script_init_method' ) ) {

                function cs_addthis_script_init_method() {
                    wp_enqueue_script( 'cs_addthis_js', cs_server_protocol() . 's7.addthis.com/js/250/addthis_widget.js#pubid=xa-4e4412d954dccc64', '', '', true );
                }

            }
            /**
             *
             * @social login script
             */
            if ( ! function_exists( 'cs_socialconnect_scripts' ) ) {

                function cs_socialconnect_scripts() {
                    wp_enqueue_script( 'cs_socialconnect_js', plugins_url( '/templates/elements/login/cs-social-login/media/js/cs-connect.js', __FILE__ ), '', '', true );
                }

            }

            /**
             *
             * @google auto complete script
             */
            if ( ! function_exists( 'cs_google_autocomplete_scripts' ) ) {

                function cs_google_autocomplete_scripts() {
                    wp_enqueue_script( 'cs_location_autocomplete_js', plugins_url( '/assets/scripts/jquery.location-autocomplete.js', __FILE__ ), '', '' );
                }

            }
            if ( is_admin() ) {
                // admin css files
                wp_enqueue_style( 'cs_admin_styles_css', plugins_url( '/admin/assets/css/admin_style.css', __FILE__ ) );
                wp_enqueue_style( 'cs_datatable_css', plugins_url( '/admin/assets/css/datatable.css', __FILE__ ) );
                wp_enqueue_style( 'cs_fonticonpicker_css', plugins_url( '/assets/icomoon/css/jquery.fonticonpicker.min.css', __FILE__ ) );
                wp_enqueue_style( 'cs_iconmoon_css', plugins_url( '/assets/icomoon/css/iconmoon.css', __FILE__ ) );
                wp_enqueue_style( 'cs_fonticonpicker_bootstrap_css', plugins_url( '/assets/icomoon/theme/bootstrap-theme/jquery.fonticonpicker.bootstrap.css', __FILE__ ) );
                wp_enqueue_style( 'cs_bootstrap_css', plugins_url( '/admin/assets/css/bootstrap.css', __FILE__ ) );
                // admin js files
                wp_enqueue_script( 'cs_datatable_js', plugins_url( '/admin/assets/scripts/datatable.js', __FILE__ ), '', '', true );
                wp_enqueue_script( 'cs_chosen_jquery_js', plugins_url( '/assets/scripts/chosen.jquery.js', __FILE__ ) );
                wp_enqueue_script( 'cs_custom_wp_admin_script_js', plugins_url( '/admin/assets/scripts/cs_functions.js', __FILE__ ), '', '', true );
                wp_enqueue_script( 'cs_jobhunt_shortcodes_js', plugins_url( '/admin/assets/scripts/shortcode_functions.js', __FILE__ ), '', '', true );
                wp_enqueue_script( 'fonticonpicker_js', plugins_url( '/assets/icomoon/js/jquery.fonticonpicker.min.js', __FILE__ ) );
                cs_datetime_picker_scripts();
            }
            // get user inline style
            $this->cs_custom_inline_styles_method();
        }

        public static function cs_enqueue_tabs_script() {
            
        }

        /**
         *
         * @Responsive Tabs Styles and Scripts
         */
        public static function cs_enqueue_responsive_front_scripts() {


            $my_theme = wp_get_theme( 'JobCareer' );
            if ( ! $my_theme->exists() ) {
                if ( is_rtl() ) {
                    wp_enqueue_style( 'jobcareer_rtl_css', plugins_url( '/assets/css/rtl.css', __FILE__ ) );
                }
                wp_enqueue_style( 'jobcareer_responsive_css', plugins_url( '/assets/css/responsive.css', __FILE__ ) );
            }
        }

        /**
         *
         * @Data Table Style Scripts
         */

        /**
         * Start Function how to Add table Style Script
         */
        public static function cs_data_table_style_script() {
            wp_enqueue_script( 'cs_jquery_data_table_js', plugins_url( '/assets/scripts/jquery.data_tables.js', __FILE__ ), '', '', true );
            wp_enqueue_style( 'cs_data_table_css', plugins_url( '/assets/css/jquery.data_tables.css', __FILE__ ) );
        }

        /**
         * End Function how to Add Tablit Style Script
         */
        public static function cs_jquery_ui_scripts() {
            
        }

        /**
         * Start Function how to Add Location Picker Scripts
         */
        public function cs_location_gmap_script() {
            wp_enqueue_script( 'cs_jquery_latlon_picker_js', plugins_url( '/assets/scripts/jquery_latlon_picker.js', __FILE__ ), '', '', true );
        }

        /**
         * End Function how to Add Location Picker Scripts
         */

        /**
         * Start Function how to Add Google Place Scripts
         */
        public function cs_google_place_scripts() {
			global $cs_plugin_options;
            $google_api_key = '?libraries=places';
			if ( isset( $cs_plugin_options['cs_google_api_key'] ) && $cs_plugin_options['cs_google_api_key'] != '' ) {
				$google_api_key = '?key=' . $cs_plugin_options['cs_google_api_key'] . '&libraries=places';
			}
            wp_enqueue_script( 'cs_google_autocomplete_script', 'https://maps.googleapis.com/maps/api/js' . $google_api_key );
        }

        // start function for google map files 
        public static function cs_googlemapcluster_scripts() {
            wp_enqueue_script( 'jquery-googlemapcluster', plugins_url( '/assets/scripts/markerclusterer.js', __FILE__ ), '', '', true );
            wp_enqueue_script( 'cs_map_info_js', plugins_url( '/assets/scripts/map_infobox.js', __FILE__ ), '', '', true );
        }

        /**
         * End Function how to Add Google Place Scripts
         */

        /**
         * Start Function how to Add Google Autocomplete Scripts
         */
        public function cs_autocomplete_scripts() {
            wp_enqueue_script( 'jquery-ui-autocomplete' );
            wp_enqueue_script( 'jquery-ui-slider' );
        }

        /**
         * End Function how to Add Google Autocomplete Scripts
         */
        // Start function for global code
        public function cs_all_scodes() {
            global $cs_jh_scodes;
        }

        // Start function for auto login user
        public function cs_auto_login_user() {
            
        }

        public static $email_template_type = 'general';
        public static $email_default_template = 'Hello! I am general email template by [COMPANY_NAME].';
        public static $email_template_variables = array(
            array(
                'tag' => 'SITE_NAME',
                'display_text' => 'Site Name',
                'value_callback' => array( 'wp_jobhunt', 'cs_get_site_name' ),
            ),
            array(
                'tag' => 'ADMIN_EMAIL',
                'display_text' => 'Admin Email',
                'value_callback' => array( 'wp_jobhunt', 'cs_get_admin_email' ),
            ),
//            array(
//                'tag' => 'USER_NAME',
//                'display_text' => 'User Name',
//                'value_callback' => array('wp_jobhunt', 'cs_get_user_name'),
//            ),
            array(
                'tag' => 'SITE_URL',
                'display_text' => 'SITE URL',
                'value_callback' => array( 'wp_jobhunt', 'cs_get_site_url' ),
            ),
        );

        public function email_template_settings_callback( $email_template_options ) {
            $email_template_options['types'][] = self::$email_template_type;
            $email_template_options['templates']['general'] = self::$email_default_template;
            $email_template_options['variables']['General'] = self::$email_template_variables;

            return $email_template_options;
        }

        public static function cs_get_site_name() {
            return get_bloginfo( 'name' );
        }

        public static function cs_get_admin_email() {
            return get_bloginfo( 'admin_email' );
        }

//        public static function cs_get_user_name() {
//            $current_user = wp_get_current_user();
//            // If logged in.
//            if (0 != $current_user->ID) {
//                return $current_user->user_firstname . ' ' . $current_user->current_lastname;
//            }
//
//            return false;
//        }

        public static function cs_get_site_url() {
            return get_bloginfo( 'url' );
        }

        public static function cs_replace_tags( $template, $variables ) {
            // Add general variables to the list
            $variables = array_merge( self::$email_template_variables, $variables );
            foreach ( $variables as $key => $variable ) {
                $callback_exists = false;

                // Check if function/method exists.
                if ( is_array( $variable['value_callback'] ) ) { // If it is a method of a class.
                    $callback_exists = method_exists( $variable['value_callback'][0], $variable['value_callback'][1] );
                } else { // If it is a function.
                    $callback_exists = function_exists( $variable['value_callback'] );
                }

                // Substitute values in place of tags if callback exists.
                if ( true == $callback_exists ) {
                    // Make a call to callback to get value.
                    $value = call_user_func( $variable['value_callback'] );

                    // If we have some value to substitute then use that.
                    if ( false != $value ) {
                        $template = str_replace( '[' . $variable['tag'] . ']', $value, $template );
                    }
                }
            }
            return $template;
        }

        public static function get_template( $email_template_index, $email_template_variables, $email_default_template ) {
            $email_template = '';
            $template_data = array( 'subject' => '', 'from' => '', 'recipients' => '', 'email_notification' => '', 'email_type' => '', 'email_template' => '' );
            // Check if there is a template select else go with default template.
            $selected_template_id = jobhunt_check_if_template_exists( $email_template_index, 'jh-templates' );
            if ( false != $selected_template_id ) {

                // Check if a temlate selected else default template is used.
                if ( $selected_template_id != 0 ) {
                    $templateObj = get_post( $selected_template_id );
                    if ( $templateObj != null ) {
                        $email_template = $templateObj->post_content;
                        $template_id = $templateObj->ID;
                        $template_data['subject'] = wp_jobhunt::cs_replace_tags( get_post_meta( $template_id, 'jh_subject', true ), $email_template_variables );
                        $template_data['from'] = wp_jobhunt::cs_replace_tags( get_post_meta( $template_id, 'jh_from', true ), $email_template_variables );
                        $template_data['recipients'] = wp_jobhunt::cs_replace_tags( get_post_meta( $template_id, 'jh_recipients', true ), $email_template_variables );
                        $template_data['email_notification'] = get_post_meta( $template_id, 'jh_email_notification', true );
                        $template_data['email_type'] = get_post_meta( $template_id, 'jh_email_type', true );
                    }
                } else {
                    // Get default template.
                    $email_template = $email_default_template;
                    $template_data['email_notification'] = 1;
                }
            } else {
                $email_template = $email_default_template;
                $template_data['email_notification'] = 1;
            }
            $email_template = wp_jobhunt::cs_replace_tags( $email_template, $email_template_variables );
            $template_data['email_template'] = $email_template;
            return $template_data;
        }

    }

}
/*
  Default Sidebars On/OFF Check
 */
if ( ! function_exists( 'callback_function' ) ) {
    add_action( 'wp_loaded', 'callback_function' );

    function callback_function() {
        if ( ! is_admin() ) {
            $cs_plugin_options = get_option( 'cs_plugin_options' );
            $cs_default_sidebar_option = isset( $cs_plugin_options['cs_default-sidebars'] ) ? $cs_plugin_options['cs_default-sidebars'] : '';
            if ( $cs_default_sidebar_option == 'on' ) {
                global $wp_registered_sidebars;
                foreach ( $wp_registered_sidebars as $sidebar_id ) {
                    $cs_unregister_id = isset( $sidebar_id['id'] ) ? $sidebar_id['id'] : '';
                    if ( $cs_unregister_id != '' ) {
                        unregister_sidebar( $sidebar_id['id'] );
                    }
                }
            }
        }
    }

}
/*
 * Check if an email template exists
 */
if ( ! function_exists( 'jobhunt_check_if_template_exists' ) ) {

    function jobhunt_check_if_template_exists( $slug, $type ) {
        global $wpdb;
        $post = $wpdb->get_row( "SELECT ID FROM " . $wpdb->prefix . "posts WHERE post_name = '" . $slug . "' && post_type = '" . $type . "'", 'ARRAY_A' );
        if ( isset( $post ) && isset( $post['ID'] ) ) {
            return $post['ID'];
        } else {
            return false;
        }
    }

}

/**
 *
 * @Create Object of class To Activate Plugin
 */
if ( class_exists( 'wp_jobhunt' ) ) {
    $cs_jobhunt = new wp_jobhunt();
    register_activation_hook( __FILE__, array( 'wp_jobhunt', 'activate' ) );
    register_deactivation_hook( __FILE__, array( 'wp_jobhunt', 'deactivate' ) );
}

//Remove Sub Menu add new job
function modify_menu() {
    global $submenu;
    unset( $submenu['edit.php?post_type=jobs'][10] );
}

add_action( 'admin_menu', 'modify_menu' );
