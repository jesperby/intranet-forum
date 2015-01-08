<?php

function komin_preprocess_html(&$variables) {

  $variables['site_name'] = variable_get('site_frontpage', url());

  $theme_path = url(drupal_get_path('theme', 'komin'));

  $variables['komin_style'] = $theme_path . '/css/application.css';
  //$variables['komin_script'] = $theme_path . '/js/application.js';

  // Set development class
  $variables['classes_array'][] = 'development';

  // Set Malmö Assets classes
  $variables['classes_array'][] = 'malmo-form';
  $variables['classes_array'][] = 'malmo-masthead-more';

}

function komin_preprocess_links(&$variables) {
  $forum_buttons = array(
    'comment-add',
    'comment-reply',
    'comment-edit',
    'comment-delete',
    'post-edit',
    'post-delete',
    'subscriptions-subscribe'
  );
  $forum_buttons_default = array(
    'comment-edit',
    'comment-delete',
    'post-edit',
    'post-delete',
    'subscriptions-subscribe'
  );

  foreach ($forum_buttons as $button_id) {
    if (isset($variables['links'][$button_id])) {
      if (!isset($variables['links'][$button_id]['attributes']['class'])) {
        $variables['links'][$button_id]['attributes']['class'] = array();
      }

      $variables['links'][$button_id]['attributes']['class'][] = 'btn';
      if (in_array($button_id, $forum_buttons_default)) {
        $variables['links'][$button_id]['attributes']['class'][] = 'btn-default';
      }
    }
  }

}

function komin_preprocess_button(&$vars) {
  $vars['element']['#attributes']['class'][] = 'btn';
  if (isset($vars['element']['#id']) && $vars['element']['#id'] == 'edit-submit') {
    $vars['element']['#attributes']['class'][] = 'btn-primary';
  }
}

function komin_link($variables) {
  // Remove active class added by Drupal if we are a button
  if (!empty($variables['options']['attributes']['class'])) {
    $classes = & $variables['options']['attributes']['class'];
    if (is_array($classes) && in_array('btn', $classes)) {
      if (($key = array_search('active', $classes)) !== false) {
        unset($classes[$key]);
      }
    }
  }

  return '<a href="' . check_plain(url($variables['path'], $variables['options'])) . '"' . drupal_attributes(
    $variables['options']['attributes']
  ) . '>' . ($variables['options']['html'] ? $variables['text'] : check_plain($variables['text'])) . '</a>';
}

function komin_field_widget_form_alter(&$element, &$form_state, $context) {
  $element['#attributes']['class'][] = 'input-wide';
}

function komin_form_user_login_block_alter(&$form, &$form_state, $form_id) {
  $form['name']['#attributes']['class'][] = 'input-wide';
  $form['pass']['#attributes']['class'][] = 'input-wide';
}

function komin_forum_node_form_after_build(&$form) {
  $form['body']['und'][0]['format']['#access'] = false;
  return $form;
}

function komin_comment_node_forum_form_after_build(&$form) {
  $form['comment_body']['und'][0]['format']['#access'] = false;
  return $form;
}

function komin_advanced_forum_l(&$variables) {
  $text = $variables['text'];
  $path = empty($variables['path']) ? NULL : $variables['path'];
  $options = empty($variables['options']) ? array() : $variables['options'];
  $button_class = empty($variables['button_class']) ? NULL : $variables['button_class'];

  if (!isset($options['attributes'])) {
    $options['attributes'] = array();
  }
  if (!is_null($button_class)) {
    // Buttonized link: add our button class and the span.
    if (!isset($options['attributes']['class'])) {
      $options['attributes']['class'] = array($button_class);
    }
    else {
      $options['attributes']['class'][] = $button_class;
    }

    // Add btn class
    $options['attributes']['class'][] = "btn";
    $options['html'] = TRUE;
    $l = l($text, $path, $options);
  }
  else {
    // Standard link: just send it through l().
    $l = l($text, $path, $options);
  }

  return $l;
}

function komin_advanced_forum_reply_link(&$variables) {
  $node = $variables['node'];

  // Get the information about whether the user can reply and the link to do
  // so if the user is allowed to.
  $reply_link = advanced_forum_get_reply_link($node);

  if (is_array($reply_link)) {
    // Reply is allowed. Variable contains the link information.
    $output = theme(
      'advanced_forum_l', array(
        'text' => $reply_link['title'],
        'path' => $reply_link['href'],
        'options' => $reply_link['options'],
        'button_class' => 'btn-info'
      )
    );
    return $output;
  }
  elseif ($reply_link == 'reply-locked') {
    return '<span class="topic-reply-locked">' . t('Topic locked') . '<span>';
  }
  elseif ($reply_link == 'reply-forbidden') {
    // User is not allowed to reply to this topic.
    return theme('comment_post_forbidden', array('node' => $node));
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
        case 'node-forum-form' :
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

  if ($form_id == 'forum_node_form') {
    // After build hook to hide text formatting options
    $form['#after_build'][] = 'komin_forum_node_form_after_build';
    $form['title']['#attributes']['class'][] = 'input-wide';
  }
  else {
    if ($form_id == 'comment_node_forum_form') {
      // After build hook to hide text formatting options
      $form['#after_build'][] = 'komin_comment_node_forum_form_after_build';
      // Hide the subject
      unset($form['subject']);
      // Hide the author
      unset($form['author']['_author']);
    }
  }
}

function komin_form_element(&$variables) {
  $element = & $variables['element'];
  $is_checkbox = FALSE;
  $is_radio = FALSE;

  // This function is invoked as theme wrapper, but the rendered form element
  // may not necessarily have been processed by form_builder().
  $element += array('#title_display' => 'before',);

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
    $attributes['class'][]
      = 'form-item-' . strtr($element['#name'], array(' ' => '-', '_' => '-', '[' => '-', ']' => '',));
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
    case 'before' :
    case 'invisible' :
      $output .= ' ' . theme('form_element_label', $variables);
      $output .= ' ' . $prefix . $element['#children'] . $suffix . "\n";
      break;

    case 'after' :
      if ($is_radio || $is_checkbox) {
        $output .= ' ' . $prefix . $element['#children'] . $suffix;
      }
      else {
        $variables['#children'] = ' ' . $prefix . $element['#children'] . $suffix;
      }
      $output .= ' ' . theme('form_element_label', $variables) . "\n";
      break;

    case 'none' :
    case 'attribute' :
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

function komin_pager($variables) {
  $output = "";
  $items = array();
  $tags = $variables['tags'];
  $element = $variables['element'];
  $parameters = $variables['parameters'];
  $quantity = $variables['quantity'];

  global $pager_page_array, $pager_total;

  // Calculate various markers within this pager piece:
  // Middle is used to "center" pages around the current page.
  $pager_middle = ceil($quantity / 2);
  // Current is the page we are currently paged to.
  $pager_current = $pager_page_array[$element] + 1;
  // First is the first page listed by this pager piece (re quantity).
  $pager_first = $pager_current - $pager_middle + 1;
  // Last is the last page listed by this pager piece (re quantity).
  $pager_last = $pager_current + $quantity - $pager_middle;
  // Max is the maximum page number.
  $pager_max = $pager_total[$element];

  // Prepare for generation loop.
  $i = $pager_first;
  if ($pager_last > $pager_max) {
    // Adjust "center" if at end of query.
    $i = $i + ($pager_max - $pager_last);
    $pager_last = $pager_max;
  }
  if ($i <= 0) {
    // Adjust "center" if at start of query.
    $pager_last = $pager_last + (1 - $i);
    $i = 1;
  }

  $li_previous = theme(
    'pager_previous',
    array('text' => t('Previous'), 'element' => $element, 'interval' => 1, 'parameters' => $parameters,)
  );
  $li_next = theme(
    'pager_next', array('text' => t('Next'), 'element' => $element, 'interval' => 1, 'parameters' => $parameters,)
  );

  if ($pager_total[$element] > 1) {

    if ($li_previous) {
      $items[] = array('class' => array('prev'), 'data' => $li_previous,);
    }
    // When there is more than one page, create the pager list.
    if ($i != $pager_max) {
      if ($i > 1) {
        $items[] = array('class' => array('pager-ellipsis', 'disabled'), 'data' => '<span>…</span>',);
      }
      // Now generate the actual pager piece.
      for (; $i <= $pager_last && $i <= $pager_max; $i++) {
        if ($i < $pager_current) {
          $items[] = array(
            // 'class' => array('pager-item'),
            'data' => theme(
              'pager_previous', array(
                'text' => $i,
                'element' => $element,
                'interval' => ($pager_current - $i),
                'parameters' => $parameters,
              )
            ),
          );
        }
        if ($i == $pager_current) {
          $items[] = array(
            // Add the active class.
            'class' => array('active'),
            'data' => l($i, '#', array('fragment' => '', 'external' => TRUE)),
          );
        }
        if ($i > $pager_current) {
          $items[] = array(
            'data' => theme(
              'pager_next', array(
                'text' => $i,
                'element' => $element,
                'interval' => ($i - $pager_current),
                'parameters' => $parameters,
              )
            ),
          );
        }
      }
      if ($i < $pager_max) {
        $items[] = array('class' => array('pager-ellipsis', 'disabled'), 'data' => '<span>…</span>',);
      }
    }
    // End generation.
    if ($li_next) {
      $items[] = array('class' => array('next'), 'data' => $li_next,);
    }

    return '<div class="pagination">' . theme('item_list', array('items' => $items,)) . '</div>';
  }
  return $output;
}

function komin_get_author_profile_link($account) {
  $url = variable_get('komin_profile_url');
  if (empty($url)) {
    return '/user/' . $account->uid;
  }

  return str_replace('{username}', $account->name, $url);
}

/**
 * Displays the author information within a post.
 */
function komin_advanced_forum_simple_author_pane(&$variables) {
  $context = $variables['context'];

  $account = user_load($context->uid);

  if(!empty($account->field_fname[LANGUAGE_NONE][0]['value']) && !empty($account->field_lname[LANGUAGE_NONE][0]['value'])){
    $name = $account->field_fname[LANGUAGE_NONE][0]['value'] . ' ' . $account->field_lname[LANGUAGE_NONE][0]['value'];
  }
  elseif (!empty($account->field_display_name[LANGUAGE_NONE][0]['value'])) {
    $name = $account->field_display_name[LANGUAGE_NONE][0]['value'];
  }
  else {
    $name = $account->name;
  }
  $picture = 'https://webapps06.malmo.se/avatars/'.$account->name.'/small_quadrat.jpg';


  return
     '<div class="author-pane"><a href="' . komin_get_author_profile_link($account) . '"> <div class="avatar"><img src="'. $picture .'" alt=""></div>' . $name . '</a>' . ' ' /* $picture */
    . '</div>';
}

function komin_advanced_forum_subforum_list(&$variables) {
  $result = '<ul>';
  foreach ($variables['subforum_list'] as $tid => $subforum) {
    $text = l($subforum->name, "forum/$tid");
    $text .= ' (' . $subforum->total_posts;

    if (empty($subforum->new_posts)) {
      $text .= ')';
    }
    else {
      $text .= ' - ' . l($subforum->new_posts_text, $subforum->new_posts_path, array('fragment' => 'new')) . ')';
    }

    $result .= '<li>' . $text . '</li>';
  }
  $result .= '</ul>';
  return $result;
}

/**
 * Displays the username more or less everywhere.
 */
function komin_username($variables) {
  $account = user_load($variables['uid']);
  if (empty($account->field_display_name[LANGUAGE_NONE][0]['value'])) {
    $name = $account->name;
  }
  else {
    $name = $account->field_display_name[LANGUAGE_NONE][0]['value'];
  }

  return '<a href="' . komin_get_author_profile_link($account) . '" class="forum-author">' . $name . '</a>';
}

function komin_subscriptions_ui_table($element) {
  $rows = array();
  $headers = array();
  $header_strings = array(
    array('class' => 'subscriptions-table', 'width' => '30%'),
    array('data' => t('On&nbsp;updates'), 'width' => '1*', 'style' => 'writing-mode: lr-tb'),
    array('data' => t('On&nbsp;comments'))
  );
  $element = $element['element'];
  foreach (element_children($element['subscriptions']) as $key) {
    $row = array();
    foreach (array('subscriptions', 'updates', 'comments') as $eli => $elv) {
      if (isset($element[$elv]) && $element[$elv]['#access']) {
        $row[] = drupal_render($element[$elv][$key]);
        $headers[$eli] = $header_strings[$eli];
      }
    }
    $rows[] = $row;
  }

  $col_indexes = array_keys($headers);
  unset($headers[end($col_indexes)]['width']);

  $output = theme('table', array('header' => $headers, 'rows' => $rows));
  $output .= drupal_render_children($element);
  return $output;
}

/*
 * Render status messages.
 */
function komin_status_messages($variables) {
  $display = $variables['display'];
  $output = '';

  foreach (drupal_get_messages($display) as $type => $messages) {
    switch ($type) {
      case 'status' :
        $class = 'success';
        break;
      case 'error' :
        $class = 'error';
        break;
      case 'warning' :
        $class = 'warning';
        break;
    }

    foreach ($messages as $message) {
      $output .= '<div class="' . $class . '">' . $message . '</div>';
    }
  }
  return $output;
}
function komin_breadcrumb($variables) {
    if (!empty($variables)){
        $breadcrumb = $variables['breadcrumb'];
        $check = l(t('Home'), '<front>');
        $crumbs = '<div class="breadcrumb">';

        foreach($breadcrumb as $nr => $b){
            if($b == $check){
                $breadcrumb[$nr] = l(t('Start'),'https://webapps06.malmo.se/dashboard/');
            }
            $crumbs .= $breadcrumb[$nr];
        }

        $crumbs .="</div>";
        return $crumbs;
    }
}
