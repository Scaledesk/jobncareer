<?php
/**
 * The template for Job Detail 
 */
// set view in db if user comes first time cookie
global $post, $current_user, $cs_form_fields2, $cs_plugin_options;
$cs_job_posted_date_formate = 'd-m-Y H:i:s';
$cs_job_expired_date_formate = 'd-m-Y H:i:s';
$current_post_id = get_the_ID();

$login_user_name = '';
$login_user_email = '';
$login_user_phone = '';
$cs_emp_funs = new cs_employer_functions();
if (is_user_logged_in()) {
    $user_info = get_userdata($current_user->ID);
    if (isset($user_info->display_name))
        $login_user_name = $user_info->display_name;
    if (isset($user_info->user_email))
        $login_user_email = $user_info->user_email;

    $login_user_phone = get_user_meta($user_info->ID, 'cs_phone_number', true);
}
$cs_job_status = get_post_meta($post->ID, 'cs_job_status', true);
$cs_job_emplyr = get_post_meta($post->ID, 'cs_job_username', true);
$cs_post_view = true;
if ($cs_job_status != 'active') {
    $cs_post_view = false;
    if (is_user_logged_in() && $cs_job_emplyr == $current_user->ID) {
        $cs_post_view = true;
        $cs_owner_view = true;
    }
    if (is_user_logged_in() && current_user_can('administrator')) {
        $cs_post_view = true;
        $cs_owner_view = true;
    }
}
if ($cs_post_view == true) {
    if (have_posts()):
        while (have_posts()) : the_post();
            $job_post = $post;
            // get all job types
            $all_specialisms = get_the_terms($job_post->ID, 'specialisms');
            $specialisms_values = '';
            $specialism_flag = 1;
            if ($all_specialisms != '') {
                foreach ($all_specialisms as $specialismsitem) {
                    $specialisms_values .= $specialismsitem->slug;
                    if ($specialism_flag != count($all_specialisms)) {
                        $specialisms_values .= ", ";
                    }
                    $specialism_flag++;
                }
            }
            // get posted user

            $cs_job_username = get_post_meta(get_the_ID(), 'cs_job_username', true);
            $employer_post = get_userdata($cs_job_username);
            // count employer jobs
            $mypost = array('posts_per_page' => "-1", 'post_type' => 'jobs', 'order' => "DESC", 'orderby' => 'post_date',
                'post_status' => 'publish', 'ignore_sticky_posts' => 1,
                'meta_query' => array(
                    array(
                        'key' => 'cs_job_username',
                        'value' => $cs_job_username,
                        'compare' => '=',
                    ),
                    array(
                        'key' => 'cs_job_posted',
                        'value' => strtotime(date($cs_job_posted_date_formate)),
                        'compare' => '<=',
                    ),
                    array(
                        'key' => 'cs_job_expired',
                        'value' => strtotime(date($cs_job_expired_date_formate)),
                        'compare' => '>=',
                    ),
                    array(
                        'key' => 'cs_job_status',
                        'value' => 'active',
                        'compare' => '=',
                    ),
                )
            );
            $loop_count = new WP_Query($mypost);
            $count_employer_jobs = $loop_count->post_count;
            $job_address = get_user_address_string_for_detail($job_post->ID);

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

            $all_job_type = get_the_terms($post->ID, 'job_type');
            // var_dump( $all_job_type);
            $job_type_values = '';
            $job_type_class = '';
            $job_type_flag = 1;
            $cs_search_result_page = isset($cs_plugin_options['cs_search_result_page']) ? $cs_plugin_options['cs_search_result_page'] : '';
            if ($all_job_type != '') {
                foreach ($all_job_type as $job_type) {

                    $t_id_main = $job_type->term_id;
                    $job_type_color_arr = get_option("job_type_color_$t_id_main");
                    $job_type_color = '';
                    if (isset($job_type_color_arr['text'])) {
                        $job_type_color = $job_type_color_arr['text'];
                    }
                    //$job_type_class .= get_term_link($t_id_main);	
                    $cs_link = ' href="javascript:void(0);"';
                    if ($cs_search_result_page != '') {
                       $cs_link = ' href="' . esc_url_raw(get_page_link($cs_search_result_page) . '?job_type=' . $job_type->slug) . '"';
                    }
                    $job_type_values .= '<a ' . force_balance_tags($cs_link) . ' class="freelance" style="border-color:' . $job_type_color . '">' . $job_type->name . '</a>';

                    if ($job_type_flag != count($all_specialisms)) {
                        $job_type_values .= " ";
                        $job_type_class .= " ";
                    }
                    $job_type_flag++;
                }
            }
            ?>
            <div class="main-section jobs-detail-4">
                <?php
                // getting from plugin options
                $cs_job_detail_cover_img = isset($cs_plugin_options['cs_job_default_cover_image']) ? $cs_plugin_options['cs_job_default_cover_image'] : '';
                $cs_job_default_cover_image_top_padding = isset($cs_plugin_options['cs_job_default_cover_image_top_padding']) ? $cs_plugin_options['cs_job_default_cover_image_top_padding'] : '0';
                $cover_image_top_padding = 'padding-top:' . $cs_job_default_cover_image_top_padding . 'px;';

                $cs_title_f_size = isset($cs_plugin_options['cs_job_default_header_title_f_size']) ? $cs_plugin_options['cs_job_default_header_title_f_size'] : '';
                $cs_title_color = isset($cs_plugin_options['cs_job_default_header_title_color']) ? $cs_plugin_options['cs_job_default_header_title_color'] : '';
                $cs_title_style_str = '';
                if (($cs_title_f_size != '' && $cs_title_f_size > 0) || $cs_title_color != '') {
                    $cs_title_style_str .= ' style="';
                    if ($cs_title_f_size != '' && $cs_title_f_size > 0) {
                        $cs_title_style_str .= ' font-size: ' . $cs_title_f_size . 'px !important;';
                    }
                    if ($cs_title_color != '') {
                        $cs_title_style_str .= ' color: ' . $cs_title_color . ' !important;';
                    }
                    $cs_title_style_str .= '"';
                }
                ?>
                <div class="page-section">
                    <div class="<?php if (isset($cs_plugin_options['cs_plugin_single_container']) && $cs_plugin_options['cs_plugin_single_container'] == 'on') echo 'container' ?>">
                        <div class="row">
                            <div class="section-fullwidtht col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="row">
                                    <div class="section-content col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <div class="jobs-info">
                                            <?php
                                            ob_start();
                                            $flag = 1;
                                            if (isset($employer_post) && $employer_post != '') {

                                                // $cs_employee_address = get_user_meta($employer_post->ID, 'cs_post_comp_address', true);
                                                $cs_employee_web_http = $employer_post->user_url;
                                                $cs_employee_email = $employer_post->user_email;
                                                $cs_employee_web = preg_replace('#^https?://#', '', $cs_employee_web_http);
                                                $cs_employee_facebook = get_user_meta($employer_post->ID, 'cs_facebook', true);
                                                $cs_employee_twitter = get_user_meta($employer_post->ID, 'cs_twitter', true);
                                                $cs_employee_linkedin = get_user_meta($employer_post->ID, 'cs_linkedin', true);
                                                $cs_employee_google_plus = get_user_meta($employer_post->ID, 'cs_google_plus', true);
                                                $cs_employee_employer_img = get_user_meta($employer_post->ID, 'user_img', true);
                                                $cs_employee_employer_img = cs_get_img_url($cs_employee_employer_img, 'cs_media_5');
                                                if (!cs_image_exist($cs_employee_employer_img) || $cs_employee_employer_img == "") {
                                                    $cs_employee_employer_img = esc_url(wp_jobhunt::plugin_url() . 'assets/images/img-not-found4x3.jpg');
                                                }
                                                $cs_employee_emp_username = $employer_post->display_name;
                                                ?> 
                                                <div class="cs-text">
                                                    <?php if ($specialisms_values <> '') { ?>
                                                        <?php echo force_balance_tags($job_type_values); ?>
                                                    <?php } ?>
                                                    <ul class="post-options">
                                                        <?php if ($job_address <> '') {
                                                            ?>
                                                            <li><i class="icon-location6"></i><a><?php echo esc_html($job_address); ?></a></li>
                                                            <?php
                                                        }
                                                        $cs_job_posted_date = get_post_meta($job_post->ID, 'cs_job_posted', true);
                                                        if (isset($cs_job_posted_date) && $cs_job_posted_date != '') {
                                                            ?>
                                                            <li>
                                                                <i class="icon-calendar5"></i>
                                                                <?php _e("Post Date:", "jobhunt"); ?>  <span><?php echo date_i18n(get_option('date_format'), (get_post_meta($job_post->ID, 'cs_job_posted', true))); ?></span>
                                                            </li>
                                                            <?php
                                                        }
                                                        // Application closing date frontend filter in application deadline add on
                                                        echo apply_filters('job_hunt_application_deadline_date_frontend', $current_post_id);
                                                        ?>
                                                    </ul>
                                                </div>
                                                <div class="apply-buttons">
                                                    <?php
                                                    $user = cs_get_user_id();
                                                    if (!is_user_logged_in() || !$cs_emp_funs->is_employer()) {
                                                        ?>
                                                        <?php cs_add_dirpost_favourite($job_post->ID); ?>
                                                    <?php } else {
                                                        ?>
                                                        <a class="cs-add-wishlist btn small" href="javascript:void(0)"  id="<?php echo 'addjobs_to_wishlist' . intval($job_post->ID); ?>" 
                                                           onclick="show_alert_msg('<?php echo __("Become a candidate then try again.", "jobhunt"); ?>')" >
                                                            <i class="icon-heart-o"></i></a>
                                                        <?php
                                                    }
                                                    if (!is_user_logged_in()) {
                                                        ?>
                                                        <a class="btn large linkedin social_login_login_linkedin" href="#" data-applyjobid="<?php echo intval($job_post->ID); ?>">
                                                            <div data-applyjobid="<?php echo intval($job_post->ID); ?>" class="linkedin_jobid_apply"></div><i class="icon-linkedin4"></i><?php _e('Apply with Linkedin', 'jobhunt'); ?>
                                                        </a>
                                                        <?php do_action('apply_with_facebook_button', $job_post->ID); ?>
                                                    <?php } ?>
                                                    <?php
                                                    $class_apply = '';
                                                    if (isset($_SESSION['apply_job_id'])) {
                                                        $class_apply = 'applyauto';
                                                        unset($_SESSION['apply_job_id']);
                                                    }
                                                    if (is_user_logged_in()) {
                                                        $user = cs_get_user_id();
                                                        $user_role = cs_get_loginuser_role();
                                                        if (isset($user_role) && $user_role <> '' && $user_role == 'cs_candidate') {
                                                            $cs_applied_list = array();
                                                            if (isset($user) and $user <> '' and is_user_logged_in()) {
                                                                $finded_result_list = cs_find_index_user_meta_list($job_post->ID, 'cs-user-jobs-applied-list', 'post_id', cs_get_user_id());
                                                                if (is_array($finded_result_list) && !empty($finded_result_list)) {
                                                                    ?>
                                                                    <a href="javascript:void(0);" class="btn large like applied_icon" >
                                                                        <?php _e('Applied', 'jobhunt') ?>
                                                                    </a>
                                                                    <?php
                                                                } else {
                                                                    ?>
                                                                    <a class="btn large like applied_icon <?php echo $class_apply; ?>" onclick="cs_addjobs_left_to_applied('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', '<?php echo intval($job_post->ID); ?>', this)" >
                                                                        <?php _e('Apply for this job', 'jobhunt') ?>
                                                                    </a>
                                                                    <?php
                                                                }
                                                            } else {
                                                                ?>
                                                                <a class="btn large like applied_icon <?php echo $class_apply; ?>" onclick="cs_addjobs_left_to_applied('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', '<?php echo intval($job_post->ID); ?>', this)" > 
                                                                    <?php _e('Apply for this job', 'jobhunt') ?>
                                                                </a>	
                                                                <?php
                                                            }
                                                        }
                                                    } else {
                                                        $cs_rand_id = rand(34563, 34323990);
                                                        ?>
                                                        <a href="javascript:void(0);" class="btn large like applied_icon" onclick="trigger_func('#btn-header-main-login');"> 
                                                            <?php _e('Apply Now', 'jobhunt') ?></a>
                                                    <?php } ?>
                                                </div>

                                                <?php
                                            }
                                            $job_info_output = ob_get_clean();
                                            echo apply_filters('_2_columns_job_info', $job_info_output, $current_post_id);
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="page-section">
                    <div class="<?php if (isset($cs_plugin_options['cs_plugin_single_container']) && $cs_plugin_options['cs_plugin_single_container'] == 'on') echo 'container' ?>">
                        <div class="row">
                            <div class="section-content col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="rich-editor-text">
                                    <h6><?php _e('Job Description', 'jobhunt'); ?></h6>
                                    <?php
                                    wp_reset_query();
                                    the_content();
                                    echo apply_filters('view_more', $current_post_id);
                                    ?>
                                    <?php ob_start(); ?>
                                    <?php if (cs_social_share(0)) { ?>
                                        <div class="social-media">
                                            <span><?php _e('Share', 'jobhunt'); ?></span>
                                            <ul>
                                                <?php
                                                echo cs_social_share(0);
                                                ?>
                                            </ul>
                                        </div>
                                    <?php } ?>
                                    <?php
                                    $social_links = ob_get_clean();
                                    echo apply_filters('social_links', $social_links, $current_post_id);
                                    ?>
                                </div>
                                <div class="cs-content-holder">
                                    <section class="cs-featured-jobs list">

                                        <div class="featured-holder">
                                            <?php
                                            ob_start();
                                            $filter_arr2[] = '';
                                            // specilisim filter for all jobs
                                            $specialisms = '';
                                            if ($specialisms_values != '')
                                                $specialisms = explode(",", $specialisms_values);
                                            if ($specialisms != '' && $specialisms != 'All specialisms') {
                                                $filter_multi_spec_arr = ['relation' => 'OR',];
                                                foreach ($specialisms as $specialisms_key) {
                                                    if ($specialisms_key != '') {
                                                        $filter_multi_spec_arr[] = array(
                                                            'taxonomy' => 'specialisms',
                                                            'field' => 'slug',
                                                            'terms' => array($specialisms_key)
                                                        );
                                                    }
                                                }
                                                $filter_arr2[] = array(
                                                    $filter_multi_spec_arr
                                                );
                                            }
                                            $featured_job_mypost = array('posts_per_page' => "10", 'post_type' => 'jobs', 'order' => "DESC", 'orderby' => 'post_date',
                                                'post_status' => 'publish', 'ignore_sticky_posts' => 1,
                                                'post__not_in' => array($job_post->ID),
                                                'tax_query' => array(
                                                    'relation' => 'AND',
                                                    $filter_arr2
                                                ),
                                                'meta_query' => array(
                                                    array(
                                                        'key' => 'cs_job_posted',
                                                        'value' => strtotime(date($cs_job_posted_date_formate)),
                                                        'compare' => '<=',
                                                    ),
                                                    array(
                                                        'key' => 'cs_job_expired',
                                                        'value' => strtotime(date($cs_job_expired_date_formate)),
                                                        'compare' => '>=',
                                                    ),
                                                    array(
                                                        'key' => 'cs_job_status',
                                                        'value' => 'active',
                                                        'compare' => '=',
                                                    ),
                                                )
                                            );

                                            // Exclude expired jobs from listing
                                            $featured_job_mypost = apply_filters('job_hunt_jobs_listing_parameters', $featured_job_mypost);

                                            $featured_job_loop_count = new WP_Query($featured_job_mypost);
                                            $featuredjob_count_post = $featured_job_loop_count->post_count;
                                            if ($featuredjob_count_post > 0) {
                                                ?>
                                                <h4><?php _e('Related Jobs', 'jobhunt') ?> (<?php echo ( $featuredjob_count_post ); ?>) </h4>
                                                <ul class="cs-company-jobs">
                                                    <?php
                                                    // getting if record not found

                                                    $cs_search_result_page = isset($cs_plugin_options['cs_search_result_page']) ? $cs_plugin_options['cs_search_result_page'] : '';
                                                    $flag = 1;
                                                    while ($featured_job_loop_count->have_posts()) : $featured_job_loop_count->the_post();
                                                        global $post;
                                                        $cs_job_posted = get_post_meta($post->ID, 'cs_job_posted', true);
                                                        $cs_job_employer = get_post_meta($post->ID, "cs_job_username", true); //
                                                        $cs_jobs_thumb_url = get_the_author_meta('user_img', $cs_job_employer);
                                                        $cs_jobs_address = get_user_address_string_for_list($post->ID);
                                                        if (!cs_image_exist($cs_jobs_thumb_url) || $cs_jobs_thumb_url == "") {
                                                            $cs_jobs_thumb_url = esc_url(wp_jobhunt::plugin_url() . 'assets/images/img-not-found4x3.jpg');
                                                        }
                                                        $all_job_type = get_the_terms($post->ID, 'job_type');
                                                        $job_type_values = '';
                                                        $job_type_class = '';
                                                        $job_type_flag = 1;
                                                        if ($all_job_type != '') {
                                                            foreach ($all_job_type as $job_type) {

                                                                $t_id_main = $job_type->term_id;
                                                                $job_type_color_arr = get_option("job_type_color_$t_id_main");
                                                                $job_type_color = '';
                                                                if (isset($job_type_color_arr['text'])) {
                                                                    $job_type_color = $job_type_color_arr['text'];
                                                                }
                                                                $cs_link = ' href="javascript:void(0);"';
                                                                if ($cs_search_result_page != '') {
                                                                    $cs_link = ' href="' . esc_url_raw(get_page_link($cs_search_result_page) . '?job_type=' . $job_type->slug) . '"';
                                                                }
                                                                $job_type_values .= '<a ' . force_balance_tags($cs_link) . ' class="categories" style="color:' . $job_type_color . '">' . $job_type->name . '</a>';

                                                                if ($job_type_flag != count($all_specialisms)) {
                                                                    $job_type_values .= " ";
                                                                    $job_type_class .= " ";
                                                                }
                                                                $job_type_flag++;
                                                            }
                                                        }
                                                        ?>
                                                        <li>
                                                            <div class="cs-text">
                                                                <span><a href="<?php echo esc_url(get_permalink($post->ID)); ?>"><?php echo the_title(); ?></a></span>
                                                                <?php
                                                                _e(' on', 'jobhunt');

                                                                if (isset($cs_job_posted) && $cs_job_posted != '') {
                                                                    ?>
                                                                    <span class="post-date"><?php echo date_i18n(get_option('date_format'), $cs_job_posted) ?></span>
                                                                <?php } ?>
                                                                <?php echo force_balance_tags($job_type_values); ?>
                                                            </div>
                                                        </li>

                                                        <?php
                                                        $flag++;
                                                    endwhile;
                                                    wp_reset_postdata();
                                                    ?>
                                                </ul>
                                            <?php } ?>
                                            <?php
                                            $related_jobs_output = ob_get_clean();
                                            echo apply_filters('related_jobs', $related_jobs_output, $current_post_id);
                                            ?>
                                        </div>

                                    </section>
                                </div>   
                            </div>
                            <div class="section-sidebar col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="jobs-detail-listing">
                                    <ul class="row">
                                        <?php
                                        $cs_job_cus_fields = get_option("cs_job_cus_fields");
                                        if (is_array($cs_job_cus_fields) && sizeof($cs_job_cus_fields) > 0) {

                                            $custom_field_box = 1;

                                            foreach ($cs_job_cus_fields as $cus_field) {
                                                if ($cus_field['meta_key'] != '') {
                                                    $data = get_post_meta($job_post->ID, $cus_field['meta_key'], true);
                                                    // empty check of value
                                                    if ($cus_field['label'] != '')
                                                        if ($data != "") {
                                                            ?>  <li class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                                <div class="listing-inner">
                                                                    <i class="<?php echo sanitize_html_class($cus_field['fontawsome_icon']) ?>"></i>
                                                                    <div class="cs-text">
                                                                        <span><?php echo esc_html($cus_field['label']); ?> </span> <strong><?php
                                                                            // check the data is array or not
                                                                            if (is_array($data)) {
                                                                                $data_flage = 1;
                                                                                $comma = '';
                                                                                foreach ($data as $datavalue) {
                                                                                    if ($cus_field['type'] == 'dropdown') {
                                                                                        $options = $cus_field['options']['value'];
                                                                                        if (isset($options)) {
                                                                                            $finded_array = array_search($datavalue, $options);
                                                                                            $datavalue = isset($finded_array) ? $cus_field['options']['label'][$finded_array] : '';
                                                                                        }
                                                                                        echo $comma . esc_html($datavalue);
                                                                                        $comma = ', ';
                                                                                    } else {
                                                                                        echo esc_html($datavalue);
                                                                                    }
                                                                                    if ($data_flage != count($data)) {
                                                                                        echo "";
                                                                                    }
                                                                                    $data_flage++;
                                                                                }
                                                                            } else {
                                                                                if ($cus_field['type'] == 'dropdown') {
                                                                                    $options = $cus_field['options']['value'];
                                                                                    if (isset($options)) {
                                                                                        $finded_array = array_search($data, $options);
                                                                                        $data = isset($finded_array) ? $cus_field['options']['label'][$finded_array] : '';
                                                                                    }
                                                                                    echo esc_html($data);
                                                                                } else {
                                                                                    echo esc_html($data);
                                                                                }
                                                                            }
                                                                            ?></strong> </div>
                                                                </div>
                                                            </li><?php
                                                            if (($custom_field_box % 3 == 0 && $custom_field_box > 0) && count($cs_job_cus_fields) != $custom_field_box)
                                                                echo '';
                                                            $custom_field_box++;
                                                        }
                                                }
                                            }
                                            if ($custom_field_box % 3 != 0 && $custom_field_box > 0)
                                                echo "";
                                        }
                                        ?>

                                    </ul>
                                </div>
                                <?php
                                ob_start();
                                $flag = 1;
                                if (isset($employer_post) && $employer_post != '') {
                                    $cs_employee_address = get_user_meta($employer_post->ID, 'cs_post_comp_address', true);
                                    $cs_employee_web_http = $employer_post->user_url;
                                    $cs_employee_email = $employer_post->user_email;
                                    $cs_employee_web = preg_replace('#^https?://#', '', $cs_employee_web_http);
                                    $cs_employee_facebook = get_user_meta($employer_post->ID, 'cs_facebook', true);
                                    $cs_employee_twitter = get_user_meta($employer_post->ID, 'cs_twitter', true);
                                    $cs_employee_linkedin = get_user_meta($employer_post->ID, 'cs_linkedin', true);
                                    $cs_employee_google_plus = get_user_meta($employer_post->ID, 'cs_google_plus', true);
                                    $cs_employee_employer_img = get_user_meta($employer_post->ID, 'user_img', true);
                                    $cs_employee_employer_img = cs_get_img_url($cs_employee_employer_img, 'cs_media_5');
                                    if (!cs_image_exist($cs_employee_employer_img) || $cs_employee_employer_img == "") {
                                        $cs_employee_employer_img = esc_url(wp_jobhunt::plugin_url() . 'assets/images/img-not-found4x3.jpg');
                                    }
                                    $cs_employee_emp_username = $employer_post->display_name;
                                    $cs_phone_number = get_user_meta($employer_post->ID, 'cs_phone_number', true);
                                    $cs_email = $cs_email = $employer_post->user_email;
                                    ?>
                                    <div class="company-info">
                                        <div class="cs-media">
                                            <figure><a href="<?php echo esc_url(get_author_posts_url($employer_post->ID)); ?>">
                                                    <img src="<?php echo esc_url($cs_employee_employer_img); ?>" alt="<?php echo esc_html($employer_post->post_title); ?>" /></a> </figure>
                                        </div>
                                        <div class="cs-text">
                                            <strong> <a href="<?php echo esc_url(get_permalink($employer_post->ID)); ?>"> <?php the_title(); ?></a></strong>
                                            <span>    <?php if ($cs_employee_address != '') echo '<span> ' . $cs_employee_address . '</span>'; ?> </span>
                                            <a href="<?php echo esc_url(get_author_posts_url($employer_post->ID)); ?>"><?php _e('View all Jobs', 'jobhunt'); ?></a>
                                        </div>
                                        <ul class="admin-contect">
                                            <?php if ($cs_phone_number <> '') { ?>
                                                <li>
                                                    <i class="icon-phone8"></i>
                                                    <p>
                                                        <?php echo esc_html($cs_phone_number); ?> 
                                                    </p>
                                                </li><?php } if ($cs_email <> '') { ?>
                                                <li>
                                                    <i class="icon-mail6"></i>
                                                    <p>
                                                        <a href="mailto:<?php echo sanitize_email($cs_email); ?>"><?php echo ($cs_email); ?></a>
                                                    </p>
                                                </li>
                                            <?php } if ($cs_employee_web <> '') { ?>
                                                <li>
                                                    <i class="icon-link4"></i>
                                                    <p>
                                                        <a href="<?php echo esc_url($cs_employee_web_http); ?>"><?php echo esc_html($cs_employee_web); ?></a>
                                                    </p>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                    <?php
                                }

                                $company_info_output = ob_get_clean();
                                echo apply_filters('company_info', $company_info_output, $current_post_id);

                                ob_start();
                                $employer_post_id = is_object($employer_post) ? $employer_post->ID : '';
                                $post_loc_latitude = get_user_meta($employer_post_id, 'cs_post_loc_latitude', true);
                                $post_loc_longitude = get_user_meta($employer_post_id, 'cs_post_loc_longitude', true);
                                $post_zoom_level = get_user_meta($employer_post_id, 'cs_post_loc_zoom', true);
                                if (!isset($post_zoom_level) || $post_zoom_level == '') {
                                    $post_zoom_level = '8';
                                }

                                if ($post_loc_latitude != '' && $post_loc_latitude != '') {
                                    $cs_jobcareer_theme_options = get_option('cs_theme_options');
                                    $cs_map_view = isset($cs_jobcareer_theme_options['def_map_style']) ? $cs_jobcareer_theme_options['def_map_style'] : '';
                                    ?>
                                    <div class="map-sec">
                                        <?php
                                        $arg = array(
                                            'map_height' => '250',
                                            'map_lat' => $post_loc_latitude,
                                            'map_lon' => $post_loc_longitude,
                                            'map_zoom' => $post_zoom_level,
                                            'map_type' => 'ROADMAP',
                                            'map_info' => preg_replace("/\r|\n/", "", $cs_employee_address),
                                            'map_info_width' => '250',
                                            'map_info_height' => '100',
                                            'map_marker_icon' => wp_jobhunt::plugin_url() . 'assets/images/recruiter-map-icon.png',
                                            'map_show_marker' => 'true',
                                            'map_controls' => 'false',
                                            'map_draggable' => 'true',
                                            'map_scrollwheel' => 'true',
                                            'map_border' => 'yes',
                                            'cs_map_style' => $cs_map_view,
                                        );
                                        if (function_exists('cs_job_map')) {
                                            cs_job_map($arg);
                                        }
                                        ?>
                                    </div>

                                    <?php
                                }

                                $map_output = ob_get_clean();
                                echo apply_filters('_2_columns_map', $map_output, $current_post_id);

                                $cs_plugin_options = get_option('cs_plugin_options');
                                $cs_safetysafe_switch = $cs_plugin_options['cs_safetysafe_switch'];
                                if ($cs_safetysafe_switch != '' && $cs_safetysafe_switch == 'on') {
                                    ?>
                                    <div class="safety-save">
                                        <div class="warning-title ">
                                            <h4 class="cs-color"><i class="icon-warning4"></i><?php _e('Safety Information', 'jobhunt') ?></h4>
                                        </div>
                                        <div class="cs-text">
                                            <ul class="save-info">
                                                <?php
                                                $cs_safety_title_array = isset($cs_plugin_options['cs_safety_title_array']) ? $cs_plugin_options['cs_safety_title_array'] : '';
                                                $cs_safety_desc_array = isset($cs_plugin_options['cs_safety_desc_array']) ? $cs_plugin_options['cs_safety_desc_array'] : '';
                                                if (is_array($cs_safety_desc_array) && sizeof($cs_safety_desc_array) > 0) {
                                                    $cs_safety_count = 0;
                                                    foreach ($cs_safety_desc_array as $cs_safety_desc) {
                                                        ?>
                                                        <li>
                                                            <h3><?php echo esc_html($cs_safety_title_array[$cs_safety_count]); ?></h3>
                                                            <p><?php echo esc_html($cs_safety_desc); ?></p>
                                                        </li>
                                                        <?php
                                                        $cs_safety_count++;
                                                    }
                                                } else {
                                                    ?>
                                                    <li>
                                                        <p><?php _e('There is no record found', 'jobhunt') ?></p>
                                                    </li>
                                                    <?php
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <?php
        endwhile;
    endif;
} else {
    ?>
    <div class="main-section">
        <div class="<?php if (isset($cs_plugin_options['cs_plugin_single_container']) && $cs_plugin_options['cs_plugin_single_container'] == 'on') echo 'container' ?>">
            <div class="row">
                <div class="col-md-12">
                    <div class="unauthorized">
                        <h1>
                            <?php _e('You are not <span>authorized</span>', 'jobhunt') ?>
                        </h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}