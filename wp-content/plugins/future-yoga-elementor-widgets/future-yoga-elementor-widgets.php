<?php

/**
 * Plugin Name: FutureYoga Elementor Extension
 * Description: Custom Elementor extension.
 * Plugin URI:  https://manuelpetteno.com
 * Version:     1.0.0
 * Author:      Manuel Pettenò
 * Author URI:  https://manuelpetteno.com
 * Text Domain: elementor-test-extension
 */

namespace FutureYoga_Elementor;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Main Elementor Test Extension Class
 *
 * The main class that initiates and runs the plugin.
 *
 * @since 1.0.0
 */
final class FutureYoga_Elementor_Extension
{

    /**
     * Plugin Version
     *
     * @since 1.0.0
     *
     * @var string The plugin version.
     */
    const VERSION = '1.0.0';

    /**
     * Minimum Elementor Version
     *
     * @since 1.0.0
     *
     * @var string Minimum Elementor version required to run the plugin.
     */
    const MINIMUM_ELEMENTOR_VERSION = '2.0.0';

    /**
     * Minimum PHP Version
     *
     * @since 1.0.0
     *
     * @var string Minimum PHP version required to run the plugin.
     */
    const MINIMUM_PHP_VERSION = '7.0';

    /**
     * Instance
     *
     * @since 1.0.0
     *
     * @access private
     * @static
     *
     * @var FutureYoga_Elementor_Extension The single instance of the class.
     */
    private static $_instance = null;


    public $plugin_name = 'future-yoga-elementor-extension';
    /**
     * Instance
     *
     * Ensures only one instance of the class is loaded or can be loaded.
     *
     * @since 1.0.0
     *
     * @access public
     * @static
     *
     * @return FutureYoga_Elementor_Extension An instance of the class.
     */
    public static function instance()
    {

        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Constructor
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function __construct()
    {

        add_action('init', [$this, 'i18n']);
        add_action('plugins_loaded', [$this, 'init']);
        $this->add_styles();
        $this->add_scripts();

        function shortcode_wrap($text) {
            return '<h2 class="elementor-heading-title elementor-size-default elementor-inline-editing" data-elementor-setting-key="title">' . $text . '</h2>';
        }

        add_action('wp',  function () {
            add_filter('body_class', function ($classes) {

                $classes[] = 'fy-elementor-plugin';
                $classes[] = 'fy-has-featured-image';
                return $classes;
            });

            add_filter('ocean_page_header_overlay', function ($overlay_markup) {
                global $post_override;
                global $wp_query;

                if(is_singular()) {
                    $featured_image_url = get_the_post_thumbnail_url($post_override->ID);
                }

                if(is_archive()) {
                    $tax = $wp_query->get_queried_object();
                    $featured_image_id = get_field('immagine_di_categoria', $tax->taxonomy . '_' . $tax->term_id);
                    $featured_image_url = wp_get_attachment_url($featured_image_id);
                }


                if(empty($featured_image_url)) {
                    $featured_image_url = wp_get_attachment_url(2271);
                }

                if (!empty($featured_image_url)) {
                    $new_markup = '<span style="background-image: url(\'' . $featured_image_url . '\');" ';
                    $overlay_markup = str_replace('<span ', $new_markup, $overlay_markup);
                }
                return $overlay_markup;
            });

            add_filter( 'ocean_after_page_header_inner', function() {
                global $post_override;
                if(!is_archive()) :
                    $categories = get_the_terms($post_override->ID, 'tribe_events_cat');
                    if(!empty($categories)) :
                    ?>
                    <script defer="defer">
                        (function($){
                            var categories = [
                            <?php foreach($categories as $cat) : ?>
                                {
                                    'id': '<?php echo $cat->term_id; ?>',
                                    'name': '<?php echo $cat->name; ?>',
                                    'url': '<?php echo get_term_link($cat->term_id, 'tribe_events_cat'); ?>'
                                },
                            <?php endforeach; ?>
                            ]

                            var $categories = categories.map(function(cat) {
                                return $('<li><a href="' + cat.url + '">' + cat.name + '</a></li>');
                            });
                            var $ul = $('<ul class="header__categories" style="opacity: 0;"></ul>');
                            $ul.append($categories);
                            $('.page-header-inner').prepend($ul).find('.header__categories').css({'opacity': '1'});
                        })(jQuery);
                    </script>
                    <?php
                    endif;
                endif;
            });

            //add_action('wpforms_frontend_output_form_before', function($form_data, $form) {
            //    if(is_single('tribe_events')) {
            //        foreach($form_data['fields'] as $field) {
            //            if($field['placeholder'] === 'corso') {
            //                $event = tribe_get_event();
            //                $title = get_the_title();
            //                $field['default_value'] = `{$title} | {$event->start_date} - {$event->end_date} | {$event->venues}`;
            //            }
            //        }
            //    }
//
            //    return $form_data;
            //}, 10, 2);
        });

        // Replaces the excerpt "Read More" text by a link
        add_filter('excerpt_more', function ($more) {
            global $post;
        return '<a class="moretag" href="'. get_permalink($post->ID) . '">...</a>';
        });
    }

    /**
     * Load Textdomain
     *
     * Load plugin localization files.
     *
     * Fired by `init` action hook.
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function i18n()
    {

        load_plugin_textdomain('elementor-test-extension');
    }

    /**
     * Initialize the plugin
     *
     * Load the plugin only after Elementor (and other plugins) are loaded.
     * Checks for basic plugin requirements, if one check fail don't continue,
     * if all check have passed load the files required to run the plugin.
     *
     * Fired by `plugins_loaded` action hook.
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function init()
    {

        // Check if Elementor installed and activated
        if (!did_action('elementor/loaded')) {
            add_action('admin_notices', [$this, 'admin_notice_missing_main_plugin']);
            return;
        }

        // Check for required Elementor version
        if (!version_compare(ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=')) {
            add_action('admin_notices', [$this, 'admin_notice_minimum_elementor_version']);
            return;
        }

        // Check for required PHP version
        if (version_compare(PHP_VERSION, self::MINIMUM_PHP_VERSION, '<')) {
            add_action('admin_notices', [$this, 'admin_notice_minimum_php_version']);
            return;
        }

        //Register Widget Category
        add_action('elementor/elements/categories_registered', [$this, 'add_elementor_widget_categories']);


        // Add Plugin actions
        add_action('elementor/widgets/widgets_registered', [$this, 'init_widgets'], 20);
        //add_action('elementor/controls/controls_registered', [$this, 'init_controls']);
    }

    /**
     * Admin notice
     *
     * Warning when the site doesn't have Elementor installed or activated.
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function admin_notice_missing_main_plugin()
    {

        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }

        $message = sprintf(
            /* translators: 1: Plugin name 2: Elementor */
            esc_html__('"%1$s" requires "%2$s" to be installed and activated.', 'elementor-test-extension'),
            '<strong>' . esc_html__('Elementor Test Extension', 'elementor-test-extension') . '</strong>',
            '<strong>' . esc_html__('Elementor', 'elementor-test-extension') . '</strong>'
        );

        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }

    /**
     * Admin notice
     *
     * Warning when the site doesn't have a minimum required Elementor version.
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function admin_notice_minimum_elementor_version()
    {

        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }

        $message = sprintf(
            /* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
            esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'elementor-test-extension'),
            '<strong>' . esc_html__('Elementor Test Extension', 'elementor-test-extension') . '</strong>',
            '<strong>' . esc_html__('Elementor', 'elementor-test-extension') . '</strong>',
            self::MINIMUM_ELEMENTOR_VERSION
        );

        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }

    /**
     * Admin notice
     *
     * Warning when the site doesn't have a minimum required PHP version.
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function admin_notice_minimum_php_version()
    {

        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }

        $message = sprintf(
            /* translators: 1: Plugin name 2: PHP 3: Required PHP version */
            esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'elementor-test-extension'),
            '<strong>' . esc_html__('Elementor Test Extension', 'elementor-test-extension') . '</strong>',
            '<strong>' . esc_html__('PHP', 'elementor-test-extension') . '</strong>',
            self::MINIMUM_PHP_VERSION
        );

        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }

    /**
     * Create Category
     *
     * @param [type] $elements_manager
     * @return void
     */
    function add_elementor_widget_categories($elements_manager)
    {

        $elements_manager->add_category(
            'future-yoga',
            [
                'title' => __('Future Yoga Elemetor Addons', 'fy-elementor-extension'),
                'icon' => 'fa fa-plug',
            ]
        );
    }


    /**
     * Init Widgets
     *
     * Include widgets files and register them
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function init_widgets($wm)
    {

        // Include Widget files
        require_once __DIR__ . '/widgets/image-box.php';
        require_once __DIR__ . '/widgets/test-widget.php';
        require_once __DIR__ . '/widgets/buttons.php';
        require_once __DIR__ . '/widgets/member.php';
        require_once __DIR__ . '/widgets/activities.php';
        require_once __DIR__ . '/widgets/teacher-contacts.php';
        require_once __DIR__ . '/widgets/events-list.php';
        require_once __DIR__ . '/widgets/venue-excerpt.php';

        // Register widget
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\Widget_FutureYoga_Image_Box());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\Elementor_oEmbed_Widget());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\FutureYoga_Buttons());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\FutureYoga_Member());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\FutureYoga_Activities());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\FutureYoga_Teacher_Contacts());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\FutureYoga_Events());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\FutureYoga_Events_Locations());
    }

    /**
     * Init Controls
     *
     * Include controls files and register them
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function init_controls()
    {

        // Include Control files
        require_once __DIR__ . '/controls/test-control.php';

        // Register control
        \Elementor\Plugin::$instance->controls_manager->register_control('control-type-', new \Test_Control());
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Cbg_B2b_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Cbg_B2b_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'dist/main.css', array(), $this->VERSION, 'all');
    }


    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Cbg_B2b_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Cbg_B2b_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'dist/main.js', array('jquery'), $this->VERSION, false);
    }

    public function add_styles()
    {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('elementor/frontend/before_enqueue_scripts', array($this, 'enqueue_styles'));
    }

    public function add_scripts()
    {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }
}

FutureYoga_Elementor_Extension::instance();
