<?php
/**
 * Create Custom Post Type and it's meta boxes for Job Alert Notifications
 *
 * @package	Job Hunt
 */

// Direct access not allowed.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'create_plugin_options' ) ) {
	/**
	 * Create Plugin Options
	 */
	function create_plugin_options($cs_setting_options) {
		$on_off_option = array('yes' => __("Yes", JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN), 'no' => __("No", JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN));

		$cs_setting_options[] = array(
			"name" => __('Job Alerts', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
			"fontawesome" => 'icon-bell-o',
			"id" => 'tab-job-alert-settings',
			"std" => "",
			"type" => "main-heading",
			"options" => ''
		);
		$cs_setting_options[] = array(
			"name" => __("Job Alerts", JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
			"id" => "tab-job-alert-settings",
			"type" => "sub-heading"
		);
		$cs_setting_options[] = array(
			"name" => __('Set Alert Frequencies', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
			"id" => "tab-user-alert-frequency",
			"std" => __('Frequency', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
			"type" => "section",
			"options" => ""
		);
		$cs_setting_options[] = array(
			"name" => __("Annually", JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
			"desc" => "",
			"hint_text" => __("Do you want to allow user to set alert frequency to annually?", JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
			"id" => "jobhunt_frequency_annually",
			"std" => "",
			"type" => "checkbox",
			"options" => $on_off_option
		);
		$cs_setting_options[] = array(
			"name" => __("Biannually", JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
			"desc" => "",
			"hint_text" => __("Do you want to allow user to set alert frequency to biannually?", JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
			"id" => "jobhunt_frequency_biannually",
			"std" => "",
			"type" => "checkbox",
			"options" => $on_off_option
		);
		$cs_setting_options[] = array(
			"name" => __("Monthly", JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
			"desc" => "",
			"hint_text" => __("Do you want to allow user to set alert frequency to monthly?", JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
			"id" => "jobhunt_frequency_monthly",
			"std" => "",
			"type" => "checkbox",
			"options" => $on_off_option
		);
		$cs_setting_options[] = array(
			"name" => __("Fortnightly", JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
			"desc" => "",
			"hint_text" => __("Do you want to allow user to set alert frequency to fortnight?", JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
			"id" => "jobhunt_frequency_fortnightly",
			"std" => "",
			"type" => "checkbox",
			"options" => $on_off_option
		);
		$cs_setting_options[] = array(
			"name" => __("Weekly", JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
			"desc" => "",
			"hint_text" => __("Do you want to allow user to set alert frequency to weekly?", JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
			"id" => "jobhunt_frequency_weekly",
			"std" => "",
			"type" => "checkbox",
			"options" => $on_off_option
		);
		$cs_setting_options[] = array(
			"name" => __("Daily", JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
			"desc" => "",
			"hint_text" => __("Do you want to allow user to set alert frequency to daily?", JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
			"id" => "jobhunt_frequency_daily",
			"std" => "",
			"type" => "checkbox",
			"options" => $on_off_option
		);
		$cs_setting_options[] = array(
			"name" => __("Never", JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
			"desc" => "",
			"hint_text" => __("Do you want to allow user to set alert frequency to Never?", JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
			"id" => "jobhunt_frequency_never",
			"std" => "",
			"type" => "checkbox",
			"options" => $on_off_option
		);
                
            $cs_setting_options[] = array(
			"name" => __("Terms & conditions text", JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
			"desc" => "",
			"hint_text" => __("This will be used on front-end  job listing page to create a job alert.", JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
			"id" => "jobhunt_terms_conditions",
			"std" => "",
			"cs_editor" => true,
			"type" => "textarea",
		);
                
		$cs_setting_options[] = array(
			"col_heading" => __("Job Alerts", JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
			"type" => "col-right-text",
			"help_text" => ""
		);

		return $cs_setting_options;
	}
}
// Add Plugin Options
add_filter('cs_jobhunt_plugin_addons_options', 'create_plugin_options', 10, 1);


if ( ! function_exists( 'jobhunt_jobs_shortcode_admin_fields_callback' ) ) {
	/**
	 * Add Option to enable/disable 'Email me job like these' button 'Job Options Shortcode Element Settings'
	 */
	function jobhunt_jobs_shortcode_admin_fields_callback($attrs) {
		global $cs_html_fields;
		
		$cs_opt_array = array(
			'name' => __('Job Alert Shortcode', 'jobhunt'),
			'desc' => '',
			'hint_text' => __('Do you want to show "Email Me Jobs Like These" button on this jobs listing page to set job alerts.', 'jobhunt'),
			'echo' => true,
			'field_params' => array(
				'std' => $attrs['cs_job_alert_button'],
				'id' => 'job_alert_button',
				'cust_name' => 'cs_job_alert_button[]',
				'classes' => 'dropdown chosen-select',
				'options' => array(
					'enable' => __('Enable', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
					'disable' => __('Disable', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
				),
				'return' => true,
			),
		);

		$cs_html_fields->cs_select_field($cs_opt_array);

	}
}
// Add Option to enable/disable 'Email me job like these' button 'Job Options Shortcode Element Settings'
add_action('jobhunt_jobs_shortcode_admin_fields', 'jobhunt_jobs_shortcode_admin_fields_callback', 10, 1);


if ( ! function_exists( 'jobhunt_save_jobs_shortcode_admin_fields_callback' ) ) {
	/**
	 * Save Option to enable/disable 'Email me job like these' button 'Job Options Shortcode Element Settings'
	 */
	function jobhunt_save_jobs_shortcode_admin_fields_callback($shortcode, $data, $cs_counter_job) {
		
		if (isset($data['cs_job_alert_button'][$cs_counter_job]) && $data['cs_job_alert_button'][$cs_counter_job] != '') {
			$shortcode .= 'cs_job_alert_button="' . htmlspecialchars($data['cs_job_alert_button'][$cs_counter_job]) . '" ';
		}
		return $shortcode;
	}
}
// Add Plugin Options
add_filter('jobhunt_save_jobs_shortcode_admin_fields', 'jobhunt_save_jobs_shortcode_admin_fields_callback', 10, 3);


if ( ! function_exists( 'jobhunt_jobs_shortcode_admin_default_attributes_callback' ) ) {
	/**
	 * Set default Option to enable/disable 'Email me job like these' button 'Job Options Shortcode Element Settings'
	 */
	function jobhunt_jobs_shortcode_admin_default_attributes_callback($defaults) {
		$defaults['cs_job_alert_button'] = 'enable';
		return $defaults;
	}
}
// Register default variable on backend
add_filter('jobhunt_jobs_shortcode_admin_default_attributes', 'jobhunt_jobs_shortcode_admin_default_attributes_callback', 10, 1);
// Register default variable on frontend
add_filter('jobhunt_jobs_shortcode_frontend_default_attributes', 'jobhunt_jobs_shortcode_admin_default_attributes_callback', 10, 1);