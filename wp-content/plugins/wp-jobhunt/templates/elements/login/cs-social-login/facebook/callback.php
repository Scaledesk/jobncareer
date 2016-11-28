<?php
global $cs_plugin_options;

require_once 'facebook.php';
$cs_plugin_options = get_option('cs_plugin_options');
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
                        'social_login_signature': '<?php echo esc_html($signature) ?>',
                        'social_login_access_token': '<?php echo esc_html($access_token) ?>'});

                    window.close();
                }
            </script>
        </head>
        <body onLoad="init();"></body>
    </html>
    <?php
} else {
    $redirect_uri = urlencode(plugin_dir_url(__FILE__) . 'callback.php');
    wp_redirect('https://graph.facebook.com/oauth/authorize?client_id=' . $client_id . '&redirect_uri=' . $redirect_uri . '&scope=email');
}