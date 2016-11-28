<?php
global $cs_settings_init;

require_once ABSPATH . '/wp-admin/includes/file.php';

// Home Demo
$cs_demo = cs_get_settings_demo('demo.json');

$cs_settings_init = array(
	"plugin_options" => $cs_demo,
);