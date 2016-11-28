<?php
/**
 * Employer 4Columns
 *
 */
?>
<section class="cs-company-listing <?php echo cs_allow_special_char($main_col); ?>">
    <?php
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
    $box_size = 'col-lg-3 col-md-3 col-sm-6 col-xs-6';
    if (isset($atts['cs_employer_boxsize']) && $atts['cs_employer_boxsize'] != '') {
        $cs_employer_boxsize = $atts['cs_employer_boxsize'];
        $cs_employer_boxsize_larg = $cs_employer_boxsize < 6 ? 6 : '12';
        $box_size = 'col-lg-' . $cs_employer_boxsize . ' col-md-' . $cs_employer_boxsize . ' col-sm-' . $cs_employer_boxsize_larg . ' col-xs-' . $cs_employer_boxsize_larg;
    }
    ?>
    <ul class="employers-listing grid row">
        <?php
        // getting if record not found
        if ($count_post > 0) {
            // getting job with page number

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
                    ?>
                    <li class="<?php echo esc_html($box_size); ?>">
                        <div class="cs-media">
                            <figure>
                                <a href="<?php echo esc_url(get_author_posts_url($cs_user->ID)); ?>"><img alt="" src="<?php echo esc_url($cs_employee_employer_img); ?>"></a>
                            </figure>
                        </div>
                        <div class="cs-text cs-bgcolor">
                            <div class="text-inner">
                                <span><?php echo $cs_user->display_name ?></span>
                                <a href="<?php echo esc_url(get_author_posts_url($cs_user->ID)); ?>"><?php
                                    echo esc_html($count_job_post);
                                    _e(" Jobs", 'jobhunt')
                                    ?></a>
                            </div>
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
    if ((isset($users_per_page) && $count_post > $users_per_page && $users_per_page > 0) && $a['cs_employer_show_pagination'] == 'pagination') {
        echo '<nav>';
        cs_user_pagination($total_pages, $page);
        echo '</nav>';
    }//==Pagination End 
    ?>
</section>