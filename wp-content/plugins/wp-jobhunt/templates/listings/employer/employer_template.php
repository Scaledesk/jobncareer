<?php
/**
 * File Type: Employer Shortcode
 *
 * Start Function how to show the employee listing
 *
 */
if ( ! function_exists( 'cs_employer_listing' ) ) {

	function cs_employer_listing( $atts, $content = "" ) {
		global $wp_query;
		foreach ( $wp_query->query_vars as $key => $val ) {
			$_GET[$key] = $val;
		}
		ob_start();
		global $wpdb, $cs_plugin_options, $cs_form_fields2;
		$a = shortcode_atts(
				array(
			'column_size' => '',
			'cs_employer_show_pagination' => 'pagination', // yes or no
			'cs_employer_pagination' => '10', // as per your requirement only numbers(0-9)
			'cs_employer_searchbox' => 'yes', // yes or no
			'cs_employer_searchbox_top' => 'no', // yes or no
			'cs_employer_view' => 'simple', // 2columns, 3columns, 4columns
			'cs_employer_title' => '', // simplelist or detaillist
				), $atts
		);
		extract( shortcode_atts( $a, $atts ) );
		if ( isset( $column_size ) && $column_size != '' ) {
			$column_class = jobcareer_custom_column_class( $column_size );
			echo '<div class="' . $column_class . '">';
		}
		?>

		<!-- alert for complete theme -->
		<?php
		// getting all record of employer for paging
		if ( empty( $_GET['page_employer'] ) )
			$_GET['page_employer'] = 1;
		$qrystr = '';
		#############################################
		#           Filtration Start            #####
		#############################################

		$filter_arr = '';
		$posted = '';
		$specialisms = '';
		$location = '';
		$default_date_time_formate = 'd-m-Y H:i:s';
		$cs_employer_activity_date_formate = 'd-m-Y H:i:s';
		if ( isset( $_GET['location'] ) )
			$location = $_GET['location'];
		if ( isset( $_GET['posted'] ) )
			$posted = $_GET['posted'];
		if ( isset( $_GET['specialisms'] ) && $_GET['specialisms'] != '' ) {
			$specialisms = $_GET['specialisms'];
			$qrystr .= '&specialisms=' . $_GET['specialisms'];
			if ( ! is_array( $specialisms ) )
				$specialisms = Array( $specialisms );
		} elseif ( isset( $_GET['specialisms_string'] ) && $_GET['specialisms_string'] != '' ) {
			$specialisms = explode( ",", $_GET['specialisms_string'] );
			$qrystr .= '&specialisms=' . $_GET['specialisms_string'];
		}
		$cus_fields_count_arr = '';
		// posted date check
		if ( $posted != '' ) {
			$lastdate = '';
			$now = '';
			$qrystr .= '&posted=' . $posted;  // added again this var in query string for linking again
			if ( $posted == 'lasthour' ) {
				$now = date( $default_date_time_formate );
				$lastdate = date( $default_date_time_formate, strtotime( '-1 hours', time() ) );
			} elseif ( $posted == 'last24' ) {
				$now = date( $default_date_time_formate );
				$lastdate = date( $default_date_time_formate, strtotime( '-24 hours', time() ) );
			} elseif ( $posted == '7days' ) {
				$now = date( $default_date_time_formate );
				$lastdate = date( $default_date_time_formate, strtotime( '-7 days', time() ) );
			} elseif ( $posted == '14days' ) {
				$now = date( $default_date_time_formate );
				$lastdate = date( $default_date_time_formate, strtotime( '-14 days', time() ) );
			} elseif ( $posted == '30days' ) {
				$now = date( $default_date_time_formate );
				$lastdate = date( $default_date_time_formate, strtotime( '-30 days', time() ) );
			}
			if ( $lastdate != '' && $now != '' ) {
				$filter_arr[] = array(
					'key' => 'cs_user_last_activity_date',
					'value' => strtotime( $lastdate ),
					'compare' => '>=',
				);
				// for count query
				$cus_fields_count_arr['posted'][] = array(
					'key' => 'cs_user_last_activity_date',
					'value' => strtotime( $lastdate ),
					'compare' => '>=',
				);
			}
		}
		$cus_fields_count_arr = '';
		$location_condition_arr = '';
		// location check
		if ( $location != '' ) {
			$cs_radius_switch = isset( $cs_plugin_options['cs_radius_switch'] ) ? $cs_plugin_options['cs_radius_switch'] : '';
			if ( isset( $_GET['radius'] ) && $_GET['radius'] > 0 && $cs_radius_switch == 'on' ) {
				$cs_radius = $_GET['radius'];
				$cs_radius_measure = isset( $cs_plugin_options['cs_radius_measure'] ) ? $cs_plugin_options['cs_radius_measure'] : '';
				$distance_km_miles = $cs_radius_measure;
				$qrystr .= '&radius=' . $cs_radius;  // added again this var in query string for linking again
				$cs_radius = preg_replace( "/[^0-9,.]/", "", $cs_radius );
				if ( $distance_km_miles == 'km' ) {
					if ( isset( $_GET['radius'] ) ) {
						$cs_radius = $cs_radius * 0.621371;	// for km
					}
				}
				$Latitude = '';
				$Longitude = '';
				$prepAddr = '';
				$minLat = '';
				$maxLat = '';
				$minLong = '';
				$maxLong = '';
				if ( isset( $_GET['location'] ) && ! empty( $_GET['location'] ) ) {
					$address = sanitize_text_field( $_GET['location'] );
					$prepAddr = str_replace( ' ', '+', $address );
					$geocode = file_get_contents( cs_server_protocol() . 'google.com/maps/api/geocode/json?address=' . $prepAddr . '&sensor=false' );
					$output = json_decode( $geocode );
					$Latitude = $output->results[0]->geometry->location->lat;
					$Longitude = $output->results[0]->geometry->location->lng;
					if ( isset( $Latitude ) && $Latitude <> '' && isset( $Longitude ) && $Longitude <> '' ) {
						$zcdRadius = new RadiusCheck( $Latitude, $Longitude, $cs_radius );
						$minLat = $zcdRadius->MinLatitude();
						$maxLat = $zcdRadius->MaxLatitude();
						$minLong = $zcdRadius->MinLongitude();
						$maxLong = $zcdRadius->MaxLongitude();
					}
				}
				if ( $minLat != '' && $maxLat != '' && $minLong != '' && $maxLong != '' ) {
					$radius_array = array(
						'relation' => 'AND',
						array(
							'key' => 'cs_post_loc_latitude',
							'value' => array( $minLat, $maxLat ),
							'compare' => 'BETWEEN',
							'type' => 'CHAR'
						),
						array(
							'key' => 'cs_post_loc_longitude',
							'value' => array( $minLong, $maxLong ),
							'compare' => 'BETWEEN',
							'type' => 'CHAR'
						),
					);
				}
			}
			$qrystr .= '&location=' . $location;  // added again this var in query string for linking again
			$cs_location_type = isset( $cs_plugin_options['cs_search_by_location'] ) ? $cs_plugin_options['cs_search_by_location'] : '';
			if ( $cs_location_type == 'countries_and_cities' || $cs_location_type == 'countries_only' ) {
				if ( isset( $radius_array ) && is_array( $radius_array ) ) {

					$location_condition_arr[] = array(
						'relation' => 'AND',
						array(
							'relation' => 'OR',
							array(
								'key' => 'cs_post_loc_city',
								'value' => $location,
								'compare' => '=',
							),
							array(
								'key' => 'cs_post_loc_country',
								'value' => $location,
								'compare' => '=',
							),
							$radius_array,
						)
					);
					// for count query
					$cus_fields_count_arr['location'][] = array(
						'relation' => 'AND',
						array(
							'relation' => 'OR',
							array(
								'key' => 'cs_post_loc_city',
								'value' => $location,
								'compare' => '=',
							),
							array(
								'key' => 'cs_post_loc_country',
								'value' => $location,
								'compare' => '=',
							),
							$radius_array,
						)
					);
				} else {
					$location_condition_arr[] = array(
						'relation' => 'OR',
						array(
							'key' => 'cs_post_loc_city',
							'value' => $location,
							'compare' => '=',
						),
						array(
							'key' => 'cs_post_loc_country',
							'value' => $location,
							'compare' => '=',
						)
					);

					// for count query
					$cus_fields_count_arr['location'][] = array(
						'key' => 'cs_post_loc_city',
						'value' => $location,
						'compare' => '=',
					);
					$cus_fields_count_arr['location'][] = array(
						'key' => 'cs_post_loc_country',
						'value' => $location,
						'compare' => '=',
					);
				}
			} elseif ( $cs_location_type == 'cities_only' || $cs_location_type == 'single_city' ) {

				if ( isset( $radius_array ) && is_array( $radius_array ) ) {
					$location_condition_arr[] = array(
						'relation' => 'AND',
						array(
							'relation' => 'OR',
							array(
								'key' => 'cs_post_loc_city',
								'value' => $location,
								'compare' => '=',
							),
							$radius_array,
						)
					);
					// for count query
					$cus_fields_count_arr['location'][] = array(
						'relation' => 'AND',
						array(
							'relation' => 'OR',
							array(
								'key' => 'cs_post_loc_city',
								'value' => $location,
								'compare' => '=',
							),
							$radius_array,
						)
					);
				} else {
					$location_condition_arr[] = array(
						'key' => 'cs_post_loc_city',
						'value' => $location,
						'compare' => '=',
					);

					// for count query
					$cus_fields_count_arr['location'][] = array(
						'key' => 'cs_post_loc_city',
						'value' => $location,
						'compare' => '=',
					);
				}
			}
		}
		// end posted date check
		// location check

		$alphanumaric = '';
		$alphabatic_qrystr = '';
		if ( isset( $_GET['alphanumaric'] ) && $_GET['alphanumaric'] != '' ) {
			$alphanumaric = $_GET['alphanumaric'];
		}
		if ( $alphanumaric != '' ) {

			$qrystr .= '&alphanumaric=' . $alphanumaric; // using this in paging
			$keyword = 'a-z';
			$comapare = ' NOT REGEXP ';
			if ( $alphanumaric != "numeric" ) {
				$keyword = $alphanumaric;
				$comapare = ' REGEXP '; // only specific alphabets
			}
			$alphabatic_qrystr = " AND display_name " . $comapare . " '^[" . $keyword . "]' ";
		}
		// posted date check
		$filter_arr2[] = '';
		// specialism check
		if ( $specialisms != '' && $specialisms != 'All specialisms' ) {

			foreach ( $specialisms as $specialisms_key ) {
				$filter_arr[] = array(
					'key' => 'cs_specialisms',
					'value' => $specialisms_key,
					'compare' => 'LIKE',
				);
			}
		}
		// end specialism check
		// load all custom fileds for filtration 
		$cs_employer_cus_fields = get_option( "cs_employer_cus_fields" );
		if ( is_array( $cs_employer_cus_fields ) && sizeof( $cs_employer_cus_fields ) > 0 ) {
			foreach ( $cs_employer_cus_fields as $cus_field ) {
				if ( isset( $cus_field['enable_srch'] ) && $cus_field['enable_srch'] == 'yes' ) {
					$query_str_var_name = $cus_field['meta_key'];
					if ( isset( $_GET[$query_str_var_name] ) && $_GET[$query_str_var_name] != '' ) {
						if ( ! isset( $cus_field['multi'] ) || $cus_field['multi'] != 'yes' ) {
							$qrystr .= '&' . $query_str_var_name . '=' . $_GET[$query_str_var_name];
						}
						if ( $cus_field['type'] == 'dropdown' ) {
							if ( isset( $cus_field['multi'] ) && $cus_field['multi'] == 'yes' ) {
								$_query_string_arr = getMultipleParameters();
								$filter_multi_arr = ['relation' => 'OR', ];
								foreach ( $_query_string_arr[$query_str_var_name] as $query_str_var_name_key ) {
									if ( $cus_field['post_multi'] == 'yes' ) {
										$filter_multi_arr[] = array(
											'key' => $query_str_var_name,
											'value' => serialize( $query_str_var_name_key ),
											'compare' => 'Like',
										);
									} else {
										$filter_multi_arr[] = array(
											'key' => $query_str_var_name,
											'value' => $query_str_var_name_key,
											'compare' => '=',
										);
									}
									$qrystr .= '&' . $query_str_var_name . '=' . $query_str_var_name_key;
								}
								$filter_arr[] = array(
									$filter_multi_arr
								);
								// for count query
								$cus_fields_count_arr[$query_str_var_name][] = array(
									$filter_multi_arr
								);
							} else {
								if ( $cus_field['post_multi'] == 'yes' ) {
									$filter_arr[] = array(
										'key' => $query_str_var_name,
										'value' => serialize( $_GET[$query_str_var_name] ),
										'compare' => 'Like',
									);
									// for count query
									$cus_fields_count_arr[$query_str_var_name][] = array(
										'key' => $query_str_var_name,
										'value' => serialize( $_GET[$query_str_var_name] ),
										'compare' => 'Like',
									);
								} else {
									$filter_arr[] = array(
										'key' => $query_str_var_name,
										'value' => $_GET[$query_str_var_name],
										'compare' => '=',
									);
									// for count query
									$cus_fields_count_arr[$query_str_var_name][] = array(
										'key' => $query_str_var_name,
										'value' => $_GET[$query_str_var_name],
										'compare' => '=',
									);
								}
							}
						} elseif ( $cus_field['type'] == 'text' || $cus_field['type'] == 'email' || $cus_field['type'] == 'url' ) {
							$filter_arr[] = array(
								'key' => $query_str_var_name,
								'value' => $_GET[$query_str_var_name],
								'compare' => 'LIKE',
							);
							// for count query
							$cus_fields_count_arr[$query_str_var_name][] = array(
								'key' => $query_str_var_name,
								'value' => $_GET[$query_str_var_name],
								'compare' => 'LIKE',
							);
						} elseif ( $cus_field['type'] == 'range' ) {
							$ranges_str_arr = explode( ",", $_GET[$query_str_var_name] );
							$range_first = isset( $ranges_str_arr[0] ) ? $ranges_str_arr[0] : '';
							$range_seond = isset( $ranges_str_arr[1] ) ? $ranges_str_arr[1] : '';
							$filter_arr[] = array(
								'key' => $query_str_var_name,
								'value' => $range_first,
								'compare' => '>=',
								'type' => 'NUMERIC'
							);
							$filter_arr[] = array(
								'key' => $query_str_var_name,
								'value' => $range_seond,
								'compare' => '<=',
								'type' => 'NUMERIC'
							);

							// for count query
							$cus_fields_count_arr[$query_str_var_name][] = array(
								'key' => $query_str_var_name,
								'value' => $range_first,
								'compare' => '>=',
								'type' => 'NUMERIC'
							);
							// for count query
							$cus_fields_count_arr[$query_str_var_name][] = array(
								'key' => $query_str_var_name,
								'value' => $range_seond,
								'compare' => '<=',
								'type' => 'NUMERIC'
							);
						} elseif ( $cus_field['type'] == 'date' ) {
							$filter_arr[] = array(
								'key' => $query_str_var_name,
								'value' => $_GET[$query_str_var_name],
								'compare' => 'LIKE',
							);
							$cus_fields_count_arr[$query_str_var_name][] = array(
								'key' => $query_str_var_name,
								'value' => $_GET[$query_str_var_name],
								'compare' => 'LIKE',
							);
						}
					}
				}
			}
		}
		// end load all custom fileds for filtration

		$meta_post_ids_arr = '';
		$company_name_id_condition = '';
		if ( isset( $filter_arr ) && ! empty( $filter_arr ) ) {

			$meta_post_ids_arr = cs_get_query_whereclase_by_array( $filter_arr, true );
			// if no result found in filtration 
			if ( empty( $meta_post_ids_arr ) ) {
				$meta_post_ids_arr = array( 0 );
			}
			$ids = $meta_post_ids_arr != '' ? implode( ",", $meta_post_ids_arr ) : '0';
			$company_name_id_condition = " ID in (" . $ids . ") AND ";
		}
		$cs_company_name = '';
		if ( isset( $_GET['cs_company_name'] ) ) {
			$cs_company_name = $_GET['cs_company_name'];
			$cs_company_name = str_replace( "+", " ", $cs_company_name );
		}
		$mypost = '';
		if ( $cs_company_name != '' ) {
			$qrystr .= '&cs_company_name=' . $cs_company_name; // using this in paging
			$post_ids = $wpdb->get_col( "SELECT ID FROM $wpdb->users WHERE " . $company_name_id_condition . " UCASE(display_name) LIKE '%$cs_company_name%'" . $alphabatic_qrystr );
			if ( $post_ids ) {

				$mypost = array( 'role' => 'cs_employer', 'order' => 'DESC', 'orderby' => 'registered',
					'include' => $post_ids,
					'meta_query' => array(
						array(
							'key' => 'cs_user_status',
							'value' => 'active',
							'compare' => '=',
						),
						array(
							'key' => 'cs_user_last_activity_date',
							'value' => strtotime( date( $cs_employer_activity_date_formate ) ),
							'compare' => '<=',
						),
						$location_condition_arr,
					)
				);
			}
		} else {

			//$post_ids = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE " . $company_name_id_condition . " 1=1 " . $alphabatic_qrystr . " AND post_type='employer' AND post_status='publish'");
			$post_ids = $wpdb->get_col( "SELECT ID FROM $wpdb->users WHERE " . $company_name_id_condition . " 1=1 " . $alphabatic_qrystr );
			if ( $post_ids ) {

				$mypost = array( 'role' => 'cs_employer', 'order' => 'DESC', 'orderby' => 'registered',
					'include' => $post_ids,
					'meta_query' => array(
						array(
							'key' => 'cs_user_status',
							'value' => 'active',
							'compare' => '=',
						),
						array(
							'key' => 'cs_user_last_activity_date',
							'value' => strtotime( date( $cs_employer_activity_date_formate ) ),
							'compare' => '<=',
						),
						$location_condition_arr,
					)
				);
			}
		}

		$loop_count = new WP_User_Query( $mypost );

		$count_post = $loop_count->total_users;
		// show paging check from atributes
		if ( $a['cs_employer_show_pagination'] == 'pagination' ) {
			$cs_blog_num_post = $a['cs_employer_pagination']; //pick from atribute 
		} else {
			if ( isset( $a['cs_employer_pagination'] ) and $a['cs_employer_pagination'] <> '' ) {
				if ( $a['cs_employer_pagination'] != 0 )
					$cs_blog_num_post = $a['cs_employer_pagination'];
				else
					$cs_blog_num_post = "999999";
			} else {
				$cs_blog_num_post = "999999";
			}
		}


		// result query with paging element
		$args = '';
		if ( $count_post > 0 ) {

			$total_users = $count_post;

			// grab the current page number and set to 1 if no page number is set
			$page = isset( $_GET['page_id_all'] ) ? $_GET['page_id_all'] : 1;

			// how many users to show per page
			$users_per_page = absint( $cs_blog_num_post );

			// calculate the total number of pages.
			$total_pages = 1;
			$offset = 1;

			if ( $users_per_page > 0 ) {
				$offset = $users_per_page * ($page - 1);
			}
			if ( $total_users > 0 && $users_per_page > 0 ) {
				$total_pages = ceil( $total_users / $users_per_page );
			}

			if ( $cs_company_name != '' ) {
				$post_ids = $wpdb->get_col( "SELECT ID FROM $wpdb->users WHERE " . $company_name_id_condition . " UCASE(display_name) LIKE '%$cs_company_name%'" . $alphabatic_qrystr );
				if ( $post_ids ) {

					$args = array( 'number' => $users_per_page, 'role' => 'cs_employer', 'offset' => $offset, 'order' => 'ASC', 'orderby' => 'display_name',
						'include' => $post_ids,
						'meta_query' => array(
							array(
								'key' => 'cs_user_status',
								'value' => 'active',
								'compare' => '=',
							),
							array(
								'key' => 'cs_user_last_activity_date',
								'value' => strtotime( date( $cs_employer_activity_date_formate ) ),
								'compare' => '<=',
							),
							$location_condition_arr,
						)
					);
				}
			} else {

				$post_ids = $wpdb->get_col( "SELECT ID FROM $wpdb->users WHERE " . $company_name_id_condition . " 1=1 " . $alphabatic_qrystr );
				if ( $post_ids ) {
					$args = array( 'number' => $users_per_page, 'role' => 'cs_employer', 'offset' => $offset, 'order' => 'ASC', 'orderby' => 'display_name',
						'include' => $post_ids,
						'meta_query' => array(
							array(
								'key' => 'cs_user_status',
								'value' => 'active',
								'compare' => '=',
							),
							array(
								'key' => 'cs_user_last_activity_date',
								'value' => strtotime( date( $cs_employer_activity_date_formate ) ),
								'compare' => '<=',
							),
							$location_condition_arr,
						)
					);
				}
			}
			// end result query with paging
		}
		?>
		<div class="cs-content-holder">

			<?php
			if ( $a['cs_employer_searchbox'] == 'yes' ) {
				?>
				<?php $random_id = rand( 0, 9999999 ); ?>
				<!-- specialism popup -->
				<div class="modal fade" id="light" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">

							<div class="modal-body">
								<div class="white_content">
									<a class="close" data-dismiss="modal">&nbsp;</a>
									<form action="#" method="get" id="frm_all_specialisms<?php echo esc_html( $random_id ); ?>" >
										<?php
										// parse query string and create hidden fileds
										$final_query_str = str_replace( "?", "", $qrystr );
										$final_query_str = cs_remove_qrystr_extra_var( $final_query_str, 'specialisms', 'no' );
										$query = explode( '&', $final_query_str );
										foreach ( $query as $param ) {
											if ( ! empty( $param ) ) {
												list($name, $value) = explode( '=', $param );
												$new_str = $name . "=" . $value;
												if ( is_array( $name ) ) {
													foreach ( $_query_str_single_value as $_query_str_single_value_arr ) {
														$cs_opt_array = array(
															'id' => '',
															'std' => $value,
															'cust_id' => "",
															'cust_name' => $name . "[]",
															'classes' => '',
														);
														$cs_form_fields2->cs_form_hidden_render( $cs_opt_array );
													}
												} else {
													$cs_opt_array = array(
														'id' => '',
														'std' => $value,
														'cust_id' => "",
														'cust_name' => $name,
														'classes' => '',
													);
													$cs_form_fields2->cs_form_hidden_render( $cs_opt_array );
												}
											}
										}
										?>
										<ul class="custom-listing">
											<?php
											// get all job types
											$specialisms_parent_id = 0;
											$input_type_specialism = 'radio';   // if first level then select only sigle specialism
											if ( $specialisms != '' ) {
												$selected_spec = get_term_by( 'slug', $specialisms[0], 'specialisms' );
												$specialisms_parent_id = $selected_spec->term_id;
											}
											$specialisms_args = array(
												'orderby' => 'name',
												'order' => 'ASC',
												'fields' => 'all',
												'slug' => '',
												'hide_empty' => false,
												'parent' => $specialisms_parent_id,
											);
											$all_specialisms = get_terms( 'specialisms', $specialisms_args );
											if ( count( $all_specialisms ) <= 0 ) {
												$specialisms_args = array(
													'orderby' => 'name',
													'order' => 'ASC',
													'fields' => 'all',
													'slug' => '',
													'parent' => isset( $selected_spec->parent ) ? $selected_spec->parent : '',
												);


												$all_specialisms = get_terms( 'specialisms', $specialisms_args );

												if ( isset( $selected_spec->parent ) && $selected_spec->parent != 0 ) {	// if parent is not root means not main parent
													$input_type_specialism = 'checkbox';   // if first level then select multiple specialism
												}
											} else {

												if ( $specialisms_parent_id != 0 ) {	// if parent is not root means not main parent
													$input_type_specialism = 'checkbox';   // if first level then select multiple specialism
												}
											}
											if ( $input_type_specialism == 'checkbox' ) {
												$cs_opt_array = array(
													'id' => '',
													'std' => '',
													'cust_id' => "specialisms_string_all",
													'cust_name' => 'specialisms_string_all',
												);
												$cs_form_fields2->cs_form_hidden_render( $cs_opt_array );
											}
											$random_ids = rand( 0, 999999 );

											if ( $all_specialisms != '' ) {
												$random_ids = rand( 0, 999999 );
												foreach ( $all_specialisms as $specialismsitem ) {
													############ get count for this itration ##########
													$job_id_para = '';
													if ( $cs_company_name != '' ) {
														//$post_ids = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE " . $company_name_id_condition . " UCASE(post_title) LIKE '%$cs_company_name%'  " . $alphabatic_qrystr . "  AND post_type='employer' AND post_status='publish'");
														$post_ids = $wpdb->get_col( "SELECT ID FROM $wpdb->users WHERE " . $company_name_id_condition . " UCASE(display_name) LIKE '%$cs_company_name%'" . $alphabatic_qrystr );
														if ( $post_ids ) {
															$specialisms_mypost = array( 'role' => 'cs_employer', 'order' => 'DESC', 'orderby' => 'registered',
																'include' => $post_ids,
																'meta_query' => array(
																	array(
																		'key' => 'cs_user_status',
																		'value' => 'active',
																		'compare' => '=',
																	),
																	array(
																		'key' => 'cs_specialisms',
																		'value' => $specialismsitem->slug,
																		'compare' => 'LIKE',
																	),
																	array(
																		'key' => 'cs_user_last_activity_date',
																		'value' => strtotime( date( $cs_employer_activity_date_formate ) ),
																		'compare' => '<=',
																	),
																	$location_condition_arr,
																)
															);
														}
													} else {
														$post_ids = $wpdb->get_col( "SELECT ID FROM $wpdb->users WHERE " . $company_name_id_condition . " 1=1 " . $alphabatic_qrystr );
														if ( $post_ids ) {

															$specialisms_mypost = array( 'role' => 'cs_employer', 'order' => 'DESC', 'orderby' => 'registered',
																'include' => $post_ids,
																'meta_query' => array(
																	array(
																		'key' => 'cs_user_status',
																		'value' => 'active',
																		'compare' => '=',
																	),
																	array(
																		'key' => 'cs_specialisms',
																		'value' => $specialismsitem->slug,
																		'compare' => 'LIKE',
																	),
																	array(
																		'key' => 'cs_user_last_activity_date',
																		'value' => strtotime( date( $cs_employer_activity_date_formate ) ),
																		'compare' => '<=',
																	),
																	$location_condition_arr,
																)
															);
														}
													}

													$specialisms_loop_count = new WP_User_Query( $specialisms_mypost );
													$specialisms_count_post = $specialisms_loop_count->total_users;
													###################################################
													if ( $input_type_specialism == 'checkbox' ) {
														if ( isset( $specialisms ) && is_array( $specialisms ) ) {
															if ( in_array( $specialismsitem->slug, $specialisms ) ) {
																$cs_opt_array = array(
																	'id' => '',
																	'std' => $specialismsitem->slug,
																	'cust_type' => $input_type_specialism,
																	'cust_id' => "checklistcomplete" . $random_ids,
																	'cust_name' => 'specialisms_string_all',
																	'return' => true,
																	'extra_atr' => 'checked="checked" onchange="javascript:submit_specialism_form(\'frm_all_specialisms' . $random_id . '\', \'specialisms_string_all\');"',
																);

																echo '<li class="' . $input_type_specialism . '">' . $cs_form_fields2->cs_form_text_render( $cs_opt_array ) . '
								<label for="checklist' . $random_ids . '">' . $specialismsitem->name . ' <span>(' . $specialisms_count_post . ')</span></label></li>';
															} else {
																$cs_opt_array = array(
																	'id' => '',
																	'std' => $specialismsitem->slug,
																	'cust_type' => $input_type_specialism,
																	'cust_id' => "checklistcomplete" . $random_ids,
																	'cust_name' => 'specialisms_string_all',
																	'return' => true,
																	'extra_atr' => 'onchange="submit_specialism_form(\'frm_all_specialisms' . $random_id . '\', \'specialisms_string_all\');"',
																);
																echo '<li class="' . $input_type_specialism . '">' . $cs_form_fields2->cs_form_text_render( $cs_opt_array ) . '
								<label for="checklist' . $random_ids . '">' . $specialismsitem->name . '<span>(' . $specialisms_count_post . ')</span></label></li>';
															}
														} else {
															$cs_opt_array = array(
																'id' => '',
																'std' => $specialismsitem->slug,
																'cust_type' => $input_type_specialism,
																'cust_id' => "checklistcomplete" . $random_ids,
																'cust_name' => '',
																'return' => true,
																'extra_atr' => 'onchange="submit_specialism_form(\'frm_all_specialisms' . $random_id . '\', \'specialisms_string_all\');" ',
															);


															echo '<li class="' . $input_type_specialism . '">' . $cs_form_fields2->cs_form_text_render( $cs_opt_array )
															. '<label for="checklist' . $random_ids . '">' . $specialismsitem->name . '<span>(' . $specialisms_count_post . ')</span></label></li>';
														}
													} else
													if ( $input_type_specialism == 'radio' ) {
														if ( isset( $specialisms ) && is_array( $specialisms ) ) {
															if ( in_array( $specialismsitem->slug, $specialisms ) ) {
																$cs_opt_array = array(
																	'id' => '',
																	'std' => $specialismsitem->slug,
																	'cust_type' => $input_type_specialism,
																	'cust_id' => "checklistcomplete" . $random_ids,
																	'cust_name' => 'specialisms',
																	'return' => true,
																	'extra_atr' => 'checked="checked" onchange="javascript:frm_all_specialisms' . $random_id . '.submit();" ',
																);
																echo '<li class="' . $input_type_specialism . '">' . $cs_form_fields2->cs_form_text_render( $cs_opt_array ) . '
							<label  class="active" for="checklistcomplete' . $random_ids . '">' . $specialismsitem->name . ' <span>(' . $specialisms_count_post . ')</span>  <i class="icon-check-circle"></i></label></li>';
															} else {
																$cs_opt_array = array(
																	'id' => '',
																	'std' => $specialismsitem->slug,
																	'cust_type' => $input_type_specialism,
																	'cust_id' => "checklistcomplete" . $random_ids,
																	'cust_name' => 'specialisms',
																	'return' => true,
																	'extra_atr' => 'onchange="javascript:frm_all_specialisms.submit();" ',
																);
																echo '<li class="' . $input_type_specialism . '">' . $cs_form_fields2->cs_form_text_render( $cs_opt_array ) . ''
																. '<label for="checklistcomplete' . $random_ids . '">' . $specialismsitem->name . '<span>(' . $specialisms_count_post . ')</span></label></li>';
															}
														} else {
															$cs_opt_array = array(
																'id' => '',
																'std' => $specialismsitem->slug,
																'cust_type' => $input_type_specialism,
																'cust_id' => "checklistcomplete" . $random_ids,
																'cust_name' => 'specialisms',
																'return' => true,
																'extra_atr' => 'onchange="javascript:frm_all_specialisms' . $random_id . '.submit();" ',
															);
															echo '<li class="' . $input_type_specialism . '">' . $cs_form_fields2->cs_form_text_render( $cs_opt_array )
															. '<label for="checklistcomplete' . $random_ids . '">' . $specialismsitem->name . '<span>(' . $specialisms_count_post . ')</span></label></li>';
														}
													}
													$random_ids ++;
												}
											}
											?>
										</ul>
									</form>

								</div>
							</div>
						</div>
					</div>
				</div>
				<div id="fade" class="black_overlay"></div>
				<?php
			}
			$main_col = '';
			if ( $a['cs_employer_searchbox'] == 'yes' ) {
				echo '<div class="row">';
				echo '<div class="cs-content-holder">';
				include('employer-searchbox.php');
			}
			if ( $a['cs_employer_searchbox'] == 'yes' ) {
				$main_col = 'col-lg-9 col-md-9 col-sm-12 col-xs-12';
			}
//             employer views
			if ( $a['cs_employer_view'] == 'simple' ) {
				include( 'views/cs_simple.php' );
			} elseif ( $a['cs_employer_view'] == 'alphabatic' ) {
				include( 'views/cs_alphabatic.php' );
			} elseif ( $a['cs_employer_view'] == 'grid' ) {
				include( 'views/cs_grid.php' );
			} elseif ( $a['cs_employer_view'] == 'list' ) {
				include( 'views/cs_list.php' );
			} elseif ( $a['cs_employer_view'] == 'box' ) {
				include( 'views/cs_box.php' );
			} elseif ( $a['cs_employer_view'] == 'fancy' ) {
				include( 'views/cs_fancy.php' );
			}
			//   end employer view
			if ( $a['cs_employer_searchbox'] == 'yes' ) {
				echo '</div>';
				echo '</div>';
			}
			?>
		</div>
		<?php
		if ( isset( $column_size ) && $column_size != '' ) {
			echo '</div>';
		}
		$employer_post_data = ob_get_clean();

		return $employer_post_data;
	}

	add_shortcode( 'cs_employer', 'cs_employer_listing' );
}
/*
 *
 * End Function how to show the employee listing
 *
 */