<?php

namespace Drupal\modulefunda\services;

use Drupal\Core\Database\Connection;

class db_insert
{
    protected $database;

    public function __construct(Connection $database)
    {
        $this->database = $database;
    }

    /**
     * Set Data function
     */

    public function setData($form_state)
    {
        $this->database->insert('funda')
            ->fields(array(
                'name'   => $form_state->getValue('name'),
                'email'   => $form_state->getValue('email'),
                'created'   => time(),
            ))->execute();
    }

    /**
     * Get Data function
     */

     public function getData()
    {
        
        $query = \Drupal::database();
        $result = $query->select('funda', 'f')
            ->fields('f', ['name'])
            // ->condition('your_condition', 'your_value')
            ->execute();

        // Initialize an empty array to store the rows.
        $rows = [];

        foreach ($result as $row) {
            $rows[] = [
                'name' => $row->name,
            ];
        }

        // Build a renderable array (e.g., a table) to display the data.
        // $header = ['id', 'Name', 'Email'];

        $output['table'] = [
            '#theme' => 'table',
            // '#header' => $header,
            '#rows' => $rows,
        ];
        return $output;
    }
}
