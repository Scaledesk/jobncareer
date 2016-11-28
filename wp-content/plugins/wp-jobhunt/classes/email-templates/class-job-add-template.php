<?php

/**
 * File Type: Job Add Email Templates
 */
if (!class_exists('jobhunt_job_add_email_template')) {

    class jobhunt_job_add_email_template {

        public $email_template_type;
        public $email_default_template;
        public $email_template_variables;
        public $template_type;
        public $email_template_index;
        public $user;
        public $job_id;
        public $is_email_sent;
        public static $is_email_sent1;
        public $template_group;

        public function __construct() {
            $this->user = array();
            $this->email_template_type = 'Job Add';

            $this->email_default_template = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0"/></head><body style="margin: 0; padding: 0;"><div style="background-color: #eeeeef; padding: 50px 0;"><table style="max-width: 640px;" border="0" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td style="padding: 40px 30px 30px 30px;" align="center" bgcolor="#33333e"><h1 style="color: #fff;">Job Add</h1></td></tr><tr><td bgcolor="#ffffff" style="padding: 40px 30px 40px 30px;"><table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td width="260" valign="top"><table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td>Hello [JOB_USER_NAME]! Your Job "[JOB_TITLE]" is added successfully.</td></tr></table></td></tr></table></td></tr><tr><td style="background-color: #ffffff; padding: 30px 30px 30px 30px;"><table border="0" width="100%" cellspacing="0" cellpadding="0"><tbody><tr><td style="font-family: Arial, sans-serif; font-size: 14px;">&reg; [SITE_NAME], 2016</td></tr></tbody></table></td></tr></tbody></table></div></body></html>';

            $this->email_template_variables = array(
                array(
                    'tag' => 'JOB_USER_NAME',
                    'display_text' => 'Job User',
                    'value_callback' => array($this, 'get_job_added_user_name'),
                ),
                array(
                    'tag' => 'JOB_TITLE',
                    'display_text' => 'Job Title',
                    'value_callback' => array($this, 'get_job_added_title'),
                ),
            );
            $this->template_group = 'Job';

            $this->email_template_index = 'job-added-template';
            add_action('init', array($this, 'add_email_template'), 5);
            add_filter('jobhunt_email_template_settings', array($this, 'template_settings_callback'), 12, 1);
            add_action('jobhunt_job_add_email', array($this, 'jobhunt_job_add_email_callback'), 10, 2);

            // Add options in Email Templates Addon
            // add_filter('cs_jobhunt_email_templates_options', array($this, 'email_templates_options_callback'), 10, 1);
        }

        public function jobhunt_job_add_email_callback($user = '', $job_id = '') {

            $this->user = $user;
            $this->job_id = $job_id;
            $template = $this->get_template();
            // checking email notification is enable/disable
            if (isset($template['email_notification']) && $template['email_notification'] == 1) {

                $blogname = get_option('blogname');
                $admin_email = get_option('admin_email');
                // getting template fields
                $subject = (isset($template['subject']) && $template['subject'] != '' ) ? $template['subject'] : __('Job Added', 'jobhunt');
                $from = (isset($template['from']) && $template['from'] != '') ? $template['from'] : esc_attr($blogname) . ' <' . $admin_email . '>';
                $recipients = (isset($template['recipients']) && $template['recipients'] != '') ? $template['recipients'] : $this->get_job_added_email();
                $email_type = (isset($template['email_type']) && $template['email_type'] != '') ? $template['email_type'] : 'html';

                $args = array(
                    'to' => $recipients,
                    'subject' => $subject,
                    'from' => $from,
                    'message' => $template['email_template'],
                    'email_type' => $email_type,
                    'class_obj' => $this,
                );
                do_action('jobhunt_send_mail', $args);
                jobhunt_job_add_email_template::$is_email_sent1 = $this->is_email_sent;
            }
        }

        public function add_email_template() {
            $email_templates = array();
            $email_templates[$this->template_group] = array();
            $email_templates[$this->template_group][$this->email_template_index] = array(
                'title' => $this->email_template_type,
                'template' => $this->email_default_template,
                'email_template_type' => $this->email_template_type,
                'is_recipients_enabled' => TRUE,
                'description' => __('This template is used to send email when new job add.', 'jobhunt'),
                'jh_email_type' => 'html',
            );
            do_action('jobhunt_load_email_templates', $email_templates);
        }

        public function template_settings_callback($email_template_options) {

            $email_template_options["types"][] = $this->email_template_type;

            $email_template_options["templates"][$this->email_template_type] = $this->email_default_template;

            $email_template_options["variables"][$this->email_template_type] = $this->email_template_variables;

            return $email_template_options;
        }

        public function get_template() {
            return wp_jobhunt::get_template($this->email_template_index, $this->email_template_variables, $this->email_default_template);
        }

        function get_job_added_user_name() {

            $user_name = $this->user->display_name;
            return $user_name;
        }

        function get_job_added_email() {

            $email = $this->user->user_email;
            return $email;
        }

        function get_job_added_title() {

            $title = get_the_title($this->job_id);
            return $title;
        }

//        public function email_templates_options_callback($cs_setting_options) {
//            $cs_setting_options[] = array(
//                "name" => __('Job Added', 'jobhunt'),
//                "desc" => '',
//                "hint_text" => '',
//                "id" => $this->email_template_index,
//                "std" => '',
//                'classes' => 'chosen-select-no-single',
//                "type" => 'select_values',
//                "options" => array(),
//                'email_template_type' => $this->email_template_type,
//            );
//
//            return $cs_setting_options;
//        }

    }

    new jobhunt_job_add_email_template();
}
