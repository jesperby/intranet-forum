<?php

function komin_preprocess_html(&$variables) {


  $variables['site_name'] = variable_get('site_frontpage', url());

  $theme_path = url(drupal_get_path('theme', 'komin'));

  $variables['komin_style'] = $theme_path . '/css/application.css';
  $variables['komin_script'] = $theme_path . '/js/application.js';


}