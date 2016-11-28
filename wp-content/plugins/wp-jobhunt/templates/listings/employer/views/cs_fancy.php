<?php
/**
 * Employer 3Columns
 *
 */
?>
<section class="recriutment-listing <?php echo($main_col); ?>">
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
    <div class="cs-employer-slide-listing">
        <div class="cs-employer-fancy row">
            <?php
            // getting if record not found
            if ($count_post > 0) {
                // getting job with page number
                $loop = new WP_User_Query($args);
                $flag = 1;
                if (!empty($loop->results)) {
                    foreach ($loop->results as $cs_user) {
                        $cs_employee_address = get_user_address_string_for_list($cs_user->ID, 'usermeta');
                        $cs_employee_web_http = $cs_user->user_url;
                        $cs_employee_web = preg_replace('#^https?://#', '', $cs_employee_web_http);
                        $cs_employee_emp_username = $cs_user->ID;
                        $cs_employee_comp_name = $cs_user->display_name;
                        $cs_employee_employer_img = get_user_meta($cs_user->ID, 'user_img', true);
                        $cs_employee_employer_img = cs_get_img_url($cs_employee_employer_img, 'cs_media_5');
                        if (!cs_image_exist($cs_employee_employer_img) || $cs_employee_employer_img == "") {
                            $cs_employee_employer_img = esc_url(wp_jobhunt::plugin_url() . 'assets/images/img-not-found16x9.jpg');
                        }
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
                                        $specialism_html .= '<span><a>' . $specialismsitem->name . '</a></span>';
                                    } else {
                                        $specialism_html .= '<span> <a>' . $specialismsitem->name . '</a></span>';
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
                        ?>
                        <div class="<?php echo esc_html($box_size); ?>">
                            <a href="<?php echo get_author_posts_url($cs_user->ID) ?>"></a>
                            <div class="cs-media">
                                <figure><img src="<?php echo esc_url($cs_employee_employer_img); ?>" alt="" /></figure>
                                <figcaption></figcaption>
                            </div>
                            <span><?php echo esc_html($cs_employee_comp_name) ?></span> <em>(<?php printf(__('%s Jobs', 'jobhunt'), absint($count_job_post)) ?>)</em>
                        </div>
                        <?php
                        $flag++;
                    }
                }
            } else {
                echo '<div class="ln-no-match">';
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
                echo '</div>';
            }
            ?>
        </div>
    </div>
    <?php
    //==Pagination Start
    if ((isset($users_per_page) && $count_post > $users_per_page && $users_per_page > 0) && $a['cs_employer_show_pagination'] == 'pagination') {
        echo '<nav>';
        cs_user_pagination($total_pages, $page);
        echo '</nav>';
    }//==Pagination End 
    ?>
</section>