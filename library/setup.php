<?php


function get_local($asset = '')
{
  return get_stylesheet_directory_uri() . '/dist' . '/' . $asset;
}

function my_scripts()
{
  wp_deregister_script('jquery');
  wp_deregister_script('jquery-migrate');

  wp_enqueue_style('customtheme-styles', get_local('app.css'), array());
  wp_enqueue_style('customtheme-blockstyles', get_local('custom-blocks.css'), array());
  wp_dequeue_style('wp-block-library');
  wp_dequeue_style('wp-block-library-theme');
}
add_action('wp_enqueue_scripts', 'my_scripts');
