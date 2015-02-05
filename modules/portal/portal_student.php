<?php
class portal_student {

	/**
	 * Main function for student grades
	 * @param  string $user     Username for the portal
	 * @param  string $password Password for the portal
	 * @return string           HTML of page
	 */
	public static function main($user='', $password='') {
		portal_main::login(portal_main::$url_students, $user, $password);
		return portal_main::getWhole(portal_main::$url_students);
	}

}