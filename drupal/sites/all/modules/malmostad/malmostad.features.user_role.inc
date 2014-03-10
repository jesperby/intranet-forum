<?php
/**
 * @file
 * malmostad.features.user_role.inc
 */

/**
 * Implements hook_user_default_roles().
 */
function malmostad_user_default_roles() {
  $roles = array();

  // Exported role: administrator.
  $roles['administrator'] = array(
    'name' => 'administrator',
    'weight' => 3,
  );

  // Exported role: moderator.
  $roles['moderator'] = array(
    'name' => 'moderator',
    'weight' => 2,
  );

  return $roles;
}
