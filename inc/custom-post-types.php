<?php

function snowfall_cities_custom_post_type() {

	$args = array(
		'labels' => array(
			'name'          => __('Snowfall Cities'),
			'singular_name' => __('Snowfall City'),
		),
		'menu_icon' => 'dashicons-building',
		'public'      => true,
		'has_archive' => true,
	);

	register_post_type('snowfall_cities', $args);
}

add_action('init', 'snowfall_cities_custom_post_type');

