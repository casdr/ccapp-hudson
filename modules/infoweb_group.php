<?php
class infoweb_group {
	public static $ref = 5;
	// Location of the data
	public static $class_les = array('times'=>0, 'teacher'=>7, 'class'=>9, 'room'=>11);
	public static $class_toets = array('times'=>0, 'class'=>2, 'teacher'=>4, 'room'=>6, 'groups'=>8);

	/**
	 * Main function to return the schedule of an group in a array
	 * @param  integer $id   The groupname
	 * @param  integer $week Weeknumber
	 * @return array         The schedule
	 */
	public static function main($id=0, $week=0) {
		$id = strtoupper($id);
		infoweb_main::setWeek($week);
		$page = infoweb_main::getWhole($id, self::$ref);
		return infoweb_main::createArray($page, $week, self::$class_les, self::$class_toets);
	}
}