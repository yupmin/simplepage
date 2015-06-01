<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
function date_string($timestamp, $kind = NULL, $now = NULL) {
	//if (is_null($kind)) $kind = 2;
	if (is_null($now)) $now = 'now';

	$datetime = new DateTime('@'.$timestamp);
	$now_datetime = new DateTime($now);

	switch($kind) {
		case 1:
			if ($datetime->format('Ymd') == $now_datetime->format('Ymd'))
				$_date_string = $datetime->format('H:i');
			else if ($datetime->format('Y') == $now_datetime->format('Y'))
				$_date_string = $datetime->format('m-d');
			else 
				$_date_string = $datetime->format('Y');
			break;
		case 2:
			if ($datetime->format('Ymd') == $now_datetime->format('Ymd'))
				$_date_string = $datetime->format('H:i:s');
			else
				$_date_string = $datetime->format('Y-m-d');
			break;
		case 3:
			$_date_string = $datetime->format('Y-m-d');
			break;
		case 4:
			$diff_datetime = $now_datetime->diff($datetime);
			if($diff_datetime->y > 0) {
				$_date_string = $diff_datetime->y.' '.l('year'.($diff_datetime->y > 1 ? 's':''));
			} else if($diff_datetime->m > 0) {
				$_date_string = $diff_datetime->m.' '.l('month'.($diff_datetime->m > 1 ? 's':''));
			} else if($diff_datetime->d > 0) {
				$_date_string = $diff_datetime->d.' '.l('day'.($diff_datetime->d > 1 ? 's':''));
			} else if($diff_datetime->h > 0) {
				$_date_string = $diff_datetime->h.' '.l('hour'.($diff_datetime->h > 1  ? 's':''));
			} else if($diff_datetime->i > 0) {
				$_date_string = $diff_datetime->i.' '.l('minute'.($diff_datetime->i > 1 ? 's':''));
			} else if($diff_datetime->s > 0) {
				$_date_string = $diff_datetime->s.' '.l('second'.($diff_datetime->s > 1 ? 's':''));
			}	 
			$_date_string .= ' '.l('ago');
			break;
		default:
			$_date_string = $datetime->format('Y-m-d H:i:s');
	}
	return $_date_string;
}

function get_timezones_by_continent($show_offset = NULL, $time_standard = NULL) {
	if (empty($show_offset)) $show_offset = FALSE;
	if (empty($time_standard)) $time_standard = 'UTC';
	$locations = array();
	$zones = timezone_identifiers_list();

	foreach ($zones as $zone) {
		$zone = explode('/', $zone); // 0 => Continent, 1 => City

		// Only use "friendly" continent names
		if ($zone[0] == 'Africa' || $zone[0] == 'America' || $zone[0] == 'Antarctica' || $zone[0] == 'Arctic' || $zone[0] == 'Asia' || $zone[0] == 'Atlantic' || $zone[0] == 'Australia' || $zone[0] == 'Europe' || $zone[0] == 'Indian' || $zone[0] == 'Pacific') {       
			if (isset($zone[1]) != '') {
				try {
					$_offset = get_timezone_offset($time_standard, $zone[0]. '/' . $zone[1]);
					$_hour = intval($_offset / 3600);
					$_minute = abs(($_offset % 3600) / 60);
					$locations[$zone[0]][$zone[0]. '/' . $zone[1]] = str_replace('_', ' ', $zone[1]);
					if ($show_offset) {
						$locations[$zone[0]][$zone[0]. '/' . $zone[1]] .= ', UTC'.($_hour >= 0 ? '+':'-').(sprintf('%02d', abs($_hour))).':'.(sprintf('%02d', $_minute));
					}
				} catch (Exception $e) { }
			}
		} else if ($zone[0] == 'UTC') {
			$locations[$zone[0]] = $zone[0];
		}
	}
	return $locations;
}
function get_timezone_offset($remote_tz, $origin_tz = NULL) {
	if($origin_tz === NULL) {
		if(!is_string($origin_tz = date_default_timezone_get())) {
			return false; // A UTC timestamp was returned -- bail out!
		}
	}
	$origin_dtz = new DateTimeZone($origin_tz);
	$remote_dtz = new DateTimeZone($remote_tz);
	$origin_dt = new DateTime("now", $origin_dtz);
	$remote_dt = new DateTime("now", $remote_dtz);
	$offset = $origin_dtz->getOffset($origin_dt) - $remote_dtz->getOffset($remote_dt);
	return $offset;
}
/* EOF */
