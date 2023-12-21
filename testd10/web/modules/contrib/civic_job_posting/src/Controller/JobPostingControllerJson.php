<?php

namespace Drupal\civic_job_posting\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Controller\ControllerBase;
use Drupal\field\Entity\FieldConfig;

/**
 * Implementing JSON api.
 */
class JobPostingControllerJson extends ControllerBase {

  /**
   * Gets currency values from field_job_base_salary_currency.
   */
  public function salaryCurrencyRender() {
    $fieldCurrency = FieldConfig::loadByName('node', 'job', 'field_job_base_salary_currency')->getSetting('allowed_values');
    return new JsonResponse(['job_salary_currency' => $fieldCurrency]);
  }

  /**
   * Gets salary unit from field_job_salary_unit.
   */
  public function salaryUnitRender() {
    $fieldSalaryUnit = FieldConfig::loadByName('node', 'job', 'field_job_salary_unit')->getSetting('allowed_values');
    return new JsonResponse(['job_salary_unit' => $fieldSalaryUnit]);
  }

  /**
   * Gets employment type unit from field_job_employment_type.
   */
  public function employmentTypeRender() {
    $fieldEmployment = FieldConfig::loadByName('node', 'job', 'field_job_employment_type')->getSetting('allowed_values');
    return new JsonResponse(['job_employment-type' => $fieldEmployment]);
  }

}
