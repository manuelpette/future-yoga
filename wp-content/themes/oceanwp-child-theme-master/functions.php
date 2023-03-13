<?php
/**
 * OceanWP Child Theme Functions
 *
 * When running a child theme (see http://codex.wordpress.org/Theme_Development
 * and http://codex.wordpress.org/Child_Themes), you can override certain
 * functions (those wrapped in a function_exists() call) by defining them first
 * in your child theme's functions.php file. The child theme's functions.php
 * file is included before the parent theme's file, so the child theme
 * functions will be used.
 *
 * Text Domain: oceanwp
 * @link http://codex.wordpress.org/Plugin_API
 *
 */

/**
 * Load the parent style.css file
 *
 * @link http://codex.wordpress.org/Child_Themes
 */
function oceanwp_child_enqueue_parent_style() {

	// Dynamically get version number of the parent stylesheet (lets browsers re-cache your stylesheet when you update the theme).
	$theme   = wp_get_theme( 'OceanWP' );
	$version = $theme->get( 'Version' );

	// Load the stylesheet.
	wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'oceanwp-style' ), $version );

}

add_action( 'wp_enqueue_scripts', 'oceanwp_child_enqueue_parent_style' );

function enqueue_events_ajax_script()
{
	if(is_post_type_archive('tribe_events')) {
		wp_enqueue_script( 'script_events_ajax', get_stylesheet_directory_uri() . '/ajax_events.js' );
		wp_localize_script( 'script_events_ajax', 'events_ajax', array(
			'url'      => admin_url( 'admin-ajax.php' )
		));
	}
}
add_action( "wp_enqueue_scripts", "enqueue_events_ajax_script" );

add_action('wp', function(){
	global $post;
	$GLOBALS['post_override'] = $post;
});

function shortcode_wrap($text) {
	return '<h2 class="elementor-heading-title elementor-size-default elementor-inline-editing" data-elementor-setting-key="title">' . $text . '</h2>';
}

function event_date(){
	global $post_override;
	$start_date = tribe_get_start_date($post_override->ID, false, 'l, d F Y');
	$end_date = tribe_get_end_date($post_override->ID, false, 'l, d F Y');

	return shortcode_wrap($start_date !== $end_date ? $start_date . ' - ' . $end_date : $start_date);
}

add_shortcode('fy_event_dates', 'event_date');

function event_times(){
	global $post_override;
	$start_time = tribe_get_start_time($post_override->ID, 'H:i');
	$end_time = tribe_get_end_time($post_override->ID, 'H:i');

	return shortcode_wrap($start_time !== $end_time ? __('dalle', 'futureyoga') . ' ' .$start_time . ' alle ' . $end_time : __('alle', 'futureyoga') . ' ' . $start_time);
}

add_shortcode('fy_event_times', 'event_times');


function event_location(){
	global $post_override;
	$event_venue = false;
	$coordinates = false;
	$event = tribe_get_event($post_override->ID);

	if (!empty($event->venues->all())) {
		$venues = $event->venues->all();
		$event_venue = tribe_get_venue_object($venues[0]);
	}

	if (!empty($event_venue)){
		$venue = $event_venue->post_title . ( !empty($event_venue->address) || !empty($event_venue->city)  ? ' | ' : '' ) . ( !empty($event_venue->address) ? $event_venue->address . ', ' : '' ) . (!empty($event_venue->city) ? $event_venue->city : '');

		return shortcode_wrap($venue);
	}
}

add_shortcode('fy_event_location', 'event_location');

function event_teacher(){
	global $post_override;

	$teacher = get_field('insegnante', $post_override->ID);
	$teacher_id = $teacher->ID;

	if(!empty($teacher)) {
		$teacher = get_the_title($teacher_id);
		$teacher_cat = get_field('categoria_principale', $teacher_id);
	}

	if(empty($teacher)) {
		$teacher = get_field('nome_insegnante', $post_override->ID);
	}

	if(empty($teacher)) {
		$teacher = __('Future Yoga', 'futureyoga');
	}

	if($teacher_cat && !empty($teacher_cat)) {
		$teacher_cat_link = get_term_link($teacher_cat);
		$teacher_link = get_the_permalink($teacher_id);
		$teacher = '<a href="'.$teacher_link .'">'. $teacher . '</a>';
	}

	return shortcode_wrap($teacher);
}

add_shortcode('fy_event_teacher', 'event_teacher');

/**
 * Alter your post layouts
 *
 * Replace is_singular( 'post' ) by the function where you want to alter the layout
 * You can also use is_page ( 'page name' ) to alter layouts on specific pages
 * @return full-width, full-screen, left-sidebar, right-sidebar or both-sidebars
 *
 */
function my_post_layout_class( $class ) {

	// Alter your layout
	if ( is_singular() || is_archive() ) {
		$class = 'full-width';
	}

	// Return correct class
	return $class;

}
add_filter( 'ocean_post_layout_class', 'my_post_layout_class', 20 );

add_filter('ocean_page_header_overlay', function ($overlay_markup) {
	global $post_override;

	$featured_image_url = get_the_post_thumbnail_url($post_override->ID);
	if (!empty($featured_image_url)) {
		$new_markup = '<span style="background-image: url(\'' . $featured_image_url . '\');" ';
		$overlay_markup = str_replace('<span ', $new_markup, $overlay_markup);
	}
	return $overlay_markup;
});

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

function get_filtered_events($query_params, $offset = 0, $filtered_events = array(), $parents_cache = array()) {
	if($offset) {
		$query_params['offset'] = $offset;
	}

	$events = new WP_Query($query_params);

	$next_offset = $offset;
	//DUMP($offset . ' to '. ($offset + $query_params['posts_per_page']));

	// Filtering out recurring posts except for closest one to today
	if($events->have_posts()) {
		while ($events->have_posts()) {
			$events->the_post();
			$current = get_post();
			//DUMP($current->post_title . tribe_get_start_date($current->ID, true, 'Y-m-d H:i:s'));

			if(tribe_is_recurring_event(get_the_ID())) {
				if(array_search($current->post_parent, $parents_cache) === false && $current->post_parent !== 0) {
					$parents_cache[] = $current->post_parent;
					$filtered_events[] = $current;
				}
			} else {
					$filtered_events[] = $current;
			}

			if(count($filtered_events) >= $query_params['posts_per_page']){
				break;
			} else {
 				++$next_offset;
			}
		}
	} else {
		return array(
			'posts' => $filtered_events,
			'next_offset' => 'end',
			'parents_cache' => $parents_cache
		);
	}

	if(count($filtered_events) < $query_params['posts_per_page']) {
		return get_filtered_events($query_params, $next_offset, $filtered_events, $parents_cache);
	} else {
		return array(
			'posts' => $filtered_events,
			'next_offset' => $next_offset,
			'parents_cache' => $parents_cache
		);
	}
}

function load_more_events_callback()
{
    $query_params = (array)json_decode(trim(stripcslashes( $_POST['params']) ));
	$query_params['meta_query'] = array((array)$query_params['meta_query'][0]);
    $query_params['tax_query'] = array((array)$query_params['tax_query'][0]);
	$query_offset = (int)filter_var(trim( $_POST['next_offset'] ), FILTER_SANITIZE_STRING);
	$parents_cache = json_decode(filter_var(trim( $_POST['parents_cache'] ), FILTER_SANITIZE_STRING));

	$filtered_events = get_filtered_events($query_params, $query_offset, array(), $parents_cache);
	$events = $filtered_events['posts'];
	$next_offset = $filtered_events['next_offset'];
	$parents_cache = $filtered_events['parents_cache'];

	ob_start();
	get_events_list_markup($events, $query_params, $next_offset, $parents_cache);
	$markup = ob_get_clean();

    wp_send_json( array(
		'markup' => $markup,
		'query_params' => $query_params,
		'next_offset' => $next_offset,
		'parents_cache' => $parents_cache
	));
    die();
}
// Utenti autenticati
add_action( 'wp_ajax_nopriv_load_more_events', 'load_more_events_callback' );
// Utenti non autenticati
add_action( 'wp_ajax_load_more_events', 'load_more_events_callback' );

function get_events_list_markup($events, $query_params, $next_offset, $parents_cache) {
	foreach ($events as $event) :
		$current = get_post($event);
		$current_id = $current->ID;
		$event = tribe_get_event($current_id);

		$teacher = array(
			'name' => '',
			'avatar' => wp_get_attachment_image(1135)
		);

		$linked_teacher = get_field('insegnante', $current_id);

		if (!empty($linked_teacher)) {
			$teacher = array(
				'name' => $linked_teacher->post_title,
				'avatar' => get_the_post_thumbnail($linked_teacher->ID)
			);
		} else {
			$teacher_name = get_field('nome_insegnante', $current_id);

			if (!empty($teacher_name)) {
				$teacher = array(
					'name' => $teacher_name,
					'avatar' => wp_get_attachment_image(get_field('immagine_insegnante', $current_id))
				);
			}
		}



		$event_data = array(
			'title' => get_the_title($current_id),
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
		<div class="event" data-event-id="<?php echo $current_id; ?>">
			<div class="event__img">
				<?php echo get_the_post_thumbnail($current_id, 'medium'); ?>
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
					<?php echo get_the_excerpt($current_id); ?>
				</div>
				<div class="fy-buttons">
					<a href="<?php echo get_the_permalink($current_id); ?>" class="fy-button--empty">
						<span><?php _e('Scopri di più', 'futureyoga'); ?></span>
					</a>
				</div>
			</div>
		</div>

	<?php endforeach; ?>
	<?php if($next_offset !== 'end'): ?>
		<div class="fy-buttons" data-load-more-wrapper="true">
			<i class="loader eicon-loading"></i>
			<button class="fy-button--full" data-load-more data-next-offset="<?php echo $next_offset; ?>" data-params='<?php echo json_encode($query_params); ?>' data-parents-cache='<?php echo json_encode($parents_cache); ?>'>
				<span><?php _e('Carica altri', 'futureyoga'); ?></span>
			</button>
		</div>
	<?php endif;
}

add_filter( 'auto_update_plugin', '__return_false' );