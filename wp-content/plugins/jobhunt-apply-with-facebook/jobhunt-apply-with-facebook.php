<?php

/**
 * Plugin Name: JobHunt Apply With Facebook
 * Plugin URI: http://themeforest.net/user/Chimpstudio/
 * Description: Job Hunt Apply With Facebook Add on
 * Version: 1.1
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
class Job_Hunt_Apply_With_Facebook {

    public $admin_notices;

    /**
     * construct function.
     */
    public function __construct() {

        // Define constants
        define('JOBHUNT_FACEBOOK_APPLY_PLUGIN_VERSION', '1.0');
        define('JOBHUNT_FACEBOOK_APPLY_PLUGIN_DOMAIN', 'jobhunt-facebook-apply');
        define('JOBHUNT_FACEBOOK_APPLY_PLUGIN_URL', WP_PLUGIN_URL . '/jobhunt-apply-with-facebook');
        define('JOBHUNT_FACEBOOK_APPLY_CORE_DIR', WP_PLUGIN_DIR . '/jobhunt-apply-with-facebook');
        define('JOBHUNT_FACEBOOK_APPLY_LANGUAGES_DIR', JOBHUNT_FACEBOOK_APPLY_CORE_DIR . '/languages');

        $this->admin_notices = array();
		
		// Notices for Admin
		add_action('admin_notices', array($this, 'job_facebook_apply_notices_callback'));
        
		// Initialize Addon
        add_action('init', array($this, 'init'));

        // Filters
		add_filter('cs_jobhunt_plugin_addons_options', array($this, 'create_plugin_options'), 11, 1);
	   
		// Apply with facebook button
		add_action('apply_with_facebook_button', array($this, 'apply_with_facebook_button_callback'), 10, 1);
    }

    /**
     *  Load text domain and enqueue Script
     */
    public function init() {
		global $cs_plugin_options;
        
		// Add Plugin textdomain
        load_plugin_textdomain(JOBHUNT_FACEBOOK_APPLY_PLUGIN_DOMAIN, false, JOBHUNT_FACEBOOK_APPLY_LANGUAGES_DIR);
		
		// Check if facebook settings are added into plugin options
		if( isset( $cs_plugin_options['cs_apply_with_facebook'] ) && $cs_plugin_options['cs_apply_with_facebook'] == 'on' ){
			if( !isset( $cs_plugin_options['cs_facebook_app_id'] ) || $cs_plugin_options['cs_facebook_app_id'] == '' ){
				$this->admin_notices = array( '<div class="error">' . __('<em><b>Job Hunt Apply With Facebook</b></em> needs the <b>Application ID</b> & <b>Secret Key</b>. Please provide in Jobhunt plugin settings->API Settings.', JOBHUNT_FACEBOOK_APPLY_PLUGIN_DOMAIN) . '</div>' );
			}
		}

        // Enqueue JS
        wp_enqueue_script(JOBHUNT_FACEBOOK_APPLY_PLUGIN_DOMAIN . '-script', JOBHUNT_FACEBOOK_APPLY_PLUGIN_URL . '/assets/js/apply-fb-style.js',array( 'jquery' ));
    }
	
	public function job_facebook_apply_notices_callback() {
        foreach ($this->admin_notices as $value) {
            echo $value;
        }
    }

    /**
     * Draw Apply With Facebook Button.
     *
     * @param job_id is the job which user want to apply.
     */
	 
	public function apply_with_facebook_button_callback($job_id){
		global $cs_plugin_options;
		if( isset( $cs_plugin_options['cs_apply_with_facebook'] ) && $cs_plugin_options['cs_apply_with_facebook'] == 'on' ){
			echo '<a class="btn large facebook social_login_login_facebook_apply" href="#" data-applyjobid="'.$job_id.'">
				<div data-applyjobid="'.$job_id.'" class="facebook_jobid_apply"></div><i class="icon-facebook"></i>Apply with Facebook</a>';
		}
	}
	
	
	/**
     * Create plugin options
     */
    public function create_plugin_options($cs_setting_options) {

        $on_off_option = array('yes' => __("Yes", JOBHUNT_FACEBOOK_APPLY_PLUGIN_DOMAIN), 'no' => __("No", JOBHUNT_FACEBOOK_APPLY_PLUGIN_DOMAIN));

        $cs_setting_options[] = array(
            "name" => __('Apply With Facebook', JOBHUNT_FACEBOOK_APPLY_PLUGIN_DOMAIN),
            "fontawesome" => 'icon-facebook',
            "id" => 'tab-apply-with-facebook-settings',
            "std" => "",
            "type" => "main-heading",
            "options" => ''
        );

        $cs_setting_options[] = array(
            "name" => __("Apply With Facebook", JOBHUNT_FACEBOOK_APPLY_PLUGIN_DOMAIN),
            "id" => "tab-apply-with-facebook-settings",
            "type" => "sub-heading"
        );

        $cs_setting_options[] = array(
            "name" => __("Apply With Facebook", JOBHUNT_FACEBOOK_APPLY_PLUGIN_DOMAIN),
            "desc" => "",
            "hint_text" => __("If this switch is set ON User can apply using facebook. If it will be OFF, User will not be able to apply using facebook.", JOBHUNT_FACEBOOK_APPLY_PLUGIN_DOMAIN),
            "id" => "apply_with_facebook",
            "std" => "",
            "type" => "checkbox",
            "options" => $on_off_option
        );

        $cs_setting_options[] = array(
            "col_heading" => __("Apply With Facebook", JOBHUNT_FACEBOOK_APPLY_PLUGIN_DOMAIN),
            "type" => "col-right-text",
            "help_text" => ""
        );

        return $cs_setting_options;
    }

}

new Job_Hunt_Apply_With_Facebook();