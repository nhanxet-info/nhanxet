<?php

/**
 * @file
 * Contains \Drupal\w3c_validator\Validator\Result.
 *
 * The aim of this class is to format the errors or warning messages in a
 * similar way, no matter which validator as been employed.
 * NOTE : still, this is oriented to easily understand W3C Markup Validator
 * soap1.2 response format.
 *
 */

namespace Drupal\w3c_validator\Validator;

class Result {

  /**
   * Line corresponding to the message.
   *
   * Within the source code of the validated document, refers to the line
   * which caused this message.
   * @var int
   */
  public $line;

  /**
   * Column corresponding to the message.
   *
   * Within the source code of the validated document, refers to the column
   * within the line for the message.
   * @var int
   */
  public $col;

  /**
   * The actual message.
   * @var string
   */
  public $message;

  /**
   * Explanation for this message.
   *
   * HTML snippet which describes the message, usually with information on
   * how to correct the problem.
   * @var string
   */
  public $explanation;

  /**
   * Source which caused the message.
   *
   * The snippet of HTML code which invoked the message to give the
   * context of the e.
   * @var string
   */
  public $source;

  /**
   * Constructor for a response message
   *
   * @param object $node
   *   A DOMDocument node.
   */
  function __construct($node = NULL) {
    if (isset($node)) {
      // Iterate through the class properties.
      foreach (get_class_vars(get_class($this)) as $var => $val) {
        // For each of this class properties, find the #id element within the
        // given node. This works since the class reflects the W3C validator
        // output markup.
        $element = $node->getElementsByTagName($var);
        if ($element->length) {
          $this->$var = $element->item(0)->nodeValue;
        }
      }
    }
  }
}
