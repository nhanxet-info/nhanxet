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
      new \Twig_SimpleFunction('fb_status', [$this, 'fb_status']),
      new \Twig_SimpleFunction('get_viewing', [$this, 'get_viewing']),
    ];
  }

  public function fb_status($url) {
    $base_url = \Drupal::request()->getHost();
    $json = json_decode(file_get_contents("http://graph.facebook.com/?ids=http://" . $base_url . $url),true);
    return $json;
  }
  
  public function get_viewing($nid) {
    $query = \Drupal::database()->select('nodeviewcount', 'n');
    $query->addField('n', 'nid');
    $query->condition('n.nid', $nid); 
    $result = $query->execute()->fetchCol();
    return count($result);
  }
  
}