<?php
/**
 * File Type: Template Functions
 * User profile links
 */
/**
 * Start Function how to get candidate porfile page id
 */
if (!function_exists('cs_candidate_post_id')) {

    function cs_candidate_post_id($uid = '') {
        global $post, $cs_form_fields2;
        $cs_post_id = '';
        if (isset($uid) and $uid <> '') {
            $args = array(
                'posts_per_page' => "1",
                'post_type' => 'candidate',
                'post_status' => 'publish',
                'meta_key' => 'cs_user',
                'meta_value' => (int) $uid
            );
            $custom_query = new WP_Query($args);
            if ($custom_query->have_posts()):
                while ($custom_query->have_posts()): $custom_query->the_post();
                    $cs_post_id = $post->ID;
                endwhile;
            endif;
            wp_reset_postdata();
        } else {
            $cs_post_id = '';
        }
        return $cs_post_id;
    }

}
// Start function for employer post id
if (!function_exists('cs_employer_post_id')) {

    function cs_employer_post_id($uid = '') {
        global $post;
        $cs_post_id = '';
        if (isset($uid) and $uid <> '') {
            $args = array(
                'posts_per_page' => "1",
                'post_type' => 'employer',
                'post_status' => 'publish',
                'meta_key' => 'cs_user',
                'meta_value' => (int) $uid
            );
            $custom_query = new WP_Query($args);
            if ($custom_query->have_posts()) :
                while ($custom_query->have_posts()) : $custom_query->the_post();
                    $cs_post_id = $post->ID;
                endwhile;
            endif;
            wp_reset_postdata();
        }else {
            $cs_post_id = '';
        }
        return $cs_post_id;
    }

}
/**
 * End Function how to get candidate porfile page id
 */
/**
 * Start Function how to get candidate porfile link
 */
if (!function_exists('cs_user_admin_profile_link')) {

    function cs_user_admin_profile_link($page_id = '', $profile_page = '', $uid = '') {
        if (!isset($page_id) or $page_id == '') {
            $user_link = home_url('/') . '?author=' . $uid;
        } else {
            $user_link = get_permalink($page_id) . '#' . $profile_page;
        }
        return esc_url($user_link);
    }

}
/**
 * Start function to get user profile link
 */
if (!function_exists('cs_users_profile_link')) {

    function cs_users_profile_link($page_id = '', $profile_page = '', $uid = '') {
        $user_link = '';
        if (!isset($page_id) or $page_id == '') {
            $user_link = home_url('/') . '?author=' . $uid;
        } else {
            if (strpos(get_permalink($page_id), "?") !== false) {
                $user_link = get_permalink($page_id) . '&profile_tab=' . $profile_page;
            } else {
                $user_link = get_permalink($page_id) . '?profile_tab=' . $profile_page;
            }
        }
        return esc_url($user_link);
    }

}
/**
 * Start Function how to user logout
 */
if (!function_exists('cs_user_logout')) {

    function cs_user_logout($action = '', $uid = '') {
        if (is_user_logged_in()) {
            echo '<a  href="' . esc_url(wp_logout_url(cs_server_protocol() . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'])) . '"><i class="icon-logout"></i>' . __('Logout', 'jobhunt') . '</a>';
        }
    }

}
/**
 * end Function how to user logout
 */
/**
 * Start Function how to add candidate profile menu
 */
if (!function_exists('cs_profile_menu')) {

    function cs_profile_menu($action = '', $uid = '') {
        global $cs_plugin_options, $current_user, $wp_roles, $userdata;
        $cs_page_id = isset($cs_theme_options['cs_dashboard']) ? $cs_theme_options['cs_dashboard'] : '';
        $cs_resume_display = isset($_REQUEST['profile_tab']) && $_REQUEST['profile_tab'] == 'resume' ? 'block' : 'none';
        $cs_education_switch = isset($cs_plugin_options['cs_education_switch']) ? $cs_plugin_options['cs_education_switch'] : '';
        $cs_experience_switch = isset($cs_plugin_options['cs_experience_switch']) ? $cs_plugin_options['cs_experience_switch'] : '';
        $cs_portfolio_switch = isset($cs_plugin_options['cs_portfolio_switch']) ? $cs_plugin_options['cs_portfolio_switch'] : '';
        $cs_skills_switch = isset($cs_plugin_options['cs_skills_switch']) ? $cs_plugin_options['cs_skills_switch'] : '';
        $cs_award_switch = isset($cs_plugin_options['cs_award_switch']) ? $cs_plugin_options['cs_award_switch'] : '';
        ?>
        <aside class="section-sidebar col-lg-3 col-md-3 col-sm-12 col-xs-12">
            <ul class="account-menu">
                <li id="candidate_left_profile_link" <?php if ((isset($_REQUEST['profile_tab']) && $_REQUEST['profile_tab'] == 'profile') || (!isset($_REQUEST['profile_tab']) || $_REQUEST['profile_tab'] == '')) echo 'class="active"'; ?>>
                    <a id="candidate_profile_click_link_id"  href="javascript:void(0);" onclick="cs_dashboard_tab_load('profile', 'candidate', '<?php echo esc_js(admin_url('admin-ajax.php')); ?>', '<?php echo absint($uid); ?>');" ><i class="icon-user9"></i><?php _e('My Profile', 'jobhunt'); ?></a>
                </li> 
                <?php if ($cs_education_switch == 'on' || $cs_experience_switch == 'on' || $cs_portfolio_switch == 'on' || $cs_skills_switch == 'on' || $cs_award_switch == 'on') { ?>
                    <li id="candidate_left_resume_link" <?php if (isset($_REQUEST['profile_tab']) && $_REQUEST['profile_tab'] == 'resume') echo 'class="active"'; ?>>
                        <a id="candidate_resume_click_link_id"  href="javascript:void(0);" onclick="cs_dashboard_tab_load('resume', 'candidate', '<?php echo esc_js(admin_url('admin-ajax.php')); ?>', '<?php echo absint($uid); ?>');" ><i class="icon-newspaper4"></i><?php _e('My Resume', 'jobhunt'); ?></a>	
                        <div id="inner-links" style="display: <?php echo sanitize_html_class($cs_resume_display) ?>;">
                            <ul>
                                <?php if ($cs_education_switch == 'on') { ?>
                                    <li><a href="#education"><?php _e('Education', 'jobhunt'); ?></a></li>
                                <?php } if ($cs_experience_switch == 'on') { ?>
                                    <li><a href="#experience"><?php _e('Experience', 'jobhunt'); ?></a></li>
                                <?php } if ($cs_portfolio_switch == 'on') { ?>
                                    <li><a href="#portfolio"><?php _e('Portfolio', 'jobhunt'); ?></a></li>
                                <?php } if ($cs_skills_switch == 'on') { ?>
                                    <li><a href="#skills"><?php _e('Skills', 'jobhunt'); ?></a></li>
                                <?php } if ($cs_award_switch == 'on') { ?>
                                    <li><a href="#awards"><?php _e('Honors Award', 'jobhunt'); ?></a></li>
                                <?php } ?>
                            </ul>
                        </div>
                    </li>
                <?php } ?>
                <li id="candidate_left_shortlisted_jobs_link" <?php if (isset($_REQUEST['profile_tab']) && $_REQUEST['profile_tab'] == 'shortlisted_jobs') echo 'class="active"'; ?>>
                    <a id="candidate_shortlisted_jobs_click_link_id"  href="javascript:void(0);" onclick="cs_dashboard_tab_load('shortlisted-jobs', 'candidate', '<?php echo esc_js(admin_url('admin-ajax.php')); ?>', '<?php echo absint($uid); ?>');" ><i class="icon-heart6"></i><?php _e('Shortlisted jobs', 'jobhunt'); ?></a>
                </li>
                <li id="candidate_left_applied_jobs_link" <?php if (isset($_REQUEST['profile_tab']) && $_REQUEST['profile_tab'] == 'applied_jobs') echo 'class="active"'; ?>>
                    <a id="candidate_applied_jobs_click_link_id"  href="javascript:void(0);" onclick="cs_dashboard_tab_load('applied-jobs', 'candidate', '<?php echo esc_js(admin_url('admin-ajax.php')); ?>', '<?php echo absint($uid); ?>');" ><i class="icon-briefcase2"></i> <?php _e('Applied jobs', 'jobhunt'); ?></a>	
                </li>
                <?php
                // Register hook to allow addiont of new items via addons
				$profile_tab = isset( $_REQUEST['profile_tab'] ) ? $_REQUEST['profile_tab'] : '';
                do_action('jobhunt_candidate_dashboard_menu_left', $profile_tab, $uid);
                ?>
                <li id="candidate_left_cv_link" <?php if (isset($_REQUEST['profile_tab']) && $_REQUEST['profile_tab'] == 'cv') echo 'class="active"'; ?>>
                    <a id="candidate_cv_click_link_id"  href="javascript:void(0);" onclick="cs_dashboard_tab_load('cv', 'candidate', '<?php echo esc_js(admin_url('admin-ajax.php')); ?>', '<?php echo absint($uid); ?>');" ><i class="icon-vcard"></i><?php _e('CV &amp; Cover Letter', 'jobhunt'); ?></a>
                </li>
                <li id="candidate_left_change_password_link" <?php if (isset($_REQUEST['profile_tab']) && $_REQUEST['profile_tab'] == 'change_password') echo 'class="active"'; ?>>
                    <a id="candidate_change_password_click_link_id"  href="javascript:void(0);" onclick="cs_dashboard_tab_load('change_password', 'candidate', '<?php echo esc_js(admin_url('admin-ajax.php')); ?>', '<?php echo absint($uid); ?>');" ><i class="icon-key4"></i><?php _e('Change Password', 'jobhunt'); ?></a>
                </li>
		<li><a href="resume-writing/">CXO RESUME</a></li>
		<li><a href="cv-packages/">Star Profiles</a></li>
                <li><?php cs_user_logout(); ?></li>
                <li><a href="www.scaledesk.com">CXO RESUME</a></li>

            </ul>
            <?php
            $candidate_skills = cs_candidate_skills_calculation($current_user);
            $candidate_skills_perc = isset($candidate_skills['percentage']) ? $candidate_skills['percentage'] : 0;
            $candidate_skills_msg = isset($candidate_skills['msg']) ? $candidate_skills['msg'] : '';

            echo '<div class="skill-percent-main">';
            if ($candidate_skills_perc >= 0 && $candidate_skills_perc <= 100) {
                echo '
                <div class="skills-percentage-bar">
                <h6>' . __('Skills Percentage:', 'jobhunt') . '</h6>
                <div class="skill-process"><span style="width:' . $candidate_skills_perc . '%; background:#55a747;"><span>' . $candidate_skills_perc . '%</span></span></div>
                </div>';
            } else if ($candidate_skills_perc > 100) {
                echo '
                <div class="skills-percentage-bar">
                <h6>' . __('Skills Percentage:', 'jobhunt') . '</h6>
                <div class="skill-process"><span style="width:100%; background:#55a747;"><span>100%</span></span></div>
                </div>';
            }
            if ($candidate_skills_msg != '') {
                echo '<p>' . $candidate_skills_msg . '</p>';
            }
            echo '</div>';
            ?>
        </aside>
        <?php
    }

}
/**
 * Start function to get user profile image
 */
if (!function_exists('cs_user_profile')) {

    function cs_user_profile($cs_post_id = '', $cs_media_img = 'cs_media_img') {
        $cs_image_url = get_post_meta($cs_post_id, $cs_media_img, true);
        if (isset($cs_image_url) and $cs_image_url <> '') {
            $cs_image_url = '<img src="' . esc_url($cs_image_url) . '" alt="" width="50" height="50">';
        } else {
            $cs_image_url = '<i class="icon-user9"></i>';
        }
        return $cs_image_url;
    }

}

/**
 * Start Function how to add candidate profile menu in top position
 */
if (!function_exists('cs_profiletop_menu')) {

    function cs_profiletop_menu($action = '', $uid = '') {
        global $post, $cs_plugin_options, $current_user, $wp_roles, $userdata;
        $uid = (isset($uid) and $uid <> '') ? $uid : $current_user->ID;
        $user_display_name = get_the_author_meta('display_name', $uid);
        $cs_page_id = isset($cs_theme_options['cs_dashboard']) ? $cs_theme_options['cs_dashboard'] : '';
        $cs_candidate_switch = isset($cs_plugin_options['cs_candidate_switch']) ? $cs_plugin_options['cs_candidate_switch'] : '';

        $user_role = cs_get_loginuser_role();
        $cs_profile_img_name = '';
        $cs_user_role_type = '';
        if (isset($user_role) && $user_role <> '' && $user_role == 'cs_employer') {
            $cs_user_role_type = 'employer';
            $cs_page_id = isset($cs_plugin_options['cs_emp_dashboard']) ? $cs_plugin_options['cs_emp_dashboard'] : '';
            $cs_profile_img_name = get_the_author_meta('user_img', $uid);
        } elseif (isset($user_role) && $user_role <> '' && $user_role == 'cs_candidate') {
            $cs_user_role_type = 'candidate';
            $cs_page_id = isset($cs_plugin_options['cs_js_dashboard']) ? $cs_plugin_options['cs_js_dashboard'] : '';
            $cs_profile_img_name = get_the_author_meta('user_img', $uid);
        }
        $cs_loc_country = get_the_author_meta('cs_post_loc_country', $uid);
        $cs_loc_city = get_the_author_meta('cs_post_loc_city', $uid);
        $menu_cls = $data_toogle = '';
        if ($cs_page_id == get_the_ID()) {
            $menu_cls = 'nav nav-tabs';
            $data_toogle = '';
        }
        $cs_profile_image = '';
        ?>
        <div class="login">
            <div class="login-dashboard-main">
                <div class="cs-loging-dashboard">
                    <?php
                    $cs_profile_image = cs_get_image_url($cs_profile_img_name, 'cs_media_4');
                    if (!cs_image_exist($cs_profile_image) || $cs_profile_image == "") {
                        $cs_profile_image = esc_url(wp_jobhunt::plugin_url() . 'assets/images/img-not-found16x9.jpg');
                    }
                    ?>
                    <div class="dropdown keep-open">  
                        <a class="navicon-button x dropdown-toggle" data-toggle="dropdown" href="#">
                            <div class="navicon"></div>
                            <figure><?php
                                if ($cs_profile_image != '') {
                                    echo '<img src="' . esc_url($cs_profile_image) . '" alt="" width="50" height="40">';
                                }
                                ?>
                            </figure>
                        </a>
                        <div class="cs-login-dropdown">
                            <?php
                            if ($cs_page_id != '' && $cs_user_role_type != '') {
                                if ($cs_user_role_type == 'candidate') {
                                    ?>
                                    <ul class="dropdown-menu <?php echo esc_html($menu_cls); ?>">
                                        <li>
                                            <h5><?php echo esc_html($user_display_name) ?></h5>
                                            <?php
                                            if ($cs_loc_country != '' && $cs_loc_city != '') {
                                                ?>
                                                <span><?php echo esc_html(ucfirst(urldecode($cs_loc_country))) . ', ' . esc_html(ucfirst(urldecode($cs_loc_city))) ?></span>
                                                <?php
                                            }
                                            if (is_user_logged_in()) {
                                                ?>
                                                <a class="logout-btn" href="<?php echo esc_url(wp_logout_url(cs_server_protocol() . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'])) ?>"><i class="icon-logout"></i></a>
                                                <?php
                                            }
                                            ?>
                                        </li>
                                        <li>
                                            <a href="<?php echo esc_url(cs_users_profile_link($cs_page_id, 'profile', $uid)); ?>" <?php echo force_balance_tags($data_toogle); ?>><i class="icon-user9"></i> <?php _e('My Profile', 'jobhunt'); ?></a>
                                        </li> 
                                        <li>
                                            <a href="<?php echo esc_url(cs_users_profile_link($cs_page_id, 'resume', $uid)); ?>" <?php echo force_balance_tags($data_toogle); ?>><i class="icon-newspaper4"></i> <?php _e('My Resume', 'jobhunt'); ?></a>	
                                        </li>
                                        <li>
                                            <a href="<?php echo esc_url(cs_users_profile_link($cs_page_id, 'shortlisted_jobs', $uid)); ?>" <?php echo force_balance_tags($data_toogle); ?>><i class="icon-heart11"></i> <?php _e('Shortlisted jobs', 'jobhunt'); ?></a>
                                        </li>
                                        <li>
                                            <a href="<?php echo esc_url(cs_users_profile_link($cs_page_id, 'applied_jobs', $uid)); ?>" <?php echo force_balance_tags($data_toogle); ?>><i class="icon-suitcase5"></i> <?php _e('Applied jobs', 'jobhunt'); ?></a>	
                                        </li>
                                        <?php
											do_action('jobhunt_top_menu_employer_dashboard', $cs_page_id, $uid, $data_toogle );
                                        ?>
                                        <li>
                                            <a href="<?php echo esc_url(cs_users_profile_link($cs_page_id, 'cv', $uid)); ?>" <?php echo force_balance_tags($data_toogle); ?>><i class="icon-vcard"></i> <?php _e('CV & cover letter', 'jobhunt'); ?></a>
                                        </li>
                                        <li>
                                            <a href="<?php echo esc_url(cs_users_profile_link($cs_page_id, 'change_password', $uid)); ?>" <?php echo force_balance_tags($data_toogle); ?>><i class="icon-key4"></i> <?php _e('Change Password', 'jobhunt'); ?></a>
                                        </li>
                                    </ul>
                                <?php } elseif ($cs_user_role_type == 'employer') { ?>
                                    <ul class="dropdown-menu <?php echo esc_html($menu_cls); ?>">
                                        <li>
                                            <h5><?php echo esc_html($user_display_name) ?></h5>
                                            <?php
                                            if ($cs_loc_country != '' && $cs_loc_city != '') {
                                                ?>
                                                <span><?php echo esc_html(ucfirst(urldecode($cs_loc_country))) . ', ' . esc_html(ucfirst(urldecode($cs_loc_city))) ?></span>
                                                <?php
                                            }
                                            if (is_user_logged_in()) {
                                                ?>
                                                <a class="logout-btn" href="<?php echo esc_url(wp_logout_url(cs_server_protocol() . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'])) ?>"><i class="icon-logout"></i></a>
                                                <?php
                                            }
                                            ?>
                                        </li>
                                        <li>
                                            <a href="<?php echo esc_url(cs_users_profile_link($cs_page_id, 'profile', $uid)); ?>" <?php echo force_balance_tags($data_toogle); ?>><i class="icon-newspaper4"></i> <?php _e('Company Profile', 'jobhunt'); ?></a>
                                        </li> 
                                        <li>
                                            <a href="<?php echo esc_url(cs_users_profile_link($cs_page_id, 'jobs', $uid)); ?>" <?php echo force_balance_tags($data_toogle); ?>><i class="icon-suitcase5"></i> <?php _e('Manage Jobs', 'jobhunt'); ?></a>
                                        </li>
                                        <li>
                                            <a href="<?php echo esc_url(cs_users_profile_link($cs_page_id, 'transactions', $uid)); ?>" <?php echo force_balance_tags($data_toogle); ?>><i class="icon-graph"></i> <?php _e('Transactions', 'jobhunt'); ?></a>	
                                        </li><?php if ($cs_candidate_switch == 'on') { ?>
                                            <li>
                                                <a href="<?php echo esc_url(cs_users_profile_link($cs_page_id, 'resumes', $uid)); ?>" <?php echo force_balance_tags($data_toogle); ?>><i class="icon-heart11"></i> <?php _e('Resumes', 'jobhunt'); ?></a>	
                                            </li>
                                        <?php } else { ?>
                                            <li>
                                                <a href="<?php echo esc_url(cs_users_profile_link($cs_page_id, 'resumes', $uid)); ?>" <?php echo force_balance_tags($data_toogle); ?>><i class="icon-newspaper4"></i> <?php _e('Resumes', 'jobhunt'); ?></a>	
                                            </li>
                                        <?php } ?>
                                        <li>
                                            <a href="<?php echo esc_url(cs_users_profile_link($cs_page_id, 'packages', $uid)); ?>" <?php echo force_balance_tags($data_toogle); ?>><i class="icon-vcard"></i> <?php _e('Packages', 'jobhunt'); ?></a>
                                        </li>
                                        <li>
                                            <a href="<?php echo esc_url(cs_users_profile_link($cs_page_id, 'postjobs', $uid)); ?>" <?php echo force_balance_tags($data_toogle); ?>><i class="icon-plus-square"></i> <?php _e('Post a New Job', 'jobhunt'); ?></a>
                                        </li>
										<?php
											do_action('jobhunt_top_menu_employer_dashboard', $cs_page_id, $uid, $data_toogle );
                                        ?>

                                        <li>
                                            <a href="<?php echo esc_url(cs_users_profile_link($cs_page_id, 'change_password', $uid)); ?>" <?php echo force_balance_tags($data_toogle); ?>><i class="icon-key4"></i> <?php _e('Change Password', 'jobhunt'); ?></a>
                                        </li>

                                    </ul>
                                    <?php
                                }
                            } else {
                                ?>
                                <ul class="dropdown-menu <?php echo esc_html($menu_cls); ?>">
                                    <li>
                                        <h5><?php echo esc_html($user_display_name) ?></h5>
                                        <?php
                                        if (is_user_logged_in()) {
                                            ?>
                                            <a class="logout-btn" href="<?php echo esc_url(wp_logout_url(cs_server_protocol() . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'])) ?>"><i class="icon-logout"></i></a>
                                                <?php
                                            }
                                            ?>
                                    </li>
                                </ul>
                            <?php }
                            ?>                    
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

}
/**
 * end Function how to add candidate profile menu in top position
 *
 * Start Function how to show Education in list
 */
if (!function_exists('cs_education_list_fe')) {

    function cs_education_list_fe() {
        global $post, $cs_form_fields_frontend, $current_user, $cs_form_fields2, $cs_html_fields_frontend, $cs_html_fields;
        $uid = $current_user->ID;
        $cs_opt_array = '';

        $cs_get_edu_list = isset($uid) ? get_user_meta($uid, 'cs_edu_list_array', true) : '';
        $cs_edu_titles = isset($uid) ? get_user_meta($uid, 'cs_edu_title_array', true) : '';
        $cs_edu_from_dates = isset($uid) ? get_user_meta($uid, 'cs_edu_from_date_array', true) : '';
        $cs_edu_to_dates = isset($uid) ? get_user_meta($uid, 'cs_edu_to_date_array', true) : '';
        $cs_edu_institutes = isset($uid) ? get_user_meta($uid, 'cs_edu_institute_array', true) : '';
        $cs_edu_descs = isset($uid) ? get_user_meta($uid, 'cs_edu_desc_array', true) : '';
        $html = '
	<div class="cs-list-table">
		<ul class="top-heading-list">
			<li><span>' . __('Qualification', 'jobhunt') . '</span></li>
			<li><span>' . __('Dates', 'jobhunt') . '</span></li>
			<li><span>' . __('School / Colleges', 'jobhunt') . '</span></li>
		</ul>
		<ul id="total_education_list" class="accordion-list">';
        if (isset($cs_get_edu_list) && is_array($cs_get_edu_list) && count($cs_get_edu_list) > 0) {
            $cs_award_counter = 0;

            foreach ($cs_get_edu_list as $award_list) {
                if (isset($award_list) && $award_list <> '') {

                    $counter_extra_feature = $extra_feature_id = $award_list;
                    $cs_edu_title = isset($cs_edu_titles[$cs_award_counter]) ? $cs_edu_titles[$cs_award_counter] : '';
                    $cs_edu_from_date = isset($cs_edu_from_dates[$cs_award_counter]) ? $cs_edu_from_dates[$cs_award_counter] : '';
                    $cs_edu_to_date = isset($cs_edu_to_dates[$cs_award_counter]) ? $cs_edu_to_dates[$cs_award_counter] : '';
                    $cs_edu_institute = isset($cs_edu_institutes[$cs_award_counter]) ? $cs_edu_institutes[$cs_award_counter] : '';
                    $cs_edu_desc = isset($cs_edu_descs[$cs_award_counter]) ? $cs_edu_descs[$cs_award_counter] : '';
                    $ca_awards_array = array(
                        'counter_extra_feature' => $counter_extra_feature,
                        'extra_feature_id' => $extra_feature_id,
                        'cs_edu_title' => $cs_edu_title,
                        'cs_edu_from_date' => $cs_edu_from_date,
                        'cs_edu_to_date' => $cs_edu_to_date,
                        'cs_edu_institute' => $cs_edu_institute,
                        'cs_edu_desc' => $cs_edu_desc,
                        'cs_get_edu_list' => $award_list,
                    );
                    $html .= cs_add_education_to_list_fe($ca_awards_array);
                }
                $cs_award_counter++;
            }
        } else {
            $html .= '<li class="cs-no-record">' . cs_info_messages_listing(__("There is no record in education list", 'jobhunt')) . '</li>';
        }
        $html .= '
		</ul>
	</div>
	<div id="add_education" style="display: none;">
		<div class="btm-section">
                    <div class="input-info">
                        <div class="row">
                            <div class="cs-heading-area">
				<span class="cs-btnclose" onClick="javascript:cs_remove_overlay(\'add_education\',\'append\')"> <i class="icon-times"></i></span> 	
			</div>';
        $cs_opt_array = array('name' => __('Title', 'jobhunt'),
            'desc' => '',
            'field_params' => array(
                'std' => '',
                'id' => 'cs_edu_title',
                'before' => 'col-md-12',
                'after' => '</div>',
                'classes' => 'cs-form-text cs-input form-control',
                'extra_atr' => 'placeholder="' . __('Title*', 'jobhunt') . '"',
                'cust_id' => 'cs_edu_title',
                'cust_name' => '',
                'return' => true,
                'required' => true
            ),
        );


        $html .= $cs_html_fields_frontend->cs_form_text_render($cs_opt_array);

        $html .= '<script>
				jQuery(function(){
                        jQuery("#cs_edu_from_date").datetimepicker({
                            format:"d-m-Y",
                            timepicker:false
                        });
                });
				jQuery(function(){
                        jQuery("#cs_edu_to_date").datetimepicker({
                            format:"d-m-Y",
                            timepicker:false
                        });
                });
				</script>';

        $cs_opt_array = array('name' => __('From Date*', 'jobhunt'),
            'desc' => '',
            'field_params' => array(
                'std' => '',
                'id' => 'cs_edu_from_date',
                'before' => 'col-md-6',
                'after' => '</div>',
                'id' => 'edu_from_date',
                'classes' => 'cs-form-text cs-input form-control',
                'extra_atr' => 'placeholder="' . __('From Date*', 'jobhunt'). '"  ',
                'cust_id' => 'cs_edu_from_date',
                'cust_name' => '',
                'return' => true,
                'required' => true
            ),
        );

        $html .= $cs_html_fields_frontend->cs_form_text_render($cs_opt_array);

        $cs_opt_array = array('name' => __('To Date*', 'jobhunt'),
            'desc' => '',
            'field_params' => array(
                'std' => '',
                'id' => 'cs_edu_to_date',
                'before' => 'col-md-6',
                'after' => '</div>',
                'id' => 'edu_from_date',
                'classes' => 'cs-form-text cs-input form-control',
                'extra_atr' => 'placeholder="' . __('To Date*', 'jobhunt'). '"',
                'cust_id' => 'cs_edu_to_date',
                'cust_name' => '',
                'return' => true,
                'required' => true
            ),
        );
        $html .= $cs_html_fields_frontend->cs_form_text_render($cs_opt_array);

        $cs_opt_array = array('name' => __('Institute', 'jobhunt'),
            'desc' => '',
            'field_params' => array(
                'std' => '',
                'id' => 'cs_edu_institute',
                'before' => 'col-md-12',
                'after' => '</div>',
                'id' => 'edu_institute',
                'classes' => 'cs-form-text cs-input form-control',
                'extra_atr' => 'placeholder="' . __('Institute', 'jobhunt'). '"',
                'cust_id' => 'cs_edu_institute',
                'cust_name' => '',
                'return' => true,
                'required' => true
            ),
        );


        $html .= $cs_html_fields_frontend->cs_form_text_render($cs_opt_array);

        $cs_opt_array = array('name' => __('Description', 'jobhunt'),
            'desc' => '',
            'field_params' => array(
                'std' => '',
                'id' => 'edu_desc',
                'before' => 'col-md-12',
                'after' => '</div>',
                'classes' => 'cs-form-text cs-input form-control',
                'extra_atr' => 'placeholder="' . __('Description', 'jobhunt'). '"',
                'return' => true,
                'cs_editor' => true,
                'required' => true
            ),
        );
        $html .= $cs_html_fields_frontend->cs_form_textarea_render($cs_opt_array);

        $cs_opt_array = array(
            'std' => __("Add Education", "jobhunt"),
            'cust_id' => 'cs_add_edus',
            'cust_name' => '',
            'cust_type' => 'button',
            'extra_atr' => ' onClick="add_education_to_list_fe(\'' . esc_js(admin_url('admin-ajax.php')) . '\', \'' . esc_js(wp_jobhunt::plugin_url()) . '\',\'edu_list\')"',
            'classes' => 'acc-submit cs-section-update cs-bgcolor csborder-color cs-color',
            'return' => true,
        );
        $html .= $cs_form_fields2->cs_form_text_render($cs_opt_array);


        $html .= '<div class="feature-loader"></div>
                     </div>
                </div>
            </div>
	  </div>
	  <a href="javascript:cs_createpop(\'add_education\',\'filter\')" class="button add-more cs-color">
		<i class="icon-plus8"></i>' . __("Add New", "jobhunt") . '</a>';
        echo force_balance_tags($html, true);
    }

}

/**
 * End Function how to show Education in list
 */
/**
 * Start Function  to Remove Resume Options in Form List In Candidate
 */
if (!function_exists('cs_remove_resume_options_fromlist')) :

    function cs_remove_resume_options_fromlist() {
        global $post, $cs_form_fields_frontend, $cs_form_fields2, $current_user;
        $uid = $current_user->ID;
        if (isset($uid) && $uid <> '') {
            $list_type = $_POST['list_type'];
            $list_id = $_POST['list_id'];

            if ($list_type == 'edu_list') {
                $cs_get_edu_list = get_user_meta($uid, 'cs_edu_list_array', true);
                $finded_key = array_search($list_id, $cs_get_edu_list);
                if ($finded_key >= 0) {
                    $cs_get_edu_list = get_user_meta($uid, 'cs_edu_list_array', true);
                    $cs_edu_titles = get_user_meta($uid, 'cs_edu_title_array', true);
                    $cs_edu_from_dates = get_user_meta($uid, 'cs_edu_from_date_array', true);
                    $cs_edu_to_dates = get_user_meta($uid, 'cs_edu_to_date_array', true);
                    $cs_edu_institutes = get_user_meta($uid, 'cs_edu_institute_array', true);
                    $cs_edu_descs = get_user_meta($uid, 'cs_edu_desc_array', true);

                    unset($cs_get_edu_list[$finded_key]);
                    unset($cs_edu_titles[$finded_key]);
                    unset($cs_edu_from_dates[$finded_key]);
                    unset($cs_edu_to_dates[$finded_key]);
                    unset($cs_edu_institutes[$finded_key]);
                    unset($cs_edu_descs[$finded_key]);
                    $cs_get_edu_list = array_values($cs_get_edu_list);
                    $cs_edu_titles = array_values($cs_edu_titles);
                    $cs_edu_from_dates = array_values($cs_edu_from_dates);
                    $cs_edu_to_dates = array_values($cs_edu_to_dates);
                    $cs_edu_institutes = array_values($cs_edu_institutes);
                    $cs_edu_descs = array_values($cs_edu_descs);
                    update_user_meta($uid, 'cs_edu_list_array', $cs_get_edu_list);
                    update_user_meta($uid, 'cs_edu_title_array', $cs_edu_titles);
                    update_user_meta($uid, 'cs_edu_from_date_array', $cs_edu_from_dates);
                    update_user_meta($uid, 'cs_edu_to_date_array', $cs_edu_to_dates);
                    update_user_meta($uid, 'cs_edu_institute_array', $cs_edu_institutes);
                    update_user_meta($uid, 'cs_edu_desc_array', $cs_edu_descs);
                    echo 'success';
                } else {
                    echo 'error';
                }
            }
            if ($list_type == 'experience_list') {
                $cs_get_exp_list = get_user_meta($uid, 'cs_exp_list_array', true);
                $finded_key = array_search($list_id, $cs_get_exp_list);
                if ($finded_key >= 0) {
                    $cs_get_exp_list = get_user_meta($uid, 'cs_exp_list_array', true);
                    $cs_exp_title = get_user_meta($uid, 'cs_exp_title_array', true);
                    $cs_exp_from_dates = get_user_meta($uid, 'cs_exp_from_date_array', true);
                    $cs_exp_to_dates = get_user_meta($uid, 'cs_exp_to_date_array', true);
                    $cs_exp_companies = get_user_meta($uid, 'cs_exp_company_array', true);
                    $cs_exp_descs = get_user_meta($uid, 'cs_exp_desc_array', true);
                    unset($cs_get_exp_list[$finded_key]);
                    unset($cs_exp_title[$finded_key]);
                    unset($cs_exp_from_dates[$finded_key]);
                    unset($cs_exp_to_dates[$finded_key]);
                    unset($cs_exp_companies[$finded_key]);
                    unset($cs_exp_descs[$finded_key]);
                    $cs_get_exp_list = array_values($cs_get_exp_list);
                    $cs_exp_title = array_values($cs_exp_title);
                    $cs_exp_from_dates = array_values($cs_exp_from_dates);
                    $cs_exp_to_dates = array_values($cs_exp_to_dates);
                    $cs_exp_companies = array_values($cs_exp_companies);
                    $cs_exp_descs = array_values($cs_exp_descs);
                    update_user_meta($uid, 'cs_exp_list_array', $cs_get_exp_list);
                    update_user_meta($uid, 'cs_exp_title_array', $cs_exp_title);
                    update_user_meta($uid, 'cs_exp_from_date_array', $cs_exp_from_dates);
                    update_user_meta($uid, 'cs_exp_to_date_array', $cs_exp_to_dates);
                    update_user_meta($uid, 'cs_exp_company_array', $cs_exp_companies);
                    update_user_meta($uid, 'cs_exp_desc_array', $cs_exp_descs);
                    echo 'success';
                } else {
                    echo 'error';
                }
            }
            if ($list_type == 'skill_list') {
                $cs_get_skills_list = get_user_meta($uid, 'cs_skills_list_array', true);
                $finded_key = array_search($list_id, $cs_get_skills_list);
                if ($finded_key >= 0) {
                    $cs_get_skills_list = get_user_meta($uid, 'cs_skills_list_array', true);
                    $cs_skills_title = get_user_meta($uid, 'cs_skill_title_array', true);
                    $cs_skills_from_dates = get_user_meta($uid, 'cs_skill_percentage_array', true);
                    unset($cs_get_skills_list[$finded_key]);
                    unset($cs_skills_title[$finded_key]);
                    unset($cs_skills_from_dates[$finded_key]);
                    $cs_get_skills_list = array_values($cs_get_skills_list);
                    $cs_skills_title = array_values($cs_skills_title);
                    $cs_skills_from_dates = array_values($cs_skills_from_dates);
                    update_user_meta($uid, 'cs_skills_list_array', $cs_get_skills_list);
                    update_user_meta($uid, 'cs_skill_title_array', $cs_skills_title);
                    update_user_meta($uid, 'cs_skill_percentage_array', $cs_skills_from_dates);
                    echo 'success';
                } else {
                    echo 'error';
                }
            }
            if ($list_type == 'portfolio_list') {
                $cs_get_port_list = get_user_meta($uid, 'cs_port_list_array', true);
                $finded_key = array_search($list_id, $cs_get_port_list);
                if ($finded_key >= 0) {
                    $cs_get_port_list = get_user_meta($uid, 'cs_port_list_array', true);
                    $cs_image_titles = get_user_meta($uid, 'cs_image_title_array', true);
                    $cs_image_uploads = get_user_meta($uid, 'cs_image_upload_array', true);
                    unset($cs_get_port_list[$finded_key]);
                    unset($cs_image_titles[$finded_key]);
                    unset($cs_image_uploads[$finded_key]);
                    $cs_get_port_list = array_values($cs_get_port_list);
                    $cs_image_titles = array_values($cs_image_titles);
                    $cs_image_uploads = array_values($cs_image_uploads);
                    update_user_meta($uid, 'cs_port_list_array', $cs_get_port_list);
                    update_user_meta($uid, 'cs_image_title_array', $cs_image_titles);
                    update_user_meta($uid, 'cs_image_upload_array', $cs_image_uploads);
                    echo 'success';
                } else {
                    echo 'error';
                }
            }
            if ($list_type == 'award_list') {

                $cs_get_award_list = get_user_meta($uid, 'cs_award_list_array', true);
                $finded_key = array_search($list_id, $cs_get_award_list);
                if ($finded_key >= 0) {
                    $cs_get_award_list = get_user_meta($uid, 'cs_award_list_array', true);
                    $cs_award_names = get_user_meta($uid, 'cs_award_name_array', true);
                    $cs_award_years = get_user_meta($uid, 'cs_award_year_array', true);
                    $cs_award_descs = get_user_meta($uid, 'cs_award_description_array', true);
                    unset($cs_get_award_list[$finded_key]);
                    unset($cs_award_names[$finded_key]);
                    unset($cs_award_years[$finded_key]);
                    unset($cs_award_descs[$finded_key]);
                    $cs_get_award_list = array_values($cs_get_award_list);
                    $cs_award_names = array_values($cs_award_names);
                    $cs_award_years = array_values($cs_award_years);
                    $cs_award_descs = array_values($cs_award_descs);
                    update_user_meta($uid, 'cs_award_list_array', $cs_get_award_list);
                    update_user_meta($uid, 'cs_award_name_array', $cs_award_names);
                    update_user_meta($uid, 'cs_award_year_array', $cs_award_years);
                    update_user_meta($uid, 'cs_award_description_array', $cs_award_descs);
                    echo 'success';
                } else {
                    echo 'error';
                }
            }
        } else {
            _e('You have to login first', 'jobhunt');
        }
        die();
    }

endif;
add_action("wp_ajax_cs_remove_resume_options_fromlist", "cs_remove_resume_options_fromlist");
add_action("wp_ajax_nopriv_cs_remove_resume_options_fromlist", "cs_remove_resume_options_fromlist");
/**
 * End Function  to Remove Resume Options in Form List In Candidate
 */
/**
 * Start Function  to Add Eduction in the education list
 */
if (!function_exists('cs_add_education_to_list_fe')) {

    function cs_add_education_to_list_fe($cs_atts) {
        global $post, $cs_education_counter, $cs_form_fields_frontend, $cs_form_fields2;
        $cs_defaults = array(
            'counter_extra_feature' => '',
            'extra_feature_id' => '',
            'cs_edu_title' => '',
            'cs_edu_from_date' => '',
            'cs_edu_to_date' => '',
            'cs_edu_institute' => '',
            'cs_edu_desc' => '',
            'cs_get_edu_list' => '',
        );
        extract(shortcode_atts($cs_defaults, $cs_atts));
        foreach ($_POST as $keys => $values) {
            $$keys = $values;
        }
        if (isset($_POST['cs_edu_title']) && $_POST['cs_edu_title'] <> '')
            $cs_edu_title = $_POST['cs_edu_title'];
        if (isset($_POST['cs_edu_from_date']) && $_POST['cs_edu_from_date'] <> '')
            $cs_edu_from_date = $_POST['cs_edu_from_date'];
        if (isset($_POST['cs_edu_to_date']) && $_POST['cs_edu_to_date'] <> '')
            $cs_edu_to_date = $_POST['cs_edu_to_date'];
        if (isset($_POST['cs_edu_institute']) && $_POST['cs_edu_institute'] <> '')
            $cs_edu_institute = $_POST['cs_edu_institute'];
        if (isset($_POST['cs_edu_desc']) && $_POST['cs_edu_desc'] <> '')
            $cs_edu_desc = $_POST['cs_edu_desc'];
        if (isset($_POST['cs_get_edu_list']) && $_POST['cs_get_edu_list'] <> '')
            $cs_get_edu_list = $_POST['cs_get_edu_list'];
        if ($extra_feature_id == '' && $counter_extra_feature == '') {
            $counter_extra_feature = $extra_feature_id = time();
        }
        $html = '<li class="parentdelete parentdeleterow-' . esc_attr($cs_get_edu_list) . '" id="edit_track' . esc_attr($extra_feature_id) . '">
                <div class="top-section">
                        <div class="title" id="subject-title' . esc_attr($extra_feature_id) . '">
                                <span>' . esc_attr($cs_edu_title) . '</span>
                        </div>
                        <div class="date"><span>' . date('Y', strtotime($cs_edu_from_date)) . ' - ' . date('Y', strtotime($cs_edu_to_date)) . '</span></div>
                        <div class="location"><span>' . $cs_edu_institute . '</span></div>
                        <div class="option">
                                <a data-toggle="tooltip" data-placement="top" title="' . __("Edit", "jobhunt") . '" href="javascript:cs_createpop(\'edit_track_form' . esc_js($extra_feature_id) . '\',\'filter\')" class="actions edit">
                                <i class="icon-gear"></i></a>
                                <a data-toggle="tooltip" data-placement="top" title="' . __("Remove", "jobhunt") . '" href="javascript:void(0)" onclick="javascript:cs_remove_resume_options_fromlist(\'' . esc_js(admin_url('admin-ajax.php')) . '\',\'edu_list\',\'' . esc_js($cs_get_edu_list) . '\')" class="delete-it btndeleteit actions delete-' . esc_attr($cs_get_edu_list) . '"><i class="icon-trash-o"></i>
                        </div>
                </div>
                <div class="btm-section">
                        <div id="edit_track_form' . esc_attr($extra_feature_id) . '" style="display: none;" class="table-form-elem input-info">
                            <div class="row">';

        $cs_opt_array = array(
            'std' => $extra_feature_id,
            'cust_id' => 'cs_edu_list_array',
            'cust_name' => 'cs_edu_list_array[]',
            'cust_type' => 'hidden',
            'return' => true,
        );
        $html .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

        $html .='
			<div class="cs-heading-area">
				<span onclick="javascript:cs_remove_overlay(\'edit_track_form' . esc_js($extra_feature_id) . '\',\'append\')" class="cs-btnclose">
				<i class="icon-times"></i></span>
				<div class="clear"></div>
			</div>';

        $html .='<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';

        $cs_opt_array = array(
            'std' => $cs_edu_title,
            'cust_id' => 'cs_edu_title' . esc_js($extra_feature_id),
            'cust_name' => 'cs_edu_title_array[]',
            'extra_atr' => ' placeholder="' . __('Title*', 'jobhunt'). '" required="required"',
            'classes' => 'cs-form-text cs-input form-control',
            'return' => true,
        );
        $html .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

        $html .= '
            </div>';
        $html .= '<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><script>
                jQuery(function(){
                        jQuery("#cs_edu_from_date' . esc_attr($extra_feature_id) . '").datetimepicker({
                            format:"d-m-Y",
                            timepicker:false
                        });
                });
          </script>';

        $cs_opt_array = array(
            'std' => $cs_edu_from_date,
            'cust_id' => 'cs_edu_from_date' . esc_js($extra_feature_id),
            'cust_name' => 'cs_edu_from_date_array[]',
            'extra_atr' => ' placeholder="' . __('From Date*', 'jobhunt'). '" required="required"',
            'classes' => 'cs-form-text cs-input form-control',
            'return' => true,
        );
        $html .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

        $html .= '</div><div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><script>
                jQuery(function(){
                    jQuery("#cs_edu_to_date' . esc_attr($extra_feature_id) . '").datetimepicker({
                        format:"d-m-Y",
                        timepicker:false
                    });
                });
            </script>';

        $cs_opt_array = array(
            'std' => $cs_edu_to_date,
            'cust_id' => 'cs_edu_to_date' . esc_js($extra_feature_id),
            'cust_name' => 'cs_edu_to_date_array[]',
            'extra_atr' => ' placeholder="' . __('To Date*', 'jobhunt'). '" required="required"',
            'classes' => 'cs-form-text cs-input form-control',
            'return' => true,
        );
        $html .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

        $html .= '
			</div>';
        $html .='<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';

        $cs_opt_array = array(
            'std' => $cs_edu_institute,
            'cust_id' => 'cs_edu_institute' . esc_js($extra_feature_id),
            'cust_name' => 'cs_edu_institute_array[]',
            'extra_atr' => ' placeholder="' . __('Institute*', 'jobhunt'). '" required="required"',
            'classes' => 'cs-form-text cs-input form-control',
            'return' => true,
        );
        $html .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

        $html .= '
			</div>';

        $html .='<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
        $html .= $cs_form_fields2->cs_form_textarea_render(
                array('name' => __('Description', 'jobhunt'),
                    'id' => 'edu_desc',
                    'std' => $cs_edu_desc,
                    'description' => '',
                    'return' => true,
                    'array' => true,
                    'cs_editor' => true,
                    'force_std' => true,
                    'hint' => ''
                )
        );
        $html .= '</div>';

        $html .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <button type="button" value="update" name="button_action" class="acc-submit cs-section-update cs-bgcolor csborder-color cs-color" 
                     onclick="javascript:edit_education_to_list_fe(\'' . esc_js(admin_url('admin-ajax.php')) . '\', \'' . esc_js(wp_jobhunt::plugin_url()) . '\',\'edu_list\', \'' . $extra_feature_id . '\')" >' . __('Update', 'jobhunt') . '</button>
                        <span class="form-update-loader" id="response-message' . esc_attr($extra_feature_id) . '"></span>
                        </div>
                        </div>
                    </div>
		</div>
			</li>';
        if (isset($_POST['cs_edu_title']) && isset($_POST['cs_edu_from_date'])) {
            echo force_balance_tags($html);
        } else {
            return $html;
        }
        if (isset($_POST['cs_edu_title']) && isset($_POST['cs_edu_from_date']))
            die();
    }

    add_action('wp_ajax_cs_add_education_to_list_fe', 'cs_add_education_to_list_fe');
}
/**
 * End Function  to Add Eduction in the education list
 */
/**
 * Start Function  how to show experience in the list
 */
if (!function_exists('cs_experience_list_fe')) {

    function cs_experience_list_fe() {
        global $post, $current_user, $cs_form_fields_frontend, $cs_award_counter, $cs_form_fields2, $cs_html_fields_frontend;

        $uid = $current_user->ID;
        $cs_get_exp_list = get_user_meta($uid, 'cs_exp_list_array', true);
        $cs_exp_titles = get_user_meta($uid, 'cs_exp_title_array', true);
        $cs_exp_to_presents = get_user_meta($uid, 'cs_exp_to_present_array', true);
        $cs_exp_from_dates = get_user_meta($uid, 'cs_exp_from_date_array', true);
        $cs_exp_to_dates = get_user_meta($uid, 'cs_exp_to_date_array', true);
        $cs_exp_companys = get_user_meta($uid, 'cs_exp_company_array', true);
        $cs_exp_descs = get_user_meta($uid, 'cs_exp_desc_array', true);

        $html = '
	
	<div class="cs-list-table">
		<ul class="top-heading-list">
                    <li><span>' . __("Skills @ Company", "jobhunt") . '</span></li>
                    <li><span>' . __("Dates", "jobhunt") . '</span></li>
		</ul>
		<ul id="total_experience_list" class="accordion-list">';
        if (isset($cs_get_exp_list) && is_array($cs_get_exp_list) && count($cs_get_exp_list) > 0) {
            $cs_award_counter = 0;
            foreach ($cs_get_exp_list as $award_list) {
                if (isset($award_list) && $award_list <> '') {
                    $counter_extra_feature = $extra_feature_id = $award_list;
                    $cs_exp_title = isset($cs_exp_titles[$cs_award_counter]) ? $cs_exp_titles[$cs_award_counter] : '';
                    $cs_exp_to_present = isset($cs_exp_to_presents[$cs_award_counter]) ? $cs_exp_to_presents[$cs_award_counter] : '';
                    $cs_exp_from_date = isset($cs_exp_from_dates[$cs_award_counter]) ? $cs_exp_from_dates[$cs_award_counter] : '';
                    $cs_exp_to_date = isset($cs_exp_to_dates[$cs_award_counter]) ? $cs_exp_to_dates[$cs_award_counter] : '';
                    $cs_exp_company = isset($cs_exp_companys[$cs_award_counter]) ? $cs_exp_companys[$cs_award_counter] : '';
                    $cs_exp_desc = isset($cs_exp_descs[$cs_award_counter]) ? $cs_exp_descs[$cs_award_counter] : '';
                    $ca_awards_array = array(
                        'counter_extra_feature' => $counter_extra_feature,
                        'extra_feature_id' => $extra_feature_id,
                        'cs_exp_title' => $cs_exp_title,
                        'cs_exp_to_present' => $cs_exp_to_present,
                        'cs_exp_from_date' => $cs_exp_from_date,
                        'cs_exp_to_date' => $cs_exp_to_date,
                        'cs_exp_company' => $cs_exp_company,
                        'cs_exp_desc' => $cs_exp_desc,
                        'cs_get_exp_list' => $award_list,
                    );
                    $html .= cs_add_experience_to_list_fe($ca_awards_array);
                }
                $cs_award_counter++;
            }
        } else {
            $html .= '<li class="cs-no-record">' . cs_info_messages_listing(__("There is no record in experience list", 'jobhunt')) . '</li>';
        }
        $html .= '</ul>
	  </div>
	  <div id="add_experience" style="display: none;">
		<div class="btm-section">
                    <div class="input-info">
                        <div class="row">
                            <div class="cs-heading-area">
                                <span class="cs-btnclose" onClick="javascript:cs_remove_overlay(\'add_experience\',\'append\')"> <i class="icon-times"></i></span> 	
                            </div>';

        $cs_opt_array = array(
            'name' => __('Title', 'jobhunt'),
            'desc' => '',
            'field_params' => array(
                'std' => '',
                'id' => 'cs_exp_title',
                'before' => 'col-md-12',
                'after' => '</div>',
                'classes' => 'cs-form-text cs-input form-control',
                'extra_atr' => 'placeholder="' . __('Title*', 'jobhunt'). '"',
                'cust_id' => 'cs_exp_title',
                'cust_name' => '',
                'return' => true,
                'required' => true
            ),
        );

        $html .= $cs_html_fields_frontend->cs_form_text_render($cs_opt_array);

        $html .= '<script>
				jQuery(function(){
                        jQuery("#cs_exp_from_date").datetimepicker({
                            format:"d-m-Y",
                            timepicker:false
                        });
                });
				jQuery(function(){
                        jQuery("#cs_exp_to_date").datetimepicker({
                            format:"d-m-Y",
                            timepicker:false
                        });
                });
				</script>';


        $cs_opt_array = array('name' => __('From Date*', 'jobhunt'),
            'desc' => '',
            'field_params' => array(
                'std' => '',
                'id' => 'cs_exp_from_date',
                'before' => 'col-md-5',
                'after' => '</div>',
                'id' => 'exp_from_date',
                'classes' => 'cs-form-text cs-input form-control',
                'extra_atr' => 'placeholder="' . __('From Date*', 'jobhunt'). '"',
                'cust_id' => 'cs_exp_from_date',
                'cust_name' => '',
                'return' => true,
                'required' => true
            ),
        );


        $html .= $cs_html_fields_frontend->cs_form_text_render($cs_opt_array);

        $cs_opt_array = array('name' => __('To Date*', 'jobhunt'),
            'desc' => '',
            'field_params' => array(
                'std' => '',
                'id' => 'cs_exp_to_date',
                'before' => 'col-md-5',
                'after' => '</div>',
                'id' => 'exp_to_date',
                'classes' => 'cs-form-text cs-input form-control',
                'extra_atr' => 'placeholder="' . __('To Date*', 'jobhunt'). '"',
                'cust_id' => 'cs_exp_to_date',
                'cust_name' => '',
                'return' => true,
                'required' => true
            ),
        );

        $html .= $cs_html_fields_frontend->cs_form_text_render($cs_opt_array);

        $html .= '<div class="col-md-2">';
        $html .= '<label>' . __('Present', 'jobhunt') . '</label>';
        $cs_opt_array = array('name' => __('Present', 'jobhunt'),
            'desc' => '',
            'field_params' => array(
                'std' => '',
                'id' => 'exp_to_present_till',
                'classes' => 'form-control',
                'cust_id' => 'cs_exp_to_present_till',
                'cust_name' => '',
                'cust_type' => 'checkbox',
                'return' => true,
            ),
        );
        $html .= $cs_html_fields_frontend->cs_form_text_render($cs_opt_array);
        $html .= '</div>';

        $cs_opt_array = array('name' => __('Company', 'jobhunt'),
            'desc' => '',
            'field_params' => array(
                'std' => '',
                'id' => 'cs_exp_company',
                'before' => 'col-md-12',
                'after' => '</div>',
                'id' => 'edu_institute',
                'classes' => 'cs-form-text cs-input form-control',
                'extra_atr' => 'placeholder="' . __('Company', 'jobhunt'). '"',
                'cust_id' => 'cs_exp_company',
                'cust_name' => '',
                'return' => true,
                'required' => true
            ),
        );
        $html .= $cs_html_fields_frontend->cs_form_text_render($cs_opt_array);
        $cs_opt_array = array('name' => __('Description', 'jobhunt'),
            'desc' => '',
            'field_params' => array(
                'std' => '',
                'id' => 'cs_exp_desc',
                'before' => 'col-md-12',
                'after' => '</div>',
                'id' => 'exp_desc',
                'classes' => 'cs-form-text cs-input form-control',
                'cs_editor' => true,
                'extra_atr' => 'placeholder="' . __('Description', 'jobhunt'). '"',
                'cust_id' => 'cs_exp_desc',
                'cust_name' => 'cs_exp_desc',
                'return' => true,
                'required' => true
            ),
        );

        $html .= $cs_html_fields_frontend->cs_form_textarea_render($cs_opt_array);

        $cs_opt_array = array(
            'name' => '&nbsp;',
            'desc' => '',
            'hint_text' => '',
            'field_params' => array(
                'std' => __('Add Experience', 'jobhunt'),
                'cust_id' => 'cs_add_epsx',
                'cust_name' => '',
                'classes' => 'acc-submit cs-section-update cs-bgcolor csborder-color cs-color',
                'cust_type' => 'button',
                'extra_atr' => ' onClick="add_experience_to_list_fe(\'' . esc_js(admin_url('admin-ajax.php')) . '\', \'' . esc_js(wp_jobhunt::plugin_url()) . '\',\'experience_list\')"',
                'return' => true,
            ),
        );
        $html .= $cs_html_fields_frontend->cs_form_text_render($cs_opt_array);

        $html .= '
		<div class="feature-loader"></div>
	  	</div></div></div></div><a href="javascript:cs_createpop(\'add_experience\',\'filter\')" class="button add-more cs-color">
	  	<i class="icon-plus8"></i>' . __("Add New", "jobhunt") . '</a>';

        echo force_balance_tags($html, true);
    }

}
/**
 * End Function  how to show experience in the list
 */
/**
 * Start Function  how to Add experience in the list
 */
if (!function_exists('cs_add_experience_to_list_fe')) {

    function cs_add_experience_to_list_fe($cs_atts) {
        global $post, $cs_form_fields_frontend, $cs_award_counter, $cs_form_fields2;
        $cs_defaults = array(
            'counter_extra_feature' => '',
            'extra_feature_id' => '',
            'cs_exp_title' => '',
            'cs_exp_to_present' => '',
            'cs_exp_from_date' => '',
            'cs_exp_to_date' => '',
            'cs_exp_company' => '',
            'cs_exp_desc' => '',
            'cs_get_exp_list' => '',
        );
        extract(shortcode_atts($cs_defaults, $cs_atts));
        foreach ($_POST as $keys => $values) {
            $$keys = $values;
        }
        if (isset($_POST['cs_exp_title']) && $_POST['cs_exp_title'] <> '')
            $cs_exp_title = $_POST['cs_exp_title'];
        if (isset($_POST['cs_exp_to_present_till']) && $_POST['cs_exp_to_present_till'] <> '')
            $cs_exp_to_present = $_POST['cs_exp_to_present_till'];
        if (isset($_POST['cs_exp_from_date']) && $_POST['cs_exp_from_date'] <> '')
            $cs_exp_from_date = $_POST['cs_exp_from_date'];
        if (isset($_POST['cs_exp_to_date']) && $_POST['cs_exp_to_date'] <> '')
            $cs_exp_to_date = $_POST['cs_exp_to_date'];
        if (isset($_POST['cs_exp_company']) && $_POST['cs_exp_company'] <> '')
            $cs_exp_company = $_POST['cs_exp_company'];
        if (isset($_POST['cs_exp_desc']) && $_POST['cs_exp_desc'] <> '')
            $cs_exp_desc = $_POST['cs_exp_desc'];
        if (isset($_POST['cs_get_exp_list']) && $_POST['cs_get_exp_list'] <> '')
            $cs_get_exp_list = $_POST['cs_get_exp_list'];
        if ($extra_feature_id == '' && $counter_extra_feature == '') {
            $counter_extra_feature = $extra_feature_id = time();
        }

        $html = '<li class="parentdelete parentdeleterow-' . esc_attr($cs_get_exp_list) . ' id="edit_track' . esc_attr($extra_feature_id) . '">
                <div class="top-section">
                    <div class="title" id="subject-title' . esc_attr($extra_feature_id) . '">
                            <span>' . $cs_exp_title . ' @ ' . $cs_exp_company . '</span>
                    </div>
                    <div class="date"><span>' . date('Y', strtotime($cs_exp_from_date)) . '-' . date('Y', strtotime($cs_exp_to_date)) . '</span></div>
                    <div class="option">
                        <a data-toggle="tooltip" data-placement="top" title="' . __("Edit", "jobhunt") . '" href="javascript:cs_createpop(\'edit_track_form' . esc_js($extra_feature_id) . '\',\'filter\')" class="actions edit">
                            <i class="icon-gear"></i>
                        </a>
                        <a data-toggle="tooltip" data-placement="top" title="' . __("Remove", "jobhunt") . '" href="javascript:void(0)" onclick="javascript:cs_remove_resume_options_fromlist(\'' . esc_js(admin_url('admin-ajax.php')) . '\',\'experience_list\',\'' . esc_js($cs_get_exp_list) . '\')" class="delete-it btndeleteit actions delete-' . esc_attr($cs_get_exp_list) . '"><i class="icon-trash-o"></i>
                        </a>
                    </div>
                </div>			
                <div class="btm-section">
                        <div id="edit_track_form' . esc_attr($extra_feature_id) . '" style="display: none;" class="input-info">
                            <div class="row">';

        $cs_opt_array = array(
            'std' => $extra_feature_id,
            'cust_id' => 'cs_exp_list_array',
            'cust_name' => 'cs_exp_list_array[]',
            'cust_type' => 'hidden',
            'return' => true,
        );
        $html .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

        $html .= '	
                      	<div class="cs-heading-area">
                            <span onclick="javascript:cs_remove_overlay(\'edit_track_form' . esc_js($extra_feature_id) . '\',\'append\')" class="cs-btnclose">
                                <i class="icon-times"></i>
                            </span>
                        <div class="clear"></div>
                      	</div>';

        $html .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';

        $cs_opt_array = array(
            'std' => $cs_exp_title,
            'cust_id' => 'cs_exp_title' . $extra_feature_id,
            'cust_name' => 'cs_exp_title_array[]',
            'extra_atr' => ' required="required"',
            'classes' => 'cs-form-text cs-input form-control',
            'return' => true,
        );
        $html .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

        $html .= '
			</div>';
        $html .= '<div class="col-md-5"><script>
                jQuery(function(){
                        jQuery("#cs_exp_from_date' . esc_attr($extra_feature_id) . '").datetimepicker({
                            format:"d-m-Y",
                            timepicker:false
                        });
                });
          </script>';

        $cs_opt_array = array(
            'std' => $cs_exp_from_date,
            'cust_id' => 'cs_exp_from_date' . $extra_feature_id,
            'cust_name' => 'cs_exp_from_date_array[]',
            'extra_atr' => ' required="required" placeholder="' . __('From Date*', 'jobhunt'). '"',
            'classes' => 'cs-form-text cs-input form-control',
            'return' => true,
        );
        $html .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

        $html .= '
		</div>';
        $html .= '<div class="col-md-5"><script>
                jQuery(function(){
                        jQuery("#cs_exp_to_date' . esc_attr($extra_feature_id) . '").datetimepicker({
                            format:"d-m-Y",
                            timepicker:false
                        });
                });
          </script>';

        $cs_opt_array = array(
            'std' => $cs_exp_to_date,
            'cust_id' => 'cs_exp_to_date' . $extra_feature_id,
            'cust_name' => 'cs_exp_to_date_array[]',
            'extra_atr' => ' required="required" placeholder="' . __('To Date*', 'jobhunt'). '"',
            'classes' => 'cs-form-text cs-input form-control',
            'return' => true,
        );
        $html .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

        $html .= '
		</div>';

        $html .= '<div class="col-md-2">';
        $html .= '<label>' . __('Present', 'jobhunt') . '</label>';

        $cs_opt_array = array(
            'std' => $cs_exp_to_present,
            'cust_id' => 'cs_exp_to_present' . $extra_feature_id,
            'cust_name' => '',
            'classes' => 'form-control',
            'simple' => true,
            'return' => true,
        );
        $html .= $cs_form_fields2->cs_form_checkbox_render($cs_opt_array);
        $cs_opt_array = array(
            'std' => $cs_exp_to_present,
            'cust_id' => 'cs_exp_to_present_hid' . $extra_feature_id,
            'cust_name' => 'cs_exp_to_present_array[]',
            'extra_atr' => ' style="display:none; position:absolute;"',
            'return' => true,
        );
        $html .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

        $html .= '
		</div>';

        $html .= '
        <script>
		jQuery(document).on("click", "#cs_exp_to_present' . absint($extra_feature_id) . '", function() {
            if( jQuery(this).is(":checked") ) {
				jQuery("#cs_exp_to_present_hid' . absint($extra_feature_id) . '").attr("value", "on");
			} else {
				jQuery("#cs_exp_to_present_hid' . absint($extra_feature_id) . '").attr("value", "");
			}
        });
		</script>';

        $html .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';

        $cs_opt_array = array(
            'std' => $cs_exp_company,
            'cust_id' => 'cs_exp_company' . $extra_feature_id,
            'cust_name' => 'cs_exp_company_array[]',
            'extra_atr' => ' required="required" placeholder="' . __('Company*', 'jobhunt'). '"',
            'classes' => 'cs-form-text cs-input form-control',
            'return' => true,
        );
        $html .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

        $html .= '
		</div>';

        $html .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
        $html .= $cs_form_fields2->cs_form_textarea_render(
                array('name' => __('Description', 'jobhunt'),
                    'id' => 'exp_desc',
                    'std' => $cs_exp_desc,
                    'description' => '',
                    'return' => true,
                    'array' => true,
                    'force_std' => true,
                    'cs_editor' => true,
                    'hint' => ''
                )
        );
        $html .= '
		</div>';

        $html .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <button value="update" name="button_action" class="acc-submit cs-section-update cs-bgcolor csborder-color cs-color" 
                    onclick="javascript:edit_experience_to_list_fe(\'' . esc_js(admin_url('admin-ajax.php')) . '\', \'' . esc_js(wp_jobhunt::plugin_url()) . '\',\'experience_list\', \'' . $extra_feature_id . '\')" >' . __('Update', 'jobhunt') . '</button>
                        <span class="form-update-loader" id="response-message' . esc_attr($extra_feature_id) . '"></span>
                    </div>
                  </div></div></div>
          </li>';
        if (isset($_POST['cs_exp_title']) && isset($_POST['cs_exp_from_date'])) {
            echo force_balance_tags($html);
        } else {
            return $html;
        }
        if (isset($_POST['cs_exp_title']) && isset($_POST['cs_exp_from_date']))
            die();
    }

    add_action('wp_ajax_cs_add_experience_to_list_fe', 'cs_add_experience_to_list_fe');
}
/**
 * End Function  how to Add experience in the list
 */
/**
 * Start Function  how to Show skills in the list
 */
if (!function_exists('cs_skills_list_fe')) {

    function cs_skills_list_fe() {
        global $post, $current_user, $cs_form_fields_frontend, $skill_list_counter, $cs_form_fields2;

        $uid = $current_user->ID;
        $cs_get_skill_list = get_user_meta($uid, 'cs_skills_list_array', true);
        $cs_skill_titles = get_user_meta($uid, 'cs_skill_title_array', true);
        $cs_skill_percentages = get_user_meta($uid, 'cs_skill_percentage_array', true);
        $html = '
	<script>
            /*jQuery(document).ready(function($) {
                $("#total_experience_list").sortable({
                        cancel : \'td div.table-form-elem\'
                });
            });*/
	</script>
	<div class="cs-list-table">
		<ul class="top-heading-list">
                    <li><span>' . __("Skill or Expertise", "jobhunt") . '</span></li>
                    <li><span>' . __("Level", "jobhunt") . '</span></li>
		</ul>
		<ul id="total_skills_list" class="accordion-list">';
        if (isset($cs_get_skill_list) && is_array($cs_get_skill_list) && count($cs_get_skill_list) > 0) {
            $skill_list_counter = 0;
            foreach ($cs_get_skill_list as $skill_list) {
                if (isset($skill_list) && $skill_list <> '') {
                    $counter_extra_feature = $extra_feature_id = $skill_list;
                    $cs_skill_title = isset($cs_skill_titles[$skill_list_counter]) ? $cs_skill_titles[$skill_list_counter] : '';
                    $cs_skill_percentage = isset($cs_skill_percentages[$skill_list_counter]) ? $cs_skill_percentages[$skill_list_counter] : '';
                    $cs_skills_array = array(
                        'counter_extra_feature' => $counter_extra_feature,
                        'extra_feature_id' => $extra_feature_id,
                        'cs_skill_title' => $cs_skill_title,
                        'cs_skill_percentage' => $cs_skill_percentage,
                        'cs_get_skill_list' => $skill_list,
                    );
                    $html .= cs_add_skills_to_list_fe($cs_skills_array);
                }
                $skill_list_counter++;
            }
        } else {
            $html .= '<li class="cs-no-record">' . cs_info_messages_listing(__("There is no record in skills list", 'jobhunt')) . '</li>';
        }
        $html .= '</ul>
                </div>
                    <div id="add_skills" style="display: none;">
                        <div class="btm-section">
                            <div class="input-info">
				<div class="row">
					<div class="cs-heading-area">
						<span class="cs-btnclose" onClick="javascript:cs_remove_overlay(\'add_skills\',\'append\')"> <i class="icon-times"></i></span> 	
					</div>';

        $html .= '<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">';
        $html .= '<label>' . __('Skill Heading', 'jobhunt') . '</label>';
        $html .=$cs_form_fields2->cs_form_text_render(
                array('name' => __('Skill Title*', 'jobhunt'),
                    'id' => 'skill_title',
                    'cust_id' => 'cs_skill_title',
                    'cust_name' => '',
                    'std' => '',
                    'description' => '',
                    'return' => true,
                    'hint' => '',
                    "required" => true
                )
        );
        $html .= '</div>';


        $html .= '<div class="col-md-3">';
        $html .= '<label>' . __('Percentage', 'jobhunt') . '</label>';
        $cs_opt_array = array(
            'std' => '',
            'cust_id' => 'cs_skill_percentage',
            'cust_name' => 'cs_skill_percentage',
            'extra_atr' => ' required="required" placeholder="' . __('Skill Percentage*', 'jobhunt') . '" onkeypress="return isNumberKey(event)"',
            'classes' => 'cs-form-text cs-input form-control',
            'return' => true,
        );
        $html .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

        $html .= '

	</div>';

        $html .= '<div class="col-md-3">';

        $cs_opt_array = array(
            'std' => __("Add Skill", "jobhunt"),
            'cust_id' => 'cs_add_skill',
            'cust_name' => '',
            'cust_type' => 'button',
            'extra_atr' => ' onClick="add_skills_to_list_fe(\'' . esc_js(admin_url('admin-ajax.php')) . '\', \'' . esc_js(wp_jobhunt::plugin_url()) . '\',\'skill_list\')"',
            'classes' => 'acc-submit cs-section-update cs-bgcolor csborder-color cs-color',
            'return' => true,
        );
        $html .= $cs_form_fields2->cs_form_text_render($cs_opt_array);
        $html .= '
                <div class="feature-loader"></div>
            </div>
            </div></div></div></div>
                <a href="javascript:cs_createpop(\'add_skills\',\'filter\')" class="button add-more cs-color">
                <i class="icon-plus8"></i>' . __("Add New", "jobhunt") . '</a>';
        echo force_balance_tags($html, true);
    }

}
/**
 * End Function  how to Show skills in the list
 */
/**
 * Start Function  how to Add skills in the list
 */
if (!function_exists('cs_add_skills_to_list_fe')) {

    function cs_add_skills_to_list_fe($cs_atts) {
        global $post, $cs_form_fields_frontend, $skill_list_counter, $cs_form_fields2, $cs_html_fields;
        $cs_defaults = array(
            'counter_extra_feature' => '',
            'extra_feature_id' => '',
            'cs_skill_title' => '',
            'cs_skill_percentage' => '',
            'cs_get_skill_list' => '',
        );
        extract(shortcode_atts($cs_defaults, $cs_atts));
        foreach ($_POST as $keys => $values) {
            $$keys = $values;
        }
        if (isset($_POST['cs_skill_title']) && $_POST['cs_skill_title'] <> '')
            $cs_skill_title = $_POST['cs_skill_title'];
        if (isset($_POST['cs_skill_percentage']) && $_POST['cs_skill_percentage'] <> '')
            $cs_skill_percentage = $_POST['cs_skill_percentage'];
        if (isset($_POST['cs_get_skill_list']) && $_POST['cs_get_skill_list'] <> '')
            $cs_get_skill_list = $_POST['cs_get_skill_list'];

        if ($extra_feature_id == '' && $counter_extra_feature == '') {
            $counter_extra_feature = $extra_feature_id = time();
        }
        $html = '<li class="parentdelete parentdeleterow-' . esc_attr($cs_get_skill_list) . '" id="edit_track' . esc_attr($extra_feature_id) . '">
                <div class="top-section">
                        <div class="title" id="subject-title' . esc_attr($extra_feature_id) . '">
                            <span><i class="skills-icon"></i>' . $cs_skill_title . '</span>
                        </div>
                        <div class="date"><span>' . $cs_skill_percentage . '</span></div>
                        <div class="option">
                            <a data-toggle="tooltip" data-placement="top" title="' . __("Edit", "jobhunt") . '" href="javascript:cs_createpop(\'edit_track_form' . esc_js($extra_feature_id) . '\',\'filter\')" class="actions edit">
                                <i class="icon-gear"></i>
                            </a>
                            <a data-toggle="tooltip" data-placement="top" title="' . __("Remove", "jobhunt") . '" href="javascript:void(0)" onclick="javascript:cs_remove_resume_options_fromlist(\'' . esc_js(admin_url('admin-ajax.php')) . '\',\'skill_list\',\'' . esc_js($cs_get_skill_list) . '\')"  class="delete-it btndeleteit actions delete-' . esc_attr($cs_get_skill_list) . '">
                                <i class="icon-trash-o"></i>
                            </a>
                        </div>
                </div>			
                <div class="btm-section">
                            <div id="edit_track_form' . esc_attr($extra_feature_id) . '" style="display: none;" class="input-info">
                                <div class="row">';

        $cs_opt_array = array(
            'std' => $extra_feature_id,
            'cust_id' => 'cs_skills_list_array',
            'cust_name' => 'cs_skills_list_array[]',
            'cust_type' => 'hidden',
            'return' => true,
        );
        $html .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

        $html .= '
                      	<div class="cs-heading-area">
                            <span onclick="javascript:cs_remove_overlay(\'edit_track_form' . esc_js($extra_feature_id) . '\',\'append\')" class="cs-btnclose">
                                <i class="icon-times"></i>
                            </span>
                        <div class="clear"></div>
                      	</div>';

        $html .= '<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">';

        $html .= '<label>' . __("Skill Heading", "jobhunt") . '</label>';
        $cs_opt_array = array(
            'std' => $cs_skill_title,
            'cust_id' => 'cs_skill_title' . $extra_feature_id,
            'cust_name' => 'cs_skill_title_array[]',
            'extra_atr' => ' placeholder="' . __('Skill Title*', 'jobhunt') . '"',
            'classes' => 'cs-form-text cs-input form-control',
            'return' => true,
        );
        $html .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

        $html .= '
	</div>';

        $html .='<div class="col-md-3">';
        $html .= '<label>' . __("Percentage", "jobhunt") . '</label>';
        $cs_opt_array = array(
            'std' => $cs_skill_percentage,
            'cust_id' => 'cs_skill_percentage' . $extra_feature_id,
            'cust_name' => 'cs_skill_percentage_array[]',
            'extra_atr' => ' placeholder="' . __('Skill Percentage', 'jobhunt') . '" onkeypress="return isNumberKey(event)"',
            'classes' => 'cs-form-text cs-input form-control',
            'return' => true,
        );
        $html .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

        $html .= '
	</div>';

        $html .= '<div class="col-md-3">
                <button type="button" value="update" name="button_action" class="acc-submit cs-section-update cs-bgcolor csborder-color cs-color" 
                onclick="javascript:edit_skills_to_list_fe(\'' . esc_js(admin_url('admin-ajax.php')) . '\', \'' . esc_js(wp_jobhunt::plugin_url()) . '\',\'skill_list\', \'' . $extra_feature_id . '\')" >' . __('Update', 'jobhunt') . '</button>
                <span class="form-update-loader" id="response-message' . esc_attr($extra_feature_id) . '"></span>   
            </div>
                </div></div></div>
            </li>';
        if (isset($_POST['cs_skill_title']) && isset($_POST['cs_skill_percentage'])) {
            echo force_balance_tags($html);
        } else {
            return $html;
        }
        if (isset($_POST['cs_skill_title']) && isset($_POST['cs_skill_title']))
            die();
    }

    add_action('wp_ajax_cs_add_skills_to_list_fe', 'cs_add_skills_to_list_fe');
}

/**
 * End Function  how to Add skills in the list
 */
/**
 * Start Function  how to Show awards in the list
 */
if (!function_exists('cs_award_list_fe')) {

    function cs_award_list_fe() {
        global $post, $current_user, $cs_form_fields_frontend, $cs_html_fields_frontend, $cs_form_fields2;

        $uid = $current_user->ID;
        $cs_get_award_list = get_user_meta($uid, 'cs_award_list_array', true);
        $cs_award_names = get_user_meta($uid, 'cs_award_name_array', true);
        $cs_award_years = get_user_meta($uid, 'cs_award_year_array', true);
        $cs_award_descs = get_user_meta($uid, 'cs_award_description_array', true);
        $html = '
	<script>
            /*jQuery(document).ready(function($) {
                $("#total_experience_list").sortable({
                        cancel : \'td div.table-form-elem\'
                });
            });*/
	</script>
	<div class="cs-list-table">
		<ul class="top-heading-list">
                    <li><span>' . __("honors / awards", "jobhunt") . '</span></li>
                    <li><span>' . __("Dates", "jobhunt") . '</span></li>
		</ul>
		<ul id="total_award_list" class="accordion-list">';
        if (isset($cs_get_award_list) && is_array($cs_get_award_list) && count($cs_get_award_list) > 0) {
            $cs_award_counter = 0;
            foreach ($cs_get_award_list as $award_list) {
                if (isset($award_list) && $award_list <> '') {
                    $counter_extra_feature = $extra_feature_id = $award_list;
                    $cs_award_name = isset($cs_award_names[$cs_award_counter]) ? $cs_award_names[$cs_award_counter] : '';
                    $cs_award_year = isset($cs_award_years[$cs_award_counter]) ? $cs_award_years[$cs_award_counter] : '';
                    $cs_award_description = isset($cs_award_descs[$cs_award_counter]) ? $cs_award_descs[$cs_award_counter] : '';
                    $ca_awards_array = array(
                        'counter_extra_feature' => $counter_extra_feature,
                        'extra_feature_id' => $extra_feature_id,
                        'cs_award_name' => $cs_award_name,
                        'cs_award_year' => $cs_award_year,
                        'cs_award_description' => $cs_award_description,
                        'cs_get_award_list' => $award_list,
                    );
                    $html .= cs_add_award_to_list_fe($ca_awards_array);
                }
                $cs_award_counter++;
            }
        } else {
            $html .= '<li class="cs-no-record">' . cs_info_messages_listing(__("There is no record in honors & awards list", 'jobhunt')) . '</li>';
        }
        $html .= '
	  </ul>
	  </div>
	  <div id="add_awards" style="display: none;">
		<div class="btm-section">
                    <div class="input-info">
                        <div class="row">
                            <div class="cs-heading-area">
                                <span class="cs-btnclose" onClick="javascript:cs_remove_overlay(\'add_awards\',\'append\')"> <i class="icon-times"></i></span> 	
                            </div>';
        $html .= '<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">';
        $cs_opt_array = array('name' => __('Award Name*', 'jobhunt'),
            'desc' => '',
            'field_params' => array(
                'std' => '',
                'id' => 'cs_award_name_pop',
                'classes' => 'cs-form-text cs-input form-control',
                'extra_atr' => 'placeholder="' . __('Award Name*', 'jobhunt') . '"',
                'cust_id' => 'cs_award_name_pop',
                'cust_name' => '',
                'return' => true,
                'required' => true
            ),
        );
        $html .= $cs_html_fields_frontend->cs_form_text_render($cs_opt_array);
        $html .= '</div>';
        $html .= '<script>
				jQuery(function(){
                        jQuery("#cs_award_year_pop").datetimepicker({
                            format:"d-m-Y",
                            timepicker:false
                        });
                });
				</script>';
        $html .= '<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">';
        $cs_opt_array = array('name' => __('Select Year*', 'jobhunt'),
            'desc' => '',
            'field_params' => array(
                'std' => '',
                'id' => 'cs_award_year_pop',
                'classes' => 'cs-form-text cs-input form-control',
                'extra_atr' => 'placeholder="' . __('Select Year*', 'jobhunt') . '"',
                'cust_id' => 'cs_award_year_pop',
                'cust_name' => '',
                'return' => true,
                'required' => true
            ),
        );

        $html .= $cs_html_fields_frontend->cs_form_text_render($cs_opt_array);
        $html .= '</div>';
        $html .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
        $cs_opt_array = array('name' => __('Award Description', 'jobhunt'),
            'desc' => '',
            'field_params' => array(
                'std' => '',
                'id' => 'cs_award_desc_pop',
                'id' => 'cs_exp_desc',
                'classes' => 'cs-form-text cs-input form-control',
                'extra_atr' => 'placeholder="' . __('Award Description', 'jobhunt') . '"',
                'cust_id' => 'cs_award_desc_pop',
                'cust_name' => '',
                'cs_editor' => true,
                'return' => true,
                'required' => true
            ),
        );
        $html .= $cs_html_fields_frontend->cs_form_textarea_render($cs_opt_array);


        $html .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';

        $cs_opt_array = array(
            'std' => __("Add Award", "jobhunt"),
            'id' => '',
            'cust_id' => '',
            'cust_name' => '',
            'cust_type' => 'button',
            'extra_atr' => ' onClick="add_award_to_list_fe(\'' . esc_js(admin_url('admin-ajax.php')) . '\', \'' . esc_js(wp_jobhunt::plugin_url()) . '\',\'award_list\')"',
            'classes' => 'acc-submit cs-section-update cs-bgcolor csborder-color cs-color',
            'return' => true,
        );
        $html .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

        $html .= '</div>';

        $html .= '
		  </div></div></div></div><a href="javascript:cs_createpop(\'add_awards\',\'filter\')" class="button add-more cs-color">
		<i class="icon-plus8"></i>' . __("Add New", "jobhunt") . '</a>';

        echo force_balance_tags($html, true);
    }

}
/**
 * End Function  how to Show awards in the list
 */
/**
 * Start Function  how to Add awards in the list
 */
if (!function_exists('cs_add_award_to_list_fe')) {

    function cs_add_award_to_list_fe($cs_atts) {
        global $post, $cs_form_fields_frontend, $cs_form_fields2;
        $cs_defaults = array(
            'counter_extra_feature' => '',
            'extra_feature_id' => '',
            'cs_award_name' => '',
            'cs_award_year' => '',
            'cs_award_description' => '',
            'cs_get_award_list' => '',
        );
        extract(shortcode_atts($cs_defaults, $cs_atts));

        foreach ($_POST as $keys => $values) {
            $$keys = $values;
        }
        if (isset($_POST['cs_award_name']) && $_POST['cs_award_name'] <> '')
            $cs_award_name = $_POST['cs_award_name'];
        if (isset($_POST['cs_award_year']) && $_POST['cs_award_year'] <> '')
            $cs_award_year = $_POST['cs_award_year'];
        if (isset($_POST['cs_award_description']) && $_POST['cs_award_description'] <> '')
            $cs_award_description = $_POST['cs_award_description'];
        if (isset($_POST['cs_get_award_list']) && $_POST['cs_get_award_list'] <> '')
            $cs_get_award_list = $_POST['cs_get_award_list'];

        if ($extra_feature_id == '' && $counter_extra_feature == '') {
            $counter_extra_feature = $extra_feature_id = time();
        }

        $html = '<li class="parentdelete  parentdeleterow-' . esc_attr($cs_get_award_list) . '" id="edit_track' . esc_attr($extra_feature_id) . '">
                <div class="top-section">
                    <div class="title" id="subject-title' . esc_attr($extra_feature_id) . '">
                            <span>' . $cs_award_name . '</span>
                    </div>
                    <div class="date"><span>' . date('Y', strtotime($cs_award_year)) . '</span></div>
                    <div class="option">
                    <a data-toggle="tooltip" data-placement="top" title="' . __("Edit", "jobhunt") . '" href="javascript:cs_createpop(\'edit_track_form' . esc_js($extra_feature_id) . '\',\'filter\')" class="actions edit">
                    <i class="icon-gear"></i></a>
                    <a data-toggle="tooltip" data-placement="top" title="' . __("Remove", "jobhunt") . '" href="javascript:void(0)" onclick="javascript:cs_remove_resume_options_fromlist(\'' . esc_js(admin_url('admin-ajax.php')) . '\',\'award_list\',\'' . esc_js($cs_get_award_list) . '\')" class="delete-it btndeleteit actions delete-' . esc_attr($cs_get_award_list) . '"><i class="icon-trash-o"></i></a></div>
                </div>			
                <div class="btm-section">
                            <div id="edit_track_form' . esc_attr($extra_feature_id) . '" style="display: none;" class="input-info">
                              <div class="row">';

        $cs_opt_array = array(
            'std' => $extra_feature_id,
            'cust_id' => 'cs_award_list_array',
            'cust_name' => 'cs_award_list_array[]',
            'cust_type' => 'hidden',
            'return' => true,
        );
        $html .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

        $html .= '
                                <div class="cs-heading-area">
                                <span onclick="javascript:cs_remove_overlay(\'edit_track_form' . esc_js($extra_feature_id) . '\',\'append\')" class="cs-btnclose">
                                    <i class="icon-times"></i>
                                </span>
                    <div class="clear"></div>
                      	</div>';
        $html .= '<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">';

        $cs_opt_array = array(
            'std' => $cs_award_name,
            'cust_id' => 'cs_award_name' . $extra_feature_id,
            'cust_name' => 'cs_award_name_array[]',
            'extra_atr' => ' placeholder="' . __('Award Name', 'jobhunt') . '"',
            'classes' => 'cs-form-text cs-input form-control',
            'return' => true,
        );
        $html .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

        $html .= '
	</div>';
        $html .= '<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><script>
                jQuery(function(){
                    jQuery("#cs_award_year' . esc_attr($extra_feature_id) . '").datetimepicker({
                            format:"d-m-Y",
                            timepicker:false
                    });
                });
          </script>';

        $cs_opt_array = array(
            'std' => $cs_award_year,
            'cust_id' => 'cs_award_year' . $extra_feature_id,
            'cust_name' => 'cs_award_year_array[]',
            'extra_atr' => ' placeholder="' . __('Select Year*', 'jobhunt') . '" required="required"',
            'classes' => 'cs-form-text cs-input form-control',
            'return' => true,
        );
        $html .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

        $html .= '
		</div>';

        $html .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
        $html .= $cs_form_fields2->cs_form_textarea_render(
                array('name' => __('Award Description', 'jobhunt'),
                    'id' => 'award_description',
                    'classes' => 'col-md-12',
                    'std' => $cs_award_description,
                    'description' => '',
                    'return' => true,
                    'array' => true,
                    'cs_editor' => true,
                    'force_std' => true,
                    'hint' => ''
                )
        );
        $html .= '</div>';

        $html .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <button type="button" class="acc-submit cs-section-update cs-bgcolor csborder-color cs-color" value="update" name="button_action" onclick="javascript:edit_award_to_list_fe(\'' . esc_js(admin_url('admin-ajax.php')) . '\', \'' . esc_js(wp_jobhunt::plugin_url()) . '\',\'award_list\', \'' . $extra_feature_id . '\' )" >' . __('Update', 'jobhunt') . '</button>
                <span class="form-update-loader" id="response-message' . esc_attr($extra_feature_id) . '"></span>   
              </div>
               </div></div>
            </li>';

        if (isset($_POST['cs_award_name']) && isset($_POST['cs_award_year'])) {
            echo force_balance_tags($html);
        } else {
            return $html;
        }
        if (isset($_POST['cs_award_name']) && isset($_POST['cs_award_name']))
            die();
    }

    add_action('wp_ajax_cs_add_award_to_list_fe', 'cs_add_award_to_list_fe');
}
/**
 * End Function  how to Add awards in the list
 */
/**
 * Start Function  how to Show portfolio in the list
 */
if (!function_exists('cs_portfolio_list_fe')) {

    function cs_portfolio_list_fe() {
        $cs_jobhunt = new wp_jobhunt();
        global $post, $current_user, $cs_form_fields_frontend, $cs_portfolio_counter, $cs_form_fields2;
        $uid = $current_user->ID;
        $cs_get_port_list = get_user_meta($uid, 'cs_port_list_array', true);
        $cs_image_titles = get_user_meta($uid, 'cs_image_title_array', true);
        $cs_image_uploads = get_user_meta($uid, 'cs_image_upload_array', true);


        $html = '
		<script>
            /*jQuery(document).ready(function($) {
                $("#total_portfolio_list").sortable({
                        cancel : \'td div.table-form-elem\'
                });
            });*/
		</script>
		<div class="cs-list-table">
            <ul id="total_portfolio_list" class="accordion-list">';
        if (isset($cs_get_port_list) && is_array($cs_get_port_list) && count($cs_get_port_list) > 0) {
            $cs_portfolio_counter = 0;
            foreach ($cs_get_port_list as $award_list) {
                if (isset($award_list) && $award_list <> '') {
                    $counter_extra_feature = $extra_feature_id = $award_list;
                    $cs_image_title = isset($cs_image_titles[$cs_portfolio_counter]) ? $cs_image_titles[$cs_portfolio_counter] : '';
                    $cs_image_upload = isset($cs_image_uploads[$cs_portfolio_counter]) ? $cs_image_uploads[$cs_portfolio_counter] : '';
                    $ca_awards_array = array(
                        'counter_extra_feature' => $counter_extra_feature,
                        'extra_feature_id' => $extra_feature_id,
                        'cs_image_title' => $cs_image_title,
                        'cs_image_upload' => $cs_image_upload,
                    );
                    $html .= cs_add_portfolio_to_list_fe($ca_awards_array);
                }
                $cs_portfolio_counter++;
            }
        } else {
            $html .= '<li class="cs-no-record">' . cs_info_messages_listing(__("There is no record in portfolio list", 'jobhunt')) . '</li>';
        }
        // only for add form
        $html .= '
	  </ul>
	  </div>
	  <div id="add_portfolio" style="display: none;">          
        <form id="frm_add_portfolio_list" action="#"  enctype="multipart/form-data" method="POST">
		<div class="btm-section">
			<div class="input-info">
				<div class="row">
					<div class="cs-heading-area">
						<span class="cs-btnclose" onClick="javascript:cs_remove_overlay(\'add_portfolio\',\'append\')"> <i class="icon-times"></i></span> 	
					</div>';


        $html .= '<script type="text/javascript">
                    function chnage_selected_image(){
                    var file_type = jQuery(\'#cs_portfolio_image_upload\')[0].files[0]["type"];                            
                    var img_url = URL.createObjectURL(jQuery(\'#cs_portfolio_image_upload\')[0].files[0]);
                    var validimage = 0;
                    if(file_type == "image/jpeg" || file_type == "image/jpg" || file_type == "image/png"){
                        validimage = 1;
                    }else{
                    validimage = 0;
                        jQuery(\'.cs-img-detail #portfolio-add-img-msg\').addClass("error-msg");
                        jQuery(\'#cs_candidate_portolfio_img\').attr(\'src\', "");
                    }

                    var _URL = window.URL || window.webkitURL;                  
                    var files = jQuery(\'#cs_portfolio_image_upload\')[0].files[0];


                    var img = new Image(),
                        fileSize=Math.round(files.size / 1024);
                    if(fileSize >= 1024){

                        validimage = 0;
                            jQuery(\'.cs-img-detail #portfolio-add-img-msg\').addClass("error-msg");
                            jQuery(\'#cs_candidate_portolfio_img\').attr(\'src\', "");
                        }else{
                            validimage = 1;
                        }
                    img.onload = function () {
                        var width=this.width,
                            height=this.height,
                            imgsrc=this.src;  

                        if(width>=180 && height >= 135){
                        validimage = 1;
                        }else{
                        validimage = 0;
                            jQuery(\'.cs-img-detail #portfolio-add-img-msg\').addClass("error-msg");
                            jQuery(\'#cs_candidate_portolfio_img\').attr(\'src\', "");
                        }

                    };   
                    img.src = _URL.createObjectURL(files);
                   if (validimage == 1){
                        jQuery(\'.cs-img-detail #portfolio-add-img-msg\').removeClass("error-msg");
                        jQuery(\'#cs_candidate_portolfio_img\').attr(\'src\', img_url);
                    }
                    }
                </script>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 cs-img-detail">
                <div class="alert alert-dismissible user-img"> 
                    <div class="page-wrap" id="cs_candidate_portolfio_box">
                        <figure>
                            '; {
            $html .= '<img src="' . esc_url($cs_jobhunt->plugin_url()) . 'assets/images/upload-img.jpg" id="cs_candidate_portolfio_img" width="100" alt="" />';
        }
        $html .= '</figure>
                    </div>
                    </div>
                    <div class="upload-btn-div">
                        <div class="fileUpload uplaod-btn btn cs-color csborder-color">
                            <span class="cs-color">' . __('Browse', 'jobhunt') . '</span>
                            <label class="browse-icon">';

        $cs_opt_array = array(
            'std' => __('Browse', 'jobhunt'),
            'cust_id' => 'cs_portfolio_image_upload',
            'cust_name' => 'cs_portfolio_image_upload',
            'cust_type' => 'file',
            'extra_atr' => ' onchange="chnage_selected_image()"',
            'classes' => 'upload cs-uploadimgjobseek',
            'return' => true,
        );
        $html .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

        $html .= '	</label>				
                        </div>
                        <br />
                        <span class="cs-color" id="portfolio-add-img-msg">' . __('Max file size is 1MB, Minimum dimension: 180x135 And Suitable files are .jpg & .png', 'jobhunt') . '</span>              
                    </div>
                </div>';

        $html .= '<div class="col-lg-7 col-md-7 col-sm-12 col-xs-12">';
        $html .= $cs_form_fields2->cs_form_text_render(
                array('name' => __('Image Title*', 'jobhunt'),
                    'id' => 'image_title',
                    'std' => '',
                    'description' => '',
                    'return' => true,
                    'hint' => '',
                    'required' => true,
                )
        );
        $html .= '</div>';

        $html .= '
		<div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
			<ul class="form-elements noborder">
                <li class="to-label"></li>
                    <li class="to-field">';

        $cs_opt_array = array(
            'std' => __('Add Portfolio', 'jobhunt'),
            'cust_id' => 'add_pt_to_js_list',
            'cust_name' => '',
            'cust_type' => 'button',
            'extra_atr' => ' onClick="add_portfolio_to_candidate_list(\'' . esc_js(admin_url('admin-ajax.php')) . '\', \'frm_add_portfolio_list\')"',
            'classes' => 'acc-submit cs-section-update cs-bgcolor csborder-color cs-color',
            'return' => true,
        );
        $html .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

        $html .= '
                        <div class="portfolio-feature-loader"></div>
                    </li>
                </ul>
				</div>
          </div></div></div>';

        $cs_opt_array = array(
            'std' => 'cs_add_portfolio_to_candidate_list',
            'cust_id' => 'action',
            'cust_name' => 'action',
            'cust_type' => 'hidden',
            'return' => true,
        );
        $html .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

        $cs_opt_array = array(
            'std' => $uid,
            'cust_id' => 'cs_user',
            'cust_name' => 'cs_user',
            'cust_type' => 'hidden',
            'return' => true,
        );
        $html .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

        $html .= '
</form>                          
</div><a href="javascript:cs_createpop(\'add_portfolio\',\'filter\')" class="button add-more cs-color">
	  		<i class="icon-plus8"></i>' . __("Add New", "jobhunt") . '</a>';

        echo force_balance_tags($html, true);
    }

}
/**
 * End Function  how to Show portfolio in the list
 */
/**
 * Start Function  how to add portfolio in the list
 */
if (!function_exists('cs_add_portfolio_to_list_fe')) {

    function cs_add_portfolio_to_list_fe($cs_atts) {
        $cs_jobhunt = new wp_jobhunt();
        global $post, $current_user, $cs_portfolio_counter, $cs_form_fields_frontend, $cs_form_fields2;
        $uid = $current_user->ID;
        $cs_defaults = array(
            'counter_extra_feature' => '',
            'extra_feature_id' => '',
            'cs_image_title' => '',
            'cs_image_upload' => '',
        );
        extract(shortcode_atts($cs_defaults, $cs_atts));
        foreach ($_POST as $keys => $values) {
            $$keys = $values;
        }
        if (isset($_POST['cs_image_title']) && $_POST['cs_image_title'] <> '')
            $cs_image_title = $_POST['cs_image_title'];
        if (isset($_POST['cs_image_upload']) && $_POST['cs_image_upload'] <> '')
            $cs_image_upload = $_POST['cs_image_upload'];
        $cs_image_upload_hidden = $cs_image_upload;
        if ($extra_feature_id == '' && $counter_extra_feature == '') {
            $counter_extra_feature = $extra_feature_id = time();
        }

        $image_url = '';
        if ($cs_image_upload == '') {
            $cs_image_upload = esc_url(wp_jobhunt::plugin_url() . 'assets/images/img-not-found16x9.jpg');
        } else {
            if (!cs_image_exist($cs_image_upload)) {
                $cs_image_upload = cs_get_img_url($cs_image_upload, 'cs_media_5', false, false);
            }
        }
        if (!cs_image_exist($cs_image_upload)) {
            $cs_image_upload = esc_url(wp_jobhunt::plugin_url() . 'assets/images/img-not-found16x9.jpg');
        }

        $html = '<li class="parentdelete parentdeleterow-' . esc_attr($extra_feature_id) . '" id="edit_track' . esc_attr($extra_feature_id) . '">
                <div class="top-section">
                    <div class="pic-holder"> <img src="' . esc_url($cs_image_upload) . '" alt="" /> </div>
                    <div class="title" id="subject-title' . esc_attr($extra_feature_id) . '">
                            <span>' . $cs_image_title . '</span>
                    </div>
                    <div class="option">
                        <a data-toggle="tooltip" data-placement="top" title="' . __("Edit", "jobhunt") . '" href="javascript:cs_createpop(\'edit_track_form' . esc_js($extra_feature_id) . '\',\'filter\')" class="actions edit">
                            <i class="icon-gear"></i></a>
                            <a data-toggle="tooltip" data-placement="top" title="' . __("Remove", "jobhunt") . '" onclick="javascript:cs_remove_resume_options_fromlist(\'' . esc_js(admin_url('admin-ajax.php')) . '\',\'portfolio_list\',\'' . esc_js($extra_feature_id) . '\')" class="delete-it btndeleteit actions delete-' . esc_attr($extra_feature_id) . '"><i class="icon-trash-o"></i>
                        </a>
                    </div>
                </div>
                <div class="btm-section">
		<div id="edit_track_form' . esc_attr($extra_feature_id) . '" style="display: none;" class="input-info">
                    <form id="edit_portfolio_list' . esc_attr($extra_feature_id) . '" action="#"  enctype="multipart/form-data" method="POST">
                    <div class="row">';

        $cs_opt_array = array(
            'std' => $extra_feature_id,
            'cust_id' => 'post_id' . absint($extra_feature_id),
            'cust_name' => 'cs_port_list_array' . absint($extra_feature_id),
            'cust_type' => 'hidden',
            'return' => true,
        );
        $html .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

        $html .= '  <div class="cs-heading-area">
                        <span onclick="javascript:cs_remove_overlay(\'edit_track_form' . esc_js($extra_feature_id) . '\',\'append\')" class="cs-btnclose"><i class="icon-times"></i></span>
                        <div class="clear"></div>
                    </div>';



        $html .= '<script type="text/javascript">
                function chnage_selected_image' . absint($extra_feature_id) . '(){
                    jQuery(\'#cs_user_img' . absint($extra_feature_id) . '_box\').show();                            
                    var file_type = jQuery(\'#cs_portfolio_image_upload' . absint($extra_feature_id) . '\')[0].files[0]["type"];

                    var img_path = URL.createObjectURL(jQuery(\'#cs_portfolio_image_upload' . absint($extra_feature_id) . '\')[0].files[0]);
                    var validimage = 0;
                    if(file_type == "image/jpeg" || file_type == "image/jpg" || file_type == "image/png"){
                        validimage = 1;
                    }else{
                        validimage = 0;
                        jQuery(\'#portfolio-add-img-msg' . absint($extra_feature_id) . '\').addClass("error-msg");
                        jQuery(\'#cs_user_img' . absint($extra_feature_id) . '_img\').attr(\'src\', "");
                    }
                    var _URL = window.URL || window.webkitURL;                  
                            var files = jQuery(\'#cs_portfolio_image_upload' . absint($extra_feature_id) . '\')[0].files[0];
                            var img = new Image(),
                                fileSize=Math.round(files.size / 1024);
                               if(fileSize >= 1024){                                
                                validimage = 0;
                                    jQuery(\'#portfolio-add-img-msg' . absint($extra_feature_id) . '\').addClass("error-msg");
                                    jQuery(\'#cs_user_img' . absint($extra_feature_id) . '_img\').attr(\'src\', "");
                                }else{
                                    validimage = 1;
                                } 
                            img.onload = function () {
                                var width=this.width,
                                    height=this.height,
                                    imgsrc=this.src;  
                                if(width>=180 && height >= 135){
                                validimage = 1;
                                }else{
                                validimage = 0;
                                    jQuery(\'#portfolio-add-img-msg' . absint($extra_feature_id) . '\').addClass("error-msg");
                                    jQuery(\'#cs_user_img' . absint($extra_feature_id) . '_img\').attr(\'src\', "");
                                }                                
                            };   
                            img.src = _URL.createObjectURL(files);
                   if (validimage == 1){
                        jQuery(\'#portfolio-add-img-msg' . absint($extra_feature_id) . '\').removeClass("error-msg");
                        jQuery(\'#cs_user_img' . absint($extra_feature_id) . '_img\').attr(\'src\', img_path);
                    }
                }
            </script>';
        $html .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 cs-img-detail">
                <div class="alert alert-dismissible user-img"> 
                    <div class="page-wrap" id="cs_user_img' . absint($extra_feature_id) . '_box">
            <figure>';

        if ($cs_image_upload <> '') {

            $html .= '<img src="' . esc_url($cs_image_upload) . '" id="cs_user_img' . absint($extra_feature_id) . '_img" width="100" alt="" />
                  <div class="gal-edit-opts close"><a href="javascript:cs_del_media(\'cs_user_img' . absint($extra_feature_id) . '\')" class="delete"><span aria-hidden="true">×</span></a></div>';
        } else {
            $html .= '<img src="' . esc_url($cs_jobhunt->plugin_url()) . 'assets/images/upload-img.jpg" id="cs_user_img' . absint($extra_feature_id) . '_img"  width="100" alt="" />';
        }
        $html .= '</figure>
                        </div>
                    </div>
                    <div class="upload-btn-div">
                        <div class="fileUpload uplaod-btn btn cs-color csborder-color">
                            <span class="cs-color">' . __('Browse', 'jobhunt') . '</span>';

        $cs_opt_array = array(
            'std' => $cs_image_upload_hidden,
            'cust_id' => 'cs_user_img' . absint($extra_feature_id),
            'cust_name' => 'media_img' . absint($extra_feature_id),
            'cust_type' => 'hidden',
            'return' => true,
        );
        $html .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

        $html .= '
                            <label class="browse-icon">';

        $cs_opt_array = array(
            'std' => __('Browse', 'jobhunt'),
            'cust_id' => 'cs_portfolio_image_upload' . absint($extra_feature_id),
            'cust_name' => 'cs_portfolio_image_upload' . absint($extra_feature_id),
            'cust_type' => 'file',
            'exttra_atr' => ' onchange="chnage_selected_image' . absint($extra_feature_id) . '()" id="cs_portfolio_image_upload' . absint($extra_feature_id) . '"',
            'classes' => 'upload cs-uploadimgjobseek',
            'return' => true,
        );
        $html .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

        $html .= '
							</label>				
                         </div>
                        <br />
                        <span id="portfolio-add-img-msg' . absint($extra_feature_id) . '"> ' . __('Max file size is 1MB, Minimum dimension: 180x135 And Suitable files are .jpg & .png', 'jobhunt') . '</span>              
                    </div>
                </div>';

        $html .= '<div class="col-lg-7 col-md-7 col-sm-12 col-xs-12">';
        $html .= $cs_form_fields2->cs_form_text_render(
                array('name' => __('Image Title*', 'jobhunt'),
                    'id' => 'image_title' . absint($extra_feature_id),
                    'std' => $cs_image_title,
                    'description' => '',
                    'return' => true,
                    'hint' => '',
                    'required' => true
                )
        );
        $html .= '</div>';

        $html .= '<div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
                    <button type="button" value="update" name="button_action" class="acc-submit cs-section-update cs-bgcolor csborder-color cs-color" onClick="edit_portfolio_to_candidate_list(\'' . esc_js(admin_url('admin-ajax.php')) . '\', \'edit_portfolio_list' . esc_attr($extra_feature_id) . '\', \'' . $extra_feature_id . '\' )" >' . __('Update Portfolio', 'jobhunt') . '</button>
                <div class="portfolio-feature-loader"></div>   
              </div>
            </div>';

        $cs_opt_array = array(
            'std' => 'cs_edit_portfolio_to_candidate_list',
            'cust_id' => 'action',
            'cust_name' => 'action',
            'cust_type' => 'hidden',
            'return' => true,
        );
        $html .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

        $cs_opt_array = array(
            'std' => $uid,
            'cust_id' => 'cs_user',
            'cust_name' => 'cs_user',
            'cust_type' => 'hidden',
            'return' => true,
        );
        $html .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

        $html .= '</form>
                </div>
             </div> 
          </li>';
        if (isset($_POST['cs_image_title']) && isset($_POST['cs_image_upload'])) {
            echo force_balance_tags($html);
        } else {
            return $html;
        }
        if (isset($_POST['cs_image_title']) && isset($_POST['cs_image_title']))
            die();
    }

    add_action('wp_ajax_cs_add_portfolio_to_list_fe', 'cs_add_portfolio_to_list_fe');
}

if (!function_exists('ajax_form_save')) {

    function ajax_form_save() {
        global $post, $current_user, $reset_date, $cs_options;
        if (isset($_POST['cs_user']) && $_POST['cs_user'] <> '') {
            $user_id = $_POST['cs_user'];
            // check demo user check
            $get_demouser_info = get_user_by('id', $user_id);
            if (isset($get_demouser_info->user_login) && $get_demouser_info->user_login == 'jobcareer-candidate') {
                echo __("You don't have access to update in demo mode.", "jobdoor");
                die();
            }
            if (!current_user_can('edit_user', $user_id)) {
                return false;
            }

            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                return;
            }

            $data = array();
            // update email
            if (isset($_POST['user_email'])) {
                $email_response = wp_update_user(array('ID' => $user_id, 'user_email' => $_POST['user_email']));

                if (isset($email_response->errors)) {
                    echo __('Your given email address is already used or invalid, Please try with correct information', 'jobhunt');
                    die();
                }
            }

            // update display name
            if (isset($_POST['display_name'])) {
                wp_update_user(array('ID' => $user_id, 'display_name' => $_POST['display_name']));
                $user_name = sanitize_title($_POST['display_name']);
                wp_update_user(array('ID' => $user_id, 'user_nicename' => $user_name));
            }
            // update website url
            if (isset($_POST['user_url'])) {
                wp_update_user(array('ID' => $user_id, 'user_url' => $_POST['user_url']));
            }
            // update first name
            if (isset($_POST['first_name'])) {
                wp_update_user(array('ID' => $user_id, 'first_name' => $_POST['first_name']));
            }
            // update last name
            if (isset($_POST['last_name'])) {
                wp_update_user(array('ID' => $user_id, 'last_name' => $_POST['last_name']));
            }

            // description
            if (isset($_POST['candidate_content'])) {
                wp_update_user(array('ID' => $user_id, 'description' => $_POST['candidate_content']));
            }

            foreach ($_POST as $key => $value) {
                if (strstr($key, 'cs_')) {
                    if ($key == 'cs_transaction_expiry_date' || $key == 'cs_job_expired' || $key == 'cs_job_posted' || $key == 'cs_user_last_activity_date') {
                        if ($value == '' || $key == 'cs_user_last_activity_date') {
                            $value = date('d-m-Y H:i:s');
                        }
                        $data[$key] = strtotime($value);
                        update_user_meta($user_id, $key, strtotime($value));
                    } else {
                        if ($key == 'cs_cus_field') {
                            if (is_array($value) && sizeof($value) > 0) {
                                foreach ($value as $c_key => $c_val) {
                                    update_user_meta($user_id, $c_key, $c_val);
                                }
                            }
                        } else {
                            $data[$key] = $value;
                            update_user_meta($user_id, $key, $value);
                        }
                    }
                }
            }
		
            update_user_meta($user_id, 'cs_array_data', $data);
			
			
				$cs_media_image = cs_user_avatar();
			
            if ($cs_media_image == '') {
                $cs_media_image = $_POST['media_img'];
            } else {
                $cs_prev_img = get_user_meta($current_user->ID, 'user_img', true);
                cs_remove_img_url($cs_prev_img);
            }
			if (isset($_POST['user_profile'])) {
				update_user_meta($current_user->ID, 'user_img', $cs_media_image);
			}

            $cover_media_upload = cs_user_avatar('cand_cover_media_upload');

            if ($cover_media_upload == '') {
                $cover_media_upload = $_POST['cs_cover_candidate_img'];
            } else {
                $cs_cover_prev_img = get_user_meta($current_user->ID, 'cover_user_img', true);
                cs_remove_img_url($cs_cover_prev_img);
            }
			if (isset($_POST['user_profile'])) {
            update_user_meta($current_user->ID, 'cover_user_img', $cover_media_upload);
			}

            echo __('Update Successfully', 'jobhunt');
        } else {
            echo "Save Failed";
        }
        die();
    }

    add_action('wp_ajax_ajax_form_save', 'ajax_form_save');
}

/**
 * End Function  how to add portfolio in the list
 */
/**
 * Start Function  how to add candidate cv form in ajax base
 */
if (!function_exists('ajax_candidate_cv_form_save')) {

    function ajax_candidate_cv_form_save() {
        global $post, $reset_date, $cs_options;
        if (isset($_POST['cs_user']) && $_POST['cs_user'] <> '') {
            $user_id = $_POST['cs_user'];
            $cs_cover_letter = $_POST['cs_cover_letter'];
            $post_author = isset($_POST['cs_user']) ? $_POST['cs_user'] : '';
            $cs_media_image = cs_user_cv();
            if (isset($cs_media_image['error']) && $cs_media_image['error'] == 1) {
                echo esc_html($cs_media_image['message']);
            } else {
                if ($cs_media_image == '') {
                    $cs_media_image = isset($_POST['cs_candidate_cv']) ? $_POST['cs_candidate_cv'] : '';
                }
                update_user_meta($user_id, 'cs_candidate_cv', $cs_media_image);
                update_user_meta($user_id, 'cs_cover_letter', $cs_cover_letter);
                echo __('Update Successfully', 'jobhunt');
            }
        } else {
            echo __('Save failed, Please try again', 'jobhunt');
        }
        die();
    }

    add_action('wp_ajax_ajax_candidate_cv_form_save', 'ajax_candidate_cv_form_save');
}

add_filter('upload_mimes', 'add_custom_upload_mimes');
if (!function_exists('add_custom_upload_mimes')) {

    function add_custom_upload_mimes($existing_mimes) {
        $existing_mimes['zip'] = 'application/zip';
        $existing_mimes['swf'] = 'application/x-shockwave-flash';
        $existing_mimes['rtf'] = 'text/richtext';
        $existing_mimes['tiff'] = 'image/tiff';
        $existing_mimes['pdf'] = 'application/pdf';
        $existing_mimes['doc'] = 'application/msword';
        return $existing_mimes;
    }

}

/**
 * End Function  how to upload custom mimes(image formet)
 */
// User Avatar

function cs_cust_upload_dir($upload) {
    $upload['subdir'] = $upload['basedir'];
    $upload['path'] = $upload['basedir'];
    $upload['url'] = $upload['baseurl'];
    return $upload;
}

/*
 * Override the default upload path.
 */

function cs_user_images_custom_directory($dir) {
    global $plugin_user_images_directory;
    return array(
        'path' => $dir['basedir'] . '/' . $plugin_user_images_directory,
        'url' => $dir['baseurl'] . '/' . $plugin_user_images_directory,
        'subdir' => '/' . $plugin_user_images_directory,
            ) + $dir;
}

/**
 * Start Function  how to upload User image(Avatar)
 */
if (!function_exists('cs_user_avatar')) {

    function cs_user_avatar($Fieldname = 'media_upload') {
        $img_resized_name = '';
        // Register our new path for user images.
        add_filter('upload_dir', 'cs_user_images_custom_directory');

        if (is_user_logged_in() && isset($_FILES[$Fieldname]) && $_FILES[$Fieldname] != '') {

            $json = array();
            require_once ABSPATH . 'wp-admin/includes/image.php';
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';
            $cs_allowed_image_types = array(
                'jpg|jpeg|jpe' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
            );
            $status = wp_handle_upload($_FILES[$Fieldname], array('test_form' => false, 'mimes' => $cs_allowed_image_types));
            if (empty($status['error'])) {

                $image = wp_get_image_editor($status['file']);

                if (!is_wp_error($image)) {
                    $sizes_array = array(
                        array('width' => 270, 'height' => 203, 'crop' => true),
                        array('width' => 236, 'height' => 168, 'crop' => true),
                        array('width' => 200, 'height' => 200, 'crop' => true),
                        array('width' => 180, 'height' => 135, 'crop' => true),
                        array('width' => 150, 'height' => 113, 'crop' => true),
                    );
                    $resize = $image->multi_resize($sizes_array, true);
                }

                if (is_wp_error($image)) {
                    echo '<span class="error-msg">' . $image->get_error_message() . '</span>';
                } else {
                    $wp_upload_dir = wp_upload_dir();
                    $img_resized_name = isset($resize[0]['file']) ? basename($resize[0]['file']) : '';
                    $filename = $img_resized_name;
                    $filetype = wp_check_filetype(basename($filename), null);
                    if ($filename != '') {
                        // Prepare an array of post data for the attachment.
                        $attachment = array(
                            'guid' => $wp_upload_dir['url'] . '/' . ($filename),
                            'post_mime_type' => $filetype['type'],
                            'post_title' => preg_replace('/\.[^.]+$/', '', ($filename)),
                            'post_content' => '',
                            'post_status' => 'inherit'
                        );

                        // Insert the attachment.
                        $attach_id = wp_insert_attachment($attachment, $filename);

                        // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
                        require_once( ABSPATH . 'wp-admin/includes/image.php' );

                        // Generate the metadata for the attachment, and update the database record.
                        $attach_data = wp_generate_attachment_metadata($attach_id, $filename);
                        wp_update_attachment_metadata($attach_id, $attach_data);
                    }
                }
                //$uploads = wp_upload_dir();
                //$img_resized_name = isset($resize[0]['file']) ? basename($resize[0]['file']) : '';
            } else {
                $img_resized_name = '';
            }

            //remove_filter('upload_dir', 'cs_cust_upload_dir');
        }
        // Set everything back to normal.
        remove_filter('upload_dir', 'cs_user_images_custom_directory');
        return $img_resized_name;
    }

}

/**
 * Start Function  how to upload User image(Avatar)
 */
if (!function_exists('cs_import_user_profile_images')) {

    function cs_import_user_profile_images($fieldname_url = '', $fieldname_orignal) {
        $img_resized_name = '';
        if (is_user_logged_in() && isset($fieldname_url) && $fieldname_url != '') {
            $cs_allowed_image_types = array(
                'jpg|jpeg|jpe' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
            );
            $image = wp_get_image_editor($fieldname_url);
            if (!is_wp_error($image)) {
                $sizes_array = array(
                    array('width' => 270, 'height' => 203, 'crop' => true),
                    array('width' => 236, 'height' => 168, 'crop' => true),
                    array('width' => 200, 'height' => 200, 'crop' => true),
                    array('width' => 180, 'height' => 135, 'crop' => true),
                    array('width' => 150, 'height' => 113, 'crop' => true),
                );
                $resize = $image->multi_resize($sizes_array, true);
            }

            if (is_wp_error($image)) {
                
            } else {
                $wp_upload_dir = wp_upload_dir();
                $img_resized_name = isset($fieldname_orignal) ? basename($fieldname_orignal) : '';
                $filename = $img_resized_name;
                $filetype = wp_check_filetype(basename($filename), null);
                if ($filename != '') {
                    // Prepare an array of post data for the attachment.
                    $attachment = array(
                        'guid' => $wp_upload_dir['url'] . '/' . ($filename),
                        'post_mime_type' => $filetype['type'],
                        'post_title' => preg_replace('/\.[^.]+$/', '', ($filename)),
                        'post_content' => '',
                        'post_status' => 'inherit'
                    );

                    // Insert the attachment.
                    $attach_id = wp_insert_attachment($attachment, $filename);
                    // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
                    require_once( ABSPATH . 'wp-admin/includes/image.php' );

                    // Generate the metadata for the attachment, and update the database record.
                    $attach_data = wp_generate_attachment_metadata($attach_id, $filename);
                    wp_update_attachment_metadata($attach_id, $attach_data);
                }
            }
        }
        return $img_resized_name;
    }

}


/**
 * End Function  how to upload User image(Avatar)
 */
/**
 * Start Function  how to upload candidate profile image
 */
if (!function_exists('cs_candidate_profile_image')) {

    function cs_candidate_profile_image($image_name) {
        $img_resized_name = '';
        if (is_user_logged_in() && isset($_FILES[$image_name]) && $_FILES[$image_name] != '') {
            $json = array();
            require_once ABSPATH . 'wp-admin/includes/image.php';
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';
            $cs_allowed_image_types = array(
                'jpg|jpeg|jpe' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
            );
            $status = wp_handle_upload($_FILES[$image_name], array('test_form' => false, 'mimes' => $cs_allowed_image_types));
            if (empty($status['error'])) {
                $image = wp_get_image_editor($status['file']);
                if (!is_wp_error($image)) {
                    $sizes_array = array(
                        array('width' => 180, 'height' => 135, 'crop' => true),
                    );
                    $resize = $image->multi_resize($sizes_array, true);
                }
                if (is_wp_error($image)) {

                    $reponse['error'] = 1;
                    $reponse['message'] = '<span class="error-msg">' . $image->get_error_message() . '</span>';
                    echo json_encode($reponse);
                    exit();
                }
                $uploads = wp_upload_dir();
                $img_resized_name = isset($resize[0]['file']) ? basename($resize[0]['file']) : '';
            } else {
                $img_resized_name = '';
            }
        }
        return $img_resized_name;
    }

}

/**
 * End Function  how to upload candidate profile image
 */
/**
 * Start Function  how to upload User CV
 */
if (!function_exists('cs_user_cv')) {

    function cs_user_cv() {
        $resized_url = '';
        if (is_user_logged_in() && isset($_FILES['media_upload'])) {
            $json = array();
            require_once ABSPATH . 'wp-admin/includes/file.php';
            $current_user_id = get_current_user_id();
            $cs_allowed_file_types = array(
                'pdf' => 'application/pdf',
                'doc' => 'application/msword',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'rtf' => 'application/rtf',
                'txt' => 'text/plain',
            );
            $status = wp_handle_upload($_FILES['media_upload'], array('test_form' => false, 'mimes' => $cs_allowed_file_types, 'unique_filename_callback' => 'my_cust_filename'));
            if (isset($status) && !isset($status['error'])) {
                $uploads = wp_upload_dir();
                $resized_url = $status['url'];
            } else {
                if (isset($status['error'])) {
                    $resized_url = array('error' => 1, 'message' => $status['error']);
                } else {
                    $resized_url = '';
                }
            }
        }
        return $resized_url;
    }

}
/**
 * Start function to get custom file name
 */
if (!function_exists('my_cust_filename')) {

    function my_cust_filename($dir, $name, $ext) {
        return $name . "_" . date('Y-d-m-H-i-s') . $ext;
    }

}

// Start function for candidate custom fields frontend 
if (!function_exists('cs_candidate_custom_fields_frontend')) {

    function cs_candidate_custom_fields_frontend($cs_candidate_id = '') {
        global $cs_form_fields2, $cs_html_fields;
        $cs_html = '';
        $cs_job_cus_fields = get_option("cs_candidate_cus_fields");
        if (is_array($cs_job_cus_fields) && sizeof($cs_job_cus_fields) > 0) {

            foreach ($cs_job_cus_fields as $cus_field) {

                $cs_type = isset($cus_field['type']) ? $cus_field['type'] : '';
                switch ($cs_type) {
                    case('text'):
                        $cs_label = isset($cus_field['label']) ? $cus_field['label'] : '';
                        $cs_meta_key = isset($cus_field['meta_key']) ? $cus_field['meta_key'] : '';
                        $cs_default_val = isset($cus_field['default_value']) ? $cus_field['default_value'] : '';
                        $cs_required = isset($cus_field['required']) && $cus_field['required'] == 'yes' ? ' required="required"' : '';
                        $cs_help_txt = isset($cus_field['help']) ? $cus_field['help'] : '';

                        if ($cs_candidate_id != '') {
                            $cs_default_val = get_user_meta((int) $cs_candidate_id, "$cs_meta_key", true);
                        }

                        $cs_html .= '<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                        <label>' . esc_attr($cs_label) . '</label>';
                        $cs_opt_array = array(
                            'std' => $cs_default_val,
                            'cust_id' => $cs_meta_key,
                            'cust_name' => 'cs_cus_field[' . esc_attr($cs_meta_key) . ']',
                            'classes' => 'form-control',
                            'return' => true,
                        );

                        if (isset($cus_field['placeholder']) && $cus_field['placeholder'] != '') {
                            $cs_opt_array['extra_atr'] = ' placeholder="' . $cus_field['placeholder'] . '" ' . $cs_required;
                        } else {
                            $cs_opt_array['extra_atr'] = $cs_required;
                        }

                        $cs_html .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

                        if ($cs_help_txt <> '') {
                            $cs_html .= '<span class="cs-caption">' . $cs_help_txt . '</span>';
                        }
                        $cs_html .= '</div>';
                        break;
                    case('textarea'):
                        $cs_label = isset($cus_field['label']) ? $cus_field['label'] : '';
                        $cs_rows = isset($cus_field['rows']) ? $cus_field['rows'] : '';
                        $cs_cols = isset($cus_field['cols']) ? $cus_field['cols'] : '';
                        $cs_meta_key = isset($cus_field['meta_key']) ? $cus_field['meta_key'] : '';
                        $cs_default_val = isset($cus_field['default_value']) ? $cus_field['default_value'] : '';
                        $cs_required = isset($cus_field['required']) && $cus_field['required'] == 'yes' ? ' required="required"' : '';
                        $cs_help_txt = isset($cus_field['help']) ? $cus_field['help'] : '';
                        if ($cs_candidate_id != '') {
                            $cs_default_val = get_user_meta((int) $cs_candidate_id, "$cs_meta_key", true);
                        }
                        $cs_html .= '<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
					<label>' . esc_attr($cs_label) . '</label>';

                        $cs_opt_array = array(
                            'std' => $cs_default_val,
                            'cust_id' => $cs_meta_key,
                            'cust_name' => 'cs_cus_field[' . esc_attr($cs_meta_key) . ']',
                            'classes' => 'form-control',
                            'extra_atra' => $cs_required . ' rows="' . $cs_rows . '" cols="' . $cs_cols . '"',
                            'return' => true,
                        );
                        $cs_html .= $cs_form_fields2->cs_form_textarea_render($cs_opt_array);

                        if ($cs_help_txt <> '') {
                            $cs_html .= '<span class="cs-caption">' . $cs_help_txt . '</span>';
                        }
                        $cs_html .= '</div>';
                        break;
                    case('dropdown'):
                        $div_class = 'class="select-holder"';
                        $cs_label = isset($cus_field['label']) ? $cus_field['label'] : '';
                        $cs_meta_key = isset($cus_field['meta_key']) ? $cus_field['meta_key'] : '';
                        $cs_default_val = isset($cus_field['default_value']) ? $cus_field['default_value'] : '';
                        $cs_required = isset($cus_field['required']) && $cus_field['required'] == 'yes' ? ' required="required"' : '';
                        $cs_help_txt = isset($cus_field['help']) ? $cus_field['help'] : '';
                        if ($cs_candidate_id != '') {
                            $cs_default_val = get_user_meta((int) $cs_candidate_id, "$cs_meta_key", true);
                        }
                        $cs_dr_name = ' name="cs_cus_field[' . sanitize_html_class($cs_meta_key) . ']"';
                        $cs_dr_mult = '';
                        if (isset($cus_field['post_multi']) && $cus_field['post_multi'] == 'yes') {
                            $cs_dr_name = ' name="cs_cus_field[' . sanitize_html_class($cs_meta_key) . '][]"';
                            $cs_dr_mult = ' multiple="multiple"';
                            $div_class = '';
                        }
                        $a_options = '';
                        if (isset($cus_field['first_value']) && $cus_field['first_value'] != '') {
                            $a_options .= '<option value="">' . $cus_field['first_value'] . '</option>';
                        }
                        $cs_opt_counter = 0;
                        foreach ($cus_field['options']['value'] as $cs_option) {
                            if (isset($cus_field['post_multi']) && $cus_field['post_multi'] == 'yes') {
                                $cs_checkd = '';
                                if (is_array($cs_default_val) && in_array($cs_option, $cs_default_val)) {
                                    $cs_checkd = ' selected="selected"';
                                }
                            } else {
                                $cs_checkd = $cs_option == $cs_default_val ? ' selected="selected"' : '';
                            }
                            $cs_opt_label = $cus_field['options']['label'][$cs_opt_counter];
                            $a_options .= '<option value="' . $cs_option . '"' . $cs_checkd . '>' . $cs_opt_label . '</option>';
                            $cs_opt_counter++;
                        }

                        $cs_html .= '<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                          <label>' . esc_attr($cs_label) . '</label>
                          <div ' . $div_class . '>';
                        $cs_opt_array = array(
                            'std' => '',
                            'cust_name' => '',
                            'cust_id' => '',
                            'id' => '',
                            'classes' => 'chosen-select form-control chosen-select ',
                            'options_markup' => true,
                            'options' => $a_options,
                            'return' => true,
                        );

                        if (isset($cus_field['first_value']) && $cus_field['first_value'] != '') {
                            $cs_opt_array['extra_atr'] = $cs_dr_name . ' ' . $cs_dr_mult . $cs_required . ' data-placeholder="' . $cus_field['first_value'] . '"';
                        } else {
                            $cs_opt_array['extra_atr'] = $cs_dr_name . ' ' . $cs_dr_mult . $cs_required;
                        }

                        $cs_html .= $cs_form_fields2->cs_form_select_render($cs_opt_array);
                        if ($cs_help_txt <> '') {
                            $cs_html .= '<span class="cs-caption">' . $cs_help_txt . '</span>';
                        }
                        $cs_html .= '</div>
					</div>';
                        break;
                    case('date'):
                        $cs_label = isset($cus_field['label']) ? $cus_field['label'] : '';
                        $cs_meta_key = isset($cus_field['meta_key']) ? $cus_field['meta_key'] : '';
                        $cs_default_val = isset($cus_field['default_value']) ? $cus_field['default_value'] : '';
                        $cs_required = isset($cus_field['required']) && $cus_field['required'] == 'yes' ? ' required="required"' : '';
                        $cs_format = isset($cus_field['date_format']) ? $cus_field['date_format'] : 'd-m-Y';
                        $cs_help_txt = isset($cus_field['help']) ? $cus_field['help'] : '';
                        if ($cs_candidate_id != '') {
                            $cs_default_val = get_user_meta((int) $cs_candidate_id, "$cs_meta_key", true);
                        }
                        $cs_html .= '<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                        <script>
                                            jQuery(function(){
                                                jQuery("#' . $cs_meta_key . '").datetimepicker({
                                                    format:"' . $cs_format . '",
                                                    timepicker:false
                                                });
                                            });
                                        </script>
                                            <label>' . esc_attr($cs_label) . '</label>';

                        $cs_opt_array = array(
                            'std' => $cs_default_val,
                            'cust_id' => $cs_meta_key,
                            'cust_name' => 'cs_cus_field[' . esc_attr($cs_meta_key) . ']',
                            'classes' => 'form-control',
                            'return' => true,
                        );

                        if (isset($cus_field['placeholder']) && $cus_field['placeholder'] != '') {
                            $cs_opt_array['extra_atr'] = ' placeholder="' . $cus_field['placeholder'] . '" ' . $cs_required;
                        } else {
                            $cs_opt_array['extra_atr'] = $cs_required;
                        }
                        $cs_html .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

                        if ($cs_help_txt <> '') {
                            $cs_html .= '<span class="cs-caption">' . $cs_help_txt . '</span>';
                        }
                        $cs_html .= '</div>';
                        break;
                    case('email'):
                        $cs_label = isset($cus_field['label']) ? $cus_field['label'] : '';
                        $cs_meta_key = isset($cus_field['meta_key']) ? $cus_field['meta_key'] : '';
                        $cs_default_val = isset($cus_field['default_value']) ? $cus_field['default_value'] : '';
                        $cs_required = isset($cus_field['required']) && $cus_field['required'] == 'yes' ? ' required="required"' : '';
                        $cs_help_txt = isset($cus_field['help']) ? $cus_field['help'] : '';
                        if ($cs_candidate_id != '') {
                            $cs_default_val = get_user_meta((int) $cs_candidate_id, "$cs_meta_key", true);
                        }
                        $cs_html .= '<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
					<label>' . esc_attr($cs_label) . '</label>';

                        $cs_opt_array = array(
                            'std' => $cs_default_val,
                            'cust_id' => $cs_meta_key,
                            'cust_name' => 'cs_cus_field[' . esc_attr($cs_meta_key) . ']',
                            'classes' => 'form-control',
                            'return' => true,
                        );
                        if (isset($cus_field['placeholder']) && $cus_field['placeholder'] != '') {
                            $cs_opt_array['extra_atr'] = ' placeholder="' . $cus_field['placeholder'] . '" ' . $cs_required;
                        } else {
                            $cs_opt_array['extra_atr'] = $cs_required;
                        }

                        $cs_html .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

                        if ($cs_help_txt <> '') {
                            $cs_html .= '<span class="cs-caption">' . $cs_help_txt . '</span>';
                        }
                        $cs_html .= '</div>';
                        break;
                    case('url'):
                        $cs_label = isset($cus_field['label']) ? $cus_field['label'] : '';
                        $cs_meta_key = isset($cus_field['meta_key']) ? $cus_field['meta_key'] : '';
                        $cs_default_val = isset($cus_field['default_value']) ? $cus_field['default_value'] : '';
                        $cs_required = isset($cus_field['required']) && $cus_field['required'] == 'yes' ? ' required="required"' : '';
                        $cs_help_txt = isset($cus_field['help']) ? $cus_field['help'] : '';
                        if ($cs_candidate_id != '') {
                            $cs_default_val = get_user_meta((int) $cs_candidate_id, "$cs_meta_key", true);
                        }
                        $cs_html .= '<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
					<label>' . esc_attr($cs_label) . '</label>';

                        $cs_opt_array = array(
                            'std' => $cs_default_val,
                            'cust_id' => $cs_meta_key,
                            'cust_name' => 'cs_cus_field[' . esc_attr($cs_meta_key) . ']',
                            'classes' => 'form-control',
                            'return' => true,
                        );
                        if (isset($cus_field['placeholder']) && $cus_field['placeholder'] != '') {
                            $cs_opt_array['extra_atr'] = ' placeholder="' . $cus_field['placeholder'] . '" ' . $cs_required;
                        } else {
                            $cs_opt_array['extra_atr'] = $cs_required;
                        }

                        $cs_html .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

                        if ($cs_help_txt <> '') {
                            $cs_html .= '<span class="cs-caption">' . $cs_help_txt . '</span>';
                        }
                        $cs_html .= '</div>';
                        break;
                    case('range'):
                        $cs_label = isset($cus_field['label']) ? $cus_field['label'] : '';
                        $cs_meta_key = isset($cus_field['meta_key']) ? $cus_field['meta_key'] : '';
                        $cs_default_val = isset($cus_field['default_value']) ? $cus_field['default_value'] : '';
                        $cs_required = isset($cus_field['required']) && $cus_field['required'] == 'yes' ? ' required="required"' : '';
                        $cs_help_txt = isset($cus_field['help']) ? $cus_field['help'] : '';
                        if ($cs_candidate_id != '') {
                            $cs_default_val = get_user_meta((int) $cs_candidate_id, "$cs_meta_key", true);
                        }
                        $cs_html .= '<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
					<label>' . esc_attr($cs_label) . '</label>';

                        $cs_opt_array = array(
                            'std' => $cs_default_val,
                            'cust_id' => $cs_meta_key,
                            'cust_name' => 'cs_cus_field[' . esc_attr($cs_meta_key) . ']',
                            'classes' => 'form-control',
                            'return' => true,
                        );
                        if (isset($cus_field['placeholder']) && $cus_field['placeholder'] != '') {
                            $cs_opt_array['extra_atr'] = ' placeholder="' . $cus_field['placeholder'] . '" ' . $cs_required;
                        } else {
                            $cs_opt_array['extra_atr'] = $cs_required;
                        }

                        $cs_html .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

                        if ($cs_help_txt <> '') {
                            $cs_html .= '<span class="cs-caption">' . $cs_help_txt . '</span>';
                        }
                        $cs_html .= '</div>';
                        break;
                }
            }
        }
        return $cs_html;
    }

}

// Start function to add candidate portfolio list

if (!function_exists('cs_add_portfolio_to_candidate_list')) {

    function cs_add_portfolio_to_candidate_list() {
        $reponse = '';
        global $current_user;
        $portfolio_list_id = '';
        $portfolio_list_id = time();
        $portfolio_image_url = '';
        $portfolio_title = '';
        $uid = (isset($uid) and $uid <> '') ? $uid : $current_user->ID;
        if (isset($_POST['cs_image_title'])) {
            $portfolio_title = $_POST['cs_image_title'];
        }
        if ($portfolio_title == '') {
            $reponse['error'] = 1;
            $reponse['message'] = '<span class="error-msg">' . __('Please enter image title', 'jobhunt') . '</span>';
            echo json_encode($reponse);
            exit();
        }
        $portfolio_image_url = cs_candidate_profile_image('cs_portfolio_image_upload');
        // for add only
        // get old all data 
        $cs_get_port_list = get_user_meta($uid, 'cs_port_list_array', true);
        $cs_image_titles = get_user_meta($uid, 'cs_image_title_array', true);
        $cs_image_uploads = get_user_meta($uid, 'cs_image_upload_array', true);
        $cs_get_port_list[] = $portfolio_list_id;
        $cs_image_titles[] = $portfolio_title;
        $cs_image_uploads[] = $portfolio_image_url;
        // update new data 
        update_user_meta($uid, 'cs_port_list_array', $cs_get_port_list);
        update_user_meta($uid, 'cs_image_title_array', $cs_image_titles);
        update_user_meta($uid, 'cs_image_upload_array', $cs_image_uploads);
        $reponse['error'] = 0;
        $reponse['message'] = '<span class="success-msg">' . __('Saved Successfully', 'jobhunt') . '</span>';
        echo json_encode($reponse);
        exit();
    }

    add_action('wp_ajax_cs_add_portfolio_to_candidate_list', 'cs_add_portfolio_to_candidate_list');
}

//  Start function to edit portfolio candidate list

if (!function_exists('cs_edit_portfolio_to_candidate_list')) {

    function cs_edit_portfolio_to_candidate_list() {
        global $current_user;
        $portfolio_list_id = '';
        $reponse = '';
        $flag_id = '';
        if (isset($_POST['flag_id'])) {
            $flag_id = $_POST['flag_id'];
        }
        if (isset($_POST['cs_port_list_array' . $flag_id])) {
            $portfolio_list_id = $_POST['cs_port_list_array' . $flag_id];
        }
        $portfolio_image_url = '';
        $portfolio_title = '';

        $uid = (isset($uid) and $uid <> '') ? $uid : $current_user->ID;
        if (isset($_POST['cs_image_title' . $flag_id])) {
            $portfolio_title = $_POST['cs_image_title' . $flag_id];
        }
        if ($portfolio_title == '') {
            $reponse['error'] = 1;
            $reponse['message'] = '<span class="error-msg">' . __('Please enter image title', 'jobhunt') . '</span>';
            echo json_encode($reponse);
            exit();
        }
        $portfolio_image_url = cs_candidate_profile_image('cs_portfolio_image_upload');
        if ($portfolio_image_url == '') {
            $portfolio_image_url = $_POST['media_img' . $flag_id];
        }


        // for edit only
        $cs_get_port_list = get_user_meta($uid, 'cs_port_list_array', true);
        $finded_key = array_search($portfolio_list_id, $cs_get_port_list);
        if ($finded_key >= 0) {

            $cs_get_port_list = get_user_meta($uid, 'cs_port_list_array', true);
            $cs_image_titles = get_user_meta($uid, 'cs_image_title_array', true);
            $cs_image_uploads = get_user_meta($uid, 'cs_image_upload_array', true);
            $cs_get_port_list[$finded_key] = $portfolio_list_id;
            $cs_image_titles[$finded_key] = $portfolio_title;
            $cs_image_uploads[$finded_key] = $portfolio_image_url;
            update_user_meta($uid, 'cs_port_list_array', $cs_get_port_list);
            update_user_meta($uid, 'cs_image_title_array', $cs_image_titles);
            update_user_meta($uid, 'cs_image_upload_array', $cs_image_uploads);
        }
        $reponse['error'] = 0;
        $reponse['message'] = '<span class="success-msg">' . __('Saved Successfully', 'jobhunt') . '</span>';
        echo json_encode($reponse);
        exit();
    }

    add_action('wp_ajax_cs_edit_portfolio_to_candidate_list', 'cs_edit_portfolio_to_candidate_list');
}

if (!function_exists('cs_candidate_skills_set_array')) {

    function cs_candidate_skills_set_array() {

        $job_custom_fields = get_option("cs_candidate_cus_fields");

        $skills_array = array(
            'display_name' => array(
                'name' => __('Full Name', 'jobhunt'),
            ),
            'user_img' => array(
                'name' => __('Profile Image', 'jobhunt'),
            ),
            'cover_user_img' => array(
                'name' => __('Cover Image', 'jobhunt'),
            ),
            'cs_job_title' => array(
                'name' => __('Job Title', 'jobhunt'),
            ),
            'cs_minimum_salary' => array(
                'name' => __('Minimum Salary', 'jobhunt'),
            ),
            'cs_specialisms' => array(
                'name' => __('Specialisms', 'jobhunt'),
            ),
            'description' => array(
                'name' => __('Description', 'jobhunt'),
            ),
            'social_network' => array(
                'name' => __('Social Network', 'jobhunt'),
                'list' => array(
                    'cs_facebook' => array(
                        'name' => __('Facebook', 'jobhunt'),
                    ),
                    'cs_twitter' => array(
                        'name' => __('Twitter', 'jobhunt'),
                    ),
                    'cs_google_plus' => array(
                        'name' => __('Google Plus', 'jobhunt'),
                    ),
                    'cs_linkedin' => array(
                        'name' => __('Linkedin', 'jobhunt'),
                    ),
                ),
            ),
            'contact_info' => array(
                'name' => __('Contact Information', 'jobhunt'),
                'list' => array(
                    'cs_phone_number' => array(
                        'name' => __('Phone Number', 'jobhunt'),
                    ),
                    'user_email' => array(
                        'name' => __('Email', 'jobhunt'),
                    ),
                    'user_url' => array(
                        'name' => __('Website', 'jobhunt'),
                    ),
                    'cs_post_loc_country' => array(
                        'name' => __('Country', 'jobhunt'),
                    ),
                    'cs_post_loc_city' => array(
                        'name' => __('City', 'jobhunt'),
                    ),
                    'cs_post_comp_address' => array(
                        'name' => __('Complete Address', 'jobhunt'),
                    ),
                ),
            ),
            'resume' => array(
                'name' => __('Resume', 'jobhunt'),
                'list' => array(
                    'cs_edu_title_array' => array(
                        'name' => __('Education', 'jobhunt'),
                    ),
                    'cs_exp_title_array' => array(
                        'name' => __('Experience', 'jobhunt'),
                    ),
                    'cs_port_list_array' => array(
                        'name' => __('Portfolio', 'jobhunt'),
                    ),
                    'cs_skills_list_array' => array(
                        'name' => __('Skills', 'jobhunt'),
                    ),
                    'cs_award_list_array' => array(
                        'name' => __('Honors & Awards', 'jobhunt'),
                    ),
                ),
            ),
            'cv_cover_letter' => array(
                'name' => __('CV & Cover Letter', 'jobhunt'),
                'list' => array(
                    'cs_candidate_cv' => array(
                        'name' => __('CV', 'jobhunt'),
                    ),
                    'cs_cover_letter' => array(
                        'name' => __('Cover Letter', 'jobhunt'),
                    ),
                ),
            ),
        );

        if (is_array($job_custom_fields) && sizeof($job_custom_fields) > 0) {

            $custom_fields_array = array();
            $skills_array['custom_fields']['name'] = __('Custom Fields', 'jobhunt');
            foreach ($job_custom_fields as $job_field) {

                $meta_key = isset($job_field['meta_key']) ? $job_field['meta_key'] : '';
                $field_label = isset($job_field['label']) ? $job_field['label'] : '';
                if ($meta_key != '' && $field_label != '') {
                    $custom_fields_array[$meta_key] = array(
                        'name' => $field_label,
                    );
                }
            }
            $skills_array['custom_fields']['list'] = $custom_fields_array;
            if (empty($custom_fields_array)) {
                unset($skills_array['custom_fields']);
            }
        }

        return $skills_array;
    }

}

if (!function_exists('cs_skill_option_key')) {

    function cs_skill_option_key($key = '') {

        $opt_id = str_replace('cs_', '', $key) . '_skill';
        $opt_id = 'cs_' . $opt_id;
        return $opt_id;
    }

}

if (!function_exists('cs_candidate_skills_calculation')) {
    function cs_candidate_skills_calculation($user = '') {

        global $cs_plugin_options;

        if (is_object($user)) {
            // Getting all skills with percentage
            $skills_array = cs_candidate_skills_set_array();

            // Calculating Skills percentage
            $total_percentage = 0;
            $msg = '';
            if (is_array($skills_array) && sizeof($skills_array) > 0) {

                foreach ($skills_array as $skill_key => $skill_set) {

                    if ($skill_key == 'display_name' || $skill_key == 'description') {
                        $skill_value = isset($user->$skill_key) ? $user->$skill_key : '';
                        $skill_name = isset($skill_set['name']) ? $skill_set['name'] : '';
                        $skill_option_key = cs_skill_option_key($skill_key);
                        $skill_percentage = isset($cs_plugin_options[$skill_option_key]) ? $cs_plugin_options[$skill_option_key] : 0;
                        if (!empty($skill_value)) {
                            $total_percentage += $skill_percentage;
                        } else {
                            if ($msg == '' && $skill_percentage > 0) {
                                $msg = sprintf(__('Put value for "%s" field to increase your skill up to "%s"'), $skill_name, $skill_percentage . '%');
                            }
                        }
                    } else if (array_key_exists('list', $skill_set) && is_array($skill_set['list'])) {
                        foreach ($skill_set['list'] as $skill_list_key => $skill_list_set) {

                            $skill_option_key = cs_skill_option_key($skill_list_key);
                            $skill_percentage = isset($cs_plugin_options[$skill_option_key]) ? $cs_plugin_options[$skill_option_key] : 0;
                            $skill_name = isset($skill_list_set['name']) ? $skill_list_set['name'] : '';
                            if ($skill_list_key == 'user_email' || $skill_list_key == 'user_url') {
                                $skill_value = isset($user->$skill_list_key) ? $user->$skill_list_key : '';
                                if (!empty($skill_value)) {
                                    $total_percentage += $skill_percentage;
                                } else {
                                    if ($msg == '' && $skill_percentage > 0) {
                                        $msg = sprintf(__('Put value for "%s" field to increase your skill up to "%s"'), $skill_name, $skill_percentage . '%');
                                    }
                                }
                            } else {
                                $skill_value = get_user_meta($user->ID, $skill_list_key, true);
                                if (!empty($skill_value)) {
                                    $total_percentage += $skill_percentage;
                                } else {
                                    if ($msg == '' && $skill_percentage > 0) {
                                        $msg = sprintf(__('Put value for "%s" field to increase your skill up to "%s"'), $skill_name, $skill_percentage . '%');
                                    }
                                }
                            }
                        }
                    } else {
                        $skill_value = get_user_meta($user->ID, $skill_key, true);
                        $skill_name = isset($skill_set['name']) ? $skill_set['name'] : '';
                        $skill_option_key = cs_skill_option_key($skill_key);
                        $skill_percentage = isset($cs_plugin_options[$skill_option_key]) ? $cs_plugin_options[$skill_option_key] : 0;
                        if (!empty($skill_value)) {
                            $total_percentage += $skill_percentage;
                        } else {
                            if ($msg == '' && $skill_percentage > 0) {
                                $msg = sprintf(__('Put value for "%s" field to increase your skill up to "%s"'), $skill_name, $skill_percentage . '%');
                            }
                        }
                    }
                }
            }

            // Updating user Skills Percentage
            update_user_meta($user->ID, 'cs_candidate_skills_percentage', $total_percentage);

            $return_array = array(
                'percentage' => $total_percentage,
                'msg' => $msg,
            );

            return $return_array;
        }
    }

}
