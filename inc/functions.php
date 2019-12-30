<?php


function generate_posts($city_data) {
  foreach($city_data as $data) {
    // echo '<pre>';
    // var_dump($city);
    // echo '</pre>';

    // $content = 'First snows, Record snow, Multi-Day storms, Blizzards, and general snowfall information can be tough to find. You might be looking to move to a new city or have just moved and you are maybe fearful of how bad winter can get.

    // <a href="https://www.snowplownews.com/winter-weather-news.cfm">Source</a>

    // Winter snow records are typically hard to find so we first looked toward the NOAA (National Oceanic and Atmospheric Association) to see if these all-important records could be found.';

    $content = 'Test';

    $postarr = [
      'post_title' => 'Snow Records for ' . $data['City'] . ', ' . $data['STATE'],
      'post_type' => 'snowfall_cities',
      'post_status' => 'publish',
      'post_content' => $content,
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
  // header('Location: ' . $_SERVER['HTTP_REFERER']);
  // exit;
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