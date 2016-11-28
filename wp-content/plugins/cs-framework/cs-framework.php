<?php
/*
  Plugin Name: CS Framework
  Plugin URI: http://themeforest.net/user/Chimpstudio/
  Description: Custom Post Types Management
  Version: 1.3
  Author: ChimpStudio
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

if (!class_exists('cs_framework')) {

    class cs_framework {

        public $plugin_url;

        //=====================================================================
        // Construct
        //=====================================================================
        public function __construct() {
            global $post, $wp_query, $cs_frame_options;
            add_action('init', array($this, 'load_plugin_textdomain'));
            $cs_frame_options = get_option('cs_frame_options');

            add_filter('template_include', array(&$this, 'cs_single_template'));
            add_action('wp_enqueue_scripts', array(&$this, 'cs_plugin_files_enqueue'));
            add_action('admin_enqueue_scripts', array(&$this, 'cs_plugin_files_enqueue'));

            // Theme Importer
            require_once('include/cs-importer/theme_importer.php');
            require_once('include/cs-importer/class-widget-data.php');

            // Mailchimp Functions
            require_once('include/cs-mailchimp/mailchimp.class.php');
            require_once('include/cs-mailchimp/mailchimp_functions.php');
        }

        /**
         * Fetch and return version of the current plugin
         *
         * @return	string	version of this plugin
         */
        public static function get_plugin_version() {
            $plugin_data = get_plugin_data(__FILE__);
            return $plugin_data['Version'];
        }

        /**
         *
         * @Text Domain
         */
        public function load_plugin_textdomain() {
            global $cs_plugin_options;

            if (function_exists('icl_object_id')) {

                global $sitepress, $wp_filesystem;
                require_once ABSPATH . '/wp-admin/includes/file.php';
                $backup_url = '';
                if (false === ($creds = request_filesystem_credentials($backup_url, '', false, false, array()) )) {
                    return true;
                }
                if (!WP_Filesystem($creds)) {
                    request_filesystem_credentials($backup_url, '', true, false, array());
                    return true;
                }
                $cs_languages_dir = plugin_dir_path(__FILE__) . 'languages/';
                $cs_all_langs = $wp_filesystem->dirlist($cs_languages_dir);
                $cs_mo_files = array();
                if (is_array($cs_all_langs) && sizeof($cs_all_langs) > 0) {

                    foreach ($cs_all_langs as $file_key => $file_val) {

                        if (isset($file_val['name'])) {

                            $cs_file_name = $file_val['name'];

                            $cs_ext = pathinfo($cs_file_name, PATHINFO_EXTENSION);

                            if ($cs_ext == 'mo') {
                                $cs_mo_files[] = $cs_file_name;
                            }
                        }
                    }
                }

                $cs_active_langs = $sitepress->get_current_language();
                foreach ($cs_mo_files as $mo_file) {
                    if (strpos($mo_file, $cs_active_langs) !== false) {
                        $cs_lang_mo_file = $mo_file;
                    }
                }
            }

            $locale = apply_filters('plugin_locale', get_locale(), 'cs_frame');
            $dir = trailingslashit(WP_LANG_DIR);
            if (isset($cs_lang_mo_file) && $cs_lang_mo_file != '') {
                load_textdomain('cs_frame', plugin_dir_path(__FILE__) . "languages/" . $cs_lang_mo_file);
            } else {
                load_textdomain('cs_frame', plugin_dir_path(__FILE__) . "languages/cs_frame-" . $locale . '.mo');
            }
        }

        /**
         *
         * @PLugin URl
         */
        public static function plugin_url() {
            return plugin_dir_url(__FILE__);
        }

        /**
         *
         * @Plugin Path
         */
        public static function plugin_dir() {
            return plugin_dir_path(__FILE__);
        }

        /**
         *
         * @Activate the plugin
         */
        public static function activate() {
            add_option('cs_frame_plugin_activation', 'installed');
            add_option('cs_frame', '1');
            add_action('init', 'cs_activation_data');
        }

        /**
         *
         * @Deactivate the plugin
         */
        static function deactivate() {
            delete_option('cs_frame_plugin_activation');
            delete_option('cs_frame', false);
        }

        /**
         *
         * @ Include Template
         */
        public function cs_single_template($single_template) {
            global $post;
            if (is_single()) {

                // do something
            }
            return $single_template;
        }

        /**
         *
         * @ Include Default Scripts and styles
         */
        public function cs_plugin_files_enqueue() {
            if (is_admin()) {
                wp_enqueue_media();
                wp_enqueue_script('my-upload', '', array('jquery', 'media-upload', 'thickbox', 'jquery-ui-droppable', 'jquery-ui-datepicker', 'jquery-ui-slider', 'wp-color-picker'));
                wp_enqueue_script('cs_frame_functions_js', plugins_url('/assets/scripts/cs_frame_functions.js', __FILE__), '', '', true);
            }
        }

        public static function cs_enqueue_timepicker_script() {

            if (is_admin()) {
                wp_enqueue_script('cs_datetimepicker_js', plugins_url('/assets/scripts/jquery_datetimepicker.js', __FILE__), '', '', true);
                wp_enqueue_style('cs_datetimepicker_css', plugins_url('/assets/css/jquery_datetimepicker.css', __FILE__));
            }
        }

    }

}

/**
 *
 * @Create Object of class To Activate Plugin
 */
if (class_exists('cs_framework')) {
    $cs_frame = new cs_framework();
    register_activation_hook(__FILE__, array('cs_framework', 'activate'));
    register_deactivation_hook(__FILE__, array('cs_framework', 'deactivate'));
}