<?php
/**
 * Post single content
 *
 * @package OceanWP WordPress theme
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<?php do_action( 'ocean_before_single_post_content' ); ?>

<div class="entry-content clr"<?php oceanwp_schema_markup( 'entry_content' ); ?>>
	<?php the_content(); ?>

<?php
global $wp_query;
$posts_per_page = 16;

$query_params = array(
	'posts_per_page' => $posts_per_page,
    'post_status' => 'publish',
	'post_type' => 'tribe_events',
	'orderby' => 'meta_value',
	'order' => 'ASC',
	'meta_query' => array(
		array(
			'key'     => '_EventStartDate',
			'value'   =>  wp_date('Y-m-d H:i:s'),
			'compare' => '>=',
		),
	)
);

$term = $wp_query->get_queried_object();
if(!empty($term->term_id)) {
	$query_params['tax_query'] = array(
			array(
				'taxonomy' => $term->taxonomy,
				'field'    => 'term_id',
				'terms'    => array( $term->term_id ),
			),
		);
}

$filtered_events = get_filtered_events($query_params);
$events = $filtered_events['posts'];
$next_offset = $filtered_events['next_offset'];
$parents_cache = $filtered_events['parents_cache'];

if (count($events)) : ?>
	<div class="elementor-element">
	<div class="elementor-widget-container">
	<div class="fy-events-wrap">
			<div class="events-list">
				<div class="events-row" data-ajax-events>
					<?php echo get_events_list_markup($events, $query_params, $next_offset, $parents_cache); ?>
				</div>
			</div>
            <!-- <small data-load-more-end><?php _e('Tutti gli eventi disponibili sono visualizzati', 'futureyoga'); ?></small> -->
		</div>
		</div>
		</div>

		<?php else : ?>

			<p class="text-center"><?php _e('Spiacente non ci sono eventi in arrivo. Puoi consultare gli eventi passati dal calendario.', 'futureyoga'); ?></p>

<?php endif; ?>



</div><!-- .entry -->

<?php do_action( 'ocean_after_single_post_content' ); ?>
