<?php
/**
 * File Type: Plugin Functions
 */
if (!class_exists('cs_job_plugin_functions')) {

    class cs_job_plugin_functions {

        // The single instance of the class
        protected static $_instance = null;

        /**
         * Start construct Functions
         */
        public function __construct() {
            add_action('save_post', array($this, 'cs_save_post_option'), 11);
            add_action('create_specialisms', array($this, 'cs_save_jobs_spec_fields'));
            add_action('edited_specialisms', array($this, 'cs_save_jobs_spec_fields'));
            add_action('specialisms_edit_form_fields', array($this, 'cs_edit_jobs_spec_fields'));
            add_action('specialisms_add_form_fields', array($this, 'cs_jobs_spec_fields'));
            add_action('create_cs_locations', array($this, 'cs_save_jobs_locations_fields'));
            add_action('edited_cs_locations', array($this, 'cs_save_jobs_locations_fields'));
            add_action('cs_locations_edit_form_fields', array($this, 'cs_edit_jobs_locations_fields'));
            add_action('cs_locations_add_form_fields', array($this, 'cs_jobs_locations_fields'));
            add_action('create_job_type', array($this, 'cs_save_jobs_jobtype_fields'));
            add_action('edited_job_type', array($this, 'cs_save_jobs_jobtype_fields'));
            add_action('job_type_edit_form_fields', array($this, 'cs_edit_jobs_job_type_fields'));
            add_action('job_type_add_form_fields', array($this, 'cs_jobs_job_type_fields'));
            add_action('media_buttons', array($this, 'reg_shortcodes_btn'), 11);

            add_filter('manage_users_columns', array($this, 'cs_new_modify_user_table'));
            add_filter('manage_users_custom_column', array($this, 'cs_new_modify_user_table_row'), 10, 3);
            if (wp_is_mobile()) {
                add_action('wp_nav_menu_items', array($this, 'cs_login_header_item'), 30, 2);
            }
            if (!wp_is_mobile()) {
                add_filter('wp_nav_menu_items', array($this, 'cs_login_menu_item'), 10, 2);
            }
        }

        /**
         * End construct Functions
         * Start Creating  Instance of the Class Function
         */
        public static function instance() {
            if (is_null(self::$_instance)) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        public function cs_new_modify_user_table($column) {
            $column['display_name'] = 'Display Name';
            $column['jobs'] = 'Jobs';
            return $column;
        }

        public function cs_login_menu_item($items, $args) {

            global $post, $cs_plugin_options, $cs_theme_options;
            if (isset($cs_plugin_options['cs_user_dashboard_switchs']) && $cs_plugin_options['cs_user_dashboard_switchs'] == 'on') {
                $cs_menu_location = isset($cs_plugin_options['cs_menu_login_location']) ? $cs_plugin_options['cs_menu_login_location'] : '';
                if ($args->theme_location == $cs_menu_location) {
                    $cs_html = '';
                    $cs_user_dashboard_switchs = '';
                    if (isset($cs_plugin_options) && $cs_plugin_options != '') {
                        if (isset($cs_plugin_options['cs_user_dashboard_switchs'])) {
                            $cs_user_dashboard_switchs = $cs_plugin_options['cs_user_dashboard_switchs'];
                        }
                    }

                    $cs_emp_funs = new cs_employer_functions();
                    if (isset($cs_user_dashboard_switchs) and $cs_user_dashboard_switchs == "on") {

                        if (is_user_logged_in()) {

                            ob_start();
                            $cs_emp_funs->cs_header_favorites();
                            do_shortcode('[cs_user_login register_role="contributor"] [/cs_user_login]');
                            $cs_html .= ob_get_clean();
                        } else {
                            ob_start();
                            ?>
                            <div class="cs-loginsec">
                                <ul class="cs-drp-dwn">
                                    <li><?php echo do_shortcode('[cs_user_login register_role="contributor"] [/cs_user_login]') ?></li>
                                </ul>
                            </div>
                            <?php
                            $cs_html .= ob_get_clean();
                        }
                    } else {
                        ob_start();
                        echo do_shortcode('[cs_user_login register_role="contributor"] [/cs_user_login]');
                        if (is_user_logged_in() && !$cs_emp_funs->is_employer()) {    // only for candidate
                            if (candidate_header_wishlist() != '') {

                                echo ' <div class="wish-list" id="top-wishlist-content">';
                                echo candidate_header_wishlist();
                                echo '</div>';
                            }
                        }
                        $cs_html .= ob_get_clean();
                    }

                    $items .= '<li class="cs-login-area">' . $cs_html . '</li>';
                }
            }
            return $items;
        }

        public function cs_login_header_item($items, $args) {

            global $post, $cs_plugin_options, $cs_theme_options;
            if (isset($cs_plugin_options['cs_user_dashboard_switchs']) && $cs_plugin_options['cs_user_dashboard_switchs'] == 'on') {
                $cs_menu_location = isset($cs_plugin_options['cs_menu_login_location']) ? $cs_plugin_options['cs_menu_login_location'] : '';
                $cs_html = '';
                if ($args->theme_location == $cs_menu_location) {

                    $cs_user_dashboard_switchs = '';
                    if (isset($cs_plugin_options) && $cs_plugin_options != '') {
                        if (isset($cs_plugin_options['cs_user_dashboard_switchs'])) {
                            $cs_user_dashboard_switchs = $cs_plugin_options['cs_user_dashboard_switchs'];
                        }
                    }

                    $cs_emp_funs = new cs_employer_functions();
                    if (isset($cs_user_dashboard_switchs) and $cs_user_dashboard_switchs == "on") {

                        if (is_user_logged_in()) {

                            ob_start();
                            $cs_emp_funs->cs_header_favorites();
                            do_shortcode('[cs_user_login register_role="contributor"] [/cs_user_login]');
                            $cs_html .= ob_get_clean();
                        } else {
                            ob_start();
                            ?>
                            <div class="cs-loginsec">
                                <ul class="cs-drp-dwn">
                                    <li><?php echo do_shortcode('[cs_user_login register_role="contributor"] [/cs_user_login]') ?></li>
                                </ul>
                            </div>
                            <?php
                            $cs_html .= ob_get_clean();
                        }
                    } else {
                        ob_start();
                        echo do_shortcode('[cs_user_login register_role="contributor"] [/cs_user_login]');
                        if (is_user_logged_in() && !$cs_emp_funs->is_employer()) {    // only for candidate
                            if (candidate_header_wishlist() != '') {

                                echo '<div class="wish-list" id="top-wishlist-content">';
                                echo candidate_header_wishlist();
                                echo '</div>';
                            }
                        }
                        $cs_html .= ob_get_clean();
                    }
                }
                echo $cs_html;
            }
            return $items;
        }

        public function cs_new_modify_user_table_row($val, $column_name, $user_id) {
            $user = get_userdata($user_id);
            switch ($column_name) {
                case 'display_name' :
                    $cs_user = get_userdata($user_id);
                    $return = $cs_user->display_name;
                    break;
                case 'jobs' :
                    $cs_user = get_userdata($user_id);
                    $args = array(
                        'post_type' => 'jobs',
                        'post_status' => 'publish',
                        'posts_per_page' => -1,
                        'meta_query' => array(
                            array(
                                'key' => 'cs_job_username',
                                'value' => $user_id,
                                'compare' => '=',
                            ),
                        ),
                    );

                    $query = new WP_Query($args);

                    $author_posts_link = admin_url('edit.php?author=' . $user_id . '&post_type=jobs');

                    if ($query->found_posts > 0) {
                        $return = '<a href="' . $author_posts_link . '">' . $query->found_posts . '</a>';
                    } else {
                        $return = $query->found_posts;
                    }
                    break;
                default:
            }
            return $return;
        }

        /**
         * End Creating  Instance Main Fuunctions
         * Start Saving Post  options Function
         */
        public function cs_save_post_option($post_id = '') {
            global $post, $cs_plugin_options;
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                return;
            }
            $data = array();
            
            $cs_user_cv = cs_user_cv();
            if (isset($cs_user_cv) and $cs_user_cv <> '') {
                update_post_meta($post_id, 'cs_candidate_cv', $cs_user_cv);
                $_POST['cs_candidate_cv'] = $cs_user_cv;
            }
            foreach ($_POST as $key => $value) {
                if (strstr($key, 'cs_')) {
                    if($key == 'cs_post_comp_address') {
                        continue;
                    }
                    if ($key == 'cs_transaction_expiry_date' || $key == 'cs_job_expired' || $key == 'cs_job_posted' || $key == 'cs_user_last_activity_date' || $key == 'cs_user_last_activity_date') {
                        if ($key == 'cs_user_last_activity_date' && $value == '' || $key == 'cs_user_last_activity_date') {
                            $value = date('d-m-Y H:i:s');
                        }
                        $data[$key] = strtotime($value);
                        update_post_meta($post_id, $key, strtotime($value));
                    } else {
                        if ($key == 'cs_cus_field' && is_admin() ) {
                            if (is_array($value) && sizeof($value) > 0) {
                                foreach ($value as $c_key => $c_val) {
                                    update_post_meta($post_id, $c_key, $c_val);
                                }
                            }
                        } else {
                            if ($key == 'cs_job_featured') {
                                if (is_admin()) {
                                    $data[$key] = $value;
                                    update_post_meta($post_id, $key, $value);
                                }
                            } else {
                                $data[$key] = $value;
                                update_post_meta($post_id, $key, $value);
                            }
                        }
                    }
                }
                if ($key == 'job_img' || $key == 'user_img' || $key == 'cover_user_img') {
                    update_post_meta($post_id, $key, cs_save_img_url($value));
                }
            }
            update_post_meta($post_id, 'cs_array_data', $data);
            do_action('job_hunt_update_application_deadline_field', $post_id, $_POST);
            if( isset($post) && $post->post_type == 'jobs' ){
                do_action('jobhunt_job_updated_on_admin',$post_id);
                
                //job status approved/not approved email hook
                $job_old_status = isset($_POST['cs_job_old_status']) ? $_POST['cs_job_old_status'] : '';
                do_action( 'jobhunt_job_status_changed', $post_id, $job_old_status );
            }
        }

        /**
         * End Saving Post  options Function
         * Start Insert Shortcode Function
         */
        public function reg_shortcodes_btn() {
            global $cs_form_fields2;
            $cs_rand = rand(2342344, 95676556);
            $shortcode_array = array();
            $shortcode_array['cv_package'] = array(
                'title' => __('CV Package', 'jobhunt'),
                'name' => 'cv_package',
                'icon' => 'icon-table',
                'categories' => 'loops misc',
            );
            $shortcode_array['job_package'] = array(
                'title' => __('Job Package', 'jobhunt'),
                'name' => 'job_package',
                'icon' => 'icon-table',
                'categories' => 'loops misc',
            );
            $shortcode_array['job_post'] = array(
                'title' => __('Job Post', 'jobhunt'),
                'name' => 'job_post',
                'icon' => 'icon-table',
                'categories' => 'loops misc',
            );
            $shortcode_array['job_specialisms'] = array(
                'title' => __('Job Specialisms', 'jobhunt'),
                'name' => 'job_specialisms',
                'icon' => 'icon-table',
                'categories' => 'loops misc',
            );
            $shortcode_array['jobs_search'] = array(
                'title' => __('Job Search', 'jobhunt'),
                'name' => 'jobs_search',
                'icon' => 'icon-table',
                'categories' => 'loops misc',
            );
            $shortcode_array['candidate'] = array(
                'title' => __('Candidate', 'jobhunt'),
                'name' => 'candidate',
                'icon' => 'icon-home',
                'categories' => 'loops misc',
            );
            $shortcode_array['employer'] = array(
                'title' => __('Employer', 'jobhunt'),
                'name' => 'employer',
                'icon' => 'icon-home',
                'categories' => 'loops misc',
            );
            $shortcode_array['employer'] = array(
                'title' => __('Employer', 'jobhunt'),
                'name' => 'employer',
                'icon' => 'icon-home',
                'categories' => 'loops misc',
            );
            $shortcode_array['jobs'] = array(
                'title' => __('Jobs', 'jobhunt'),
                'name' => 'jobs',
                'icon' => 'icon-home',
                'categories' => 'loops misc',
            );
            $shortcode_array['register'] = array(
                'title' => __('Register', 'jobhunt'),
                'name' => 'register',
                'icon' => 'icon-home',
                'categories' => 'loops misc',
            );

            $cs_shortcodes_list_option = '';
            $cs_shortcodes_list_option[] = "Shortcode";
            $my_theme = wp_get_theme();
            if ($my_theme->name != 'JobCareer') {
                foreach ($shortcode_array as $val) {
                    $cs_shortcodes_list_option[$val['name']] = $val['title'];
                }

                $cs_opt_array = array(
                    'id' => '',
                    'std' => __("Browse", 'jobhunt'),
                    'cust_id' => '',
                    'cust_name' => '',
                    'classes' => 'sc_select chosen-select select-small',
                    'return' => true,
                    'options' => $cs_shortcodes_list_option,
                    'extra_atr' => "onchange=\"cs_shortocde_selection(this.value,'" . admin_url('admin-ajax.php') . "','composer-" . absint($cs_rand) . "')\"",
                );
                $cs_shortcodes_list = $cs_form_fields2->cs_form_select_render($cs_opt_array);

                $cs_shortcodes_list .= '<span id="cs-shrtcode-loader"></span>';

                echo force_balance_tags($cs_shortcodes_list);
            }
        }

        /**
         * End Insert Shortcode Function
         * Start Special Characters Function
         */
        public function cs_special_chars($input = '') {
            $output = $input; // output line 
            return $output;
        }

        /**
         * End Special Characters Function
         * Start Regular Expression  Text Function
         */
        public function cs_slugy_text($str) {
            $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $str);
            $clean = strtolower(trim($clean, '_'));
            $clean = preg_replace("/[\/_|+ -]+/", '_', $clean);
            return $clean;
        }

        /**
         * End Regular Expression  Text Function
         * Start  Creating  Random Id Function
         */
        public function cs_rand_id() {
            $output = rand(12345678, 98765432);
            return $output;
        }

        /**
         * End  Creating  Random Id Function
         * Start Advance Deposit Function
         */
        public function cs_percent_return($num) {
            if (is_numeric($num) && $num > 0 && $num <= 100) {
                $num = $num;
            } else if (is_numeric($num) && $num > 0 && $num > 100) {
                $num = 100;
            } else {
                $num = 0;
            }

            return $num;
        }

        /**
         * Number Format Function
         * Function how to get  attachment image src 
         */
        public function cs_num_format($num) {
            $cs_number = number_format((float) $num, 2, '.', '');
            return $cs_number;
        }

        public function cs_attach_image_src($attachment_id, $width, $height) {
            $image_url = wp_get_attachment_image_src($attachment_id, array($width, $height), true);
            if ($image_url[1] == $width and $image_url[2] == $height)
                ;
            else
                $image_url = wp_get_attachment_image_src($attachment_id, "full", true);
            $parts = explode('/uploads/', $image_url[0]);
            if (count($parts) > 1)
                return $image_url[0];
        }

        /**
         *  End How to get first image from gallery and its image src Function
         * Get post Id Through meta key Fundtion
         */
        public function cs_get_post_id_by_meta_key($key, $value) {
            global $wpdb;
            $meta = $wpdb->get_results("SELECT * FROM `" . $wpdb->postmeta . "` WHERE meta_key='" . $key . "' AND meta_value='" . $value . "'");

            if (is_array($meta) && !empty($meta) && isset($meta[0])) {
                $meta = $meta[0];
            }
            if (is_object($meta)) {
                return $meta->post_id;
            } else {
                return false;
            }
        }

        /**
         *  end Get post Id Through meta key Fundtion
         * Start Show All Taxonomy(categories) Function
         */
        public function cs_show_all_cats($parent, $separator, $selected = "", $taxonomy) {

            if ($parent == "") {
                global $wpdb;
                $parent = 0;
            } else
                $separator .= " &ndash; ";
            $args = array(
                'parent' => $parent,
                'hide_empty' => 0,
                'taxonomy' => $taxonomy
            );
            $categories = get_categories($args);
            
            foreach ($categories as $category) {
                ?>
                <option <?php if ($selected == $category->slug) echo "selected"; ?> value="<?php echo esc_attr($category->slug); ?>"><?php echo esc_attr($separator . $category->cat_name); ?></option>
                <?php
                cs_show_all_cats($category->term_id, $separator, $selected, $taxonomy);
            }
        }

        /**
         *  End Show All Taxonomy(categories) Function
         *  Start how to icomoon get
         */
        public function cs_icomoons($icon_value = '', $id = '', $name = '') {
            global $cs_form_fields2;
            ob_start();
            ?>
            <script>
                jQuery(document).ready(function ($) {

                    var e9_element = $('#e9_element_<?php echo cs_allow_special_char($id); ?>').fontIconPicker({
                        theme: 'fip-bootstrap'
                    });
                    // Add the event on the button
                    $('#e9_buttons_<?php echo cs_allow_special_char($id); ?> button').on('click', function (e) {
                        e.preventDefault();
                        // Show processing message
                        $(this).prop('disabled', true).html('<i class="icon-cog demo-animate-spin"></i><?php _e('Please wait...','jobhunt'); ?>');
                        $.ajax({
                            url: '<?php echo wp_jobhunt::plugin_url(); ?>/assets/icomoon/js/selection.json',
                            type: 'GET',
                            dataType: 'json'
                        })
                                .done(function (response) {
                                    // Get the class prefix
                                    var classPrefix = response.preferences.fontPref.prefix,
                                            icomoon_json_icons = [],
                                            icomoon_json_search = [];
                                    $.each(response.icons, function (i, v) {
                                        icomoon_json_icons.push(classPrefix + v.properties.name);
                                        if (v.icon && v.icon.tags && v.icon.tags.length) {
                                            icomoon_json_search.push(v.properties.name + ' ' + v.icon.tags.join(' '));
                                        } else {
                                            icomoon_json_search.push(v.properties.name);
                                        }
                                    });
                                    // Set new fonts on fontIconPicker
                                    e9_element.setIcons(icomoon_json_icons, icomoon_json_search);
                                    // Show success message and disable
                                    $('#e9_buttons_<?php echo cs_allow_special_char($id); ?> button').removeClass('btn-primary').addClass('btn-success').text(<?php _e('Successfully loaded icons', 'jobhunt'); ?>).prop('disabled', true);
                                })
                                .fail(function () {
                                    // Show error message and enable
                                    $('#e9_buttons_<?php echo cs_allow_special_char($id); ?> button').removeClass('btn-primary').addClass('btn-danger').text(<?php _e('Error: Try Again?', 'jobhunt'); ?>).prop('disabled', false);
                                });
                        e.stopPropagation();
                    });

                    jQuery("#e9_buttons_<?php echo cs_allow_special_char($id); ?> button").click();
                });


            </script>
            <?php
            $cs_opt_array = array(
                'id' => '',
                'std' => cs_allow_special_char($icon_value),
                'cust_id' => "e9_element_" . cs_allow_special_char($id),
                'cust_name' => cs_allow_special_char($name) . "[]",
                'return' => true,
            );

            echo $cs_form_fields2->cs_form_text_render($cs_opt_array);
            ?>
            <span id="e9_buttons_<?php echo cs_allow_special_char($id); ?>" style="display:none">
                <button autocomplete="off" type="button" class="btn btn-primary"><?php echo __('Load from IcoMoon selection.json', 'jobhunt'); ?></button>
            </span>
            <?php
            $fontawesome = ob_get_clean();
            return $fontawesome;
        }

        /**
         * @ render Random ID
         * Start Get Current  user ID Function
         *
         */
        public static function cs_generate_random_string($length = 3) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, strlen($characters) - 1)];
            }
            return $randomString;
        }

        public function cs_get_user_id() {
            global $current_user;
            wp_get_current_user();
            return $current_user->ID;
        }

        /**
         * End Current get user ID Function
         * How to create location Fields(fields) Function
         */
        public function cs_location_fields($user = '') {

            global $cs_plugin_options, $post, $cs_html_fields, $cs_form_fields2;
            $cs_map_latitude = isset($cs_plugin_options['map_latitude']) ? $cs_plugin_options['map_latitude'] : '';
            $cs_map_longitude = isset($cs_plugin_options['map_longitude']) ? $cs_plugin_options['map_longitude'] : '';
            $cs_map_zoom = isset($cs_plugin_options['map_zoom']) ? $cs_plugin_options['map_zoom'] : '2';
            $cs_array_data = '';
            if (isset($user) && !empty($user)) { // get values from usermeta
                $cs_array_data = get_the_author_meta('cs_array_data', $user->ID);
                if (isset($cs_array_data) && !empty($cs_array_data)) {
                    $cs_post_loc_city = get_the_author_meta('cs_post_loc_city', $user->ID);
                    $cs_post_loc_country = get_the_author_meta('cs_post_loc_country', $user->ID);
                    $cs_post_loc_latitude = get_the_author_meta('cs_post_loc_latitude', $user->ID);
                    $cs_post_loc_longitude = get_the_author_meta('cs_post_loc_longitude', $user->ID);
                    $cs_post_loc_zoom = get_the_author_meta('cs_post_loc_zoom', $user->ID);
                    $cs_post_loc_address = get_the_author_meta('cs_post_loc_address', $user->ID);
                    $cs_post_comp_address = get_the_author_meta('cs_post_comp_address', $user->ID);
                    $cs_add_new_loc = get_the_author_meta('cs_add_new_loc', $user->ID);
                } else {
                    $cs_post_loc_country = '';
                    $cs_post_loc_region = '';
                    $cs_post_loc_city = '';
                    $cs_post_loc_address = '';
                    $cs_post_loc_latitude = isset($cs_plugin_options['cs_post_loc_latitude']) ? $cs_plugin_options['cs_post_loc_latitude'] : '';
                    $cs_post_loc_longitude = isset($cs_plugin_options['cs_post_loc_longitude']) ? $cs_plugin_options['cs_post_loc_longitude'] : '';
                    $cs_post_loc_zoom = isset($cs_plugin_options['cs_post_loc_zoom']) ? $cs_plugin_options['cs_post_loc_zoom'] : '';
                    $loc_city = '';
                    $loc_postcode = '';
                    $loc_region = '';
                    $loc_country = '';
                    $event_map_switch = '';
                    $event_map_heading = '';
                    $cs_add_new_loc = '';
                    $cs_post_comp_address = '';
                }
            } else {  // get values from postmeta
                $cs_array_data = get_post_meta($post->ID, 'cs_array_data', true);
                if (isset($cs_array_data) && !empty($cs_array_data)) {
                    $cs_post_loc_city = get_post_meta($post->ID, 'cs_post_loc_city', true);
                    $cs_post_loc_country = get_post_meta($post->ID, 'cs_post_loc_country', true);
                    $cs_post_loc_latitude = get_post_meta($post->ID, 'cs_post_loc_latitude', true);
                    $cs_post_loc_longitude = get_post_meta($post->ID, 'cs_post_loc_longitude', true);
                    $cs_post_loc_zoom = get_post_meta($post->ID, 'cs_post_loc_zoom', true);
                    $cs_post_loc_address = get_post_meta($post->ID, 'cs_post_loc_address', true);
                    $cs_post_comp_address = get_post_meta($post->ID, 'cs_post_comp_address', true);
                    $cs_add_new_loc = get_post_meta($post->ID, 'cs_add_new_loc', true);
                } else {
                    $cs_post_loc_country = '';
                    $cs_post_loc_region = '';
                    $cs_post_loc_city = '';
                    $cs_post_loc_address = '';
                    $cs_post_loc_latitude = isset($cs_plugin_options['cs_post_loc_latitude']) ? $cs_plugin_options['cs_post_loc_latitude'] : '';
                    $cs_post_loc_longitude = isset($cs_plugin_options['cs_post_loc_longitude']) ? $cs_plugin_options['cs_post_loc_longitude'] : '';
                    $cs_post_loc_zoom = isset($cs_plugin_options['cs_post_loc_zoom']) ? $cs_plugin_options['cs_post_loc_zoom'] : '';
                    $loc_city = '';
                    $loc_postcode = '';
                    $loc_region = '';
                    $loc_country = '';
                    $event_map_switch = '';
                    $event_map_heading = '';
                    $cs_add_new_loc = '';
                    $cs_post_comp_address = '';
                }
            }
            if ($cs_post_loc_latitude == '')
                $cs_post_loc_latitude = $cs_map_latitude;
            if ($cs_post_loc_longitude == '')
                $cs_post_loc_longitude = $cs_map_longitude;
            if ($cs_post_loc_zoom == '')
                $cs_post_loc_zoom = $cs_map_zoom;
            $cs_jobhunt = new wp_jobhunt();

            $cs_jobhunt->cs_location_gmap_script();
            $cs_jobhunt->cs_google_place_scripts();
            $cs_jobhunt->cs_autocomplete_scripts();

            /**
             * How to get countries againts location Function Start
             *
             */
            $locations_parent_id = 0;
            $country_args = array(
                'orderby' => 'name',
                'order' => 'ASC',
                'fields' => 'all',
                'slug' => '',
                'hide_empty' => false,
                'parent' => $locations_parent_id,
            );
            $cs_location_countries = get_terms('cs_locations', $country_args);
            $location_countries_list = '';
            $location_states_list = '';
            $location_cities_list = '';
            $iso_code_list_main = '';
            $iso_code_list_admin = '';
            $iso_code = '';
            $iso_code_list = '';
            if (isset($cs_location_countries) && !empty($cs_location_countries)) {
                $selected_iso_code = '';
                foreach ($cs_location_countries as $key => $country) {
                    $selected = '';
                    $t_id_main = $country->term_id;
                    $iso_code_list_main = get_option("iso_code_$t_id_main");
                    if (isset($iso_code_list_main['text'])) {
                        $iso_code_list_admin = $iso_code_list_main['text'];
                    }
                    if (isset($cs_post_loc_country) && $cs_post_loc_country == $country->slug) {
                        $selected = 'selected';
                        $t_id = $country->term_id;
                        $iso_code_list = get_option("iso_code_$t_id");
                        if (isset($iso_code_list['text'])) {
                            $selected_iso_code = $iso_code_list['text'];
                        }
                    }
                    $location_countries_list .= "<option " . $selected . "  value='" . $country->slug . "' data-name='" . $iso_code_list_admin . "'>" . $country->name . "</option>";
                }
            }
            $selected_country = $cs_post_loc_country;
            $selected_city = $cs_post_loc_city;
            if (isset($cs_location_countries) && !empty($cs_location_countries) && isset($cs_post_loc_country) && !empty($cs_post_loc_country)) {
                // load all cities against state  
                $cities = '';
                $selected_spec = get_term_by('slug', $selected_country, 'cs_locations');
                if (isset($selected_spec->term_id)) {
                    $state_parent_id = $selected_spec->term_id;
                } else {
                    $state_parent_id = '';
                }
                $states_args = array(
                    'orderby' => 'name',
                    'order' => 'ASC',
                    'fields' => 'all',
                    'slug' => '',
                    'hide_empty' => false,
                    'parent' => $state_parent_id,
                );
                $cities = get_terms('cs_locations', $states_args, false, 'it');

                if (isset($cities) && $cities != '' && is_array($cities)) {
                    foreach ($cities as $key => $city) {
                        $selected = ( $selected_city == $city->slug) ? 'selected' : '';
                        $location_cities_list .= "<option " . $selected . " value='" . $city->slug . "'>" . $city->name . "</option>";
                    }
                }
            }
            ?>
            <fieldset class="gllpLatlonPicker"  style="width:100%; float:left;">
                <div class="page-wrap page-opts left" style="overflow:hidden; position:relative;" id="locations_wrap" data-themeurl="<?php echo wp_jobhunt::plugin_url(); ?>" data-plugin_url="<?php echo wp_jobhunt::plugin_url(); ?>" data-ajaxurl="<?php echo esc_js(admin_url('admin-ajax.php'), 'jobhunt'); ?>" data-map_marker="<?php echo wp_jobhunt::plugin_url(); ?>/assets/images/map-marker.png">
                    <div class="option-sec" style="margin-bottom:0;">
                        <div class="opt-conts">

                            <?php
                            $output = '';
                            $cs_opt_array = array(
                                'name' => __('Country', 'jobhunt'),
                                'desc' => '',
                                'field_params' => array(
                                    'std' => '',
                                    'id' => 'loc_country',
                                    'cust_id' => 'loc_country',
                                    'cust_name' => 'cs_post_loc_country',
                                    'classes' => 'chosen-select form-select-country dir-map-search single-select SlectBox',
                                    'options_markup' => true,
                                    'return' => true,
                                ),
                            );
                            if (isset($value['contry_hint']) && $value['contry_hint'] != '') {
                                $cs_opt_array['hint_text'] = $value['contry_hint'];
                            }

                            if (isset($location_countries_list) && $location_countries_list != '') {
                                $cs_opt_array['field_params']['options'] = '<option value="">' . __('Select Country', 'jobhunt') . '</option>' . $location_countries_list;
                            } else {
                                $cs_opt_array['field_params']['options'] = '<option value="">' . __('Select Country', 'jobhunt') . '</option>';
                            }

                            if (isset($value['split']) && $value['split'] <> '') {
                                $cs_opt_array['split'] = $value['split'];
                            }

                            $output .= $cs_html_fields->cs_select_field($cs_opt_array);

                            $cs_opt_array = array(
                                'name' => __('City', 'jobhunt'),
                                'id' => 'loc_city',
                                'desc' => '',
                                'field_params' => array(
                                    'std' => '',
                                    'id' => 'loc_city',
                                    'cust_id' => 'loc_city',
                                    'cust_name' => 'cs_post_loc_city',
                                    'classes' => 'chosen-select form-select-city dir-map-search single-select',
                                    'markup' => '<span class="loader-cities"></span>',
                                    'options_markup' => true,
                                    'return' => true,
                                ),
                            );
                            if (isset($value['city_hint']) && $value['city_hint'] != '') {
                                $cs_opt_array['hint_text'] = $value['city_hint'];
                            }
                            if (isset($location_cities_list) && $location_cities_list != '') {
                                $cs_opt_array['field_params']['options'] = '<option value="">' . __('Select City', 'jobhunt') . '</option>' . $location_cities_list;
                            } else {
                                $cs_opt_array['field_params']['options'] = '<option value="">' . __('Select City', 'jobhunt') . '</option>';
                            }
                            if (isset($value['split']) && $value['split'] <> '') {
                                $cs_opt_array['split'] = $value['split'];
                            }

                            $output .= $cs_html_fields->cs_select_field($cs_opt_array);

                            $cs_opt_array = array(
                                'name' => __('Complete Address', 'jobhunt'),
                                'desc' => '',
                                'hint_text' => __('Enter you complete address with city, state or country.', 'jobhunt'),
                                'field_params' => array(
                                    'std' => $cs_post_comp_address,
                                    'id' => 'complete_address',
                                    'cust_id' => 'complete_address',
                                    'cust_name' => 'cs_post_comp_address',
                                    'return' => true,
                                ),
                            );

                            if (isset($value['address_hint']) && $value['address_hint'] != '') {
                                $cs_opt_array['hint_text'] = $value['address_hint'];
                            }
                            if (isset($value['split']) && $value['split'] <> '') {
                                $cs_opt_array['split'] = $value['split'];
                            }

                            $output .= $cs_html_fields->cs_textarea_field($cs_opt_array);

                            $output .= '
                            <div class="theme-help" id="mailing_information">
                                <h4 style="padding-bottom:0px;">' . __('Find on Map', 'jobhunt') . '</h4>
                                <div class="clear"></div>
                            </div>';

                            $cs_opt_array = array(
                                'name' => __('Address', 'jobhunt'),
                                'desc' => '',
                                'field_params' => array(
                                    'std' => $cs_post_loc_address,
                                    'id' => 'loc_address',
                                    'classes' => 'directory-search-locationa',
                                    'extra_atr' => 'onkeypress="cs_gl_search_map(this.value)"',
                                    'cust_id' => 'loc_address',
                                    'cust_name' => 'cs_post_loc_address',
                                    'return' => true,
                                ),
                            );

                            if (isset($value['address_hint']) && $value['address_hint'] != '') {
                                $cs_opt_array['hint_text'] = $value['address_hint'];
                            }
                            if (isset($value['split']) && $value['split'] <> '') {
                                $cs_opt_array['split'] = $value['split'];
                            }

                            $output .= $cs_html_fields->cs_text_field($cs_opt_array);
                            $cs_opt_array = array(
                                'name' => __('Latitude', 'jobhunt'),
                                'id' => 'post_loc_latitude',
                                'desc' => '',
                                //'styles' => 'display:none;',
                                'field_params' => array(
                                    'std' => $cs_post_loc_latitude,
                                    'id' => 'post_loc_latitude',
                                    'cust_name' => 'cs_post_loc_latitude',
                                    //'cust_type' => 'hidden',
                                    'classes' => 'gllpLatitude',
                                    'return' => true,
                                ),
                            );

                            if (isset($value['split']) && $value['split'] <> '') {
                                $cs_opt_array['split'] = $value['split'];
                            }

                            $output .= $cs_html_fields->cs_text_field($cs_opt_array);
                            $cs_opt_array = array(
                                'name' => __('Longitude', 'jobhunt'),
                                'id' => 'post_loc_longitude',
                                'desc' => '',
                                'field_params' => array(
                                    'std' => $cs_post_loc_longitude,
                                    'id' => 'post_loc_longitude',
                                    'cust_name' => 'cs_post_loc_longitude',
                                    'classes' => 'gllpLongitude',
                                    'return' => true,
                                ),
                            );

                            if (isset($value['split']) && $value['split'] <> '') {
                                $cs_opt_array['split'] = $value['split'];
                            }
                            $output .= $cs_html_fields->cs_text_field($cs_opt_array);
                            $cs_opt_array = array(
                                'name' => '',
                                'id' => 'map_search_btn',
                                'desc' => '',
                                'field_params' => array(
                                    'std' => __('Search This Location on Map', 'jobhunt'),
                                    'id' => 'map_search_btn',
                                    'cust_type' => 'button',
                                    'classes' => 'gllpSearchButton cs-bgcolor',
                                    'return' => true,
                                ),
                            );

                            if (isset($value['split']) && $value['split'] <> '') {
                                $cs_opt_array['split'] = $value['split'];
                            }

                            $output .= $cs_html_fields->cs_text_field($cs_opt_array);
                            $output .= $cs_html_fields->cs_full_opening_field(array());
                            $output .= '<div class="clear"></div>';

                            $cs_opt_array = array(
                                'id' => 'add_new_loc',
                                'std' => $cs_add_new_loc,
                                'cust_type' => 'hidden',
                                'classes' => 'gllpSearchField',
                                'return' => true,
                            );

                            $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);
                            $cs_opt_array = array(
                                'id' => 'post_loc_zoom',
                                'std' => $cs_post_loc_zoom,
                                'cust_type' => 'hidden',
                                'classes' => 'gllpZoom',
                                'return' => true,
                            );

                            $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);
                            $output .= '<div class="clear"></div><div class="cs-map-section" style="float:left; width:100%; height:100%;"><div class="gllpMap" id="cs-map-location-id"></div></div>';
                            $output .= $cs_html_fields->cs_closing_field(array(
                                'desc' => '',
                                    )
                            );
                            $output .= '</div></div></div></fieldset>';

                            echo balanceTags($output);
                            ?>

                            </fieldset>
                            <script type="text/javascript">
                                "use strict";
                                var autocomplete;
                                jQuery(document).ready(function () {
                                    cs_load_location_ajax();
                                });

                                function cs_gl_search_map() {

                                    var vals;
                                    vals = jQuery('#loc_address').val();
                                    vals = vals + ", " + jQuery('#loc_city').val();
                                    vals = vals + ", " + jQuery('#loc_region').val();
                                    vals = vals + ", " + jQuery('#loc_country').val();
                                    jQuery('.gllpSearchField').val(vals);
                                }

                                (function ($) {
                                    $(function () {
            <?php $cs_jobhunt->cs_google_place_scripts() ?>
                                        //var autocomplete = '';
                                        autocomplete = new google.maps.places.Autocomplete(document.getElementById('loc_address'));
            <?php if (isset($selected_iso_code) && !empty($selected_iso_code)) { ?>
                                            autocomplete.setComponentRestrictions({'country': '<?php echo $selected_iso_code; ?>'});

                <?php
            }
            ?>
                                    });
                                })(jQuery);

                            </script>
                            <?php
                        }

                        /**
                         * How to show location fields in front end
                         *
                         */
                        public function cs_frontend_location_fields($post_id = '', $field_postfix = '', $user = '') {

                            global $cs_plugin_options, $post, $cs_html_fields, $cs_html_fields2, $cs_html_fields_frontend, $cs_form_fields2;
                            $cs_map_latitude = isset($cs_plugin_options['map_latitude']) ? $cs_plugin_options['map_latitude'] : '';
                            $cs_map_longitude = isset($cs_plugin_options['map_longitude']) ? $cs_plugin_options['map_longitude'] : '';
                            $cs_map_zoom = isset($cs_plugin_options['map_zoom']) ? $cs_plugin_options['map_zoom'] : '11';

                            $cs_array_data = '';
                            if (isset($user) && !empty($user)) { // get values from usermeta
                                $cs_array_data = get_the_author_meta('cs_array_data', $user->ID);
                                if (isset($cs_array_data) && !empty($cs_array_data)) {
                                    $cs_post_loc_city = get_the_author_meta('cs_post_loc_city', $user->ID);
                                    $cs_post_loc_country = get_the_author_meta('cs_post_loc_country', $user->ID);
                                    $cs_post_loc_latitude = get_the_author_meta('cs_post_loc_latitude', $user->ID);
                                    $cs_post_loc_longitude = get_the_author_meta('cs_post_loc_longitude', $user->ID);
                                    $cs_post_loc_zoom = get_the_author_meta('cs_post_loc_zoom', $user->ID);
                                    $cs_post_loc_address = get_the_author_meta('cs_post_loc_address', $user->ID);
                                    $cs_post_comp_address = get_the_author_meta('cs_post_comp_address', $user->ID);
                                    $cs_add_new_loc = get_the_author_meta('cs_add_new_loc', $user->ID);
                                } else {
                                    $cs_post_loc_country = '';
                                    $cs_post_loc_region = '';
                                    $cs_post_loc_city = '';
                                    $cs_post_loc_address = '';
                                    $cs_post_loc_latitude = isset($cs_plugin_options['cs_post_loc_latitude']) ? $cs_plugin_options['cs_post_loc_latitude'] : '';
                                    $cs_post_loc_longitude = isset($cs_plugin_options['cs_post_loc_longitude']) ? $cs_plugin_options['cs_post_loc_longitude'] : '';
                                    $cs_post_loc_zoom = isset($cs_plugin_options['cs_post_loc_zoom']) ? $cs_plugin_options['cs_post_loc_zoom'] : '';
                                    $loc_city = '';
                                    $loc_postcode = '';
                                    $loc_region = '';
                                    $loc_country = '';
                                    $event_map_switch = '';
                                    $event_map_heading = '';
                                    $cs_add_new_loc = '';
                                    $cs_post_comp_address = '';
                                }
                            } else {
                                $cs_array_data = get_post_meta($post_id, 'cs_array_data', true);
                                $cs_post_loc_address = get_post_meta($post_id, 'cs_post_loc_address', true);

                                if (isset($cs_array_data) && !empty($cs_array_data)) {
                                    $cs_post_loc_city = get_post_meta($post_id, 'cs_post_loc_city', true);
                                    $cs_post_loc_country = get_post_meta($post_id, 'cs_post_loc_country', true);
                                    $cs_post_loc_latitude = get_post_meta($post_id, 'cs_post_loc_latitude', true);
                                    $cs_post_loc_longitude = get_post_meta($post_id, 'cs_post_loc_longitude', true);
                                    $cs_post_loc_zoom = get_post_meta($post_id, 'cs_post_loc_zoom', true);
                                    $cs_post_loc_address = get_post_meta($post_id, 'cs_post_loc_address', true);
                                    $cs_post_comp_address = get_post_meta($post_id, 'cs_post_comp_address', true);
                                    $cs_add_new_loc = get_post_meta($post_id, 'cs_add_new_loc', true);
                                } else {

                                    $cs_post_loc_country = '';
                                    $cs_post_loc_region = '';
                                    $cs_post_loc_city = '';
                                    $cs_post_loc_address = '';
                                    $cs_post_comp_address = '';
                                    $cs_post_loc_latitude = isset($cs_plugin_options['cs_post_loc_latitude']) ? $cs_plugin_options['cs_post_loc_latitude'] : '';
                                    $cs_post_loc_longitude = isset($cs_plugin_options['cs_post_loc_longitude']) ? $cs_plugin_options['cs_post_loc_longitude'] : '';
                                    $cs_post_loc_zoom = isset($cs_plugin_options['cs_post_loc_zoom']) ? $cs_plugin_options['cs_post_loc_zoom'] : '';
                                    $loc_city = '';
                                    $loc_postcode = '';
                                    $loc_region = '';
                                    $loc_country = '';
                                    $event_map_switch = '';
                                    $event_map_heading = '';
                                    $cs_add_new_loc = '';
                                }
                            }
                            if ($cs_post_loc_latitude == '')
                                $cs_post_loc_latitude = isset($cs_plugin_options['cs_post_loc_latitude']) ? $cs_plugin_options['cs_post_loc_latitude'] : '';
                            if ($cs_post_loc_longitude == '')
                                $cs_post_loc_longitude = isset($cs_plugin_options['cs_post_loc_longitude']) ? $cs_plugin_options['cs_post_loc_longitude'] : '';
                            if ($cs_post_loc_zoom == '')
                                $cs_post_loc_zoom = isset($cs_plugin_options['cs_post_loc_zoom']) ? $cs_plugin_options['cs_post_loc_zoom'] : '';
                            $cs_jobhunt = new wp_jobhunt();
                            $cs_jobhunt->cs_location_gmap_script();
                            $cs_jobhunt->cs_google_place_scripts();
                            $cs_jobhunt->cs_autocomplete_scripts();
                            $locations_parent_id = 0;
                            $country_args = array(
                                'orderby' => 'name',
                                'order' => 'ASC',
                                'fields' => 'all',
                                'slug' => '',
                                'hide_empty' => false,
                                'parent' => $locations_parent_id,
                            );
                            $cs_location_countries = get_terms('cs_locations', $country_args);
                            $location_countries_list = '';
                            $location_states_list = '';
                            $location_cities_list = '';
                            $iso_code_list = '';
                            $iso_code_list_main = '';
                            $iso_code = '';
                            if (isset($cs_location_countries) && !empty($cs_location_countries)) {
                                $selected_iso_code = '';
                                foreach ($cs_location_countries as $key => $country) {
                                    $selected = '';
                                    $t_id_main = $country->term_id;
                                    $iso_code_list_main = get_option("iso_code_$t_id_main");
                                    if (isset($iso_code_list_main['text'])) {
                                        $iso_code_list_main = $iso_code_list_main['text'];
                                    }
                                    if (isset($cs_post_loc_country) && $cs_post_loc_country == $country->slug) {
                                        $selected = 'selected';
                                        $t_id = $country->term_id;
                                        $iso_code_list = get_option("iso_code_$t_id");
                                        if (isset($iso_code_list['text'])) {
                                            $selected_iso_code = $iso_code_list['text'];
                                        }
                                    }
                                    $location_countries_list .= "<option " . $selected . "  value='" . $country->slug . "' data-name='" . $iso_code_list_main . "'>" . $country->name . "</option>";
                                }
                            }
                            $selected_country = $cs_post_loc_country;
                            $selected_city = $cs_post_loc_city;
                            if (isset($cs_location_countries) && !empty($cs_location_countries) && isset($cs_post_loc_country) && !empty($cs_post_loc_country)) {
                                // load all cities against state  
                                $cities = '';
                                $selected_spec = get_term_by('slug', $selected_country, 'cs_locations');
                                $city_parent_id = isset($selected_spec->term_id) ? $selected_spec->term_id : '';
                                $cities_args = array(
                                    'orderby' => 'name',
                                    'order' => 'ASC',
                                    'fields' => 'all',
                                    'slug' => '',
                                    'hide_empty' => false,
                                    'parent' => $city_parent_id,
                                );
                                $cities = get_terms('cs_locations', $cities_args);
                                if (isset($cities) && $cities != '' && is_array($cities)) {
                                    foreach ($cities as $key => $city) {
                                        $selected = ( $selected_city == $city->slug) ? 'selected' : '';
                                        $location_cities_list .= "<option " . $selected . " value='" . $city->slug . "'>" . $city->name . "</option>";
                                    }
                                }
                            }
                            ?>
                            <fieldset style="width:100%; float:left;" id="fe_map<?php echo absint($field_postfix) ?>">
                                <div class="page-wrap page-opts left" style=" position:relative;" id="locations_wrap" data-themeurl="<?php echo wp_jobhunt::plugin_url(); ?>" data-plugin_url="<?php echo wp_jobhunt::plugin_url(); ?>" data-ajaxurl="<?php echo esc_js(admin_url('admin-ajax.php'), 'jobhunt'); ?>" data-map_marker="<?php echo wp_jobhunt::plugin_url() ?>/assets/images/map-marker.png">
                                    <div class="option-sec" style="margin-bottom:0;">
                                        <div class="opt-conts">
                                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                <label><?php _e('Country', 'jobhunt'); ?></label>
                                                <div class="select-holder">
                                                    <?php
                                                    $output = '';
                                                    $cs_opt_array = array(
                                                        'name' => __('Country', 'jobhunt'),
                                                        'desc' => '',
                                                        'field_params' => array(
                                                            'std' => '',
                                                            'id' => 'loc_country',
                                                            'cust_id' => 'loc_country',
                                                            'extra_atr' => 'data-placeholder="' . __("Select Country", "jobhunt") . '"',
                                                            'cust_name' => 'cs_post_loc_country',
                                                            'classes' => 'form-control form-select-country dir-map-search single-select SlectBox chosen-select',
                                                            'options_markup' => true,
                                                            'return' => true,
                                                        ),
                                                    );
                                                    if (isset($value['contry_hint']) && $value['contry_hint'] != '') {
                                                        $cs_opt_array['hint_text'] = $value['contry_hint'];
                                                    }

                                                    if (isset($location_countries_list) && $location_countries_list != '') {
                                                        $cs_opt_array['field_params']['options'] = '<option value="">' . __('Select Country', 'jobhunt') . '</option>' . $location_countries_list;
                                                    } else {
                                                        $cs_opt_array['field_params']['options'] = '<option value="">' . __('Select Country', 'jobhunt') . '</option>';
                                                    }

                                                    if (isset($value['split']) && $value['split'] <> '') {
                                                        $cs_opt_array['split'] = $value['split'];
                                                    }

                                                    echo $cs_html_fields_frontend->cs_form_select_render($cs_opt_array);
                                                    ?>

                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                <label><?php echo __('City', 'jobhunt') ?></label>
                                                <div class="select-holder">

                                                    <span class="loader-cities"></span>

                                                    <?php
                                                    $cs_opt_array = array(
                                                        'name' => __('City', 'jobhunt'),
                                                        'id' => 'loc_city',
                                                        'desc' => '',
                                                        'field_params' => array(
                                                            'std' => '',
                                                            'id' => 'loc_city',
                                                            'cust_id' => 'loc_city',
                                                            'extra_atr' => 'data-placeholder="' . __("Select City", "jobhunt") . '"',
                                                            'cust_name' => 'cs_post_loc_city',
                                                            'classes' => 'chosen-select form-control form-select-city dir-map-search single-select SlectBox',
                                                            'options_markup' => true,
                                                            'return' => true,
                                                        ),
                                                    );
                                                    if (isset($value['city_hint']) && $value['city_hint'] != '') {
                                                        $cs_opt_array['hint_text'] = $value['city_hint'];
                                                    }
                                                    if (isset($location_cities_list) && $location_cities_list != '') {
                                                        $cs_opt_array['field_params']['options'] = '<option value="">' . __('Select City', 'jobhunt') . '</option>' . $location_cities_list;
                                                    } else {
                                                        $cs_opt_array['field_params']['options'] = '<option value="">' . __('Select City', 'jobhunt') . '</option>';
                                                    }
                                                    if (isset($value['split']) && $value['split'] <> '') {
                                                        $cs_opt_array['split'] = $value['split'];
                                                    }
                                                    echo $cs_html_fields_frontend->cs_form_select_render($cs_opt_array);
                                                    ?>

                                                </div>
                                            </div>
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <label><?php _e('Complete Address', 'jobhunt'); ?></label>
                                                <?php
                                                $cs_opt_array = array(
                                                    'std' => $cs_post_comp_address,
                                                    'id' => 'post_comp_address',
                                                    'cust_id' => 'cs_post_comp_address',
                                                    'cust_name' => 'cs_post_comp_address',
                                                    'extra_atr' => ' placeholder="' . __('Complete Address', 'jobhunt') . '"',
                                                    'return' => false,
                                                );

                                                $cs_form_fields2->cs_form_textarea_render($cs_opt_array);
                                                ?>
                                                <!--<textarea name="cs_post_comp_address" id="cs_post_comp_address"><?php echo $cs_post_comp_address ?></textarea>-->
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                <label><?php _e('Find on Map', 'jobhunt'); ?></label>
                                                <?php
                                                $cs_opt_array = array(
                                                    'name' => '',
                                                    'desc' => '',
                                                    'field_params' => array(
                                                        'std' => $cs_post_loc_address,
                                                        'id' => 'loc_address',
                                                        'classes' => 'form-control directory-search-location',
                                                        'extra_atr' => 'onkeypress="cs_fe_search_map(this.value)" placeholder="' . __('Complete Address', 'jobhunt') . '"',
                                                        'cust_id' => 'loc_address',
                                                        'cust_name' => 'cs_post_loc_address',
                                                        'return' => true,
                                                    ),
                                                );
                                                if (isset($value['address_hint']) && $value['address_hint'] != '') {
                                                    $cs_opt_array['hint_text'] = $value['address_hint'];
                                                }
                                                if (isset($value['split']) && $value['split'] <> '') {
                                                    $cs_opt_array['split'] = $value['split'];
                                                }
                                                echo $cs_html_fields_frontend->cs_form_text_render($cs_opt_array);
                                                ?>

                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                <label><?php _e('Latitude', 'jobhunt'); ?></label>
                                                <?php
                                                $cs_opt_array = array(
                                                    'name' => __('Latitude', 'jobhunt'),
                                                    'id' => 'post_loc_latitude',
                                                    'desc' => '',
                                                    //'styles' => 'display:none;',
                                                    'field_params' => array(
                                                        'std' => $cs_post_loc_latitude,
                                                        'id' => 'post_loc_latitude',
                                                        'cust_name' => 'cs_post_loc_latitude',
                                                        'extra_atr' => ' placeholder="' . __('Latitude', 'jobhunt') . '"',
                                                        'classes' => 'form-control gllpLatitude',
                                                        'return' => true,
                                                    ),
                                                );

                                                if (isset($value['split']) && $value['split'] <> '') {
                                                    $cs_opt_array['split'] = $value['split'];
                                                }

                                                echo $cs_html_fields_frontend->cs_form_text_render($cs_opt_array);
                                                ?>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                <label><?php _e('Longitude', 'jobhunt'); ?></label>
                                                <?php
                                                $cs_opt_array = array(
                                                    'name' => __('Longitude', 'jobhunt'),
                                                    'id' => 'post_loc_longitude',
                                                    'desc' => '',
                                                    'field_params' => array(
                                                        'std' => $cs_post_loc_longitude,
                                                        'id' => 'post_loc_longitude',
                                                        'cust_name' => 'cs_post_loc_longitude',
                                                        'extra_atr' => ' placeholder="' . __('Longitude', 'jobhunt') . '"',
                                                        'classes' => 'form-control gllpLongitude',
                                                        'return' => true,
                                                    ),
                                                );

                                                if (isset($value['split']) && $value['split'] <> '') {
                                                    $cs_opt_array['split'] = $value['split'];
                                                }
                                                echo $cs_html_fields_frontend->cs_form_text_render($cs_opt_array);
                                                ?>
                                            </div>

                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <label></label>
                                                <?php
                                                $cs_opt_array = array(
                                                    'name' => '',
                                                    'id' => 'map_search_btn',
                                                    'desc' => '',
                                                    'field_params' => array(
                                                        'std' => __('Search Location', 'jobhunt'),
                                                        'id' => 'map_search_btn',
                                                        'cust_type' => 'button',
                                                        'classes' => 'acc-submit cs-section-update cs-color csborder-color gllpSearchButton',
                                                        'return' => true,
                                                    ),
                                                );

                                                if (isset($value['split']) && $value['split'] <> '') {
                                                    $cs_opt_array['split'] = $value['split'];
                                                }

                                                echo $cs_html_fields_frontend->cs_form_text_render($cs_opt_array);
                                                ?>   
                                            </div>
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="float: left; width:100%;" >
                                                <div class="clear"></div>
                                                <?php
                                                $cs_opt_array = array(
                                                    'id' => '',
                                                    'id' => 'add_new_loc',
                                                    'std' => $cs_add_new_loc,
                                                    'classes' => 'gllpSearchField_fe',
                                                    'extra_atr' => 'style="margin-bottom:10px;"',
                                                    'return' => true,
                                                );

                                                echo $cs_form_fields2->cs_form_hidden_render($cs_opt_array);

                                                $cs_opt_array = array(
                                                    'id' => '',
                                                    'std' => esc_attr($cs_post_loc_zoom),
                                                    'cust_id' => esc_attr($cs_post_loc_zoom),
                                                    'cust_name' => "cs_post_loc_zoom",
                                                    'classes' => 'gllpZoom',
                                                    'return' => true,
                                                );

                                                echo $cs_form_fields2->cs_form_hidden_render($cs_opt_array);

                                                $cs_opt_array = array(
                                                    'id' => '',
                                                    'std' => __("update map", 'jobhunt'),
                                                    'cust_id' => '',
                                                    'cust_name' => "",
                                                    'classes' => 'gllpUpdateButton',
                                                    'return' => true,
                                                    'cust_type' => 'button',
                                                    'extra_atr' => 'style="display:none"',
                                                );
                                                echo $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                                ?>
                                                <div class="clear"></div>
                                                <div class="cs-map-section" style="float:left; width:100%; height:270px;">
                                                    <div class="gllpMap" id="cs-map-location-fe-id" style="float:left; width:100%; height:270px;"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            <script>
                                jQuery(document).ready(function () {

                                    cs_fe_search_map();
                                    cs_load_location_ajax();
                                    if (jQuery("#fe_map<?php echo absint($field_postfix) ?> #cs-map-location-fe-id").hasClass("gllpMap")) {
                                        var vals;
                                        cs_map_location_load('<?php echo absint($field_postfix); ?>');
                                        if (vals)
                                            cs_search_map(vals);
                                    }
                                });
                                function cs_fe_search_map() {

                                    var vals;
                                    vals = jQuery('#fe_map<?php echo absint($field_postfix) ?> #loc_address').val();
                                    jQuery('#fe_map<?php echo absint($field_postfix); ?> .gllpSearchField_fe').val(vals);
                                }

                                (function ($) {
                                    $(function () {
            <?php
            $cs_jobhunt->cs_google_place_scripts();
            ?> //var autocomplete;
                                        autocomplete = new google.maps.places.Autocomplete(document.getElementById('loc_address'));
            <?php if (isset($selected_iso_code) && !empty($selected_iso_code)) { ?>
                                            autocomplete.setComponentRestrictions({'country': '<?php echo esc_js($selected_iso_code) ?>'});
            <?php } ?>
                                    });
                                })(jQuery);
                                jQuery(document).ready(function () {
                                    var $ = jQuery;
                                    jQuery("[id^=map_canvas]").css("pointer-events", "none");
                                    jQuery("[id^=cs-map-location]").css("pointer-events", "none");
                                    // on leave handle
                                    var onMapMouseleaveHandler = function (event) {
                                        var that = jQuery(this);
                                        that.on('click', onMapClickHandler);
                                        that.off('mouseleave', onMapMouseleaveHandler);
                                        jQuery("[id^=map_canvas]").css("pointer-events", "none");
                                        jQuery("[id^=cs-map-location]").css("pointer-events", "none");
                                    }
                                    // on click handle
                                    var onMapClickHandler = function (event) {
                                        var that = jQuery(this);
                                        // Disable the click handler until the user leaves the map area
                                        that.off('click', onMapClickHandler);
                                        // Enable scrolling zoom
                                        that.find('[id^=map_canvas]').css("pointer-events", "auto");
                                        that.find('[id^=cs-map-location]').css("pointer-events", "auto");

                                        // Handle the mouse leave event
                                        that.on('mouseleave', onMapMouseleaveHandler);
                                    }
                                    // Enable map zooming with mouse scroll when the user clicks the map
                                    jQuery('.cs-map-section').on('click', onMapClickHandler);
                                    // new addition
                                });

                            </script>
                            <?php
                        }

                        /**
                         * Start How to add  Categories(Taxonomy) fields  Function
                         *
                         */
                        public function cs_jobs_spec_fields($tag) {    //check for existing featured ID
                            global $cs_form_fields2;
                            if (isset($tag->term_id)) {
                                $t_id = $tag->term_id;
                            } else {
                                $t_id = "";
                            }
                            $spec_image = '';
                            ?>

                            <div class="form-field">
                                <label for="tag-image"><?php _e("Image", 'jobhunt'); ?></label>
                                <ul class="form-elements col-lg-8 col-md-8 col-sm-12 col-xs-12" style="float:left; width:95%; margin:0 0 50px 0; padding:0;">
                                    <li class="to-field" style="width:100%;">
                                        <div class="page-wrap" style="overflow:hidden; background: none !important; float: left !important; clear: both !important; display:<?php echo esc_attr($spec_image) && trim($spec_image) != '' ? 'inline' : 'none'; ?>" id="spec_image<?php echo esc_attr($t_id) ?>_box" >
                                            <div class="gal-active" style="padding-left:0 !important;">
                                                <div class="dragareamain" style="padding-bottom:0px;">
                                                    <ul id="gal-sortable" style="width:200px;">
                                                        <li class="ui-state-default">
                                                            <div class="thumb-secs"> <img src="<?php echo esc_url($spec_image); ?>"  id="spec_image<?php echo esc_attr($t_id); ?>_img" width="200" />
                                                                <div class="gal-edit-opts"> <a   href="javascript:del_media('spec_image<?php echo esc_attr($t_id); ?>')" class="delete"></a> </div>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                        $cs_opt_array = array(
                                            'id' => '',
                                            'std' => esc_url($spec_image),
                                            'cust_id' => "spec_image" . esc_attr($t_id),
                                            'cust_name' => "spec_image",
                                            'classes' => '',
                                            'return' => true,
                                        );
                                        echo $cs_form_fields2->cs_form_hidden_render($cs_opt_array);
                                        ?>
                                        <label class="browse-icon" style="float: left !important; clear: both !important;">
                                            <?php
                                            $cs_opt_array = array(
                                                'id' => '',
                                                'std' => __("Browse", 'jobhunt'),
                                                'cust_id' => '',
                                                'cust_name' => "spec_image" . esc_attr($t_id),
                                                'classes' => 'uploadMedia left',
                                                'return' => true,
                                                'extra_atr' => ' style="background:#ff6363 !important;"',
                                                'cust_type' => 'button',
                                            );
                                            echo $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                            ?>
                                        </label>
                                    </li>
                                </ul>
                            </div>
                            <?php
                            $cs_opt_array = array(
                                'id' => '',
                                'std' => "1",
                                'cust_id' => "",
                                'cust_name' => "spec_image_meta",
                                'return' => true,
                            );
                            echo $cs_form_fields2->cs_form_hidden_render($cs_opt_array);
                        }

                        /*                         *
                         * End How to add Categories fields Function
                         * Start How to Edit Categories Fields Function
                         * */

                        public function cs_edit_jobs_spec_fields($tag) {    //check for existing featured ID
                            global $cs_form_fields2;
                            if (isset($tag->term_id)) {
                                $t_id = $tag->term_id;
                            } else {
                                $t_id = "";
                            }
                            $cs_counter = $tag->term_id;
                            $cat_meta = get_term_meta($t_id, 'spec_meta_data', true);
                            $spec_image = isset($cat_meta['img']) ? $cat_meta['img'] : '';
                            ?>
                            <tr>
                                <th><label for="cat_f_img_url"> <?php _e("Choose Icon", "jobhunt"); ?></label></th>
                                <td>
                                    <ul class="form-elements col-lg-8 col-md-8 col-sm-12 col-xs-12" style="margin:0; padding:0;">
                                        <li class="to-field" style="width:100%;">
                                            <div class="page-wrap" style="overflow:hidden; background: none !important; float: left !important; clear: both !important; display:<?php echo esc_attr($spec_image) && trim($spec_image) != '' ? 'inline' : 'none'; ?>" id="spec_image<?php echo esc_attr($cs_counter) ?>_box" >
                                                <div class="gal-active" style="padding-left:0 !important;">
                                                    <div class="dragareamain" style="padding-bottom:0px;">
                                                        <ul id="gal-sortable" style="width:200px;">
                                                            <li class="ui-state-default">
                                                                <div class="thumb-secs"> <img src="<?php echo esc_url($spec_image); ?>"  id="spec_image<?php echo esc_attr($cs_counter); ?>_img" width="200" />
                                                                    <div class="gal-edit-opts"> <a href="javascript:del_media('spec_image<?php echo esc_attr($cs_counter); ?>')" class="delete"></a> </div>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                            $cs_opt_array = array(
                                                'id' => '',
                                                'std' => esc_url($spec_image),
                                                'cust_id' => "spec_image" . esc_attr($cs_counter),
                                                'cust_name' => "spec_image",
                                            );
                                            $cs_form_fields2->cs_form_hidden_render($cs_opt_array);
                                            ?>
                                            <label class="browse-icon" style="float: left !important; clear: both !important;">
                                                <?php
                                                $cs_opt_array = array(
                                                    'id' => '',
                                                    'std' => __("Browse", 'jobhunt'),
                                                    'cust_id' => '',
                                                    'cust_name' => "spec_image" . esc_attr($cs_counter),
                                                    'classes' => 'uploadMedia left',
                                                    'cust_type' => 'button',
                                                );
                                                $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                                ?>
                                            </label>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                            <?php
                            $cs_opt_array = array(
                                'id' => '',
                                'std' => "1",
                                'cust_id' => "",
                                'cust_name' => "spec_image_meta",
                                'return' => true,
                            );
                            echo $cs_form_fields2->cs_form_hidden_render($cs_opt_array);
                        }

                        /**
                         * Start Function save extra category extra fields callback function
                         *
                         */
                        public function cs_save_jobs_spec_fields($term_id) {
                            if (isset($_POST['spec_image_meta']) and $_POST['spec_image_meta'] == '1') {
                                $t_id = $term_id;
                                $spec_image_img = '';
                                if (isset($_POST['spec_image'])) {
                                    $spec_image_img = $_POST['spec_image'];
                                }
                                $cat_meta = array(
                                    'img' => $spec_image_img,
                                );
                                //save the option array
                                update_term_meta($t_id, 'spec_meta_data', $cat_meta);
                            }
                        }

                        // Add Category Fields
                        public function cs_jobs_locations_fields($tag) { //check for existing featured ID
                            global $cs_form_fields2;
                            if (isset($tag->term_id)) {
                                $t_id = $tag->term_id;
                            } else {
                                $t_id = '';
                            }
                            $locations_image = '';
                            $iso_code = '';
                            ?>
                            <div class="form-field">

                                <label><?php _e("ISO Code", "jobhunt"); ?></label>
                                <ul class="form-elements" style="margin:0; padding:0;">
                                    <li class="to-field" style="width:100%;">
                                        <?php
                                        $cs_opt_array = array(
                                            'id' => '',
                                            'std' => "",
                                            'cust_id' => "iso_code",
                                            'cust_name' => "iso_code",
                                            'return' => true,
                                        );
                                        echo $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                        ?>
                                    </li>
                                </ul>
                                <br> <br>
                            </div>
                            <?php
                            $cs_opt_array = array(
                                'id' => '',
                                'std' => "1",
                                'cust_id' => "",
                                'cust_name' => "locations_image_meta",
                                'return' => true,
                            );
                            echo $cs_form_fields2->cs_form_hidden_render($cs_opt_array);
                        }

                        public function cs_edit_jobs_locations_fields($tag) { //check for existing featured ID
                            global $cs_form_fields2;
                            if (isset($tag->term_id)) {
                                $t_id = $tag->term_id;
                            } else {
                                $t_id = "";
                            }
                            $cat_meta = get_option("iso_code_$t_id");
                            $iso_code = $cat_meta['text'];

                            $cs_opt_array = array(
                                'id' => '',
                                'std' => "1",
                                'cust_id' => "",
                                'cust_name' => "locations_image_meta",
                                'return' => true,
                            );
                            echo $cs_form_fields2->cs_form_hidden_render($cs_opt_array);
                            ?>
                            <tr>
                                <th><label for="cat_f_img_url"> <?php _e("ISO Code", "jobhunt"); ?></label></th>
                                <td>
                                    <ul class="form-elements" style="margin:0; padding:0;">
                                        <li class="to-field" style="width:100%;">
                                            <?php
                                            $cs_opt_array = array(
                                                'id' => '',
                                                'std' => esc_attr($iso_code),
                                                'cust_id' => "iso_code",
                                                'cust_name' => "iso_code",
                                                'return' => true,
                                            );
                                            echo $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                            ?>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                            <?php
                        }

                        /**
                         * Start Function how to save location in jobs fields
                         */
                        public function cs_save_jobs_locations_fields($term_id) {
                            if (isset($_POST['locations_image_meta']) and $_POST['locations_image_meta'] == '1') {
                                $t_id = $term_id;
                                get_option("locations_image_$t_id");
                                $locations_image_img = '';
                                if (isset($_POST['locations_image'])) {
                                    $locations_image_img = $_POST['locations_image'];
                                }
                                if (isset($_POST['iso_code'])) {
                                    $iso_code = $_POST['iso_code'];
                                    //echo $iso_code; exit;
                                }
                                $cat_meta = array(
                                    'img' => $locations_image_img,
                                );
                                $cat_meta = array(
                                    'text' => $iso_code,
                                );
                                update_option("locations_image_$t_id", $cat_meta);
                                update_option("iso_code_$t_id", $cat_meta);
                            }
                        }

                        public function cs_jobs_job_type_fields($tag) {
                            global $cs_form_fields2;
                            if (isset($tag->term_id)) {
                                $t_id = $tag->term_id;
                            } else {
                                $t_id = "";
                            }
                            $locations_image = '';
                            $job_type_color = '';
                            wp_enqueue_style('wp-color-picker');
                            wp_enqueue_script('wp-color-picker');
                            ?>
                            <script type="text/javascript">
                                jQuery(document).ready(function ($) {
                                    $('.bg_color').wpColorPicker();
                                });
                            </script>
                            <div class="form-field">

                                <label><?php _e("Job Type Color", "jobhunt"); ?></label>
                                <ul class="form-elements" style="margin:0; padding:0;">
                                    <li class="to-field" style="width:100%;">
                                        <?php
                                        $cs_opt_array = array(
                                            'id' => '',
                                            'std' => "",
                                            'cust_id' => "job_type_color",
                                            'cust_name' => "job_type_color",
                                            'classes' => 'bg_color',
                                            'return' => true,
                                        );
                                        echo $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                        ?>
                                    </li>
                                </ul>
                                </br> </br>
                            </div>
                            <?php
                        }

                        public function cs_edit_jobs_job_type_fields($tag) {    //check for existing featured ID
                            global $cs_form_fields2;
                            wp_enqueue_style('wp-color-picker');
                            wp_enqueue_script('wp-color-picker');
                            if (isset($tag->term_id)) {
                                $t_id = $tag->term_id;
                            } else {
                                $t_id = "";
                            }
                            ?>
                            <script type="text/javascript">
                                jQuery(document).ready(function ($) {
                                    $('.bg_color').wpColorPicker();
                                });
                            </script>
                            <?php
                            $cat_meta = get_option("job_type_color_$t_id");
                            $job_type_color = $cat_meta['text'];
                            ?>

                            <tr>
                                <th><label for="cat_f_img_url"> <?php _e("Job Type Color", "jobhunt"); ?></label></th>
                                <td>
                                    <ul class="form-elements" style="margin:0; padding:0;">
                                        <li class="to-field" style="width:100%;">
                                            <?php
                                            $cs_opt_array = array(
                                                'id' => '',
                                                'std' => esc_attr($job_type_color),
                                                'cust_id' => "job_type_color",
                                                'cust_name' => "job_type_color",
                                                'classes' => 'bg_color',
                                                'return' => true,
                                            );
                                            echo $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                            ?>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                            <?php
                        }

                        /**
                         * Start Function how to save location in jobs fields
                         */
                        public function cs_save_jobs_jobtype_fields($term_id) {
                            if (isset($_POST['job_type_color'])) {
                                $t_id = $term_id;

                                if (isset($_POST['job_type_color'])) {
                                    $job_type_color = $_POST['job_type_color'];
                                }

                                $cat_meta = array(
                                    'text' => $job_type_color,
                                );

                                update_option("job_type_color_$t_id", $cat_meta);
                            }
                        }

                        /**
                         * End Function how to save location in jobs fields
                         * How to know about working  current Theme Function Start
                         */
                        public function cs_get_current_theme() {
                            $cs_theme = wp_get_theme();
                            $theme_name = $cs_theme->get('Name');
                            return $theme_name;
                        }

                    }

                    /**
                     * End Function How to know about working  current Theme Function
                     * Design Pattern for Object initilization
                     */
                    function CS_FUNCTIONS() {
                        return cs_job_plugin_functions::instance();
                    }

                    $GLOBALS['cs_job_plugin_functions'] = CS_FUNCTIONS();
                }

                