<?php


namespace Drupal\employ\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\code\Database\Database;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Url;


class DeleteSt extends FormBase
{
    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'del_st';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $id = \Drupal::routeMatch()->getParameter('id');
        $query = \Drupal::database();
        $data = $query->select('student', 't')
            ->fields('t', ['date', 'color'])
            ->condition('t.id', $id, '=')
            ->execute()->fetchAll(\PDO::FETCH_OBJ);
        // print_r($data[0]->name);
        $form['confirmation_message'] = [
            '#markup' => '<p>' . $this->t('Do you want to delete this data ' . $data[0]->color . '?') . '</p>',
        ];
        $form['actions']['confirm'] = [
            '#type' => 'submit',
            '#value' => $this->t('Confirm'),
            '#submit' => ['::submitForm'],
        ];

        $form['actions']['cancel'] = [
            '#type' => 'submit',
            '#value' => $this->t('Cancel'),
            '#submit' => ['::submitForm'],
        ];

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {

        $id = \Drupal::routeMatch()->getParameter('id');
        $triggering_element = $form_state->getTriggeringElement();

        // Get the name of the triggering button.
        $button_name = $triggering_element['#value'];
        // print_r($button_name);
        // exit;
        // $host =\Drupal::service('base.current')->getPath();
        // $host = \Drupal::request()->getSchemeAndHttpHost();
        $host = Url::fromRoute('<front>', [], ['absolute' => TRUE])->toString();
        // print_r($host);
        // exit;


        if ($button_name == 'Confirm') {
            // echo "done";
            // exit;
            $query = \Drupal::database();
            $data = $query->select('student', 't')
                ->fields('t', ['color'])
                ->condition('t.id', $id, '=')
                ->execute()->fetchAll(\PDO::FETCH_OBJ);
            // echo "<pre>";
            // print_r($data);
            // exit;
            $query->delete('student')->condition('id', $id, '=')->execute();


         
            $this->messenger()->addMessage($this->t("CSV data " . $data[0]->color . " Deleted successfully"));
            $url = \Drupal\Core\Url::fromUri($host . '/student-list');
            $response = new TrustedRedirectResponse($url->toString());
            $form_state->setResponse($response);
        } else {
            // echo "good";
            // exit;
            $this->messenger()->addError($this->t("CSV data " . $id . " Delete Canceled"));
            $url = \Drupal\Core\Url::fromUri($host . '/student-list');
            $response = new TrustedRedirectResponse($url->toString());
            // $response = new TrustedRedirectResponse($url);
            $form_state->setResponse($response);
            // return $response;
        }
    }
}
