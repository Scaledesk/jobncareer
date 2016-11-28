<?php
/*
 *
 * @Shortcode Name : Cv Package
 * @retrun
 *
 */
$cs_jh_scodes['cv_package'] = array(
    'title' => __('CV Package', 'jobhunt'),
    'name' => 'cv_package',
    'icon' => 'icon-table',
    'categories' => 'loops misc',
    'attributes' => array(
        'cv_package_title' => '',
        'cv_pkges' => '',
    )
);
/*
 *
 * Start Function how to shortcode of CV Packages
 *
 */
if (!function_exists('jobcareer_pb_cv_package')) {

    function jobcareer_pb_cv_package($die = 0) {
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
            $PREFIX = 'cs_cv_package';
            $parseObject = new ShortcodeParse();
            $shortcode_element_id = $_POST['shortcode_element_id'];
            $shortcode_str = stripslashes($shortcode_element_id);
            $output = $parseObject->cs_shortcodes($output, $shortcode_str, true, $PREFIX);
        }
        $defaults = array(
            'cv_package_title' => '',
            'cv_pkges' => '',
            'cv_columns'=>'',
        );
        if (isset($output['0']['atts']))
            $atts = $output['0']['atts'];
        else
            $atts = array();
        if (isset($output['0']['content']))
            $cv_package_content = $output['0']['content'];
        else
            $cv_package_content = '';
        $cv_package_element_size = '100';
        foreach ($defaults as $key => $values) {
            if (isset($atts[$key]))
                $$key = $atts[$key];
            else
                $$key = $values;
        }
        $name = 'jobcareer_pb_cv_package';
        $coloumn_class = 'column_' . $cv_package_element_size;
        if (isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode') {
            $shortcode_element = 'shortcode_element_class';
            $shortcode_view = 'cs-pbwp-shortcode';
            $filter_element = 'ajax-drag';
            $coloumn_class = '';
        }
        $cs_rand_id = rand(13441324, 93441324);
        ?>
        <div id="<?php echo esc_attr($name . $cs_counter) ?>_del" class="column  parentdelete <?php echo esc_attr($coloumn_class); ?> <?php echo esc_attr($shortcode_view); ?>" item="cv_package" data="<?php echo cs_element_size_data_array_index($cv_package_element_size) ?>" >
            <?php cs_element_setting($name, $cs_counter, $cv_package_element_size, '', 'ellipsis-h', $type = ''); ?>
            <div class="cs-wrapp-class-<?php echo intval($cs_counter) ?> <?php echo esc_attr($shortcode_element); ?>" id="<?php echo esc_attr($name . $cs_counter) ?>" data-shortcode-template="[cs_cv_package {{attributes}}]{{content}}[/cs_cv_package]" style="display: none;">
                <div class="cs-heading-area">
                    <h5><?php _e('JC: CV Package Option', 'jobhunt'); ?></h5>
                    <a href="javascript:removeoverlay('<?php echo esc_js($name . $cs_counter) ?>','<?php echo esc_js($filter_element); ?>')" class="cs-btnclose"><i class="icon-times"></i></a> </div>
                <div class="cs-pbwp-content">
                    <div class="cs-wrapp-clone cs-shortcode-wrapp">

                        <?php
                         if (isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode') {
                                    jobcareer_shortcode_element_size();
                                }
                        $cs_opt_array = array(
                            'name' => __('Element Title', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => __('Add CV packages element title here.', 'jobhunt'),
                            'echo' => true,
                            'field_params' => array(
                                'std' => $cv_package_title,
                                'id' => 'cv_package_title',
                                'cust_name' => 'cv_package_title[]',
                                'return' => true,
                            ),
                        );

                        $cs_html_fields->cs_text_field($cs_opt_array);
$cs_opt_array = array(
                            'name' => __('Columns', 'jobhunt'),
                            'desc' => '',
                            'echo' => true,
                            'hint_text' => __("Select column for display view", "jobhunt"),
                            'field_params' => array(
                                'std' => esc_attr($cv_columns),
                                'cust_id' => 'cv_columns',
                                'cust_name' => 'cv_columns[]',
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
                        $cs_plugin_options = get_option('cs_plugin_options');
                        $cs_cv_pkgs_options = isset($cs_plugin_options['cs_cv_pkgs_options']) ? $cs_plugin_options['cs_cv_pkgs_options'] : '';
                        $cv_pkges = explode(',', $cv_pkges);
                        if (!is_array($cv_pkges)) {
                            $cv_pkges = array();
                        }
                        if (is_array($cs_cv_pkgs_options) && sizeof($cs_cv_pkgs_options) > 0) {

                            $cs_pkgs_options = '';
                            foreach ($cs_cv_pkgs_options as $cv_pkg_key => $cv_pkg) {
                                if (isset($cv_pkg_key) && $cv_pkg_key <> '') {
                                    $cv_pkg_id = isset($cv_pkg['cv_pkg_id']) ? $cv_pkg['cv_pkg_id'] : '';
                                    $cv_pkg_title = isset($cv_pkg['cv_pkg_title']) ? $cv_pkg['cv_pkg_title'] : '';
                                    $cs_selected = in_array($cv_pkg_id, $cv_pkges) ? ' selected="selected"' : '';

                                    $cs_pkgs_options .= '<option' . $cs_selected . ' value="' . absint($cv_pkg_id) . '">' . CS_FUNCTIONS()->cs_special_chars($cv_pkg_title) . '</option>' . "\n";
                                }
                            }

                            $cs_opt_array = array(
                                'name' => __('Packages', 'jobhunt'),
                                'desc' => '',
                                'hint_text' => __('Select CV packages here. If you don’t have any CV packages create from your Dashboard >> Jobs >> Settings >> Packages >> CV Search Add new.', 'jobhunt'),
                                'multi' => true,
                                'echo' => true,
                                'field_params' => array(
                                    'std' => $cv_pkges,
                                    'id' => 'cv_pkges',
                                    'cust_name' => 'cv_pkges[' . $cs_rand_id . '][]',
                                    'options_markup' => true,
                                    'options' => $cs_pkgs_options,
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
                                    'std' => 'cv_package',
                                    'cust_id' => "",
                                    'cust_name' => "cs_orderby[]",
                                );

                                $cs_form_fields2->cs_form_hidden_render($cs_opt_array);
                                $cs_opt_array = array(
                                    'id' => '',
                                    'std' => absint($cs_rand_id),
                                    'cust_id' => "",
                                    'cust_name' => "cs_cv_pkg_id[]",
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
        </script>
        <?php
        if ($die <> 1)
            die();
    }

    add_action('wp_ajax_jobcareer_pb_cv_package', 'jobcareer_pb_cv_package');
}
/*
 *
 * End Function how to shortcode of CV Packages
 *
 */