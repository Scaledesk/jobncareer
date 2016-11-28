<?php
/*
 *
 * Start Function how to manage of page of candidate
 *
 */

if (!function_exists('jobcareer_pb_candidate')) {

    function jobcareer_pb_candidate($die = 0) {
        global $cs_node, $cs_html_fields, $post, $cs_plugin_options;
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
            $PREFIX = 'cs_candidate';
            $parseObject = new ShortcodeParse();
            $shortcode_element_id = $_POST['shortcode_element_id'];
            $shortcode_str = stripslashes($shortcode_element_id);
            $output = $parseObject->cs_shortcodes($output, $shortcode_str, true, $PREFIX);
        }
        $cs_loc_latitude = isset($cs_plugin_options['cs_post_loc_latitude']) ? $cs_plugin_options['cs_post_loc_latitude'] : '';
        $cs_loc_longitude = isset($cs_plugin_options['cs_post_loc_longitude']) ? $cs_plugin_options['cs_post_loc_longitude'] : '';
        $cs_map_zoom_level = isset($cs_plugin_options['cs_map_zoom_level']) ? $cs_plugin_options['cs_map_zoom_level'] : '';

        $defaults = array('column_size' => '1/1', 'cs_candidate_title' => '', 'cs_candidate_map' => '', 'cs_candidate_map_lat' => $cs_loc_latitude, 'cs_candidate_map_long' => $cs_loc_longitude, 'cs_candidate_map_zoom' => $cs_map_zoom_level, 'cs_candidate_map_height' => '', 'cs_candidate_map_style' => 'style-2', 'cs_candidate_searchbox' => 'yes', 'cs_candidate_view' => 'list','cs_candidate_cols'=>'6', 'cs_candidate_searchbox_top' => 'yes', 'cs_candidate_show_pagination' => 'pagination', 'cs_candidate_pagination' => '10');
        if (isset($output['0']['atts']))
            $atts = $output['0']['atts'];
        else
            $atts = array();
        if (isset($output['0']['content']))
            $candidate_content = $output['0']['content'];
        else
            $candidate_content = '';
        $candidate_element_size = '50';
        foreach ($defaults as $key => $values) {
            if (isset($atts[$key]))
                $$key = $atts[$key];
            else
                $$key = $values;
        }
        $name = 'jobcareer_pb_candidate';
        $coloumn_class = 'column_' . $candidate_element_size;
        if (isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode') {
            $shortcode_element = 'shortcode_element_class';
            $shortcode_view = 'cs-pbwp-shortcode';
            $filter_element = 'ajax-drag';
            $coloumn_class = '';
        }
        ?>
        <div id="<?php echo esc_attr($name . $cs_counter); ?>_del" class="column  parentdelete <?php echo esc_attr($coloumn_class); ?> <?php echo esc_attr($shortcode_view); ?>" item="candidate" data="<?php echo element_size_data_array_index($candidate_element_size) ?>">
            <?php cs_element_setting($name, $cs_counter, $candidate_element_size); ?>
            <div class="cs-wrapp-class-<?php echo intval($cs_counter); ?> <?php echo esc_attr($shortcode_element); ?>" id="<?php echo esc_attr($name . $cs_counter); ?>" data-shortcode-template="[cs_candidate {{attributes}}]"  style="display: none;">
                <div class="cs-heading-area">
                    <h5><?php _e('JC: Candidate Options', 'jobhunt') ?></h5>
                    <a href="javascript:cs_remove_overlay('<?php echo esc_attr($name . $cs_counter) ?>','<?php echo esc_attr($filter_element); ?>')" class="cs-btnclose"><i class="icon-times"></i></a> </div>
                <div class="cs-pbwp-content">
                    <div class="cs-wrapp-clone cs-shortcode-wrapp">
                        <?php
                        if (isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode') {
                            cs_shortcode_element_size();
                        }

                        $cs_opt_array = array(
                            'name' => __('Element Title', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => __("Enter element title here.", "jobhunt"),
                            'echo' => true,
                            'field_params' => array(
                                'std' => $cs_candidate_title,
                                'id' => 'candidate_title',
                                'cust_name' => 'cs_candidate_title[]',
                                'return' => true,
                            ),
                        );

                        $cs_html_fields->cs_text_field($cs_opt_array);
                        
                        $cs_opt_array = array(
                            'name' => __('Candidate Styles', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => __("Choose job view with this dropdown", "jobhunt"),
                            'echo' => true,
                            'field_params' => array(
                                'std' => $cs_candidate_view,
                                'id' => 'candidate_view',
                                'cust_name' => 'cs_candidate_view[]',
                                'classes' => 'dropdown chosen-select',
								'extra_atr' => ' onchange=cs_cand_view_change(this.value)',
                                'options' => array(
                                    'grid' => __('Grid', 'jobhunt'),
                                    'list' => __('List', 'jobhunt'),
                                    'box' => __('Box', 'jobhunt'),
                                ),
                                'return' => true,
                            ),
                        );
						?>
						<script>
						function cs_cand_view_change(selected_val){
	
	if(selected_val=='list'){
		$('#cs_cand_col_area').hide();
	}
	else{
		$('#cs_cand_col_area').show();
	}
	
}
</script>
<?php
                        $cs_html_fields->cs_select_field($cs_opt_array);
						if($cs_candidate_view == 'grid' || $cs_candidate_view == 'box'){
							 $cs_col_display = 'block';
						 }
						 else{
							  $cs_col_display = 'none';
						 }

                        echo '<div id="cs_cand_col_area" style="display:' . $cs_col_display . ';">';
						$cs_opt_array = array(
                            'name' => __('Columns', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => __("Choose jnumber of Columns", "jobhunt"),
                            'echo' => true,
                            'field_params' => array(
                                'std' => $cs_candidate_cols,
                                'id' => 'candidate_cols',
                                'cust_name' => 'cs_candidate_cols[]',
                                'classes' => 'dropdown chosen-select',
                                'options' => array(
											'1' => esc_html__('1 Column', 'jobhunt'),
                                            '2' => esc_html__('2 Columns', 'jobhunt'),
                                            '3' => esc_html__('3 Columns', 'jobhunt'),
                                            '4' => esc_html__('4 Columns', 'jobhunt'),
											'6' => esc_html__('6 Columns', 'jobhunt'),
                                    
                                ),
                                'return' => true,
                            ),
                        );
                        $cs_html_fields->cs_select_field($cs_opt_array);
                        echo '</div>';
                        $cs_opt_array = array(
                            'name' => __('Map on Top', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => __("ON/OFF map. This will display a map on top.", "jobhunt"),
                            'echo' => true,
                            'field_params' => array(
                                'std' => $cs_candidate_map,
                                'id' => 'candidate_map',
                                'cust_name' => 'cs_candidate_map[]',
                                'classes' => 'dropdown chosen-select',
                                'extra_atr' => ' onchange="cs_candidate_map_switch(this.value)"',
                                'options' => array(
                                    'no' => __('No', 'jobhunt'),
                                    'yes' => __('Yes', 'jobhunt'),
                                ),
                                'return' => true,
                            ),
                        );

                        $cs_html_fields->cs_select_field($cs_opt_array);

                        $cs_map_display = $cs_candidate_map == 'yes' ? 'block' : 'none';

                        echo '<div id="cs_cand_map_area" style="display:' . $cs_map_display . ';">';

                        $cs_opt_array = array(
                            'name' => __('Map Latitude', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => __("Enter Latitude for Map.", "jobhunt"),
                            'echo' => true,
                            'field_params' => array(
                                'std' => $cs_candidate_map_lat,
                                'id' => 'candidate_map_lat',
                                'cust_name' => 'cs_candidate_map_lat[]',
                                'return' => true,
                            ),
                        );
                        $cs_html_fields->cs_text_field($cs_opt_array);
                        $cs_opt_array = array(
                            'name' => __('Map Longitude', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => __("Enter Longitude for Map.", "jobhunt"),
                            'echo' => true,
                            'field_params' => array(
                                'std' => $cs_candidate_map_long,
                                'id' => 'candidate_map_long',
                                'cust_name' => 'cs_candidate_map_long[]',
                                'return' => true,
                            ),
                        );
                        $cs_html_fields->cs_text_field($cs_opt_array);
                        $cs_opt_array = array(
                            'name' => __('Zoom Level', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => __("Enter Zoom Level for Map.", "jobhunt"),
                            'echo' => true,
                            'field_params' => array(
                                'std' => $cs_candidate_map_zoom,
                                'id' => 'candidate_map_zoom',
                                'cust_name' => 'cs_candidate_map_zoom[]',
                                'return' => true,
                            ),
                        );
                        $cs_html_fields->cs_text_field($cs_opt_array);

                        $cs_opt_array = array(
                            'name' => __('Map Container Height', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => __("Enter Height for Map.", "jobhunt"),
                            'echo' => true,
                            'field_params' => array(
                                'std' => $cs_candidate_map_height,
                                'id' => 'candidate_map_height',
                                'cust_name' => 'cs_candidate_map_height[]',
                                'return' => true,
                            ),
                        );
                        $cs_html_fields->cs_text_field($cs_opt_array);

                        echo '</div>';

                        $cs_opt_array = array(
                            'name' => __('Filterable', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => __("ON/OFF search box with this dropdown. Search box will display same like sidebar of listing.", "jobhunt"),
                            'echo' => true,
                            'field_params' => array(
                                'std' => $cs_candidate_searchbox,
                                'id' => 'candidate_searchbox',
                                'cust_name' => 'cs_candidate_searchbox[]',
                                'classes' => 'dropdown chosen-select',
                                'options' => array(
                                    'yes' => __('Yes', 'jobhunt'),
                                    'no' => __('No', 'jobhunt'),
                                ),
                                'return' => true,
                            ),
                        );

                        $cs_html_fields->cs_select_field($cs_opt_array);

                        $cs_opt_array = array(
                            'name' => __('Search Box On Top', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => __("Search box top of content can be enable disable from here.", "jobhunt"),
                            'echo' => true,
                            'field_params' => array(
                                'std' => $cs_candidate_searchbox_top,
                                'id' => 'candidate_searchbox_top',
                                'cust_name' => 'cs_candidate_searchbox_top[]',
                                'classes' => 'dropdown chosen-select',
                                'options' => array(
                                    'yes' => __('Yes', 'jobhunt'),
                                    'no' => __('No', 'jobhunt'),
                                ),
                                'return' => true,
                            ),
                        );


                        $cs_html_fields->cs_select_field($cs_opt_array);

                        $cs_opt_array = array(
                            'name' => __('Pagination', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => __("Pagination is the process of dividing a document into discrete pages. Manage candidate pagination via this dropdown.", "jobhunt"),
                            'echo' => true,
                            'field_params' => array(
                                'std' => $cs_candidate_show_pagination,
                                'id' => 'candidate_show_pagination',
                                'cust_name' => 'cs_candidate_show_pagination[]',
                                'classes' => 'dropdown chosen-select',
                                'options' => array('pagination' => __('Pagination', 'jobhunt'), 'single_page' => __('Single Page', 'jobhunt')),
                                'return' => true,
                            ),
                        );

                        $cs_html_fields->cs_select_field($cs_opt_array);


                        $cs_opt_array = array(
                            'name' => __('Records Per Page', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => __("Add number of post for show posts on page.", "jobhunt"),
                            'echo' => true,
                            'field_params' => array(
                                'std' => $cs_candidate_pagination,
                                'id' => 'candidate_pagination',
                                'cust_name' => 'cs_candidate_pagination[]',
                                'return' => true,
                            ),
                        );

                        $cs_html_fields->cs_text_field($cs_opt_array);

                        if (isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode') {
                            ?>
                            <ul class="form-elements insert-bg">
                                <li class="to-field">
                                    <a class="insert-btn cs-main-btn" onclick="javascript:Shortcode_tab_insert_editor('<?php echo esc_js(str_replace('jobcareer_pb_', '', $name)); ?>', '<?php echo esc_js($name . $cs_counter); ?>', '<?php echo esc_js($filter_element); ?>')" ><?php _e('Insert', 'jobhunt') ?></a>
                                </li>
                            </ul>
                            <div id="results-shortocde"></div>
                            <?php
                        } else {
                            $cs_opt_array = array(
                                'name' => '',
                                'id' => '',
                                'desc' => '',
                                'echo' => true,
                                'fields_list' => array(
                                    array('type' => 'hidden', 'field_params' => array(
                                            'std' => 'candidate',
                                            'id' => '',
                                            'cust_id' => '',
                                            'cust_name' => 'cs_orderby[]',
                                            'cust_type' => '',
                                            'classes' => '',
                                            'return' => true,
                                        ),
                                    ),
                                    array('type' => 'text', 'field_params' => array(
                                            'std' => __("Save", "jobhunt"),
                                            'id' => '',
                                            'cust_type' => 'button',
                                            'cust_id' => '',
                                            'cust_name' => '',
                                            'return' => true,
                                            'extra_atr' => 'style="margin-right:10px;" onclick="javascript:_removerlay(jQuery(this))" ',
                                        ),
                                    ),
                                ),
                            );
                            $cs_html_fields->cs_multi_fields($cs_opt_array);
                        }
                        ?>
                    </div>
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

    add_action('wp_ajax_jobcareer_pb_candidate', 'jobcareer_pb_candidate');
}
/*
 *
 * End Function how to manage of page of candidate
 *
 */
