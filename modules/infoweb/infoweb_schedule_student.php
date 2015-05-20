<?php
class infoweb_student {
	public static $ref = 2;
	//Lines
	public static $class_les = array('times'=>0, 'teacher'=>7, 'class'=>9, 'lesson'=>9, 'room'=>11, 'groups'=>13);
	public static $class_toets = array('times'=>0, 'class'=>6, 'lesson'=>8, 'teacher'=>4, 'room'=>2, 'groups'=>8);

	public static function main($id, $week) {
		infoweb_main::setWeek($week);
		$page = infoweb_main::getWhole($id, self::$ref);
		return infoweb_main::createArray($page, $week, self::$class_les, self::$class_toets);
	}
	public static function ics($id) {
		$time = strtotime('monday this week');
		infoweb_main::setSecure(self::$ref);
		$cookies = infoweb_main::$cookiestr;
		// Set the url
		$url = infoweb_main::$base_url.'/export.php?ref='.self::$ref.'&id='.$id.'&dag='.$time;
		// Run a GET request to URL using the cookies
		$page = curl::get($url, array(CURLOPT_COOKIE=>$cookies));
		return str_replace('VERSION:2.11.1004', 'VERSION:2.0', $page);
	}
}
