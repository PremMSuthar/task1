<?php
namespace Drupal\custom_table_actions\Plugin\views\row;

use Drupal\views\Plugin\views\row\RfcRow;


// Defines a custom Views row plugin to add edit and delete options.
@ViewsRow(
  id = "custom_table_actions",
  title = @Translation("Custom Table Actions"),
  help = @Translation("Adds edit and delete options to the table rows."),
  theme = "views_view_row",
  display_types = {"upload_file"},
)
 
class CustomTableActionsRow extends RfcRow {

  /**
   * Renders the row with edit and delete options.
   */
  public function render($row) {
    // Implement custom rendering logic here.
    // You can add edit and delete links or buttons.
    // For example:
    $output = '<a href="#">Edit</a>';
    $output .= ' | ';
    $output .= '<a href="">Delete</a>';

    return $output;
  }

}
