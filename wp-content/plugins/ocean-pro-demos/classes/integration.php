<?php
/**
 * Integrations page in Theme Panel
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Start Class.
class OEW_ImageApi_Integrations {

	/**
	 * Start things up
	 */
	public function __construct() {
		add_filter( 'ocean_integrations_settings', array( $this, 'settings' ) );
		add_action( 'ocean_integrations_after_content', array( $this, 'content' ) );
		add_action( 'admin_menu', array( $this, 'add_page' ), 999 );
	}

	/**
	 * Get settings.
	 *
	 * @since   1.1.0
	 */
	public static function settings( $array ) {

		$array['api_images_integration']     = get_option( 'owp_api_images_integration' );
		$array['api_images_integration_server']     = get_option( 'owp_api_images_integration_server' );
		$array['flaticon_integration']       = get_option( 'owp_flaticon_integration' );
		$array['freepik_integration']        = get_option( 'owp_freepik_integration' );
		$array['freepik_image_width']        = get_option( 'owp_freepik_image_width' );
		$array['freepik_image_width_custom'] = get_option( 'owp_freepik_image_width_custom' );

		return $array;
	}


	/**
	 * Add sub menu page
	 *
	 * @since 1.0.0
	 */
	public function add_page() {
		add_submenu_page(
			'oceanwp',
			esc_html__( 'Ocean Images', 'ocean-pro-demos' ),
			esc_html__( 'Ocean Images', 'ocean-pro-demos' ),
			'manage_options',
			'admin.php?page=oceanwp#ocean-images',
			''
		);
	}

	/**
	 * Integrations content
	 *
	 * @since   1.1.0
	 */
	public static function content() {

		// Return if Ocean Extra is disabled.
		if ( ! class_exists( 'Ocean_Extra_Theme_Panel' ) ) {
			return;
		}

		// Get settings.
		$settings = OWP_Integrations::get_settings(); ?>

		<hr>

		<h2 id="ocean-images"><?php esc_html_e( 'Ocean Images', 'ocean-pro-demos' ); ?></h2>

		<table class="form-table">
			<tbody>
				<tr id="owp_api_images_integration_tr">
					<th scope="row">
						<label for="owp_api_images_integration"><?php esc_html_e( 'Enable Ocean Images Module', 'ocean-pro-demos' ); ?></label>
					</th>
					<td>
						<select name="owp_integrations[api_images_integration]" id="owp_api_images_integration">
							<option <?php selected( $settings['api_images_integration'], '0', true ); ?> value="0">
								<?php esc_html_e( 'Disable', 'ocean-pro-demos' ); ?>
							</option>
							<option <?php selected( $settings['api_images_integration'], '1', true ); ?> value="1">
								<?php esc_html_e( 'Enable', 'ocean-pro-demos' ); ?>
							</option>							
						</select>
					</td>
				</tr>
				<tr id="owp_api_images_integration_server_tr">
					<th scope="row">
						<label for="owp_api_images_integration_server"><?php esc_html_e( 'Server', 'ocean-pro-demos' ); ?></label>
					</th>
					<td>
						<select name="owp_integrations[api_images_integration_server]" id="owp_api_images_integration_server">
							<option <?php selected( $settings['api_images_integration_server'], 'main', true ); ?> value="main">
								<?php esc_html_e( 'Main', 'ocean-pro-demos' ); ?>
							</option>
							<option <?php selected( $settings['api_images_integration_server'], 'alternative', true ); ?> value="alternative">
								<?php esc_html_e( 'Alternative', 'ocean-pro-demos' ); ?>
							</option>							
						</select>
					</td>
				</tr>
			</tbody>
		</table>
		<table class="form-table api-ingegrations">
		<tbody>
			<tr id="owp_flaticon_integration_tr">
				<th scope="row">
					<label for="owp_flaticon_integration"><?php esc_html_e( 'Enable Flaticon', 'ocean-pro-demos' ); ?></label>
				</th>
				<td>
					<select name="owp_integrations[flaticon_integration]" id="owp_flaticon_integration">
						<option <?php selected( $settings['flaticon_integration'], '0', true ); ?> value="0">
							<?php esc_html_e( 'Disable', 'ocean-pro-demos' ); ?>
						</option>
						<option <?php selected( $settings['flaticon_integration'], '1', true ); ?> value="1">
							<?php esc_html_e( 'Enable', 'ocean-pro-demos' ); ?>
						</option>
					</select>
				</td>
			</tr>
		</tbody>
	</table>
	<table class="form-table api-ingegrations">
		<tbody>
			<tr id="owp_freepik_integration_tr">
				<th scope="row">
					<label for="owp_freepik_integration"><?php esc_html_e( 'Enable Freepik', 'ocean-pro-demos' ); ?></label>
				</th>
				<td>
					<select name="owp_integrations[freepik_integration]" id="owp_freepik_integration">
						<option <?php selected( $settings['freepik_integration'], '0', true ); ?> value="0">
							<?php esc_html_e( 'Disable', 'ocean-pro-demos' ); ?>
						</option>
						<option <?php selected( $settings['freepik_integration'], '1', true ); ?> value="1">
							<?php esc_html_e( 'Enable', 'ocean-pro-demos' ); ?>
						</option>
					</select>
				</td>
			</tr>
			<tr id="owp_freepik_image_width_tr">
				<th scope="row">
					<label for="owp_freepik_image_width"><?php esc_html_e( 'Freepik Image Width', 'ocean-pro-demos' ); ?></label>
				</th>
				<td>
					<select name="owp_integrations[freepik_image_width]" id="owp_freepik_image_width">
						<option <?php selected( $settings['freepik_image_width'], 'origin', true ); ?> value="origin">
							<?php esc_html_e( 'Original', 'ocean-pro-demos' ); ?>
						</option>
						<option <?php selected( $settings['freepik_image_width'], '500', true ); ?> value="500">
							<?php esc_html_e( '500px', 'ocean-pro-demos' ); ?>
						</option>
						<option <?php selected( $settings['freepik_image_width'], '800', true ); ?> value="800">
							<?php esc_html_e( '800px', 'ocean-pro-demos' ); ?>
						</option>
						<option <?php selected( $settings['freepik_image_width'], '1000', true ); ?> value="1000">
							<?php esc_html_e( '1000px', 'ocean-pro-demos' ); ?>
						</option>
						<option <?php selected( $settings['freepik_image_width'], '1380', true ); ?> value="1380">
							<?php esc_html_e( '1380px', 'ocean-pro-demos' ); ?>
						</option>
						<option <?php selected( $settings['freepik_image_width'], '1600', true ); ?> value="1600">
							<?php esc_html_e( '1600px', 'ocean-pro-demos' ); ?>
						</option>
						<option <?php selected( $settings['freepik_image_width'], '2560', true ); ?> value="1600">
							<?php esc_html_e( '2560px', 'ocean-pro-demos' ); ?>
						</option>
						<option <?php selected( $settings['freepik_image_width'], 'custom', true ); ?> value="custom">
							<?php esc_html_e( 'Custom Size', 'ocean-pro-demos' ); ?>
						</option>
					</select>
				</td>
			</tr>
			<tr id="owp_freepik_image_width_custom_tr">
				<th scope="row">
					<label for="owp_freepik_image_width_custom"><?php esc_html_e( 'Width Size (px)', 'ocean-pro-demos' ); ?></label>
				</th>
				<td>
					<input class="regular-text" name="owp_integrations[freepik_image_width_custom]" min="1" step="1" value="<?php echo $settings['freepik_image_width_custom']; ?>" type="number" placeholder="<?php esc_attr_e( 'Enter image width in pixels', 'ocean-pro-demos' ); ?>"/>
				</td>
			</tr>
		</tbody>
	</table>

	<script>
		jQuery(document).ready(function(){
			jQuery('#owp_api_images_integration').on('change', function() {
				if(jQuery(this).val() === '0') {
					jQuery('.api-ingegrations').hide();
				} else {
					jQuery('.api-ingegrations').show();
				}
			});
			jQuery('#owp_api_images_integration').trigger('change');

			jQuery('#owp_freepik_integration').on('change', function() {
				if(jQuery(this).val() === '0') {
					jQuery('#owp_freepik_image_width_tr').hide();
				} else {
					jQuery('#owp_freepik_image_width_tr').show();
				}
			});
			jQuery('#owp_freepik_integration').trigger('change');

			jQuery('#owp_freepik_image_width').on('change', function() {
				if(jQuery(this).val() !== 'custom') {
					jQuery('#owp_freepik_image_width_custom_tr').hide();
				} else {
					jQuery('#owp_freepik_image_width_custom_tr').show();
				}
			});
			jQuery('#owp_freepik_image_width').trigger('change');
		});
	</script>
		<?php
	}

}
new OEW_ImageApi_Integrations();
