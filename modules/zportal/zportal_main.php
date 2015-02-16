<?php
class zportal_main {

	public static $base_url = 'https://candea.zportal.nl/api/v2/';

	/**
	 * Get the start and end date of a week
	 * @param  integer  The year
	 * @param  integer  The week number
	 * @return array    array(start, end)
	 */
	public static function getStartEnd($year, $week=0) {
		$wt = date("Y-m-d", strtotime($year."W".$week));
		$start = strtotime('last Monday', $wt);
		$end = strtotime('next Sunday', $wt) + 86400;
		return array($start, $end);
	}

	/**
	 * Get the access token using user code
	 * @param  integer User code
	 * @return string  Access token
	 */
	public static function getToken($code=0) {
		$url = self::$base_url.'oauth/token';
		$data = array(
			'grant_type'=>'authorization_token',
			'code'=>$code
		);
		$curl = curl::post($url, $data);
		$json = json_decode($curl, true);
		return $json['access_token'];
	}

	/**
	 * Get the schedule
	 * @param  string   The token
	 * @param  integer  Start timestamp
	 * @param  integer  End timestamp
	 * @return array    The schedule data
	 */
	public static function getSchedule($token='', $start=0, $end=0) {
		$url = self::$baseurl.'appointments';
		$data = array(
			'user'=>'~me',
			'start'=>$start,
			'end'=>$end,
			'access_token'=>$token
		);
		$fieldstring = curl::fieldstring($data);
		$url .= '?'.$fieldstring;
		$curl = curl::get($url);
		$json = json_decode($curl, true);
		return $json['response']['data'];
	}

}