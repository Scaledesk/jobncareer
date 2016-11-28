<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$main_col = 'col-lg-12 col-md-12 col-sm-12 col-xs-12';
if ($a['cs_candidate_searchbox'] == 'yes') {
    echo '<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">';
}
include plugin_dir_path(__FILE__) . '../candidate-search-keywords.php';

if (is_user_logged_in() && !$login_user_is_employer_flag) {
    ?>
    <div id="cs-not-emp" style="display:none;"><?php _e('Oppsss!! You are not logged in as employer to shortlist applicant.', 'jobhunt') ?></div>
    <?php
}
?>
<ul class="cs-candidate-list">
    <?php
    // getting if record not found
    if ($count_post > 0) {
        // getting job with page number
        $loop = new WP_User_Query($args);
        global $cs_plugin_options, $current_user;
        $cs_emp_funs = new cs_employer_functions();
        $cs_candidate_switch = isset($cs_plugin_options['cs_candidate_switch']) ? $cs_plugin_options['cs_candidate_switch'] : '';
        $cs_fav_resumes = get_user_meta($current_user->ID, "cs_fav_resumes", true);

        $flag = 1;
        $counter = 1;
        if (!empty($loop->results)) {
            foreach ($loop->results as $cs_user) {
                $cs_post_loc_city = urldecode(get_user_meta($cs_user->ID, 'cs_post_loc_city', true));
                $cs_candidate_web_http = get_user_meta($cs_user->ID, 'cs_website', true);
                $cs_candidate_web = preg_replace('#^https?://#', '', $cs_candidate_web_http);
                $candidate_img = get_user_meta($cs_user->ID, 'user_img', true);
                $cs_minimum_salary = get_user_meta($cs_user->ID, 'cs_minimum_salary', true);
                $cs_user_last_activity_date = get_user_meta($cs_user->ID, 'cs_user_last_activity_date', true);
                // diffrent size image if exist
                $candidate_img = cs_get_img_url($candidate_img, 'cs_media_4');
                if (!cs_image_exist($candidate_img) || $candidate_img == "") {
                    $candidate_img = esc_url(wp_jobhunt::plugin_url() . 'assets/images/img-not-found16x9.jpg');
                }
                $cs_candidate_user = $cs_user->ID;
                $cs_get_exp_list = get_user_meta($cs_user->ID, 'cs_exp_list_array', true);
                $cs_exp_titles = get_user_meta($cs_user->ID, 'cs_exp_title_array', true);
                $cs_exp_from_dates = get_user_meta($cs_user->ID, 'cs_exp_from_date_array', true);
                $cs_exp_to_dates = get_user_meta($cs_user->ID, 'cs_exp_to_date_array', true);
                $cs_exp_companys = get_user_meta($cs_user->ID, 'cs_exp_company_array', true);
                // get all job types
                $all_specialisms = get_user_meta($cs_user->ID, 'cs_specialisms', true);
                $specialisms_values = '';
                $specialisms_class = '';
                $specialism_html = '';
                $specialism_flag = 1;
                if ($all_specialisms != '') {
                    foreach ($all_specialisms as $specialisms_item) {
                        $specialismsitem = get_term_by('slug', $specialisms_item, 'specialisms');
                        if (is_object($specialismsitem)) {
                            if ($specialism_flag == 1) {
                                $specialism_html .= '<span><a class="cs-color">' . $specialismsitem->name . '</a></span>';
                            } else {
                                $specialism_html .= '<span>, <a class="cs-color">' . $specialismsitem->name . '</a></span>';
                            }
                            $specialisms_values .= $specialismsitem->name;
                            $specialisms_class .= $specialismsitem->slug;
                            if ($specialism_flag != count($all_specialisms)) {
                                $specialisms_values .= ", ";
                                $specialisms_class .= " ";
                            }
                            $specialism_flag++;
                        }
                    }
                }
                $recent_exp_company = '';
                $recent_exp_titles = '';
                $recent_exp_company = '';
                $recent_exp_titles = '';
                if (isset($cs_get_exp_list) && is_array($cs_get_exp_list) && count($cs_get_exp_list) > 0) {
                    $required_index = find_heighest_date_index($cs_exp_to_dates, 'd-m-Y');
                    $recent_exp_company = isset($cs_exp_companys[$required_index]) ? $cs_exp_companys[$required_index] : '';
                    $recent_exp_title = isset($cs_exp_titles[$required_index]) ? $cs_exp_titles[$required_index] : '';
                }
                $last_login_timestring = get_user_last_login($cs_candidate_user);
                $user_registered_timestamp = isset($cs_candidate_user) && $cs_candidate_user != '' ? get_user_registered_timestamp($cs_candidate_user) : '';
                // Check is candidate available for view
                $cs_candidate_view = false;
                $cs_href = '';
                if ($cs_candidate_switch == 'on') {
                    if (is_user_logged_in() && $login_user_is_employer_flag && $cs_emp_funs->cs_check_emp_resume($cs_candidate_user)) {
                        $cs_candidate_view = true;
                    } else {
                        $cs_candidate_view = false;
                    }
                    // check if this candidate apply a job and owner of that job need to view the profile of this candidate
                    if (check_candidate_applications($cs_candidate_user) > 0) {
                        $cs_candidate_view = true;
                    }
                    // check if you this is your own profile
                    if (is_user_logged_in()) {
                        $user_profile_id = get_current_user_id();
                        if ((isset($user_profile_id)) && $cs_candidate_user == $user_profile_id) {
                            $cs_candidate_view = true;
                        }
                    }
                    if ($cs_candidate_view == true) {
                        $cs_href = ' href="' . esc_url(get_author_posts_url($cs_user->ID)) . '"';
                    }
                } else {
                    $cs_href = ' href="' . esc_url(get_author_posts_url($cs_user->ID)) . '"';
                }
                ?>
                <li>
                    <div class="cs-media">
                        <figure>
                            <a <?php echo CS_FUNCTIONS()->cs_special_chars($cs_href) ?>>
                                <img src="<?php echo esc_url($candidate_img) ?>" alt="" />
                            </a>
                        </figure>
                    </div>
                    <div class="cs-text">
                        <div class="cs-post-title">
                            <h5><a <?php echo CS_FUNCTIONS()->cs_special_chars($cs_href) ?> ><?php echo $cs_user->display_name ?></a> <?php if ($cs_post_loc_city != '') { ?><span class="cs-location cs-color"><?php echo ($cs_post_loc_city) ?></span><?php } ?></h5>
                        </div>
                        <div class="post-option">
            <?php if ($recent_exp_titles != '' && $recent_exp_company != '') { ?>
                                <span class="cs-postion"><em><?php echo esc_html($recent_exp_titles) ?></em> @ <?php echo esc_html($recent_exp_company) ?></span>
                                <?php
                            }
                            if ($cs_minimum_salary != '') {
                                echo '<span class="cs-min-salary">' . $default_currency_sign . $cs_minimum_salary . __(' p.a minimum', 'jobhunt') . '</span>';
                            }
                            if ($cs_user_last_activity_date != '') {
                                ?>
                                <span class="cs-post-date"><?php echo __('Last Activity', 'jobhunt') . ' ' . cs_time_elapsed_string($cs_user_last_activity_date); ?></span>
                            <?php } ?>
                        </div>
                            <?php echo force_balance_tags($specialism_html) ?>
                    </div>
                    <div class="cs-uploaded jobseeker-detail">
                        <span class="cs-btn-holder">
            <?php
            $cs_candidate = $cs_candidate_user;
            if ($cs_candidate_switch == 'on') {
				
                if (!is_user_logged_in()) {
                    ?>
                                    <a data-toggle="tooltip" data-placement="top" title="<?php echo __("Add to List", "jobhunt"); ?>" class="ad_to_list add_list_icon cs-button" onclick="trigger_func('#btn-header-main-login');"><?php _e('Add to List', 'jobhunt') ?></a>
                                    <?php
                                } else if (is_user_logged_in() && !$login_user_is_employer_flag) {
                                    ?>
                                    <a data-toggle="tooltip" data-placement="top" title="<?php echo __("Add to List", "jobhunt"); ?>" class="ad_to_list add_list_icon cs-button" id="cs_empl_check_<?php echo absint($cs_candidate_user) ?>" data-id="<?php echo absint($cs_candidate_user) ?>"><?php _e('Add to List', 'jobhunt') ?></a>
                                    <?php
                                } else if (is_user_logged_in() && $login_user_is_employer_flag) {
                                    if (is_array($cs_fav_resumes) && in_array($cs_candidate, $cs_fav_resumes)) {
                                        ?>
                                        <a <?php echo CS_FUNCTIONS()->cs_special_chars($cs_href) ?> class="add_list_icon ad_to_list cs_resume_added cs-button"><?php _e('View Detail', 'jobhunt') ?></a>
                                        <?php
                                    } else {
                                        ?>
                                        <a data-toggle="tooltip" data-placement="top" title="<?php echo __("Add to List", "jobhunt"); ?>" class="add_list_icon ad_to_list cs-button" id="add-to-btn-<?php echo absint($cs_candidate_user) ?>" onclick="cs_add_to_list('<?php echo esc_js(admin_url('admin-ajax.php')); ?>', '<?php echo absint($cs_candidate_user) ?>')"><?php _e('Add to List', 'jobhunt') ?></a>
                                        <?php
                                    }
                                }
                            } 
				//else {
                                // not payment chargable list
                                if (!is_user_logged_in()) {
                                    ?>
                                    <a data-toggle="tooltip" data-placement="top" title="<?php echo __("Shortlist", "jobhunt"); ?>" class="ad_to_short_list add_list_icon cs-button" onclick="trigger_func('#btn-header-main-login');"><i class="icon-plus-circle"></i><?php _e('Shortlist', 'jobhunt') ?></a>
                                    <?php
                                } else if (is_user_logged_in() && !$login_user_is_employer_flag) {
                                    ?>
                                    <a data-toggle="tooltip" data-placement="top" title="<?php echo __("Shortlist", "jobhunt"); ?>" class="ad_to_short_list add_list_icon cs-button" id="cs_empl_check_<?php echo absint($cs_candidate_user) ?>" data-id="<?php echo absint($cs_candidate_user) ?>"><i class="icon-plus-circle"></i><?php _e('Shortlist', 'jobhunt') ?></a>
                                    <?php
                                } else if (is_user_logged_in() && $login_user_is_employer_flag) {
                                    $finded_result_list = cs_find_index_user_meta_list($cs_candidate, 'cs-user-resumes-wishlist', 'post_id', cs_get_user_id());
                                    if (is_array($finded_result_list) && !empty($finded_result_list)) {
                                        ?>
                                        <a data-toggle="tooltip" data-placement="top" title="<?php echo __("Shortlisted", "jobhunt"); ?>" href="javascript:void(0);" class="ad_to_short_list add_list_icon cs_resume_added cs-button" id="add-to-btn-<?php echo absint($cs_candidate_user) ?>" onclick="cs_add_favr('<?php echo esc_js(admin_url('admin-ajax.php')); ?>', '<?php echo absint($cs_candidate_user) ?>', 'remove')"><i class="icon-minus-circle"></i><?php _e('Shortlisted', 'jobhunt') ?></a>
                                        <?php
                                    } else {
                                        ?>
                                        <a data-toggle="tooltip" data-placement="top" title="<?php echo __("Shortlist", "jobhunt"); ?>" class="ad_to_short_list add_list_icon cs-button" id="add-to-btn-<?php echo absint($cs_candidate_user) ?>" onclick="cs_add_favr('<?php echo esc_js(admin_url('admin-ajax.php')); ?>', '<?php echo absint($cs_candidate_user) ?>', 'add')"><i class="icon-plus-circle"></i><?php _e('Shortlist', 'jobhunt') ?></a>
                                        <?php
                                    }
                                }
                      //}
                            ?>
                        </span>
                    </div>
                </li>
            <?php
            $counter ++;
            $flag++;
        }
    }
} else {
    echo '<li class="ln-no-match">';
    echo '<div class="massage-notfound">
            <div class="massage-title">
             <h6><i class="icon-warning4"></i><strong> ' . __('Sorry !', 'jobhunt') . '</strong>&nbsp; ' . __("There are no listings matching your search.", 'jobhunt') . ' </h6>
            </div>
             <ul>
                <li>' . __("Please re-check the spelling of your keyword", 'jobhunt') . ' </li>
                <li>' . __("Try broadening your search by using general terms", 'jobhunt') . '</li>
                <li>' . __("Try adjusting the filters applied by you", 'jobhunt') . '</li>
             </ul>
          </div>';
    echo '</li>';
}
?> 

</ul>
<?php
if ((isset($users_per_page) && $count_post > $users_per_page && $users_per_page > 0) && $a['cs_candidate_show_pagination'] == 'pagination') {
    echo '<nav>';
    cs_user_pagination($total_pages, $page);
    echo '</nav>';
}
if ($a['cs_candidate_searchbox'] == 'yes') {
    echo '</div>';
}