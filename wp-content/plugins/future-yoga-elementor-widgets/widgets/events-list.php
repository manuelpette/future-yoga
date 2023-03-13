<?php

namespace FutureYoga_Elementor\Widgets;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Widget_Base;
use WP_Query;

function start_pager_hack( $queryArgs ){
	global $wp_query;
	global $temp_query;

		$paged = ( get_query_var('paged') ? get_query_var('paged') : 1 );
		$queryArgs['paged'] = $paged;

		$currentQuery = new WP_Query( $queryArgs );
		$temp_query = $wp_query;
		$wp_query   = NULL;
		$wp_query = $currentQuery;
}

function end_pager_hack(){
	global $wp_query;
	global $temp_query;

	$wp_query = NULL;
	$wp_query = $temp_query;
}

class FutureYoga_Events extends Widget_Base
{

	public function get_name()
	{
		return 'fy-events';
	}

	public function get_title()
	{
		return __('Future Yoga Events', 'fy-elementor-widgets');
	}

	public function get_icon()
	{
		return 'fy-icon eicon-posts-grid';
	}

	public function get_categories()
	{
		return array('future-yoga');
	}

	public function get_keywords()
	{
		return array(
			'eventi',
			'lista',
			'corsi',
			'future',
			'yoga'
		);
	}

	protected function register_controls()
	{

		$this->start_controls_section(
			'section_events',
			array(
				'label' => __('General', 'ocean-elementor-widgets'),
			)
		);

		$this->add_control(
			'posts_per_page',
			array(
				'label'   => __('Eventi per pagina', 'fy-elementor-widgets'),
				'type'    => Controls_Manager::NUMBER,
				'default' => 16,
				'dynamic' => array('active' => true),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			array(
				'label' => __('Member', 'ocean-elementor-widgets'),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'member_bg',
			array(
				'label'     => __('Background Color', 'ocean-elementor-widgets'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .oew-member-wrap' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'member_border',
				'selector' => '{{WRAPPER}} .oew-member-wrap',
			)
		);

		$this->add_responsive_control(
			'member_border_radius',
			array(
				'label'      => __('Border Radius', 'ocean-elementor-widgets'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array('px', '%', 'em'),
				'selectors'  => array(
					'{{WRAPPER}} .oew-member-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				),
			)
		);

		$this->add_responsive_control(
			'member_padding',
			array(
				'label'      => __('Padding', 'ocean-elementor-widgets'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array('px', '%', 'em'),
				'selectors'  => array(
					'{{WRAPPER}} .oew-member-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'member_margin',
			array(
				'label'      => __('Margin', 'ocean-elementor-widgets'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array('px', '%', 'em'),
				'selectors'  => array(
					'{{WRAPPER}} .oew-member-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'content_heading',
			array(
				'label'     => __('Content', 'ocean-elementor-widgets'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'text_align',
			array(
				'label'     => __('Text Alignment', 'ocean-elementor-widgets'),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'    => array(
						'title' => __('Left', 'ocean-elementor-widgets'),
						'icon'  => 'eicon-text-align-left',
					),
					'center'  => array(
						'title' => __('Center', 'ocean-elementor-widgets'),
						'icon'  => 'eicon-text-align-center',
					),
					'right'   => array(
						'title' => __('Right', 'ocean-elementor-widgets'),
						'icon'  => 'eicon-text-align-right',
					),
					'justify' => array(
						'title' => __('Justified', 'ocean-elementor-widgets'),
						'icon'  => 'eicon-text-align-justify',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .oew-member-wrap' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'content_padding',
			array(
				'label'      => __('Content Padding', 'ocean-elementor-widgets'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array('px', 'em', '%'),
				'selectors'  => array(
					'{{WRAPPER}} .oew-member-wrap .oew-member-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_image',
			array(
				'label' => __('Image', 'ocean-elementor-widgets'),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'image_border',
				'label'       => __('Border', 'ocean-elementor-widgets'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .oew-member-wrap .oew-member-image',
			)
		);

		$this->add_control(
			'image_border_radius',
			array(
				'label'      => __('Border Radius', 'ocean-elementor-widgets'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array('px', '%'),
				'selectors'  => array(
					'{{WRAPPER}} .oew-member-wrap .oew-member-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				),
			)
		);

		$this->add_control(
			'image_spacing',
			array(
				'label'     => __('Spacing', 'ocean-elementor-widgets'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .oew-member-wrap .oew-member-image' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_name',
			array(
				'label' => __('Name', 'ocean-elementor-widgets'),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'name_color',
			array(
				'label'     => __('Color', 'ocean-elementor-widgets'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .oew-member-wrap .oew-member-name' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'name_typography',
				'selector' => '{{WRAPPER}} .oew-member-wrap .oew-member-name',
			)
		);

		$this->add_responsive_control(
			'name_spacing',
			array(
				'label'     => __('Spacing', 'ocean-elementor-widgets'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .oew-member-wrap .oew-member-name' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_role',
			array(
				'label' => __('Role', 'ocean-elementor-widgets'),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'role_color',
			array(
				'label'     => __('Color', 'ocean-elementor-widgets'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .oew-member-wrap .oew-member-role' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'role_typography',
				'selector' => '{{WRAPPER}} .oew-member-wrap .oew-member-role',
			)
		);

		$this->add_responsive_control(
			'role_spacing',
			array(
				'label'     => __('Spacing', 'ocean-elementor-widgets'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .oew-member-wrap .oew-member-role' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_text',
			array(
				'label' => __('Text', 'ocean-elementor-widgets'),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'     => __('Color', 'ocean-elementor-widgets'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .oew-member-wrap .oew-member-description' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'text_typography',
				'selector' => '{{WRAPPER}} .oew-member-wrap .oew-member-description',
			)
		);

		$this->add_responsive_control(
			'text_spacing',
			array(
				'label'     => __('Spacing', 'ocean-elementor-widgets'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .oew-member-wrap .oew-member-description' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_social',
			array(
				'label' => __('Social Icon', 'ocean-elementor-widgets'),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'icons_bg',
			array(
				'label'     => __('Icons Background', 'ocean-elementor-widgets'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .oew-member-wrap .oew-member-icons' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'icons_wrap_padding',
			array(
				'label'      => __('Icons Padding', 'ocean-elementor-widgets'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array('px', 'em', '%'),
				'selectors'  => array(
					'{{WRAPPER}} .oew-member-wrap .oew-member-icons' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs('tabs_icons_style');

		$this->start_controls_tab(
			'tab_icons_normal',
			array(
				'label' => __('Normal', 'ocean-elementor-widgets'),
			)
		);

		$this->add_control(
			'icons_background',
			array(
				'label'     => __('Background Color', 'ocean-elementor-widgets'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .oew-member-wrap .oew-member-icons a' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'icons_color',
			array(
				'label'     => __('Color', 'ocean-elementor-widgets'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .oew-member-wrap .oew-member-icons a' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_icons_hover',
			array(
				'label' => __('Hover', 'ocean-elementor-widgets'),
			)
		);

		$this->add_control(
			'icons_hover_background',
			array(
				'label'     => __('Background Color', 'ocean-elementor-widgets'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .oew-member-wrap .oew-member-icons a:hover' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'icons_hover_color',
			array(
				'label'     => __('Color', 'ocean-elementor-widgets'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .oew-member-wrap .oew-member-icons a:hover' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'icons_hover_border_color',
			array(
				'label'     => __('Border Color', 'ocean-elementor-widgets'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .oew-member-wrap .oew-member-icons a:hover' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'icons_border',
				'label'       => __('Border', 'ocean-elementor-widgets'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .oew-member-wrap .oew-member-icons a',
				'separator'   => 'before',
			)
		);

		$this->add_control(
			'icons_border_radius',
			array(
				'label'      => __('Border Radius', 'ocean-elementor-widgets'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array('px', '%'),
				'selectors'  => array(
					'{{WRAPPER}} .oew-member-wrap .oew-member-icons a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icons_padding',
			array(
				'label'      => __('Padding', 'ocean-elementor-widgets'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array('px', 'em', '%'),
				'selectors'  => array(
					'{{WRAPPER}} .oew-member-wrap .oew-member-icons a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'icons_size',
			array(
				'label'     => __('Icon Size', 'ocean-elementor-widgets'),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => array(
					'{{WRAPPER}} .oew-member-wrap .oew-member-icons a' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'icons_indent',
			array(
				'label'     => __('Icon Spacing', 'ocean-elementor-widgets'),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => array(
					'{{WRAPPER}} .oew-member-wrap .oew-member-icons a' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .oew-member-wrap .oew-member-icons a:first-child' => 'margin-left: 0;',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render()
	{
		$settings  = $this->get_settings_for_display();
		$posts_per_page = $settings['posts_per_page'];

		$this->add_render_attribute('wrap', 'class', 'fy-events-wrap');
		$queryOptions = array(
			'posts_per_page' => $posts_per_page,
			'post_type' => 'tribe_events',
			'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
			'orderby' => 'meta_value_num',
			'order' => 'ASC',
			'meta_query' => array(
				array(
					'key'     => '_EventStartDate',
					'value'   => date('Y-m-d H:i:s'),
					'compare' => '>=',
				),
			)
		);

		start_pager_hack($queryOptions);
?>

		<?php if (have_posts()) : ?>
			<div class="events-list">
				<div class="events-row">
					<?php while (have_posts()) :
						the_post();
						$event = tribe_get_event(get_the_ID());


						$teacher = array(
							'name' => '',
							'avatar' => wp_get_attachment_image(1135)
						);

						$linked_teacher = get_field('insegnante', get_the_ID());

						if (!empty($linked_teacher)) {
							$teacher = array(
								'name' => $linked_teacher->post_title,
								'avatar' => get_the_post_thumbnail($linked_teacher->ID)
							);
						}

						$teacher_name = get_field('nome_insegnante', get_the_ID());

						if (!empty($teacher_name)) {
							$teacher = array(
								'name' => $teacher_name,
								'avatar' => wp_get_attachment_image(get_field('immagine_insegnante', get_the_ID()))
							);
						}

						$event_data = array(
							'title' => get_the_title(),
							'teacher' => $teacher
						);


						$event_timestamp = 	strtotime($event->start_date);

						$event_venue = false;
						$coordinates = false;

						if (!empty($event->venues->all())) {
							$venues = $event->venues->all();
							$event_venue = tribe_get_venue_object($venues[0]);
							$coordinates = tribe_get_coordinates($event_venue);
						}

					?>
						<div class="event">
							<div class="event__img">
								<?php the_post_thumbnail(null, 'medium'); ?>
							</div>
							<div class="event__data">
								<div class="event-teacher">
									<div class="event-teacher__avatar"><?php echo $teacher['avatar']; ?></div>
									<div class="event-teacher__name"><?php echo $teacher['name']; ?></div>
								</div>
							</div>
							<h3 class="event__name"><?php echo $event_data['title']; ?></h3>
							<div class="event__meta">
								<div class="event__date-wrapper meta">
									<i class="eicon-calendar"></i><time datetime="<?php echo $event->start_date; ?>"><?php echo date_i18n('l, j M Y', $event_timestamp) . ' alle ' . date_i18n('H:i', $event_timestamp); ?> </time>
								</div>

								<?php if (!empty($event_venue)) : ?>
									<div class="event__date-wrapper meta">
										<?php if ($coordinates['lat'] != 0 && $coordinates['lng'] != 0) : ?><a href="https://www.google.com/maps/dir/<?php echo $coordinates['lat'] . ',' . $coordinates['lng']; ?>" class="event__geolocation" target="_blank"><?php endif; ?>
											<i class="eicon-map-pin"></i>
											<p class="event_venue"><?php echo $event_venue->address  . ', ' . $event_venue->city; ?></p>
											<?php if ($coordinates['lat'] != 0 && $coordinates['lng'] != 0) : ?>
											</a><?php endif; ?>
									</div>
								<?php endif; ?>

								<div class="event__excerpt">
									<?php the_excerpt() ?>
								</div>
								<div class="fy-buttons">
									<a href="<?php the_permalink(); ?>" class="fy-button--empty">
										<span><?php _e('Scopri di più', 'futureyoga'); ?></span>
									</a>
								</div>
							</div>
						</div>

					<?php endwhile; ?>
				</div>
			</div>
			<?php the_posts_pagination(); ?>
		<?php else : ?>

			<p class="text-center"><?php _e('Spiacente non ci sono eventi in arrivo. Puoi consultare gli eventi passati dal calendario.', 'futureyoga'); ?></p>

<?php endif;
		end_pager_hack();
		wp_reset_query();
	}
}
