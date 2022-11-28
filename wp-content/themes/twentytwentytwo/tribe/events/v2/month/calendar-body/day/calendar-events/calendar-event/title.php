<?php

/**
 * View: Day View - Single Event Title
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events/v2/day/event/title.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @version 5.0.0
 *
 * @var WP_Post $event The event post object with properties added by the `tribe_get_event` function.
 *
 * @see tribe_get_event() For the format of the event object.
 */

$args = array(
	'orderby'                => 'name',
	'order'                  => 'ASC',
	'hide_empty'             => false,
	'meta_key'   => 'mostra_in_legenda',
	'meta_value'   => '1',
	'meta_compare'   => '=',
);

$terms = wp_get_post_terms($event->ID, 'tribe_events_cat', $args);

?>
<h3 class="tribe-events-calendar-day__event-title tribe-common-h6 tribe-common-h6--min-medium">
	<a href="<?php echo esc_url($event->permalink); ?>" title="<?php echo esc_attr($event->title); ?>" rel="bookmark" class="tribe-events-calendar-day__event-title-link tribe-common-anchor-thin">
		<?php
		// phpcs:ignore
		echo $event->title;
		?>
	</a>
</h3>

<?php if (count($terms)) : ?>
	<ul class="calendar-single-event__cats">
	<?php foreach ($terms as $term) : ?>
		<li class="calendar-single-event__cat">
			<a href="<?php echo get_term_link($term->term_id); ?>">
				<i class="calendar-single-event__cat-color-dot" style="background-color:<?php echo get_field('colore_legenda', 'tribe_events_cat_'  . $term->term_id); ?>"></i>
				<span class="calendar-single-event__tooltip"><?php echo $term->name; ?></span>
			</a>
		</li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>