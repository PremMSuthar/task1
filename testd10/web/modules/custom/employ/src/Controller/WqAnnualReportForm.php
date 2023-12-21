<?php

namespace Drupal\employ\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\code\Database\Database;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

class WqAnnualReportForm extends ControllerBase
{



    public function content()
    {
        $query = \Drupal::database();
        $result = $query->select('student', 's')
            ->fields('s')->execute()
    // ->extend('Drupal\Core\Database\Query\PagerSelectExtender')->limit(50)
    ->fetchAll();
        // echo "<pre>";
        // print_r($result);
        // exit;
        $rows = [];
        foreach ($result as  $value) {
            $obj=  new \DateTime($value->date);
            $rows[] = [
                'id' => $value->id,
                'date' => $obj->format('y-m-d'),
                'color' => $value->color
            ];
        }
        $header = ['id', 'date', 'color'];

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
    //    return$rows;
}
// }
