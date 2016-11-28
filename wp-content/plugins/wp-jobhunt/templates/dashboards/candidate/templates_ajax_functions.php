<?php
/*
 * @candidate ajax profile page
 */

if (!function_exists('cs_ajax_candidate_profile')) {

    /**
     * Start Function how to create and save candidate  metaboxes profile with the help of  Ajax
     */
    function cs_ajax_candidate_profile($uid = '') {
        global $post, $current_user, $cs_form_fields2, $cs_theme_fields, $cs_form_fields_frontend;
        $uid = (isset($_POST['cs_uid']) and $_POST['cs_uid'] <> '') ? $_POST['cs_uid'] : $current_user->ID;
        if ($uid <> '') {
            $cs_user_data = get_userdata($uid);
            $cs_description = $cs_user_data->description;
            $cs_first_name = $cs_user_data->first_name;
            $cs_last_name = $cs_user_data->last_name;
            $cs_display_name = $cs_user_data->display_name;
            $cs_job_title = get_user_meta($uid, 'cs_job_title', true);
            $cs_dob = get_user_meta($uid, 'cs_dob', true);
            $cs_user_status = get_user_meta($uid, 'cs_user_status', true);
            $cs_minimum_salary = get_user_meta($uid, 'cs_minimum_salary', true);
            $cs_allow_search = get_user_meta($uid, 'cs_allow_search', true);
            $cs_religion = get_user_meta($uid, 'cs_religion', true);
            $cs_id_num = get_user_meta($uid, 'cs_id_num', true);
            $cs_facebook = get_user_meta($uid, 'cs_facebook', true);
            $cs_twitter = get_user_meta($uid, 'cs_twitter', true);
            $cs_google_plus = get_user_meta($uid, 'cs_google_plus', true);
            $cs_linkedin = get_user_meta($uid, 'cs_linkedin', true);
            $cs_phone_number = get_user_meta($uid, 'cs_phone_number', true);
            $cs_email = $cs_user_data->user_email;
            $cs_website = $cs_user_data->user_url;
            $cs_marital_status = get_user_meta($uid, 'cs_marital_status', true);
            $cs_value = get_user_meta($uid, 'user_img', true);
            $imagename_only = $cs_value;
            $cs_cover_candidate_img_value = get_user_meta($uid, 'cover_user_img', true);
            $cover_imagename_only = $cs_cover_candidate_img_value;
            $cs_jobhunt = new wp_jobhunt();
            ?>
            <div class="cs-loader"></div>
            <?php if ($cs_display_name != '') { ?>
                <h3 class="cs-candidate-title"><?php printf(__('Welcome %s', 'jobhunt'), esc_html($cs_display_name)) ?></h3>
            <?php } ?>
            <form id="cs_candidate" name="cs_candidate"  method="POST" enctype="multipart/form-data" >
                <div class="scetion-title">
                    <h4><?php _e('My Profile', 'jobhunt'); ?></h4>
                </div>
                <div class="dashboard-content-holder">
                    <section class="cs-profile-info">
                        <div class="cs-img-detail">
                            <div class="alert alert-dismissible user-img"> 
                                <div class="page-wrap" id="cs_user_img_box">
                                    <figure>
                                        <?php
                                        if ($cs_value <> '') {
                                            $cs_value = cs_get_image_url($cs_value, '');
                                            ?>
                                            <img src="<?php echo esc_url($cs_value); ?>" id="cs_user_img_img" width="100" alt="" />
                                            <div class="gal-edit-opts close"><a href="javascript:cs_del_media('cs_user_img')" class="delete">
                                                    <span aria-hidden="true">×</span></a>
                                            </div>
                                        <?php } else { ?>
                                            <img src="<?php echo esc_url($cs_jobhunt->plugin_url()); ?>assets/images/upload-img.jpg" id="cs_user_img_img" width="100" alt="" />
                                            <?php
                                        }
                                        ?>
                                    </figure>
                                </div>
                            </div>
                            <div class="upload-btn-div">
                                <div class="fileUpload uplaod-btn btn cs-color csborder-color">
                                    <span class="cs-color"><?php _e('Browse', 'jobhunt'); ?></span>
                                    <?php
                                    $cs_opt_array = array(
                                        'std' => $imagename_only,
                                        'cust_id' => 'cs_user_img',
                                        'cust_name' => 'media_img',
                                        'cust_type' => 'hidden',
                                    );
                                    $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                    ?>
                                    <label class="browse-icon">
                                        <?php
                                        $cs_opt_array = array(
                                            'std' => __('Browse', 'jobhunt'),
                                            'cust_id' => 'cs_media_upload',
                                            'cust_name' => 'media_upload',
                                            'cust_type' => 'file',
                                            'classes' => 'upload cs-uploadimgjobseek',
                                        );
                                        $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                        ?>
                                    </label>				
                                </div>
                                <br />
                                <span id="cs_candidate_profile_img_msg"><?php _e('Max file size is 1MB, Minimum dimension: 270x210 And Suitable files are .jpg & .png', 'jobhunt'); ?></span>
                            </div>
                        </div>
                        <div class="cs-img-detail">
                            <div class="alert alert-dismissible user-img"> 
                                <div class="page-wrap" id="cs_cover_candidate_img_box">
                                    <figure>
                                        <?php
                                        if ($cs_cover_candidate_img_value <> '') {

                                            $cs_cover_candidate_img_value = cs_get_img_url($cs_cover_candidate_img_value, 'cs_media_4');
                                            ?>
                                            <img src="<?php echo esc_url($cs_cover_candidate_img_value); ?>" id="cs_cover_candidate_img_img" width="100" alt="" />
                                            <div class="gal-edit-opts close">
                                                <a href="javascript:cs_del_media('cs_cover_candidate_img')" class="delete">
                                                    <span aria-hidden="true">×</span>
                                                </a>
                                            </div>
                                        <?php } else { ?>
                                            <img src="<?php echo esc_url($cs_jobhunt->plugin_url()); ?>assets/images/upload-img.jpg" id="cs_cover_candidate_img_img" width="100" alt="" />
                                            <?php
                                        }
                                        ?>
                                    </figure>
                                </div>
                            </div>
                            <div class="upload-btn-div">
                                <div class="fileUpload uplaod-btn btn cs-color csborder-color">
                                    <span class="cs-color"><?php _e('Browse Cover', 'jobhunt'); ?></span>
                                    <?php
                                    $cs_opt_array = array(
                                        'std' => $cover_imagename_only,
                                        'id' => '',
                                        'return' => true,
                                        'cust_id' => 'cs_cover_candidate_img',
                                        'cust_name' => 'cs_cover_candidate_img',
                                        'prefix' => '',
                                    );
                                    echo force_balance_tags($cs_form_fields2->cs_form_hidden_render($cs_opt_array));
                                    $cs_opt_array = array(
                                        'std' => __('Browse Cover', 'jobhunt'),
                                        'id' => '',
                                        'return' => true,
                                        'force_std' => true,
                                        'cust_id' => '',
                                        'cust_name' => 'cand_cover_media_upload',
                                        'classes' => 'left cs-candi-cover-uploadimg upload',
                                        'cust_type' => 'file',
                                    );
                                    echo force_balance_tags($cs_form_fields2->cs_form_text_render($cs_opt_array));
                                    ?>
                                </div>
                                <br /> 
                                <span id="cs_candidate_profile_cover_msg"><?php _e('Max file size is 1MB, Minimum dimension: 1600x400 And Suitable files are .jpg & .png', 'jobhunt') ?></span>
                            </div>
                        </div>
                        <div class="input-info">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <label><?php _e('Full Name', 'jobhunt'); ?></label>
                                    <?php
                                    $cs_opt_array = array(
                                        'cust_id' => 'display_name',
                                        'cust_name' => 'display_name',
                                        'std' => $cs_display_name,
                                        'desc' => '',
                                        'extra_atr' => ' placeholder="' . __('Title', 'jobhunt') . '"',
                                        'required' => 'yes',
                                        'classes' => 'form-control',
                                        'hint_text' => '',
                                    );

                                    $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                    ?>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <label><?php _e('Job Title', 'jobhunt'); ?></label>
                                    <?php
                                    $cs_opt_array = array(
                                        'id' => 'job_title',
                                        'std' => $cs_job_title,
                                        'desc' => '',
                                        'extra_atr' => ' placeholder="' . __('Job Title', 'jobhunt') . '" required="required"',
                                        'classes' => 'form-control',
                                        'hint_text' => '',
                                    );

                                    $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                    ?>
                                </div>

                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <label><?php _e('Minimum Salary', 'jobhunt'); ?></label>
                                    <?php
                                    $cs_opt_array = array(
                                        'id' => 'minimum_salary',
                                        'std' => $cs_minimum_salary,
                                        'desc' => '',
                                        'extra_atr' => ' placeholder="' . __('Minimum Salary', 'jobhunt') . '"',
                                        'classes' => 'form-control',
                                        'hint_text' => '',
                                    );

                                    $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                    ?>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <label><?php _e('Allow In Search', 'jobhunt'); ?></label>
                                    <div class="select-holder">
                                        <?php
                                        $cs_opt_array = array(
                                            'id' => 'allow_search',
                                            'std' => $cs_allow_search,
                                            'desc' => '',
                                            'extra_atr' => 'data-placeholder="' . __("Please Select", "jobhunt") . '"',
                                            'classes' => 'form-control chosen-default chosen-select-no-single',
                                            'options' => array('' => __('Please Select', 'jobhunt'), 'yes' => __('Yes', 'jobhunt'), 'no' => __('No', 'jobhunt')),
                                            'hint_text' => '',
                                        );

                                        $cs_form_fields2->cs_form_select_render($cs_opt_array);
                                        ?>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <label><?php _e('Specialisms', 'jobhunt'); ?></label>
                                    <div>
                                        <?php echo get_specialisms_dropdown('cs_specialisms', 'cs_specialisms', $uid, 'form-control chosen-select', true) ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <label><?php _e('Description', 'jobhunt'); ?></label>
                                    <?php
                                    /*
                                      $cs_description = (isset($cs_description)) ? ($cs_description) : '';
                                      wp_editor($cs_description, 'candidate_content', array(
                                      'textarea_name' => 'candidate_content',
                                      'editor_class' => 'text-input',
                                      'media_buttons' => false,
                                      'textarea_rows' => 6,
                                      'editor_height' => '180px',
                                      'cs_editor' => true,
                                      'quicktags' => false,
                                      'tinymce' => array(
                                      'menubar' => false
                                      )
                                      )
                                      );
                                     */
                                    $cs_description = (isset($cs_description)) ? ($cs_description) : '';
                                    echo $cs_form_fields2->cs_form_textarea_render(
                                            array('name' => __('Description', 'jobhunt'),
                                                'id' => 'candidate_content',
                                                'classes' => 'col-md-12',
                                                'cust_name' => 'candidate_content',
                                                'std' => $cs_description,
                                                'description' => '',
                                                'return' => true,
                                                'array' => true,
                                                'cs_editor' => true,
                                                'force_std' => true,
                                                'hint' => ''
                                            )
                                    );
                                    ?>
                                    <script type="text/javascript">
                                        /*
                                         var str = et_tinyMCEPreInit.replace(/cs_comp_init_detail/gi, 'candidate_content');
                                         var ajax_tinymce_init = JSON.parse(str);
                                         tinymce.init({
                                         selector: "textarea#candidate_content",
                                         menubar: false,
                                         setup: function (editor) {
                                         editor.on('change', function () {
                                         editor.save();
                                         });
                                         }
                                                     
                                         });
                                         tinymce.editors = [];
                                         */
                                    </script>
                                </div>
                            </div>
                        </div>
                    </section> 
                    <section class="cs-social-network">
                        <div class="scetion-title">
                            <h4><?php _e('Social Network', 'jobhunt'); ?></h4>
                        </div>
                        <div class="input-info">
                            <div class="row">
                                <div class="social-media-info">
                                    <div class="social-input col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                        <?php
                                        $cs_opt_array = array(
                                            'id' => 'facebook',
                                            'std' => $cs_facebook,
                                            'desc' => '',
                                            'extra_atr' => ' placeholder="' . __('Facebook', 'jobhunt') . '" required="required"',
                                            'classes' => 'form-control',
                                            'hint_text' => '',
                                        );

                                        $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                        ?>
                                        <i class="icon-facebook2"></i> 
                                    </div>
                                    <div class="social-input col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                        <?php
                                        $cs_opt_array = array(
                                            'id' => 'twitter',
                                            'std' => $cs_twitter,
                                            'desc' => '',
                                            'extra_atr' => ' placeholder="' . __('Twitter', 'jobhunt') . '" required="required"',
                                            'classes' => 'form-control',
                                            'hint_text' => '',
                                        );

                                        $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                        ?>
                                        <i class="icon-twitter6"></i> 
                                    </div>
                                    <div class="social-input col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                        <?php
                                        $cs_opt_array = array(
                                            'id' => 'google_plus',
                                            'std' => $cs_google_plus,
                                            'desc' => '',
                                            'extra_atr' => ' placeholder="' . __('Google Plus', 'jobhunt') . '" required="required"',
                                            'classes' => 'form-control',
                                            'hint_text' => '',
                                        );
                                        $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                        ?>
                                        <i class="icon-googleplus7"></i> 
                                    </div>
                                    <div class="social-input col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                        <?php
                                        $cs_opt_array = array(
                                            'id' => 'linkedin',
                                            'std' => $cs_linkedin,
                                            'desc' => '',
                                            'extra_atr' => ' placeholder="' . __('Linkedin', 'jobhunt') . '" required="required"',
                                            'classes' => 'form-control',
                                            'hint_text' => '',
                                        );

                                        $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                        ?>
                                        <i class="icon-linkedin4"></i> 
                                    </div>
                                </div>

                            </div>
                        </div>
                    </section>
                    <section class="cs-social-network">
                        <div class="scetion-title">
                            <h4><?php _e('Contact Information', 'jobhunt'); ?></h4>
                        </div>
                        <div class="input-info">
                            <div class="row">
                                <div class="social-media-info">
                                    <div class="social-input col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                        <label><?php _e('Phone Number', 'jobhunt'); ?></label>
                                        <?php
                                        $cs_opt_array = array(
                                            'id' => 'phone_number',
                                            'std' => $cs_phone_number,
                                            'desc' => '',
                                            'extra_atr' => ' placeholder="' . __('Phone Number', 'jobhunt') . '" required="required"',
                                            'classes' => 'form-control',
                                            'hint_text' => '',
                                        );

                                        $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                        ?>
                                    </div>
                                    <div class="social-input col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                        <label><?php _e('Email', 'jobhunt'); ?></label>
                                        <?php
                                        $cs_opt_array = array(
                                            'cust_id' => 'user_email',
                                            'cust_name' => 'user_email',
                                            'std' => $cs_email,
                                            'desc' => '',
                                            'extra_atr' => ' placeholder="' . __('Email', 'jobhunt') . '" required="required"',
                                            'required' => 'yes',
                                            'classes' => 'form-control',
                                            'hint_text' => '',
                                        );

                                        $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                        ?>
                                    </div>
                                    <div class="social-input col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                        <label><?php _e('Website', 'jobhunt'); ?></label>
                                        <?php
                                        $cs_opt_array = array(
                                            'cust_id' => 'user_url',
                                            'cust_name' => 'user_url',
                                            'std' => $cs_website,
                                            'desc' => '',
                                            'extra_atr' => ' placeholder="' . __('Website', 'jobhunt') . '"  required="required"',
                                            'classes' => 'form-control',
                                            'hint_text' => '',
                                        );

                                        $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                        ?>
                                    </div>

                                    <?php CS_FUNCTIONS()->cs_frontend_location_fields($uid, '', $current_user); ?>

                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="cs-extra-info">
                        <div class="scetion-title">
                            <h4><?php _e('Extra Information', 'jobhunt'); ?></h4>
                        </div>
                        <div class="input-info">
                            <div class="row">
                                <div class="social-media-info">
                                    <!--<div class="social-input col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                        <label><?php _e('Active Profile for Recruiters', 'jobhunt'); ?></label>
                                        <div class="select-holder">
                                    <?php
                                    $cs_opt_array = array(
                                        'id' => 'allow_search',
                                        'std' => $cs_allow_search,
                                        'desc' => '',
                                        'extra_atr' => 'data-placeholder="' . __("Please Select", "jobhunt") . '"',
                                        'classes' => 'form-control chosen-default chosen-select-no-single',
                                        'options' => array('' => __('Please Select', 'jobhunt'), 'yes' => __('Yes', 'jobhunt'), 'no' => __('No', 'jobhunt')),
                                        'hint_text' => '',
                                    );

                                    $cs_form_fields2->cs_form_select_render($cs_opt_array);
                                    ?>
                                        </div>
                                    </div>-->
                                    <?php
                                    $cs_job_cus_fields = get_option("cs_candidate_cus_fields");
                                    if (is_array($cs_job_cus_fields) && sizeof($cs_job_cus_fields) > 0) {
                                        echo cs_candidate_custom_fields_frontend($uid);
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>

                    </section>

                    <section class="cs-update-btn">
                        <?php
                        $cs_opt_array = array(
                            'std' => 'update_profile',
                            'id' => '',
                            'echo' => false,
                            'cust_name' => 'user_profile',
                            'cust_id' => 'user_profile',
                        );
                        $cs_form_fields2->cs_form_hidden_render($cs_opt_array);

                        $cs_opt_array = array(
                            'std' => $uid,
                            'id' => '',
                            'echo' => false,
                            'cust_name' => 'cs_user',
                            'cust_id' => 'cs_user',
                        );
                        $cs_form_fields2->cs_form_hidden_render($cs_opt_array);
                        ?>
                        <a  href="javascript:void(0);" name="button_action" class="acc-submit cs-section-update cs-color csborder-color" onclick="javascript:ajax_profile_form_save('<?php echo esc_js(admin_url('admin-ajax.php')); ?>', '<?php echo esc_js(wp_jobhunt::plugin_url()); ?>', 'cs_candidate')"><?php _e('Update', 'jobhunt'); ?></a>
                        <?php
                        $cs_opt_array = array(
                            'std' => 'ajax_form_save',
                            'id' => '',
                            'echo' => false,
                            'cust_name' => 'action',
                        );
                        $cs_form_fields2->cs_form_hidden_render($cs_opt_array);

                        $cs_opt_array = array(
                            'std' => $uid,
                            'id' => '',
                            'echo' => false,
                            'cust_name' => 'post_id',
                        );
                        $cs_form_fields2->cs_form_hidden_render($cs_opt_array);
                        ?>
                    </section>  
                </div>

            </form>
            <?php
        } else {
            _e('Please create user profile.', 'jobhunt');
        }
        ?>
        <script type="text/javascript">
            /*
             * modern selection box function
             */
            jQuery(document).ready(function ($) {
                chosen_selectionbox();
            });
            /*
             * modern selection box function
             */
        </script>
        <?php
        die();
    }

    add_action('wp_ajax_cs_ajax_candidate_profile', 'cs_ajax_candidate_profile');
    add_action("wp_ajax_nopriv_cs_ajax_candidate_profile", "cs_ajax_candidate_profile");
}

if (!function_exists('cs_candidate_change_password')) {

    function cs_candidate_change_password() {

        $html = '
        <div class="scetion-title">
            <h3>' . __('Change Password', 'jobhunt') . '</h3>
        </div>
        <div class="change-pass-content-holder">
            <div class="input-info">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <label>' . __('Old Password', 'jobhunt') . '</label>
                        <input type="password" name="old_password" class="form-control">
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <label>' . __('New Password', 'jobhunt') . '</label>
                        <input type="password" name="new_password" class="form-control">
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <label>' . __('Confirm Password', 'jobhunt') . '</label>
                        <input type="password" name="confirm_password" class="form-control">
                    </div>
                    <div class="col-md-12 col-md-12 col-sm-12 col-xs-12">
                        <input type="button" value="' . __('Update', 'jobhunt') . '" id="candidate-change-pass-trigger" class="acc-submit cs-section-update cs-color csborder-color">   
                    </div>
                </div>
            </div>
        </div>';

        echo force_balance_tags($html);
        die;
    }

    add_action('wp_ajax_cs_candidate_change_password', 'cs_candidate_change_password');
    add_action("wp_ajax_nopriv_cs_candidate_change_password", "cs_candidate_change_password");
}

/**
 * End Function how to create and save candidate  metaboxes profile with the help of  Ajax
 *  * Start Function favorite jobs for jobseek in ajax base
 */
if (!function_exists('cs_ajax_candidate_favjobs')) {

    function cs_ajax_candidate_favjobs($uid = '') {
        global $post, $cs_form_fields2;
        $uid = (isset($_POST['cs_uid']) and $_POST['cs_uid'] <> '') ? $_POST['cs_uid'] : '';
        if ($uid <> '') {
            ?>
            <section class="cs-favorite-jobs">
                <?php
                $user = cs_get_user_id();
                if (isset($user) && $user <> '') {
                    $cs_shortlist_array = get_user_meta($user, 'cs-user-jobs-wishlist', true);
                    if (!empty($cs_shortlist_array))
                        $cs_shortlist = array_column_by_two_dimensional($cs_shortlist_array, 'post_id');
                    else
                        $cs_shortlist = array();
                }
                ?>
                <div class="scetion-title">
                    <h3><?php _e('Shortlisted jobs', 'jobhunt'); ?></h3>
                </div>
                <ul class="top-heading-list">
                    <li><span><?php _e('Job Title', 'jobhunt'); ?></span></li>
                    <li><span><?php _e('Date Saved', 'jobhunt'); ?></span></li>
                </ul>
                <?php
                if (!empty($cs_shortlist) && count($cs_shortlist) > 0) {

                    $cs_blog_num_post = 10;
                    if (empty($_REQUEST['page_id_all']))
                        $_REQUEST['page_id_all'] = 1;
                    $mypost = array('posts_per_page' => "-1", 'post__in' => $cs_shortlist, 'post_type' => 'jobs', 'order' => "ASC");
                    $loop_count = new WP_Query($mypost);
                    $count_post = $loop_count->post_count;
                    $args = array('posts_per_page' => $cs_blog_num_post, 'post_type' => 'jobs', 'paged' => $_REQUEST['page_id_all'], 'order' => 'DESC', 'orderby' => 'post_date', 'post_status' => 'publish', 'ignore_sticky_posts' => 1, 'post__in' => $cs_shortlist,);
                    $custom_query = new WP_Query($args);
                    if ($custom_query->have_posts()):
                        ?>
                        <ul class="feature-jobs">
                            <?php
                            while ($custom_query->have_posts()): $custom_query->the_post();
                                $cs_jobs_thumb_url = '';
                                $employer_img = '';
                                // get employer images at run time
                                $cs_job_employer = get_post_meta($post->ID, "cs_job_username", true);
                                $employer_img = get_the_author_meta('user_img', $cs_job_employer);
                                if ($employer_img != '') {
                                    $cs_jobs_thumb_url = cs_get_img_url($employer_img, 'cs_media_5');
                                }
                                if (!cs_image_exist($cs_jobs_thumb_url) || $cs_jobs_thumb_url == "") {
                                    $cs_jobs_thumb_url = esc_url(wp_jobhunt::plugin_url() . 'assets/images/img-not-found16x9.jpg');
                                }
                                ?>
                                <li class="holder-<?php echo intval($post->ID); ?>">
                                    <a class="hiring-img" href="<?php echo esc_url(get_permalink($post->ID)); ?>"><img src="<?php echo esc_url($cs_jobs_thumb_url); ?>" alt=""></a>
                                    <div class="company-detail-inner">
                                        <h6><a href="<?php echo esc_url(the_permalink()); ?>"><?php the_title(); ?></a></h6>
                                    </div>
                                    <div class="company-date-option">
                                        <span>
                                            <?php
                                            // getting added in wishlist date
                                            $finded = in_multiarray($post->ID, $cs_shortlist_array, 'post_id');
                                            if ($finded != '')
                                                if ($cs_shortlist_array[$finded[0]]['date_time'] != '') {
                                                    echo date_i18n(get_option('date_format'), $cs_shortlist_array[$finded[0]]['date_time']);
                                                }
                                            ?>
                                        </span>
                                        <div class="control" >
                                            <a data-toggle="tooltip" data-placement="top" title="<?php echo __("Remove", "jobhunt"); ?>" data-postid="<?php echo intval($post->ID); ?>" href="javascript:void(0);" onclick="javascript:cs_delete_wishlist('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', this)"  class="close close-<?php echo intval($post->ID); ?>"><i class="icon-trash-o"></i></a>
                                        </div>
                                    </div>
                                </li>
                                <?php
                            endwhile;
                            ?>
                        </ul>
                        <?php
                        //==Pagination Start
                        if ($count_post > $cs_blog_num_post && $cs_blog_num_post > 0) {
                            echo '<nav>';
                            echo cs_ajax_pagination($count_post, $cs_blog_num_post, 'shortlisted-jobs', 'candidate', $uid, '');
                            echo '</nav>';
                        }//==Pagination End 
                    endif;
                    ?>

                    <?php
                } else {
                    echo '<div class="cs-no-record">' . cs_info_messages_listing(__("There is no shortlist job.", 'jobhunt')) . '</div>';
                }
                ?>
            </section>  		
            <?php
        } else {
            echo '<div class="no-result"><h1>' . __('Please create user profile.', 'jobhunt') . '</h1></div>';
        }
        ?>
        <script>
            jQuery(document).ready(function () {
                jQuery('[data-toggle="tooltip"]').tooltip();
            });
        </script>
        <?php
        die();
    }

    add_action("wp_ajax_cs_ajax_candidate_favjobs", "cs_ajax_candidate_favjobs");
    add_action("wp_ajax_nopriv_cs_ajax_candidate_favjobs", "cs_ajax_candidate_favjobs");
}
/**
 * End Function favorite jobs for jobseek in ajax base
 */
/**
 * Start Function Applied  jobs for jobseek in ajax base
 */
if (!function_exists('cs_ajax_candidate_appliedjobs')) {

    function cs_ajax_candidate_appliedjobs($uid = '') {
        global $post, $cs_form_fields2;
        $uid = (isset($_POST['cs_uid']) and $_POST['cs_uid'] <> '') ? $_POST['cs_uid'] : '';
        if ($uid <> '') {
            $user = cs_get_user_id();
            if (isset($user) && $user <> '') {
                $cs_jobapplied_array = get_user_meta($user, 'cs-user-jobs-applied-list', true);
                if (!empty($cs_jobapplied_array))
                    $cs_jobapplied = array_column_by_two_dimensional($cs_jobapplied_array, 'post_id');
                else
                    $cs_jobapplied = array();
            }
            ?>
            <div class="cs-loader"></div>
            <section class="cs-favorite-jobs">
                <div class="scetion-title">
                    <h3><?php _e('Applied Jobs', 'jobhunt'); ?></h3>
                    <?php
                    $args = array(
                        'posts_per_page' => "-1", 'post__in' => $cs_jobapplied, 'post_type' => 'jobs',
                        'meta_query' => array(
                            'relation' => 'AND',
                            array(
                                'key' => 'cs_job_expired',
                                'value' => strtotime(date('d-m-Y')),
                                'compare' => '<',
                            )
                        ),
                        'order' => "ASC"
                    );
                    $custom_query = new WP_Query($args);
                    if ($custom_query->post_count > 0) {
                        ?>
                        <span>
                            <a href="javascript:void(0);" onclick="javascript:cs_ajax_remove_appliedjobs('<?php echo esc_js(admin_url('admin-ajax.php')) ?>', '<?php echo esc_js(wp_jobhunt::plugin_url()); ?>',<?php echo absint($uid); ?>);">
                                <?php _e('Remove Ended Jobs', 'jobhunt'); ?>
                            </a>
                        </span>
                        <?php
                    }
                    ?>
                </div>
                <ul class="top-heading-list">
                    <li><span><?php _e('Job Title', 'jobhunt'); ?></span></li>
                    <li><span><?php _e('Date Applied', 'jobhunt'); ?></span></li>
                </ul>
                <?php if (!empty($cs_jobapplied) && count($cs_jobapplied) > 0) { ?>
                    <ul class="feature-jobs">
                        <?php
                        $cs_blog_num_post = 10;
                        if (empty($_REQUEST['page_id_all']))
                            $_REQUEST['page_id_all'] = 1;
                        $mypost = array('posts_per_page' => "-1", 'post__in' => $cs_jobapplied, 'post_type' => 'jobs', 'order' => "ASC");
                        $loop_count = new WP_Query($mypost);
                        $count_post = $loop_count->post_count;

                        $args = array('posts_per_page' => $cs_blog_num_post, 'post__in' => $cs_jobapplied, 'post_type' => 'jobs', 'paged' => $_REQUEST['page_id_all'], 'order' => "ASC");
                        $custom_query = new WP_Query($args);

                        if ($custom_query->have_posts()) :
                            while ($custom_query->have_posts()) : $custom_query->the_post();
                                $cs_job_expired = get_post_meta($post->ID, 'cs_job_expired', true) . '<br>';
                                $cs_org_name = get_post_meta($post->ID, 'cs_org_name', true);

                                $cs_jobs_thumb_url = '';
                                $employer_img = '';
                                // get employer images at run time
                                $cs_job_employer = get_post_meta($post->ID, "cs_job_username", true);
                                $employer_img = get_the_author_meta('user_img', $cs_job_employer);
                                if ($employer_img != '') {
                                    $cs_jobs_thumb_url = cs_get_img_url($employer_img, 'cs_media_5');
                                }
                                if (!cs_image_exist($cs_jobs_thumb_url) || $cs_jobs_thumb_url == "") {
                                    $cs_jobs_thumb_url = esc_url(wp_jobhunt::plugin_url() . 'assets/images/img-not-found16x9.jpg');
                                }
                                ?>
                                <li class="holder-<?php
                                echo intval($post->ID);
                                if ($cs_job_expired < strtotime(date('d-m-Y'))) {
                                    echo ' cs-expired';
                                }
                                ?>">
                                    <a class="hiring-img" href="<?php echo esc_url(get_permalink($post->ID)); ?>"><img src="<?php echo esc_url($cs_jobs_thumb_url); ?>" alt=""></a>
                                    <div class="company-detail-inner">
                                        <?php
                                        echo '<h6>
                                                <a href="' . esc_url(get_the_permalink()) . '">' . get_the_title() . '</a>';
                                        if ($cs_org_name <> '') {
                                            echo '<a href="' . esc_url(get_the_permalink()) . '">@ ' . $cs_org_name . '</a>';
                                        }
                                        echo '</h6>';
                                        if ($cs_job_expired < strtotime(date('d-m-Y'))) {
                                            echo '<span>';
                                            _e('Ended', 'jobhunt');
                                            echo '</span>';
                                        }
                                        ?>
                                    </div>
                                    </div>
                                    <div class="company-date-option">
                                        <span><?php
                                            $finded = in_multiarray($post->ID, $cs_jobapplied_array, 'post_id');
                                            if ($finded != '')
                                                if ($cs_jobapplied_array[$finded[0]]['date_time'] != '') {
                                                    echo date_i18n(get_option('date_format'), $cs_jobapplied_array[$finded[0]]['date_time']);
                                                }
                                            ?></span>
                                        <?php
                                        if ($cs_job_expired < strtotime(date('d-m-Y'))) {
                                            ?>
                                            <div class="control">
                                                <a data-toggle="tooltip" data-placement="top" title="<?php _e("Remove", "jobhunt"); ?>" id="remove_resume_link<?php echo absint($post->ID); ?>" href="javascript:void(0);"  class="delete" 
                                                   onclick="javascript:cv_removejobs('<?php echo esc_js(admin_url('admin-ajax.php')) ?>', '<?php echo absint($post->ID); ?>',<?php echo absint($uid); ?>);" > 
                                                    <i class="icon-trash-o"></i>
                                                </a>  
                                            </div>
                                        <?php } ?>
                                    </div>
                                </li>
                                <?php
                            endwhile;
                        endif;
                        ?>
                    </ul>


                    <?php
                    //==Pagination Start
                    if ($count_post > $cs_blog_num_post && $cs_blog_num_post > 0) {
                        echo '<nav>';
                        echo cs_ajax_pagination($count_post, $cs_blog_num_post, 'applied-jobs', 'candidate', $uid, '');
                        echo '</nav>';
                    }//==Pagination End 
                    ?>
                    <?php
                } else {
                    echo '<div class="cs-no-record">' . cs_info_messages_listing(__("You did not applied for any job.", 'jobhunt')) . '</div>';
                }
                ?>
            </section>
            <?php
        } else {
            echo '<div class="no-result"><h1>' . __('Please create user profile.', 'jobhunt') . '</h1></div>';
        }
        ?>
        <script>
            jQuery(document).ready(function () {
                jQuery('[data-toggle="tooltip"]').tooltip();
            });
        </script>
        <?php
        die();
    }

    add_action("wp_ajax_cs_ajax_candidate_appliedjobs", "cs_ajax_candidate_appliedjobs");
    add_action("wp_ajax_nopriv_cs_ajax_candidate_appliedjobs", "cs_ajax_candidate_appliedjobs");
}
/**
 * End Function Applied  jobs for jobseek in ajax base
 */
/**
 * Start Function for Candidate Resume in Ajax base
 */
if (!function_exists('cs_ajax_candidate_resume')) {

    function cs_ajax_candidate_resume($uid = '') {
        global $post, $cs_plugin_options, $cs_form_fields2;
        $cs_award_switch = isset($cs_plugin_options['cs_award_switch']) ? $cs_plugin_options['cs_award_switch'] : '';
        $cs_portfolio_switch = isset($cs_plugin_options['cs_portfolio_switch']) ? $cs_plugin_options['cs_portfolio_switch'] : '';
        $cs_skills_switch = isset($cs_plugin_options['cs_skills_switch']) ? $cs_plugin_options['cs_skills_switch'] : '';
        $cs_education_switch = isset($cs_plugin_options['cs_education_switch']) ? $cs_plugin_options['cs_education_switch'] : '';
        $cs_experience_switch = isset($cs_plugin_options['cs_experience_switch']) ? $cs_plugin_options['cs_experience_switch'] : '';
        $cs_document_switch = isset($cs_plugin_options['cs_document_switch']) ? $cs_plugin_options['cs_document_switch'] : '';
        $uid = (isset($_POST['cs_uid']) and $_POST['cs_uid'] <> '') ? $_POST['cs_uid'] : '';
        $cs_post_id = $uid;
        if ($cs_post_id <> '') {
            ?>
            <div id="main_resume_content">
                <section class="tabs-list">
                    <h3><?php _e('My Resume', 'jobhunt'); ?></h3>
                </section>
                <?php if ($cs_education_switch == 'on') { ?>        
                    <section class="cs-tabs cs-education" id="education">
                        <h4><i class="icon-graduation"></i><?php _e('Education', 'jobhunt'); ?></h4>
                        <ul class="accordion-list">
                            <form id="edu_list" name="cs_edu_list" enctype="multipart/form-data" method="POST">
                                <?php
                                cs_education_list_fe();
                                $cs_opt_array = array(
                                    'std' => 'ajax_form_save',
                                    'id' => '',
                                    'echo' => true,
                                    'cust_name' => 'action',
                                );
                                $cs_form_fields2->cs_form_hidden_render($cs_opt_array);

                                $cs_opt_array = array(
                                    'std' => $cs_post_id,
                                    'id' => '',
                                    'echo' => true,
                                    'cust_name' => 'cs_user',
                                );
                                $cs_form_fields2->cs_form_hidden_render($cs_opt_array);
                                ?>
                            </form>
                        </ul>
                    </section>
                    <?php
                }
                if ($cs_experience_switch == 'on') {
                    ?>        
                    <section class="cs-tabs cs-experience" id="experience">
                        <h4><i class="icon-briefcase4"></i><?php _e('Experience', 'jobhunt'); ?></h4>
                        <ul class="accordion-list">
                            <form id="experience_list" enctype="multipart/form-data" method="POST">
                                <?php
                                cs_experience_list_fe();

                                $cs_opt_array = array(
                                    'std' => 'ajax_form_save',
                                    'cust_id' => 'action',
                                    'cust_name' => 'action',
                                    'cust_type' => 'hidden',
                                );
                                $cs_form_fields2->cs_form_text_render($cs_opt_array);

                                $cs_opt_array = array(
                                    'std' => $cs_post_id,
                                    'cust_id' => 'cs_user',
                                    'cust_name' => 'cs_user',
                                    'cust_type' => 'hidden',
                                );
                                $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                ?>

                            </form>
                        </ul>
                    </section>
                    <?php
                }
                if ($cs_portfolio_switch == 'on') {
                    ?>        
                    <section class="cs-tabs cs-portfolio" id="portfolio">
                        <h4><i class="icon-pictures5"></i><?php _e('Portfolio', 'jobhunt'); ?></h4>

                        <ul class="accordion-list">
                            <?php cs_portfolio_list_fe(); ?>
                        </ul>

                    </section>
                    <?php
                }
                if ($cs_skills_switch == 'on') {
                    ?>        
                    <section class="cs-tabs cs-skills" id="skills">
                        <h4><i class="icon-pie2"></i><?php _e('Skills', 'jobhunt'); ?></h4>

                        <form id="skill_list" enctype="multipart/form-data" method="POST">
                            <?php
                            cs_skills_list_fe();
                            ?>

                            <?php
                            $cs_opt_array = array(
                                'std' => 'ajax_form_save',
                                'cust_id' => 'action',
                                'cust_name' => 'action',
                                'cust_type' => 'hidden',
                            );
                            $cs_form_fields2->cs_form_text_render($cs_opt_array);

                            $cs_opt_array = array(
                                'std' => $cs_post_id,
                                'cust_id' => 'cs_user',
                                'cust_name' => 'cs_user',
                                'cust_type' => 'hidden',
                            );
                            $cs_form_fields2->cs_form_text_render($cs_opt_array);
                            ?>

                        </form>
                    </section>
                    <?php
                }
                if ($cs_award_switch == 'on') {
                    ?>        
                    <section class="cs-tabs cs-awards" id="awards">
                        <h4><i class="icon-trophy5"></i><?php _e('Honors & Awards', 'jobhunt'); ?></h4>

                        <form id="award_list"   enctype="multipart/form-data" method="POST">
                            <?php cs_award_list_fe(); ?>
                            <?php
                            $cs_opt_array = array(
                                'std' => 'ajax_form_save',
                                'cust_id' => 'action',
                                'cust_name' => 'action',
                                'cust_type' => 'hidden',
                            );
                            $cs_form_fields2->cs_form_text_render($cs_opt_array);

                            $cs_opt_array = array(
                                'std' => $cs_post_id,
                                'cust_id' => 'cs_user',
                                'cust_name' => 'cs_user',
                                'cust_type' => 'hidden',
                            );
                            $cs_form_fields2->cs_form_text_render($cs_opt_array);
                            ?>
                        </form>

                    </section>
                    <?php
                }
                ?>
            </div><?php
        } else {
            _e('Please create user profile.', 'jobhunt');
        }
        ?>
        <script>
            jQuery(document).ready(function () {
                jQuery('[data-toggle="tooltip"]').tooltip();
            });
        </script>
        <?php
        die();
    }

    add_action("wp_ajax_cs_ajax_candidate_resume", "cs_ajax_candidate_resume");
    add_action("wp_ajax_nopriv_cs_ajax_candidate_resume", "cs_ajax_candidate_resume");
}
/**
 * End Function for Candidate Resume in Ajax base
 */
/**
 * Start Function for Candidate CV's & Cover in Ajax Base
 */
if (!function_exists('cs_ajax_candidate_cvcover')) {

    function cs_ajax_candidate_cvcover($uid = '') {
        global $post, $cs_form_fields_frontend, $cs_form_fields2;
        if ($uid == '')
            $uid = (isset($_POST['cs_uid']) and $_POST['cs_uid'] <> '') ? $_POST['cs_uid'] : '';
        $cs_cover_letter = get_user_meta($uid, 'cs_cover_letter', true);
        $cs_candidate_cv = get_user_meta($uid, 'cs_candidate_cv', true);
        ?>
        <div class="cs-loader"></div>
        <section class="cs-cover-letter">
            <div class="scetion-title">
                <h3><?php _e('CV & Cover Letter', 'jobhunt'); ?> </h3>
            </div>
            <div class="dashboard-content-holder">
                <form id="candidate_cv" name="cs_candidate"  enctype="multipart/form-data" method="POST">
                    <div class="cs-img-detail resume-upload">
                        <div class="inner-title">
                            <h5><?php _e('Your CV', 'jobhunt'); ?></h5>
                        </div>
                        <div class="upload-btn-div">

                            <div class="dragareamain" style="padding-bottom:0px;">
                                <script type="text/ecmascript">
                                    jQuery(document).ready(function(){
                                        jQuery('.cs-uploadimg').change( function(e) {
                                            var img = URL.createObjectURL(e.target.files[0]);
                                            //var img = URL.createObjectURL(e.target.files[0]['type']);
                                            jQuery('#cs_candidate_cv').attr('value', img);
                                        });
                                    });
                                </script>
                                <div class="fileUpload uplaod-btn btn csborder-color cs-color">
                                    <span class="cs-color"><?php _e('Browse', 'jobhunt'); ?></span>
                                    <label class="browse-icon">

                                        <?php
                                        $cs_opt_array = array(
                                            'std' => __('Browse', 'jobhunt'),
                                            'cust_id' => 'media_upload',
                                            'cust_name' => 'media_upload',
                                            'cust_type' => 'file',
                                            'force_std' => true,
                                            'extra_atr' => ' onchange="checkName(this, \'cs_candidate_cv\', \'button_action\')"',
                                            'classes' => 'upload cs-uploadimg cs-color csborder-color',
                                        );
                                        $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                        ?>
                                    </label>
                                </div>
                                <div id="selecteduser-cv">
                                    <?php
                                    if (isset($cs_candidate_cv) and $cs_candidate_cv <> '' && (!isset($cs_candidate_cv['error']))) {
                                        $cs_opt_array = array(
                                            'std' => $cs_candidate_cv,
                                            'cust_id' => 'cs_candidate_cv',
                                            'cust_name' => 'cs_candidate_cv',
                                            'cust_type' => 'hidden',
                                        );
                                        $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                        ?>
                                        <div class="alert alert-dismissible user-resume" id="cs_candidate_cv_box">
                                            <div>
                                                <?php
                                                if (isset($cs_candidate_cv) && $cs_candidate_cv != '') {
                                                    if (cs_check_coverletter_exist($cs_candidate_cv)) {
                                                        $uploads = wp_upload_dir();
                                                        echo '<a target="_blank" href="' . esc_url($cs_candidate_cv) . '">';
                                                        // uploaded file
                                                        $parts = preg_split('~_(?=[^_]*$)~', basename($cs_candidate_cv));
                                                        echo esc_html($parts[0]); // outputs "one_two_three"
                                                        echo '</a>';
                                                        ?>
                                                        <div class="gal-edit-opts close"><a href="javascript:cs_del_cover_letter('cs_candidate_cv')" class="delete">
                                                                <span aria-hidden="true">×</span></a>
                                                        </div>
                                                        <?php
                                                    } else {
                                                        _e("File not Available", "jobhunt");
                                                    }
                                                }
                                                ?>

                                            </div>
                                        </div>
                                    <?php } ?>				
                                </div>
                            </div>
                            <span class="cs-status-msg-cv-upload"><?php _e('Suitable files are .doc,docx,rft,pdf & .pdf', 'jobhunt'); ?></span>              
                        </div>
                    </div>
                    <div class="inner-title">
                        <h5><?php _e('Your Cover Letter', 'jobhunt'); ?></h5>
                    </div><?php
                    $cs_cover_letter = (isset($cs_cover_letter)) ? ($cs_cover_letter) : '';
					echo $cs_form_fields2->cs_form_textarea_render(
                         array('name' => __('Your Cover Letter', 'jobhunt'),
                            'id' => 'cs_cover_letter',
                            'classes' => 'col-md-12',
                            'cust_name' => 'cs_cover_letter',
                            'std' => $cs_cover_letter,
                            'description' => '',
                            'return' => true,
                            'array' => true,
                            'cs_editor' => true,
                            'force_std' => true,
                            'hint' => ''
                        )
					);
                    ?>
                    <section class="cs-update-btn">
                        <?php
                        $cs_opt_array = array(
                            'std' => 'update_cv_profile',
                            'cust_id' => 'user_profile',
                            'cust_name' => 'user_profile',
                            'cust_type' => 'hidden',
                        );
                        $cs_form_fields2->cs_form_text_render($cs_opt_array);

                        $cs_opt_array = array(
                            'std' => $uid,
                            'cust_id' => 'cs_user',
                            'cust_name' => 'cs_user',
                            'cust_type' => 'hidden',
                        );
                        $cs_form_fields2->cs_form_text_render($cs_opt_array);
                        ?>
                        <a  href="javascript:void(0);" name="button_action" class="acc-submit cs-section-update cs-color csborder-color" onclick="javascript:ajax_candidate_cv_form_save('<?php echo esc_js(admin_url('admin-ajax.php')); ?>', '<?php echo esc_js(wp_jobhunt::plugin_url()); ?>', 'candidate_cv', '<?php echo absint($uid); ?>')"><?php _e('Update', 'jobhunt'); ?></a>

                        <?php
                        $cs_opt_array = array(
                            'std' => 'ajax_candidate_cv_form_save',
                            'cust_id' => 'action',
                            'cust_name' => 'action',
                            'cust_type' => 'hidden',
                        );
                        $cs_form_fields2->cs_form_text_render($cs_opt_array);

                        $cs_opt_array = array(
                            'std' => $uid,
                            'cust_id' => 'cs_user',
                            'cust_name' => 'cs_user',
                            'cust_type' => 'hidden',
                        );
                        $cs_form_fields2->cs_form_text_render($cs_opt_array);
                        ?>
                    </section>
                </form>
            </div>
        </section>
        <?php
        die();
    }

    add_action("wp_ajax_cs_ajax_candidate_cvcover", "cs_ajax_candidate_cvcover");
    add_action("wp_ajax_nopriv_cs_ajax_candidate_cvcover", "cs_ajax_candidate_cvcover");
}
/**
 * End Function for Candidate CV's & Cover in Ajax Base
 */
/**
 * Start Function for Candidate post type session in Ajax
 */
if (!function_exists('cs_ajax_set_session')) {

    function cs_ajax_set_session() {
        if (session_id() == '') {
            session_start();
        }
        $_SESSION["cs_post_type"] = $_POST['post_type'];
        die();
    }

    add_action("wp_ajax_cs_ajax_set_session", "cs_ajax_set_session");
    add_action("wp_ajax_nopriv_cs_ajax_set_session", "cs_ajax_set_session");
}