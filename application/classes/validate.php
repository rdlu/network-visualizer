<?php defined('SYSPATH') or die('No direct script access.');

class Validate extends Kohana_Validate {
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

    public static function polling($value, Model_Profile $fields) {
        $gap = (int) $fields->gap;
        $count = (int) $fields->count;
        $minWait = $count*$gap*2.2;
        return $value > $minWait;
    }

    public static function isId($value) {
        return true;
    }
}