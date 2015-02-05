<?php
class infoweb_main {
	public static $base_host = 'infoweb.candea.nl';
	public static $base_url = 'http://infoweb.candea.nl';
	public static $cookiestr = '';
	
	// Days and times
	public static $days_les = array('lesma'=>0,'lesdi'=>1,'leswo'=>2,'lesdo'=>3,'lesvr'=>4);
	public static $days_toets = array('toetsma'=>0,'toetsdi'=>1,'toetswo'=>2,'toetsdo'=>3,'toetsvr'=>4);

	public static $hover_remove = array("showHoverInfo('', '", "', this);");
	
	/**
	 * Set the week number and store it in self::$cookiestr
	 * @param integer $week Weeknumber
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
	
	/**
	 * Get the whole schedule page
	 * @param  integer $id  ID of the 'thing'
	 * @param  integer $ref Type in an integer
	 * @return string       Le page
	 */
	public static function getWhole($id=0, $ref=0) {
		// Get the cookies for the week
		$cookies = infoweb_main::$cookiestr;
		// Set the url
		$url = infoweb_main::$base_url.'/index.php?ref='.$ref.'&id='.$id;
		// Run a GET request to URL using the cookies
		$page = curl::get($url, array(CURLOPT_COOKIE=>$cookies));
		// Return the page
		return $page;
	}
	
	/**
	 * Turn the page into an usable array
	 * @param  string  $page        The schedule page
	 * @param  integer $week        Weeknumber
	 * @param  array   $class_les  Array with the lines of data
	 * @param  array   $class_toets Array with the lines of data for tests
	 * @return array                Array with the schedule data
	 */
	public static function createArray($page='', $week=0, $class_les=array(), $class_toets=array()) {
		$classes = array();
		foreach(self::$days_les as $day)
			$classes[$day] = array();
		$ci = 0;
		$page = str_get_html($page);
		if($page->find('.roosterdeel', 0) == false) return false;
		$schedule = $page->find('.roosterdeel', 0);
		foreach($schedule->find('div [class=les],[class=toets]') as $class) {
			$wonum = trim(str_replace(range(0,9),'',$class->id));
			if($class->class == 'les') {
				$day_int = self::$days_les[$wonum];
				$class_this = $class_les;
			}
			if($class->class == 'toets') {
				$day_int = self::$days_toets[$wonum];
				$class_this = $class_toets;
			}
			if(isset($oldday) && $day_int != $oldday) $ci = 0;
			
			$lines = explode('<br />', preg_replace('#<a.*?>([^>]*)</a>#i', '$1', str_replace(self::$hover_remove, '', $class->onclick)));
			$ttimes = explode(' - ', $lines[$class_this['times']]);
			if($ci != 0 && $oldday == $day_int && isset($classes[$day_int][$ci - 1]) && $classes[$day_int][$ci - 1]['end'] < $ttimes[0]) {
				foreach($class_this as $key=>$val)
					$classes[$day_int][$ci][$key] = '';
				$classes[$day_int][$ci]['times'] = $classes[$day_int][$ci - 1]['end'].' - '.$ttimes[0];
				$classes[$day_int][$ci]['start'] = $classes[$day_int][$ci - 1]['end'];
				$classes[$day_int][$ci]['end'] = $ttimes[0];
				$classes[$day_int][$ci]['canceled'] = false;
				$classes[$day_int][$ci]['changed'] = false;
				$classes[$day_int][$ci]['class'] = 'Pauze';
				$classes[$day_int][$ci]['type'] = 'break';
				$classes[$day_int][$ci]['break'] = true;
				$ci++;
			}
			$classes[$day_int][$ci]['day'] = $day_int;
			foreach($class_this as $key=>$val) {
				if(isset($lines[$val]))
					$classes[$day_int][$ci][$key] = $lines[$val];
				else
					$classes[$day_int][$ci][$key] = '';
			}
			$extimes = explode(' - ', $classes[$day_int][$ci]['times']);
			$classes[$day_int][$ci]['start'] = $extimes[0];
			$classes[$day_int][$ci]['end'] = $extimes[1];
			$classes[$day_int][$ci]['canceled'] = false;
			$classes[$day_int][$ci]['changed'] = false;
			$classes[$day_int][$ci]['type'] = $class->class;
			if(strpos($class->style, 'tijd.gif') != 0)
				$classes[$day_int][$ci]['changed'] = true;
			$classes[$day_int][$ci]['break'] = false;
			$classes[$day_int][$ci]['test'] = false;
			if($classes[$day_int][$ci]['type'] == 'toets')
				$classes[$day_int][$ci]['test'] = true;

			$classes[$day_int][$ci]['current'] = false;
			$ctime = date('H:i');
			if($classes[$day_int][$ci]['start'] <= $ctime && $classes[$day_int][$ci]['end'] >= $ctime && date('w') == $classes[$day_int][$ci]['day'] && date('W') == $week) {
				$duration = strtotime($classes[$day_int][$ci]['end']) - strtotime($classes[$day_int][$ci]['start']);
				$cduration = strtotime($ctime) - strtotime($classes[$day_int][$ci]['start']);
				$classes[$day_int][$ci]['progress'] = 100 / $duration * $cduration;
			}

			$oldday = $day_int;
			$ci++;
		}
		return $classes;
	}
}