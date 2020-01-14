<?php
/**
 * Adds "Import" button and form on listing page
 */

add_action('admin_head-edit.php','addCustomImportButton');

function addCustomImportButton() {
  global $current_screen;

  if ('snowfall_cities' != $current_screen->post_type) {
    return;
  }

  ?>
    <script type="text/javascript">
      (function($) {
        $(document).ready( function() {
          const uploadFormHTML = `<form id="csv-upload" action="<?php echo esc_attr( admin_url( 'admin-ajax.php' ) ); ?>" method="post" enctype="multipart/form-data" style="display: none; background: lightblue; padding: 10px; margin: 10px 0;">
            <input type="hidden" name="action" value="snowfall_actions" />
            <input type="hidden" name="do" value="upload_csv" />
            <input type="hidden" name="redirect" value='admin.php?page=snowfall' />        
            <label for="csv_file">Select CSV to upload:</label>
            <input type="file" name="csv_file" id="csv_file">
            <input type="submit" value="Upload CSV" name="submit" class="button button-primary">
          </form>`;
          
          $($(".wrap .page-title-action")[0])
            .hide()
            .after(uploadFormHTML)
            .after('<a href="#" class="page-title-action toggle-upload">Import CSV</a>');
          
          $('body').on('click', '.toggle-upload', function() {
            const ok = confirm('I understand that uploading a CSV will overwrite all existing posts.');
            if (ok)
              $('#csv-upload').toggle();
          });
        });
      })(jQuery)
    </script>
  <?php
}
