<?php

class Date extends Kohana_Date {

	public static function toTimestamp($str,$format='%d/%m/%Y %H:%M:%S') {
		echo $str;
		$a = strptime($str, $format);
		//var_dump($a);
		return mktime($a['tm_hour'], $a['tm_min'], $a['tm_sec'], $a['tm_mon']+1, $a['tm_mday'], $a['tm_year']+1900);
	}

    public static function sqlTimestamp2Unix($str) {
        $format = '%Y-%m-%d %H:%M:%S';
        $a = strptime($str, $format);
        return date("U", mktime($a['tm_hour'], $a['tm_min'], $a['tm_sec'], $a['tm_mon']+1, $a['tm_mday'], $a['tm_year']+1900));
    }

    public static function intl2sql($str) {
        if (preg_match("/^(0?[1-9]|[12][0-9]|3[01])[\/\.\- ](0?[1-9]|1[0-2])[\/\.\- ](19|20\d{2})$/", $str, $matches))
            return $matches[3] . $matches[2] . $matches[1];
        return false;
    }
}
