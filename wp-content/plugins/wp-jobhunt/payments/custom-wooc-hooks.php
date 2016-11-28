<?php 
if(!class_exists('Payment_Processing')){
	class Payment_Processing {
		
		public function __construct(){
			global $rcv_parameters;
			$rcv_parameters		= array();	
			$Payment_Processing	= '';
			add_action( 'woocommerce_order_status_cancelled',  array( $this, 'custom_order_status_cancelled' )); 
			add_action( 'woocommerce_thankyou', array( $this, 'custom_thankyou_page' )); 
			add_action( 'woocommerce_checkout_order_processed', array( $this, 'action_woocommerce_new_order' ));
			add_filter( 'woocommerce_checkout_fields' , array( $this, 'custom_override_checkout_fields' ));
			add_filter( 'woocommerce_order_status_pending_to_processing' , array( $this, 'custom_payment_complete' ));
			add_action( 'woocommerce_payment_complete', array( $this, 'custom_payment_complete' ));
			add_action( 'woocommerce_order_status_processing', array( $this, 'custom_payment_complete' ));
			add_action( 'woocommerce_coupons_enabled', array( $this, 'custom_hide_coupon_field' ));
			add_filter( 'woocommerce_payment_complete_order_status', array( $this, 'custom_payment_complete_order_status' ), 10, 2);
		}
		
		public function processing_payment($payment_args){
			global $wpdb,$rcv_parameters,$woocommerce;
			$rcv_parameters	= $payment_args;
			extract($payment_args);
			
			
			$wpdb->query("DELETE ".$wpdb->prefix."posts
			FROM ".$wpdb->prefix."posts
			INNER JOIN ".$wpdb->prefix."postmeta ON ".$wpdb->prefix."postmeta.post_id = ".$wpdb->prefix."posts.ID
			WHERE (".$wpdb->prefix."postmeta.meta_key = 'referance_ID' AND ".$wpdb->prefix."postmeta.meta_value = '".$package_id."')");
			
			$post = array(
				'post_author' => 1,
				'post_content' => '',
				'post_status' => "publish",
				'post_title' => $package_name,
				'post_parent' => '',
				'post_type' => "product",
			);

			//Create post
			$post_id = wp_insert_post( $post );

			update_post_meta( $post_id, '_visibility', 'visible' );
			update_post_meta( $post_id, '_stock_status', 'instock');
			update_post_meta( $post_id, '_regular_price', $price);
			update_post_meta( $post_id, 'referance_ID', $package_id);
			update_post_meta( $post_id, '_price', $price );
			update_post_meta( $post_id, 'rcv_parameters', $payment_args );
			update_post_meta( $post_id, '_virtual', 'yes' );
			
			$woocommerce->cart->empty_cart();		
			$woocommerce->cart->add_to_cart( $post_id );
			
			$checkout_url = $woocommerce->cart->get_checkout_url();
			
			echo "<script>window.top.location.href='$checkout_url';</script>";
			exit;
		}
		
		public function custom_order_status_cancelled($order_id) {
			global $cs_plugin_options;
			$rcv_parameters	= get_post_meta($order_id, '_rcv_parameters', true);
			if(isset($rcv_parameters) && !empty($rcv_parameters)){
				$_REQUEST['order_id']		= $order_id;
				$_REQUEST['payment_status']	= 'Cancelled';
				$_REQUEST['payment_source']	= 'wooC';
				$redirect_url				= add_query_arg( $_REQUEST, $cs_plugin_options['cs_dir_paypal_ipn_url']);
				wp_remote_get($redirect_url);			
				$return_url				= $rcv_parameters['redirect_url'];
				
				$order 					= new WC_Order( $order_id );
					foreach($order->get_items()	as $item){
						wp_delete_post($item['product_id']);
					}
				wp_delete_post($order_id);
				wp_redirect( $return_url);
			}
		}
		
		public function custom_thankyou_page($order_id) {
			global $cs_plugin_options;
			$rcv_parameters	= get_post_meta($order_id, '_rcv_parameters', true);
			if(isset($rcv_parameters) && !empty($rcv_parameters)){
				$return_url				= $rcv_parameters['redirect_url'];
				$order 					= new WC_Order( $order_id );
				$payment_method			= get_post_meta( $order_id, '_payment_method', true );
				$order_status_array	= array(
						'payment_method'	=> $payment_method,
						'order_id'			=> $order_id,
						'status_code'		=> 200,
						'status_message'	=> __('Thank you. Your order has been received.','jobhunt'),
				);
				update_option('custom_order_status_array',$order_status_array);
				wp_redirect( $return_url);
			}
		}
		
		public function action_woocommerce_new_order( $order_id ) { 
			$order 					= new WC_Order( $order_id );
			foreach($order->get_items()	as $item){
				$product_id	= $item['product_id'];
			}
			$rcv_parameters	= get_post_meta($item['product_id'], 'rcv_parameters', true);
			$job_id			= $rcv_parameters['custom_var']['cs_order_id'];
			if(isset($rcv_parameters) && !empty($rcv_parameters)){
				update_post_meta( $order_id, '_rcv_parameters', $rcv_parameters );
			}
			$current_user = wp_get_current_user();
			update_post_meta( $job_id, 'cs_transaction_pay_method', get_post_meta($order_id, '_payment_method', true) );
			$user_id	= get_current_user_id();
			update_post_meta( $job_id, 'cs_first_name', get_user_meta($user_id, 'first_name', true) );
			update_post_meta( $job_id, 'cs_last_name', get_user_meta($user_id, 'last_name', true) );
			update_post_meta( $job_id, 'cs_full_address', get_user_meta($user_id, 'cs_post_comp_address', true) );
			update_post_meta( $job_id, 'cs_summary_email', $current_user->user_email );
		}

		
		public function custom_override_checkout_fields( $fields ) {
			global $woocommerce;
			$items = $woocommerce->cart->get_cart();
			
			foreach($items as $item) { 
				 $product = $item['data']->post; 
				 $product_id = $product->ID; 
			}
			$rcv_parameters	= get_post_meta( $product_id, 'rcv_parameters');
			
			if(isset($rcv_parameters) && !empty($rcv_parameters)){
				$fields	= array();
			}
			return $fields;
		}		
		
		public function custom_payment_complete($order_id){
			global $cs_plugin_options;
			$_REQUEST['order_id']		= $order_id;
			$_REQUEST['payment_status']	= 'approved';
			$_REQUEST['payment_source']	= 'wooC';
			$redirect_url				= add_query_arg( $_REQUEST, $cs_plugin_options['cs_dir_paypal_ipn_url']);
			wp_remote_get($redirect_url);
		}
		
		public function custom_payment_complete_order_status( $order_status, $order_id ){
			include_once("listner.php");
			$cs_plugin_options	= get_option('cs_plugin_options');
			$rcv_parameters		= get_post_meta($order_id, '_rcv_parameters', true);
			$cs_order_id		= $rcv_parameters['custom_var']['cs_order_id'];
			$payment_method		= get_post_meta( $cs_order_id, 'cs_transaction_pay_method', true );
			if( $order_status	== 'processing' ){
				$cs_order_id		= $rcv_parameters['custom_var']['cs_order_id'];
				$cs_job_id			= $rcv_parameters['custom_var']['cs_job_id'];
				$transaction_id = get_post_meta($order_id, '_transaction_id', true);
				$mc_currency = get_post_meta($order_id, '_order_currency', true);
				$payment_gross = get_post_meta($order_id, '_order_total', true);
				$transaction_array = array();
				$transaction_array['cs_trans_id'] = esc_attr($transaction_id);
				$transaction_array['cs_transaction_status'] = 'approved';
				$transaction_array['cs_trans_currency'] = esc_attr($mc_currency);
				$transaction_array['cs_transaction_amount'] = esc_attr($payment_gross);
				$transaction_array['cs_job_id'] = $cs_job_id;
				cs_update_transaction($transaction_array, $cs_order_id);
				cs_update_post($cs_job_id, $cs_order_id);
			}
			return 'completed';
		}
		
		public function custom_hide_coupon_field( $enabled ) {
			if (is_checkout() ) {
				$enabled = false;
			}
			return $enabled;
		}

		public function custom_order_status_display(){
			global $woocommerce;
			$return_data	= get_option('custom_order_status_array');
			delete_option('custom_order_status_array');
			return $return_data;
		}
		
		public function remove_raw_data($order_id){
			if(isset($order_id) && $order_id !=''){
				$order 			= new WC_Order( $order_id );
				foreach($order->get_items()	as $item){
							wp_delete_post($item['product_id']);
					}
				wp_delete_post($order_id);
			}
		}
	}
		global $Payment_Processing;
		$Payment_Processing	= new Payment_Processing();
}