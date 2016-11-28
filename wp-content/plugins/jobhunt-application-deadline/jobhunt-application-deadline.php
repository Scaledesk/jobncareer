<?php

/**
 * Plugin Name: JobHunt Application Deadline
 * Plugin URI: http://themeforest.net/user/Chimpstudio/
 * Description: Job Hunt Application Deadline Add on
 * Version: 1.3
 * Author: ChimpStudio
 * Author URI: http://themeforest.net/user/Chimpstudio/
 * @package Job Hunt
 */
// Direct access not allowed.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Job_Hunt_Application_Deadline class.
 */
class Job_Hunt_Application_Deadline {

    public $admin_notices;

    /**
     * construct function.
     */
    public function __construct() {

        // Define constants
        define('JOBHUNT_APPLICATION_DEADLINE_PLUGIN_VERSION', '1.0');
        define('JOBHUNT_APPLICATION_DEADLINE_PLUGIN_DOMAIN', 'jobhunt-application-deadline');
        define('JOBHUNT_APPLICATION_DEADLINE_PLUGIN_URL', WP_PLUGIN_URL . '/jobhunt-application-deadline');
        define('JOBHUNT_APPLICATION_DEADLINE_CORE_DIR', WP_PLUGIN_DIR . '/jobhunt-application-deadline');
        define('JOBHUNT_APPLICATION_DEADLINE_LANGUAGES_DIR', JOBHUNT_APPLICATION_DEADLINE_CORE_DIR . '/languages');

        $this->admin_notices = array();
        //admin notices
        add_action('admin_notices', array($this, 'job_application_deadline_notices_callback'));
        if (!$this->check_dependencies()) {
            return false;
        }
        // Initialize Addon
        add_action('init', array($this, 'init'));

        // Add Plugin Options
        add_filter('cs_jobhunt_plugin_addons_options', array($this, 'create_plugin_options'), 11, 1);

        // Filters
        add_filter('job_hunt_application_deadline_field', array($this, 'application_deadline_field'), 10, 1);
        add_filter('job_hunt_application_deadline_field_frontend', array($this, 'application_deadline_field_frontend'), 10, 1);
        add_filter('job_hunt_update_application_deadline_field', array($this, 'update_application_deadline_field'), 12, 2);
        add_filter('job_hunt_update_application_deadline_field_frontend', array($this, 'update_application_deadline_field_frontend'), 20, 2);
        add_filter('job_hunt_jobs_listing_parameters', array($this, 'exclude_expired_jobs_from_listing'), 10, 2);
        add_filter('job_hunt_jobs_listing_parameters', array($this, 'jobs_sort_by_closing_date'), 10, 2);
        add_filter('job_hunt_check_job_deadline_date', array($this, 'check_job_deadline_date'), 10, 2);
        add_filter('job_hunt_application_deadline_date_frontend', array($this, 'application_deadline_date_frontend'), 10, 1);
        add_filter('job_hunt_jobs_sort_options', array($this, 'jobs_sort_options'), 10, 1);

        // Add column to admin
        add_filter('manage_edit-jobs_columns', array($this, 'columns'), 15);
        add_action('manage_jobs_posts_custom_column', array($this, 'custom_columns'), 15, 2);
    }

    /**
     *  Load text domain and enqueue style
     */
    public function init() {
        // Add Plugin textdomain
		$locale = apply_filters('plugin_locale', get_locale(), JOBHUNT_APPLICATION_DEADLINE_PLUGIN_DOMAIN);
		load_textdomain(JOBHUNT_APPLICATION_DEADLINE_PLUGIN_DOMAIN, plugin_dir_path(__FILE__) . "languages/" . JOBHUNT_APPLICATION_DEADLINE_PLUGIN_DOMAIN . "-" . $locale . '.mo');

        // Enqueue CSS
        wp_enqueue_style(JOBHUNT_APPLICATION_DEADLINE_PLUGIN_DOMAIN . '-styles', JOBHUNT_APPLICATION_DEADLINE_PLUGIN_URL . '/assets/css/dealine-style.css');
    }

    /**
     * Check plugin dependencies (JobHunt), nag if missing.
     *
     * @param boolean $disable disable the plugin if true, defaults to false.
     */
    public function check_dependencies($disable = false) {
        $result = true;
        $active_plugins = get_option('active_plugins', array());

        if (is_multisite()) {
            $active_sitewide_plugins = get_site_option('active_sitewide_plugins', array());
            $active_sitewide_plugins = array_keys($active_sitewide_plugins);
            $active_plugins = array_merge($active_plugins, $active_sitewide_plugins);
        }

        $jobhunt_is_active = in_array('wp-jobhunt/wp-jobhunt.php', $active_plugins);

        if (!$jobhunt_is_active) {
            $this->admin_notices[] = '<div class="error">' . __('<em><b>Job Hunt Application Deadline</b></em> needs the <b>Job Hunt</b> plugin. Please install and activate it.', JOBHUNT_APPLICATION_DEADLINE_PLUGIN_DOMAIN) . '</div>';
        }

        if (!$jobhunt_is_active) {
            if ($disable) {
                include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                deactivate_plugins(array(__FILE__));
            }
            $result = false;
        }

        return $result;
    }

    public function job_application_deadline_notices_callback() {
        foreach ($this->admin_notices as $value) {
            echo $value;
        }
    }

    /**
     * Create plugin options
     */
    public function create_plugin_options($cs_setting_options) {

        $on_off_option = array('yes' => __("Yes", JOBHUNT_APPLICATION_DEADLINE_PLUGIN_DOMAIN), 'no' => __("No", JOBHUNT_APPLICATION_DEADLINE_PLUGIN_DOMAIN));

        $cs_setting_options[] = array(
            "name" => __('Application Deadline', JOBHUNT_APPLICATION_DEADLINE_PLUGIN_DOMAIN),
            "fontawesome" => 'icon-calendar5',
            "id" => 'tab-application-deadline-settings',
            "std" => "",
            "type" => "main-heading",
            "options" => ''
        );

        $cs_setting_options[] = array(
            "name" => __("Application Deadline", JOBHUNT_APPLICATION_DEADLINE_PLUGIN_DOMAIN),
            "id" => "tab-application-deadline-settings",
            "type" => "sub-heading"
        );

        $cs_setting_options[] = array(
            "name" => __("Disable Expired Jobs", JOBHUNT_APPLICATION_DEADLINE_PLUGIN_DOMAIN),
            "desc" => "",
            "hint_text" => __("Manage expired jobs option here. If this switch is set ON, expired jobs will not be display on jobs listing pages. If it will be OFF, expired jobs will display on jobs listing pages.", JOBHUNT_APPLICATION_DEADLINE_PLUGIN_DOMAIN),
            "id" => "view_expired_jobs",
            "std" => "",
            "type" => "checkbox",
            "options" => $on_off_option
        );

        $cs_setting_options[] = array(
            "name" => __("Sort by Application Deadline", JOBHUNT_APPLICATION_DEADLINE_PLUGIN_DOMAIN),
            "desc" => "",
            "hint_text" => __("Manage sort by application deadline date option here. If this switch is set ON, application deadline date option will display in sort by dropdown on jobs listing pages. If it will be OFF, application deadline date option will not be display in sort by dropdown on jobs listing pages.", JOBHUNT_APPLICATION_DEADLINE_PLUGIN_DOMAIN),
            "id" => "sort_by_closing_date",
            "std" => "",
            "type" => "checkbox",
            "options" => $on_off_option
        );

        $cs_setting_options[] = array(
            "col_heading" => __("Application Deadline", JOBHUNT_APPLICATION_DEADLINE_PLUGIN_DOMAIN),
            "type" => "col-right-text",
            "help_text" => ""
        );

        return $cs_setting_options;
    }

    /**
     * Application deadline field in admin
     */
    public function application_deadline_field() {
        global $cs_html_fields;

        $cs_opt_array = array(
            'name' => __('Application Deadline Date:', JOBHUNT_APPLICATION_DEADLINE_PLUGIN_DOMAIN),
            'desc' => '',
            'hint_text' => __('Application deadline date. The listing will automatically end after this date.', JOBHUNT_APPLICATION_DEADLINE_PLUGIN_DOMAIN),
            'echo' => true,
            'field_params' => array(
                'std' => '', //date('d-m-Y'),
                'id' => 'application_closing_date',
                'format' => 'd-m-Y',
                'strtotime' => true,
                'extra_atr' => ' placeholder="' . __('d-m-Y', JOBHUNT_APPLICATION_DEADLINE_PLUGIN_DOMAIN) . '"',
                'return' => true,
            ),
        );
        $cs_html_fields->cs_date_field($cs_opt_array);
    }

    /**
     * Application deadline field in frontend
     */
    public function application_deadline_field_frontend($job_id) {
        global $cs_form_fields2;

        $date = date('d-m-Y');
        if ($job_id != '') {
            $date = get_post_meta($job_id, 'cs_application_closing_date', true);
            $date = date('d-m-Y', $date);
        }
        $output = '';
        $output .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
        $output .= '<label>' . __('Application Deadline Date', JOBHUNT_APPLICATION_DEADLINE_PLUGIN_DOMAIN) . '</label>';
        $field_params = array(
            'std' => $date, //date('d-m-Y'),
            'id' => 'application_closing_date',
            'format' => 'd-m-Y',
            'strtotime' => true,
            'extra_atr' => ' placeholder="' . __('d-m-Y', JOBHUNT_APPLICATION_DEADLINE_PLUGIN_DOMAIN) . '"',
            'return' => true,
        );
        $output .= $cs_form_fields2->cs_form_date_render($field_params);
        $output .= '</div>';
        echo $output;
    }

    /**
     * Update application deadline field from admin
     */
    public function update_application_deadline_field($post_id, $fields = '') {

        if ($post_id != '' && $fields != '' && isset($fields['cs_application_closing_date']) && $fields['cs_application_closing_date'] != '') {
            $value = $fields['cs_application_closing_date'];
            update_post_meta($post_id, 'cs_application_closing_date', strtotime($value));
        }
    }

    /**
     * Update application deadline field from frontend
     */
    public function update_application_deadline_field_frontend($post_id, $fields = '') {

        if ($post_id != '' && $fields != '' && isset($fields['cs_application_closing_date']) && $fields['cs_application_closing_date'] != '') {
            $value = $fields['cs_application_closing_date'];
            update_post_meta($post_id, 'cs_application_closing_date', $value);
        }
    }

    /**
     * Exclude expired jobs from listing
     * @return array
     */
    public function exclude_expired_jobs_from_listing($args = '', $job_sort_by = '') {
        global $cs_plugin_options;

        $cs_view_expired_jobs = isset($cs_plugin_options['cs_view_expired_jobs']) ? $cs_plugin_options['cs_view_expired_jobs'] : '';

        if ($cs_view_expired_jobs == 'on') {
            $filter_arr = array(
                'key' => 'cs_application_closing_date',
                'value' => strtotime(date('d-m-Y')),
                'compare' => '>=',
            );
            $args['meta_query'][] = $filter_arr;
        }

        return $args;
    }

    /**
     *  Jobs listing pages sort by application closing date filter
     */
    public function jobs_sort_by_closing_date($args = '', $job_sort_by = '') {

        if ($job_sort_by == 'application_closing_date') {
            $args['meta_key'] = 'cs_application_closing_date';
            $args['orderby'] = 'meta_value';
            $args['order'] = 'DESC';
        }
        return $args;
    }

    /**
     *  Jobs listing pages sort by filter options
     */
    public function jobs_sort_options($sort_options) {
        global $cs_plugin_options;

        $cs_sort_by_closing_date = isset($cs_plugin_options['cs_sort_by_closing_date']) ? $cs_plugin_options['cs_sort_by_closing_date'] : '';

        if ($cs_sort_by_closing_date == 'on') {
            $sort_options['application_closing_date'] = __('Application Deadline Date', JOBHUNT_APPLICATION_DEADLINE_PLUGIN_DOMAIN);
        }

        return $sort_options;
    }

    /**
     *  Check job deadline date expired or not
     *  @return array 
     */
    public function check_job_deadline_date($post_id, $args) {

        $cs_application_closing_date = get_post_meta($post_id, 'cs_application_closing_date', true);
        if (($cs_application_closing_date < strtotime(date('d-m-Y'))) && $cs_application_closing_date != '') {
            $args['status'] = 0;
            $args['msg'] = __("You can't apply this job due to application deadline.", JOBHUNT_APPLICATION_DEADLINE_PLUGIN_DOMAIN);
        }

        return $args;
    }

    /**
     *  Application deadline date frontend
     *  @return html string
     */
    public function application_deadline_date_frontend($post_id) {

        $cs_application_closing_date = get_post_meta($post_id, 'cs_application_closing_date', true);
        $expiring = floor(($cs_application_closing_date - strtotime(date('d-m-Y'))) / (60 * 60 * 24));

        $deadline_class = $deadline_date = '';
        $deadline_label = __('Apply Before: ', JOBHUNT_APPLICATION_DEADLINE_PLUGIN_DOMAIN);



        if ($cs_application_closing_date != '' && $expiring >= 0) {
            $deadline_date .= '<li class="application-deadline-date">';
            $deadline_date .= '<i class="icon-calendar5"></i>';
            $deadline_date .= esc_attr($deadline_label) . '  <span>' . date_i18n(get_option('date_format'), $cs_application_closing_date) . '</span>';
            $deadline_date .= '</li>';
        }

        return $deadline_date;
    }

    /**
     * Add a job closing date column to admin
     * @return array
     */
    public function columns($columns) {
        $new_columns = array();

        foreach ($columns as $key => $value) {
            $new_columns[$key] = $value;
            if ($key == 'expired') {
                $new_columns['deadline_date'] = __('Deadline Date', JOBHUNT_APPLICATION_DEADLINE_PLUGIN_DOMAIN);
            }
        }

        return $new_columns;
    }

    /**
     * Add a job closing date column value to admin
     * @return string
     */
    public function custom_columns($column) {
        global $post;
        
        if ($column == 'deadline_date') {
            $cs_application_closing_date = get_post_meta($post->ID, 'cs_application_closing_date', true);
            $cs_application_closing_date = isset($cs_application_closing_date) && $cs_application_closing_date != '' ? date_i18n('d/m/Y', ($cs_application_closing_date)) : '&ndash;';
            echo esc_html($cs_application_closing_date);
        }
    }

}

new Job_Hunt_Application_Deadline();
