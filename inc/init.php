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