<?php

namespace Drupal\modulefunda\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Drupal\Core\url;


class TableData extends ControllerBase
{
    public function content()
    {

        $query = \Drupal::Database();
        $result = $query->select('funda','f')
        ->fields('f')->execute()->fetchall();

        $rows = [];
        foreach($result as $row){
            $rows[] = [
                            'id' => $row->id,
                            'name' => $row->name,
                            'email' => $row->email,
            ];
        }
            $header = ['id','name','desc'];

            $table['tablelist'] = [
                '#type' => 'table',
                '#header' => $header,
                '#rows' => $rows,
            ];
            return $table;
        
    }
}
