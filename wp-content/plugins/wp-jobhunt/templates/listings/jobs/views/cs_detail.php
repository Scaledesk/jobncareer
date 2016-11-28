<?php
/**
 * Jobs detail list
 *
 */
global $wpdb;
$main_col = '';
if ($a['cs_job_searchbox'] == 'yes') {
    $main_col = 'col-lg-9 col-md-9 col-sm-12 col-xs-12';
}
?>
<div class="hiring-holder <?php echo cs_allow_special_char($main_col); ?>">
    <?php
    include plugin_dir_path(__FILE__) . '../jobs-search-keywords.php';

    if ((isset($a['cs_job_title']) && $a['cs_job_title'] != '') || (isset($a['cs_job_top_search']) && $a['cs_job_top_search'] != "None" && $a['cs_job_top_search'] <> '')) {
        echo ' <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"> <div class="row">';
        // section title
        if (isset($a['cs_job_title']) && $a['cs_job_title'] != '') {
            echo '<div class="cs-element-title"><h2>' . $a['cs_job_title'] . '</h2>';
            if (isset($a['cs_job_sub_title']) && $a['cs_job_sub_title'] != '') {
                echo '<span>' . $a['cs_job_sub_title'] . '</span>';
            }
            echo '</div>';
        }
        // sub title with total rec 
        if (isset($a['cs_job_top_search']) && $a['cs_job_top_search'] != "None" && $a['cs_job_top_search'] <> '') {

            if (isset($a['cs_job_top_search']) and $a['cs_job_top_search'] == "total_records") {
                echo '<h2>';
                ?><span class="result-count"><?php if (isset($count_post) && $count_post != '') echo esc_html($count_post) . " "; ?></span><?php
                if (isset($specialisms) && is_array($specialisms)) {
                    echo get_specialism_headings($specialisms);
                } else {

                    echo __("Jobs & Vacancies", "jobhunt");
                }
                echo "</h2>";
            } else if (isset($a['cs_job_top_search']) and $a['cs_job_top_search'] == "Filters") {
                include plugin_dir_path(__FILE__) . '../jobs-sort-filters.php';
            }
        }
        echo '</div></div>';
    }
    ?>
    <ul class="jobs-listing simple">
        <?php
        
        global $cs_plugin_options;
        $cs_search_result_page = isset($cs_plugin_options['cs_search_result_page']) ? $cs_plugin_options['cs_search_result_page'] : '';

        $loop = new WP_Query($args);
        $found_posts = $loop->found_posts;
        
        // getting if record not found
        if( $loop->have_posts()){
            $flag = 1;
            while ($loop->have_posts()) : $loop->the_post();
                global $post;

                $job_post = $post;
                $cs_job_posted = get_post_meta($post->ID, 'cs_job_posted', true);

                $cs_jobs_address = get_user_address_string_for_list($post->ID);
                $cs_jobs_thumb_url = ''; //get_post_meta($post->ID, 'job_img', true);
                // get employer images at run time
                $cs_job_employer = get_post_meta($post->ID, "cs_job_username", true); //
                $employer_img = get_the_author_meta('user_img', $cs_job_employer);
                //$cs_jobs_address = get_user_address_string_for_list($cs_job_employer, 'usermeta');
                if ($employer_img != '') {
                    $cs_jobs_thumb_url = cs_get_img_url($employer_img, 'cs_media_5');
                }
//                $cs_jobs_thumb_url = get_post_meta($post->ID, 'job_img', true);
//                $cs_jobs_thumb_url = cs_get_img_url($cs_jobs_thumb_url, 'cs_media_5');
                if (!cs_image_exist($cs_jobs_thumb_url) || $cs_jobs_thumb_url == "") {
                    $cs_jobs_thumb_url = esc_url(wp_jobhunt::plugin_url() . 'assets/images/img-not-found16x9.jpg');
                }
                $cs_job_featured = get_post_meta($post->ID, 'cs_job_featured', true);
                // get all job types
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
                ?>
                <li>	<div class="jobs-content">
                        <div class="cs-media">
                            <?php if (isset($cs_jobs_thumb_url) and $cs_jobs_thumb_url <> '') { ?>
                                <figure><a class="hiring-detail-img" href="<?php echo esc_url(get_permalink($post->ID)); ?>"><img src="<?php echo esc_url($cs_jobs_thumb_url); ?>" alt=""></a></figure>
                            <?php } ?>
                            <div class="list-options">
                                <?php
                                echo '<div class="cs-shortlist">';
                                if (!is_user_logged_in() || !$login_user_is_employer_flag) {

                                    if (is_user_logged_in()) {
                                        $user = cs_get_user_id();

                                        $finded_result_list = cs_find_index_user_meta_list($post->ID, 'cs-user-jobs-wishlist', 'post_id', cs_get_user_id());
                                        if (isset($user) and $user <> '' and is_user_logged_in()) {
                                            if (is_array($finded_result_list) && !empty($finded_result_list)) {
                                                ?>
                                                <a class="cs-color whishlist_icon shortlist" href="javascript:void(0)"  data-toggle="tooltip" data-placement="top" title="<?php _e("Added to Shortlist", "jobhunt"); ?>" id="<?php echo 'addjobs_to_wishlist' . intval($post->ID); ?>" onclick="cs_removejobs_to('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', '<?php echo absint($post->ID); ?>', this)" ><i class="icon-heart6"></i>
                                                    <?php _e('Shortlisted', 'jobhunt'); ?>
                                                </a> 
                                                <?php
                                            } else {
                                                ?>
                                                <a class="cs-color whishlist_icon shortlist" href="javascript:void(0)"  data-toggle="tooltip" data-placement="top" title="<?php _e("Add to Shortlist", "jobhunt"); ?>" id="<?php echo 'addjobs_to_wishlist' . intval($post->ID); ?>" onclick="cs_addjobs_to_wish('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', '<?php echo absint($post->ID); ?>', this)" ><i class="icon-heart-o"></i> <?php _e('Shortlist', 'jobhunt'); ?></a> 
                                                <?php
                                            }
                                        } else {
                                            ?>
                                            <a class="cs-color whishlist_icon shortlist" href="javascript:void(0)"  data-toggle="tooltip" data-placement="top" title="<?php _e("Add to Shortlist", "jobhunt"); ?>" id="<?php echo 'addjobs_to_wishlist' . intval($post->ID); ?>" onclick="cs_addjobs_to_wish('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', '<?php echo absint($post->ID); ?>', this)" ><i class="icon-heart-o">

                                                    <?php _e('Shortlist', 'jobhunt'); ?></i>
                                            </a> 	
                                            <?php
                                        }
                                    } else {
                                        ?>
                                        <button type="button" class="cs-color heart-btn shortlist" data-toggle="tooltip" data-placement="top" title="<?php _e("Add to Shortlist", "jobhunt"); ?>" onclick="trigger_func('#btn-header-main-login');"><i class='icon-heart-o'></i></button> <?php _e('Shortlist', 'jobhunt'); ?>
                                        <?php
                                    }
                                } else {
                                    ?>
                                    <a class="cs-color whishlist_icon shortlist" href="javascript:void(0)"  data-toggle="tooltip" data-placement="top" title="<?php _e("Add to Shortlist", "jobhunt"); ?>" id="<?php echo 'addjobs_to_wishlist' . intval($post->ID); ?>" onclick="show_alert_msg('<?php echo __("Become a candidate then try again.", "jobhunt"); ?>')" ><i class="icon-heart-o"></i></a>
                                    <?php
                                }
                                echo '</div>';
                                ?>
                            </div>
                        </div>
                        <div class="cs-text">
                            <div class="cs-post-title">
                                <h3><a href="<?php echo esc_url(get_permalink($post->ID)); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h3>
                            </div>
                            <?php if ($cs_job_posted <> '') { ?><span><?php echo __("Posted:", "jobhunt") . " " . cs_time_elapsed_string($cs_job_posted); ?></span><?php } ?>
                            <?php if ($cs_job_featured == "yes" || $cs_job_featured == 'on') { ?>     
                                <span class="listing-featered"><?php _e('FEATURED', 'jobhunt'); ?></span>
                            <?php } ?>
                            <ul class="payment-detail">
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
                                                    ?>  <li> 
                                                        <?php
                                                        if (isset($cus_field['fontawsome_icon']) && $cus_field['fontawsome_icon'] != '') {
                                                            echo '<i class="' . $cus_field['fontawsome_icon'] . '"></i>';
                                                        }
                                                        ?>

                                                        <?php echo esc_html($cus_field['label']); ?>
                                                        <span> <?php
                                                            // check the data is array or not
                                                            if (is_array($data)) {
                                                                $data_flage = 1;
                                                                foreach ($data as $datavalue) {
                                                                    echo esc_html($datavalue);
                                                                    if ($data_flage != count($data)) {
                                                                        echo "";
                                                                    }
                                                                    $data_flage++;
                                                                }
                                                            } else {
                                                                echo esc_html($data);
                                                            }
                                                            ?> </span>
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
                            <p><?php echo wp_trim_words($post->post_content, 30, '...'); ?></p>
                        </div>
                    </div>
                </li><?php
                $flag++;
            endwhile;
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
    if ($found_posts > $cs_blog_num_post && $cs_blog_num_post > 0 && $a['cs_job_show_pagination'] == "pagination") {
        echo '<nav>';
        echo cs_pagination($found_posts, $cs_blog_num_post, $qrystr, 'Show Pagination', 'page_job');
        echo ' </nav>';
    }//==Pagination End 
    ?>
</div>
