<?php

use \Doctrine\Common\Inflector\Inflector;

function singularize($str)
{
  return Inflector::singularize($str);
}

function strip_format($str)
{
  $temp = str_replace('.php', '', $str);
  return $temp;
}

function lowercase($str)
{
  $lower = strip_format($str);
  $lower = str_replace('-', '_', $lower);
  $lower = strtolower($lower);
  return $lower;
}

function uppercase($str)
{
  $upper = strip_format($str);
  $upper = str_replace('-', ' ', $upper);
  $upper = str_replace('_', ' ', $upper);
  $upper = ucwords($upper);
  return $upper;
}

function capitalize($str)
{
  $caps = strip_format($str);
  $caps = ucfirst($caps);
  return $caps;
}

function redirect_layouts_home()
{
  header("Location: /wp-admin/admin.php?page=nextlevel_layouts");
  die();
}

function create_layout_files($lower, $caps, $upper)
{
  $customizer_template = dirname(__FILE__, 2) . "/layouts/template.php";
  $layout_template = dirname(__FILE__, 2) . "/template.php";
  $filename = str_replace('_', '-', $lower);

  // Customizer Class
  copy($customizer_template, dirname(__FILE__, 2) . "/layouts/$filename.php");
  $customizer = dirname(__FILE__, 2) . "/layouts/$filename.php";

  $replaced_lower = str_replace("REPLACE_ME_LOWER", $lower, file_get_contents($customizer));
  file_put_contents($customizer, $replaced_lower);

  $replaced_caps = str_replace("REPLACE_ME_CAPS", $caps, file_get_contents($customizer));
  file_put_contents($customizer, $replaced_caps);

  $replaced_upper = str_replace("REPLACE_ME_UPPER", $upper, file_get_contents($customizer));
  file_put_contents($customizer, $replaced_upper);

  // WordPress Layout
  copy($layout_template, dirname(__FILE__, 2) . "/$filename.php");
  $layout = dirname(__FILE__, 2) . "/$filename.php";

  $replaced_lower = str_replace("REPLACE_ME_LOWER", $lower, file_get_contents($layout));
  file_put_contents($layout, $replaced_lower);

  $replaced_upper = str_replace("REPLACE_ME_UPPER", $upper, file_get_contents($layout));
  file_put_contents($layout, $replaced_upper);
}
