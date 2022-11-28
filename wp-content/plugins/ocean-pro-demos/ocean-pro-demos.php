<?php
/**
 * Plugin Name:			Ocean Pro Demos
 * Description:			A premium plugin with powerful and complete options for web designers: import OceanWP Full website templates with all pages and styling; Elementor Sections Library templates; and import images and icons from Freepik.
 * Version:				1.4.1
 * Update URI: https://api.freemius.com
 * Author:				OceanWP
 * Author URI:			https://oceanwp.org/
 * Requires at least:	5.6
 * Tested up to:		6.1
 *
 * Text Domain: ocean-pro-demos
 * Domain Path: /languages
 *
 * @package Ocean_Pro_Demos
 * @category Core
 * @author OceanWP
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns the main instance of Ocean_Pro_Demos to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Ocean_Pro_Demos
 */
function Ocean_Pro_Demos() {
	return Ocean_Pro_Demos::instance();
} // End Ocean_Pro_Demos()

Ocean_Pro_Demos();

/**
 * Main Ocean_Pro_Demos Class
 *
 * @class Ocean_Pro_Demos
 * @version	1.0.0
 * @since 1.0.0
 * @package	Ocean_Pro_Demos
 */
final class Ocean_Pro_Demos {
	/**
	 * Ocean_Pro_Demos The single instance of Ocean_Pro_Demos.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $token;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $version;

	// Admin - Start
	/**
	 * The admin object.
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $admin;

	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct( $widget_areas = array() ) {
		$this->token 			= 'demos';
		$this->plugin_url 		= plugin_dir_url( __FILE__ );
		$this->plugin_path 		= plugin_dir_path( __FILE__ );
		$this->version 			= '1.4.1';

		define( 'OPD_PATH', $this->plugin_path );
		define( 'OPD_URL', $this->plugin_url );
		define( 'OPD_VERSION', $this->version );

		register_activation_hook( __FILE__, array( $this, 'install' ) );

		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		include_once plugin_dir_path(__FILE__) . 'api-images.php';

	}
	
	public function init() {
        // Add pro demos in the demos page
        add_filter( 'owp_demos_data', array( $this, 'get_pro_demos' ) );
		
		$ocean_elementor_library_is_disabled = get_option('disable_ocean_elementor_library', 'no') == 'yes';

		
		// Include Elementor Library.	
		if ( did_action( 'elementor/loaded' ) && is_user_logged_in() && ! $ocean_elementor_library_is_disabled ) {
			include_once( OPD_PATH . 'elementor-library/classes/class-lib-mngr.php' );
			include_once( OPD_PATH . 'elementor-library/classes/class-lib-src.php' );
		}
    }

	public function admin_notice_missing_main_plugin() {
		return sprintf(
			wp_kses(
				'<div class="notice notice-warning is-dismissible"><p><strong>"%1$s"</strong> requires <strong>"%2$s"</strong> to be installed and activated.</p></div>',
				array(
					'div' => array(
						'class'  => array(),
						'p'      => array(),
						'strong' => array(),
					),
				)
			),
			'Ocean Elementor Library',
			'Elementor'
		);
	}

	/**
	 * Main Ocean_Pro_Demos Instance
	 *
	 * Ensures only one instance of Ocean_Pro_Demos is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Ocean_Pro_Demos()
	 * @return Ocean_Pro_Demos Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	} // End instance()

	/**
	 * Load the localisation file.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'demos', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	}

	/**
	 * Installation.
	 * Runs on activation. Logs the version number and assigns a notice message to a WordPress option.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function install() {
		$this->_log_version_number();
	}

	/**
	 * Log the plugin version number.
	 * @access  private
	 * @since   1.0.0
	 * @return  void
	 */
	private function _log_version_number() {
		// Log the version number.
		update_option( $this->token . '-version', $this->version );
	}

	public static function ocean_elementor_library_html_path( $file ) {
		$file = OPD_PATH . 'elementor-library/ocean_elementor_library_panel.php';
		return $file;
	}

	public static function add_theme_panel_section( $sections ) {
		$sections['ocean-elementor-library'] = array(
			'title' => __( 'Elementor Library', 'oceanwp' ),
			'href'  => 'ocean-elementor-library',
			'order' => 91,
		);
		return $sections;
	}
	/**
	 * Get pro demos.
	 *
	 * @since   1.0.0
	 */
	public static function get_pro_demos( $data ) {

		// Demos url
		$url = 'https://demos.oceanwp.org/';

		$data['elementor'] = array(
			'assurance' => array(
				'categories'        => array( 'Business' ),
				'xml_file'     		=> $url . 'assurance/sample-data.xml',
				'theme_settings' 	=> $url . 'assurance/oceanwp-export.dat',
				'form_file'  		=> $url . 'assurance/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'News',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'justice' => array(
				'categories'        => array( 'Business' ),
				'xml_file'     		=> $url . 'justice/sample-data.xml',
				'theme_settings' 	=> $url . 'justice/oceanwp-export.dat',
				'form_file'  		=> $url . 'justice/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'News',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'startup' => array(
				'categories'        => array( 'Business' ),
				'xml_file'     		=> $url . 'startup/sample-data.xml',
				'theme_settings' 	=> $url . 'startup/oceanwp-export.dat',
				'form_file'  		=> $url . 'startup/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'News',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'coaching' => array(
				'categories'        => array( 'Business', 'One Page' ),
				'xml_file'     		=> $url . 'coaching/sample-data.xml',
				'theme_settings' 	=> $url . 'coaching/oceanwp-export.dat',
				'form_file'  		=> $url . 'coaching/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'News',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'aesthetic' => array(
				'categories'        => array( 'Business', 'One Page' ),
				'xml_file'     		=> $url . 'aesthetic/sample-data.xml',
				'theme_settings' 	=> $url . 'aesthetic/oceanwp-export.dat',
				'form_file'  		=> $url . 'aesthetic/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'News',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'cook' => array(
				'categories'        => array( 'Business', 'One Page' ),
				'xml_file'     		=> $url . 'cook/sample-data.xml',
				'theme_settings' 	=> $url . 'cook/oceanwp-export.dat',
				'form_file'  		=> $url . 'cook/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'News',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'wedevent' => array(
				'categories'        => array( 'Business' ),
				'xml_file'     		=> $url . 'wedevent/sample-data.xml',
				'theme_settings' 	=> $url . 'wedevent/oceanwp-export.dat',
				'form_file'  		=> $url . 'wedevent/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'News',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'earphone' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'earphone/sample-data.xml',
				'theme_settings' 	=> $url . 'earphone/oceanwp-export.dat',
				'form_file'  		=> $url . 'earphone/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'suitcase' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'suitcase/sample-data.xml',
				'theme_settings' 	=> $url . 'suitcase/oceanwp-export.dat',
				'form_file'  		=> $url . 'suitcase/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'cap' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'cap/sample-data.xml',
				'theme_settings' 	=> $url . 'cap/oceanwp-export.dat',
				'form_file'  		=> $url . 'cap/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'soccer' => array(
				'categories'        => array( 'Business' ),
				'xml_file'     		=> $url . 'soccer/sample-data.xml',
				'theme_settings' 	=> $url . 'soccer/oceanwp-export.dat',
				'form_file'  		=> $url . 'soccer/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'News',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'veterinary' => array(
				'categories'        => array( 'Business', 'One Page' ),
				'xml_file'     		=> $url . 'veterinary/sample-data.xml',
				'theme_settings' 	=> $url . 'veterinary/oceanwp-export.dat',
				'form_file'  		=> $url . 'veterinary/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'News',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'ponyclub' => array(
				'categories'        => array( 'Business' ),
				'xml_file'     		=> $url . 'ponyclub/sample-data.xml',
				'theme_settings' 	=> $url . 'ponyclub/oceanwp-export.dat',
				'form_file'  		=> $url . 'ponyclub/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'News',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'doctor' => array(
				'categories'        => array( 'Business'  ),
				'xml_file'     		=> $url . 'doctor/sample-data.xml',
				'theme_settings' 	=> $url . 'doctor/oceanwp-export.dat',
				'form_file'  		=> $url . 'doctor/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'News',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'gardener' => array(
				'categories'        => array( 'Business' ),
				'xml_file'     		=> $url . 'gardener/sample-data.xml',
				'theme_settings' 	=> $url . 'gardener/oceanwp-export.dat',
				'form_file'  		=> $url . 'gardener/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'News',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'psychologist' => array(
				'categories'        => array( 'Business' ),
				'xml_file'     		=> $url . 'psychologist/sample-data.xml',
				'theme_settings' 	=> $url . 'psychologist/oceanwp-export.dat',
				'form_file'  		=> $url . 'psychologist/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'News',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'doctor' => array(
				'categories'        => array( 'Business' ),
				'xml_file'     		=> $url . 'doctor/sample-data.xml',
				'theme_settings' 	=> $url . 'doctor/oceanwp-export.dat',
				'form_file'  		=> $url . 'doctor/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'News',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'masseuse' => array(
				'categories'        => array( 'Business', 'One Page' ),
				'xml_file'     		=> $url . 'masseuse/sample-data.xml',
				'theme_settings' 	=> $url . 'masseuse/oceanwp-export.dat',
				'form_file'  		=> $url . 'masseuse/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'News',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'out' => array(
				'categories'        => array( 'Business', 'One Page' ),
				'xml_file'     		=> $url . 'out/sample-data.xml',
				'theme_settings' 	=> $url . 'out/oceanwp-export.dat',
				'form_file'  		=> $url . 'out/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'News',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'pumps' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'pumps/sample-data.xml',
				'theme_settings' 	=> $url . 'pumps/oceanwp-export.dat',
				'form_file'  		=> $url . 'pumps/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'clean' => array(
				'categories'        => array( 'Business', 'One Page' ),
				'xml_file'     		=> $url . 'clean/sample-data.xml',
				'theme_settings' 	=> $url . 'clean/oceanwp-export.dat',
				'form_file'  		=> $url . 'clean/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'News',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'nightclub' => array(
				'categories'        => array( 'Business', 'One Page' ),
				'xml_file'     		=> $url . 'nightclub/sample-data.xml',
				'theme_settings' 	=> $url . 'nightclub/oceanwp-export.dat',
				'form_file'  		=> $url . 'nightclub/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'News',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'university' => array(
				'categories'        => array( 'Business', 'One Page' ),
				'xml_file'     		=> $url . 'university/sample-data.xml',
				'theme_settings' 	=> $url . 'university/oceanwp-export.dat',
				'form_file'  		=> $url . 'university/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'News',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'baker' => array(
				'categories'        => array( 'Business' ),
				'xml_file'     		=> $url . 'baker/sample-data.xml',
				'theme_settings' 	=> $url . 'baker/oceanwp-export.dat',
				'form_file'  		=> $url . 'baker/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'News',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'bistro' => array(
				'categories'        => array( 'Business', 'One Page' ),
				'xml_file'     		=> $url . 'bistro/sample-data.xml',
				'theme_settings' 	=> $url . 'bistro/oceanwp-export.dat',
				'form_file'  		=> $url . 'bistro/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'News',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'clubfitness' => array(
				'categories'        => array( 'Business', 'One Page' ),
				'xml_file'     		=> $url . 'clubfitness/sample-data.xml',
				'theme_settings' 	=> $url . 'clubfitness/oceanwp-export.dat',
				'form_file'  		=> $url . 'clubfitness/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'News',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'pool' => array(
				'categories'        => array( 'Business', 'One Page' ),
				'xml_file'     		=> $url . 'pool/sample-data.xml',
				'theme_settings' 	=> $url . 'pool/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'pool/widgets.wie',
				'form_file'  		=> $url . 'pool/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'building' => array(
				'categories'        => array( 'Business' ),
				'xml_file'     		=> $url . 'building/sample-data.xml',
				'theme_settings' 	=> $url . 'building/oceanwp-export.dat',
				'form_file'  		=> $url . 'building/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'News',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'rings' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'rings/sample-data.xml',
				'theme_settings' 	=> $url . 'rings/oceanwp-export.dat',
				'form_file'  		=> $url . 'rings/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'towel' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'towel/sample-data.xml',
				'theme_settings' 	=> $url . 'towel/oceanwp-export.dat',
				'form_file'  		=> $url . 'towel/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'backpack' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'backpack/sample-data.xml',
				'theme_settings' 	=> $url . 'backpack/oceanwp-export.dat',
				'form_file'  		=> $url . 'backpack/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'diffusers' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'diffusers/sample-data.xml',
				'theme_settings' 	=> $url . 'diffusers/oceanwp-export.dat',
				'form_file'  		=> $url . 'diffusers/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'teddy' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'teddy/sample-data.xml',
				'theme_settings' 	=> $url . 'teddy/oceanwp-export.dat',
				'form_file'  		=> $url . 'teddy/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'croquette' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'croquette/sample-data.xml',
				'theme_settings' 	=> $url . 'croquette/oceanwp-export.dat',
				'form_file'  		=> $url . 'croquette/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'nail' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'nail/sample-data.xml',
				'theme_settings' 	=> $url . 'nail/oceanwp-export.dat',
				'form_file'  		=> $url . 'nail/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'statue' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'statue/sample-data.xml',
				'theme_settings' 	=> $url . 'statue/oceanwp-export.dat',
				'form_file'  		=> $url . 'statue/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'boat' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'boat/sample-data.xml',
				'theme_settings' 	=> $url . 'boat/oceanwp-export.dat',
				'form_file'  		=> $url . 'boat/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'fountain' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'fountain/sample-data.xml',
				'theme_settings' 	=> $url . 'fountain/oceanwp-export.dat',
				'form_file'  		=> $url . 'fountain/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'picture' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'picture/sample-data.xml',
				'theme_settings' 	=> $url . 'picture/oceanwp-export.dat',
				'form_file'  		=> $url . 'picture/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'phone' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'phone/sample-data.xml',
				'theme_settings' 	=> $url . 'phone/oceanwp-export.dat',
				'form_file'  		=> $url . 'phone/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'knife' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'knife/sample-data.xml',
				'theme_settings' 	=> $url . 'knife/oceanwp-export.dat',
				'form_file'  		=> $url . 'knife/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'scents' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'scents/sample-data.xml',
				'theme_settings' 	=> $url . 'scents/oceanwp-export.dat',
				'form_file'  		=> $url . 'scents/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'buoy' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'buoy/sample-data.xml',
				'theme_settings' 	=> $url . 'buoy/oceanwp-export.dat',
				'form_file'  		=> $url . 'buoy/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'spice' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'spice/sample-data.xml',
				'theme_settings' 	=> $url . 'spice/oceanwp-export.dat',
				'form_file'  		=> $url . 'spice/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'agenda' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'agenda/sample-data.xml',
				'theme_settings' 	=> $url . 'agenda/oceanwp-export.dat',
				'form_file'  		=> $url . 'agenda/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'egg' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'egg/sample-data.xml',
				'theme_settings' 	=> $url . 'egg/oceanwp-export.dat',
				'form_file'  		=> $url . 'egg/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'essential' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'essential/sample-data.xml',
				'theme_settings' 	=> $url . 'essential/oceanwp-export.dat',
				'form_file'  		=> $url . 'essential/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'basket' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'basket/sample-data.xml',
				'theme_settings' 	=> $url . 'basket/oceanwp-export.dat',
				'form_file'  		=> $url . 'basket/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'champagne' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'champagne/sample-data.xml',
				'theme_settings' 	=> $url . 'champagne/oceanwp-export.dat',
				'form_file'  		=> $url . 'champagne/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'bluetooth' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'bluetooth/sample-data.xml',
				'theme_settings' 	=> $url . 'bluetooth/oceanwp-export.dat',
				'form_file'  		=> $url . 'bluetooth/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'hairstyle' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'hairstyle/sample-data.xml',
				'theme_settings' 	=> $url . 'hairstyle/oceanwp-export.dat',
				'form_file'  		=> $url . 'hairstyle/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'tennis' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'tennis/sample-data.xml',
				'theme_settings' 	=> $url . 'tennis/oceanwp-export.dat',
				'form_file'  		=> $url . 'tennis/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'barbecue' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'barbecue/sample-data.xml',
				'theme_settings' 	=> $url . 'barbecue/oceanwp-export.dat',
				'form_file'  		=> $url . 'barbecue/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'shampoo' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'shampoo/sample-data.xml',
				'theme_settings' 	=> $url . 'shampoo/oceanwp-export.dat',
				'form_file'  		=> $url . 'shampoo/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'horse' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'horse/sample-data.xml',
				'theme_settings' 	=> $url . 'horse/oceanwp-export.dat',
				'form_file'  		=> $url . 'horse/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'carpet' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'carpet/sample-data.xml',
				'theme_settings' 	=> $url . 'carpet/oceanwp-export.dat',
				'form_file'  		=> $url . 'carpet/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'health' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'health/sample-data.xml',
				'theme_settings' 	=> $url . 'health/oceanwp-export.dat',
				'form_file'  		=> $url . 'health/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'pretty' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'pretty/sample-data.xml',
				'theme_settings' 	=> $url . 'pretty/oceanwp-export.dat',
				'form_file'  		=> $url . 'pretty/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'paddle' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'paddle/sample-data.xml',
				'theme_settings' 	=> $url . 'paddle/oceanwp-export.dat',
				'form_file'  		=> $url . 'paddle/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'parfum' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'parfum/sample-data.xml',
				'theme_settings' 	=> $url . 'parfum/oceanwp-export.dat',
				'form_file'  		=> $url . 'parfum/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'piano' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'piano/sample-data.xml',
				'theme_settings' 	=> $url . 'piano/oceanwp-export.dat',
				'form_file'  		=> $url . 'piano/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'eventcake' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'eventcake/sample-data.xml',
				'theme_settings' 	=> $url . 'eventcake/oceanwp-export.dat',
				'form_file'  		=> $url . 'eventcake/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'roses' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'roses/sample-data.xml',
				'theme_settings' 	=> $url . 'roses/oceanwp-export.dat',
				'form_file'  		=> $url . 'roses/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'caps' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'caps/sample-data.xml',
				'theme_settings' 	=> $url . 'caps/oceanwp-export.dat',
				'form_file'  		=> $url . 'caps/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'yard' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'yard/sample-data.xml',
				'theme_settings' 	=> $url . 'yard/oceanwp-export.dat',
				'form_file'  		=> $url . 'yard/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'bedding' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'bedding/sample-data.xml',
				'theme_settings' 	=> $url . 'bedding/oceanwp-export.dat',
				'form_file'  		=> $url . 'bedding/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'pet' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'pet/sample-data.xml',
				'theme_settings' 	=> $url . 'pet/oceanwp-export.dat',
				'form_file'  		=> $url . 'pet/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'rider' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'rider/sample-data.xml',
				'theme_settings' 	=> $url . 'rider/oceanwp-export.dat',
				'form_file'  		=> $url . 'rider/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'candle' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'candle/sample-data.xml',
				'theme_settings' 	=> $url . 'candle/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'candle/widgets.wie',
				'form_file'  		=> $url . 'candle/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'bikini' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'bikini/sample-data.xml',
				'theme_settings' 	=> $url . 'bikini/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'bikini/widgets.wie',
				'form_file'  		=> $url . 'bikini/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'board' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'board/sample-data.xml',
				'theme_settings' 	=> $url . 'board/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'board/widgets.wie',
				'form_file'  		=> $url . 'board/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'tea' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'tea/sample-data.xml',
				'theme_settings' 	=> $url . 'tea/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'tea/widgets.wie',
				'form_file'  		=> $url . 'tea/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'jean' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'jean/sample-data.xml',
				'theme_settings' 	=> $url . 'jean/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'jean/widgets.wie',
				'form_file'  		=> $url . 'jean/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'cupcake' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'cupcake/sample-data.xml',
				'theme_settings' 	=> $url . 'cupcake/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'cupcake/widgets.wie',
				'form_file'  		=> $url . 'cupcake/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'tools' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'tools/sample-data.xml',
				'theme_settings' 	=> $url . 'tools/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'tools/widgets.wie',
				'form_file'  		=> $url . 'tools/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'bikes' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'bikes/sample-data.xml',
				'theme_settings' 	=> $url . 'bikes/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'bikes/widgets.wie',
				'form_file'  		=> $url . 'bikes/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'zen' => array(
				'categories'        => array( 'Business', 'One Page' ),
				'xml_file'     		=> $url . 'zen/sample-data.xml',
				'theme_settings' 	=> $url . 'zen/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'zen/widgets.wie',
				'form_file'  		=> $url . 'zen/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'wine' => array(
				'categories'        => array( 'eCommerce', 'Business' ),
				'xml_file'     		=> $url . 'wine/sample-data.xml',
				'theme_settings' 	=> $url . 'wine/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'wine/widgets.wie',
				'form_file'  		=> $url . 'wine/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'watch' => array(
				'categories'        => array( 'eCommerce', 'Business' ),
				'xml_file'     		=> $url . 'watch/sample-data.xml',
				'theme_settings' 	=> $url . 'watch/oceanwp-export.dat',
				'form_file'  		=> $url . 'watch/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'sushi' => array(
				'categories'        => array( 'Business' ),
				'xml_file'     		=> $url . 'sushi/sample-data.xml',
				'theme_settings' 	=> $url . 'sushi/oceanwp-export.dat',
				'form_file'  		=> $url . 'sushi/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'suit' => array(
				'categories'        => array( 'eCommerce', 'Business' ),
				'xml_file'     		=> $url . 'suit/sample-data.xml',
				'theme_settings' 	=> $url . 'suit/oceanwp-export.dat',
				'form_file'  		=> $url . 'suit/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'organic' => array(
				'categories'        => array( 'eCommerce', 'Business' ),
				'xml_file'     		=> $url . 'organic/sample-data.xml',
				'theme_settings' 	=> $url . 'organic/oceanwp-export.dat',
				'form_file'  		=> $url . 'organic/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
						array(
							'slug' 		=> 'ocean-footer-callout',
							'init'  	=> 'ocean-footer-callout/ocean-footer-callout.php',
							'name' 		=> 'Ocean Footer Callout',
						),
					),
				),
			),

			'precious' => array(
				'categories'        => array( 'eCommerce', 'Business' ),
				'xml_file'     		=> $url . 'precious/sample-data.xml',
				'theme_settings' 	=> $url . 'precious/oceanwp-export.dat',
				'form_file'  		=> $url . 'precious/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'mynails' => array(
				'categories'        => array( 'Business', 'One Page' ),
				'xml_file'     		=> $url . 'mynails/sample-data.xml',
				'theme_settings' 	=> $url . 'mynails/oceanwp-export.dat',
				'form_file'  		=> $url . 'mynails/form.json',
				'home_title'  		=> 'Home',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'massage' => array(
				'categories'        => array( 'Business', 'One Page' ),
				'xml_file'     		=> $url . 'massage/sample-data.xml',
				'theme_settings' 	=> $url . 'massage/oceanwp-export.dat',
				'form_file'  		=> $url . 'massage/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'News',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'hope' => array(
				'categories'        => array( 'Business', 'One Page' ),
				'xml_file'     		=> $url . 'hope/sample-data.xml',
				'theme_settings' 	=> $url . 'hope/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'hope/widgets.wie',
				'form_file'  		=> $url . 'hope/form.json',
				'home_title'  		=> 'Home',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'ocean-stick-anything',
							'init'  	=> 'ocean-stick-anything/ocean-stick-anything.php',
							'name'  	=> 'Ocean Stick Anything',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'glasses' => array(
				'categories'        => array( 'eCommerce', 'Business' ),
				'xml_file'     		=> $url . 'glasses/sample-data.xml',
				'theme_settings' 	=> $url . 'glasses/oceanwp-export.dat',
				'form_file'  		=> $url . 'glasses/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'festival' => array(
				'categories'        => array( 'Business', 'One Page' ),
				'xml_file'     		=> $url . 'festival/sample-data.xml',
				'theme_settings' 	=> $url . 'festival/oceanwp-export.dat',
				'form_file'  		=> $url . 'festival/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'cream' => array(
				'categories'        => array( 'Business', 'One Page' ),
				'xml_file'     		=> $url . 'cream/sample-data.xml',
				'theme_settings' 	=> $url . 'cream/oceanwp-export.dat',
				'form_file'  		=> $url . 'cream/form.json',
				'home_title'  		=> 'Home',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'clinic' => array(
				'categories'        => array( 'Business', 'Blog' ),
				'xml_file'     		=> $url . 'clinic/sample-data.xml',
				'theme_settings' 	=> $url . 'clinic/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'clinic/widgets.wie',
				'form_file'  		=> $url . 'clinic/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'choco' => array(
				'categories'        => array( 'Business', 'Blog' ),
				'xml_file'     		=> $url . 'choco/sample-data.xml',
				'theme_settings' 	=> $url . 'choco/oceanwp-export.dat',
				'form_file'  		=> $url . 'choco/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'News',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'boxe' => array(
				'categories'        => array( 'eCommerce', 'Business' ),
				'xml_file'     		=> $url . 'boxe/sample-data.xml',
				'theme_settings' 	=> $url . 'boxe/oceanwp-export.dat',
				'form_file'  		=> $url . 'boxe/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'bounty' => array(
				'categories'        => array( 'Corporate' ),
				'xml_file'     		=> $url . 'bounty/sample-data.xml',
				'theme_settings' 	=> $url . 'bounty/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'bounty/widgets.wie',
				'form_file'  		=> $url . 'bounty/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'News',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'give',
							'init'  	=> 'give/give.php',
							'name'  	=> 'Give - Donation Plugin',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'boots' => array(
				'categories'        => array( 'eCommerce', 'Business' ),
				'xml_file'     		=> $url . 'boots/sample-data.xml',
				'theme_settings' 	=> $url . 'boots/oceanwp-export.dat',
				'form_file'  		=> $url . 'boots/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'books' => array(
				'categories'        => array( 'eCommerce', 'Business' ),
				'xml_file'     		=> $url . 'books/sample-data.xml',
				'theme_settings' 	=> $url . 'books/oceanwp-export.dat',
				'form_file'  		=> $url . 'books/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'bartender' => array(
				'categories'        => array( 'Business', 'Blog' ),
				'xml_file'     		=> $url . 'bartender/sample-data.xml',
				'theme_settings' 	=> $url . 'bartender/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'bartender/widgets.wie',
				'form_file'  		=> $url . 'bartender/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'News',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'baby' => array(
				'categories'        => array( 'eCommerce', 'Business' ),
				'xml_file'     		=> $url . 'baby/sample-data.xml',
				'theme_settings' 	=> $url . 'baby/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'baby/widgets.wie',
				'form_file'  		=> $url . 'baby/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '5',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'nutritionist' => array(
				'categories'        => array( 'Business', 'One Page' ),
				'xml_file'     		=> $url . 'nutritionist/sample-data.xml',
				'theme_settings' 	=> $url . 'nutritionist/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'nutritionist/widgets.wie',
				'form_file'  		=> $url . 'nutritionist/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'wstore' => array(
				'categories'        => array( 'eCommerce', 'Business' ),
				'xml_file'     		=> $url . 'wstore/sample-data.xml',
				'theme_settings' 	=> $url . 'wstore/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'wstore/widgets.wie',
				'form_file'  		=> $url . 'wstore/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '5',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
						array(
							'slug' 		=> 'ocean-footer-callout',
							'init'  	=> 'ocean-footer-callout/ocean-footer-callout.php',
							'name' 		=> 'Ocean Footer Callout',
						),
					),
				),
			),

			'delicious' => array(
				'categories'        => array( 'Business', 'One Page' ),
				'xml_file'     		=> $url . 'delicious/sample-data.xml',
				'theme_settings' 	=> $url . 'delicious/oceanwp-export.dat',
				'form_file'  		=> $url . 'delicious/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'lotus' => array(
				'categories'        => array( 'Corporate', 'One Page' ),
				'xml_file'     		=> $url . 'lotus/sample-data.xml',
				'theme_settings' 	=> $url . 'lotus/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'lotus/widgets.wie',
				'form_file'  		=> $url . 'lotus/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '3',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'hair' => array(
				'categories'        => array( 'Corporate' ),
				'xml_file'     		=> $url . 'hair/sample-data.xml',
				'theme_settings' 	=> $url . 'hair/oceanwp-export.dat',
				'form_file'  		=> $url . 'hair/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'pastry' => array(
				'categories'        => array( 'One Page', 'Business' ),
				'xml_file'     		=> $url . 'pastry/sample-data.xml',
				'theme_settings' 	=> $url . 'pastry/oceanwp-export.dat',
				'form_file'  		=> $url . 'pastry/form.json',
				'home_title'  		=> 'Home',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'church' => array(
				'categories'        => array( 'Corporate' ),
				'xml_file'     		=> $url . 'church/sample-data.xml',
				'theme_settings' 	=> $url . 'church/oceanwp-export.dat',
				'form_file'  		=> $url . 'church/form.json',
				'home_title'  		=> 'Home',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'give',
							'init'  	=> 'give/give.php',
							'name'  	=> 'Give - Donation Plugin',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'jacob' => array(
				'categories'        => array( 'One Page', 'Blog' ),
				'xml_file'     		=> $url . 'jacob/sample-data.xml',
				'theme_settings' 	=> $url . 'jacob/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'jacob/widgets.wie',
				'form_file'  		=> $url . 'jacob/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '7',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-portfolio',
							'init'  	=> 'ocean-portfolio/ocean-portfolio.php',
							'name' 		=> 'Ocean Portfolio',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
						array(
							'slug' 		=> 'ocean-side-panel',
							'init'  	=> 'ocean-side-panel/ocean-side-panel.php',
							'name' 		=> 'Ocean Side Panel',
						),
					),
				),
			),

			'coffeeshop' => array(
				'categories'        => array( 'eCommerce', 'Business' ),
				'xml_file'     		=> $url . 'coffeeshop/sample-data.xml',
				'theme_settings' 	=> $url . 'coffeeshop/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'coffeeshop/widgets.wie',
				'form_file'  		=> $url . 'coffeeshop/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '7',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
						array(
							'slug' 		=> 'ocean-woo-popup',
							'init'  	=> 'ocean-woo-popup/ocean-woo-popup.php',
							'name' 		=> 'Ocean Woo Popup',
						),
						array(
							'slug' 		=> 'ocean-popup-login',
							'init'  	=> 'ocean-popup-login/ocean-popup-login.php',
							'name' 		=> 'Ocean Popup Login',
						),
					),
				),
			),

			'recipes' => array(
				'categories'        => array( 'Blog', 'Business' ),
				'xml_file'     		=> $url . 'recipes/sample-data.xml',
				'theme_settings' 	=> $url . 'recipes/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'recipes/widgets.wie',
				'form_file'  		=> $url . 'recipes/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '7',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'ocean-modal-window',
							'init'  	=> 'ocean-modal-window/ocean-modal-window.php',
							'name'  	=> 'Ocean Modal Window',
						),
						array(
							'slug'  	=> 'ocean-posts-slider',
							'init'  	=> 'ocean-posts-slider/ocean-posts-slider.php',
							'name'  	=> 'Ocean Posts Slider',
						),
						array(
							'slug'  	=> 'ocean-stick-anything',
							'init'  	=> 'ocean-stick-anything/ocean-stick-anything.php',
							'name'  	=> 'Ocean Stick Anything',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-hooks',
							'init'  	=> 'ocean-hooks/ocean-hooks.php',
							'name' 		=> 'Ocean Hooks',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'minimal' => array(
				'categories'        => array( 'Blog', 'One Page' ),
				'xml_file'     		=> $url . 'minimal/sample-data.xml',
				'theme_settings' 	=> $url . 'minimal/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'minimal/widgets.wie',
				'form_file'  		=> $url . 'minimal/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '7',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'ocean-modal-window',
							'init'  	=> 'ocean-modal-window/ocean-modal-window.php',
							'name'  	=> 'Ocean Modal Window',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-portfolio',
							'init'  	=> 'ocean-portfolio/ocean-portfolio.php',
							'name' 		=> 'Ocean Portfolio',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'carshop' => array(
				'categories'        => array(  'Business', 'Corporate' ),
				'xml_file'     		=> $url . 'carshop/sample-data.xml',
				'theme_settings' 	=> $url . 'carshop/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'carshop/widgets.wie',
				'form_file'  		=> $url . 'carshop/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '7',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'ocean-posts-slider',
							'init'  	=> 'ocean-posts-slider/ocean-posts-slider.php',
							'name'  	=> 'Ocean Posts Slider',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'auto-listings',
							'init'  	=> 'auto-listings/auto-listings.php',
							'name'  	=> 'Auto Listings',
						),
						array(
							'slug'  	=> 'meta-box',
							'init'  	=> 'meta-box/meta-box.php',
							'name'  	=> 'Meta Box',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
						array(
							'slug' 		=> 'ocean-popup-login',
							'init'  	=> 'ocean-popup-login/ocean-popup-login.php',
							'name' 		=> 'Ocean Popup Login',
						),
					),
				),
			),

			'blogroll' => array(
				'categories'        => array(  'Blog', 'One Page' ),
				'xml_file'     		=> $url . 'blogroll/sample-data.xml',
				'theme_settings' 	=> $url . 'blogroll/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'blogroll/widgets.wie',
				'form_file'  		=> $url . 'blogroll/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '7',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'ocean-posts-slider',
							'init'  	=> 'ocean-posts-slider/ocean-posts-slider.php',
							'name'  	=> 'Ocean Posts Slider',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'wild' => array(
				'categories'        => array(  'Business', 'Corporate' ),
				'xml_file'     		=> $url . 'wild/sample-data.xml',
				'theme_settings' 	=> $url . 'wild/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'wild/widgets.wie',
				'form_file'  		=> $url . 'wild/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '7',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'trainer' => array(
				'categories'        => array(  'Business', 'Corporate', 'Sport' ),
				'xml_file'     		=> $url . 'trainer/sample-data.xml',
				'theme_settings' 	=> $url . 'trainer/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'trainer/widgets.wie',
				'form_file'  		=> $url . 'trainer/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '7',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
						array(
							'slug' 		=> 'ocean-side-panel',
							'init'  	=> 'ocean-side-panel/ocean-side-panel.php',
							'name' 		=> 'Ocean Side Panel',
						),
					),
				),
			),

			'toys' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'toys/sample-data.xml',
				'theme_settings' 	=> $url . 'toys/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'toys/widgets.wie',
				'form_file'  		=> $url . 'toys/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '7',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'tattoo' => array(
				'categories'        => array(  'Business', 'Corporate' ),
				'xml_file'     		=> $url . 'tattoo/sample-data.xml',
				'theme_settings' 	=> $url . 'tattoo/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'tattoo/widgets.wie',
				'form_file'  		=> $url . 'tattoo/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '7',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'paris' => array(
				'categories'        => array( 'One Page', 'Business' ),
				'xml_file'     		=> $url . 'paris/sample-data.xml',
				'theme_settings' 	=> $url . 'paris/oceanwp-export.dat',
				'home_title'  		=> 'Home',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
						array(
							'slug' 		=> 'ocean-sticky-footer',
							'init'  	=> 'ocean-sticky-footer/ocean-sticky-footer.php',
							'name' 		=> 'Ocean Sticky Footer',
						),
					),
				),
			),

			'paint' => array(
				'categories'        => array( 'Corporate', 'Business' ),
				'xml_file'     		=> $url . 'paint/sample-data.xml',
				'theme_settings' 	=> $url . 'paint/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'paint/widgets.wie',
				'form_file'  		=> $url . 'paint/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '7',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'nextgen' => array(
				'categories'        => array( 'eCommerce', 'Business' ),
				'xml_file'     		=> $url . 'nextgen/sample-data.xml',
				'theme_settings' 	=> $url . 'nextgen/oceanwp-export.dat',
				'form_file'  		=> $url . 'nextgen/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '7',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
						array(
							'slug' 		=> 'ocean-footer-callout',
							'init'  	=> 'ocean-footer-callout/ocean-footer-callout.php',
							'name' 		=> 'Ocean Footer Callout',
						),
					),
				),
			),

			'emma' => array(
				'categories'        => array( 'One Page' ),
				'xml_file'     		=> $url . 'emma/sample-data.xml',
				'theme_settings' 	=> $url . 'emma/oceanwp-export.dat',
				'form_file'  		=> $url . 'emma/form.json',
				'home_title'  		=> 'Home',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'digital' => array(
				'categories'        => array( 'Corporate' ),
				'xml_file'     		=> $url . 'digital/sample-data.xml',
				'theme_settings' 	=> $url . 'digital/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'digital/widgets.wie',
				'form_file'  		=> $url . 'digital/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '7',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'designer' => array(
				'categories'        => array( 'Corporate' ),
				'xml_file'     		=> $url . 'designer/sample-data.xml',
				'theme_settings' 	=> $url . 'designer/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'designer/widgets.wie',
				'form_file'  		=> $url . 'designer/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '7',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'dark' => array(
				'categories'        => array( 'One Page' ),
				'xml_file'     		=> $url . 'dark/sample-data.xml',
				'theme_settings' 	=> $url . 'dark/oceanwp-export.dat',
				'home_title'  		=> 'Home',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'clothes' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'clothes/sample-data.xml',
				'theme_settings' 	=> $url . 'clothes/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'clothes/widgets.wie',
				'form_file'  		=> $url . 'clothes/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '7',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'classy' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'classy/sample-data.xml',
				'theme_settings' 	=> $url . 'classy/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'classy/widgets.wie',
				'form_file'  		=> $url . 'classy/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '7',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'beauty' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'beauty/sample-data.xml',
				'theme_settings' 	=> $url . 'beauty/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'beauty/widgets.wie',
				'form_file'  		=> $url . 'beauty/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '7',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
						array(
							'slug' 		=> 'ocean-footer-callout',
							'init'  	=> 'ocean-footer-callout/ocean-footer-callout.php',
							'name' 		=> 'Ocean Footer Callout',
						),
					),
				),
			),

			'bar' => array(
				'categories'        => array( 'Corporate' ),
				'xml_file'     		=> $url . 'bar/sample-data.xml',
				'theme_settings' 	=> $url . 'bar/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'bar/widgets.wie',
				'form_file'  		=> $url . 'bar/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '7',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'event' => array(
				'categories'        => array( 'Corporate' ),
				'xml_file'     		=> $url . 'event/sample-data.xml',
				'theme_settings' 	=> $url . 'event/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'event/widgets.wie',
				'form_file'  		=> $url . 'event/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '7',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'very-simple-event-list',
							'init'  	=> 'very-simple-event-list/vsel.php',
							'name'  	=> 'Very Simple Event List',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'bakery' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'bakery/sample-data.xml',
				'theme_settings' 	=> $url . 'bakery/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'bakery/widgets.wie',
				'form_file'  		=> $url . 'bakery/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '7',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'smart-slider-3',
							'init'  	=> 'smart-slider-3/smart-slider-3.php',
							'name'  	=> 'Smart Slider 3',
						),
						array(
							'slug'  	=> 'woo-gutenberg-products-block',
							'init'  	=> 'woo-gutenberg-products-block/woocommerce-gutenberg-products-block.php',
							'name'  	=> 'WooCommerce Blocks',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
						array(
							'slug' 		=> 'ocean-woo-popup',
							'init'  	=> 'ocean-woo-popup/ocean-woo-popup.php',
							'name' 		=> 'Ocean Woo Popup',
						),
						array(
							'slug' 		=> 'ocean-popup-login',
							'init'  	=> 'ocean-popup-login/ocean-popup-login.php',
							'name' 		=> 'Ocean Popup Login',
						),
					),
				),
			),

			'corporate' => array(
				'categories'        => array( 'Corporate' ),
				'xml_file'     		=> $url . 'corporate/sample-data.xml',
				'theme_settings' 	=> $url . 'corporate/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'corporate/widgets.wie',
				'form_file'  		=> $url . 'corporate/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '7',
				'elementor_width'  	=> '1140',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
						array(
							'slug' 		=> 'ocean-portfolio',
							'init'  	=> 'ocean-portfolio/ocean-portfolio.php',
							'name' 		=> 'Ocean Portfolio',
						),
						array(
							'slug' 		=> 'ocean-popup-login',
							'init'  	=> 'ocean-popup-login/ocean-popup-login.php',
							'name' 		=> 'Ocean Popup Login',
						),
					),
				),
			),

			'destination' => array(
				'categories'        => array( 'Corporate' ),
				'xml_file'     		=> $url . 'destination/sample-data.xml',
				'theme_settings' 	=> $url . 'destination/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'destination/widgets.wie',
				'form_file'  		=> $url . 'destination/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '7',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'lauren' => array(
				'categories'        => array( 'Corporate' ),
				'xml_file'     		=> $url . 'lauren/sample-data.xml',
				'theme_settings' 	=> $url . 'lauren/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'lauren/widgets.wie',
				'form_file'  		=> $url . 'lauren/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '7',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
						array(
							'slug' 		=> 'ocean-portfolio',
							'init'  	=> 'ocean-portfolio/ocean-portfolio.php',
							'name' 		=> 'Ocean Portfolio',
						),
					),
				),
			),

			'onestore' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'onestore/sample-data.xml',
				'theme_settings' 	=> $url . 'onestore/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'onestore/widgets.wie',
				'form_file'  		=> $url . 'onestore/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '7',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'ocean-posts-slider',
							'init'  	=> 'ocean-posts-slider/ocean-posts-slider.php',
							'name'  	=> 'Ocean Posts Slider',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
						array(
							'slug'  	=> 'smart-slider-3',
							'init'  	=> 'smart-slider-3/smart-slider-3.php',
							'name'  	=> 'Smart Slider 3',
						),
						array(
							'slug'  	=> 'woo-gutenberg-products-block',
							'init'  	=> 'woo-gutenberg-products-block/woocommerce-gutenberg-products-block.php',
							'name'  	=> 'WooCommerce Blocks',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-hooks',
							'init'  	=> 'ocean-hooks/ocean-hooks.php',
							'name' 		=> 'Ocean Hooks',
						),
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
						array(
							'slug' 		=> 'ocean-woo-popup',
							'init'  	=> 'ocean-woo-popup/ocean-woo-popup.php',
							'name' 		=> 'Ocean Woo Popup',
						),
						array(
							'slug' 		=> 'ocean-popup-login',
							'init'  	=> 'ocean-popup-login/ocean-popup-login.php',
							'name' 		=> 'Ocean Popup Login',
						),
					),
				),
			),

			'outfits' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'outfits/sample-data.xml',
				'theme_settings' 	=> $url . 'outfits/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'outfits/widgets.wie',
				'form_file'  		=> $url . 'outfits/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '7',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'ocean-posts-slider',
							'init'  	=> 'ocean-posts-slider/ocean-posts-slider.php',
							'name'  	=> 'Ocean Posts Slider',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
						array(
							'slug'  	=> 'smart-slider-3',
							'init'  	=> 'smart-slider-3/smart-slider-3.php',
							'name'  	=> 'Smart Slider 3',
						),
						array(
							'slug'  	=> 'woo-gutenberg-products-block',
							'init'  	=> 'woo-gutenberg-products-block/woocommerce-gutenberg-products-block.php',
							'name'  	=> 'WooCommerce Blocks',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-hooks',
							'init'  	=> 'ocean-hooks/ocean-hooks.php',
							'name' 		=> 'Ocean Hooks',
						),
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
						array(
							'slug' 		=> 'ocean-footer-callout',
							'init'  	=> 'ocean-footer-callout/ocean-footer-callout.php',
							'name' 		=> 'Ocean Footer Callout',
						),
						array(
							'slug' 		=> 'ocean-woo-popup',
							'init'  	=> 'ocean-woo-popup/ocean-woo-popup.php',
							'name' 		=> 'Ocean Woo Popup',
						),
						array(
							'slug' 		=> 'ocean-popup-login',
							'init'  	=> 'ocean-popup-login/ocean-popup-login.php',
							'name' 		=> 'Ocean Popup Login',
						),
					),
				),
			),

			'simply' => array(
				'categories'        => array( 'Blog' ),
				'xml_file'     		=> $url . 'simply/sample-data.xml',
				'theme_settings' 	=> $url . 'simply/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'simply/widgets.wie',
				'form_file'  		=> $url . 'simply/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '7',
				'elementor_width'  	=> '1140',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
						array(
							'slug'  	=> 'ocean-portfolio',
							'init'  	=> 'ocean-portfolio/ocean-portfolio.php',
							'name'  	=> 'Ocean Portfolio',
						),
					),
				),
			),

			'studio' => array(
				'categories'        => array( 'Corporate' ),
				'xml_file'     		=> $url . 'studio/sample-data.xml',
				'theme_settings' 	=> $url . 'studio/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'studio/widgets.wie',
				'form_file'  		=> $url . 'studio/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '7',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
						array(
							'slug' 		=> 'ocean-portfolio',
							'init'  	=> 'ocean-portfolio/ocean-portfolio.php',
							'name' 		=> 'Ocean Portfolio',
						),
					),
				),
			),

			'tech' => array(
				'categories'        => array( 'Blog' ),
				'xml_file'     		=> $url . 'tech/sample-data.xml',
				'theme_settings' 	=> $url . 'tech/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'tech/widgets.wie',
				'form_file'  		=> $url . 'tech/form.json',
				'blog_title'  		=> 'Home',
				'posts_to_show'  	=> '10',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-modal-window',
							'init'  	=> 'ocean-modal-window/ocean-modal-window.php',
							'name'  	=> 'Ocean Modal Window',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'ocean-posts-slider',
							'init'  	=> 'ocean-posts-slider/ocean-posts-slider.php',
							'name'  	=> 'Ocean Posts Slider',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
						array(
							'slug'  	=> 'ocean-popup-login',
							'init'  	=> 'ocean-popup-login/ocean-popup-login.php',
							'name'  	=> 'Ocean Popup Login',
						),
					),
				),
			),

			'simpleblog' => array(
				'categories'        => array( 'Blog' ),
				'xml_file'     		=> $url . 'simpleblog/sample-data.xml',
				'theme_settings' 	=> $url . 'simpleblog/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'simpleblog/widgets.wie',
				'form_file'  		=> $url . 'simpleblog/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '7',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'ocean-posts-slider',
							'init'  	=> 'ocean-posts-slider/ocean-posts-slider.php',
							'name'  	=> 'Ocean Posts Slider',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'agency' => array(
				'categories'        => array( 'Corporate' ),
				'xml_file'     		=> $url . 'agency/sample-data.xml',
				'theme_settings' 	=> $url . 'agency/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'agency/widgets.wie',
				'form_file'  		=> $url . 'agency/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '7',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
						array(
							'slug' 		=> 'ocean-portfolio',
							'init'  	=> 'ocean-portfolio/ocean-portfolio.php',
							'name' 		=> 'Ocean Portfolio',
						),
					),
				),
			),

			'barber' => array(
				'categories'        => array( 'Corporate' ),
				'xml_file'     		=> $url . 'barber/sample-data.xml',
				'theme_settings' 	=> $url . 'barber/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'barber/widgets.wie',
				'form_file'  		=> $url . 'barber/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '3',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'bright' => array(
				'categories'        => array( 'Corporate' ),
				'xml_file'     		=> $url . 'bright/sample-data.xml',
				'theme_settings' 	=> $url . 'bright/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'bright/widgets.wie',
				'form_file'  		=> $url . 'bright/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
						array(
							'slug' 		=> 'ocean-portfolio',
							'init'  	=> 'ocean-portfolio/ocean-portfolio.php',
							'name' 		=> 'Ocean Portfolio',
						),
					),
				),
			),

			'charity' => array(
				'categories'        => array( 'Corporate' ),
				'xml_file'     		=> $url . 'charity/sample-data.xml',
				'theme_settings' 	=> $url . 'charity/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'charity/widgets.wie',
				'form_file'  		=> $url . 'charity/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '9',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'give',
							'init'  	=> 'give/give.php',
							'name'  	=> 'Give - Donation Plugin',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'computer' => array(
				'categories'        => array( 'Corporate' ),
				'xml_file'     		=> $url . 'computer/sample-data.xml',
				'theme_settings' 	=> $url . 'computer/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'computer/widgets.wie',
				'form_file'  		=> $url . 'computer/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '3',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'construction' => array(
				'categories'        => array( 'Corporate' ),
				'xml_file'     		=> $url . 'construction/sample-data.xml',
				'theme_settings' 	=> $url . 'construction/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'construction/widgets.wie',
				'form_file'  		=> $url . 'construction/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '3',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-portfolio',
							'init'  	=> 'ocean-portfolio/ocean-portfolio.php',
							'name' 		=> 'Ocean Portfolio',
						),
						array(
							'slug' 		=> 'ocean-sticky-footer',
							'init'  	=> 'ocean-sticky-footer/ocean-sticky-footer.php',
							'name' 		=> 'Ocean Sticky Footer',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'coffee' => array(
				'categories'        => array( 'Corporate' ),
				'xml_file'     		=> $url . 'coffee/sample-data.xml',
				'theme_settings' 	=> $url . 'coffee/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'coffee/widgets.wie',
				'form_file'  		=> $url . 'coffee/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '3',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-instagram',
							'init'  	=> 'ocean-instagram/ocean-instagram.php',
							'name' 		=> 'Ocean Instagram',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'design' => array(
				'categories'        => array( 'Corporate' ),
				'xml_file'     		=> $url . 'design/sample-data.xml',
				'theme_settings' 	=> $url . 'design/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'design/widgets.wie',
				'form_file'  		=> $url . 'design/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '7',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
						array(
							'slug' 		=> 'ocean-portfolio',
							'init'  	=> 'ocean-portfolio/ocean-portfolio.php',
							'name' 		=> 'Ocean Portfolio',
						),
					),
				),
			),

			'fitness' => array(
				'categories'        => array( 'Corporate' ),
				'xml_file'     		=> $url . 'fitness/sample-data.xml',
				'theme_settings' 	=> $url . 'fitness/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'fitness/widgets.wie',
				'form_file'  		=> $url . 'fitness/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '7',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'florist' => array(
				'categories'        => array( 'Corporate' ),
				'xml_file'     		=> $url . 'florist/sample-data.xml',
				'theme_settings' 	=> $url . 'florist/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'florist/widgets.wie',
				'form_file'  		=> $url . 'florist/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '4',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
						array(
							'slug' 		=> 'ocean-portfolio',
							'init'  	=> 'ocean-portfolio/ocean-portfolio.php',
							'name' 		=> 'Ocean Portfolio',
						),
					),
				),
			),

			'freelance' => array(
				'categories'        => array( 'Corporate' ),
				'xml_file'     		=> $url . 'freelance/sample-data.xml',
				'theme_settings' 	=> $url . 'freelance/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'freelance/widgets.wie',
				'form_file'  		=> $url . 'freelance/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '3',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'hairdresser' => array(
				'categories'        => array( 'Corporate' ),
				'xml_file'     		=> $url . 'hairdresser/sample-data.xml',
				'theme_settings' 	=> $url . 'hairdresser/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'hairdresser/widgets.wie',
				'form_file'  		=> $url . 'hairdresser/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '3',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'hosting' => array(
				'categories'        => array( 'Corporate' ),
				'xml_file'     		=> $url . 'hosting/sample-data.xml',
				'theme_settings' 	=> $url . 'hosting/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'hosting/widgets.wie',
				'form_file'  		=> $url . 'hosting/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '3',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-modal-window',
							'init'  	=> 'ocean-modal-window/ocean-modal-window.php',
							'name'  	=> 'Ocean Modal Window',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'interior' => array(
				'categories'        => array( 'Corporate' ),
				'xml_file'     		=> $url . 'interior/sample-data.xml',
				'theme_settings' 	=> $url . 'interior/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'interior/widgets.wie',
				'form_file'  		=> $url . 'interior/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '3',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'inspire' => array(
				'categories'        => array( 'Corporate' ),
				'xml_file'     		=> $url . 'inspire/sample-data.xml',
				'theme_settings' 	=> $url . 'inspire/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'inspire/widgets.wie',
				'form_file'  		=> $url . 'inspire/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '7',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
						array(
							'slug' 		=> 'ocean-portfolio',
							'init'  	=> 'ocean-portfolio/ocean-portfolio.php',
							'name' 		=> 'Ocean Portfolio',
						),
					),
				),
			),

			'learn' => array(
				'categories'        => array( 'Corporate' ),
				'xml_file'     		=> $url . 'learn/sample-data.xml',
				'theme_settings' 	=> $url . 'learn/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'learn/widgets.wie',
				'form_file'  		=> $url . 'learn/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '3',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'nails' => array(
				'categories'        => array( 'Corporate' ),
				'xml_file'     		=> $url . 'nails/sample-data.xml',
				'theme_settings' 	=> $url . 'nails/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'nails/widgets.wie',
				'form_file'  		=> $url . 'nails/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '3',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-footer-callout',
							'init'  	=> 'ocean-footer-callout/ocean-footer-callout.php',
							'name' 		=> 'Ocean Footer Callout',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'medical' => array(
				'categories'        => array( 'Corporate' ),
				'xml_file'     		=> $url . 'medical/sample-data.xml',
				'theme_settings' 	=> $url . 'medical/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'medical/widgets.wie',
				'form_file'  		=> $url . 'medical/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '3',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
						array(
							'slug' 		=> 'ocean-footer-callout',
							'init'  	=> 'ocean-footer-callout/ocean-footer-callout.php',
							'name' 		=> 'Ocean Footer Callout',
						),
						array(
							'slug' 		=> 'ocean-side-panel',
							'init'  	=> 'ocean-side-panel/ocean-side-panel.php',
							'name' 		=> 'Ocean Side Panel',
						),
					),
				),
			),

			'music' => array(
				'categories'        => array( 'Corporate' ),
				'xml_file'     		=> $url . 'music/sample-data.xml',
				'theme_settings' 	=> $url . 'music/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'music/widgets.wie',
				'form_file'  		=> $url . 'music/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '3',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'photo' => array(
				'categories'        => array( 'Corporate' ),
				'xml_file'     		=> $url . 'photo/sample-data.xml',
				'theme_settings' 	=> $url . 'photo/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'photo/widgets.wie',
				'form_file'  		=> $url . 'photo/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '10',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'photography' => array(
				'categories'        => array( 'Corporate' ),
				'xml_file'     		=> $url . 'photography/sample-data.xml',
				'theme_settings' 	=> $url . 'photography/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'photography/widgets.wie',
				'form_file'  		=> $url . 'photography/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
						array(
							'slug' 		=> 'ocean-portfolio',
							'init'  	=> 'ocean-portfolio/ocean-portfolio.php',
							'name' 		=> 'Ocean Portfolio',
						),
					),
				),
			),

			'pizza' => array(
				'categories'        => array( 'Corporate' ),
				'xml_file'     		=> $url . 'pizza/sample-data.xml',
				'theme_settings' 	=> $url . 'pizza/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'pizza/widgets.wie',
				'form_file'  		=> $url . 'pizza/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'scuba' => array(
				'categories'        => array( 'Corporate' ),
				'xml_file'     		=> $url . 'scuba/sample-data.xml',
				'theme_settings' 	=> $url . 'scuba/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'scuba/widgets.wie',
				'form_file'  		=> $url . 'scuba/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '9',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
						array(
							'slug' 		=> 'ocean-full-screen',
							'init'  	=> 'ocean-full-screen/ocean-full-screen.php',
							'name' 		=> 'Ocean Full Screen',
						),
					),
				),
			),

			'skate' => array(
				'categories'        => array( 'Corporate' ),
				'xml_file'     		=> $url . 'skate/sample-data.xml',
				'theme_settings' 	=> $url . 'skate/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'skate/widgets.wie',
				'form_file'  		=> $url . 'skate/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'surfing' => array(
				'categories'        => array( 'Corporate' ),
				'xml_file'     		=> $url . 'surfing/sample-data.xml',
				'theme_settings' 	=> $url . 'surfing/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'surfing/widgets.wie',
				'form_file'  		=> $url . 'surfing/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '3',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'veggie' => array(
				'categories'        => array( 'Corporate' ),
				'xml_file'     		=> $url . 'veggie/sample-data.xml',
				'theme_settings' 	=> $url . 'veggie/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'veggie/widgets.wie',
				'form_file'  		=> $url . 'veggie/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '3',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'wedding' => array(
				'categories'        => array( 'Corporate' ),
				'xml_file'     		=> $url . 'wedding/sample-data.xml',
				'theme_settings' 	=> $url . 'wedding/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'wedding/widgets.wie',
				'form_file'  		=> $url . 'wedding/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '5',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
						array(
							'slug' 		=> 'ocean-popup-login',
							'init'  	=> 'ocean-popup-login/ocean-popup-login.php',
							'name' 		=> 'Ocean Popup Login',
						),
						array(
							'slug' 		=> 'ocean-portfolio',
							'init'  	=> 'ocean-portfolio/ocean-portfolio.php',
							'name' 		=> 'Ocean Portfolio',
						),
					),
				),
			),

			'consulting' => array(
				'categories'        => array( 'One Page' ),
				'xml_file'     		=> $url . 'consulting/sample-data.xml',
				'theme_settings' 	=> $url . 'consulting/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'consulting/widgets.wie',
				'form_file'  		=> $url . 'consulting/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '3',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'spa' => array(
				'categories'        => array( 'One Page' ),
				'xml_file'     		=> $url . 'spa/sample-data.xml',
				'theme_settings' 	=> $url . 'spa/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'spa/widgets.wie',
				'form_file'  		=> $url . 'spa/form.json',
				'home_title'  		=> 'Home',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'restaurant' => array(
				'categories'        => array( 'One Page' ),
				'xml_file'     		=> $url . 'restaurant/sample-data.xml',
				'theme_settings' 	=> $url . 'restaurant/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'restaurant/widgets.wie',
				'form_file'  		=> $url . 'restaurant/form.json',
				'home_title'  		=> 'Home',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
					),
				),
			),

			'chocolate' => array(
				'categories'        => array( 'One Page' ),
				'xml_file'     		=> $url . 'chocolate/sample-data.xml',
				'theme_settings' 	=> $url . 'chocolate/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'chocolate/widgets.wie',
				'form_file'  		=> $url . 'chocolate/form.json',
				'home_title'  		=> 'Home',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
						array(
							'slug' 		=> 'ocean-sticky-footer',
							'init'  	=> 'ocean-sticky-footer/ocean-sticky-footer.php',
							'name' 		=> 'Ocean Sticky Footer',
						),
					),
				),
			),

			'hotel' => array(
				'categories'        => array( 'One Page' ),
				'xml_file'     		=> $url . 'hotel/sample-data.xml',
				'theme_settings' 	=> $url . 'hotel/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'hotel/widgets.wie',
				'form_file'  		=> $url . 'hotel/form.json',
				'home_title'  		=> 'Home',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'makeup' => array(
				'categories'        => array( 'One Page' ),
				'xml_file'     		=> $url . 'makeup/sample-data.xml',
				'theme_settings' 	=> $url . 'makeup/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'makeup/widgets.wie',
				'form_file'  		=> $url . 'makeup/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '3',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'portfolio' => array(
				'categories'        => array( 'One Page' ),
				'xml_file'     		=> $url . 'portfolio/sample-data.xml',
				'theme_settings' 	=> $url . 'portfolio/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'portfolio/widgets.wie',
				'form_file'  		=> $url . 'portfolio/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '10',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-portfolio',
							'init'  	=> 'ocean-portfolio/ocean-portfolio.php',
							'name' 		=> 'Ocean Portfolio',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
						array(
							'slug' 		=> 'ocean-side-panel',
							'init'  	=> 'ocean-side-panel/ocean-side-panel.php',
							'name' 		=> 'Ocean Side Panel',
						),
					),
				),
			),

			'skyscraper' => array(
				'categories'        => array( 'One Page' ),
				'xml_file'     		=> $url . 'skyscraper/sample-data.xml',
				'theme_settings' 	=> $url . 'skyscraper/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'skyscraper/widgets.wie',
				'form_file'  		=> $url . 'skyscraper/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'book' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'book/sample-data.xml',
				'theme_settings' 	=> $url . 'book/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'book/widgets.wie',
				'form_file'  		=> $url . 'book/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '4',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'easy-digital-downloads',
							'init'  	=> 'easy-digital-downloads/easy-digital-downloads.php',
							'name'  	=> 'Easy Digital Downloads',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
						array(
							'slug' 		=> 'ocean-popup-login',
							'init'  	=> 'ocean-popup-login/ocean-popup-login.php',
							'name' 		=> 'Ocean Popup Login',
						),
						array(
							'slug' 		=> 'ocean-woo-popup',
							'init'  	=> 'ocean-woo-popup/ocean-woo-popup.php',
							'name' 		=> 'Ocean Woo Popup',
						),
					),
				),
			),

			'cycle' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'cycle/sample-data.xml',
				'theme_settings' 	=> $url . 'cycle/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'cycle/widgets.wie',
				'form_file'  		=> $url . 'cycle/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '3',
				'elementor_width'  	=> '1260',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'smart-slider-3',
							'init'  	=> 'smart-slider-3/smart-slider-3.php',
							'name'  	=> 'Smart Slider 3',
						),
						array(
							'slug'  	=> 'woo-gutenberg-products-block',
							'init'  	=> 'woo-gutenberg-products-block/woocommerce-gutenberg-products-block.php',
							'name'  	=> 'WooCommerce Blocks',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
						array(
							'slug' 		=> 'ocean-woo-popup',
							'init'  	=> 'ocean-woo-popup/ocean-woo-popup.php',
							'name' 		=> 'Ocean Woo Popup',
						),
					),
				),
			),

			'school' => array(
				'categories'        => array( 'Corporate' ),
				'xml_file'     		=> $url . 'school/sample-data.xml',
				'theme_settings' 	=> $url . 'school/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'school/widgets.wie',
				'form_file'  		=> $url . 'school/form.json',
				'home_title'  		=> 'Home',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'lifterlms',
							'init'  	=> 'lifterlms/lifterlms.php',
							'name'  	=> 'LifterLMS',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'streetfood' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'streetfood/sample-data.xml',
				'theme_settings' 	=> $url . 'streetfood/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'streetfood/widgets.wie',
				'form_file'  		=> $url . 'streetfood/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '7',
				'elementor_width'  	=> '1260',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'woo-gutenberg-products-block',
							'init'  	=> 'woo-gutenberg-products-block/woocommerce-gutenberg-products-block.php',
							'name'  	=> 'WooCommerce Blocks',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
						array(
							'slug' 		=> 'ocean-popup-login',
							'init'  	=> 'ocean-popup-login/ocean-popup-login.php',
							'name' 		=> 'Ocean Popup Login',
						),
						array(
							'slug' 		=> 'ocean-woo-popup',
							'init'  	=> 'ocean-woo-popup/ocean-woo-popup.php',
							'name' 		=> 'Ocean Woo Popup',
						),
					),
				),
			),

			'jewelry' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'jewelry/sample-data.xml',
				'theme_settings' 	=> $url . 'jewelry/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'jewelry/widgets.wie',
				'form_file'  		=> $url . 'jewelry/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '3',
				'elementor_width'  	=> '1260',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
						array(
							'slug' 		=> 'ocean-footer-callout',
							'init'  	=> 'ocean-footer-callout/ocean-footer-callout.php',
							'name' 		=> 'Ocean Footer Callout',
						),
						array(
							'slug' 		=> 'ocean-woo-popup',
							'init'  	=> 'ocean-woo-popup/ocean-woo-popup.php',
							'name' 		=> 'Ocean Woo Popup',
						),
					),
				),
			),

			'shoes' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'shoes/sample-data.xml',
				'theme_settings' 	=> $url . 'shoes/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'shoes/widgets.wie',
				'form_file'  		=> $url . 'shoes/form.json',
				'home_title'  		=> 'Home',
				'elementor_width'  	=> '1320',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '316',
				'woo_crop_width'  	=> '4',
				'woo_crop_height' 	=> '5',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-footer-callout',
							'init'  	=> 'ocean-footer-callout/ocean-footer-callout.php',
							'name' 		=> 'Ocean Footer Callout',
						),
						array(
							'slug' 		=> 'ocean-popup-login',
							'init'  	=> 'ocean-popup-login/ocean-popup-login.php',
							'name' 		=> 'Ocean Popup Login',
						),
					),
				),
			),

			'flowers' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'flowers/sample-data.xml',
				'theme_settings' 	=> $url . 'flowers/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'flowers/widgets.wie',
				'form_file'  		=> $url . 'flowers/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '3',
				'elementor_width'  	=> '1220',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '478',
				'woo_thumb_size' 	=> '294',
				'woo_crop_width'  	=> '4',
				'woo_crop_height' 	=> '5',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'garden' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'garden/sample-data.xml',
				'theme_settings' 	=> $url . 'garden/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'garden/widgets.wie',
				'form_file'  		=> $url . 'garden/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '3',
				'elementor_width'  	=> '1220',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '441',
				'woo_thumb_size' 	=> '270',
				'woo_crop_width'  	=> '4',
				'woo_crop_height' 	=> '5',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
						array(
							'slug' 		=> 'ocean-footer-callout',
							'init'  	=> 'ocean-footer-callout/ocean-footer-callout.php',
							'name' 		=> 'Ocean Footer Callout',
						),
					),
				),
			),

			'service' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'service/sample-data.xml',
				'theme_settings' 	=> $url . 'service/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'service/widgets.wie',
				'form_file'  		=> $url . 'service/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '9',
				'elementor_width'  	=> '1220',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '441',
				'woo_thumb_size' 	=> '270',
				'woo_crop_width'  	=> '4',
				'woo_crop_height' 	=> '5',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
						array(
							'slug' 		=> 'ocean-popup-login',
							'init'  	=> 'ocean-popup-login/ocean-popup-login.php',
							'name' 		=> 'Ocean Popup Login',
						),
						array(
							'slug' 		=> 'ocean-footer-callout',
							'init'  	=> 'ocean-footer-callout/ocean-footer-callout.php',
							'name' 		=> 'Ocean Footer Callout',
						),
					),
				),
			),

			'style' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'style/sample-data.xml',
				'theme_settings' 	=> $url . 'style/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'style/widgets.wie',
				'form_file'  		=> $url . 'style/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '7',
				'elementor_width'  	=> '1220',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '441',
				'woo_thumb_size' 	=> '270',
				'woo_crop_width'  	=> '4',
				'woo_crop_height' 	=> '5',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
						array(
							'slug' 		=> 'ocean-popup-login',
							'init'  	=> 'ocean-popup-login/ocean-popup-login.php',
							'name' 		=> 'Ocean Popup Login',
						),
						array(
							'slug' 		=> 'ocean-woo-popup',
							'init'  	=> 'ocean-woo-popup/ocean-woo-popup.php',
							'name' 		=> 'Ocean Woo Popup',
						),
					),
				),
			),

			'electronic' => array(
				'categories'        => array( 'Coming Soon' ),
				'xml_file'     		=> $url . 'electronic/sample-data.xml',
				'theme_settings' 	=> $url . 'electronic/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'electronic/widgets.wie',
				'form_file'  		=> $url . 'electronic/form.json',
				'home_title'  		=> 'Home',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'fashion' => array(
				'categories'        => array( 'Coming Soon' ),
				'xml_file'     		=> $url . 'fashion/sample-data.xml',
				'theme_settings' 	=> $url . 'fashion/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'fashion/widgets.wie',
				'form_file'  		=> $url . 'fashion/form.json',
				'home_title'  		=> 'Home',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'food' => array(
				'categories'        => array( 'Coming Soon' ),
				'xml_file'     		=> $url . 'food/sample-data.xml',
				'theme_settings' 	=> $url . 'food/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'food/widgets.wie',
				'form_file'  		=> $url . 'food/form.json',
				'home_title'  		=> 'Home',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'gaming' => array(
				'categories'        => array( 'Coming Soon' ),
				'xml_file'     		=> $url . 'gaming/sample-data.xml',
				'theme_settings' 	=> $url . 'gaming/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'gaming/widgets.wie',
				'form_file'  		=> $url . 'gaming/form.json',
				'home_title'  		=> 'Home',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'pink' => array(
				'categories'        => array( 'Coming Soon' ),
				'xml_file'     		=> $url . 'pink/sample-data.xml',
				'theme_settings' 	=> $url . 'pink/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'pink/widgets.wie',
				'form_file'  		=> $url . 'pink/form.json',
				'home_title'  		=> 'Home',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'maria' => array(
				'categories'  		=> array( 'Blog', 'One Page' ),
				'xml_file'     		=> $url . 'maria/sample-data.xml',
				'theme_settings' 	=> $url . 'maria/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'maria/widgets.wie',
				'form_file'  		=> $url . 'maria/form.json',
				'home_title'  		=> '',
				'blog_title'  		=> 'Home',
				'posts_to_show'  	=> '7',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'ocean-stick-anything',
							'init'  	=> 'ocean-stick-anything/ocean-stick-anything.php',
							'name'  	=> 'Ocean Stick Anything',
						),
					),
				),
			),

			'photos' => array(
				'categories'  		=> array( 'Business', 'Corporate' ),
				'xml_file'     		=> $url . 'photos/sample-data.xml',
				'theme_settings' 	=> $url . 'photos/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'photos/widgets.wie',
				'form_file'  		=> $url . 'photos/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '7',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
				),
			),

			'architect' => array(
				'categories'  		=> array( 'Business' ),
				'xml_file'     		=> $url . 'architect/sample-data.xml',
				'theme_settings' 	=> $url . 'architect/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'architect/widgets.wie',
				'form_file'  		=> $url . 'architect/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '3',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),
			
			'blogger' => array(
				'categories'  		=> array( 'Blog' ),
				'xml_file'     		=> $url . 'blogger/sample-data.xml',
				'theme_settings' 	=> $url . 'blogger/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'blogger/widgets.wie',
				'form_file'  		=> $url . 'blogger/form.json',
				'home_title'  		=> '',
				'blog_title'  		=> 'Home',
				'posts_to_show'  	=> '12',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
					),
				),
			),
			
			'coach' => array(
				'categories'  		=> array( 'Business', 'Sport', 'One Page' ),
				'xml_file'     		=> $url . 'coach/sample-data.xml',
				'theme_settings' 	=> $url . 'coach/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'coach/widgets.wie',
				'form_file'  		=> $url . 'coach/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '3',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
					),
				),
			),
			
			'gym' => array(
				'categories'  		=> array( 'Business', 'Sport' ),
				'xml_file'     		=> $url . 'gym/sample-data.xml',
				'theme_settings' 	=> $url . 'gym/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'gym/widgets.wie',
				'form_file'  		=> $url . 'gym/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'News',
				'posts_to_show'  	=> '3',
				'elementor_width'  	=> '1100',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),
			
			'lawyer' => array(
				'categories'  		=> array( 'Business' ),
				'xml_file'     		=> $url . 'lawyer/sample-data.xml',
				'theme_settings' 	=> $url . 'lawyer/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'lawyer/widgets.wie',
				'form_file'  		=> $url . 'lawyer/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '3',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-side-panel',
							'init'  	=> 'ocean-side-panel/ocean-side-panel.php',
							'name' 		=> 'Ocean Side Panel',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),
			
			'megagym' => array(
				'categories'  		=> array( 'Business', 'Sport', 'One Page' ),
				'xml_file'     		=> $url . 'megagym/sample-data.xml',
				'theme_settings' 	=> $url . 'megagym/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'megagym/widgets.wie',
				'form_file'  		=> $url . 'megagym/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '3',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),
			
			'personal' => array(
				'categories'  		=> array( 'Blog' ),
				'xml_file'     		=> $url . 'personal/sample-data.xml',
				'theme_settings' 	=> $url . 'personal/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'personal/widgets.wie',
				'form_file'  		=> $url . 'personal/form.json',
				'home_title'  		=> '',
				'blog_title'  		=> 'Home',
				'posts_to_show'  	=> '3',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-posts-slider',
							'init'  	=> 'ocean-posts-slider/ocean-posts-slider.php',
							'name'  	=> 'Ocean Posts Slider',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
					),
				),
			),
			
			'simple' => array(
				'categories'  		=> array( 'eCommerce' ),
				'xml_file'     		=> $url . 'simple/sample-data.xml',
				'theme_settings' 	=> $url . 'simple/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'simple/widgets.wie',
				'form_file'  		=> $url . 'simple/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '3',
				'elementor_width'  	=> '1100',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '454',
				'woo_thumb_size' 	=> '348',
				'woo_crop_width'  	=> '3',
				'woo_crop_height' 	=> '4',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-modal-window',
							'init'  	=> 'ocean-modal-window/ocean-modal-window.php',
							'name'  	=> 'Ocean Modal Window',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
						array(
							'slug' 		=> 'ocean-footer-callout',
							'init'  	=> 'ocean-footer-callout/ocean-footer-callout.php',
							'name' 		=> 'Ocean Footer Callout',
						),
						array(
							'slug' 		=> 'ocean-sticky-footer',
							'init'  	=> 'ocean-sticky-footer/ocean-sticky-footer.php',
							'name' 		=> 'Ocean Sticky Footer',
						),
					),
				),
			),
			
			'store' => array(
				'categories'  		=> array( 'eCommerce' ),
				'xml_file'     		=> $url . 'store/sample-data.xml',
				'theme_settings' 	=> $url . 'store/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'store/widgets.wie',
				'form_file'  		=> $url . 'store/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1220',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '504',
				'woo_thumb_size' 	=> '265',
				'woo_crop_width'  	=> '4',
				'woo_crop_height' 	=> '5',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-custom-sidebar',
							'init'  	=> 'ocean-custom-sidebar/ocean-custom-sidebar.php',
							'name'  	=> 'Ocean Custom Sidebar',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'ocean-stick-anything',
							'init'  	=> 'ocean-stick-anything/ocean-stick-anything.php',
							'name'  	=> 'Ocean Stick Anything',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
						array(
							'slug' 		=> 'ocean-footer-callout',
							'init'  	=> 'ocean-footer-callout/ocean-footer-callout.php',
							'name' 		=> 'Ocean Footer Callout',
						),
					),
				),
			),
			
			'stylish' => array(
				'categories'  		=> array( 'Business' ),
				'xml_file'     		=> $url . 'stylish/sample-data.xml',
				'theme_settings' 	=> $url . 'stylish/oceanwp-export.dat',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '12',
				'elementor_width'  	=> '1420',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
					),
				),
			),
			
			'travel' => array(
				'categories'  		=> array( 'Blog' ),
				'xml_file'     		=> $url . 'travel/sample-data.xml',
				'theme_settings' 	=> $url . 'travel/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'travel/widgets.wie',
				'form_file'  		=> $url . 'travel/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '4',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-custom-sidebar',
							'init'  	=> 'ocean-custom-sidebar/ocean-custom-sidebar.php',
							'name'  	=> 'Ocean Custom Sidebar',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),
			
			'lingerie' => array(
				'categories'  		=> array( 'eCommerce' ),
				'xml_file'     		=> $url . 'lingerie/sample-data.xml',
				'theme_settings' 	=> $url . 'lingerie/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'lingerie/widgets.wie',
				'form_file'  		=> $url . 'lingerie/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '3',
				'elementor_width'  	=> '1220',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '433',
				'woo_thumb_size' 	=> '265',
				'woo_crop_width'  	=> '4',
				'woo_crop_height' 	=> '5',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
						array(
							'slug' 		=> 'ocean-footer-callout',
							'init'  	=> 'ocean-footer-callout/ocean-footer-callout.php',
							'name' 		=> 'Ocean Footer Callout',
						),
						array(
							'slug' 		=> 'ocean-woo-popup',
							'init'  	=> 'ocean-woo-popup/ocean-woo-popup.php',
							'name' 		=> 'Ocean Woo Popup',
						),
					),
				),
			),
			
			'yoga' => array(
				'categories'  		=> array( 'Business', 'Sport' ),
				'xml_file'     		=> $url . 'yoga/sample-data.xml',
				'theme_settings' 	=> $url . 'yoga/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'yoga/widgets.wie',
				'form_file'  		=> $url . 'yoga/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '3',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
					),
				),
			),

			'hdelicious' => array(
				'categories'        => array( 'Business', 'One Page' ),
				'xml_file'     		=> $url . 'hdelicious/sample-data.xml',
				'theme_settings' 	=> $url . 'hdelicious/oceanwp-export.dat',
				'form_file'  		=> $url . 'hdelicious/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'leadin',
							'init'  	=> 'leadin/leadin.php',
							'name'  	=> 'HubSpot',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'hdigital' => array(
				'categories'        => array( 'Corporate' ),
				'xml_file'     		=> $url . 'hdigital/sample-data.xml',
				'theme_settings' 	=> $url . 'hdigital/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'hdigital/widgets.wie',
				'form_file'  		=> $url . 'hdigital/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '7',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'leadin',
							'init'  	=> 'leadin/leadin.php',
							'name'  	=> 'HubSpot',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'hdesigner' => array(
				'categories'        => array( 'Corporate' ),
				'xml_file'     		=> $url . 'hdesigner/sample-data.xml',
				'theme_settings' 	=> $url . 'hdesigner/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'hdesigner/widgets.wie',
				'form_file'  		=> $url . 'hdesigner/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '7',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'leadin',
							'init'  	=> 'leadin/leadin.php',
							'name'  	=> 'HubSpot',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
					),
				),
			),

			'hagency' => array(
				'categories'        => array( 'Corporate' ),
				'xml_file'     		=> $url . 'hagency/sample-data.xml',
				'theme_settings' 	=> $url . 'hagency/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'hagency/widgets.wie',
				'form_file'  		=> $url . 'hagency/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '7',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'leadin',
							'init'  	=> 'leadin/leadin.php',
							'name'  	=> 'HubSpot',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
						array(
							'slug' 		=> 'ocean-portfolio',
							'init'  	=> 'ocean-portfolio/ocean-portfolio.php',
							'name' 		=> 'Ocean Portfolio',
						),
					),
				),
			),

			'hcorporate' => array(
				'categories'        => array( 'Corporate' ),
				'xml_file'     		=> $url . 'hcorporate/sample-data.xml',
				'theme_settings' 	=> $url . 'hcorporate/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'hcorporate/widgets.wie',
				'form_file'  		=> $url . 'hcorporate/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '7',
				'elementor_width'  	=> '1140',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'elementor',
							'init'  	=> 'elementor/elementor.php',
							'name'  	=> 'Elementor',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'leadin',
							'init'  	=> 'leadin/leadin.php',
							'name'  	=> 'HubSpot',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-elementor-widgets',
							'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
							'name' 		=> 'Ocean Elementor Widgets',
						),
						array(
							'slug' 		=> 'ocean-portfolio',
							'init'  	=> 'ocean-portfolio/ocean-portfolio.php',
							'name' 		=> 'Ocean Portfolio',
						),
						array(
							'slug' 		=> 'ocean-popup-login',
							'init'  	=> 'ocean-popup-login/ocean-popup-login.php',
							'name' 		=> 'Ocean Popup Login',
						),
					),
				),
			),
		);

		$data['gutenberg'] = array(
			'gsushi' => array(
				'categories'        => array( 'Business' ),
				'xml_file'     		=> $url . 'gsushi/sample-data.xml',
				'theme_settings' 	=> $url . 'gsushi/oceanwp-export.dat',
				'form_file'  		=> $url . 'gsushi/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-gutenberg-blocks',
							'init'  	=> 'ocean-gutenberg-blocks/ocean-gutenberg-blocks.php',
							'name' 		=> 'Ocean Gutenberg Blocks',
						),
					),
				),
			),

			'gnutritionist' => array(
				'categories'        => array( 'Business', 'One Page' ),
				'xml_file'     		=> $url . 'gnutritionist/sample-data.xml',
				'theme_settings' 	=> $url . 'gnutritionist/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'gnutritionist/widgets.wie',
				'form_file'  		=> $url . 'gnutritionist/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-gutenberg-blocks',
							'init'  	=> 'ocean-gutenberg-blocks/ocean-gutenberg-blocks.php',
							'name' 		=> 'Ocean Gutenberg Blocks',
						),
					),
				),
			),

			'gclothes' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'gclothes/sample-data.xml',
				'theme_settings' 	=> $url . 'gclothes/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'gclothes/widgets.wie',
				'form_file'  		=> $url . 'gclothes/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '7',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-gutenberg-blocks',
							'init'  	=> 'ocean-gutenberg-blocks/ocean-gutenberg-blocks.php',
							'name' 		=> 'Ocean Gutenberg Blocks',
						),
					),
				),
			),

			'ggames' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'ggames/sample-data.xml',
				'theme_settings' 	=> $url . 'ggames/oceanwp-export.dat',
				'form_file'  		=> $url . 'ggames/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-gutenberg-blocks',
							'init'  	=> 'ocean-gutenberg-blocks/ocean-gutenberg-blocks.php',
							'name' 		=> 'Ocean Gutenberg Blocks',
						),
					),
				),
			),

			'gbarbecue' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'gbarbecue/sample-data.xml',
				'theme_settings' 	=> $url . 'gbarbecue/oceanwp-export.dat',
				'form_file'  		=> $url . 'gbarbecue/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-gutenberg-blocks',
							'init'  	=> 'ocean-gutenberg-blocks/ocean-gutenberg-blocks.php',
							'name' 		=> 'Ocean Gutenberg Blocks',
						),
					),
				),
			),

			'gmynails' => array(
				'categories'        => array( 'Business', 'One Page' ),
				'xml_file'     		=> $url . 'gmynails/sample-data.xml',
				'theme_settings' 	=> $url . 'gmynails/oceanwp-export.dat',
				'form_file'  		=> $url . 'gmynails/form.json',
				'home_title'  		=> 'Home',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-gutenberg-blocks',
							'init'  	=> 'ocean-gutenberg-blocks/ocean-gutenberg-blocks.php',
							'name' 		=> 'Ocean Gutenberg Blocks',
						),
					),
				),
			),

			'gtoys' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'gtoys/sample-data.xml',
				'theme_settings' 	=> $url . 'gtoys/oceanwp-export.dat',
				'widgets_file'  	=> $url . 'gtoys/widgets.wie',
				'form_file'  		=> $url . 'gtoys/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '7',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-gutenberg-blocks',
							'init'  	=> 'ocean-gutenberg-blocks/ocean-gutenberg-blocks.php',
							'name' 		=> 'Ocean Gutenberg Blocks',
						),
					),
				),
			),

			'gpet' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'gpet/sample-data.xml',
				'theme_settings' 	=> $url . 'gpet/oceanwp-export.dat',
				'form_file'  		=> $url . 'gpet/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-gutenberg-blocks',
							'init'  	=> 'ocean-gutenberg-blocks/ocean-gutenberg-blocks.php',
							'name' 		=> 'Ocean Gutenberg Blocks',
						),
					),
				),
			),

			'gearphone' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'gearphone/sample-data.xml',
				'theme_settings' 	=> $url . 'gearphone/oceanwp-export.dat',
				'form_file'  		=> $url . 'gearphone/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-gutenberg-blocks',
							'init'  	=> 'ocean-gutenberg-blocks/ocean-gutenberg-blocks.php',
							'name' 		=> 'Ocean Gutenberg Blocks',
						),
					),
				),
			),

			'gcap' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'gcap/sample-data.xml',
				'theme_settings' 	=> $url . 'gcap/oceanwp-export.dat',
				'form_file'  		=> $url . 'gcap/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-gutenberg-blocks',
							'init'  	=> 'ocean-gutenberg-blocks/ocean-gutenberg-blocks.php',
							'name' 		=> 'Ocean Gutenberg Blocks',
						),
					),
				),
			),

			'gcroquette' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'gcroquette/sample-data.xml',
				'theme_settings' 	=> $url . 'gcroquette/oceanwp-export.dat',
				'form_file'  		=> $url . 'gcroquette/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-gutenberg-blocks',
							'init'  	=> 'ocean-gutenberg-blocks/ocean-gutenberg-blocks.php',
							'name' 		=> 'Ocean Gutenberg Blocks',
						),
					),
				),
			),

			'gpumps' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'gpumps/sample-data.xml',
				'theme_settings' 	=> $url . 'gpumps/oceanwp-export.dat',
				'form_file'  		=> $url . 'gpumps/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-gutenberg-blocks',
							'init'  	=> 'ocean-gutenberg-blocks/ocean-gutenberg-blocks.php',
							'name' 		=> 'Ocean Gutenberg Blocks',
						),
					),
				),
			),

			'gbackpack' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'gbackpack/sample-data.xml',
				'theme_settings' 	=> $url . 'gbackpack/oceanwp-export.dat',
				'form_file'  		=> $url . 'gbackpack/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-gutenberg-blocks',
							'init'  	=> 'ocean-gutenberg-blocks/ocean-gutenberg-blocks.php',
							'name' 		=> 'Ocean Gutenberg Blocks',
						),
					),
				),
			),

			'gessential' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'gessential/sample-data.xml',
				'theme_settings' 	=> $url . 'gessential/oceanwp-export.dat',
				'form_file'  		=> $url . 'gessential/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-gutenberg-blocks',
							'init'  	=> 'ocean-gutenberg-blocks/ocean-gutenberg-blocks.php',
							'name' 		=> 'Ocean Gutenberg Blocks',
						),
					),
				),
			),

			'gbasket' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'gbasket/sample-data.xml',
				'theme_settings' 	=> $url . 'gbasket/oceanwp-export.dat',
				'form_file'  		=> $url . 'gbasket/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-gutenberg-blocks',
							'init'  	=> 'ocean-gutenberg-blocks/ocean-gutenberg-blocks.php',
							'name' 		=> 'Ocean Gutenberg Blocks',
						),
					),
				),
			),

			'gout' => array(
				'categories'        => array( 'Business', 'One Page' ),
				'xml_file'     		=> $url . 'gout/sample-data.xml',
				'theme_settings' 	=> $url . 'gout/oceanwp-export.dat',
				'form_file'  		=> $url . 'gout/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'News',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-gutenberg-blocks',
							'init'  	=> 'ocean-gutenberg-blocks/ocean-gutenberg-blocks.php',
							'name' 		=> 'Ocean Gutenberg Blocks',
						),
					),
				),
			),

			'gagenda' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'gagenda/sample-data.xml',
				'theme_settings' 	=> $url . 'gagenda/oceanwp-export.dat',
				'form_file'  		=> $url . 'gagenda/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-gutenberg-blocks',
							'init'  	=> 'ocean-gutenberg-blocks/ocean-gutenberg-blocks.php',
							'name' 		=> 'Ocean Gutenberg Blocks',
						),
					),
				),
			),

			'gteddy' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'gteddy/sample-data.xml',
				'theme_settings' 	=> $url . 'gteddy/oceanwp-export.dat',
				'form_file'  		=> $url . 'gteddy/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-gutenberg-blocks',
							'init'  	=> 'ocean-gutenberg-blocks/ocean-gutenberg-blocks.php',
							'name' 		=> 'Ocean Gutenberg Blocks',
						),
					),
				),
			),

			'grings' => array(
				'categories'        => array( 'eCommerce' ),
				'xml_file'     		=> $url . 'grings/sample-data.xml',
				'theme_settings' 	=> $url . 'grings/oceanwp-export.dat',
				'form_file'  		=> $url . 'grings/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'Blog',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1140',
				'is_shop'  			=> true,
				'woo_image_size'  	=> '600',
				'woo_thumb_size' 	=> '300',
				'woo_crop_width'  	=> '1',
				'woo_crop_height' 	=> '1',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-product-sharing',
							'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
							'name'  	=> 'Ocean Product Sharing',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
						array(
							'slug'  	=> 'woocommerce',
							'init'  	=> 'woocommerce/woocommerce.php',
							'name'  	=> 'WooCommerce',
						),
						array(
							'slug'  	=> 'ti-woocommerce-wishlist',
							'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
							'name'  	=> 'WooCommerce Wishlist',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-gutenberg-blocks',
							'init'  	=> 'ocean-gutenberg-blocks/ocean-gutenberg-blocks.php',
							'name' 		=> 'Ocean Gutenberg Blocks',
						),
					),
				),
			),

			'gcoaching' => array(
				'categories'        => array( 'Business', 'One Page' ),
				'xml_file'     		=> $url . 'gcoaching/sample-data.xml',
				'theme_settings' 	=> $url . 'gcoaching/oceanwp-export.dat',
				'form_file'  		=> $url . 'gcoaching/form.json',
				'home_title'  		=> 'Home',
				'blog_title'  		=> 'News',
				'posts_to_show'  	=> '6',
				'elementor_width'  	=> '1220',
				'required_plugins'  => array(
					'free' => array(
						array(
							'slug'  	=> 'ocean-extra',
							'init'  	=> 'ocean-extra/ocean-extra.php',
							'name'  	=> 'Ocean Extra',
						),
						array(
							'slug'  	=> 'ocean-social-sharing',
							'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
							'name'  	=> 'Ocean Social Sharing',
						),
						array(
							'slug'  	=> 'wpforms-lite',
							'init'  	=> 'wpforms-lite/wpforms.php',
							'name'  	=> 'WPForms',
						),
					),
					'premium' => array(
						array(
							'slug' 		=> 'ocean-sticky-header',
							'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
							'name' 		=> 'Ocean Sticky Header',
						),
						array(
							'slug' 		=> 'ocean-gutenberg-blocks',
							'init'  	=> 'ocean-gutenberg-blocks/ocean-gutenberg-blocks.php',
							'name' 		=> 'Ocean Gutenberg Blocks',
						),
					),
				),
			),
		);

		// Return
		return $data;

	}

} // End Class

#--------------------------------------------------------------------------------
#region Freemius
#--------------------------------------------------------------------------------

if ( ! function_exists( 'ocean_pro_demos_fs' ) ) {
    // Create a helper function for easy SDK access.
    function ocean_pro_demos_fs() {
        global $ocean_pro_demos_fs;

        if ( ! isset( $ocean_pro_demos_fs ) ) {
            $ocean_pro_demos_fs = OceanWP_EDD_Addon_Migration::instance( 'ocean_pro_demos_fs' )->init_sdk( array(
                'id'         => '3797',
                'slug'       => 'demos',
                'public_key' => 'pk_a34c58ab5e7159d54e88175c1c03f',
				'bundle_id' => '3767',
				'bundle_public_key' => 'pk_c334eb1ae413deac41e30bf00b9dc',
				'bundle_license_auto_activation' => true,

            ) );

            if ( $ocean_pro_demos_fs->can_use_premium_code__premium_only() ) {
                Ocean_Pro_Demos::instance()->init();
            }
        }

        return $ocean_pro_demos_fs;
    }

    function ocean_pro_demos_fs_addon_init() {
        if ( class_exists( 'Ocean_Extra' ) ) {
            OceanWP_EDD_Addon_Migration::instance( 'ocean_pro_demos_fs' )->init();
        }
    }

    if ( 0 == did_action( 'owp_fs_loaded' ) ) {
        // Init add-on only after parent theme was loaded.
        add_action( 'owp_fs_loaded', 'ocean_pro_demos_fs_addon_init', 15 );
    } else {
        if ( class_exists( 'Ocean_Extra' ) ) {
            /**
             * This makes sure that if the theme was already loaded
             * before the plugin, it will run Freemius right away.
             *
             * This is crucial for the plugin's activation hook.
             */
            ocean_pro_demos_fs_addon_init();
        }
    }

    function ocean_pro_demos_fs_try_migrate() {
        OceanWP_EDD_Addon_Migration::instance( 'ocean_pro_demos_fs' )->try_migrate_addon(
            '19721',
            'Ocean_Pro_Demos',
            'Pro Demos'
        );
    }
}

#endregion
