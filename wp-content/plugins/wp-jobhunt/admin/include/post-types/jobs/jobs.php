<?php

/**
 * File Type: Job Post Type
 */
if (!class_exists('post_type_job')) {

    class post_type_job {

        /**
         * Start Contructer Function
         */
        public function __construct() {
            add_action('init', array(&$this, 'cs_job_register'));
            add_filter('manage_jobs_posts_columns', array(&$this, 'cs_job_columns_add'));
            add_action('manage_jobs_posts_custom_column', array(&$this, 'cs_job_columns'), 10, 2);
            //add_action('the_content', array(&$this, 'cs_trim_content'), 10, 2);	
        }

        /**
         * Start Wp's Initilize action hook Function
         */
        public function cs_job_init() {
            // Initialize Post Type
            $this->cs_job_register();
        }

        /**
         * End Wp's Initilize action hook Function
         */
        public function cs_trim_content() {

            global $post;
            $read_more = '....';
            $the_content = get_the_content($post->ID);
            if (strlen(esc_html__(get_the_content($post->ID))) > 200) {
                $the_content = substr(esc_html__(get_the_content($post->ID)), 0, 200) . $read_more;
            }

            return $the_content;
        }

        /**
         * Start Function How to Register post type
         */
        public function cs_job_register() {
            $labels = array(
                'name' => __('Jobs', 'jobhunt'),
                'menu_name' => __('Jobs', 'jobhunt'),
                'add_new_item' => __('Add New Job', 'jobhunt'),
                'edit_item' => __('Edit Job', 'jobhunt'),
                'new_item' => __('New Job Item', 'jobhunt'),
                'add_new' => __('Add New Job', 'jobhunt'),
                'view_item' => __('View Job Item', 'jobhunt'),
                'search_items' => __('Search', 'jobhunt'),
                'not_found' => __('Nothing found', 'jobhunt'),
                'not_found_in_trash' => __('Nothing found in Trash', 'jobhunt'),
                'parent_item_colon' => ''
            );
            $args = array(
                'labels' => $labels,
                'public' => true,
                'exclude_from_search' => true,
                'publicly_queryable' => true,
                'show_ui' => true,
                'has_archive' => true,
                'query_var' => false,
                'menu_icon' => 'dashicons-admin-post',
                'rewrite' => true,
                'capability_type' => 'post',
                'hierarchical' => false,
                'menu_position' => null,
                
                'supports' => array('title', 'editor')
            );

            register_post_type('jobs', $args);
        }

        /**
         * End Function How to Register post type
         */

        /**
         * Start Function How to Add Title Columns
         */
        public function cs_job_columns_add($columns) {

            unset(
                    $columns['date']
            );

            $columns['company'] = __('Company', 'jobhunt');
            $columns['job_type'] = __('Job Type', 'jobhunt');
            $columns['specialisms'] = __('Specialisms', 'jobhunt');
            $columns['posted'] = __('Posted', 'jobhunt');
            $columns['expired'] = __('Expired', 'jobhunt');
            $columns['views'] = '<i class="icon-eye7"></i> / <i class="icon-thumbsup"></i> / <i class="icon-users"></i>';
            $columns['status'] = __('Status', 'jobhunt');

            return $columns;
        }

        /**
         * End Function How to Add Title Columns
         */

        /**
         * Start Function How to Add  Columns
         */
        public function cs_job_columns($name) {
            global $post, $gateway;

            switch ($name) {
                default:
                    //echo "name is " . $name;
                    break;
                case 'company':
                    $cs_job_employer = get_post_meta($post->ID, "cs_job_username", true); //
                    $cs_job_employer_data = cs_get_postmeta_data('cs_user', $cs_job_employer, '=', 'employer', true);
                    $employer_title = '';
                    if (isset($cs_job_employer_data)) {
                        foreach ($cs_job_employer_data as $cs_job_employer_single) {
                            $employer_title = get_the_title($cs_job_employer_single->ID);
                        }
                    }

                    $cs_user = get_userdata($cs_job_employer);
                    if (isset($cs_user->display_name)) {
                        echo $cs_user->display_name;
                    }

                    echo $employer_title;
                    break;
                case 'job_type':
                    $categories = get_the_terms($post->ID, 'job_type');
                    if ($categories <> "") {
                        $couter_comma = 0;
                        foreach ($categories as $category) {
                            echo esc_attr($category->name);
                            $couter_comma++;
                            if ($couter_comma < count($categories)) {
                                echo ", ";
                            }
                        }
                    }
                    break;
                case 'specialisms':

                    $categories = get_the_terms($post->ID, 'specialisms');
                    if ($categories <> "") {
                        $couter_comma = 0;
                        foreach ($categories as $category) {
                            echo esc_attr($category->name);
                            $couter_comma++;
                            if ($couter_comma < count($categories)) {
                                echo ", ";
                            }
                        }
                    }
                    break;
                case 'posted':

                    $cs_job_posted = get_post_meta($post->ID, 'cs_job_posted', true);
                    $cs_job_posted_date = isset($cs_job_posted) && $cs_job_posted != '' ? date_i18n('d/m/Y', ($cs_job_posted)) : '';
                    echo esc_html($cs_job_posted_date);
                    break;
                case 'expired':

                    $cs_job_expired = get_post_meta($post->ID, 'cs_job_expired', true);
                    $cs_job_expiry_date = isset($cs_job_expired) && $cs_job_expired != '' ? date_i18n('d/m/Y', ($cs_job_expired)) : '';
                    echo esc_html($cs_job_expiry_date);
                    break;
                case 'views':
                    $cs_views = get_post_meta($post->ID, "cs_count_views", true);
                    echo absint($cs_views);
                    echo ' / ';
                    $cs_shortlisted = count_usermeta('cs-jobs-wishlist', serialize(strval($post->ID)), 'LIKE');
                    echo absint($cs_shortlisted);
                    echo ' / ';
                    $applications = count_usermeta('cs-jobs-applied', serialize(strval($post->ID)), 'LIKE');
                    echo absint($applications);
                    break;
                case 'status':
                    echo get_post_meta($post->ID, 'cs_job_status', true);
                    break;
            }
        }

        /**
         * End Function How to Add  Columns
         */
        // End of class	
    }

    // Initialize Object
    $job_object = new post_type_job();
}