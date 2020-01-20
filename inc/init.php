<?php 

function snowfall_scripts() {
  global $post;
  $all_cities = get_option('all_snowfall_data');
  $current_city = get_post_meta( $post->ID);

  wp_register_style( 'snowfall_styles', plugins_url('css/style.css', __DIR__ ) );  
  wp_register_script( 'chart_js', 'https://cdn.jsdelivr.net/npm/chart.js@2.8.0', array(), false, true );
  wp_register_script( 'snowfall_single', plugins_url('js/snowfall-single.js', __DIR__ ), array(), false, true );
  
  if (is_archive('snowfall_cities') ) {
    wp_enqueue_style( 'snowfall_styles' );
  }

  if (is_singular('snowfall_cities')) {
    wp_enqueue_style( 'snowfall_styles' );
    wp_enqueue_script( 'chart_js' );
    wp_localize_script( 'snowfall_single', 'wp_data', array(
      'all_snowfall_data' => $all_cities,
      'current_city' => $current_city,
    ) );
    wp_enqueue_script('snowfall_single');
  }
}
add_action('wp_enqueue_scripts', 'snowfall_scripts');


// Enable ajax routes
add_action( 'wp_ajax_snowfall_actions', 'snowfall_actions' );
function snowfall_actions() {
  include( plugin_dir_path( __DIR__ ) . 'inc/actions.php' );
}

// order Archive alphabetically
function alpha_order_classes( $query ) {
  if ( $query->is_post_type_archive('snowfall_cities') && $query->is_main_query() ) {
    $query->set( 'orderby', 'title' );
    $query->set( 'order', 'ASC' );
  }
}
add_action( 'pre_get_posts', 'alpha_order_classes' );

// Custom archive template
function snowfall_records_load_templates($original_template) {
  if (is_post_type_archive('snowfall_cities')) {
    return plugin_dir_path(__DIR__) . 'templates/archive-snowfall_cities.php';
  }
  return $original_template;
}
add_action('template_include', 'snowfall_records_load_templates');