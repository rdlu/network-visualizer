<?php
/**
 * Model Cidade: Mapeia as cidades
 * @author Rodrigo Dlugokenski
 */
class Model_Cidade extends ORM
{
    protected $_has_many = array(
        'bairros' => array(),
    );

    protected $_belongs_to = array(
        'uf' => array(),
    );

    public function filters()
    {
        return array(
            'nome' => array(array('trim')),
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
        );
    }

    protected function _init()
    {
        $this->_fields += array(
            'id' => new Sprig_Field_Auto(),
            'nome' => new Sprig_Field_Char(array('max_length' => 250)),
            'bairros' => new Sprig_Field_HasMany(array('model' => 'Bairro')),
            'uf' => new Sprig_Field_BelongsTo(array('model' => 'Uf')),
        );
    }
}
