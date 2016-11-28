<?php
/*
 *
 * Start Function how to manage of element_size
 *
 */
if (!function_exists('element_size_data_array_index')) {

    function element_size_data_array_index($size) {
        if ($size == "" or $size == 100)
            return 0;
        else if ($size == 75)
            return 1;
        else if ($size == 67)
            return 2;
        else if ($size == 50)
            return 3;
        else if ($size == 33)
            return 4;
        else if ($size == 25)
            return 5;
    }

}
/*
 *
 * Start Function how to manage of element_size array index
 *
 */
if (!function_exists('cs_element_size_data_array_index')) {

    function cs_element_size_data_array_index($size) {
        if ($size == "" or $size == 100)
            return 0;
        else if ($size == 75)
            return 1;
        else if ($size == 67)
            return 2;
        else if ($size == 50)
            return 3;
        else if ($size == 33)
            return 4;
        else if ($size == 25)
            return 5;
    }

}
/*
 *
 * Start Function how to manage of element_size using shortcode
 *
 */
if (!function_exists('cs_shortcode_element_size')) {

    function cs_shortcode_element_size($column_size = '') {
        global $cs_html_fields;
       
        $cs_opt_array = array(
            'name' => __('Size', 'jobhunt'),
            'desc' => '',
            'hint_text' => __('Select column width. This width will be calculated depend page width', 'jobhunt'),
            'echo' => true,
            'field_params' => array(
                'std' => $column_size,
                'id' => '',
                'cust_id' => 'column_size',
                'cust_name' => 'column_size[]',
                'options' => array(
                    '1/1' => __('1 Column', 'jobhunt'),
                    '1/2' => __('2 Columns', 'jobhunt'),
                    '1/3' => __('3 Columns', 'jobhunt'),
                    '1/4' => __('4 Columns', 'jobhunt'),
                    '1/6' => __('6 Columns', 'jobhunt'),
                ),
                'return' => true,
                'classes' => 'column_size chosen-select-no-single'
            ),
        );


        $cs_html_fields->cs_select_field($cs_opt_array);
    }

}
/*
 *
 * Start Function how to manage of element setting
 *
 */
if (!function_exists('cs_element_setting')) {

    function cs_element_setting($name, $cs_counter, $element_size, $element_description = '', $page_element_icon = 'icon-star', $type = '') {
        global $cs_form_fields2;
        $element_title = str_replace("jobcareer_pb_", "", $name);
        //echo "name == ".$name;
        $elm_name = str_replace("jobcareer_pb_", "", $name);
        $element_list = cs_element_list();
        ?>
        <div class="column-in">
            <?php
            $cs_opt_array = array(
                'id' => '',
                'std' => esc_attr($element_size),
                'cust_id' => "",
                'cust_name' => esc_attr($element_title) . "_element_size[]",
                'classes' => 'item',
            );
            $cs_form_fields2->cs_form_hidden_render($cs_opt_array);
            ?>
            <a href="javascript:;" onclick="javascript:_createpopshort(jQuery(this))" class="options"><i class="icon-gear"></i></a>
            <a href="#" class="delete-it btndeleteit"><i class="icon-trash-o"></i></a> &nbsp;
            <a class="decrement" onclick="javascript:decrement(this)"><i class="icon-minus4"></i></a> &nbsp; 
            <a class="increment" onclick="javascript:increment(this)"><i class="icon-plus3"></i></a> 
            <span> <i class="cs-icon <?php echo str_replace("jobcareer_pb_", "", $name); ?>-icon"></i> 
                <strong><?php
        if (isset($element_list['element_list'][$elm_name])) {
            echo cs_validate_data($element_list['element_list'][$elm_name]);
        }
            ?></strong><br/>
                    <?php echo esc_attr($element_description); ?> 
            </span>
        </div>
        <?php
    }

}
/*
 *
 * End Function how to manage of element setting
 *
 *
 * Start Function how to manage of page element list
 *
 */
if (!function_exists('cs_element_list')) {

    function cs_element_list() {
        $element_list = array();
        $element_list['element_list'] = array(
			'register' => __('Register', 'jobhunt'),
            'cv_package' => __('CV Package', 'jobhunt'),
            'cv package' => __('CV Package', 'jobhunt'),
            'job_package' => __('Job Package', 'jobhunt'),
            'job package' => __('Job Package', 'jobhunt'),
            'jobs_search' => __('Jobs Search', 'jobhunt'),
            'jobs search' => __('Jobs Search', 'jobhunt'),
			'job_post' => __('Job Post', 'jobcareer'),
            'job post' => __('Job Post', 'jobcareer'),
			'about' => __('About', 'jobhunt'),
			'about' => __('About', 'jobhunt'),
            'candidate' => __('Candidate', 'jobhunt'),
            'quotes' => __('Quotes', 'jobhunt'),
            'employer' => __('Employer', 'jobhunt'),
            'jobhunt' => __('Jobs', 'jobhunt'),
            'job_specialisms' => __('Job specialisms', 'jobhunt'),
            'gallery' => __('gallery', 'jobhunt'),
            'slider' => __('Slider', 'jobhunt'),
            'blog' => __('Blog', 'jobhunt'),
            'flex_editor' => __('Flex Editor', 'jobhunt'),
            'flex editor' => __('Flex Editor', 'jobhunt'),
            'team' => __('Team', 'jobhunt'),
            'teams' => __('Teams', 'jobhunt'),
            'column' => __('Column', 'jobhunt'),
            'flex_column' => __('Column', 'jobhunt'),
            'flex column' => __('Column', 'jobhunt'),
            'accordions' => __('Accordions', 'jobhunt'),
            'contact' => __('Contact', 'jobhunt'),
            'divider' => __('Divider', 'jobhunt'),
            'message_box' => __('Message Box', 'jobhunt'),
            'image' => __('Image', 'jobs'),
            'image_frame' => __('Image Frame', 'jobhunt'),
            'map' => __('Map', 'jobhunt'),
            'video' => __('Video', 'jobhunt'),
            'slider' => __('Quote', 'jobhunt'),
            'quick_slider' => __('Quick Quote', 'jobhunt'),
            'quick slider' => __('Quick Quote', 'jobhunt'),
            'dropcap' => __('Drop cap', 'jobhunt'),
            'pricetable' => __('Price Table', 'jobhunt'),
            'tabs' => __('Tabs', 'jobhunt'),
            'sitemap' => __('Sitemap', 'jobhunt'),
            'accordion' => __('Accordion', 'jobhunt'),
            'prayer' => __('Prayer', 'jobhunt'),
            'prayer' => __('Prayer', 'jobhunt'),
            'table' => __('Table', 'jobhunt'),
            'call_to_action' => __('Call to Action', 'jobhunt'),
            'call to action' => __('Call to Action', 'jobhunt'),
            'clients' => __('Clients', 'jobhunt'),
            'heading' => __('Heading', 'jobhunt'),
            'testimonials' => __('Testimonials', 'jobhunt'),
            'infobox' => __('Info box', 'jobhunt'),
            'spacer' => __('Spacer', 'jobhunt'),
            'promobox' => __('Promo Box', 'jobhunt'),
            'offerslider' => __('Offer Slider', 'jobhunt'),
            'audio' => __('Audio', 'jobhunt'),
            'icons' => __('Icons', 'jobhunt'),
            'contactform' => __('Contact Form', 'jobhunt'),
            'tooltip' => __('Tooltip', 'jobhunt'),
            'services' => __('Services', 'jobhunt'),
            'icon_box' => __('Icon Box', 'jobhunt'),
            'highlight' => __('Highlight', 'jobhunt'),
            'list' => __('List', 'jobhunt'),
            'mesage' => __('Message', 'jobhunt'),
            'faq' => __('Faq', 'jobhunt'),
            'progressbars' => __('Progress bars', 'jobhunt'),
            'counter' => __('Counter', 'jobhunt'),
            'members' => __('Members', 'jobhunt'),
            'icon_box' => __('Icon Box', 'jobhunt'),
            'mailchimp' => __('Mail Chimp', 'jobhunt'),
            'facilities' => __('Facilities', 'jobhunt'),
            'tweets' => __('Tweets', 'jobhunt'),
            'button' => __('Button', 'jobhunt'),
            'team_post' => __('Team', 'jobhunt'),
            'team post' => __('Team', 'jobhunt'),
            'multi_counters' => __('Counter', 'jobhunt'),
            'multi counters' => __('Counter', 'jobhunt'),
            'portfolio' => __('Portfolio', 'jobhunt'),
            'multi_price_table' => __('Multi Price Table', 'jobhunt'),
            'employer' => __('Employer', 'jobhunt'),
            'jobs' => __('Jobs', 'jobhunt'),
            'job_specialisms' => __('Job specialisms', 'jobhunt'),
            'job specialisms' => __('Job specialisms', 'jobhunt'),
        );
        return $element_list;
    }

}
/*
 *
 * Start Function  to validate data
 *
 */
if (!function_exists('cs_validate_data')) {

    function cs_validate_data($input = '') {
        $output = $input;
        return $output;
    }

}