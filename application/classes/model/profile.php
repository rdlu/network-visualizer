<?php
/**
 * Model Profile: Mapeia as entidades do netmetric (agentes e gerentes)
 * @author Rodrigo Dlugokenski
 */
class Model_Profile extends ORM
{
    protected $_has_many = array(
        'processes' => array(),
        'metrics' => array(),
    );

    public function filters()
    {
        return array(
            'name' => array(array('trim')),
            'description' => array(array('trim')),
        );
    }

    public function rules()
    {
        return array(
            'name' => array(
                array('not_empty'),
                array('min_length', array(':value', 3)),
                array('max_length', array(':value', 32)),
                array(array($this, 'metric_available')),
            ),
            'count' => array(
                array('not_empty'),
                array('digit'),
            ),
            'probeCount' => array(
                array('not_empty'),
                array('digit'),
            ),
            'probeSize' => array(
                array('not_empty'),
                array('digit'),
            ),
            'gap' => array(
                array('not_empty'),
                array('digit'),
            ),
            'timeout' => array(
                array('not_empty'),
                array('digit'),
            ),
            'polling' => array(
                array('not_empty'),
                array('digit'),
            ),
            'protocol' => array(
                array('not_empty'),
                array('range', array(':value', 0, 1))
            ),
            'description' => array(
            ),
            'qosType' => array(
                array('not_empty'),
                array('range', array(':value', 0, 1))
            ),
            'qosValue' => array(
                array('not_empty'),
                array('key_exists', array(':value', Kohana::$config->load('qos.dscp')))
            ),
        );
    }

    public function enum($field)
    {
        switch ($field) {
            case 'status':
                return array('Inativo', 'Ativo');
                break;
            case 'protocol':
                return array('UDP', 'TCP');
                break;
            case 'qosType':
                return array(0 => 'DiffServ (DSCP)', 1 => 'TOS (RFC-1349)');
            case 'qosValue':
                return Kohana::$config->load('qos.dscp');
        }

        return null;
    }

    public function verbose($field)
    {
        $enum = $this->enum($field);
        return $enum[$this->$field];
    }

    public function title($field)
    {
        return __($field);
    }
}
