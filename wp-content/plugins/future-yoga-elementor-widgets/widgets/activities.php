<?php
namespace FutureYoga_Elementor\Widgets;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

class FutureYoga_Activities extends Widget_Base {

	public function get_name() {
		return 'fy-activities';
	}

	public function get_title() {
		return __( 'Future Yoga Activities', 'fy-elementor-widgets' );
	}

	public function get_icon() {
		return 'fy-icon eicon-products-archive';
	}

	public function get_categories() {
		return array( 'future-yoga' );
	}

	public function get_keywords() {
		return array(
			'attivita',
			'activities',
			'categorie',
			'categories',
			'eventi',
			'events'
		);
	}

	public function get_script_depends() {
		return array( 'fy-activities' );
	}

	public function get_style_depends() {
		return array( 'fy-activities' );
	}


	protected function register_controls() {

		$this->start_controls_section(
			'section_activities',
			array(
				'label' => __( 'General', 'fy-elementor-widgets' ),
			)
		);

		$this->add_control(
			'max_activities',
			array(
				'label'   => __( 'Numero di attività massimo', 'fy-elementor-widgets' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 6
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_title',
			array(
				'label' => __( 'Titolo', 'fy-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Color', 'ocean-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .fy-activities-wrap .oew-member-name' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_html_tag',
			array(
				'label'   => __( 'Name HTML Tag', 'ocean-elementor-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h3',
				'options' => oew_get_available_tags(),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .fy-activities-wrap .oew-member-name',
			)
		);

		$this->add_responsive_control(
			'title_spacing',
			array(
				'label'     => __( 'Spacing', 'ocean-elementor-widgets' ),
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
			'section_style_text',
			array(
				'label' => __( 'Text', 'ocean-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'     => __( 'Color', 'ocean-elementor-widgets' ),
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
				'label'     => __( 'Spacing', 'ocean-elementor-widgets' ),
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

		$this->add_responsive_control(
			'text_align',
			array(
				'label'     => __( 'Text Alignment', 'ocean-elementor-widgets' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'    => array(
						'title' => __( 'Left', 'ocean-elementor-widgets' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'  => array(
						'title' => __( 'Center', 'ocean-elementor-widgets' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'   => array(
						'title' => __( 'Right', 'ocean-elementor-widgets' ),
						'icon'  => 'eicon-text-align-right',
					),
					'justify' => array(
						'title' => __( 'Justified', 'ocean-elementor-widgets' ),
						'icon'  => 'eicon-text-align-justify',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .fy-activities-wrap' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

	}

	protected function render() {
		$settings  = $this->get_settings_for_display();
		$title_tag = $settings['title_html_tag'];
		
		$activities_ids = get_field('attivita', get_the_ID());
		$activities = get_terms(array(
			'taxonomy' => 'tribe_events_cat',
			'include' => $activities_ids,
			'hide_empty' => false,
		));

		$this->add_render_attribute( 'wrap', 'class', 'fy-activities-wrap' ); ?>

		<?php
		if(!empty($activities)) :
		foreach($activities as $activity) : ?>
		<?php
			$url = get_term_link($activity->term_id);
			$image_id = get_field('immagine_di_categoria', 'tribe_events_cat_'. $activity->term_id);
			$excerpt = get_field('descrizione_breve', 'tribe_events_cat_'. $activity->term_id);
			$label = get_field('etichetta_di_categoria', 'tribe_events_cat_'. $activity->term_id);
			$activity_label = !empty($label) ? $label : $activity->name;
		?>
        <a class="fy-activity-card" href="<?php echo $url ?>">

		<div <?php echo $this->get_render_attribute_string( 'wrap' ); ?>>

            <?php
            if ( ! empty( $image_id ) ) {
                ?>
                <div class="oew-member-image">
					<?php echo wp_get_attachment_image($image_id, 'medium'); ?>
                </div>
                <?php
            }
            ?>

            <div class="oew-member-content">
                <?php

                    ?>
                    <<?php echo $title_tag; ?> class="oew-member-name">
                        <?php echo $activity_label ?>
                    </<?php echo $title_tag; ?>>

                <?php
                if ( ! empty( $excerpt ) ) {
                    ?>
                    <div class="oew-member-description"><?php echo $excerpt; ?></div>
                    <?php
                }
                ?>
            </div>
         </div>
		</a>
		<?php
		endforeach;
		endif;
		?>
		<?php
	}
}
