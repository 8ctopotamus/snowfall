<?php

function nice_date($dateString) {
  return date("F jS, Y", strtotime($dateString)); 
}

function generate_post_content($data) {
  global $statesByAbbreviation;
  $cityState = $data['City'] . ', '. $statesByAbbreviation[$data['STATE']];
  $twoDaysEndDate = date('Y-m-d', strtotime($data['2 days DATE'] . " + 1 day"));
  $threeDaysEndDate = date('Y-m-d', strtotime($data['3 days DATE'] . " + 2 days"));

  $content = "Businesses in $cityState rely on accurate snowfall record-keeping to plan for the most extreme conditions during the winter. Snow plow removal companies, Municipalities, Universities and others that fight snow can plan for the worst with this data...";

  // tabs
  $content .= '<h2>1, 2 & 3 Day Snow Accumulation Records</h2>
  <div id="chart-tabs" class="tabs">
    <nav class="tab-list">
      <a class="tab tab-active" href="#one" data-idx="1">One Day</a>
      <a class="tab" href="#two" data-idx="2">Two Days</a>
      <a class="tab" href="#three" data-idx="3">Three Days</a>
    </nav>';

  // one day tab
  $content .= '<div id="one" class="tab-content tab-show">';
    $content .= '<h3>Greatest Amount of Snow in One Day:</h3>';
    $content .= "<strong>" . $data['1 days QTY'] . "in.</strong> — The one-day record snowfall for $cityState was recorded on " . nice_date( $data['1 days DATE'] ) . " as seen in the chart below.";
    $content .= '<canvas id="most-snow-1-days" aria-label="Most snow in one day" role="img">
      <small>Greatest Amount of Snow in One Day</small>
    </canvas>
  </div>';

  // two days tab
  $content .= '<div id="two" class="tab-content">';
    $content .= '<h3>Greatest Amount of Snow in Two Days:</h3>';
    $content .= "<strong>" . $data['2 days QTY'] . "in.</strong> — The two-day cumulative snow record covered the period from " . nice_date( $data['2 days DATE'] ) . " to" . nice_date( $twoDaysEndDate ) . ".";
    $content .= '<canvas id="most-snow-2-days"> aria-label="Most snow in two days" role="img">
      <small>Greatest Amount of Snow in Two Days</small>
    </canvas>
  </div>';

  // three days tab
  $content .= '<div id="three" class="tab-content">';
    $content .= '<h3>Greatest Amount of Snow in Three Days:</h3>';
    $content .= "<strong>" . $data['3 days QTY'] . "in.</strong> — The two-day cumulative snow record covered the period from " . nice_date( $data['3 days DATE'] ) . " to" . nice_date( $threeDaysEndDate ) . ".";
    $content .= '<canvas id="most-snow-3-days">aria-label="Most snow in three days" role="img">
      <small>Greatest Amount of Snow in Three Days</small>
    </canvas>
  </div>';
  
  $content .= '<p class="text-center">These snow records are reviewed yearly and updated as new data is recorded.</p>';

  $content .= '</div>'; // end of tabs

  $content .= "SPN enlisted the help of the leading winter-expert Meteorologists to collect snow records throughout the United States. SPN has assimilated snow record data from over 220 U.S. Cities including $cityState. Snowfall records are reviewed and updated yearly.";
  
  // greatest snowfall
  $content .= '<h3>Greatest Snowfall in One Season:</h3>';

  $content .= $data['Greatest Snowfall'] . " inches - The greatest cumulative snow fall for $cityState. This occurred during the year that ended " . nice_date( $data['GreatestEndingDate'] ) . ".  This was surely a banner year for snowplow contractors!";

  $content .= '<canvas id="greatest-snowfall" aria-label="Greatest Snowfall in One Season" role="img">
    <small>Greatest Snowfall in One Season</small>
  </canvas>';

  $content .= 'United States snow records can be found by reviewing the <a href="' . site_url() . '/snowfall_records">snowfall records of over 200 cities</a>.</p>';

  $content .= "<h3>Last Year's Storms - 12 Months in 1 Minute</h3>";
    
  $content .= '<iframe src="https://www.snowplownews.com/snow-precipitation-animated.cfm" border="0" marginheight="0" marginwidth="0" scrolling="no" width="410" height="300" frameborder="1"></iframe>';
  $content .= '<a href="https://www.snowplownews.com/winter-weather-news.cfm" title="Snow Plow News - Winter Weather News / Snow Research"><p>Source: <cite>Snow Plow News - Winter Weather News / Snow Research</a></cite></p></a>';
  
  $content .= "<p>Our memory of the past winter can sometimes be judged by a strong beginning to the winter, a long & cold winter season or a massive snowstorm that tested all snow removal equipment at the end of the season.</p>
  <p>For snow and ice contractors, the winter is typically judged by how much money made it into the bank. For municipal workers, overtime.</p> 
  <p>This chart shows the nature of all the winter storms over the past season - in just 1 minute. Enjoy the show!</p>";
  
  $content .= '<p>For additional snow and winter records research you can check out the <a href="' . site_url() . '/snowfall_records">SPN snow records page</a>.</p>';
  
  return $content;
}

function generate_posts($city_data) {
  global $statesByAbbreviation;
  foreach($city_data as $data) {
    $postarr = [
      'post_title' => 'Official Snowfall Records for ' . $data['City'] . ', ' . $statesByAbbreviation[$data['STATE']],
      'post_name' => $data['City'] . '-' . urlencode($statesByAbbreviation[$data['STATE']]), // slug
      'post_type' => 'snowfall_records',
      'post_status' => 'publish',
      'post_content' => generate_post_content($data),
      'meta_input' => $data,
    ];
    wp_insert_post($postarr);
  }
}

function delete_custom_posts(){
  global $wpdb;
  $post_type = 'snowfall_records';
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