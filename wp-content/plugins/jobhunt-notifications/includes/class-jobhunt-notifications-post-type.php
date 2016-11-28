<?php
/**
 * Create Custom Post Type and it's meta boxes for Job Alert Notifications
 *
 * @package	Job Hunt
 */
// Direct access not allowed.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * WP_Job_Hunt_Custom_Post_Type class.
 */
class WP_Job_Hunt_Custom_Post_Type {

    /**
     * Constructor
     */
    public function __construct() {
        $this->create_job_alert_post_type();

        // Configure meta boxes to be created for Job Notifications job type.
        add_action('add_meta_boxes', array($this, 'jobhunt_add_meta_boxes_to_job_alerts'));

        // Handle AJAX to create a job alert.
        add_action('wp_ajax_jobhunt_create_job_alert', array($this, 'create_job_alert_callback'));
        add_action('wp_ajax_nopriv_jobhunt_create_job_alert', array($this, 'create_job_alert_callback'));

        // Handle AJAX to delete a job alert.
        add_action('wp_ajax_jobhunt_remove_job_alert', array($this, 'remove_job_alert_callback'));
        add_action('wp_ajax_nopriv_jobhunt_remove_job_alert', array($this, 'remove_job_alert_callback'));
        
        // Handle AJAX to delete a job alert.
        add_action('wp_ajax_jobhunt_unsubscribe_job_alert', array($this, 'unsubscribe_job_alert'));
        add_action('wp_ajax_nopriv_jobhunt_unsubscribe_job_alert', array($this, 'unsubscribe_job_alert'));
    }

    /**
     * Register Custom Post Type for Job Notifications
     */
    public function create_job_alert_post_type() {
        // Check if post type already exists then don't register
        if (post_type_exists("job_hunt_notification")) {
            return;
        }
        $labels = array(
            'name' => _x('Job Alerts', 'post type general name', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
            'singular_name' => _x('Job Alert', 'post type singular name', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
            'menu_name' => _x('Job Alerts', 'admin menu', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
            'name_admin_bar' => _x('Job Alert', 'add new on admin bar', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
            'add_new' => _x('Add New', 'book', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
            'add_new_item' => __('Add New Job Alert', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
            'new_item' => __('New Job Alert', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
            'edit_item' => __('Edit Job Alert', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
            'view_item' => __('View Job Alert', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
            'all_items' => __('Job Alerts', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
            'search_items' => __('Search Job Alerts', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
            'parent_item_colon' => __('Parent Job Alerts:', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
            'not_found' => __('No Job Alerts found.', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
            'not_found_in_trash' => __('No Job Alerts found in Trash.', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
        );

        $args = array(
            'labels' => $labels,
            'description' => __('This allows user to manage job alerts.', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => 'edit.php?post_type=jobs',
            'query_var' => true,
            'capability_type' => 'post',
            'publicly_queryable' => false,
            'exclude_from_search' => true,
            'hierarchical' => false,
            'rewrite' => array('slug' => 'job-alert'),
            'supports' => false,
            'has_archive' => false,
        );

        // Register custom post type.
        register_post_type("job-alert", $args);
    }

    /**
     * Add meta boxes for Custom post type Job Alerts
     */
    public function jobhunt_add_meta_boxes_to_job_alerts() {
        add_meta_box('jobhunt_meta_jobs', __('Job Alert Options', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN), array($this, 'jobhunt_create_meta_boxes_to_job_alerts'), 'job-alert', 'normal', 'high');
    }

    public function jobhunt_create_meta_boxes_to_job_alerts() {
        global $post;
        ?>
        <div class="page-wrap page-opts left">
            <div class="option-sec" style="margin-bottom:0;">
                <div class="opt-conts">
                    <div class="elementhidden">
                        <?php $this->jobhunt_job_alert_options(); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="clear"></div>
        <?php
    }

    public function jobhunt_job_alert_options() {
        global $post, $cs_html_fields;
        $cs_opt_array = array(
            'name' => __('Email', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
            'desc' => '',
            'hint_text' => '',
            'echo' => true,
            'field_params' => array(
                'std' => get_post_meta($post->ID, 'cs_email', true),
                'id' => 'email',
                'return' => true,
            ),
        );
        $cs_html_fields->cs_text_field($cs_opt_array);

        $cs_opt_array = array(
            'name' => __('Name', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
            'desc' => '',
            'hint_text' => '',
            'echo' => true,
            'field_params' => array(
                'std' => get_post_meta($post->ID, 'cs_name', true),
                'id' => 'name',
                'return' => true,
            ),
        );
        $cs_html_fields->cs_text_field($cs_opt_array);
        $cs_opt_array = array(
            'name' => __('Query', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
            'desc' => '',
            'hint_text' => '',
            'echo' => true,
            'field_params' => array(
                'std' => get_post_meta($post->ID, 'cs_query', true),
                'id' => 'query',
                'return' => true,
            ),
        );
        $cs_html_fields->cs_text_field($cs_opt_array);

        $on_off_option = array('yes' => __("Yes", JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN), 'no' => __("No", JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN));
        $cs_opt_array = array(
            "name" => __("Annually", JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
            "desc" => "",
            "hint_text" => __("Do you want to allow user to set alert frequency to annually?", JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
            'echo' => true,
            "options" => $on_off_option,
            'field_params' => array(
                'std' => get_post_meta($post->ID, 'cs_frequency_annually', true),
                'id' => 'frequency_annually',
                'return' => true,
            ),
        );
        $cs_html_fields->cs_checkbox_field($cs_opt_array);
        $cs_opt_array = array(
            "name" => __("Bi-Annually", JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
            "desc" => "",
            "hint_text" => __("Do you want to allow user to set alert frequency to bi-annually?", JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
            'echo' => true,
            "options" => $on_off_option,
            'field_params' => array(
                'std' => get_post_meta($post->ID, 'cs_frequency_biannually', true),
                'id' => 'frequency_biannually',
                'return' => true,
            ),
        );
        $cs_html_fields->cs_checkbox_field($cs_opt_array);
        $cs_opt_array = array(
            "name" => __("Monthly", JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
            "desc" => "",
            "hint_text" => __("Do you want to allow user to set alert frequency to monthly?", JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
            'echo' => true,
            "options" => $on_off_option,
            'field_params' => array(
                'std' => get_post_meta($post->ID, 'cs_frequency_monthly', true),
                'id' => 'frequency_monthly',
                'return' => true,
            ),
        );
        $cs_html_fields->cs_checkbox_field($cs_opt_array);
        $cs_opt_array = array(
            "name" => __("Fortnightly", JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
            "desc" => "",
            "hint_text" => __("Do you want to allow user to set alert frequency to fortnight?", JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
            'echo' => true,
            "options" => $on_off_option,
            'field_params' => array(
                'std' => get_post_meta($post->ID, 'cs_frequency_fortnightly', true),
                'id' => 'frequency_fortnightly',
                'return' => true,
            ),
        );
        $cs_html_fields->cs_checkbox_field($cs_opt_array);
        $cs_opt_array = array(
            "name" => __("Weekly", JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
            "desc" => "",
            "hint_text" => __("Do you want to allow user to set alert frequency to weekly?", JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
            'echo' => true,
            "options" => $on_off_option,
            'field_params' => array(
                'std' => get_post_meta($post->ID, 'cs_frequency_weekly', true),
                'id' => 'frequency_weekly',
                'return' => true,
            ),
        );
        $cs_html_fields->cs_checkbox_field($cs_opt_array);
        $cs_opt_array = array(
            "name" => __("Daily", JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
            "desc" => "",
            "hint_text" => __("Do you want to allow user to set alert frequency to daily?", JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
            'echo' => true,
            "options" => $on_off_option,
            'field_params' => array(
                'std' => get_post_meta($post->ID, 'cs_frequency_daily', true),
                'id' => 'frequency_daily',
                'return' => true,
            ),
        );
        $cs_html_fields->cs_checkbox_field($cs_opt_array);
        $cs_opt_array = array(
            "name" => __("Never", JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
            "desc" => "",
            "hint_text" => __("Do you want to allow user to set alert frequency to Never?", JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN),
            'echo' => true,
            "options" => $on_off_option,
            'field_params' => array(
                'std' => get_post_meta($post->ID, 'cs_frequency_never', true),
                'id' => 'frequency_never',
                'return' => true,
            ),
        );
        $cs_html_fields->cs_checkbox_field($cs_opt_array);
    }

    public function create_job_alert_callback() {
        check_ajax_referer('jobhunt_create_job_alert', 'security');

        // Read data from user input.
        $email = sanitize_text_field($_POST['email']);
        $name = sanitize_text_field($_POST['name']);
        $location = sanitize_text_field($_POST['location']);
        $query = end(explode('?', $location));
        $frequency = sanitize_text_field($_POST['frequency']);
        $jobs_query = $_POST['query'];

        if (empty($name) || empty($email) || empty($query) || empty($frequency)) {
            $return = array('success' => false, "message" => __("Provided data is incomplete.", JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN));
        } else {
			$meta_query = array(
				array(
					'key' => 'cs_email',
					'value' => $email,
					'compare' => '=',
				),
				array(
					'key' => 'cs_frequency_' . $frequency,
					'value' => 'on',
					'compare' => '=',
				),
			);
			if ($jobs_query <> '') {
				$meta_query[] = array(
					'key' => 'cs_jobs_query',
					'value' => $jobs_query,
					'compare' => '=',
				);
			}
			$args = array(
				'post_type' => 'job-alert',
				'meta_query' => $meta_query,
			);
            $obj_query = new WP_Query($args);
            $count = $obj_query->post_count;
            if ($count > 0) {
                $return = array('success' => false, "message" => __("A job already exists with this criteria", JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN));
            } else {
                // Insert Job Alert as a post.
                $job_alert_data = array(
                    'post_title' => $name,
                    'post_status' => 'publish',
                    'post_type' => 'job-alert',
                    'comment_status' => 'closed',
                    'post_author' => get_current_user_id(),
                );
                $job_alert_id = wp_insert_post($job_alert_data);

                // Update email.
                update_post_meta($job_alert_id, 'cs_email', $email);
                // Update name.
                update_post_meta($job_alert_id, 'cs_name', $name);
                // Update frequencies.
                $frequencies = array(
                    'annually',
                    'biannually',
                    'monthly',
                    'fortnightly',
                    'weekly',
                    'daily',
                    'never',
                );
                $selected_frequencies = explode(',', $frequency);
                foreach ($selected_frequencies as $key => $frequency) {
                    if (in_array($frequency, $frequencies)) {
                        update_post_meta($job_alert_id, 'cs_frequency_' . $frequency, 'on');
                    }
                }
                // Update query.
                update_post_meta($job_alert_id, 'cs_query', $query);
                // Last time email sent.
                update_post_meta($job_alert_id, 'cs_last_time_email_sent', 0);

                // Query.
                update_post_meta($job_alert_id, 'cs_jobs_query', $jobs_query);

                $return = array('success' => true, "message" => __("Job alert successfully added.", JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN));
            }
        }
        echo json_encode($return);
        wp_die();
    }
    
    public function unsubscribe_job_alert() {
        $job_alert_id = sanitize_text_field( $_REQUEST['jaid'] );
        $post_data = get_post( $job_alert_id );
        if( $post_data ){
            wp_delete_post( $job_alert_id );
            echo '<div class="job_alert_unsubscribe_msg" style="text-align: center;"><h3>'. __( 'Job alert successfully unsubscribed.', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN) .'</h3></div>';
        }else{
            echo '<div class="job_alert_unsubscribe_msg" style="text-align: center;"><h3>'. __( 'Sorry! Job alert already unsubscribed.', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN) .'</h3></div>';
        }
        die();
    }
    
    public function remove_job_alert_callback() {
        $status = 0;
        $msg = '';
        if (isset($_POST['post_id'])) {
            wp_delete_post($_POST['post_id']);
            $status = 1;
            $msg = __("Job Alert Successfully deleted", JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN);
        } else {
            $msg = __("Provided data incomplete", JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN);
            $status = 0;
        }
        echo json_encode(array("msg" => $msg, 'status' => $status));
        wp_die();
    }

}

new WP_Job_Hunt_Custom_Post_Type();
