<?php
/**
 * Helpers for Job Alert Notifications
 *
 * @package	Job Hunt
 */

// Direct access not allowed.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WP_Job_Hunt_Alert_Helpers class.
 */
class WP_Job_Hunt_Alert_Helpers {

	public static function get_script_str() {
        ob_start();
        ?>
        (function($) {
			$(function() {
				$(".delete-job-alert a" ).click(function() {
					var post_id = $(this).data("post-id");
					$('#id_confrmdiv').show();
					$('#id_truebtn').click(function () {
						var dataString = 'post_id=' + post_id + '&action=jobhunt_remove_job_alert';
						jQuery('.holder-' + post_id).find('.delete-job-alert').html('<i class="icon-spinner8 icon-spin"></i>');
						jQuery.ajax({
							type: "POST",
							url: "<?php echo admin_url('admin-ajax.php'); ?>",
							data: dataString,
							dataType: "JSON",
							success: function (response) {
								if (response.status == 0) {
									show_alert_msg( response.msg );
								} else {
									jQuery('.holder-' + post_id).remove();
									//jQuery('.feature-jobs').find('.holder-' + post_id).remove();
								}
							}
						});
						$('#id_confrmdiv').hide();
						return false;
					});
					$('#id_falsebtn').click(function () {
						$('#id_confrmdiv').hide();
						return false;
					});
					return false;
				});
			});
        })(jQuery);
        <?php
        return ob_get_clean();
    }

	public static function query_to_array($query) {
        $qrystr_arr = getMultipleParameters($query);
        $arr = array();

        foreach ($qrystr_arr as $qry_var => $qry_val) {
            if ($qry_val != '') {
                if (!is_array($qry_val))
                    if (strpos($qry_val, ',') !== FALSE) {
                        $qry_val = explode(",", $qry_val);
                    }
                // only if specialism child selected
                if (is_array($qry_val)) {
                    foreach ($qry_val as $qry_val_var => $qry_val_value) {
                        if ($qry_val_value != '') {
                            if ($qry_var == 'specialisms') { // only one remove the specialism
                                if (strpos($qrystr, ',' . $qry_val_value) !== FALSE) {
                                    $speciliasim_new_qry = str_replace(',' . $qry_val_value, "", $qrystr);
                                } else if (strpos($qrystr, $qry_val_value . ',') !== FALSE) {
                                    $speciliasim_new_qry = str_replace($qry_val_value . ',', "", $qrystr);
                                } else {
                                    $speciliasim_new_qry = str_replace($qry_val_value, "", $qrystr);
                                }

                                $arr[ $qry_var ] = str_replace("+", " ", $qry_val_value);
                            } else {
                                $qrystr1 = str_replace("&" . $qry_var . '[]=' . $qry_val_value, "", $qrystr);
                                $qrystr1 = str_replace("&" . $qry_var . '=' . $qry_val_value, "", $qrystr);;
								$arr[ $qry_var ] = str_replace("+", " ", $qry_val_value);
                            }
                        }
                    }
                } else {
                    if ($qry_var == 'specialisms') { // only remove the 
                        if (strpos($qrystr, ',' . $qry_val) !== FALSE) {
                            $speciliasim_new_qry = str_replace(',' . $qry_val, "", $qrystr);
                        } elseif (strpos($qrystr, $qry_val . ",") !== FALSE) {
                            $speciliasim_new_qry = str_replace($qry_val . ",", "", $qrystr);
                        } else {
                            $speciliasim_new_qry = str_replace($qry_val, "", $qrystr);
                        }
						$arr[$qry_var] = str_replace("+", " ", $qry_val);
                    } else {
                        $arr[$qry_var] = str_replace("+", " ", $qry_val);
                    }
                }
            }
        }
        $arr = array_filter($arr, function ( $elem ) {
            $extra = array("200", "Find Job");
            return !in_array($elem, $extra);
        });
        //return implode(',', $arr);
		return $arr;
    }

}