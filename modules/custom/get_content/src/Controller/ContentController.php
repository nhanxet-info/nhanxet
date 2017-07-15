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
  public function get_data() {  
    $parameters = \Drupal::request()->query;
    $parameters = $parameters->all();    
    $url = $parameters['url'];
//    $term_id = $parameters['tid'];
    $data = $this->get_content_default->get_all_url_per_page($url);
    
    return [
      '#type' => 'markup',
      '#markup' => 'Complete',
    ];    
  }  
  
  public function get_quan_huyen() {
    $parameters = \Drupal::request()->query;
    $parameters = $parameters->all();    
    $url = $parameters['url'];
//    $data = $this->get_content_default->get_all_url_quan_huyen($url);
    $this->get_content_default->get_all_url_province($url);
    
    return [
      '#type' => 'markup',
      '#markup' => 'Get Quan/Huyen Completely',
    ];    
  }
  
  public function get_phuong_xa_url() {
    $parameters = \Drupal::request()->query;
    $parameters = $parameters->all();    
    $url = $parameters['url'];
    $this->get_content_default->get_all_url_phuong_xa($url);
    
    return [
      '#type' => 'markup',
      '#markup' => 'Get Phuong/Xa Completely',
    ];    
  }
}