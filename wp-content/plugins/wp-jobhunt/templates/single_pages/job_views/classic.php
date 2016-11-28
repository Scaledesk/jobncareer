<?php
/**
 * The template for Job Detail 
 */
// set view in db if user comes first time cookie
global $post, $current_user, $cs_form_fields2, $cs_plugin_options;

$cs_job_posted_date_formate = 'd-m-Y H:i:s';
$cs_job_expired_date_formate = 'd-m-Y H:i:s';
$current_post_id = get_the_ID();
?>
<div id="main">
    <?php
    /*
     *  login user detail
     *      
     */
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
                // getting employer information
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
                ?>
                <div class="main-section">
                    <?php
                    // getting from plugin options
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
                    <div class="<?php if (isset($cs_plugin_options['cs_plugin_single_container']) && $cs_plugin_options['cs_plugin_single_container'] == 'on') echo 'container' ?>">
                        <div class="row">
                            <aside class="col-md-4 employer-info">
                                <?php
                                ob_start();
                                $flag = 1;
                                if (isset($employer_post) && $employer_post != '') {
                                    // getting employer data
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
                                    <div class="employer-company">
                                        <figure><a href="<?php echo esc_url(get_author_posts_url($employer_post->ID)); ?>"><img src="<?php echo esc_url($cs_employee_employer_img); ?>" alt="<?php echo esc_html($employer_post->post_title); ?>" /></a><span class="cs-bgcolor"><?php echo esc_html($count_employer_jobs); ?></span></figure>
                                        <div class="cs-text">
                                            <h4><a href="<?php echo esc_url(get_permalink($employer_post->ID)); ?>"><?php echo esc_html($employer_post->post_title); ?></a></h4>
                                            <ul class="detail-list">
                                                <?php if ($job_address != '') echo '<li><address> ' . $job_address . '</address></li>'; ?>
                                                <?php
                                                if ($cs_employee_web != '') {
                                                    echo '<li><a href="' . esc_url($cs_employee_web_http) . '" class="site-link">' . $cs_employee_web . '</a></li>';
                                                }
                                                ?>
                                                <li>
                                                    <span><a data-target="#cs-msgbox-<?php echo absint($job_post->ID) ?>" data-toggle="modal" href="#"><i class="icon-mail"></i> <?php _e('Email', 'jobhunt'); ?></a></span>
                                                </li>
                                            </ul>
                                            <ul class="share-medea">
                                                <?php if ($cs_employee_facebook != '') { ?>
                                                    <li><a target="_blank" href="<?php echo esc_url($cs_employee_facebook); ?>" data-original-title="facebook"><i class="icon-facebook2"></i></a></li>
                                                    <?php
                                                }
                                                if ($cs_employee_twitter != '') {
                                                    ?>
                                                    <li><a target="_blank" href="<?php echo esc_url($cs_employee_twitter); ?>" data-original-title="twitter"><i class="icon-twitter2"></i></a></li>
                                                    <?php
                                                }
                                                if ($cs_employee_linkedin != '') {
                                                    ?>
                                                    <li><a target="_blank" href="<?php echo esc_url($cs_employee_linkedin); ?>" data-original-title="linkedin"><i class="icon-linkedin5"></i></a></li>
                                                    <?php
                                                }
                                                if ($cs_employee_google_plus != '') {
                                                    ?>
                                                    <li><a target="_blank" href="<?php echo esc_url($cs_employee_google_plus); ?>" data-original-title="google" ><i class="icon-googleplus7"></i></a></li>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                    </div>
                                    <?php
                                    $flag++;
                                }
                                $company_info_output = ob_get_clean();
                                echo apply_filters('company_info', $company_info_output, $current_post_id);
                                // get safety safe from options
                                $cs_plugin_options = get_option('cs_plugin_options');
                                $cs_safetysafe_switch = $cs_plugin_options['cs_safetysafe_switch'];
                                if ($cs_safetysafe_switch != '' && $cs_safetysafe_switch == 'on') {
                                    ?>
                                    <div class="safety-save">
                                        <div class="warning-title demo">
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
                            </aside>
                            <section class="page-content col-md-8">
                                <div class="cs-content-holder">
                                    <section class="col-md-12 job-information">

                                        <div class="row">
                                            <div class="job-info">
                                                <?php ob_start(); ?>
                                                <h2><?php
                                                    if (isset($cs_owner_view) && $cs_owner_view == true) {
                                                        echo ucfirst($cs_job_status) . ': ';
                                                    } echo esc_html($job_post->post_title);
                                                    ?></h2> 
                                                <ul class="posted-detail pull-left">
                                                    <li><i class="icon-calendar5"></i><?php _e("Post Date:", "jobhunt"); ?><span><?php echo date_i18n(get_option('date_format'), (get_post_meta($job_post->ID, 'cs_job_posted', true))); ?></span></li>
                                                    <?php
                                                    // Application closing date frontend filter in application deadline add on
                                                    echo apply_filters('job_hunt_application_deadline_date_frontend', $current_post_id);
                                                    ?>
            <!--                                                    <li><i class="icon-warning4"></i><?php _e("Deadline", "jobhunt"); ?><span><?php echo date_i18n(get_option('date_format'), (get_post_meta($job_post->ID, 'cs_job_expired', true))); ?></span></li>-->
                                                    <li><i class="icon-eye7"></i><?php _e("Views", "jobhunt"); ?><span><?php
                                                            echo get_post_meta($job_post->ID, "cs_count_views", true);
                                                            ;
                                                            ?></span>
                                                    </li>
                                                </ul>
                                                <?php
                                                // getting job application count
                                                $applications = 0;
                                                $cs_applicants = count_usermeta('cs-user-jobs-applied-list', serialize(strval($job_post->ID)), 'LIKE', true);
                                                $applications += count($cs_applicants);
                                                ?>
                                                <a class="btn-application pull-right cs-color" href="javascript:void(0);"><i class="icon-text"></i><span id="applications_count"><?php echo esc_html($applications); ?></span> <?php _e('Applications', 'jobhunt'); ?></a> 
                                                <?php
                                                $company_info_output = ob_get_clean();
                                                echo apply_filters('classic_job_info', $company_info_output, $current_post_id);
                                                ?>
                                            </div>
                                            <div class="job-detail">
                                                <ul><?php
                                                    $cs_job_cus_fields = get_option("cs_job_cus_fields");
                                                    if (is_array($cs_job_cus_fields) && sizeof($cs_job_cus_fields) > 0) {
                                                        echo '<li>';
                                                        $custom_field_box = 1;

                                                        foreach ($cs_job_cus_fields as $cus_field) {
                                                            if ($cus_field['meta_key'] != '') {
                                                                $data = get_post_meta($job_post->ID, $cus_field['meta_key'], true);
                                                                // empty check of value
                                                                if ($cus_field['label'] != '')
                                                                    if ($data != "") {
                                                                        ?><div class="col-md-4"> <span><?php echo esc_html($cus_field['label']); ?> </span><strong class="text-capitalize"><?php
                                                                        // check the data is array or not
                                                                        if (is_array($data)) {
                                                                            $data_flage = 1;
                                                                            foreach ($data as $datavalue) {
                                                                                if ($cus_field['type'] == 'dropdown') {
                                                                                    $options = $cus_field['options']['value'];
                                                                                    if (isset($options)) {
                                                                                        $finded_array = array_search($datavalue, $options);
                                                                                        $datavalue = isset($finded_array) ? $cus_field['options']['label'][$finded_array] : '';
                                                                                    }
                                                                                    echo esc_html($datavalue);
                                                                                } else {
                                                                                    echo esc_html($datavalue);
                                                                                }
                                                                                if ($data_flage != count($data)) {
                                                                                    echo ", ";
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
                                                                        ?></strong></div><?php
                                                                        if (($custom_field_box % 3 == 0 && $custom_field_box > 0) && count($cs_job_cus_fields) != $custom_field_box)
                                                                            echo '</li><li>';
                                                                        $custom_field_box++;
                                                                    }
                                                            }
                                                        }
                                                        if ($custom_field_box % 3 != 0 && $custom_field_box > 0)
                                                            echo "</li>";
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                            <?php
                                            $job_address = get_user_address_string_for_detail($job_post->ID);
                                            if ($job_address != '') {
                                                ?>
                                                <div class="job-description">
                                                    <address><i class="icon-location6"></i><?php echo esc_html($job_address); ?></address>

                                                </div>
                                            <?php }
                                            ?>
                                            <div class="job-description">
                                                <h5><?php _e('Job Description', 'jobhunt'); ?></h5>
                                                <?php
                                                the_content();
                                                echo apply_filters('view_more', $current_post_id);
                                                ?>
                                            </div>
                                            <?php ob_start(); ?>
                                            <div class="share-job">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <ul class="more-jobs">
                                                            <?php if (!is_user_logged_in() || !$cs_emp_funs->is_employer()) { ?>
                                                                <li><?php cs_add_dirpost_favourite($job_post->ID); ?></li>
                                                            <?php } else {
                                                                ?>
                                                                <li><a class="cs-add-wishlist" href="javascript:void(0)"  id="<?php echo 'addjobs_to_wishlist' . intval($list_job_id); ?>" onclick="show_alert_msg('<?php echo __("Become a candidate then try again.", "jobhunt"); ?>')" ><i class="icon-heart-o"></i><?php _e('Shortlist', 'jobhunt'); ?></a></li>
                                                            <?php }
                                                            ?>
                                                            <li>

                                                                <a data-target="#cs-msgbox-<?php echo absint($job_post->ID) ?>" data-toggle="modal" href="#"><i class="icon-mail"></i> <?php _e('Email', 'jobhunt'); ?></a>

                                                            </li>
                                                        </ul>

                                                    </div>
                                                    <?php if (cs_social_share(0)) { ?>
                                                        <div class="col-md-6">  
                                                            <div class="share-post pull-right">
                                                                <ul class="share-medea">
                                                                    <?php echo cs_social_share(0); ?>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <?php
                                            $social_links_output = ob_get_clean();
                                            echo apply_filters('social_links', $social_links_output, $current_post_id);
                                            ?>
                                            <?php ob_start(); ?>
                                            <div class="apply-job">
                                                <div class="cs-text">
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
                                                                    <a href="javascript:void(0);" class="like applied_icon" >
                                                                        <span><i class="icon-thumbsup"></i></span><?php _e('Applied', 'jobhunt') ?>
                                                                    </a>
                                                                    <?php
                                                                } else {
                                                                    ?>
                                                                    <a class="like applied_icon <?php echo $class_apply; ?>" onclick="cs_addjobs_left_to_applied('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', '<?php echo intval($job_post->ID); ?>', this)" >
                                                                        <span><i class="icon-thumbsup"></i></span><?php _e('Apply for this job', 'jobhunt') ?>
                                                                    </a>
                                                                    <?php
                                                                }
                                                            } else {
                                                                ?>
                                                                <a class="like applied_icon <?php echo $class_apply; ?>" onclick="cs_addjobs_left_to_applied('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', '<?php echo intval($job_post->ID); ?>', this)" > 
                                                                    <span><i class="icon-thumbsup"></i></span><?php _e('Apply for this job', 'jobhunt') ?>
                                                                </a>	
                                                                <?php
                                                            }
                                                        }
                                                    } else {
                                                        $cs_rand_id = rand(34563, 34323990);
                                                        ?>
                                                        <a href="javascript:void(0);" class="like applied_icon" onclick="trigger_func('#btn-header-main-login');">  <span><i class="icon-thumbsup"></i></span><?php _e('Apply Now', 'jobhunt') ?></a>
                                                        <?php
                                                    }
                                                    if (!is_user_logged_in()) {
                                                        ?>
                                                        <a href="#" class="linkedin social_login_login_linkedin"><div data-applyjobid="<?php echo intval($job_post->ID); ?>" class="linkedin_jobid_apply"></div><i class="icon-linkedin2"></i><span><?php _e('Apply with Linkedin', 'jobhunt'); ?></span></a>
                                                        <?php do_action('apply_with_facebook_button', $job_post->ID); ?>
                                                    <?php }
                                                    ?>
                                                </div>
                                            </div>
                                            <?php
                                            $apply_buttons_output = ob_get_clean();
                                            echo apply_filters('apply_buttons', $apply_buttons_output, $current_post_id);
                                            ?>
                                        </div>
                                    </section>
                                </div>
                                <section class="cs-content-holder">
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
                                            // getting if record not found
                                            if ($featuredjob_count_post > 0) {
                                                ?>
                                                <h4><?php _e('Related Jobs', 'jobhunt') ?> (<?php echo ( $featuredjob_count_post ); ?>) </h4>
                                                <ul class="cs-company-jobs">
                                                    <?php
                                                    global $cs_plugin_options;
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
                                                        // var_dump( $all_job_type);
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
                                                                //$job_type_class .= get_term_link($t_id_main);	
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
                                                                <?php }echo force_balance_tags($job_type_values);
                                                                ?>
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
                                </section>   
                            </section>
                        </div>
                    </div>
                </div>
                <?php
                $cs_sitekey = isset($cs_plugin_options['cs_sitekey']) ? $cs_plugin_options['cs_sitekey'] : '';
                $cs_secretkey = isset($cs_plugin_options['cs_secretkey']) ? $cs_plugin_options['cs_secretkey'] : '';
                cs_google_recaptcha_scripts();
                ?>
                <script type="text/javascript">
                    var recaptcha8;
                    var cs_multicap = function () {

                        recaptcha8 = grecaptcha.render('recaptcha8', {
                            'sitekey': '<?php echo ($cs_sitekey); ?>', //Replace this with your Site key
                            'theme': 'light'
                        });
                    };
                </script>
                <div class="modal fade" id="cs-msgbox-<?php echo absint($job_post->ID) ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-body">
                                <div class="cs-profile-contact-detail cs-contact-modal" data-adminurl="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" data-cap="recaptcha8">
                                    <a class="close" data-dismiss="modal">&times;</a>
                                    <form id="ajaxcontactemployer" action="#" method="post" enctype="multipart/form-data">
                                        <div id="ajaxcontact-response" class=""></div>

                                        <div class="input-filed">
                                            <i class="icon-user9"></i>
                                            <?php
                                            $cs_opt_array = array(
                                                'id' => '',
                                                'std' => isset($login_user_name) ? esc_html($login_user_name) : '',
                                                'cust_id' => "ajaxcontactname",
                                                'cust_name' => "ajaxcontactname",
                                                'classes' => 'form-control',
                                                'extra_atr' => 'placeholder="' . __('Enter your Name', 'jobhunt') . '*"',
                                                'required' => 'yes',
                                            );
                                            $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                            ?>

                                        </div>
                                        <div class="input-filed"> <i class="icon-envelope4"></i>
                                            <?php
                                            $cs_opt_array = array(
                                                'id' => '',
                                                'std' => isset($login_user_email) ? esc_html($login_user_email) : '',
                                                'cust_id' => "ajaxcontactemail",
                                                'cust_name' => "ajaxcontactemail",
                                                'classes' => 'form-control',
                                                'extra_atr' => 'placeholder="' . __('Email Address', 'jobhunt') . '*"',
                                                'required' => 'yes',
                                            );
                                            $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                            ?>
                                        </div>
                                        <div class="input-filed"> <i class="icon-mobile4"></i>
                                            <?php
                                            $cs_opt_array = array(
                                                'id' => '',
                                                'std' => isset($login_user_phone) ? esc_html($login_user_phone) : '',
                                                'cust_id' => "ajaxcontactphone",
                                                'cust_name' => "ajaxcontactphone",
                                                'classes' => 'form-control',
                                                'extra_atr' => 'placeholder="' . __('Phone Number', 'jobhunt') . '"',
                                            );
                                            $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                            ?>
                                        </div>
                                        <div class="input-filed">
                                            <?php
                                            $cs_opt_array = array(
                                                'id' => '',
                                                'std' => '',
                                                'cust_id' => "ajaxcontactcontents",
                                                'cust_name' => "ajaxcontactcontents",
                                                'classes' => 'form-control',
                                                'extra_atr' => 'placeholder="' . __('Message should have more than 50 characters', 'jobhunt') . '"',
                                            );
                                            $cs_form_fields2->cs_form_textarea_render($cs_opt_array);
                                            ?>
                                        </div>

                                        <?php
                                        global $cs_plugin_options;
                                        $cs_captcha_switch = isset($cs_plugin_options['cs_captcha_switch']) ? $cs_plugin_options['cs_captcha_switch'] : '';

                                        if ($cs_captcha_switch == 'on') {
                                            echo '<div class="input-holder recaptcha-reload" id="recaptcha8_div">';
                                            echo cs_captcha('recaptcha8');
                                            echo '</div>';
                                        }
                                        ?>

                                        <div class="submit-btn profile-contact-btn" data-employerid="<?php echo esc_html($employer_post->ID); ?>">

                                            <?php
                                            $cs_opt_array = array(
                                                'id' => '',
                                                'std' => __('Send Email', 'jobhunt'),
                                                'cust_id' => "employerid_contactus",
                                                'cust_name' => "employerid_contactus",
                                                'cust_type' => 'button',
                                                'classes' => 'cs-bgcolor',
                                            );
                                            $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                            $cs_opt_array = array(
                                                'std' => 'cs_registration_validation',
                                                'cust_id' => 'action',
                                                'cust_name' => 'action',
                                                'cust_type' => 'hidden',
                                                'return' => true,
                                            );
                                            $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                            ?>
                                            <div id="main-cs-loader" class="loader_class"></div>
                                        </div>

                                        <?php
                                        $cs_terms_condition = isset($cs_plugin_options['cs_terms_condition']) ? $cs_plugin_options['cs_terms_condition'] : '';
                                        if ($cs_terms_condition != '') {
                                            ?>
                                            <span class="cs-terms"><?php _e('You accepts our', 'jobhunt') ?><a target="_blank" href="<?php echo esc_url(get_permalink($cs_terms_condition)) ?>"> <?php _e('Terms and Conditions', 'jobhunt') ?></a></span> 
                                            <?php
                                        }
                                        ?>
                                    </form>
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
    ?>

</div>