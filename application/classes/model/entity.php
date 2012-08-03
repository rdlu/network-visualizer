<?php
/**
 * Model Entities: Mapeia as entidades do netmetric (agentes e gerentes)
 * @author Rodrigo Dlugokenski
 */

class Model_Entity extends ORM
{
    protected $_has_many = array(
        'processes_as_source' => array('model' => 'process', 'foreign_key' => 'source_id'),
        'processes_as_destination' => array('model' => 'process', 'foreign_key' => 'destination_id'),
        'destinations' => array(
            'model' => 'entity',
            'through' => 'processes',
            'foreign_key' => 'source_id'),
        'sources' => array(
            'model' => 'entity',
            'through' => 'processes',
            'foreign_key' => 'destination_id'),
    );

    public function filters()
    {
        return array(
            'name' => array(array('trim')),
            'ipaddress' => array(array('trim')),
            'city' => array(array('trim')),
            'longitude' => array(array('trim')),
            'latitude' => array(array('trim'))
        );
    }

    public function rules()
    {
        return array(
            'isAndroid' => array(
                array('not_empty'),
                array('range', array(':value', 0, 1))
            ),
            'ipaddress' => array(
                array('not_empty'),
                array('min_length', array(':value', 7)),
                array('max_length', array(':value', 255)),
                array('ipOrHostname'),
                array(array($this, 'ipaddress_available')),
            ),
            'name' => array(
                array('not_empty'),
                array('min_length', array(':value', 3)),
                array('max_length', array(':value', 255)),
                array(array($this, 'name_available')),
            ),
            'state' => array(
                array('not_empty'),
                array('key_exists', array(':value', Model_Uf::toArray()))
            ),
            'city' => array(
                array('not_empty'),
                array('min_length', array(':value', 3)),
                array('max_length', array(':value', 255)),
            ),
            'longitude' => array(
                array('coordinate'),
            ),
            'latitude' => array(
                array('coordinate'),
            ),
            'polling' => array(
                array('polling')
            ),
            'status' => array(
                array('range', array(':value', -1, 3))
            )
        );
    }

    public function save(Validation $validation = NULL)
    {
        if ($this->id == null && !$this->loaded()) {
            $this->added = date('U');
            $this->updated = date('U');
        } else $this->updated = date('U');

        parent::save($validation);
    }

    public function name_available($name)
    {
        // There are simpler ways to do this, but I will use ORM for a while
        return !ORM::factory('entity', array('name' => $name))->loaded();
    }

    public function ipaddress_available($ipaddress)
    {
        // There are simpler ways to do this, but I will use ORM for a while
        return !ORM::factory('entity', array('ipaddress' => $ipaddress))->loaded();
    }

    public function status()
    {
        switch ($this->status()) {
            case 0:
                return 'Inativo';
            case 1:
                return 'Ativo';
            case 2:
                return 'Alerta';
            case 3:
                return 'Erro';
            case -1:
                return 'Bloqueado';
            default:
                throw new Exception("Codigo de erro não reconhecido no Model_Entity");
        }
    }
}
