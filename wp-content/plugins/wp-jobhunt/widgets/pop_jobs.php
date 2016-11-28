<?php
/**
 * @Recent posts widget Class
 *
 *
 */
if (!class_exists('pop_jobs')) {

    class pop_jobs extends WP_Widget {

        /**
         * @init Recent posts Module
         *
         *
         */
        public function __construct() {
            parent::__construct(
				'pop_jobs', // Base ID
				__('CS : Recent Jobs', 'jobhunt'), // Name
				array('classname' => 'widget-jobs', 'description' => __('Recent Jobs.', 'jobhunt'),) // Args
            );
        }

        /**
         * @Recent posts html form
         *
         *
         */
        function form($instance) {
            global $cs_theme_form_fields, $cs_html_fields;
            $instance = wp_parse_args((array) $instance, array('title' => ''));
            $title = $instance['title'];
            $showcount = isset($instance['showcount']) ? esc_attr($instance['showcount']) : '';
            $description = isset($instance['description']) ? esc_attr($instance['description']) : '';
            $button_title = isset($instance['button_title']) ? esc_attr($instance['button_title']) : '';
            $button_link = isset($instance['button_link']) ? esc_attr($instance['button_link']) : '';
            $cs_opt_array = array(
                'name' => __('Title', 'jobhunt'),
                'desc' => '',
                'hint_text' => '',
                'echo' => true,
                'field_params' => array(
                    'std' => esc_attr($title),
                    'id' => cs_allow_special_char($this->get_field_id('title')),
                    'classes' => '',
                    'cust_id' => cs_allow_special_char($this->get_field_name('title')),
                    'cust_name' => cs_allow_special_char($this->get_field_name('title')),
                    'return' => true,
                    'required' => false
                ),
            );
            echo $cs_html_fields->cs_text_field($cs_opt_array);
            $cs_opt_array = array(
                'name' => __('Number of Posts To Display', 'jobhunt'),
                'desc' => '',
                'hint_text' => '',
                'echo' => true,
                'field_params' => array(
                    'std' => esc_attr($showcount),
                    'id' => cs_allow_special_char($this->get_field_id('showcount')),
                    'classes' => '',
                    'cust_id' => cs_allow_special_char($this->get_field_name('showcount')),
                    'cust_name' => cs_allow_special_char($this->get_field_name('showcount')),
                    'return' => true,
                    'required' => false
                ),
            );
            echo $cs_html_fields->cs_text_field($cs_opt_array);
            $cs_opt_array = array(
                'name' => __('Description', 'jobhunt'),
                'desc' => '',
                'hint_text' => '',
                'echo' => true,
                'field_params' => array(
                    'std' => cs_allow_special_char($description),
                    'id' => cs_allow_special_char($this->get_field_id('description')),
                    'classes' => '',
                    'cust_id' => cs_allow_special_char($this->get_field_name('description')),
                    'cust_name' => cs_allow_special_char($this->get_field_name('description')),
                    'return' => true,
                    'required' => false
                ),
            );
            echo $cs_html_fields->cs_textarea_field($cs_opt_array);
            $cs_opt_array = array(
                'name' => __('Button Title', 'jobhunt'),
                'desc' => '',
                'hint_text' => '',
                'echo' => true,
                'field_params' => array(
                    'std' => esc_attr($button_title),
                    'id' => cs_allow_special_char($this->get_field_id('button_title')),
                    'classes' => '',
                    'cust_id' => cs_allow_special_char($this->get_field_name('button_title')),
                    'cust_name' => cs_allow_special_char($this->get_field_name('button_title')),
                    'return' => true,
                    'required' => false
                ),
            );
            echo $cs_html_fields->cs_text_field($cs_opt_array);
            $cs_opt_array = array(
                'name' => __('button Link', 'jobhunt'),
                'desc' => '',
                'hint_text' => '',
                'echo' => true,
                'field_params' => array(
                    'std' => esc_attr($button_link),
                    'id' => cs_allow_special_char($this->get_field_id('button_link')),
                    'classes' => '',
                    'cust_id' => cs_allow_special_char($this->get_field_name('button_link')),
                    'cust_name' => cs_allow_special_char($this->get_field_name('button_link')),
                    'return' => true,
                    'required' => false
                ),
            );
            echo $cs_html_fields->cs_text_field($cs_opt_array);
        }

        /**
         * @Recent posts update form data
         *
         *
         */
        function update($new_instance, $old_instance) {
            $instance = $old_instance;
            $instance['title'] = $new_instance['title'];
            $instance['showcount'] = $new_instance['showcount'];
            $instance['description'] = $new_instance['description'];
            $instance['button_title'] = $new_instance['button_title'];
            $instance['button_link'] = $new_instance['button_link'];
            return $instance;
        }

        /**
         * @Display Recent posts widget
         *
         *
         */
        function widget($args, $instance) {
            global $cs_node;
            extract($args, EXTR_SKIP);
            $title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
            $title = htmlspecialchars_decode(stripslashes($title));
            $showcount = empty($instance['showcount']) ? ' ' : apply_filters('widget_title', $instance['showcount']);
            $description = empty($instance['description']) ? '' : esc_attr($instance['description']);
            $button_title = empty($instance['button_title']) ? '' : esc_attr($instance['button_title']);
            $button_link = empty($instance['button_link']) ? '' : $instance['button_link'];


            global $wpdb, $post;

            if ($instance['showcount'] == "") {
                $instance['showcount'] = '-1';
            }
            echo '<div class="widget widget-jobs">';

            if (!empty($title) && $title <> ' ') {
                echo cs_allow_special_char($before_title);
                echo cs_allow_special_char($title);
                echo cs_allow_special_char($after_title);
            }
            if (isset($description) && $description <> "") {
                ?>
                <div class="job-promote cs-bgcolor">
                    <?php if (isset($description) && $description <> "") { ?> <h2><?php echo htmlspecialchars_decode($description); ?></h2><?php } ?>
                    <?php if (isset($button_title) && $button_title <> "") { ?> <a href="<?php echo esc_url($button_link); ?>"> <?php echo esc_attr($button_title); ?></a> <?php } ?> 
                </div>
            <?php } ?>    
            <ul class="cs-recent-jobs"> 
                <?php
                $cs_job_username = "";
                if (isset($select_category) and $select_category <> ' ' and $select_category <> '') {
                    $args = array('posts_per_page' => "$showcount", 'post_type' => 'jobs',);
                } else {
                    $args = array('posts_per_page' => "$showcount", 'post_type' => 'jobs');
                }
                $title_limit = 3;

                $custom_query = new WP_Query($args);
                if ($custom_query->have_posts() <> "") {
                    while ($custom_query->have_posts()) : $custom_query->the_post();
                        $cs_post_id = get_the_ID();
                        $cs_post_loc_address = get_post_meta($post->ID, "cs_post_loc_address", true);
                        $cs_job_employer = get_post_meta($post->ID, "cs_job_username", true); //
                        $cs_job_posted = get_post_meta($post->ID, 'cs_job_posted', true);

                        $cs_job_employer_data = cs_get_postmeta_data('cs_user', $cs_job_employer, '=', 'employer', true);
                        $employer_name = '';
                        if (isset($cs_job_employer_data)) {
                            foreach ($cs_job_employer_data as $cs_job_employer_single) {
                                $cs_jobs_address = get_user_address_string_for_list($cs_job_employer_single->ID);
                                $employer_name = $cs_job_employer_single->post_title;
                                $employer_name = ', by <a class="cs-color" href="' . get_permalink($cs_job_employer_single->ID) . '">' . $employer_name . '</a>';
                            }
                        }
                        // get all job types
                        $all_specialisms = get_the_terms($post->ID, 'specialisms');
                        $specialisms_values = '';
                        $specialisms_class = '';
                        $specialism_flag = 1;
                        if ($all_specialisms != '') {
                            foreach ($all_specialisms as $specialismsitem) {
                                $specialisms_values .= $specialismsitem->name;
                                $specialisms_class .= $specialismsitem->slug;
                                if ($specialism_flag != count($all_specialisms)) {
                                    $specialisms_values .= ", ";
                                    $specialisms_class .= " ";
                                }
                                $specialism_flag++;
                            }
                        }
                        ?>
                        <li>
                            <h5><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
                            <address><?php
                                if (isset($cs_jobs_address) && $cs_jobs_address <> '') {
                                    echo esc_html($cs_jobs_address) . ", ";
                                    ?><?php } ?><span class="cs-categories"> <?php echo esc_html($specialisms_values); ?></span></address> 
                                <?php if ($cs_job_posted <> '') {
                                    ?><span class="cs-post-date"><?php echo __('Posted', 'jobhunt') . " " . cs_time_elapsed_string($cs_job_posted) . " " . $employer_name; ?></span>
                                <?php } ?>
                        </li>
                        <?php
                    endwhile;
                } else {
                    if (function_exists('cs_fnc_no_result_found')) {
                        cs_fnc_no_result_found(false);
                    }
                }
                ?>
                <li><a class="cs-view-all" href="<?php echo get_post_type_archive_link('jobs'); ?>"><?php _e('View All Jobs', 'jobhunt'); ?></a></li>
            </ul>

            <?php
            echo '</div>';
        }

    }

}
add_action('widgets_init', create_function('', 'return register_widget("pop_jobs");'));