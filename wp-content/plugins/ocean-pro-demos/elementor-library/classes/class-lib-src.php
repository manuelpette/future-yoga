<?php
/**
 * Library api class
 *
 * @package OceanLibrary
 * @author OceanWP
 */
namespace Ocean_Template_Library\Elementor;

use Elementor\TemplateLibrary\Source_Base;

defined( 'ABSPATH' ) || die();

class Ocean_Template_Library_Source extends Source_Base {

	/**
	 * Template library data cache
	 */
	const LIBRARY_CACHE_KEY = 'otl_library_cache';
	private static $CHECKER_URL = 'https://demos.oceanwp.org/elementor-library/check-license.php';

	/**
	 * Template info api url
	 *
	 */
	const API_TEMPLATES_INFO_URL = 'https://demos.oceanwp.org/elementor-library/templates-info.json';

	/**
	 * Template data api url
	 */
	const API_TEMPLATE_DATA_URL = 'https://demos.oceanwp.org/elementor-library/templates/';

	public function get_id() {
		return 'ocean-library';
	}

	public function get_title() {
		return __( 'Ocean Library', 'ocean-pro-demos' );
	}

	public function register_data() {}

	public function save_item( $template_data ) {
		return new \WP_Error( 'invalid_request', 'Cannot save template to a Ocean library' );
	}

	public function update_item( $new_data ) {
		return new \WP_Error( 'invalid_request', 'Cannot update template to a Ocean library' );
	}

	public function delete_template( $template_id ) {
		return new \WP_Error( 'invalid_request', 'Cannot delete template from a Ocean library' );
	}

	public function export_template( $template_id ) {
		return new \WP_Error( 'invalid_request', 'Cannot export template from a Ocean library' );
	}

	public function get_items( $args = [] ) {
		$library_data = self::get_library_data();
		$validated_license = $this->check_license();
		$templates = [];

		if ( ! empty( $library_data['templates'] ) ) {
			foreach ( $library_data['templates'] as $template_data ) {
				$tmp_template_data =  $this->prepare_template( $template_data );
				$tmp_template_data['isPlus'] = $validated_license ? false : $tmp_template_data['isPlus'];
				$templates[] = $tmp_template_data;
			}
		}

		return apply_filters( 'ocean_elementor_library_tags', $templates );
	}

	/**
	 * Check the License.
	 *
	 * @return void
	 */
	private function check_license() {

		global $ocean_pro_demos_fs;
		if( ! empty( $ocean_pro_demos_fs ) ) {		
			$plugin_id = $ocean_pro_demos_fs->get_site()->id;
			$_license = $ocean_pro_demos_fs->_get_license();
			$license_id = empty( $_license->parent_license_id ) ? '' : $_license->parent_license_id;
			$is_registered = $ocean_pro_demos_fs->is_registered();
			$is_tracking_allowed = $ocean_pro_demos_fs->is_tracking_allowed();
			if( empty( $plugin_id ) || empty( $license_id ) || empty( $is_registered ) || empty( $is_tracking_allowed ) ) {
				return false;
			}
		} else {
			return false;
		}

		$data['plugin_id'] = $plugin_id;
		$data['wp_site_url'] = get_site_url();
		$data['license_id'] = $license_id;
		$data['is_registered'] = $is_registered;
		$data['is_tracking_allowed'] = $is_tracking_allowed;


		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, self::$CHECKER_URL );

		curl_setopt( $ch, CURLOPT_POST, 1 );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $data ) );

		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		$response_data = curl_exec( $ch );
		curl_close( $ch );

		$response_data = json_decode( $response_data, true );

		return $response_data['success'];
	}
	
	/**
	 * Get Tags.
	 *
	 * @return void
	 */
	public function get_tags() {
		$library_data = self::get_library_data();

		return ( ! empty( $library_data['tags'] ) ? $library_data['tags'] : [] );
	}

	/**
	 * Get Type Tags.
	 *
	 * @return void
	 */
	public function get_type_tags() {
		$library_data = self::get_library_data();

		return ( ! empty( $library_data['type_tags'] ) ? $library_data['type_tags'] : [] );
	}

	/**
	 * Prepare template items to match model
	 *
	 * @param array $template_data
	 * @return array
	 */
	private function prepare_template( array $template_data ) {
		return [
			'template_id' => $template_data['id'],
			'title'       => $template_data['title'],
			'type'        => $template_data['type'],
			'folder'      => $template_data['folder'],
			'thumbnail'   => $template_data['thumbnail'],
			'date'        => $template_data['created_at'],
			'tags'        => $template_data['tags'],
			'isPlus'      => $template_data['is_plus'],
			'url'         => $template_data['url'],
		];
	}

	/**
	 * Get library data from remote source and cache
	 *
	 * @param boolean $force_update
	 * @return array
	 */
	private static function request_library_data( $force_update = false ) {
		$data = get_option( self::LIBRARY_CACHE_KEY );

		if ( $force_update || false === $data ) {
			$timeout = ( $force_update ) ? 25 : 8;

			$response = wp_remote_get( self::API_TEMPLATES_INFO_URL, [
				'timeout' => $timeout,
			] );

			if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
				update_option( self::LIBRARY_CACHE_KEY, [] );
				return false;
			}

			$data = json_decode( wp_remote_retrieve_body( $response ), true );

			if ( empty( $data ) || ! is_array( $data ) ) {
				update_option( self::LIBRARY_CACHE_KEY, [] );
				return false;
			}

			update_option( self::LIBRARY_CACHE_KEY, $data, 'no' );
		}

		return $data;
	}

	/**
	 * Get library data
	 *
	 * @param boolean $force_update
	 * @return array
	 */
	public static function get_library_data( $force_update = false ) {
		self::request_library_data( $force_update );

		$data = get_option( self::LIBRARY_CACHE_KEY );

		if ( empty( $data ) ) {
			return [];
		}

		return $data;
	}

	/**
	 * Get remote template.
	 *
	 * Retrieve a single remote template from Elementor.com servers.
	 *
	 * @param int $template_id The template ID.
	 *
	 * @return array Remote template.
	 */
	public function get_item( $template_id ) {
		$templates = $this->get_items();

		return $templates[ $template_id ];
	}
	
	/**
	 * Request template data from the API.
	 *
	 * @param  mixed $template_id
	 * @return void
	 */
	public static function request_template_data( $template_id, $type = '', $folder = '' ) {
		if ( empty( $template_id ) ) {
			return;
		}

		$body = [
			'home_url' => trailingslashit( home_url() ),
			'version' => OPD_VERSION,
		];

		$remote_url = self::API_TEMPLATE_DATA_URL;
		if( ! empty( $type ) && ! empty( $folder ) ) {
			$remote_url .= $type . '/' . $folder . '/';
		}

		$response = wp_remote_get(
			$remote_url . $template_id . '.json',
			[
				'body' => $body,
				'timeout' => 25
			]
		);

		return wp_remote_retrieve_body( $response );
	}

	/**
	 * Get remote template data.
	 *
	 * Retrieve the data of a single remote template from Elementor.com servers.
	 *
	 * @return array|\WP_Error Remote Template data.
	 */
	public function get_data( array $args, $context = 'display' ) {
		$data = self::request_template_data( $args['template_id'], $args['type'], $args['folder'] );

		$data = json_decode( $data, true );

		if ( empty( $data ) || empty( $data['content'] ) ) {
			throw new \Exception( __( 'Template does not have any content', 'ocean-pro-demos' ) );
		}

		$data['content'] = $this->replace_elements_ids( $data['content'] );
		$data['content'] = $this->process_export_import_content( $data['content'], 'on_import' );

		$post_id = $args['editor_post_id'];
		$document = otl_elementor()->documents->get( $post_id );

		if ( $document ) {
			$data['content'] = $document->get_elements_raw_data( $data['content'], true );
		}

		return $data;
	}
}
