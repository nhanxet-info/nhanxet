<?php
namespace Drupal\custom;

/**
 * Class DefaultService.
 *
 * @package Drupal\MyTwigModule
 */
class MyTwigExtension extends \Twig_Extension {

  /**
   * {@inheritdoc}
   * This function must return the name of the extension. It must be unique.
   */
  public function getName() {
    return '';
  }

  /**
   * In this function we can declare the extension function
   */
  public function getFunctions() {
    return [
      new \Twig_SimpleFunction('get_viewing', [$this, 'get_viewing']),
    ];
  }
  
  public function get_viewing($nid) {
    $query = \Drupal::database()->select('nodeviewcount', 'n');
    $query->addField('n', 'nid');
    $query->condition('n.nid', $nid); 
    $result = $query->execute()->fetchCol();
    return count($result);
  }
  
}