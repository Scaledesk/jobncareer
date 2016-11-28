<?php

/**
 * File Type: Booking Templates
 */
if (!class_exists('cs_dashboard_templates')) {

    class cs_dashboard_templates {
        /**
         * Start construct Functions
         */
        public function __construct() {
            $this->templates = array();
              add_filter('page_attributes_dropdown_pages_args', array($this, 'dashboard_register_templates'));
              add_filter('wp_insert_post_data', array($this, 'dashboard_register_templates'));
              add_filter('template_include', array($this, 'dashboard_page_templates'));
              register_deactivation_hook(__FILE__, array($this, 'deactivate'));
              $this->templates = array(
                'page_employer.php' => __('Employer', 'jobhunt'),
                'page_candidate.php' => __('Candidate', 'jobhunt'),
            );
             $templates = wp_get_theme()->get_page_templates();
            $templates = array_merge($templates, $this->templates);            
        }
        /**
         * end construct Functions
         */
        /**
         * Adds our template to the pages cache in order to trick WordPress
         * into thinking the template file exists where it doens't really exist.
         */        
        /**
         * Start Function how to register template in dashboard
         */        
        public function dashboard_register_templates($atts) {
            $cache_key = 'page_templates-' . md5(get_theme_root() . '/' . get_stylesheet());
            $templates = wp_cache_get($cache_key, 'themes');
            if (empty($templates)) {
                $templates = array();
            } // end if
            wp_cache_delete($cache_key, 'themes');

            $templates = array_merge($templates, $this->templates);
            wp_cache_add($cache_key, $templates, 'themes', 1800);

            return $atts;
        }
        
        /**
         * End Function for to register template in dashboard
         */

// end dashboard_register_templates
       
        /**
         * Start Function if the templae page is assigned to the page funciton
         */
        
        public function dashboard_page_templates($template) {
            global $post;
            if (!isset($post))
                return $template;
            if (!isset($this->templates[get_post_meta($post->ID, '_wp_page_template', true)])) {
                return $template;
            }
            $file = plugin_dir_path(__FILE__) . get_post_meta($post->ID, '_wp_page_template', true);
            if (file_exists($file)) {
                return $file;
            }
            return $template;
        }
       /**
         * end Function for if the templae page is assigned to the page funciton
         */ 
       
        /**
         * Start Function for to deactivate the plugin
         */
        static function deactivate($network_wide) {
            foreach ($this as $value) {
                cs_delete_template($value);
            }
        }
        /**
         * end Function for to deactivate the plugin
         */
    }
    // end class
    // Initialize Object
    $cs_dashboard_templates = new cs_dashboard_templates();
}