<?php

namespace Drupal\get_content;

/**
 * Interface DefaultServiceInterface.
 *
 * @package Drupal\get_content
 */
interface DefaultServiceInterface {
  public function get_all_url_phuong_xa($url);
  public function get_all_url_per_page($url, $limit, $date);
  public function get_data_via_page($url, $p_url, $date);  
  public function get_all_url_province($url);
  public function get_all_url_quan_huyen($url, $province);
  public function get_detail_url($url, $times);
}