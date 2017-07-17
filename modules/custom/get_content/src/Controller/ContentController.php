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
    $limit = $parameters['limit'];
//    for($i = 1; $i< 100000000; $i++) {
//      echo rand(1,$i);
//    }
    $data = $this->get_content_default->get_all_url_per_page($url,$limit);
//    echo 'ok';
//    return [
//      '#type' => 'markup',
//      '#markup' => 'Complete 111',
//    ];  
    echo $data;
    if($data > 0) {
      $url_arr = explode('?p=', $url);
      $next_url = $url_arr[0] . '?p=' . ($limit);
      $limit += 20;
      header("Refresh:2; http://nhanxet.local/get_content?limit=$limit&url=$next_url");
    }
    else {
      $filename = 'full_url';
      $file = file($filename);
      $output = $file[0];
//      echo $output;
      unset($file[0]); //delete first line
      file_put_contents($filename, $file); //delete first line
      header("Refresh:2; http://nhanxet.local/get_content?limit=21&url=$output");
    }
    
    return [
      '#theme' => 'get_content',
      '#test_var' => $data,
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
  public function get_detail_url() {
    $parameters = \Drupal::request()->query;
    $parameters = $parameters->all();    
    $url = $parameters['url'];
    $times = $parameters['times'];
    $this->get_content_default->get_detail_url($url, $times);
    
    return [
      '#type' => 'markup',
      '#markup' => 'Get Phuong/Xa Completely',
    ];    
  }
}