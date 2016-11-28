<?php
/**
 * @Social Network widget Class
 *
 *
 */
if (!class_exists('cs_social_meida_followers_widget')) {

    class cs_social_meida_followers_widget extends WP_Widget {
        /*
         *
         * Start Function how to create constructer
         *
         */

        public function __construct() {
            parent::__construct(
                    'cs_social_meida_followers_widget', // Base ID
                    __('CS : Social Media Followers Widget', 'jobhunt'), // Name
                    array('classname' => 'socialmedia-widget', 'description' => __('Recent Posts from category.', 'jobhunt'),) // Args
            );
        }

        /*
         *
         * End Function how to create constructer
         * Start Function how to create function instance 
         */

        function form($instance) {
            global $cs_theme_form_fields, $cs_html_fields, $cs_theme_html_fields;
            global $cs_theme_option, $cs_plugin_options;
            $instance = wp_parse_args((array) $instance, array('title' => ''));
            $title = $instance['title'];
            $facebook_page_url = isset($instance['facebook_page_url']) ? esc_attr($instance['facebook_page_url']) : '';
            $facebook_acess_token = isset($instance['facebook_acess_token']) ? esc_attr($instance['facebook_acess_token']) : '';
            $facebook_text = isset($instance['facebook_text']) ? esc_attr($instance['facebook_text']) : 'Facebook Fans';
            $twitter_text = isset($instance['twitter_text']) ? esc_attr($instance['twitter_text']) : 'Twitter Followers';
            $twitter_username = isset($instance['twitter_username']) ? esc_attr($instance['twitter_username']) : '';
            $googleplus_text = isset($instance['googleplus_text']) ? esc_attr($instance['googleplus_text']) : 'Google+ Followers';
            $googleplus_id = isset($instance['googleplus_id']) ? esc_attr($instance['googleplus_id']) : '';
            $youtube_text = isset($instance['youtube_text']) ? esc_attr($instance['youtube_text']) : 'Youtube Followers';
            $youtube_id = isset($instance['youtube_id']) ? esc_attr($instance['youtube_id']) : '';
            $vimeo_text = isset($instance['vimeo_text']) ? esc_attr($instance['vimeo_text']) : 'vimeo Followers';
            $vimeo_id = isset($instance['vimeo_id']) ? esc_attr($instance['vimeo_id']) : '';
            $dribble_text = isset($instance['dribble_text']) ? esc_attr($instance['dribble_text']) : 'Dribble Followers';
            $dribble_id = isset($instance['dribble_id']) ? esc_attr($instance['dribble_id']) : '';
            $consumer_key = $consumer_secret = $access_token = $access_token_secret = '';
            if (isset($cs_plugin_options['consumer_key']))
                $consumer_key = $cs_plugin_options['consumer_key'];
            if (isset($cs_plugin_options['consumer_secret']))
                $consumer_secret = $cs_plugin_options['consumer_secret'];
            if (isset($cs_plugin_options['access_token']))
                $access_token = $cs_plugin_options['access_token'];
            if (isset($cs_plugin_options['consumer_secret']))
                $access_token_secret = $cs_plugin_options['access_token_secret'];

            $cs_opt_array = array(
                'name' => __('Widget Title', 'jobhunt'),
                'desc' => '',
                'hint_text' => '',
                'echo' => true,
                'field_params' => array(
                    'std' => esc_attr($title),
                    'id' => cs_allow_special_char($this->get_field_id('title')),
                    'classes' => 'upcoming',
                    'cust_id' => cs_allow_special_char($this->get_field_name('title')),
                    'cust_name' => cs_allow_special_char($this->get_field_name('title')),
                    'return' => true,
                    'required' => false
                ),
            );
            echo force_balance_tags($cs_html_fields->cs_text_field($cs_opt_array));

            $cs_opt_array = array(
                'name' => __('Facebook Text', 'jobhunt'),
                'desc' => '',
                'hint_text' => '',
                'echo' => true,
                'field_params' => array(
                    'std' => esc_attr($facebook_text),
                    'id' => cs_allow_special_char($this->get_field_id('facebook_text')),
                    'classes' => 'upcoming',
                    'cust_id' => cs_allow_special_char($this->get_field_name('facebook_text')),
                    'cust_name' => cs_allow_special_char($this->get_field_name('facebook_text')),
                    'return' => true,
                    'required' => false
                ),
            );
            echo force_balance_tags($cs_html_fields->cs_text_field($cs_opt_array));

            $cs_opt_array = array(
                'name' => __('Facebook Acess token', 'jobhunt'),
                'desc' => '',
                'hint_text' => '',
                'echo' => true,
                'field_params' => array(
                    'std' => esc_attr($facebook_acess_token),
                    'id' => cs_allow_special_char($this->get_field_id('facebook_acess_token')),
                    'classes' => 'upcoming',
                    'cust_id' => cs_allow_special_char($this->get_field_name('facebook_acess_token')),
                    'cust_name' => cs_allow_special_char($this->get_field_name('facebook_acess_token')),
                    'return' => true,
                    'required' => false
                ),
            );
            echo force_balance_tags($cs_html_fields->cs_text_field($cs_opt_array));

            $cs_opt_array = array(
                'name' => __('Facebook Page Id', 'jobhunt'),
                'desc' => '',
                'hint_text' => 'Example: envato',
                'echo' => true,
                'field_params' => array(
                    'std' => esc_attr($facebook_page_url),
                    'id' => cs_allow_special_char($this->get_field_id('facebook_page_url')),
                    'classes' => 'upcoming',
                    'cust_id' => cs_allow_special_char($this->get_field_name('facebook_page_url')),
                    'cust_name' => cs_allow_special_char($this->get_field_name('facebook_page_url')),
                    'return' => true,
                    'required' => false
                ),
            );
            echo force_balance_tags($cs_html_fields->cs_text_field($cs_opt_array));
            ?>

            <?php
            if (!empty($consumer_key) && !empty($consumer_secret) && !empty($access_token) && !empty($access_token_secret)) {
                $notice = __('You\'ll need to set up the Twitter API Setting options before using it', 'jobhunt') . __('You can make your changes', 'jobhunt') . ' <a href="' . get_admin_url() . 'themes.php?page=cs_theme_options#tab-api-key-show">' . __('here', 'jobhunt') . '.</a>';
                echo esc_html($notice);
            }

            $cs_opt_array = array(
                'name' => __('Twitter Text', 'jobhunt'),
                'desc' => '',
                'hint_text' => '',
                'echo' => true,
                'field_params' => array(
                    'std' => esc_attr($twitter_text),
                    'id' => cs_allow_special_char($this->get_field_id('twitter_text')),
                    'classes' => 'upcoming',
                    'cust_id' => cs_allow_special_char($this->get_field_name('twitter_text')),
                    'cust_name' => cs_allow_special_char($this->get_field_name('twitter_text')),
                    'return' => true,
                    'required' => false
                ),
            );
            echo force_balance_tags($cs_html_fields->cs_text_field($cs_opt_array));

            $cs_opt_array = array(
                'name' => __('Twitter Username', 'jobhunt'),
                'desc' => '',
                'hint_text' => __('Example: @envato', 'jobhunt'),
                'echo' => true,
                'field_params' => array(
                    'std' => esc_attr($twitter_username),
                    'id' => cs_allow_special_char($this->get_field_id('twitter_username')),
                    'classes' => 'upcoming',
                    'cust_id' => cs_allow_special_char($this->get_field_name('twitter_username')),
                    'cust_name' => cs_allow_special_char($this->get_field_name('twitter_username')),
                    'return' => true,
                    'required' => false
                ),
            );
            echo force_balance_tags($cs_html_fields->cs_text_field($cs_opt_array));

            $cs_opt_array = array(
                'name' => __('Google+ Text', 'jobhunt'),
                'desc' => '',
                'hint_text' => '',
                'echo' => true,
                'field_params' => array(
                    'std' => esc_attr($googleplus_text),
                    'id' => cs_allow_special_char($this->get_field_id('googleplus_text')),
                    'classes' => 'upcoming',
                    'cust_id' => cs_allow_special_char($this->get_field_name('googleplus_text')),
                    'cust_name' => cs_allow_special_char($this->get_field_name('googleplus_text')),
                    'return' => true,
                    'required' => false
                ),
            );
            echo force_balance_tags($cs_html_fields->cs_text_field($cs_opt_array));



            $cs_opt_array = array(
                'name' => __('Google Page Id', 'jobhunt'),
                'desc' => '',
                'hint_text' => '',
                'echo' => true,
                'field_params' => array(
                    'std' => esc_attr($googleplus_id),
                    'id' => cs_allow_special_char($this->get_field_id('googleplus_id')),
                    'classes' => 'upcoming',
                    'cust_id' => cs_allow_special_char($this->get_field_name('googleplus_id')),
                    'cust_name' => cs_allow_special_char($this->get_field_name('googleplus_id')),
                    'return' => true,
                    'required' => false
                ),
            );
            echo force_balance_tags($cs_html_fields->cs_text_field($cs_opt_array));

            $cs_opt_array = array(
                'name' => __('Dribbble Text', 'jobhunt'),
                'desc' => '',
                'hint_text' => '',
                'echo' => true,
                'field_params' => array(
                    'std' => esc_attr($dribble_text),
                    'id' => cs_allow_special_char($this->get_field_id('dribble_text')),
                    'classes' => 'upcoming',
                    'cust_id' => cs_allow_special_char($this->get_field_name('dribble_text')),
                    'cust_name' => cs_allow_special_char($this->get_field_name('dribble_text')),
                    'return' => true,
                    'required' => false
                ),
            );
            echo force_balance_tags($cs_html_fields->cs_text_field($cs_opt_array));

            $cs_opt_array = array(
                'name' => __('Dribbble Text', 'jobhunt'),
                'desc' => '',
                'hint_text' => __('Example: http://dribbble.com/username', 'jobhunt'),
                'echo' => true,
                'field_params' => array(
                    'std' => esc_attr($dribble_id),
                    'id' => cs_allow_special_char($this->get_field_id('dribble_id')),
                    'classes' => 'upcoming',
                    'cust_id' => cs_allow_special_char($this->get_field_name('dribble_id')),
                    'cust_name' => cs_allow_special_char($this->get_field_name('dribble_id')),
                    'return' => true,
                    'required' => false
                ),
            );
            echo force_balance_tags($cs_html_fields->cs_text_field($cs_opt_array));
        }

        /*
         *
         * End Function how to create function instance 
         * Start Function how to create update function instance 
         */

        function update($new_instance, $old_instance) {
            $instance = $old_instance;
            $instance['title'] = $new_instance['title'];
            $instance['facebook_text'] = $new_instance['facebook_text'];
            $instance['facebook_page_url'] = $new_instance['facebook_page_url'];
            $instance['facebook_acess_token'] = $new_instance['facebook_acess_token'];
            $instance['twitter_text'] = $new_instance['twitter_text'];
            $instance['twitter_username'] = $new_instance['twitter_username'];
            $instance['googleplus_text'] = $new_instance['googleplus_text'];
            $instance['googleplus_id'] = $new_instance['googleplus_id'];
            $instance['youtube_text'] = $new_instance['youtube_text'];
            $instance['youtube_id'] = $new_instance['youtube_id'];
            $instance['vimeo_text'] = $new_instance['vimeo_text'];
            $instance['vimeo_id'] = $new_instance['vimeo_id'];
            $instance['dribble_text'] = $new_instance['dribble_text'];
            $instance['dribble_id'] = $new_instance['dribble_id'];
            return $instance;
        }

        /*
         *
         * End Function how to create update function instance 
         * Start Function how to create widget function instance 
         */

        function widget($args, $instance) {
            global $cs_plugin_options, $cs_theme_option, $facebook_page_url, $facebook_acess_token, $consumer_key, $consumer_secret;
            extract($args, EXTR_SKIP);
            $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title']);
            $facebook_page_url = $instance['facebook_page_url'];
            $facebook_acess_token = $instance['facebook_acess_token'];
            $facebook_text = $instance['facebook_text'];
            $twitter_username = $instance['twitter_username'];
            $twitter_text = $instance['twitter_text'];
            $googleplus_text = $instance['googleplus_text'];
            $googleplus_id = $instance['googleplus_id'];
            $youtube_text = $instance['youtube_text'];
            $youtube_id = $instance['youtube_id'];
            $vimeo_text = $instance['vimeo_text'];
            $vimeo_id = $instance['vimeo_id'];
            $dribble_text = $instance['dribble_text'];
            $dribble_id = $instance['dribble_id'];
            cs_enqueue_count_nos();
            /* for twitter */
            $consumer_key = $consumer_secret = $access_token = $access_token_secret = '';
            if (isset($cs_plugin_options['cs_consumer_key']) and $cs_plugin_options['cs_consumer_key'] <> '')
                $consumer_key = $cs_plugin_options['cs_consumer_key'];
            if (isset($cs_plugin_options['cs_consumer_secret']) and $cs_plugin_options['cs_consumer_secret'] <> '')
                $consumer_secret = $cs_plugin_options['cs_consumer_secret'];


            echo cs_allow_special_char($before_widget);
            if (!empty($title) && $title <> ' ') {
                echo cs_allow_special_char($before_title);
                echo cs_allow_special_char($title);
                echo cs_allow_special_char($after_title);
            }
            ?>
            <ul class="socialmedia-list">   
                <?php
                if (function_exists('curl_init') && !empty($facebook_page_url)) {
                    $facbook_count = cs_facebook_like_count($facebook_page_url);
                    ?>   
                    <li><a href="<?php echo cs_server_protocol(); ?>facebook.com/<?php echo esc_url($facebook_page_url); ?>" data-original-title="facebook"><i class="icon-facebook-square"></i><span>
                                <?php echo esc_html($facebook_text); ?></span><em>(<?php echo cs_facebook_like_count(); ?>)</em></a></li>           

                    <?php
                }
                   if (!empty($twitter_username) && !empty($consumer_key) && !empty($consumer_secret)) {
                    ?>
                    <li><a href="<?php echo cs_server_protocol(); ?>twitter.com/<?php echo esc_html($twitter_username); ?>" data-original-title="twitter"><i class="icon-twitter2"></i><span>
                                <?php echo esc_html($twitter_text); ?></span><em>(<?php echo getTwitterFollowers($twitter_username); ?>)</em></a></li>
                    <?php
                }
                if (function_exists('file_get_contents') && !empty($googleplus_id)) {
                    $gplus_count = cs_google_plus_count($googleplus_id);
                    ?> 
                    <li><a href="<?php echo cs_server_protocol(); ?>plus.google.com/<?php echo esc_html($googleplus_id); ?>/posts" data-original-title="google"><i class="icon-google-plus"></i><span>
                                <?php echo esc_html($googleplus_text); ?></span><em>(<?php echo esc_html($gplus_count); ?>)</em></a></li> <?php } ?>
                <?php
                if (isset($dribble_id) && !empty($dribble_id)) {
                    $dribble = cs_dribble_count($dribble_id);
                    ?> 
                    <li><a href="<?php echo esc_url($dribble_id); ?>" data-original-title="linkedin"><i class="icon-dribbble2"></i><span><?php echo esc_html($dribble_text); ?>
                            </span><em>(<?php echo esc_html($dribble); ?>)</em></a></li>
                <?php } ?>
            </ul>
            <?php
            echo cs_allow_special_char($after_widget);
        }

    }

}

/*
 *
 * End Function how to create widget function instance 
 * Start Function how to create facebook like counter function
 */
add_action('widgets_init', create_function('', 'return register_widget("cs_social_meida_followers_widget");'));
if (!function_exists('cs_facebook_like_count')) {

    function cs_facebook_like_count() {
        global $facebook_page_url, $facebook_acess_token;
        $fb_count = '';
        $transName = 'cs_facebook_count';
        $cacheTime = 60 * 60 * 2;
        $fb_count = get_transient($transName);
        $curl_url = cs_server_protocol() . 'graph.facebook.com/' . $facebook_page_url . '?fields=likes&access_token=' . $facebook_acess_token . '';
        $args = array(
            'decompress' => false,
        );
        $results = wp_remote_get($curl_url, $args);
        $html = '';

        if (isset($results) && array_key_exists('error', $results)) {
            $html .= $cs_facebook_message = 'Error - ' . $results['error']['message'];
        } else {
            $results = isset($results['body']) ? json_decode($results['body']): '';
            $html .= isset($results->likes) ? $results->likes : '';
        }
        return $html;
    }

}
// Twitter Count
/*
 *
 * End Function how to create facebook like counter function
 * Start Function how to create twitter followers function
 */
if (!function_exists('getTwitterFollowers')) {

    function getTwitterFollowers($screenName = '') {
        global $consumer_key, $consumer_secret;
        $consumerKey = $consumer_key;
        $consumerSecret = $consumer_secret;
        $token = get_option('cfTwitterToken');
        // get follower count from cache
        $numberOfFollowers = get_transient('csTwitterFollowers');
        // cache version does not exist or expired
        if (false === $numberOfFollowers) {
            // getting new auth bearer only if we don't have one
            if (!$token) {
                // preparing credentials
                $credentials = $consumerKey . ':' . $consumerSecret;
                $toSend = base64_encode($credentials);
                // http post arguments
                $args = array(
                    'method' => 'POST',
                    'decompress' => false,
                    'headers' => array(
                        'Authorization' => 'Basic ' . $toSend,
                    ),
                    'body' => array('grant_type' => 'client_credentials')
                );
                add_filter('https_ssl_verify', '__return_false');
                $response = wp_remote_get(cs_server_protocol() . 'api.twitter.com/oauth2/token', $args);
                $keys = json_decode(wp_remote_retrieve_body($response));
                if ($keys) {
                    $access_token = isset($keys->access_token) ? $keys->access_token : '';
                    update_option('cfTwitterToken', $access_token);
                    $token = $access_token;
                }
            }
            $args = array(
                'httpversion' => '1.1',
                'blocking' => true,
                'decompress' => false,
                'headers' => array(
                    'Authorization' => "Bearer $token"
                )
            );
            add_filter('https_ssl_verify', '__return_false');
            $api_url = cs_server_protocol() . "api.twitter.com/1.1/users/show.json?screen_name=$screenName";
            $response = wp_remote_get($api_url, $args);
            if (!is_wp_error($response)) {
                $followers = json_decode(wp_remote_retrieve_body($response));
                $numberOfFollowers = isset($followers->followers_count) ? $followers->followers_count : '';
            } else {
                $numberOfFollowers = get_option('cfNumberOfFollowers');
            }
            set_transient('cfTwitterFollowers', $numberOfFollowers, 1 * 60 * 60);
            update_option('cfNumberOfFollowers', $numberOfFollowers);
        }
        return $numberOfFollowers;
    }

}

/*
 *
 * End Function how to create twitter followers function
 * Start Function how to create google plus count function
 */
if (!function_exists('cs_google_plus_count')) {

    function cs_google_plus_count($googleplus_id) {
        $google_count = get_transient('gplus_count');
        if (isset($googleplus_id) && $googleplus_id <> '') {
            $count = 0;
            $data = file_get_contents(cs_server_protocol() . 'plus.google.com/' . $googleplus_id . '/posts');

            if (is_wp_error($data)) {
                return $google_count;
            } else {
                if (preg_match('/>([0-9,]+) people</i', $data, $matches)) {
                    $results = str_replace(',', '', $matches[1]);
                }
                if (isset($results) && !empty($results)) {
                    $google_count = $results;
                    set_transient('gplus_count', $results, 60 * 60 * 1);
                }
                if ($googleplus_id == '+envato') {
                    $google_count = '9526';
                    return $google_count;
                } else {
                    return $google_count * 60 * 60 * 1;
                }
            }
        }
    }

}

/*
 *
 * End Function how to create google plus count function
 * Start Function how to create youtube_subscriptions function
 */
if (!function_exists('cs_youtube_subscriptions')) {

    function cs_youtube_subscriptions($channel_id) {
        $youtube_link = parse_url($channel_id);
        $subscriptions = 0;
        if (isset($youtube_link['host']) && $youtube_link['host'] <> '') {
            if ($youtube_link['host'] == 'www.youtube.com' || $youtube_link['host'] == 'youtube.com') {
                $subscriptions = get_transient('youtube_count');
                try {
                    if (strpos(strtolower($channel_id), "channel") === false)
                        $youtube_name = str_replace('/user/', '', $youtube_link['path']);
                    else
                        $youtube_name = str_replace('/channel/', '', $youtube_link['path']);

                    $json = file_get_contents(cs_server_protocol() . "gdata.youtube.com/feeds/api/users/" . $youtube_name . "?alt=json");
                    $data = json_decode($json, true);
                    $subscriptions = $data['entry']['yt$statistics']['subscriberCount'];
                    if (!empty($subscriptions)) {
                        set_transient('youtube_count', $subscriptions, 1200);
                    }
                } catch (Exception $e) {
                    $subscriptions = get_transient('youtube_count');
                }
                return $subscriptions;
            }
        }
    }

}
/*
 *
 * End Function how to create youtube_subscriptions function
 * Start Function how to create Vimeo Subscribers function
 */
if (!function_exists('cs_vimeo_count')) {

    function cs_vimeo_count($page_link) {
        $vimeo_link = parse_url($page_link);
        $vimeo = 0;
        if (isset($vimeo_link['host']) && $vimeo_link['host'] <> '') {
            if ($vimeo_link['host'] == 'www.vimeo.com' || $vimeo_link['host'] == 'vimeo.com') {
                try {
                    $page_name = str_replace('/channels/', '', $vimeo_link['path']);
                    $data = json_decode(file_get_contents(cs_server_protocol() . 'vimeo.com/api/v2/channel/' . $page_name . '/info.json'));
                    $vimeo = $data->total_subscribers;
                    if (!empty($vimeo) && is_int($vimeo)) {
                        set_transient('vimeo_count', $vimeo, 1200);
                    }
                } catch (Exception $e) {
                    $vimeo = get_transient('vimeo_count');
                }
                return $vimeo;
            }
        }
    }

}
/*
 *
 * End Function how to create Vimeo Subscribers function
 * Start Function how to create dribble_count function
 */
if (!function_exists('cs_dribble_count')) {

    function cs_dribble_count($profile_link) {
        $dribbble_link = @parse_url($profile_link);
        $dribbble = 0;
        if (isset($dribbble_link['host']) && $dribbble_link['host'] <> '') {
            if ($dribbble_link['host'] == 'www.dribbble.com' || $dribbble_link['host'] == 'dribbble.com') {
                try {
                    $page_name = str_replace('/', '', $dribbble_link['path']);
                    $data = @json_decode(file_get_contents(cs_server_protocol() . 'api.dribbble.com/' . $page_name));

                    $dribbble = isset($data->followers_count) ? $data->followers_count : '';
                    if (!empty($dribbble)) {
                        set_transient('dribbble_count', $dribbble, 1200);
                    }
                } catch (Exception $e) {
                    $dribbble = get_transient('dribbble_count');
                }
                if (empty($dribbble)) {
                    if ($profile_link == cs_server_protocol() . 'dribbble.com/envato') {
                        $dribbble = '1200';
                    } else {
                        $dribbble = '781';
                    }
                }
                return $dribbble;
            }
        }
    }

}