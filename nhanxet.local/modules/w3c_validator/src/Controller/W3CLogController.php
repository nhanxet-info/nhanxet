<?php

/**
 * @file
 * Contains \Drupal\w3c_validator\Controller\W3CLogController.
 */

namespace Drupal\w3c_validator\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Link;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Url;
use Drupal\w3c_validator\W3CProcessor;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller routines for w3c_validator module validation log routes.
 */
class W3CLogController extends ControllerBase {

  /**
   * The renderer service.
   *
   * @var \Drupal\Core\Render\Renderer
   */
  protected $renderer;

  /**
   * The form builder service.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  protected $formBuilder;

  /**
   * @var \Drupal\w3c_validator\W3CProcessor
   */
  protected $w3cProcessor;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('renderer'),
      $container->get('form_builder'),
      $container->get('w3c.processor')
    );
  }

  /**
   * Constructs a W3CLogController object.
   *
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer service.
   * @param \Drupal\Core\Form\FormBuilderInterface $form_builder
   *   The form builder service.
   */
  public function __construct(RendererInterface $renderer, FormBuilderInterface $form_builder, W3CProcessor $w3c_processor) {
    $this->renderer = $renderer;
    $this->formBuilder = $form_builder;
    $this->w3cProcessor = $w3c_processor;
  }

  /**
   * Return the 'overview' page.
   *
   * This page displays a report of all pages, exposing their current validation
   * state. Validation errors are displayed if existing as well as a form to
   * re-validate it all if necessary.
   *
   * @return array
   *   A render array containing our 'overview report' page content.
   */
  public function overview() {
    $output = array(
      '#prefix' => '<div id="foobar">',
      '#suffix' => '</div>',
    );
    $rows = array();

    // Add re-validation form on top.
    $output['operations'] = $this->formBuilder->getForm('Drupal\w3c_validator\Form\W3CValidatorOperationForm');

    // Retrieve all site pages.
    $pages = $this->w3cProcessor->findAllPages();

    // Retrieve all validated pages.
    $all_validated_pages = $this->w3cProcessor->findAllValidatedPages();

    // Loop over result to build display.
    foreach ($pages as $url => $page) {

      // Build validation result.
      $validation = $this->buildValidationResult($all_validated_pages, $url);

      // Build the result display using form API.
      $row = array();
      $row[$url]['summary'] = $this->buildValidationDisplay($page, $validation);
      if (isset($validation['status']) && $validation['status'] != $this->t('Unknown')) {
        $row[$url]['details'] = $this->buildValidationDetailDisplay($validation);
      }
      // Render results.
      $rows[] = array(
        'data' => array(
          array(
            'data' => $this->renderer->render($row),
            'class' => 'w3c_validator-wrapper collapsed',
          ),
        ),
        'class' => array($validation['class']),
      );
    }

    $output['pages'] = array(
      '#type' => 'table',
      '#rows' => $rows,
      '#attributes' => array('id' => 'w3c-report'),
      '#empty' => $this->t('No data to display.'),
    );

    $output['#attached']['library'][] = 'w3c_validator/w3c_validator.report';
    return $output;
  }

  /**
   *  This private method builds the validation result data.
   *
   * @param String $all_validated_pages
   *   The array of all stored results for already validated pages.
   * @param String $url
   *   The URL to build validation result for
   *
   * @return array
   *   The validation result.
   */
  protected function buildValidationResult($all_validated_pages, $url) {
    $validation = array();

    // Check if the page is validated.
    if (isset($all_validated_pages[$url])) {

      // Retrieve the validation result.
      $validation = $all_validated_pages[$url];

      $validation['result'] = $this->t('@errors errors, @warnings warnings', array('@errors' => $validation['error_count'], '@warnings' => $validation['warning_count']));

      // If page is not yet validated.
      if ($validation['need_validation']) {
        $validation['class'] = 'color-outdated';
        $validation['status']  = $this->t('Outdated');
      }
      // If page is valid.
      elseif ($validation['validity']) {
        $validation['class'] = ($validation['warning_count']) ? 'color-warning' : 'color-status';
        $validation['status']  = $this->t('Valid');
      }
      // If page is invalid.
      else {
        $validation['class'] = 'color-error';
        $validation['status']  = $this->t('Invalid');
      }
    }
    // If complitely unknown page.
    else {
      $validation['class'] = 'color-unknown';
      $validation['status']  = $this->t('Unknown');
      $validation['result']  = $this->t('Not yet validated');
    }
    return $validation;
  }

  /**
   * Helper method to build the result row ready to display.
   *
   * @param array $page
   *   The page to validate, as per stored in DB from this module.
   * @param array $validation
   *   An array of preprocess validation values for that page.
   *
   * @return array
   *   A formAPI array representing a result row ready to display.
   */
  protected function buildValidationDisplay($page, $validation) {
    $display = array(
      '#type' => 'container',
      '#attributes' => array('class' => array('page-summary')),
    );
    $display['icon'] = array(
      '#prefix' => '<span class="icon">',
      '#suffix' => '</span>',
    );
    $display['title'] = array(
      '#prefix' => '<span class="title">',
      '#suffix' => '</span>',
      '#markup'  => $page['title'],
    );
    $display['result'] = array(
      '#prefix' => '<span class="result">',
      '#suffix' => '</span>',
      '#markup'  => $validation['result'],
    );
    $display['status'] = array(
      '#prefix' => '<span class="status">',
      '#suffix' => '</span>',
      '#markup'  => $validation['status'],
    );

    return $display;
  }

  /**
   * Helper method to build the details of validation results for the current
   * row, ready to display.
   *
   * @param array $validation
   *   An array of preprocess validation values for that page.
   *
   * @return array
   *   A formAPI array representing the details of validation results, ready to
   *   display.
   */
  protected function buildValidationDetailDisplay($validation) {

    // Build the container for details results.
    $display = array(
      '#prefix' => '<div class="fieldset-wrapper analysis-results">',
      '#suffix' => '</div>',
    );

    // Build the title according to validity.
    if ($validation['validity']) {
      $output = $this->t('This document was successfully checked !');
    }
    else {
      $output = $this->t('Errors found while checking this document !');
    }
    $display['message'] = array(
      '#prefix' => '<h2 class="message ' . $validation['class'] . '">',
      '#suffix' => '</h2>',
      '#markup'  => $output,
    );

    // Build rows for details summary table.
    // Render results.
    $uri = Url::fromUri('base:' . $validation['uri'], array('absolute' => TRUE));
    $rows[] = array($this->t('Uri'), Link::fromTextAndUrl($uri->toString(), $uri));
    $rows[] = array($this->t('Validity'), $validation['status']);
    $url = Url::fromUri('http://validator.w3.org/check', array('query' => array('uri' => $uri->toString()), 'attributes' => array('target' => '_new')));
    $rows[] = array($this->t('Validator results'), Link::fromTextAndUrl($url->toString(), $url));
    $rows[] = array($this->t('Doctype'), $validation['doctype']);
    $rows[] = array($this->t('Summary'), $validation['result']);
    $display['detail-table'] = array(
      '#type' => 'table',
      '#rows' => $rows,
      '#attributes' => array('class' => 'report'),
      '#empty' => $this->t('No data to display.'),
    );

    // Display errors.
    $display['errors-title'] = array(
      '#prefix' => '<h2>',
      '#suffix' => '</h2>',
      '#markup'  => $this->t('Errors'),
    );
    $validation['errors'] = is_array($validation['errors']) ? $validation['errors'] : unserialize($validation['errors']);
    if (is_array($validation['errors']) && !empty($validation['errors'])) {
      foreach ($validation['errors'] as $id => $error) {
        $display['error'][$id] = array(
          '#prefix' => '<div class="message-wrapper message-error">',
          '#suffix' => '</div>',
          'message' => array(
            '#prefix' => '<div class="message">',
            '#suffix' => '</div>',
            '#markup' => '<span class="where">' . t('Line @line, Column @col:', array('@line' => $error->line, '@col' => $error->col)) . '</span><span class="descr">' . t(' @descr', array('@descr' => $error->message)) . '</span>',
          ),
          'source' => array(
            '#prefix' => '<div class="source">',
            '#suffix' => '</div>',
            '#markup' => $error->source,
          ),
        );
      }
    }
    else {
      $display['error'] = array(
        '#prefix' => '<div>',
        '#suffix' => '</div>',
        '#markup'  => $this->t('No errors found'),
      );
    }

    // Display errors.
    $display['warnings-title'] = array(
      '#prefix' => '<h2>',
      '#suffix' => '</h2>',
      '#markup'  => $this->t('Warnings'),
    );
    $validation['warnings'] = is_array($validation['warnings']) ? $validation['warnings'] : unserialize($validation['warnings']);
    if (is_array($validation['warnings']) && !empty($validation['warnings'])) {
      foreach ($validation['warnings'] as $id => $warning) {
        $display['warning'][$id] = array(
          '#prefix' => '<div class="message-wrapper message-warning">',
          '#suffix' => '</div>',
          'message' => array(
            '#prefix' => '<div class="message">',
            '#suffix' => '</div>',
            '#markup' => '<span class="where">' . t('Line @line, Column @col:', array('@line' => $warning->line, '@col' => $warning->col)) . '</span><span class="descr">' . t(' @descr', array('@descr' => $warning->message)) . '</span>',
          ),
          'source' => array(
            '#prefix' => '<div class="source">',
            '#suffix' => '</div>',
            '#markup' => $warning->source,
          ),
        );
      }
    }
    else {
      $display['warning'] = array(
        '#prefix' => '<div>',
        '#suffix' => '</div>',
        '#markup'  => $this->t('No warnings found'),
      );
    }

    return $display;
  }
}
