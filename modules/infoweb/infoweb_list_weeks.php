<?php
class infoweb_weeks {
	public static function main() {
		$page = curl::get(infoweb_main::$base_url.'/index.php?ref=2');
		$page = str_get_html($page);
		$selector = $page->find('#select_week', 0);
		$i = 0;
		foreach($selector->find('option') as $option) {
			//Remove the weeknumber
			$dates = explode('-', str_replace(array($option->value, ' '), '', $option->plaintext));
			$weeks[$i]['start'] = str_replace('/', '-', $dates[0]);
			$weeks[$i]['end'] = str_replace('/', '-', $dates[1]);
			$weeks[$i]['week'] = $option->value;
			if(isset($option->selected) && $option->selected == 'selected') $weeks[$i]['current'] = true;
			else $weeks[$i]['current'] = false;
			if(isset($option->disabled) && $option->disabled == 'disabled') $weeks[$i]['status'] = false;
			else $weeks[$i]['status'] = true;
			$i++;
		}
		return $weeks;
	}
}