<?php
/*
 *
 * Candidate Top view Searchbox
 * 
 *
 */
global $cs_plugin_options;
?>
<div class="main-search inner-search">
    <?php if (isset($atts['cs_employer_searchbox_title_top']) && $atts['cs_employer_searchbox_title_top'] != '') { ?>
        <h4><?php echo esc_html($atts['cs_employer_searchbox_title_top']); ?></h4>
    <?php } ?>
    <form class="search-area" class="top-view-srch-form" data-ajaxurl="<?php echo esc_js(admin_url('admin-ajax.php')); ?>">
        <!-- extra query string -->
        <?php
        // parse query string and create hidden fileds
        if (isset($qrystr)) {
            $final_query_str = str_replace("?", "", $qrystr);
            $final_query_str = cs_remove_qrystr_extra_var($final_query_str, 'cs_candidatename', 'no');
            $final_query_str = cs_remove_qrystr_extra_var($final_query_str, 'radius', 'no');
            $final_query_str = cs_remove_qrystr_extra_var($final_query_str, 'location', 'no');
            $final_query_str = str_replace("?", "", $final_query_str);
            parse_str($final_query_str, $_query_str_arr);
            foreach ($_query_str_arr as $_query_str_single_var => $_query_str_single_value) {
                if (is_array($_query_str_single_value)) {
                    $_query_str_single_value = array_unique($_query_str_single_value);
                    foreach ($_query_str_single_value as $_query_str_single_value_arr) {

                        $cs_opt_array = array(
                            'std' => $_query_str_single_value_arr,
                            'id' => '',
                            'before' => '',
                            'after' => '',
                            'classes' => '',
                            'extra_atr' => '',
                            'cust_id' => '',
                            'cust_name' => $_query_str_single_var . '[]',
                            'required' => false
                        );
                        $cs_form_fields2->cs_form_hidden_render($cs_opt_array);
                    }
                } else {

                    $cs_opt_array = array(
                        'std' => $_query_str_single_value,
                        'id' => '',
                        'before' => '',
                        'after' => '',
                        'classes' => '',
                        'extra_atr' => '',
                        'cust_id' => '',
                        'cust_name' => $_query_str_single_var,
                        'required' => false
                    );
                    $cs_form_fields2->cs_form_hidden_render($cs_opt_array);
                }
            }
        }
        ?>
        <!-- end extra query string -->
        <div class="col-lg-3 col-md-3">
            <div class="search-input"> 
                <?php
                $cs_opt_array = array(
                    'std' => isset($cs_candidatename) ? esc_attr($cs_candidatename) : '',
                    'id' => '',
                    'before' => '',
                    'after' => '',
                    'extra_atr' => 'placeholder="' . __('Enter any keyword', 'jobhunt') . '"',
                    'cust_id' => 'cs_candidatename'.rand(),
                    'cust_name' => 'cs_candidatename',
                    'required' => false
                );

                $cs_form_fields2->cs_form_text_render($cs_opt_array);
                ?>
            </div>
        </div>
        <?php
        if (isset($cs_plugin_options['cs_jobhunt_search_location']) && $cs_plugin_options['cs_jobhunt_search_location'] == 'on') {
            ?>
            <div class="col-lg-3 col-md-3">
                <div class="select-location">
                    <?php
                    $cs_radius = '';
                    if (isset($_GET['radius']) && $_GET['radius'] > 0) {

                        $cs_radius = $_GET['radius'];
                    }

                    $cs_locatin_cust = cs_location_convert();

                    $cs_geo_location = isset($cs_plugin_options['cs_geo_location']) ? $cs_plugin_options['cs_geo_location'] : '';
                    $cookie_geo_loc = isset($_COOKIE['cs_geo_loc']) ? $_COOKIE['cs_geo_loc'] : '';
                    $cookie_geo_switch = isset($_COOKIE['cs_geo_switch']) ? $_COOKIE['cs_geo_switch'] : '';
                    if ($cs_geo_location == 'on' && $cookie_geo_switch == 'on' && $cookie_geo_loc != '') {
                        $cs_locatin_cust = $cookie_geo_loc;
                    }
                    if (isset($_GET['location'])) {
                        $cs_locatin_cust = cs_location_convert();
                    }
                    $cs_loc_name = '';
                    $cs_select_display = 'block';
                    $cs_input_display = 'none';
                    $cs_undo_display = 'none';
                    if ($cs_locatin_cust != '') {
                        $cs_loc_name = ' name="location"';
                        $cs_select_display = 'none';
                        $cs_input_display = 'block';
                        $cs_undo_display = 'block';
                    }

                    $cs_radius_switch = isset($cs_plugin_options['cs_radius_switch']) ? $cs_plugin_options['cs_radius_switch'] : '';
                    $min_value = 0;
                    $max_value = '';
                    if ($cs_radius_switch == 'on') {
                        $cs_default_radius = isset($cs_plugin_options['cs_default_radius']) ? $cs_plugin_options['cs_default_radius'] : '';
                        $cs_radius_measure = isset($cs_plugin_options['cs_radius_measure']) ? $cs_plugin_options['cs_radius_measure'] : '';
                        $cs_radius_measure = $cs_radius_measure == 'km' ? __('KM', 'jobhunt') : __('Miles', 'jobhunt');

                        $min_value = isset($cs_plugin_options['cs_radius_min']) ? $cs_plugin_options['cs_radius_min'] : '';

                        $max_value = isset($cs_plugin_options['cs_radius_max']) ? $cs_plugin_options['cs_radius_max'] : '';

                        $radius_step = isset($cs_plugin_options['cs_radius_step']) ? $cs_plugin_options['cs_radius_step'] : '';

                        // from submitted value
                        $cs_radius = preg_replace("/[^0-9,.]/", "", $cs_radius);
                        if ($cs_radius == '') {
                            $cs_radius = $cs_default_radius;
                        }
                    }
                    ?>
                    <div id="cs-top-select-holder" class="select-location" data-locationadminurl="<?php echo esc_url(admin_url("admin-ajax.php")) ?>">
                        <?php
                        if ($cs_plugin_options['cs_google_autocomplete_enable'] == 'on') {
                            cs_get_custom_locationswith_google_auto('<div id="cs-top-select-holder" class="search-country" style="display:' . cs_allow_special_char($cs_select_display) . '"><div class="select-holder">', '</div><span>' . __('Please select your desired location', 'jobhunt') . '</span> </div>', false, true);
                        } else {
                            cs_get_custom_locations('<div id="cs-top-select-holder" class="search-country" style="display:' . cs_allow_special_char($cs_select_display) . '"><div class="select-holder">', '</div><span>' . __('Please select your desired location', 'jobhunt') . '</span> </div>');
                        }
                        $list_rand = rand(0, 499999999);
                        ?>
                        <a id="location_redius_popup<?php echo absint($list_rand); ?>" href="javascript:void(0);" class="location-btn pop"><i class="icon-target3"></i></a>
                        <?php
                        if ($cs_radius_switch == 'on') {
                            ?>
                            <div id="popup<?php echo absint($list_rand); ?>" style="display:none;"  class="select-popup">
                                <a class="cs-location-close-popup" id="cs_close<?php echo absint($list_rand); ?>"><i class="cs-color icon-times"></i></a>
                                <p><?php _e("Show With in", "jobhunt"); ?></p>
                                <input id="ex6<?php echo absint($list_rand); ?>" name="radius" type="text" data-slider-min="<?php echo absint($min_value); ?>" data-slider-max="<?php echo absint($max_value); ?>" data-slider-step="<?php echo absint($radius_step); ?>" data-slider-value="<?php echo absint($cs_radius); ?>"/>
                                <span id="ex6CurrentSliderValLabel_top"><span id="ex6SliderVal<?php echo absint($list_rand); ?>"><?php echo absint($cs_radius); ?></span><?php echo esc_html($cs_radius_measure); ?></span>
                                <?php
                                if ($cs_geo_location == 'on') {
                                    ?>
                                    <p class="my-location"><?php _e("of", "jobhunt"); ?> <i class="cs-color icon-location-arrow"></i><a class="cs-color" onclick="cs_get_location(this)"><?php _e("My location", "jobhunt"); ?></a></p>
                                    <?php
                                }
                                ?>
                            </div>
                            <?php
                        }
                        ?>
                    </div>

                    <?php
                    $cs_form_fields2->cs_form_text_render(
                            array(
                                'id' => '',
                                'classes' => 'cs-geo-location form-control txt-field geo-search-location',
                                'cust_name' => $cs_loc_name,
                                'extra_atr' => ' onchange="this.form.submit()" style="display:' . cs_allow_special_char($cs_input_display) . ';" ' . $cs_loc_name,
                                'std' => $cs_locatin_cust,
                            )
                    );
                    ?>

                    <div class="cs-undo-select" style="display:<?php echo cs_allow_special_char($cs_undo_display) ?>;">
                        <i class="icon-times"></i>
                    </div>
                    <script type="text/javascript">
                        jQuery(document).ready(function () {
                            jQuery("#ex6<?php echo absint($list_rand); ?>").slider();
                            jQuery("#ex6<?php echo absint($list_rand); ?>").on("slide", function (slideEvt) {
                                jQuery("#ex6SliderVal<?php echo absint($list_rand); ?>").text(slideEvt.value);
                            });
                            jQuery('#location_redius_popup<?php echo absint($list_rand); ?>').click(function (event) {
                                event.preventDefault();
                                jQuery("#popup<?php echo absint($list_rand); ?>").css('display', 'block') //to show
                                return false;
                            });

                            jQuery('#cs_close<?php echo absint($list_rand); ?>').click(function () {
                                jQuery("#popup<?php echo absint($list_rand); ?>").css('display', 'none') //to show
                                return false;
                            });
                        });
                    </script>
                </div>
            </div>
            <?php
        }
        ?>
        <div class="col-lg-4 col-md-4">
            <div class="search-category">
                <div class="select-holder">
                    <select name="specialisms" id="specialisms" data-placeholder="<?php echo __('All specialisms','jobhunt'); ?>" class="chosen-select">
                        <option value=""><?php _e('All specialisms', 'jobhunt') ?></option>
                        <?php
                        $specialisms_args = array(
                            'orderby' => 'name',
                            'order' => 'ASC',
                            'number' => '',
                            'fields' => 'all',
                            'slug' => '',
							'hide_empty' => false,
                            'parent' => '0',
                        );

                        // get all job types
                        $all_specialisms = get_terms('specialisms', $specialisms_args);
                        if ($all_specialisms != '') {
                            foreach ($all_specialisms as $specialismsitem) {
                                if (is_array($specialisms) && in_array($specialismsitem->slug, $specialisms))
                                    echo '<option value="' . $specialismsitem->slug . '" selected="selected">' . $specialismsitem->name . ' </option>';
                                else
                                    echo '<option value="' . $specialismsitem->slug . '">' . $specialismsitem->name . ' </option>';
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-2">
            <div class="search-btn">
                <input type="submit" class="cs-bgcolor" value="<?php _e("Search Resume", "jobhunt"); ?>">
            </div>
        </div>
    </form>
</div>
