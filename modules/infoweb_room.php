<?php
class infoweb_room {
	public static $ref = 4;
	// Location of the data
	public static $class_les = array('times'=>0, 'teacher'=>7, 'class'=>9, 'groups'=>11);
	public static $class_toets = array('times'=>0, 'class'=>2, 'teacher'=>4, 'room'=>6, 'groups'=>8);

	/**
	 * Main function that returns the schedule in an array
	 * @param  [type] $id   [description]
	 * @param  [type] $week [description]
	 * @return [type]       [description]
	 */
	public static function main($id, $week) {
		$id = strtoupper($id);
		infoweb_main::setWeek($week);
		$page = infoweb_main::getWhole($id, self::$ref);
		return infoweb_main::createArray($page, $week, self::$class_les, self::$class_toets);
	}

	
}