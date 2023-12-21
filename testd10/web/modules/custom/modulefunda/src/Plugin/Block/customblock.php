<?php

namespace Drupal\modulefunda\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Provide a 'Custom' block.
 * 
 * @Block(
 *  id = "custom_block",
 * admin_label = @Translation("Custom Block"),
 * category = @Translation("Custom Block")
 * )
 */

class customblock extends BlockBase
{
    /**
     * {@inheritdoc}
     */

    public function build()
    {
        $data = \Drupal::service('modulefunda.db_insert')->getData();
        return [
            '#theme' => 'my_template',
            '#data' => $data,
        ];
    }
}
