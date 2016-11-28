<?php
if (!isset($_SESSION)) {
    session_start();
}
require_once('linkedin_oauth2.class.php');
$_REQUEST['state']  = (isset($_REQUEST['state']))?$_REQUEST['state']:'';
$state  = base64_decode($_REQUEST['state']);
$state  = json_decode($state);
if ((isset($_REQUEST['linkedin']) && $_REQUEST['linkedin'] == 'yes') || (isset($_SESSION['linkedin']) && $_SESSION['linkedin'] == 'yes') || (isset($state->linkedin) && $state->linkedin == 'yes')) {

    global $cs_plugin_options, $wpdb;
    
    if (isset($_REQUEST['linkedin']))
        $_SESSION['linkedin'] = $_REQUEST['linkedin'];
    else {
        unset($_SESSION['linkedin']);
    }
    if (isset($cs_plugin_options['cs_linkedin_app_id']))
        $linkedin_app_id = $cs_plugin_options['cs_linkedin_app_id'];
    if (isset($cs_plugin_options['cs_linkedin_secret']))
        $linkedin_secret = $cs_plugin_options['cs_linkedin_secret'];

    try {
        // start the session
        //if (!session_start()) {
        if (!isset($_SESSION)) {
            throw new LinkedInException('This script requires session support, which appears to be disabled according to session_start().');
        }
        // display constants
        $API_CONFIG = array(
            'appKey' => $linkedin_app_id,
            'appSecret' => $linkedin_secret,
        );
        
        define('PORT_HTTP', '80');
        define('PORT_HTTP_SSL', '443');
        // set index
        $_REQUEST['ltype'] = (isset($_REQUEST['ltype'])) ? $_REQUEST['ltype'] : '';
        $_REQUEST['ltype'] = (isset($state->ltype))? $state->ltype:$_REQUEST['ltype'];
        switch ($_REQUEST['ltype']) {
            case 'initiate':
                if (isset($_REQUEST['apply_job_id']) && $_REQUEST['apply_job_id'] != '') {
                    $_SESSION['apply_job_id'] = $_REQUEST['apply_job_id'];
                }
                /**
                 * Handle user initiated LinkedIn connection, create the LinkedIn object.
                 */
                // check for the correct http protocol (i.e. is this script being served via http or https)
                if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
                    $protocol = 'https';
                } else {
                    $protocol = 'http';
                }
                // set the callback url
                $API_CONFIG['callbackUrl'] = site_url();
                $OBJ_linkedin = new LinkedIn( $linkedin_app_id, $linkedin_secret, $API_CONFIG['callbackUrl'], 'r_basicprofile,r_emailaddress');
                
                // check for response from LinkedIn
                
                $_GET['lResponse'] = (isset($_GET['lResponse'])) ? $_GET['lResponse'] : '0';
                $_GET['lResponse'] = (isset($state->lResponse))? $state->lResponse:$_GET['lResponse'];
                $_REQUEST['apply_job_id'] = (isset($_REQUEST['apply_job_id'])) ? $_REQUEST['apply_job_id'] : '';
                $_REQUEST['ltype'] = (isset($_REQUEST['ltype'])) ? $_REQUEST['ltype'] : '';
                $_REQUEST['linkedin'] = (isset($_REQUEST['linkedin'])) ? $_REQUEST['linkedin'] : '';
                $_REQUEST['social-login'] = (isset($_REQUEST['social-login'])) ? $_REQUEST['social-login'] : '';
                
                if (!$_GET['lResponse']) {
                    $stateString = json_encode(
                            array(
                                "apply_job_id" => $_REQUEST["apply_job_id"],
                                "ltype" => $_REQUEST["ltype"],
                                "lResponse" => 1,
                                "linkedin" => $_REQUEST["linkedin"],
                                "social_login" => $_REQUEST["social-login"]
                                )
                            );
                    $OBJ_linkedin->addState( base64_encode($stateString) );
                    // LinkedIn hasn't sent us a response, the user is initiating the connection
                    // send a request for a LinkedIn access token
                    $OBJ_linkedin->resetToken();
                    if ( false === $OBJ_linkedin->authorize() ){
                        // bad token request
                        ?>
                        <script>
                            alert("<?php echo _e('Request token retrieval failed. Please check your settings and then try again.!', 'jobhunt'); ?>");
                            window.opener.location.reload();
                            window.close();
                        </script>
                        <?php
                    }
                    
                } else {



                    // LinkedIn has sent a response, user has granted permission, take the temp access token, the user's secret and the verifier to request the user's real secret key
                    $_REQUEST['state']  = (isset($_REQUEST['state']))?$_REQUEST['state']:'';
                    $state  = base64_decode($_REQUEST['state']);
                    $state   = json_decode($state);
                    $OBJ_linkedin->authorize();
                    
                    //exit;
                    $response = $OBJ_linkedin->fetch('GET', '/v1/people/~:(id,first-name,last-name,picture-url,email-address,phone-numbers,headline)');
                   
                    $OBJ_linkedin->resetToken();
                    
                    if ( isset( $response->id ) && $response->id !== '') {
                        // the request went through without an error, gather user's 'access' tokens
                        $_SESSION['oauth']['linkedin']['access'] = $response;
                        // set the user as authorized for future quick reference
                        $_SESSION['oauth']['linkedin']['authorized'] = TRUE;
                        $_SESSION['oauth']['linkedin']['authorized'] = (isset($_SESSION['oauth']['linkedin']['authorized'])) ? $_SESSION['oauth']['linkedin']['authorized'] : FALSE;
                       
                        if ( isset( $response->id ) && $response->id !== '') {
                            
                            $linkedin_id = (string) $response->id;
                            if( isset ( $response->firstName ) && '' !== $response->firstName ){
                                $linkedin_firstname = (string) $response->firstName;
                            }
                            if( isset ( $response->lastName ) && '' !== $response->lastName ){
                                $linkedin_lastname = (string) $response->lastName;
                            }
                            if( isset ( $response->pictureUrl ) && '' !== $response->pictureUrl ){
                                $linkedin_picture_url = (string) $response->pictureUrl;
                            }
                            if( isset ( $response->emailAddress ) && '' !== $response->emailAddress ){
                                $linkedin_email = (string) $response->emailAddress;
                            }
                            if( isset ( $response->phoneNumbers ) && '' !== $response->phoneNumbers ){
                                $linkedin_phone = (string) $response->phoneNumbers;
                            }
                            if( isset ( $response->headline ) && '' !== $response->headline ){
                                $linkedin_job_title = (string) $response->headline;
                            }
                          
                            #############################################
                            #       Login / register as guest user      #
                            #############################################
                            $email = filter_var($linkedin_email, FILTER_SANITIZE_EMAIL);
                            if (!is_user_logged_in()) {
                                $ID = email_exists($email);
                                if ($ID == NULL) { // Register
                                    //print_r($ID);exit;
                                    if ($ID == false) { // Real register
                                        //require_once get_template_directory() . WPINC . '/registration.php';
                                        $random_password = wp_generate_password($length = 12, $include_standard_special_chars = false);
                                        if (!isset($cs_linkedin_settings['linkedin_user_prefix']))
                                            $cs_linkedin_settings['linkedin_user_prefix'] = 'linkedin-';
                                        $sanitized_user_login = sanitize_user($cs_linkedin_settings['linkedin_user_prefix'] . $linkedin_firstname);
                                        if (!validate_username($sanitized_user_login)) {
                                            $sanitized_user_login = sanitize_user($cs_linkedin_settings['linkedin_user_prefix'] . $result->{'id'});
                                        }
                                        $defaul_user_name = $sanitized_user_login;
                                        $i = 1;
                                        while (username_exists($sanitized_user_login)) {
                                            $sanitized_user_login = $defaul_user_name . $i;
                                            $i++;
                                        }
                                        //$ID = wp_create_user($sanitized_user_login, $random_password, $email);
										$userdata = array('user_login' => $sanitized_user_login, 'user_email' => $email, 'user_pass' => $random_password, 'role' => 'cs_candidate');
										$ID = wp_insert_user($userdata);
                                                                                
                                        if (!is_wp_error($ID)) {
											
											$reg_user = get_user_by('ID', $ID);
											// Site owner email hook
											do_action('jobhunt_new_user_notification_site_owner', $reg_user->data->user_login, $reg_user->data->user_email);
											// send candidate email template hook
											do_action( 'jobhunt_candidate_register', $reg_user, $random_password );
											
                                            $new_user = new WP_User($ID);
                                            // update user meta
                                            $new_user->set_role('cs_candidate');
                                            update_user_meta($ID, 'cs_user_last_activity_date', strtotime(date('d-m-Y H:i:s')));
                                            update_user_meta($ID, 'cs_allow_search', 'yes');
                                            update_user_meta($ID, 'cs_user_status', 'active');
                                            // Notification
                                            //wp_new_user_notification($ID, $random_password);
                                            $user_info = get_userdata($ID);
                                            wp_update_user(array(
                                                'ID' => $ID,
                                                'display_name' => $linkedin_firstname . " " . $linkedin_lastname,
                                                'first_name' => $linkedin_firstname,
                                                'last_name' => $linkedin_lastname,
                                            ));
                                            update_user_meta($ID, 'cs_linkedin_default_password', $user_info->user_pass);
                                            update_user_meta($ID, 'cs_user_linkedin_id', $linkedin_id);
                                            update_user_meta($ID, 'cs_user_registered', 'linkedin');
                                            update_post_meta($ID, 'user_img', $linkedin_picture_url);

                                            if (isset($cs_plugin_options['cs_candidate_review_option']) && $cs_plugin_options['cs_candidate_review_option'] == 'on') {
                                                $wpdb->update(
                                                        $wpdb->prefix . 'users', array('user_status' => 1), array('ID' => esc_sql($ID))
                                                );
                                                update_user_meta($ID, 'cs_user_status', 'active');
                                            } else {
                                                $wpdb->update(
                                                        $wpdb->prefix . 'users', array('user_status' => 0), array('ID' => esc_sql($ID))
                                                );
                                                update_user_meta($ID, 'cs_user_status', 'inactive');
                                            }
                                        } else {
                                            return;
                                        }
                                    }

                                    if (isset($cs_linkedin_settings['linkedin_redirect_reg']) && $cs_linkedin_settings['linkedin_redirect_reg'] != '' && $cs_linkedin_settings['linkedin_redirect_reg'] != 'auto') {
                                        set_site_transient(cs_linkedin_uniqid() . '_linkedin_r', $cs_linkedin_settings['linkedin_redirect_reg'], 3600);
                                    }
                                } else { // if already exist
                                    $current_user = get_userdata($ID);
                                    $user_roles = isset($current_user->roles) ? $current_user->roles : '';
                                    if (($user_roles != '' && in_array("cs_candidate", $user_roles))) {
                                        // update user meta
                                        update_user_meta($ID, 'cs_user_last_activity_date', strtotime(date('d-m-Y H:i:s')));
                                        update_user_meta($ID, 'cs_allow_search', 'yes');
                                        update_user_meta($ID, 'cs_user_status', 'active');
                                    } else {
                                        ?>
                                        <script>
                                            alert("<?php echo _e('This Linked-in profile is already linked with other account. Linking process failed!', 'jobhunt'); ?>");
                                            window.opener.location.reload();
                                            window.close();
                                        </script>
                                        <?php
                                        $ID = Null;     // set null bcz this user exist in other Role
                                    }
                                }

                                if ($ID) {
                                    // start the session
                                    //if (session_id() == '') {
                                    //     session_start();
                                    // }
                                    //echo "userinfo===<pre>";print_r($status);exit;
//                                    $user_info = get_userdata($ID);
////                                    //print_r($user_info);
                                    //exit;
                                    if( isset( $state->apply_job_id ) && $state->apply_job_id != '' ){
                                        if (!session_id()) {
                                            session_start();
                                        }
                                        $_SESSION['apply_job_id']   = $state->apply_job_id;
                                        $job_link       = get_permalink( $state->apply_job_id );
                                        $job_link .= (parse_url($job_link, PHP_URL_QUERY) ? '&' : '?') . 'likedin-login-request='.$ID;
                                       
                                        ?>
                                            <script>
                                                window.opener.location.href = "<?php echo $job_link; ?>";
                                                window.close();
                                            </script>
                                        <?php
                                    } else {
                                    ?>
                                    <script>
                                        window.opener.location.href = "index.php?likedin-login-request=<?php echo $ID; ?>";
                                        window.close();
                                    </script>
                                    <?php
                                    }
                                    exit();
                                }
                            } else {
                                $user_info = wp_get_current_user();
                                set_site_transient($user_info->ID . '_cs_linkedin_admin_notice', __('This Linked-in profile is already linked with other account. Linking process failed!', 'jobhunt'), 3600);
                            }

                            #############################################
                            #       End Login / Register User           #
                            #############################################                           
                        }
                    } else {
                        // bad token access
                        ?>
                        <script>
                            alert("<?php echo _e('Request token retrieval failed. Please check your settings and then try again.!', 'jobhunt'); ?>");
                            window.opener.location.reload();
                            window.close();
                        </script>
                        <?php
                    }
                }//exit;
                break;

            default:
                // nothing being passed back, display demo page
                // check PHP version
                if (version_compare(PHP_VERSION, '5.0.0', '<')) {
                    throw new LinkedInException('You must be running version 5.x or greater of PHP to use this library.');
                }

                // check for cURL
                if (extension_loaded('curl')) {
                    $curl_version = curl_version();
                    $curl_version = $curl_version['version'];
                } else {
                    throw new LinkedInException('You must load the cURL extension to use this library.');
                }
                break;
        }
    } catch (LinkedInException $e) {
        // exception raised by library call
        echo $e->getMessage();
    }
}
