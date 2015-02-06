<?php
/**
 * Created by Cas.
 * User: cdreuver
 * Date: 6-2-2015
 * Time: 17:36
 */

class infoweb_list_groups {
  public static function main() {
    $all = array();
    $file = dirname(__FILE__).'/../../data/students.json';
    $data = file_get_contents($file);
    $list = json_decode($data, true);
    foreach ($list as $key => $val) {
      $all[substr($key, 0, 1)][] = $key;
    }
    return $all;
  }
}