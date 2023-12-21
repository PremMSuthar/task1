<?php
// In your_module/src/Form/BulkDeleteForm.php

namespace Drupal\employ\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\code\Database\Database;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\Core\Url;

class BulkDeleteForm extends FormBase
{

    public function getFormId()
    {
        return 'your_module_bulk_delete_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state)
    {
        // Query your custom table to fetch the data to be displayed.
        $query = \Drupal::database();
        $result = $query->select('student', 't')
            ->fields('t', ['id', 'color'])
            ->execute()->fetchAll(\PDO::FETCH_OBJ);
        // echo "<pre>";
        // print_r($result);
        // exit;


        foreach ($result as $record) {
            $form['items'][$record->id] = [
                '#type' => 'checkbox',
                '#values' => $record->id,
                '#default_value' => 0, // Initialize all checkboxes as unchecked.
            ];
            $form['items'][$record->name] = [
                '#type' => 'checkbox',
                '#values' => $record->id,
                '#default_value' => 0, // Initialize all checkboxes as unchecked.
            ];
        }

        $form['submit'] = [
            '#type' => 'submit',
            '#value' => t('Delete Selected Items'),
        ];

        return $form;
    }

    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        // $selected_items = array_filter($form_state->getValue('items'));

        // $formdata = $form_state->getValues();
        $selected_items = array_filter($form_state['values']['items']);

        echo "<pre>";
        print_r($selected_items);
        exit;
        foreach ($selected_items as $item_id => $value) {

            // Delete the selected item(s) from the custom table.
            //   $query = \Drupal::database();
            //   $query->delete('student')
            //     ->condition('id', $item_id)
            //     ->execute();
            print_r($item_id);
        }

        $this->messenger()->addMessage(t('Selected items have been deleted.'));
    }
}
