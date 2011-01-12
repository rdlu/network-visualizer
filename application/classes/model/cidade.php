<?php
 /**
 * Model Cidade: Mapeia as cidades
 * @author Rodrigo Dlugokenski
 */
class Model_Cidade extends Sprig {
    protected function _init() {
        $this->_fields += array(
            'id'=>new Sprig_Field_Auto(),
            'nome'=>new Sprig_Field_Char(array('max_length'=>250)),
            'bairros' => new Sprig_Field_HasMany(array('model'=>'Bairro')),
            'uf' => new Sprig_Field_BelongsTo(array('model'=>'Uf')),
        );
    }
}
