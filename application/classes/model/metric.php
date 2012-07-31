<?php
/**
 * Model Metric: Mapeia os tipos de metricas do netmetric
 * @author Rodrigo Dlugokenski
 */
class Model_Metric extends ORM
{

    protected $_has_many = array(
        'thresholdProfiles' => array('model' => 'thresholdProfile', 'through' => 'thresholdvalues'),
        'thresholdValues' => array('model' => 'ThresholdValue', 'null' => false),
        'processes' => array('model' => 'process', 'through' => 'metrics_processes'),
    );

    protected $_belongs_to = array(
        'profile' => array()
    );

    public function rules()
    {
        return array(
            'name' => array(
                array('not_empty'),
                array('min_length', array(':value', 3)),
                array('max_length', array(':value', 20)),
                array(array($this, 'metric_available')),
            ),
            'plugin' => array(
                array('not_empty'),
                array('min_length', array(':value', 3)),
                array('max_length', array(':value', 20)),
                array(array($this, 'plugin_available')),
            ),
        );
    }

    public function name_available($name)
    {
        // There are simpler ways to do this, but I will use ORM for a while
        return ORM::factory('metric', array('name' => $name))->loaded();
    }

    public function plugin_available($name)
    {
        // There are simpler ways to do this, but I will use ORM for a while
        return ORM::factory('metric', array('plugin' => $name))->loaded();
    }
}
