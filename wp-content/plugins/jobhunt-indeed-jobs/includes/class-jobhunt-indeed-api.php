<?php
// Direct access not allowed.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Job_Hunt_Indeed_API
 */
class Job_Hunt_Indeed_API {

    /**
     * Get jobs from the indeed API
     * @return array()
    */
    public static function get_jobs_from_indeed($args) {
        
        // default indeed api arguments
        $default_args = array(
            'v' => 2,
            'format' => 'json',
            'radius' => 25,
            'start' => 0,
            'latlong' => 1
        );
        // getting user ip
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        $default_args['userip'] = $ip;
        // getting user agent
        $default_args['useragent'] = $_SERVER['HTTP_USER_AGENT'];
        
        $endpoint = "http://api.indeed.com/ads/apisearch?";
        
        $args = wp_parse_args( $args, $default_args );
        $results = array();
        $result = wp_remote_get( $endpoint . http_build_query($args, '', '&') );
        if (!is_wp_error($result) && !empty($result['body'])) {
            $results = (array) json_decode($result['body']);
        }
        return isset($results['results']) ? $results['results'] : $results;
    }
    
    /**
     * indeed api countries list
    */
    public static function indeed_api_countries() {
        $country = array();
        $country['us'] = __('United States', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        $country['ar'] = __('Argentina', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        $country['au'] = __('Australia', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        $country['at'] = __('Austria', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        $country['bh'] = __('Bahrain', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        $country['be'] = __('Belgium', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        $country['br'] = __('Brazil', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        $country['ca'] = __('Canada', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        $country['cl'] = __('Chile', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        $country['cn'] = __('China', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        $country['co'] = __('Colombia', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        $country['cz'] = __('Czech Republic', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        $country['dk'] = __('Denmark', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        $country['fi'] = __('Finland', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        $country['fr'] = __('France', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        $country['de'] = __('Germany', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        $country['gr'] = __('Greece', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        $country['hk'] = __('Hong Kong', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        $country['hu'] = __('Hungary', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        $country['in'] = __('India', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        $country['id'] = __('Indonesia', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        $country['ie'] = __('Ireland', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        $country['il'] = __('Israel', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        $country['it'] = __('Italy', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        $country['jp'] = __('Japan', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        $country['kr'] = __('Korea', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        $country['kw'] = __('Kuwait', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        $country['lu'] = __('Luxembourg', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        $country['my'] = __('Malaysia', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        $country['mx'] = __('Mexico', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        $country['nl'] = __('Netherlands', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        $country['nz'] = __('New Zealand', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        $country['no'] = __('Norway', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        $country['om'] = __('Oman', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        $country['pk'] = __('Pakistan', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        $country['pe'] = __('Peru', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        $country['ph'] = __('Philippines', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        $country['pl'] = __('Poland', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        $country['pt'] = __('Portugal', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        $country['qa'] = __('Qatar', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        $country['ro'] = __('Romania', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        $country['ru'] = __('Russia', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        $country['sa'] = __('Saudi Arabia', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        $country['sg'] = __('Singapore', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        $country['za'] = __('South Africa', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        $country['es'] = __('Spain', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        $country['se'] = __('Sweden', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        $country['ch'] = __('Switzerland', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        $country['tw'] = __('Taiwan', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        $country['tr'] = __('Turkey', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        $country['ae'] = __('United Arab Emirates', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        $country['gb'] = __('United Kingdom', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        $country['ve'] = __('Venezuela', JOBHUNT_INDEED_JOBS_PLUGIN_DOMAIN);
        return $country;
    }

}