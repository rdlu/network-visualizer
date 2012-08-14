<?php
class Model_User extends Model_Auth_User
{

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

    public function rules()
    {
        return array(
            'username' => array(
                array('not_empty'),
                array('min_length', array(':value', 4)),
                array('max_length', array(':value', 32)),
                array('regex', array(':value', '/^[a-z](?=[\w.]{3,31}$)\w*\.?\w*$/i')),
                array(array($this, 'unique'), array('username', ':value')),
            ),
            'password' => array(
                array('not_empty'),
            ),
            'email' => array(
                array('not_empty'),
                array('min_length', array(':value', 4)),
                array('max_length', array(':value', 127)),
                array('email'),
                array(array($this, 'unique'), array('email', ':value')),
            ),
        );
    }
}