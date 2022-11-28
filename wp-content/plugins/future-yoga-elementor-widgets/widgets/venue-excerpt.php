<?php

namespace FutureYoga_Elementor\Widgets;

// Elementor Classes
use Elementor\Widget_Base;

class FutureYoga_Events_Locations extends Widget_Base
{

	public function get_name()
	{
		return 'fy-events-location';
	}

	public function get_title()
	{
		return __('Future Yoga Events Location', 'fy-elementor-widgets');
	}

	public function get_icon()
	{
		return 'fy-icon eicon-pin';
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

    }

    public function shortcode_wrap($text) {
        return '<h2 class="elementor-heading-title elementor-size-default elementor-inline-editing" data-elementor-setting-key="title">' . $text . '</h2>';
    }

	protected function render()
	{
        $venue = get_post_meta( 1783, '_EventVenueID');
        $venue_address = get_post_meta($venue, '_VenueAddress')  . ', ' . get_post_meta($venue, '_VenueCity');
		$settings  = $this->get_settings_for_display();
        echo $this->shortcode_wrap($venue_address);
	}
}
