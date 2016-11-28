<?php
/**
 * File Type: Candidate Shortcode
 */
/*
 *
 * Start Function how to manage of candidate_listingt
 *
 */
if (!function_exists('cs_candidate_listing')) {

    function cs_candidate_listing($atts, $content = "") {
	global $wp_query;
	foreach( $wp_query->query_vars as $key => $val ) {
		$_GET[$key] = $val;
	}

        global $post, $wpdb, $column_container, $cs_form_fields2, $cs_plugin_options;

        $rand_counter = rand(11342345, 96754534);

        $cs_loc_latitude = isset($cs_plugin_options['cs_post_loc_latitude']) ? $cs_plugin_options['cs_post_loc_latitude'] : '';
        $cs_loc_longitude = isset($cs_plugin_options['cs_post_loc_longitude']) ? $cs_plugin_options['cs_post_loc_longitude'] : '';

        $cs_chek_section_view = '';

        if (isset($column_container)) {
            $column_attributes = $column_container->attributes();
            $cs_chek_section_view = $column_attributes->cs_section_view;
        }

        ob_start();
        global $cs_candidate_cols;
        $a = shortcode_atts(array(
            'column_size' => '',
            'cs_candidate_title' => '',
            'cs_candidate_view' => 'list',
            'cs_candidate_cols' => '6',
            'cs_candidate_searchbox' => 'yes', // yes or no
            'cs_candidate_map' => '',
            'cs_candidate_map_lat' => $cs_loc_latitude,
            'cs_candidate_map_long' => $cs_loc_longitude,
            'cs_candidate_map_zoom' => '11',
            'cs_candidate_map_height' => '300',
            'cs_candidate_map_style' => 'style-2',
            'cs_candidate_searchbox_top' => 'yes', // yes or no
            'cs_candidate_show_pagination' => 'pagination', // yes or no
            'cs_candidate_pagination' => '10', // as per your requirement only numbers(0-9)
                ), $atts
        );
        extract(shortcode_atts($a, $atts));
        $column_class = '';
        if (isset($column_size) && $column_size != '') {
            $column_class = jobcareer_custom_column_class($column_size);
            // echo '<div class="' . $column_class . '">';
        }
        if (isset($column_size) && $column_size != '') {
            ?>
            <div class="<?php echo $column_class; ?>">
            <?php } ?>
            <!-- alert for complete theme -->
            <div class="cs_alerts"></div>
            <!-- main-cs-loader for complete theme -->
            <div class="main-cs-loader" ></div>
            <?php
            $login_user_is_employer_flag = 0;
            $login_user_is_candidate_flag = 0;
            $cs_emp_funs = new cs_employer_functions();
            if (is_user_logged_in()) {
                $user_role = cs_get_loginuser_role();
                if (isset($user_role) && $user_role <> '' && $user_role == 'cs_employer') {
                    $login_user_is_employer_flag = 1;
                } else if (isset($user_role) && $user_role <> '' && $user_role == 'cs_candidate') {
                    $login_user_is_candidate_flag = 1;
                }
            }

            $default_currency_sign = '';
            if (isset($cs_plugin_options['cs_currency_sign'])) {
                $default_currency_sign = $cs_plugin_options['cs_currency_sign'];
            }
            if (empty($_GET['page_resume'])) {
                $_GET['page_resume'] = 1;
            }
            $qrystr = '';
            // Filtration Start      
            // filtration proccess
            $filter_arr = '';
            $posted = '';
            $specialisms = '';
            $job_title = '';
            $location = '';
            $default_date_time_formate = 'd-m-Y H:i:s';
            $cs_user_last_activity_date_date_formate = 'd-m-Y H:i:s';
            if (isset($_REQUEST['job_title'])) {
                $job_title = $_REQUEST['job_title'];
                $job_title = str_replace("+", " ", $job_title);
            }
            if (isset($_GET['location'])) {
                $location = $_GET['location'];
            }
            if (isset($_GET['posted'])) {
                $posted = $_GET['posted'];
            }
            if (isset($_GET['specialisms']) && $_GET['specialisms'] != '') {
                $specialisms = $_GET['specialisms'];
                $qrystr .= '&specialisms=' . $_GET['specialisms'];
                if (!is_array($specialisms))
                    $specialisms = Array($specialisms);
            } else if (isset($_GET['specialisms_string']) && $_GET['specialisms_string'] != '') {
                $specialisms = explode(",", $_GET['specialisms_string']);
                $qrystr .= '&specialisms=' . $_GET['specialisms_string'];
            }
            $cus_fields_count_arr = '';
            $location_condition_arr = '';
            // location check
            if ($location != '') {
                $cs_radius_switch = isset($cs_plugin_options['cs_radius_switch']) ? $cs_plugin_options['cs_radius_switch'] : '';
                if (isset($_GET['radius']) && $_GET['radius'] > 0 && $cs_radius_switch == 'on') {
                    $cs_radius = $_GET['radius'];
                    $cs_radius_measure = isset($cs_plugin_options['cs_radius_measure']) ? $cs_plugin_options['cs_radius_measure'] : '';
                    $distance_km_miles = $cs_radius_measure;
                    $qrystr .= '&radius=' . $cs_radius; // added again this var in query string for linking again
                    $cs_radius = preg_replace("/[^0-9,.]/", "", $cs_radius);
                    if ($distance_km_miles == 'km') {
                        if (isset($_GET['radius'])) {
                            $cs_radius = $cs_radius * 0.621371; // for km
                        }
                    }

                    $Latitude = '';
                    $Longitude = '';
                    $prepAddr = '';
                    $minLat = '';
                    $maxLat = '';
                    $minLong = '';
                    $maxLong = '';

                    if (isset($_GET['location']) && !empty($_GET['location'])) {
                        $address = sanitize_text_field($_GET['location']);
                        $prepAddr = str_replace(' ', '+', $address);
                        $geocode = file_get_contents(cs_server_protocol() . 'google.com/maps/api/geocode/json?address=' . $prepAddr . '&sensor=false');
                        $output = json_decode($geocode);
                        $Latitude = $output->results[0]->geometry->location->lat;
                        $Longitude = $output->results[0]->geometry->location->lng;
                        if (isset($Latitude) && $Latitude <> '' && isset($Longitude) && $Longitude <> '') {
                            $zcdRadius = new RadiusCheck($Latitude, $Longitude, $cs_radius);
                            $minLat = $zcdRadius->MinLatitude();
                            $maxLat = $zcdRadius->MaxLatitude();
                            $minLong = $zcdRadius->MinLongitude();
                            $maxLong = $zcdRadius->MaxLongitude();
                        }
                    }
                    if ($minLat != '' && $maxLat != '' && $minLong != '' && $maxLong != '') {
                        $radius_array = array(
                            'relation' => 'AND',
                            array(
                                'key' => 'cs_post_loc_latitude',
                                'value' => array($minLat, $maxLat),
                                'compare' => 'BETWEEN',
                                'type' => 'CHAR'
                            ),
                            array(
                                'key' => 'cs_post_loc_longitude',
                                'value' => array($minLong, $maxLong),
                                'compare' => 'BETWEEN',
                                'type' => 'CHAR'
                            ),
                        );
                        
                    }
                }
                $qrystr .= '&location=' . $location;  // added again this var in query string for linking again
                $cs_location_type = isset($cs_plugin_options['cs_search_by_location']) ? $cs_plugin_options['cs_search_by_location'] : '';
                if ($cs_location_type == 'countries_and_cities' || $cs_location_type == 'countries_only') {
                    if (isset($radius_array) && is_array($radius_array)) {

                        $location_condition_arr[] = array(
                            'relation' => 'AND',
                            array(
                                'relation' => 'OR',
                                array(
                                    'key' => 'cs_post_loc_city',
                                    'value' => $location,
                                    'compare' => '=',
                                ),
                                array(
                                    'key' => 'cs_post_loc_country',
                                    'value' => $location,
                                    'compare' => '=',
                                ),
                                $radius_array,
                            )
                        );
                        // for count query
                        $cus_fields_count_arr['location'][] = array(
                            'relation' => 'AND',
                            array(
                                'relation' => 'OR',
                                array(
                                    'key' => 'cs_post_loc_city',
                                    'value' => $location,
                                    'compare' => '=',
                                ),
                                array(
                                    'key' => 'cs_post_loc_country',
                                    'value' => $location,
                                    'compare' => '=',
                                ),
                                $radius_array,
                            )
                        );
                    } else {
                        $location_condition_arr[] = array(
                            'relation' => 'OR',
                            array(
                                'key' => 'cs_post_loc_city',
                                'value' => $location,
                                'compare' => '=',
                            ),
                            array(
                                'key' => 'cs_post_loc_country',
                                'value' => $location,
                                'compare' => '=',
                            )
                        );

                        // for count query
                        $cus_fields_count_arr['location'][] = array(
                            'key' => 'cs_post_loc_city',
                            'value' => $location,
                            'compare' => '=',
                        );
                        $cus_fields_count_arr['location'][] = array(
                            'key' => 'cs_post_loc_country',
                            'value' => $location,
                            'compare' => '=',
                        );
                    }
                } elseif ($cs_location_type == 'cities_only' || $cs_location_type == 'single_city') {

                    if (isset($radius_array) && is_array($radius_array)) {
                        $location_condition_arr[] = array(
                            'relation' => 'AND',
                            array(
                                'relation' => 'OR',
                                array(
                                    'key' => 'cs_post_loc_city',
                                    'value' => $location,
                                    'compare' => '=',
                                ),
                                $radius_array,
                            )
                        );
                        // for count query
                        $cus_fields_count_arr['location'][] = array(
                            'relation' => 'AND',
                            array(
                                'relation' => 'OR',
                                array(
                                    'key' => 'cs_post_loc_city',
                                    'value' => $location,
                                    'compare' => '=',
                                ),
                                $radius_array,
                            )
                        );
                    } else {
                        $location_condition_arr[] = array(
                            'key' => 'cs_post_loc_city',
                            'value' => $location,
                            'compare' => '=',
                        );

                        // for count query
                        $cus_fields_count_arr['location'][] = array(
                            'key' => 'cs_post_loc_city',
                            'value' => $location,
                            'compare' => '=',
                        );
                    }
                }
            }

            // posted date check
            if ($posted != '') {
                $lastdate = '';
                $now = '';
                $qrystr .= '&posted=' . $posted;  // added again this var in query string for linking again
                if ($posted == 'lasthour') {
                    $now = date($default_date_time_formate);
                    $lastdate = date($default_date_time_formate, strtotime('-1 hours', time()));
                } elseif ($posted == 'last5hour') {
                    $now = date($default_date_time_formate);
                    $lastdate = date($default_date_time_formate, strtotime('-5 hours', time()));
                } elseif ($posted == 'last24') {
                    $now = date($default_date_time_formate);
                    $lastdate = date($default_date_time_formate, strtotime('-24 hours', time()));
                } elseif ($posted == '7days') {
                    $now = date($default_date_time_formate);
                    $lastdate = date($default_date_time_formate, strtotime('-7 days', time()));
                } elseif ($posted == '14days') {
                    $now = date($default_date_time_formate);
                    $lastdate = date($default_date_time_formate, strtotime('-14 days', time()));
                } elseif ($posted == '30days') {
                    $now = date($default_date_time_formate);
                    $lastdate = date($default_date_time_formate, strtotime('-30 days', time()));
                }
                if ($lastdate != '' && $now != '') {
                    $filter_arr[] = array(
                        'key' => 'cs_user_last_activity_date',
                        'value' => strtotime($lastdate),
                        'compare' => '>=',
                    );

                    // for count query
                    $cus_fields_count_arr['posted'][] = array(
                        'key' => 'cs_user_last_activity_date',
                        'value' => strtotime($lastdate),
                        'compare' => '>=',
                    );
                }
            }

            $filter_arr2[] = '';
            // specialism check

            if ($specialisms != '' && $specialisms != 'All specialisms') {
                foreach ($specialisms as $specialisms_key) {
                    $filter_arr[] = array(
                        'key' => 'cs_specialisms',
                        'value' => $specialisms_key,
                        'compare' => 'LIKE',
                    );
                }
            }


            $cs_candidate_cus_fields = get_option("cs_candidate_cus_fields");
            if (is_array($cs_candidate_cus_fields) && sizeof($cs_candidate_cus_fields) > 0) {
                foreach ($cs_candidate_cus_fields as $cus_field) {
                    if (isset($cus_field['enable_srch']) && $cus_field['enable_srch'] == 'yes') {
                        $query_str_var_name = $cus_field['meta_key'];
                        if (isset($_GET[$query_str_var_name]) && $_GET[$query_str_var_name] != '') {
                            if (!isset($cus_field['multi']) || $cus_field['multi'] != 'yes') {

                                $qrystr .= '&' . $query_str_var_name . '=' . $_GET[$query_str_var_name];
                            }
                            if ($cus_field['type'] == 'dropdown') {
                                if (isset($cus_field['multi']) && $cus_field['multi'] == 'yes') {
                                    $_query_string_arr = getMultipleParameters();

                                    $filter_multi_arr = array('relation' => 'OR');
                                    foreach ($_query_string_arr[$query_str_var_name] as $query_str_var_name_key) {
                                        if ($cus_field['post_multi'] == 'yes') {
                                            $filter_multi_arr[] = array(
                                                'key' => $query_str_var_name,
                                                'value' => serialize($query_str_var_name_key),
                                                'compare' => 'Like',
                                            );
                                        } else {
                                            $filter_multi_arr[] = array(
                                                'key' => $query_str_var_name,
                                                'value' => $query_str_var_name_key,
                                                'compare' => '=',
                                            );
                                        }
                                        $qrystr .= '&' . $query_str_var_name . '=' . $query_str_var_name_key;
                                    }
                                    $filter_arr[] = array(
                                        $filter_multi_arr
                                    );
                                    // for count query
                                    $cus_fields_count_arr[$query_str_var_name][] = array(
                                        $filter_multi_arr
                                    );
                                } else {
                                    if ($cus_field['post_multi'] == 'yes') {
                                        $filter_arr[] = array(
                                            'key' => $query_str_var_name,
                                            'value' => serialize($_GET[$query_str_var_name]),
                                            'compare' => 'Like',
                                        );
                                        // for count query
                                        $cus_fields_count_arr[$query_str_var_name][] = array(
                                            'key' => $query_str_var_name,
                                            'value' => serialize($_GET[$query_str_var_name]),
                                            'compare' => 'Like',
                                        );
                                    } else {
                                        $filter_arr[] = array(
                                            'key' => $query_str_var_name,
                                            'value' => $_GET[$query_str_var_name],
                                            'compare' => '=',
                                        );
                                        // for count query
                                        $cus_fields_count_arr[$query_str_var_name][] = array(
                                            'key' => $query_str_var_name,
                                            'value' => $_GET[$query_str_var_name],
                                            'compare' => '=',
                                        );
                                    }
                                }
                            } elseif ($cus_field['type'] == 'text' || $cus_field['type'] == 'email' || $cus_field['type'] == 'url') {
                                $filter_arr[] = array(
                                    'key' => $query_str_var_name,
                                    'value' => $_GET[$query_str_var_name],
                                    'compare' => 'LIKE',
                                );
                                // for count query
                                $cus_fields_count_arr[$query_str_var_name][] = array(
                                    'key' => $query_str_var_name,
                                    'value' => $_GET[$query_str_var_name],
                                    'compare' => 'LIKE',
                                );
                            } elseif ($cus_field['type'] == 'range') {
                                $ranges_str_arr = explode("-", $_GET[$query_str_var_name]);
                                if (!isset($ranges_str_arr[1])) {
                                    $ranges_str_arr = explode(",", $ranges_str_arr[0]);
                                }
                                $range_first = $ranges_str_arr[0];
                                $range_seond = $ranges_str_arr[1];
                                $filter_arr[] = array(
                                    'key' => $query_str_var_name,
                                    'value' => $range_first,
                                    'compare' => '>=',
                                );
                                $filter_arr[] = array(
                                    'key' => $query_str_var_name,
                                    'value' => $range_seond,
                                    'compare' => '<=',
                                );
                                // for count query
                                $cus_fields_count_arr[$query_str_var_name][] = array(
                                    'key' => $query_str_var_name,
                                    'value' => $range_first,
                                    'compare' => '>=',
                                );
                                // for count query
                                $cus_fields_count_arr[$query_str_var_name][] = array(
                                    'key' => $query_str_var_name,
                                    'value' => $range_seond,
                                    'compare' => '<=',
                                );
                            } elseif ($cus_field['type'] == 'date') {
                                $filter_arr[] = array(
                                    'key' => $query_str_var_name,
                                    'value' => $_GET[$query_str_var_name],
                                    'compare' => 'LIKE',
                                );
                                $cus_fields_count_arr[$query_str_var_name][] = array(
                                    'key' => $query_str_var_name,
                                    'value' => $_GET[$query_str_var_name],
                                    'compare' => 'LIKE',
                                );
                            }
                        }
                    }
                }
            }

            $meta_post_ids_arr = '';
            $candidatename_id_condition = '';
            if (isset($filter_arr) && !empty($filter_arr)) {
                $meta_post_ids_arr = cs_get_query_whereclase_by_array($filter_arr, true);
                // if no result found in filtration 
                if (empty($meta_post_ids_arr)) {
                    $meta_post_ids_arr = array(0);
                }
                $ids = $meta_post_ids_arr != '' ? implode(",", $meta_post_ids_arr) : '0';
                $candidatename_id_condition = " ID in (" . $ids . ") AND ";
            }
            $cs_candidatename = '';
            if (isset($_GET['cs_candidatename'])) {
                $cs_candidatename = $_GET['cs_candidatename'];
                $cs_candidatename = str_replace("+", " ", $cs_candidatename);
            }
            $mypost = '';
            if ($cs_candidatename != '') {
                $qrystr .= '&cs_candidatename=' . $cs_candidatename; // using this in paging
                //$post_ids = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE " . $candidatename_id_condition . " UCASE(post_title) LIKE '%$cs_candidatename%' AND post_type='candidate' AND post_status='publish'");
                $post_ids = $wpdb->get_col("SELECT ID FROM $wpdb->users WHERE " . $candidatename_id_condition . " UCASE(display_name) LIKE '%$cs_candidatename%'");
                if ($post_ids) {

                    $mypost = array('role' => 'cs_candidate', 'order' => 'DESC', 'orderby' => 'registered', 'user_status' => 1,
                        'include' => $post_ids,
                        'meta_query' => array(
                            array(
                                'key' => 'cs_user_status',
                                'value' => 'active',
                                'compare' => '=',
                            ),
                            array(
                                'relation' => 'OR',
                                array(
                                    'key' => 'cs_allow_search',
                                    'value' => 'yes',
                                    'compare' => '=',
                                ),
                                array(
                                    'key' => 'cs_allow_search',
                                    'value' => '',
                                    'compare' => '=',
                                ),
                            ),
                            $location_condition_arr,
                        )
                    );
                }
            } else {
                $mypost = array('role' => 'cs_candidate', 'order' => 'DESC', 'orderby' => 'registered', 'user_status' => 1,
                    'include' => $meta_post_ids_arr,
                    'meta_query' => array(
                        array(
                            'relation' => 'OR',
                            array(
                                'key' => 'cs_allow_search',
                                'value' => 'yes',
                                'compare' => '=',
                            ),
                            array(
                                'key' => 'cs_allow_search',
                                'value' => '',
                                'compare' => '=',
                            ),
                        ),
                        $location_condition_arr,
                        array(
                            'key' => 'cs_user_status',
                            'value' => 'active',
                            'compare' => '=',
                        ),
                    )
                );
            }
            $loop_count = new WP_User_Query($mypost);
            $count_post = $loop_count->total_users;
            // Map start
            include('candidate-map.php');

            $args = '';
            if ($count_post > 0) {
                if ($a['cs_candidate_show_pagination'] == 'pagination') {
                    $cs_blog_num_post = $a['cs_candidate_pagination']; //pick from atribute 
                } else {

                    if (isset($a['cs_candidate_pagination']) and $a['cs_candidate_pagination'] <> '') {
                        if ($a['cs_candidate_pagination'] != 0)
                            $cs_blog_num_post = $a['cs_candidate_pagination'];
                        else
                            $cs_blog_num_post = "999999";
                    } else {
                        $cs_blog_num_post = "999999";
                    }
                }

                $total_users = $count_post;

                // grab the current page number and set to 1 if no page number is set
                $page = isset($_GET['page_id_all']) ? $_GET['page_id_all'] : 1;

                // how many users to show per page
                $users_per_page = absint($cs_blog_num_post);

                // calculate the total number of pages.
                $total_pages = 1;
                $offset = 1;

                if ($users_per_page > 0) {
                    $offset = $users_per_page * ($page - 1);
                }
                if ($total_users > 0 && $users_per_page > 0) {
                    $total_pages = ceil($total_users / $users_per_page);
                }

                if ($cs_candidatename != '') {

                    //$post_ids = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE " . $candidatename_id_condition . " UCASE(post_title) LIKE '%$cs_candidatename%' AND post_type='candidate' AND post_status='publish'");
                    $post_ids = $wpdb->get_col("SELECT ID FROM $wpdb->users WHERE " . $candidatename_id_condition . " UCASE(display_name) LIKE '%$cs_candidatename%'");
                    if ($post_ids) {

                        $args = array('number' => $users_per_page, 'role' => 'cs_candidate', 'user_status' => 1, 'offset' => $offset, 'order' => 'DESC', 'orderby' => 'registered',
                            'include' => $post_ids,
                            'meta_query' => array(
                                array(
                                    'key' => 'cs_user_status',
                                    'value' => 'active',
                                    'compare' => '=',
                                ),
                                array(
                                    'relation' => 'OR',
                                    array(
                                        'key' => 'cs_allow_search',
                                        'value' => 'yes',
                                        'compare' => '=',
                                    ),
                                    array(
                                        'key' => 'cs_allow_search',
                                        'value' => '',
                                        'compare' => '=',
                                    ),
                                ),
                                $location_condition_arr,
                            )
                        );
                    }
                } else {

                    $args = array('number' => $users_per_page, 'role' => 'cs_candidate', 'offset' => $offset, 'order' => 'DESC', 'orderby' => 'registered', 'user_status' => 1,
                        'include' => $meta_post_ids_arr,
                        'meta_query' => array(
                            array(
                                'key' => 'cs_user_status',
                                'value' => 'active',
                                'compare' => '=',
                            ),
                            array(
                                'relation' => 'OR',
                                array(
                                    'key' => 'cs_allow_search',
                                    'value' => 'yes',
                                    'compare' => '=',
                                ),
                                array(
                                    'key' => 'cs_allow_search',
                                    'value' => '',
                                    'compare' => '=',
                                ),
                            ),
                            $location_condition_arr,
                        )
                    );
                }
            }

            if ($cs_chek_section_view == 'wide' && $cs_candidate_map == 'yes') {
                echo '<div class="container">';
            }
            if ($cs_candidate_title <> '') {
                ?>
                <div class="cs-element-title">
                    <h2><?php echo esc_html($cs_candidate_title); ?></h2>
                </div>
                <?php
            }
            if ($cs_candidate_searchbox_top == 'yes') {
                include plugin_dir_path(__FILE__) . 'candidate-top-view-search.php';
            }

            if ($a['cs_candidate_searchbox'] == 'yes') {
                echo '<div class="row">';
                echo '<div class="cs-content-holder">';
            }
            if ($a['cs_candidate_searchbox'] == 'yes') {
                $random_id = rand(50, 99999);
                ?>            
                <!-- specialism popup -->
                <div class="modal fade" id="light" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">

                            <div class="modal-body">
                                <div class="white_content">
                                    <a class="close" data-dismiss="modal">&nbsp;</a>
                                    <form action="#" method="get" id="frm_all_specialisms<?php echo esc_html($random_id); ?>" >
                                        <?php
                                        $final_query_str = str_replace("?", "", $qrystr);
                                        $final_query_str = cs_remove_qrystr_extra_var($final_query_str, 'specialisms', 'no');
                                        $query = explode('&', $final_query_str);
                                        foreach ($query as $param) {
                                            if (!empty($param)) {
                                                list($name, $value) = explode('=', $param);
                                                $new_str = $name . "=" . $value;
                                                if (is_array($name)) {
                                                    foreach ($_query_str_single_value as $_query_str_single_value_arr) {
                                                        $cs_opt_array = array(
                                                            'std' => $value,
                                                            'id' => '',
                                                            'return' => false,
                                                            'cust_name' => $name . '[]',
                                                            'prefix' => '',
                                                        );
                                                        echo force_balance_tags($cs_form_fields2->cs_form_hidden_render($cs_opt_array));
                                                    }
                                                } else {

                                                    $cs_opt_array = array(
                                                        'std' => $value,
                                                        'id' => '',
                                                        'return' => false,
                                                        'cust_name' => $name,
                                                        'prefix' => '',
                                                    );
                                                    force_balance_tags($cs_form_fields2->cs_form_hidden_render($cs_opt_array));
                                                }
                                            }
                                        }
                                        ?>
                                        <ul class="custom-listing">
                                            <?php
                                            // get all job types
                                            $specialisms_parent_id = 0;
                                            $input_type_specialism = 'radio';   // if first level then select only sigle specialism
                                            if ($specialisms != '') {
                                                $selected_spec = get_term_by('slug', $specialisms[0], 'specialisms');
                                                $specialisms_parent_id = $selected_spec->term_id;
                                            }
                                            $specialisms_args = array(
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
                                                    'fields' => 'all',
                                                    'slug' => '',
                                                    'hide_empty' => false,
													'parent' => isset($selected_spec->parent) ? $selected_spec->parent : '',
                                                );
                                                $all_specialisms = get_terms('specialisms', $specialisms_args);
                                                if (isset($selected_spec->parent) && $selected_spec->parent != 0) {    // if parent is not root means not main parent
                                                    $input_type_specialism = 'checkbox';   // if first level then select multiple specialism
                                                }
                                            } else {
                                                if ($specialisms_parent_id != 0) {    // if parent is not root means not main parent
                                                    $input_type_specialism = 'checkbox';   // if first level then select multiple specialism
                                                }
                                            }
                                            if ($input_type_specialism == 'checkbox') {
                                                $cs_opt_array = array(
                                                    'std' => '',
                                                    'id' => '',
                                                    'return' => true,
                                                    'cust_id' => 'specialisms_string_all',
                                                    'cust_name' => 'specialisms_string_all',
                                                    'prefix' => '',
                                                );
                                                echo force_balance_tags($cs_form_fields2->cs_form_hidden_render($cs_opt_array));
                                            }
                                            $number_option = rand(0, 99999999);
                                            if ($all_specialisms != '') {
                                                $number_option = rand(0, 99999999);
                                                foreach ($all_specialisms as $specialismsitem) {
                                                    // get count for this itration 
                                                    $job_id_para = '';
                                                    if ($cs_candidatename != '') {

                                                        //$post_ids = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE " . $candidatename_id_condition . " UCASE(post_title) LIKE '%$cs_candidatename%' AND post_type='candidate' AND post_status='publish'");
                                                        $post_ids = $wpdb->get_col("SELECT ID FROM $wpdb->users WHERE " . $candidatename_id_condition . " AND UCASE(display_name) LIKE '%$cs_candidatename%'");
                                                        if ($post_ids) {

                                                            $specialisms_mypost = array('role' => 'cs_candidate', 'order' => 'DESC', 'orderby' => 'registered',
                                                                'include' => $post_ids,
                                                                'meta_query' => array(
                                                                    array(
                                                                        'key' => 'cs_user_status',
                                                                        'value' => 'active',
                                                                        'compare' => '=',
                                                                    ),
                                                                    array(
                                                                        array(
                                                                            'key' => 'cs_specialisms',
                                                                            'value' => $specialismsitem->slug,
                                                                            'compare' => 'LIKE',
                                                                        ),
                                                                    ),
                                                                    array(
                                                                        'relation' => 'OR',
                                                                        array(
                                                                            'key' => 'cs_allow_search',
                                                                            'value' => 'yes',
                                                                            'compare' => '=',
                                                                        ),
                                                                        array(
                                                                            'key' => 'cs_allow_search',
                                                                            'value' => '',
                                                                            'compare' => '=',
                                                                        )
                                                                    ),
                                                                )
                                                            );
                                                        }
                                                    } else {

                                                        $specialisms_mypost = array('role' => 'cs_candidate', 'order' => 'DESC', 'orderby' => 'registered',
                                                            'include' => $meta_post_ids_arr,
                                                            'meta_query' => array(
                                                                array(
                                                                    'key' => 'cs_user_status',
                                                                    'value' => 'active',
                                                                    'compare' => '=',
                                                                ),
                                                                array(
                                                                    array(
                                                                        'key' => 'cs_specialisms',
                                                                        'value' => $specialismsitem->slug,
                                                                        'compare' => 'LIKE',
                                                                    ),
                                                                ),
                                                                array(
                                                                    'relation' => 'OR',
                                                                    array(
                                                                        'key' => 'cs_allow_search',
                                                                        'value' => 'yes',
                                                                        'compare' => '=',
                                                                    ),
                                                                    array(
                                                                        'key' => 'cs_allow_search',
                                                                        'value' => '',
                                                                        'compare' => '=',
                                                                    )
                                                                ),
                                                            )
                                                        );
                                                    }

                                                    $specialisms_loop_count = new WP_User_Query($specialisms_mypost);
                                                    $specialisms_count_post = $specialisms_loop_count->total_users;

                                                    if ($input_type_specialism == 'checkbox') {
                                                        if (isset($specialisms) && is_array($specialisms)) {
                                                            if (in_array($specialismsitem->slug, $specialisms)) {
                                                                echo '<li class="' . $input_type_specialism . '">';
                                                                $cs_opt_array = array(
                                                                    'std' => $specialismsitem->slug,
                                                                    'id' => '',
                                                                    'return' => true,
                                                                    'cust_id' => 'checklistcomplete' . $number_option,
                                                                    'cust_type' => $input_type_specialism,
                                                                    'extra_atr' => 'onchange="javascript:submit_specialism_form(\'frm_all_specialisms' . $random_id . '\', \'specialisms_string_all\');" checked="checked',
                                                                    'prefix' => '',
                                                                );
                                                                echo force_balance_tags($cs_form_fields2->cs_form_text_render($cs_opt_array));

                                                                echo '<label for="checklist' . $number_option . '">' . $specialismsitem->name . ' <span>(' . $specialisms_count_post . ')</span></label></li>';
                                                            } else {

                                                                echo '<li class="' . $input_type_specialism . '">';
                                                                $cs_opt_array = array(
                                                                    'std' => $specialismsitem->slug,
                                                                    'id' => '',
                                                                    'return' => true,
                                                                    'cust_id' => 'checklistcomplete' . $number_option,
                                                                    'cust_type' => $input_type_specialism,
                                                                    'extra_atr' => ' onchange="submit_specialism_form(\'frm_all_specialisms' . $random_id . '\', \'specialisms_string_all\');',
                                                                    'prefix' => '',
                                                                );
                                                                echo force_balance_tags($cs_form_fields2->cs_form_text_render($cs_opt_array));

                                                                echo '<label for="checklist' . $number_option . '">' . $specialismsitem->name . '<span>(' . $specialisms_count_post . ')</span></label></li>';
                                                            }
                                                        } else {
                                                            echo '<li class="' . $input_type_specialism . '">';

                                                            $cs_opt_array = array(
                                                                'std' => $specialismsitem->slug,
                                                                'id' => '',
                                                                'return' => true,
                                                                'cust_id' => 'checklistcomplete' . $number_option,
                                                                'cust_type' => $input_type_specialism,
                                                                'extra_atr' => 'onchange="submit_specialism_form(\'frm_all_specialisms' . $random_id . '\', \'specialisms_string_all\');"',
                                                                'prefix' => '',
                                                            );
                                                            echo force_balance_tags($cs_form_fields2->cs_form_text_render($cs_opt_array));

                                                            echo ' <label for="checklist' . $number_option . '">' . $specialismsitem->name . '<span>(' . $specialisms_count_post . ')</span></label></li>';
                                                        }
                                                    } else
                                                    if ($input_type_specialism == 'radio') {

                                                        if (isset($specialisms) && is_array($specialisms)) {
                                                            if (in_array($specialismsitem->slug, $specialisms)) {
                                                                echo '<li class="' . $input_type_specialism . '">';
                                                                $cs_opt_array = array(
                                                                    'std' => $specialismsitem->slug,
                                                                    'id' => '',
                                                                    'return' => true,
                                                                    'cust_id' => 'checklistcomplete' . $number_option,
                                                                    'cust_type' => $input_type_specialism,
                                                                    'cust_name' => 'specialisms',
                                                                    'extra_atr' => 'onchange="javascript:frm_all_specialisms' . $random_id . '.submit();""  checked="checked"',
                                                                    'prefix' => '',
                                                                );
                                                                echo force_balance_tags($cs_form_fields2->cs_form_text_render($cs_opt_array));
                                                                echo '<label  class="active" for="checklistcomplete' . $number_option . '">' . $specialismsitem->name . ' <span>(' . $specialisms_count_post . ')</span>  <i class="icon-check-circle"></i></label></li>';
                                                            } else {
                                                                echo '<li class="' . $input_type_specialism . '">';
                                                                $cs_opt_array = array(
                                                                    'std' => $specialismsitem->slug,
                                                                    'id' => '',
                                                                    'return' => true,
                                                                    'cust_id' => 'checklistcomplete' . $number_option,
                                                                    'cust_type' => $input_type_specialism,
                                                                    'cust_name' => 'specialisms',
                                                                    'extra_atr' => 'onchange="javascript:frm_all_specialisms' . $random_id . '.submit();" ',
                                                                    'prefix' => '',
                                                                );
                                                                echo force_balance_tags($cs_form_fields2->cs_form_text_render($cs_opt_array));

                                                                echo '<label for="checklistcomplete' . $number_option . '">' . $specialismsitem->name . '<span>(' . $specialisms_count_post . ')</span></label></li>';
                                                            }
                                                        } else
                                                            echo '<li class="' . $input_type_specialism . '">';
                                                        $cs_opt_array = array(
                                                            'std' => $specialismsitem->slug,
                                                            'id' => '',
                                                            'return' => true,
                                                            'cust_id' => 'checklistcomplete' . $number_option,
                                                            'cust_type' => $input_type_specialism,
                                                            'cust_name' => 'specialisms',
                                                            'extra_atr' => 'onchange="javascript:frm_all_specialisms' . $random_id . '.submit();" ',
                                                            'prefix' => '',
                                                        );
                                                        echo force_balance_tags($cs_form_fields2->cs_form_text_render($cs_opt_array));
                                                        echo '<label for="checklistcomplete' . $number_option . '">' . $specialismsitem->name . '<span>(' . $specialisms_count_post . ')</span></label></li>';
                                                    }
                                                    $number_option++;
                                                }
                                            }
                                            $cs_candidate_view = $a['cs_candidate_view'];
                                            $cs_candidate_title = $a['cs_candidate_title'];
                                            ?>
                                        </ul>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="fade" class="black_overlay"></div>
                <?php
                include('candidate-searchbox.php');
            }
            if (isset($cs_candidate_view) and $cs_candidate_view == "grid") {
                include('views/cs_grid.php');
            } else if (isset($cs_candidate_view) and $cs_candidate_view == "box") {
                include('views/cs_box.php');
            } else {
                include 'views/cs_list.php';
            }
            if ($a['cs_candidate_searchbox'] == 'yes') {
                echo '</div>';
                echo '</div>';
            }
            if ($cs_chek_section_view == 'wide' && $cs_candidate_map == 'yes') {
                echo '</div>';
            }

//		if ($column_class != '') {
//            echo '</div>';
//        }
            if (isset($column_class) && $column_class != '') {
                ?>
            </div>
            <?php
        }
        $candidate_post_data = ob_get_clean();
        return $candidate_post_data;
    }

    add_shortcode('cs_candidate', 'cs_candidate_listing');
}

if (!function_exists('cs_add_to_list')) {

    function cs_add_to_list() {

        global $cs_plugin_options, $cs_form_fields2, $current_user;
        $cs_candidate_switch = isset($cs_plugin_options['cs_candidate_switch']) ? $cs_plugin_options['cs_candidate_switch'] : '';
        $cs_emp_funs = new cs_employer_functions();
        $cs_json = array();
        $cs_id = isset($_POST['id']) ? $_POST['id'] : '';
        $cs_candidate = (string) $cs_id;
        $cs_json['msg'] = __("Error.", 'jobhunt');
        $cs_json['btn_txt'] = __("Added", 'jobhunt');

        if ($cs_candidate_switch == 'on' && $cs_emp_funs->is_employer() && $cs_emp_funs->is_cv_pkg_subs() && $cs_candidate != '') {

            $cs_subs_pkg = $cs_emp_funs->is_cv_pkg_subs(true);
            $cs_trans_id = isset($cs_subs_pkg['trans_id']) ? $cs_subs_pkg['trans_id'] : '';
            $cs_emp_id = $current_user->ID;

            $cs_resume_ids = get_post_meta($cs_trans_id, "cs_resume_ids", true);
            $cs_fav_resumes = get_user_meta($cs_emp_id, "cs_fav_resumes", true);

            if (empty($cs_fav_resumes)) {
                $cs_fav_resumes = array();
            }

            if (substr($cs_resume_ids, 0, 1) == ',') {
                $cs_resume_ids = substr($cs_resume_ids, 1);
            }

            if (!empty($cs_resume_ids)) {
                $cs_resume_ids = explode(',', $cs_resume_ids);
            } else {
                $cs_resume_ids = array();
            }

            $cs_fav_resumes[] = $cs_candidate;

            $cs_fav_resumes = array_unique($cs_fav_resumes);

            if (!empty($cs_fav_resumes)) {
                update_user_meta($cs_emp_id, 'cs_fav_resumes', $cs_fav_resumes);
            }

            $cs_resume_ids[] = $cs_candidate;

            if (!empty($cs_resume_ids)) {
                $cs_resume_ids_array = implode(',', $cs_resume_ids);
                update_post_meta($cs_trans_id, "cs_resume_ids", $cs_resume_ids_array);
            }

            // Set Favorite in Session
            $cs_emp_funs->cs_set_user_fav($cs_candidate);
            $cs_json['msg'] = __("Added Successfully.", 'jobhunt');
            $cs_json['btn_txt'] = __("Added", 'jobhunt');
        } else if ($cs_candidate_switch == 'on' && $cs_emp_funs->is_employer() && !$cs_emp_funs->is_cv_pkg_subs() && $cs_candidate != '') {
            $cs_json['msg'] = __("Please subscribe a package first to Add to List", 'jobhunt');
            $cs_json['btn_txt'] = __("Add to List", 'jobhunt');
        } else if ($cs_candidate_switch != 'on' && $cs_emp_funs->is_employer() && $cs_candidate != '') {
            $cs_emp_id = $current_user->ID;
            $cs_fav_resumes = get_user_meta($cs_emp_id, "cs_fav_resumes", true);

            $cs_fav_resumes = unserialize($cs_fav_resumes);

            if (!in_array($cs_candidate, $cs_fav_resumes) && $cs_fav_resumes[0] != '') {

                $cs_fav_resumes_array = array_merge($cs_fav_resumes, array($cs_candidate));
                $cs_fav_resumes_array = serialize($cs_fav_resumes_array);
                update_user_meta($cs_emp_id, "cs_fav_resumes", $cs_fav_resumes_array);
            } else if (!in_array($cs_candidate, $cs_fav_resumes) && $cs_fav_resumes[0] == '') {
                update_user_meta($cs_emp_id, "cs_fav_resumes", serialize(array($cs_candidate)));
            }

            // Set Favorite in Session
            $cs_emp_funs->cs_set_user_fav($cs_candidate);
            $cs_json['msg'] = __("Added Successfully.", 'jobhunt');
            $cs_json['btn_txt'] = __("Added", 'jobhunt');
        }
        echo json_encode($cs_json);
        die;
    }

    add_action('wp_ajax_cs_add_to_list', 'cs_add_to_list');
}
if (!function_exists('cs_add_favr')) {

    function cs_add_favr() {
        global $current_user, $cs_form_fields2;
        $user = cs_get_user_id();
        $cs_json = array();
        $cs_id = isset($_POST['id']) ? $_POST['id'] : '';
        $type = isset($_POST['type']) ? $_POST['type'] : '';
        $cs_candidate = (string) $cs_id;
        if ($type == 'add') {
            cs_create_user_meta_list($cs_candidate, 'cs-user-resumes-wishlist', $user);
            $cs_json['msg'] = __("Added to shortlist", 'jobhunt');
            $cs_json['btn_txt'] = '<i class="icon-minus-circle"></i>' . __("Shortlisted", 'jobhunt');
            $cs_json['class'] = "add";
        } elseif ($type == 'remove') {
            cs_remove_from_user_meta_list($cs_candidate, 'cs-user-resumes-wishlist', $user);
            $cs_json['msg'] = __("Removed from shortlist", 'jobhunt');
            $cs_json['btn_txt'] = '<i class="icon-plus-circle"></i>' . __("Shortlist", 'jobhunt');
            $cs_json['class'] = "remove";
        }
        echo json_encode($cs_json);
        die;
    }

    add_action('wp_ajax_cs_add_favr', 'cs_add_favr');
}
