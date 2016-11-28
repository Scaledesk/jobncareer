<?php
//load_plugin_textdomain( 'jobhunt', false, basename( dirname( __FILE__ ) ) . '/languages' );
//echo plugin_dir_path( __FILE__ );
if (!defined('CS_CSV_DELIMITER'))
    define('CS_CSV_DELIMITER', ',');

/**
 * Main plugin class
 *
 * @since 0.1
 * */
class cs_user_import {

    private static $log_dir_path = '';
    private static $log_dir_url = '';

    /**
     * Initialization
     *
     * @since 0.1
     * */
    public function __construct() {
        add_action('admin_menu', array(&$this, 'cs_user_import_page'));
        add_action('init', array(&$this, 'cs_import_user_process'));
        add_action('init', array(&$this, 'cs_export_user_process'));
        $upload_dir = wp_upload_dir();
        self::$log_dir_path = trailingslashit($upload_dir['basedir']);
        self::$log_dir_url = trailingslashit($upload_dir['baseurl']);
    }

    /**
     * Add administration menus
     *
     * @since 0.1
     * */
    public function cs_user_import_page() {
        add_submenu_page('edit.php?post_type=jobs', __('Import / Export Users', 'jobhunt'), __('Import / Export Users', 'jobhunt'), 'manage_options', 'user-import', array(&$this, 'cs_import_user_form'));
    }

    /**
     * Import Users from cs_framework Process
     *
     * @since 0.1
     * */
    public function cs_import_user_demodata($password_nag = false, $new_user_notification = false, $users_update = false, $filename = "") {

        $first_str_filename = 'user_data';      // zip file name without extension
        //echo $filename;die();
        // if another file name is provided then first fetch content from remote resource
        if ($filename != "") {
            global $wp_filesystem;
            $users_data = $wp_filesystem->get_contents($filename);
            $filename = plugin_dir_path(__FILE__) . 'demo/' . $first_str_filename . '_' . time() . '.zip';
            $wp_filesystem->put_contents($filename, $users_data);
        } else {
            $filename = plugin_dir_path(__FILE__) . 'demo/' . $first_str_filename . '.zip';
        }
        //echo $filename;die();


        add_filter('upload_dir', 'cs_user_images_custom_directory');
        $cs_upload_dir = wp_upload_dir();


//        $zip = new ZipArchive;
//        $zip->open($filename, ZipArchive::CREATE);
//        $zip->extractTo($cs_upload_dir['path'] . '/' . $first_str_filename . '/');
//        $zip->close();

        WP_Filesystem();
        $unzipfile = unzip_file($filename, $cs_upload_dir['path'] . '/' . $first_str_filename . '/');
        // delete zip archive
        unlink($filename);

        $withoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $filename);
        $csv_filename = $cs_upload_dir['path'] . '/' . $first_str_filename . '/' . $first_str_filename . '.csv';
        // Setup settings variables
        $password_nag = isset($_POST['password_nag']) ? $_POST['password_nag'] : false;
        $users_update = isset($_POST['users_update']) ? $_POST['users_update'] : false;
        $new_user_notification = isset($_POST['new_user_notification']) ? $_POST['new_user_notification'] : false;
        $results = '';

        if (file_exists($csv_filename)) {
            $results = self::import_csv($csv_filename, array(
                        'password_nag' => $password_nag,
                        'new_user_notification' => $new_user_notification,
                        'users_update' => $users_update
            ));
        }

        $images_directory = $cs_upload_dir['path'] . '/' . $first_str_filename;
        if (is_dir($images_directory)) {

            if ($dh = opendir($images_directory)) {

                while (($file = readdir($dh)) !== false) {
                    $image_filename_url = $cs_upload_dir['path'] . '/' . $first_str_filename . '/' . $file;
                    $newfile = $cs_upload_dir['path'] . '/' . $file;
                    if (is_file($image_filename_url)) {
                        cs_import_user_profile_images($image_filename_url, $file);
                    }
                }
                closedir($dh);
                // move all image
            } if ($dh = opendir($images_directory)) {
                while (($file = readdir($dh)) !== false) {
                    $image_filename_url = $cs_upload_dir['path'] . '/' . $first_str_filename . '/' . $file;
                    $newfile = $cs_upload_dir['path'] . '/' . $file;
                    if (is_file($image_filename_url)) {
                        if (copy($image_filename_url, $newfile)) {
                            unlink($image_filename_url);
                        }
                    }
                }
                closedir($dh);
                rmdir($images_directory);
            }
        }

        // Set everything back to normal.
        remove_filter('upload_dir', 'cs_user_images_custom_directory');
        // No users imported?
        // return $results;
    }

    /**
     * Import Users Process
     *
     * @since 0.1
     * */
    public function cs_import_user_process() {
        if (isset($_POST['_wpnonce-cs-import-users-page'])) {
            check_admin_referer('cs-import-users-page', '_wpnonce-cs-import-users-page');
            if (isset($_POST['btn-import-users'])) {
                if (isset($_FILES['users_csv']['tmp_name'])) {
                    $temp = explode(".", $_FILES["users_csv"]["name"]);
                    $first_str_filename = current($temp); // $mode = 'foot';
                    $allowedExts = array("zip", "rar");
                    $extension = end($temp);

                    if (in_array($extension, $allowedExts)) {
                        add_filter('upload_dir', 'cs_user_images_custom_directory');
                        $cs_upload_dir = wp_upload_dir();
                        $filename = $_FILES['users_csv']['tmp_name'];
//                        $zip = new ZipArchive;
//                        $zip->open($filename, ZipArchive::CREATE);
//                        $zip->extractTo($cs_upload_dir['path'] . '/' . $first_str_filename . '/');
//                        $zip->close();


                        WP_Filesystem();
                        $unzipfile = unzip_file($filename, $cs_upload_dir['path'] . '/' . $first_str_filename . '/');

                        $withoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $filename);
                        $csv_filename = $cs_upload_dir['path'] . '/' . $first_str_filename . '/' . $first_str_filename . '.csv';
                        // Setup settings variables
                        $password_nag = isset($_POST['password_nag']) ? $_POST['password_nag'] : false;
                        $users_update = isset($_POST['users_update']) ? $_POST['users_update'] : false;
                        $new_user_notification = isset($_POST['new_user_notification']) ? $_POST['new_user_notification'] : false;
                        $results = '';

                        if (file_exists($csv_filename)) {
                            $results = self::import_csv($csv_filename, array(
                                        'password_nag' => $password_nag,
                                        'new_user_notification' => $new_user_notification,
                                        'users_update' => $users_update
                            ));
                        }
                        $images_directory = $cs_upload_dir['path'] . '/' . $first_str_filename;
                        if (is_dir($images_directory)) {

                            if ($dh = opendir($images_directory)) {

                                while (($file = readdir($dh)) !== false) {
                                    $image_filename_url = $cs_upload_dir['path'] . '/' . $first_str_filename . '/' . $file;
                                    $newfile = $cs_upload_dir['path'] . '/' . $file;
                                    if (is_file($image_filename_url)) {
                                        cs_import_user_profile_images($image_filename_url, $file);
                                    }
                                }
                                closedir($dh);
                                // move all image
                            } if ($dh = opendir($images_directory)) {
                                while (($file = readdir($dh)) !== false) {
                                    $image_filename_url = $cs_upload_dir['path'] . '/' . $first_str_filename . '/' . $file;
                                    $newfile = $cs_upload_dir['path'] . '/' . $file;
                                    if (is_file($image_filename_url)) {
                                        if (copy($image_filename_url, $newfile)) {
                                            unlink($image_filename_url);
                                        }
                                    }
                                }
                                closedir($dh);
                                rmdir($images_directory);
                            }
                        }
                        // Set everything back to normal.
                        remove_filter('upload_dir', 'cs_user_images_custom_directory');
                        // No users imported?
                        if (!isset($results['user_ids']))
                            wp_redirect(add_query_arg('import', 'fail', wp_get_referer()));

                        // Some users imported?
                        elseif ($results['errors'])
                            wp_redirect(add_query_arg('import', 'errors', wp_get_referer()));

                        // All users imported? :D
                        else
                            wp_redirect(add_query_arg('import', 'success', wp_get_referer()));

                        exit;
                    }// end zip file check
                    else {
                        //echo "else";exit;
                        wp_redirect(add_query_arg('import', 'filetype_error', wp_get_referer()));
                        exit;
                    }
                }

                wp_redirect(add_query_arg('import', 'file', wp_get_referer()));
                exit;
            }
        }
    }

    /**
     * Export Users Process
     *
     * @since 0.1
     * */
    public function cs_export_user_process() {
        global $wpdb;
        if (isset($_POST['_wpnonce-cs-export-users-page'])) {
            check_admin_referer('cs-export-users-page', '_wpnonce-cs-export-users-page');
            if (isset($_POST['btn-export-users'])) {
                $unique_filename = 'user_data_' . date('Ymd_His');
                add_filter('upload_dir', 'cs_user_images_custom_directory');
                $cs_upload_dir = wp_upload_dir();

                $existing_file = $cs_upload_dir['path'] . '/';
                $existing_file_url = $cs_upload_dir['url'] . '/';

                $csv_file_name = $unique_filename . '.csv';
                $zip_file_name = $unique_filename . '.zip';

                $user_default_fields = array(
                    'user_login', 'user_email', 'user_pass', 'first_name', 'last_name', 'display_name', 'role'
                );
                ob_clean();
                $field = '';
                $getField = '';
                $__user = $wpdb->prefix . 'users';
                $__usermeta = $wpdb->prefix . 'usermeta';
                if ($__user) {
                    $result = $wpdb->get_results("SELECT * FROM $__user");
                    $meta_keys = $wpdb->get_results("SELECT distinct(meta_key) FROM $__usermeta");
                    foreach ($user_default_fields as $single_field) {
                        $getField .= $single_field . ',';
                    }
                    // meta fields
                    $field_count = 0;
                    foreach ($meta_keys as $meta_single_field) {
                        if (isset($meta_single_field->meta_key) && $meta_single_field->meta_key != '') {
                            $getField .= $meta_single_field->meta_key . ',';
                            $field_count++;
                        }
                    }

                    $sub = substr_replace($getField, '', -1);
                    $fields = $sub; # GET FIELDS NAME

                    $each_field = explode(',', $sub);
                    // create zip file
                    $zip = new ZipArchive;
                    $zip->open($cs_upload_dir['path'] . '/' . $zip_file_name, ZipArchive::CREATE);

                    //WP_Filesystem();
                    //$unzipfile = unzip_file($filename, $cs_upload_dir['path'] . '/' . $zip_file_name);

//                    echo "<pre>";
//                    print_r($unzipfile);
//                    echo "</pre>";
//                    exit;
                    foreach ($result as $row) {
                        $fields .= "\n"; # FORCE NEW LINE IF LOOP COMPLETE
                        if (isset($row->user_login)) {
                            $value = str_replace(array("\n", "\n\r", "", "\r"), "\t", $row->user_login); # REPLACE NEW LINE WITH TAB
                            $value = str_getcsv($value, ",", "\"", "\\"); # SEQUENCING DATA IN CSV FORMAT, REQUIRED PHP >= 5.3.0
                            $fields .= $value[0] . ','; # SEPARATING FIELDS WITH COMMA
                        } else {
                            $fields .= ','; # SEPARATING FIELDS WITH COMMA
                        }
                        if (isset($row->user_email)) {
                            $value = str_replace(array("\n", "\n\r", "", "\r"), "\t", $row->user_email); # REPLACE NEW LINE WITH TAB
                            $value = str_getcsv($value, ",", "\"", "\\"); # SEQUENCING DATA IN CSV FORMAT, REQUIRED PHP >= 5.3.0
                            $fields .= $value[0] . ','; # SEPARATING FIELDS WITH COMMA
                        } else {
                            $fields .= ','; # SEPARATING FIELDS WITH COMMA
                        }
                        if (isset($row->user_pass)) {
                            $value = str_replace(array("\n", "\n\r", "", "\r"), "\t", $row->user_pass); # REPLACE NEW LINE WITH TAB
                            $value = str_getcsv($value, ",", "\"", "\\"); # SEQUENCING DATA IN CSV FORMAT, REQUIRED PHP >= 5.3.0
                            $fields .= $value[0] . ','; # SEPARATING FIELDS WITH COMMA
                        } else {
                            $fields .= ','; # SEPARATING FIELDS WITH COMMA
                        }
                        if (isset($row->first_name)) {
                            $value = str_replace(array("\n", "\n\r", "", "\r"), "\t", $row->first_name); # REPLACE NEW LINE WITH TAB
                            $value = str_getcsv($value, ",", "\"", "\\"); # SEQUENCING DATA IN CSV FORMAT, REQUIRED PHP >= 5.3.0
                            $fields .= $value[0] . ','; # SEPARATING FIELDS WITH COMMA
                        } else {
                            $fields .= ','; # SEPARATING FIELDS WITH COMMA
                        }
                        if (isset($row->last_name)) {
                            $value = str_replace(array("\n", "\n\r", "", "\r"), "\t", $row->last_name); # REPLACE NEW LINE WITH TAB
                            $value = str_getcsv($value, ",", "\"", "\\"); # SEQUENCING DATA IN CSV FORMAT, REQUIRED PHP >= 5.3.0
                            $fields .= $value[0] . ','; # SEPARATING FIELDS WITH COMMA
                        } else {
                            $fields .= ','; # SEPARATING FIELDS WITH COMMA
                        }
                        if (isset($row->display_name)) {
                            $value = str_replace(array("\n", "\n\r", "", "\r"), "\t", $row->display_name); # REPLACE NEW LINE WITH TAB
                            $value = str_getcsv($value, ",", "\"", "\\"); # SEQUENCING DATA IN CSV FORMAT, REQUIRED PHP >= 5.3.0
                            $fields .= $value[0] . ','; # SEPARATING FIELDS WITH COMMA
                        } else {
                            $fields .= ','; # SEPARATING FIELDS WITH COMMA
                        }
                        // user role 
                        $user_info = get_userdata($row->ID);
                        if (isset($user_info->roles)) {

                            $value = str_replace(array("\n", "\n\r", "", "\r"), "\t", implode(',', $user_info->roles)); # REPLACE NEW LINE WITH TAB
                            $value = str_getcsv($value, ",", "\"", "\\"); # SEQUENCING DATA IN CSV FORMAT, REQUIRED PHP >= 5.3.0
                            $fields .= $value[0] . ','; # SEPARATING FIELDS WITH COMMA
                        } else {
                            $fields .= ','; # SEPARATING FIELDS WITH COMMA
                        }

                        // meta fields
                        $field_count = 0;
                        foreach ($meta_keys as $meta_single_field) {
                            $field_meta_key = $meta_single_field->meta_key;
                            if (isset($field_meta_key) && $field_meta_key != '') {
                                $field_meta_key_value = get_user_meta($row->ID, $field_meta_key) ? get_user_meta($row->ID, $field_meta_key, true) : ' ';
                                $field_meta_key_value = str_replace(',', "\rC", $field_meta_key_value);
                                $value = str_replace(array("\n", "\n\r", "", "\r"), "\t", $field_meta_key_value); # REPLACE NEW LINE WITH TAB

                                if (is_array($value)) {
                                    $value = serialize($value);
                                }
                                $value = str_getcsv($value, ",", "\"", "\\"); # SEQUENCING DATA IN CSV FORMAT, REQUIRED PHP >= 5.3.0
                                $fields .= $value[0] . ','; # SEPARATING FIELDS WITH COMMA
                                $field_count++;

                                // move file into user media directory
                                if (($field_meta_key == 'cover_user_img' && $field_meta_key_value != '') || ( $field_meta_key == 'user_img' && $field_meta_key_value != '')) {
                                    $orignal_image_name = cs_get_orignal_image_nam($field_meta_key_value, 'large');
                                    $existing_file_name = $existing_file . $orignal_image_name;
                                    $existing_file_name_url = $existing_file_url . $orignal_image_name;

                                    if (cs_image_exist($existing_file_name_url)) {
                                        // add image file into zip file
                                        $zip->addFile($existing_file_name, $orignal_image_name);
                                    }
                                }
                            }
                        }
                    }
                    $filepath = $cs_upload_dir['path'] . "/";
                    global $wp_filesystem;
                    $filename = trailingslashit($filepath) . $csv_file_name;
                    if ($wp_filesystem->put_contents($filename, $fields)) {


                        // add csv file into zip file
                        $zip->addFile($filename, $csv_file_name);
                        $zip->close();
                        $filename = $zip_file_name;
                        // http headers for zip downloads
                        header("Pragma: public");
                        header("Expires: 0");
                        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                        header("Cache-Control: public");
                        header("Content-Description: File Transfer");
                        header("Content-type: application/octet-stream");
                        header("Content-Disposition: attachment; filename=\"" . $filename . "\"");
                        header("Content-Transfer-Encoding: binary");
                        header("Content-Length: " . filesize($filepath . $filename));
                        ob_end_flush();
                        @readfile($filepath . $filename);
                    } else {
                        echo __('There is an error in your users data import, please try later', 'jobhunt');
                    }
                }
                // Set everything back to normal.
                remove_filter('upload_dir', 'cs_user_images_custom_directory');
            }
        }
    }

    /**
     * Content of the settings page
     *
     * @since 0.1
     * */
    public function cs_import_user_form() {
        if (!current_user_can('create_users'))
            wp_die(__('You do not have sufficient permissions to access this page.', 'jobhunt'));
        ?>

        <div class="wrap">
            <h2><?php _e('Import / Export Users', 'jobhunt'); ?></h2>
            <?php
            $error_log_file = self::$log_dir_path . 'is_iu_errors.log';
            $error_log_url = self::$log_dir_url . 'is_iu_errors.log';

            if (!file_exists($error_log_file)) {
                if (!@fopen($error_log_file, 'x'))
                    echo '<div class="updated"><p><strong>' . sprintf(__('Notice: please make the directory %s writable so that you can see the error log.', 'jobhunt'), self::$log_dir_path) . '</strong></p></div>';
            }

            if (isset($_GET['import'])) {
                $error_log_msg = '';
                if (file_exists($error_log_file))
                    $error_log_msg = sprintf(__(', please <a href="%s">check the error log</a>', 'jobhunt'), $error_log_url);

                switch ($_GET['import']) {
                    case 'file':
                        echo '<div class="error"><p><strong>' . __('Error during file upload.', 'jobhunt') . '</strong></p></div>';
                        break;
                    case 'data':
                        echo '<div class="error"><p><strong>' . __('Cannot extract data from uploaded file or no file was uploaded.', 'jobhunt') . '</strong></p></div>';
                        break;
                    case 'fail':
                        echo '<div class="error"><p><strong>' . sprintf(__('No user was successfully imported%s.', 'jobhunt'), $error_log_msg) . '</strong></p></div>';
                        break;
                    case 'errors':
                        echo '<div class="error"><p><strong>' . sprintf(__('Some users were successfully imported but some were not%s.', 'jobhunt'), $error_log_msg) . '</strong></p></div>';
                        break;
                    case 'success':
                        echo '<div class="updated"><p><strong>' . __('Users import was successful.', 'jobhunt') . '</strong></p></div>';
                        break;
                    case 'filetype_error':
                        echo '<div class="error"><p><strong>' . __('You have selected invalid file type, Please try again.', 'jobhunt') . '</strong></p></div>';
                        break;
                    default:
                        break;
                }
            }
            if (isset($_GET['export'])) {
                $error_log_msg = '';
                if (file_exists($error_log_file))
                    $error_log_msg = sprintf(__(', please <a href="%s">check the error log</a>', 'jobhunt'), $error_log_url);

                switch ($_GET['export']) {
                    case 'success':
                        echo '<div class="updated"><p><strong>' . __('Users has been done export successful.', 'jobhunt') . '</strong></p></div>';
                        break;
                    default:
                        break;
                }
            }
            ?>
            <h3><?php _e("Import User Data", "jobhunt"); ?></h3>
            <form method="post" action="" enctype="multipart/form-data">
                <?php wp_nonce_field('cs-import-users-page', '_wpnonce-cs-import-users-page'); ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><label for="users_csv"><?php _e('Zip file', 'jobhunt'); ?></label></th>
                        <td>
                            <input type="file" id="users_csv" name="users_csv" value="" class="all-options" /><br />
                            <span class="description"><?php echo sprintf(__('You may want to see <a href="%s">the demo file</a>.', 'jobhunt'), plugin_dir_url(__FILE__) . 'demo/user_data.zip'); ?></span>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Notification', 'jobhunt'); ?></th>
                        <td><fieldset>
                                <legend class="screen-reader-text"><span><?php _e('Notification', 'jobhunt'); ?></span></legend>
                                <label for="new_user_notification">
                                    <input id="new_user_notification" name="new_user_notification" type="checkbox" value="1" />
                                    <?php _e('Send to new users', 'jobhunt') ?>
                                </label>
                            </fieldset></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Password nag', 'jobhunt'); ?></th>
                        <td><fieldset>
                                <legend class="screen-reader-text"><span><?php _e('Password nag', 'jobhunt'); ?></span></legend>
                                <label for="password_nag">
                                    <input id="password_nag" name="password_nag" type="checkbox" value="1" />
                                    <?php _e('Show password nag on new users signon', 'jobhunt') ?>
                                </label>
                            </fieldset></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Users update', 'jobhunt'); ?></th>
                        <td><fieldset>
                                <legend class="screen-reader-text"><span><?php _e('Users update', 'jobhunt'); ?></span></legend>
                                <label for="users_update">
                                    <input id="users_update" name="users_update" type="checkbox" value="1" />
                                    <?php _e('Update user when a username or email exists', 'jobhunt'); ?>
                                </label>
                            </fieldset></td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">&nbsp;</th>
                        <td>
                            <p class="submit">
                                <input type="submit" name="btn-import-users" class="button-primary" value="<?php _e('Import Users', 'jobhunt'); ?>" />
                            </p>
                        </td>
                    </tr>
                </table>

            </form>
            <h3><?php _e("Export All Users", "jobhunt"); ?></h3>
            <form method="post" action="" enctype="multipart/form-data">
                <?php wp_nonce_field('cs-export-users-page', '_wpnonce-cs-export-users-page'); ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><label for="users_csv">&nbsp;</label></th>
                        <td>
                            <input type="submit" name="btn-export-users" class="button-primary" value="<?php _e('Export Users', 'jobhunt'); ?>" />
                        </td>
                    </tr>

                </table>
            </form>   
        </div>
        <?php
    }

    /**
     * Import a csv file
     *
     * @since 0.5
     */
    public static function import_csv($filename, $args) {
        $errors = $user_ids = array();

        $defaults = array(
            'password_nag' => false,
            'new_user_notification' => false,
            'users_update' => false
        );
        extract(wp_parse_args($args, $defaults));

        // User data fields list used to differentiate with user meta
        $userdata_fields = array(
            'ID', 'user_login', 'user_pass',
            'user_email', 'user_url', 'user_nicename',
            'display_name', 'user_registered', 'first_name',
            'last_name', 'nickname', 'description',
            'rich_editing', 'comment_shortcuts', 'admin_color',
            'use_ssl', 'show_admin_bar_front', 'show_admin_bar_admin',
            'role'
        );

        require_once ( plugin_dir_path(__FILE__) . 'cs_class.php' );

        // Loop through the file lines
        $file_handle = fopen($filename, 'r');
        $csv_reader = new cs_csv_reader($file_handle, CS_CSV_DELIMITER, "\xEF\xBB\xBF"); // Skip any UTF-8 byte order mark.

        $first = true;
        $rkey = 0;
        while (( $line = $csv_reader->get_row() ) !== NULL) {

            // If the first line is empty, abort
            // If another line is empty, just skip it
            if (empty($line)) {
                if ($first)
                    break;
                else
                    continue;
            }

            // If we are on the first line, the columns are the headers
            if ($first) {
                $headers = $line;
                $first = false;
                continue;
            }

            // Separate user data from meta
            $userdata = $usermeta = array();
            foreach ($line as $ckey => $column) {
                $column_name = isset($headers[$ckey]) ? $headers[$ckey] : '';
                $column = trim($column);

                if (in_array($column_name, $userdata_fields)) {
                    $userdata[$column_name] = $column;
                } else {
                    $usermeta[$column_name] = $column;
                }
            }

            // A plugin may need to filter the data and meta
            $userdata = apply_filters('is_iu_import_userdata', $userdata, $usermeta);
            $usermeta = apply_filters('is_iu_import_usermeta', $usermeta, $userdata);

            // If no user data, bailout!
            if (empty($userdata))
                continue;

            // Something to be done before importing one user?
            do_action('is_iu_pre_user_import', $userdata, $usermeta);

            $user = $user_id = false;

            if (isset($userdata['ID']))
                $user = get_user_by('ID', $userdata['ID']);

            if (!$user && $users_update) {
                if (isset($userdata['user_login']))
                    $user = get_user_by('login', $userdata['user_login']);

                if (!$user && isset($userdata['user_email']))
                    $user = get_user_by('email', $userdata['user_email']);
            }

            $update = false;
            if ($user) {
                $userdata['ID'] = $user->ID;
                $update = true;
            }

            // If creating a new user and no password was set, let auto-generate one!
            if (!$update && empty($userdata['user_pass']))
                $userdata['user_pass'] = wp_generate_password(12, false);

            if ($update)
                $user_id = wp_update_user($userdata);
            else
                $user_id = wp_insert_user($userdata);

            // Is there an error o_O?
            if (is_wp_error($user_id)) {
                $errors[$rkey] = $user_id;
            } else {
                // If no error, let's update the user meta too!
                if ($usermeta) {
                    foreach ($usermeta as $metakey => $metavalue) {
                        $metavalue = maybe_unserialize($metavalue);
                        update_user_meta($user_id, $metakey, $metavalue);
                    }
                }

                // If we created a new user, maybe set password nag and send new user notification?
                if (!$update) {
                    if ($password_nag)
                        update_user_option($user_id, 'default_password_nag', true, true);

                    if ($new_user_notification)
                        wp_new_user_notification($user_id, $userdata['user_pass']);
                }

                // Some plugins may need to do things after one user has been imported. Who know?
                do_action('is_iu_post_user_import', $user_id);

                $user_ids[] = $user_id;
            }

            $rkey++;
        }
        fclose($file_handle);

        // One more thing to do after all imports?
        do_action('is_iu_post_users_import', $user_ids, $errors);

        // Let's log the errors
        self::log_errors($errors);

        return array(
            'user_ids' => $user_ids,
            'errors' => $errors
        );
    }

    /**
     * Log errors to a file
     *
     * @since 0.2
     * */
    private static function log_errors($errors) {
        if (empty($errors))
            return;

        $log = @fopen(self::$log_dir_path . 'is_iu_errors.log', 'a');
        @fwrite($log, sprintf(__('BEGIN %s', 'jobhunt'), date('Y-m-d H:i:s', time())) . "\n");

        foreach ($errors as $key => $error) {
            $line = $key + 1;
            $message = $error->get_error_message();
            @fwrite($log, sprintf(__('[Line %1$s] %2$s', 'jobhunt'), $line, $message) . "\n");
        }

        @fclose($log);
    }

}

if (class_exists('cs_user_import')) {
    $cs_user_import = new cs_user_import();
}
