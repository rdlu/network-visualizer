<?php
 /**
 * Model UF: Mapeia as unidades da federacao
 * @author Rodrigo Dlugokenski
 */
class Model_Uf extends Sprig {
    protected function _init() {
        $this->_fields += array(
            'id'=>new Sprig_Field_Auto(),
            'nome'=>new Sprig_Field_Char(array('max_lenght'=>250)),
            'sigla'=>new Sprig_Field_Char(array('max_lenght'=>2)),
            'cidades' => new Sprig_Field_HasMany(array('model'=>'Cidade')),
        );
    }

    public static function toArray() {
        $states = Sprig::factory('uf')->load(NULL, FALSE);

        foreach($states as $state) {
            $return[$state->sigla] = $state->nome;
        }

        return $return;
    }
}
