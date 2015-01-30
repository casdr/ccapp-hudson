<?php
class app_iotd {
	/**
	 * Return the URL of the image of the day
	 * @return string URL
	 */
	public static function main() {
		$html = file_get_html('http://my.nature.org/nature/photos');
		$page = str_get_html($html);
		$img = $page->find('img', 3)->src;
		return $img;
	}
}