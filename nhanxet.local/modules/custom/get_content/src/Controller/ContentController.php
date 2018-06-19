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
    $date = $parameters['date'];
    $data = $this->get_content_default->get_all_url_per_page($url,$limit,$date);
    dpm($data);
    $i = 1;
    if($data == 15) {
      $url_arr = explode('?p=', $url);
      $next_url = $url_arr[0] . '?p=' . ($limit);
      $limit += 20;
      header("Refresh:$i; http://nhanxet.local/get_content?limit=$limit&date=$date&url=$next_url");
    }
    else {
      $filename = 'full_url';
      $file = file($filename);
      $output = $file[0];
      if(strlen($output) > 0) {
        unset($file[0]); //delete first line
        file_put_contents($filename, $file); //delete first line
        header("Refresh:$i; http://nhanxet.local/get_content?limit=21&date=$date&url=$output");
      }
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