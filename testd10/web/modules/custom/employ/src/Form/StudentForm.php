<?php


namespace Drupal\employ\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\code\Database\Database;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\Core\Url;

class StudentForm extends FormBase
{
    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'std_form';
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
            $data = $query->select('student', 't')
                ->fields('t', ['date', 'color'])
                ->condition('t.id', $id, '=')
                ->execute()->fetchAll(\PDO::FETCH_OBJ);
        }
        // print_r($data);
        // $is_edit = !empty($id);
        $coloropt = array(
            '0' => 'Select color',
            'White' => 'White',
            'Green' => 'Green',
            'Blue' => 'Blue',
        );

        $form['color'] = array(
            '#type' => 'select',
            '#title' => t('Color'),
            '#options' => $coloropt,
            '#default_value' => $is_edit ? $data[0]->color : '',
        );
        $form['date'] = array(
            '#type' => 'date',
            '#title' => t('Enter DOB:'),
            '#default_value' => $is_edit ? $data[0]->date : '',
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
        if (!empty($id)) {
            $query->update('student')
                ->fields($formdata)
                ->condition('id', $id)
                ->execute();
            // print_r($formdata[0]);
            // exit;

            $this->messenger()->addMessage($this->t("CSV data " . $formdata['color'] . " Updated successfully"));
        } else {
            $query->insert('student')
                ->fields($formdata)
                ->execute();
            // print_r($formdata['color']);
            // exit;

            $LAST =  $query->lastInsertId();

            $this->messenger()->addMessage($this->t("The data: " . $formdata['color'] . " has been saved."));
        }
        if ($query == true) {
            $url = \Drupal\Core\Url::fromUri($host . '/student-list');
            $response = new TrustedRedirectResponse($url->toString());
            $form_state->setResponse($response);
        }
    }
}
