<?php

class Demo_Routes {


	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'download_image' ) );
	}

	function download_image() {
		$my_namespace = 'demo-api-images';
		$my_endpoint   = '/download';
		register_rest_route(
			$my_namespace,
			$my_endpoint,
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'demo_api_images_images_download' ),
				'permission_callback' => function () {
					return Demo_Api_Images::demo_api_images_set_has_access();
				},
			)
		);
	}

	function demo_api_images_images_download( WP_REST_Request $request ) {
		
		global $ocean_pro_demos_fs;
		if( ! empty( $ocean_pro_demos_fs ) ) {		
			$plugin_id = $ocean_pro_demos_fs->get_site()->id;
			$_license = $ocean_pro_demos_fs->_get_license();
			$license_id = empty( $_license->parent_license_id ) ? '' : $_license->parent_license_id;
			$is_registered = $ocean_pro_demos_fs->is_registered();
			$is_tracking_allowed = $ocean_pro_demos_fs->is_tracking_allowed();
			if( empty( $plugin_id ) || empty( $license_id ) || empty( $is_registered ) || empty( $is_tracking_allowed ) ) {
				$this->forbiddenAccessResponse();
			}
		} else {
			$this->forbiddenAccessResponse();
		}


		if ( ! Demo_Api_Images::demo_api_images_set_has_access() ) {
			// Exit if not allowed.
			$this->forbiddenAccessResponse();
		}

		// Core WP includes.
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		// Get JSON Data.
		$data = $request->get_body_params(); // Get contents of request body.

		if ( $data ) {
			$title = sanitize_text_field( $data['id'] ); // Title.
			$alt   = sanitize_text_field( $data['alt'] ); // Alt text.

			$id       = $data['id']; // Image ID.
			$provider = $data['provider_id'];

			$image_width = 0;

			switch ( $provider ) {
				case 'flaticon':
					$filename = basename( $data['image_url'] );

					break;
				case 'freepik':
					$filename = basename( $data['image_url'] );
					$image_width = get_option( 'owp_freepik_image_width' );
					$image_width = $image_width == 'custom' ? get_option( 'owp_freepik_image_width_custom' ) : $image_width;

					break;
			}

			$data['action'] = 'download';
			$data['plugin_id'] = $plugin_id;
			$data['wp_site_url'] = get_site_url();
			$data['license_id'] = $license_id;
			$data['is_registered'] = $is_registered;
			$data['is_tracking_allowed'] = $is_tracking_allowed;
			$data['image_width'] = $image_width;


			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_URL, DEMO_API_IMAGES_SERVER_URL );

			curl_setopt( $ch, CURLOPT_POST, 1 );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $data ) );

			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
			$response_data = curl_exec( $ch );
			curl_close( $ch );

			$response_data = json_decode( $response_data, true );

			if ( $response_data['success'] == true && ! empty( $response_data['body'] ) && ! empty( $response_data['type'] ) ) {
				// Upload remote file.
				$mirror = wp_upload_bits( $filename, null, base64_decode( $response_data['body'] ) );

				// Build Attachment Data Array.
				$attachment = array(
					'post_title'     => $title,
					'post_content'   => '',
					'post_status'    => 'inherit',
					'post_mime_type' => $response_data['type'],
				);

				// Insert as attachment.
				$image_id = wp_insert_attachment( $attachment, $mirror['file'] );

				// Add Alt Text as Post Meta.
				update_post_meta( $image_id, '_wp_attachment_image_alt', $alt );

				// Generate Metadata.
				$attach_data = wp_generate_attachment_metadata( $image_id, $mirror['file'] );
				wp_update_attachment_metadata( $image_id, $attach_data );

				// Success.
				$response = array(
					'success'    => true,
					'msg'        => __( 'Image successfully uploaded to the media library!', 'ocean-pro-demos' ),
					'id'         => $id,
					'attachment' => array(
						'id'  => $image_id,
						'url' => wp_get_attachment_url( $image_id ),
						'alt' => $alt,
					),
				);

				wp_send_json( $response );
			} else {
				wp_send_json( $response_data );
			}
		} else {

			$response = array(
				'success'    => false,
				'msg'        => __( 'There was an error getting image details from the request, please try again.', 'ocean-pro-demos' ),
				'id'         => '',
				'attachment' => '',
				'url'        => '',
			);

			wp_send_json( $response );
		}
	}

	protected function getFlaticonBearerToken() {
		$ch = curl_init();

		curl_setopt( $ch, CURLOPT_URL, 'https://api.flaticon.com/v3/app/authentication' );
		curl_setopt( $ch, CURLOPT_POST, 1 );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );

		$post_params = array(
			'apikey' => base64_decode( DEMO_API_IMAGES_FLATICON_APP_ID ),
		);

		curl_setopt(
			$ch,
			CURLOPT_POSTFIELDS,
			http_build_query( $post_params )
		);

		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );

		$api_data = curl_exec( $ch );

		curl_close( $ch );

		$api_data = json_decode( $api_data, true );

		$api_data['data']['expires'] = current_time( 'timestamp' ) + 3000;

		update_option( 'demo_api_images_flaticon_bearer_token', $api_data['data'] );

		return $api_data['data'];
	}


	protected static function remote_file_exists( $url, $args ) {
		if ( empty( $args ) ) {
			$response = wp_remote_head( $url );
		} else {
			$response = wp_remote_head( $url, $args );
		}

		return 200 === wp_remote_retrieve_response_code( $response );
	}

	protected function forbiddenAccessResponse() {
		$response = array(
			'success' => false,
			'msg'     => __( 'You do not have sufficient access to upload images with API Images.', 'ocean-pro-demos' ),
		);
		wp_send_json( $response );
	}
}

new Demo_Routes();
