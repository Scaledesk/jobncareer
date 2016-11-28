<?php

/**
 * Start Function  how to Create Theme Options in Backend 
 */
if (!function_exists('cs_settings_options_page')) {

    function cs_settings_options_page() {

        global $cs_setting_options, $cs_form_fields2;
        $cs_plugin_options = get_option('cs_plugin_options');
        $obj = new jobhunt_options_fields();
        $return = $obj->cs_fields($cs_setting_options);
        $cs_opt_btn_array = array(
            'id' => '',
            'std' => __('Save All Settings', 'jobhunt'),
            'cust_id' => "submit_btn",
            'cust_name' => "submit_btn",
            'cust_type' => 'button',
            'classes' => 'bottom_btn_save',
            'extra_atr' => 'onclick="javascript:plugin_option_save(\'' . esc_js(admin_url('admin-ajax.php')) . '\');" ',
            'return' => true,
        );


        $cs_opt_hidden1_array = array(
            'id' => '',
            'std' => 'plugin_option_save',
            'cust_id' => "",
            'cust_name' => "action",
            'return' => true,
        );


        $cs_opt_hidden2_array = array(
            'id' => '',
            'std' => wp_jobhunt::plugin_url(),
            'cust_id' => "cs_plugin_url",
            'cust_name' => "cs_plugin_url",
            'return' => true,
        );

        $cs_opt_btn_cancel_array = array(
            'id' => '',
            'std' => __('Reset All Options', 'jobhunt'),
            'cust_id' => "submit_btn",
            'cust_name' => "reset",
            'cust_type' => 'button',
            'classes' => 'bottom_btn_reset',
            'extra_atr' => 'onclick="javascript:cs_rest_plugin_options(\'' . esc_js(admin_url('admin-ajax.php')) . '\');"',
            'return' => true,
        );

        $html = '
        <div class="theme-wrap fullwidth">
            <div class="inner">
                <div class="outerwrapp-layer">
                    <div class="loading_div" id="cs_loading_msg_div"> <i class="icon-circle-o-notch icon-spin"></i> <br>
                        ' . __('Please Wait...', 'jobhunt') . '
                    </div>
                    <div class="form-msg"> <i class="icon-check-circle-o"></i>
                        <div class="innermsg"></div>
                    </div>
                </div>
                <div class="row">
                    <form id="plugin-options" method="post">
			<div class="col1">
                            <nav class="admin-navigtion">
                                <div class="logo"> <a href="javascript;;" class="logo1"><img src="' . esc_url(wp_jobhunt::plugin_url()) . 'assets/images/logo.png" /></a> <a href="#" class="nav-button"><i class="icon-align-justify"></i></a> </div>
                                <ul>
                                    ' . force_balance_tags($return[1], true) . '
                                </ul>
                            </nav>
                        </div>
                        <div class="col2">
                        ' . force_balance_tags($return[0], true) . '
                        </div>

                        <div class="clear"></div>
                        <div class="footer">
                        ' . $cs_form_fields2->cs_form_text_render($cs_opt_btn_array) . '
                        ' . $cs_form_fields2->cs_form_hidden_render($cs_opt_hidden1_array) . '
                        ' . $cs_form_fields2->cs_form_hidden_render($cs_opt_hidden2_array) . '
                        ' . $cs_form_fields2->cs_form_text_render($cs_opt_btn_cancel_array) . '
                                
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="clear"></div>';
        $html .= '<script type="text/javascript">
			// Sub Menus Show/hide
			jQuery(document).ready(function($) {
                jQuery(".sub-menu").parent("li").addClass("parentIcon");
                $("a.nav-button").click(function() {
                    $(".admin-navigtion").toggleClass("navigation-small");
                });                
                $("a.nav-button").click(function() {
                    $(".inner").toggleClass("shortnav");
                });                
                $(".admin-navigtion > ul > li > a").click(function() {
                    var a = $(this).next(\'ul\')
                    $(".admin-navigtion > ul > li > a").not($(this)).removeClass("changeicon")
                    $(".admin-navigtion > ul > li ul").not(a) .slideUp();
                    $(this).next(\'.sub-menu\').slideToggle();
                    $(this).toggleClass(\'changeicon\');
                });
                $(\'[data-toggle="popover"]\').popover(\'destroy\');
            });            
            function show_hide(id){
				var link = id.replace("#", "");
                jQuery(\'.horizontal_tab\').fadeOut(0);
                jQuery("#"+link).fadeIn(400);
            }            
            function toggleDiv(id) { 
                jQuery(\'.col2\').children().hide();
                jQuery(id).show();
                location.hash = id+"-show";
                var link = id.replace("#", "");
                jQuery(\'.categoryitems li\').removeClass(\'active\');
                jQuery(".menuheader.expandable") .removeClass(\'openheader\');
                jQuery(".categoryitems").hide();
		jQuery("."+link).addClass(\'active\');
		jQuery("."+link) .parent("ul").show().prev().addClass("openheader");
                google.maps.event.trigger(document.getElementById("cs-map-location-id"), "resize");
            }
            jQuery(document).ready(function() {
                jQuery(".categoryitems").hide();
                jQuery(".categoryitems:first").show();
                jQuery(".menuheader:first").addClass("openheader");
                jQuery(".menuheader").live(\'click\', function(event) {
                    if (jQuery(this).hasClass(\'openheader\')){
                        jQuery(".menuheader").removeClass("openheader");
                        jQuery(this).next().slideUp(200);
                        return false;
                    }
                    jQuery(".menuheader").removeClass("openheader");
                    jQuery(this).addClass("openheader");
                    jQuery(".categoryitems").slideUp(200);
                    jQuery(this).next().slideDown(200); 
                    return false;
                });                
                var hash = window.location.hash.substring(1);
                var id = hash.split("-show")[0];
                if (id){
                    jQuery(\'.col2\').children().hide();
                    jQuery("#"+id).show();
                    jQuery(\'.categoryitems li\').removeClass(\'active\');
                    jQuery(".menuheader.expandable") .removeClass(\'openheader\');
                    jQuery(".categoryitems").hide();
                    jQuery("."+id).addClass(\'active\');
                    jQuery("."+id) .parent("ul").slideDown(300).prev().addClass("openheader");
                } 
            });
            
        </script>';
        echo force_balance_tags($html, true);
    }

    /**
     * end Function  how to Create Theme Options in Backend 
     */
}
/**
 * Start Function  how to Create Theme Options setting in Backend 
 */
if (!function_exists('cs_settings_option')) {

    function cs_settings_option() {
        global $cs_setting_options;
        $cs_theme_menus = get_registered_nav_menus();
        $cs_plugin_options = get_option('cs_plugin_options');
        $on_off_option = array("show" => "on", "hide" => "off");

        $cs_min_days = array();
        for ($days = 1; $days < 11; $days++) {
            $cs_min_days[$days] = "$days day";
        }
        $cs_setting_options[] = array(
            "name" => __("General Options", "jobhunt"),
            "fontawesome" => 'icon-tools3',
            "id" => "tab-general",
            "std" => "",
            "type" => "heading",
            "options" => array(
                'tab-general-page-settings' => __('Page Settings', 'jobhunt'),
                'tab-general-default-location' => __('Default Location', 'jobhunt'),
                'tab-candidate-skills-sets' => __('Candidate Skills Sets', 'jobhunt'),
                'tab-general-others' => __('Others', 'jobhunt'),
                'tab-smtp' => __('SMTP Settings', 'jobhunt'),
            )
        );
        $cs_setting_options[] = array(
            "name" => __("Gateways", "jobhunt"),
            "fontawesome" => 'icon-wallet2',
            "id" => "tab-gateways-settings",
            "std" => "",
            "type" => "main-heading",
            "options" => ''
        );
        $cs_setting_options[] = array(
            "name" => __("Packages", "jobhunt"),
            "fontawesome" => 'icon-credit-card',
            "id" => "tab-packages-settings",
            "std" => "",
            "type" => "heading",
            "options" => array(
                'tab-job-pkgs' => __('Job Credit', 'jobhunt'),
                'tab-cv-pkgs' => __('CV Search', 'jobhunt'),
                'tab-featured_jobs' => __('Featured Jobs', 'jobhunt'),
            )
        );
        $cs_setting_options[] = array(
            "name" => __("Custom Fields", "jobhunt"),
            "fontawesome" => 'icon-list-alt',
            "id" => "tab-custom-fields",
            "std" => "",
            "type" => "heading",
            "options" => array(
                'tab-cusfields-jobs' => __('Jobs Fields', 'jobhunt'),
                'tab-cusfields-candidates' => __('Candidates Fields', 'jobhunt'),
                'tab-cusfields-employers' => __('Recruiters', 'jobhunt'),
            )
        );
        $cs_setting_options[] = array(
            "name" => __("Api Settings", "jobhunt"),
            "fontawesome" => 'icon-link4',
            "id" => "tab-api-setting",
            "std" => "",
            "type" => "main-heading",
            "options" => ''
        );
        $cs_setting_options[] = array(
            "name" => __("Search Options", "jobhunt"),
            "fontawesome" => 'icon-search6',
            "id" => "tab-basic-settings",
            "std" => "",
            "type" => "main-heading",
            "options" => '',
        );
        $cs_setting_options[] = array(
            "name" => __("Social Icon", "jobhunt"),
            "fontawesome" => 'icon-users5',
            "id" => "tab-social-icons",
            "std" => "",
            "type" => "main-heading",
            "options" => ''
        );
        // General Settings
        $cs_setting_options[] = array("name" => __("General Options", "jobhunt"),
            "id" => "tab-general-page-settings",
            "type" => "sub-heading",
            "help_text" => "",
        );
        $cs_setting_options[] = array("name" => __('User Settings', 'jobhunt'),
            "id" => "tab-user-settings",
            "std" => __('User Settings', 'jobhunt'),
            "type" => "section",
            "options" => ""
        );
        $cs_setting_options[] = array("name" => __("User Header Login", "jobhunt"),
            "desc" => "",
            "hint_text" => __("Dashboard and Front-End login/register option can be hide by turning off this switch.", "jobhunt"),
            "id" => "user_dashboard_switchs",
            "std" => "on",
            "type" => "checkbox",
            "options" => $on_off_option
        );
        $cs_setting_options[] = array(
            "name" => __("Menu Location", "jobhunt"),
            "desc" => "",
            "hint_text" => __("Show login section in Menu", "jobhunt"),
            "id" => "menu_login_location",
            "std" => "",
            'classes' => 'chosen-select-no-single',
            "type" => "select_values",
            "options" => $cs_theme_menus,
        );
        $cs_setting_options[] = array(
            "name" => __("Employer Dashboard", "jobhunt"),
            "desc" => "",
            "hint_text" => __("Select page for employer dashboard here. This page is set in page template drop down. To create employer dashboard page, go to Pages > Add new page, set the page template to 'employer' in the right menu.", "jobhunt"),
            "id" => "cs_emp_dashboard",
            "std" => "",
            "type" => "select_dashboard",
            "options" => '',
        );
        $cs_setting_options[] = array(
            "name" => __("Candidates Dashboard", "jobhunt"),
            "desc" => "",
            "hint_text" => __("Select page for Candidates dashboard here. This page is set in page template drop down. To create Candidate dashboard page, go to Pages > Add new page, set the page template to 'Candidate' in the right menu.", "jobhunt"),
            "id" => "cs_js_dashboard",
            "std" => "30",
            "type" => "select_dashboard",
            "options" => '',
        );
        $cs_setting_options[] = array(
            "name" => __("Author Page Slug", 'jobhunt'),
            "desc" => "",
            "hint_text" => __("Please enter slug for author page (employer and candidate)", "jobhunt"),
            "id" => "author_page_slug",
            "std" => "user",
            "type" => "text"
        );

        $cs_setting_options[] = array("name" => __("Title Font Size", "jobhunt"),
            "desc" => "",
            "hint_text" => __("Please set font size for title.", "jobhunt"),
            "id" => "job_default_header_title_f_size",
            "std" => '0',
            "type" => "text"
        );
        $cs_setting_options[] = array("name" => __("Title Color", "jobhunt"),
            "desc" => "",
            "hint_text" => __("Please select color for title.", "jobhunt"),
            "id" => "job_default_header_title_color",
            "std" => '',
            "type" => "color"
        );
        $cs_setting_options[] = array("name" => __("Character List For Filter", 'jobhunt'),
            "desc" => "",
            "hint_text" => __('Please add character list in given format: A,B,C,D,E, ... etc.', "jobhunt"),
            "id" => "job_user_filter_character",
            "std" => 'A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z',
            "type" => "textarea",
        );
        $cs_setting_options[] = array("name" => __("Demo Login Users", "jobhunt"),
            "id" => "tab-demo-user-login-options",
            "std" => __("Demo Login Users", "jobhunt"),
            "type" => "section",
            "options" => ""
        );
        $cs_setting_options[] = array(
            "name" => __("Demo User Login", "jobhunt"),
            "desc" => "",
            "hint_text" => '',
            "id" => "demo_user_login_switch",
            "std" => "on",
            "type" => "checkbox",
            "options" => $on_off_option
        );


        $cs_employers_list = array('' => __("Please Select", "jobhunt"));
        $cs_users = get_users('orderby=nicename&role=cs_employer');
        foreach ($cs_users as $user) {
            $cs_employers_list[$user->ID] = $user->display_name;
        }


        $cs_setting_options[] = array(
            'name' => __('Recruiter', 'jobhunt'),
            "desc" => "",
            "hint_text" => __("Please select a user for recruiter login", "jobhunt"),
            'id' => 'job_demo_user_employer',
            "std" => "",
            "classes" => "chosen-select",
            "type" => "select",
            "options" => $cs_employers_list,
        );

        $cs_candidate_list = array('' => __("Please Select", "jobhunt"));
        $cs_users = get_users('orderby=nicename&role=cs_candidate');
        foreach ($cs_users as $user) {
            $cs_candidate_list[$user->ID] = $user->display_name;
        }

        $cs_setting_options[] = array(
            'name' => __('Candidate', 'jobhunt'),
            "desc" => "",
            "hint_text" => __("Please select a user for candidate login", "jobhunt"),
            'id' => 'job_demo_user_candidate',
            "std" => "",
            "classes" => "chosen-select",
            "type" => "select",
            "options" => $cs_candidate_list,
        );

        $cs_setting_options[] = array("name" => __("Language Settings", "jobhunt"),
            "id" => "tab-lang-options",
            "std" => __("Language Settings", "jobhunt"),
            "type" => "section",
            "options" => ""
        );

        $dir = wp_jobhunt::plugin_dir() . '/languages/';
        $cs_plugin_language[''] = __('Select Language File', "jobhunt");
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    $ext = pathinfo($file, PATHINFO_EXTENSION);
                    if ($ext == 'mo') {
                        $cs_plugin_language[$file] = $file;
                    }
                }
                closedir($dh);
            }
        }

        $cs_setting_options[] = array("name" => __("Select Plugin Language", "jobhunt"),
            "desc" => "",
            "hint_text" => __("You can select any language for your wp_job hunt plugin from drop down here. Any language added through language plugin will show up in this drop down. **All areas related to employers / job candidates on front-end will be translated in selected language.", "jobhunt"),
            "id" => "language_file",
            "std" => "30",
            "classes" => 'chosen-select-no-single',
            "type" => "select",
            "options" => $cs_plugin_language,
        );

        $cs_setting_options[] = array("name" => __("Job Settings", "jobhunt"),
            "id" => "tab-job-options",
            "std" => __("Jobs Settings", "jobhunt"),
            "type" => "section",
            "options" => ""
        );

        $cs_setting_options[] = array(
            "name" => __("Free Jobs Posting", "jobhunt"),
            "desc" => "",
            "hint_text" => '',
            "id" => "free_jobs_switch",
            "std" => "on",
            "type" => "checkbox",
            "options" => $on_off_option
        );

        $cs_setting_options[] = array(
            "name" => __("Job Detail Style", "jobhunt"),
            "desc" => "",
            "hint_text" => __("Select Job Detail Page Style", "jobhunt"),
            "id" => "job_detail_style",
            "std" => "",
            "classes" => "chosen-select-no-single",
            "type" => "select",
            "options" => array(
                '' => __("Please Select", "jobhunt"),
                '2_columns' => __("2 Columns", "jobhunt"),
                '3_columns' => __("3 Columns", "jobhunt"),
                'classic' => __("Classic", "jobhunt"),
                'fancy' => __("Fancy", "jobhunt"),
                'map_view' => __("Map View", "jobhunt"),
            ),
        );
        //Default css Elements
        $cs_setting_options[] = array("name" => __("Default css", "jobhunt"),
            "id" => "tab-job-options",
            "std" => __("Default css elements", "jobhunt"),
            "type" => "section",
            "options" => ""
        );
        $cs_setting_options[] = array("name" => __("Default css ", "jobhunt"),
            "desc" => "",
            "hint_text" => __("Default css for common elements (h1,h2,p etc)", "jobhunt"),
            "id" => "common-elements-style",
            "std" => "on",
            "type" => "checkbox",
            "options" => $on_off_option
        );

        // Default sidebar
        $cs_setting_options[] = array("name" => __("Default Sidebars", "jobhunt"),
            "id" => "tab-job-options",
            "std" => __("Default Sidebar", "jobhunt"),
            "type" => "section",
            "options" => ""
        );
        $cs_setting_options[] = array("name" => __("Default Sidebars off", "jobhunt"),
            "desc" => "",
            "hint_text" => __("It will disable widgets of all Sidebars", "jobhunt"),
            "id" => "default-sidebars",
            "std" => "on",
            "type" => "checkbox",
            "options" => $on_off_option
        );
        // custom css
        $cs_setting_options[] = array("name" => __("Custom Css", "jobhunt"),
            "id" => "tab-job-options",
            "std" => __("Default Sidebar", "jobhunt"),
            "type" => "section",
            "options" => ""
        );
        $cs_setting_options[] = array("name" => __("Custom Css", "jobhunt"),
            "desc" => "",
            "hint_text" => __("This is custom css area", "jobhunt"),
            "id" => "style-custom-css",
            "std" => "",
            "type" => "textarea",
        );
        $cs_setting_options[] = array(
            "type" => "col-right-text",
        );

        // general default location 
        // Default location

        $cs_setting_options[] = array("name" => __("Default Location", "jobhunt"),
            "id" => "tab-general-default-location",
            "type" => "sub-heading",
            "extra" => "div",
            "help_text" => __('Default Location Set default location for your site. This location can be set from Jobs > Locations in back end admin area. This will show location of admin only. It is not linked with Geo-location or Candidate.', 'jobhunt'),
        );

        $cs_setting_options[] = array("name" => __('Default Location', 'jobhunt'),
            "id" => "tab-settings-default-location",
            "std" => __('Default Location', 'jobhunt'),
            "type" => "section",
            "options" => "",
        );

        $cs_setting_options[] = array("name" => __("Cluster Icon", "jobhunt"),
            "desc" => "",
            "hint_text" => "",
            "id" => "cs_map_cluster_icon",
            "std" => wp_jobhunt::plugin_url() . 'assets/images/culster-icon.png',
            "display" => "none",
            "type" => "upload logo"
        );

        $cs_setting_options[] = array("name" => __("Map Marker Icon", "jobhunt"),
            "desc" => "",
            "hint_text" => "",
            "id" => "cs_map_marker_icon",
            "std" => wp_jobhunt::plugin_url() . 'assets/images/map-marker.png',
            "display" => "none",
            "type" => "upload logo"
        );

        $cs_setting_options[] = array(
            "name" => __("Zoom Level", 'jobhunt'),
            "desc" => "",
            "hint_text" => '',
            "id" => "map_zoom_level",
            "std" => "11",
            "type" => "text"
        );

        $cs_setting_options[] = array(
            "name" => __("Map Cluster Color", 'jobhunt'),
            "desc" => "",
            "hint_text" => '',
            "id" => "map_cluster_color",
            "std" => "#000000",
            "type" => "color"
        );

        $cs_setting_options[] = array(
            "name" => __("Map Auto Zoom", "jobhunt"),
            "desc" => "",
            "hint_text" => __('Manual Zoom will not work if Auto Zoom is on.', 'jobhunt'),
            "id" => "map_auto_zoom",
            "main_id" => 'cs_map_auto_zoom_main',
            "std" => "",
            "type" => "checkbox"
        );

        $cs_setting_options[] = array(
            "name" => __("Map Lock", "jobhunt"),
            "desc" => "",
            "hint_text" => "",
            "id" => "map_lock",
            "main_id" => 'cs_map_lock_main',
            "std" => "",
            "type" => "checkbox"
        );

        $cs_setting_options[] = array("name" => __("Default Address", 'jobhunt'),
            "desc" => "",
            "hint_text" => "",
            "id" => "default_locations",
            "std" => "",
            "type" => "default_location_fields",
            "contry_hint" => __("Set default location for the site here. **See further description in the right panel", "jobhunt"),
            "city_hint" => __("To set the city, first select  a country. **See further description in the right panel.", "jobhunt"),
            "address_hint" => __("Set default street address here. **See further description in the right panel.", "jobhunt"),
        );
        $cs_setting_options[] = array("col_heading" => __("Default Location", "jobhunt"),
            "type" => "col-right-text",
            "extra" => "div",
            "help_text" => __('Set default location for your site (Country, City & Address). This location can be set from Jobs > Locations in back end admin area. This will show location of admin only and willl fetch results from the given location first. It is not linked with Geo-location or Candidate.', 'jobhunt'),
        );
        //End default location 

        /*
         * Candidate Skills Section
         */
        $cs_setting_options[] = array("name" => __("Candidate Skills Sets", "jobhunt"),
            "id" => "tab-candidate-skills-sets",
            "type" => "sub-heading",
            "help_text" => __("Admin Can set candidate's percentage criteria. If that candidate Percentage skill will be less than this percentage candidate will not allow to apply any job.","jobhunt")
            );

        $skills_array = cs_candidate_skills_set_array();

        if (is_array($skills_array) && sizeof($skills_array) > 0) {

            $cs_setting_options[] = array(
                "type" => 'custom_div',
                "id" => "cadidate-skills-set-calc-sec",
            );
            foreach ($skills_array as $skills_array_key => $skills_array_set) {

                if (array_key_exists('list', $skills_array_set) && is_array($skills_array_set['list'])) {
                    $skill_sec_name = isset($skills_array_set['name']) ? $skills_array_set['name'] : '';
                    if ($skill_sec_name != '' && $skills_array_key != '') {
                        $cs_setting_options[] = array(
                            "name" => $skill_sec_name,
                            "id" => "tab-settings-$skills_array_key-skill",
                            "std" => $skill_sec_name,
                            "type" => "section",
                            "options" => ""
                        );
                    }
                    foreach ($skills_array_set['list'] as $skill_list_key => $skill_list_set) {
                        $skill_name = isset($skill_list_set['name']) ? $skill_list_set['name'] : '';
                        if ($skill_list_key != '' && $skill_name != '') {

                            $this_opt_id = str_replace('cs_', '', $skill_list_key) . '_skill';
                            $cs_setting_options[] = array(
                                "name" => $skill_name,
                                "desc" => "",
                                "hint_text" => '',
                                "id" => "$this_opt_id",
                                "std" => "0",
                                "classes" => "candidate_skill_field",
                                "type" => "text",
                            );
                        }
                    }
                } else {
                    $skill_name = isset($skills_array_set['name']) ? $skills_array_set['name'] : '';
                    if ($skills_array_key != '' && $skill_name != '') {
                        $this_opt_id = str_replace('cs_', '', $skills_array_key) . '_skill';
                        $cs_setting_options[] = array(
                            "name" => $skill_name,
                            "desc" => "",
                            "hint_text" => '',
                            "id" => "$this_opt_id",
                            "std" => "0",
                            "classes" => "candidate_skill_field",
                            "type" => "text",
                        );
                    }
                }
            }

            $cs_setting_options[] = array(
                "type" => 'division_close',
            );
        }



        $cs_setting_options[] = array("name" => __("Required Skill Set", "jobhunt"),
            "id" => "tab-required-skill-set-options",
            "std" => __("Required Skill Set", "jobhunt"),
            "type" => "section",
            "options" => ""
        );
        $cs_setting_options[] = array(
            "name" => __("Candidate Skills Percentage", "jobhunt"),
            "desc" => "",
            "hint_text" => __("Set Candidate Skills Percentage such as 70. If Candidate's Skills Percentage less than this Percentage then He/She will not able to apply any Job.", "jobhunt"),
            "id" => "candidate_skills_percentage",
            "std" => "0",
            "type" => "text",
        );

        $cs_setting_options[] = array(
            "type" => "candidate_skills",
        );
        /*
         * End Candidate Skills List
         */

        $cs_setting_options[] = array("col_heading" => __("Candidate Skills Sets", "jobhunt"),
            "type" => "col-right-text",
            "help_text" => __("Admin Can set candidate's percentage criteria. If that candidate Percentage skill will be less than this percentage candidate will not allow to apply any job.","jobhunt")
       );

        // general others
        // Default location fields
        $cs_setting_options[] = array("name" => __("Others", "jobhunt"),
            "id" => "tab-general-others",
            "type" => "sub-heading",
        );
        $cs_setting_options[] = array("name" => __('Candidates', 'jobhunt'),
            "id" => "tab-settings-candidates",
            "std" => __('Candidates', 'jobhunt'),
            "type" => "section",
            "options" => ""
        );
        $cs_setting_options[] = array("name" => __("Candidates Profile", "jobhunt"),
            "desc" => "",
            "hint_text" => __("Trun off this option to allow employers to see profile of candidate without payment. If it will be ON, the candidate's profile will not be accessable publically, Employer will have to purchase a package to access the profile of job candidates.", "jobhunt"),
            "id" => "candidate_switch",
            "std" => "on",
            "type" => "checkbox",
            "options" => $on_off_option
        );
        $cs_setting_options[] = array(
            "name" => __("Awards", "jobhunt"),
            "desc" => "",
            "hint_text" => __("Turn OFF this switch to hide Awards tab for candidate on frontend. (For admin in backend area of candidate, the tab of Awards will also hide). If the switch is ON, candidate will be able to set / manage his Awards from front-end and admin will see the tab of 'Awards' in candidate back end area.", "jobhunt"),
            "id" => "award_switch",
            "std" => "on",
            "type" => "checkbox",
            "options" => $on_off_option
        );
        $cs_setting_options[] = array(
            "name" => __("Portfolio", "jobhunt"),
            "desc" => "",
            "hint_text" => __("Turn OFF this switch to hide Portfolio tab for candidate on frontend. (For admin in backend area of candidate, the tab of Portfolio will also hide). If the switch is ON, candidate will be able to set / manage his portfolio from front-end and admin will see the tab of 'Portfolio' in candidate back end area.", "jobhunt"),
            "id" => "portfolio_switch",
            "std" => "on",
            "type" => "checkbox",
            "options" => $on_off_option
        );
        $cs_setting_options[] = array(
            "name" => __("Skills", "jobhunt"),
            "desc" => "",
            "hint_text" => __("Turn OFF this switch to hide Skills tab for candidate on frontend. (For admin in backend area of candidate, the tab of Skills will also hide). If the switch is ON, candidate will be able to set / manage his Skills from front-end and admin will see the tab of 'Skills' in candidate back end area.", "jobhunt"),
            "id" => "skills_switch",
            "std" => "on",
            "type" => "checkbox",
            "options" => $on_off_option
        );
        $cs_setting_options[] = array(
            "name" => __("Education", "jobhunt"),
            "desc" => "",
            "hint_text" => __("Turn OFF this switch to hide Education tab for candidate on frontend. (For admin in backend area of candidate, the tab of Education will also hide). If the switch is ON, candidate will be able to set / manage his Education from front-end and admin will see the tab of 'Education' in candidate back end area.", "jobhunt"),
            "id" => "education_switch",
            "std" => "on",
            "type" => "checkbox",
            "options" => $on_off_option
        );
        $cs_setting_options[] = array(
            "name" => __("Experience", "jobhunt"),
            "desc" => "",
            "hint_text" => __("Turn OFF this switch to hide Experience tab for candidate on frontend. (For admin in backend area of candidate, the tab of Experience will also hide). If the switch is ON, candidate will be able to set / manage his Experience section from front-end and admin will see the tab of 'Experience' in candidate back end area.", "jobhunt"),
            "id" => "experience_switch",
            "std" => "on",
            "type" => "checkbox",
            "options" => $on_off_option
        );

        $cs_setting_options[] = array("name" => __('Submissions', 'jobhunt'),
            "id" => "tab-settings-submissions",
            "std" => __('Submissions', 'jobhunt'),
            "type" => "section",
            "options" => ""
        );
        $cs_setting_options[] = array("name" => __("Search Result Page", 'jobhunt'),
            "desc" => '',
            "hint_text" => __("Set the specific page where you want to show search results. The slected page must have jobs page element on it. (Add jobs page element while creating the job search result page).", 'jobhunt'),
            "id" => "cs_search_result_page",
            "std" => '',
            "type" => "select_dashboard",
            "options" => ''
        );
        $cs_setting_options[] = array(
            "name" => __("Terms and Conditions", "jobhunt"),
            "desc" => "",
            "hint_text" => __("Select page for Terms and Conditions here. This page is set in page template drop down.", "jobhunt"),
            "id" => "cs_terms_condition",
            "std" => "",
            "type" => "select_dashboard",
            "options" => '',
        );
        $cs_setting_options[] = array("name" => __("Single Pages Container On/Off", "jobhunt"),
            "desc" => "",
            "hint_text" => __("Add boostrap container class at all single pages related our plugin.", "jobhunt"),
            "id" => "plugin_single_container",
            "std" => "on",
            "type" => "checkbox",
            "options" => $on_off_option
        );
        $cs_setting_options[] = array("name" => __("Job Publish/Pending On/Off", "jobhunt"),
            "desc" => "",
            "hint_text" => __("Turn this switcher OFF to allow direct publishing of submitted jobs by employers without review / moderation. If this switch is ON, jobs will be published after admin review / moderation.", "jobhunt"),
            "id" => "jobs_review_option",
            "std" => "on",
            "type" => "checkbox",
            "options" => $on_off_option
        );


        $cs_setting_options[] = array("name" => __("Candidate auto-approval ON/OFF", "jobhunt"),
            "desc" => "",
            "hint_text" => __("Turn this switcher OFF to allow direct publishing of submitted jobs by candidate without review / moderation. If this switch is ON, jobs will be published after admin review / moderation", "jobhunt"),
            "id" => "candidate_review_option",
            "std" => "on",
            "type" => "checkbox",
            "options" => $on_off_option
        );

        $cs_setting_options[] = array("name" => __("Employer auto-approval ON/OFF", "jobhunt"),
            "desc" => "",
            "hint_text" => __("Turn this switcher OFF to allow direct publishing of submitted jobs by employers without review / moderation. If this switch is ON, jobs will be published after admin review / moderation", "jobhunt"),
            "id" => "employer_review_option",
            "std" => "on",
            "type" => "checkbox",
            "options" => $on_off_option
        );

        $cs_setting_options[] = array("name" => __("VAT On/Off", "jobhunt"),
            "desc" => "",
            "hint_text" => __("This switch will control VAT calculation and its payment along with package price. If this switch will be ON, user must have to pay VAT percentage separately. Turn OFF the switch to exclude VAT from payment.", "jobhunt"),
            "id" => "vat_switch",
            "std" => "on",
            "type" => "checkbox",
            "options" => $on_off_option
        );
        $cs_setting_options[] = array("name" => __("Value Added Tax in %", "jobhunt"),
            "desc" => "",
            "hint_text" => __("Here you can add VAT percentage according to your country laws & regulations.", "jobhunt"),
            "id" => "payment_vat",
            "std" => "",
            "type" => "text",
        );
        global $gateways;
        $general_settings = new CS_PAYMENTS();
        $cs_settings = $general_settings->cs_general_settings();

        foreach ($cs_settings as $key => $params) {
            $cs_setting_options[] = $params;
        }
         $cs_setting_options[] = array("name" => __('Safety Text', 'jobhunt'),
            "id" => "safety_text",
            "std" => __("Safety Text", 'jobhunt'),
            "type" => "section",
            "options" => "",
        );
        $cs_setting_options[] = array("name" => __("Safety Text On/Off", "jobhunt"),
            "desc" => "",
            "hint_text" => __("This switch will control your Safety Text. Help / warning or any kind of text added will safety on job detail page. ", "jobhunt"),
            "id" => "safetysafe_switch",
            "std" => "on",
            "type" => "checkbox",
            "options" => $on_off_option
        );
        $cs_setting_options[] = array("name" => __("Add Text", "jobhunt"),
            "desc" => "",
            "hint_text" => "",
            "id" => "safetysafe_text",
            "std" => "",
            "type" => "safetytext",
            "options" => ""
        );
        $cs_setting_options[] = array("name" => __('Payments Confirmation Page', 'jobhunt'),
            "id" => "tab-welcome-page",
            "std" => __("Payments Confirmation Page", 'jobhunt'),
            "type" => "section",
            "options" => "",
        );
        $cs_setting_options[] = array("name" => __("Title", 'jobhunt'),
            "desc" => "",
            "hint_text" => __("This title will print on frontend when employer post a new job as confirmation title on payment page.", "jobhunt"),
            "id" => "job_welcome_title",
            "std" => "",
            "type" => "text",
        );
        $cs_setting_options[] = array("name" => __("Content", 'jobhunt'),
            "desc" => "",
            "hint_text" => __("This Content will print on frontend when employer post a new job as confirmation content on payment page.", "jobhunt"),
            "id" => "job_welcome_con",
            "std" => "",
            "type" => "textarea",
        );

        




        $cs_setting_options[] = array("col_heading" => __("Others", "jobhunt"),
            "type" => "col-right-text",
            "help_text" => ""
        );

        $smtp_option = array("smtp" => "Send all WordPress emails via SMTP.", "mail" => "Use the PHP mail() function to send emails.");
        $return_option = array("true" => "Set the return-path to match the From Email");
        $cs_setting_options[] = array("name" => __("SMTP Settings", "jobhunt"),
            "id" => "tab-smtp",
            "type" => "sub-heading",
        );


        $cs_setting_options[] = array("col_heading" => __("SMTP Settings", "jobhunt"),
            "type" => "tab-smtp",
            "help_text" => ""
        );
        // <input name="mail_from" type="text" id="mail_from" value="<?php print(get_option('mail_from')); " //size="40" class="regular-text" />
        $cs_setting_options[] = array("name" => __("Enable SMTP to Send Emails?", 'jobhunt'),
            "desc" => "",
            "hint_text" => __("Turn it on If you want to send Email Through SMTP..", "jobhunt"),
            "id" => "use_smtp_mail",
            "std" => "",
            "type" => "checkbox",
			"onchange" => "use_smtp_mail_opt(this.name)",
			"options" => $on_off_option,
        );
		
		$cs_setting_options[] = array(
            "type" => "division",
            "enable_id" => "cs_use_smtp_mail",
            "enable_val" => "on",
            "extra_atts" => 'id="cs-no-smtp-div"',
        );
		
		$cs_setting_options[] = array("name" => __("Send e-mail via GMail?", 'jobhunt'),
            "desc" => "",
            "hint_text" => __("Turn it on If you want to send Email Through Google Email Server..", "jobhunt"),
            "id" => "gmail_mail",
            "std" => "",
            "type" => "checkbox",
			"onchange" => "cs_mail_with_gmail(this.name)",
			"options" => $on_off_option
        );
		
		$cs_setting_options[] = array("name" => __("Use SMTP Authentication?", 'jobhunt'),
            "desc" => "",
            "hint_text" => __("Turn it on If you want to use SMTP Authentication.., If checked, you must provide the SMTP username and password below", "jobhunt"),
            "id" => "use_smtp_auth",
            "std" => "",
            "type" => "checkbox",
        );
		
        $cs_setting_options[] = array("name" => __("SMTP Host Name", 'jobhunt'),
            "desc" => "",
            "hint_text" => __("Enter your Smtp host here, It is name of your service provider", "jobhunt"),
            "id" => "smtp_host",
            "std" => "",
            "type" => "text",
        );

        $cs_setting_options[] = array("name" => __("SMTP Port", 'jobhunt'),
            "desc" => "",
            "hint_text" => __("Enter your Smtp port here, e.g 25 or 465, This is generally 25.", "jobhunt"),
            "id" => "smtp_port",
            "std" => "",
            "type" => "text",
        );
		
        $cs_setting_options[] = array("name" => __("Connection prefix", 'jobhunt'),
            "desc" => "",
            "hint_text" => "Sets connection prefix for secure connections (prefix method must be supported by your PHP install and your SMTP host)",
            "id" => "secure_connection_type",
            "cust_name" => "mail_set_return_path",
            "std" => "true",
            "type" => "select",
            "options" => array('ssl' => 'ssl', 'tls' => 'tls'),
        );

//        $cs_setting_options[] = array(
//            "name" => __("SMTP Options", 'jobhunt'),
//            "id" => "SMTP",
//            "std" => "SMTP Options",
//            "type" => "section",
//            "options" => ""
//        );

        
		
        $cs_setting_options[] = array("name" => __("SMTP username", 'jobhunt'),
            "desc" => "",
            "hint_text" => 'Enter SMTP Username here',
            "id" => "smtp_username",
            "std" => "",
            "type" => "text",
        );
        $cs_setting_options[] = array("name" => __("SMTP Password", 'jobhunt'),
            "desc" => "",
            "hint_text" => 'Please Enter SMTP Password here',
            "id" => "smtp_password",
            "std" => "",
            "type" => "password",
        );

        $cs_setting_options[] = array("name" => __("Wordwrap length", 'jobhunt'),
            "desc" => "",
            "hint_text" => 'Enter Wordwrap length here, Sets word wrapping on the body of the message to a given number of characters.',
            "id" => "wordwrap_length",
            "std" => "",
            "type" => "text",
        );
		
		

        $cs_setting_options[] = array("name" => __("Enable debugging?", 'jobhunt'),
            "desc" => "",
            "hint_text" => __("Only check this if you are experiencing problems and would like more error reporting to occur. Uncheck this once you have finished debugging.", "jobhunt"),
            "id" => "smtp_debugging",
            "std" => "",
            "type" => "checkbox",
        );

		
        $cs_setting_options[] = array("name" => __("Sender e-mail", 'jobhunt'),
            "desc" => "",
            "hint_text" => __('Sets the From e-mail address for all outgoing messages. Leave blank to use the WordPress default. This value will be used even if you do not enable SMTP. NOTE: This may not take effect depending on your mail server and settings, especially if using SMTPAuth (such as for GMail).', "jobhunt"),
            "id" => "smtp_sender_email",
            "std" => "",
            "type" => "text",
        );
		
		

        $cs_setting_options[] = array("name" => __("Sender name", 'jobhunt'),
            "desc" => "",
            "hint_text" => __('Sets the From name for all outgoing messages. Leave blank to use the WordPress default. This value will be used even if you do not enable SMTP.', "jobhunt"),
            "id" => "sender_name",
            "std" => "",
            "type" => "text",
        );
		
		

//        
//        $cs_setting_options[] = array("name" => __("To:", 'jobhunt'),
//            "desc" => "",
//            "hint_text" => __("Type an email address here and then click Send Test to generate a test email.", "jobhunt"),
//            "id" => "to",
//            "std" => "",
//            "type" => "text",
//        );

$cs_address= esc_js(admin_url('admin-ajax.php')) ;
        $cs_setting_options[] = array("name" => '',
            "desc" => "",
            "hint_text" => '',
            "id" => "submit_test_email",
            "std" => "Send Test",
            "type" => "text",
            "cust_type" => "button",
        );
		$cs_setting_options[] = array(
            "type" => "division_close",
        );
        $cs_setting_options[] = array("col_heading" => __("SMTP Settings", "jobhunt"),
            "type" => "col-right-text",
            "help_text" => ""
        );
        // Payments Gateways
        $cs_setting_options[] = array(
            "name" => __("Gateways Settings", "jobhunt"),
            "id" => "tab-gateways-settings",
            "type" => "sub-heading"
        );
        $cs_gateways_id = CS_FUNCTIONS()->cs_rand_id();
		
		if ( class_exists( 'WooCommerce' ) ) {
			$cs_setting_options[] = array("name" => __("Woocommerce Payment Gateways", 'jobhunt'),
				"desc" => "",
				"hint_text" => __("Make it on to use the woocommerce payment gateways instead of builtin ones."),
				"id" => "use_woocommerce_gateway",
				"std" => "off",
				"type" => "checkbox",
				"onchange" => "use_wooC_gateways(this.name)",
				"options" => $on_off_option
			);
			
			$cs_setting_options[] = array(
				"type" => "division",
				"enable_id" => "cs_use_woocommerce_gateway",
				"enable_val" => "",
				"extra_atts" => 'id="cs-no-wooC-gateway-div"',
			);
		}
        foreach ($gateways as $key => $value) {
            if (class_exists($key)) {
                $settings = new $key();
                $cs_settings = $settings->settings($cs_gateways_id);
                foreach ($cs_settings as $key => $params) {
                    $cs_setting_options[] = $params;
                }
            }
        }
		if ( class_exists( 'WooCommerce' ) ) {
			$cs_setting_options[] = array(
				"type" => "division_close",
			);
		}
		
		
        $cs_setting_options[] = array("col_heading" => __("Packages", "jobhunt"),
            "type" => "col-right-text",
            "help_text" => ""
        );
        // Packages
        $cs_setting_options[] = array("name" => __("Job Credit", "jobhunt"),
            "id" => "tab-job-pkgs",
            "type" => "sub-heading"
        );
        $cs_setting_options[] = array("name" => __("Job Credit", "jobhunt"),
            "desc" => "",
            "hint_text" => __("Add/Edit Packages", "jobhunt"),
            "id" => "cs-job-packages",
            "std" => '',
            "type" => "packages"
        );
        $cs_setting_options[] = array("col_heading" => __("Job Credit", "jobhunt"),
            "type" => "col-right-text",
            "help_text" => ""
        );
        $cs_setting_options[] = array("name" => __("CV Search", "jobhunt"),
            "id" => "tab-cv-pkgs",
            "type" => "sub-heading"
        );
        $cs_setting_options[] = array("name" => __("CV Search", "jobhunt"),
            "desc" => "",
            "hint_text" => __("Add/Edit Packages", "jobhunt"),
            "id" => "cs-cv-packages",
            "std" => '',
            "type" => "cv_pkgs"
        );
        $cs_setting_options[] = array("col_heading" => __("CV Search", "jobhunt"),
            "type" => "col-right-text",
            "help_text" => ""
        );



        $cs_setting_options[] = array("name" => __("Featured Jobs", "jobhunt"),
            "id" => "tab-featured_jobs",
            "type" => "sub-heading"
        );
        //content box heading
        $cs_setting_options[] = array("name" => __('Featured Jobs', 'jobhunt'),
            "id" => "tab-settings-featured-jobs",
            "std" => __('Featured Jobs', 'jobhunt'),
            "type" => "section",
            "options" => ""
        );
        $cs_setting_options[] = array("name" => __("Feature Price", 'jobhunt'),
            "desc" => "",
            "hint_text" => __("Set price for a featured Job.", 'jobhunt'),
            "id" => "job_feat_price",
            "std" => "",
            "type" => "text",
        );
        $cs_setting_options[] = array("name" => __("Feature Price Text", 'jobhunt'),
            "desc" => "",
            "hint_text" => __("Add text for user to describe the detail and advantages of featured job.", "jobhunt"),
            "id" => "job_feat_txt",
            "std" => "",
            "type" => "textarea",
        );
        $cs_setting_options[] = array("name" => __("Payment Text", 'jobhunt'),
            "desc" => "",
            "hint_text" => __('Set text for featured job payment confirmation. The text will show when user will complete featured job payment.', 'jobhunt'),
            "id" => "job_pay_txt",
            "std" => "",
            "type" => "textarea",
        );
        $cs_setting_options[] = array("col_heading" => __("Payment Text", "jobhunt"),
            "type" => "col-right-text",
            "hint_text" => __("Here you can add payment text whatever you want it will show up just under payment gateways while paying  for job.", "jobhunt"),
            "help_text" => ""
        );
        // Custom Fields
        $cs_setting_options[] = array(
            "name" => __("Jobs Fields", "jobhunt"),
            "id" => "tab-cusfields-jobs",
            "type" => "sub-heading"
        );
        $cs_setting_options[] = array("name" => __("Jobs Custom Fields", "jobhunt"),
            "id" => "tab-user-settings",
            "std" => __("Jobs Custom Fields", "jobhunt"),
            "type" => "section",
            "options" => ""
        );
        $cs_setting_options[] = array("name" => __("Custom Fields", "jobhunt"),
            "desc" => "",
            "hint_text" => "",
            "id" => "cs-custom-fields",
            "std" => "",
            "type" => "custom_fields",
        );
        $cs_setting_options[] = array("col_heading" => __("Custom Fields", "jobhunt"),
            "type" => "col-right-text",
            "help_text" => ""
        );
        // Candidates
        $cs_setting_options[] = array(
            "name" => __("Candidates Fields", "jobhunt"),
            "id" => "tab-cusfields-candidates",
            "type" => "sub-heading"
        );
        $cs_setting_options[] = array("name" => __("Candidates Custom Fields", "jobhunt"),
            "id" => "tab-user-settings",
            "std" => __('Candidates Custom Fields', 'jobhunt'),
            "type" => "section",
            "options" => ""
        );
        $cs_setting_options[] = array("name" => __("Candidates Fields", "jobhunt"),
            "desc" => "",
            "hint_text" => "",
            "id" => "cs-custom-fields",
            "std" => "",
            "type" => "candidate_custom_fields",
        );
        $cs_setting_options[] = array("col_heading" => __("Candidates Fields", "jobhunt"),
            "type" => "col-right-text",
            "help_text" => ""
        );
        // Employer
        $cs_setting_options[] = array(
            "name" => __("Recruiters Fields", "jobhunt"),
            "id" => "tab-cusfields-employers",
            "type" => "sub-heading"
        );
        $cs_setting_options[] = array("name" => __("Recruiters Custom Fields", "jobhunt"),
            "id" => "tab-user-settings",
            "std" => __("Recruiters Custom Fields", "jobhunt"),
            "type" => "section",
            "options" => ""
        );
        $cs_setting_options[] = array("name" => __("Custom Fields", "jobhunt"),
            "desc" => "",
            "hint_text" => "",
            "id" => "cs-custom-fields",
            "std" => "",
            "type" => "employer_custom_fields",
        );
        $cs_setting_options[] = array("col_heading" => __("Recruiters Fields", "jobhunt"),
            "type" => "col-right-text",
            "help_text" => ""
        );
        $cs_setting_options[] = array(
            "name" => __("Api Settings", "jobhunt"),
            "id" => "tab-api-setting",
            "type" => "sub-heading"
        );
        $cs_setting_options[] = array(
            "name" => __("Twitter", 'jobhunt'),
            "id" => "Twitter",
            "std" => "Twitter",
            "type" => "section",
            "options" => ""
        );
        $cs_setting_options[] = array(
            "name" => __("Show Twitter", 'jobhunt'),
            "desc" => "",
            "hint_text" => __("Manage user registration via Twitter here. If this switch is set ON, users will be able to sign up / sign in with Twitter. If it will be OFF, users will not be able to register / sign in through Twitter.", 'jobhunt'),
            "id" => "twitter_api_switch",
            "std" => "on",
            "type" => "checkbox"
        );
        $cs_setting_options[] = array(
            "name" => __("Consumer Key", 'jobhunt'),
            "desc" => "",
            "hint_text" => __("Insert Twitter Consumer Key here. When you create your Twitter App, you will get this key.", "jobhunt"),
            "id" => "consumer_key",
            "std" => "",
            "type" => "text"
        );
        $cs_setting_options[] = array(
            "name" => __("Consumer Secret", 'jobhunt'),
            "desc" => "",
            "hint_text" => __("Insert Twitter Consumer secret here. When you create your Twitter App, you will get this key.", "jobhunt"),
            "id" => "consumer_secret",
            "std" => "",
            "type" => "text"
        );
        $cs_setting_options[] = array(
            "name" => __("Access Token", 'jobhunt'),
            "desc" => "",
            "hint_text" => __("Insert Twitter Access Token for permissions. When you create your Twitter App, you will get this Token", 'jobhunt'),
            "id" => "access_token",
            "std" => "",
            "type" => "text"
        );
        $cs_setting_options[] = array(
            "name" => __("Access Token Secret", 'jobhunt'),
            "desc" => "",
            "hint_text" => __("Insert Twitter Access Token Secret here. When you create your Twitter App, you will get this Token", 'jobhunt'),
            "id" => "access_token_secret",
            "std" => "",
            "type" => "text"
        );
        //end Twitter Api		
        $cs_setting_options[] = array(
            "name" => __("Facebook", 'jobhunt'),
            "id" => "Facebook",
            "std" => "Facebook",
            "type" => "section",
            "options" => ""
        );
        $cs_setting_options[] = array(
            "name" => __("Facebook Login On/Off", 'jobhunt'),
            "desc" => "",
            "hint_text" => __("Manage user registration via Facebook here. If this switch is set ON, users will be able to sign up / sign in with Facebook. If it will be OFF, users will not be able to register / sign in through Facebook.", 'jobhunt'),
            "id" => "facebook_login_switch",
            "std" => "on",
            "type" => "checkbox",
            "options" => $on_off_option
        );
        $cs_setting_options[] = array(
            "name" => __("Facebook Application ID", 'jobhunt'),
            "desc" => "",
            "hint_text" => __("Here you have to add your Facebook application ID. You will get this ID when you create Facebook App.", 'jobhunt'),
            "id" => "facebook_app_id",
            "std" => "",
            "type" => "text"
        );
        $cs_setting_options[] = array(
            "name" => __("Facebook Secret", 'jobhunt'),
            "desc" => "",
            "hint_text" => __("Put your Facebook Secret here. You can find it in your Facebook Application Dashboard", 'jobhunt'),
            "id" => "facebook_secret",
            "std" => "",
            "type" => "text"
        );
        //end facebook api
        //start linkedin api
        $cs_setting_options[] = array(
            "name" => __("Linked-in", 'jobhunt'),
            "id" => "Linked-in",
            "std" => "Linked-in",
            "type" => "section",
            "options" => ""
        );
        $cs_setting_options[] = array(
            "name" => __("Linked-in Login On/Off", 'jobhunt'),
            "desc" => "",
            "hint_text" => __("Manage user registration via Linked-in here. If this switch is set ON, users will be able to sign up / sign in with Linked-in. If it will be OFF, users will not be able to register / sign in through Linked-in.", 'jobhunt'),
            "id" => "linkedin_login_switch",
            "std" => "on",
            "type" => "checkbox",
            "options" => $on_off_option
        );
        $cs_setting_options[] = array(
            "name" => __("Linked-in Application Id", 'jobhunt'),
            "desc" => "",
            "hint_text" => __("Add LinkedIn application ID. To get your Linked-in Application ID, go to your Linked-in Dashboard", "jobhunt"),
            "id" => "linkedin_app_id",
            "std" => "",
            "type" => "text"
        );
        $cs_setting_options[] = array(
            "name" => __("Linked-in Secret", 'jobhunt'),
            "desc" => "",
            "hint_text" => __("Put your Linked-in Secret here. You can find it in your Linked-in Application Dashboard", 'jobhunt'),
            "id" => "linkedin_secret",
            "std" => "",
            "type" => "text"
        );
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $protocol = 'https';
        } else {
            $protocol = 'http';
        }
        $cs_setting_options[] = array(
            "name" => __("Linked-in Application Redirect URI", 'jobhunt'),
            "desc" => "",
            "hint_text" => __("Please add this URL into your Linked-in App settings as redirect uri", "jobhunt"),
            "id" => "linkedin_app_redirect_uri",
            "std" => site_url(),
            "type" => "text",
            "active" =>"in-active",
        );
        //end linkedin api
        //start google api
        $cs_setting_options[] = array(
            "name" => __("Google", 'jobhunt'),
            "id" => "Google",
            "std" => "Google+",
            "type" => "section",
            "options" => ""
        );
        $cs_setting_options[] = array(
            "name" => __("Google+ Login On/Off", 'jobhunt'),
            "desc" => "",
            "hint_text" => __("Manage user registration via Google+ here. If this switch is set ON, users will be able to sign up / sign in with Google+. If it will be OFF, users will not be able to register / sign in through Google+.", 'jobhunt'),
            "id" => "google_login_switch",
            "std" => "on",
            "type" => "checkbox",
            "options" => $on_off_option
        );
        $cs_setting_options[] = array(
            "name" => __("Google+ Client ID", 'jobhunt'),
            "desc" => "",
            "hint_text" => __("Put your Google+ client ID here.  To get this ID, go to your Google+ account Dashboard", 'jobhunt'),
            "id" => "google_client_id",
            "std" => "",
            "type" => "text"
        );
        $cs_setting_options[] = array(
            "name" => __("Google+ Client Secret", 'jobhunt'),
            "desc" => "",
            "hint_text" => __("Put your google+ client secret here.  To get client secret, go to your Google+ account", 'jobhunt'),
            "id" => "google_client_secret",
            "std" => "",
            "type" => "text"
        );
        $cs_setting_options[] = array(
            "name" => __("Google+ API key", 'jobhunt'),
            "desc" => "",
            "hint_text" => __('Put your Google+ API key here.  To get API, go to your Google+ account', 'jobhunt'),
            "id" => "google_api_key",
            "std" => "",
            "type" => "text"
        );
        $cs_setting_options[] = array(
            "name" => __("Fixed redirect url for login", 'jobhunt'),
            "desc" => "",
            "hint_text" => __('Put your google+ redirect url here.', 'jobhunt'),
            "id" => "google_login_redirect_url",
            "std" => "",
            "type" => "text"
        );
        //end google api
        // captcha settings
        $cs_setting_options[] = array(
            "name" => __("Captcha", 'jobhunt'),
            "id" => "Captcha",
            "std" => "Captcha",
            "type" => "section",
            "options" => ""
        );
        $cs_setting_options[] = array("name" => __("Captcha", "jobhunt"),
            "desc" => "",
            "hint_text" => __("Manage your captcha code for secured Signup here. If this switch will be ON, user can register after entering Captcha code. It helps to avoid robotic / spam sign-up", 'jobhunt'),
            "id" => "captcha_switch",
            "std" => "on",
            "type" => "checkbox",
            "options" => $on_off_option
        );
        $cs_setting_options[] = array(
            "name" => __("Site Key", "jobhunt"),
            "desc" => "",
            "hint_text" => __("Put your site key for captcha. You can get this site key after registering your site on Google.", "jobhunt"),
            "id" => "sitekey",
            "std" => "",
            "type" => "text",
        );
        $cs_setting_options[] = array(
            "name" => __("Secret Key", "jobhunt"),
            "desc" => "",
            "hint_text" => __("Put your site Secret key for captcha. You can get this Secret Key after registering your site on Google.", "jobhunt"),
            "id" => "secretkey",
            "std" => "",
            "type" => "text",
        );
        $cs_setting_options[] = array("col_heading" => __("API Settings", "jobhunt"),
            "type" => "col-right-text",
            "help_text" => ""
        );
        // end captcha settings
        // Search Settings
        // Basic Search Settings
        $cs_setting_options[] = array(
            "name" => __("Searching Options", "jobhunt"),
            "id" => "tab-basic-settings",
            "type" => "sub-heading"
        );
        $cs_setting_options[] = array("name" => __('Searching Options', 'jobhunt'),
            "id" => "tab-settings-Searching-Options",
            "std" => __('Searching Options', 'jobhunt'),
            "type" => "section",
            "options" => ""
        );
        $cs_setting_options[] = array("name" => __("Location Search", "jobhunt"),
            "desc" => "",
            "hint_text" => __("Use this Option to Enable/Disable Location filters for frontend At Jobs, Candidate, Employer’s and job search element. ", "jobhunt"),
            "id" => "jobhunt_search_location",
            "std" => "on",
            "type" => "checkbox",
            "onchange" => "cs_search_view_change(this.name)",
            "options" => $on_off_option
        );
        $cs_setting_options[] = array(
            "type" => "division",
            "enable_id" => "cs_jobhunt_search_location",
            "enable_val" => "on",
            "extra_atts" => 'id="cs_search_view_area"',
        );
        $cs_setting_options[] = array("name" => __("Google Auto complete", "jobhunt"),
            "desc" => "",
            "hint_text" => __("When a user will type a part of any address, this option will auto-complete the remaining.*This option will only work if 'Location Search' is enabled. ", "jobhunt"),
            "id" => "google_autocomplete_enable",
            "std" => "on",
            "type" => "checkbox",
            "options" => $on_off_option
        );
        $cs_setting_options[] = array("name" => __("Enable Geo Location", "jobhunt"),
            "desc" => "",
            "hint_text" => __("Geo Location will help users to find jobs in their area.**This option will only work if 'Location Search' is enabled.", "jobhunt"),
            "id" => "geo_location",
            "std" => "on",
            "type" => "checkbox",
            "options" => $on_off_option
        );
        $cs_setting_options[] = array("name" => __("Enable Radius", "jobhunt"),
            "desc" => "",
            "hint_text" => __("This Option will help users to filter jobs with radius.**This option will only work if location search is enabled. ", "jobhunt"),
            "id" => "radius_switch",
            "std" => "on",
            "type" => "checkbox",
            "options" => $on_off_option
        );
        $cs_setting_options[] = array(
            "name" => __("Radius Inputs", "jobhunt"),
            "id" => "radius_min",
            "id2" => "radius_max",
            "id3" => "radius_step",
            "std" => "0",
            "std2" => "500",
            "std3" => "20",
            "placeholder" => __("Min Value", "jobhunt"),
            "placeholder2" => __("Max Value", "jobhunt"),
            "placeholder3" => __("Increment Step", "jobhunt"),
            "hint_text" => __("Use this field to add radius inputs minimum to maximum. **This option wil only work if location search is enabled.", "jobhunt"),
            "desc" => "",
            "type" => "text3",
        );
        $cs_setting_options[] = array(
            "name" => __("Default Radius", "jobhunt"),
            "id" => "default_radius",
            "std" => "200",
            "hint_text" => __("When a user will filter jobs with any address, this radius will be implemented as default. **This option will only work if location search is enabled.", "jobhunt"),
            "desc" => "",
            "type" => "text",
        );
        $cs_setting_options[] = array("name" => __("Radius Measurement", 'jobhunt'),
            "desc" => "",
            "hint_text" => __("Set radius Measurement unit from drop down (km/miles) in which users will search.** This option will only work if location search is enabled", "jobhunt"),
            "id" => "radius_measure",
            "std" => "",
            "type" => "select_values",
            'classes' => 'chosen-select-no-single',
            "options" => array(
                'miles' => __('Miles', 'jobhunt'),
                'km' => __('KM', 'jobhunt')
            ),
        );
        $cs_setting_options[] = array("name" => __("Search By Location", 'jobhunt'),
            "desc" => "",
            "hint_text" => __("Use this option to set search by location with given option (country, City etc ) in the dropdown. There are limited options for search which are given in drop down. No extra parameter can be set for search with location. *This option will only work if location search is enabled", 'jobhunt'),
            "id" => "search_by_location",
            "std" => "",
            "type" => "select_values",
            'classes' => 'chosen-select-no-single',
            "extra_atts" => ' onchange="cs_single_city_change(this.value)"',
            "options" => array(
                "countries_only" => __("Countries only", 'jobhunt'),
                "countries_and_cities" => __("Countries and Cities", 'jobhunt'),
                "cities_only" => __("Cities only", 'jobhunt'),
                "single_city" => __("Single City", 'jobhunt'),
            )
        );
        $cs_location_countries = get_option('cs_location_countries');
        $states_list = get_option('cs_location_states');
        $cities_list = get_option('cs_location_cities');
        $cities_array = array();
        $cities_array[''] = __('Select City', 'jobhunt');
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
        if (isset($cs_location_countries) && !empty($cs_location_countries)) {
            foreach ($cs_location_countries as $key => $country) {
                // load all cities against state  
                $cities = '';
                $selected_spec = get_term_by('slug', $country->slug, 'cs_locations');
                $city_parent_id = $selected_spec->term_id;
                $cities_args = array(
                    'orderby' => 'name',
                    'order' => 'ASC',
                    'fields' => 'all',
                    'slug' => '',
                    'hide_empty' => false,
                    'parent' => $city_parent_id,
                );
                $cities = get_terms('cs_locations', $cities_args);
                if (isset($cities) && $cities != '' && is_array($cities)) {
                    foreach ($cities as $key => $city) {
                        $cities_array[$city->slug] = $city->name;
                    }
                }
            }
        }

        $cs_setting_options[] = array(
            "type" => "division",
            "enable_id" => "cs_search_by_location",
            "enable_val" => "single_city",
            "extra_atts" => 'id="cs_single_city_area"',
        );

        $cs_setting_options[] = array("name" => __("Select City", 'jobhunt'),
            "desc" => "",
            "hint_text" => __("If your above 'Search By Location' option will be 'single city' then you must have to select city from the dropdown.", "jobhunt"),
            "id" => "",
            "std" => "",
            'classes' => 'chosen-select-no-single',
            "type" => "select_values",
            "options" => $cities_array,
        );
        $cs_setting_options[] = array(
            "type" => "division_close",
        );
        $cs_setting_options[] = array(
            "type" => "division_close",
        );
        $cs_setting_options[] = array("col_heading" => __("SEARCH OPTIONS", "jobhunt"),
            "type" => "col-right-text",
            "help_text" => ""
        );
        /* social Network setting */
        $cs_setting_options[] = array("name" => __("social Sharing", 'jobhunt'),
            "id" => "tab-social-icons",
            "type" => "sub-heading"
        );
        $cs_setting_options[] = array("name" => __("Facebook", 'jobhunt'),
            "desc" => "",
            "hint_text" => "",
            "id" => "facebook_share",
            "std" => "on",
            "type" => "checkbox");
        $cs_setting_options[] = array("name" => __("Twitter", 'jobhunt'),
            "desc" => "",
            "hint_text" => "",
            "id" => "twitter_share",
            "std" => "on",
            "type" => "checkbox");
        $cs_setting_options[] = array("name" => __("Google Plus", 'jobhunt'),
            "desc" => "",
            "hint_text" => "",
            "id" => "google_plus_share",
            "std" => "on",
            "type" => "checkbox");
        $cs_setting_options[] = array("name" => __("Pinterest", 'jobhunt'),
            "desc" => "",
            "hint_text" => "",
            "id" => "pintrest_share",
            "std" => "on",
            "type" => "checkbox"
        );
        $cs_setting_options[] = array("name" => __("Tumblr", 'jobhunt'),
            "desc" => "",
            "hint_text" => "",
            "id" => "tumblr_share",
            "std" => "on",
            "type" => "checkbox");
        $cs_setting_options[] = array("name" => __("Dribbble", 'jobhunt'),
            "desc" => "",
            "hint_text" => "",
            "id" => "dribbble_share",
            "std" => "off",
            "type" => "checkbox");
        $cs_setting_options[] = array("name" => __("Instagram", 'jobhunt'),
            "desc" => "",
            "hint_text" => "",
            "id" => "instagram_share",
            "std" => "on",
            "type" => "checkbox");
        $cs_setting_options[] = array("name" => __("StumbleUpon", 'jobhunt'),
            "desc" => "",
            "hint_text" => "",
            "id" => "stumbleupon_share",
            "std" => "on",
            "type" => "checkbox");
        $cs_setting_options[] = array("name" => __("youtube", 'jobhunt'),
            "desc" => "",
            "hint_text" => "",
            "id" => "youtube_share",
            "std" => "on",
            "type" => "checkbox");
        $cs_setting_options[] = array("name" => __("share more", 'jobhunt'),
            "desc" => "",
            "hint_text" => "",
            "id" => "share_share",
            "std" => "off",
            "type" => "checkbox");
        /* social network end */

        $cs_setting_options[] = array("col_heading" => __("Social Icon", "jobhunt"),
            "type" => "col-right-text",
            "help_text" => ""
        );

        // Jobsline Add-ons.
        /**
         * Apply the filters by calling the 'cs_jobhunt_plugin_addons_options' function we
         * "hooked" to 'cs_jobhunt_plugin_addons_options' using the add_filter() function above.
         */
        $cs_setting_options = apply_filters('cs_jobhunt_plugin_addons_options', $cs_setting_options);
        // End Jobsline Add-ons.

        $cs_setting_options[] = array("name" => __("import & export", 'jobhunt'),
            "fontawesome" => 'icon-database',
            "id" => "tab-import-export-options",
            "std" => "",
            "type" => "main-heading",
            "options" => ""
        );
        $cs_setting_options[] = array("name" => __("import & export", 'jobhunt'),
            "id" => "tab-import-export-options",
            "type" => "sub-heading"
        );


        $cs_setting_options[] = array("name" => __("Backup", "jobhunt"),
            "desc" => "",
            "hint_text" => '',
            "id" => "backup_options",
            "std" => "",
            "type" => "generate_backup"
        );

        $cs_setting_options[] = array(
            "name" => __("Users Import / Export", 'jobhunt'),
            "id" => "user-import-export",
            "std" => __("Users Import / Export", 'jobhunt'),
            "type" => "section",
            "options" => ""
        );
        $cs_setting_options[] = array(
            "name" => __("Import Users Data", 'jobhunt'),
            "desc" => "",
            "hint_text" => '',
            "id" => "backup_options",
            "std" => "",
            "type" => "user_import_export",
        );

        $cs_setting_options[] = array("col_heading" => __("import & export", "jobhunt"),
            "type" => "col-right-text",
            "help_text" => ""
        );

        update_option('cs_plugin_data', $cs_setting_options);
    }

}
$output = '';
$output .= '</div>';
