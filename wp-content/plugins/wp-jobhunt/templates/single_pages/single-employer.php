<?php
/**
 * The template for Employer Detail
 */
global $author, $current_user, $cs_plugin_options, $cs_form_fields2, $jobcareer_options;        // $jobcareer_options getting from theme if it exist

$cs_user_data = get_userdata($author);

$cs_uniq = rand(11111111, 99999999);
cs_set_post_views($cs_user_data->ID);
get_header();
/*
 *  login user detail
 *      
 */
?><div class="main-section">
    <div class="content-area" id="primary">
        <main class="site-main" id="main">
            <div class="post-1 post type-post status-publish format-standard hentry category-uncategorized">
                <!-- alert for complete theme -->
                <div class="cs_alerts" ></div>
                <?php
                $login_user_name = '';
                $login_user_email = '';
                $login_user_phone = '';
                $cs_emp_funs = new cs_employer_functions();
                if (is_user_logged_in()) {
                    $login_user_name = $current_user->display_name;
                    $login_user_email = $current_user->user_email;
                    $login_user_phone = get_user_meta($current_user->ID, 'cs_contact_information', true);
                }
                $cs_job_posted_date_formate = 'd-m-Y H:i:s';
                $cs_job_expired_date_formate = 'd-m-Y H:i:s';
                $cs_employee_address = get_user_address_string_for_list($cs_user_data->ID);
                $cs_employee_web_http = $cs_user_data->user_url;
                $cs_employee_email = $cs_user_data->user_email;
                $cs_employee_web = preg_replace('#^https?://#', '', $cs_employee_web_http);
                $cs_employee_facebook = get_user_meta($cs_user_data->ID, 'cs_facebook', true);
                $cs_employee_twitter = get_user_meta($cs_user_data->ID, 'cs_twitter', true);
                $cs_employee_linkedin = get_user_meta($cs_user_data->ID, 'cs_linkedin', true);
                $cs_employee_google_plus = get_user_meta($cs_user_data->ID, 'cs_google_plus', true);
                $cs_employee_contact = get_user_meta($cs_user_data->ID, 'cs_contact_information', true);
                $all_specialisms = get_user_meta($cs_user_data->ID, 'cs_specialisms', true);

                $specialisms_values = '';
                $specialisms_class = '';
                $specialism_html = '';
                $specialism_flag = 1;
                if ($all_specialisms != '') {
                    foreach ($all_specialisms as $specialisms_item) {
                        $specialismsitem = get_term_by('slug', $specialisms_item, 'specialisms');
                        if (is_object($specialismsitem)) {
                            if ($specialism_flag == 1) {
                                $specialism_html .= '<span class="cs-single-specialism" >' . $specialismsitem->name . '</a></span>';
                            } else {
                                $specialism_html .= '<span class="cs-single-specialism" >' . $specialismsitem->name . '</a></span>';
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
                $cs_employee_employer_img = get_user_meta($cs_user_data->ID, 'user_img', true);
                $cs_employee_employer_img = cs_get_img_url($cs_employee_employer_img, 'cs_media_5');
                if (!cs_image_exist($cs_employee_employer_img) || $cs_employee_employer_img == "") {
                    $cs_employee_employer_img = esc_url(wp_jobhunt::plugin_url() . 'assets/images/img-not-found16x9.jpg');
                }
                $cs_employee_cover_employer_img = get_user_meta($cs_user_data->ID, 'cover_user_img', true);
                $cs_employee_cover_employer_img = cs_get_img_url($cs_employee_cover_employer_img, '');

                $cs_employee_emp_username = $cs_user_data->ID;
                // getting all record of jobs for paging
                if (empty($_GET['page_id_all']))
                    $_GET['page_id_all'] = 1;
                $mypost = array('posts_per_page' => "-1", 'post_type' => 'jobs', 'order' => "DESC", 'orderby' => 'post_date',
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
                $loop_count = new WP_Query($mypost);
                $count_post = $loop_count->post_count;
                $cs_blog_num_post = '10';
                $cs_header_creds = cs_header_cover_style('employer', $cs_employee_cover_employer_img, '250');
                $cs_header_style = isset($cs_header_creds[0]) ? $cs_header_creds[0] : '';
                $cs_header_paralax_class = isset($cs_header_creds[1]) ? ' ' . $cs_header_creds[1] : '';
                $cs_employer_default_cover_style = isset($jobcareer_options['cs_employer_default_cover_style']) ? $jobcareer_options['cs_employer_default_cover_style'] . '-view' : '';
                ?>
                <section class="breadcrumb-sec">
                    <div class="employer-header<?php echo esc_html($cs_header_paralax_class) ?> <?php echo esc_html($cs_employer_default_cover_style) ?>"<?php echo ($cs_header_style != '' ? ' style="' . $cs_header_style . '"' : '') ?>></div>
                    <div class="page-section">
                        <div class="<?php if (isset($cs_plugin_options['cs_plugin_single_container']) && $cs_plugin_options['cs_plugin_single_container'] == 'on') echo 'container' ?>">
                            <div class="row">
                                <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                                    <div class="admin-info">
                                        <div class="cs-media">
                                            <?php if (isset($cs_employee_employer_img) && $cs_employee_employer_img <> "") { ?>
                                                <figure><a href="<?php echo esc_html(esc_url($cs_employee_web_http)); ?>"><img alt="Company Logo" src="<?php echo esc_url($cs_employee_employer_img); ?>"></a></figure>
                                            <?php } ?>
                                        </div>
                                        <div class="cs-text">
                                            <div class="cs-post-title"><h3><a><?php echo $cs_user_data->display_name; ?></a></h3></div>
                                            <?php if ($cs_employee_address != '') { ?><address><span><i class="icon-location6"></i><?php echo esc_html($cs_employee_address) ?></span></address><?php
                                            }
                                            if ($cs_employee_facebook != '' || $cs_employee_twitter != '' || $cs_employee_linkedin != '' || $cs_employee_google_plus != '') {
                                                ?>
                                                <ul class="employer-social-media">
                                                    <?php if ($cs_employee_facebook != '') { ?>
                                                        <li><a href="<?php echo esc_url($cs_employee_facebook) ?>" data-original-title="facebook"><i class="icon-facebook7"></i></a></li>
                                                    <?php } if ($cs_employee_twitter != '') { ?>
                                                        <li><a href="<?php echo esc_url($cs_employee_twitter) ?>" data-original-title="twitter"><i class="icon-twitter6"></i></a></li>
                                                    <?php } if ($cs_employee_linkedin != '') { ?>
                                                        <li><a href="<?php echo esc_url($cs_employee_linkedin) ?>" data-original-title="linkedin"><i class="icon-linkedin4"></i></a></li>
                                                    <?php } if ($cs_employee_google_plus != '') { ?>
                                                        <li><a href="<?php echo esc_url($cs_employee_google_plus) ?>" data-original-title="google"><i class="icon-googleplus7"></i></a></li>            
                                                    <?php } ?>
                                                </ul>
                                                <?php if ($cs_employee_web_http != '') { 
                                                    ?><span class="visit-website"><i class="icon-globe4"></i><a href="<?php echo esc_url($cs_employee_web_http); ?>"><?php _e("Visit Website", 'jobhunt') ?></a></span><?php
                                                }
                                                if (isset($specialism_html)) {
                                                    ?><div class="cs-specialism"><?php
                                                            echo force_balance_tags($specialism_html);
                                                            ?></div><?php
                                                }
                                                ?>
                                                <span class="vacancies"><strong class="cs-color"><?php echo absint($count_post) ?></strong><?php _e("vacancies", 'jobhunt') ?></span>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="company-detail">
                                        <ul class="employer-categories">
                                            <?php
                                            $cs_employer_cus_fields = get_option("cs_employer_cus_fields");
                                            if (is_array($cs_employer_cus_fields) && sizeof($cs_employer_cus_fields) > 0) {
                                                //echo '<li>';
                                                $custom_field_box = 1;
                                                foreach ($cs_employer_cus_fields as $cus_field) {
                                                    if ($cus_field['meta_key'] != '') {
                                                        $data = get_user_meta($cs_user_data->ID, $cus_field['meta_key'], true);
                                                        // empty check of value
                                                        if ($cus_field['label'] != '') {
                                                            if ($data != "") {
                                                                echo '<li>';
                                                                echo '<strong>';
                                                                echo esc_html($cus_field['label']);
                                                                echo '</strong>';
                                                                // check the data is array or not
                                                                if (is_array($data)) {
                                                                    $data_flage = 1;
                                                                    foreach ($data as $datavalue) {
                                                                        if ($cus_field['type'] == 'dropdown') {
                                                                            $options = $cus_field['options']['value'];
                                                                            if (isset($options)) {
                                                                                $finded_array = array_search($datavalue, $options);
                                                                                $datavalue = isset($finded_array) ? $cus_field['options']['label'][$finded_array] : '';
                                                                            }
                                                                            echo esc_html($datavalue);
                                                                        } else {
                                                                            echo esc_html($datavalue);
                                                                        }
                                                                        if ($data_flage != count($data)) {
                                                                            echo ", ";
                                                                        }
                                                                        $data_flage++;
                                                                    }
                                                                } else {
                                                                    if ($cus_field['type'] == 'dropdown') {
                                                                        $options = $cus_field['options']['value'];
                                                                        if (isset($options)) {
                                                                            $finded_array = array_search($data, $options);
                                                                            $data = isset($finded_array) ? $cus_field['options']['label'][$finded_array] : '';
                                                                        }
                                                                        echo esc_html($data);
                                                                    } else {
                                                                        echo esc_html($data);
                                                                    }
                                                                }

                                                                echo '</li>';
                                                                $custom_field_box++;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                            ?>
                                        </ul>

                                        <div class="cs-editor-text">
                                            <h4><?php printf(__('About %s', 'jobhunt'), $cs_user_data->display_name) ?></h4>
                                            <?php echo $cs_user_data->description ?>
                                        </div>
                                        <?php
                                        // getting job with page number
                                        $args = array('posts_per_page' => "$cs_blog_num_post", 'post_type' => 'jobs', 'paged' => $_GET['page_id_all'],
                                            'order' => 'DESC', 'orderby' => 'post_date', 'post_status' => 'publish', 'ignore_sticky_posts' => 1,
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
                                        $loop = new WP_Query($args);
                                        if ($count_post > 0) {
                                            global $cs_plugin_options;
                                            $cs_search_result_page = isset($cs_plugin_options['cs_search_result_page']) ? $cs_plugin_options['cs_search_result_page'] : '';
                                            ?><h4><?php
                                                _e('Jobs From', 'jobhunt');
                                                echo " " . $cs_user_data->display_name;
                                                ?></h4>

                                            <ul class="cs-company-jobs">
                                                <?php
                                                $loop = new WP_Query($args);
                                                $flag = 1;
                                                while ($loop->have_posts()) : $loop->the_post();
                                                    global $post;
                                                    $job_id = $post->ID;
                                                    $cs_job_posted = get_post_meta($job_id, 'cs_job_posted', true);

                                                    $cs_job_featured = get_post_meta($job_id, 'cs_job_featured', true);
                                                    // get all job types
                                                    $all_job_type = get_the_terms($job_id, 'job_type');
                                                    $job_type_values = '';
                                                    $job_type_class = '';
                                                    $job_type_flag = 1;
                                                    if ($all_job_type != '') {
                                                        foreach ($all_job_type as $job_type) {

                                                            $t_id_main = $job_type->term_id;
                                                            $job_type_color_arr = get_option("job_type_color_$t_id_main");
                                                            $job_type_color = '';
                                                            if (isset($job_type_color_arr['text'])) {
                                                                $job_type_color = $job_type_color_arr['text'];
                                                            }
                                                            //$job_type_class .= get_term_link($t_id_main);	
                                                            $cs_link = ' href="javascript:void(0);"';
                                                            if ($cs_search_result_page != '') {
                                                                $cs_link = ' href="' . esc_url_raw(get_page_link($cs_search_result_page) . '?job_type=' . $job_type->slug) . '"';
                                                            }
                                                            $job_type_values .= '<a ' . force_balance_tags($cs_link) . ' class="categories" style="color:' . $job_type_color . '">' . $job_type->name . '</a>';

                                                            if ($job_type_flag != count($all_job_type)) {
                                                                $job_type_values .= " ";
                                                                $job_type_class .= " ";
                                                            }
                                                            $job_type_flag++;
                                                        }
                                                    }
                                                    ?>
                                                    <li>

                                                        <div class="cs-text">
                                                            <?php if ($cs_job_featured == "yes" || $cs_job_featured == "on") { ?>     
                                                                <span class="listing-featered"><?php _e('FEATURED', 'jobhunt'); ?></span>
                                                            <?php } ?>
                                                            <h5><a href="<?php echo esc_url(get_permalink(get_the_id())); ?>"><?php echo the_title(); ?></a></h5>
                                                            <?php
                                                            if (isset($cs_job_posted) && $cs_job_posted != '') {
                                                                ?><span class="post-date"><small><?php _e('on', 'jobhunt'); ?></small> <?php echo cs_time_elapsed_string($cs_job_posted); ?></span>
                                                                    <?php
                                                                }
                                                                ?>

                                                            <?php echo force_balance_tags($job_type_values); ?>
                                                        </div>
                                                    </li>
                                                    <?php
                                                    $flag++;
                                                endwhile;
                                                wp_reset_postdata();
                                                ?> 
                                            </ul>
                                            <nav>
                                                <?php
                                                //==Pagination Start
                                                if ($count_post > $cs_blog_num_post && $cs_blog_num_post > 0) {
                                                    $qrystr = '';
                                                    if (isset($_GET['page_id']))
                                                        $qrystr .= "&amp;page_id=" . $_GET['page_id'];


                                                    echo cs_pagination($count_post, $cs_blog_num_post, $qrystr, 'Show Pagination');
                                                }
                                                //==Pagination End 
                                                ?>
                                            </nav>
                                            <?php
                                        }
                                        $cs_sitekey = isset($cs_plugin_options['cs_sitekey']) ? $cs_plugin_options['cs_sitekey'] : '';
                                        $cs_secretkey = isset($cs_plugin_options['cs_secretkey']) ? $cs_plugin_options['cs_secretkey'] : '';
                                        cs_google_recaptcha_scripts();
                                        ?>
                                        <script>
                                            var recaptcha7;
                                            var cs_multicap = function () {
                                                //Render the recaptcha1 on the element with ID "recaptcha1"

                                                //Render the recaptcha2 on the element with ID "recaptcha2"
                                                recaptcha7 = grecaptcha.render('recaptcha7', {
                                                    'sitekey': '<?php echo ($cs_sitekey); ?>', //Replace this with your Site key
                                                    'theme': 'light'
                                                });
                                            };

                                        </script>
                                    </div>
                                </div>
                                <aside class="section-sidebar col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                    <div class="employer-contact-form">
                                        <h4><?php echo __("Contact", "jobhunt") . ' ' . $cs_user_data->display_name; ?></h4>
                                        <div class="cs-profile-contact-detail" data-adminurl="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" data-cap="recaptcha7">
                                            <div id="cs_employer_contactus" data-validationmsg="<?php _e("Please ensure that all required fields are completed and formatted correctly", "jobs"); ?>">
                                                <form id="ajaxcontactemployer" action="#" method="post" enctype="multipart/form-data">
                                                    <div id="ajaxcontact-response" class=""></div>
                                                    <div class="input-filed">
                                                        <i class=" icon-user9"></i>  
                                                        <?php
                                                        $cs_opt_array = array(
                                                            'id' => '',
                                                            'std' => isset($login_user_name) ? esc_html($login_user_name) : '',
                                                            'cust_id' => "ajaxcontactname",
                                                            'cust_name' => "ajaxcontactname",
                                                            'classes' => 'form-control',
                                                            'extra_atr' => 'placeholder="' . __('Enter your Name', 'jobhunt') . '*"',
                                                            'required' => 'yes',
                                                        );
                                                        $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                                        ?>

                                                    </div>
                                                    <div class="input-filed">
                                                        <i class="icon-envelope4"></i>
                                                        <?php
                                                        $cs_opt_array = array(
                                                            'id' => '',
                                                            'std' => isset($login_user_email) ? esc_html($login_user_email) : '',
                                                            'cust_id' => "ajaxcontactemail",
                                                            'cust_name' => "ajaxcontactemail",
                                                            'classes' => 'form-control',
                                                            'extra_atr' => 'placeholder="' . __('Email Address', 'jobhunt') . '*"',
                                                            'required' => 'yes',
                                                        );
                                                        $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                                        ?>
                                                    </div>
                                                    <div class="input-filed">
                                                        <i class="icon-mobile4"></i>
                                                        <?php
                                                        $cs_opt_array = array(
                                                            'id' => '',
                                                            'std' => isset($login_user_phone) ? esc_html($login_user_phone) : '',
                                                            'cust_id' => "ajaxcontactphone",
                                                            'cust_name' => "ajaxcontactphone",
                                                            'classes' => 'form-control',
                                                            'extra_atr' => 'placeholder="' . __('Phone Number', 'jobhunt') . '"',
                                                        );
                                                        $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                                        ?>
                                                    </div>
                                                    <div class="input-filed">
                                                        <?php
                                                        $cs_opt_array = array(
                                                            'id' => '',
                                                            'std' => '',
                                                            'cust_id' => "ajaxcontactcontents",
                                                            'cust_name' => "ajaxcontactcontents",
                                                            'classes' => 'form-control',
                                                            'extra_atr' => 'placeholder="' . __('Message should have more than 50 characters', 'jobhunt') . '"',
                                                        );
                                                        $cs_form_fields2->cs_form_textarea_render($cs_opt_array);
                                                        ?>
                                                    </div>
                                                    <?php
                                                    global $cs_plugin_options;
                                                    $cs_captcha_switch = isset($cs_plugin_options['cs_captcha_switch']) ? $cs_plugin_options['cs_captcha_switch'] : '';

                                                    if ($cs_captcha_switch == 'on') {
                                                        echo '<div class="input-holder recaptcha-reload" id="recaptcha7_div">';
                                                        echo cs_captcha('recaptcha7');
                                                        echo '</div>';
                                                    }
                                                    ?>

                                                    <div class="submit-btn profile-contact-btn" data-employerid="<?php echo esc_html($cs_user_data->ID); ?>">                                          
                                                        <?php
                                                        $cs_opt_array = array(
                                                            'id' => '',
                                                            'std' => __('Send an Email', 'jobhunt'),
                                                            'cust_id' => "employerid_contactus",
                                                            'cust_name' => "employerid_contactus",
                                                            'classes' => 'acc-submit cs-bgcolor',
                                                            'extra_atr' => '',
                                                            'cust_type' => 'submit',
                                                        );
                                                        $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                                        ?>
                                                        <div id="loader-data"></div>
                                                    </div>

                                                    <?php
                                                    $cs_terms_condition = isset($cs_plugin_options['cs_terms_condition']) ? $cs_plugin_options['cs_terms_condition'] : '';
                                                    if ($cs_terms_condition != '') {
                                                        ?>
                                                        <span class="cs-terms"><?php _e('You accepts our', 'jobhunt') ?><a target="_blank" href="<?php echo esc_url(get_permalink($cs_terms_condition)) ?>"> <?php _e('Terms and Conditions', 'jobhunt') ?></a></span> 
                                                        <?php
                                                    }
                                                    ?>
                                                </form>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="widget widget-map">
                                        <?php
                                        $cs_jobcareer_theme_options = get_option('cs_theme_options');
                                        $cs_map_view = isset($cs_jobcareer_theme_options['def_map_style']) ? $cs_jobcareer_theme_options['def_map_style'] : '';

                                        $post_loc_latitude = get_user_meta($cs_user_data->ID, 'cs_post_loc_latitude', true);
                                        $post_loc_longitude = get_user_meta($cs_user_data->ID, 'cs_post_loc_longitude', true);
                                        $cs_post_loc_address = get_user_meta($cs_user_data->ID, 'cs_post_loc_address', true);
                                        $cs_post_comp_address = get_user_meta($cs_user_data->ID, 'cs_post_comp_address', true);
                                        $post_zoom_level = get_user_meta($cs_user_data->ID, 'cs_post_loc_zoom', true);
                                        if (!isset($post_zoom_level) || $post_zoom_level == '') {
                                            $post_zoom_level = '8';
                                        }
                                        $arg = array(
                                            'map_lat' => $post_loc_latitude,
                                            'map_lon' => $post_loc_longitude,
                                            'map_zoom' => $post_zoom_level,
                                            'map_height' => '200',
                                            'map_type' => 'ROADMAP',
                                            'map_info_width' => '455',
                                            'map_info_height' => '444',
                                            'map_marker_icon' => 'Browse',
                                            'map_show_marker' => 'true',
                                            'map_info' => $cs_post_loc_address,
                                            'map_marker_icon' => wp_jobhunt::plugin_url() . 'assets/images/recruiter-map-icon.png',
                                            'map_controls' => 'true',
                                            'map_draggable' => 'true',
                                            'map_scrollwheel' => 'true',
                                            'map_border' => 'yes',
                                            'cs_map_style' => $cs_map_view,
                                        );
                                        if (function_exists('cs_job_map')) {
                                            cs_job_map($arg);
                                        }
                                        ?>
                                        <div class="cs-loctions">
                                            <?php if ($cs_post_comp_address != '') { ?>
                                                <span>
                                                    <?php echo nl2br($cs_post_comp_address) ?>
                                                </span>
                                            <?php } if ($cs_employee_contact != '') { ?>
                                                <span><?php
                                                    _e('Phone: ', 'jobhunt');
                                                    echo esc_html($cs_employee_contact)
                                                    ?></span>
                                            <?php } if ($cs_employee_email != '') { ?>
                                                <span><?php
                                                    _e('Email: ', 'jobhunt');
                                                    echo sanitize_email($cs_employee_email)
                                                    ?></span>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </aside>

                            </div>

                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>
</div>
<?php
get_footer();
