<?php

/**
 * @file
 * Describes API functions for the RedHen Connection module.
 */

/**
 * @addtogroup hooks
 * @{
 */

/**
 * Alter the display name for a connection.
 *
 * @param string $label
 *   The generated name.
 * @param Drupal\redhen_connection\ConnectionInterface $connection
 *   The connection whose name is being generated.
 */
function hook_redhen_connection_label_alter(&$label, Drupal\redhen_connection\ConnectionInterface $connection) {
  if (!$connection->isActive()) {
    $label = t("@label1 used to work at @label2", $label->getArguments());
  }
}

/**
 * @} End of "addtogroup hooks".
 */
