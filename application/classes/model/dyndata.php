<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Dyndata extends ORM
{
        protected $_db_group = 'zebes';
	protected $_table_name = 'dyndata';

	public function filters()
	{
		return array(
			'username' => array(
				array('trim'),
			),
			'timestamp' => array(
				//array('Date::toTimestamp', array(':value')),
			),
			'cellid' => array(
				array('trim', array(':value'," \t\n\r\0\x0B-")),
			),
		);
	}


	public function rules()
	{
		return array(
			'username' => array(
				// Uses Valid::not_empty($value);
				array('not_empty'),
                                array('max_length', array(':value', 16)),
			),
			'timestamp' => array(
				// Uses Valid::not_empty($value);
				array('not_empty')
			),
		);
	}
 

}
