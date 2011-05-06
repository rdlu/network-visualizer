<?php
class Model_User extends Model_Auth_User {

	/**
	 * Password validation for plain passwords.
	 *
	 * @param array $values
	 * @return Validation
	 */
	public static function get_password_validation($values)
	{
		return Validation::factory($values)
			->rule('password', 'min_length', array(':value', 5))
			->rule('password_confirm', 'matches', array(':validation', ':field', 'password'));
	}
}