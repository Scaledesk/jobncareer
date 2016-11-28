<?php
/**
 * Jobs Listing search box
 *
 */
global $wpdb, $cs_plugin_options, $cs_form_fields2;
 $popup_randid = rand(10000, 4999999);
?>
<aside class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
    <div class="cs-listing-filters">
        <div class="cs-job-inputs">
            <form class="side-loc-srch-form" method="get" data-ajaxurl="<?php echo esc_js(admin_url('admin-ajax.php')); ?>">
                <?php
                $final_query_str = str_replace("?", "", $qrystr);
                $final_query_str = cs_remove_qrystr_extra_var($final_query_str, 'location', 'no');
                $final_query_str = cs_remove_qrystr_extra_var($final_query_str, 'job_title', 'no');
                $final_query_str = cs_remove_qrystr_extra_var($final_query_str, 'radius', 'no');
                $final_query_str = str_replace("?", "", $final_query_str);
                $query = explode('&', $final_query_str);
                foreach ($query as $param) {
                    if (!empty($param)) {
                        $param_array = explode('=', $param);
                        $name = isset($param_array[0]) ? $param_array[0] : '';
                        $value = isset($param_array[1]) ? $param_array[1] : '';
                        $new_str = $name . "=" . $value;
                        if (is_array($name)) {
                            foreach ($_query_str_single_value as $_query_str_single_value_arr) {
                                $cs_form_fields2->cs_form_hidden_render(
                                        array(
                                            'id' => $name,
                                            'cust_name' => $name . '[]',
                                            'std' => $value,
                                        )
                                );
                            }
                        } else {
                            $cs_form_fields2->cs_form_hidden_render(
                                    array(
                                        'id' => $name,
                                        'cust_name' => $name,
                                        'std' => $value,
                                    )
                            );
                        }
                    }
                }
                ?>
                <!-- end extra query string -->
                <div class="search-input">
                    <i class="icon-search7"></i>
                    <?php
                    $cs_form_fields2->cs_form_text_render(
                            array(
                                'id' => 'job_title',
                                'cust_name' => 'job_title',
                                'cust_type' => 'search',
                                'extra_atr' => ' placeholder="' . __('Search Keywords', 'jobhunt') . '"',
                                'classes' => 'form-control txt-field side-location-field',
                                'std' => $job_title,
                            )
                    );
                    ?>
                </div>
                <!-- location with radius -->
                <?php
                if (isset($cs_plugin_options['cs_jobhunt_search_location']) && $cs_plugin_options['cs_jobhunt_search_location'] == 'on') {
                    ?>
                    <div class="job-side-location-field">
                        <?php
                        $cs_radius = '';
                        if (isset($_GET['radius']) && $_GET['radius'] > 0) {
                            $cs_radius = $_GET['radius'];
                        }
                        $cs_locatin_cust = cs_location_convert();
                        $cs_geo_location = isset($cs_plugin_options['cs_geo_location']) ? $cs_plugin_options['cs_geo_location'] : '';
                        $cookie_geo_loc = isset($_COOKIE['cs_geo_loc']) ? $_COOKIE['cs_geo_loc'] : '';
                        $cookie_geo_switch = isset($_COOKIE['cs_geo_switch']) ? $_COOKIE['cs_geo_switch'] : '';
                        if ($cs_geo_location == 'on' && $cookie_geo_switch == 'on' && $cookie_geo_loc != '') {
                            $cs_locatin_cust = $cookie_geo_loc;
                        }
                        if (isset($_GET['location'])) {
                            $cs_locatin_cust = cs_location_convert();
                        }
                        $cs_loc_name = '';
                        $cs_select_display = 'block';
                        $cs_input_display = 'none';
                        $cs_undo_display = 'none';
                        if ($cs_locatin_cust != '') {
                            $cs_loc_name = ' name="location"';
                            $cs_select_display = 'none';
                            $cs_input_display = 'block';
                            $cs_undo_display = 'block';
                        }
                        $cs_radius_switch = isset($cs_plugin_options['cs_radius_switch']) ? $cs_plugin_options['cs_radius_switch'] : '';
                        $min_value = 0;
                        $max_value = '';
                        if ($cs_radius_switch == 'on') {
                            $cs_default_radius = isset($cs_plugin_options['cs_default_radius']) ? $cs_plugin_options['cs_default_radius'] : '';
                            $cs_radius_measure = isset($cs_plugin_options['cs_radius_measure']) ? $cs_plugin_options['cs_radius_measure'] : '';
                            $cs_radius_measure = $cs_radius_measure == 'km' ? __('KM', 'jobhunt') : __('Miles', 'jobhunt');
                            $min_value = isset($cs_plugin_options['cs_radius_min']) ? $cs_plugin_options['cs_radius_min'] : '';
                            $max_value = isset($cs_plugin_options['cs_radius_max']) ? $cs_plugin_options['cs_radius_max'] : '';
                            $radius_step = isset($cs_plugin_options['cs_radius_step']) ? $cs_plugin_options['cs_radius_step'] : '';
                            // from submitted value
                            $cs_radius = preg_replace("/[^0-9,.]/", "", $cs_radius);
                            if ($cs_radius == '') {
                                $cs_radius = $cs_default_radius;
                            }
                        }
                        ?>
                        <div id="cs-select-holder" class="select-location" data-locationadminurl="<?php echo esc_url(admin_url("admin-ajax.php")) ?>">
                            <?php
                            if ($cs_plugin_options['cs_google_autocomplete_enable'] == 'on') {
                                cs_get_custom_locationswith_google_auto('<div id="cs-select-holder" class="search-country" style="display:' . cs_allow_special_char($cs_select_display) . '"><div class="select-holder">', '</div><span>' . __('Please select your desired location', 'jobhunt') . '</span> </div>');
                            } else {
                                cs_get_custom_locations('<div id="cs-select-holder" class="search-country" style="display:' . cs_allow_special_char($cs_select_display) . '"><div class="select-holder">', '</div><span>' . __('Please select your desired location', 'jobhunt') . '</span> </div>');
                            }
                           
                            ?>
                            <a id="location_redius_popup<?php echo absint($popup_randid); ?>" href="javascript:void(0);" class="location-btn pop"><i class="icon-target3"></i></a>
                            <?php
                            if ($cs_radius_switch == 'on') {
                                ?>
                                <div id="popup<?php echo absint($popup_randid) ?>" style="display:none;" class="select-popup">
                                    <a class="cs-location-close-popup cs-color" id="cs_close<?php echo absint($popup_randid); ?>"><i class="cs-color icon-times"></i></a>
                                    <p><?php _e("Show With in", "jobhunt") ?></p>
                                    <input onchange="this.form.submit();" id="ex6<?php echo absint($popup_randid); ?>" type="text" name="radius" data-slider-min="<?php echo absint($min_value); ?>" data-slider-max="<?php echo absint($max_value); ?>" data-slider-step="<?php echo absint($radius_step); ?>" data-slider-value="<?php echo absint($cs_radius); ?>"/>
                                    <span id="ex6CurrentSliderValLabel"><span id="ex6SliderVal<?php echo absint($popup_randid); ?>"><?php echo absint($cs_radius); ?></span><?php echo esc_html($cs_radius_measure); ?></span>
                                    <?php
                                    if ($cs_geo_location == 'on') {
                                        ?>
                                        <p class="my-location"><?php _e("of", "jobhunt"); ?> <i class="cs-color icon-location-arrow"></i><a class="cs-color" onclick="cs_get_location(this)"><?php _e("My location", "jobhunt"); ?></a></p>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                        <?php
                        // text filed only showing when you click on get my own direction
                        $cs_form_fields2->cs_form_text_render(
                                array(
                                    'id' => '',
                                    'classes' => 'cs-geo-location form-control txt-field  geo-search-location',
                                    'extra_atr' => ' onchange="this.form.submit()" placeholder="' . __('All Locations', 'jobhunt') . '" style="display:' . cs_allow_special_char($cs_input_display) . ';" ' . $cs_loc_name,
                                    'std' => $cs_locatin_cust,
                                )
                        );
                        ?>
                        <div class="cs-undo-select" style="display:<?php echo cs_allow_special_char($cs_undo_display) ?>;">
                            <i class="icon-times"></i>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </form>
        </div>
        <div class="cs-job-posted">
            <div class="searchbox-heading"> <h5><?php echo __('Date Posted', 'jobhunt'); ?></h5> </div>
            <ul>
                <li class="cs-radio-btn"><a href="<?php
                    echo cs_remove_qrystr_extra_var($qrystr, 'posted');
                    if (cs_remove_qrystr_extra_var($qrystr, 'posted') != '?')
                        echo '&';
                    ?>posted=lasthour" <?php if ($posted == 'lasthour') echo 'class="active"'; ?> onclick="cs_listing_content_load();"><?php _e('Last Hour', 'jobhunt') ?><?php if ($posted == 'lasthour') echo ''; ?></a></li>
                <li class="cs-radio-btn"><a href="<?php
                    echo cs_remove_qrystr_extra_var($qrystr, 'posted');
                    if (cs_remove_qrystr_extra_var($qrystr, 'posted') != '?')
                        echo '&';
                    ?>posted=last24" <?php if ($posted == 'last24') echo 'class="active"'; ?> onclick="cs_listing_content_load();"><?php _e('Last 24 hours', 'jobhunt') ?><?php if ($posted == 'last24') echo ''; ?></a></li>
                <li class="cs-radio-btn"><a href="<?php
                    echo cs_remove_qrystr_extra_var($qrystr, 'posted');
                    if (cs_remove_qrystr_extra_var($qrystr, 'posted') != '?')
                        echo '&';
                    ?>posted=7days" <?php if ($posted == '7days') echo 'class="active"'; ?> onclick="cs_listing_content_load();"><?php _e('Last 7 days', 'jobhunt') ?><?php if ($posted == '7days') echo ''; ?></a></li>
                <li class="cs-radio-btn"><a href="<?php
                    echo cs_remove_qrystr_extra_var($qrystr, 'posted');
                    if (cs_remove_qrystr_extra_var($qrystr, 'posted') != '?')
                        echo '&';
                    ?>posted=14days" <?php if ($posted == '14days') echo 'class="active"'; ?> onclick="cs_listing_content_load();"><?php _e('Last 14 days', 'jobhunt') ?><?php if ($posted == '14days') echo ''; ?></a></li>
                <li class="cs-radio-btn"><a href="<?php
                    echo cs_remove_qrystr_extra_var($qrystr, 'posted');
                    if (cs_remove_qrystr_extra_var($qrystr, 'posted') != '?')
                        echo '&';
                    ?>posted=30days" <?php if ($posted == '30days') echo 'class="active"'; ?> onclick="cs_listing_content_load();"><?php _e('Last 30 days', 'jobhunt') ?><?php if ($posted == '30days') echo ''; ?></a></li>
                <li class="cs-radio-btn"><a href="<?php
                    echo cs_remove_qrystr_extra_var($qrystr, 'posted');
                    if (cs_remove_qrystr_extra_var($qrystr, 'posted') != '?')
                        echo '&';
                    ?>posted=all" <?php if ($posted == 'all' || $posted == '') echo 'class="active"'; ?> onclick="cs_listing_content_load();"><?php _e('All', 'jobhunt') ?><?php if ($posted == 'all' || $posted == '') echo ''; ?></a></li>
            </ul>
        </div>
        <div class="cs-job-vacancy">
            <div class="searchbox-heading"> <h5><?php echo __('Vacancy Type', 'jobhunt'); ?></h5> </div>
            <form method="GET" id="frm_job_type_list">
                <?php
                // parse query string and create hidden fileds
                $final_query_str = str_replace("?", "", $qrystr);
                $final_query_str = cs_remove_qrystr_extra_var($final_query_str, 'job_type', 'no');
                $query = explode('&', $final_query_str);
                foreach ($query as $param) {
                    if (!empty($param)) {
                        $param_array = explode('=', $param);
                        $name = isset($param_array[0]) ? $param_array[0] : '';
                        $value = isset($param_array[1]) ? $param_array[1] : '';
                        $new_str = $name . "=" . $value;
                        if (is_array($name)) {
                            foreach ($_query_str_single_value as $_query_str_single_value_arr) {
                                $cs_form_fields2->cs_form_hidden_render(
                                        array(
                                            'id' => $name,
                                            'cust_name' => $name . '[]',
                                            'std' => $value,
                                        )
                                );
                            }
                        } else {
                            $cs_form_fields2->cs_form_hidden_render(
                                    array(
                                        'id' => $name,
                                        'cust_name' => $name,
                                        'std' => $value,
                                    )
                            );
                        }
                    }
                }

                $job_type_args = array(
                    'orderby' => 'name',
                    'order' => 'ASC',
                    'fields' => 'all',
                    'slug' => '',
                    'hide_empty' => false,
                    'parent' => 0,
                );

                $all_job_types = get_terms('job_type', $job_type_args);
                ?>
                <ul class="cs_vacancy_type">
                    <?php
                    // get all job types
                    $all_job_type = get_terms('job_type');
                    if ($all_job_type != '') {
                        foreach ($all_job_type as $job_typeitem) {
                            $job_type_mypost = '';
                            if ($job_title != '') {

                                $post_ids = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE " . $job_title_id_condition . " UCASE(post_title) LIKE '%$job_title%' OR UCASE(post_content) LIKE '%$job_title%' AND post_type='jobs' AND post_status='publish'");
                                if ($post_ids) {
                                    $job_type_mypost = array('posts_per_page' => "-1", 'post_type' => 'jobs', 'order' => "DESC", 'orderby' => 'post_date',
                                        'post_status' => 'publish', 'ignore_sticky_posts' => 1,
                                        'post__in' => $post_ids,
                                        'tax_query' => array(
                                            'relation' => 'AND',
                                            array(
                                                'taxonomy' => 'job_type',
                                                'field' => 'slug',
                                                'terms' => array($job_typeitem->slug)
                                            ),
                                        //$filter_arr2
                                        ),
                                        'meta_query' => array(
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
                                }
                            } else {
                                $job_type_mypost = array('posts_per_page' => "-1", 'post_type' => 'jobs', 'order' => "DESC", 'orderby' => 'post_date',
                                    'post__in' => $meta_post_ids_arr,
                                    'post_status' => 'publish', 'ignore_sticky_posts' => 1,
                                    'tax_query' => array(
                                        'relation' => 'AND',
                                        array(
                                            'taxonomy' => 'job_type',
                                            'field' => 'slug',
                                            'terms' => array($job_typeitem->slug)
                                        ),
                                    ),
                                    'meta_query' => array(
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
                            }

                            $job_type_loop_count = new WP_Query($job_type_mypost);
                            $job_type_count_post = $job_type_loop_count->post_count;
                            ###################################################
                            $job_type_qry_str = '';
                            $job_type_qry_str .= cs_remove_qrystr_extra_var($qrystr, 'job_type');
                            if (cs_remove_qrystr_extra_var($qrystr, 'job_type') != '?') {
                                $job_type_qry_str .= '&';
                            }

                            $job_type_qry_str .= 'job_type=' . $job_typeitem->slug;

                            $t_id_main = '';
                            if (isset($job_typeitem->term_id)) {
                                $t_id_main = $job_typeitem->term_id;
                            }
                            $jobtype_color_list_main = get_option("job_type_color_$t_id_main");
                            $jobtype_color = '';
                            if (isset($jobtype_color_list_main['text'])) {
                                $jobtype_color = $jobtype_color_list_main['text'];
                            }
                            $jobtype_color_Str = '';
                            if (isset($jobtype_color) && $jobtype_color != '') {
                                $jobtype_color_Str = '<span class=\'cs_vacancy_type_list\' style=\'background-color:' . $jobtype_color . ';\'></span>';
                            }
                            if ($job_type == $job_typeitem->slug) {
                                echo '<li>' . $jobtype_color_Str . '<a href="' . $job_type_qry_str . '" class="active" onclick="cs_listing_content_load();">' . $job_typeitem->name . ' <span>(' . $job_type_count_post . ')</span></a></li>';
                            } else {
                                echo '<li>' . $jobtype_color_Str . '<a href="' . $job_type_qry_str . '" onclick="cs_listing_content_load();">' . $job_typeitem->name . ' <span>(' . $job_type_count_post . ')</span></a></li>';
                            }
                        }
                    }
                    ?>
                </ul>
            </form>
        </div>
        <div class="cs-job-specialisms">
            <div class="searchbox-heading"> <h5><?php echo __('specialisms', 'jobhunt') ?></h5> </div>
            <form method="GET" id="frm_specialisms_list" >
                <?php
                // parse query string and create hidden fileds
                $final_query_str = str_replace("?", "", $qrystr);
                $final_query_str = cs_remove_qrystr_extra_var($final_query_str, 'specialisms', 'no');
                $query = explode('&', $final_query_str);
                foreach ($query as $param) {
                    if (!empty($param)) {
                        $param_array = explode('=', $param);
                        $name = isset($param_array[0]) ? $param_array[0] : '';
                        $value = isset($param_array[1]) ? $param_array[1] : '';
                        $new_str = $name . "=" . $value;
                        if (is_array($name)) {
                            foreach ($_query_str_single_value as $_query_str_single_value_arr) {
                                $cs_form_fields2->cs_form_hidden_render(
                                        array(
                                            'id' => $name,
                                            'cust_name' => $name . '[]',
                                            'std' => $value,
                                        )
                                );
                            }
                        } else {
                            $cs_form_fields2->cs_form_hidden_render(
                                    array(
                                        'id' => $name,
                                        'cust_name' => $name,
                                        'std' => $value,
                                    )
                            );
                        }
                    }
                }
                ?>
                <ul class="specialism_list">
                    <?php
                    // get all job types

                    $specialisms_parent_id = 0;
                    $specialism_show_count = 7;
                    $input_type_specialism = 'radio';   // if first level then select only sigle specialism
                    if ($specialisms != '') {
                        $selected_spec = get_term_by('slug', $specialisms[0], 'specialisms');
                        if (isset($selected_spec->term_id))
                            $specialisms_parent_id = $selected_spec->term_id;
                        echo '<li><a href ="' . cs_remove_qrystr_extra_var($qrystr, 'specialisms') . '" onclick="cs_listing_content_load();">' . __('View all specialisms', 'jobhunt') . '</a></li><li>&nbsp;</li>';
                    }
                    $specialisms_args = array(
                        'orderby' => 'name',
                        'order' => 'ASC',
                        'number' => $specialism_show_count,
                        'fields' => 'all',
                        'slug' => '',
                        'hide_empty' => false,
                        'parent' => $specialisms_parent_id,
                    );
                    $specialisms_all_args = array(
                        'orderby' => 'name',
                        'order' => 'ASC',
                        'fields' => 'all',
                        'slug' => '',
                        'hide_empty' => false,
                        'parent' => $specialisms_parent_id,
                    );
                    $all_specialisms = get_terms('specialisms', $specialisms_args);
                    if (count($all_specialisms) <= 0) {
                        $specialisms_args = array(
                            'orderby' => 'name',
                            'order' => 'ASC',
                            'number' => $specialism_show_count,
                            'fields' => 'all',
                            'hide_empty' => false,
                            'slug' => '',
                            'parent' => $selected_spec->parent,
                        );
                        $specialisms_all_args = array(
                            'orderby' => 'name',
                            'order' => 'ASC',
                            'fields' => 'all',
                            'hide_empty' => false,
                            'slug' => '',
                            'parent' => $selected_spec->parent,
                        );
                        $all_specialisms = get_terms('specialisms', $specialisms_args);
                        if ($selected_spec->parent != 0) {
                            $input_type_specialism = 'checkbox';
                        }
                    } else {
                        if ($specialisms_parent_id != 0) {    // if parent is not root means not main parent
                            $input_type_specialism = 'checkbox';   // if first level then select multiple specialism
                        }
                    }
                    if ($input_type_specialism == 'checkbox') {
                        $cs_form_fields2->cs_form_hidden_render(
                                array(
                                    'cust_id' => 'specialisms_string',
                                    'cust_name' => 'specialisms_string',
                                    'std' => '',
                                )
                        );
                    }
                    if ($all_specialisms != '') {
                        $number_option = 1;
                        $show_specialism = 'yes';
                        if ($input_type_specialism == 'radio' && $specialisms != '') {
                            if (is_array($specialisms) && is_array_empty($specialisms)) {
                                $show_specialism = 'yes';
                            } else {
                                $show_specialism = 'no';
                            }
                        } else {
                            $show_specialism = 'yes';
                        }
                        if ($show_specialism == 'yes') {
                            foreach ($all_specialisms as $specialismsitem) {

                                $job_id_para = '';
                                $specialisms_mypost = '';
                                if ($job_title != '') {

                                    $post_ids = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE " . $job_title_id_condition . " UCASE(post_title) LIKE '%$job_title%' OR UCASE(post_content) LIKE '%$job_title%' AND post_type='jobs' AND post_status='publish'");
                                    if ($post_ids) {
                                        $specialisms_mypost = array('posts_per_page' => "-1", 'post_type' => 'jobs', 'order' => "DESC", 'orderby' => 'post_date',
                                            'post_status' => 'publish', 'ignore_sticky_posts' => 1,
                                            'post__in' => $post_ids,
                                            'tax_query' => array(
                                                'relation' => 'AND',
                                                array(
                                                    'taxonomy' => 'specialisms',
                                                    'field' => 'slug',
                                                    'terms' => array($specialismsitem->slug)
                                                ),
                                            //$filter_arr2
                                            ),
                                            'meta_query' => array(
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
                                    }
                                } else {
                                    $specialisms_mypost = array('posts_per_page' => "-1", 'post_type' => 'jobs', 'order' => "DESC", 'orderby' => 'post_date',
                                        'post__in' => $meta_post_ids_arr,
                                        'post_status' => 'publish', 'ignore_sticky_posts' => 1,
                                        'tax_query' => array(
                                            'relation' => 'AND',
                                            array(
                                                'taxonomy' => 'specialisms',
                                                'field' => 'slug',
                                                'terms' => array($specialismsitem->slug)
                                            ),
                                        ),
                                        'meta_query' => array(
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
                                }
                                $specialisms_loop_count = new WP_Query($specialisms_mypost);
                                $specialisms_count_post = $specialisms_loop_count->post_count;
                                if ($input_type_specialism == 'checkbox') {
                                    if (isset($specialisms) && is_array($specialisms)) {
                                        if (in_array($specialismsitem->slug, $specialisms)) {

                                            echo '<li class="' . $input_type_specialism . '">'
                                            . '<div class="checkbox checkbox-primary">';

                                            $cs_form_fields2->cs_form_checkbox_render(
                                                    array(
                                                        'simple' => true,
                                                        'cust_id' => 'checklist' . $number_option,
                                                        'cust_name' => '',
                                                        'extra_atr' => ' onclick="cs_listing_content_load();" onchange="javascript:submit_specialism_form(\'frm_specialisms_list\', \'specialisms_string\');" checked="checked"',
                                                        'classes' => $input_type_specialism,
                                                        'std' => $specialismsitem->slug,
                                                    )
                                            );
                                            echo '<label for="checklist' . $number_option . '">' . $specialismsitem->name . '</label> <span>(' . $specialisms_count_post . ')</span></div></li>';
                                        } else {
                                            echo '<li class="' . $input_type_specialism . '">'
                                            . '<div class="checkbox checkbox-primary">';

                                            $cs_form_fields2->cs_form_checkbox_render(
                                                    array(
                                                        'simple' => true,
                                                        'cust_id' => 'checklist' . $number_option,
                                                        'cust_name' => '',
                                                        'extra_atr' => ' onclick="cs_listing_content_load();" onchange="submit_specialism_form(\'frm_specialisms_list\', \'specialisms_string\');"',
                                                        'classes' => $input_type_specialism,
                                                        'std' => $specialismsitem->slug,
                                                    )
                                            );

                                            echo '<label for="checklist' . $number_option . '">' . $specialismsitem->name . '<span>(' . $specialisms_count_post . ')</span></label></div></li>';
                                        }
                                    } else {
                                        echo '<li class="' . $input_type_specialism . '">'
                                        . '<div class="checkbox checkbox-primary">';

                                        $cs_form_fields2->cs_form_checkbox_render(
                                                array(
                                                    'simple' => true,
                                                    'cust_id' => 'checklist' . $number_option,
                                                    'cust_name' => '',
                                                    'extra_atr' => ' onclick="cs_listing_content_load();" onchange="submit_specialism_form(\'frm_specialisms_list\', \'specialisms_string\');"',
                                                    'classes' => $input_type_specialism,
                                                    'std' => $specialismsitem->slug,
                                                )
                                        );

                                        echo '<label for="checklist' . $number_option . '">' . $specialismsitem->name . '<span>(' . $specialisms_count_post . ')</span></label></div></li>';
                                    }
                                } else
                                if ($input_type_specialism == 'radio') {
                                    if (isset($specialisms) && is_array($specialisms)) {
                                        if (in_array($specialismsitem->slug, $specialisms)) {
                                            echo '<li class="' . $input_type_specialism . '">'
                                            . '<div class="checkbox checkbox-primary">';
                                            $cs_form_fields2->cs_form_radio_render(
                                                    array(
                                                        'cust_id' => 'checklist' . $number_option,
                                                        'cust_name' => ' name="specialisms"',
                                                        'extra_atr' => ' onclick="cs_listing_content_load();" onchange="javascript:frm_specialisms_list.submit();" checked="checked"',
                                                        'classes' => $input_type_specialism,
                                                        'std' => $specialismsitem->slug,
                                                    )
                                            );

                                            echo '<label class="active" for="checklist' . $number_option . '">' . $specialismsitem->name . '<span>(' . $specialisms_count_post . ')</span></label>  </div></li>';
                                        } else {

                                            echo '<li class="' . $input_type_specialism . '">'
                                            . '<div class="checkbox checkbox-primary">';
                                            $cs_form_fields2->cs_form_radio_render(
                                                    array(
                                                        'cust_id' => 'checklist' . $number_option,
                                                        'cust_name' => 'specialisms',
                                                        'extra_atr' => ' onclick="cs_listing_content_load();" onchange="javascript:frm_specialisms_list.submit();"',
                                                        'classes' => $input_type_specialism,
                                                        'std' => $specialismsitem->slug,
                                                    )
                                            );

                                            echo '<label for="checklist' . $number_option . '">' . $specialismsitem->name . '<span>(' . $specialisms_count_post . ')</span></label></div></li>';
                                        }
                                    } else {

                                        echo '<li class="' . $input_type_specialism . '">'
                                        . '<div class="checkbox checkbox-primary">';

                                        $cs_form_fields2->cs_form_radio_render(
                                                array(
                                                    'cust_id' => 'checklist' . $number_option,
                                                    'cust_name' => 'specialisms',
                                                    'extra_atr' => ' onclick="cs_listing_content_load();" onchange="javascript:frm_specialisms_list.submit();"',
                                                    'classes' => $input_type_specialism,
                                                    'std' => $specialismsitem->slug,
                                                )
                                        );

                                        echo '<label for="checklist' . $number_option . '">' . $specialismsitem->name . '<span>(' . $specialisms_count_post . ')</span></label></div></li>';
                                    }
                                }
                                $number_option++;
                            }
                        }
                    }

                    if ($show_specialism == 'yes') {
                        $all_specialismscount = get_terms('specialisms', $specialisms_all_args);
                        if (count($all_specialismscount) > $specialism_show_count) {
                            ?>
                            <li>
                                <div class="checkbox checkbox-primary">
                                    <a data-target="#light" data-toggle="modal" href="#"><?php _e('More', 'jobhunt') ?></a>
                                </div>
                            </li>
                            <?php
                        }
                    }
                    ?>
                </ul>
            </form>
        </div>

        <?php
        $cs_job_cus_fields = get_option("cs_job_cus_fields");
        if (is_array($cs_job_cus_fields) && sizeof($cs_job_cus_fields) > 0) {
            ?>
            <a class="cs-expand-filters"><i class="icon-minus8"></i> <?php _e('Collapse all Filters', 'jobhunt') ?></a>
            <div class="accordion" id="accordion2">
                <?php
                $custom_field_flag = 11;
                foreach ($cs_job_cus_fields as $cus_fieldvar => $cus_field) {
                    $all_item_empty = 0;
                    if (isset($cus_field['options']['value']) && is_array($cus_field['options']['value'])) {
                        foreach ($cus_field['options']['value'] as $cus_field_options_value) {

                            if ($cus_field_options_value != '') {
                                $all_item_empty = 0;
                                break;
                            } else {
                                $all_item_empty = 1;
                            }
                        }
                    }
                    if (isset($cus_field['enable_srch']) && $cus_field['enable_srch'] == 'yes' && ($all_item_empty == 0)) {
                        $query_str_var_name = $cus_field['meta_key'];
                        $collapse_condition = 'no';
                        if (isset($cus_field['collapse_search'])) {
                            $collapse_condition = $cus_field['collapse_search'];
                        }
                        $count_filtration = $cus_fields_count_arr;
                        $filter_new_arr = '';
                        if (isset($count_filtration[$query_str_var_name])) {
                            unset($count_filtration[$query_str_var_name]);
                            $filter_temp_arr = $count_filtration;

                            foreach ($filter_temp_arr as $var => $value) {
                                $filter_new_arr[] = $value;
                            }
                        } else {
                            if (isset($count_filtration) && $count_filtration != '') {
                                foreach ($count_filtration as $var => $value) {
                                    $filter_new_arr[] = $value;
                                }
                            }
                        }
                        $filter_new_arr = isset($filter_new_arr) && !empty($filter_new_arr) ? call_user_func_array('array_merge', $filter_new_arr) : '';
                        $meta_post_ids_cus_fields_arr = '';
                        $meta_post_job_title_id_condition = '';
                        if (!empty($filter_new_arr)) {
                            $meta_post_ids_cus_fields_arr = cs_get_query_whereclase_by_array($filter_new_arr);
                            if (empty($meta_post_ids_cus_fields_arr)) {
                                $meta_post_ids_cus_fields_arr = array(0);
                            }
                            $ids = $meta_post_ids_cus_fields_arr != '' ? implode(",", $meta_post_ids_cus_fields_arr) : '0';
                            $meta_post_job_title_id_condition = " ID in (" . $ids . ") AND ";
                        }
                        ?>
                        <div class="accordion-group">
                            <div class="accordion-heading"> 
                                <a class="accordion-toggle <?php
                                if ($collapse_condition == 'yes') {
                                    echo 'collapsed';
                                }
                                ?>" data-toggle="collapse" data-parent="#accordion2" href="#collapse<?php echo esc_html($custom_field_flag); ?>">
                                       <?php echo esc_html($cus_field['label']); ?>
                                </a> 
                            </div>
                            <div id="collapse<?php echo esc_html($custom_field_flag); ?>" class="accordion-body collapse <?php
                            if ($collapse_condition != 'yes') {
                                echo 'in';
                            }
                            ?>">
                                <div class="accordion-inner">
                                    <?php
                                    if ($cus_field['type'] == 'dropdown') {
                                        $_query_string_arr = getMultipleParameters();
                                        ?>
                                        <form action="#" method="get" name="frm_<?php echo str_replace(" ", "_", str_replace("-", "_", $query_str_var_name)); ?>" id="frm_<?php echo str_replace(" ", "_", str_replace("-", "_", $query_str_var_name)); ?>">
                                            <ul class="custom-listing">
                                                <?php
                                                $final_query_str = cs_remove_qrystr_extra_var($qrystr, $query_str_var_name);
                                                $final_query_str = str_replace("?", "", $final_query_str);
                                                $query = explode('&', $final_query_str);
                                                foreach ($query as $param) {
                                                    if (!empty($param)) {
                                                        $param_array = explode('=', $param);
                                                        $name = isset($param_array[0]) ? $param_array[0] : '';
                                                        $value = isset($param_array[1]) ? $param_array[1] : '';
                                                        $new_str = $name . "=" . $value;
                                                        if (is_array($name)) {

                                                            foreach ($_query_str_single_value as $_query_str_single_value_arr) {
                                                                echo '<li>';
                                                                $cs_form_fields2->cs_form_hidden_render(
                                                                        array(
                                                                            'id' => $name,
                                                                            'cust_name' => $name . '[]',
                                                                            'std' => $value,
                                                                        )
                                                                );
                                                                echo '</li>';
                                                            }
                                                        } else {
                                                            echo '<li>';
                                                            $cs_form_fields2->cs_form_hidden_render(
                                                                    array(
                                                                        'id' => $name,
                                                                        'cust_name' => $name,
                                                                        'std' => $value,
                                                                    )
                                                            );
                                                            echo '</li>';
                                                        }
                                                    }
                                                }
                                                $number_option_flag = 1;
                                                $cut_field_flag = 0;
                                                foreach ($cus_field['options']['value'] as $cus_field_options_value) {
                                                    if ($cus_field['options']['value'][$cut_field_flag] == '' || $cus_field['options']['label'][$cut_field_flag] == '') {
                                                        $cut_field_flag++;
                                                        continue;
                                                    }
                                                    if ($cus_field_options_value != '') {
                                                        if ($cus_field['multi'] == 'yes') {
                                                            $dropdown_arr = '';
                                                            if ($cus_field['post_multi'] == 'yes') {
                                                                $dropdown_arr = array(
                                                                    'key' => $query_str_var_name,
                                                                    'value' => serialize($cus_field_options_value),
                                                                    'compare' => 'Like',
                                                                );
                                                            } else {
                                                                $dropdown_arr = array(
                                                                    'key' => $query_str_var_name,
                                                                    'value' => $cus_field_options_value,
                                                                    'compare' => '=',
                                                                );
                                                            }

                                                            $cus_field_mypost = '';
                                                            if ($job_title != '') {

                                                                $post_ids = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE " . $meta_post_job_title_id_condition . " UCASE(post_title) LIKE '%$job_title%' OR UCASE(post_content) LIKE '%$job_title%'   AND post_type='jobs' AND post_status='publish'");
                                                                if ($post_ids) {
                                                                    $cus_field_mypost = array('posts_per_page' => "-1", 'post_type' => 'jobs', 'order' => "DESC", 'orderby' => 'post_date',
                                                                        'post_status' => 'publish', 'ignore_sticky_posts' => 1,
                                                                        'post__in' => $post_ids,
                                                                        'tax_query' => array(
                                                                            'relation' => 'AND',
                                                                            $filter_arr2
                                                                        ),
                                                                        'meta_query' => array(
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
                                                                            $dropdown_arr,
                                                                        )
                                                                    );
                                                                }
                                                            } else {
                                                                $cus_field_mypost = array('posts_per_page' => "-1", 'post_type' => 'jobs', 'order' => "DESC", 'orderby' => 'post_date',
                                                                    'post__in' => $meta_post_ids_cus_fields_arr,
                                                                    'post_status' => 'publish', 'ignore_sticky_posts' => 1,
                                                                    'tax_query' => array(
                                                                        'relation' => 'AND',
                                                                        $filter_arr2
                                                                    ),
                                                                    'meta_query' => array(
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
                                                                        $dropdown_arr,
                                                                    )
                                                                );
                                                            }
                                                            $cus_field_loop_count = new WP_Query($cus_field_mypost);
                                                            $cus_field_count_post = $cus_field_loop_count->post_count;
                                                            if (isset($_query_string_arr[$query_str_var_name]) && isset($cus_field_options_value) && is_array($_query_string_arr[$query_str_var_name]) && in_array($cus_field_options_value, $_query_string_arr[$query_str_var_name])) {
                                                                echo '<li class="checkbox" >';

                                                                $cs_form_fields2->cs_form_checkbox_render(
                                                                        array(
                                                                            'simple' => true,
                                                                            'cust_id' => $query_str_var_name . '_' . $number_option_flag,
                                                                            'cust_name' => $query_str_var_name,
                                                                            'extra_atr' => ' onclick="cs_listing_content_load();" onchange="javascript:frm_' . str_replace(" ", "_", str_replace("-", "_", $query_str_var_name)) . '.submit();" checked="checked"',
                                                                            'std' => $cus_field_options_value,
                                                                        )
                                                                );

                                                                echo '<label class="cs-color" for="' . $query_str_var_name . '_' . $number_option_flag . '">' . $cus_field['options']['label'][$cut_field_flag] . '<span>(' . $cus_field_count_post . ')</span></label></li>';
                                                            } else {
                                                                echo '<li class="checkbox" >';

                                                                $cs_form_fields2->cs_form_checkbox_render(
                                                                        array(
                                                                            'simple' => true,
                                                                            'cust_id' => $query_str_var_name . '_' . $number_option_flag,
                                                                            'cust_name' => $query_str_var_name,
                                                                            'extra_atr' => ' onclick="cs_listing_content_load();" onchange="javascript:frm_' . str_replace(" ", "_", str_replace("-", "_", $query_str_var_name)) . '.submit();"',
                                                                            'std' => $cus_field_options_value,
                                                                        )
                                                                );

                                                                echo '<label class="cs-color" for="' . $query_str_var_name . '_' . $number_option_flag . '">' . $cus_field['options']['label'][$cut_field_flag] . '<span>(' . $cus_field_count_post . ')</span></label></li>';
                                                            }
                                                        } else {
                                                            //get count for this itration
                                                            $dropdown_arr = '';
                                                            if ($cus_field['post_multi'] == 'yes') {
                                                                $dropdown_arr = array(
                                                                    'key' => $query_str_var_name,
                                                                    'value' => serialize($cus_field_options_value),
                                                                    'compare' => 'Like',
                                                                );
                                                            } else {
                                                                $dropdown_arr = array(
                                                                    'key' => $query_str_var_name,
                                                                    'value' => $cus_field_options_value,
                                                                    'compare' => '=',
                                                                );
                                                            }
                                                            $cus_field_mypost = '';
                                                            if ($job_title != '') {

                                                                $post_ids = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE " . $meta_post_job_title_id_condition . " UCASE(post_title) LIKE '%$job_title%' OR UCASE(post_content) LIKE '%$job_title%'  AND post_type='jobs' AND post_status='publish'");
                                                                if ($post_ids) {
                                                                    $cus_field_mypost = array('posts_per_page' => "-1", 'post_type' => 'jobs', 'order' => "DESC", 'orderby' => 'post_date',
                                                                        'post_status' => 'publish', 'ignore_sticky_posts' => 1,
                                                                        'post__in' => $post_ids,
                                                                        'tax_query' => array(
                                                                            'relation' => 'AND',
                                                                            $filter_arr2
                                                                        ),
                                                                        'meta_query' => array(
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
                                                                            $dropdown_arr,
                                                                        )
                                                                    );
                                                                }
                                                            } else {
                                                                $cus_field_mypost = array('posts_per_page' => "-1", 'post_type' => 'jobs', 'order' => "DESC", 'orderby' => 'post_date',
                                                                    'post__in' => $meta_post_ids_cus_fields_arr,
                                                                    'post_status' => 'publish', 'ignore_sticky_posts' => 1,
                                                                    'tax_query' => array(
                                                                        'relation' => 'AND',
                                                                        $filter_arr2
                                                                    ),
                                                                    'meta_query' => array(
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
                                                                        $dropdown_arr,
                                                                    )
                                                                );
                                                            }
                                                            $cus_field_loop_count = new WP_Query($cus_field_mypost);
                                                            $cus_field_count_post = $cus_field_loop_count->post_count;
                                                            $amp_sign = '';
                                                            if (cs_remove_qrystr_extra_var($qrystr, $query_str_var_name) != '?')
                                                                $amp_sign = '&';
                                                            if (isset($_GET[$query_str_var_name]) && $_GET[$query_str_var_name] == $cus_field_options_value) {

                                                                echo '<li><a onclick="cs_listing_content_load();"  class="text-capitalize active" href="' . cs_remove_qrystr_extra_var($qrystr, $query_str_var_name) . '">' . $cus_field['options']['label'][$cut_field_flag] . ' <span>(' . $cus_field_count_post . ')</span></a></li>';
                                                            } else {
                                                                echo '<li><a onclick="cs_listing_content_load();"  class="text-capitalize" href="' . cs_remove_qrystr_extra_var($qrystr, $query_str_var_name) . $amp_sign . $query_str_var_name . '=' . $cus_field_options_value . '">' . $cus_field['options']['label'][$cut_field_flag] . ' <span>(' . $cus_field_count_post . ')</span></a></li>';
                                                            }
                                                        }
                                                    }
                                                    $number_option_flag++;
                                                    $cut_field_flag++;
                                                }
                                                ?>
                                            </ul>
                                        </form>
                                        <?php
                                    } else if ($cus_field['type'] == 'text' || $cus_field['type'] == 'email' || $cus_field['type'] == 'url') {
                                        ?>
                                        <form action="#" method="get" name="frm_<?php echo esc_html($query_str_var_name); ?>">
                                            <?php
                                            $final_query_str = cs_remove_qrystr_extra_var($qrystr, $query_str_var_name);
                                            $final_query_str = str_replace("?", "", $final_query_str);
                                            parse_str($final_query_str, $_query_str_arr);
                                            foreach ($_query_str_arr as $_query_str_single_var => $_query_str_single_value) {
                                                if (is_array($_query_str_single_value)) {
                                                    foreach ($_query_str_single_value as $_query_str_single_value_arr) {
                                                        $cs_form_fields2->cs_form_hidden_render(
                                                                array(
                                                                    'id' => $_query_str_single_var,
                                                                    'cust_name' => $_query_str_single_var . '[]',
                                                                    'std' => $_query_str_single_value_arr,
                                                                )
                                                        );
                                                    }
                                                } else {
                                                    $cs_form_fields2->cs_form_hidden_render(
                                                            array(
                                                                'id' => $_query_str_single_var,
                                                                'cust_name' => $_query_str_single_var,
                                                                'std' => $_query_str_single_value,
                                                            )
                                                    );
                                                }
                                            }

                                            $cs_form_fields2->cs_form_text_render(
                                                    array(
                                                        'id' => $query_str_var_name,
                                                        'cust_name' => $query_str_var_name,
                                                        'classes' => 'form-control',
                                                        'extra_atr' => ' onclick="cs_listing_content_load();" onchange="javascript: ' . esc_html($query_str_var_name) . '.submit();"',
                                                        'std' => isset($_GET[$query_str_var_name]) ? $_GET[$query_str_var_name] : '',
                                                    )
                                            );
                                            ?>

                                        </form>
                                        <?php
                                    } else if ($cus_field['type'] == 'date') {

                                        $cus_field_date_formate_arr = explode(" ", $cus_field['date_format']);
                                        ?>
                                        <script>
                                            jQuery(function () {
                                                jQuery("#cs_<?php echo esc_html($query_str_var_name); ?>").datetimepicker({
                                                    format: "<?php echo esc_html($cus_field_date_formate_arr[0]); ?>",
                                                    timepicker: false
                                                });
                                            });
                                        </script>
                                        <form action="#" method="get" name="frm_<?php echo esc_html($query_str_var_name); ?>">
                                            <?php
                                            // parse query string and create hidden fileds
                                            $final_query_str = cs_remove_qrystr_extra_var($qrystr, $query_str_var_name);
                                            $final_query_str = str_replace("?", "", $final_query_str);
                                            parse_str($final_query_str, $_query_str_arr);
                                            foreach ($_query_str_arr as $_query_str_single_var => $_query_str_single_value) {
                                                if (is_array($_query_str_single_value)) {
                                                    foreach ($_query_str_single_value as $_query_str_single_value_arr) {
                                                        $cs_form_fields2->cs_form_hidden_render(
                                                                array(
                                                                    'id' => $_query_str_single_var,
                                                                    'cust_name' => $_query_str_single_var . '[]',
                                                                    'std' => $_query_str_single_value_arr,
                                                                )
                                                        );
                                                    }
                                                } else {
                                                    $cs_form_fields2->cs_form_hidden_render(
                                                            array(
                                                                'id' => $_query_str_single_var,
                                                                'cust_name' => $_query_str_single_var,
                                                                'std' => $_query_str_single_value,
                                                            )
                                                    );
                                                }
                                            }

                                            $cs_form_fields2->cs_form_text_render(
                                                    array(
                                                        'id' => $query_str_var_name,
                                                        'cust_name' => $query_str_var_name,
                                                        'classes' => 'form-control',
                                                        'extra_atr' => ' onclick="cs_listing_content_load();" onchange="javascript: ' . esc_html($query_str_var_name) . '.submit();"',
                                                        'std' => isset($_GET[$query_str_var_name]) ? $_GET[$query_str_var_name] : '',
                                                    )
                                            );
                                            ?>

                                        </form>
                                        <?php
                                    } elseif ($cus_field['type'] == 'range') {

                                        $range_min = $cus_field['min'];
                                        $range_max = $cus_field['max'];
                                        $range_increment = $cus_field['increment'];
                                        $filed_type = $cus_field['srch_style']; //input, slider, input_slider
                                        if (strpos($filed_type, '-') !== FALSE) {
                                            $filed_type_arr = explode("_", $filed_type);
                                        } else {
                                            $filed_type_arr[0] = $filed_type;
                                        }
                                        $range_flag = 0;
                                        while (count($filed_type_arr) > $range_flag) {
                                            if ($filed_type_arr[$range_flag] == 'input') { // if input style
                                                echo '<ul>';
                                                while ($range_min < $range_max) {
                                                    $cus_field_mypost = '';
                                                    if ($job_title != '') {

                                                        $post_ids = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE " . $meta_post_job_title_id_condition . " UCASE(post_title) LIKE '%$job_title%' OR UCASE(post_content) LIKE '%$job_title%'   AND post_type='jobs' AND post_status='publish'");
                                                        if ($post_ids) {
                                                            $cus_field_mypost = array('posts_per_page' => "-1", 'post_type' => 'jobs', 'order' => "DESC", 'orderby' => 'post_date',
                                                                'post_status' => 'publish', 'ignore_sticky_posts' => 1,
                                                                'post__in' => $post_ids,
                                                                'tax_query' => array(
                                                                    'relation' => 'AND',
                                                                    $filter_arr2
                                                                ),
                                                                'meta_query' => array(
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
                                                                    array(
                                                                        'key' => $query_str_var_name,
                                                                        'value' => $range_min,
                                                                        'compare' => '>=',
                                                                    ),
                                                                    array(
                                                                        'key' => $query_str_var_name,
                                                                        'value' => $range_min + $range_increment,
                                                                        'compare' => '<=',
                                                                    ),
                                                                )
                                                            );
                                                        }
                                                    } else {
                                                        $cus_field_mypost = array('posts_per_page' => "-1", 'post_type' => 'jobs', 'order' => "DESC", 'orderby' => 'post_date',
                                                            'post__in' => $meta_post_ids_cus_fields_arr,
                                                            'post_status' => 'publish', 'ignore_sticky_posts' => 1,
                                                            'tax_query' => array(
                                                                'relation' => 'AND',
                                                                $filter_arr2
                                                            ),
                                                            'meta_query' => array(
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
                                                                array(
                                                                    'key' => $query_str_var_name,
                                                                    'value' => $range_min,
                                                                    'compare' => '>=',
                                                                ),
                                                                array(
                                                                    'key' => $query_str_var_name,
                                                                    'value' => $range_min + $range_increment,
                                                                    'compare' => '<=',
                                                                ),
                                                            )
                                                        );
                                                    }

                                                    $cus_field_loop_count = new WP_Query($cus_field_mypost);
                                                    $cus_field_count_post = $cus_field_loop_count->post_count;
                                                    ?>
                                                    <li>
                                                        <a onclick="cs_listing_content_load();" <?php
                                                        if (isset($_GET[$query_str_var_name]) && $_GET[$query_str_var_name] == ($range_min . "-" . ($range_min + $range_increment))) {
                                                            echo 'class="active"';
                                                        }
                                                        ?>href="<?php
                                                           if (isset($_GET[$query_str_var_name]) && $_GET[$query_str_var_name] == ($range_min . "-" . ($range_min + $range_increment))) {
                                                               echo cs_remove_qrystr_extra_var($qrystr, $query_str_var_name);
                                                           } else {
                                                               echo cs_remove_qrystr_extra_var($qrystr, $query_str_var_name) . "&" . $query_str_var_name . '=' . $range_min . "-" . ($range_min + $range_increment);
                                                           }
                                                           ?>"><?php
                                                               echo esc_attr($range_min);
                                                               echo " - ";
                                                               $range_min = $range_min + $range_increment;
                                                               echo esc_attr($range_min);
                                                               ?> <span><?php echo '(' . $cus_field_count_post . ')'; ?></span><?php
                                                            if (isset($_GET[$query_str_var_name]) && $_GET[$query_str_var_name] == ($range_min . "-" . ($range_min + $range_increment))) {
                                                                echo '';
                                                            }
                                                            ?></a>
                                                    </li><?php
                                                    $range_min = $range_min + $range_increment;
                                                }
                                                echo '</ul>';
                                            } elseif ($filed_type_arr[$range_flag] == 'slider') { // if slider style
                                                ?>
                                                <form action="#" method="get" name="frm_<?php echo esc_html($query_str_var_name); ?>" id="frm_<?php echo esc_html($query_str_var_name); ?>">
                                                    <?php
                                                    $final_query_str = cs_remove_qrystr_extra_var($qrystr, $query_str_var_name);
                                                    $final_query_str = str_replace("?", "", $final_query_str);
                                                    parse_str($final_query_str, $_query_str_arr);
                                                    foreach ($_query_str_arr as $_query_str_single_var => $_query_str_single_value) {
                                                        if (is_array($_query_str_single_value)) {
                                                            foreach ($_query_str_single_value as $_query_str_single_value_arr) {
                                                                $cs_form_fields2->cs_form_hidden_render(
                                                                        array(
                                                                            'name' => '',
                                                                            'id' => $_query_str_single_var . '[]',
                                                                            'classes' => '',
                                                                            'std' => $_query_str_single_value_arr,
                                                                            'description' => '',
                                                                            'hint' => ''
                                                                        )
                                                                );
                                                            }
                                                        } else {
                                                            $cs_form_fields2->cs_form_hidden_render(
                                                                    array(
                                                                        'name' => '',
                                                                        'id' => $_query_str_single_var,
                                                                        'classes' => '',
                                                                        'std' => $_query_str_single_value,
                                                                        'description' => '',
                                                                        'hint' => ''
                                                                    )
                                                            );
                                                        }
                                                    }
                                                    $range_complete_str_first = "";
                                                    $range_complete_str_second = "";
                                                    if (isset($_GET[$query_str_var_name])) {
                                                        $range_complete_str = $_GET[$query_str_var_name];
                                                        $range_complete_str_arr = explode(",", $range_complete_str);
                                                        $range_complete_str_first = isset($range_complete_str_arr[0]) ? $range_complete_str_arr[0] : '';
                                                        $range_complete_str_second = isset($range_complete_str_arr[1]) ? $range_complete_str_arr[1] : '';
                                                    } else {
                                                        $range_complete_str = '';
                                                        if (isset($_GET[$query_str_var_name]))
                                                            $range_complete_str = $_GET[$query_str_var_name];
                                                        $range_complete_str_first = $cus_field['min'];
                                                        $range_complete_str_second = $cus_field['max'];
                                                    }
                                                    echo '<div class="cs-selector-range">
                                                                <input name="' . $query_str_var_name . '" onchange="range_form_submit' . $cus_fieldvar . '();" id="slider-range' . esc_html($query_str_var_name) . '" type="text" class="span2" value="" data-slider-min="' . $cus_field['min'] . '" data-slider-max="' . $cus_field['max'] . '" data-slider-step="5" data-slider-value="[' . $range_complete_str_first . ',' . $range_complete_str_second . ']" />
                                                                       <div class="selector-value">
                                                                        <span>' . $cus_field['min'] . '</span>
                                                                        <span class="pull-right">' . $cus_field['max'] . '</span>
                                                                       </div>
                                                               </div>';
                                                    ?>
                                                </form>
                                                <?php
                                                echo '<script>
                                                    function range_form_submit' . $cus_fieldvar . '(){
                                                        cs_listing_content_load();
                                                        jQuery("#frm_' . esc_html($query_str_var_name) . '").submit();
                                                    }
                                                    jQuery(document).ready(function(){
                                                            jQuery("#slider-range' . esc_html($query_str_var_name) . '").slider({
                                                                stop: function(event, ui) {
                                                                    cs_listing_content_load();
                                                                    jQuery("#frm_' . esc_html($query_str_var_name) . '").submit();
                                                                }
                                                        });
                                                    });

                                                    </script>';
                                            }
                                            $range_flag++;
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div><?php
                    }
                    $custom_field_flag++;
                }
                ?>

            </div>
            <?php
        }
        ?>
    </div>
</aside>
<script>
    jQuery(document).ready(function () {
        jQuery(".btn-primary").click(function () {
            jQuery(".collapse").collapse('toggle');
        });

        jQuery(document).on('click', '.cs-expand-filters', function () {
            if (jQuery(this).hasClass('cs-colapse')) {
                jQuery(".collapse").collapse('hide');
                jQuery(this).html('<i class="icon-plus8"></i> <?php _e('Expand all Filters', 'jobhunt') ?>');
                jQuery(this).removeClass('cs-colapse');
            } else {
                jQuery(".collapse").collapse('show');
                jQuery(this).html('<i class="icon-minus8"></i> <?php _e('Collapse all Filters', 'jobhunt') ?>');
                jQuery(this).addClass('cs-colapse');
            }
        });
//         jQuery("#ex6<?php echo absint($popup_randid); ?>").slider({
//            stop: function (event, ui) {
//                alert('aaaa');
//               // jQuery("#frm_' . esc_html($query_str_var_name) . '").submit();
//            }
//        });
        jQuery("#ex6<?php echo absint($popup_randid); ?>").slider({
        });
        jQuery("#ex6<?php echo absint($popup_randid); ?>").on("blur", function (slideEvt) {
            alert('123');
        });
        jQuery("#ex6<?php echo absint($popup_randid); ?>").on("slide", function (slideEvt) {
            jQuery("#ex6SliderVal<?php echo absint($popup_randid); ?>").text(slideEvt.value);
        });
        jQuery('#location_redius_popup<?php echo absint($popup_randid); ?>').click(function (event) {
            event.preventDefault();
            jQuery("#popup<?php echo absint($popup_randid); ?>").css('display', 'block') //to show
            return false;
        });

        jQuery('#cs_close<?php echo absint($popup_randid); ?>').click(function () {
            jQuery("#popup<?php echo absint($popup_randid); ?>").css('display', 'none') //to hide
            return false;
        });

    });
</script>
