<?php
/*
 *
 * @Shortcode Name : Job Search
 * @retrun
 *
 */
/*
 *
 * Start Function  job search
 *
 */
if (!function_exists('jobcareer_pb_jobs_search')) {

    function jobcareer_pb_jobs_search($die = 0) {
        global $cs_node, $cs_html_fields, $post, $cs_form_fields2;
        $shortcode_element = '';
        $filter_element = 'filterdrag';
        $shortcode_view = '';
        $output = array();
        $counter = $_POST['counter'];
        $cs_counter = $_POST['counter'];
        if (isset($_POST['action']) && !isset($_POST['shortcode_element_id'])) {
            $POSTID = '';
            $shortcode_element_id = '';
        } else {
            $POSTID = $_POST['POSTID'];
            $PREFIX = 'cs_jobs_search';
            $parseObject = new ShortcodeParse();
            $shortcode_element_id = $_POST['shortcode_element_id'];
            $shortcode_str = stripslashes($shortcode_element_id);
            $output = $parseObject->cs_shortcodes($output, $shortcode_str, true, $PREFIX);
        }
        $defaults = array(
            'jobs_search_title' => '',
            'job_search_style' => '',
            'job_search_layout' => '',
            'job_search_layout_bg' => '',
            'job_search_layout_heading_color' => '',
            'job_search_title_field_switch' => '',
            'job_search_specialisam_field_switch' => '',
            'job_search_location_field_switch' => '',
            'job_lable_switch' => '',
            'job_search_hint_switch' => '',
            'job_advance_search_switch' => '',
            'job_advance_search_url' => '',
        );

        if (isset($output['0']['atts']))
            $atts = $output['0']['atts'];
        else
            $atts = array();
        if (isset($output['0']['content']))
            $jobs_search_content = $output['0']['content'];
        else
            $jobs_search_content = '';
        $jobs_search_element_size = '100';
        foreach ($defaults as $key => $values) {
            if (isset($atts[$key]))
                $$key = $atts[$key];
            else
                $$key = $values;
        }
        $name = 'jobcareer_pb_jobs_search';
        $coloumn_class = 'column_' . $jobs_search_element_size;
        if (isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode') {
            $shortcode_element = 'shortcode_element_class';
            $shortcode_view = 'cs-pbwp-shortcode';
            $filter_element = 'ajax-drag';
            $coloumn_class = '';
        }
        ?>
        <div id="<?php echo esc_attr($name . $cs_counter) ?>_del" class="column  parentdelete <?php echo esc_attr($coloumn_class); ?> <?php echo esc_attr($shortcode_view); ?>" item="jobs_search" data="<?php echo cs_element_size_data_array_index($jobs_search_element_size) ?>" >
            <?php cs_element_setting($name, $cs_counter, $jobs_search_element_size, '', 'ellipsis-h', $type = ''); ?>
            <div class="cs-wrapp-class-<?php echo intval($cs_counter) ?> <?php echo esc_attr($shortcode_element); ?>" id="<?php echo esc_attr($name . $cs_counter) ?>" data-shortcode-template="[cs_jobs_search {{attributes}}]{{content}}[/cs_jobs_search]" style="display: none;">
                <div class="cs-heading-area">
                    <h5><?php _e('JC: Job Search Option', 'jobhunt'); ?></h5>
                    <a href="javascript:removeoverlay('<?php echo esc_js($name . $cs_counter) ?>','<?php echo esc_js($filter_element); ?>')" class="cs-btnclose"><i class="icon-times"></i></a> </div>
                <div class="cs-pbwp-content">
                    <div class="cs-wrapp-clone cs-shortcode-wrapp">
                                                        <?php
                                if (isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode') {
                                    jobcareer_shortcode_element_size();
                                }
                                ?>
                        <?php
						$cs_opt_array = array(
                            'name' => __('Element Title', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => __("Enter title of search", "jobhunt"),
                            'echo' => true,
                            'field_params' => array(
                                'std' => $jobs_search_title,
                                'id' => 'jobs_search_title',
                                'cust_name' => 'jobs_search_title[]',
                                'return' => true,
                            ),
                        );

                        $cs_html_fields->cs_text_field($cs_opt_array);
						
                        $cs_opt_array = array(
                            'name' => __('Views', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => __("Choose element layout which you want", "jobhunt"),
                            'echo' => true,
                            'field_params' => array(
                                'std' => esc_attr($job_search_style),
                                'cust_id' => '',
                                'cust_name' => 'job_search_style[]',
                                'options' => array(
                                    'career' => __('Default', 'jobhunt'),
                                    'modren' => __('Modern', 'jobhunt'),
                                ),
                                'return' => true,
                            ),
                        );
                        $cs_html_fields->cs_select_field($cs_opt_array);
                        $cs_opt_array = array(
                            'name' => __('Background Color', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => __("Choose search layout background color", "jobhunt"),
                            'echo' => true,
                            'field_params' => array(
                                'std' => isset($job_search_layout_bg) ? esc_attr($job_search_layout_bg) : '',
                                'cust_id' => '',
                                'classes' => 'bg_color',
                                'cust_name' => 'job_search_layout_bg[]',
                                'return' => true,
                            ),
                        );
                        $cs_html_fields->cs_text_field($cs_opt_array);

                        $cs_opt_array = array(
                            'name' => __('Heading Color', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => __("Choose search layout heading color", "jobhunt"),
                            'echo' => true,
                            'field_params' => array(
                                'std' => isset($job_search_layout_heading_color) ? esc_attr($job_search_layout_heading_color) : '',
                                'cust_id' => '',
                                'classes' => 'bg_color',
                                'cust_name' => 'job_search_layout_heading_color[]',
                                'return' => true,
                            ),
                        );
                        $cs_html_fields->cs_text_field($cs_opt_array);

                        

                        $cs_opt_array = array(
                            'name' => __('Keyword Title', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => __("Select Title Field Enable/Disable.", "jobhunt"),
                            'echo' => true,
                            'field_params' => array(
                                'std' => $job_search_title_field_switch,
                                'id' => 'job_search_title_field_switch',
                                'cust_name' => 'job_search_title_field_switch[]',
                                'classes' => 'chosen-select-no-single select-medium',
                                'options' => array(
                                    'yes' => __('Yes', 'jobhunt'),
                                    'no' => __('No', 'jobhunt'),
                                ),
                                'return' => true,
                            ),
                        );
                        $cs_html_fields->cs_select_field($cs_opt_array);

                        $cs_opt_array = array(
                            'name' => __('Specialism Field', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => __("Select Specialism Field Enable/Disable.", "jobhunt"),
                            'echo' => true,
                            'field_params' => array(
                                'std' => $job_search_specialisam_field_switch,
                                'id' => 'job_search_specialisam_field_switch',
                                'cust_name' => 'job_search_specialisam_field_switch[]',
                                'classes' => 'chosen-select-no-single select-medium',
                                'options' => array(
                                    'yes' => __('Yes', 'jobhunt'),
                                    'no' => __('No', 'jobhunt'),
                                ),
                                'return' => true,
                            ),
                        );
                        $cs_html_fields->cs_select_field($cs_opt_array);


                        $cs_opt_array = array(
                            'name' => __('Location Field', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => __("Select Location Field Enable/Disable.", "jobhunt"),
                            'echo' => true,
                            'field_params' => array(
                                'std' => $job_search_location_field_switch,
                                'id' => 'job_search_location_field_switch',
                                'cust_name' => 'job_search_location_field_switch[]',
                                'classes' => 'chosen-select-no-single select-medium',
                                'options' => array(
                                    'yes' => __('Yes', 'jobhunt'),
                                    'no' => __('No', 'jobhunt'),
                                ),
                                'return' => true,
                            ),
                        );
                        $cs_html_fields->cs_select_field($cs_opt_array);


                        $cs_opt_array = array(
                            'name' => __('Labels on/off', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => __("Select lables Enable/Disable.", "jobhunt"),
                            'echo' => true,
                            'field_params' => array(
                                'std' => $job_lable_switch,
                                'id' => 'job_labele_switch',
                                'cust_name' => 'job_lable_switch[]',
                                'classes' => 'chosen-select-no-single select-medium',
                                'options' => array(
                                    'yes' => __('Yes', 'jobhunt'),
                                    'no' => __('No', 'jobhunt'),
                                ),
                                'return' => true,
                            ),
                        );
                        $cs_html_fields->cs_select_field($cs_opt_array);

                        $cs_opt_array = array(
                            'name' => __('Hint text on/off', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => __("Select Hint Enable/Disable.", "jobhunt"),
                            'echo' => true,
                            'field_params' => array(
                                'std' => $job_search_hint_switch,
                                'id' => 'job_search_hint_switch',
                                'cust_name' => 'job_search_hint_switch[]',
                                'classes' => 'chosen-select-no-single select-medium',
                                'options' => array(
                                    'yes' => __('Yes', 'jobhunt'),
                                    'no' => __('No', 'jobhunt'),
                                ),
                                'return' => true,
                            ),
                        );
                        $cs_html_fields->cs_select_field($cs_opt_array);


                        $cs_opt_array = array(
                            'name' => __('Advance Search on/off', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => __("Select Location Field Enable/Disable.", "jobhunt"),
                            'echo' => true,
                            'field_params' => array(
                                'std' => $job_advance_search_switch,
                                'id' => 'job_advance_search_switch',
                                'cust_name' => 'job_advance_search_switch[]',
                                'classes' => 'chosen-select-no-single select-medium',
                                'options' => array(
                                    'no' => __('No', 'jobhunt'),
                                    'yes' => __('Yes', 'jobhunt'),
                                ),
                                'return' => true,
                                'extra_atr' => 'onchange="cs_display_url_field(this.value);"',
                            ),
                        );
                        $cs_html_fields->cs_select_field($cs_opt_array);

                        $display = "display:none;";
                        if (isset($job_advance_search_switch) && $job_advance_search_switch == 'yes') {
                            $display = "display:block;";
                        }
                        $cs_opt_array = array(
                            'name' => __('URL', 'jobhunt'),
                            'desc' => '',
                            'id' => 'advance_url_field',
                            'styles' => $display,
                            'hint_text' => __("Enter url for advance click", "jobhunt"),
                            'echo' => true,
                            'field_params' => array(
                                'std' => $job_advance_search_url,
                                'id' => 'job_advance_search_url',
                                'cust_name' => 'job_advance_search_url[]',
                                'return' => true,
                            ),
                        );

                        $cs_html_fields->cs_text_field($cs_opt_array);
                        ?>
                    </div>
                    <?php if (isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode') { ?>
                        <ul class="form-elements insert-bg">
                            <li class="to-field"> <a class="insert-btn cs-main-btn" onclick="javascript:Shortcode_tab_insert_editor('<?php echo str_replace('jobcareer_pb_', '', $name); ?>', '<?php echo esc_js($name . $cs_counter) ?>', '<?php echo esc_js($filter_element); ?>')" ><?php _e('Insert', 'jobhunt'); ?></a> </li>
                        </ul>
                        <div id="results-shortocde"></div>
                    <?php } else { ?>
                        <ul class="form-elements noborder">
                            <li class="to-label"></li>
                            <li class="to-field">
                                <?php
                                $cs_opt_array = array(
                                    'id' => '',
                                    'std' => 'jobs_search',
                                    'cust_id' => "",
                                    'cust_name' => "cs_orderby[]",
                                );
                                $cs_form_fields2->cs_form_hidden_render($cs_opt_array);
                                $cs_opt_array = array(
                                    'id' => '',
                                    'std' => __('Save', 'jobhunt'),
                                    'cust_id' => "",
                                    'cust_name' => "",
                                    'cust_type' => 'button',
                                    'extra_atr' => 'style="margin-right:10px;" onclick="javascript:_removerlay(jQuery(this))"',
                                );
                                $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                ?>
                            </li>
                        </ul>
                    <?php } ?>
                </div>
            </div>
        </div>
        <script>

            /*
             * popup over 
             */
            popup_over();
            /*
             *End popup over 
             */


            /*
             * modern selection box function
             */
            jQuery(document).ready(function ($) {
                chosen_selectionbox();
            });
            /*
             * modern selection box function
             */
        </script> 
        <?php
        if ($die <> 1)
            die();
    }

    add_action('wp_ajax_jobcareer_pb_jobs_search', 'jobcareer_pb_jobs_search');
}
/*
 *
 * End Function  job search 
 *
 */