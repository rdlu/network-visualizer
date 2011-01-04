<?php
/**
 * Model Process: Mapeia os processos de medição
 * @author Rodrigo Dlugokenski
 */

class Model_Process extends Sprig {

    protected function _init() {
        $this->_fields += array(
            'id' => new Sprig_Field_Auto,
            /**
             *  Descricao/Observacoes
             */
            'added' => new Sprig_Field_Timestamp(array('auto_now_create' => true, 'editable'=>false)),
            'updated' => new Sprig_Field_Timestamp(array('auto_now_update' => true,'auto_now_create' => true, 'editable'=>false)),
            'source' => new Sprig_Field_BelongsTo(array('model'=>'Entity','column'=>'source_id')),
            'destination' => new Sprig_Field_BelongsTo(array(
                                                            'model'=>'Entity',
                                                            'column'=>'destination_id',
                                                            'empty' => false,
                                                            'rules'=>array('isId'=>array()))),
            'profile' => new Sprig_Field_BelongsTo(array('model'=>'Profile')),
            /*
             *  Define o estado dessa entidade, valores:
             * 1, ativo normal, aceitando novos cadastros de processos
             * 2, ativo bloqueado, processos rodando, mas nao pode cadastrar novos
             * 0, inativo, nao pode ser usado pra cadastrar novos processos
             * -1, inativo, em processo de exclusao, aguardando outros jobs terminarem
             * -2, inativo, bloqueado, NAO esta em processo de exclusao, so nao pode ter receber novos processos
             */
            'status'=> new Sprig_Field_Integer(
                array('choices'=>array(
                         1=>'Ativo',2=>'Bloqueado',0=>'Inativo'),
                     'default'=>1)),
        );
    }
}
