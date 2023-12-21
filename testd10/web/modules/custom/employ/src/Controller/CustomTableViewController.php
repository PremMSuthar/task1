<?php

namespace Drupal\employ\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\code\Database\Database;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

class CustomTableViewController extends ControllerBase
{

    public function content()
    {
        // Your table building logic goes here.
        // You can use Drupal's database API or Entity API to fetch data and build the table.
        // For this example, we'll create a simple table with dummy data.
        $query = \Drupal::database();
        $result = $query->select('upload_file', 't')
    ->fields('t', ['id', 'name', 'email']);
    $pager = $query->extend('Drupal\Core\Database\Query\PagerSelectExtender')->limit(30);
    $result = $pager->execute();
            
            // ->condition('your_condition', 'your_value')
            // $result->execute();

        // Initialize an empty array to store the rows.
        $rows = [];

        foreach ($result as $row) {
            $rows[] = [
                'id' => $row->id,
                'name' => $row->name,
                'email' => $row->email,
                '#type' => 'pager'
            ];
            // $row['pager'] = [
                
            //   ];
        }

        // Build a renderable array (e.g., a table) to display the data.
        $header = ['id', 'Name', 'Email'];

        $output['table'] = [
            '#theme' => 'table',
            '#header' => $header,
            '#rows' => $rows,
        ];


     

        // return [
        //   '#markup' => render($table),
        // ];
        return $output;
    }
  
}
