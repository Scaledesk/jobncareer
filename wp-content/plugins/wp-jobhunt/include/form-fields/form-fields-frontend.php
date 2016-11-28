<?php

/**
 * File Type: Form Fields
 */
if (!class_exists('cs_form_fields_frontend')) {

    class cs_form_fields_frontend {

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

        /* ----------------------------------------------------------------------
         * @ render description
         * --------------------------------------------------------------------- */

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

        /* ----------------------------------------------------------------------
         * @ render Headings
         * --------------------------------------------------------------------- */

        public function cs_heading_render($params = '') {
            global $post;
            extract($params);
            $cs_output = '<div class="theme-help" id="' . sanitize_html_class($id) . '">
                            <h4 style="padding-bottom:0px;">' . esc_attr($name) . '</h4>
                            <div class="clear"></div>
                          </div>';
            echo force_balance_tags($cs_output);
        }

        /* ----------------------------------------------------------------------
         * @ render text field
         * --------------------------------------------------------------------- */

        public function cs_form_text_render($params = '') {
            global $post, $pagenow;
            extract($params);
            $cs_rand_value = '';
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
            if (isset($no_border) && $no_border == true) {
                $no_border = ' noborder';
            } else {
                $no_border = '';
            }
            if (isset($force_std) && $force_std == true) {
                $value = $std;
            }
            $cs_rand_id = time();
            $html_id = ' id="cs_' . sanitize_html_class($id) . '"';
            $html_name = ' name="cs_' . sanitize_html_class($id) . '"';
            if (isset($array) && $array == true) {
                $cs_rand_value = rand(0, 9999997878);
                $html_name = ' name="cs_' . sanitize_html_class($id) . '_array[]"';
            }
            // Disbaled Field
            $cs_visibilty = '';
            if (isset($active) && $active == 'in-active') {
                $cs_visibilty = 'readonly="readonly"';
            }
            $cs_required = '';
            if (isset($required) && $required == 'yes') {
                $cs_required = ' required="required"';
            }
            $cs_icon = '';
            $cs_icon = (isset($icon) and $icon <> '') ? '<i class="' . $icon . '"></i>' : '';
            //Calculate Remainings
            $cs_output = '<div class="' . $classes . '">';
            $html_id = ' id="cs_' . sanitize_html_class($id) . $cs_rand_value . '"';
            $cs_output .= $cs_icon;
            $cs_output .= '<input type="text" ' . $cs_visibilty . $cs_required . ' class="cs-form-text cs-input form-control" ' . $html_id . $html_name . '  value="' . sanitize_text_field($value) . '" placeholder="' . $name . '" />';
            $cs_output .= '</div>';
            if (isset($return) && $return == true) {
                return force_balance_tags($cs_output);
            } else {
                echo force_balance_tags($cs_output);
            }
        }

        /* ----------------------------------------------------------------------
         * @ render Radio field
         * --------------------------------------------------------------------- */

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
            $cs_output .= '<input type="radio" class="cs-form-text cs-input " name="cs_' . sanitize_html_class($id) . '" id="cs_' . sanitize_html_class($id) . '" value="' . sanitize_text_field($value) . '" />';
            $cs_output .= '</div>';
            $cs_output .= $this->cs_form_description($description);
            $cs_output .= '</li>';
            $cs_output .= '</ul>';
            echo force_balance_tags($cs_output);
        }

        /* ----------------------------------------------------------------------
         * @ render text field
         * --------------------------------------------------------------------- */

        public function cs_form_hidden_render($params = '') {
            global $post, $pagenow;
            extract($params);
            $cs_rand_id = time();
            $html_name = ' name="cs_' . sanitize_html_class($id) . '"';
            if (isset($array) && $array == true) {
                $html_name = ' name="cs_' . sanitize_html_class($id) . '_array[]"';
            }
            $cs_output = '<input type="hidden" id="cs_' . sanitize_text_field($id) . '" class="cs-form-text cs-input"' . $html_name . ' value="' . sanitize_text_field($std) . '" />';
            if (isset($return) && $return == 'true') {
                return force_balance_tags($cs_output);
            } else {
                echo force_balance_tags($cs_output);
            }
        }

        /* ----------------------------------------------------------------------
         * @ render Date field
         * --------------------------------------------------------------------- */

        public function cs_form_date_render($params = '') {
            global $post, $pagenow;
            extract($params);
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
            $cs_output .= '<input type="text"' . $cs_required . ' class="cs-form-text cs-input form-control" ' . $html_id . $html_name . '  value="' . sanitize_text_field($value) . '" placeholder="' . $name . '" />';
            $cs_output .= $this->cs_form_description($description);
            $cs_output .= '</div>';
            if (isset($return) && $return == true) {
                return force_balance_tags($cs_output);
            } else {
                echo force_balance_tags($cs_output);
            }
        }

        /* ----------------------------------------------------------------------
         * @ render Textarea field
         * --------------------------------------------------------------------- */

        public function cs_form_textarea_render($params = '') {
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
            $cs_rand_id = time();
            if (isset($force_std) && $force_std == true) {
                $value = $std;
            }
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
            $cs_output = '<div class="' . $classes . '">';
            $cs_output .= ' <textarea' . $cs_required . ' rows="5" cols="30"' . $html_id . $html_name . ' placeholder="' . $name . '">' . sanitize_text_field($value) . '</textarea>';
            $cs_output .= $this->cs_form_description($description);
            $cs_output .= '</div>';
            if (isset($return) && $return == true) {
                return force_balance_tags($cs_output);
            } else {
                echo force_balance_tags($cs_output);
            }
        }

        /* ----------------------------------------------------------------------
         * @ render Rich edito field
         * --------------------------------------------------------------------- */

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
            $cs_output .= '<div class="col-md-12">';
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

        /* ----------------------------------------------------------------------
         * @ render select field
         * --------------------------------------------------------------------- */

        public function cs_form_select_render($params = '') {
            global $post, $pagenow;
            extract($params);
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
            $cs_output .= $this->cs_form_label($name);
                 $cs_output .= '<select' . $html_id . $html_name . ' ' . $cs_onchange . $cs_required . ' data-placeholder="' . __("Please Select", "jobhunt") . '" class="chosen-select">';
            foreach ($options as $key => $option) {
                $cs_output .= '<option ' . selected($key, $value, false) . 'value="' . $key . '">' . $option . '</option>';
            }
            $cs_output .= '</select>';
            $cs_output .= $holder;
            $cs_output .= $this->cs_form_description($description);
               if (isset($return) && $return == true) {
                return force_balance_tags($cs_output);
            } else {
                echo force_balance_tags($cs_output);
            }
        }

        /* ----------------------------------------------------------------------
         * @ render Multi Select field
         * --------------------------------------------------------------------- */

        public function cs_form_multiselect_render($params = '') {
            global $post, $pagenow;
            extract($params);
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
            $cs_output .= '<select' . $cs_required . ' class="multiple" multiple="multiple" ' . $html_id . $html_name . ' ' . $cs_onchange . ' style="height:110px !important;"data-placeholder="' . __("Please Select", "jobhunt") . '" class="chosen-select">';
            foreach ($options as $key => $option) {
                $selected = '';
                if (in_array($key, $value)) {
                    $selected = 'selected="selected"';
                }
                $cs_output .= '<option ' . $selected . 'value="' . $key . '">' . $option . '</option>';
            }
            $cs_output .= '</select>';
            $cs_output .= $this->cs_form_description($description);
            $cs_output .= '</li>';
            $cs_output .= '</ul>';
            if (isset($return) && $return == true) {
                return force_balance_tags($cs_output);
            } else {
                echo force_balance_tags($cs_output);
            }
        }

        /* ----------------------------------------------------------------------
         * @ render Checkbox field
         * --------------------------------------------------------------------- */

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
            $cs_output .= '<input type="hidden"' . $html_id . $html_name . ' value="' . sanitize_text_field($std) . '" />';
            $cs_output .= '<input type="checkbox" class="myClass"' . $btn_name . $checked . ' />';
            $cs_output .= '<span class="pbwp-box"></span>';
            $cs_output .= '</label>';
            $cs_output .= $this->cs_form_description($description);
            $cs_output .= '</li>';
            $cs_output .= '</ul>';
            if (isset($return) && $return == true) {
                return force_balance_tags($cs_output);
            } else {
                echo force_balance_tags($cs_output);
            }
        }

        /* ----------------------------------------------------------------------
         * @ render File Upload field
         * --------------------------------------------------------------------- */

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
            $cs_output .= '<input type="text" class="cs-form-text cs-input" ' . $html_id . $html_name . ' value="' . sanitize_text_field($value) . '" />';
            $cs_output .= '<label class="cs-browse">';
            $cs_output .= '<input type="button" ' . $html_id_btn . $html_name . ' class="uploadfile left" value="' . __('Browse', 'jobhunt') . '"/>';
            $cs_output .= '</label>';
            $cs_output .= '</div>';
            $cs_output .= $this->cs_form_description($description);
            $cs_output .= '</li>';
            $cs_output .= '</ul>';
            if (isset($return) && $return == true) {
                return force_balance_tags($cs_output);
            } else {
                echo force_balance_tags($cs_output);
            }
        }

        /* ----------------------------------------------------------------------
         * @ render File Upload field
         * --------------------------------------------------------------------- */

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
            $cs_output .= '<input' . $html_id . $html_name . 'type="hidden" class="" value="' . $value . '"/>';
            $cs_output .= '<label class="browse-icon"><input' . $btn_name . 'type="button" class="cs-uploadMedia left ' . $class . '" value="' . __('Browse', 'jobhunt') . '" /></label>';
            $cs_output .= '</li>';
            $cs_output .= '</ul>';
            if (isset($return) && $return == true) {
                return force_balance_tags($cs_output);
            } else {
                echo force_balance_tags($cs_output);
            }
        }

        /* ----------------------------------------------------------------------
         * @ render File Upload field
         * --------------------------------------------------------------------- */

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
            $cs_output .= '<input' . $html_id . $html_name . 'type="hidden" class="" value="' . $value . '"/>';
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
            if (isset($return) && $return == true) {
                return force_balance_tags($cs_output);
            } else {
                echo force_balance_tags($cs_output);
            }
        }

        /* ----------------------------------------------------------------------
         * @ render Random String
         * --------------------------------------------------------------------- */

        public function cs_generate_random_string($length = 3) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, strlen($characters) - 1)];
            }
            return $randomString;
        }

    }

    global $cs_form_fields_frontend;
    $cs_form_fields_frontend = new cs_form_fields_frontend();
}