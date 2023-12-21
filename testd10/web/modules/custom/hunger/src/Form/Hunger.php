<?php

namespace Drupal\hunger\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class Hunger extends ConfigFormBase
{

    /**
     * {@inheritdoc}
     */

    public function getFormId()
    {
        return 'hunger_admin_setting';
    }

    /**
     * {@inheritdoc}
     */
    protected function getEditableConfigNames()
    {
        return [
            'hunger.admin_setting'
        ];
    }

    /**
     * {@inheritdoc}
     */

    public function buildForm(array $form, FormStateInterface $form_state)
    {

        $config = $this->config('hunger.admin_setting');
        $form['name'] = [
            '#type' => 'textfield',
            '#title' => 'Name',
            '#default_value' => $config->get('name'),
        ];
        return parent::buildForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */

    public function submitForm(array &$form, FormStateInterface $form_State)
    {
        $this->config('hunger.admin_setting')
            ->set('name', $form_State->getValue('name'))->save();
        parent::submitForm($form, $form_State);
    }
}
