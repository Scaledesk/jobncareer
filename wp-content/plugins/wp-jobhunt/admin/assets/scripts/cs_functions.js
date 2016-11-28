/*
 * hide page section
 */


var $ = jQuery;
jQuery(document).ready(function ($) {

    "use strict";
    /*
     * Media Upload 
     */
    var contheight;
    jQuery(document).on("click", ".uploadMedia", function () {
        var $ = jQuery;
        var id = $(this).attr("name");
        var custom_uploader = wp.media({
            title: 'Select File',
            button: {
                text: 'Add File'
            },
            multiple: false
        })
                .on('select', function () {
                    var attachment = custom_uploader.state().get('selection').first().toJSON();
                    jQuery('#' + id).val(attachment.url);
                    jQuery('#' + id + '_img').attr('src', attachment.url);
                    jQuery('#' + id + '_box').show();
                }).open();

    });

//load role related profile fields
    jQuery(document).on("change", "#role", function () {
        var selected_role = jQuery(this).find(":selected").val();
        if (selected_role == 'cs_candidate') {
            //jQuery("#employer_tab").hide();
            // jQuery("#candidate_tab").show();
            jQuery(".cs-user-customfield-block").show();
            jQuery(".cs-employer-fields").hide();
            jQuery(".cs-candidate-fields").show();
        } else if (selected_role == 'cs_employer') {
            jQuery(".cs-user-customfield-block").show();
            jQuery(".cs-candidate-fields").hide();
            jQuery(".cs-employer-fields").show();
            // jQuery("#candidate_tab").hide();
            // jQuery("#employer_tab").show();
        } else {
           jQuery(".cs-user-customfield-block").hide();
           jQuery(".cs-employer-fields").hide();
            jQuery(".cs-candidate-fields").hide();
            //  jQuery("#candidate_tab").hide();
            // jQuery("#employer_tab").hide();
        }

    });



    /*
     * hide page section
     */

    var myUrl = window.location.href; //get URL
    var myUrlTab = myUrl.substring(myUrl.indexOf("#")); // For localhost/tabs.html#tab2, myUrlTab = #tab2     
    var myUrlTabName = myUrlTab.substring(0, 4); // For the above example, myUrlTabName = #tab
    jQuery("#tabbed-content > div").addClass('hidden-tab'); // Initially hide all content #####EDITED#####
    jQuery("#cs-options-tab li:first a").attr("id", "current"); // Activate first tab
    jQuery("#tabbed-content > div:first").hide().removeClass('hidden-tab').fadeIn(); // Show first tab content   #####EDITED#####
    jQuery("#cs-options-tab > li:first").addClass('active');

    jQuery("#cs-options-tab a").on("click", function (e) {
        e.preventDefault();
        if (jQuery(this).attr("id") == "current") { //detection for current tab
            return
        } else {
            resetTabs();
            console.log(this);
            jQuery("#cs-options-tab > li").removeClass('active')
            jQuery(this).attr("id", "current"); // Activate this
            jQuery(this).parents('li').addClass('active');
            jQuery(jQuery(this).attr('name')).hide().removeClass('hidden-tab').fadeIn(); // Show content for current tab
        }
    });

    if (jQuery("#cs-options-tab li").length > 0) {
        for (var i = 1; i <= jQuery("#cs-options-tab li").length; i++) {
            if (myUrlTab == myUrlTabName + i) {
                resetTabs();
                jQuery("a[name='" + myUrlTab + "']").attr("id", "current"); // Activate url tab
                jQuery(myUrlTab).hide().removeClass('hidden-tab').fadeIn(); // Show url tab content        
            }
        }
    }


    // End here
    jQuery(document).on('click', '#wrapper_boxed_layoutoptions1', function (event) {

        var theme_option_layout = jQuery('#wrapper_boxed_layoutoptions1 input[name=layout_option]:checked').val();
        if (theme_option_layout == 'wrapper_boxed') {
            jQuery("#layout-background-theme-options").show();
        } else {
            jQuery("#layout-background-theme-options").hide();
        }
    });
    jQuery(document).on('click', '#wrapper_boxed_layoutoptions2', function (event) {
        var theme_option_layout = jQuery('#wrapper_boxed_layoutoptions2 input[name=layout_option]:checked').val();
        if (theme_option_layout == 'wrapper_boxed') {
            jQuery("#layout-background-theme-options").show();
        } else {
            jQuery("#layout-background-theme-options").hide();

        }

    });
    /*
     * textarea header_code_indent
     */

    jQuery('textarea.header_code_indent').keydown(function (e) {
        if (e.keyCode == 9) {
            var start = $(this).get(0).selectionStart;
            $(this).val($(this).val().substring(0, start) + "    " + $(this).val().substring($(this).get(0).selectionEnd));
            $(this).get(0).selectionStart = $(this).get(0).selectionEnd = start + 4;
            return false;
        }
    });
    /*
     * Toggle Function
     */

    jQuery(".hidediv").hide();
    jQuery(document).on('click', '.showdiv', function (event) {
        jQuery(this).parents("article").stop().find(".hidediv").toggle(300);
    });





});


/*
 * upload file url
 */

jQuery(document).on('click', 'uploadfileurl', function () {
    var $ = jQuery;
    var id = $(this).attr("name");
    var custom_uploader = wp.media({
        title: 'Select File',
        button: {
            text: 'Add File'
        },
        multiple: false
    })
            .on('select', function () {
                var attachment = custom_uploader.state().get('selection').first().toJSON();
                jQuery('#' + id).val(attachment.url);
            }).open();

});
/*
 * reset tabs
 */
function resetTabs() {
    jQuery("#tabbed-content > div").addClass('hidden-tab'); //Hide all content
    jQuery("#cs-options-tab a").attr("id", ""); //Reset id's      
}
/*
 * del media
 */
function del_media(id) {
    var $ = jQuery;
    jQuery('input[name="' + id + '"]').show();
    jQuery('#' + id + '_box').hide();
    jQuery('#' + id).val('');
    jQuery('#' + id).next().show();
}
/*
 * toggle with value
 */
function toggle_with_value(id) {
    if (id == 0) {
        jQuery("#wrapper_repeat_event").hide();
    } else {
        jQuery("#wrapper_repeat_event").show();
    }
}

/*
 * chosen selection box
 */

function chosen_selectionbox() {
    if (jQuery('.chosen-select, .chosen-select-deselect, .chosen-select-no-single, .chosen-select-no-results, .chosen-select-width').length != '') {
        var config = {
            '.chosen-select': {width: "100%"},
            '.chosen-select-deselect': {allow_single_deselect: true},
            '.chosen-select-no-single': {disable_search_threshold: 10, width: "100%"},
            '.chosen-select-no-results': {no_results_text: 'Oops, nothing found!'},
            '.chosen-select-width': {width: "95%"}
        }
        for (var selector in config) {
            jQuery(selector).chosen(config[selector]);
        }
    }
}

/*
 * gllsearch map
 */
function gll_search_map() {
    var vals;
    vals = jQuery('#cs_location_address').val();
    jQuery('.gllpSearchField').val(vals);
}
/*
 * remove image
 */
function remove_image(id) {
    var $ = jQuery;
    $('#' + id).val('');
    $('#' + id + '_img_div').hide();
    //$('#'+id+'_div').attr('src', '');
}
/*
 * slideout
 */
function slideout() {
    setTimeout(function () {
        jQuery(".form-msg").slideUp("slow", function () {
        });
    }, 5000);
}
/*
 * div remove
 */
function cs_div_remove(id) {
    jQuery("#" + id).remove();
}
/*
 * cs_toggle
 */
function cs_toggle(id) {
    jQuery("#" + id).slideToggle("slow");
}
/*
 * cs_toggle_height
 */
function cs_toggle_height(value, id) {
    var $ = jQuery;
    if (value == "Post Slider") {
        jQuery("#post_slider" + id).show();
        jQuery("#choose_slider" + id).hide();
        jQuery("#layer_slider" + id).hide();
        jQuery("#show_post" + id).show();
    } else if (value == "Flex Slider") {
        jQuery("#choose_slider" + id).show();
        jQuery("#layer_slider" + id).hide();
        jQuery("#post_slider" + id).hide();
        jQuery("#show_post" + id).hide();
    } else if (value == "Custom Slider") {
        jQuery("#layer_slider" + id).show();
        jQuery("#choose_slider" + id).hide();
        jQuery("#post_slider" + id).hide();
        jQuery("#show_post" + id).hide();
    } else {
        jQuery("#" + id).removeClass("no-display");
        jQuery("#post_slider" + id).show();
        jQuery("#choose_slider" + id).hide();
        jQuery("#layer_slider" + id).hide();
        jQuery("#show_post" + id).hide();
    }
}
/*
 * cs_toggle_list
 */
function cs_toggle_list(value, id) {
    var $ = jQuery;

    if (value == "custom_icon") {
        jQuery("#" + id).addClass("no-display");
        jQuery("#cs_list_icon").show();
    } else {
        jQuery("#" + id).removeClass("no-display");
        jQuery("#cs_list_icon").hide();
    }
}
/*
 * cs_counter_image
 */
function cs_counter_image(value, id) {
    var $ = jQuery;

    if (value == "icon") {
        jQuery(".selected_image_type" + id).hide();
        jQuery(".selected_icon_type" + id).show();
    } else {
        jQuery(".selected_image_type" + id).show();
        jQuery(".selected_icon_type" + id).hide();
    }

}
/*
 * cs_counter_view_type
 */
function cs_counter_view_type(value, id) {
    var $ = jQuery;

    if (value == "icon-border") {
        jQuery("#selected_view_icon_type" + id).hide();
        jQuery("#selected_view_border_type" + id).show();
        jQuery("#selected_view_icon_image_type" + id).hide();
        jQuery("#selected_view_icon_icon_type" + id).show();
    } else {
        jQuery("#selected_view_icon_type" + id).show();
        jQuery("#selected_view_border_type" + id).hide();
        jQuery("#selected_view_icon_image_type" + id).show();
    }

}

/*
 * cs_service_toggle_image
 */
function cs_service_toggle_image(value, id, object) {
    var $ = jQuery;
    var selectedValue = $('#cs_service_type-' + id).val();
    if (value == "image") {
        jQuery("#modern-size-" + id).hide();
        jQuery("#selected_image_type" + id).show();
        jQuery("#selected_icon_type" + id).hide();

    } else if (value == "icon") {
        if (selectedValue == 'modern') {
            jQuery("#modern-size-" + id).show();
        } else {
            jQuery("#modern-size-" + id).hide();
        }

        jQuery("#selected_image_type" + id).hide();
        jQuery("#selected_icon_type" + id).show();
    }

}
/*
 * cs_service_toggle_view
 */
function cs_service_toggle_view(value, id, object) {
    var $ = jQuery;
    if (value == "modern") {
        jQuery("#cs-service-bg-color-" + id).show();
        jQuery("#modern-size-" + id).show();
        jQuery("#service-position-classic-" + id).hide();
        jQuery("#service-position-modern-" + id).show();
        jQuery("#cs-modern-bg-color-" + id + " #bg-service").html('Button bg Color');

    } else if (value == "classic") {
        jQuery("#modern-size-" + id).hide();
        jQuery("#cs-service-bg-color-" + id).hide();
        jQuery("#service-position-modern-" + id).hide();
        jQuery("#service-position-classic-" + id).show();
        jQuery("#cs-modern-bg-color-" + id + " #bg-service").html('Text Color');
    }

}
/*
 * cs_icon_toggle_view
 */
function cs_icon_toggle_view(value, id, object) {
    var $ = jQuery;
    if (value == "bg_style") {
        jQuery("#selected_icon_view_" + id + " #label-icon").html('Icon Background Color');

    } else if (value == "border_style") {
        jQuery("#selected_icon_view_" + id + " #label-icon").html('Border Color');
    }

}
/*
 * Counter Image Show Hide End
 */

/*
 * CPricetable Title Show Hide Start
 */

function cs_pricetable_style_vlaue(value, id) {
    var $ = jQuery;
    if (value == "classic") {
        jQuery("#pricetbale-title" + id).hide();
    } else {
        jQuery("#pricetbale-title" + id).show();
    }
}
/*
 * show_sidebar
 */
function show_sidebar(id, random_id) {
    "use strict";
    var $ = jQuery;
    jQuery(document).on('click', 'input[class="radio_cs_sidebar]', function (event) {
        jQuery(this).parent().parent().find(".check-list").removeClass("check-list");
        jQuery(this).siblings("label").children("#check-list").addClass("check-list");
    });
    var randomeID = "#" + random_id;
    if ((id == 'left') || (id == 'right')) {
        $(randomeID + "_sidebar_right," + randomeID + "_sidebar_left").hide();
        $(randomeID + "_sidebar_" + id).show();
    } else if ((id == 'both') || (id == 'none')) {
        $(randomeID + "_sidebar_right," + randomeID + "_sidebar_left").hide();
    }
}
/*
 * show_sidebar_page
 */
function show_sidebar_page(id) {
    var $ = jQuery;
    jQuery(document).on('click', 'input[name="cs_page_layout"]', function () {
        jQuery(this).parent().parent().find(".check-list").removeClass("check-list");
        jQuery(this).siblings("label").children("#check-list").addClass("check-list");
    });
    if ((id == 'left') || (id == 'right')) {
        $("#wrapper_sidebar_left,#wrapper_sidebar_right").hide();
        $("#wrapper_sidebar_" + id).show();
    } else if (id == 'both') {
        $("#wrapper_sidebar_right,#wrapper_sidebar_left").show();
    } else if (id == 'none') {
        $("#wrapper_sidebar_right,#wrapper_sidebar_left").hide();
    }
}
/*
 * cs_toggle_gal
 */

function cs_toggle_gal(id, counter) {
    if (id == 0) {
        jQuery("#link_url" + counter).hide();
        jQuery("#video_code" + counter).hide();
    } else if (id == 1) {
        jQuery("#link_url" + counter).hide();
        jQuery("#video_code" + counter).show();
    } else if (id == 2) {
        jQuery("#link_url" + counter).show();
        jQuery("#video_code" + counter).hide();
    }
}
/*
 * delete_this
 */
var counter = 0;
function delete_this(id) {
    jQuery('#' + id).remove();
    jQuery('#' + id + '_del').remove();
    count_widget--;
    if (count_widget < 1)
        jQuery("#add_page_builder_item").addClass("hasclass");
}

var Data = [
    {"Class": "column_100", "title": "100", "element": ["employer", "job_package","gallery","cv_package","jobs_search" ,"slider", "blog", "column", "accordions", "team", "client", "quotes", "contact", "column", "divider", "message_box", 'image', "image_frame", "map", "video", "slider", "dropcap", "pricetable", "tabs", "accordion", "advance_search", "prayer", "teams", "services", "table", "flex_column", "clients", "spacer", "heading", "testimonials", "infobox", "promobox", "offerslider", "audio", "icons", "contactform", "tooltip", "highlight", "list", "mesage", "faq", "counter", "members", "icon_box", "mailchimp", "facilities", "events", "team_post", "portfolio", "quick_slider", "flex_editor", "multi_price_table", "candidate", "employer", "jobs", "job_specialisms", "candidate", "tweets", "button", "call_to_action", "progressbars", "sitemap", "about", "multiple_team", "newsletter"]},
    {"Class": "column_75", "title": "75", "element": ["employer", "job_package","cv_package","jobs_search" , "slider", "blog", "column", "accordions", "team", "client", "quotes", "contact", "column", "divider", "message_box", 'image', "image_frame", "map", "video", "slider", "dropcap", "pricetable", "tabs", "accordion", "advance_search", "prayer", "teams", "services", "table", "flex_column", "clients", "heading", "infobox", "promobox", "offerslider", "audio", "icons", "contactform", "tooltip", "highlight", "list", "mesage", "faq", "counter", "members", "icon_box", "mailchimp", "facilities", "events", "team_post", "portfolio", "quick_slider", "flex_editor", "multi_price_table", "candidate", "employer", "jobs", "job_specialisms", "candidate", "tweets", "button", "call_to_action", "progressbars", "about", "multiple_team", "newsletter"]},
    {"Class": "column_67", "title": "67", "element": ["employer", "slider", "column", "contact", "column", "message_box", "slider", "dropcap", "tabs", "advance_search", "prayer", "services", "heading", "promobox", "offerslider", "audio", "icons", "tooltip", "highlight", "mesage", "counter", "members", "mailchimp", "facilities", "events", "team_post", "portfolio", "quick_slider", "about", "list"]},
    {"Class": "column_50", "title": "50", "element": ["employer", "job_package","cv_package", "jobs_search" ,"slider", "blog", "column", "accordions", "team", "client", "quotes", "contact", "column", "divider", "message_box", 'image', "image_frame", "map", "video", "slider", "dropcap", "pricetable", "tabs", "accordion", "advance_search", "prayer", "teams", "services", "table", "flex_column", "clients", "heading", "infobox", "promobox", "offerslider", "audio", "icons", "contactform", "tooltip", "highlight", "list", "mesage", "faq", "counter", "members", "icon_box", "mailchimp", "facilities", "events", "team_post", "portfolio", "quick_slider", "flex_editor", "multi_price_table", "candidate", "employer", "jobs", "job_specialisms", "candidate", "tweets", "button", "call_to_action", "progressbars", "about", "multiple_team", "newsletter"]},
    {"Class": "column_33", "title": "33", "element": ["employer", "slider", "column", "contact", "column", "message_box", "slider", "dropcap", "tabs", "advance_search", "prayer", "services", "heading", "promobox", "offerslider", "audio", "icons", "tooltip", "highlight", "mesage", "counter", "members", "mailchimp", "facilities", "events", "team_post", "portfolio", "quick_slider", "about", "list"]},
    {"Class": "column_25", "title": "25", "element": ["employer", "jobs_search" , "slider", "column", "accordions", "team", "quotes", "contact", "column", "divider", "message_box", 'image', "image_frame", "map", "video", "slider", "dropcap", "pricetable", "tabs", "accordion", "advance_search", "prayer", "teams", "services", "table", "flex_column", "heading", "infobox", "promobox", "offerslider", "audio", "icons", "contactform", "tooltip", "highlight", "list", "mesage", "faq", "counter", "members", "icon_box", "mailchimp", "facilities", "events", "team_post", "portfolio", "quick_slider", "flex_editor", "multi_price_table",  "tweets", "button", "call_to_action", "progressbars", "about", "multiple_team", "newsletter"]},
];

var DataElement = [{
        "ClassName": "col_width_full",
        "element": ["gallery", "slider", "blog", "events", "contact", "parallax","jobs_search"]
    }];


var _commonshortcode = (function (id) {
    var mainConitem = jQuery("#" + id)
    var totalItemCon = mainConitem.find(".cs-wrapp-clone").size();
    mainConitem.find(".fieldCounter").val(totalItemCon);
    mainConitem.sortable({
        cancel: '.cs-clone-append .form-elements,.cs-disable-true',
        placeholder: "ui-state-highlight"
    });

});
var counter_ingredient = 0;
var html_popup = "<div id='confirmOverlay' style='display:block'> \
								<div id='confirmBox'><div id='confirmText'>Are you sure to do this?</div> \
								<div id='confirmButtons'><div class='button confirm-yes'>Delete</div>\
								<div class='button confirm-no'>Cancel</div><br class='clear'></div></div></div>"


//page Section items delete start
jQuery(document).on("click", ".btndeleteitsection", function () {
    jQuery(this).parents(".parentdeletesection").addClass("warning");
    jQuery(this).parent().append(html_popup);

    jQuery(document).on('click', '.confirm-yes', function (event) {
        jQuery(this).parents(".parentdeletesection").fadeOut(400, function () {
            jQuery(this).remove();
        });
        jQuery("#confirmOverlay").remove();
        count_widget--;
        if (count_widget == 0)
            jQuery("#add_page_builder_item").removeClass("hasclass");
    });
    jQuery(document).on('click', '.confirm-no', function (event) {
        jQuery(this).parents(".parentdeletesection").removeClass("warning");
        jQuery("#confirmOverlay").remove();
    });
    return false;
});


//page Builder items delete start

jQuery(document).on("click", ".btndeleteit", function () {

    jQuery(this).parents(".parentdelete").addClass("warning");
    jQuery(this).parent().append(html_popup);

    jQuery(document).on('click', '.confirm-yes', function (event) {
        jQuery(this).parents(".parentdelete").fadeOut(400, function () {
            jQuery(this).remove();
        });

        jQuery(this).parents(".parentdelete").each(function () {
            var lengthitem = jQuery(this).parents(".dragarea").find(".parentdelete").size() - 1;
            jQuery(this).parents(".dragarea").find("input.textfld").val(lengthitem);
        });

        jQuery("#confirmOverlay").remove();
        count_widget--;
        if (count_widget == 0)
            jQuery("#add_page_builder_item").removeClass("hasclass");

    });
    jQuery(document).on('click', '.confirm-no', function (event) {
        jQuery(this).parents(".parentdelete").removeClass("warning");
        jQuery("#confirmOverlay").remove();
    });

    return false;
});

/*
 * page Builder items delete end
 */

/*
 * adding social network start
 */

function social_icon_del(id) {
    jQuery("#del_" + id).remove();
    jQuery("#" + id).remove();
}

/*
 * Sidebar Layout
 */

function cs_slider_element_toggle(id) {
    if (id == 'default_header') {
        jQuery("#wrapper_default_header").hide();
        jQuery("#wrapper_breadcrumb_header").hide();
        jQuery("#wrapper_custom_slider").hide();
        jQuery("#wrapper_map").hide();
        jQuery("#wrapper_no-header").hide();
    } else if (id == 'custom_slider') {
        jQuery("#wrapper_custom_slider").show();
        jQuery("#wrapper_default_header").hide();
        jQuery("#wrapper_breadcrumb_header").hide();
        jQuery("#wrapper_map").hide();
        jQuery("#wrapper_no-header").hide();
    } else if (id == 'no-header') {
        jQuery("#wrapper_no-header").show();
        jQuery("#wrapper_default_header").hide();
        jQuery("#wrapper_breadcrumb_header").hide();
        jQuery("#wrapper_custom_slider").hide();
        jQuery("#wrapper_map").hide();
    } else if (id == 'breadcrumb_header') {
        jQuery("#wrapper_breadcrumb_header").show();
        jQuery("#wrapper_default_header").show();
        jQuery("#wrapper_custom_slider").hide();
        jQuery("#wrapper_map").hide();
        jQuery("#wrapper_no-header").hide();
    } else if (id == 'map') {
        jQuery("#wrapper_map").show();
        jQuery("#wrapper_default_header").hide();
        jQuery("#wrapper_breadcrumb_header").hide();
        jQuery("#wrapper_custom_slider").hide();
        jQuery("#wrapper_no-header").hide();
    } else {
        jQuery("#wrapper_default_header").hide();
        jQuery("#wrapper_breadcrumb_header").hide();
        jQuery("#wrapper_custom_slider").hide();
        jQuery("#wrapper_map").hide();
        jQuery("#wrapper_no-header").hide();
    }

}

/*
 * toggle hide/show
 */
function cs_hide_show_toggle(id, div, type) {

    if (type == 'theme_options') {
        if (id == 'default') {
            jQuery("#cs_sh_paddingtop_range").hide();
            jQuery("#cs_sh_paddingbottom_range").hide();
        } else if (id == 'custom') {
            jQuery("#cs_sh_paddingtop_range").show();
            jQuery("#cs_sh_paddingbottom_range").show();
        }

    } else {
        if (id == 'default') {
            jQuery("#" + div).hide();
        } else if (id == 'custom') {
            jQuery("#" + div).show();
        }
    }
}

/*
 * background options
 */

function cs_section_background_settings_toggle(id, rand_no) {

    if (id == "no-image") {
        jQuery(".section-custom-background-image-" + rand_no).hide();
        jQuery(".section-slider-" + rand_no).hide();
        jQuery(".section-custom-slider-" + rand_no).hide();
        jQuery(".section-background-video-" + rand_no).hide();
    } else if (id == "section-custom-background-image") {
        jQuery(".section-slider-" + rand_no).hide();
        jQuery(".section-custom-slider-" + rand_no).hide();
        jQuery(".section-background-video-" + rand_no).hide();
        jQuery(".section-custom-background-image-" + rand_no).show();
    } else if (id == "section-slider") {
        jQuery(".section-custom-background-image-" + rand_no).hide();
        jQuery(".section-slider-" + rand_no).show();
        jQuery(".section-custom-slider-" + rand_no).hide();
        jQuery(".section-background-video-" + rand_no).hide();

    } else if (id == "section-custom-slider") {
        jQuery(".section-custom-background-image-" + rand_no).hide();
        jQuery(".section-slider-" + rand_no).hide();
        jQuery(".section-custom-slider-" + rand_no).show();
        jQuery(".section-background-video-" + rand_no).hide();

    } else if (id == "section_background_video") {
        jQuery(".section-custom-background-image-" + rand_no).hide();
        jQuery(".section-slider-" + rand_no).hide();
        jQuery(".section-custom-slider-" + rand_no).hide();
        jQuery(".section-background-video-" + rand_no).show();

    } else {
        jQuery(".section-custom-background-image-" + rand_no).hide();
        jQuery(".section-slider-" + rand_no).hide();
        jQuery(".section-custom-slider-" + rand_no).hide();
        jQuery(".section-background-video-" + rand_no).hide();
    }
}


/*
 * thumbnail view
 */

function cs_thumbnail_view(id) {
    if (id == "none") {
        jQuery("#wrapper_thumb_slider").hide();
        jQuery("#wrapper_post_thumb_image").hide();

    } else if (id == "single") {
        jQuery("#wrapper_thumb_slider").hide();
        jQuery("#wrapper_post_thumb_image").show();
        jQuery("#wrapper_thumb_audio").hide();
    } else if (id == "slider") {
        jQuery("#wrapper_post_thumb_image").hide();
        jQuery("#wrapper_thumb_slider").show();
        jQuery("#wrapper_thumb_audio").hide();
    } else if (id == "audio") {
        jQuery("#wrapper_post_thumb_image").hide();
        jQuery("#wrapper_thumb_slider").hide();
        jQuery("#wrapper_thumb_audio").show();
    }


}
/*
 * post view
 */
function cs_post_view(id) {
    if (id == "single") {
        jQuery("#wrapper_post_detail, #wrapper_post_detail_slider, #wrapper_audio_view, #wrapper_video_view").hide();
        jQuery("#wrapper_post_detail").show();
    } else if (id == "audio") {
        jQuery("#wrapper_post_detail, #wrapper_post_detail_slider, #wrapper_audio_view, #wrapper_video_view").hide();
        jQuery("#wrapper_audio_view").show();
    } else if (id == "video") {
        jQuery("#wrapper_post_detail, #wrapper_post_detail_slider, #wrapper_audio_view, #wrapper_video_view").hide();
        jQuery("#wrapper_video_view").show();
    } else if (id == "slider") {
        jQuery("#wrapper_post_detail, #wrapper_post_detail_slider, #wrapper_audio_view, #wrapper_video_view").hide();
        jQuery("#wrapper_post_detail_slider").show();
    } else {
        jQuery("#wrapper_post_detail, #wrapper_post_detail_slider, #wrapper_audio_view, #wrapper_video_view").hide();
    }
}

/*
 * show slider
 */
function cs_show_slider(value) {
    if (value == 'Revolution Slider') {
        jQuery('#tab-sub-header-options ul,#tab-sub-header-options #cs_background_img_box').hide();
        jQuery('#cs_default_header_header').show();
        jQuery('#cs_custom_slider_1').show();
    } else if (value == 'No sub Header') {
        jQuery('#tab-sub-header-options ul,#tab-sub-header-options #cs_background_img_box').not('#tab-sub-header-options ul#cs_header_border_color_color').hide();
        jQuery('#cs_default_header_header,#tab-sub-header-options ul#cs_header_border_color_color').show();
    } else {
        jQuery('#tab-sub-header-options ul,#tab-sub-header-options #cs_background_img_box').show();
        jQuery('#cs_custom_slider_1,#cs_header_border_color_color').hide();
    }
}
/*
 * add field
 */

function cs_add_field(id, type) {
    var wrapper = jQuery("#" + id + " .input_fields_wrap"); //Fields wrapper
    var items = jQuery("#" + id + " .input_fields_wrap > div").length + 1;

    var uniqueNum = type + '_' + Math.floor(Math.random() * 99999);

    var remove = 'javascript:cs_remove_field("' + uniqueNum + '","' + id + '")';

    jQuery("#" + id + "  .counter_num").val(items);

    jQuery(wrapper).append('<div class="cs-wrapp-clone cs-shortcode-wrapp  cs-pbwp-content" id="' + uniqueNum + '"><ul class="form-elements bcevent_title"><li class="to-label"><label>Pricing Feature ' + items + '</label></li><li class="to-field"><div class="input-sec"><input class="txtfield" type="text" value="" name="pricing_feature[]"></div><div id="price_remove"><a class="remove_field" onclick=' + remove + '><i class="icon-minus-circle" style="color:#000; font-size:18px"></i></div></a></li></ul></div>'); //add input box
}
/*
 * remove field
 */

function cs_remove_field(id, wrapper) {
    var totalItems = jQuery("#" + wrapper + "  .counter_num").val() - 1;
    jQuery("#" + wrapper + "  .counter_num").val(totalItems);
    jQuery("#" + wrapper + " #" + id + "").remove();
}

jQuery('#tab-location-settings-cs-events').bind('tabsshow', function (event, ui) {
    if (ui.panel.id == "map-tab") {
        resizeMap();
    }
});
/*
 * createclone
 */
function _createclone(object, id, section, post) {

    var _this = object.closest(".column");
    _this.clone().insertAfter(_this);
    callme();
    jQuery(".draginner").sortable({
        connectWith: '.draginner',
        handle: '.column-in',
        cancel: '.draginner .poped-up,#confirmOverlay',
        revert: false,
        start: function (event, ui) {
            jQuery(ui.item).css({"width": "25%"})
        },
        receive: function (event, ui) {
            callme();
            getsorting(ui)
        },
        placeholder: "ui-state-highlight",
        forcePlaceholderSize: true
    });
    return false;
}
/*
 * ajax shortcode widget element
 */
function ajax_shortcode_widget_element(object, admin_url, POSTID, name) {
    var action = '';
    var wraper = object.closest(".column-in").next();
    var _structure = "<div id='cs-pbwp-outerlay'><div id='cs-widgets-list'></div></div>";

    jQuery(wraper).wrap(_structure).delay(100).fadeIn(150);
    var shortcodevalue = object.closest(".column-in").next().find(".cs-textarea-val").val();
    if (shortcodevalue) {

        var elementnamevalue = object.closest(".column-in").next().find(".cs-dcpt-element").val();
        SuccessLoader();
        //_createpop(wraper, "filterdrag");
        counter++;
        var dcpt_element_data = '';
        if (elementnamevalue) {
            var dcpt_element_data = '&element_name=' + elementnamevalue;
        }
        var newCustomerForm = "action=jobcareer_pb_" + name + '&counter=' + Math.floor((Math.random() * 100000) + 1) + '&shortcode_element_id=' + encodeURIComponent(shortcodevalue) + '&POSTID=' + POSTID + dcpt_element_data;
        var edit_url = action + counter;
        //_createpop();
        jQuery.ajax({
            type: "POST",
            url: admin_url,
            data: newCustomerForm,
            success: function (data) {
                var rsponse = data;
                var response_html = jQuery(rsponse).find(".cs-pbwp-content").html();
                object.closest(".column-in").next().find(".pagebuilder-data-load").html(response_html);
                object.closest(".column-in").next().find(".cs-wiget-element-type").val('form');
                jQuery('.loader').remove();
                jQuery('.bg_color').wpColorPicker();
                jQuery('div.cs-drag-slider').each(function () {
                    var _this = jQuery(this);
                    _this.slider({
                        range: 'min',
                        step: _this.data('slider-step'),
                        min: _this.data('slider-min'),
                        max: _this.data('slider-max'),
                        value: _this.data('slider-value'),
                        slide: function (event, ui) {
                            jQuery(this).parents('li.to-field').find('.cs-range-input').val(ui.value)
                        }
                    });
                });
                jQuery(".draginner").sortable({
                    connectWith: '.draginner',
                    handle: '.column-in',
                    cancel: '.draginner .poped-up,#confirmOverlay',
                    revert: false,
                    receive: function (event, ui) {
                        callme();
                        getsorting(ui)
                    },
                    placeholder: "ui-state-highlight",
                    forcePlaceholderSize: true

                });
            }
        });
    }
}

/*
 * aremoverlay
 */

function _removerlay(object) {
    jQuery("#cs-widgets-list .loader").remove();
    var _elem1 = "<div id='cs-pbwp-outerlay'></div>",
            _elem2 = "<div id='cs-widgets-list'></div>";
    var $elem;
    $elem = object.closest('div[class*="cs-wrapp-class-"]');
    $elem.unwrap(_elem2);
    $elem.unwrap(_elem1);
    $elem.hide()
}
/*
 * create pop short
 */
function _createpopshort(object) {

    var _structure = "<div id='cs-pbwp-outerlay'><div id='cs-widgets-list'></div></div>";
    //$elem = jQuery('#cs-widgets-list');

    var a = object.closest(".column-in").next();
    jQuery(a).wrap(_structure).delay(100).fadeIn(150);
	
    
	
	
}

// Post xml import

/*
 * Header Options
 */

// 
function cs_header_option(val) {
    if (val == 'none') {
        jQuery('#wrapper_rev_slider,#wrapper_headerbg_image').hide();
    } else if (val == 'cs_rev_slider') {
        jQuery('#wrapper_rev_slider').fadeIn();
        jQuery('#wrapper_headerbg_image').hide();
    } else if (val == 'cs_bg_image_color') {
        jQuery('#wrapper_headerbg_image').fadeIn();
        jQuery('#wrapper_rev_slider').hide();
    }
}

/*
 * banner widget toggle
 */

function cs_banner_widget_toggle(view, id) {
    if (view == 'random') {
        jQuery("#cs_banner_style_field_" + id).show();
        jQuery("#cs_banner_code_field_" + id).hide();
        jQuery("#cs_banner_number_field_" + id).show();
    }
    else if (view == 'single') {
        jQuery("#cs_banner_style_field_" + id).hide();
        jQuery("#cs_banner_code_field_" + id).show();
        jQuery("#cs_banner_number_field_" + id).hide();
    }
}
/**
 * add qual list
 *
 */
var counter_qual = 0;
function add_qual_list(admin_url, theme_url) {

    counter_qual++;
    var dataString = 'cs_qual_name=' + jQuery("#cs_qual_name").val() +
            '&cs_qual_desc=' + jQuery("#cs_qual_desc").val() +
            '&action=add_qual_to_list';
    jQuery(".feature-loader").html("<i class='icon-spinner8 icon-spin'></i>");
    jQuery.ajax({
        type: "POST",
        url: admin_url,
        data: dataString,
        success: function (response) {
            jQuery("#total_quals").append(response);
            jQuery(".feature-loader").html("");
            removeoverlay('add_qual_title', 'append');
            jQuery("#cs_qual_name").val("Title");
            jQuery("#cs_qual_desc").val("");
        }
    });
    return false;
}
/**
 * schedule list
 *
 */
var counter_schedule = 0;
function add_schedule_list(admin_url, theme_url) {

    counter_schedule++;
    var dataString = 'cs_schedule_name=' + jQuery("#cs_schedule_name").val() +
            '&cs_schedule_time=' + jQuery("#cs_schedule_time").val() +
            '&cs_schedule_desc=' + jQuery("#cs_schedule_desc").val() +
            '&action=add_schedule_to_list';
    jQuery(".feature-loader").html("<i class='icon-spinner8 icon-spin'></i>");
    jQuery.ajax({
        type: "POST",
        url: admin_url,
        data: dataString,
        success: function (response) {
            jQuery("#total_schedules").append(response);
            jQuery(".feature-loader").html("");
            removeoverlay('add_schedule_title', 'append');
            jQuery("#cs_schedule_name").val("Title");
            jQuery("#cs_schedule_time").val("");
            jQuery("#cs_schedule_desc").val("");
        }
    });
    return false;
}
/**
 * camp sched list
 *
 */
var counter_camp_sched = 0;
function add_camp_sched_list(admin_url, theme_url) {

    counter_camp_sched++;
    var dataString = 'cs_camp_sched_name=' + jQuery("#cs_camp_sched_name").val() +
            '&cs_camp_sched_time=' + jQuery("#cs_camp_sched_time").val() +
            '&cs_camp_sched_loc=' + jQuery("#cs_camp_sched_loc").val() +
            '&cs_camp_sched_desc=' + jQuery("#cs_camp_sched_desc").val() +
            '&action=add_camp_sched_to_list';
    jQuery(".feature-loader").html("<i class='icon-spinner8 icon-spin'></i>");
    jQuery.ajax({
        type: "POST",
        url: admin_url,
        data: dataString,
        success: function (response) {
            jQuery("#total_camp_scheds").append(response);
            jQuery(".feature-loader").html("");
            removeoverlay('add_camp_sched_title', 'append');
            jQuery("#cs_camp_sched_name").val("Title");
            jQuery("#cs_camp_sched_time").val("");
            jQuery("#cs_camp_sched_loc").val("");
            jQuery("#cs_camp_sched_desc").val("");
        }
    });
    return false;
}

function cs_display_url_field(id) {
    "use strict";
    if (id == 'yes') {
        jQuery("#advance_url_field").show();
    } else {
        jQuery("#advance_url_field").hide();
        jQuery("#cs_job_advance_search_url").val('');

    }
    return true;
}

//send smtp mail
function send_smtp_mail(admin_url) {
    "use strict";
    jQuery(".outerwrapp-layer,.loading_div").fadeIn(100);
    var candidate_skills_calc = 0;

     var serializedValues = jQuery("#plugin-options input,#plugin-options select,#plugin-options textarea,#plugin-options checkbox").serialize() + '&action=send_smtp_mail';
    
    jQuery.ajax({
        type: "POST",
        url: admin_url,
        data: serializedValues,
        success: function (response) {
            jQuery(".loading_div").hide();
            jQuery(".form-msg .innermsg").html(response);
            jQuery(".form-msg").show();
            jQuery(".outerwrapp-layer").fadeTo(2000, 1000).slideUp(1000);
            slideout();
        }
    });
}

function cs_mail_with_gmail(opt_id){
	jQuery("#cs_smtp_host").val('smtp.gmail.com');
	jQuery("#cs_smtp_port").val('465');
	jQuery("#cs_secure_connection_type").val('ssl');
	alert('You must provide your Gmail Email address as SMTP username and Gmail Password as SMTP Password.');
}
function use_smtp_mail_opt(opt_id){
	if(jQuery("#"+opt_id).val()=='on'){
		jQuery("#cs-no-smtp-div").show();
	} else {
		jQuery("#cs-no-smtp-div").hide();
	}
}

function use_wooC_gateways(opt_id){
	if(jQuery("#"+opt_id).val()!='on'){
		jQuery("#cs-no-wooC-gateway-div").show();
	} else {
		jQuery("#cs-no-wooC-gateway-div").hide();
	}
}
/*
 * chosen selection box
 */