<?php

/*
 * @Shortcode Name : Job Post
 * @retrun
 *
 */
/*
 *
 * Start Function  shortcode of job post
 *
 */
if (!function_exists('cs_job_post_shortcode')) {
    function cs_job_post_shortcode($atts) {
        global $post, $current_user;
        $defaults = array(
            'job_post_title' => '',
        );
        extract(shortcode_atts($defaults, $atts));
        ob_start();
        $cs_plugin_options = get_option('cs_plugin_options');
        $cs_emp_temps = new cs_employer_templates();
        if ($job_post_title != '') {
            echo '<div class="cs-element-title"><h2>' . esc_html($job_post_title) . '</h2></div>';
        }
        echo '<div id="postjobs">';
        $cs_emp_temps->cs_employer_post_job(true);
        echo '</div>';
        $cs_html = ob_get_clean();
        return do_shortcode($cs_html);
    }
    add_shortcode('cs_job_post', 'cs_job_post_shortcode');
}
