<?php
class infoweb_student {
	public static $ref = 2;
	public static $class_les = array('times'=>0, 'teacher'=>7, 'class'=>9, 'room'=>11, 'groups'=>13);
	public static $class_toets = array('times'=>0, 'class'=>2, 'teacher'=>4, 'room'=>6, 'groups'=>8, 'comment'=>10);
	/**
	 * Get the whole page from infoweb
	 * @param  integer Student ID
	 * @return string The whole page
	 */
	public static function getWhole($id=0) {
		$cookies = infoweb_main::$cookiestr;
		$url = infoweb_main::$base_url.'/index.php?ref='.self::$ref.'&id='.$id;
		$page = curl::get($url, array(CURLOPT_COOKIE=>$cookies));
		return $page;
	}
}