<?php
if (!function_exists('email_exists'))
    require_once ABSPATH . WPINC . '/registration.php';

// set query vars
function cs_query_vars($vars) {
    $vars[] = 'social-login';
    return $vars;
}

add_action('query_vars', 'cs_query_vars');

// set parse request
function cs_parse_request($wp) {

    $plugin_url = plugin_dir_url(__FILE__);
    if (array_key_exists('social-login', $wp->query_vars)) {
        if (!session_id()) {
            session_start();
        }
        $_REQUEST['state']  = (isset($_REQUEST['state']))?$_REQUEST['state']:'';
        $state  = base64_decode($_REQUEST['state']);
        $state  = json_decode($state);
        if (isset($wp->query_vars['social-login']) && $wp->query_vars['social-login'] == 'twitter') {
            cs_twitter_connect();
        } else if (isset($wp->query_vars['social-login']) && $wp->query_vars['social-login'] == 'twitter-callback') {
            cs_twitter_callback();
        } else if (isset($wp->query_vars['social-login']) && $wp->query_vars['social-login'] == 'linkedin' || (isset($state->social_login) && $state->social_login == 'linkedin' )) {
            require_once "linkedin/linkedin_function.php";
            die();
        } else if (isset($wp->query_vars['social-login']) && $wp->query_vars['social-login'] == 'facebook-callback') {
			if(isset($_REQUEST['state'])){
				$_SESSION['apply_job_id']		= $_REQUEST['state'];
			}
            require_once 'facebook/callback.php';
            die();
        }
        wp_die();
    }
    if (isset($_REQUEST['likedin-login-request'])) {

        if (!session_id()) {
            session_start();
        }
        $user_info = get_userdata($_REQUEST['likedin-login-request']);
        $ID = $_REQUEST['likedin-login-request'];

        $user_login = $user_info->user_login;
        $user_id = $user_info->ID;
        wp_set_current_user($user_id, $user_login);
        wp_set_auth_cookie($user_id);
        do_action('wp_login', $user_login, $user_info);
    }
}

add_action('parse_request', 'cs_parse_request');

// login process method
function cs_social_process_login($is_ajax = false) {
    global $cs_plugin_options, $wpdb;


    if (isset($_REQUEST['redirect_to']) && $_REQUEST['redirect_to'] != '') {
        $redirect_to = $_REQUEST['redirect_to'];
        // Redirect to https if user wants ssl
        if (isset($secure_cookie) && $secure_cookie && false !== strpos($redirect_to, 'wp-admin'))
            $redirect_to = preg_replace('|^http://|', 'https://', $redirect_to);
    } else {
        $redirect_to = admin_url();
    }

    $cs_page_id = isset($cs_plugin_options['cs_js_dashboard']) ? $cs_plugin_options['cs_js_dashboard'] : $_POST['redirect_to'];
    $redirect_to = get_permalink((int) $cs_page_id);
    $redirect_to = apply_filters('social_login_redirect_to', $redirect_to);
    $social_login_provider = $_REQUEST['social_login_provider'];
    $cs_provider_identity_key = 'social_login_' . $social_login_provider . '_id';
    $cs_provided_signature = $_REQUEST['social_login_signature'];
    switch ($social_login_provider) {
        case 'facebook':
            if (session_id() == '') {
                session_start();
            }
            $fields = array(
                'id', 'name', 'first_name', 'last_name', 'link', 'website',
                'gender', 'locale', 'about', 'email', 'hometown', 'location',
                'birthday'
            );
            cs_social_login_verify_signature($_REQUEST['social_login_access_token'], $cs_provided_signature, $redirect_to);
            $fb_json = json_decode(cs_http_get_contents("https://graph.facebook.com/me?access_token=" . $_REQUEST['social_login_access_token'] . "&fields=" . implode(',', $fields)));
            if (isset($fb_json->error->type) ? $fb_json->error->type : '' == 'OAuthException') {
                ?>
                <script>
                    alert("<?php echo _e('Please check facebook account developers settings.', 'jobhunt'); ?>");
                    window.close();
                </script>
                <?php
                exit();
            } else {
                $cs_provider_identity = $fb_json->{ 'id' };
                $cs_profile_pic = 'https://graph.facebook.com/' . $cs_provider_identity . '/picture';
                $cs_facebook = $fb_json->{ 'link' };
                $cs_gender = $fb_json->{ 'gender' };
                $cs_email = $fb_json->{ 'email' };
                $cs_first_name = $fb_json->{ 'first_name' };
                $cs_last_name = $fb_json->{ 'last_name' };
                $cs_profile_url = $fb_json->{ 'link' };
                $cs_gender = $fb_json->{ 'gender' };
                $cs_name = $cs_first_name . ' ' . $cs_last_name;
                $user_login = strtolower($cs_first_name . $cs_last_name);
            }
            break;
        case 'twitter':
            $cs_provider_identity = $_REQUEST['social_login_twitter_identity'];
            cs_social_login_verify_signature($cs_provider_identity, $cs_provided_signature, $redirect_to);
            $cs_name = $_REQUEST['social_login_name'];
            $cs_twitter = 'https://twitter.com/' . $_REQUEST['social_login_screen_name'];
            $names = explode(" ", $cs_name);
            $cs_first_name = '';
            if (isset($names[0]))
                $cs_first_name = $names[0];
            $cs_last_name = '';
            if (isset($names[1]))
                $cs_last_name = $names[1];
            $cs_screen_name = $_REQUEST['social_login_screen_name'];
            $cs_profile_url = '';
            $cs_gender = '';
            // Get host name from URL
            $site_url = parse_url(site_url());
            $cs_email = 'tw_' . md5($cs_provider_identity) . '@' . $site_url['host'] . '.com';
            $user_login = $cs_screen_name;

            break;
        default:
            break;
    }

    // Get user by meta
    $user_id = cs_social_get_user_by_meta($cs_provider_identity_key, $cs_provider_identity);

    if ($user_id) {
        $current_user = get_userdata($user_id);
        $user_roles = isset($current_user->roles) ? $current_user->roles : '';
        if (($user_roles != '' && in_array("cs_candidate", $user_roles))) {
            $user_data = get_userdata($user_id);
            $user_login = $user_data->user_login;

            // update user meta
            update_user_meta($user_id, 'cs_user_last_activity_date', strtotime(date('d-m-Y H:i:s')));
            update_user_meta($user_id, 'cs_allow_search', 'yes');
            update_user_meta($user_id, 'cs_user_status', 'active');
            if (isset($cs_facebook) && $cs_facebook != '') {
                update_user_meta($user_id, 'cs_facebook', $cs_facebook);
            }
            if (isset($cs_twitter) && $cs_twitter != '') {
                update_user_meta($user_id, 'cs_twitter', $cs_twitter);
            }
        } else {
            ?>
            <script>
                alert("<?php echo _e('This profile is already linked with other account. Linking process failed!', 'jobhunt'); ?>");
                window.opener.location.reload();
                window.close();
            </script>
            <?php
            $ID = Null;     // set null bcz this user exist in other Role
        }
    } elseif ($user_id = email_exists($cs_email)) { // User not found by provider identity, check by email
        $current_user = get_userdata($user_id);
        $user_roles = isset($current_user->roles) ? $current_user->roles : '';
        if (($user_roles != '' && in_array("cs_candidate", $user_roles))) {
            // update user meta
            update_user_meta($user_id, $cs_provider_identity_key, $cs_provider_identity);

            $user_data = get_userdata($user_id);
            $user_login = $user_data->user_login;

            // update user meta
            update_user_meta($user_id, 'cs_user_last_activity_date', strtotime(date('d-m-Y H:i:s')));
            update_user_meta($user_id, 'cs_allow_search', 'yes');
            update_user_meta($user_id, 'cs_user_status', 'active');
            if (isset($cs_facebook) && $cs_facebook != '') {
                update_user_meta($user_id, 'cs_facebook', $cs_facebook);
            }
            if (isset($cs_twitter) && $cs_twitter != '') {
                update_user_meta($user_id, 'cs_twitter', $cs_twitter);
            }
        } else {
            ?>
            <script>
                alert("<?php echo _e('This profile is already linked with other account. Linking process failed!', 'jobhunt'); ?>");
                window.opener.location.reload();
                window.close();
            </script>
            <?php
            $ID = Null;     // set null bcz this user exist in other Role
        }
    } else { // Create new user and associate provider identity
        if (get_option('users_can_register')) {

            $user_login = cs_get_unique_username($user_login);
            $userdata = array('user_login' => $user_login, 'user_email' => $cs_email, 'first_name' => $cs_first_name, 'last_name' => $cs_last_name, 'user_url' => $cs_profile_url, 'user_pass' => wp_generate_password(), 'role' => 'cs_candidate');
            // Create a new user
            $user_id = wp_insert_user($userdata);
            
            $random_password = wp_generate_password($length = 12, $include_standard_special_chars = false);
            wp_set_password( $random_password, $user_id );
            $reg_user = get_user_by('ID', $user_id);
            // Site owner email hook
            do_action('jobhunt_new_user_notification_site_owner', $reg_user->data->user_login, $reg_user->data->user_email);
            // send candidate email template hook
            do_action( 'jobhunt_candidate_register', $reg_user, $random_password );
            
            $new_user = new WP_User($user_id);
            // update user meta
            $new_user->set_role('cs_candidate');
            update_user_meta($user_id, 'cs_user_last_activity_date', strtotime(date('d-m-Y H:i:s')));
            update_user_meta($user_id, 'cs_allow_search', 'yes');
            update_user_meta($user_id, 'cs_user_status', 'active');
			
			if(isset($_SESSION['apply_job_id']) && $_SESSION['apply_job_id'] != '' ){
				$redirect_to = get_permalink((int) $_SESSION['apply_job_id']);
			}
			
            if (isset($cs_facebook) && $cs_facebook != '') {
                update_user_meta($user_id, 'cs_facebook', $cs_facebook);
            }
            if (isset($cs_twitter) && $cs_twitter != '') {
                update_user_meta($user_id, 'cs_twitter', $cs_twitter);
            }

            update_user_meta($user_id, 'cs_user_registered', $social_login_provider);

            if ($user_id && is_integer($user_id)) {
                update_user_meta($user_id, $cs_provider_identity_key, $cs_provider_identity);
            }
            if (isset($cs_plugin_options['cs_candidate_review_option']) && $cs_plugin_options['cs_candidate_review_option'] == 'on') {
                $wpdb->update(
                        $wpdb->prefix . 'users', array('user_status' => 1), array('ID' => esc_sql($user_id))
                );
                update_user_meta($user_id, 'cs_user_status', 'active');
            } else {
                $wpdb->update(
                        $wpdb->prefix . 'users', array('user_status' => 0), array('ID' => esc_sql($user_id))
                );
                update_user_meta($user_id, 'cs_user_status', 'inactive');
            }
        } else {
            add_filter('wp_login_errors', 'wp_login_errors');

            return;
        }
    }

    wp_set_auth_cookie($user_id);

    do_action('social_connect_login', $user_login);

    if ($is_ajax) {
        echo '{"redirect":"' . $redirect_to . '"}';
    } else {
        wp_safe_redirect($redirect_to);
    }

    exit();
}

// login error
function cs_login_errors($errors) {
    $errors->errors = array();
    $errors->add('registration_disabled', '<strong>' . __('ERROR', 'jobhunt') . '</strong>:', __('Registration is disabled.', 'jobhunt'));




    return $errors;
}

// get unique username
function cs_get_unique_username($user_login, $c = 1) {
    if (username_exists($user_login)) {
        if ($c > 5)
            $append = '_' . substr(md5($user_login), 0, 3) . $c;
        else
            $append = $c;

        $user_login = apply_filters('social_login_username_exists', $user_login . $append);
        return cs_get_unique_username($user_login, ++$c);
    } else {
        return $user_login;
    }
}

add_action('login_form_social_login', 'cs_social_process_login');

// ajax login
function cs_ajax_login() {
    if (isset($_POST['login_submit']) && $_POST['login_submit'] == 'ajax' && // Plugins will need to pass this param
            isset($_POST['action']) && $_POST['action'] == 'social_login')
        cs_social_process_login(true);
}

add_action('init', 'cs_ajax_login');

// filter user avatar
function cs_filter_avatar($avatar, $id_or_email, $size, $default, $alt) {
    $custom_avatar = '';
    $social_id = '';
    $provider_id = '';
    $user_id = (!is_integer($id_or_email) && !is_string($id_or_email) && get_class($id_or_email)) ? $id_or_email->user_id : $id_or_email;

    if (!empty($user_id)) {

        $providers = array('facebook', 'twitter');

        $social_login_provider = isset($_COOKIE['social_login_current_provider']) ? $_COOKIE['social_login_current_provider'] : '';
        if (!empty($social_login_provider) && $social_login_provider == 'twitter') {
            $providers = array('twitter', 'facebook');
        }
        foreach ($providers as $search_provider) {
            $social_id = get_user_meta($user_id, 'social_login_' . $search_provider . '_id', true);
            if (!empty($social_id)) {
                $provider_id = $search_provider;
                break;
            }
        }
    }
    if (!empty($social_id)) {
        
    }

    if (!empty($custom_avatar)) {
        update_user_meta($user_id, 'custom_avatar', $custom_avatar);
        $return = '<img class="avatar" src="' . esc_url($custom_avatar) . '" style="width:' . $size . 'px" alt="' . $alt . '" />';
    } else if ($avatar) {
        // gravatar
        $return = $avatar;
    } else {
        // default
        $return = '<img class="avatar" src="' . esc_url($default) . '" style="width:' . $size . 'px" alt="' . $alt . '" />';
    }

    return $return;
}

// social add comment meta
function cs_social_add_comment_meta($comment_id) {
    $social_login_comment_via_provider = isset($_POST['social_login_comment_via_provider']) ? $_POST['social_login_comment_via_provider'] : '';
    if ($social_login_comment_via_provider != '') {
        update_comment_meta($comment_id, 'social_login_comment_via_provider', $social_login_comment_via_provider);
    }
}

add_action('comment_post', 'cs_social_add_comment_meta');

// social comment meta
function cs_social_comment_meta($link) {
    global $comment;
    $images_url = get_template_directory_uri() . '/media/img/';
    if (is_object($comment)) {
        $social_login_comment_via_provider = get_comment_meta($comment->comment_ID, 'social_login_comment_via_provider', true);
        if ($social_login_comment_via_provider && current_user_can('manage_options')) {
            return $link . '&nbsp;<img class="social_login_comment_via_provider" alt="' . $social_login_comment_via_provider . '" src="' . $images_url . $social_login_comment_via_provider . '_16.png"  />';
        } else {
            return $link;
        }
    }
    return $link;
}

add_action('get_comment_author_link', 'cs_social_comment_meta');

// social login form
function cs_comment_form_social_login() {
    if (comments_open() && !is_user_logged_in()) {
        cs_social_login_form();
    }
}

// login page url
function cs_login_page_uri() {
    global $cs_form_fields2;
    $cs_opt_array = array(
        'id' => '',
        'cust_id' => 'social_login_form_uri',
        'std' => esc_url(site_url('wp-login.php', 'login_post')),
        'cust_type' => 'hidden',
        'classes' => '',
    );

    $cs_form_fields2->cs_form_text_render($cs_opt_array);
}

add_action('wp_footer', 'cs_login_page_uri');

// get user by meta key
function cs_social_get_user_by_meta($meta_key, $meta_value) {
    global $wpdb;

    $sql = "SELECT user_id FROM $wpdb->usermeta WHERE meta_key = '%s' AND meta_value = '%s'";
    return $wpdb->get_var($wpdb->prepare($sql, $meta_key, $meta_value));
}

// generate social signature
function cs_social_generate_signature($data) {
    return hash('SHA256', AUTH_KEY . $data);
}

// login verify signature
function cs_social_login_verify_signature($data, $signature, $redirect_to) {
    $generated_signature = cs_social_generate_signature($data);

    if ($generated_signature != $signature) {
        wp_safe_redirect($redirect_to);
        exit();
    }
}

// get the contents of url
function cs_http_get_contents($url) {
    $response = wp_remote_get($url);

    if (is_wp_error($response)) {
        die(sprintf(__('Something went wrong: %s', 'jobhunt'), $response->get_error_message()));
    } else {
        return $response['body'];
    }
}

// add custom styling
function cs_add_stylesheets() {
    if (is_admin()) {
        if (!wp_style_is('social_login', 'registered')) {

            wp_register_style("social_login_css", plugins_url('media/css/cs-social-style.css', __FILE__));
        }

        if (did_action('wp_print_styles')) {
            wp_print_styles('social_login');
            wp_print_styles('wp-jquery-ui-dialog');
        } else {
            wp_enqueue_style("social_login");
            wp_enqueue_style("wp-jquery-ui-dialog");
        }
    }
}

add_action('login_enqueue_scripts', 'cs_add_stylesheets');
add_action('wp_head', 'cs_add_stylesheets');

// add admin side styling
function cs_add_admin_stylesheets() {
    if (is_admin()) {
        if (!wp_style_is('social_login', 'registered')) {
            wp_register_style("social_login_css", plugins_url('media/css/cs-social-style.css', __FILE__));
        }

        if (did_action('wp_print_styles')) {
            wp_print_styles('social_login');
        } else {
            wp_enqueue_style("social_login");
        }
    }
}

add_action('admin_print_styles', 'cs_add_admin_stylesheets');

// add javascripts files
function cs_add_javascripts() {
    if (is_admin()) {
        $deps = array('jquery', 'jquery-ui-core', 'jquery-ui-dialog');
        $wordpress_enabled = 0;


        if ($wordpress_enabled) {
            $deps[] = 'jquery-ui-dialog';
        }

        if (!wp_script_is('social_login_js', 'registered'))
            wp_register_script('social_login_js', plugins_url('media/js/cs-connect.js', __FILE__), $deps);

        wp_enqueue_script('social_login_js');
        wp_localize_script('social_login_js', 'social_login_data', array('wordpress_enabled' => $wordpress_enabled));
    }
}

add_action('login_enqueue_scripts', 'cs_add_javascripts');
add_action('wp_enqueue_scripts', 'cs_add_javascripts');

// Twitter Callback

function cs_twitter_callback() {
    global $cs_plugin_options;
    $consumer_key = isset($cs_plugin_options['cs_consumer_key']) ? $cs_plugin_options['cs_consumer_key'] : '';
    $consumer_secret = isset($cs_plugin_options['cs_consumer_secret']) ? $cs_plugin_options['cs_consumer_secret'] : '';

    if (!class_exists('TwitterOAuth')) {
        require_once wp_jobhunt::plugin_dir() . 'include/cs-twitter/twitteroauth.php';
    }

    if (!empty($_SESSION)) {
        $connection = new TwitterOAuth($consumer_key, $consumer_secret, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
        $access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);
        $_SESSION['access_token'] = $access_token;
        unset($_SESSION['oauth_token']);
        unset($_SESSION['oauth_token_secret']);
    }

    if (200 == $connection->http_code) {
        $_SESSION['status'] = 'verified';
        $user = $connection->get('account/verify_credentials');
        $name = $user->name;
        $screen_name = $user->screen_name;
        $twitter_id = $user->id;
        $signature = cs_social_generate_signature($twitter_id);
        ?>
        <html>
            <head>
                <script>
                    function init() {
                        window.opener.wp_social_login({'action': 'social_login', 'social_login_provider': 'twitter',
                            'social_login_signature': '<?php echo esc_attr($signature) ?>',
                            'social_login_twitter_identity': '<?php echo esc_attr($twitter_id) ?>',
                            'social_login_screen_name': '<?php echo esc_attr($screen_name) ?>',
                            'social_login_name': '<?php echo esc_attr($name) ?>'});
                        window.close();
                    }
                </script>
            </head>
            <body onLoad="init();"></body>
        </html>
        <?php
        die();
    } else {

        echo __('Login error', 'jobhunt');
    }
}

// Twitter connect
function cs_twitter_connect() {
    global $cs_plugin_options;
    if (!class_exists('TwitterOAuth')) {
        require_once wp_jobhunt::plugin_dir() . 'include/cs-twitter/twitteroauth.php';
    }
    $consumer_key = $cs_plugin_options['cs_consumer_key'];
    $consumer_secret = $cs_plugin_options['cs_consumer_secret'];
    $twitter_oath_callback = home_url('index.php?social-login=twitter-callback');
    if ($consumer_key != '' && $consumer_secret != '') {
        $connection = new TwitterOAuth($consumer_key, $consumer_secret);
        $request_token = $connection->getRequestToken($twitter_oath_callback);

        if (!empty($request_token)) {
            $_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
            $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
        }

        switch ($connection->http_code) {
            case 200:
                $url = $connection->getAuthorizeURL($token);
                wp_redirect($url);
                break;
            default:
                _e('There is problem while connecting to twitter', 'jobhunt');
        }
        exit();
    }
}

// Facebook Callback

function cs_facebook_callback() {
    global $cs_plugin_options;

    require_once plugin_dir_url(__FILE__) . 'facebook/facebook.php';

    $client_id = $cs_plugin_options['cs_facebook_app_id'];
    $secret_key = $cs_plugin_options['cs_facebook_secret'];


    if (isset($_GET['code'])) {
        $code = $_GET['code'];
        $access_token = $code;
        parse_str(cs_http_get_contents("https://graph.facebook.com/oauth/access_token?" .
                        'client_id=' . $client_id . '&redirect_uri=' . home_url('index.php?social-login=facebook-callback') .
                        '&client_secret=' . $secret_key .
                        '&code=' . urlencode($code)));
        $signature = cs_social_generate_signature($access_token);
        do_action('social_login_before_register_facebook', $code, $signature, $access_token);
        ?>
        <html>
            <head>
                <script>
                    function init() {
                        window.opener.wp_social_login({'action': 'social_login', 'social_login_provider': 'facebook',
                            'social_login_signature': '<?php echo esc_attr($signature) ?>',
                            'social_login_access_token': '<?php echo esc_attr($access_token) ?>'});
                        window.close();
                    }
                </script>
            </head>
            <body onLoad="init();"></body>
        </html>
        <?php
    } else {
        $redirect_uri = urlencode(plugin_dir_url(__FILE__) . 'facebook/callback.php');
        wp_redirect('https://graph.facebook.com/oauth/authorize?client_id=' . $client_id . '&redirect_uri=' . $redirect_uri . '&scope=email');
    }
}
