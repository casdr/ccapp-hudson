<?php
class infoweb_student {
	public static $ref = 2;
	// Location of the data
	public static $class_les = array('times'=>0, 'teacher'=>7, 'class'=>9, 'room'=>11, 'groups'=>13);
	public static $class_toets = array('times'=>0, 'class'=>2, 'teacher'=>4, 'room'=>6, 'groups'=>8);

	//Days and times
	public static $days_les = array('lesma'=>0,'lesdi'=>1,'leswo'=>2,'lesdo'=>3,'lesvr'=>4);
	public static $days_toets = array('toetsma'=>0,'toetsdi'=>1,'toetswo'=>2,'toetsdo'=>3,'toetsvr'=>4);

	//Remove stuff in hoverinfo
	public static $hover_remove = array("showHoverInfo('', '", "', this);");

	public static function main($id, $week) {
		infoweb_main::setWeek($week);
		$page = infoweb_main::getWhole($id, 2);
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
			$classes[$day_int][$ci]['day'] = $day_int;
			$lines = explode('<br />', preg_replace('#<a.*?>([^>]*)</a>#i', '$1', str_replace(self::$hover_remove, '', $class->onclick)));
			foreach($class_this as $key=>$val)
				$classes[$day_int][$ci][$key] = $lines[$val];
			$extimes = explode(' - ', $classes[$day_int][$ci]['times']);
			$classes[$day_int][$ci]['start'] = $extimes[0];
			$classes[$day_int][$ci]['end'] = $extimes[1];
			$classes[$day_int][$ci]['canceled'] = false;
			$classes[$day_int][$ci]['changed'] = false;
			$ci++;
		}
		return $classes;
	}
}