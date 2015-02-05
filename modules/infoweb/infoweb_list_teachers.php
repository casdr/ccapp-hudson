<?php
class infoweb_list_teachers {
	public static function main() {
		return self::get();
	}
	public static function get() {
		$teachers = array();
	  $week = date('W');

	  //Select the right week
	  $wset = curl_init();
	  curl_setopt($wset, CURLOPT_URL, infoweb_main::$base_url.'/index.php?ref=4');
	  curl_setopt($wset, CURLOPT_HEADER, 1);
	  curl_setopt($wset, CURLOPT_RETURNTRANSFER, 1);
	  curl_setopt($wset, CURLOPT_TIMEOUT, '3');
	  $wresult = curl_exec($wset);

	  //Get the cookies from the header
	  preg_match('/^Set-Cookie:\s*([^;]*)/mi', $wresult, $cookies);
	  parse_str($cookies[1], $cookies);

	  //Make the cookie usable
	  $cookiestr = '';
	  foreach($cookies as $key=>$val) $cookiestr .= "$key=$val; ";
	  //Get the whole page
	  $sget = curl_init();

	  curl_setopt($sget, CURLOPT_URL, infoweb_main::$base_url.'/selectie.inc.php?wat=groep&weeknummer='.$week.'&groep=*allen&type=1&sid=1245');
	  curl_setopt($sget, CURLOPT_RETURNTRANSFER, 1);
	  curl_setopt($sget, CURLOPT_COOKIE, $cookiestr);
	  curl_setopt($sget, CURLOPT_TIMEOUT, '3');
	  $sresult = curl_exec($sget);
	  $html = str_get_html($sresult);
	  $list = $html->find('select[id=select_element]', 0);
	  foreach($list->find('option') as $opt) {
	      if($opt->value != '-1') {
	        $fi = substr($opt->value, 0, 1);
	        $teachers[$fi][] = $opt->value;
	      }
	  }
	  return $teachers;
	}
}