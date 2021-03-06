<?php

/**
 * @file
 * Contains \Drupal\w3c_validator\Validator\Response.
 *
 * The aim of this class is to format the response in a similar way, no matter
 * which validator as been employed.
 * NOTE : still, this is oriented to easily understand W3C Markup Validator
 * soap1.2 response format.
 *
 */

namespace Drupal\w3c_validator\Validator;

class Response {

  /**
   * The address of the document validated
   * @var string
   */
  public $uri;

  /**
   * Location of the service which provided the validation result.
   * @var string
   */
  public $checkedby;

  /**
   * Detected (or forced) Document Type for the validated document.
   * @var string
   */
  public $doctype;

  /**
   * Detected (or forced) Character Encoding for the validated document.
   * @var string
   */
  public $charset;

  /**
   * Whether or not the document validated passed formal validation.
   * (true|false boolean)
   * @var bool
   */
  public $validity;

  /**
   * Number of errors found.
   * @var int
   */
  public $errorCount;

  /**
   * Array of error objects formated as W3Cvalidator_Message.
   * @var array
   */
  public $errors = array();

  /**
   * Number of warnings found.
   * @var int
   */
  public $warningCount;

  /**
   * Array of warning objects formated as W3Cvalidator_Message.
   * @var array
   */
  public $warnings = array();

}
