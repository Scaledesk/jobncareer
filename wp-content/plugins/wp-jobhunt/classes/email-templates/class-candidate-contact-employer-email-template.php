<?php

/**
 * File Type: Job Candidate Templates
 */
if (!class_exists('jobhunt_candidate_contact_email_template')) {

    class jobhunt_candidate_contact_email_template {

        public $email_template_type;
        public $email_default_template;
        public $email_template_variables;
        public $email_template_index;
        public $args;
        public $is_email_sent;
        public $template_group;
        public static $is_email_sent1;

        public function __construct() {

            $this->email_template_type = 'Candidate Contact Employer';
            $this->email_default_template = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0"/></head><body style="margin: 0; padding: 0;"><div style="background-color: #eeeeef; padding: 50px 0;"><table style="max-width: 640px;" border="0" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td style="padding: 40px 30px 30px 30px;" align="center" bgcolor="#33333e"><h1 style="color: #fff;">Candidate Contact Employer</h1></td></tr><tr><td bgcolor="#ffffff" style="padding: 40px 30px 40px 30px;"><table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td width="260" valign="top"><table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td>Name: [CANDIDATE_NAME]</td></tr><tr><td style="padding: 10px 0 0 0;">Email: [CANDIDATE_EMAIL]</td></tr><tr><td style="padding: 10px 0 0 0;">Phone: [CANDIDATE_PHONE]</td></tr><tr><td style="padding: 10px 0 0 0;">Message: [CANDIDATE_MESSAGE]</td></tr></table></td></tr></table></td></tr><tr><td style="background-color: #ffffff; padding: 30px 30px 30px 30px;"><table border="0" width="100%" cellspacing="0" cellpadding="0"><tbody><tr><td style="font-family: Arial, sans-serif; font-size: 14px;">&reg; [SITE_NAME], 2016</td></tr></tbody></table></td></tr></tbody></table></div></body></html>';

            $this->email_template_variables = array(
                array(
                    'tag' => 'CANDIDATE_NAME',
                    'display_text' => 'Name',
                    'value_callback' => array($this, 'get_candidate_name'),
                ),
                array(
                    'tag' => 'CANDIDATE_EMAIL',
                    'display_text' => 'Email',
                    'value_callback' => array($this, 'get_candidate_email'),
                ),
                array(
                    'tag' => 'CANDIDATE_PHONE',
                    'display_text' => 'Phone',
                    'value_callback' => array($this, 'get_candidate_phone'),
                ),
                array(
                    'tag' => 'CANDIDATE_MESSAGE',
                    'display_text' => 'Message',
                    'value_callback' => array($this, 'get_candidate_message'),
                ),
            );
            $this->template_group = 'Candidate';
            $this->email_template_index = 'candidate-email-template';

            add_filter('jobhunt_email_template_settings', array($this, 'template_settings_callback'), 12, 1);
            add_action('jobhunt_candidate_contact_email', array($this, 'candidate_contact_email_callback'), 10, 2);
            add_action('init', array($this, 'add_email_template'), 5);
            // Add options in Email Templates Addon
            // add_filter('cs_jobhunt_email_templates_options', array($this, 'email_templates_options_callback'), 10, 1);
        }

        public function candidate_contact_email_callback($args = '') {
            // Form args
            $this->name = isset($args['name']) ? $args['name'] : '';
            $this->email = isset($args['email']) ? $args['email'] : '';
            $this->phone = isset($args['phone']) ? $args['phone'] : '';
            $this->message = isset($args['message']) ? $args['message'] : '';
            $this->employer_email = isset($args['employer_email']) ? $args['employer_email'] : '';

            $template = $this->get_template();
            // checking email notification is enable/disable
            if (isset($template['email_notification']) && $template['email_notification'] == 1) {

                $blogname = get_option('blogname');
                $admin_email = get_option('admin_email');
                // getting template fields
                $subject = (isset($template['subject']) && $template['subject'] != '' ) ? $template['subject'] : __('Candidate Contact Employer', 'jobhunt');
                $from = (isset($template['from']) && $template['from'] != '') ? $template['from'] : esc_attr($blogname) . ' <' . $admin_email . '>';
                $recipients = (isset($template['recipients']) && $template['recipients'] != '') ? $template['recipients'] : $this->employer_email;
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
                jobhunt_candidate_contact_email_template::$is_email_sent1 = $this->is_email_sent;
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
                'description' => 'This template is used for cadidate contact to employer.',
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

        function get_candidate_name() {
            return $this->name;
        }

        function get_candidate_email() {
            return $this->email;
        }

        function get_candidate_phone() {
            return $this->phone;
        }

        function get_candidate_message() {
            return $this->message;
        }
        
        function get_employer_email() {
            return $this->args['employer_email'];
        }

//        public function email_templates_options_callback($cs_setting_options) {
//            $cs_setting_options[] = array(
//                "name" => __('Candidate Contact Employer', 'jobhunt'),
//                "desc" => '',
//                "hint_text" => '',
//                "id" => $this->email_template_index,
//                "std" => '',
//                'classes' => 'chosen-select-no-single',
//                "type" => 'select_values',
//                "options" => array(),
//                'email_template_type' => $this->email_template_type,
//            );
//            return $cs_setting_options;
//        }

    }

    new jobhunt_candidate_contact_email_template();
}
