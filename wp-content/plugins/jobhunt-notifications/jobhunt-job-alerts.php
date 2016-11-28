<?php
/**
 * Plugin Name: JobHunt Job Alerts
 * Plugin URI: http://themeforest.net/user/Chimpstudio/
 * Description: Job Hunt Notifications Add on
 * Version: 1.3
 * Author: ChimpStudio
 * Author URI: http://themeforest.net/user/Chimpstudio/
 * @package Job Hunt
 *
 * @package	Job Hunt
 */
// Direct access not allowed.
if ( ! defined('ABSPATH') ) {
    exit;
}

/**
 * JobHunt_Job_Alerts class.
 */
class JobHunt_Job_Alerts {

    public static $job_details = array();
    public $email_template_type;
    public $email_default_template;
    public $email_template_variables;
    public $admin_notices;
    public $email_template_index;
    public $template_group;

    /**
     * Defined constants, include classes, enqueue scripts, bind hooks to parent plugin
     */
    public function __construct() {
        // Define constants
        define('JOBHUNT_NOTIFICATIONS_PLUGIN_VERSION', '1.0');
        define('JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN', 'jobhunt-notifications');
        define('JOBHUNT_NOTIFICATIONS_FILE', __FILE__);
        define('JOBHUNT_NOTIFICATIONS_CORE_DIR', WP_PLUGIN_DIR . '/jobhunt-notifications');
        define('JOBHUNT_NOTIFICATIONS_INCLUDES_DIR', JOBHUNT_NOTIFICATIONS_CORE_DIR . '/includes');
        define('JOBHUNT_NOTIFICATIONS_TEMPLATES_DIR', JOBHUNT_NOTIFICATIONS_CORE_DIR . '/templates');
        define('JOBHUNT_NOTIFICATIONS_LANGUAGES_DIR', JOBHUNT_NOTIFICATIONS_CORE_DIR . '/languages');
        define('JOBHUNT_NOTIFICATIONS_PLUGIN_URL', WP_PLUGIN_URL . '/jobhunt-notifications');
        $this->admin_notices = array();
        //admin notices
        add_action('admin_notices', array( $this, 'job_alert_notices_callback' ));
        if ( ! $this->check_dependencies() ) {
            return false;
        }

        $this->email_template_type = 'job alert';

        $this->email_default_template = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/></head><body style="margin: 0; padding: 0;"><div style="background-color: #eeeeef; padding: 50px 0;"><table style="max-width: 640px;" border="0" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td style="padding: 40px 30px 30px 30px;" align="center" bgcolor="#33333e"><h1 style="color: #fff;">'. __('Job Alert', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN) . '</h1></td></tr><tr><td bgcolor="#ffffff" style="padding: 40px 30px 40px 30px;"><table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td>'. __('Hello', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN) . '! '. __('Following are the jobs for which you have subscribed at ', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN) .' [SITE_NAME]</td></tr><tr><td style="padding: 10px 0 0 0;">'. __('Job Alert Title', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN) . ': [JOB_ALERT_TITLE]</td></tr><tr><td style="padding: 10px 0 0 0;">[JOB_ALERT_JOBS_LIST]</td></tr><tr><td style="padding: 10px 0 0 0;">'. __('All Jobs Listing Link', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN) . ': [JOB_ALERT_FULL_LISTING_URL]</td></tr><tr><td style="padding: 10px 0 0 0;">'. __('Jobs Count', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN) . ': [JOB_ALERT_TOTAL_JOBS_COUNT]</td></tr><tr><td style="padding: 10px 0 0 0;">'. __('Alert Frequency', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN) . ': [JOB_ALERT_FREQUENCY]</td></tr><tr><td style="padding: 10px 0 0 0;">'. __('To unsubscribe job alert', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN) . ': [JOB_ALERT_UNSUBSCRIBE_LINK]</td></tr></table></td></tr><tr><td style="background-color: #ffffff; padding: 30px 30px 30px 30px;"><table border="0" width="100%" cellspacing="0" cellpadding="0"><tbody><tr><td style="font-family: Arial, sans-serif; font-size: 14px;">&reg; [SITE_NAME], 2016</td></tr></tbody></table></td></tr></tbody></table></div></body></html>';

        $this->email_template_variables = array(
            array(
                'tag' => 'JOB_ALERT_TITLE',
                'display_text' => 'Job Alert Title',
                'value_callback' => array( $this, 'get_job_alert_title' ),
            ),
            array(
                'tag' => 'JOB_ALERT_JOBS_LIST',
                'display_text' => __('Filtered Jobs List', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
                'value_callback' => array( $this, 'get_filtered_jobs_list' ),
            ),
            array(
                'tag' => 'JOB_ALERT_TOTAL_JOBS_COUNT',
                'display_text' => __('Total Jobs Found', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
                'value_callback' => array( $this, 'get_total_jobs_count' ),
            ),
            array(
                'tag' => 'JOB_ALERT_UNSUBSCRIBE_LINK',
                'display_text' => __('Job Alert Unsubscribe Link', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
                'value_callback' => array( $this, 'get_unsubscribe_link' ),
            ),
            array(
                'tag' => 'JOB_ALERT_FREQUENCY',
                'display_text' => __('Job Alert Frequency', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
                'value_callback' => array( $this, 'get_frequency' ),
            ),
            array(
                'tag' => 'JOB_ALERT_FULL_LISTING_URL',
                'display_text' => __('Job Alert Full Listing URL', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
                'value_callback' => array( $this, 'get_full_listing_url' ),
            ),
        );

        $this->email_template_index = 'job-alert-template';
        $this->template_group = 'job alert';

        // Initialize Addon
        add_action('init', array( $this, 'init' ), 0);
    }

    /**
     * Initialize application, load text domain, enqueue scripts and bind hooks
     */
    public function init() {
     //   load_plugin_textdomain(JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN, false, JOBHUNT_NOTIFICATIONS_LANGUAGES_DIR);
        // Add Plugin textdomain
        $locale = apply_filters('plugin_locale', get_locale(), JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN);
        load_textdomain(JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN, plugin_dir_path(__FILE__) . "languages/" . JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN . "-" . $locale . '.mo');

        add_action('wp_enqueue_scripts', array( $this, 'enqueue_scripts' ));
        //add_filter('jobhunt_notifications_theme_options', array($this, 'theme_options'));
        // Include Custom Post Type class - Create Notification type and meta boxes.
        require_once JOBHUNT_NOTIFICATIONS_INCLUDES_DIR . '/class-jobhunt-notifications-post-type.php';
        require_once JOBHUNT_NOTIFICATIONS_INCLUDES_DIR . '/class-jobhunt-notifications-plugin-options.php';
        require_once JOBHUNT_NOTIFICATIONS_INCLUDES_DIR . '/class-jobhunt-notifications-helpers.php';
        require_once JOBHUNT_NOTIFICATIONS_INCLUDES_DIR . '/class-jobhunt-notifications-employer-ui.php';
        require_once JOBHUNT_NOTIFICATIONS_INCLUDES_DIR . '/class-jobhunt-notifications-candidate-ui.php';

        // Add hook for dashboard candidate top menu links.
        add_action('jobhunt_after_jobs_listing', array( $this, 'after_jobs_listing_callback' ), 10, 2);

        // Add hook for frontend UI.
        add_action('pre_jobhunt_jobs_listing', array( $this, 'frontend_ui_callback' ), 10, 0);

        // Hook our function , create_daily_alert_schedule_callback(), into the action create_daily_alert_schedule.
        add_action('create_daily_alert_schedule', array( $this, 'create_daily_alert_schedule_callback' ));

        // Add options in Email Templates Addon
        //add_filter('cs_jobhunt_email_templates_options', array($this, 'email_templates_options_callback'), 10, 1);
        // Add optinos in Email Template Settings
        add_filter('jobhunt_email_template_settings', array( $this, 'email_template_settings_callback' ), 0, 1);

        //add_action('init', array( $this, 'create_daily_alert_schedule_callback' ), 1000, 1);

        add_action('init', array( $this, 'add_email_template_callback' ), 5);
    }

    /**
     * Enqueue Frontend Styles and Scripts
     */
    public function enqueue_scripts() {
        // Enqueue CSS
        wp_enqueue_style('jobhunt-notifications-css', JOBHUNT_NOTIFICATIONS_PLUGIN_URL . '/assets/css/jobhunt-notifications-frontend.css');
        // Register JS, should be included in header as this uses some variables.
        wp_enqueue_script('jobhunt-notifications-js', JOBHUNT_NOTIFICATIONS_PLUGIN_URL . '/assets/js/jobhunt-notifications.js', array( 'jquery' ), JOBHUNT_NOTIFICATIONS_PLUGIN_VERSION, true);
        wp_localize_script('jobhunt-notifications-js', 'jobhunt_notifications', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'security' => wp_create_nonce("jobhunt_create_job_alert"),
                )
        );
    }

    /**
     * Check plugin dependencies (JobHunt), nag if missing.
     *
     * @param boolean $disable disable the plugin if true, defaults to false.
     */
    public function check_dependencies($disable = false) {
        $result = true;
        $active_plugins = get_option('active_plugins', array());
        if ( is_multisite() ) {
            $active_sitewide_plugins = get_site_option('active_sitewide_plugins', array());
            $active_sitewide_plugins = array_keys($active_sitewide_plugins);
            $active_plugins = array_merge($active_plugins, $active_sitewide_plugins);
        }

        $jobhunt_is_active = in_array('wp-jobhunt/wp-jobhunt.php', $active_plugins);
        if ( ! $jobhunt_is_active ) {
            $this->admin_notices[] = '<div class="error">' . __('<em><b>Job Hunt Notifications</b></em> needs the <b>Job Hunt</b> plugin. Please install and activate it.', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN) . '</div>';
        }
        if ( ! $jobhunt_is_active ) {
            if ( $disable ) {
                include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                deactivate_plugins(array( __FILE__ ));
            }
            $result = false;
        }
        return $result;
    }

    public function job_alert_notices_callback() {
        foreach ( $this->admin_notices as $value ) {
            echo $value;
        }
    }

    public function after_jobs_listing_callback($jobs_query, $sort_by) {
        echo '<div class="jobs_query hidden">' . json_encode($jobs_query) . '</div>';
    }

    public function frontend_ui_callback() {
        $cs_plugin_options = get_option('cs_plugin_options');
        $jobhunt_terms_conditions = isset($cs_plugin_options['cs_jobhunt_terms_conditions']) ? $cs_plugin_options['cs_jobhunt_terms_conditions'] : '';
        $frequencies = array(
            'cs_jobhunt_frequency_daily' => __('Daily', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
            'cs_jobhunt_frequency_weekly' => __('Weekly', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
            'cs_jobhunt_frequency_fortnightly' => __('Fortnightly', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
            'cs_jobhunt_frequency_monthly' => __('Monthly', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
            'cs_jobhunt_frequency_biannually' => __('Biannually', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
            'cs_jobhunt_frequency_annually' => __('Annually', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
            'cs_jobhunt_frequency_never' => __('Never', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
        );
        $options_str = '';
        $is_one_checked = false;
        $checked = 'checked="checked"';
        foreach ( $frequencies as $frequency => $label ) {
            // If it is 'on' then show it's option
            if ( isset($cs_plugin_options[$frequency]) && 'on' == $cs_plugin_options[$frequency] ) {
                $options_str .= '<label><input name="alert-frequency" class="radio-frequency" maxlength="75" type="radio" value="' . strtolower($label) . '" ' . $checked . '> ' . $label . '</label>';
                if ( false == $is_one_checked ) {
                    $checked = '';
                    $is_one_checked = true;
                }
            }
        }
        // If no option for frequency is selected then set 'Never' as default option
        //if ( $options_str == '' ) {
        //	$options_str .= '<label><input name="alert-frequency" class="radio-frequency" maxlength="75" type="radio" value="' . strtolower('Never') . '" ' . $checked . '> Never</label>';
        //}
        // Get logged in user email and hide email address field.
        $user = wp_get_current_user();
        $disabled = '';
        $email = '';
        if ( $user->ID > 0 ) {
            $email = $user->user_email;
            $disabled = ' disabled="disabled"';
        }
        echo '
	            <div class="job-alert-box job-alert job-alert-container-top" style="display:none;">
				<div class="btn-close-job-alert-box">x</div>
				<h3>' . __('Email me jobs like these', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN) . '</h3>
				<form class="job-alerts">
					<div class="newsletter">
						<input name="alerts-name" placeholder="' . __('Job alert name...', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN) . '" class="form-control name-input-top" maxlength="75" type="text">
						<input type="email" class="email-input-top alerts-email" placeholder=' . __("example@email.com", JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN) . ' name="alerts-email" value="' . $email . '"  ' . $disabled . '/>
						<button name="AlertsEmail" class="jobalert-submit" type="submit">' . __('Submit', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN) . '</button>
					</div>
					' . (
        strlen($options_str) == 0 ? '' : (
                '<div class="alert-frequency">
								<span>' . __('Alert Frequency', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN) . ':</span>
								' . $options_str . '
							</div>'
                )
        ) .
        '<div class="validation error hide">
						<label class="" for="alerts-email-top">' . __('Please enter a valid email', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN) . '</label>
					</div>';
        if ( $jobhunt_terms_conditions <> '' ) {
            echo '<div class="terms-message">' . html_entity_decode($jobhunt_terms_conditions) . '</div>';
        }
        echo '</form>
			</div>
		';
    }

    public static function create_daily_alert_schedule() {
        // Use wp_next_scheduled to check if the event is already scheduled.
        $timestamp = wp_next_scheduled('create_daily_alert_schedule');

        // If $timestamp == false schedule daily alerts since it hasn't been done previously.
        if ( $timestamp == false ) {
            // Schedule the event for right now, then to repeat daily using the hook 'create_daily_alert_schedule'.
            wp_schedule_event(time(), 'daily', 'create_daily_alert_schedule');
        }
    }

    public function add_email_template_callback() {
        $email_templates = array();
        $email_templates[$this->template_group] = array();
        $email_templates[$this->template_group][$this->email_template_index] = array(
            'title' => $this->email_template_type,
            'template' => $this->email_default_template,
            'email_template_type' => $this->email_template_type,
            'is_recipients_enabled' => false,
            'description' => __('This template will be used when sending a job alert, Template will have a list of jobs as per user set filters.', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
            'jh_email_type' => 'html',
        );
        do_action('jobhunt_load_email_templates', $email_templates);
    }

    public function get_template() {
        return wp_jobhunt::get_template($this->email_template_index, $this->email_template_variables, $this->email_default_template);
    }

    public function create_daily_alert_schedule_callback() {
        // Get alerts
        $args = array(
            'post_type' => 'job-alert',
        );
        $job_details = array();
        $job_alerts = new WP_Query($args);
        while ( $job_alerts->have_posts() ) {
            $job_alerts->the_post();
            $job_id = get_the_ID();
            $frequency_annually = get_post_meta($job_id, 'cs_frequency_annually', true);
            $frequency_biannually = get_post_meta($job_id, 'cs_frequency_biannually', true);
            $frequency_monthly = get_post_meta($job_id, 'cs_frequency_monthly', true);
            $frequency_fortnightly = get_post_meta($job_id, 'cs_frequency_fortnightly', true);
            $frequency_weekly = get_post_meta($job_id, 'cs_frequency_weekly', true);
            $frequency_daily = get_post_meta($job_id, 'cs_frequency_daily', true);
            $frequency_never = get_post_meta($job_id, 'cs_frequency_never', true);
            $last_time_email_sent = get_post_meta($job_id, 'cs_last_time_email_sent', true);
            $set_frequency = '';
            if ( ! empty($frequency_annually) ) {
                $selected_frequency = '+365 days';
                $set_frequency = __('Annually', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN);
            } else if ( ! empty($frequency_biannually) ) {
                $selected_frequency = '+182 days';
                $set_frequency = __('Bi-Annually', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN);
            } else if ( ! empty($frequency_monthly) ) {
                $selected_frequency = '+30 days';
                $set_frequency = __('Monthly', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN);
            } else if ( ! empty($frequency_fortnightly) ) {
                $selected_frequency = '+15 days';
                $set_frequency = __('Fortnightly', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN);
            } else if ( ! empty($frequency_weekly) ) {
                $selected_frequency = '+7 days';
                $set_frequency = __('Weekly', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN);
            } else if ( ! empty($frequency_daily) ) {
                $selected_frequency = '+1 days';
                $set_frequency = __('Daily', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN);
            } else if ( ! empty($frequency_never) ) {
                $selected_frequency = false;
                $set_frequency = __('never', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN);
            } else {
                $selected_frequency = false;
                $set_frequency = '';
            }
            if ( $selected_frequency != false ) {
                $email_template_variables = array();
                $email_template = '';
                if ( time() > strtotime($selected_frequency, intval($last_time_email_sent)) ) {
                    // Set this for email data.
                    self::$job_details = array(
                        'id' => $job_id,
                        'title' => get_the_title(),
                        'jobs_query' => json_decode(get_post_meta($job_id, 'cs_jobs_query', true), true),
                        'email' => get_post_meta($job_id, 'cs_email', true),
                        'url_query' => get_post_meta($job_id, 'cs_query', true),
                        'frequency' => $selected_frequency,
                        'set_frequency' => $set_frequency,
                    );
                    $template = $this->get_template();
                    // Checking email notification is enabled/disabled.
                    if ( isset($template['email_notification']) && $template['email_notification'] == 1 && self::get_job_alerts_count(self::$job_details['jobs_query'], self::$job_details['frequency']) > 0 ) {

                        $subject = (isset($template['subject']) && $template['subject'] != '' ) ? $template['subject'] : __('New Jobs Found at', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN) . get_bloginfo('name');
                        //$from = (isset($template['from']) && $template['from'] != '') ? $template['from'] : esc_attr($blogname) . ' <' . $admin_email . '>';
                        $recipients = (isset($template['recipients']) && $template['recipients'] != '') ? $template['recipients'] : self::$job_details['email']; //$this->get_job_added_email();
                        $email_type = (isset($template['email_type']) && $template['email_type'] != '') ? $template['email_type'] : 'plain_text';

                        $args = array(
                            'to' => self::$job_details['email'],
                            'subject' => $subject,
                            //'from' => $from,
                            'message' => $template['email_template'],
                            'email_type' => $email_type,
                            'class_obj' => $this,
                        );
                        // Update last time email sent for this job alert.
                        update_post_meta($job_id, 'cs_last_time_email_sent', time());
                        //  Send email.
                        do_action('jobhunt_send_mail', $args);
                    }
                } else {
                    // echo 'no';
                }
                // echo the_title();
            }
        }
    }

    public static function remove_daily_alert_schedule() {
        wp_clear_scheduled_hook('create_daily_alert_schedule');
    }

//    public function email_templates_options_callback($cs_setting_options) {
//        $cs_setting_options[] = array(
//            "name" => __('Job Alert Email', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
//            "desc" => '',
//            "hint_text" => '',
//            "id" => 'job_alert_email_template',
//            "std" => '',
//            'classes' => 'chosen-select-no-single',
//            "type" => 'select_values',
//            "options" => array(),
//            'email_template_type' => 'job alert',
//        );
//
//        return $cs_setting_options;
//    }
//
    public function email_template_settings_callback($email_template_options) {

        $email_template_options["types"][] = $this->email_template_type;
        $email_template_options["templates"]["job alert"] = $this->email_default_template;
        $email_template_options["variables"]["job alert"] = $this->email_template_variables;
        return $email_template_options;
    }

    public static function get_job_alert_title() {
        if ( isset(self::$job_details['title']) ) {
            return ucfirst(self::$job_details['title']);
        }
        return false;
    }

    public static function get_filtered_jobs_list() {
        if ( isset(self::$job_details['jobs_query']) ) {
            $jobs_query = self::$job_details['jobs_query'];
            $frequency = str_replace('+', '-', self::$job_details['frequency']);
            $jobs_query['meta_query'][] = array(
                'key' => 'cs_job_posted',
                'value' => strtotime(date('Y-m-d', strtotime($frequency))),
                'compare' => '>=',
            );
            $jobs_query['posts_per_page'] = 10;
            $loop = new WP_Query($jobs_query);
            ob_start();
            ?>
            <table cellpadding="0px" cellspacing="0px">
                <?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
                    <tr><td style="padding: 5px 0 0 0;"><a href="<?php echo get_post_permalink(); ?>"><?php echo the_title(); ?></a></td></tr>
                        <?php endwhile; ?>
            </table>
            <?php
            $html1 = ob_get_clean();
            return $html1;
        }
        return false;
    }

    public static function get_total_jobs_count() {

        if ( isset(self::$job_details['jobs_query']) ) {
            return self::get_job_alerts_count(self::$job_details['jobs_query'], self::$job_details['frequency']);
        }
        return false;
    }

    public static function get_unsubscribe_link() {
        if ( isset(self::$job_details['id']) ) {
            return '<a href="' . admin_url('admin-ajax.php') . '?action=jobhunt_unsubscribe_job_alert&jaid=' . self::$job_details['id'] . '">' . __('Unsubscribe', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN) . '</a>';
        }
        return false;
    }

    public static function get_frequency() {
        if ( isset(self::$job_details['set_frequency']) ) {
            return self::$job_details['set_frequency'];
        }
        return false;
    }

    public static function get_full_listing_url() {
        if ( isset(self::$job_details['id']) ) {
            $cs_plugin_options = get_option('cs_plugin_options');
            $default_listing_page = '';
            if ( isset($cs_plugin_options['cs_search_result_page']) ) {
                $page = get_post($cs_plugin_options['cs_search_result_page']);
                $default_listing_page = $page->post_name;
            }
            //cs_search_result_page
            return '<a href="' . get_bloginfo('url') . '/' . $default_listing_page . '?' . self::$job_details['url_query'] . '">' . __('View Full Listing', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN) . '</a>';
        }
        return false;
    }

    public static function get_job_alerts_count($jobs_query, $frequency) {
        $frequency = str_replace('+', '-', $frequency);

        $jobs_query['meta_query'][] = array(
            'key' => 'cs_job_posted',
            'value' => strtotime(date('Y-m-d', strtotime($frequency))),
            'compare' => '>=',
        );
        $jobs_query['posts_per_page'] = -1;
        $loop_count = new WP_Query($jobs_query);

        return $loop_count->found_posts;
    }

}

// On plugin activation register daily cron job.
register_activation_hook(__FILE__, array( 'JobHunt_Job_Alerts', 'create_daily_alert_schedule' ));
// On plugin deactivation unregister daily cron job.
register_deactivation_hook(__FILE__, array( 'JobHunt_Job_Alerts', 'remove_daily_alert_schedule' ));
new JobHunt_Job_Alerts();
