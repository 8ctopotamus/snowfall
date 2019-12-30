<?php

function generate_post_content($data) {
  $content = '<p>First snows, Record snow, Multi-Day storms, Blizzards, and general snowfall information can be tough to find. You might be looking to move to a new city or have just moved and you are maybe fearful of how bad winter can get.</p>';
  $content .= '<p>Source: <cite><a href="https://www.snowplownews.com/winter-weather-news.cfm">Snow Plow News - Winter Weather News / Snow Research</a></cite></p>';
  $content .= '<img src="'. esc_url( plugins_url( '../img/spn-weather-research-map.jpg', __FILE__ ) ) .'" alt="Snow Plow News Winter Weather Research Screenshot">';
  $content .= '<p>Winter snow records are typically hard to find so we first looked toward the NOAA (National Oceanic and Atmospheric Association) to see if these all-important records could be found.</p><p>Here is the records section of the NOAA site – it is not that easy to navigate.  We also found that these snowfall records do not exist in one spot – the records come from many reporting authorities all over the U.S.</p>';
  $content .= '<img src="'. esc_url( plugins_url( '../img/noa-screenshot.jpg', __FILE__ ) ) .'" alt="Snow Plow News Winter Weather Research Screenshot">';
  $content .= '<p>So we enlisted the help of some of the leading winter-expert Meteorologists to collect snow records throughout the United States. SPN has assimilated snow record data from over 220 U.S. Cities including ' . $data['City'] . ', '. $data['STATE'] . '.</p>';
  return $content;
}

function generate_posts($city_data) {
  foreach($city_data as $data) {
    // echo '<pre>';
    // var_dump($city);
    // echo '</pre>';

    $postarr = [
      'post_title' => 'Snow Records for ' . $data['City'] . ', ' . $data['STATE'],
      'post_type' => 'snowfall_cities',
      'post_status' => 'publish',
      'post_content' => generate_post_content($data),
    ];
    // echo '<pre>';
    // var_dump($postarr);
    // echo '</pre>';
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
  return $result!==false;
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
    delete_custom_posts();
    generate_posts($city_data);
  } else {
    echo 'No CSV provided.';
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