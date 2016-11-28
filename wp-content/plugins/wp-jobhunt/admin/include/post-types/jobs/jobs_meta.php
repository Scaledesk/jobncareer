<?php
/**
 * @Add Meta Box For Jobs Post
 * @return
 *
 */
if (!function_exists('cs_meta_jobs_add')) {
    add_action('add_meta_boxes', 'cs_meta_jobs_add');

    /**
     * Start Function How to Add meata box function
     */
    function cs_meta_jobs_add() {
        add_meta_box('cs_meta_jobs', __('Jobs Options', 'jobhunt'), 'cs_meta_jobs', 'jobs', 'normal', 'high');
    }

    /**
     * End Function How to Add meata box function
     */
}

if (!function_exists('cs_meta_jobs')) {

    /**
     * Start Function How to Attach mata box with post
     */
    function cs_meta_jobs($post) {
        global $post;
        ?>
        <div class="page-wrap page-opts left">
            <div class="option-sec" style="margin-bottom:0;">
                <div class="opt-conts">
                    <div class="elementhidden">
                        <?php
                        if (function_exists('cs_job_options')) {
                            cs_job_options();
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="clear"></div>
        <?php
    }

}
/**
 * End Function How to Attach mata box with post
 */
/**
 * Start Function How to add form options in  html
 */
if (!function_exists('cs_job_options')) {

    function cs_job_options() {
        global $post, $cs_form_fields, $cs_form_fields2, $cs_html_fields, $cs_plugin_options;

        //$cs_selected_style = isset($cs_plugin_options['cs_job_detail_style']) ? $cs_plugin_options['cs_job_detail_style'] : '';

        $cs_job_types = array();
        $cs_args = array('posts_per_page' => '-1', 'post_type' => 'jobs_capacity', 'orderby' => 'ID', 'post_status' => 'publish');
        $cust_query = get_posts($cs_args);
        $cs_job_capacity = get_post_meta($post->ID, 'cs_job_capacity', true);
        $cs_job_featured = get_post_meta($post->ID, 'cs_job_featured', true);
        $cs_users_list = array();
        $cs_users = get_users('orderby=nicename&role=cs_employer');
        foreach ($cs_users as $user) {
            $cs_users_list[$user->ID] = $user->display_name;
        }
        $cs_packages_list = array();
        $cs_packages_options = get_option('cs_plugin_options');
        $cs_packages_options = $cs_packages_options['cs_packages_options'];
        if (isset($cs_packages_options) && is_array($cs_packages_options) && count($cs_packages_options) > 0) {
            $cs_packages_list[''] = '-- ' . __('Select', 'jobhunt') . ' --';
            foreach ($cs_packages_options as $package_key => $package) {
                if (isset($package_key) && $package_key <> '') {
                    $package_id = isset($package['package_id']) ? $package['package_id'] : '';
                    $package_title = isset($package['package_title']) ? $package['package_title'] : '';
                    $cs_packages_list[$package_id] = $package_title;
                }
            }
        }

        $cs_opt_array = array(
            'name' => __('Id Number', 'jobhunt'),
            'desc' => '',
            'hint_text' => '',
            'echo' => true,
            'field_params' => array(
                'std' => '',
                'id' => 'job_id',
                'return' => true,
            ),
        );

        $cs_html_fields->cs_text_field($cs_opt_array);

        $cs_opt_array = array(
            'name' => __('Transaction Id', 'jobhunt'),
            'desc' => '',
            'hint_text' => '',
            'echo' => true,
            'field_params' => array(
                'std' => '',
                'id' => 'trans_id',
                'return' => true,
            ),
        );

        $cs_html_fields->cs_text_field($cs_opt_array);

        $cs_opt_array = array(
            'name' => __('Posted by', 'jobhunt'),
            'desc' => '',
            'hint_text' => '',
            'echo' => true,
            'field_params' => array(
                'std' => '',
                'id' => 'job_username',
                'classes' => 'chosen-select-no-single',
                'options' => $cs_users_list,
                'return' => true,
            ),
        );

        $cs_html_fields->cs_select_field($cs_opt_array);

        $cs_opt_array = array(
            'name' => __('Posted on:', 'jobhunt'),
            'desc' => '',
            'hint_text' => '',
            'echo' => true,
            'field_params' => array(
                'id' => 'job_posted',
                'classes' => '',
                'strtotime' => true,
                'std' => '',//date('d-m-Y H:i:s'),
                'description' => '',
                'hint' => '',
                'format' => 'd-m-Y H:i:s',
                'return' => true,
            ),
        );

        $cs_html_fields->cs_date_field($cs_opt_array);

        $cs_opt_array = array(
            'name' => __('Expired on:', 'jobhunt'),
            'desc' => '',
            'hint_text' => '',
            'echo' => true,
            'field_params' => array(
                'std' =>'', //date('d-m-Y'),
                'id' => 'job_expired',
                'format' => 'd-m-Y',
                'strtotime' => true,
                'return' => true,
            ),
        );

        $cs_html_fields->cs_date_field($cs_opt_array);
        
        apply_filters( 'job_hunt_application_deadline_field', '' );

        if ($cs_job_featured == 'yes' || $cs_job_featured == 'on') {
            $cs_opt_array = array(
                'name' => __('Featured ', 'jobhunt'),
                'desc' => '',
                'hint_text' => '',
                'echo' => true,
                'field_params' => array(
                    'std' => 'yes',
                    'id' => 'job_featured',
                    'classes' => 'chosen-select-no-single',
                    'options' => array('yes' => __('Yes', 'jobhunt'), 'no' => __('No', 'jobhunt')),
                    'return' => true,
                ),
            );

            $cs_html_fields->cs_select_field($cs_opt_array);
        } else {
            $cs_opt_array = array(
                'name' => __('Featured', 'jobhunt'),
                'desc' => '',
                'hint_text' => '',
                'echo' => true,
                'field_params' => array(
                    'std' => 'no',
                    'id' => 'job_featured',
                    'classes' => 'chosen-select-no-single',
                    'options' => array('no' => __('No', 'jobhunt'), 'yes' => __('Yes', 'jobhunt')),
                    'return' => true,
                ),
            );

            $cs_html_fields->cs_select_field($cs_opt_array);
        }
        $cs_opt_array = array(
            'name' => __('Package', 'jobhunt'),
            'desc' => '',
            'hint_text' => '',
            'echo' => true,
            'field_params' => array(
                'std' => '',
                'id' => 'job_package',
                'classes' => 'chosen-select-no-single',
                'options' => $cs_packages_list,
                'return' => true,
            ),
        );

        $cs_html_fields->cs_select_field($cs_opt_array);

        $cs_opt_array = array(
            'name' => __('Status', 'jobhunt'),
            'desc' => '',
            'hint_text' => '',
            'echo' => true,
            'field_params' => array(
                'std' => '',
                'id' => 'job_status',
                'classes' => 'chosen-select-no-single',
                'options' => array('awaiting-activation' => __('Awaiting Activation', 'jobhunt'), 'active' => __('Active', 'jobhunt'), 'inactive' => __('Inactive', 'jobhunt'), 'delete' => __('Delete' , 'jobhunt')),
                'return' => true,
            ),
        );
        
        $cs_job_status = get_post_meta( $post->ID, 'cs_job_status', true );
        $cs_form_fields2->cs_form_hidden_render(
            array(
                'name' => __('Job Old Status', 'jobhunt'),
                'id' => 'job_old_status',
                'classes' => '',
                'std' => $cs_job_status,
                'description' => '',
                'hint' => ''
            )
        );

        $cs_html_fields->cs_select_field($cs_opt_array);

        $cs_opt_array = array(
            'name' => __('Style', 'jobhunt'),
            'desc' => '',
            'hint_text' => '',
            'echo' => true,
            'field_params' => array(
                'std' => '', //$cs_selected_style,
                'id' => 'job_style',
                'classes' => 'chosen-select-no-single',
                'options' => array(
                    '' => __("Default - Selected From Plugin Options", "jobhunt"),
                    '2_columns' => __("2 Columns", "jobhunt"),
                    '3_columns' => __("3 Columns", "jobhunt"),
                    'classic' => __("Classic", "jobhunt"),
                    'fancy' => __("Fancy", "jobhunt"),
                    'map_view' => __("Map View", "jobhunt"),
                ),
                'return' => true,
            ),
        );

        $cs_html_fields->cs_select_field($cs_opt_array);
        
        do_action( 'jobhunt_indeed_job_admin_fields' );
        
        $cs_form_fields2->cs_form_hidden_render(
                array(
                    'name' => __('Organization', 'jobhunt'),
                    'id' => 'org_name',
                    'classes' => '',
                    'std' => '',
                    'description' => '',
                    'hint' => ''
                )
        );
        $cs_html_fields->cs_heading_render(
                array(
                    'name' => __('Mailing Information', 'jobhunt'),
                    'id' => 'mailing_information',
                    'classes' => '',
                    'std' => '',
                    'description' => '',
                    'hint' => ''
                )
        );
        CS_FUNCTIONS()->cs_location_fields();

        $cs_job_cus_fields = get_option("cs_job_cus_fields");
        if (is_array($cs_job_cus_fields) && sizeof($cs_job_cus_fields) > 0) {
            $cs_html_fields->cs_heading_render(
                    array('name' => __('Custom Fields', 'jobhunt'),
                        'id' => 'cs_fields_section',
                        'classes' => '',
                        'std' => '',
                        'description' => '',
                    )
            );
            foreach ($cs_job_cus_fields as $cus_field) {
                $cs_type = isset($cus_field['type']) ? $cus_field['type'] : '';
                switch ($cs_type) {
                    case('text'):
                        if (isset($cus_field['meta_key']) && $cus_field['meta_key'] != '') {
                            $cs_opt_array = array(
                                'name' => isset($cus_field['label']) ? $cus_field['label'] : '',
                                'desc' => '',
                                'hint_text' => isset($cus_field['help']) ? $cus_field['help'] : '',
                                'echo' => true,
                                'field_params' => array(
                                    'std' => isset($cus_field['default_value']) ? $cus_field['default_value'] : '',
                                    'id' => $cus_field['meta_key'],
                                    'cus_field' => true,
                                    'return' => true,
                                ),
                            );

                            $cs_html_fields->cs_text_field($cs_opt_array);
                        }
                        break;
                    case('textarea'):
                        if (isset($cus_field['meta_key']) && $cus_field['meta_key'] != '') {
                            $cs_opt_array = array(
                                'name' => isset($cus_field['label']) ? $cus_field['label'] : '',
                                'desc' => '',
                                'hint_text' => isset($cus_field['help']) ? $cus_field['help'] : '',
                                'echo' => true,
                                'field_params' => array(
                                    'std' => isset($cus_field['default_value']) ? $cus_field['default_value'] : '',
                                    'id' => $cus_field['meta_key'],
                                    'cus_field' => true,
                                    'return' => true,
                                ),
                            );

                            $cs_html_fields->cs_textarea_field($cs_opt_array);
                        }
                        break;
                    case('dropdown'):
                        if (isset($cus_field['meta_key']) && $cus_field['meta_key'] != '') {
                            $cs_options = array();
                            if (isset($cus_field['options']['value']) && is_array($cus_field['options']['value']) && sizeof($cus_field['options']['value']) > 0) {
                                if (isset($cus_field['first_value']) && $cus_field['first_value'] != '') {
                                    $cs_options[''] = $cus_field['first_value'];
                                }
                                $cs_opt_counter = 0;
                                foreach ($cus_field['options']['value'] as $cs_option) {

                                    $cs_opt_label = $cus_field['options']['label'][$cs_opt_counter];
                                    $cs_options[$cs_option] = $cs_opt_label;
                                    $cs_opt_counter++;
                                }
                            }

                            $cs_opt_array = array(
                                'name' => isset($cus_field['label']) ? $cus_field['label'] : '',
                                'desc' => '',
                                'hint_text' => isset($cus_field['help']) ? $cus_field['help'] : '',
                                'echo' => true,
                                'field_params' => array(
                                    'std' => isset($cus_field['default_value']) ? $cus_field['default_value'] : '',
                                    'id' => $cus_field['meta_key'],
                                    'options' => $cs_options,
                                    'classes' => 'chosen-select-no-single',
                                    'cus_field' => true,
                                    'return' => true,
                                ),
                            );

                            if (isset($cus_field['post_multi']) && $cus_field['post_multi'] == 'yes') {
                                $cs_opt_array['multi'] = true;
                            }

                            $cs_html_fields->cs_select_field($cs_opt_array);
                        }
                        break;
                    case('date'):
                        if (isset($cus_field['meta_key']) && $cus_field['meta_key'] != '') {
                            $cs_format = isset($cus_field['date_format']) && $cus_field['date_format'] != '' ? $cus_field['date_format'] : 'd-m-Y';

                            $cs_opt_array = array(
                                'name' => isset($cus_field['label']) ? $cus_field['label'] : '',
                                'desc' => '',
                                'hint_text' => isset($cus_field['help']) ? $cus_field['help'] : '',
                                'echo' => true,
                                'field_params' => array(
                                    'std' => isset($cus_field['default_value']) ? $cus_field['default_value'] : '',
                                    'id' => $cus_field['meta_key'],
                                    'format' => $cs_format,
                                    'cus_field' => true,
                                    'return' => true,
                                ),
                            );

                            $cs_html_fields->cs_date_field($cs_opt_array);
                        }
                        break;
                    case('email'):
                        if (isset($cus_field['meta_key']) && $cus_field['meta_key'] != '') {
                            $cs_opt_array = array(
                                'name' => isset($cus_field['label']) ? $cus_field['label'] : '',
                                'desc' => '',
                                'hint_text' => isset($cus_field['help']) ? $cus_field['help'] : '',
                                'echo' => true,
                                'field_params' => array(
                                    'std' => isset($cus_field['default_value']) ? $cus_field['default_value'] : '',
                                    'id' => $cus_field['meta_key'],
                                    'cus_field' => true,
                                    'return' => true,
                                ),
                            );

                            $cs_html_fields->cs_text_field($cs_opt_array);
                        }
                        break;
                    case('url'):
                        if (isset($cus_field['meta_key']) && $cus_field['meta_key'] != '') {

                            $cs_opt_array = array(
                                'name' => isset($cus_field['label']) ? $cus_field['label'] : '',
                                'desc' => '',
                                'hint_text' => isset($cus_field['help']) ? $cus_field['help'] : '',
                                'echo' => true,
                                'field_params' => array(
                                    'std' => isset($cus_field['default_value']) ? $cus_field['default_value'] : '',
                                    'id' => $cus_field['meta_key'],
                                    'cus_field' => true,
                                    'return' => true,
                                ),
                            );

                            $cs_html_fields->cs_text_field($cs_opt_array);
                        }
                        break;
                    case('range'):
                        if (isset($cus_field['meta_key']) && $cus_field['meta_key'] != '') {
                            $cs_opt_array = array(
                                'name' => isset($cus_field['label']) ? $cus_field['label'] : '',
                                'desc' => '',
                                'hint_text' => isset($cus_field['help']) ? $cus_field['help'] : '',
                                'echo' => true,
                                'field_params' => array(
                                    'std' => isset($cus_field['default_value']) ? $cus_field['default_value'] : '',
                                    'id' => $cus_field['meta_key'],
                                    'cus_field' => true,
                                    'return' => true,
                                ),
                            );

                            $cs_html_fields->cs_text_field($cs_opt_array);
                        }
                        break;
                }
            }
        }
    }

}
/**
 * End Function How to add form options in  html
 */
/**
 * Start Function How to create taxonomies
 */
// create job category
if (!function_exists('create_specialisms_taxonomies')) {
    add_action('init', 'create_specialisms_taxonomies');

    function create_specialisms_taxonomies() {
        register_taxonomy("specialisms", array("jobs", "employer", "candidate"), array
            (
            "hierarchical" => true,
            "label" => __("Specialisms", 'jobhunt'),
            'labels' => array('new_item_name' => __('New Item Specialism', 'jobhunt'),
                'add_new_item' => __('Add New Specialism', 'jobhunt'),
                'edit_item' => __('Edit Specialism', 'jobhunt'),
                "singular_name" => __("Specialism", 'jobhunt')
            ),
            "public" => false,
            'show_ui' => true,
            "show_in_menu" => true,
            "rewrite" => false)
        );
    }

}
/**
 * End Function How to create taxonomies
 */
/**
 * Start Function How to create taxonomies
 */
// create job type category
if (!function_exists('create_job_type_taxonomies')) {
    add_action('init', 'create_job_type_taxonomies');

    function create_job_type_taxonomies() {
        register_taxonomy("job_type", array("jobs"), array
            (
            "hierarchical" => true,
            "label" => __("Job Type", 'jobhunt'),
            'labels' => array('new_item_name' => __('New Item Job Type', 'jobhunt'),
                'add_new_item' => __('Add New Job Type', 'jobhunt'),
                'edit_item' => __('Edit Job Type', 'jobhunt'),
                "singular_name" => __("Job Type", 'jobhunt')
            ),
            "public" => false,
            'show_ui' => true,
            "show_in_menu" => true,
            "rewrite" => false)
        );
    }

}


/**
 * Start Function How to remove meta
 */
add_action('admin_footer-edit-tags.php', 'cs_remove_catmeta');
if (!function_exists('cs_remove_catmeta')) {

    function cs_remove_catmeta() {
        global $current_screen;
        switch ($current_screen->id) {
            case 'edit-job_type':
                ?>
                <script type="text/javascript">
                    jQuery(window).load(function ($) {
                        jQuery('#parent').parent().remove();
                    });
                </script>
                <?php
                break;
            case 'edit-post_tag':
                break;
        }
    }

}

/**
 * Start Function How to create taxonomies
 */
if (!function_exists('create_locations_taxonomies')) {
    add_action('init', 'create_locations_taxonomies');

    function create_locations_taxonomies() {
        register_taxonomy("cs_locations", array("jobs"), array
            (
            "hierarchical" => true,
            "label" => __("Locations", 'jobhunt'),
            'labels' => array('new_item_name' => __('New Location', 'jobhunt'),
                'add_new_item' => __('Add New Location', 'jobhunt'),
                'edit_item' => __('Edit Location', 'jobhunt'),
                'show_ui' => true,
                "singular_label" => "cs_locations"),
            'not_found' => __('No locations found.', 'jobhunt'),
            "public" => false,
            'show_ui' => true,
            "show_in_menu" => true,
            "rewrite" => false
                )
        );
    }

}

/**
 * Start Function remove taxonomies meta
 */
if (!function_exists('remove_my_taxanomy_meta')) {

    add_action('admin_menu', 'remove_my_taxanomy_meta');

    function remove_my_taxanomy_meta() {
        remove_meta_box('cs_locationsdiv', 'jobs', 'side');
    }

}


if (!function_exists('full_width_location')) {
    add_action('admin_footer', 'full_width_location');

    function full_width_location() {

        $a_get_current_requset_uri = $_SERVER["REQUEST_URI"];
        $a_get_current_requset_uri = explode("?", $a_get_current_requset_uri);
        $a_get_current_taxonomy = isset($a_get_current_requset_uri[1]) ? explode("&", $a_get_current_requset_uri[1]) : '';
        if (is_array($a_get_current_taxonomy) && count($a_get_current_taxonomy) > 0) {
            if (isset($a_get_current_taxonomy[0]) && $a_get_current_taxonomy['0'] == 'taxonomy=cs_locations') {
                echo '<style type="text/css">';
                echo '#col-right {width:100%;}
							#popup_div {
									background-color: #fff;
									border: 2px solid #ccc;
									box-shadow: 10px 10px 5px #888888;
									display: none;
									padding: 12px;
									position: fixed;
									left: 497px;
									top: 90px; 
									z-index: 10000;
								} 
								
								#close_div{float:right;} 
								';
                echo '</style>';
                ?>
                <script type="text/javascript">
                    jQuery(document).ready(function ($) {
                        jQuery(".top .bulkactions").prepend('<a href="javascript:void(0);" id="btn_add" class="button"><?php _e('Add Location', 'jobhunt') ?></a>'); // add link 
                        jQuery("#col-left").hide();

                        var popupDiv = '<div id="popup_div"><span id="close_div" class="icon icon-close icon-close2"></span></div>'; // popup container html
                        jQuery("#wpfooter").prepend(popupDiv); // send to footer div
                        jQuery("#tag-name").attr("required", "true");
                        jQuery(".term-description-wrap").hide();
                        jQuery("#popup_div").hide();
                        // jQuery("#parent").addClass('postform chosen-select');

                        jQuery(document).on('click', '#btn_add', function () {
                            jQuery("#popup_div").show();
                            jQuery("#col-left").show();
                            var texonomy_form = jQuery("#col-left").html();
                            jQuery("#popup_div").append(texonomy_form);
                            jQuery("#col-container #col-left").hide();
                            jQuery("#col-container #col-left").html('');
                            return false;
                        });
                        jQuery(document).on('click', '#close_div', function () {
                            jQuery("#popup_div").slideUp();
                        })



                    });
                </script>

                <?php
            }
        }
    }

}
/**
 * Start Function How to create coloumes of post and theme
 */
if (!function_exists('theme_columns')) {

    add_filter("manage_edit-cs_locations_columns", 'theme_columns');

    function theme_columns($theme_columns) {
        $new_columns = array(
            'cb' => '<input type="checkbox" />',
            'name' => __('Name', 'jobhunt'),
            'header_icon' => '',
            'slug' => __('Slug', 'jobhunt'),
            'posts' => __('Posts', 'jobhunt')
        );
        return $new_columns;
    }

}

if (!function_exists('custom_specialisms_columns')) {

    add_filter("manage_edit-specialisms_columns", 'custom_specialisms_columns');

    function custom_specialisms_columns($custom_specialisms_columns) {
        $new_columns = array(
            'cb' => '<input type="checkbox" />',
            'name' => __('Name'),
            'header_icon' => '',
            'slug' => __('Slug', 'jobhunt'),
            'count_employer' => __('Count Employer', 'jobhunt'),
            'count_candidate' => __('Count candidate', 'jobhunt'),
            'count_job' => __('Count jobs', 'jobhunt'),
        );
        return $new_columns;
    }

}

/**
 * Start Function for custom specialisms values
 */
if (!function_exists('custom_specialisms_columns_values')) {

    function custom_specialisms_columns_values($deprecated, $column_name, $term_id = '') {

        if ($column_name == 'count_employer') {  // count employer
            echo get_post_count_by_term(array('term_id' => $term_id, 'post_type' => 'employer'), 'specialisms');
        }
        if ($column_name == 'count_candidate') { // count candidate
            echo get_post_count_by_term(array('term_id' => $term_id, 'post_type' => 'candidate'), 'specialisms');
        }
        if ($column_name == 'count_job') {  // count job
            echo get_post_count_by_term(array('term_id' => $term_id, 'post_type' => 'jobs'), 'specialisms');
        }
    }

    add_filter('manage_specialisms_custom_column', 'custom_specialisms_columns_values', 10, 3);
}

/**
 * Start Function for count terms
 */
if (!function_exists('get_post_count_by_term')) {

    function get_post_count_by_term($a_meta, $taxonomy_type) {
        $post_count = 0;

        if (is_array($a_meta)) {
            $args = array(
                'post_type' => $a_meta['post_type'], 'posts_per_page' => "-1",
                'tax_query' => array(
                    array(
                        'taxonomy' => $taxonomy_type,
                        'field' => 'id',
                        'terms' => isset($a_meta['term_id']) ? $a_meta['term_id'] : '',
                    )
                ),
                'post_status' => 'publish'
            );
            $query = new WP_Query($args);
            $post_count = $query->post_count;
        }
        return $post_count;
    }

}