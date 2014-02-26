<?php

function komin_preprocess_html(&$variables) {

  $variables['site_name'] = variable_get('site_frontpage', url());

  $theme_path = url(drupal_get_path('theme', 'komin'));

  $variables['komin_style'] = $theme_path . '/css/application.css';
  $variables['komin_script'] = $theme_path . '/js/application.js';

  // Set development class
  $variables['classes_array'][] = 'development';

  // Set Malmö Assets classes
  $variables['classes_array'][] = 'malmo-form';
  $variables['classes_array'][] = 'malmo-masthead-more';
}

function komin_preprocess_button(&$vars) {
  $vars['element']['#attributes']['class'][] = 'btn';
  if (isset($vars['element']['#id']) && $vars['element']['#id'] == 'edit-submit') {
    $vars['element']['#attributes']['class'][] = 'btn-primary';
  }
}

function komin_form_alter(&$form, &$form_state, $form_id) {

  /*
   * Add class to form tag
   */
  if (isset($form['#attributes']['class'])) {
    $additional_classes = array();
    foreach ($form['#attributes']['class'] as $class) {
      switch ($class) {
        case 'node-forum-form':
          $additional_classes[] = 'form-horizontal';
          $additional_classes[] = 'control-group';
          break;
      }
    }

    foreach ($additional_classes as $class) {
      if (!in_array($class, $form['#attributes']['class'])) {
        $form['#attributes']['class'][] = $class;
      }
    }
  }

  if($form_id == 'forum_node_form') {
    // After build hook to hide text formatting options
    $form['#after_build'][] = 'komin_forum_node_form_after_build';
    $form['title']['#attributes']['class'][] = 'input-wide';
  } else if($form_id == 'comment_node_forum_form') {
    // After build hook to hide text formatting options
    $form['#after_build'][] = 'komin_comment_node_forum_form_after_build';
    // Hide the subject
	unset($form['subject']);
    // Hide the author
	unset($form['author']['_author']);
  }
}

function komin_form_element(&$variables) {
  $element = & $variables['element'];
  $is_checkbox = FALSE;
  $is_radio = FALSE;

  // This function is invoked as theme wrapper, but the rendered form element
  // may not necessarily have been processed by form_builder().
  $element += array(
    '#title_display' => 'before',
  );

  // Add element #id for #type 'item'.
  if (isset($element['#markup']) && !empty($element['#id'])) {
    $attributes['id'] = $element['#id'];
  }

  // Check for errors and set correct error class.
  if (isset($element['#parents']) && form_get_error($element)) {
    $attributes['class'][] = 'error';
  }

  if (!empty($element['#type'])) {
    $attributes['class'][] = 'form-type-' . strtr($element['#type'], '_', '-');
  }
  if (!empty($element['#name'])) {
    $attributes['class'][] = 'form-item-' . strtr(
        $element['#name'], array(
          ' ' => '-',
          '_' => '-',
          '[' => '-',
          ']' => '',
        )
      );
  }

  // Malmostad
  $attributes['class'][] = 'control-group';

  // Add a class for disabled elements to facilitate cross-browser styling.
  if (!empty($element['#attributes']['disabled'])) {
    $attributes['class'][] = 'form-disabled';
  }
  if (!empty($element['#autocomplete_path']) && drupal_valid_path($element['#autocomplete_path'])) {
    $attributes['class'][] = 'form-autocomplete';
  }
  $attributes['class'][] = 'form-item';

  // See http://getbootstrap.com/css/#forms-controls.
  if (isset($element['#type'])) {
    if ($element['#type'] == "radio") {
      $attributes['class'][] = 'radio';
      $is_radio = TRUE;
    }
    elseif ($element['#type'] == "checkbox") {
      $attributes['class'][] = 'checkbox';
      $is_checkbox = TRUE;
    }
    else {
      $attributes['class'][] = 'form-group';
    }
  }

  $description = FALSE;
  $tooltip = FALSE;
  // Convert some descriptions to tooltips.
  // @see bootstrap_tooltip_descriptions setting in _bootstrap_settings_form()
  if (!empty($element['#description'])) {
    $description = $element['#description'];
    if (theme_get_setting('bootstrap_tooltip_enabled') && theme_get_setting('bootstrap_tooltip_descriptions')
      && $description === strip_tags($description)
      && strlen($description) <= 200
    ) {
      $tooltip = TRUE;
      $attributes['data-toggle'] = 'tooltip';
      $attributes['title'] = $description;
    }
  }

  $output = '<div' . drupal_attributes($attributes) . '>' . "\n";

  // If #title is not set, we don't display any label or required marker.
  if (!isset($element['#title'])) {
    $element['#title_display'] = 'none';
  }

  $prefix = '<div class="controls">';
  $suffix = '</div>';
  if (isset($element['#field_prefix']) || isset($element['#field_suffix'])) {
    // Determine if "#input_group" was specified.
    if (!empty($element['#input_group'])) {
      $prefix .= '<div class="input-group">';
      $prefix .= isset($element['#field_prefix']) ?
        '<span class="input-group-addon">' . $element['#field_prefix'] . '</span>' : '';
      $suffix .= isset($element['#field_suffix']) ?
        '<span class="input-group-addon">' . $element['#field_suffix'] . '</span>' : '';
      $suffix .= '</div>';
    }
    else {
      $prefix .= isset($element['#field_prefix']) ? $element['#field_prefix'] : '';
      $suffix .= isset($element['#field_suffix']) ? $element['#field_suffix'] : '';
    }
  }

  switch ($element['#title_display']) {
    case 'before':
    case 'invisible':
      $output .= ' ' . theme('form_element_label', $variables);
      $output .= ' ' . $prefix . $element['#children'] . $suffix . "\n";
      break;

    case 'after':
      if ($is_radio || $is_checkbox) {
        $output .= ' ' . $prefix . $element['#children'] . $suffix;
      }
      else {
        $variables['#children'] = ' ' . $prefix . $element['#children'] . $suffix;
      }
      $output .= ' ' . theme('form_element_label', $variables) . "\n";
      break;

    case 'none':
    case 'attribute':
      // Output no label and no required marker, only the children.
      $output .= ' ' . $prefix . $element['#children'] . $suffix . "\n";
      break;
  }

  if ($description && !$tooltip) {
    $output .= '<p class="help-block">' . $element['#description'] . "</p>\n";
  }

  $output .= "</div>\n";

  return $output;
}

function komin_forum_node_form_after_build(&$form) {
  $form['body']['und'][0]['format']['#access'] = false;
  return $form;
}

function komin_comment_node_forum_form_after_build(&$form) {
  $form['comment_body']['und'][0]['format']['#access'] = false;
  return $form;
}

function komin_form_element_label(&$variables) {
  $element = $variables['element'];

  // This is also used in the installer, pre-database setup.
  $t = get_t();

  // Determine if certain things should skip for checkbox or radio elements.
  $skip = (isset($element['#type']) && ('checkbox' === $element['#type'] || 'radio' === $element['#type']));

  // If title and required marker are both empty, output no label.
  if ((!isset($element['#title']) || $element['#title'] === '' && !$skip) && empty($element['#required'])) {
    return '';
  }

  // If the element is required, a required marker is appended to the label.
  $required = !empty($element['#required']) ? theme('form_required_marker', array('element' => $element)) : '';

  $title = filter_xss_admin($element['#title']);

  $attributes = array();

  // Style the label as class option to display inline with the element.
  if ($element['#title_display'] == 'after' && !$skip) {
    $attributes['class'][] = $element['#type'];
  }
  // Show label only to screen readers to avoid disruption in visual flows.
  elseif ($element['#title_display'] == 'invisible') {
    $attributes['class'][] = 'element-invisible';
  }

  // Malmostad
  $attributes['class'][] = 'control-label';

  if (!empty($element['#id'])) {
    $attributes['for'] = $element['#id'];
  }

  // Insert radio and checkboxes inside label elements.
  $output = '';
  if (isset($variables['#children'])) {
    $output .= $variables['#children'];
  }

  // Malmostad - Prepend colon
  $title .= ':';

  // Append label.
  $output .= $t('!title !required', array('!title' => $title, '!required' => $required));

  // The leading whitespace helps visually separate fields from inline labels.
  return ' <label' . drupal_attributes($attributes) . '>' . $output . "</label>\n";
}

function komin_field_widget_form_alter(&$element, &$form_state, $context) {
  $element['#attributes']['class'][] = 'input-wide';
}

function komin_form_user_login_block_alter(&$form, &$form_state, $form_id) {
  $form['name']['#attributes']['class'][] = 'input-wide';
  $form['pass']['#attributes']['class'][] = 'input-wide';
}
