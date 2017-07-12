<?php

/**
 * @file
 * Contains \Drupal\w3c_validator\Form\W3CValidatorOperationForm.
 */

namespace Drupal\w3c_validator\Form;

use Drupal\Core\Database\Connection;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides the operation form on report page.
 */
class W3CValidatorOperationForm extends FormBase {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * Constructs a new W3CValidatorOperationForm.
   *
   * @param \Drupal\Core\Database\Connection $connection
   *   The database connection.
   */
  public function __construct(Connection $connection) {
    $this->connection = $connection;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'w3c_validator_operation_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
  	$warning = t('This will revalidate all of the following pages. This operation may be long.');
  	$warning .= '<br/><br/><i><b>' . t('BEWARE : You are using the online W3C HTML Validator at http://validator.w3.org/check.') . '<br/>' . t('This may be consider spam and abuse of service. Therefore, performing this operation, you may get banned temporarily.') . '</b></i>';

  	// Load module settings.
  	$module_settings = $this->configFactory()->get('w3c_validator.settings');

  	$form['advanced_operations'] = array(
  		'#type'         => 'details',
  		'#title'        => t('advanced operations'),
  		'#description' 	=> $warning,
  		'#collapsible'  => TRUE,
  		'#collapsed'    => TRUE,
  	);

  	// Use admin helper tool option settings.
  	$form['advanced_operations']['use_token'] = array(
	    '#type'           => 'checkbox',
	    '#title'          => t('Validate as logged user.'),
	    '#description'    => t('If enabled, pages will be validated as you can see it. Otherwise, as an anonymous user.'),
	    '#default_value'  => $module_settings->get('use_token'),
    );

  	// Include admin pages.
  	$form['advanced_operations']['admin_pages'] = array(
	    '#type'           => 'checkbox',
	    '#title'          => t('Include admin pages.'),
	    '#description'    => t('If enabled, admin pages will be included in validation.'),
	    '#default_value'  => $module_settings->get('admin_pages'),
  	);

  	$form['advanced_operations']['w3c_validator_revalidate_all'] = array(
      '#type' => 'submit',
      '#value' => 'Re-validate all pages',
  		'#prefix'				=> '<br/>',
  	);
  	return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    // Save global configurations.
    $this->configFactory->getEditable('w3c_validator.settings')
    ->set('use_token', $values['use_token'])
    ->set('admin_pages', $values['admin_pages'])
    ->save();

    $form_state->setRedirect('w3c_validator.confirm');
  }

}
