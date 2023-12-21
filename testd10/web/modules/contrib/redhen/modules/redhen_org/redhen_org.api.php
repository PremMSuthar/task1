<?php

/**
 * @file
 * Describes API functions for the RedHen Org module.
 */

/**
 * @addtogroup hooks
 * @{
 */

/**
 * Alter the display name for a org.
 *
 * @param string $name
 *   The generated name.
 * @param Drupal\redhen_org\OrgInterface $org
 *   The org whose name is being generated.
 */
function hook_redhen_org_name_alter(&$name, Drupal\redhen_org\OrgInterface $org) {
  // Use ALL CAPS when displaying Redhen Org name.
  $name = strtoupper($org->get('name')->value);
}

/**
 * @} End of "addtogroup hooks".
 */
