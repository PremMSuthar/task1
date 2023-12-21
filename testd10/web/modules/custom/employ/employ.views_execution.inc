<?php

/**
 * Implements hook_views_plugins().
 */
function employ_views_plugins($module = NULL)
{
    $plugins = array();

    if ($module == 'views') {
        $plugins['row']['employ'] = array(
            'title' => t('Custom Table Actions'),
            'help' => t('Add edit and delete options to custom table rows.'),
            'handler' => 'employ_plugin',
        );
    }

    return $plugins;
}
