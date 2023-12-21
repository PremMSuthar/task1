<?php

    class employ_plugin extends views_plugin_row {
      // Override the render() function to customize row rendering.
      function render($row) {
        // Get your row data and column values.
        $row_data = $this->view->style_plugin->row_plugin->get_render_tokens($row);

        // Create edit and delete links.
        $edit_link = l(t('Edit'), '/edit/' . $row_data['id']);
        $delete_link = l(t('Delete'), '/delete/' . $row_data['id']);

        // Return the custom formatted output.
        return $edit_link . ' | ' . $delete_link;
      }
    }