<?php

/**
 * Scripts Panel
 *
 * @package Ocean_Extra
 * @category Core
 * @author OceanWP
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Start Class
class Ocean_PD_Theme_Panel {

	/**
	 * Start things up
	 */
	public function __construct() {

		// Add custom scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

		add_filter( 'oceanwp_theme_panel_pane_ocean_images_settings', array( $this, 'ocean_images_part' ) );
	}

	/**
	 * Admin Scripts.
	 */
	public static function admin_scripts( $hook ) {
		$current_screen = get_current_screen();
		// Only load scripts when needed
		if ( 'toplevel_page_oceanwp' != $current_screen->id ) {
			return;
		}

		// JS
		wp_enqueue_script( 'ocean-pd-scripts-themepanel', plugins_url( '/assets/js/theme-panel.min.js', __FILE__ ), DEMO_API_IMAGES_ASSETS_VERSION, true );
	}

	function ocean_images_part() {
		return DEMO_API_IMAGES_PATH . 'includes/themepanel/views/panes/ocean-images-settings.php';
	}

	public static function get_ocean_images_settings() {
		$settings = array();

		return apply_filters( 'ocean_integrations_settings', $settings );
	}

}

new Ocean_PD_Theme_Panel();
