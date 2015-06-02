<?php
class app_iotd {
	/**
	 * Return the URL of the image of the day
	 * @return string URL
	 */
	public static function main() {
		$html = file_get_html('http://photography.nationalgeographic.com/photography/photo-of-the-day/nature-weather/');
		$page = str_get_html($html);
		$link = $page->find('#search_results', 0)->find('a', 0)->href;

		$html = str($link);
		$page = str_get_html($html);
		$img = $page->find('.primary_photo', 0)->find('img', 0)->src;
		return $img;
	}
}