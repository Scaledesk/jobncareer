<?php

/**
 * File Type: Job hunt option fields file
 */
if (!class_exists('jobhunt_options_fields')) {

    class jobhunt_options_fields {

        public function __construct() {
            
        }

        /**
         * Start Function  how to create Fields Settings
         */
        public function cs_fields($cs_setting_options) {
            global $cs_plugin_options, $cs_form_fields2, $cs_html_fields, $help_text, $col_heading;

            $cs_plugin_options = get_option('cs_plugin_options');
            $counter = 0;
            $cs_counter = 0;
            $menu = '';
            $output = '';
            $parent_heading = '';
            $style = '';
            $cs_countries_list = '';
            foreach ($cs_setting_options as $value) {
                $counter++;
                $val = '';

                $select_value = '';
                if (isset($value['help_text']) && $value['help_text'] <> '') {
                    $help_text = $value['help_text'];
                } else {
                    $help_text = '';
                }
                if (isset($value['col_heading']) && $value['col_heading'] <> '') {
                    $col_heading = $value['col_heading'];
                } else {
                    $col_heading = '';
                }
                $cs_classes = '';
                if (isset($value['classes']) && $value['classes'] != "") {
                    $cs_classes = $value['classes'];
                }
                switch ($value['type']) {
                    case "heading":
                        $cs_opt_array = array(
                            'name' => $value['name'],
                            'fontawesome' => $value['fontawesome'],
                            'options' => $value['options'],
                        );

                        $menu .= $cs_html_fields->cs_set_heading($cs_opt_array);
                        break;

                    case "main-heading":
                        $cs_opt_array = array(
                            'name' => $value['name'],
                            'fontawesome' => $value['fontawesome'],
                            'id' => $value['id'],
                        );
                        $menu .= $cs_html_fields->cs_set_main_heading($cs_opt_array);
                        break;

                    case "sub-heading":
                        $cs_counter++;
                        $cs_opt_array = array(
                            'name' => $value['name'],
                            'counter' => $cs_counter,
                            'id' => $value['id'],
                            'extra' => isset($value['extra']) ? $value['extra'] : '',
                        );
                        $output .= $cs_html_fields->cs_set_sub_heading($cs_opt_array);
                        break;
                    case "col-right-text":
                        $cs_opt_array = array(
                            'col_heading' => $col_heading,
                            'help_text' => $help_text,
                            'extra' => isset($value['extra']) ? $value['extra'] : '',
                        );
                        $output .= $cs_html_fields->cs_set_col_right($cs_opt_array);
                        break;
                    case "announcement":
                        $cs_counter++;
                        $cs_opt_array = array(
                            'name' => $value['name'],
                            'std' => $value['std'],
                            'id' => $value['id'],
                        );
                        $output .= $cs_html_fields->cs_set_announcement($cs_opt_array);
                        break;
                    case "division":
                        $extra_atts = isset($value['extra_atts']) ? $value['extra_atts'] : '';
                        $d_enable = ' style="display:none;"';
                        if (isset($value['enable_val'])) {
                            $enable_id = isset($value['enable_id']) ? $value['enable_id'] : '';
                            $enable_val = isset($value['enable_val']) ? $value['enable_val'] : '';
                            $d_val = '';
                            if (isset($cs_plugin_options)) {
                                if (isset($cs_plugin_options[$enable_id])) {
                                    $d_val = $cs_plugin_options[$enable_id];
                                }
                            }
                            $d_enable = $d_val == $enable_val ? ' style="display:block;"' : ' style="display:none;"';
                        }
                        $output .= '<div' . $d_enable . ' ' . $extra_atts . '>';
                        break;

                    case "custom_div":
                        $attss = '';
                        if (isset($value['class']) && $value['class'] != '') {
                            $attss .= ' class="' . $value['class'] . '"';
                        }
                        if (isset($value['id']) && $value['id'] != '') {
                            $attss .= ' id="' . $value['id'] . '"';
                        }
                        $output .= '<div' . $attss . '>';
                        break;
                    case "division_close":
                        $output .= '</div>';
                        break;
                    case "section":

                        $cs_opt_array = array(
                            'id' => $value['id'],
                            'std' => $value['std'],
                        );

                        if (isset($value['accordion']) && $value['accordion'] <> '') {
                            $cs_opt_array['accordion'] = $value['accordion'];
                        }

                        if (isset($value['active']) && $value['active'] <> '') {
                            $cs_opt_array['active'] = $value['active'];
                        }

                        if (isset($value['parrent_id']) && $value['parrent_id'] <> '') {
                            $cs_opt_array['parrent_id'] = $value['parrent_id'];
                        }

                        $output .= $cs_html_fields->cs_set_section($cs_opt_array);
                        break;
                    case 'password' :
                        if (isset($cs_plugin_options)) {
                            if (isset($cs_plugin_options['cs_' . $value['id']])) {
                                $val = $cs_plugin_options['cs_' . $value['id']];
                            } else {
                                $val = $value['std'];
                            }
                        } else {
                            $val = $value['std'];
                        }
                        $cust_type = 'password';
                        $extra_atr = '';
                        $value['cust_type'] = isset($value['cust_type']) ? $value['cust_type'] : '';
                        if ($value['cust_type'] != '') {
                            $cust_type = $value['cust_type'];
                            $extra_atr = 'onClick="send_test_mail(\'' . esc_js(admin_url('admin-ajax.php')) . '\', \'' . esc_js(wp_jobhunt::plugin_url()) . '\')" value = "' . $value["std"] . '"';
                        }

                        $cs_opt_array = array(
                            'name' => $value['name'],
                            'id' => $value['id'],
                            'desc' => $value['desc'],
                            'hint_text' => $value['hint_text'],
                            'field_params' => array(
                                'std' => $val,
                                'cust_type' => $cust_type,
                                'extra_att' => $extra_atr,
                                'id' => $value['id'],
                                'return' => true,
                            ),
                        );

                        if (isset($value['classes']) && $value['classes'] <> '') {
                            $cs_opt_array['field_params']['classes'] = $value['classes'];
                        }

                        if (isset($value['split']) && $value['split'] <> '') {
                            $cs_opt_array['split'] = $value['split'];
                        }

                        $output .= $cs_html_fields->cs_text_field($cs_opt_array);

                        break;
                    case 'text' :
                        if (isset($cs_plugin_options)) {
                            if (isset($cs_plugin_options['cs_' . $value['id']])) {
                                $val = $cs_plugin_options['cs_' . $value['id']];
                            } else {
                                $val = $value['std'];
                            }
                        } else {
                            $val = $value['std'];
                        }
                        $active  = '';
                        if( isset ( $value['active'] ) && $value['active'] !=='' ){
                            $active = $value['active'];
                        }
                        $cust_type = '';
                        $extra_atr = '';
                        $value['cust_type'] = isset($value['cust_type']) ? $value['cust_type'] : '';
                        if ($value['cust_type'] != '') {
                            $cust_type = $value['cust_type'];
                            $extra_atr = 'onclick="javascript:send_smtp_mail(\'' . esc_js(admin_url('admin-ajax.php')) . '\');" ';
                        }

                        $cs_opt_array = array(
                            'name' => $value['name'],
                            'id' => $value['id'],
                            'desc' => $value['desc'],
                            'hint_text' => $value['hint_text'],
                            'field_params' => array(
                                'std' => $val,
                                'cust_type' => $cust_type,
                                'extra_atr' => $extra_atr,
                                'id' => $value['id'],
                                'active' => $active,
                                'return' => true,
                            ),
                        );

                        if (isset($value['classes']) && $value['classes'] <> '') {
                            $cs_opt_array['field_params']['classes'] = $value['classes'];
                        }

                        if (isset($value['split']) && $value['split'] <> '') {
                            $cs_opt_array['split'] = $value['split'];
                        }

//if($cs_opt_array['id']=="submit_test_email")
//{
//
//    $output .= '<form method="post" action="">';
//}
                        $output .= $cs_html_fields->cs_text_field($cs_opt_array);
//                   if($cs_opt_array['id']=="submit_test_email")
//{
//    $output .= '</form>';
//}     

                        break;

                    case 'text3' :
                        if (isset($cs_plugin_options)) {
                            if (isset($cs_plugin_options['cs_' . $value['id']])) {
                                $val = $cs_plugin_options['cs_' . $value['id']];
                                $val2 = $cs_plugin_options['cs_' . $value['id2']];
                                $val3 = $cs_plugin_options['cs_' . $value['id3']];
                            } else {
                                $val = $value['std'];
                                $val2 = $value['std2'];
                                $val3 = $value['std3'];
                            }
                        } else {
                            $val = $value['std'];
                            $val2 = $value['std2'];
                            $val3 = $value['std3'];
                        }

                        $cs_opt_array = array(
                            'name' => $value['name'],
                            'id' => 'radius_fields',
                            'desc' => '',
                            'hint_text' => $value['hint_text'],
                            'fields_list' => array(
                                array(
                                    'type' => 'text', 'field_params' => array(
                                        'std' => $val,
                                        'id' => $value['id'],
                                        'extra_atr' => ' placeholder="' . $value['placeholder'] . '"',
                                        'return' => true,
                                        'classes' => 'input-small',
                                    ),
                                ),
                                array(
                                    'type' => 'text', 'field_params' => array(
                                        'std' => $val2,
                                        'id' => $value['id2'],
                                        'extra_atr' => ' placeholder="' . $value['placeholder2'] . '"',
                                        'return' => true,
                                        'classes' => 'input-small',
                                    ),
                                ),
                                array(
                                    'type' => 'text', 'field_params' => array(
                                        'std' => $val3,
                                        'id' => $value['id3'],
                                        'extra_atr' => ' placeholder="' . $value['placeholder3'] . '"',
                                        'return' => true,
                                        'classes' => 'input-small',
                                    ),
                                )
                            ),
                        );

                        $output .= $cs_html_fields->cs_multi_fields($cs_opt_array);

                        break;
                    case 'range' :
                        if (isset($cs_plugin_options)) {
                            if (isset($cs_plugin_options['cs_' . $value['id']])) {
                                $val = $cs_plugin_options['cs_' . $value['id']];
                            } else {
                                $val = $value['std'];
                            }
                        } else {
                            $val = $value['std'];
                        }

                        $cs_opt_array = array(
                            'name' => $value['name'],
                            'id' => $value['id'],
                            'desc' => $value['desc'],
                            'hint_text' => $value['hint_text'],
                            'field_params' => array(
                                'std' => $val,
                                'id' => $value['id'],
                                'range' => true,
                                'min' => $value['min'],
                                'max' => $value['max'],
                                'return' => true,
                            ),
                        );

                        if (isset($value['split']) && $value['split'] <> '') {
                            $cs_opt_array['split'] = $value['split'];
                        }

                        $output .= $cs_html_fields->cs_text_field($cs_opt_array);

                        break;
                    case 'textarea':
                        $val = $value['std'];
                        $std = get_option($value['id']);
                        if (isset($cs_plugin_options)) {
                            if (isset($cs_plugin_options['cs_' . $value['id']])) {
                                $val = $cs_plugin_options['cs_' . $value['id']];
                            } else {
                                $val = $value['std'];
                            }
                        } else {
                            $val = $value['std'];
                        }
						if(!isset($value['cs_editor'])){
							$value['cs_editor']	= false;
						} 
                        $cs_opt_array = array(
                            'name' => $value['name'],
                            'id' => $value['id'],
                            'desc' => $value['desc'],
                            'hint_text' => $value['hint_text'],
                            'field_params' => array(
                                'std' => $val,
                                'id' => $value['id'],
                                'return' => true,
                                'cs_editor' => $value['cs_editor'],
                            ),
                        );

                        if (isset($value['split']) && $value['split'] <> '') {
                            $cs_opt_array['split'] = $value['split'];
                        }

                        $output .= $cs_html_fields->cs_textarea_field($cs_opt_array);
                        break;
                    case "radio":
                        if (isset($cs_plugin_options)) {
                            if (isset($cs_plugin_options['cs_' . $value['id']])) {
                                $select_value = $cs_plugin_options['cs_' . $value['id']];
                            }
                        } else {
                            
                        }
                        $output .= '<div id="mail_from_name" class="form-elements">';
                        $output .= '<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12"><label>' . $value['name'] . '</label></div>';
                        $output .= '<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">';
                        foreach ($value['options'] as $key => $option) {
                            $checked = '';
                            if ($select_value != '') {
                                if ($select_value == $option) {
                                    $checked = ' checked';
                                }
                            } else {
                                if ($value['std'] == $option) {
                                    $checked = ' checked';
                                }
                            }

                            $output .= $cs_html_fields->cs_radio_field(
                                    array(
                                        'name' => $value['name'],
                                        'id' => $value['id'],
                                        'classes' => '',
                                        'std' => '',
                                        'description' => $option,
                                        'hint' => '',
                                        'prefix_on' => false,
                                        'extra_atr' => $checked,
                                        'field_params' => array(
                                            'std' => $option,
                                            'id' => $value['id'],
                                            'return' => true,
                                        ),
                                    )
                            );
                        }
                        $output .= '</div></div>';
                        break;
                    case 'select':
                        if (isset($cs_plugin_options) and $cs_plugin_options <> '') {
                            if (isset($cs_plugin_options['cs_' . $value['id']]) and $cs_plugin_options['cs_' . $value['id']] <> '') {
                                $select_value = $cs_plugin_options['cs_' . $value['id']];
                            } else {
                                $select_value = $value['std'];
                            }
                        } else {
                            $select_value = $value['std'];
                        }

                        if ($select_value == 'absolute') {
                            if ($cs_plugin_options['cs_headerbg_options'] == 'cs_rev_slider') {
                                $output .= '<style>
                                                    #cs_headerbg_image_upload,#cs_headerbg_color_color,#cs_headerbg_image_box{ display:none;}
                                                    #tab-header-options ul#cs_headerbg_slider_1,#tab-header-options ul#cs_headerbg_options_header{ display:block;}
                                            </style>';
                            } else if ($cs_plugin_options['cs_headerbg_options'] == 'cs_bg_image_color') {
                                $output .= '<style>
                                                    #cs_headerbg_image_upload,#cs_headerbg_color_color,#cs_headerbg_image_box{ display:block;}
                                                    #tab-header-options ul#cs_headerbg_slider_1{ display:none; }
                                            </style>';
                            } else {
                                $output .= '<style>
                                                    #cs_headerbg_options_header{display:block;}
                                                    #cs_headerbg_image_upload,#cs_headerbg_color_color,#cs_headerbg_image_box{ display:none;}
                                                    #tab-header-options ul#cs_headerbg_slider_1{ display:none; }
                                            </style>';
                            }
                        } elseif ($select_value == 'relative') {
                            $output .='<style>
                                                    #tab-header-options ul#cs_headerbg_slider_1,#tab-header-options ul#cs_headerbg_options_header,#tab-header-options ul#cs_headerbg_image_upload,#tab-header-options ul#cs_headerbg_color_color,#tab-header-options #cs_headerbg_image_box{ display:none;}
                                      </style>';
                        }
                        $output .= ($value['id'] == 'cs_bgimage_position') ? '<div class="main_tab">' : '';
                        $select_header_bg = ($value['id'] == 'cs_header_position') ? 'onchange=javascript:cs_set_headerbg(this.value)' : '';

                        $cs_opt_array = array(
                            'name' => $value['name'],
                            'id' => $value['id'],
                            'desc' => $value['desc'],
                            'hint_text' => $value['hint_text'],
                            'field_params' => array(
                                'std' => $select_value,
                                'id' => $value['id'],
                                'options' => $value['options'],
                                'classes' => $cs_classes,
                                'return' => true,
                            ),
                        );

                        if (isset($value['change']) && $value['change'] == 'yes') {
                            $cs_opt_array['field_params']['onclick'] = $value['id'] . '_change(this.value)';
                        }

                        if (isset($value['split']) && $value['split'] <> '') {
                            $cs_opt_array['split'] = $value['split'];
                        }

                        $output .= $cs_html_fields->cs_select_field($cs_opt_array);

                        $output .=($value['id'] == 'cs_bgimage_position') ? '</div>' : '';
                        break;
                    case 'select_values' :
                        if (isset($cs_plugin_options) and $cs_plugin_options <> '') {
                            if (isset($cs_plugin_options['cs_' . $value['id']]) and $cs_plugin_options['cs_' . $value['id']] <> '') {
                                $select_value = $cs_plugin_options['cs_' . $value['id']];
                            } else {
                                $select_value = $value['std'];
                            }
                        } else {
                            $select_value = $value['std'];
                        }
                        $output .= ($value['id'] == 'cs_bgimage_position') ? '<div class="main_tab">' : '';
                        $select_header_bg = ($value['id'] == 'cs_header_position') ? 'onchange=javascript:cs_set_headerbg(this.value)' : '';
                        $cs_search_display = '';
                        if ($value['id'] == 'cs_search_by_location') {
                            $cs_job_loc_sugg = isset($cs_plugin_options['cs_job_loc_sugg']) ? $cs_plugin_options['cs_job_loc_sugg'] : '';
                            $cs_search_display = $cs_job_loc_sugg == 'Website' ? 'block' : 'none';
                        }
                        if ($value['id'] == 'cs_search_by_location_city') {
                            $cs_search_by_location = isset($cs_plugin_options['cs_search_by_location']) ? $cs_plugin_options['cs_search_by_location'] : '';
                            $cs_search_display = $cs_search_by_location == 'single_city' ? 'block' : 'none';
                        }
                        $cs_opt_array = array(
                            'name' => $value['name'],
                            'id' => $value['id'],
                            'desc' => $value['desc'],
                            'hint_text' => $value['hint_text'],
                            'field_params' => array(
                                'std' => $select_value,
                                'id' => $value['id'],
                                'options' => $value['options'],
                                'classes' => $cs_classes,
                                'return' => true,
                            ),
                        );

                        if (isset($value['change']) && $value['change'] == 'yes') {
                            $cs_opt_array['field_params']['onclick'] = $value['id'] . '_change(this.value)';
                        }

                        if (isset($value['extra_atts']) && $value['extra_atts'] != '') {
                            $cs_opt_array['field_params']['extra_atr'] = $value['extra_atts'];
                        }

                        if (isset($value['split']) && $value['split'] <> '') {
                            $cs_opt_array['split'] = $value['split'];
                        }

                        $output .= $cs_html_fields->cs_select_field($cs_opt_array);

                        break;
                    case 'ad_select':
                        if (isset($cs_plugin_options) and $cs_plugin_options <> '') {
                            if (isset($cs_plugin_options['cs_' . $value['id']]) and $cs_plugin_options['cs_' . $value['id']] <> '') {
                                $select_value = $cs_plugin_options['cs_' . $value['id']];
                            } else {
                                $select_value = $value['std'];
                            }
                        } else {
                            $select_value = $value['std'];
                        }
                        if ($select_value == 'absolute') {
                            if ($cs_plugin_options['cs_headerbg_options'] == 'cs_rev_slider') {
                                $output .='<style>
                                                    #cs_headerbg_image_upload,#cs_headerbg_color_color,#cs_headerbg_image_box{ display:none;}
                                                    #tab-header-options ul#cs_headerbg_slider_1,#tab-header-options ul#cs_headerbg_options_header{ display:block;}
                                            </style>';
                            } else if ($cs_plugin_options['cs_headerbg_options'] == 'cs_bg_image_color') {
                                $output .='<style>
                                                    #cs_headerbg_image_upload,#cs_headerbg_color_color,#cs_headerbg_image_box{ display:block;}
                                                    #tab-header-options ul#cs_headerbg_slider_1{ display:none; }
                                            </style>';
                            } else {
                                $output .='<style>
                                                    #cs_headerbg_options_header{display:block;}
                                                    #cs_headerbg_image_upload,#cs_headerbg_color_color,#cs_headerbg_image_box{ display:none;}
                                                    #tab-header-options ul#cs_headerbg_slider_1{ display:none; }
                                            </style>';
                            }
                        } elseif ($select_value == 'relative') {
                            $output .='<style>
                                            #tab-header-options ul#cs_headerbg_slider_1,#tab-header-options ul#cs_headerbg_options_header,#tab-header-options ul#cs_headerbg_image_upload,#tab-header-options ul#cs_headerbg_color_color,#tab-header-options #cs_headerbg_image_box{ display:none;}
                                     </style>';
                        }
                        $output .= ($value['id'] == 'cs_bgimage_position') ? '<div class="main_tab">' : '';
                        $select_header_bg = ($value['id'] == 'cs_header_position') ? 'onchange=javascript:cs_set_headerbg(this.value)' : '';
                        $cs_opt_array = array(
                            'name' => $value['name'],
                            'id' => $value['id'],
                            'desc' => $value['desc'],
                            'hint_text' => $value['hint_text'],
                            'field_params' => array(
                                'std' => $select_value,
                                'id' => $value['id'],
                                'options' => $value['options'],
                                'return' => true,
                            ),
                        );

                        if (isset($value['change']) && $value['change'] == 'yes') {
                            $cs_opt_array['field_params']['onclick'] = $value['id'] . '_change(this.value)';
                        }

                        if (isset($value['split']) && $value['split'] <> '') {
                            $cs_opt_array['split'] = $value['split'];
                        }

                        $output .= $cs_html_fields->cs_select_field($cs_opt_array);

                        break;

                    case "checkbox":
                        $std = '';
                        if (isset($cs_plugin_options)) {
                            if (isset($cs_plugin_options['cs_' . $value['id']])) {
                                $std = $cs_plugin_options['cs_' . $value['id']];
                            }
                        } else {
                            $std = $value['std'];
                        }
                        $simple = false;
                        if (isset($value['simple'])) {
                            $simple = $value['simple'];
                        }
                        $cs_opt_array = array(
                            'name' => $value['name'],
                            'id' => $value['id'],
                            'desc' => $value['desc'],
                            'hint_text' => $value['hint_text'],
                            'field_params' => array(
                                'std' => $std,
                                'id' => $value['id'],
                                'extra_atr' => isset($value['onchange']) ? 'onchange=' . $value['onchange'] : '',
                                'return' => true,
                                'simple' => $simple,
                            ),
                        );

                        if (isset($value['onchange']) && $value['onchange'] <> '') {
                            $cs_opt_array['field_params']['extra_atr'] = ' onchange=' . $value['onchange'];
                        }

                        if (isset($value['split']) && $value['split'] <> '') {
                            $cs_opt_array['split'] = $value['split'];
                        }

                        $output .= $cs_html_fields->cs_checkbox_field($cs_opt_array);

                        break;
                    case "color":
                        $val = $value['std'];
                        if (isset($cs_plugin_options)) {
                            if (isset($cs_plugin_options['cs_' . $value['id']])) {
                                $val = $cs_plugin_options['cs_' . $value['id']];
                            }
                        } else {
                            $std = $value['std'];
                            if ($std != '') {
                                $val = $std;
                            }
                        }
                        $cs_opt_array = array(
                            'name' => $value['name'],
                            'id' => $value['id'],
                            'desc' => $value['desc'],
                            'hint_text' => $value['hint_text'],
                            'field_params' => array(
                                'std' => $val,
                                'classes' => 'bg_color',
                                'id' => $value['id'],
                                'return' => true,
                            ),
                        );

                        if (isset($value['split']) && $value['split'] <> '') {
                            $cs_opt_array['split'] = $value['split'];
                        }

                        $output .= $cs_html_fields->cs_text_field($cs_opt_array);
                        break;
                    case "packages":
                        $obj = new cs_plugin_options();
                        $output .= $obj->cs_packages_section();
                        break;
                    case "cv_pkgs":
                        $obj = new cs_plugin_options();
                        $output .= $obj->cs_cv_pkgs_section();
                        break;
                    case "safetytext":
                        ob_start();
                        $obj = new cs_plugin_options();
                        $obj->cs_safetytext_section();
                        $post_data = ob_get_clean();
                        $output .= $post_data;
                        break;
                    case "gateways":
                        global $gateways;
                        $general_settings = new CS_PAYMENTS();
                        $cs_counter = '';
                        foreach ($gateways as $key => $value) {
                            $output .='<div class="theme-help">';
                            $output .='<h4>' . $value . '</h4>';
                            $output .='<div class="clear"></div>';
                            $output .='</div>';
                            if (class_exists($key)) {
                                $settings = new $key();
                                $cs_settings = $settings->settings();
                                $html = '';
                                foreach ($cs_settings as $key => $params) {
                                    ob_start();
                                    cs_settings_fields($key, $params);
                                    $post_data = ob_get_clean();
                                    $output .= $post_data;
                                }
                            }
                        }
                        break;

                    case "upload":
                        $cs_counter++;
                        if (isset($cs_plugin_options) and $cs_plugin_options <> '' && isset($cs_plugin_options['cs_' . $value['id']])) {
                            $val = $cs_plugin_options['cs_' . $value['id']];
                        } else {
                            $val = $value['std'];
                        }
                        $display = ($val <> '' ? 'display' : 'none');
                        if (isset($value['tab'])) {
                            $output .= '<div class="main_tab"><div class="horizontal_tab" style="display:' . $value['display'] . '" id="' . $value['tab'] . '">';
                        }
                        $cs_opt_array = array(
                            'name' => $value['name'],
                            'id' => $value['id'],
                            'std' => $val,
                            'desc' => $value['desc'],
                            'hint_text' => $value['hint_text'],
                            'field_params' => array(
                                'std' => $val,
                                'id' => $value['id'],
                                'return' => true,
                            ),
                        );

                        if (isset($value['split']) && $value['split'] <> '') {
                            $cs_opt_array['split'] = $value['split'];
                        }

                        $output .= $cs_html_fields->cs_upload_file_field($cs_opt_array);

                        if (isset($value['tab'])) {
                            $output.='</div></div>';
                        }
                        break;
                    case "upload logo":
                        $cs_counter++;

                        if (isset($cs_plugin_options) and $cs_plugin_options <> '' && isset($cs_plugin_options['cs_' . $value['id']])) {
                            $val = $cs_plugin_options['cs_' . $value['id']];
                        } else {
                            $val = $value['std'];
                        }

                        $display = ($val <> '' ? 'display' : 'none');
                        if (isset($value['tab'])) {
                            $output .='<div class="main_tab"><div class="horizontal_tab" style="display:' . $value['display'] . '" id="' . $value['tab'] . '">';
                        }
                        $cs_opt_array = array(
                            'name' => $value['name'],
                            'id' => $value['id'],
                            'std' => $val,
                            'desc' => $value['desc'],
                            'hint_text' => $value['hint_text'],
                            'field_params' => array(
                                'std' => $val,
                                'id' => $value['id'],
                                'return' => true,
                            ),
                        );

                        if (isset($value['split']) && $value['split'] <> '') {
                            $cs_opt_array['split'] = $value['split'];
                        }

                        $output .= $cs_html_fields->cs_upload_file_field($cs_opt_array);

                        if (isset($value['tab'])) {
                            $output.='</div></div>';
                        }
                        break;
                    case "custom_fields":
                        $cs_counter++;
                        global $cs_job_cus_fields;
                        $cs_job_cus_fields = get_option("cs_job_cus_fields");
                        $cs_fields_obj = new cs_custom_fields_options();
                        $output .= '<div class="inside-tab-content">
                                        <div class="dragitem">
                                            <div class="pb-form-buttons">
                                            <span class="cs_cus_fields_text">' . __("Click to Add", "jobhunt") . '</span>
                                                    <ul>
                                                     
                                                    <li><a ' . cs_tooltip_helptext_string(__('Text', 'jobhunt'), true) . ' href="javascript:cs_add_custom_field(\'jobcareer_pb_text\')" data-type="text" data-name="custom_text"><i class="icon-file-text-o"></i></a></li>
                                                    <li><a ' . cs_tooltip_helptext_string(__('Textarea', 'jobhunt'), true) . ' href="javascript:cs_add_custom_field(\'jobcareer_pb_textarea\')" data-type="textarea" data-name="custom_textarea"><i class="icon-text"></i></a></li>
                                                    <li><a ' . cs_tooltip_helptext_string(__('Dropdown', 'jobhunt'), true) . ' href="javascript:cs_add_custom_field(\'jobcareer_pb_dropdown\')" data-type="select" data-name="custom_select"><i class="icon-download10"></i></a></li>
                                                    <li><a ' . cs_tooltip_helptext_string(__('Date', 'jobhunt'), true) . ' href="javascript:cs_add_custom_field(\'jobcareer_pb_date\')" data-type="date" data-name="custom_date"><i class="icon-calendar-o"></i></a></li>
                                                    <li><a ' . cs_tooltip_helptext_string(__('Email', 'jobhunt'), true) . ' href="javascript:cs_add_custom_field(\'jobcareer_pb_email\')" data-type="email" data-name="custom_email"><i class="icon-envelope4"></i></a></li>
                                                    <li><a ' . cs_tooltip_helptext_string(__('Url', 'jobhunt'), true) . ' href="javascript:cs_add_custom_field(\'jobcareer_pb_url\')" data-type="url" data-name="custom_url"><i class="icon-link4"></i></a></li>
                                                    <li><a ' . cs_tooltip_helptext_string(__('Range', 'jobhunt'), true) . ' href="javascript:cs_add_custom_field(\'jobcareer_pb_range\')" data-type="url" data-name="custom_range"><i class=" icon-target5"></i></a></li>
                                                    </ul>
                                            </div>
                                        </div>
                                    <div id="cs_field_elements" class="cs-custom-fields">';
                        $cs_count_node = time();
                        if (is_array($cs_job_cus_fields) && sizeof($cs_job_cus_fields) > 0) {
                            foreach ($cs_job_cus_fields as $f_key => $cs_field) {
                                global $cs_f_counter;
                                $cs_f_counter = $f_key;
                                if (isset($cs_field['type']) && $cs_field['type'] == "text") {
                                    $cs_count_node++;
                                    $output .= $cs_fields_obj->jobcareer_pb_text(1, true);
                                } else if (isset($cs_field['type']) && $cs_field['type'] == "textarea") {
                                    $cs_count_node++;
                                    $output .= $cs_fields_obj->jobcareer_pb_textarea(1, true);
                                } else if (isset($cs_field['type']) && $cs_field['type'] == "dropdown") {
                                    $cs_count_node++;
                                    $output .= $cs_fields_obj->jobcareer_pb_dropdown(1, true);
                                } else if (isset($cs_field['type']) && $cs_field['type'] == "date") {
                                    $cs_count_node++;
                                    $output .= $cs_fields_obj->jobcareer_pb_date(1, true);
                                } else if (isset($cs_field['type']) && $cs_field['type'] == "email") {
                                    $cs_count_node++;
                                    $output .= $cs_fields_obj->jobcareer_pb_email(1, true);
                                } else if (isset($cs_field['type']) && $cs_field['type'] == "url") {
                                    $cs_count_node++;
                                    $output .= $cs_fields_obj->jobcareer_pb_url(1, true);
                                } else if (isset($cs_field['type']) && $cs_field['type'] == "range") {
                                    $cs_count_node++;
                                    $output .= $cs_fields_obj->jobcareer_pb_range(1, true);
                                }
                            }
                        }

                        $output .= '</div>
                                    <script type="text/javascript">
                                        jQuery(function() {
                                                cs_custom_fields_script(\'cs_field_elements\');
                                        });
                                        jQuery(document).ready(function($) {
                                                cs_check_fields_avail();
                                        });
                                        var counter = ' . esc_js($cs_count_node) . ';
                                        function cs_add_custom_field(action){
                                            counter++;
                                            var fields_data = "action=" + action + \'&counter=\' + counter;
                                            jQuery.ajax({
                                                type:"POST",
                                                url: "' . esc_js(admin_url('admin-ajax.php')) . '",
                                                data: fields_data,
                                                success:function(data){
                                                    jQuery("#cs_field_elements").append(data);
                                                }
                                            });
                                        }
                                    </script>
                                </div>';
                        break;

                    case "candidate_custom_fields":
                        $cs_counter++;

                        global $cs_candidate_cus_fields;

                        $cs_candidate_cus_fields = get_option("cs_candidate_cus_fields");

                        $cs_fields_obj = new cs_custom_candidate_fields_options();
                        $output .= '<div class="inside-tab-content">
                                        <div class="dragitem">
                                            <div class="pb-form-buttons">
                                                <span class="cs_cus_fields_text">' . __("Click to Add", "jobhunt") . '</span>
                                                <ul>
                                                    <li><a ' . cs_tooltip_helptext_string(__('Text', 'jobhunt'), true) . ' href="javascript:cs_add_candidate_custom_field(\'jobcareer_pb_candidate_text\')" data-type="text" data-name="custom_text"><i class="icon-file-text-o"></i></a></li>
                                                    <li><a ' . cs_tooltip_helptext_string(__('Textarea', 'jobhunt'), true) . ' href="javascript:cs_add_candidate_custom_field(\'jobcareer_pb_candidate_textarea\')" data-type="textarea" data-name="custom_textarea"><i class="icon-text"></i></a></li>
                                                    <li><a ' . cs_tooltip_helptext_string(__('Dropdown', 'jobhunt'), true) . ' href="javascript:cs_add_candidate_custom_field(\'jobcareer_pb_candidate_dropdown\')" data-type="select" data-name="custom_select"><i class="icon-download10"></i></a></li>
                                                    <li><a ' . cs_tooltip_helptext_string(__('Date', 'jobhunt'), true) . ' href="javascript:cs_add_candidate_custom_field(\'jobcareer_pb_candidate_date\')" data-type="date" data-name="custom_date"><i class="icon-calendar-o"></i></a></li>
                                                    <li><a ' . cs_tooltip_helptext_string(__('Email', 'jobhunt'), true) . ' href="javascript:cs_add_candidate_custom_field(\'jobcareer_pb_candidate_email\')" data-type="email" data-name="custom_email"><i class="icon-envelope4"></i></a></li>
                                                    <li><a ' . cs_tooltip_helptext_string(__('Url', 'jobhunt'), true) . ' href="javascript:cs_add_candidate_custom_field(\'jobcareer_pb_candidate_url\')" data-type="url" data-name="custom_url"><i class="icon-link4"></i></a></li>
                                                    <li><a ' . cs_tooltip_helptext_string(__('Range', 'jobhunt'), true) . ' href="javascript:cs_add_candidate_custom_field(\'jobcareer_pb_candidate_range\')" data-type="url" data-name="custom_range"><i class=" icon-target5"></i></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                            <div id="cs_candidate_field_elements" class="cs-custom-fields">';

                        $cs_count_node = time();
                        if (is_array($cs_candidate_cus_fields) && sizeof($cs_candidate_cus_fields) > 0) {
                            foreach ($cs_candidate_cus_fields as $f_key => $cs_field) {
                                global $cs_f_counter;
                                $cs_f_counter = $f_key;
                                if (isset($cs_field['type']) && $cs_field['type'] == "text") {
                                    $cs_count_node++;
                                    $output .= $cs_fields_obj->jobcareer_pb_candidate_text(1, true);
                                } else if (isset($cs_field['type']) && $cs_field['type'] == "textarea") {
                                    $cs_count_node++;
                                    $output .= $cs_fields_obj->jobcareer_pb_candidate_textarea(1, true);
                                } else if (isset($cs_field['type']) && $cs_field['type'] == "dropdown") {
                                    $cs_count_node++;
                                    $output .= $cs_fields_obj->jobcareer_pb_candidate_dropdown(1, true);
                                } else if (isset($cs_field['type']) && $cs_field['type'] == "date") {
                                    $cs_count_node++;
                                    $output .= $cs_fields_obj->jobcareer_pb_candidate_date(1, true);
                                } else if (isset($cs_field['type']) && $cs_field['type'] == "email") {
                                    $cs_count_node++;
                                    $output .= $cs_fields_obj->jobcareer_pb_candidate_email(1, true);
                                } else if (isset($cs_field['type']) && $cs_field['type'] == "url") {
                                    $cs_count_node++;
                                    $output .= $cs_fields_obj->jobcareer_pb_candidate_url(1, true);
                                } else if (isset($cs_field['type']) && $cs_field['type'] == "range") {
                                    $cs_count_node++;
                                    $output .= $cs_fields_obj->jobcareer_pb_candidate_range(1, true);
                                }
                            }
                        }
                        $output .= '</div>
                                    <script type="text/javascript">
                                        jQuery(function() {
                                            cs_custom_fields_script(\'cs_candidate_field_elements\');
                                        });
                                        jQuery(document).ready(function($) {
                                            cs_check_candidate_fields_avail();
                                        });
                                        var counter = ' . esc_js($cs_count_node) . ';
                                        function cs_add_candidate_custom_field(action){
                                            counter++;
                                            var fields_data = "action=" + action + \'&counter=\' + counter;
                                            jQuery.ajax({
                                                type:"POST",
                                                url: "' . esc_js(admin_url('admin-ajax.php')) . '",
                                                data: fields_data,
                                                success:function(data){
                                                    jQuery("#cs_candidate_field_elements").append(data);
                                                }
                                            });
                                        }
                                    </script>
                            </div>';
                        break;
                    case "employer_custom_fields":
                        $cs_counter++;
                        global $cs_employer_cus_fields;
                        $cs_employer_cus_fields = get_option("cs_employer_cus_fields");
                        $cs_fields_obj = new cs_employer_custom_fields_options();
                        $output .= '
                                    <div class="inside-tab-content">
                                        <div class="dragitem">
                                            <div class="pb-form-buttons">
                                            <span class="cs_cus_fields_text">' . __("Click to Add", "jobhunt") . '</span>
                                                <ul>
                                                    <li><a ' . cs_tooltip_helptext_string(__('Text', 'jobhunt'), true) . '  href="javascript:cs_add_employer_custom_field(\'jobcareer_pb_employer_text\')" data-type="text" data-name="custom_text"><i class="icon-file-text-o"></i></a></li>
                                                    <li><a ' . cs_tooltip_helptext_string(__('Textarea', 'jobhunt'), true) . '  href="javascript:cs_add_employer_custom_field(\'jobcareer_pb_employer_textarea\')" data-type="textarea" data-name="custom_textarea"><i class="icon-text"></i></a></li>
                                                    <li><a ' . cs_tooltip_helptext_string(__('Dropdown', 'jobhunt'), true) . '  href="javascript:cs_add_employer_custom_field(\'jobcareer_pb_employer_dropdown\')" data-type="select" data-name="custom_select"><i class="icon-download10"></i></a></li>
                                                    <li><a ' . cs_tooltip_helptext_string(__('Date', 'jobhunt'), true) . '  href="javascript:cs_add_employer_custom_field(\'jobcareer_pb_employer_date\')" data-type="date" data-name="custom_date"><i class="icon-calendar-o"></i></a></li>
                                                    <li><a ' . cs_tooltip_helptext_string(__('Email', 'jobhunt'), true) . '  href="javascript:cs_add_employer_custom_field(\'jobcareer_pb_employer_email\')" data-type="email" data-name="custom_email"><i class="icon-envelope4"></i></a></li>
                                                    <li><a ' . cs_tooltip_helptext_string(__('Url', 'jobhunt'), true) . '  href="javascript:cs_add_employer_custom_field(\'jobcareer_pb_employer_url\')" data-type="url" data-name="custom_url"><i class="icon-link4"></i></a></li>
                                                    <li><a ' . cs_tooltip_helptext_string(__('Range', 'jobhunt'), true) . '  href="javascript:cs_add_employer_custom_field(\'jobcareer_pb_employer_range\')" data-type="url" data-name="custom_range"><i class=" icon-target5"></i></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                            <div id="cs_employer_field_elements" class="cs-custom-fields">';
                        $cs_count_node = time();
                        if (is_array($cs_employer_cus_fields) && sizeof($cs_employer_cus_fields) > 0) {
                            foreach ($cs_employer_cus_fields as $f_key => $cs_field) {
                                global $cs_f_counter;
                                $cs_f_counter = $f_key;
                                if (isset($cs_field['type']) && $cs_field['type'] == "text") {
                                    $cs_count_node++;
                                    $output .= $cs_fields_obj->jobcareer_pb_employer_text(1, true);
                                } else if (isset($cs_field['type']) && $cs_field['type'] == "textarea") {
                                    $cs_count_node++;
                                    $output .= $cs_fields_obj->jobcareer_pb_employer_textarea(1, true);
                                } else if (isset($cs_field['type']) && $cs_field['type'] == "dropdown") {
                                    $cs_count_node++;
                                    $output .= $cs_fields_obj->jobcareer_pb_employer_dropdown(1, true);
                                } else if (isset($cs_field['type']) && $cs_field['type'] == "date") {
                                    $cs_count_node++;
                                    $output .= $cs_fields_obj->jobcareer_pb_employer_date(1, true);
                                } else if (isset($cs_field['type']) && $cs_field['type'] == "email") {
                                    $cs_count_node++;
                                    $output .= $cs_fields_obj->jobcareer_pb_employer_email(1, true);
                                } else if (isset($cs_field['type']) && $cs_field['type'] == "url") {
                                    $cs_count_node++;
                                    $output .= $cs_fields_obj->jobcareer_pb_employer_url(1, true);
                                } else if (isset($cs_field['type']) && $cs_field['type'] == "range") {
                                    $cs_count_node++;
                                    $output .= $cs_fields_obj->jobcareer_pb_employer_range(1, true);
                                }
                            }
                        }
                        $output .= '</div>
                                        <script type="text/javascript">
                                            jQuery(function() {
                                                cs_custom_fields_script(\'cs_employer_field_elements\');
                                            });
                                            jQuery(document).ready(function($) {
                                                cs_check_employer_fields_avail();
                                            });
                                            var counter = ' . esc_js($cs_count_node) . ';
                                            function cs_add_employer_custom_field(action){
                                                counter++;
                                                var fields_data = "action=" + action + \'&counter=\' + counter;
                                                jQuery.ajax({
                                                    type:"POST",
                                                    url: "' . esc_js(admin_url('admin-ajax.php')) . '",
                                                    data: fields_data,
                                                    success:function(data){
                                                        jQuery("#cs_employer_field_elements").append(data);
                                                    }
                                                });
                                            }
                                        </script>
                                    </div>';
                        break;
                    case 'select_dashboard':
                        if (isset($cs_plugin_options) and $cs_plugin_options <> '') {
                            if (isset($cs_plugin_options[$value['id']])) {
                                $select_value = $cs_plugin_options[$value['id']];
                            }
                        } else {
                            $select_value = $value['std'];
                        }
                        $field_args = array(
                            'depth' => 0,
                            'child_of' => 0,
                            'class' => 'chosen-select',
                            'sort_order' => 'ASC',
                            'sort_column' => 'post_title',
                            'show_option_none' => __('Please select a page', "jobhunt"),
                            'hierarchical' => '1',
                            'exclude' => '',
                            'include' => '',
                            'meta_key' => '',
                            'meta_value' => '',
                            'authors' => '',
                            'exclude_tree' => '',
                            'selected' => $select_value,
                            'echo' => 0,
                            'name' => $value['id'],
                            'post_type' => 'page'
                        );
                        $cs_opt_array = array(
                            'name' => $value['name'],
                            'id' => $value['id'],
                            'desc' => $value['desc'],
                            'hint_text' => $value['hint_text'],
                            'std' => $select_value,
                            'args' => $field_args,
                            'return' => true,
                        );

                        if (isset($value['split']) && $value['split'] <> '') {
                            $cs_opt_array['split'] = $value['split'];
                        }

                        $output .= $cs_html_fields->cs_select_page_field($cs_opt_array);

                        break;
                    case 'default_location_fields':
                        global $cs_plugin_options, $post;
                        $cs_map_latitude = isset($cs_plugin_options['map_latitude']) ? $cs_plugin_options['map_latitude'] : '';
                        $cs_map_longitude = isset($cs_plugin_options['map_longitude']) ? $cs_plugin_options['map_longitude'] : '';
                        $cs_map_zoom = isset($cs_plugin_options['map_zoom']) ? $cs_plugin_options['map_zoom'] : '11';
                        if (isset($cs_plugin_options) && !empty($cs_plugin_options)) {
                            $cs_post_loc_city = $cs_plugin_options['cs_post_loc_city'];
                            $cs_post_loc_country = $cs_plugin_options['cs_post_loc_country'];
                            $cs_post_loc_latitude = $cs_plugin_options['cs_post_loc_latitude'];
                            $cs_post_loc_longitude = $cs_plugin_options['cs_post_loc_longitude'];
                            $cs_post_loc_zoom = $cs_plugin_options['cs_post_loc_zoom'];
                            $cs_post_loc_address = $cs_plugin_options['cs_post_loc_address'];
                            $cs_add_new_loc = $cs_plugin_options['cs_add_new_loc'];
                        } else {
                            //if location is not set then get from default location from settings
                            $cs_post_loc_latitude = $cs_post_loc_city = $cs_post_loc_country = '';
                            $cs_post_loc_latitude = '';
                            $cs_post_loc_longitude = '';
                            $cs_post_loc_zoom = '';
                            $cs_post_loc_address = '';
                            $loc_city = '';
                            $loc_postcode = '';
                            $loc_region = '';
                            $loc_country = '';
                            $event_map_switch = '';
                            $event_map_heading = '';
                            $cs_add_new_loc = '';
                        }
                        if ($cs_post_loc_latitude == '')
                            $cs_post_loc_latitude = $cs_map_latitude;
                        if ($cs_post_loc_longitude == '')
                            $cs_post_loc_longitude = $cs_map_longitude;
                        if ($cs_post_loc_zoom == '')
                            $cs_post_loc_zoom = $cs_map_zoom;
                        $cs_jobhunt = new wp_jobhunt();
                        $cs_jobhunt->cs_location_gmap_script();
                        $cs_jobhunt->cs_google_place_scripts();
                        $cs_jobhunt->cs_autocomplete_scripts();
                        $locations_parent_id = 0;
                        $country_args = array(
                            'orderby' => 'name',
                            'order' => 'ASC',
                            'fields' => 'all',
                            'slug' => '',
                            'hide_empty' => false,
                            'parent' => $locations_parent_id,
                        );
                        $cs_location_countries = get_terms('cs_locations', $country_args);
                        $location_countries_list = '';
                        $location_states_list = '';
                        $location_cities_list = '';
                        $iso_code_list = '';
                        $iso_code_list_main = '';
                        if (isset($cs_location_countries) && !empty($cs_location_countries)) {
                            $selected_iso_code = '';
                            foreach ($cs_location_countries as $key => $country) {
                                $t_id_main = $country->term_id;
                                $iso_code_list_main = get_option("iso_code_$t_id_main");
                                if (isset($iso_code_list_main['text'])) {
                                    $iso_code_list_main = $iso_code_list_main['text'];
                                }
                                $selected_contry = '';
                                if (isset($cs_post_loc_country) && $cs_post_loc_country == $country->slug) {
                                    $selected_contry = 'selected';
                                    $t_id = $country->term_id;
                                    $iso_code_list = get_option("iso_code_$t_id");

                                    if (isset($iso_code_list['text'])) {

                                        $selected_iso_code = $iso_code_list['text'];
                                    }
                                }
                                $location_countries_list .= "<option " . $selected_contry . "  value='" . $country->slug . "' data-name='" . $iso_code_list_main . "'>" . $country->name . "</option>";
                            }
                        }
                        $selected_country = $cs_post_loc_country;
                        $selected_city = $cs_post_loc_city;
                        $states = '';
                        if ($cs_post_loc_country != '') {
                            $selected_spec = get_term_by('slug', $selected_country, 'cs_locations');
                            $state_parent_id = $selected_spec->term_id;
                            $states_args = array(
                                'orderby' => 'name',
                                'order' => 'ASC',
                                'fields' => 'all',
                                'slug' => '',
                                'hide_empty' => false,
                                'parent' => $state_parent_id,
                            );
                            $cities = get_terms('cs_locations', $states_args);
                        }
                        if (isset($cs_location_countries) && !empty($cs_location_countries) && isset($cs_post_loc_country) && !empty($cs_post_loc_country)) {
                            // load all cities against state
                            if (isset($cities) && $cities != '' && is_array($cities)) {
                                foreach ($cities as $key => $city) {
                                    $selected = ( $selected_city == $city->slug) ? 'selected' : '';
                                    $location_cities_list .= "<option " . $selected . " value='" . $city->slug . "'>" . $city->name . "</option>";
                                }
                            }
                        }

                        $output .= '<fieldset class="gllpLatlonPicker"  style="width:100%; float:left;">
                                        <div class="page-wrap page-opts left" style="overflow:hidden; position:relative;" id="locations_wrap" data-themeurl="' . wp_jobhunt::plugin_url() . '" data-plugin_url="' . wp_jobhunt::plugin_url() . '" data-ajaxurl="' . esc_js(admin_url('admin-ajax.php'), 'jobhunt') . '" data-map_marker="' . wp_jobhunt::plugin_url() . '/assets/images/map-marker.png">
                                            <div class="option-sec" style="margin-bottom:0;">
                                                <div class="opt-conts">';

                        $cs_opt_array = array(
                            'name' => __('Country', 'jobhunt'),
                            'id' => 'post_loc_country',
                            'desc' => '',
                            'field_params' => array(
                                'std' => '',
                                'id' => 'post_loc_country',
                                'cust_id' => 'loc_country',
                                'classes' => 'chosen-select form-select-country dir-map-search single-select SlectBox',
                                'options_markup' => true,
                                'return' => true,
                            ),
                        );

                        if (isset($value['contry_hint']) && $value['contry_hint'] != '') {
                            $cs_opt_array['hint_text'] = $value['contry_hint'];
                        }

                        if (isset($location_countries_list) && $location_countries_list != '') {
                            $cs_opt_array['field_params']['options'] = '<option value="">' . __('Select Country', 'jobhunt') . '</option>' . $location_countries_list;
                        } else {
                            $cs_opt_array['field_params']['options'] = '<option value="">' . __('Select Country', 'jobhunt') . '</option>';
                        }

                        if (isset($value['split']) && $value['split'] <> '') {
                            $cs_opt_array['split'] = $value['split'];
                        }

                        $output .= $cs_html_fields->cs_select_field($cs_opt_array);

                        $cs_opt_array = array(
                            'name' => __('City', 'jobhunt'),
                            'id' => 'post_loc_city',
                            'desc' => '',
                            'field_params' => array(
                                'std' => '',
                                'id' => 'post_loc_city',
                                'cust_id' => 'loc_city',
                                'classes' => 'chosen-select form-select-city dir-map-search single-select',
                                'markup' => '<span class="loader-cities"></span>',
                                'options_markup' => true,
                                'return' => true,
                            ),
                        );

                        if (isset($value['city_hint']) && $value['city_hint'] != '') {
                            $cs_opt_array['hint_text'] = $value['city_hint'];
                        }

                        if (isset($location_cities_list) && $location_cities_list != '') {
                            $cs_opt_array['field_params']['options'] = '<option value="">' . __('Select City', 'jobhunt') . '</option>' . $location_cities_list;
                        } else {
                            $cs_opt_array['field_params']['options'] = '<option value="">' . __('Select City', 'jobhunt') . '</option>';
                        }

                        if (isset($value['split']) && $value['split'] <> '') {
                            $cs_opt_array['split'] = $value['split'];
                        }

                        $output .= $cs_html_fields->cs_select_field($cs_opt_array);



                        $cs_opt_array = array(
                            'name' => __('Complete Address', 'jobhunt'),
                            'id' => 'post_loc_address',
                            'desc' => '',
                            'field_params' => array(
                                'std' => $cs_post_loc_address,
                                'id' => 'post_loc_address',
                                'classes' => 'directory-search-location',
                                'extra_atr' => 'onkeypress="cs_gl_search_map(this.value)"',
                                'cust_id' => 'loc_address',
                                'return' => true,
                            ),
                        );

                        if (isset($value['address_hint']) && $value['address_hint'] != '') {
                            $cs_opt_array['hint_text'] = $value['address_hint'];
                        }

                        if (isset($value['split']) && $value['split'] <> '') {
                            $cs_opt_array['split'] = $value['split'];
                        }

                        $output .= $cs_html_fields->cs_text_field($cs_opt_array);

                        $cs_opt_array = array(
                            'name' => __('Latitude', 'jobhunt'),
                            'id' => 'post_loc_latitude',
                            'desc' => '',
                            'styles' => 'display:none;',
                            'field_params' => array(
                                'std' => $cs_post_loc_latitude,
                                'id' => 'post_loc_latitude',
                                'cust_type' => 'hidden',
                                'classes' => 'gllpLatitude',
                                'return' => true,
                            ),
                        );

                        if (isset($value['split']) && $value['split'] <> '') {
                            $cs_opt_array['split'] = $value['split'];
                        }
                        $output .= $cs_html_fields->cs_text_field($cs_opt_array);
                        $cs_opt_array = array(
                            'name' => __('Longitude', 'jobhunt'),
                            'id' => 'post_loc_longitude',
                            'desc' => '',
                            'styles' => 'display:none;',
                            'field_params' => array(
                                'std' => $cs_post_loc_longitude,
                                'id' => 'post_loc_longitude',
                                'cust_type' => 'hidden',
                                'classes' => 'gllpLongitude',
                                'return' => true,
                            ),
                        );

                        if (isset($value['split']) && $value['split'] <> '') {
                            $cs_opt_array['split'] = $value['split'];
                        }

                        $output .= $cs_html_fields->cs_text_field($cs_opt_array);

                        $cs_opt_array = array(
                            'name' => '',
                            'id' => 'map_search_btn',
                            'desc' => '',
                            'field_params' => array(
                                'std' => __('Search This Location on Map', 'jobhunt'),
                                'id' => 'map_search_btn',
                                'cust_type' => 'button',
                                'classes' => 'gllpSearchButton',
                                'return' => true,
                            ),
                        );

                        if (isset($value['split']) && $value['split'] <> '') {
                            $cs_opt_array['split'] = $value['split'];
                        }
                        $output .= $cs_html_fields->cs_text_field($cs_opt_array);
                        $output .= $cs_html_fields->cs_full_opening_field(array());
                        $output .= '
                        <div class="clear"></div>';

                        $cs_opt_array = array(
                            'id' => 'add_new_loc',
                            'std' => $cs_add_new_loc,
                            'cust_type' => 'hidden',
                            'classes' => 'gllpSearchField',
                            'return' => true,
                        );

                        $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

                        $cs_opt_array = array(
                            'id' => 'post_loc_zoom',
                            'std' => $cs_post_loc_zoom,
                            'cust_type' => 'hidden',
                            'classes' => 'gllpZoom',
                            'return' => true,
                        );

                        $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

                        $output .= '
											<div class="clear"></div>
											<div class="cs-map-section" style="float:left; width:100%; height:100%;">
												<div class="gllpMap" id="cs-map-location-id"></div>
											</div>';

                        $output .= $cs_html_fields->cs_closing_field(array(
                            'desc' => '',
                                )
                        );

                        $output .= '
                                        </div>
                                    </div>
                                </div>
                                    </fieldset>';
                        $output .= '<script type="text/javascript">
                            var autocomplete;
                                        jQuery(document).ready(function () {
                                            cs_load_location_ajax();
                                        });
                                        function cs_gl_search_map() {
                                        alert("ddd");
                                            var vals;
                                            vals = jQuery(\'#loc_address\').val();
                                            vals = vals + ", " + jQuery(\'#loc_city\').val();
                                            vals = vals + ", " + jQuery(\'#loc_region\').val();
                                            vals = vals + ", " + jQuery(\'#loc_country\').val();
                                            jQuery(\'.gllpSearchField\').val(vals);
                                        }
                                        (function ($) {
                                            $(function () {
                                    ' . $cs_jobhunt->cs_google_place_scripts() . '
                                                autocomplete = new google.maps.places.Autocomplete(document.getElementById(\'loc_address\'));';

                        if (isset($selected_iso_code) && $selected_iso_code != '') {
                            $output .= 'autocomplete.setComponentRestrictions({\'country\': \'' . $selected_iso_code . '\'});';
                        }
                        $output .= '});
                            

                                        })(jQuery);
                                    </script>';


                        break;
                    case 'generate_backup':
                        global $wp_filesystem;
                        $backup_url = wp_nonce_url('edit.php?post_type=vehicles&page=cs_settings');
                        if (false === ($creds = request_filesystem_credentials($backup_url, '', false, false, array()) )) {
                            return true;
                        }
                        if (!WP_Filesystem($creds)) {
                            request_filesystem_credentials($backup_url, '', true, false, array());
                            return true;
                        }
                        $cs_upload_dir = wp_jobhunt::plugin_dir() . 'admin/settings/backups/';
                        $cs_upload_dir_path = wp_jobhunt::plugin_url() . 'admin/settings/backups/';
                        $cs_all_list = $wp_filesystem->dirlist($cs_upload_dir);
                        $output .= '<div class="backup_generates_area" data-ajaxurl="' . esc_url(admin_url('admin-ajax.php')) . '">';
                        $output .= '
                                    <div class="theme-help">
                                            <h4>' . __('Import Options', "jobhunt") . '</h4>
                                    </div>';

                        $output .= $cs_html_fields->cs_opening_field(array(
                            'name' => __("File URL", 'jobhunt'),
                            'hint_text' => __('Input the Url from another location and hit Import Button to apply settings.', "jobhunt"),
                                )
                        );

                        $output .= '<div  class="external_backup_areas">';
                        $cs_opt_array = array(
                            'std' => '',
                            'cust_id' => "bkup_import_url",
                            'cust_name' => '',
                            'classes' => 'input-medium',
                            'return' => true,
                        );
                        $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);
                        $cs_opt_array = array(
                            'std' => __('Import', 'jobs'),
                            'cust_id' => "cs-p-backup-url-restore",
                            'cust_name' => '',
                            'cust_type' => 'button',
                            'return' => true,
                        );
                        $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);
                        $output .= '</div>';

                        $output .= $cs_html_fields->cs_closing_field(array(
                            'desc' => '',
                                )
                        );

                        $output .= '<div class="theme-help">
                                            <h4>' . __('Export Options', "jobhunt") . '</h4>
                                    </div>';


                        $output .= $cs_html_fields->cs_opening_field(array(
                            'name' => __("Generated Files", 'jobhunt'),
                            'hint_text' => __('Here you can Generate/Download Backups. Also you can use these Backups to Restore settings.', "jobhunt"),
                                )
                        );

                        if (is_array($cs_all_list) && sizeof($cs_all_list) > 0) {
                            $cs_list_count = 1;
                            $bk_options = '';
                            foreach ($cs_all_list as $file_key => $file_val) {
                                if (isset($file_val['name'])) {
                                    $cs_slected = sizeof($cs_all_list) == $cs_list_count ? ' selected="selected"' : '';
                                    $bk_options .= '<option' . $cs_slected . '>' . $file_val['name'] . '</option>';
                                }
                                $cs_list_count++;
                            }
                            $cs_opt_array = array(
                                'std' => __('Import', 'jobhunt'),
                                'cust_id' => "",
                                'cust_name' => '',
                                'classes' => 'input-medium chosen-select-no-single',
                                'extra_atr' => ' onchange="cs_set_p_filename(this.value, \'' . esc_url($cs_upload_dir_path) . '\')"',
                                'options_markup' => true,
                                'options' => $bk_options,
                                'return' => true,
                            );
                            $output .= $cs_html_fields->cs_form_select_render($cs_opt_array);

                            $output .= '<div class="backup_action_btns">';
                            if (isset($file_val['name'])) {
                                $cs_opt_array = array(
                                    'std' => __('Restore', 'jobhunt'),
                                    'cust_id' => "cs-p-backup-restore",
                                    'cust_name' => '',
                                    'extra_atr' => ' data-file="' . $file_val['name'] . '"',
                                    'cust_type' => 'button',
                                    'return' => true,
                                );
                                $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                $output .= '<a download="' . $file_val['name'] . '" href="' . esc_url($cs_upload_dir_path . $file_val['name']) . '">' . __('Download', "jobhunt") . '</a>';
                                $cs_opt_array = array(
                                    'std' => __('Delete', 'jobs'),
                                    'cust_id' => "cs-p-backup-delte",
                                    'cust_name' => '',
                                    'extra_atr' => ' data-file="' . $file_val['name'] . '"',
                                    'cust_type' => 'button',
                                    'return' => true,
                                );
                                $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);
                            }

                            $output .= '</div>';
                            $output .= '<div>&nbsp;</div>';
                        }

                        $cs_opt_array = array(
                            'std' => __('Generate Backup', 'jobhunt'),
                            'cust_id' => "cs-p-bkp",
                            'cust_name' => '',
                            'extra_atr' => ' onclick="javascript:cs_pl_backup_generate(\'' . esc_js(admin_url('admin-ajax.php')) . '\');"',
                            'cust_type' => 'button',
                            'return' => true,
                        );
                        $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

                        $output .= $cs_html_fields->cs_closing_field(array(
                            'desc' => '',
                                )
                        );
                        $output .= '</div>';
                        break;
                    case 'user_import_export':
                        global $wp_filesystem;

                        //$output .= '<div class="backup_generates_area" data-ajaxurl="' . esc_url(admin_url('admin-ajax.php')) . '">';
                        $output .= '';

                        $output .= $cs_html_fields->cs_opening_field(array(
                            'name' => __("File URL", 'jobhunt'),
                            'hint_text' => __('', "jobhunt"),
                                )
                        );

                        $output .= '<div class="external_backup_areas">';
                        $cs_opt_array = array(
                            'std' => '',
                            'cust_id' => "user_import_url",
                            'cust_name' => '',
                            'classes' => 'input-medium',
                            'return' => true,
                        );
                        $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);
                        $cs_opt_array = array(
                            'std' => __('Import Users', 'jobhunt'),
                            'cust_id' => "cs-p-backup-url-restore",
                            'cust_name' => '',
                            'cust_type' => 'button',
                            'return' => true,
                        );
                        $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);
                        $output .= '</div>';

                        $output .= $cs_html_fields->cs_closing_field(array(
                            'desc' => '',
                                )
                        );


                        // $output .= '</div>';
                        break;


                        $output .= '</div>';
                        $output .= '</tbody>
							</table></div></div>';
                }
            }
            $output .= '</div>';
            return array($output, $menu);
        }

        /**
         * End Function  how to create Fields Settings
         */
    }

}