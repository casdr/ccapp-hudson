<?php
class infoweb_group {
	public static $ref = 5;
	// Location of the data
	public static $class_les = array('times'=>0, 'teacher'=>7, 'class'=>9, 'room'=>11);
	public static $class_toets = array('times'=>0, 'class'=>2, 'teacher'=>4, 'room'=>6, 'groups'=>8);

	//Days and times
	public static $days_les = array('lesma'=>0,'lesdi'=>1,'leswo'=>2,'lesdo'=>3,'lesvr'=>4);
	public static $days_toets = array('toetsma'=>0,'toetsdi'=>1,'toetswo'=>2,'toetsdo'=>3,'toetsvr'=>4);

	//Remove stuff in hoverinfo
	public static $hover_remove = array("showHoverInfo('', '", "', this);");

	public static function main($id, $week) {
		$id = strtoupper($id);
		infoweb_main::setWeek($week);
		$page = infoweb_main::getWhole($id, self::$ref);
		return self::getSchedule($page, $week);
	}

	/**
	 * Convert the page into a json
	 * @param  string The page
	 * @param  integer Week number
	 * @return array The data
	 */
	public static function getSchedule($page='', $week=0) {
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
				$class_this = self::$class_les;
			}
			if($class->class == 'toets') {
				$day_int = self::$days_toets[$wonum];
				$class_this = self::$class_toets;
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
				$ci++;
			}
			$classes[$day_int][$ci]['day'] = $day_int;
			foreach($class_this as $key=>$val)
				$classes[$day_int][$ci][$key] = $lines[$val];
			$extimes = explode(' - ', $classes[$day_int][$ci]['times']);
			$classes[$day_int][$ci]['start'] = $extimes[0];
			$classes[$day_int][$ci]['end'] = $extimes[1];
			$classes[$day_int][$ci]['canceled'] = false;
			$classes[$day_int][$ci]['changed'] = false;
			$classes[$day_int][$ci]['type'] = $class->class;
			if(strpos($class->style, 'tijd.gif') != 0)
				$classes[$day_int][$ci]['changed'] = true;
			$oldday = $day_int;
			$ci++;
		}
		return $classes;
	}
}