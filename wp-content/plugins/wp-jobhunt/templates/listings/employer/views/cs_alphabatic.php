<?php
/**
 * Employer alpha 1Column
 *
 */
?>
<section class = "cs-job-possitions <?php echo cs_allow_special_char($main_col); ?>">

    <?php
    global $post;
    include plugin_dir_path(__FILE__) . '../employer-search-keywords.php';

    if ($a['cs_employer_searchbox'] != 'yes') {
        ?>
        <div class="row">
            <?php
        }
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
        <div class="employer-listing">
            <ul>
                <?php
                $old_char = '';
                $new_char = '';

                if ($count_post > 0) {
                    $loop = new WP_User_Query($args);

                    $loop_count = $loop->total_users;

                    $cs_job_posted_date_formate = 'd-m-Y H:i:s';
                    $cs_job_expired_date_formate = 'd-m-Y H:i:s';
                    //echo '<li>';
                    $flag = 0;
                    if (!empty($loop->results)) {
                        foreach ($loop->results as $cs_user) {
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
                                            $specialism_html .= '<span class="cs-single-specialism" >' . $specialismsitem->name . '</span>';
                                        } else {
                                            $specialism_html .= '<span class="cs-single-specialism" >,' . $specialismsitem->name . '</span>';
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

                            $cs_employee_address = get_user_address_string_for_list($cs_user->ID, 'usermeta');
                            $cs_employee_web_http = $cs_user->user_url;
                            $cs_employee_web = preg_replace('#^https?://#', '', $cs_employee_web_http);
                            $cs_employee_employer_img = get_user_meta($cs_user->ID, 'user_img', true);
                            $cs_employee_employer_img = cs_get_img_url($cs_employee_employer_img, 'cs_media_5');
                            if (!cs_image_exist($cs_employee_employer_img) || $cs_employee_employer_img == "") {
                                $cs_employee_employer_img = esc_url(wp_jobhunt::plugin_url() . 'assets/images/img-not-found16x9.jpg');
                            }
                            $cs_employee_emp_username = $cs_user->ID;

                            $cs_post_loc_latitude = get_user_meta($cs_user->ID, 'cs_post_loc_latitude', true);

                            $cs_post_loc_longitude = get_user_meta($cs_user->ID, 'cs_post_loc_longitude', true);

                            $emp_jobpost = array('posts_per_page' => "-1", 'post_type' => 'jobs', 'order' => "DESC", 'orderby' => 'post_date',
                                'post_status' => 'publish', 'ignore_sticky_posts' => 1,
                                'meta_query' => array(
                                    array(
                                        'key' => 'cs_job_username',
                                        'value' => $cs_employee_emp_username,
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
                            ##########################
                            if ($new_char != $old_char) {
                                if ($flag != 0) {
                                    echo ' </div></li> <li> ';
                                }

                                if ($flag == 0) {
                                    echo '<li>';
                                }
                                ?>
                                <span><?php echo esc_html($new_char); ?></span>
                                <?php
                                echo '<div class="employer-box">';
                            }
                            ?>
                            <div class="employer-inner">
                                <div class="cs-media">
                                    <figure>
                                        <a href="<?php echo get_author_posts_url($cs_user->ID) ?>"><img src="<?php echo esc_url($cs_employee_employer_img); ?>" alt="image"></a>
                                    </figure>
                                </div>
                                <div class="cs-text">
                                    <div class="cs-post-title">
                                        <h3><a href="<?php echo get_author_posts_url($cs_user->ID) ?>"><?php echo $cs_user->display_name ?></a></h3>
                                        <small><?php echo esc_html($count_job_post) . "  " . __('Job(s)', 'jobhunt'); ?></small>
                                    </div>
                                    <span class="cs-color cs-single-specialism">
                                        <?php
                                        $cs_spcialist = str_replace('-', ' ', $specialisms_values);
                                        echo esc_html(ucwords($cs_spcialist));
                                        ?>
                                    </span>
                                    <ul class="post-options">
                                        <li>
                                            <?php echo esc_html($cs_employee_address); ?>
                                        </li>
                                    </ul>
                                </div>
                                <div class="company-info">
                                    <ul>
                                        <?php
                                        $cs_employer_cus_fields = get_option("cs_employer_cus_fields");
                                        if (is_array($cs_employer_cus_fields) && sizeof($cs_employer_cus_fields) > 0) {
                                            //echo '<li>';
                                            $custom_field_box = 1;
                                            $csum = 0;
                                            foreach ($cs_employer_cus_fields as $cus_field) {

                                                if ($cus_field['meta_key'] != '') {
                                                    $data = get_user_meta($cs_user->ID, $cus_field['meta_key'], true);
                                                    // empty check of value
                                                    if ($cus_field['label'] != '') {
                                                        if ($data != "") {
                                                            echo '<li>';
                                                            if (isset($cus_field['fontawsome_icon']) && $cus_field['fontawsome_icon'] != '') {
                                                                echo '<i class="' . $cus_field['fontawsome_icon'] . '"></i>';
                                                            }
                                                            echo esc_html($cus_field['label']);
                                                            ?> <span><?php
                                                                // check the data is array or not
                                                                if (is_array($data)) {
                                                                    $data_flage = 1;
                                                                    foreach ($data as $datavalue) {


                                                                        echo esc_html($datavalue);
                                                                        if ($data_flage != count($data)) {
                                                                            echo ", ";
                                                                        }
                                                                        $data_flage++;
                                                                    }
                                                                } else {

                                                                    echo esc_html($data);
                                                                }
                                                                //                                                            
                                                                ?></span><?php
                                                            echo '</li>';
                                                            $custom_field_box++;
                                                        }
                                                    }
                                                }
                                                if ($csum == 1) {
                                                    break;
                                                }
                                                $csum ++;
                                            }
                                        }
                                        ?>

                                    </ul>
                                </div>
                            </div>
                            <?php
                            $old_char = $new_char;

                            $flag++;
                        }
                    }
                    echo '</div></li>';
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
        </div>
        <?php
        if ((isset($users_per_page) && $count_post > $users_per_page && $users_per_page > 0) && $a['cs_employer_show_pagination'] == 'pagination') {
            echo '<nav>';
            cs_user_pagination($total_pages, $page);
            echo '</nav>';
        }
        if ($a['cs_employer_searchbox'] != 'yes') {
            ?>
        </div>

    <?php } ?>
</section>