
 jQuery(".cs-donation-form ul li label") .click(function(event) {
	
	var a = jQuery(this).find('input').data('amount');
	 jQuery(".cs-donation-form label input.campaign-amount").val(a)
	 jQuery(".cs-donation-form label").removeClass("cs-active");
	 jQuery(this).addClass('cs-active');
	 return false;
});

jQuery('.donation-logos').on('click', '.cs-all-gates li', function () {
    
    jQuery('.cs-all-gates').find('li > input:radio').prop("checked", false);
    jQuery('.cs-all-gates').find('li').removeClass('active');

    jQuery(this).find('input:radio').prop("checked", true);
    jQuery(this).addClass('active');
});

jQuery("#cs-donate-btn").click(function(event) {
	
	event.preventDefault();
	jQuery('#cs-donte-form').append('<div class="cs-loader"><i class="icon-spinner8 icon-spin"></i></div>');
	var admin_url = jQuery("#cs-donate-form").data('ajaxurl');
	var serializedValues = jQuery("#cs-donate-form").serialize() + '&action=cs_donation_submit';
	jQuery.ajax({
        type: "POST",
        url: admin_url,
        data: serializedValues,
        success: function (response) {
			 jQuery("#payment-retun-form").html(response);
        }
    });
	
	return false;
});

function event_list_view(value) {
    "use strict";
	if( value == 'events-map' ) {
		 jQuery("#event-map-setting").show();
	} else {
		jQuery("#event-map-setting").hide();
	}
}

function price_option_save(admin_url) {
    "use strict";
    jQuery(".outerwrapp-layer,.loading_div").fadeIn(100);
    function newValues() {
        var serializedValues = jQuery("#cs-booking-pricing input,#cs-booking-pricing select,#cs-booking-pricing textarea").serialize() + '&action=price_option_save';
        return serializedValues;
    }
    var serializedReturn = newValues();
    jQuery.ajax({
        type: "POST",
        url: admin_url,
        data: serializedReturn,
        success: function (response) {

            jQuery(".loading_div").hide();
            jQuery(".form-msg .innermsg").html(response);
            jQuery(".form-msg").show();
            jQuery(".outerwrapp-layer").delay(100).fadeOut(100)
            //window.location.reload(true);
            slideout();
        }
    });
    //return false;
}
/*--------------------------------------------------------------
 * Plugin Reset Option
 *-------------------------------------------------------------*/
function cs_rest_plugin_options(admin_url, plugin_url) {
    "use strict";
    //jQuery(".loading_div").show('');
    var var_confirm = confirm("You current plugin options will be replaced with the default plugin activation options.");
    if (var_confirm == true) {
        var dataString = 'action=plugin_option_rest_all';
        jQuery.ajax({
            type: "POST",
            url: admin_url + "/admin-ajax.php",
            data: dataString,
            success: function (response) {
                jQuery(".form-msg").show();
                jQuery(".form-msg").html(response);
                jQuery(".loading_div").hide();
                window.location.reload(true);
                slideout();
            }
        });
    }
    //return false;
}






