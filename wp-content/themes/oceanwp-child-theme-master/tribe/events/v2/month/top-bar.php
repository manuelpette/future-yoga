<?php

/**
 * View: Top Bar
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events/v2/month/top-bar.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @version 5.0.1
 *
 */
?>
<?php /**
 *
 * CALENDAR LEGEND
 */
?>

<?php
$args = array(
	'taxonomy'               => 'tribe_events_cat',
	'orderby'                => 'name',
	'order'                  => 'ASC',
	'hide_empty'             => false,
	'meta_key'   => 'mostra_in_legenda',
	'meta_value'   => '1',
	'meta_compare'   => '=',
);
$the_query = new WP_Term_Query($args);


?>
<?php if ($the_query->terms) : ?>
	<div class="calendar-legend">
		<h3><?php _e('I nostri corsi:', 'futureyoga'); ?></h3>

		<ul>
			<?php
			foreach ($the_query->get_terms() as $term) :
			?>
				<li>
					<a class="calendar-legend__cat" href="<?php echo get_term_link($term->term_id); ?>">
						<i class="calendar-legend__cat-color-dot" style="background-color:<?php echo get_field('colore_legenda', 'tribe_events_cat_'  . $term->term_id); ?>"></i>
						<span><?php echo $term->name . " (" . $term->count . ")"; ?></span>
					</a>
				</li>
			<?php
			endforeach;
			?>
		</ul>
	</div>
<?php endif; ?>
<div class="tribe-events-c-top-bar tribe-events-header__top-bar">

	<?php $this->template('month/top-bar/nav'); ?>

	<?php $this->template('components/top-bar/today'); ?>

	<?php $this->template('month/top-bar/datepicker'); ?>

	<?php $this->template('components/top-bar/actions'); ?>

</div>