<?php 

add_action( 'admin_menu', 'snowfall_options_page' );

function snowfall_options_page() {
  add_menu_page(
    'Snowfall',
    'Snowfall',
    'manage_options',
    'snowfall',
    'snowfall_options_page_html',
    ''
  );
}

function snowfall_options_page_html() {
?>
  <div class="wrap">
        
    <div class="header-flex">
      <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
      <form action="<?php echo esc_attr( admin_url( 'admin-ajax.php' ) ); ?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="snowfall_actions" />
        <input type="hidden" name="do" value="upload_csv" />
        <input type="hidden" name="redirect" value='admin.php?page=snowfall' />        
        <label for="csv_file">Select CSV to upload:</label>
        <input type="file" name="csv_file" id="csv_file">
        <input type="submit" value="Upload CSV" name="submit" class="button button-primary">
      </form>
    </div>

  </div><!-- .wrap -->
  <?php
}