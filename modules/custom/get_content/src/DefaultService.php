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
  private $source_website;
  public function __construct() {
    include "modules/custom/get_content/src/include/simple_html_dom.php";
    $this->source_website = 'https://thongtindoanhnghiep.co';
  }

  public function get_all_url_phuong_xa($url) {
    $html_tinh_tpho = file_get_html($url);
    $link_tinh_tphos = $html_tinh_tpho->find('div[class=row] p a');
    foreach($link_tinh_tphos as $key => $link_tinh_tpho) {
      if($key < 60)
        continue;
      if($key == 80)
        break;
      $html_quan_huyen = file_get_html($this->source_website . $link_tinh_tpho->href);
      
      $links_quan_huyens = $html_quan_huyen->find('ul[id=sidebar-nav] ul a');
      if(count($links_quan_huyens) > 0) {
        foreach($links_quan_huyens as $links_quan_huyen) {
          $test = $links_quan_huyen->href;
          $html_phuong_xa = file_get_html($this->source_website . $links_quan_huyen->href);
          $links_phuong_xas = $html_phuong_xa->find('ul[id=sidebar-nav] ul a');
          foreach ($links_phuong_xas as $links_phuong_xa)
            echo $link_tinh_tpho->innertext . 
                  ':::' . $links_quan_huyen->innertext . 
                  ':::' .$links_phuong_xa->innertext . ':::' . $links_phuong_xa->href . '<br />';
        }      
      }
    }  
    return t('Complete');
  }

  public function get_all_url_per_page($url, $limit,$date) {
    $url_arr = explode('?p=', $url);
    $current = $url_arr[1];
    $next = $current + 1;
    
//    echo $next;
    $html = file_get_html($url);
    $links = $html->find('h2 a');
    if(count($links) > 0) {
      $total = 0;
      foreach ($links as $link) {
        $check = $this->get_data_via_page($link->href, $url, $date);
        $total += $check;
        if($check == 0)
          break;
      }
      sleep(1); //page 22 will see some issues(delayed 2s)
      if($next < $limit && $total == 15) {
        header("Location: http://nhanxet.local/get_content?limit=$limit&date=$date&url=$url_arr[0]?p=$next");
        exit;
      }
      return $total;
    }   
    return $total;
  }

  public function get_data_via_page($url, $p_url, $date) {
    $base_source_url = 'https://thongtindoanhnghiep.co/';
    $html = file_get_html($base_source_url . $url);

    $table = $html->find('table[class=table table-striped table-bordered table-responsive table-details]', 0);

    $tds = $table->find('td');
    $data_arr = [];

    foreach ($tds as $key => $td) {
      if ($key < 31)
        $data_arr[] = html_entity_decode($td->plaintext);
      else
        $data_arr[] = html_entity_decode($td->innertext);
    }
    dpm($data_arr[1]);
    if(strtotime(str_replace(' ','', $data_arr[1])) < $date) //1499878800 => 13-07-2017
      return 0;
    $data = [
                'type' => 'company',
//                'title' => $title,
                'title' => $data_arr[3],
                'status' => 0,
                'revision' => 0,
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
        // this field need to be rewrited again by taxonomy fields
                'field_dia_chi_tru_so_chi_tiet' => [
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
                'field_ngay_nhan_tk' => [
                    'value' => $data_arr[17]
                ],
                'field_ngay_bat_dau_hd' => [
                    'value' => $data_arr[18]
                ],
                'field_von_dieu_le' => [
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
                    'value' => $data_arr[31],
                    'format' => 'basic_html'
                ],
                'field_nguon' => [
                    'value' => $base_source_url . $url
                ],
                'field_nguon_phan_trang' => [
                    'value' => $p_url
                ],
    ];

    $node = Node::create($data);
    $node->save();
    return 1;
  }
  
  public function get_all_url_province($url) {
    $myfile = fopen("info", "r") or die("Unable to open file!");
    // Output one line until end-of-file
    
    while(!feof($myfile)) {
      $line = fgets($myfile);
      $lines = explode(':::', $line);
      $tinh = $this->get_tid_by_name($lines[0]);      
      if($tinh) {
        $huyen = $this->get_tid_by_name($lines[1]);        
        if($huyen) {
          $xa = $this->get_tid_by_name($lines[2]);
          if($xa) {

          }
          else {
            $xa_id = Term::create([
              'name' => $lines[2], 
              'vid' => 'tinh_thanh_pho',
              'parent' => $huyen
            ])->save();
          }
        }
        else { //if huyen doesn't exist => xa doesn't too
          $huyen_id = Term::create([
            'name' => $lines[1], 
            'vid' => 'tinh_thanh_pho',
            'parent' => $tinh
          ])->save();
          $xa_id = Term::create([
            'name' => $lines[2], 
            'vid' => 'tinh_thanh_pho',
            'parent' => $huyen_id->id()
          ])->save();
        }
      }
      else { //if tinh doesn't exist => huyen, xa don't exist too
        $tinh_id = Term::create([
          'name' => $lines[0], 
          'vid' => 'tinh_thanh_pho',
//          'parent' => $field_dia_chi_quan_huyen
        ])->save();
        $huyen_id = Term::create([
          'name' => $lines[1], 
          'vid' => 'tinh_thanh_pho',
          'parent' => $tinh_id->id()
        ])->save();
        $xa_id = Term::create([
          'name' => $lines[2], 
          'vid' => 'tinh_thanh_pho',
          'parent' => $huyen_id->id()
        ])->save();
      }
    }
    fclose($myfile);
//    $html = file_get_html($url);
//    $provinces = $html->find('select[id=TinhThanhIDValue] option');
//    foreach ($provinces as $province) {      
//      if(strlen($province->attr['value']) > 0) {
//        $this->get_all_url_quan_huyen ($province->attr['value'], $province->plaintext);
//      }        
//    }      
    return t('Complete');
  }
  
  public function get_detail_url($url, $times) {
    
  }
  
  public function get_all_url_quan_huyen($url, $province) {
    $vid = 'tinh_thanh_pho';
    $new_term = Term::create([
      'name' => html_entity_decode($province),
      'vid' => $vid
    ]);
    $new_term->save();    
    $default_province = 'https://thongtindoanhnghiep.co/getDistrict?location=';
    $html = file_get_html($default_province . $url);
    $datas = $html->find('option');
    foreach($datas as $data) {
      if(html_entity_decode($data->plaintext) != 'Quận/Huyện')
        Term::create([
          'name' => html_entity_decode($data->plaintext), 
          'vid' => $vid,
          'parent' => $new_term->id()
        ])->save();
    }
    return t('Complete');
  }
  
  private function get_tid_by_name($name, $vid = 'tinh_thanh_pho') {
    $query = \Drupal::database()->select('taxonomy_term_field_data', 't');
    $query->addField('t', 'tid');
    $query->condition('t.vid', $vid);
    $query->condition('t.name', trim($name));
    $query->range(0, 1);
    $result = $query->execute()->fetchField();
    return $result;
  }
}


