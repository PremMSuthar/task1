<?php


namespace Drupal\employ\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\code\Database\Database;
use Drupal\file\Entity\File;
use Drupal\Core\File\FileSystem;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Messenger\MessengerTrait;
use Drupal\csv_importer\ParserInterface;



class EmployForm extends FormBase
{
    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'create_employ';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form['test_CERTIFICATE'] = [
            '#type' => 'managed_file',
            '#title' => $this->t('Certificate'),
            '#upload_location' => 'public://certfiles',
            '#upload_validators' => [
                'file_validate_extensions' => ['csv'],
            ],
        ];
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

        $form_file = $form_state->getValue('test_CERTIFICATE', 0);

        if (isset($form_file[0]) && !empty($form_file[0])) {
            $file = File::load($form_file[0]);
            $file->setPermanent();
            $file->save();


            if ($file) {
                $csv_data = file_get_contents($file->getFileUri());
                $lines = explode("\n", $csv_data);
                // echo "<pre>"; print_r($lines);
                // exit;
                // Get the column headers (assuming the first line contains headers)
                $headers = str_getcsv(array_shift($lines));
                // Insert data into the custom table
                $connection = \Drupal::database();
                foreach ($lines as $line) {
                    $data = str_getcsv($line);
                    

                    // Build an associative array with column headers as keys
                    $insert_data = [];
                    foreach ($headers as $index => $header) {
                        if (isset($data[$index])) {
                            $insert_data['name'] = $data[0];
                            $insert_data['email'] = $data[1];
                        }
                    }
                    // print_r($insert_data);exit;

                    // Insert data into the table
                    if (!empty($insert_data)) {
                        $connection->insert('upload_file')
                            ->fields($insert_data)
                            ->execute();
                            $this->messenger()->addMessage($this->t("CSV data inserted successfully"));
                    }
                }

            }
        }
    }
}
