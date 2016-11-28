<?php

/**
 * File Type: Job Update Email Templates
 */
if (!class_exists('jobhunt_job_update_email_template')) {

    class jobhunt_job_update_email_template {

        public $email_template_type;
        public $email_default_template;
        public $email_template_variables;
        public $email_template_index;
        public $args;
        public $is_email_sent;
        public $template_group;

        public function __construct($args = array()) {

            $this->email_template_type = 'Job Update Email Template';

            $this->email_default_template = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0"/></head><body style="margin: 0; padding: 0;"><div style="background-color: #eeeeef; padding: 50px 0;"><table style="max-width: 640px;" border="0" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td style="padding: 40px 30px 30px 30px;" align="center" bgcolor="#33333e"><h1 style="color: #fff;">Job Update Email Template</h1></td></tr><tr><td bgcolor="#ffffff" style="padding: 40px 30px 40px 30px;"><table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td width="260" valign="top"><table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td>Hi [EMPLOYER_NAME],</td></tr><tr><td style="padding: 10px 0 0 0;">Your Job [JOB_ID_NUMBER] has been Updated!</td></tr></table></td></tr></table></td></tr><tr><td style="background-color: #ffffff; padding: 30px 30px 30px 30px;"><table border="0" width="100%" cellspacing="0" cellpadding="0"><tbody><tr><td style="font-family: Arial, sans-serif; font-size: 14px;">&reg; [SITE_NAME], 2016</td></tr></tbody></table></td></tr></tbody></table></div></body></html>';

            $this->args = $args;

            $this->email_template_variables = array(
                array(
                    'tag' => 'EMPLOYER_NAME',
                    'display_text' => 'Employer Name',
                    'value_callback' => array($this, 'get_employer_name'),
                ),
                array(
                    'tag' => 'JOB_ID_NUMBER',
                    'display_text' => 'Job Id Number',
                    'value_callback' => array($this, 'get_job_id_number'),
                ),
            );
            $this->template_group = 'Job';
            $this->email_template_index = 'job-update-email-template';

            add_filter('jobhunt_email_template_settings', array($this, 'template_settings_callback'), 12, 1);

            // add_filter('cs_jobhunt_email_templates_options', array($this, 'email_templates_options_callback'), 10, 1);

            add_action('jobhunt_job_updated_on_front', array($this, 'jobhunt_job_updated_callback'), 10, 1);

            add_action('jobhunt_job_updated_on_admin', array($this, 'jobhunt_job_updated_callback'), 10, 1);

            add_action('init', array($this, 'add_email_template'), 5);
        }

        public function template_settings_callback($email_template_options) {

            $email_template_options["types"][] = $this->email_template_type;

            $email_template_options["templates"][$this->email_template_type] = $this->email_default_template;

            $email_template_options["variables"][$this->email_template_type] = $this->email_template_variables;

            return $email_template_options;
        }

        function get_employer_name() {
            $name = $this->args['employer_name'];
            return $name;
        }

        function get_job_id_number() {
            $email = $this->args['job_id_number'];
            return $email;
        }

//        public function email_templates_options_callback($cs_setting_options) {
//
//            $cs_setting_options[] = array(
//                "name" => __('Job Update Email Template', 'jobhunt'),
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

        public function get_template() {
            return wp_jobhunt::get_template($this->email_template_index, $this->email_template_variables, $this->email_default_template);
        }

        public function jobhunt_job_updated_callback($job_id) {

            $job_id_number = get_post_meta($job_id, 'cs_job_id');
            $employer_id = get_post_meta($job_id, 'cs_job_username');
            $user = get_user_by('ID', $employer_id[0]);

            $form_array = array(
                'employer_name' => $user->display_name,
                'job_id_number' => $job_id_number[0],
            );

            $this->args = $form_array;

            $template = $this->get_template();
            if (isset($template['email_notification']) && $template['email_notification'] == 1) {
                $blogname = get_option('blogname');
                $admin_email = get_option('admin_email');
                $subject = (isset($template['subject']) && $template['subject'] != '' ) ? $template['subject'] : __("Your Job has been Updated!", "jobhunt");
                $from = (isset($template['from']) && $template['from'] != '') ? $template['from'] : esc_attr($blogname) . ' <' . $admin_email . '>';
                $recipients = (isset($template['recipients']) && $template['recipients'] != '') ? $template['recipients'] : $user->user_email;
                $email_type = (isset($template['email_type']) && $template['email_type'] != '') ? $template['email_type'] : 'html';

                $args = array(
                    'to' => $recipients,
                    'subject' => $subject,
                    'from' => $from,
                    'message' => $template['email_template'],
                    'email_type' => $email_type,
                );
                do_action('jobhunt_send_mail', $args);
            }
        }

        public function add_email_template() {
            $email_templates = array();
            $email_templates[$this->template_group] = array();
            $email_templates[$this->template_group][$this->email_template_index] = array(
                'title' => $this->email_template_type,
                'template' => $this->email_default_template,
                'email_template_type' => $this->email_template_type,
                'is_recipients_enabled' => true,
                'description' => __('Job update emails are sent to the employer when his/her posted job is updated','jobhunt'),
                'jh_email_type' => 'html',
            );
            do_action('jobhunt_load_email_templates', $email_templates);
        }

    }

    return new jobhunt_job_update_email_template();
}