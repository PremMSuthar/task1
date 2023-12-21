<?php

use Drupal\views\ViewExecutable;

/**
 * Implements hook_views_pre_render().
 */
function custom_table_actions_views_pre_render(ViewExecutable $view) {
  // Implement your pre-render logic here.
  // This hook is called before the view's results are rendered.
}

/**
 * Implements hook_views_post_render().
 */
function custom_table_actions_views_post_render(ViewExecutable $view, $output) {
  // Implement your post-render logic here.
  // This hook is called after the view's results are rendered.
}


/**
 * Implements hook_views_plugins().
 */
function custom_table_actions_views_plugins() {
    $plugins = [];
  
    // Define your custom Views plugin.
    $plugins['row']['custom_table_actions'] = [
      'title' => t('Custom Table Actions'),
      'help' => t('Adds edit and delete options to the table rows.'),
      'handler' => 'Drupal\custom_table_actions\Plugin\views\row\CustomTableActionsRow',
    ];
  
    return $plugins;
  }
  