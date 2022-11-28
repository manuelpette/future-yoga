<?php
namespace Ocean_Template_Library\Elementor;

use Elementor\Core\Common\Modules\Ajax\Module as Ajax;

defined( 'ABSPATH' ) || die();


/**
 * Get Elementor instance
 *
 * @return \Elementor\Plugin
 */
function otl_elementor() {
	return \Elementor\Plugin::instance();
}

/**
 * Library Manager
 */
class Ocean_Template_Library_Manager {

	protected static $source = null;

	public static function init() {

		add_filter( 'ocean_elementor_library_tags', array( __CLASS__, 'ocean_elementor_library_tags' ) );
		add_filter( 'opd_elementor_library_panel_tags', array( __CLASS__, 'opd_elementor_library_panel_tags' ) );

		add_action( 'elementor/editor/footer', array( __CLASS__, 'print_template_views' ) );
		add_action( 'elementor/ajax/register_actions', array( __CLASS__, 'register_ajax_actions' ) );
		add_action( 'elementor/editor/after_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		add_action( 'elementor/preview/enqueue_styles', array( __CLASS__, 'enqueue_embedded_iframe_styles' ) );
	}

	/**
	 * Print template views
	 *
	 * @return void
	 */
	public static function print_template_views() {
		include_once OPD_PATH . 'elementor-library/templates.php';
	}

	/**
	 * Enqueue Embedded Iframe Styles
	 *
	 * @return void
	 */
	public static function enqueue_embedded_iframe_styles() {
		wp_enqueue_style(
			'ocean-elementor-admin',
			OPD_URL . 'elementor-library/assets/css/main.min.css',
			array(),
			'99999999'
		);
	}

	/**
	 * Enqueue Assets
	 *
	 * @return void
	 */
	public static function enqueue_assets() {
		wp_enqueue_style(
			'ocean-elementor-templates-library',
			OPD_URL . 'elementor-library/assets/css/template-library.min.css',
			array(
				'elementor-editor',
			),
			OPD_VERSION
		);

		wp_enqueue_script(
			'ocean-elementor-templates-library',
			OPD_URL . 'elementor-library/assets/js/template-library.min.js',
			array(
				'jquery-hover-intent',
			),
			OPD_VERSION,
			true
		);

		// Localize scripts.
		wp_localize_script(
			'ocean-pro-demos',
			'OceanLibraryLocalize',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'ocean_library_nonce' ),
			)
		);
		$localize_data = array(
			'editor_nonce' => wp_create_nonce( 'otl_editor_nonce' ),
			'i18n'         => array(
				'templatesEmptyTitle'       => esc_html__( 'No Templates Found', 'ocean-pro-demos' ),
				'templatesEmptyMessage'     => esc_html__( 'Try a different category or sync for new templates.', 'ocean-pro-demos' ),
				'templatesNoResultsTitle'   => esc_html__( 'No Results Found', 'ocean-pro-demos' ),
				'templatesNoResultsMessage' => esc_html__( 'Please make sure your search is spelled correctly or try different words.', 'ocean-pro-demos' ),
			),
		);

		wp_localize_script(
			'ocean-elementor-templates-library',
			'OceanLibraryEditor',
			$localize_data
		);
	}

	/**
	 * Getting Source
	 *
	 * @return Ocean_Template_Library_Source
	 */
	public static function get_source() {
		if ( is_null( self::$source ) ) {
			self::$source = new Ocean_Template_Library_Source();
		}

		return self::$source;
	}

	/**
	 * Register AJAX Actions.
	 *
	 * @param  mixed $ajax
	 * @return void
	 */
	public static function register_ajax_actions( Ajax $ajax ) {
		$ajax->register_ajax_action(
			'get_otl_library_data',
			function( $data ) {
				if ( ! current_user_can( 'edit_posts' ) ) {
					throw new \Exception( 'Access Denied' );
				}

				if ( ! empty( $data['editor_post_id'] ) ) {
					$editor_post_id = absint( $data['editor_post_id'] );

					if ( ! get_post( $editor_post_id ) ) {
						throw new \Exception( __( 'Post not found.', 'ocean-pro-demos' ) );
					}

					otl_elementor()->db->switch_to_post( $editor_post_id );
				}

				$result = self::get_library_data( $data );

				return $result;
			}
		);

		$ajax->register_ajax_action(
			'get_otl_template_data',
			function( $data ) {
				if ( ! current_user_can( 'edit_posts' ) ) {
					throw new \Exception( 'Access Denied' );
				}

				if ( ! empty( $data['editor_post_id'] ) ) {
					$editor_post_id = absint( $data['editor_post_id'] );

					if ( ! get_post( $editor_post_id ) ) {
						throw new \Exception( __( 'Post not found', 'ocean-pro-demos' ) );
					}

					otl_elementor()->db->switch_to_post( $editor_post_id );
				}

				if ( empty( $data['template_id'] ) ) {
					throw new \Exception( __( 'Template ID missing', 'ocean-pro-demos' ) );
				}

				$result = self::get_template_data( $data );

				return $result;
			}
		);
	}

	/**
	 * Get Template Data
	 *
	 * @param  mixed $args
	 * @return void
	 */
	public static function get_template_data( array $args ) {
		$source = self::get_source();
		$data   = $source->get_data( $args );
		return $data;
	}

	/**
	 * Ocean Elementor Library Tags
	 *
	 * @param  mixed $templates
	 * @return void
	 */
	public static function ocean_elementor_library_tags( $templates ) {
		$available_tags = get_option( 'opd_elementor_library_tags', null );

		if ( $available_tags !== null ) {
			foreach ( $templates as $index => $template_item ) {
				if ( $template_item['tags'] ) {
					foreach ( $template_item['tags'] as $key => $tag_slug ) {
						if ( empty( $available_tags[ $tag_slug ] ) ) {
							unset( $template_item['tags'][ $key ] );
						}
					}
					if ( empty( $template_item['tags'] ) ) {
						unset( $templates[ $index ] );
					}
				}
			}
		}

		return array_values( $templates );
	}

	/**
	 * Ocean Elementor Library Panel Tags
	 *
	 * @param  mixed $tags
	 * @return void
	 */
	public static function opd_elementor_library_panel_tags( $tags ) {
		$source = self::get_source();
		$tags   = $source->get_tags();
		return $tags;
	}

	/**
	 * Get library data from cache or remote
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	public static function get_library_data( array $args ) {
		$source = self::get_source();

		if ( ! empty( $args['sync'] ) ) {
			Ocean_Template_Library_Source::get_library_data( true );
		}

		$tags           = $source->get_tags();
		$available_tags = get_option( 'opd_elementor_library_tags', null );
		if ( $available_tags !== null ) {
			foreach ( $tags as $key => $val ) {
				if ( empty( $available_tags[ $key ] ) ) {
					unset( $tags[ $key ] );
				}
			}
		}

		$type_tags = $source->get_type_tags();
		if ( $available_tags !== null ) {
			foreach ( $type_tags as $key => $list ) {
				foreach ( $list as $index => $slug ) {
					if ( empty( $available_tags[ $slug ] ) ) {
						unset( $list[ $index ] );
					}
				}
				$type_tags[ $key ] = $list;
			}
		}

		return array(
			'templates' => $source->get_items(),
			'tags'      => $tags,
			'type_tags' => $type_tags,
		);
	}
}

Ocean_Template_Library_Manager::init();
