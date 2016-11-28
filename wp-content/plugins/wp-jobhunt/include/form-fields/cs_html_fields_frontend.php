<?php

/**
 * File Type: Form Fields
 */
if (!class_exists('cs_html_fields_frontend')) {

    class cs_html_fields_frontend extends cs_form_fields2 {

        private $counter = 0;

        public function __construct() {
            // Do something...
        }

        /* ----------------------------------------------------------------------
         * @ render label
         * --------------------------------------------------------------------- */

        public function cs_form_label($name = 'Label Not defined') {
            global $post, $pagenow;

            $cs_output = '<li class="to-label">';
            $cs_output .= '<label>' . $name . '</label>';
            $cs_output .= '</li>';

            return $cs_output;
        }

        /**
         * @ render description
         */
        public function cs_form_description($description = '') {
            global $post, $pagenow;
            if ($description == '') {
                return;
            }
            $cs_output = '<div class="left-info">';
            $cs_output .= '<p>' . $description . '</p>';
            $cs_output .= '</div>';
            return $cs_output;
        }

        /**
         * @ render Headings
         */
        public function cs_heading_render($params = '') {
            global $post;
            extract($params);
            $cs_output = '<div class="theme-help" id="' . sanitize_html_class($id) . '">
                            <h4 style="padding-bottom:0px;">' . esc_attr($name) . '</h4>
                            <div class="clear"></div>
                          </div>';
            echo force_balance_tags($cs_output);
        }

        /**
         * @ render text field
         */
        public function cs_form_text_render($params = '') {
            global $post, $pagenow;
            extract($params);
            $cs_output = '';
            $std = '';
            $id = '';

            $prefix_enable = 'true'; // default value of prefix add in name and id

            if (isset($prefix_on)) {
                $prefix_enable = $prefix_on;
            }

            $prefix = 'cs_'; // default prefix
            if (isset($field_prefix) && $field_prefix != '') {
                $prefix = $field_prefix;
            }
            if ($prefix_enable != true) {
                $prefix = '';
            }
            if ($pagenow == 'post.php') {
                if (isset($cus_field) && $cus_field == true) {
                    $cs_value = get_post_meta($post->ID, $id, true);
                } else {
                    $cs_value = get_post_meta($post->ID, $prefix . $id, true);
                }
            } else {
                $cs_value = isset($std) ? $std : '';
            }

            if (isset($cs_value) && $cs_value != '') {
                $value = $cs_value;
            } else {
                $value = $std;
            }

            $cs_rand_id = time();

            if (isset($rand_id) && $rand_id != '') {
                $cs_rand_id = $rand_id;
            }
            $cs_output = '';

            $cs_styles = '';
            if (isset($styles) && $styles != '') {
                $cs_styles = ' style="' . $styles . '"';
            }
            $cust_id = isset($id) ? ' id="' . $id . '"' : '';
            $extra_attr = isset($extra_att) ? ' ' . $extra_att . ' ' : '';

              if (isset($cus_field) && $cus_field == true) {
                $html_name = ' name="' . $prefix . 'cus_field[' . sanitize_html_class($id) . ']"';
            } else {
                $html_name = ' name="' . $prefix . sanitize_html_class($id) . '"';
            }

            if (isset($array) && $array == true) {
                $html_id = ' id="' . $prefix . sanitize_html_class($id) . $cs_rand_id . '"';
                $html_name = ' name="' . $prefix . sanitize_html_class($id) . '_array[]"';
            }

            if (isset($cust_id) && $cust_id != '') {
                $html_id = ' id="' . $cust_id . '"';
            }

            if (isset($cust_name) && $cust_name != '') {
                $html_name = ' name="' . $cust_name . '"';
            }

            // Disabled Field
            $cs_visibilty = '';
            if (isset($active) && $active == 'in-active') {
                $cs_visibilty = 'readonly="readonly"';
            }

            $cs_required = '';
            if (isset($required) && $required == 'yes') {
                $cs_required = ' required="required"';
            }

            $cs_classes = '';
            if (isset($classes) && $classes != '') {
                $cs_classes = ' class="' . $classes . '"';
            }
            $extra_atributes = '';
            if (isset($extra_atr) && $extra_atr != '') {
                $extra_atributes = $extra_atr;
            }

            $cs_input_type = 'text';
            if (isset($cust_type) && $cust_type != '') {
                $cs_input_type = $cust_type;
            }

            $cs_icon = '';
            $cs_icon = (isset($icon) and $icon <> '') ? '<i class="' . $icon . '"></i>' : '';
            if (isset($cs_before) && $cs_before != '') {
                $cs_output .= '<div class="' . $cs_before . '">';
            }
            if (isset($cs_after) && $cs_after != '') {
                $cs_output .= '</div>';
            }



            $html_id = ' id="cs_' . sanitize_html_class($id) . '"';
            $cs_output .= $cs_icon;

            $cs_output .= parent::cs_form_text_render($field_params);



            if (isset($echo) && $echo == true) {
                echo force_balance_tags($cs_output);
            } else {
                return $cs_output;
            }
        }

        /**
         * @ render Radio field
         */
        public function cs_form_radio_render($params = '') {
            global $post, $pagenow;
            extract($params);
            $cs_value = get_post_meta($post->ID, 'cs_' . $id, true);
            if (isset($cs_value) && $cs_value != '') {
                $value = $cs_value;
            } else {
                $value = $std;
            }
            $cs_output = '<ul class="form-elements">';
            $cs_output .= $this->cs_form_label($name);
            $cs_output .= '<li class="to-field">';
            $cs_output .= '<div class="input-sec">';
            $cs_output .= parent::cs_form_radio_render($field_params);
            $cs_output .= '</div>';
            $cs_output .= $this->cs_form_description($description);
            $cs_output .= '</li>';
            $cs_output .= '</ul>';
            if (isset($echo) && $echo == true) {
                echo force_balance_tags($cs_output);
            } else {
                return $cs_output;
            }
        }

        /**
         * @ render text field
         */
        public function cs_form_hidden_render($params = '') {
            global $post, $pagenow;
            extract($params);
            $cs_rand_id = time();
            $html_name = ' name="cs_' . sanitize_html_class($id) . '"';
            if (isset($array) && $array == true) {
                $html_name = ' name="cs_' . sanitize_html_class($id) . '_array[]"';
            }
            $cs_output .= parent::cs_form_hidden_render($field_params);

            if (isset($return) && $return == true) {
                return force_balance_tags($cs_output);
            } else {
                echo force_balance_tags($cs_output);
            }
        }

        /**
         * @ render Date field
         */
        public function cs_form_date_render($params = '') {
            global $post, $pagenow;
            extract($params);


            $cs_output = '';
            $cs_styles = '';
            if (isset($styles) && $styles != '') {
                $cs_styles = ' style="' . $styles . '"';
            }
            $cust_id = isset($id) ? ' id="' . $id . '"' : '';
            $extra_attr = isset($extra_att) ? ' ' . $extra_att . ' ' : '';
            $cs_value = get_post_meta($post->ID, 'cs_' . $id, true);
            if (isset($cs_value) && $cs_value != '') {
                $value = $cs_value;
            } else {
                $value = $std;
            }
            $cs_format = 'd-m-Y';
            if (isset($format) && $format != '') {
                $cs_format = $format;
            }
            $cs_required = '';
            if (isset($required) && $required == 'yes') {
                $cs_required = ' required="required"';
            }
            if (isset($force_std) && $force_std == true) {
                $value = $std;
            }
            $cs_rand_id = time();
            $html_id = ' id="cs_' . sanitize_html_class($id) . '"';
            $html_name = ' name="cs_' . sanitize_html_class($id) . '"';
            $cs_piker_id = $id;
            if (isset($array) && $array == true) {
                $html_id = ' id="cs_' . sanitize_html_class($id) . $cs_rand_id . '"';
                $html_name = ' name="cs_' . sanitize_html_class($id) . '_array[]"';
                $cs_piker_id = $id . $cs_rand_id;
            }
            if (isset($force_empty) && $force_empty == true) {
                $value = '';
            }
            $cs_output = '<div  class="' . $classes . '">';
            $cs_output .= '<script>
                                jQuery(function(){
                                    jQuery("#cs_' . $cs_piker_id . '").datetimepicker({
                                        format:"' . $cs_format . '",
                                        timepicker:false
                                    });
                                });
                          </script>';
            $cs_output .= parent::cs_form_date_render($field_params);
            $cs_output .= $this->cs_form_description($description);
            $cs_output .= '</div>';
            if (isset($echo) && $echo == true) {
                echo force_balance_tags($cs_output);
            } else {
                return $cs_output;
            }
        }

        /**
         * @ render Textarea field
         */
        public function cs_form_textarea_render($params = '') {
            global $post, $pagenow;
            extract($params);

            if (!isset($std)) {
                $std = '';
            }
            if (!isset($description)) {
                $description = '';
            }
            if (!isset($id)) {
                $id = '';
            }
            if ($pagenow == 'post.php') {
                $cs_value = get_post_meta($post->ID, 'cs_' . $id, true);
            } else {
                $cs_value = $std;
            }

            if (isset($cs_value) && $cs_value != '') {
                $value = $cs_value;
            } else {
                $value = $std;
            }
            $cs_rand_id = time();
            if (isset($force_std) && $force_std == true) {
                $value = $std;
            }

            $cs_output = '';
            $cs_styles = '';
            if (isset($styles) && $styles != '') {
                $cs_styles = ' style="' . $styles . '"';
            }

            $cust_id = isset($id) ? ' id="' . $id . '"' : '';
            $extra_attr = isset($extra_att) ? ' ' . $extra_att . ' ' : '';
            $html_id = ' id="cs_' . sanitize_html_class($id) . '"';
            $html_name = ' name="cs_' . sanitize_html_class($id) . '"';
            if (isset($array) && $array == true) {
                $html_id = ' id="cs_' . sanitize_html_class($id) . $cs_rand_id . '"';
                $html_name = ' name="cs_' . sanitize_html_class($id) . '_array[]"';
            }
            $cs_required = '';
            if (isset($required) && $required == 'yes') {
                $cs_required = ' required="required"';
            }
            if (isset($cs_before) && $cs_before != '') {
                $cs_output .= '<div class="' . $cs_before . '">';
            }
            $cs_output .= parent::cs_form_textarea_render($field_params);
            $cs_output .= $this->cs_form_description($description);
            $cs_output .= '</div>';
            if (isset($echo) && $echo == true) {
                echo force_balance_tags($cs_output);
            } else {
                return $cs_output;
            }
        }

        /**
         * @ render Rich edito field
         */
        public function cs_form_editor_render($params = '') {
            global $post, $pagenow;
            extract($params);
            if ($pagenow == 'post.php') {
                $cs_value = get_post_meta($post->ID, 'cs_' . $id, true);
            } else {
                $cs_value = $std;
            }
            if (isset($cs_value) && $cs_value != '') {
                $value = $cs_value;
            } else {
                $value = $std;
            }
            $cs_output = '<div class="input-info">';
            $cs_output .= '<div class="row">';
            $cs_output .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
            ob_start();
            wp_editor($value, 'cs_' . sanitize_html_class($id), $settings = array('textarea_name' => 'cs_' . sanitize_html_class($id), 'editor_class' => 'text-input', 'teeny' => true, 'media_buttons' => false, 'textarea_rows' => 8, 'quicktags' => false));
            $cs_editor_contents = ob_get_clean();
            $cs_output .= $cs_editor_contents;
            $cs_output .= '</div>';
            $cs_output .= $this->cs_form_description($description);
            $cs_output .= '</div>';
            $cs_output .= '</div>';
            if (isset($return) && $return == true) {
                return force_balance_tags($cs_output);
            } else {
                echo force_balance_tags($cs_output);
            }
        }

        /**
         * @ render select field
         */
        public function cs_form_select_render($params = '') {
            global $post, $pagenow;
            extract($params);
            if (isset($std) && $std <> '') {
                $std = $std;
            } else {
                $std = '';
            }
            if (isset($id) && $id <> '') {
                $id = $id;
            } else {
                $id = '';
            }
            if (isset($extra_att) && $extra_att <> '') {
                $extra_att = $extra_att;
            } else {
                $extra_att = '';
            }
            $cs_onchange = '';
            if ($pagenow == 'post.php') {
                $cs_value = get_post_meta($post->ID, 'cs_' . $id, true);
            } else {
                $cs_value = $std;
            }
            if (isset($cs_value) && $cs_value != '') {
                $value = $cs_value;
            } else {
                $value = $std;
            }
            $cs_output = '';
            $cs_styles = '';
            if (isset($styles) && $styles != '') {
                $cs_styles = ' style="' . $styles . '"';
            }
            if (isset($description) && $description != '') {
                $description = $description;
            } else {
                $value = '';
            }
            $cs_rand_id = time();
            $html_wraper = ' id="wrapper_' . sanitize_html_class($id) . '"';
            $html_id = ' id="cs_' . sanitize_html_class($id) . '"';
            $html_name = ' name="cs_' . sanitize_html_class($id) . '"';
            if (isset($array) && $array == true) {
                $html_id = ' id="cs_' . sanitize_html_class($id) . $cs_rand_id . '"';
                $html_name = ' name="cs_' . sanitize_html_class($id) . '_array[]"';
                $html_wraper = ' id="wrapper_' . sanitize_html_class($id) . $cs_rand_id . '"';
            }
            $cs_display = '';
            if (isset($status) && $status == 'hide') {
                $cs_display = 'style=display:none';
            }
            if (isset($onclick) && $onclick != '') {
                $cs_onchange = 'onchange="javascript:' . $onclick . '(this.value, \'' . esc_js(admin_url('admin-ajax.php')) . '\')"';
            }
            $cs_required = '';
            if (isset($required) && $required == 'yes') {
                $cs_required = ' required="required"';
            }
            $cs_output .= parent::cs_form_select_render($field_params);
           if (isset($echo) && $echo == true) {
                echo force_balance_tags($cs_output);
            } else {
                return $cs_output;
            }
        }

        /**
         * @ render Multi Select field
         */
        public function cs_form_multiselect_render($params = '') {
            global $post, $pagenow;
            extract($params);
            $cs_output = '';
            $cs_styles = '';
            if (isset($styles) && $styles != '') {
                $cs_styles = ' style="' . $styles . '"';
            }

            $cust_id = isset($id) ? ' id="' . $id . '"' : '';
            $extra_attr = isset($extra_att) ? ' ' . $extra_att . ' ' : '';

            $cs_onchange = '';
            if ($pagenow == 'post.php') {
                $cs_value = get_post_meta($post->ID, 'cs_' . $id, true);
            } else {
                $cs_value = $std;
            }
            if (isset($cs_value) && $cs_value != '') {
                $value = $cs_value;
            } else {
                $value = $std;
            }
            $cs_rand_id = time();
            $html_wraper = ' id="wrapper_' . sanitize_html_class($id) . '"';
            $html_id = ' id="cs_' . sanitize_html_class($id) . '"';
            $html_name = ' name="cs_' . sanitize_html_class($id) . '[]"';
            $cs_display = '';
            if (isset($status) && $status == 'hide') {
                $cs_display = 'style=display:none';
            }
            if (isset($onclick) && $onclick != '') {
                $cs_onchange = 'onchange="javascript:' . $onclick . '(this.value, \'' . esc_js(admin_url('admin-ajax.php')) . '\')"';
            }
            if (!is_array($value)) {
                $value = array();
            }
            $cs_required = '';
            if (isset($required) && $required == 'yes') {
                $cs_required = ' required="required"';
            }
            $cs_output = '<ul class="form-elements"' . $html_wraper . ' ' . $cs_display . '>';
            $cs_output .= $this->cs_form_label($name);
            $cs_output .= '<li class="to-field multiple">';

            $cs_output .= parent::cs_form_multiselect_render($field_params);

            $cs_output .= $this->cs_form_description($description);
            $cs_output .= '</li>';
            $cs_output .= '</ul>';
            if (isset($echo) && $echo == true) {
                echo force_balance_tags($cs_output);
            } else {
                return $cs_output;
            }
        }

        /**
         * @ render Checkbox field
         */
        public function cs_form_checkbox_render($params = '') {
            global $post, $pagenow;
            extract($params);
            if ($pagenow == 'post.php') {
                $cs_value = get_post_meta($post->ID, 'cs_' . $id, true);
            } else {
                $cs_value = $std;
            }
            if (isset($cs_value) && $cs_value != '') {
                $value = $cs_value;
            } else {
                $value = $std;
            }
            $cs_output = '';
            $cs_styles = '';
            if (isset($styles) && $styles != '') {
                $cs_styles = ' style="' . $styles . '"';
            }

            $cust_id = isset($id) ? ' id="' . $id . '"' : '';
            $extra_attr = isset($extra_att) ? ' ' . $extra_att . ' ' : '';

            $cs_rand_id = time();
            $html_id = ' id="cs_' . sanitize_html_class($id) . '"';
            $btn_name = ' name="cs_' . sanitize_html_class($id) . '"';
            $html_name = ' name="cs_' . sanitize_html_class($id) . '"';
            if (isset($array) && $array == true) {
                $html_id = ' id="cs_' . sanitize_html_class($id) . $cs_rand_id . '"';
                $btn_name = ' name="cs_' . sanitize_html_class($id) . $cs_rand_id . '"';
                $html_name = ' name="cs_' . sanitize_html_class($id) . '_array[]"';
            }
            $checked = isset($value) && $value == 'on' ? ' checked="checked"' : '';
            $cs_output = '<ul class="form-elements">';
            $cs_output .= $this->cs_form_label($name);
            $cs_output .= '<li class="to-field has_input">';
            $cs_output .= '<label class="pbwp-checkbox cs-chekbox">';
            $cs_output .= parent::cs_form_checkbox_render($field_params);
            $cs_output .= '<span class="pbwp-box"></span>';
            $cs_output .= '</label>';
            $cs_output .= $this->cs_form_description($description);
            $cs_output .= '</li>';
            $cs_output .= '</ul>';
            if (isset($echo) && $echo == true) {
                echo force_balance_tags($cs_output);
            } else {
                return $cs_output;
            }
        }

        /**
         * @ render File Upload field
         */
        public function cs_media_url($params = '') {
            global $post, $pagenow;
            extract($params);
            $cs_value = get_post_meta($post->ID, 'cs_' . $id, true);
            if (isset($cs_value) && $cs_value != '') {
                $value = $cs_value;
            } else {
                $value = $std;
            }
            $cs_rand_id = time();
            if (isset($force_std) && $force_std == true) {
                $value = $std;
            }
            $html_id = ' id="cs_' . sanitize_html_class($id) . '"';
            $html_id_btn = ' id="cs_' . sanitize_html_class($id) . '_btn"';
            $html_name = ' name="cs_' . sanitize_html_class($id) . '"';
            if (isset($array) && $array == true) {
                $html_id = ' id="cs_' . sanitize_html_class($id) . $cs_rand_id . '"';
                $html_id = ' id="cs_' . sanitize_html_class($id) . $cs_rand_id . '_btn"';
                $html_name = ' name="cs_' . sanitize_html_class($id) . '_array[]"';
            }
            $cs_output = '<ul class="form-elements">';
            $cs_output .= $this->cs_form_label($name);
            $cs_output .= '<li class="to-field">';
            $cs_output .= '<div class="input-sec">';
            $cs_output .= parent::cs_media_url($field_params);
            $cs_output .= '</div>';
            $cs_output .= $this->cs_form_description($description);
            $cs_output .= '</li>';
            $cs_output .= '</ul>';
            if (isset($echo) && $echo == true) {
                echo force_balance_tags($cs_output);
            } else {
                return $cs_output;
            }
        }

        /**
         * @ render File Upload field
         */
        public function cs_form_fileupload_render($params = '') {
            global $post, $pagenow;
            extract($params);
            if ($pagenow == 'post.php') {
                $cs_value = get_post_meta($post->ID, 'cs_' . $id, true);
            } else {
                $cs_value = $std;
            }
            if (isset($cs_value) && $cs_value != '') {
                $value = $cs_value;
            } else {
                $value = $std;
            }
            if (isset($value) && $value != '') {
                $display = 'style=display:block';
            } else {
                $display = 'style=display:none';
            }
            $class = '';
            if (isset($value) && $classes != '') {
                $class = " " . $classes;
            }
            $cs_random_id = CS_FUNCTIONS()->cs_rand_id();
            $btn_name = ' name="cs_' . sanitize_html_class($id) . '"';
            $html_id = ' id="cs_' . sanitize_html_class($id) . '"';
            $html_name = ' name="cs_' . sanitize_html_class($id) . '"';
            if (isset($array) && $array == true) {
                $btn_name = ' name="cs_' . sanitize_html_class($id) . $cs_random_id . '"';
                $html_id = ' id="cs_' . sanitize_html_class($id) . $cs_random_id . '"';
                $html_name = ' name="cs_' . sanitize_html_class($id) . '_array[]"';
            }
            $cs_output = '<ul class="form-elements">';
            $cs_output .= $this->cs_form_label($name);
            $cs_output .= '<li class="to-field">';
            $cs_output .= '<div class="page-wrap" ' . $display . ' id="cs_' . sanitize_html_class($id) . '_box">';
            $cs_output .= '<div class="gal-active">';
            $cs_output .= '<div class="dragareamain" style="padding-bottom:0px;">';
            $cs_output .= '<ul id="gal-sortable">';
            $cs_output .= '<li class="ui-state-default" id="">';
            $cs_output .= '<div class="thumb-secs"> <img src="' . esc_url($value) . '" id="cs_' . sanitize_html_class($id) . '_img" width="100" alt="" />';
            $cs_output .= '<div class="gal-edit-opts"><a href="javascript:del_media(\'cs_' . sanitize_html_class($id) . '\')" class="delete"></a> </div>';
            $cs_output .= '</div>';
            $cs_output .= '</li>';
            $cs_output .= '</ul>';
            $cs_output .= '</div>';
            $cs_output .= '</div>';
            $cs_output .= '</div>';
            $cs_output .= parent::cs_form_fileupload_render($field_params);
            $cs_output .= '</li>';
            $cs_output .= '</ul>';
            if (isset($echo) && $echo == true) {
                echo force_balance_tags($cs_output);
            } else {
                return $cs_output;
            }
        }

        /**
         * @ render File Upload field
         */
        public function cs_form_cvupload_render($params = '') {
            global $post, $pagenow;
            extract($params);
            if ($pagenow == 'post.php') {
                $cs_value = get_post_meta($post->ID, 'cs_' . $id, true);
            } else {
                $cs_value = $std;
            }
            if (isset($cs_value) && $cs_value != '') {
                $value = $cs_value;
            } else {
                $value = $std;
            }
            if (isset($value) && $value != '') {
                $display = 'style=display:block';
            } else {
                $display = 'style=display:none';
            }
            $cs_random_id = CS_FUNCTIONS()->cs_rand_id();
            $btn_name = ' name="cs_' . sanitize_html_class($id) . '"';
            $html_id = ' id="cs_' . sanitize_html_class($id) . '"';
            $html_name = ' name="cs_' . sanitize_html_class($id) . '"';
            if (isset($array) && $array == true) {
                $btn_name = ' name="cs_' . sanitize_html_class($id) . $cs_random_id . '"';
                $html_id = ' id="cs_' . sanitize_html_class($id) . $cs_random_id . '"';
                $html_name = ' name="cs_' . sanitize_html_class($id) . '_array[]"';
            }
            $cs_output = '<div class="cs-img-detail resume-upload">';
            $cs_output = '<div class="upload-btn-div">';
            $cs_output .= '<div class="dragareamain" style="padding-bottom:0px;">';
            $cs_output .= parent::cs_form_hidden_render($field_params);
            $cs_output .= '<input' . $btn_name . 'type="button" class="cs-uploadMedia uplaod-btn" value="' . __('Browse', 'jobhunt') . '"/>';
            $cs_output .= '<div class="alert alert-dismissible user-resume" id="cs_' . sanitize_html_class($id) . '_img">';
            if (isset($value) and $value <> '') {
                $cs_output .= '<div>' . basename($value);
                $cs_output .= '<button aria-label="Close" data-dismiss="alert" class="close" type="button">';
                $cs_output .= '<span aria-hidden="true" class="cs-color">×</span>';
                $cs_output .= '</button>';
                $cs_output .= '<a href="javascript:cs_del_media(\'cs_' . sanitize_html_class($id) . '\')" class="delete"></a></div>';
            }
            $cs_output .= '</div>';
            $cs_output .= '</div>';
            $cs_output .= '</div>';
            if (isset($echo) && $echo == true) {
                echo force_balance_tags($cs_output);
            } else {
                return $cs_output;
            }
        }

        /**
         * @ render Random String
         */
        public function cs_generate_random_string($length = 3) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, strlen($characters) - 1)];
            }
            return $randomString;
        }

    }

    global $cs_html_fields_frontend;
    $cs_html_fields_frontend = new cs_html_fields_frontend();
}