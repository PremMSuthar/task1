<?php

namespace Drupal\employ\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\code\Database\Database;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

class EmployController extends ControllerBase
{
    public function deletedata($id)
    {
        $query = \Drupal::database();
        $data = $query->select('upload_file', 't')
            ->fields('t', ['name', 'email'])
            ->condition('t.id', $id, '=')
            ->execute()->fetchAll(\PDO::FETCH_OBJ);
        $confirm = ['#markup' => $this->t('Are you sure you want to delete ' . $data[0]->name . '?'),];

        // print_r($confirm);
        // exit;
        return $confirm;


        // $query = \Drupal::database();
        // $query->delete('upload_file')->condition('id', $id,'=')->execute();

        //        var_dump(1);die;

        // if ($query == true) {
        //     // $url = \Drupal\Core\Url::fromUri('http://localhost/drupal/drupal9/zh-hans/csv-file');
        //     // $response = new TrustedRedirectResponse($url->toString());
        //     $this->messenger()->addMessage($this->t("CSV data Deleted successfully"));
        //     return new RedirectResponse('http://localhost/drupal/drupal9/zh-hans/csv-file');

        // }
    }
    public function confirmCancelForm()
    {
        $form['confirm'] = [
            '#type' => 'submit',
            '#value' => $this->t('Confirm'),
            '#submit' => ['::confirmSubmit'],
        ];

        $form['cancel'] = [
            '#type' => 'submit',
            '#value' => $this->t('Cancel'),
            '#submit' => ['::cancelSubmit'],
        ];

        return $form;
    }
}
