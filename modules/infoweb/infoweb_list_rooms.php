<?php
/**
 * Created by Cas.
 * User: cdreuver
 * Date: 4-2-2015
 * Time: 22:34
 */

class infoweb_list_rooms {
  /**
   * Return the rooms as an array
   * @return array
   */
  public static function main() {
    $file = file_get_contents(dirname(__FILE__).'/../../data/rooms.json');
    $array = json_decode($file, true);
    return $array;
  }

  /**
   * Generate the json
   */
  public static function generateJson() {
    $array = self::createArray();
    $path = dirname(__FILE__).'/../data/rooms.json';
    $data = json_encode($array, JSON_PRETTY_PRINT);
    file_put_contents($path, $data);
  }
  /**
   * Parse the rooms from the website
   * @return array
   */
  public static function createArray() {
    $rooms = array();
    $page = curl::get(infoweb_main::$base_url.'/index.php?ref=4');
    infoweb_main::setWeek(date('W'));
    $page = curl::get(infoweb_main::$base_url.'/selectie.inc.php?wat=groep&weeknummer='.date('W').'&groep=*allen&type=2&sid=1245', array(CURLOPT_COOKIE=>infoweb_main::$cookiestr));
    $page = str_get_html($page);
    $list = $page->find('select', 0);
    foreach($list->find('option') as $opt) {
      if($opt->value != '-1') {
        $rooms[substr($opt->value, 0, 1)][] = $opt->value;
      }
    }
    return $rooms;
  }
}