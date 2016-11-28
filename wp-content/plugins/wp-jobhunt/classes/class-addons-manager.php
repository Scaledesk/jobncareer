<?php
/**
 * List Installed and Available JobHunt Addons
 *
 * @internal
 */
if ( ! defined( 'ABSPATH' ) ) {
    die( 'Direct access not allowed.' );
}

final class JobHunt_Addons_Manager {

    private $theme_name = 'jobcareer';
    //private $remote_webservice_uri = 'http://serverwp/dev/chimp_api/addons.php?action=get_available_addons&theme_name=jobcareer';
    private $remote_webservice_uri = 'http://chimpgroup.com/wp-demo/webservice/addons.php?action=get_available_addons&theme_name=jobcareer';
    private $default_thumbnail = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVQIW2PUsHf9DwAC8AGtfm5YCAAAAABJRU5ErkJgggAA';

    public function __construct() {
        if ( ! is_admin() ) {
            return;
        }

        add_action( 'admin_menu', array( $this, 'action_admin_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'action_enqueue_scripts' ) );
    }

    public function get_page_slug() {
        return 'jobhunt-addons';
    }

    private function get_uri( $append = '' ) {
        return ( new wp_jobhunt() )->plugin_url . $append;
    }

    /**
     * Addons data including available addons and other data sent from server.
     */
    private function get_addons_data() {
        // delete_option('jobhunt_available_addons');
        $jobhunt_available_addons = get_option( 'jobhunt_available_addons' );
        $addons_data = array(
            'addons' => array(),
            'other_links' => array(),
        );
        if ( $jobhunt_available_addons ) {
            // Check if it is time to fetch addons list or use existing again.
            if ( $jobhunt_available_addons['next_fetch_time'] > time() ) {
                $addons_data = $jobhunt_available_addons['addons_data'];
            }
        }

        if ( empty( $addons_data['addons'] ) ) {

            $post_data = array(
                'action' => 'get_available_addons',
                'theme_name' => $this->theme_name,
            );

            $response = wp_remote_post( $this->remote_webservice_uri, array( 'body' => $post_data ) );
            //var_dump( $response );
            if ( is_wp_error( $response ) ) {
                $success = false;
            } else {
                $body = json_decode( $response['body'], true );

                if ( 'true' == $body['success'] ) {
                    $addons = json_decode( $body['addons'], true );

                    $addons_data['addons'] = $addons;
                    $addons_data['other_links'] = isset( $body['other_links'] ) ? $body['other_links'] : '';

                    // Save data for next time use in WP Options.
                    $data_for_option = [
                        'next_fetch_time' => time() + ( isset( $body['fetch_addons_after_seconds'] ) ? intval( $body['fetch_addons_after_seconds'] ) : 0 ),
                        'addons_data' => $addons_data,
                    ];

                    add_option( 'jobhunt_available_addons', $data_for_option );
                }
            }
        }

        /**
         * Set status of the installed plugins.
         */
        $all_plugins = get_plugins();
        $keys = array_keys( $all_plugins );

        foreach ( $addons_data['addons'] as $key => $plugin_data ) {

            if ( isset( $plugin_data['main_file_path'] ) && in_array( $plugin_data['main_file_path'], $keys ) ) {
                $plugin_data['active'] = is_plugin_active( $plugin_data['main_file_path'] );
                $plugin_data['installed'] = true;
            } else {
                $plugin_data['active'] = false;
                $plugin_data['installed'] = false;
            }
            $addons_data['addons'][$key] = $plugin_data;
        }

        // JSON Decode addons and other links list.
        if ( is_string( $addons_data['addons'] ) ) {
            $addons_data['addons'] = json_decode( $addons_data['addons'], true );
        }
        if ( is_string( $addons_data['other_links'] ) ) {
            $addons_data['other_links'] = json_decode( $addons_data['other_links'], true );
        }

        return $addons_data;
    }

    /**
     * @internal
     */
    public function action_admin_menu() {
        add_submenu_page(
                'edit.php?post_type=jobs', __( 'Addons Manager', 'jobhunt' ), __( 'Addons Manager', 'jobhunt' ), 'manage_options', $this->get_page_slug(), array( $this, 'display_list_page' )
        );
    }

    /**
     * Prepare addons list for view.
     */
    public function display_list_page() {
        $addons_data = $this->get_addons_data();
        $addons = $addons_data['addons'];
        $other_links = $addons_data['other_links'];
        $categories = array_unique( array_column( $addons, 'category' ) );
        ?>
        <div class="jc-addons-holder">
            <div class="jc-addons-title">
                <h1><?php _e( 'Addons Manager', 'jobhunt' ); ?></h1>
                <?php foreach ( $other_links as $key => $link_data ): ?>
                    <a href="<?php echo $link_data['link']; ?>"><?php echo $link_data['text']; ?></a>
                <?php endforeach; ?>
            </div>
            <div class="jc-addons-nav">
                <ul>
                    <li data-filter="*"><a href="#">All</a></li>
                    <li data-filter=".active"><a href="#">Active</a></li>
                    <li data-filter=".inactive"><a href="#">Inactive</a></li>
                    <?php foreach ( $categories as $key => $val ): ?>
                        <li data-filter="<?php echo esc_attr( '.' . $val ); ?>"><a href="#"><?php echo esc_html( ucfirst( $val ) ); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="jc-addons-products">
                <ul>
                    <?php
                    $display_default_value = '';
                    $default_thumbnail = $this->default_thumbnail;
                    $something_displayed = false;

                    foreach ( $addons as $name => &$data ) {
                        if ( $data['display'] !== true ) {
                            continue;
                        }

                        $this->render_addons_list_item( $data );

                        $something_displayed = true;
                    }
                    unset( $data );
                    ?>
                </ul>
            </div>
        </div>
        <script type="text/javascript">
            (function ($) {
                $(function () {
                    // Init Isotope.
                    var $grid = $('.jc-addons-products ul').isotope();

                    // Filter items on button click.
                    $('.jc-addons-nav ul').on('click', 'li', function () {
                        var filterValue = $(this).attr('data-filter');
                        $grid.isotope({filter: filterValue});
                    });
                });
            })(jQuery);
        </script>
        <?php
    }

    function render_addons_list_item( $data ) {

        extract( $data );

        $wrapper_class = 'col-xs-12 col-lg-6 jobhunt-addons-list-item';

        $wrapper_class .= ' ' . $category;

        if ( $installed ) {
            $wrapper_class .= ' installed';

            if ( $active ) {
                $wrapper_class .= ' active';
            } else {
                $wrapper_class .= ' inactive';
            }
        }
        ?>
        <li class="<?php echo esc_attr( $wrapper_class ) ?>" id="jobhunt-ext-<?php echo esc_attr( $slug ) ?>">
            <a href="<?php echo esc_attr( $url ); ?>">
                <figure>
                    <img src="<?php echo esc_attr( $thumbnail ); ?>" class="jobhunt-addons-list-item-thumbnail">
                </figure>
                <h2><?php echo esc_html( $name ); ?></h2>
                <div class="text">
                    <p><?php echo esc_html( $description ); ?></p>
                </div>
            </a>
        </li>
        <?php
    }

    public function is_addons_page() {
        $current_screen = get_current_screen();

        if ( empty( $current_screen ) ) {
            return false;
        }

        return (
                isset( $current_screen['base'] ) && strpos( $current_screen->base, $this->get_page_slug() ) !== false &&
                isset( $current_screen['id'] ) && strpos( $current_screen->id, $this->get_page_slug() ) !== false &&
                ! isset( $_GET['sub-page'] )
                );
    }

    /**
     * @internal
     */
    public function action_enqueue_scripts() {
        wp_enqueue_style(
                'jobhunt-addons-manager-style', $this->get_uri( '/assets/css/addons-manager-style.css' ), array(), '1.0'
        );

        wp_enqueue_script(
                'cs_isotope_min_js', $this->get_uri( '/assets/scripts/isotope.min.js' ), array(), '1.0', true
        );
    }

}

if ( ! function_exists( 'array_column' ) ) {

    function array_column( array $input, $columnKey, $indexKey = null ) {
        $array = array();
        foreach ( $input as $value ) {
            if ( ! isset( $value[$columnKey] ) ) {
                trigger_error( "Key \"$columnKey\" does not exist in array" );
                return false;
            }
            if ( is_null( $indexKey ) ) {
                $array[] = $value[$columnKey];
            } else {
                if ( ! isset( $value[$indexKey] ) ) {
                    trigger_error( "Key \"$indexKey\" does not exist in array" );
                    return false;
                }
                if ( ! is_scalar( $value[$indexKey] ) ) {
                    trigger_error( "Key \"$indexKey\" does not contain scalar value" );
                    return false;
                }
                $array[$value[$indexKey]] = $value[$columnKey];
            }
        }
        return $array;
    }

}

new JobHunt_Addons_Manager();
