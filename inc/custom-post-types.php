<?php

function snowfall_cities_custom_post_type() {
	$args = array(
		'labels' => array(
			'name'          => __('Snowfall Cities'),
			'singular_name' => __('Snowfall City'),
		),
		'public'      => true,
		'has_archive' => true,
		'menu_icon' => 'dashicons-building',
		'description' => 'Snow Plow News works with expert meteorologists to track and report snow records in key cities throughout the United States. 224 Cities are tracked in our constantly updated database.',
	);

	register_post_type('snowfall_cities', $args);
}

add_action('init', 'snowfall_cities_custom_post_type');

