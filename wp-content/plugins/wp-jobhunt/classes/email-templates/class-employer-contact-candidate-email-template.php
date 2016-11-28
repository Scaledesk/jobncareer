<?php

/**
 * File Type: Employer Contact Candidate Email Templates
 */
if (!class_exists('jobhunt_employer_contact_candidate_email_template')) {

    class jobhunt_employer_contact_candidate_email_template {

        public $email_template_type;
        public $email_default_template;
        public $email_template_variables;
        public $email_template_index;
        public $args;
        public $template_group;
        public $is_email_sent;
        public static $is_email_sent1;

        public function __construct() {

            $this->email_template_type = 'Employer Contact Candidate';

            $this->email_default_template = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0"/></head><body style="margin: 0; padding: 0;"><div style="background-color: #eeeeef; padding: 50px 0;"><table style="max-width: 640px;" border="0" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td style="padding: 40px 30px 30px 30px;" align="center" bgcolor="#33333e"><h1 style="color: #fff;">Employer Contact Candidate</h1></td></tr><tr><td bgcolor="#ffffff" style="padding: 40px 30px 40px 30px;"><table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td width="260" valign="top"><table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td>Name: [EMPLOYER_NAME]</td></tr><tr><td style="padding: 10px 0 0 0;">Email: [EMPLOYER_EMAIL]</td></tr><tr><td style="padding: 10px 0 0 0;">Phone: [EMPLOYER_PHONE]</td></tr><tr><td style="padding: 10px 0 0 0;">Message: [EMPLOYER_MESSAGE]</td></tr></table></td></tr></table></td></tr><tr><td style="background-color: #ffffff; padding: 30px 30px 30px 30px;"><table border="0" width="100%" cellspacing="0" cellpadding="0"><tbody><tr><td style="font-family: Arial, sans-serif; font-size: 14px;">&reg; [SITE_NAME], 2016</td></tr></tbody></table></td></tr></tbody></table></div></body></html>';

            $this->email_template_variables = array(
                array(
                    'tag' => 'EMPLOYER_NAME',
                    'display_text' => 'Name',
                    'value_callback' => array($this, 'get_contact_name'),
                ),
                array(
                    'tag' => 'EMPLOYER_EMAIL',
                    'display_text' => 'Email',
                    'value_callback' => array($this, 'get_contact_email'),
                ),
                array(
                    'tag' => 'EMPLOYER_PHONE',
                    'display_text' => 'Phone',
                    'value_callback' => array($this, 'get_contact_phone'),
                ),
                array(
                    'tag' => 'EMPLOYER_MESSAGE',
                    'display_text' => 'Message',
                    'value_callback' => array($this, 'get_contact_message'),
                ),
            );
            $this->template_group = 'Employer';
            $this->email_template_index = 'employer-contact-candidate-template';

            add_filter('jobhunt_email_template_settings', array($this, 'template_settings_callback'), 12, 1);
            add_action('init', array($this, 'add_email_template'), 5);
            add_action('jobhunt_employer_contact_candidate', array($this, 'employer_contact_candidate_callback'), 10, 2);
            // add_filter('cs_jobhunt_email_templates_options', array($this, 'email_templates_options_callback'), 10, 1);
        }

        public function template_settings_callback($email_template_options) {

            $email_template_options["types"][] = $this->email_template_type;

            $email_template_options["templates"][$this->email_template_type] = $this->email_default_template;

            $email_template_options["variables"][$this->email_template_type] = $this->email_template_variables;

            return $email_template_options;
        }

        function get_contact_name() {
            $name = $this->args['name'];
            return $name;
        }

        function get_contact_email() {
            $email = $this->args['email'];
            return $email;
        }

        function get_contact_phone() {
            $phone = $this->args['phone'];
            return $phone;
        }

        function get_contact_message() {
            $message = $this->args['message'];
            return $message;
        }
        
        function get_candidate_email() {
            return $this->args['candidate_email'];
        }
        
        

        public function employer_contact_candidate_callback($param = '') {
            $this->args = $param;

            $template = $this->get_template();
            // checking email notification is enable/disable
            if (isset($template['email_notification']) && $template['email_notification'] == 1) {

                $blogname = get_option('blogname');
                $admin_email = get_option('admin_email');
                // getting template fields
                $subject = (isset($template['subject']) && $template['subject'] != '' ) ? $template['subject'] : __('Employer Contact Candidate', 'jobhunt');
                $from = (isset($template['from']) && $template['from'] != '') ? $template['from'] : esc_attr($blogname) . ' <' . $admin_email . '>';
                $recipients = (isset($template['recipients']) && $template['recipients'] != '') ? $template['recipients'] : $this->get_candidate_email();
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
                jobhunt_employer_contact_candidate_email_template::$is_email_sent1 = $this->is_email_sent;
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
                'description' => 'This template is used for sending email to candidate when employer conact to candidate',
                'jh_email_type' => 'html',
            );
            do_action('jobhunt_load_email_templates', $email_templates);
        }

        public function email_templates_options_callback($cs_setting_options) {

            $cs_setting_options[] = array(
                "name" => __('Employer Contact Candidate', 'jobhunt'),
                "desc" => '',
                "hint_text" => '',
                "id" => $this->email_template_index,
                "std" => '',
                'classes' => 'chosen-select-no-single',
                "type" => 'select_values',
                "options" => array(),
                'email_template_type' => $this->email_template_type,
            );

            return $cs_setting_options;
        }

        public function get_template() {
            return wp_jobhunt::get_template($this->email_template_index, $this->email_template_variables, $this->email_default_template);
        }

    }

    return new jobhunt_employer_contact_candidate_email_template();
}
