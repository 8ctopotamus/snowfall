<?php

function snowfall_records_custom_post_type() {
	$args = array(
		'labels' => array(
			'name'          => __('Snowfall Records'),
			'singular_name' => __('Snowfall Record'),
		),
		'public'      => true,
		'has_archive' => true,
		'menu_icon' => 'dashicons-chart-bar',
		'description' => 'Snow Plow News works with expert meteorologists to track and report snow records in key cities throughout the United States. 224 Cities are tracked in our constantly updated database.',
	);

	register_post_type('snowfall_records', $args);
}

add_action('init', 'snowfall_records_custom_post_type');

