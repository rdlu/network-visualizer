<?php

class Model_ThresholdProfile extends ORM
{

    protected $_has_many = array(
        'processes' => array(),
        'metrics' => array(
            'model' => 'metric',
            'through' => 'thresholdValues',
            'foreign_key' => 'metric_id'),
        'thresholdValues' => array(
            'foreign_key' => 'thresholdValues_id'),
    );

    protected function Sprig()
    {
        $this->_fields += array(
            'id' => new Sprig_Field_Auto(),
            'name' => new Sprig_Field_Char(array('max_length' => 20)),
            'desc' => new Sprig_Field_Char(array('max_length' => 250)),
            'processes' => new Sprig_Field_HasMany(array('model' => 'Process')),
            'thresholdValues' => new Sprig_Field_HasMany(array(
                'model' => 'thresholdValue',
                'column' => 'thresholdValues_id',
                'null' => false)),
            'metrics' => new Sprig_Field_ManyToMany(array('model' => 'Metric', 'through' => 'thresholdValues'))
        );
    }
}
