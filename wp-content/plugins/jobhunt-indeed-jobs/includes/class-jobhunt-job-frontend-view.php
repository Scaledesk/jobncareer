<?php
// Direct access not allowed.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Job_Hunt_3_Columns_Job_View
 */
class Job_Hunt_Job_Fronend_View {

    /**
     * Constructor
     */
    public function __construct() {
        // 2 column job info filter
        add_filter('_2_columns_job_info', array($this, '_2_columns_job_info_frontend'), 1, 2);
        // 3 column job info filter
        add_filter('_3_columns_job_info', array($this, '_3_columns_job_info_frontend'), 1, 2);
        // fancy job info filter
        add_filter('fancy_job_info', array($this, 'fancy_job_info_frontend'), 1, 2);
        // map job info filter
        add_filter('map_job_info', array($this, 'map_job_info_frontend'), 1, 2);
        // classic job info filter
        add_filter('classic_job_info', array($this, 'classic_job_info_frontend'), 1, 2);
        // view more filter
        add_filter('view_more', array($this, 'view_more_frontend'), 1, 1);
        // apply buttons filter
        add_filter('apply_buttons', array($this, 'empty_frontend_callback'), 1, 2);
        // contact form filter
        add_filter('contact_form', array($this, 'empty_frontend_callback'), 1, 2);
        // company info filter
        add_filter('company_info', array($this, 'empty_frontend_callback'), 1, 2);
        // Social Links filter
        add_filter('social_links', array($this, 'empty_frontend_callback'), 1, 2);
        // related jobs filter
        add_filter('related_jobs', array($this, 'related_jobs_frontend'), 1, 2);
        // sidebar related jobs filter
        add_filter('sidebar_related_jobs', array($this, 'sidebar_related_jobs_frontend'), 1, 2);
        // 2 column job map filter
        add_filter('_2_columns_map', array($this, '_2_columns_map_frontend'), 1, 2);
    }

    /**
     * 2 column job info fronend view
     * @return content with html 
     */
    public function _2_columns_job_info_frontend($content, $post_id) {
        $cs_job_referral = get_post_meta($post_id, 'cs_job_referral', true);
        if ($cs_job_referral == 'indeed') {
            // getting job company name
            $cs_company_name = get_post_meta($post_id, 'cs_company_name', true);
            // getting job address
            $job_address = get_post_meta($post_id, 'cs_post_comp_address', true);
            // getting job posted date 
            $cs_job_posted_date = get_post_meta($post_id, 'cs_job_posted', true);

            // getting job types
            $terms = wp_get_post_terms($post_id, 'job_type');
            $job_types = '';
            if ($terms) {
                foreach ($terms as $term) {
                    $job_type_color = get_option("job_type_color_$term->term_id");
                    $job_type_border_color = '';
                    if (isset($job_type_color['text'])) {
                        $job_type_border_color = 'style="border-color:' . $job_type_color['text'] . ' !important"';
                    }
                    $job_types .= '<a href="' . get_term_link($term) . '" class="freelance" ' . $job_type_border_color . '>' . $term->name . '</a>';
                }
            }

            $jobs_info_output = '';
            $jobs_info_output .= '<div class="cs-text">';
            $jobs_info_output .= '<h2>' . $cs_company_name . '</h2>';
            if ($job_types != '') {
                $jobs_info_output .= force_balance_tags($job_types);
            }
            $jobs_info_output .= '<ul class="post-options">';
            if ($job_address != '') {
                $jobs_info_output .= '<li><i class="icon-location6"></i><a>' . esc_html($job_address) . '</a></li>';
            }
            if (isset($cs_job_posted_date) && $cs_job_posted_date != '') {
                $jobs_info_output .= '<li>';
                $jobs_info_output .= '<i class="icon-calendar5"></i>';
                $jobs_info_output .= __("Post Date: ", JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN) . '<span>' . date_i18n(get_option("date_format"), $cs_job_posted_date) . '</span>';
                $jobs_info_output .= '</li>';
            }

            // Application closing date frontend filter in application deadline add on
            $jobs_info_output .= apply_filters('job_hunt_application_deadline_date_frontend', $post_id);
            $jobs_info_output .= '</ul>';
            $jobs_info_output .= '</div>';
            $content = $jobs_info_output;
        }

        return $content;
    }

    /**
     * 3 column job info fronend view
     * @return content with html 
     */
    public function _3_columns_job_info_frontend($content, $post_id) {
        $cs_job_referral = get_post_meta($post_id, 'cs_job_referral', true);
        if ($cs_job_referral == 'indeed') {
            /////// getting job fields
            // getting job company name
            $cs_company_name = get_post_meta($post_id, 'cs_company_name', true);
            // getting job address
            $job_address = get_post_meta($post_id, 'cs_post_comp_address', true);
            // getting job posted date 
            $cs_job_posted_date = get_post_meta($post_id, 'cs_job_posted', true);
            // getting job count views
            $cs_count_views = get_post_meta($post_id, "cs_count_views", true);
            // getting job detail url
            $cs_detail_url = get_post_meta($post_id, "cs_job_detail_url", true);
            // no image static url
            $cs_employee_employer_img = esc_url(JOBHUNT_INDEED_JOBS_PLUGIN_URL . '/assets/images/img-not-found4x3.jpg');

            //  job info section
            $jobs_info_output = '';
            $jobs_info_output .= '<div class="jobs-info">';
            //  image section
            if ($cs_employee_employer_img <> '') {
                $jobs_info_output .= '<div class="cs-media">';
                $jobs_info_output .= '<figure>';
                $jobs_info_output .= '<a href="' . esc_url($cs_detail_url) . '">';
                $jobs_info_output .= '<img src="' . esc_url($cs_employee_employer_img) . '" alt="' . esc_html($cs_company_name) . '" />';
                $jobs_info_output .= '</a>';
                $jobs_info_output .= '</figure>';
                $jobs_info_output .= '</div>';
            }
            //  content section
            $jobs_info_output .= '<div class="cs-text">';
            $jobs_info_output .= '<strong>' . esc_attr($cs_company_name) . '</strong>';
            $jobs_info_output .= '<ul class="post-options">';

            if ($job_address != '') {
                $jobs_info_output .= '<li><i class="icon-location6"></i><a href="#">' . esc_attr($job_address) . '</a></li>';
            }

            if ($cs_job_posted_date != '') {
                $jobs_info_output .= '<li>';
                $jobs_info_output .= '<i class="icon-calendar5"></i>';
                $jobs_info_output .= __('Post Date: ', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN) . '  <span>' . date_i18n(get_option('date_format'), $cs_job_posted_date) . '</span>';
                $jobs_info_output .= '</li>';
            }
            // Application closing date frontend filter in application deadline add on
            $jobs_info_output .= apply_filters('job_hunt_application_deadline_date_frontend', $post_id);

            $jobs_info_output .= '<li>';
            $jobs_info_output .= '<i class="icon-eye7"></i>';
            $jobs_info_output .= __('Views', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN) . '<span> ' . esc_attr($cs_count_views) . '</span>';
            $jobs_info_output .= '</li>';

            $jobs_info_output .= '</ul>';
            $jobs_info_output .= '</div>';

            $jobs_info_output .= '</div>';
            $content = apply_filters('the_content', $jobs_info_output);
        }
        return $content;
    }

    /**
     * fancy job info fronend view
     * @return content with html 
     */
    public function fancy_job_info_frontend($content, $post_id) {
        $cs_job_referral = get_post_meta($post_id, 'cs_job_referral', true);
        if ($cs_job_referral == 'indeed') {
            // getting job company name
            $cs_company_name = get_post_meta($post_id, 'cs_company_name', true);
            // getting job address
            $job_address = get_post_meta($post_id, 'cs_post_comp_address', true);
            // getting job posted date 
            $cs_job_posted_date = get_post_meta($post_id, 'cs_job_posted', true);

            // getting job types
            $terms = wp_get_post_terms($post_id, 'job_type');
            $job_types = '';
            if ($terms) {
                foreach ($terms as $term) {
                    $job_type_color = get_option("job_type_color_$term->term_id");
                    $job_type_border_color = '';
                    if (isset($job_type_color['text'])) {
                        $job_type_border_color = 'style="border-color:' . $job_type_color['text'] . ' !important"';
                    }
                    $job_types .= '<a href="' . get_term_link($term) . '" class="freelance" ' . $job_type_border_color . '>' . $term->name . '</a>';
                }
            }

            $jobs_info_output = '';
            $jobs_info_output .= '<div class="cs-text">';
            $jobs_info_output .= '<h2>' . $cs_company_name . '</h2>';
            if ($job_types != '') {
                $jobs_info_output .= force_balance_tags($job_types);
            }
            $jobs_info_output .= '<ul class="post-options">';
            if ($job_address != '') {
                $jobs_info_output .= '<li><i class="icon-location6"></i><a>' . esc_html($job_address) . '</a></li>';
            }
            if (isset($cs_job_posted_date) && $cs_job_posted_date != '') {
                $jobs_info_output .= '<li>';
                $jobs_info_output .= '<i class="icon-calendar5"></i>';
                $jobs_info_output .= __("Post Date: ", JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN) . '<span>' . date_i18n(get_option("date_format"), $cs_job_posted_date) . '</span>';
                $jobs_info_output .= '</li>';
            }

            // Application closing date frontend filter in application deadline add on
            $jobs_info_output .= apply_filters('job_hunt_application_deadline_date_frontend', $post_id);
            $jobs_info_output .= '</ul>';
            $jobs_info_output .= '</div>';
            $content = $jobs_info_output;
        }

        return $content;
    }

    /**
     * map job info fronend view
     * @return content with html 
     */
    public function map_job_info_frontend($content, $post_id) {
        $cs_job_referral = get_post_meta($post_id, 'cs_job_referral', true);
        if ($cs_job_referral == 'indeed') {
            // getting job company name
            $cs_company_name = get_post_meta($post_id, 'cs_company_name', true);
            // getting job address
            $job_address = get_post_meta($post_id, 'cs_post_comp_address', true);
            // getting job posted date 
            $cs_job_posted_date = get_post_meta($post_id, 'cs_job_posted', true);

            // getting job types
            $terms = wp_get_post_terms($post_id, 'job_type');
            $job_types = '';
            if ($terms) {
                foreach ($terms as $term) {
                    $job_type_color = get_option("job_type_color_$term->term_id");
                    $job_type_border_color = '';
                    if (isset($job_type_color['text'])) {
                        $job_type_border_color = 'style="color:' . $job_type_color['text'] . ' !important"';
                    }
                    $job_types .= '<li ' . $job_type_border_color . '>' . $term->name . '</li> ';
                }
            }

            $jobs_info_output = '';
            $jobs_info_output .= '<div class="cs-text">';
            $jobs_info_output .= '<h2>' . $cs_company_name . '</h2>';
            $jobs_info_output .= '<ul class="post-options">';
            $jobs_info_output .= $job_types;
            if ($job_address != '') {
                $jobs_info_output .= '<li><i class="icon-location6"></i><a>' . esc_html($job_address) . '</a></li>';
            }
            if (isset($cs_job_posted_date) && $cs_job_posted_date != '') {
                $jobs_info_output .= '<li>';
                $jobs_info_output .= '<i class="icon-calendar5"></i>';
                $jobs_info_output .= __("Post Date: ", JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN) . '<span>' . date_i18n(get_option("date_format"), $cs_job_posted_date) . '</span>';
                $jobs_info_output .= '</li>';
            }

            // Application closing date frontend filter in application deadline add on
            $jobs_info_output .= apply_filters('job_hunt_application_deadline_date_frontend', $post_id);
            $jobs_info_output .= '</ul>';
            $jobs_info_output .= '</div>';
            $content = $jobs_info_output;
        }

        return $content;
    }

    /**
     * classic job info fronend view
     * @return content with html 
     */
    public function classic_job_info_frontend($content, $post_id) {
        $cs_job_referral = get_post_meta($post_id, 'cs_job_referral', true);
        if ($cs_job_referral == 'indeed') {
            // getting job company name
            $cs_company_name = get_post_meta($post_id, 'cs_company_name', true);
            // getting job posted date 
            $cs_job_posted_date = get_post_meta($post_id, 'cs_job_posted', true);
            // getting job expire date
            $cs_job_expired = get_post_meta($post_id, 'cs_job_expired', true);
            // getting job count views
            $cs_count_views = get_post_meta($post_id, "cs_count_views", true);

            $jobs_info_output = '';
            $jobs_info_output .= '<h2>' . $cs_company_name . '</h2>';
            $jobs_info_output .= '<ul class="posted-detail pull-left">';
            if ($cs_job_posted_date != '') {
                $jobs_info_output .= '<li>';
                $jobs_info_output .= '<i class="icon-calendar5"></i>';
                $jobs_info_output .= __("Post date ", JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN) . '<span>' . date_i18n(get_option("date_format"), $cs_job_posted_date) . '</span>';
                $jobs_info_output .= '</li>';
            }

            // Application closing date frontend filter in application deadline add on
            $jobs_info_output .= apply_filters('job_hunt_application_deadline_date_frontend', $post_id);
            if ($cs_job_posted_date != '') {
                $jobs_info_output .= '<li>';
                $jobs_info_output .= '<i class="icon-eye7"></i>';
                $jobs_info_output .= __("Views", JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN) . '<span>' . $cs_count_views . '</span>';
                $jobs_info_output .= '</li>';
            }
            $jobs_info_output .= '</ul>';

            $content = $jobs_info_output;
        }

        return $content;
    }

    /**
     * view more fronend view
     * @return content with html 
     */
    public function view_more_frontend($post_id) {
        $cs_job_referral = get_post_meta($post_id, 'cs_job_referral', true);
        $cs_detail_url = get_post_meta($post_id, 'cs_job_detail_url', true);
        $view_more = '';
        if ($cs_job_referral == 'indeed') {
            $view_more = '';
            $view_more .= '<a href="' . esc_url($cs_detail_url) . '" target="_blank">' . __('View More', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN) . '</a>';
        }
        return $view_more;
    }

    /**
     * apply buttons view
     * @return empty 
     */
    public function empty_frontend_callback($content, $post_id) {
        $cs_job_referral = get_post_meta($post_id, 'cs_job_referral', true);
        if ($cs_job_referral == 'indeed') {
            $content = '';
        }
        return $content;
    }

    /**
     * related jobs frontend view
     * @return html 
     */
    public function related_jobs_frontend($content, $post_id) {
        $post_id;
        $cs_job_referral = get_post_meta($post_id, 'cs_job_referral', true);
        if ($cs_job_referral == 'indeed') {
            $content = '';
            $cs_job_posted_date_formate = 'd-m-Y H:i:s';
            $cs_job_expired_date_formate = 'd-m-Y H:i:s';
            $date_format = get_option("date_format");

            // getting post specialisms
            $filter_arr2[] = '';
            $specialisms_arr = array();
            $specialisms = wp_get_post_terms($post_id, 'specialisms');
            if ($specialisms) {
                $specialisms_arr = ['relation' => 'OR',];
                foreach ($specialisms as $specialisms_key) {
                    $specialisms_arr[] = array(
                        'taxonomy' => 'specialisms',
                        'field' => 'slug',
                        'terms' => $specialisms_key->slug
                    );
                }
                $filter_arr2[] = array($specialisms_arr);
            }

            $rel_jobs_args = array('posts_per_page' => "10", 'post_type' => 'jobs', 'order' => "DESC", 'orderby' => 'post_date',
                'post_status' => 'publish', 'ignore_sticky_posts' => 1,
                'post__not_in' => array($post_id),
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
                        'key' => 'cs_job_referral',
                        'value' => 'indeed',
                        'compare' => '=',
                    ),
                )
            );

            // Exclude expired jobs from listing
            $rel_jobs_args = apply_filters('job_hunt_jobs_listing_parameters', $rel_jobs_args);

            $rel_job_qry = new WP_Query($rel_jobs_args);
            if ($rel_job_qry->have_posts()):
                $content .= '<h4>' . __('Indeed Related Jobs', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN) . ' (' . $rel_job_qry->found_posts . ') </h4>';
                $content .= '<ul class="cs-company-jobs">';
                while ($rel_job_qry->have_posts()): $rel_job_qry->the_post();

                    $cs_job_posted = get_post_meta(get_the_ID(), 'cs_job_posted', true);
                    $terms = wp_get_post_terms(get_the_ID(), 'job_type');
                    $term_id = isset(current($terms)->term_id) ? current($terms)->term_id : '';
                    $term_name = isset(current($terms)->name) ? current($terms)->name : '';
                    $job_type_color = get_option("job_type_color_$term_id");
                    $job_tyle_color_style = '';
                    if (isset($job_type_color['text'])) {
                        $job_tyle_color_style = 'style="color:' . $job_type_color['text'] . ' !important"';
                    }
                    $content .= '<li>';
                    $content .= '<div class="cs-text">';
                    $content .= '<span><a href="' . esc_url(get_permalink()) . '">' . get_the_title() . '</a></span>';
                    if ($cs_job_posted != '') {
                        $content .= __(' on', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
                        $content .= '<span class="post-date">' . date_i18n($date_format, $cs_job_posted) . '</span>';
                    }
                    if ($term_name) {
                        $content .= '<a ' . $job_tyle_color_style . ' class="categories cs-color" href="' . get_term_link($term_id) . '">' . $term_name . '</a>';
                    }
                    $content .= '</div>';
                    $content .= '</li>';
                endwhile;
                $content .= '</ul>';
            endif;
            wp_reset_postdata();
        }
        return $content;
    }

    /**
     * sidebar related jobs frontend view
     * @return html 
     */
    public function sidebar_related_jobs_frontend($content, $post_id) {
        $post_id;
        $cs_job_referral = get_post_meta($post_id, 'cs_job_referral', true);
        if ($cs_job_referral == 'indeed') {
            $content = '';
            $cs_job_posted_date_formate = 'd-m-Y H:i:s';
            $cs_job_expired_date_formate = 'd-m-Y H:i:s';
            $date_format = get_option("date_format");

            // getting post specialisms
            $filter_arr2[] = '';
            $specialisms_arr = array();
            $specialisms = wp_get_post_terms($post_id, 'specialisms');
            if ($specialisms) {
                $specialisms_arr = ['relation' => 'OR',];
                foreach ($specialisms as $specialisms_key) {
                    $specialisms_arr[] = array(
                        'taxonomy' => 'specialisms',
                        'field' => 'slug',
                        'terms' => $specialisms_key->slug
                    );
                }
                $filter_arr2[] = array($specialisms_arr);
            }

            $rel_jobs_args = array('posts_per_page' => "10", 'post_type' => 'jobs', 'order' => "DESC", 'orderby' => 'post_date',
                'post_status' => 'publish', 'ignore_sticky_posts' => 1,
                'post__not_in' => array($post_id),
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
                        'key' => 'cs_job_referral',
                        'value' => 'indeed',
                        'compare' => '=',
                    ),
                )
            );

            // Exclude expired jobs from listing
            $rel_jobs_args = apply_filters('job_hunt_jobs_listing_parameters', $rel_jobs_args);

            $rel_job_qry = new WP_Query($rel_jobs_args);
            if ($rel_job_qry->have_posts()):
                $content .= '<div class="widget-title"><h5>' . __('Indeed Related Jobs', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN) . ' (' . $rel_job_qry->found_posts . ') </h5></div>';
                $content .= '<ul>';
                while ($rel_job_qry->have_posts()): $rel_job_qry->the_post();

                    $cs_job_posted = get_post_meta(get_the_ID(), 'cs_job_posted', true);
                    $cs_job_employer = get_post_meta(get_the_ID(), 'cs_job_username', true);
                    $user = get_user_by('id', $cs_job_employer);

                    $content .= '<li>';
                    $content .= '<a href="' . esc_url(get_permalink()) . '">' . get_the_title() . '</a>';
                    $content .= '<div class="post-option"><span>';
                    if ($cs_job_posted != '') {
                        $content .= __('Posted on ', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
                        $content .= date_i18n($date_format, $cs_job_posted);
                        if ($user) {
                            $content .= ', ';
                        }
                    }
                    if ($user) {
                        $content .= __('by ', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
                        $content .= '<u>' . esc_html($user->display_name) . '</u>';
                    }
                    $content .= '</span></div>';
                    $content .= '</li>';
                endwhile;
                $content .= '</ul>';
            endif;
            wp_reset_postdata();
        }
        return $content;
    }

    /**
     * 2 column job map fronend view
     * @return content with html 
     */
    public function _2_columns_map_frontend($content, $post_id) {
        $cs_job_referral = get_post_meta($post_id, 'cs_job_referral', true);
        if ($cs_job_referral == 'indeed') {
            $content = '';
            $cs_post_loc_latitude = get_post_meta($post_id, 'cs_post_loc_latitude', true);
            $cs_post_loc_longitude = get_post_meta($post_id, 'cs_post_loc_longitude', true);
            $job_address = get_post_meta($post_id, 'cs_post_comp_address', true);
            $post_zoom_level = '8';
            if ($cs_post_loc_latitude && $cs_post_loc_longitude) {
                $cs_jobcareer_theme_options = get_option('cs_theme_options');
                $cs_map_view = isset($cs_jobcareer_theme_options['def_map_style']) ? $cs_jobcareer_theme_options['def_map_style'] : '';
                ?>
                <div class="map-sec">
                <?php
                $arg = array(
                    'map_height' => '250',
                    'map_lat' => $cs_post_loc_latitude,
                    'map_lon' => $cs_post_loc_longitude,
                    'map_zoom' => $post_zoom_level,
                    'map_type' => 'ROADMAP',
                    'map_info' => preg_replace("/\r|\n/", "", $job_address),
                    'map_info_width' => '250',
                    'map_info_height' => '100',
                    'map_marker_icon' => esc_url(JOBHUNT_INDEED_JOBS_PLUGIN_URL . '/assets/images/recruiter-map-icon.png'),
                    'map_show_marker' => 'true',
                    'map_controls' => 'false',
                    'map_draggable' => 'true',
                    'map_scrollwheel' => 'true',
                    'map_border' => 'yes',
                    'cs_map_style' => $cs_map_view,
                );
                if (function_exists('cs_job_map')) {
                    cs_job_map($arg);
                }
                ?>
                </div>
                    <?php
                }
            }
            return $content;
        }

    }

    new Job_Hunt_Job_Fronend_View();
    