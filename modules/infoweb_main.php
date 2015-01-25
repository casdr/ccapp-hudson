<?php
class infoweb_main {
	public static $base_host = 'infoweb.candea.nl';
	public static $base_url = 'http://infoweb.candea.nl';
	public static $cookiestr = '';
	/**
	 * Get a cookie to set the week
	 * @param integer Week number
	 */
	public static function setWeek($week=0) {
		// Set the URL
		$url = self::$base_url.'/selectie.inc.php?wat=week&weeknummer='.$week.'&type=0&groep=&element=&sid=0.7051241090521216';
		// Run a GET request
		$get = curl::get($url, array(CURLOPT_HEADER=>1));
		// Get the cookies from the header
		preg_match('/^Set-Cookie:\s*([^;]*)/mi', $get, $cookies);
		// Get the first cookie
		parse_str($cookies[1], $cookies);
		// Make the cookiestr empty
		self::$cookiestr = '';
		// Add the cookies
		foreach($cookies as $key=>$val) self::$cookiestr .= "$key=$val; ";
		return true;
	}
}