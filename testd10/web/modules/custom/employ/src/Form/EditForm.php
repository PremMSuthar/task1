<?php


namespace Drupal\employ\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\code\Database\Database;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\Core\Url;

class EditForm extends FormBase
{
    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'edit_employ';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $id = \Drupal::routeMatch()->getParameter('id');
        $is_edit = !empty($id);
        if ($is_edit) {
            $query = \Drupal::database();
            $data = $query->select('upload_file', 't')
                ->fields('t', ['name', 'email'])
                ->condition('t.id', $id, '=')
                ->execute()->fetchAll(\PDO::FETCH_OBJ);
        }
        // print_r($data);
        // $is_edit = !empty($id);
        $form['#attached']['libraries'][] = "employ/employjslibraries";
        $form['name'] = array(
            '#type' => 'textfield',
            '#title' => t('Name'),
            '#default_value' => $is_edit ? $data[0]->name : '',
        );
        $form['email'] = array(
            '#type' => 'textfield',
            '#title' => t('Email'),
            '#default_value' => $is_edit ? $data[0]->email : '',
        );
        $form['submit'] = array(
            '#type' => 'submit',
            '#value' => t("Submit"),
        );

        return $form;
    }
    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $id = \Drupal::routeMatch()->getParameter('id');
        $formdata = $form_state->getValues();
        // print_r($formdata);
        // exit;
        unset($formdata['submit'], $formdata['form_build_id'], $formdata['form_token'], $formdata['form_id'], $formdata['op']);
        $host = Url::fromRoute('<front>', [], ['absolute' => TRUE])->toString();
        $query = \Drupal::database();
        if(!empty($id)){
        $query->update('upload_file')
            ->fields($formdata)
            ->condition('id', $id)
            ->execute();
            $this->messenger()->addMessage($this->t("CSV data Updated successfully"));
        }else{
        $query->insert('upload_file')
            ->fields($formdata)
            ->execute();
            $this->messenger()->addMessage($this->t("CSV data inserted successfully"));
        }
        if ($query == true) {
            $url = \Drupal\Core\Url::fromUri($host.'/csv-file');
            $response = new TrustedRedirectResponse($url->toString());
            $form_state->setResponse($response);
        }
    }
}
