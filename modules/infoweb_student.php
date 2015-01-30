<?php
class infoweb_student {
	public static $ref = 2;
	//Days and times
	public static $days_les = array('lesma'=>0,'lesdi'=>1,'leswo'=>2,'lesdo'=>3,'lesvr'=>4);
	public static $days_toets = array('toetsma'=>0,'toetsdi'=>1,'toetswo'=>2,'toetsdo'=>3,'toetsvr'=>4);

	public static function main($id, $week) {
		infoweb_main::setWeek($week);
		$page = infoweb_main::getWhole($id, self::$ref);
		return infoweb_main::createArray($page, $week, self::$class_les, self::$class_toets);
	}
}