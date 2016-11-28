<?php
/**
 * @Jobs Counter widget Class
 *
 *
 */
if (!class_exists('socialcontact')) {

    class socialcontact extends WP_Widget {

        /**
         * Start Function how to create Jobs Counter Module
         *        
         */
        public function __construct() {
            parent::__construct(
                    'socialcontact', // Base ID
                    __('CS : Social info', 'jobhunt'), // Name
                    array('classname' => 'widget-text', 'description' => __('Enter Social info ', 'jobhunt'),)
            );
        }

        /**
         * Start Function how to create Jobs Counter html form
         *        
         */
        function form($instance) {
            global $cs_theme_form_fields, $cs_html_fields, $cs_theme_html_fields;
            $instance = wp_parse_args((array) $instance, array('title' => ''));
            $title = $instance['title'];
            $sub_title = isset($instance['sub_title']) ? $instance['sub_title'] : '';
            $image_url = isset($instance['image_url']) ? esc_url($instance['image_url']) : '';
            $telephone = isset($instance['telephone']) ? esc_attr($instance['telephone']) : '';
            $email = isset($instance['email']) ? esc_attr($instance['email']) : '';
            $fb_url = isset($instance['fb_url']) ? esc_url($instance['fb_url']) : '';
            $tw_url = isset($instance['tw_url']) ? esc_url($instance['tw_url']) : '';
            $lk_url = isset($instance['lk_url']) ? esc_url($instance['lk_url']) : '';
            $gl_url = isset($instance['gl_url']) ? esc_url($instance['gl_url']) : '';
            $ig_url = isset($instance['ig_url']) ? esc_url($instance['ig_url']) : '';
            $yt_url = isset($instance['yt_url']) ? esc_url($instance['yt_url']) : '';

            $randomID = rand(135434, 957655);
            $random = rand(1345434, 957345345655);




            $cs_opt_array = array(
                'name' => __('Title', 'jobhunt'),
                'desc' => '',
                'hint_text' => '',
                'echo' => false,
                'field_params' => array(
                    'std' => $title,
                    'id' => '',
                    'classes' => '',
                    'cust_id' => CS_FUNCTIONS()->cs_special_chars($this->get_field_id('title')),
                    'cust_name' => CS_FUNCTIONS()->cs_special_chars($this->get_field_name('title')),
                    'return' => true,
                    'required' => false
                ),
            );
            echo $cs_html_fields->cs_text_field($cs_opt_array);




            $cs_opt_array = array(
                'name' => __('Facebook Url', 'jobhunt'),
                'desc' => '',
                'hint_text' => '',
                'echo' => false,
                'field_params' => array(
                    'std' => $fb_url,
                    'id' => '',
                    'classes' => '',
                    'cust_id' => CS_FUNCTIONS()->cs_special_chars($this->get_field_id('fb_url')),
                    'cust_name' => CS_FUNCTIONS()->cs_special_chars($this->get_field_name('fb_url')),
                    'return' => true,
                    'required' => false
                ),
            );
            echo $cs_html_fields->cs_text_field($cs_opt_array);

            $cs_opt_array = array(
                'name' => __('Twitter Url', 'jobhunt'),
                'desc' => '',
                'hint_text' => '',
                'echo' => false,
                'field_params' => array(
                    'std' => $tw_url,
                    'id' => '',
                    'classes' => '',
                    'cust_id' => CS_FUNCTIONS()->cs_special_chars($this->get_field_id('tw_url')),
                    'cust_name' => CS_FUNCTIONS()->cs_special_chars($this->get_field_name('tw_url')),
                    'return' => true,
                    'required' => false
                ),
            );
            echo $cs_html_fields->cs_text_field($cs_opt_array);


            $cs_opt_array = array(
                'name' => __('Linkedin Url', 'jobhunt'),
                'desc' => '',
                'hint_text' => '',
                'echo' => false,
                'field_params' => array(
                    'std' => $lk_url,
                    'id' => '',
                    'classes' => '',
                    'cust_id' => CS_FUNCTIONS()->cs_special_chars($this->get_field_id('lk_url')),
                    'cust_name' => CS_FUNCTIONS()->cs_special_chars($this->get_field_name('lk_url')),
                    'return' => true,
                    'required' => false
                ),
            );
            echo $cs_html_fields->cs_text_field($cs_opt_array);

            $cs_opt_array = array(
                'name' => __('Google Url', 'jobhunt'),
                'desc' => '',
                'hint_text' => '',
                'echo' => false,
                'field_params' => array(
                    'std' => $gl_url,
                    'id' => '',
                    'classes' => '',
                    'cust_id' => CS_FUNCTIONS()->cs_special_chars($this->get_field_id('gl_url')),
                    'cust_name' => CS_FUNCTIONS()->cs_special_chars($this->get_field_name('gl_url')),
                    'return' => true,
                    'required' => false
                ),
            );
            echo $cs_html_fields->cs_text_field($cs_opt_array);

            $cs_opt_array = array(
                'name' => __('Rss Url', 'jobhunt'),
                'desc' => '',
                'hint_text' => '',
                'echo' => false,
                'field_params' => array(
                    'std' => $ig_url,
                    'id' => '',
                    'classes' => '',
                    'cust_id' => CS_FUNCTIONS()->cs_special_chars($this->get_field_id('ig_url')),
                    'cust_name' => CS_FUNCTIONS()->cs_special_chars($this->get_field_name('ig_url')),
                    'return' => true,
                    'required' => false
                ),
            );
            echo $cs_html_fields->cs_text_field($cs_opt_array);


            $cs_opt_array = array(
                'name' => __('Youtube Url', 'jobhunt'),
                'desc' => '',
                'hint_text' => '',
                'echo' => false,
                'field_params' => array(
                    'std' => $yt_url,
                    'id' => '',
                    'classes' => '',
                    'cust_id' => CS_FUNCTIONS()->cs_special_chars($this->get_field_id('yt_url')),
                    'cust_name' => CS_FUNCTIONS()->cs_special_chars($this->get_field_name('yt_url')),
                    'return' => true,
                    'required' => false
                ),
            );
            echo $cs_html_fields->cs_text_field($cs_opt_array);
        }
         // Start function to update social link data
        function update($new_instance, $old_instance) {
            $instance = $old_instance;
            $instance['title'] = $new_instance['title'];
            $instance['telephone'] = $new_instance['telephone'];
            $instance['email'] = $new_instance['email'];
            $instance['fb_url'] = $new_instance['fb_url'];
            $instance['tw_url'] = $new_instance['tw_url'];
            $instance['lk_url'] = $new_instance['lk_url'];
            $instance['gl_url'] = $new_instance['gl_url'];
            $instance['ig_url'] = $new_instance['ig_url'];
            $instance['yt_url'] = $new_instance['yt_url'];
            $instance['image_url'] = $new_instance['image_url'];

            return $instance;
        }

        /**
         * Start Function how to Display Jobs Counter widget
         *        
         */
        function widget($args, $instance) {
            global $cs_plugin_options;
            extract($args, EXTR_SKIP);
            $title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
            $title = htmlspecialchars_decode(stripslashes($title));
            $image_url = empty($instance['image_url']) ? '' : esc_url($instance['image_url']);
            $telephone = empty($instance['telephone']) ? '' : esc_attr($instance['telephone']);
            $email = empty($instance['email']) ? '' : esc_attr($instance['email']);
            $fb_url = empty($instance['fb_url']) ? '' : esc_url($instance['fb_url']);
            $tw_url = empty($instance['tw_url']) ? '' : esc_url($instance['tw_url']);
            $lk_url = empty($instance['lk_url']) ? '' : esc_url($instance['lk_url']);
            $gl_url = empty($instance['gl_url']) ? '' : esc_url($instance['gl_url']);
            $ig_url = empty($instance['ig_url']) ? '' : esc_url($instance['ig_url']);
            $yt_url = empty($instance['yt_url']) ? '' : esc_url($instance['yt_url']);
            ?><div class="widget widget-socialmedia">
                <div class="widget-title">
                    <h5><?php echo esc_html($title); ?></h5>
                </div>
                <?php if( $ig_url != '' || $fb_url != '' || $tw_url != '' || $lk_url != '' || $gl_url != '' || $yt_url != '' ) { ?>
                <ul>
                	<?php if( $ig_url != '' ) { ?>
                    <li><a href="<?php echo esc_html($ig_url); ?>" data-original-title="rss"><i class=" icon-rss2"></i></a><span><?php _e('Simply Hired Blog', 'jobhunt'); ?></span></li>
                    <?php } if( $fb_url != '' ) { ?>
                    <li><a href="<?php echo esc_html($fb_url); ?>" data-original-title="facebook"><i class=" icon-facebook7"></i></a><span><?php _e('Facebook', 'jobhunt'); ?></span></li>
                    <?php } if( $tw_url != '' ) { ?>
                    <li><a href="<?php echo esc_html($tw_url); ?>" data-original-title="twitter"><i class="  icon-twitter6"></i></a><span><?php _e('Twitter', 'jobhunt'); ?></span></li>
                    <?php } if( $lk_url != '' ) { ?>
                    <li><a href="<?php echo esc_html($lk_url); ?>" data-original-title="linkedin"><i class=" icon-linkedin2"></i></a><span><?php _e('Linkedin', 'jobhunt'); ?></span></li>
                    <?php } if( $gl_url != '' ) { ?>
                    <li><a href="<?php echo esc_html($gl_url); ?>" data-original-title="google"><i class=" icon-googleplus7"></i></a><span><?php _e('Google+', 'jobhunt'); ?></span></li>
                    <?php } if( $yt_url != '' ) { ?>
                    <li><a href="<?php echo esc_html($yt_url); ?>" data-original-title="youtube"><i class=" icon-youtube"></i></a><span><?php _e('YouTube', 'jobhunt'); ?></span></li>
                	<?php } ?>
                </ul>
                <?php } ?>
            </div>
            <?php
        }

    }

}
add_action('widgets_init', create_function('', 'return register_widget("socialcontact");'));