<?php
/**
 * Created by JetBrains PhpStorm.
 * User: rodrigo
 * Date: 4/19/11
 * Time: 2:59 PM
 * To change this template use File | Settings | File Templates.
 */

class Model_ThresholdValue extends Sprig {

	protected function _init() {
		$this->_fields += array(
			'id' => new Sprig_Field_Auto(),
			'min' => new Sprig_Field_Float(),
			'max' => new Sprig_Field_Float(),
			'thresholdProfile' => new Sprig_Field_BelongsTo(array(
			                                                'model' => 'thresholdProfile',
			                                                'column' => 'thresholdprofile_id',
			                                                'null' => false)),
			'metric' => new Sprig_Field_BelongsTo(array('model' => 'Metric'))
		);
	}
}
