<?php defined('SYSPATH') or die('No direct script access.');

class Valid extends Kohana_Valid {
    /**
	 * Checks that a field is long enough.
	 *
	 * @param   string   value
	 * @param   integer  minimum length required
	 * @return  boolean
	 */
	public static function min_length($value, $length)
	{
		return UTF8::strlen($value) >= $length;
	}

    public static function polling($value) {
        $gap = (int) $_POST['gap'];
        $count = (int) $_POST['count'];
        $minWait = $count*$gap*2.2;
        //Fire::info("Tempo mínimo de polling: $minWait");
        return $value*1000 > $minWait;
    }

    public static function isId($value) {
        return true;
    }

	/**
	 * Funcao que valida data em formato interncional com "/"
	 * @param  string $str
	 * @return bool
	 */
	public static function data($str) {
		if (preg_match("/^(0?[1-9]|[12][0-9]|3[01])[\/\.\- ](0?[1-9]|1[0-2])[\/\.\- ](19|20\d{2})$/", $str, $matches))
			if (checkdate($matches[2], $matches[1], $matches[3]))
				return true;
		return false;
	}

	public static function hora($str) {
		if(preg_match("/(2[0-3]|[01][0-9]):[0-5][0-9]/", $str))
			return true;
	   return false;
	}

	public static function ipOrHostname($str) {
		if(self::ip($str) || self::hostname($str))
			return true;
		return false;
	}

	public static function hostname($str) {
		if(preg_match("/^(([a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*([A-Za-z]|[A-Za-z][A-Za-z0-9\-]*[A-Za-z0-9])$/",$str))
			return true;
		return false;
	}
}
