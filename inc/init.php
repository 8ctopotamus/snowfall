<?php 

$statesByAbbreviation = [
  "AL" => "Alabama",
  "AK" => "Alaska",
  "AZ" => "Arizona",
  "AR" => "Arkansas",
  "CA" => "California",
  "CO" => "Colorado",
  "CT" => "Connecticut",
  "DE" => "Delaware",
  "FL" => "Florida",
  "GA" => "Georgia",
  "HI" => "Hawaii",
  "ID" => "Idaho",
  "IL" => "Illinois",
  "IN" => "Indiana",
  "IA" => "Iowa",
  "KS" => "Kansas",
  "KY" => "Kentucky",
  "LA" => "Louisiana",
  "ME" => "Maine",
  "MD" => "Maryland",
  "MA" => "Massachusetts",
  "MI" => "Michigan",
  "MN" => "Minnesota",
  "MS" => "Mississippi",
  "MO" => "Missouri",
  "MT" => "Montana",
  "NE" => "Nebraska",
  "NV" => "Nevada",
  "NH" => "New Hampshire",
  "NJ" => "New Jersey",
  "NM" => "New Mexico",
  "NY" => "New York",
  "NC" => "North Carolina",
  "ND" => "North Dakota",
  "OH" => "Ohio",
  "OK" => "Oklahoma",
  "OR" => "Oregon",
  "PA" => "Pennsylvania",
  "RI" => "Rhode Island",
  "SC" => "South Carolina",
  "SD" => "South Dakota",
  "TN" => "Tennessee",
  "TX" => "Texas",
  "UT" => "Utah",
  "VT" => "Vermont",
  "VA" => "Virginia",
  "WA" => "Washington",
  "WV" => "West Virginia",
  "WI" => "Wisconsin",
  "WY" => "Wyoming",
];

function snowfall_scripts() {
  global $statesByAbbreviation;
  global $post;
  $all_cities = get_option('all_snowfall_data');
  $current_city = get_post_meta($post->ID);

  wp_register_style( 'snowfall_styles', plugins_url('css/style.css', __DIR__ ) );  
  wp_register_script( 'chart_js', 'https://cdn.jsdelivr.net/npm/chart.js@2.8.0', array(), false, true );
  wp_register_script( 'snowfall_records_single', plugins_url('js/snowfall-single.js', __DIR__ ), array(), false, true );
  
  if (is_archive('snowfall_records') ) {
    wp_enqueue_style( 'snowfall_styles' );
  }

  if (is_singular('snowfall_records')) {
    wp_enqueue_style( 'snowfall_styles' );
    wp_enqueue_script( 'chart_js' );
    wp_localize_script( 'snowfall_records_single', 'wp_data', array(
      'all_snowfall_data' => $all_cities,
      'current_city' => $current_city,
      'site_url' => site_url(),
      'statesByAbbreviation' => $statesByAbbreviation,
    ) );
    wp_enqueue_script('snowfall_records_single');
  }
}
add_action('wp_enqueue_scripts', 'snowfall_scripts');

// Enable ajax routes
add_action( 'wp_ajax_snowfall_actions', 'snowfall_actions' );
function snowfall_actions() {
  include( plugin_dir_path( __DIR__ ) . 'inc/actions.php' );
}

// load custom archive template
function snowfall_records_load_templates($original_template) {
  if (is_post_type_archive('snowfall_records')) {
    return plugin_dir_path(__DIR__) . 'templates/archive-snowfall_records.php';
  }
  return $original_template;
}
add_action('template_include', 'snowfall_records_load_templates');

// archive query
function snowfall_records_archive_query( $query ) {
  if ( $query->is_post_type_archive('snowfall_records') && $query->is_main_query() && !is_admin() ) {
    $query->set( 'posts_per_page', -1 );
  }
}
add_action( 'pre_get_posts', 'snowfall_records_archive_query' );

// Single Snow Record Related Cities
function snow_record_content_filter($content) {
  $fullContent = $content;
  if (is_singular('snowfall_records')) {
    $current_city = get_post_meta(get_the_ID());
    // related cities (in same state)
    $query = new WP_Query( array( 
      'post_type' => 'any',
      'meta_key' => 'STATE',
      'meta_value' => $current_city['STATE'][0],
    ) );
    if ( $query->have_posts() ) {
      $fullContent .= "<h3>Other cities in " . $current_city['STATE'][0] . " that can be researched include:</h3>";
      $fullContent .= "<ul>";
      while ( $query->have_posts() ) {
          $query->the_post();
          $fullContent .= '<li><a href="' . get_the_permalink() . '">' . get_the_title() . '</a></li>';
      }
      $fullContent .=  '</ul>';
    }
    wp_reset_postdata();
    $fullContent .= '</ul>';
  }
  return $fullContent;
}
add_filter('the_content', 'snow_record_content_filter');

// Add back link to snowfall_records Archive to posts.
function snow_records_filter_the_title( $content ) {
  if (is_singular('snowfall_records')):
    $custom_content = '<p><a href="' . site_url("/snowfall_records/") . '">&larr; View all records</a></p>';
    $content = $custom_content . $content;
  endif;
  return $content;
}
add_filter( 'the_content', 'snow_records_filter_the_title' );