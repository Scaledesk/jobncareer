<?php
/**
 * The template for Candidate Detail
 */
global $author, $current_user, $cs_plugin_options, $cs_notification, $jobcareer_options;     // $jobcareer_options getting from theme if it exist
if (!function_exists('cs_candidates_bosy_class')) {
    add_filter('body_class', 'cs_candidates_bosy_class');

    function cs_candidates_bosy_class($classes) {
        $classes[] = 'cs-candidate-detail';
        return $classes;
    }

}
$cs_user_data = get_userdata($author);

$cs_user_name = $cs_user_data->display_name;

$cs_uniq = rand(11111111, 99999999);
cs_set_post_views($cs_user_data->ID);
get_header();
cs_visualnav_sidemenu(); // add sidemenue effect script files
/*
 *  login user detail
 *      
 */
?>
<div class="main-section">
    <div class="content-area" id="primary">
        <main class="site-main" id="main">
            <article class="post-1 post type-post status-publish format-standard hentry category-uncategorized">
                <!-- alert for complete theme -->
                <div class="cs_alerts" ></div>
                <?php
                $login_user_name = '';
                $login_user_email = '';
                $login_user_phone = '';
                $cs_emp_funs = new cs_employer_functions();
                if (is_user_logged_in()) {
                    $login_user_name = $current_user->display_name;
                    $login_user_email = $current_user->user_email;
                    $login_user_phone = get_user_meta($current_user->ID, 'cs_contact_information', true);
                }
                $cs_emp_funs = new cs_employer_functions();
                $cs_candidate_switch = isset($cs_plugin_options['cs_candidate_switch']) ? $cs_plugin_options['cs_candidate_switch'] : '';

                $cs_candidate_view = true;
                if ($cs_candidate_switch == 'on') {
                    $cs_candidate_view = false;
                    if (is_user_logged_in() && $cs_emp_funs->is_employer() && $cs_emp_funs->cs_check_emp_resume($cs_user_data->ID)) {
                        $cs_candidate_view = true;
                    } else {
                        $cs_candidate_view = false;
                    }
                    // check if this candidate apply a job and owner of that job need to view the profile of this candidate
                    if (check_candidate_applications($cs_user_data->ID) > 0) {
                        $cs_candidate_view = true;
                    }
                    // check if you this is your own profile
                    if (is_user_logged_in()) {
                        $user_profile_id = get_current_user_id();
                        if ((isset($user_profile_id)) && $cs_user_data->ID == $user_profile_id) {
                            $cs_candidate_view = true;
                        }
                    }
                }

                if ($cs_candidate_view == true) {
                    // candidate default var
                    $cs_candidate_address = get_user_meta($cs_user_data->ID, 'cs_post_loc_address', true);
                    $cs_candidate_web_http = $cs_user_data->user_url;
                    $cs_candidate_web = preg_replace('#^https?://#', '', $cs_candidate_web_http);
                    $candidate_img = get_user_meta($cs_user_data->ID, 'user_img', true);
                    $candidate_img = cs_get_img_url($candidate_img, 'cs_media_4');
                    if (!cs_image_exist($candidate_img) || $candidate_img == "") {
                        $candidate_img = esc_url(wp_jobhunt::plugin_url() . 'assets/images/img-not-found16x9.jpg');
                    }

                    // gettting candidate cover image
                    $cs_candidate_cover_employer_img = get_user_meta($cs_user_data->ID, 'cover_user_img', true);
                    $cs_candidate_cover_employer_img = cs_get_img_url($cs_candidate_cover_employer_img, '');

                    $cs_candidate_cv = get_user_meta($cs_user_data->ID, "cs_candidate_cv", true);
                    $cs_candidate_emp_username = $cs_user_data->ID;
                    $cs_candidate_job_title = get_user_meta($cs_user_data->ID, 'cs_job_title', true);
                    $cs_candidate_facebook = get_user_meta($cs_user_data->ID, 'cs_facebook', true);
                    $cs_candidate_twitter = get_user_meta($cs_user_data->ID, 'cs_twitter', true);
                    $cs_candidate_linkedin = get_user_meta($cs_user_data->ID, 'cs_linkedin', true);
                    $cs_candidate_google_plus = get_user_meta($cs_user_data->ID, 'cs_google_plus', true);
                    // get education list
                    $cs_get_edu_list = get_user_meta($cs_user_data->ID, 'cs_edu_list_array', true);
                    $cs_edu_titles = get_user_meta($cs_user_data->ID, 'cs_edu_title_array', true);
                    $cs_edu_from_dates = get_user_meta($cs_user_data->ID, 'cs_edu_from_date_array', true);
                    $cs_edu_to_dates = get_user_meta($cs_user_data->ID, 'cs_edu_to_date_array', true);
                    $cs_edu_institutes = get_user_meta($cs_user_data->ID, 'cs_edu_institute_array', true);
                    $cs_edu_descs = get_user_meta($cs_user_data->ID, 'cs_edu_desc_array', true);
                    // get experience list
                    $cs_get_exp_list = get_user_meta($cs_user_data->ID, 'cs_exp_list_array', true);
                    $cs_exp_titles = get_user_meta($cs_user_data->ID, 'cs_exp_title_array', true);
                    $cs_exp_from_dates = get_user_meta($cs_user_data->ID, 'cs_exp_from_date_array', true);
                    $cs_exp_to_dates = get_user_meta($cs_user_data->ID, 'cs_exp_to_date_array', true);
                    $cs_exp_to_presents = get_user_meta($cs_user_data->ID, 'cs_exp_to_present_array', true);
                    $cs_exp_companys = get_user_meta($cs_user_data->ID, 'cs_exp_company_array', true);
                    $cs_exp_descs = get_user_meta($cs_user_data->ID, 'cs_exp_desc_array', true);
                    // get portfolio list
                    $cs_get_port_list = get_user_meta($cs_user_data->ID, 'cs_port_list_array', true);
                    $cs_image_titles = get_user_meta($cs_user_data->ID, 'cs_image_title_array', true);
                    $cs_image_uploads = get_user_meta($cs_user_data->ID, 'cs_image_upload_array', true);
                    // get awards list
                    $cs_get_award_list = get_user_meta($cs_user_data->ID, 'cs_award_list_array', true);
                    $cs_award_names = get_user_meta($cs_user_data->ID, 'cs_award_name_array', true);
                    $cs_award_years = get_user_meta($cs_user_data->ID, 'cs_award_year_array', true);
                    $cs_award_descs = get_user_meta($cs_user_data->ID, 'cs_award_description_array', true);
                    // get skills list
                    $cs_get_skill_list = get_user_meta($cs_user_data->ID, 'cs_skills_list_array', true);
                    $cs_skill_titles = get_user_meta($cs_user_data->ID, 'cs_skill_title_array', true);
                    $cs_skill_percentages = get_user_meta($cs_user_data->ID, 'cs_skill_percentage_array', true);

//                    $cs_header_creds = cs_header_cover_style('candidate', $cs_candidate_cover_employer_img, '250');
//                    $cs_header_style = isset($cs_header_creds[0]) ? $cs_header_creds[0] : '';
//                    $cs_header_paralax_class = isset($cs_header_creds[1]) ? ' ' . $cs_header_creds[1] : '';

                    $cs_header_creds = cs_header_cover_style('candidate', $cs_candidate_cover_employer_img, '250');
                    $cs_header_style = isset($cs_header_creds[0]) ? $cs_header_creds[0] : '';
                    $cs_header_paralax_class = isset($cs_header_creds[1]) ? ' ' . $cs_header_creds[1] : '';
                    $cs_candidate_default_cover_style = isset($jobcareer_options['cs_candidate_default_cover_style']) ? $jobcareer_options['cs_candidate_default_cover_style'] . '-view' : '';
                    ?>
                    <!-- Main Start --> 
                    <div class="main-section">
                        <div class="page-section">
                            <div class="candidate-header<?php echo esc_html($cs_header_paralax_class) ?> <?php echo esc_html($cs_candidate_default_cover_style) ?>"<?php echo ($cs_header_style != '' ? ' style="' . $cs_header_style . '"' : '') ?>>
                                <div class="<?php if (isset($cs_plugin_options['cs_plugin_single_container']) && $cs_plugin_options['cs_plugin_single_container'] == 'on') echo 'container' ?>">
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <div class="cs-profile">
                                                <div class="cs-media">
                                                    <figure> <img src="<?php echo esc_url($candidate_img); ?>" alt=""> </figure>
                                                </div>
                                                <div class="info">
                                                    <div class="title"><h3><?php echo $cs_user_data->display_name; ?></h3></div>
                                                    <?php if (isset($cs_candidate_job_title) && $cs_candidate_job_title != '') {
                                                        ?><div class="join-date"><span><?php echo esc_html($cs_candidate_job_title); ?></span></div>
                                                            <?php } ?>
                                                    <div class="cs-profile-contact-info">
                                                        <ul>
                                                            <?php if ($cs_candidate_facebook != '') { ?>
                                                                <li><a href="<?php echo esc_url($cs_candidate_facebook); ?>" data-original-title="facebook"><i class="icon-facebook7"></i></a></li>
                                                                <?php
                                                            }
                                                            if ($cs_candidate_twitter != '') {
                                                                ?>
                                                                <li><a href="<?php echo esc_url($cs_candidate_twitter); ?>" data-original-title="twitter"><i class="icon-twitter6"></i></a></li>
                                                                <?php
                                                            }
                                                            if ($cs_candidate_linkedin != '') {
                                                                ?>
                                                                <li><a href="<?php echo esc_url($cs_candidate_linkedin); ?>" data-original-title="linkedin"><i class="icon-linkedin4"></i></a></li>
                                                                <?php
                                                            }
                                                            if ($cs_candidate_google_plus != '') {
                                                                ?>
                                                                <li><a href="<?php echo esc_url($cs_candidate_google_plus); ?>" data-original-title="google"><i class="icon-googleplus7"></i></a></li>
                                                                <?php
                                                            }
                                                            if (isset($cs_candidate_cv) && $cs_candidate_cv != '') {
                                                                ?>   
                                                                <li><a class="cs-candidate-download" target="_blank" href="<?php echo esc_url($cs_candidate_cv); ?>"><?php _e("Download CV", 'jobhunt'); ?></a></li>
                                                            <?php } ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="post-options">
                                                    <ul>
                                                        <li><i class="icon-clock-o"></i><?php _e('Member Since', 'jobhunt') ?>, <?php echo date_i18n(get_option('date_format'), strtotime($cs_user_data->user_registered)); ?></li>
                                                        <?php
                                                        if ($cs_candidate_address != '') {
                                                            echo '<li><i class="icon-location6"></i>' . $cs_candidate_address . '</li>';
                                                        }
                                                        ?>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="profile-nav">
                                                <nav>
                                                    <ul>
                                                        <li><a href="#about"><?php _e('About', 'jobhunt') ?></a></li>
                                                        <?php if (isset($cs_get_edu_list) && is_array($cs_get_edu_list) && count($cs_get_edu_list) > 0) { ?>
                                                            <li><a href="#education"> <?php _e("Education", "jobhunt"); ?></a></li>
                                                        <?php } ?>

                                                        <?php if (isset($cs_get_exp_list) && is_array($cs_get_exp_list) && count($cs_get_exp_list) > 0) { ?>
                                                            <li><a href="#experience"> <?php _e("Work Experience", "jobhunt"); ?></a></li>
                                                        <?php } ?>
                                                        <?php if (isset($cs_get_port_list) && is_array($cs_get_port_list) && count($cs_get_port_list) > 0) { ?>
                                                            <li><a href="#portfolio"> <?php _e("Portfolio", "jobhunt"); ?></a></li> 
                                                        <?php } ?>
                                                        <?php if (isset($cs_get_skill_list) && is_array($cs_get_skill_list) && count($cs_get_skill_list) > 0) { ?>
                                                            <li><a href="#skills"> <?php _e("Professional Skills", "jobhunt"); ?></a></li>
                                                        <?php } ?>
                                                        <?php if (isset($cs_get_award_list) && is_array($cs_get_award_list) && count($cs_get_award_list) > 0) { ?>
                                                            <li><a href="#awards-houners"> <?php _e("Awards", "jobhunt"); ?></a></li>
                                                        <?php } ?>

                                                    </ul>
                                                </nav>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="page-section">
                            <div class="<?php if (isset($cs_plugin_options['cs_plugin_single_container']) && $cs_plugin_options['cs_plugin_single_container'] == 'on') echo 'container' ?>">
                                <div class="row">
                                    <div class="col-lg-8 col-md-8 col-sm-12">
                                        <div class="sections">
                                            <div id="about">
                                                <div class="jobs-detail-listing">
                                                    <ul class="row">
                                                        <?php
                                                        $cs_candidate_cus_fields = get_option("cs_candidate_cus_fields");
                                                        if (is_array($cs_candidate_cus_fields) && sizeof($cs_candidate_cus_fields) > 0) {

                                                            $custom_field_box = 1;
                                                            foreach ($cs_candidate_cus_fields as $cus_field) {
                                                                $data = '';
                                                                if ($cus_field['meta_key'] != '') {
                                                                    $data = get_user_meta($cs_user_data->ID, $cus_field['meta_key'], true);
                                                                    // empty check of value
                                                                    if ($cus_field['label'] != '') {

                                                                        if ($data != "") {
                                                                            ?>
                                                                            <li class="col-lg-4 col-md-4 col-sm-6">
                                                                                <div class="listing-inner">
                                                                                    <?php
                                                                                    if (isset($cus_field['fontawsome_icon']) && $cus_field['fontawsome_icon'] != '') {
                                                                                        echo '<i class="' . $cus_field['fontawsome_icon'] . '"></i>';
                                                                                    }
                                                                                    ?>
                                                                                    <div class="cs-text">
                                                                                        <?php if ($cus_field['label'] <> "") { ?> <span><?php echo esc_html($cus_field['label']); ?></span> <?php } ?>
                                                                                        <strong>
                                                                                            <?php
                                                                                            // check the data is array or not
                                                                                            if (is_array($data) && !empty($data)) {
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
                                                                                            ?>

                                                                                        </strong>
                                                                                    </div>
                                                                                </div>

                                                                            </li>

                                                                            <?php
                                                                            $custom_field_box++;
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                        ?>

                                                    </ul>
                                                </div>

                                                <?php
                                                echo $cs_user_data->description;
                                                ?>
                                            </div>
                                            <?php
                                            // get all candidate switches from plugin options
                                            $cs_education_switch = isset($cs_plugin_options['cs_education_switch']) ? $cs_plugin_options['cs_education_switch'] : '';
                                            $cs_experience_switch = isset($cs_plugin_options['cs_experience_switch']) ? $cs_plugin_options['cs_experience_switch'] : '';
                                            $cs_portfolio_switch = isset($cs_plugin_options['cs_portfolio_switch']) ? $cs_plugin_options['cs_portfolio_switch'] : '';
                                            $cs_skills_switch = isset($cs_plugin_options['cs_skills_switch']) ? $cs_plugin_options['cs_skills_switch'] : '';
                                            $cs_award_switch = isset($cs_plugin_options['cs_award_switch']) ? $cs_plugin_options['cs_award_switch'] : '';

                                            if ($cs_education_switch == 'on') {
                                                if (isset($cs_get_edu_list) && is_array($cs_get_edu_list) && count($cs_get_edu_list) > 0) {
                                                    ?> 
                                                    <div id="education" class="panel-inner">
                                                        <div class="inner">
                                                            <div class="cs-element-title cs-color csborder-color">
                                                                <i class="icon-graduation"></i>
                                                                <h4><?php _e('Education', 'jobhunt') ?></h4>
                                                            </div>
                                                            <div class="cs-education">
                                                                <ul>
                                                                    <?php
                                                                    $cs_award_counter = 0;
                                                                    foreach ($cs_get_edu_list as $award_list) {
                                                                        if (isset($award_list) && $award_list <> '') {
                                                                            $counter_extra_feature = $extra_feature_id = $award_list;
                                                                            $cs_edu_title = isset($cs_edu_titles[$cs_award_counter]) ? $cs_edu_titles[$cs_award_counter] : '';
                                                                            $cs_edu_from_date = isset($cs_edu_from_dates[$cs_award_counter]) ? $cs_edu_from_dates[$cs_award_counter] : '';
                                                                            $cs_edu_to_date = isset($cs_edu_to_dates[$cs_award_counter]) ? $cs_edu_to_dates[$cs_award_counter] : '';
                                                                            $cs_edu_institute = isset($cs_edu_institutes[$cs_award_counter]) ? $cs_edu_institutes[$cs_award_counter] : '';
                                                                            $cs_edu_desc = isset($cs_edu_descs[$cs_award_counter]) ? $cs_edu_descs[$cs_award_counter] : '';
                                                                            ?>
                                                                            <li>
                                                                                <div class="cs-title">
                                                                                    <h6><?php if (isset($cs_edu_title)) echo esc_attr($cs_edu_title); ?></h6><span><?php echo date('Y', strtotime($cs_edu_from_date)); ?> - <?php echo date('Y', strtotime($cs_edu_to_date)); ?></span>
                                                                                    <span class="cs-institute"><?php if (isset($cs_edu_institute)) echo esc_attr($cs_edu_institute); ?></span>
                                                                                </div>
                                                                                <div class="education-detail">
                                                                                    <!-- <h6> <?php //if(isset($cs_edu_institute)) echo esc_html($cs_edu_institute);                                                     
                                                                            ?></h6>-->

                                                                                    <p><?php if (isset($cs_edu_desc)) echo force_balance_tags($cs_edu_desc); ?></p>
                                                                                </div>
                                                                            </li>

                                                                            <?php
                                                                        }
                                                                        $cs_award_counter++;
                                                                    }
                                                                    ?>  
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                            }
                                            if ($cs_experience_switch == 'on') {
                                                if (isset($cs_get_exp_list) && is_array($cs_get_exp_list) && count($cs_get_exp_list) > 0) {
                                                    ?>
                                                    <section id="experience">
                                                        <div class="cs-element-title cs-color csborder-color">
                                                            <i class="icon-briefcase4"></i>
                                                            <h4><?php _e('Work Experience', 'jobhunt') ?></h4>
                                                        </div>
                                                        <div class="timeline">
                                                            <ul>
                                                                <?php
                                                                $cs_award_counter = 0;
                                                                foreach ($cs_get_exp_list as $award_list) {
                                                                    if (isset($award_list) && $award_list <> '') {
                                                                        $counter_extra_feature = $extra_feature_id = $award_list;
                                                                        $cs_exp_title = isset($cs_exp_titles[$cs_award_counter]) ? $cs_exp_titles[$cs_award_counter] : '';
                                                                        $cs_exp_from_date = isset($cs_exp_from_dates[$cs_award_counter]) ? $cs_exp_from_dates[$cs_award_counter] : '';
                                                                        $cs_exp_to_date = isset($cs_exp_to_dates[$cs_award_counter]) ? $cs_exp_to_dates[$cs_award_counter] : '';
                                                                        $cs_exp_to_present = isset($cs_exp_to_presents[$cs_award_counter]) ? $cs_exp_to_presents[$cs_award_counter] : '';
                                                                        $cs_exp_company = isset($cs_exp_companys[$cs_award_counter]) ? $cs_exp_companys[$cs_award_counter] : '';
                                                                        $cs_exp_desc = isset($cs_exp_descs[$cs_award_counter]) ? $cs_exp_descs[$cs_award_counter] : '';

                                                                        $from_year = '';
                                                                        $to_year = '';
                                                                        $from_date_year = date_i18n('d-m-Y', strtotime($cs_exp_from_date));

                                                                        $from_year = $from_date_year;

                                                                        $to_date_year = date_i18n('d-m-Y', strtotime($cs_exp_to_date));
                                                                        if ($cs_exp_to_present == 'on')
                                                                            $to_year = __('Present', 'jobhunt');
                                                                        else
                                                                            $to_year = $to_date_year;
                                                                        ?>
                                                                        <li>
                                                                            <div class="cs-title">
                                                                                <span>
                                                                                    <?php
                                                                                    if ($to_year == $from_year) {
                                                                                        echo esc_html($from_year);
                                                                                    } else {
                                                                                        echo esc_html($from_year) . " - " . esc_html($to_year);
                                                                                    }
                                                                                    ?>
                                                                                </span>
                                                                                <?php if (isset($cs_exp_title)) echo '<h6>' . esc_html($cs_exp_title) . '</h6>'; ?>
                                                                            </div>
                                                                            <div class="cs-text">
                                                                                <?php if (isset($cs_exp_desc)) echo '<p>' . force_balance_tags($cs_exp_desc) . '</p>'; ?>
                                                                            </div>
                                                                        </li>
                                                                        <?php
                                                                    }
                                                                    $cs_award_counter++;
                                                                }
                                                                ?>   
                                                            </ul>
                                                        </div>
                                                    </section>
                                                    <?php
                                                }
                                            }
                                            if ($cs_portfolio_switch == 'on') {
                                                if (isset($cs_get_port_list) && is_array($cs_get_port_list) && count($cs_get_port_list) > 0) {
                                                    ?>
                                                    <section id="portfolio">
                                                        <div class="cs-element-title cs-color csborder-color">
                                                            <i class="icon-pictures5"></i>
                                                            <h4><?php _e('Portfolio', 'jobhunt') ?></h4>
                                                        </div>
                                                        <div class="cs-gallry">
                                                            <div class="row">
                                                                <?php
                                                                $cs_award_counter = 0;
                                                                foreach ($cs_get_port_list as $award_list) {
                                                                    if (isset($award_list) && $award_list <> '') {
                                                                        $counter_extra_feature = $extra_feature_id = $award_list;
                                                                        $cs_image_title = isset($cs_image_titles[$cs_award_counter]) ? $cs_image_titles[$cs_award_counter] : '';
                                                                        $cs_image_upload = isset($cs_image_uploads[$cs_award_counter]) ? $cs_image_uploads[$cs_award_counter] : '';
                                                                        if (!cs_image_exist($cs_image_upload)) {
                                                                            $cs_image_upload_thumb = cs_get_portfolio_img_url($cs_image_upload, 'cs_media_5');
                                                                            if ($cs_image_upload_thumb == '' || !cs_image_exist($cs_image_upload_thumb)) {
                                                                                $cs_image_upload_thumb = esc_url(wp_jobhunt::plugin_url() . 'assets/images/img-not-found16x9.jpg');
                                                                            }
                                                                            $cs_image_upload_larg = cs_get_portfolio_img_url($cs_image_upload, 'full');
                                                                            if ($cs_image_upload_larg == '' || !cs_image_exist($cs_image_upload_larg)) {
                                                                                $cs_image_upload_larg = esc_url(wp_jobhunt::plugin_url() . 'assets/images/img-not-found16x9.jpg');
                                                                            }
                                                                        } else {
                                                                            $cs_image_upload_thumb = $cs_image_upload;
                                                                            $cs_image_upload_larg = $cs_image_upload;
                                                                        }
                                                                        if ($cs_image_title != '' || $cs_image_upload != '') {
                                                                            ?> 

                                                                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6 thumb">
                                                                                <div class="cs-media">
                                                                                    <a class="thumbnail" href="#" data-image-id="" data-toggle="modal" data-title="<?php echo esc_html($cs_image_title); ?>" 
                                                                                       data-caption="<?php echo esc_html($cs_image_title); ?>" 
                                                                                       data-image="<?php echo esc_url($cs_image_upload_larg); ?>" data-target="#image-gallery">
                                                                                        <img class="img-responsive" src="<?php echo esc_url($cs_image_upload_thumb); ?>" alt="<?php echo esc_html($cs_image_title); ?>">
                                                                                    </a>

                                                                                </div>
                                                                            </div>
                                                                            <?php
                                                                        }
                                                                    }
                                                                    $cs_award_counter++;
                                                                }
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </section>
                                                    <div class="modal fade" id="image-gallery" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only"><?php _e('Close', 'jobhunt') ?></span></button>
                                                                    <h4 class="modal-title" id="image-gallery-title"></h4>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <img id="image-gallery-image" class="img-responsive" src="">
                                                                </div>
                                                                <div class="modal-footer">

                                                                    <div class="col-md-2 col-lg-2 col-sm-2 col-xs-2">
                                                                        <button type="button" class="btn btn-primary" id="show-previous-image"><?php _e('Previous', 'jobhunt') ?></button>
                                                                    </div>

                                                                    <div class="col-md-8 col-lg-8 col-sm-8 col-xs-8 text-justify" id="image-gallery-caption">
                                                                        <?php _e('This text will be overwritten by jQuery', 'jobhunt') ?>
                                                                    </div>

                                                                    <div class="col-md-2 col-lg-2 col-sm-2 col-xs-2">
                                                                        <button type="button" id="show-next-image" class="btn btn-default"><?php _e('Next', 'jobhunt') ?></button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                            }
                                            if ($cs_skills_switch == 'on') {
                                                if (isset($cs_get_skill_list) && is_array($cs_get_skill_list) && count($cs_get_skill_list) > 0) {
                                                    ?>
                                                    <section id="skills">
                                                        <div class="cs-element-title cs-color csborder-color">
                                                            <i class="icon-graph"></i>
                                                            <h4> <?php _e('Professional Skills', 'jobhunt') ?></h4>
                                                        </div>
                                                        <?php
                                                        $cs_award_counter = 0;
                                                        foreach ($cs_get_skill_list as $award_list) {
                                                            if (isset($award_list) && $award_list <> '') {
                                                                $counter_extra_feature = $extra_feature_id = $award_list;
                                                                $cs_skill_title = isset($cs_skill_titles[$cs_award_counter]) ? $cs_skill_titles[$cs_award_counter] : '';
                                                                $cs_skill_percentage = isset($cs_skill_percentages[$cs_award_counter]) ? $cs_skill_percentages[$cs_award_counter] : '';
                                                                ?>
                                                                <div class="progress-info">
                                                                    <h6><?php if (isset($cs_skill_title)) echo esc_html($cs_skill_title); ?></h6>
                                                                    <small> <?php if (isset($cs_skill_percentage)) echo preg_replace('/[^0-9]/', '', $cs_skill_percentage) . '%'; ?></small>
                                                                </div>
                                                                <div class="progress skill-bar">
                                                                    <div class="progress-bar progress-bar-success cs-bgcolor" role="progressbar" aria-valuenow="<?php echo preg_replace('/[^0-9]/', '', $cs_skill_percentage); ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                                </div>
                                                                <?php
                                                            }
                                                            $cs_award_counter++;
                                                        }
                                                        ?>     
                                                    </section>
                                                    <?php
                                                }
                                            }
                                            if ($cs_award_switch == 'on') {
                                                if (isset($cs_get_award_list) && is_array($cs_get_award_list) && count($cs_get_award_list) > 0) {
                                                    ?>
                                                    <div class="panel-inner" id="awards-houners">
                                                        <div class="inner">
                                                            <div class="cs-element-title cs-color csborder-color">
                                                                <i class="icon-trophy5"></i>
                                                                <h4><?php _e('Awards', 'jobhunt') ?></h4>
                                                            </div>
                                                            <div class="cs-profile-awards">
                                                                <ul>
                                                                    <?php
                                                                    $cs_award_counter = 0;
                                                                    foreach ($cs_get_award_list as $award_list) {
                                                                        if (isset($award_list) && $award_list <> '') {

                                                                            $counter_extra_feature = $extra_feature_id = $award_list;
                                                                            $cs_award_name = isset($cs_award_names[$cs_award_counter]) ? $cs_award_names[$cs_award_counter] : '';
                                                                            $cs_award_year = isset($cs_award_years[$cs_award_counter]) ? $cs_award_years[$cs_award_counter] : '';
                                                                            $cs_award_description = isset($cs_award_descs[$cs_award_counter]) ? $cs_award_descs[$cs_award_counter] : '';
                                                                            ?>
                                                                            <li>
                                                                                <div class="cs-title">
                                                                                    <span><?php echo date('Y', strtotime($cs_award_year)); ?></span>
                                                                                    <h6><?php echo esc_html($cs_award_name); ?></h6>
                                                                                </div>
                                                                                <div class="award-detail">
                                                                                    <p><?php echo esc_html($cs_award_description); ?></p>
                                                                                </div>
                                                                            </li>
                                                                            <?php
                                                                        }
                                                                        $cs_award_counter++;
                                                                    }
                                                                    ?>

                                                                </ul>

                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <aside class="col-lg-4 col-md-4 col-sm-6 col-xs-12 section-sidebar">
                                        <div class="employer-contact-form">
                                            <h5><?php printf(__("Contact %s", "jobhunt"), $cs_user_name); ?></h5>
                                            <div class="cs-profile-contact-detail" data-adminurl="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" data-cap="recaptcha7">
                                                <form id="ajaxcontactform" action="#" method="post" enctype="multipart/form-data">
                                                    <div id="ajaxcontact-response" class=""></div>
                                                    <div class="input-filed">
                                                        <i class="icon-user9"></i>
                                                        <input  id="ajaxcontactname" name="ajaxcontactname" type="text" placeholder="<?php _e('Enter your Name', 'jobhunt') ?>*" value="<?php if (isset($login_user_name)) echo esc_html($login_user_name); ?>" required>
                                                    </div>
                                                    <div class="input-filed">
                                                        <i class="icon-envelope4"></i>
                                                        <input  id="ajaxcontactemail" name="ajaxcontactemail" type="text" placeholder="<?php _e('Email Address', 'jobhunt') ?>*" value="<?php if (isset($login_user_email)) echo sanitize_email($login_user_email); ?>" required>
                                                    </div>
                                                    <div class="input-filed">
                                                        <i class="icon-mobile4"></i>
                                                        <input  id="ajaxcontactphone" name="ajaxcontactphone" type="text" placeholder="<?php _e('Phone Number', 'jobhunt') ?>" value="<?php if (isset($login_user_phone)) echo esc_html($login_user_phone); ?>">
                                                    </div>
                                                    <div class="input-filed">
                                                        <textarea id="ajaxcontactcontents" name="ajaxcontactcontents"  placeholder="<?php _e('Message should have more than 50 characters', 'jobhunt') ?>"></textarea>
                                                    </div>

                                                    <?php
                                                    $cs_sitekey = isset($cs_plugin_options['cs_sitekey']) ? $cs_plugin_options['cs_sitekey'] : '';
                                                    $cs_secretkey = isset($cs_plugin_options['cs_secretkey']) ? $cs_plugin_options['cs_secretkey'] : '';
                                                    cs_google_recaptcha_scripts();
                                                    ?>
                                                    <script>

                                                        var recaptcha7;
                                                        var cs_multicap = function () {

                                                            recaptcha7 = grecaptcha.render('recaptcha7', {
                                                                'sitekey': '<?php echo ($cs_sitekey); ?>', //Replace this with your Site key
                                                                'theme': 'light'
                                                            });
                                                        };

                                                    </script>
                                                    <?php
                                                    $cs_captcha_switch = isset($cs_plugin_options['cs_captcha_switch']) ? $cs_plugin_options['cs_captcha_switch'] : '';
                                                    if ($cs_captcha_switch == 'on') {
                                                        echo '<div class="input-holder recaptcha-reload" id="recaptcha7_div">';
                                                        echo cs_captcha('recaptcha7');
                                                        echo '</div>';
                                                    }
                                                    ?>
                                                    <div class="profile-contact-btn submit-btn" data-candidateid="<?php echo esc_html($cs_user_data->ID); ?>">
                                                        <div id="main-cs-loader" class="loader_class"></div>
                                                        <input type="submit" id="candidate_contactus"  name="candidate_contactus"  class="cs-bgcolor" value="<?php _e('Send Request', 'jobhunt'); ?>">
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
                                    </aside>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script>
                        jQuery(document).ready(function () {
                            jQuery('.progress .progress-bar').css("width",
                                    function () {
                                        return jQuery(this).attr("aria-valuenow") + "%";
                                    }
                            )
                        });
                    </script>
                    <script>
                        jQuery(document).ready(function () {

                            loadGallery(true, 'a.thumbnail');

                            //This function disables buttons when needed
                            function disableButtons(counter_max, counter_current) {
                                jQuery('#show-previous-image, #show-next-image').show();
                                if (counter_max == counter_current) {
                                    jQuery('#show-next-image').hide();
                                } else if (counter_current == 1) {
                                    jQuery('#show-previous-image').hide();
                                }
                            }

                            /**
                             * @param setIDs        Sets IDs when DOM is loaded. If using a PHP counter, set to false.
                             * @param setClickAttr  Sets the attribute for the click handler.
                             */

                            function loadGallery(setIDs, setClickAttr) {
                                var current_image,
                                        selector,
                                        counter = 0;

                                jQuery('#show-next-image, #show-previous-image').click(function () {
                                    if (jQuery(this).attr('id') == 'show-previous-image') {
                                        current_image--;
                                    } else {
                                        current_image++;
                                    }

                                    selector = jQuery('[data-image-id="' + current_image + '"]');
                                    updateGallery(selector);
                                });

                                function updateGallery(selector) {
                                    var $sel = selector;
                                    current_image = $sel.data('image-id');
                                    jQuery('#image-gallery-caption').text($sel.data('caption'));
                                    jQuery('#image-gallery-title').text($sel.data('title'));
                                    jQuery('#image-gallery-image').attr('src', $sel.data('image'));
                                    disableButtons(counter, $sel.data('image-id'));
                                }

                                if (setIDs == true) {
                                    jQuery('[data-image-id]').each(function () {
                                        counter++;
                                        jQuery(this).attr('data-image-id', counter);
                                    });
                                }
                                jQuery(setClickAttr).on('click', function () {
                                    updateGallery(jQuery(this));
                                });
                            }
                        });
                    </script>

                    <!-- Main End -->
                    <?php
                } else {
                    ?>
                    <div id="main">
                        <div class="main-section">
                            <section class="candidate-profile">
                                <div class="<?php if (isset($cs_plugin_options['cs_plugin_single_container']) && $cs_plugin_options['cs_plugin_single_container'] == 'on') echo 'container' ?>">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="unauthorized">
                                                <h1>
                                                    <?php
                                                    _e('You are not <span>authorized</span>.', 'jobhunt');
                                                    ?>
                                                </h1>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </article>
        </main>
    </div>
</div>
<?php get_footer();