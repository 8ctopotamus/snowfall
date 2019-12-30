<?php 

// Ajax routes
add_action( 'wp_ajax_snowfall_actions', 'snowfall_actions' );
function snowfall_actions() {
  include( plugin_dir_path( __DIR__ ) . 'inc/actions.php' );
}


/*
 * Admin scripts and styles
 */
function snowfall_wp_admin_assets( $hook ) {
  wp_register_style('snowfall_admin_styles', plugin_dir_url( __DIR__ ) . '/css/admin.css', false, '1.0.0');
  wp_register_script('snowfall_admin_js', plugin_dir_url( __DIR__ ) . '/js/admin.js', array('jquery'), '', true);

  if ( $hook === 'toplevel_page_snowfall' ) {
    wp_enqueue_style( 'snowfall_admin_styles' );
    wp_localize_script( 'snowfall_admin_js', 'wp_data', array(
      'ajax_url' => admin_url( 'admin-ajax.php' ),
    ));
    wp_enqueue_script( 'snowfall_admin_js' );
  }
}
add_action( 'admin_enqueue_scripts', 'snowfall_wp_admin_assets' );


/**
 * Adds "Import" button and form on listing page
 */
function addCustomImportButton() {
  global $current_screen;

  if ('snowfall_cities' != $current_screen->post_type) {
    return;
  }

  ?>
    <script type="text/javascript">
      (function($) {
        $(document).ready( function() {
          const uploadFormHTML = `<form id="csv-upload" action="<?php echo esc_attr( admin_url( 'admin-ajax.php' ) ); ?>" method="post" enctype="multipart/form-data" style="display: none;">
            <input type="hidden" name="action" value="snowfall_actions" />
            <input type="hidden" name="do" value="upload_csv" />
            <input type="hidden" name="redirect" value='admin.php?page=snowfall' />        
            <label for="csv_file">Select CSV to upload:</label>
            <input type="file" name="csv_file" id="csv_file">
            <input type="submit" value="Upload CSV" name="submit" class="button button-primary">
          </form>`;
          
          $($(".wrap .page-title-action")[0])
            .after(uploadFormHTML)
            .after('<a href="#" class="page-title-action toggle-upload">Import CSV</a>');
          
          $('body').on('click', '.toggle-upload', function() {
            $('#csv-upload').toggle();
          });
        });
      })(jQuery)
    </script>
  <?php
}
add_action('admin_head-edit.php','addCustomImportButton');