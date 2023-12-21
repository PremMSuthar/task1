<?php

namespace Drupal\civic_job_posting\Utils;

use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\File\FileUrlGeneratorInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\Component\Serialization\Json;
use Drupal\Core\Url;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Google_Client;

/**
 * The Job PostingUtils class.
 */
class JobPostingUtils {

  /**
   * The Google Index API End Point.
   */
  const END_POINT = 'https://indexing.googleapis.com/v3/urlNotifications:publish';

  /**
   * Messenger service.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $loggerFactory;

  /**
   * The current route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * The file URL generator.
   *
   * @var \Drupal\Core\File\FileUrlGeneratorInterface
   */
  protected $fileUrlGenerator;

  /**
   * The entity repository.
   *
   * @var \Drupal\Core\Entity\EntityRepositoryInterface
   */
  protected $entityRepository;

  /**
   * The job posting config.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $config;

  /**
   * Constructs a new JobPostingUtils class.
   *
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory
   *   The logger.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The current route match.
   * @param \Drupal\Core\File\FileUrlGeneratorInterface $file_url_generator
   *   The file url generator.
   * @param \Drupal\Core\Entity\EntityRepositoryInterface $entity_repository
   *   The entity repository.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(
    LoggerChannelFactoryInterface $logger_factory,
    RouteMatchInterface $route_match,
    FileUrlGeneratorInterface $file_url_generator,
    EntityRepositoryInterface $entity_repository,
    ConfigFactoryInterface $config_factory
  ) {
    $this->loggerFactory = $logger_factory;
    $this->routeMatch = $route_match;
    $this->fileUrlGenerator = $file_url_generator;
    $this->entityRepository = $entity_repository;
    $this->config = $config_factory->get('civic_job_posting.settings');
  }

  /**
   * Gets job values.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function jobPostingValues() {

    $node = $this->routeMatch->getParameter('node');

    if (!is_null($node) && $node->getType() == 'job') {

      $job_title = $node->get('title')->value;
      $job_description = $node->get('body')->value;
      $job_identifier_name = $node->get('field_job_identifier')->value;
      $job_identifier_value = $node->get('field_job_identifier_value')->value;
      $job_is_this_work_remotely = $node->get('field_is_this_work_remotely')->value;
      $job_applicant_remote_count = $node->get('field_job_applicant_remote_count')->value;
      $job_base_salary_currency = $node->get('field_job_base_salary_currency')->value;
      $job_employment_type = $node->field_job_employment_type->getValue();
      $job_expiry_date = $node->get('field_job_expiry_date')->value;

      $job_organization_logo = $node->get('field_job_organization_logo')->isEmpty() ? NULL : $this->fileUrlGenerator->generateAbsoluteString($node->get('field_job_organization_logo')->entity->getFileUri());
      $default_image = $node->get('field_job_organization_logo')->getSetting('default_image');
      if (!isset($job_organization_logo) && $default_image && $default_image['uuid']) {
        $defaultImageFile = $this->entityRepository->loadEntityByUuid('file', $default_image['uuid']);
        if ($defaultImageFile) {
          $job_organization_logo = $this->fileUrlGenerator->generateAbsoluteString($defaultImageFile->getFileUri());
        }
      }

      $job_organization_name = $node->get('field_job_organization_name')->value;
      $job_organization_url_first = $node->get('field_job_organization_url')->first();
      $job_organization_url = $job_organization_url_first->getUrl()->toString();

      $job_salary_base_value = $node->get('field_job_salary_base_value')->value;
      $job_salary_max_value = $node->get('field_job_salary_max_value')->value;
      $job_salary_min_value = $node->get('field_job_salary_min_value')->value;
      $job_salary_unit = $node->get('field_job_salary_unit')->value;
      $job_starting_date = $node->get('field_job_starting_date')->value;
      $job_location_group = $node->field_job_location_group->getValue();

      $schema = [
        '@context' => 'http://schema.org',
        // Tell search engines the content type it is looking at.
        '@type' => 'JobPosting',
        'title' => $job_title ,
        'datePosted' => $job_starting_date,
      ];

      // Job Description.
      if ($job_description) {
        $schema['description'] = $job_description;
      }

      // Expiry Date.
      if ($job_expiry_date) {
        $schema['validThrough'] = $job_expiry_date;
      }

      // Employment Type.
      if ($job_employment_type) {
        $job_employment_single_val = "";

        foreach ($job_employment_type as $job_employment_single) {
          $job_employment_single_val .= $job_employment_single['value'] . ', ';
        }

        rtrim(trim($job_employment_single_val), ',');
        $job_employment_single_val_final = rtrim($job_employment_single_val, ", ");
        $schema['employmentType'] = $job_employment_single_val_final;
      }

      // Hiring Organization.
      if ($job_organization_name) {
        $schema['hiringOrganization'] = [];
        $hiring = [
          '@type' => 'Organization',
          'name' => $job_organization_name,
        ];
        if ($job_organization_url) {
          $hiring['sameAs'] = $job_organization_url;
        }
        if ($job_organization_logo) {
          $hiring['logo'] = $job_organization_logo;
        }
        array_push($schema['hiringOrganization'], $hiring);
      }

      if ($job_location_group) {
        $schema['jobLocation'] = [];
      }

      foreach ($job_location_group as $element) {
        $p = Paragraph::load($element['target_id']);
        $job_street_address = $p->field_job_street_address->value;
        $job_region = $p->field_job_region->value;
        $job_locality = $p->field_job_locality->value;
        $job_postal_code = $p->field_job_postal_code->value;
        $job_country_code = $p->field_job_country_code->value;

        // Job Location.
        if ($job_street_address) {
          $jobLocation = [
            '@type' => 'Place',
            'address' => [
              "@type" => 'PostalAddress',
              'streetAddress' => $job_street_address,
            ],
          ];
          if ($job_locality) {
            $jobLocation['address']['addressLocality'] = $job_locality;
          }
          if ($job_region) {
            $jobLocation['address']['addressRegion'] = $job_region;
          }
          if ($job_postal_code) {
            $jobLocation['address']['postalCode'] = $job_postal_code;
          }
          if ($job_country_code) {
            $jobLocation['address']['addressCountry'] = $job_country_code;
          }
          array_push($schema['jobLocation'], $jobLocation);
        }
      }

      // Remote Work.
      if ($job_applicant_remote_count) {
        $cjp_applicant_country_multi_array = explode(',', $job_applicant_remote_count);
        $schema['applicantLocationRequirements'] = [];
        foreach ($cjp_applicant_country_multi_array as $cjp_applicant_country_multi_single) {
          $applicantCountry = [
            '@type' => 'Country',
            'name' => $cjp_applicant_country_multi_single,
          ];
          array_push($schema['applicantLocationRequirements'], $applicantCountry);
        }
      }
      if ($job_is_this_work_remotely) {
        $schema['jobLocationType'] = 'TELECOMMUTE';
      }

      // Organization Identifier.
      if ($job_identifier_name) {
        $schema['identifier'] = [];
        $identifier = [
          '@type' => 'PropertyValue',
          'name' => $job_identifier_name,
        ];
        if ($job_identifier_value) {
          $identifier['value'] = $job_identifier_value;
        }

        array_push($schema['identifier'], $identifier);
      }

      // Base Salary.
      if ($job_base_salary_currency) {
        $schema['baseSalary'] = [];
        $baseSalaryFull = [
          '@type' => 'MonetaryAmount',
          'currency' => $job_base_salary_currency,
        ];
        if ($job_salary_base_value || $job_salary_min_value  || $job_salary_max_value) {
          $baseSalaryFull['value'] = [
            '@type' => 'QuantitativeValue',
          ];
        }
        if ($job_salary_base_value && !$job_salary_min_value) {
          $baseSalaryFull['value']['value'] = $job_salary_base_value;
        }
        if ($job_salary_min_value) {
          $baseSalaryFull['value']['minValue'] = $job_salary_min_value;
        }
        if ($job_salary_max_value) {
          $baseSalaryFull['value']['maxValue'] = $job_salary_max_value;
        }
        if ($job_salary_unit) {
          $baseSalaryFull['value']['unitText'] = $job_salary_unit;
        }
        array_push($schema['baseSalary'], $baseSalaryFull);
      }
      return Json::encode($schema, JSON_PRETTY_PRINT);

    }
  }

  /**
   * Call indexing api function.
   *
   * @param int $id
   *   The node id.
   * @param string $type
   *   The action we are calling (update or delete).
   */
  public function jobPostingCallIndexingApi($id, $type) {
    $job_url = $this->jobPostingJobUrl($id)['job_url'];
    $httpClient = $this->jobPostingGoogleAuthorize();

    $content = "{
                 \"url\": \"$job_url\",
                 \"type\": \"$type\"
               }";

    $response = $httpClient->post(self::END_POINT, ['body' => $content]);
    $status_code = $response->getStatusCode();

    switch ($status_code) {
      case '200':
        $action = $type === 'URL_UPDATED' ? 'updated' : 'deleted';
        $this->loggerFactory->get('civic_job_posting')->notice('Successfully %action %url', [
          '%action' => $action,
          '%url' => $job_url,
        ]);
        break;

      default:
        $body = json_decode($response->getBody()->getContents(), TRUE);
        $this->loggerFactory->get('civic_job_posting')->error($body['error']['message']);
        break;
    }
  }

  /**
   * Get indexing api function.
   *
   * @param int $id
   *   The node id.
   */
  public function jobPostingGetIndexingApi($id) {
    $job_url = $this->jobPostingJobUrl($id)['job_url'];
    $httpClient = $this->jobPostingGoogleAuthorize();

    $response = $httpClient->get('https://indexing.googleapis.com/v3/urlNotifications/metadata?url=' . $job_url);
    $body = json_decode($response->getBody()->getContents(), TRUE);
    if (isset($body['error']['message'])) {
      $this->loggerFactory->get('civic_job_posting')->error($body['error']['message']);
    }
    else {
      $this->loggerFactory->get('civic_job_posting')->info('Google indexed job ' . $body['url']);
    }
  }

  /**
   * Produce json file.
   */
  private function jobPostingJsonFile() {
    $job_posting_settings = $this->config->get();
    $jsonAr = [
      "type" => $job_posting_settings['type'],
      "project_id" => $job_posting_settings['projectID'],
      "private_key_id" => $job_posting_settings['privateKeyID'],
      "private_key" => $job_posting_settings['privateKey'],
      "client_email" => $job_posting_settings['clientEmail'],
      "client_id" => $job_posting_settings['clientID'],
      "auth_uri" => $job_posting_settings['authUri'],
      "token_uri" => $job_posting_settings['tokenUri'],
      "auth_provider_x509_cert_url" => $job_posting_settings['authProviderx509CertUrl'],
      "client_x509_cert_url" => $job_posting_settings['clientX509CertUrl'],
    ];

    return $jsonAr;
  }

  /**
   * Google authorization function.
   */
  private function jobPostingGoogleAuthorize() {
    $array_conf_google = $this->jobPostingJsonFile();

    $client = new Google_Client();
    $client->setAuthConfig($array_conf_google);

    $client->addScope('https://www.googleapis.com/auth/indexing');

    // Get a Guzzle HTTP Client.
    $httpClient = $client->authorize();

    return $httpClient;
  }

  /**
   * Gets the job url by node id.
   *
   * @param int $id
   *   The node id.
   *
   * @return array
   *   An array with the job url.
   */
  private function jobPostingJobUrl($id) {
    $options = ['absolute' => TRUE];
    $node_url = Url::fromRoute('entity.node.canonical', ['node' => $id], $options)->toString();
    $job_url = $node_url;
    $job_url_encode = urlencode($job_url);
    return ['job_url' => $job_url, 'job_url_encode' => $job_url_encode];
  }

}
