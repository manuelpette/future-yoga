<?php
/**
 * Template library templates
 */

defined( 'ABSPATH' ) || exit;

?>
<script type="text/template" id="tmpl-otl-template-library-header-logo">
    <span class="otl-template-library-logo-wrap">
	</span>
    <span class="otl-template-library-logo-title">{{{ title }}}</span>
</script>

<script type="text/template" id="tmpl-otl-template-library-header-back">
	<i class="eicon-" aria-hidden="true"></i>
	<span><?php echo __( 'Back to Library', 'ocean-pro-demos' ); ?></span>
</script>

<script type="text/template" id="tmpl-otl-template-library-header-menu">
	<# _.each( tabs, function( args, tab ) { var activeClass = args.active ? 'elementor-active' : ''; #>
		<div class="elementor-component-tab elementor-template-library-menu-item {{activeClass}}" data-tab="{{{ tab }}}">{{{ args.title }}}</div>
	<# } ); #>
</script>

<script type="text/template" id="tmpl-otl-template-library-header-menu-responsive">
	<div class="elementor-component-tab otl-template-library-responsive-menu-item elementor-active" data-tab="desktop">
		<i class="eicon-device-desktop" aria-hidden="true" title="<?php esc_attr_e( 'Desktop view', 'ocean-pro-demos' ); ?>"></i>
		<span class="elementor-screen-only"><?php esc_html_e( 'Desktop view', 'ocean-pro-demos' ); ?></span>
	</div>
	<div class="elementor-component-tab otl-template-library-responsive-menu-item" data-tab="tab">
		<i class="eicon-device-tablet" aria-hidden="true" title="<?php esc_attr_e( 'Tab view', 'ocean-pro-demos' ); ?>"></i>
		<span class="elementor-screen-only"><?php esc_html_e( 'Tab view', 'ocean-pro-demos' ); ?></span>
	</div>
	<div class="elementor-component-tab otl-template-library-responsive-menu-item" data-tab="mobile">
		<i class="eicon-device-mobile" aria-hidden="true" title="<?php esc_attr_e( 'Mobile view', 'ocean-pro-demos' ); ?>"></i>
		<span class="elementor-screen-only"><?php esc_html_e( 'Mobile view', 'ocean-pro-demos' ); ?></span>
	</div>
</script>

<script type="text/template" id="tmpl-otl-template-library-header-actions">
	<div id="otl-template-library-header-sync" class="elementor-templates-modal-header-item">
		<i class="eicon-sync" aria-hidden="true" title="<?php esc_attr_e( 'Sync Library', 'ocean-pro-demos' ); ?>"></i>
		<span class="elementor-screen-only"><?php esc_html_e( 'Sync Library', 'ocean-pro-demos' ); ?></span>
	</div>
</script>

<script type="text/template" id="tmpl-otl-template-library-preview">
    <iframe></iframe>
</script>

<script type="text/template" id="tmpl-otl-template-library-header-insert">
	<div id="elementor-template-library-header-preview-insert-wrapper" class="elementor-templates-modal-header-item">
		{{{ otl.library.getModal().getTemplateActionButton( obj ) }}}
	</div>
</script>

<script type="text/template" id="tmpl-otl-template-library-insert-button">
	<a class="elementor-template-library-template-action elementor-button otl-template-library-insert-button">
		<i class="eicon-file-download" aria-hidden="true"></i>
		<span class="elementor-button-title"><?php esc_html_e( 'Insert', 'ocean-pro-demos' ); ?></span>
	</a>
</script>

<script type="text/template" id="tmpl-otl-template-library-plus-button">
	<a class="elementor-template-library-template-action elementor-button otl-template-library-plus-button" href="https://oceanwp.org/core-extensions-bundle/" target="_blank">
		<i class="eicon-external-link-square" aria-hidden="true"></i>
		<span class="elementor-button-title"><?php esc_html_e( 'Get Plus', 'ocean-pro-demos' ); ?></span>
	</a>
</script>

<script type="text/template" id="tmpl-otl-template-library-loading">
	<div class="elementor-loader-wrapper">
		<div class="elementor-loader">
			<div class="elementor-loader-boxes">
				<div class="elementor-loader-box"></div>
				<div class="elementor-loader-box"></div>
				<div class="elementor-loader-box"></div>
				<div class="elementor-loader-box"></div>
			</div>
		</div>
		<div class="elementor-loading-title"><?php esc_html_e( 'Loading', 'ocean-pro-demos' ); ?></div>
	</div>
</script>

<script type="text/template" id="tmpl-otl-template-library-templates">
	<div style="text-align: left;"><?php esc_html_e( 'You can use it to filter through categories faster or easier', 'ocean-pro-demos' ); ?></div>
	<div id="otl-template-library-toolbar">
		<div id="otl-template-library-toolbar-filter" class="otl-template-library-toolbar-filter">
			<# if (otl.library.getTypeTags()) { var selectedTag = otl.library.getFilter( 'tags' ); #>
				<# if ( selectedTag ) { #>
				<span class="otl-template-library-filter-btn">{{{ otl.library.getTags()[selectedTag] }}} <i class="eicon-caret-right"></i></span>
				<# } else { #>
				<span class="otl-template-library-filter-btn"><?php esc_html_e( 'Filter', 'ocean-pro-demos' ); ?> <i class="eicon-caret-right"></i></span>
				<# } #>
				<ul id="otl-template-library-filter-tags" class="otl-template-library-filter-tags">
					<li data-tag="">All</li>
					<# _.each(otl.library.getTypeTags(), function(slug) {
						var selected = selectedTag === slug ? 'active' : '';
						#>
						<li data-tag="{{ slug }}" class="{{ selected }}">{{{ otl.library.getTags()[slug] }}}</li>
					<# } ); #>
				</ul>
			<# } #>
		</div>
		<div id="otl-template-library-toolbar-counter"></div>
		<div id="otl-template-library-toolbar-search">
			<label for="otl-template-library-search" class="elementor-screen-only"><?php esc_html_e( 'Search Templates:', 'ocean-pro-demos' ); ?></label>
			<input id="otl-template-library-search" placeholder="<?php esc_attr_e( 'Search', 'ocean-pro-demos' ); ?>">
			<i class="eicon-search"></i>
		</div>
	</div>

	<div class="otl-template-library-templates-window">
		<div id="otl-template-library-templates-list"></div>
	</div>
</script>

<script type="text/template" id="tmpl-otl-template-library-template">
	<div class="otl-template-library-template-body" id="otlTemplate-{{ template_id }}">
		<div class="otl-template-library-template-preview">
			<i class="eicon-zoom-in-bold" aria-hidden="true"></i>
		</div>
		<img class="otl-template-library-template-thumbnail" src="{{ thumbnail }}">
		<# if ( obj.isPlus ) { #>
		<span class="otl-template-library-template-badge"><?php esc_html_e( 'Plus', 'ocean-pro-demos' ); ?></span>
		<# } #>
	</div>
	<div class="otl-template-library-template-footer">
		{{{ otl.library.getModal().getTemplateActionButton( obj ) }}}
		<a href="#" class="elementor-button otl-template-library-preview-button">
			<i class="eicon-device-desktop" aria-hidden="true"></i>
			<?php esc_html_e( 'Preview', 'ocean-pro-demos' ); ?>
		</a>
	</div>
</script>

<script type="text/template" id="tmpl-otl-template-library-empty">
	<div class="elementor-template-library-blank-icon">
		<img src="<?php echo ELEMENTOR_ASSETS_URL . 'images/no-search-results.svg'; ?>" class="elementor-template-library-no-results" />
	</div>
	<div class="elementor-template-library-blank-title"></div>
	<div class="elementor-template-library-blank-message"></div>
	<div class="elementor-template-library-blank-footer">
		<?php esc_html_e( 'Want to learn more about the Ocean Elementor Library?', 'ocean-pro-demos' ); ?>
		<a class="elementor-template-library-blank-footer-link" href="https://oceanwp.org/core-extensions-bundle/>" target="_blank"><?php echo __( 'Click here', 'ocean-pro-demos' ); ?></a>
	</div>
</script>
