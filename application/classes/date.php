<?php

class Date extends Kohana_Date {

	public static function toTimestamp($str,$format='%d-%m-%Y %H:%M:%S') {
		$a = strptime($str, $format);
		return mktime($a['tm_hour'], $a['tm_min'], $a['tm_sec'], $a['tm_mon'], $a['tm_mday'], $a['tm_year']);
	}
}
