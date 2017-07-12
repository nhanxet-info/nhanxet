<?php

namespace Drupal\get_content;

use \Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;
/**
 * Class DefaultService.
 *
 * @package Drupal\get_content
 */
class DefaultService implements DefaultServiceInterface {

  /**
   * Constructor.
   */
  public function __construct() {
    include "modules/custom/get_content/src/include/simple_html_dom.php";
  }
  
  public function get_page($url, $term_id) {
    $html = file_get_html($url);
    $is_next = $html->find('link[rel=next]', 0);
    $hrefs = $html->find('h2 a');
    foreach($hrefs as $element)
      $this->get_data($element->href, $url, $term_id);
    if(strlen($is_next->href) > 0) {
      return $is_next->href;
    } 
    else
      return false;
  }
  
  public function get_data($url, $parent_url, $term_id) {

    $html = file_get_html($url);
    $title = $html->find('h1', 0)->innertext;
    $body = '';
    foreach($html->find('div[class=entry-content] p') as $element) {
      $temp = $element->find('a');
      if(count($temp) == 0)
        $body .= $element->innertext . '<br>';
    }
    $node = Node::create([
      'type' => 'funny_story',
      'title' => $title,
      'status' => 1,
      'body' => [
        'value' => $body,
        'format' => 'basic_html'
      ],
      'field_temp_url' => [
          'value' => $parent_url,
          'format' => 'basic_html'
      ],
      'field_real_url' => [
          'value' => $url,
          'format' => 'basic_html'
      ],
      'field_funny_story_type'  => [
        ['target_id' => $term_id]
      ]
    ]);
    $node->save();
  }
  
  public function get_all_url_per_page($url) {
    $html = file_get_html($url);
    $links = $html->find('h2 a');
    foreach($links as $link) {
      $this->get_data_via_page($link->href, $url);
      break;
    }      
    return t('Complete');
  }
  
  public function get_data_via_page($url, $p_url) {
    $base_source_url = 'https://thongtindoanhnghiep.co/';
    $html = file_get_html($base_source_url.$url);
    $title = html_entity_decode($html->find('h1', 0)->innertext);
    
//    $term_name = $html->find('span[class=cat-links] a', 0)->innertext;
//    $term_id = $this->get_tid_by_name($term_name);
//    if(!isset($term_id) || $term_id < 1) {
//      $term_id = Term::create([
//        'name' => $term_name, 
//        'vid' => 'funny_story',
//      ])->save();
//    }   
//    $body = '';
    $table = $html->find('table[class=table table-striped table-bordered table-responsive table-details]', 0);
//    foreach($tables as $element) {
//    $ths = $table->find('th');
    $tds = $table->find('td');
//    $label_arr = [];
    $data_arr = [];
//    foreach ($ths as $th)
//      $label_arr[] = html_entity_decode($th->plaintext);
    
    foreach ($tds as $td)
      $data_arr[] = html_entity_decode($td->plaintext);
    
//    $t_label = $label_arr;
//    $t_data = $data_arr;
//      echo 'ok';
//      echo 'ok';
//      break;
//    }
    $node = Node::create([
      'type' => 'company',
      'title' => $title,
      'status' => 0,
      'field_ma_so_dtnt' => [
          'value' => $data_arr[0]          
      ],
      'field_ngay_cap' => [
          'value' => $data_arr[1]          
      ],
//      'field_funny_story_type'  => [
//        ['target_id' => $term_id]
//      ]
      'field_ngay_dong_mst' => [
          'value' => $data_arr[2]          
      ],
      'field_ten_chinh_thuc' => [
          'value' => $data_arr[3]          
      ],
      'field_ten_giao_dich' => [
          'value' => $data_arr[4]          
      ],
      'field_noi_dang_ky_quan_ly' => [
          'value' => $data_arr[5]          
      ],
      'field_dien_thoai_fax' => [
          'value' => $data_arr[6]          
      ],
      'field_dia_chi_tru_so' => [
          'value' => $data_arr[7]          
      ],
      'field_noi_dang_ky_nop_thue' => [
          'value' => $data_arr[8]          
      ],
      'field_dien_thoai_fax_thue' => [
          'value' => $data_arr[9]          
      ],
      'field_diachi_nhan_thong_bao_thue' => [
          'value' => $data_arr[10]          
      ],
      'field_qdtl_ngay_cap' => [
          'value' => $data_arr[11]          
      ],
      'field_cq_ra_quyet_dinh' => [
          'value' => $data_arr[12]          
      ],
      'field_gpkd_ngay_cap' => [
          'value' => $data_arr[13]          
      ],
      'field_co_quan_cap' => [
          'value' => $data_arr[14]          
      ],
      'field_nam_tai_chinh' => [
          'value' => $data_arr[15]          
      ],
      'field_ma_so_hien_thoi' => [
          'value' => $data_arr[16]          
      ],
      'field_ngay_bat_dau_hd' => [
          'value' => $data_arr[17]          
      ],
      'field_von_dieu_le' => [
          'value' => $data_arr[18]          
      ],
      'field_ngay_nhan_tk' => [
          'value' => $data_arr[19]          
      ],
      'field_tong_so_lao_dong' => [
          'value' => $data_arr[20]          
      ],
      'field_cap_chuong_loai_khoan' => [
          'value' => $data_arr[21]          
      ],
      'field_hinh_thuc_h_toan' => [
          'value' => $data_arr[22]          
      ],
      'field_pp_tinh_thue_gtgt' => [
          'value' => $data_arr[23]          
      ],
      'field_chu_so_huu' => [
          'value' => $data_arr[24]          
      ],
      'field_dia_chi_chu_so_huu' => [
          'value' => $data_arr[25]          
      ],
      'field_ten_giam_doc' => [
          'value' => $data_arr[26]          
      ],
      'field_dia_chi_giam_doc' => [
          'value' => $data_arr[27]          
      ],
      'field_ke_toan_truong' => [
          'value' => $data_arr[28]          
      ],
      'field_dia_chi_ke_toan_truong' => [
          'value' => $data_arr[29]          
      ],
      'field_nganh_nghe_chinh' => [
          'value' => $data_arr[30]          
      ],
      'field_loai_thue_phai_nop' => [
          'value' => $data_arr[31]          
      ],
    ]);
    $node->save();
  }
  
  private function get_tid_by_name($name, $vid = 'funny_story') {
    $query = \Drupal::database()->select('taxonomy_term_field_data', 't');
    $query->addField('t', 'tid');
    $query->condition('t.vid', $vid);
    $query->condition('t.name', trim($name));
    $query->range(0, 1);
    $result = $query->execute()->fetchField();
    return $result;
  }
  ////////////////XEM//////////////
  public function get_all_xem_url_per_page($url) {
    //http://xem.vn/new/0
    //http://xem.vn/dung-mong-de-trung-v-d-d-964569.html
    $html = file_get_html($url);
    $links = $html->find('div[class=info] h2 a');
    $href = [];
    foreach($links as $link) {
      $href[] = $link->href;
      $this->get_data_xem_via_page('http://xem.vn' . $link->href, $url);
//      break;
    }
    return t('Complete');
  }
  
  public function get_data_xem_via_page($url, $p_url) {
    $html = file_get_html($url);
    $body = $title = preg_replace("/<img[^>]+\>/i", "(image) ", trim(html_entity_decode($html->find('h1', 0)->innertext)));
    if ($title == '')
      return;
    $image_link = $html->find('div[class=photoImg] img', 0)->src;
    if(trim($image_link) == NULL)
      return;
    $image_name = basename($image_link);  
    $data = file_get_contents($image_link);    
//    $file = file_save_data($data, 'public://hinh-anh/' . date('M-Y') . '/' . $image_name, FILE_EXISTS_REPLACE);
    $file = file_save_data($data, 'public://' . $image_name, FILE_EXISTS_REPLACE);

    $body = '';
    
    $node = Node::create([
      'type' => 'funny_image',
      'title' => $title,
      'status' => 0,
      'body' => [
        'value' => $body,
        'format' => 'basic_html'
      ],
      'field_temp_url' => [
          'value' => $p_url,
          'format' => 'basic_html'
      ],
      'field_real_url' => [
          'value' => $url,
          'format' => 'basic_html'
      ], 
      'field_funny_image' => [
        'target_id' => $file->id(),
        'alt' => $title
      ],
    ]);
    $node->save();
  }
  
  function get_all_cuoibebung_url_per_page($url, $tid) {
    $html = file_get_html($url);
    $links = $html->find('h2[class=entry-title] a');
    $href = [];
    foreach($links as $link) {
      $href[] =  $link->href;
      $this->get_data_via_page_cuoibebung($link->href, $url, $tid);
    }
//    return $href;
    return t('Complete');
  }
  public function get_data_via_page_cuoibebung($url, $p_url, $tid) {
    $html = file_get_html($url);
    $title = $html->find('h1', 0)->innertext;    
   
    $body = '';
    foreach($html->find('div[class=entry-content] > p') as $element) {     
      $body .= $element->innertext . '<br>';
    }
    
    $node = Node::create([
      'type' => 'funny_story',
      'title' => $title,
      'status' => 0,
      'body' => [
        'value' => $body,
        'format' => 'basic_html'
      ],
      'field_temp_url' => [
          'value' => $p_url,
          'format' => 'basic_html'
      ],
      'field_real_url' => [
          'value' => $url,
          'format' => 'basic_html'
      ],
      'field_funny_story_type'  => [
        ['target_id' => $tid]
      ]
    ]);
    $node->save();
  }
}
