<?php

class Demo_Api_Images {


	public function __construct() {
		 $this->constants();

		include_once DEMO_API_IMAGES_PATH . '/classes/integration.php';

		$demo_api_images_active = get_option( 'owp_api_images_integration' );
		if ( '1' === $demo_api_images_active ) {
			include_once DEMO_API_IMAGES_PATH . '/classes/media-page.php';
			include_once DEMO_API_IMAGES_PATH . '/classes/routes.php';

			add_action( 'wp_enqueue_media', array( $this, 'demo_api_images_wp_media_scripts' ) );
		}
	}

	private function constants() {
		if ( ! defined( 'DEMO_API_IMAGES_NAME' ) ) {
			define( 'DEMO_API_IMAGES_NAME', 'ocean-images' );
		}
		if ( ! defined( 'DEMO_API_IMAGES_TITLE' ) ) {
			define( 'DEMO_API_IMAGES_TITLE', 'Browse Ocean Images' );
		}
		if ( ! defined( 'DEMO_API_IMAGES_SERVER_URL' ) ) {
			define( 'DEMO_API_IMAGES_SERVER_URL', 'https://imgsearch.oceanwp.org/api.php' );
		}
		if ( ! defined( 'DEMO_API_IMAGES_PATH' ) ) {
			define( 'DEMO_API_IMAGES_PATH', plugin_dir_path( __FILE__ ) );
		}
		if ( ! defined( 'DEMO_API_IMAGES_URL' ) ) {
			define( 'DEMO_API_IMAGES_URL', plugins_url( '/', __FILE__ ) );
		}
		if ( ! defined( 'DEMO_API_IMAGES_ASSETS_VERSION' ) ) {
			define( 'DEMO_API_IMAGES_ASSETS_VERSION', time() );
		}
	}

	public function demo_api_images_wp_media_scripts() {
		global $current_screen;
		if ( $current_screen->id != 'media_page_demo' && self::demo_api_images_set_has_access() ) {
			wp_enqueue_style(
				'admin-demo',
				DEMO_API_IMAGES_URL . 'assets/css/demo.css',
				'',
				DEMO_API_IMAGES_ASSETS_VERSION
			);

			if ( self::activeOneFromProviders() ) {
				wp_enqueue_script(
					'demo-media',
					DEMO_API_IMAGES_URL . 'assets/js/demo-media.js',
					array(),
					DEMO_API_IMAGES_ASSETS_VERSION,
					true
				);
				self::demo_api_images_set_localize( 'demo-media' );
			}
		}
	}

	public static function demo_api_images_set_localize( $script = 'demo-admin-page' ) {
		$active_providers = array();

		if ( '1' === get_option( 'owp_flaticon_integration' ) ) {
			$active_providers[] = 'flaticon';
		}
		if ( '1' === get_option( 'owp_freepik_integration' ) ) {
			$active_providers[] = 'freepik';
		}

		if ( ! empty( $active_providers ) ) {
			$localize_args = array(
				'demo'             => __( 'Ocean Images', 'ocean-pro-demos' ),

				'root'             => esc_url_raw( rest_url() ),
				'nonce'            => wp_create_nonce( 'wp_rest' ),

				'api_service_url'  => DEMO_API_IMAGES_SERVER_URL,

				'saving'           => __( 'Downloading image...', 'ocean-pro-demos' ),
				'loading'          => __( 'Loading...', 'ocean-pro-demos' ),
				'downloaded'       => __( 'Image was added to media library', 'ocean-pro-demos' ),
				'error_message'    => __( 'Error', 'ocean-pro-demos' ),
				'load_button_text' => __( 'Load more', 'ocean-pro-demos' ),

				'flaticon_active'  => ! empty( get_option( 'owp_flaticon_integration' ) ),
				'freepik_active'   => ! empty( get_option( 'owp_freepik_integration' ) ),
				'active_providers' => $active_providers,

				'error_upload'     => __( 'There was no response while attempting to the download image to your server. Check your server permission and max file upload size or try again', 'ocean-pro-demos' ),
			);

			global $ocean_pro_demos_fs;
			$plugin_id = '';
			$license_id = '';
			$is_registered = false;
			$is_tracking_allowed = false;
			if( ! empty( $ocean_pro_demos_fs ) ) {		
				$plugin_id = $ocean_pro_demos_fs->get_site()->id;
				$_license = $ocean_pro_demos_fs->_get_license();
   				$license_id = empty( $_license->parent_license_id ) ? '' : $_license->parent_license_id;
				$is_registered = $ocean_pro_demos_fs->is_registered();
				$is_tracking_allowed = $ocean_pro_demos_fs->is_tracking_allowed();
			}
			$localize_args['fs_plugin_id'] = $plugin_id;
			$localize_args['wp_site_url'] = get_site_url();
			$localize_args['fs_license_id'] = $license_id;
			$localize_args['fs_is_registered'] = $is_registered;
			$localize_args['fs_is_tracking_allowed'] = $is_tracking_allowed;

			wp_localize_script(
				$script,
				'demo_api_images_set_localize',
				$localize_args
			);
		}
	}

	public static function demo_api_images_set_has_access() {
		$access = false;
		if ( is_user_logged_in() && current_user_can( apply_filters( 'demo_api_images_user_role', 'upload_files' ) ) ) {
			$access = true;
		}
		return $access;
	}

	public static function activeOneFromProviders() {
		$owp_flaticon_integration = get_option( 'owp_flaticon_integration' );
		$owp_freepik_integration  = get_option( 'owp_freepik_integration' );
		if ( $owp_flaticon_integration == '1' || $owp_freepik_integration == '1' ) {
			return true;
		}
		return false;
	}
}

new Demo_Api_Images();
