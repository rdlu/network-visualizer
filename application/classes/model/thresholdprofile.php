<?php

class Model_ThresholdProfile extends Sprig {

	protected function _init() {
		$this->_fields += array(
			'id' => new Sprig_Field_Auto(),
			'name' => new Sprig_Field_Char(array('max_length'=>20)),
			'desc' => new Sprig_Field_Char(array('max_length'=>250)),
			'processes' => new Sprig_Field_HasMany(array('model' => 'Process')),
			'thresholdValues' => new Sprig_Field_HasMany(array(
			                                                'model' => 'thresholdValue',
			                                                'column' => 'thresholdValues_id',
			                                                'null' => false)),
			'metrics' => new Sprig_Field_ManyToMany(array('model' => 'Metric', 'through' => 'thresholdValues'))
		);
	}
}
