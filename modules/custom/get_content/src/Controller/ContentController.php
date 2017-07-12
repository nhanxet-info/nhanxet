<?php

namespace Drupal\get_content\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\get_content\DefaultService;

/**
 * Class ContentController.
 *
 * @package Drupal\get_content\Controller
 */
class ContentController extends ControllerBase {

  /**
   * Drupal\get_content\DefaultService definition.
   *
   * @var \Drupal\get_content\DefaultService
   */
  protected $get_content_default;

  /**
   * {@inheritdoc}
   */
  public function __construct(DefaultService $get_content_default) {
    $this->get_content_default = $get_content_default;
  }
  
  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('get_content.default')
    );
  }

  /**
   * Get_data.
   *
   * @return string
   *   Return Hello string.
   */
  public function get_data($name) {
    $parameters = \Drupal::request()->query;
    $parameters = $parameters->all();
    
    $url = $parameters['url'];
    $term_id = $parameters['tid'];
    $data = $this->get_content_default->get_all_url_per_page($url);
//    $data = $this->get_content_default->get_tid_by_name('Truyện cười con gái ');
//    dpm($data);
    return [
      '#type' => 'markup',
      '#markup' => 'Complete',
    ];
    $data = $this->get_content_default->get_page($url, $term_id);
    $i = 0;
    while ($i < 1000) {
      if($data) {
        $data = $this->get_content_default->get_page($data, $term_id);
        $i++;
      }
      else
        break;
    }
    
      
    return [
      '#type' => 'markup',
      '#markup' => 'ok => variable of I is: ' . $i,
    ];
  }
  
  /**
   * Get_data.
   *
   * @return string
   *   Return Hello string.
   */
  public function get_data_xem($name) {
    $parameters = \Drupal::request()->query;
    $parameters = $parameters->all();    
//    $url = $parameters['url'];
    $url = 'http://xem.vn/new/';
    $min = $parameters['min'];
    for ($i=$min;$i<18403;$i++)
      $this->get_content_default->get_all_xem_url_per_page($url . $i);
    return [
      '#type' => 'markup',
      '#markup' => 'Complete (xem)',
    ];    
  }
  
  /**
   * get_data_cuoibebung.
   *
   * @return string
   *   Return Hello string.
   */
  public function get_data_cuoibebung($name) {
    $parameters = \Drupal::request()->query;
    $parameters = $parameters->all();    
    $url = $parameters['url'];
    $tid = $parameters['tid'];
    
    $data = $this->get_content_default->get_all_cuoibebung_url_per_page($url, $tid);
//    dpm($data);
    return [
      '#type' => 'markup',
      '#markup' => 'Complete ',
    ];    
  }
}
