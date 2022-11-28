// Document Ready
jQuery(document).ready(function ($) {

    $(document.body).on('op_panel_loaded', function (event) {
        if ($('#owp_freepik_integration').length) {
            jQuery('#owp_freepik_integration').trigger('change');
        }
        if ($('#owp_freepik_image_width').length) {
            jQuery('#owp_freepik_image_width').trigger('change');
        }
    });


    $(document.body).on('change', '#owp_api_images_integration', function () {
        jQuery(this).val() === '0' ? jQuery('.api-ingegrations').hide() : jQuery('.api-ingegrations').show();
    });

    $(document.body).on('change', '#owp_freepik_integration', function () {
        jQuery(this).val() === '0' ? jQuery('#owp_freepik_image_width_tr').hide() : jQuery('#owp_freepik_image_width_tr').show();
    });

    $(document.body).on('change', '#owp_freepik_image_width', function () {
        jQuery(this).val() !== 'custom' ? jQuery('#owp_freepik_image_width_custom_tr').hide() : jQuery('#owp_freepik_image_width_custom_tr').show();
    });
});