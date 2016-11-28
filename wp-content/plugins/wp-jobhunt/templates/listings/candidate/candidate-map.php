<?php
if ($cs_candidate_map == 'yes') {
    // getting job with page number

    $cs_candidates = $cs_candidates_array = array();
    if (!empty($loop_count->results)) {
        foreach ($loop_count->results as $cs_user) {
            $cs_can_latitude = get_user_meta($cs_user->ID, "cs_post_loc_latitude", true);
            $cs_can_longitude = get_user_meta($cs_user->ID, "cs_post_loc_longitude", true);

            $cs_jobs_thumb_url = get_user_meta($cs_user->ID, 'user_img', true);
            $cs_jobs_thumb_url = cs_get_img_url($cs_jobs_thumb_url, 'cs_media_4');

            $cs_ext = pathinfo($cs_jobs_thumb_url, PATHINFO_EXTENSION);

            if ($cs_jobs_thumb_url != '' && $cs_ext != '') {
                $cs_cand_thumb_url = esc_url($cs_jobs_thumb_url);
            } else {
                $cs_cand_thumb_url = esc_url(wp_jobhunt::plugin_url() . 'assets/images/candidate-no-image.jpg');
            }

            $cs_get_exp_list = get_user_meta($cs_user->ID, 'cs_exp_list_array', true);
            $cs_exp_titles = get_user_meta($cs_user->ID, 'cs_exp_title_array', true);
            $cs_exp_from_dates = get_user_meta($cs_user->ID, 'cs_exp_from_date_array', true);
            $cs_exp_to_dates = get_user_meta($cs_user->ID, 'cs_exp_to_date_array', true);
            $cs_exp_companys = get_user_meta($cs_user->ID, 'cs_exp_company_array', true);

            $recent_exp_company = '';
            $recent_exp_title = '';
            if (isset($cs_get_exp_list) && is_array($cs_get_exp_list) && count($cs_get_exp_list) > 0) {
                $required_index = find_heighest_date_index($cs_exp_to_dates, 'd-m-Y');
                $recent_exp_company = isset($cs_exp_companys[$required_index]) ? $cs_exp_companys[$required_index] : '';
                $recent_exp_title = isset($cs_exp_titles[$required_index]) ? $cs_exp_titles[$required_index] : '';
            }

            $cs_post_loc_city = get_user_meta($cs_user->ID, 'cs_post_loc_city', true);
            $cs_post_loc_country = get_user_meta($cs_user->ID, 'cs_post_loc_country', true);

            $cs_candidates[] = array(
                'post_id' => $cs_user->ID,
                'post_title' => $cs_user->display_name,
                'permalink' => esc_url(get_author_posts_url($cs_user->ID)),
                'latitude' => $cs_can_latitude,
                'longitude' => $cs_can_longitude,
                'image_url' => $cs_cand_thumb_url,
                'position' => $recent_exp_title,
                'company' => $recent_exp_company,
                'city' => $cs_post_loc_city,
                'country' => $cs_post_loc_country,
            );
        }
    }

    $cs_candidates_array['posts'] = $cs_candidates;
    $cs_json_array = json_encode($cs_candidates_array);

    $cs_latitude = $cs_candidate_map_lat;
    $cs_longitude = $cs_candidate_map_long;
    $cs_map_zoom = $cs_candidate_map_zoom;
    if ($cs_candidate_map_zoom != '' && $cs_candidate_map_zoom != '' && $cs_candidate_map_zoom != '') {
        wp_jobhunt::cs_googlemapcluster_scripts();

        $cs_map_cluster_icon = isset($cs_plugin_options['cs_cs_map_cluster_icon']) ? $cs_plugin_options['cs_cs_map_cluster_icon'] : wp_jobhunt::plugin_url() . 'assets/images/culster-icon.png';
        $cs_map_marker_icon = isset($cs_plugin_options['cs_cs_map_marker_icon']) ? $cs_plugin_options['cs_cs_map_marker_icon'] : wp_jobhunt::plugin_url() . 'assets/images/map-marker.png';

        $cs_map_cluster_color = isset($cs_plugin_options['cs_map_cluster_color']) ? $cs_plugin_options['cs_map_cluster_color'] : '#000000';

        $cs_map_auto_zoom = isset($cs_plugin_options['cs_map_auto_zoom']) ? $cs_plugin_options['cs_map_auto_zoom'] : '';

        $cs_map_lock = isset($cs_plugin_options['cs_map_lock']) ? $cs_plugin_options['cs_map_lock'] : '';

        $cs_jobcareer_theme_options = get_option('cs_theme_options');
        $cs_candidate_map_style = isset($cs_jobcareer_theme_options['def_map_style']) ? $cs_jobcareer_theme_options['def_map_style'] : '';
        ?>
        <div id="cs-map-candidate-<?php echo esc_attr($rand_counter); ?>" class="cs-map-candidate">
            <?php
            $cs_map_lock_icon = 'icon-unlock';
            if ($cs_map_lock == 'on') {
                $cs_map_lock_icon = 'icon-lock3';
            }
            ?>
            <span class="gmaplock" id="gmaplock<?php echo esc_attr($rand_counter); ?>" style="cursor: pointer;"><i class="<?php echo cs_allow_special_char($cs_map_lock_icon); ?>"></i></span>
            <div id="cs_map_<?php echo absint($rand_counter) ?>" style="width: 100%; height: <?php echo absint($cs_candidate_map_height) ?>px;"></div>
        </div>
        <script type="text/javascript">
            jQuery(document).ready(function () {
                var dataobj = jQuery.parseJSON('<?php echo addslashes($cs_json_array) ?>');
				console.log(dataobj);
                cs_googlecluster_map('<?php echo esc_js($rand_counter) ?>', '<?php echo esc_js($cs_latitude); ?>', '<?php echo esc_js($cs_longitude); ?>', '<?php echo esc_url($cs_map_cluster_icon) ?>', '<?php echo esc_url($cs_map_marker_icon) ?>', dataobj, <?php echo absint($cs_map_zoom); ?>, '<?php echo esc_js($cs_map_cluster_color); ?>', '<?php echo esc_js($cs_map_auto_zoom); ?>', '<?php echo esc_js($cs_map_lock); ?>', '<?php echo esc_js($cs_candidate_map_style); ?>');
            });
        </script>
        <?php
    }
}