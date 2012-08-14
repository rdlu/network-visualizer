<?php

class Model_Leap_Metric extends DB_ORM_Model
{


    public function __construct()
    {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_lenght' => 11,
                'nullable' => false,
                'unsigned' => true
            )),
            'name' => new DB_ORM_Field_String($this, array(
                'label' => 'Nome da métrica',
                'nullable' => false,
                'max_length' => 20
            )),
            'plugin' => new DB_ORM_Field_String($this, array(
                'label' => 'Plugin do gerente',
                'nullable' => false,
                'max_length' => 20
            )),
            'desc' => new DB_ORM_Field_String($this, array(
                'label' => 'Descrição',
                'max_length' => 50
            )),
            'order' => new DB_ORM_Field_Integer($this, array(
                'max_lenght' => 2,
                'default' => 0,
                'nullable' => false,
                'unsigned' => true
            )),
            'reverse' => new DB_ORM_Field_Boolean($this, array(
                'label' => 'Métrica reversa',
                'nullable' => false
            ))
        );

        //TODO: Recompletar
        $this->relations = array(

            'profile' => new DB_ORM_Relation_BelongsTo($this, array(
                'parent_model' => 'profile',
                'parent_key' => 'id',
                'label' => __('Perfis')
            )),
            'thresholdValues' => new DB_ORM_Relation_HasMany($this, array(
                'child_key' => array('metric_id'),
                'child_model' => 'ThresholdValue',
                'parent_key' => array('id'),
                'nullable' => false
            )),
            'thresholdProfiles' => new DB_ORM_Relation_HasMany($this, array(
                'child_key' => 'id',
                'child_model' => 'thresholdProfile',
                'parent_key' => 'id',
                'through_model' => 'thresholdvalues',
                'through_keys' => array(
                    array('metric_id'),
                    array('thresholdprofile_id')
                )
            )),
            'processes' => new DB_ORM_Relation_HasMany($this, array(
                'child_key' => 'id',
                'child_model' => 'process',
                'parent_key' => 'id',
                'through_model' => 'metrics_processes',
                'through_keys' => array(
                    array('metric_id'),
                    array('process_id')
                )
            )),
        );
    }

    public function ipOrHostname($value)
    {
        return Valid::ipOrHostname($value);
    }

    public function isCoordinate($value)
    {
        return Valid::coordinate($value);
    }

    public function isValidPolling($value)
    {
        return Valid::polling($value);
    }
}
