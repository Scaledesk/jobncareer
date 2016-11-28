<?php
/**
 * Plugin Name: JobHunt Email Templates
 * Plugin URI: http://themeforest.net/user/Chimpstudio/
 * Description: JobHunt Email Templates Add on
 * Version: 1.2
 * Author: ChimpStudio
 * Author URI: http://themeforest.net/user/Chimpstudio/
 * @package Job Hunt
 *
 * @package	Directory
 */
// Direct access not allowed.
if (!defined('ABSPATH')) {
    exit;
}

// Define constants
define('JOBHUNT_EMAIL_TEMPLATES_VERSION', '1.0');
define('JOBHUNT_EMAIL_TEMPLATES_FILE', __FILE__);
define('JOBHUNT_EMAIL_TEMPLATES_CORE_DIR', WP_PLUGIN_DIR . '/jobhunt-email-templates');
define('JOBHUNT_EMAIL_TEMPLATES_INCLUDES_DIR', JOBHUNT_EMAIL_TEMPLATES_CORE_DIR . '/includes');
define('JOBHUNT_EMAIL_TEMPLATES_LANGUAGES_DIR', JOBHUNT_EMAIL_TEMPLATES_CORE_DIR . '/languages');
define('JOBHUNT_EMAIL_TEMPLATES_PLUGIN_URL', WP_PLUGIN_URL . '/jobhunt-email-templates');

require_once( JOBHUNT_EMAIL_TEMPLATES_INCLUDES_DIR . '/class-jobhunt-email-templates.php');

if( !function_exists('jobhunt_check_if_template_exists')) {
	
	function jobhunt_check_if_template_exists( $slug, $type ) {
		global $wpdb;
                $post	= $wpdb->get_row("SELECT ID FROM ".$wpdb->prefix."posts WHERE post_name = '" . $slug . "' && post_type = '" . $type . "'", 'ARRAY_A');
                
        if(isset($post) && isset($post['ID'])) {
			return $post['ID'];
		} else {
			return false;
		}
	}
}