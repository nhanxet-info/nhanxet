<?php

/**
 * @file
 * Contains \Drupal\w3c_validator\Form\W3CValidatorOperationConfirmForm.
 */

namespace Drupal\w3c_validator\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Database\Connection;
use Drupal\Core\Form\ConfirmFormBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\w3c_validator\Controller\W3CLogController;
use Drupal\w3c_validator\W3CProcessor;

/**
 * Provides a confirmation form before clearing out the logs.
 */
class W3CValidatorOperationConfirmForm extends ConfirmFormBase {

  protected $w3cProcessor;

  /**
   * Constructs a new DblogClearLogConfirmForm.
   *
   * @param \Drupal\w3c_validator\W3CProcessor $w3c_processor
   *   The validation processor.
   */
  public function __construct(W3CProcessor $w3c_processor) {
    $this->w3cProcessor = $w3c_processor;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('w3c.processor')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'w3c_validator_revalidate_confirm';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to revalidate all page ?');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('w3c_validator.overview');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $batch = array(
      'title' => t('Validating pages'),
      'operations' => array(
        array(array($this->w3cProcessor, 'validateAllPages'), array()),
      ),
      'finished' => 'getCancelUrl',
      'init_message' => t('Starting validator...'),
      'progress_message' => t('Processed @current out of @total.'),
      'error_message' => t('Error while validating pages.'),
    );
    batch_set($batch);
    $form_state->setRedirect('w3c_validator.overview');
  }

}
