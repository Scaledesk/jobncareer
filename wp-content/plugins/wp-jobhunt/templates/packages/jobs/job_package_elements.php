<?php
/*
 *
 * @Shortcode Name : Job Package
 * @retrun
 */
$cs_jh_scodes['job_package'] = array(
    'title' => __('Job Package', 'jobhunt'),
    'name' => 'job_package',
    'icon' => 'icon-table',
    'categories' => 'loops misc',
    'attributes' => array(
        'job_package_title' => '',
        'job_package_style' => '',
        'job_pkges' => '',
        'job_packages_columns' => '',
    )
);
/*
 * Start Function how to Create job package
 *
 */
if (!function_exists('jobcareer_pb_job_package')) {

    function jobcareer_pb_job_package($die = 0) {
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
            $PREFIX = 'cs_job_package';
            $parseObject = new ShortcodeParse();
            $shortcode_element_id = $_POST['shortcode_element_id'];
            $shortcode_str = stripslashes($shortcode_element_id);
            $output = $parseObject->cs_shortcodes($output, $shortcode_str, true, $PREFIX);
        }
        $defaults = array(
            'job_package_title' => '',
            'job_package_style' => '',
            'job_pkges' => '',
            'job_packages_columns' => '',
        );

        if (isset($output['0']['atts']))
            $atts = $output['0']['atts'];
        else
            $atts = array();
        if (isset($output['0']['content']))
            $job_package_content = $output['0']['content'];
        else
            $job_package_content = '';
        $job_package_element_size = '100';
        foreach ($defaults as $key => $values) {
            if (isset($atts[$key]))
                $$key = $atts[$key];
            else
                $$key = $values;
        }
        $name = 'jobcareer_pb_job_package';
        $coloumn_class = 'column_' . $job_package_element_size;
        if (isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode') {
            $shortcode_element = 'shortcode_element_class';
            $shortcode_view = 'cs-pbwp-shortcode';
            $filter_element = 'ajax-drag';
            $coloumn_class = '';
        }

        $cs_rand_id = rand(13441324, 93441324);
        ?>
        <div id="<?php echo esc_attr($name . $cs_counter) ?>_del" class="column  parentdelete <?php echo esc_attr($coloumn_class); ?> <?php echo esc_attr($shortcode_view); ?>" item="job_package" data="<?php echo cs_element_size_data_array_index($job_package_element_size) ?>" >
            <?php cs_element_setting($name, $cs_counter, $job_package_element_size, '', 'ellipsis-h', $type = ''); ?>
            <div class="cs-wrapp-class-<?php echo intval($cs_counter) ?> <?php echo esc_attr($shortcode_element); ?>" id="<?php echo esc_attr($name . $cs_counter) ?>" data-shortcode-template="[cs_job_package {{attributes}}]{{content}}[/cs_job_package]" style="display: none;">
                <div class="cs-heading-area">
                    <h5><?php _e('JC: JOB PACKAGE OPTION', 'jobhunt'); ?></h5>
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
                            'hint_text' => __('Add Job packages title here.', 'jobhunt'),
                            'echo' => true,
                            'field_params' => array(
                                'std' => $job_package_title,
                                'id' => 'job_package_title',
                                'cust_name' => 'job_package_title[]',
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
                                'std' => esc_attr($job_packages_columns),
                                'cust_id' => 'job_packages_columns',
                                'cust_name' => 'job_packages_columns[]',
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
                            'name' => __('Style', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => __('Choose packages style with this dropdown.', 'jobhunt'),
                            'echo' => true,
                            'field_params' => array(
                                'std' => $job_package_style,
                                'id' => 'job_package_style',
                                'classes' => 'dropdown chosen-select',
                                'cust_name' => 'job_package_style[]',
                                'options' => array(
                                    'advance' => __('Box', 'jobhunt'),
                                    'basic' => __('Basic', 'jobhunt'),
                                    'classic' => __('Classic', 'jobhunt'),
                                    'fancy' => __('Fancy', 'jobhunt'),
                                    'modern' => __('Modern', 'jobhunt'),
                                    'simple' => __('Simple', 'jobhunt'),
                                ),
                                'return' => true,
                            ),
                        );

                        $cs_html_fields->cs_select_field($cs_opt_array);

                        $cs_plugin_options = get_option('cs_plugin_options');
                        $cs_packages_options = isset($cs_plugin_options['cs_packages_options']) ? $cs_plugin_options['cs_packages_options'] : '';
                        $job_pkges = explode(',', $job_pkges);
                        if (!is_array($job_pkges)) {
                            $job_pkges = array();
                        }

                        if (is_array($cs_packages_options) && sizeof($cs_packages_options) > 0) {

                            $cs_pkgs_options = '';
                            foreach ($cs_packages_options as $package_key => $package) {
                                if (isset($package_key) && $package_key <> '') {
                                    $package_id = isset($package['package_id']) ? $package['package_id'] : '';
                                    $package_title = isset($package['package_title']) ? $package['package_title'] : '';
                                    $cs_selected = in_array($package_id, $job_pkges) ? ' selected="selected"' : '';

                                    $cs_pkgs_options .= '<option' . $cs_selected . ' value="' . absint($package_id) . '">' . CS_FUNCTIONS()->cs_special_chars($package_title) . '</option>' . "\n";
                                }
                            }

                            $cs_opt_array = array(
                                'name' => __('Packages', 'jobhunt'),
                                'desc' => '',
                                'hint_text' => __('Select packages which do you want show. If you don’t have any package create new from Dashboard >> Jobs >> Settings >> Packages.', 'jobhunt'),
                                'multi' => true,
                                'echo' => true,
                                'field_params' => array(
                                    'std' => $job_pkges,
                                    'id' => 'job_pkges',
                                    'cust_name' => 'job_pkges[' . $cs_rand_id . '][]',
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
                                    'std' => 'job_package',
                                    'cust_id' => "",
                                    'cust_name' => "cs_orderby[]",
                                );

                                $cs_form_fields2->cs_form_hidden_render($cs_opt_array);
                                $cs_opt_array = array(
                                    'id' => '',
                                    'std' => absint($cs_rand_id),
                                    'cust_id' => "",
                                    'cust_name' => "cs_pkg_id[]",
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
             * modern selection box function
             */
            jQuery(document).ready(function ($) {
                chosen_selectionbox();
            });
        </script> 
        <?php
        if ($die <> 1)
            die();
    }

    add_action('wp_ajax_jobcareer_pb_job_package', 'jobcareer_pb_job_package');
}