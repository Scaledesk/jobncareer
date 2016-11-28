<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
global $wpdb, $cs_plugin_options, $cs_candidate_title, $current_user;
$cs_emp_funs = new cs_employer_functions();
$cs_candidate_switch = isset($cs_plugin_options['cs_candidate_switch']) ? $cs_plugin_options['cs_candidate_switch'] : '';
$cs_fav_resumes = get_user_meta($current_user->ID, "cs_fav_resumes", true);

if ($a['cs_candidate_searchbox'] == 'yes') {
    echo '<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">';
}
?>
<div class="cs-candidate grid">
    <?php
    include plugin_dir_path(__FILE__) . '../candidate-search-keywords.php';
    ?>
    <ul class="row">
        <?php
        // getting if record not found
        if ($count_post > 0) {

            $loop = new WP_User_Query($args);

            $flag = 1;
            if (!empty($loop->results)) {
                foreach ($loop->results as $cs_user) {

                    $cs_job_posted = get_user_meta($cs_user->ID, 'cs_job_posted', true);
                    $cs_minimum_salary = get_user_meta($cs_user->ID, 'cs_minimum_salary', true);
                    $cs_job_title = get_user_meta($cs_user->ID, 'cs_job_title', true);
                    $cs_job_featured = get_user_meta($cs_user->ID, 'cs_job_featured', true);
                    $cs_jobs_address = get_user_address_string_for_list($cs_user->ID, 'usermeta');
                    $cs_jobs_thumb_url = get_user_meta($cs_user->ID, 'user_img', true);

                    $cs_get_exp_list = get_user_meta($cs_user->ID, 'cs_exp_list_array', true);
                    $cs_exp_titles = get_user_meta($cs_user->ID, 'cs_exp_title_array', true);
                    $cs_exp_from_dates = get_user_meta($cs_user->ID, 'cs_exp_from_date_array', true);
                    $cs_exp_to_dates = get_user_meta($cs_user->ID, 'cs_exp_to_date_array', true);
                    $cs_exp_companys = get_user_meta($cs_user->ID, 'cs_exp_company_array', true);

                    $cs_job_featured = get_user_meta($cs_user->ID, 'cs_job_featured', true);
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
                                    $specialism_html .= '<span><a>' . $specialismsitem->name . '</a><span>';
                                } else {
                                    $specialism_html .= ', <span><a>' . $specialismsitem->name . '</a><span>';
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

                    $cs_jobs_thumb_url = get_user_meta($cs_user->ID, 'user_img', true);
                    $cs_jobs_thumb_url = cs_get_img_url($cs_jobs_thumb_url, 'cs_media_4');
                    $cs_ext = pathinfo($cs_jobs_thumb_url, PATHINFO_EXTENSION);
                    $cs_currency_sign = isset($cs_plugin_options['cs_currency_sign']) ? $cs_plugin_options['cs_currency_sign'] : '';

                    $recent_exp_company = '';
                    $recent_exp_company = '';
                    $recent_exp_titles = '';
                    if (isset($cs_get_exp_list) && is_array($cs_get_exp_list) && count($cs_get_exp_list) > 0) {
                        $required_index = find_heighest_date_index($cs_exp_to_dates, 'd-m-Y');
                        $recent_exp_company = isset($cs_exp_companys[$required_index]) ? $cs_exp_companys[$required_index] : '';
                        $recent_exp_title = isset($cs_exp_titles[$required_index]) ? $cs_exp_titles[$required_index] : '';
                    }

                    if (isset($cs_candidate_cols) && $cs_candidate_cols != '') {
                        $number_col = 12 / $cs_candidate_cols;
                        $number_col_sm = 12;
                        $number_col_xs = 12;
                        if ($number_col == 2) {
                            $number_col_sm = 4;
                            $number_col_xs = 6;
                        }
                        if ($number_col == 3) {
                            $number_col_sm = 6;
                            $number_col_xs = 12;
                        }
                        if ($number_col == 4) {
                            $number_col_sm = 6;
                            $number_col_xs = 12;
                        }
                        if ($number_col == 6) {
                            $number_col_sm = 12;
                            $number_col_xs = 12;
                        }
                        $col_class = 'col-lg-' . $number_col . ' col-md-' . $number_col . ' col-sm-' . $number_col_sm . ' col-xs-' . $number_col_xs . '';
                    }

                    // Check is candidate available for view
                    $cs_candidate_user = $cs_user->ID;
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
                    <li class="<?php echo $col_class; ?>">
                        <div class="cs-media">
                            <figure>
                                <?php if ($cs_jobs_thumb_url != '' && $cs_ext != '') { ?>
                                    <a <?php echo CS_FUNCTIONS()->cs_special_chars($cs_href) ?>><img alt="" src="<?php echo esc_url($cs_jobs_thumb_url); ?>"></a>
                                    <?php
                                } else {
                                    $cs_jobs_thumb_url = esc_url(wp_jobhunt::plugin_url() . 'assets/images/candidate-no-image.jpg');
                                    ?>
                                    <a <?php echo CS_FUNCTIONS()->cs_special_chars($cs_href) ?>><img alt="" src="<?php echo esc_url($cs_jobs_thumb_url); ?>"></a>
                                    <?php
                                }
                                ?>
                                <figcaption>
                                    <div class="cs-text">
                                        <h6><?php echo force_balance_tags($cs_user->display_name); ?></h6>
                                        <?php if ($recent_exp_titles != '') { ?>
                                            <a <?php echo CS_FUNCTIONS()->cs_special_chars($cs_href) ?>><?php echo esc_html($recent_exp_titles) ?></a>
                                        <?php } if ($cs_jobs_address != '') { ?>
                                            <address>
                                                <?php echo esc_html($cs_jobs_address); ?>
                                            </address>
                                        <?php } ?>
                                    </div>
                                </figcaption>
                            </figure>
                        </div>
                    </li>
                    <?php
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
//==Pagination Start
    if ((isset($users_per_page) && $count_post > $users_per_page && $users_per_page > 0) && $a['cs_candidate_show_pagination'] == 'pagination') {
        echo '<nav>';
        cs_user_pagination($total_pages, $page);
        echo '</nav>';
    }//==Pagination End 
    ?>
</div>
<?php
if ($a['cs_candidate_searchbox'] == 'yes') {
    echo '</div>';
}
