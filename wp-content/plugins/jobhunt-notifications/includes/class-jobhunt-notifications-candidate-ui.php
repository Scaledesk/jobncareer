<?php
/**
 * Create Employer Dashboard UI
 *
 * @package	Job Hunt
 */

// Direct access not allowed.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WP_Job_Hunt_Alerts_Shortcode class.
 */
class WP_Job_Hunt_Candidate_UI {
	/**
	 * Construct.
	 */
	public function __construct() {
		// Initialize Addon
        $this->init();
	}

	public function init() {
		// Add hook for dashboard candidate top menu links.
        add_action('jobhunt_top_menu_candidate_dashboard', array($this, 'top_menu_candidate_dashboard_callback'), 10, 3);
		
		// Add Candidate left menu and tabs.
        add_action('jobhunt_candidate_dashboard_menu_left', array($this, 'add_candidate_dashboard_menu_left'), 10, 2);
        add_action('jobhunt_candidate_dashboard_tabs', array($this, 'add_candidate_dashboard_tab'), 10, 2);
		
		// Handle AJAX to list all candidate job alerts in frontend dashboard.
        add_action('wp_ajax_jobhunt_candidate_jobalerts', array($this, 'list_candidate_jobalerts_callback'));
        add_action('wp_ajax_nopriv_jobhunt_candidate_jobalerts', array($this, 'list_candidate_jobalerts_callback'));
	}
	public function top_menu_candidate_dashboard_callback($cs_page_id, $uid, $data_toogle) {
        echo '<li>
			<a href="' . esc_url(cs_users_profile_link($cs_page_id, 'job-alerts', $uid)) . '" ' . force_balance_tags($data_toogle) . '><i class="icon-bell-o"></i> ' . __('Job Alerts', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN) . '</a>
		</li>';
    }
	
	public function add_candidate_dashboard_menu_left($profile_tab, $uid) {
        $is_active = '';
        if (isset($profile_tab) && $profile_tab == 'job-alerts') {
            $is_active = ' active ';
        }
        echo '
			 <li id="candidate_left_job_alerts_link" class="' . $is_active . '">
				<a id="candidate_job_alerts_click_link_id"  href="javascript:void(0);" onclick="cs_dashboard_tab_load(\'job-alerts\', \'candidate\', \'' . esc_js(admin_url('admin-ajax.php')) . '\', \'' . absint($uid) . '\');" >
					<i class="icon-bell-o"></i>' . __('Job Alerts', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN) . '
				</a>
			</li>
		';
    }
	
	public function add_candidate_dashboard_tab($profile_tab, $uid) {
        $is_active = '';
        $script = '';
        if (isset($profile_tab) && $profile_tab == 'job-alerts') {
            $is_active = 'active';
            $script .= '
				<script type="text/javascript">
					jQuery(window).load(function () {
						(function (admin_url, cs_uid) {
							var dataString = \'cs_uid=\' + cs_uid + \'&action=jobhunt_candidate_jobalerts\';
							cs_data_loader_load(\'#job-alerts\');
							jQuery.ajax({
								type: "POST",
								url: admin_url,
								data: dataString,
								success: function (response) {
                                                                        jQuery(\'#job-alerts\').html(response);
									jQuery("#job-alerts .cs-loader").fadeTo(2000, 500).slideUp(500);
								}
							});
						})("' . esc_js(admin_url('admin-ajax.php')) . '", "' . absint($uid) . '");
					});
					function cs_dashboard_tab_load_job_alerts() {
						//pass
					}
				</script>
			';
        }
        echo '
			<div class="tab-pane ' . $is_active . ' fade1 tabs-container" id="job-alerts">
				<div class="cs-loader"></div>
				' . $script . '
			</div>
		';
    }
	
	public function list_candidate_jobalerts_callback() {
        global $post, $cs_form_fields2;
        $cs_blog_num_post = 10;

        $uid = empty($_POST['cs_uid']) ? '' : sanitize_text_field($_POST['cs_uid']);
        if ($uid <> '') {
            $user_id = cs_get_user_id();
            if (!empty($user_id)) {
                // Get count of total posts
                $args = array(
                    'author' => $user_id, // I could also use $user_ID, right?
                    'post_type' => 'job-alert',
                    'posts_per_page' => -1,
                    'orderby' => 'post_date',
                    'order' => 'DESC',
                );
                $job_alerts = new WP_Query($args);
                $alerts_count = $job_alerts->post_count;

                $page_num = empty($_POST['page_id_all']) ? 1 : sanitize_text_field($_POST['page_id_all']);
                // Get alerts with respect to pagination
                $args = array(
                    'author' => $user_id, // I could also use $user_ID, right?
                    'post_type' => 'job-alert',
                    'posts_per_page' => $cs_blog_num_post,
                    'paged' => $page_num,
                    'orderby' => 'post_date',
                    'order' => 'DESC',
                );
                $job_alerts = new WP_Query($args);
            }
            ?>
            <div class="cs-loader"></div>
            <section class="cs-favorite-jobs">
                <div class="scetion-title">
                    <h3><?php _e('Job Alerts', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN); ?></h3>
                </div>
				<?php
					$cs_plugin_options = get_option('cs_plugin_options');
					$search_list_page = '';
					if ( ! empty( $cs_plugin_options ) && $cs_plugin_options['cs_search_result_page']) {
						$search_list_page = $cs_plugin_options['cs_search_result_page'];
					} else {
						echo '<div class="error">'._e('Please select default search list page in Job Plugin Options', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN).'</div>';
					}
				?>
                <ul class="top-heading-list">
                    <li><span><?php _e('Alert Details', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN); ?></span></li>
                    <li><span><?php _e('Email Frequency', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN); ?></span></li>
                </ul>
                <?php if (!empty($job_alerts) && $job_alerts->have_posts()) { ?>
                    <ul class="feature-jobs">
                        <?php
                        while ($job_alerts->have_posts()) :
                            $job_alerts->the_post();

                            $cs_job_expired = get_post_meta($post->ID, 'cs_job_expired', true) . '<br>';
                            $cs_org_name = get_post_meta($post->ID, 'cs_org_name', true);
                            // Get job's Meta Data.
                            // $cs_email = get_post_meta( $post->ID, 'cs_email', true );
                            $cs_name = get_post_meta($post->ID, 'cs_name', true);
                            $cs_query = get_post_meta($post->ID, 'cs_query', true);
                            // Get selected frequencies.
                            $frequencies = array(
                                'annually',
                                'biannually',
                                'monthly',
                                'fortnightly',
                                'weekly',
                                'daily',
                                'never',
                            );
                            $selected_frequencies = array();
                            foreach ($frequencies as $key => $frequency) {
                                $frequency_val = get_post_meta($post->ID, 'cs_frequency_' . $frequency, true);
                                if (!empty($frequency_val) && $frequency_val == 'on') {
                                    $selected_frequencies[] = $frequency;
                                }
                            }
							$search_keywords = WP_Job_Hunt_Alert_Helpers::query_to_array($cs_query);
							
                            ?>
                            <li class="holder-<?php echo intval($post->ID); ?>">
                                <div class="company-detail-inner">
                                    <h6><a href="<?php echo esc_url(get_permalink($search_list_page)) . '?' . http_build_query( $search_keywords ); ?>"><?php echo $cs_name; ?></a></h6><br>
                                    <b><?php _e('Search Keywords:', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN); ?> </b><?php echo implode(', ', array_values( $search_keywords ) ); ?><br>
                                </div>

                                <div class="company-date-option">
                                    <?php echo implode(', ', array_map('ucfirst', $selected_frequencies)); ?>
                                    <div class="control delete-job-alert">
                                        <a data-toggle="tooltip" data-placement="top" title="<?php _e('Remove', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN); ?>" id="remove_resume_link<?php echo absint($post->ID); ?>" href="#"  class="delete" data-post-id="<?php echo absint($post->ID); ?>">
                                            <i class="icon-trash-o"></i>
                                        </a>
                                    </div>
                                </div>
                            </li>
                            <?php
                        endwhile;
                        ?>
                    </ul>
                    <?php
                    //==Pagination Start
                    if ($alerts_count > $cs_blog_num_post && $cs_blog_num_post > 0) {
                        echo '<nav>';
                        echo cs_ajax_pagination($alerts_count, $cs_blog_num_post, 'job-alerts', 'candidate', $uid, '');
                        echo '</nav>';
                    }//==Pagination End 
                    ?>
                    <?php
                } else {
                    echo '<div class="cs-no-record">' . cs_info_messages_listing(__("You did not have any job alerts.", JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN)) . '</div>';
                }
                ?>
            </section>
            <?php
        } else {
            echo '<div class="no-result"><h1>' . __('Please create user profile.', JOBHUNT_NOTIFICATIONS_PLUGIN_DOMAIN) . '</h1></div>';
        }
        ?>
        <script>
            jQuery(document).ready(function () {
                jQuery('[data-toggle="tooltip"]').tooltip();
            });
        <?php echo WP_Job_Hunt_Alert_Helpers::get_script_str(); ?>
        </script>
        <?php
        wp_die();
    }
}

new WP_Job_Hunt_Candidate_UI();