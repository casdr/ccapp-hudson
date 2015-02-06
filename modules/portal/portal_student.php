<?php
class portal_student {

	/**
	 * @param string $user
	 * @param string $password
	 * @param int    $periode
	 *
	 * @return mixed
	 */
	public static function main($user='', $password='', $periode=1) {
		portal_main::login(portal_main::$url_students, $user, $password);
		return array(
			'periode'=>$periode,
			'classes'=>portal_main::createArray(portal_main::getWhole(portal_main::$url_students, $periode))
		);
	}

}