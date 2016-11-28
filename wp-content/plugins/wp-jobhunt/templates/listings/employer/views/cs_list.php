<?php
/**
 * Employer Simple 3Columns
 *
 */
?>
<div class="cs-company-listing <?php echo cs_allow_special_char($main_col); ?>">
    <?php
    global $post;
    include plugin_dir_path(__FILE__) . '../employer-search-keywords.php';

    if (isset($atts['cs_employer_title']) && $atts['cs_employer_title'] != '') {
        echo '<div class="cs-element-title"><h2>';
        echo esc_html($atts['cs_employer_title']);
        echo '</h2>';
        if (isset($atts['cs_employer_sub_title']) && $atts['cs_employer_sub_title'] != '') {
            echo '<span>' . esc_html($atts['cs_employer_sub_title']) . '</span>';
        }
        echo '</div>';
    }

    if ($a['cs_employer_searchbox_top'] == 'yes') {
        include plugin_dir_path(__FILE__) . '../employer-top-view-search.php';
    }
    ?>
    <div class="row">
        <div class="mas-isotope">
            <?php
            $new_char = '';
            $old_char = '';

            if ($count_post > 0) {
                $loop = new WP_User_Query($args);
                $flag = 1;
                if (!empty($loop->results)) {
                    foreach ($loop->results as $cs_user) {
                        $cs_employee_address = get_user_address_string_for_list($cs_user->ID);
                        $cs_employee_web_http = $cs_user->user_url;
                        $cs_employee_web = preg_replace('#^https?://#', '', $cs_employee_web_http);
                        $cs_employee_employer_img = get_user_meta($cs_user->ID, 'user_img', true);
                        $cs_employee_employer_img = cs_get_img_url($cs_employee_employer_img, 'cs_media_5');
                        if (!cs_image_exist($cs_employee_employer_img) || $cs_employee_employer_img == "") {
                            $cs_employee_employer_img = esc_url(wp_jobhunt::plugin_url() . 'assets/images/img-not-found16x9.jpg');
                        }
                        $cs_employee_emp_username = $cs_user->ID;
                        $emp_jobpost = array('posts_per_page' => "-1", 'post_type' => 'jobs', 'order' => "DESC", 'orderby' => 'post_date',
                            'post_status' => 'publish', 'ignore_sticky_posts' => 1,
                            'meta_query' => array(
                                array(
                                    'key' => 'cs_job_username',
                                    'value' => $cs_employee_emp_username,
                                    'compare' => '=',
                                ),
                            )
                        );
                        $loop_job_count = new WP_Query($emp_jobpost);
                        $count_job_post = $loop_job_count->post_count;

                        $employer_post_title = $cs_user->display_name;
                        if (isset($employer_post_title)) {
                            $employer_post_title_char = substr($employer_post_title, 0, 1);
                            $new_char = strtoupper($employer_post_title_char);
                        }
                        if (!preg_match('/[a-zA-Z]/', $new_char)) {
                            $new_char = "#";
                        }

                        if ($new_char != $old_char) {
                            if ($old_char != '')
                                echo '</ul></div>';
                            ?>
                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                <strong class="search-title"><span><?php echo esc_html($new_char); ?></span></strong>
                                <ul>

                                    <?php
                                }
                                ?>
                                <li>
                                    <a href="<?php echo esc_url(get_author_posts_url($cs_user->ID)); ?>"><?php echo esc_html($employer_post_title); ?></a> 
                                </li>
                                <?php
                                $flag++;
                                $old_char = $new_char;
                            }
                        }
                    } else {
                        echo '<ul><li class="ln-no-match">';
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
                        echo '</li></ul>';
                    }
                    ?>
                </ul></div>
        </div>

        <?php
        //==Pagination Start
        if ((isset($users_per_page) && $count_post > $users_per_page && $users_per_page > 0) && $a['cs_employer_show_pagination'] == 'pagination') {
            echo '<nav>';
            cs_user_pagination($total_pages, $page);
            echo '</nav>';
        }//==Pagination End 
        ?>

    </div>
</div>