<?php

namespace Drupal\civic_job_posting\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * The configuration form for Job posting settings.
 */
class JobPostingSettings extends ConfigFormBase {

  protected $itemsCount;
  protected $config;

  /**
   * Constructor.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->config = $config_factory->getEditable('civic_job_posting.settings');
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'job_posting_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $configData = $this->config->get();
    foreach ($configData as $key => $configValue) {
      $this->config->set($key, $form_state->getValue($key))->save();
    }
    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['civic_job_posting.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $job_posting_settings = $this->config->get();

    $form['#attached']['library'][] = 'civic_job_posting/job_posting_form';

    $form['jobposting'] = [
      '#type' => 'details',
      '#title' => $this->t('Job Posting Settings'),
      '#open' => TRUE,
      '#description' => $this->t('Get your required service account credentials. Please read more <a href="@url">here</a>. After you create your service account, please import your json file that you downloaded from Google , or copy and paste each value to fields.', ['@url' => 'https://developers.google.com/search/apis/indexing-api/v3/prereqs']),
    ];

    $form['jobposting']['google_site_verification'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Google site verification'),
      '#description' => $this->t('To verify ownership with an HTML tag, choose the HTML tag method on the <a href="@url">verification details page</a>, and copy the content verification code.', ['@url' => 'https://search.google.com/search-console/ownership']),
      '#default_value' => $job_posting_settings['google_site_verification'],
    ];

    $form['jobposting']['enableIndexing'] = [
      '#type' => 'radios',
      '#title' => $this->t('Enable Google Indexing'),
      '#options' => [
        TRUE => $this->t('Yes'),
        FALSE => $this->t('No'),
      ],
      '#default_value' => $job_posting_settings['enableIndexing'] ? 1 : 0,
      '#description' => $this->t('If you want to enable Google Indexing API , please check the "Enable Indexing". If you have another way for google to index your jobs (like another plugin or sitemap) you can leave this field empty so there will be no conflict.'),
    ];

    $form['jobposting']['jsonFile'] = [
      '#type' => 'file',
      '#title' => $this->t('Upload JSON File'),
      '#description' => $this->t('Please import your json file that you downloaded from Google and check inside "SERVICE ACCOUNT FILE INFORMATION" each form field that your values are correct, then Save your Settings. Otherwise  you can copy and paste manually each value to each field.'),
      '#upload_validators' => [
        'file_validate_extensions' => ['json'],
      ],
    ];

    $form['jobposting']['ServiceAccountFileInformation'] = [
      '#type' => 'details',
      '#title' => $this->t('Service Account File Information'),
      '#open' => TRUE,
    ];

    $form['jobposting']['ServiceAccountFileInformation']['type'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Type'),
      '#default_value' => $job_posting_settings['type'],
    ];

    $form['jobposting']['ServiceAccountFileInformation']['projectID'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Project ID'),
      '#default_value' => $job_posting_settings['projectID'],
    ];

    $form['jobposting']['ServiceAccountFileInformation']['privateKeyID'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Private Key ID'),
      '#default_value' => $job_posting_settings['privateKeyID'],
    ];

    $form['jobposting']['ServiceAccountFileInformation']['privateKey'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Private Key'),
      '#default_value' => $job_posting_settings['privateKey'],
    ];

    $form['jobposting']['ServiceAccountFileInformation']['clientEmail'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Client Email'),
      '#default_value' => $job_posting_settings['clientEmail'],
    ];

    $form['jobposting']['ServiceAccountFileInformation']['clientID'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Client ID'),
      '#default_value' => $job_posting_settings['clientID'],
    ];

    $form['jobposting']['ServiceAccountFileInformation']['authUri'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Auth Uri'),
      '#default_value' => $job_posting_settings['authUri'],
    ];

    $form['jobposting']['ServiceAccountFileInformation']['tokenUri'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Token Uri'),
      '#default_value' => $job_posting_settings['tokenUri'],
    ];

    $form['jobposting']['ServiceAccountFileInformation']['authProviderx509CertUrl'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Auth Provider x509 Cert Url'),
      '#default_value' => $job_posting_settings['authProviderx509CertUrl'],
    ];

    $form['jobposting']['ServiceAccountFileInformation']['clientX509CertUrl'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Client X509 Cert Url'),
      '#default_value' => $job_posting_settings['clientX509CertUrl'],
    ];

    $form_state->setCached(FALSE);

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save configuration'),
      '#button_type' => 'primary',
    ];

    return $form;

  }

}
