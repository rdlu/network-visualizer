<?php

class Model_DynData extends ORM
{

	public function filters()
	{
		return array(
			'username' => array(
				array('trim'),
			),
			'timestamp' => array(
				array('Date::toTimestamp', array(':value')),
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
				array('not_empty')
			),
			'timestamp' => array(
				// Uses Valid::not_empty($value);
				array('not_empty')
			),
		);
	}
}
