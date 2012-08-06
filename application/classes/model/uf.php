<?php
/**
 * Model UF: Mapeia as unidades da federacao
 * @author Rodrigo Dlugokenski
 */
class Model_Uf extends ORM
{

    protected $_has_many = array(
        'cidades' => array(),
    );

    public function filters()
    {
        return array(
            'nome' => array(array('trim')),
            'sigla' => array(array('trim')),
        );
    }

    public function rules()
    {
        return array(
            'nome' => array(
                array('not_empty'),
                array('min_length', array(':value', 3)),
                array('max_length', array(':value', 255)),
                array(array($this, 'unique'), array('nome', ':value')),
            ),
            'sigla' => array(
                array('not_empty'),
                array('max_length', array(':value', 2)),
                array(array($this, 'unique'), array('sigla', ':value')),
            ),
        );
    }

    public static function toArray()
    {
        $return = array();
        $states = ORM::factory('uf')->find_all();

        foreach ($states as $state) {
            $return[$state->sigla] = $state->nome;
        }

        return $return;
    }
}
