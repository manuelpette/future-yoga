<?php

class Demo_Api_Images_Admin_Page {

	public function __construct() {
		 add_action( 'admin_menu', array( $this, 'create_page' ) );
	}

	function create_page() {
		$api_images_settings_page = add_submenu_page(
			'upload.php',
			DEMO_API_IMAGES_TITLE,
			DEMO_API_IMAGES_TITLE,
			'upload_files',
			DEMO_API_IMAGES_NAME,
			array( $this, 'settings_page' )
		);
		add_action( 'load-' . $api_images_settings_page, array( $this, 'load_scripts' ) ); // Add admin scripts.
	}

	function settings_page() {

		if ( ! Demo_Api_Images::activeOneFromProviders() ) {
			echo '<p><b>None of the providers are active</b></p>';
		}

		echo '<div class="demo-api-images-container"></div>';
	}

	function load_scripts() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	function enqueue_scripts() {
		self::images_scripts();
	}

	public static function images_scripts() {
		if ( Demo_Api_Images::activeOneFromProviders() ) {
			wp_enqueue_style( 'demo-admin', DEMO_API_IMAGES_URL . 'assets/css/demo.css', '', DEMO_API_IMAGES_ASSETS_VERSION );
			wp_enqueue_script( 'jquery', true, '', DEMO_API_IMAGES_ASSETS_VERSION, false );
			wp_enqueue_script( 'jquery-form', true, '', DEMO_API_IMAGES_ASSETS_VERSION, false );

			wp_enqueue_script( 'demo-admin-page', DEMO_API_IMAGES_URL . 'assets/js/demo-admin-page.js', '', DEMO_API_IMAGES_ASSETS_VERSION, true );
			Demo_Api_Images::demo_api_images_set_localize();
		}
	}
}

new Demo_Api_Images_Admin_Page();
