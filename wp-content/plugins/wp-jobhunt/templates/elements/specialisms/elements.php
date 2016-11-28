<?php
/*
 *
 * @Shortcode Name : Job specialisms
 * @retrun
 *
 */


if (!function_exists('jobcareer_pb_job_specialisms')) {

    function jobcareer_pb_job_specialisms($die = 0) {
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
            $PREFIX = 'cs_job_specialisms';
            $parseObject = new ShortcodeParse();
            $shortcode_element_id = $_POST['shortcode_element_id'];
            $shortcode_str = stripslashes($shortcode_element_id);
            $output = $parseObject->cs_shortcodes($output, $shortcode_str, true, $PREFIX);
        }
        $defaults = array(
            'job_specialisms_title' => '',
            'job_specialisms_title_align' => 'left',
            'job_specialisms_subtitle_switch' => 'yes',
            'job_specialisms_img' => '',
            'spec_cats' => '',
            'specialisms_columns' => '',
            'specialisms_view' => '',
        );

        if (isset($output['0']['atts'])) {
            $atts = $output['0']['atts'];
        } else
            $atts = array();
        if (isset($output['0']['content'])) {
            $job_specialisms_content = $output['0']['content'];
        } else
            $job_specialisms_content = '';
        $job_specialisms_element_size = '100';
        foreach ($defaults as $key => $values) {
            if (isset($atts[$key]))
                $$key = $atts[$key];
            else
                $$key = $values;
        }
        $name = 'jobcareer_pb_job_specialisms';
        $coloumn_class = 'column_' . $job_specialisms_element_size;
        if (isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode') {
            $shortcode_element = 'shortcode_element_class';
            $shortcode_view = 'cs-pbwp-shortcode';
            $filter_element = 'ajax-drag';
            $coloumn_class = '';
        }
        $cs_rand_id = rand(13441324, 93441324);
        ?>
        <div id="<?php echo esc_attr($name . $cs_counter) ?>_del" class="column  parentdelete <?php echo esc_attr($coloumn_class); ?> <?php echo esc_attr($shortcode_view); ?>" item="job_specialisms" data="<?php echo cs_element_size_data_array_index($job_specialisms_element_size) ?>" >
            <?php cs_element_setting($name, $cs_counter, $job_specialisms_element_size, '', 'ellipsis-h', $type = ''); ?>
            <div class="cs-wrapp-class-<?php echo intval($cs_counter) ?> <?php echo esc_attr($shortcode_element); ?>" id="<?php echo esc_attr($name . $cs_counter) ?>" data-shortcode-template="[cs_job_specialisms {{attributes}}]{{content}}[/cs_job_specialisms]" style="display: none;">
                <div class="cs-heading-area">
                    <h5><?php _e('JC: Job specialisms Option', 'jobhunt'); ?></h5>
                    <a href="javascript:removeoverlay('<?php echo esc_js($name . $cs_counter) ?>','<?php echo esc_js($filter_element); ?>')" class="cs-btnclose"><i class="icon-times"></i></a> </div>
                <div class="cs-pbwp-content">

                    <div class="cs-wrapp-clone cs-shortcode-wrapp">        

                        <?php
                        $cs_opt_array = array(
                            'name' => __('Element Title', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => __("Enter title here", "jobhunt"),
                            'echo' => true,
                            'field_params' => array(
                                'std' => $job_specialisms_title,
                                'id' => 'job_specialisms_title',
                                'cust_name' => 'job_specialisms_title[]',
                                'return' => true,
                            ),
                        );
                        $cs_html_fields->cs_text_field($cs_opt_array);

                        $cs_opt_array = array(
                            'name' => __('Element Title Align', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => __("Select Title Align here.", "jobhunt"),
                            'echo' => true,
                            'field_params' => array(
                                'std' => $job_specialisms_title_align,
                                'id' => 'job_specialisms_title_align',
                                'cust_name' => 'job_specialisms_title_align[]',
                                'options' => array('left' => __('Left', 'jobhunt'), 'center' => __('Center', 'jobhunt'), 'right' => __('Right', 'jobhunt')),
                                'return' => true,
                            ),
                        );
                        $cs_html_fields->cs_select_field($cs_opt_array);
                        $cs_opt_array = array(
                            'name' => __('Styles', 'jobhunt'),
                            'desc' => '',
                            'echo' => true,
                            'hint_text' => __("Select view style", "jobhunt"),
                            'field_params' => array(
                                'std' => esc_attr($specialisms_view),
                                'cust_id' => 'specialisms_view',
                                'cust_name' => 'specialisms_view[]',
                                'classes' => 'chosen-select-no-single select-medium',
                                'options' => array(
                                    'classic' => __('Classic', 'jobhunt'),
                                    'modern' => __('Modern', 'jobhunt'),
                                ),
                                'return' => true,
                            ),
                        );
                        $cs_html_fields->cs_select_field($cs_opt_array);
                        $cs_opt_array = array(
                            'name' => __('Columns', 'jobhunt'),
                            'desc' => '',
                            'echo' => true,
                            'hint_text' => __("Select column for display view", "jobhunt"),
                            'field_params' => array(
                                'std' => esc_attr($specialisms_columns),
                                'cust_id' => 'specialisms_columns',
                                'cust_name' => 'specialisms_columns[]',
                                'classes' => 'chosen-select-no-single select-medium',
                                'options' => array(
                                    '2' => __('Two Columns', 'jobhunt'),
                                    '3' => __('Three Columns', 'jobhunt'),
                                    '4' => __('Four Columns', 'jobhunt'),
                                ),
                                'return' => true,
                            ),
                        );
                        $cs_html_fields->cs_select_field($cs_opt_array);
                        $cs_opt_array = array(
                            'name' => __('Total Jobs Count', 'jobhunt'),
                            'desc' => '',
                            'echo' => true,
                            'hint_text' => __("Sub title switch (on/off)", "jobhunt"),
                            'field_params' => array(
                                'std' => esc_attr($job_specialisms_subtitle_switch),
                                'cust_id' => 'job_specialisms_subtitle_switch',
                                'cust_name' => 'job_specialisms_subtitle_switch[]',
                                'classes' => 'chosen-select-no-single select-medium',
                                'options' => array(
                                    'no' => __('No', 'jobhunt'),
                                    'yes' => __('Yes', 'jobhunt'),
                                ),
                                'return' => true,
                            ),
                        );
                        $cs_html_fields->cs_select_field($cs_opt_array);

                        $spec_cats = explode(',', $spec_cats);
                        if (!is_array($spec_cats)) {
                            $spec_cats = array();
                        }

                        $cs_all_cats = get_categories('taxonomy=specialisms&child_of=0&hide_empty=0');
                        if (is_array($cs_all_cats) && sizeof($cs_all_cats) > 0) {
                            $cs_spce_options = '';

                            foreach ($cs_all_cats as $cs_cat) {

                                $cs_selected = in_array($cs_cat->slug, $spec_cats) ? ' selected="selected"' : '';
                                $cs_spce_options .= '<option ' . $cs_selected . ' value=' . esc_attr($cs_cat->slug) . '>' . CS_FUNCTIONS()->cs_special_chars($cs_cat->name) . '</option>';
                            }

                            $cs_opt_array = array(
                                'name' => esc_html__('Specialisms', 'jobhunt'),
                                'desc' => '',
                                'hint_text' => __('Choose job specialisms which do you want to show.if you don\'t have any specialisms create from :Dashboard >> Jobs >> specialisms.', 'jobhunt'),
                                'multi' => true,
                                'echo' => true,
                                'field_params' => array(
                                    'std' => $spec_cats,
                                    'id' => 'spec_cats',
                                    'cust_name' => 'spec_cats[' . $cs_rand_id . '][]',
                                    'options_markup' => true,
                                    'options' => $cs_spce_options,
                                    'return' => true,
                                ),
                            );

                            $cs_html_fields->cs_select_field($cs_opt_array);
                        }
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
                            'std' => 'job_specialisms',
                            'cust_id' => "",
                            'cust_name' => "cs_orderby[]",
                        );
                        $cs_form_fields2->cs_form_hidden_render($cs_opt_array);

                        $cs_opt_array = array(
                            'id' => '',
                            'std' => absint($cs_rand_id),
                            'cust_id' => "",
                            'cust_name' => "cs_spec_id[]",
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
             * modern selection box function
             */

            jQuery(document).ready(function ($) {
                chosen_selectionbox();
                popup_over();
            });
            /*
             *End popup over 
             */
        </script>
        <?php
        if ($die <> 1)
            die();
    }

    add_action('wp_ajax_jobcareer_pb_job_specialisms', 'jobcareer_pb_job_specialisms');
}
/*
 *
 *End Functon for specialisms Job
 *
 */