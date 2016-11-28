<?php
global $cs_plugin_options;
define("DEBUG", 1);
define("USE_SANDBOX", 1);
define("LOG_FILE", "./ipn.log");
include_once('../../../../wp-load.php');

/*----------------------------------------------------	
 * Read POST data
/*----------------------------------------------------*/
$raw_post_data 		= file_get_contents('php://input');
$raw_post_array 	= explode('&', $raw_post_data);
$myPost 			= array();

foreach ($raw_post_array as $keyval) {
	$keyval = explode ('=', $keyval);
	if (count($keyval) == 2)
		$myPost[$keyval[0]] = urldecode($keyval[1]);
}

$req = 'cmd=_notify-validate';
if(function_exists('get_magic_sliders_gpc')) {
	$get_magic_sliders_exists = true;
}
foreach ($myPost as $key => $value) {
	if($get_magic_sliders_exists == true && get_magic_sliders_gpc() == 1) {
		$value = urlencode(stripslashes($value));
	} else {
		$value = urlencode($value);
	}
	$req .= "&$key=$value";
}

$tokens = explode("", trim($data));
$res = trim(end($tokens));

if ( isset( $_POST['payment_status'] ) && $_POST['payment_status'] == 'Completed'  && 
	 isset( $_POST['payer_status'] ) && $_POST['payer_status'] == 'verified'
    ) {
		
	$booking_id 		= $_POST['item_number'];
	$cs_current_date	= date('Y-m-d H:i:s');
	$transaction_array  = array();
	if(isset($booking_id) && !empty($booking_id)){
		$cs_pack_tra_meta = get_post_meta($booking_id, "dir_pakage_transaction_meta", true);
		if(is_int($cs_pack_tra_meta)){
			$cs_pack_tra_meta = array();
		}
		if($cs_pack_tra_meta == ''){
			$cs_pack_tra_meta = array();
		}
		if(!is_array($cs_pack_tra_meta) || empty($cs_pack_tra_meta)  || $cs_pack_tra_meta == ''){
			$cs_pack_tra_meta = array();	
		}
		$trans_counter = 0;
		if(is_array($cs_pack_tra_meta) && count($cs_pack_tra_meta)>0){
			$trans_counter = count($cs_pack_tra_meta);
		}
		
		$custom_var =  $_POST['custom'];
		$custom_var_array = explode('_',$custom_var);
		
		if(isset($custom_var_array['0'])){
			$user_id = $custom_var_array['0'];
		}
		
		if(isset($custom_var_array['1'])){
			$package_id = $custom_var_array['1'];
		}
		
		$featured	= 'no';
		
		if(isset($custom_var_array['2'])){
			$featured = $custom_var_array['2'];
		}
		
		$index_count = 0;
		$cs_tra_meta = get_option('cs_booking_transaction_meta', true);
		if(is_int($cs_tra_meta)){
			$cs_tra_meta = array();
		}
		
		if(!isset($cs_tra_meta) || empty($cs_tra_meta) || !is_array($cs_tra_meta)){
			$cs_tra_meta = array();
		}
 		
		if(isset($cs_tra_meta[$booking_id]) && is_array($cs_tra_meta[$booking_id]) && count($cs_tra_meta[$booking_id])>0){
			$index_count = (int)count($cs_tra_meta[$booking_id]);
		}
 		
		if(isset($_POST['txn_id']) && $_POST['txn_id'] <> ''){
			$tnx_type = 'transaction';	
		} else {
			$tnx_type = 'subscription';	
		}
		
		/*----------------------------------------------------	
		 * All Transactions Data Saved
		/*----------------------------------------------------*/
		$cs_booking_status    = isset($cs_plugin_options['cs_booking_visibility']) ? $cs_plugin_options['cs_booking_visibility'] : 'pending';
		$package_featured_ads	= isset( $cs_plugin_options['booking_featured_ad_price'] ) ? $cs_plugin_options['booking_featured_ad_price'] : 0; 
		
		$cs_tra_meta[$booking_id][$index_count][$tnx_type] = $_POST;
		update_option('cs_booking_transaction_meta', $cs_tra_meta);
 		$booking_post 				= array();
		$booking_post['ID'] 			= (int)$booking_id;
		$booking_post['post_status']  = 'pending';
		wp_update_post( $booking_post );
		
		if(isset($_POST['txn_id']) && $_POST['txn_id'] <> ''){
			$transaction_array = array();
			$transaction_array['user_id'] 		= esc_attr($user_id);
			$transaction_array['package_id'] 	= esc_attr($package_id);
			$transaction_array['item_name'] 	= esc_attr($_POST['item_name']);
			$transaction_array['txn_id'] 		= esc_attr($_POST['txn_id']);
			$transaction_array['payment_date']  = esc_attr($_POST['payment_date']);
			$transaction_array['payer_email'] 	= esc_attr($_POST['payer_email']);
			$transaction_array['payment_gross'] = esc_attr($_POST['payment_gross']);
			$transaction_array['mc_currency'] 	= esc_attr($_POST['mc_currency']);
			$transaction_array['address_name'] 	= esc_attr($_POST['address_name']);
			$transaction_array['ipn_track_id'] 	= esc_attr($_POST['ipn_track_id']);
			$cs_pack_tra_meta[$trans_counter] 	= $transaction_array;
			
			$payment_date = date_i18n( 'Y-m-d H:i:s', strtotime(esc_attr($_POST['payment_date'])));
			
			update_post_meta((int)$booking_id, 'dir_pakage_transaction_meta', $cs_pack_tra_meta);
			update_post_meta((int)$booking_id, 'dir_payment_date', $payment_date);
			
			/*----------------------------------------------------	
			 * Update Post Status
			/*----------------------------------------------------*/
			$postStatus['ID'] 			= $booking_id;
			$postStatus['post_status']  = $cs_booking_status;
			wp_update_post( $postStatus );
			
			/*----------------------------------------------------	
			 * Update Featured Status
			/*----------------------------------------------------*/
			update_post_meta( $booking_id,'cs_booking_pkg_names', $package_id );
			
			if ( $package_id =='0000000000' ){
				$package_meta = get_post_meta($booking_id, "_pakage_meta", true);
			} else {
				$cs_packages_options 	= get_option('cs_packages_options');
				$package_meta 			= $cs_packages_options[$package_id];
			}

			if(isset($package_meta['package_duration']) && $package_meta['package_duration'] == 'unlimited' ){
				
				update_post_meta((int)$booking_id, 'dir_pkg_expire_date', $cs_current_date);
			} else if(isset($package_meta['package_duration'])){
				$package_duration = $package_meta['package_duration'];
				$date 		 	  = strtotime("+".$package_duration." days", strtotime($payment_date));
				$expire_date 	  = date("Y-m-d H:i:s", $date);
				update_post_meta((int)$booking_id, 'dir_pkg_expire_date', $expire_date);
				
				if ( isset( $featured ) && $featured == 'yes' ) {
					$package_meta['package_price']	= $package_meta['package_price'] + $package_featured_ads;
				}
				update_post_meta( $booking_id, '_pakage_meta', $package_meta );
			}
				
			/*----------------------------------------------------	
			 * Update Package Add Till Date
			/*----------------------------------------------------*/
 			if ( isset( $featured ) && $featured == 'yes' ) {
				$featured_days	= isset( $cs_plugin_options['booking_featured_ad_days'] ) ? $cs_plugin_options['booking_featured_ad_days'] : 0;
				if($featured_days < 1 || $featured_days == '')
					$featured_days = 0;
				
				$featured_date		 = strtotime("+".$featured_days." days", strtotime($payment_date));
				$featured_date 		 = date("Y-m-d H:i:s", $featured_date);
				update_post_meta($booking_id, 'dir_featured_till', $featured_date);
		 	 }			
		}

		/*----------------------------------------------------	
		 * User Payment Re-attempt 
		/*----------------------------------------------------*/
		if(isset($_POST['reattempt']) && $_POST['reattempt'] <> ''){
			$pakage_subs_meta = get_post_meta($booking_id, "dir_pakage_trans_subsription_meta", true);
			if(is_int($pakage_subs_meta)){
				$pakage_subs_meta = array();
			}
			if($pakage_subs_meta == ''){
				$pakage_subs_meta = array();
			}
			if(!is_array($pakage_subs_meta) || empty($pakage_subs_meta)  || $pakage_subs_meta == ''){
				$pakage_subs_meta = array();	
			}
			$trans_counter = 0;
 			if( is_array( $pakage_subs_meta ) && count( $pakage_subs_meta ) > 0 ){
				$trans_counter = count($pakage_subs_meta);
			}
		
 			$subs_booking_array = array();
			$subs_booking_array['user_id'] 		= 	esc_attr($user_id);
			$subs_booking_array['package_id'] 	= 	esc_attr($package_id);
			$subs_booking_array['item_name'] 		= 	esc_attr($_POST['item_name']);
			$subs_booking_array['payment_date']   = esc_attr($_POST['payment_date']);
			$subs_booking_array['payer_email'] 	= 	esc_attr($_POST['payer_email']);
			$subs_booking_array['amount3'] 		= 	esc_attr($_POST['amount3']);
			$subs_booking_array['mc_currency'] 	= 	esc_attr($_POST['mc_currency']);
			$subs_booking_array['address_name'] 	= 	esc_attr($_POST['address_name']);
			$subs_booking_array['ipn_track_id'] 	= 	esc_attr($_POST['ipn_track_id']);
			$pakage_subs_meta[$trans_counter] 		= 	$subs_booking_array;
			
			$payment_date = date_i18n( 'Y-m-d H:i:s', strtotime($_POST['payment_date']));
			
			update_post_meta((int)$booking_id, 'dir_pakage_trans_subsription_meta', $pakage_subs_meta);
			update_post_meta((int)$booking_id, 'dir_payment_date', $payment_date);
 
			/*----------------------------------------------------	
			 * Update Post Status
			/*----------------------------------------------------*/
			$postStatus['ID'] 			= $booking_id;
			$postStatus['post_status']  = $cs_booking_status;
			wp_update_post( $postStatus );
			
			/*----------------------------------------------------	
			 * Update Featured Status
			/*----------------------------------------------------*/
			update_post_meta( $booking_id,'cs_booking_pkg_names', $package_id );
			
			if ( $package_id =='0000000000' ){
				$package_meta = get_post_meta($booking_id, "_pakage_meta", true);
			} else {
				$cs_packages_options 	= get_option('cs_packages_options');
				$package_meta 			= $cs_packages_options[$package_id];
			}
			
			if(isset($package_meta['package_duration']) && $package_meta['package_duration'] == 'unlimited' ){
				update_post_meta((int)$booking_id, 'dir_pkg_expire_date', $cs_current_date);
			} else if(isset($package_meta['package_duration'])){
				
				$package_duration = $package_meta['package_duration'];
				$subscr_date      = esc_attr($_POST['payment_date']);
				$date = strtotime("+".$package_duration." days", strtotime($subscr_date));
				$expire_date = date("Y-m-d H:i:s", $date);
				update_post_meta((int)$booking_id, 'dir_pkg_expire_date', $expire_date);
				
				if ( isset( $featured ) && $featured == 'yes' ) {
					$package_meta['package_price']	= $package_meta['package_price'] + $package_featured_ads;
				}
				update_post_meta( $booking_id, '_pakage_meta', $package_meta );
			}

			/*----------------------------------------------------	
			 * Update Package Add Till Date
			/*----------------------------------------------------*/
			if ( isset( $featured ) && $featured == 'yes' ) {
				
				$featured_days	= isset( $cs_plugin_options['booking_featured_ad_days'] ) ? $cs_plugin_options['booking_featured_ad_days'] : 0;
				if($featured_days < 1 || $featured_days == '')
					$featured_days = 0;
				
				$featured_date		 = strtotime("+".$featured_days." days", strtotime($payment_date));
				$featured_date 		 = date("Y-m-d H:i:s", $featured_date);
				update_post_meta($booking_id, 'dir_featured_till', $featured_date);
		 	 }	
		}
		if(DEBUG == true) {
			error_log(date('[Y-m-d H:i e] '). "Verified IPN: $req ". PHP_EOL, 3, LOG_FILE);
		}
	}
} else if (strcmp ($res, "INVALID") == 0) {
	if(DEBUG == true) {
		error_log(date('[Y-m-d H:i e] '). "Invalid IPN: $req" . PHP_EOL, 3, LOG_FILE);
	}
}