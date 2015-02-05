<?php
class portal_main {
	public static $cookiestr = '';
	public static $url_students = 'https://leerlingen.candea.nl';
	public static $url_parents = 'https://ouders.candea.nl';
	public static $url_teachers = 'https://personeel.candea.nl';

	/**
	 * @param string $url
	 * @param string $user
	 * @param string $password
	 * @return bool
	 */
	public static function login($url='', $user='', $password='') {
		$logindata = array(
			'wu_loginname' => urlencode($user),
			'wu_password' => urlencode($password),
			'Login' => urlencode('Inloggen'),
		);

		$curl = curl::post($url.'/Login?passAction=login', $logindata, array(CURLOPT_HEADER=>1, CURLOPT_FOLLOWLOCATION=>1));
		if(strpos($curl, 'Inloggegevens onjuist') != 0) return false;
		preg_match('/^Set-Cookie:\s*([^;]*)/mi', $curl, $cookies);
		parse_str($cookies[1], $cookies);

		$cookiestr = '';
		foreach($cookies as $key=>$val) $cookiestr .= "$key=$val; ";
		self::$cookiestr = $cookiestr;

		return true;
	}

	/**
	 * @param string $url
	 * @return string
	 */
	public static function getWhole($url='') {
		$curl = curl::get($url.'/Portaal/Cijferlijst/Examendossier/Cijferlijst', array(CURLOPT_COOKIE=>self::$cookiestr,CURLOPT_FOLLOWLOCATION=>1));
		return $curl;
	}
}