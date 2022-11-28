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

class FutureYoga_Teacher_Contacts extends Widget_Base
{

	public function get_name()
	{
		return 'fy-teacher-contact';
	}

	public function get_title()
	{
		return __('Future Yoga Teacher Contacts', 'fy-elementor-widgets');
	}

	public function get_icon()
	{
		return 'fy-icon eicon-form-horizontal';
	}

	public function get_categories()
	{
		return array('future-yoga');
	}

	public function get_keywords()
	{
		return array(
			'member',
			'user',
			'team',
			'contact',
			'contatti',
			'contatto',
			'insegnante'
		);
	}

	protected function register_controls()
	{

		$this->start_controls_section(
			'section_member',
			array(
				'label' => __('General', 'ocean-elementor-widgets'),
			)
		);

		$this->add_control(
			'website',
			array(
				'label'     => __('Sito', 'fy-elementor-widgets'),
				'type'      => Controls_Manager::URL,
				'placeholder' => 'https://manuelpetteno.com',
				'dynamic' => array('active' => true)
			)
		);

		$this->add_control(
			'email',
			array(
				'label'     => __('Email', 'fy-elementor-widgets'),
				'type'      => Controls_Manager::TEXT,
				'placeholder' => 'manuel.petteno@gmail.com',
				'dynamic' => array('active' => true)
			)
		);

		$this->add_control(
			'phone',
			array(
				'label'     => __('Telefono', 'fy-elementor-widgets'),
				'type'      => Controls_Manager::TEXT,
				'placeholder' => '3739037822',
				'dynamic' => array('active' => true)
			)
		);

		$this->end_controls_section();
	}

	protected function render()
	{
		$settings  = $this->get_settings_for_display();
		$phone = $settings['phone'];

		if(!str_contains($phone, '+39')) {
			$phone = '+39'. $phone;
		}

		$phone_label = str_replace('+39', '+39 ', $phone);


		$this->add_render_attribute('wrap', 'class', 'fy-teacher-contact-wrap'); ?>

		<div <?php echo $this->get_render_attribute_string('wrap'); ?>>

			<?php if (!empty($settings['website']['url'])) : ?>
				<div class="fy-teacher-website">
					<strong class="fy-teacher-contact__label"><i class="fas fa-mouse-pointer"></i><?php _e('Sito', 'futureyoga'); ?>: </strong>
					<p><a href="<?php echo $settings['website']['url']; ?>" target="_blank"><?php echo $settings['website']['url']; ?></a></p>
				</div>
			<?php endif; ?>

			<?php if (!empty($settings['email'])) : ?>
				<strong class="fy-teacher-contact__label"><i class="far fa-envelope"></i><?php _e('Email', 'futureyoga'); ?>: </strong>
				<p><a href="mailto:<?php echo $settings['email']; ?>"><?php echo $settings['email']; ?></a></p>
			<?php endif; ?>

			<?php if (!empty($settings['phone'])) : ?>
				<strong class="fy-teacher-contact__label"><i class="fas fa-mobile-alt"></i><?php _e('Telefono', 'futureyoga'); ?>: </strong>
				<p><a href="tel:<?php echo $phone; ?>"><?php echo $phone_label; ?></a></p>
			<?php endif; ?>
		</div>

<?php
	}
}
