<?php
/**
 * @file
 * malmostad.features.field_instance.inc
 */

/**
 * Implements hook_field_default_field_instances().
 */
function malmostad_field_default_field_instances() {
  $field_instances = array();

  // Exported field_instance: 'comment-comment_node_forum-field_attachment'
  $field_instances['comment-comment_node_forum-field_attachment'] = array(
    'bundle' => 'comment_node_forum',
    'deleted' => 0,
    'description' => 'Du kan bifoga upp till fem filer genom att klicka på "Välj fil" och sedan "Ladda upp".',
    'display' => array(
      'default' => array(
        'label' => 'above',
        'module' => 'file',
        'settings' => array(),
        'type' => 'file_default',
        'weight' => 1,
      ),
    ),
    'entity_type' => 'comment',
    'field_name' => 'field_attachment',
    'label' => 'Filer',
    'required' => 0,
    'settings' => array(
      'description_field' => 0,
      'file_directory' => '',
      'file_extensions' => 'txt doc docx pdf jpg png gif rtf',
      'max_filesize' => '5 MB',
      'user_register_form' => FALSE,
    ),
    'widget' => array(
      'active' => 1,
      'module' => 'file',
      'settings' => array(
        'progress_indicator' => 'throbber',
      ),
      'type' => 'file_generic',
      'weight' => 2,
    ),
  );

  // Exported field_instance: 'node-forum-field_attachment'
  $field_instances['node-forum-field_attachment'] = array(
    'bundle' => 'forum',
    'deleted' => 0,
    'description' => 'Du kan bifoga upp till fem filer genom att klicka på "Välj fil" och sedan "Ladda upp".',
    'display' => array(
      'default' => array(
        'label' => 'above',
        'module' => 'file',
        'settings' => array(),
        'type' => 'file_default',
        'weight' => 13,
      ),
      'teaser' => array(
        'label' => 'above',
        'settings' => array(),
        'type' => 'hidden',
        'weight' => 0,
      ),
    ),
    'entity_type' => 'node',
    'field_name' => 'field_attachment',
    'label' => 'Filer',
    'required' => 0,
    'settings' => array(
      'description_field' => 0,
      'file_directory' => '',
      'file_extensions' => 'txt doc docx pdf jpg png gif rtf',
      'max_filesize' => '5 MB',
      'user_register_form' => FALSE,
    ),
    'widget' => array(
      'active' => 1,
      'module' => 'file',
      'settings' => array(
        'progress_indicator' => 'throbber',
      ),
      'type' => 'file_generic',
      'weight' => 3,
    ),
  );

  // Exported field_instance: 'node-forum-field_tags'
  $field_instances['node-forum-field_tags'] = array(
    'bundle' => 'forum',
    'default_value' => NULL,
    'deleted' => 0,
    'description' => 'Ange vilket eller vilka intresseområden ditt diskussionsämne handlar om.',
    'display' => array(
      'default' => array(
        'label' => 'above',
        'module' => 'taxonomy',
        'settings' => array(),
        'type' => 'taxonomy_term_reference_link',
        'weight' => 14,
      ),
      'teaser' => array(
        'label' => 'above',
        'settings' => array(),
        'type' => 'hidden',
        'weight' => 0,
      ),
    ),
    'entity_type' => 'node',
    'field_name' => 'field_tags',
    'label' => 'Intresseområden',
    'required' => 0,
    'settings' => array(
      'user_register_form' => FALSE,
    ),
    'widget' => array(
      'active' => 1,
      'module' => 'options',
      'settings' => array(),
      'type' => 'options_buttons',
      'weight' => 4,
    ),
  );

  // Translatables
  // Included for use with string extractors like potx.
  t('Ange vilket eller vilka intresseområden ditt diskussionsämne handlar om.');
  t('Du kan bifoga upp till fem filer genom att klicka på "Välj fil" och sedan "Ladda upp".');
  t('Filer');
  t('Intresseområden');

  return $field_instances;
}
