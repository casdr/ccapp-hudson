<?php
class infoweb_student {
	public static $ref = 2;
	//Lines
	public static $class_les = array('times'=>0, 'teacher'=>7, 'class'=>9, 'room'=>11, 'groups'=>13);
	public static $class_toets = array('times'=>0, 'class'=>2, 'teacher'=>4, 'room'=>6, 'groups'=>8);

	public static function main($id, $week) {
		infoweb_main::setWeek($week);
		$page = infoweb_main::getWhole($id, self::$ref);
		return infoweb_main::createArray($page, $week, self::$class_les, self::$class_toets);
	}
}