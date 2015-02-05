<?php
class infoweb_list_students {
	public static function generate() {
		$groups = array();
		$allgroups = array();
		$students = array();
		$week = date('W');
		$i = 0;
		
		$status = fsockopen('infoweb.candea.nl', 80);
		if(!$status)
			die();

		infoweb_main::setWeek($week);
		$sresult = curl::get(infoweb_main::$base_url.'/index.php?ref=2', array(CURLOPT_COOKIE=>infoweb_main::$cookiestr, CURLOPT_HEADER=>1, CURLOPT_TIMEOUT=>3));
		$html = str_get_html($sresult);
		$list = $html->find('select[id=select_groep]', 0);
		foreach($list->find('option') as $opt) {
			if($opt->value != '-1' && $opt->value != '%23uit' && substr($opt->value, 0, 5) != 'BASIS')
				$groups[] = $opt->plaintext;
		}
		foreach($groups as $group) {
			$gresult = curl::get(infoweb_main::$base_url.'/selectie.inc.php?wat=groep&weeknummer='.$week.'&groep='.$group.'&type=0&sid=1245', array(CURLOPT_COOKIE=>infoweb_main::$cookiestr));
			$html = str_get_html($gresult);
			$list = $html->find('select', 0);
			foreach($list->find('option') as $opt) {
		    if($opt->value != '-1') {
	        $students[$i]['name'] = str_replace('  ', ' ', $opt->plaintext);
	        $students[$i]['id'] = $opt->value;
	        $i = $i + 1;
		    }
			}
			$allgroups[$group]['students'] = $students;
			$students = array();
			$i = 0;
		}
		return $allgroups;
	}
	public static function save() {
		$students = self::generate();
		$file = dirname(__FILE__).'/../../data/students.json';
		$current = json_encode($students, JSON_PRETTY_PRINT);
		file_put_contents($file, $current);
		return true;
	}
	public static function view() {
		$file = dirname(__FILE__).'/../../data/students.json';
		$data = file_get_contents($file);
		return json_decode($data, true);
	}
}