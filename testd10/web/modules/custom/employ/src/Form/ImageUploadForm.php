<?php
// modules/custom/custom_image_upload/src/Form/ImageUploadForm.php
namespace Drupal\employ\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class ImageUploadForm extends FormBase
{

    public function getFormId()
    {
        return 'custom_image_upload_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form['image_upload'] = [
            '#type' => 'managed_file',
            '#title' => $this->t('Upload an Image'),
            '#description' => $this->t('Choose an image file to upload.'),
            '#upload_location' => 'public://images/', // You can change the upload location as needed.
            '#upload_validators' => [
                'file_validate_extensions' => ['png gif jpg jpeg'],
            ],
        ];

        $form['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Submit'),
        ];

        return $form;
    }

    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        // Handle the submitted image file, e.g., move it to a permanent location.
        $file = $form_state->getValue('image_upload', 0);
        if (!empty($file)) {
            // Load the file entity.
            $file = \Drupal\file\Entity\File::load($file[0]);
            if ($file) {
                // Move the file to a permanent location.
                $file->setPermanent();
                $file->save();

                // You can also save information about the file in your database if needed.
            }
        }
                                                            
        // Redirect or display a message as needed.                             
        $this->messenger()->addMessage($this->t('The image has been uploaded.'));
    }
}
