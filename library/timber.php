<?php

$timber = new Timber\Timber();
Timber::$dirname = array('templates', 'partials');
Timber::$autoescape = false;

class MySite extends Timber\Site
{
  public function __construct()
  {
    add_filter('timber/context', array($this, 'add_to_context'));
    add_filter('timber/twig', array($this, 'add_to_twig'));
    add_action('after_setup_theme', array($this, 'theme_supports'));
    parent::__construct();
  }

  public function add_to_context($context)
  {
    $context['main_menu'] = new Timber\Menu('main-menu');
    $context['sidebar_menu'] = new Timber\Menu('sidebar-menu');
    $context['location_menu'] = new Timber\Menu('location-menu');

    $context['categories'] = get_categories(array(
      'orderby' => 'name',
      'order'   => 'ASC',
      'exclude' => '1',
      'hide_empty' => '1'
    ));


    return $context;
  }

  public function add_to_twig($twig)
  {
    $twig->addExtension(new Twig_Extension_StringLoader());
    return $twig;
  }

  public function theme_supports()
  {
    add_theme_support('automatic-feed-links');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('menus');
    add_theme_support('wp-block-styles');
  }
}
new MySite();
