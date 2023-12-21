<?php

namespace Drupal\employ\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\DataBase\DataBase;

class CustomDateFilterForm extends FormBase
{
    public function getFormId()
    {
        return 'custom_date_filter_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state)
    {
        // Add date filter elements (start date and end date) to the form.
        // You can use the Date module or any other date picker module for this.
        // For this example, we'll use simple text fields.
        $form['start_date'] = [
            '#type' => 'date',
            '#title' => $this->t('Start Date'),
        ];

        $form['end_date'] = [
            '#type' => 'date',
            '#title' => $this->t('End Date'),
        ];
        $form['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Apply Filter'),
        ];

        return $form;
    }

    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        // Retrieve the start date and end date values.
        $start_date = $form_state->getValue('start_date');
        $end_date = $form_state->getValue('end_date');

        // print_r($start_date."<br>".$end_date);
        // exit;
        // Perform data filtering/querying based on the date range.
        // You can use the Drupal database API, EntityQuery, or other methods here.
        // Customize this part according to your data source and data retrieval logic.
        // For example:
        // $filtered_data = $this->customQueryData($start_date, $end_date);
        $query = \Drupal::database();
        $data = $query->select('student', 't')
            ->fields('t', ['date', 'color'])
            ->condition('t.date',$start_date)
            ->execute();

        // Fetch the results.
        $results = $data->fetchAll();
        print_r($results);exit;

        // Process the results as needed.
        foreach ($results as $result) {
            // Do something with $result->column1 and $result->column2.
        }
    }
}
