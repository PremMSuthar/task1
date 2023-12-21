<?php

namespace Drupal\modulefunda\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;


class customform extends FormBase
{
    protected $loaddata;

    /**
     * 
     * {@inheritdoc}
     */

    public function getFormId()
    {
        return 'form_id';
    }

    public function __construct()
    {
        $this->loaddata = \Drupal::Service('modulefunda.db_insert');
    }
    /**
     * 
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form['name'] = [
            '#type' => 'textfield',
            '#title' => t("Name"),
        ];
        $form['email'] = [
            '#type' => 'textfield',
            '#title' => t("E-mail"),
        ];
        $form['action'] = [
            '#type' => 'submit',
            '#title' => t("Submit"),
            '#value' => 'Submit'
        ];
        return $form;
    }

    /**
     * 
     * {@inheritdoc}
     */

    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $query = $this->loaddata->setData($form_state);
        \Drupal::messenger()->addMessage('Recored Has Been Saved');
    }
}
