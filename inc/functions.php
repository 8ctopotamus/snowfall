<?php

function nice_date($dateString) {
  return date("F jS, Y", strtotime($dateString)); 
}

function generate_post_content($data) {
  $cityState = $data['City'] . ', '. $data['STATE'];
  $twoDaysEndDate = date('Y-m-d', strtotime($data['2 days DATE'] . " + 1 day"));
  $threeDaysEndDate = date('Y-m-d', strtotime($data['3 days DATE'] . " + 2 days"));

  $content = '<p>First snows, Record snow, Multi-Day storms, Blizzards, and general snowfall information can be tough to find. You might be looking to move to a new city or have just moved and you are maybe fearful of how bad winter can get.</p>';

  $content .= '<!--more-->';

  // tabs
  $content .= '<div id="chart-tabs" class="tabs">
  <nav class="tab-list">
    <a class="tab tab-active" href="#one" data-idx="1">One Day</a>
    <a class="tab" href="#two" data-idx="2">Two Days</a>
    <a class="tab" href="#three" data-idx="3">Three Days</a>
  </nav>';

  // one day tab
  $content .= '<div id="one" class="tab-content tab-show">';
    $content .= '<h3>Greatest Amount of Snow in One Day:</h3>';
    $content .= '<p>The record of a one day snowfall for '. $cityState .' is ' . $data['1 days QTY'] . ' inches occurring on ' . nice_date( $data['1 days DATE'] ) . '.</p>';
    $content .= '<canvas id="most-snow-1-days" aria-label="Most snow in one day" role="img">
      <small>Greatest Amount of Snow in One Day</small>
    </canvas>
  </div>';

  // two days tab
  $content .= '<div id="two" class="tab-content">';
    $content .= '<h3>Greatest Amount of Snow in Two Days:</h3>';
    $content .= 'The record of a one day snowfall for ' . $cityState . ' is ' . $data['2 days QTY'] . ' inches started on ' . nice_date( $data['2 days DATE'] ) . ' and ended on ' . nice_date( $twoDaysEndDate ) . '.';
    $content .= '<canvas id="most-snow-2-days"> aria-label="Most snow in two days" role="img">
      <small>Greatest Amount of Snow in Two Days</small>
    </canvas>
  </div>';

  // three days tab
  $content .= '<div id="three" class="tab-content">';
    $content .= '<h3>Greatest Amount of Snow in Three Days:</h3>';
    
    $content .= '<canvas id="most-snow-3-days">aria-label="Most snow in three days" role="img">
      <small>Greatest Amount of Snow in Three Days</small>
    </canvas>';
    $content .= 'The record of a one day snowfall for ' . $cityState . ' is ' . $data['3 days QTY'] . ' inches started on ' . nice_date( $data['3 days DATE'] ) . ' and ended on ' . nice_date( $threeDaysEndDate ) . '.
  </div>';

  $content .= '</div>'; // end of tabs
  
  // greatest snowfall
  $content .= '<h3>Greatest Snowfall in One Season:</h3>';
  $content .= '<p>At this point most folks are wondering what the greatest amount of snow has been recorded for ' . $cityState . ' in any given season.</p>';
  $content .= '<p>The greatest cumulative snow fall for ' . $cityState . ' is ' . $data['Greatest Snowfall'] . ' inches for the year ending ' . nice_date( $data['GreatestEndingDate'] ) . '.</p>';
  $content .= '<canvas id="greatest-snowfall" aria-label="Greatest Snowfall in One Season" role="img">
    <small>Greatest Snowfall in One Season</small>
  </canvas>';

  $content .= "<h3>Last Year's Storms - 12 Months in 1 Minute</h3>";
  $content .= '<iframe src="https://www.snowplownews.com/snow-precipitation-animated.cfm" border="0" marginheight="0" marginwidth="0" scrolling="no" width="410" height="300" frameborder="1"></iframe>';

  $content .= '<a href="https://www.snowplownews.com/winter-weather-news.cfm" title="Snow Plow News - Winter Weather News / Snow Research"><p>Source: <cite>Snow Plow News - Winter Weather News / Snow Research</a></cite></p></a>';

  $content .= '<p>Winter snow records are typically hard to find so we first looked toward the <a href="https://www.noaa.gov" title="NOAA website" target="_blank" rel="noreferrer noopener">NOAA (National Oceanic and Atmospheric Association)</a> to see if these all-important records could be found.</p>
  <p>Here is the records section of the NOAA site – it is not that easy to navigate.  We also found that these snowfall records do not exist in one spot – the records come from many reporting authorities all over the U.S.</p>';
  $content .= '<img src="'. esc_url( plugins_url( '../img/noa-screenshot.jpg', __FILE__ ) ) .'" alt="Snow Plow News Winter Weather Research Screenshot">';
  $content .= '<p>So we enlisted the help of some of the leading winter-expert Meteorologists to collect snow records throughout the United States. SPN has assimilated snow record data from over 220 U.S. Cities including ' . $cityState . '.</p>';
  
  $content .= '<p>For additional snow and winter records research you can check out the <a href="' . site_url() . '/snowfall_cities">SPN snow records page</a>.</p>';

  return $content;
}

function generate_posts($city_data) {
  foreach($city_data as $data) {
    $postarr = [
      'post_title' => 'Snow Records for ' . $data['City'] . ', ' . $data['STATE'],
      'post_type' => 'snowfall_cities',
      'post_status' => 'publish',
      'post_content' => generate_post_content($data),
      'meta_input' => $data,
    ];
    wp_insert_post($postarr);
  }
}

function delete_custom_posts(){
  global $wpdb;
  $post_type = 'snowfall_cities';
  $result = $wpdb->query( 
    $wpdb->prepare("
      DELETE posts,pt,pm
      FROM wp_posts posts
      LEFT JOIN wp_term_relationships pt ON pt.object_id = posts.ID
      LEFT JOIN wp_postmeta pm ON pm.post_id = posts.ID
      WHERE posts.post_type = %s
      ", 
      $post_type
    )
  );
  return $result !== false;
}

function parse_csv($csv) {
  $fileHandle = fopen($csv, "r");
  $keys = [];
  $data = [];
  $count = 0;
  while (($row = fgetcsv($fileHandle, 0, ",")) !== FALSE) {
    if ($count >= 1) {
      $city = [];
      for($i = 0; $i < count($keys); $i++) {
        $city[$keys[$i]] = $row[$i];
      }
      $data[] = $city;
    } else {
      $keys = $row;
    }
    $count++;
  }
  return $data;
}

function upload_csv() {
  if ( isset($_POST["submit"]) && $_FILES['csv_file']['size'] > 0 ) {
    $csv = $_FILES['csv_file']['tmp_name'];
    $city_data = parse_csv($csv);
    update_option( 'all_snowfall_data', $city_data);
    delete_custom_posts();
    generate_posts($city_data);
  } else {
    echo 'Error: No CSV provided.';
    http_response_code(500);
  }
  header('Location: ' . $_SERVER['HTTP_REFERER']);
  exit;
}

// NOTE: use this to see the CSV data
// function render_CSV_table($filepath) {
//   $fileHandle = fopen($filepath, "r");
//   $html = '<table>';
//   while (($row = fgetcsv($fileHandle, 0, ",")) !== FALSE) {
//     $html .= '<tr>';
//     foreach ($row as $col ) {
//       $html .= '<td>';
//       $html .= $col;
//       $html .= '</td>';      
//     }
//     $html .= '</tr>';
//   }
//   $html .= '<table>';
//   return $html;
// }