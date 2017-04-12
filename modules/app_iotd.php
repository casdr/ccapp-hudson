<?php
class app_iotd {
	/**
	 * Return the URL of the image of the day
	 * @return string URL
	 */
	public static function main() {
		$page = file_get_html('http://photography.nationalgeographic.com/photography/photo-of-the-day/nature-weather/');
		$link = $page->find('#search_results', 0)->find('a', 0)->href;

		$page = file_get_html('http://photography.nationalgeographic.com'.$link);
		$img = $page->find('meta[property=og:image]', 0)->content;
		return $img;
	}
}
