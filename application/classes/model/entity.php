<?php
/**
 * Model Entities: Mapeia as entidades do netmetric (agentes e gerentes)
 * @author Rodrigo Dlugokenski
 */
 
class Model_Entity extends Sprig {

    protected function _init() {
        $this->_fields += array(
            'id' => new Sprig_Field_Auto,
            /**
             *  Endereco IPv4
             */
            'ipaddress' => new Sprig_Field_Char(array('label'=>'Endereço IP',
                    'min_length'=>7,
                    'max_length'=>255,
                    'rules' => array('ipOrHostname'),
	                  'unique' => true,
            )),
            'name' => new Sprig_Field_Char(array('unique' => true,'label'=>'Nome da entidade')),
            'state' => new Sprig_Field_Char(array('label'=>'Estado','choices'=>Model_Uf::toArray())),
            'city' => new Sprig_Field_Char(array('label'=>'Cidade')),
            /** 
             *  Descricao/Observacoes
             */
            'description' => new Sprig_Field_Text(array('null' => true,'label'=>'Observações')),
            'zip' => new Sprig_Field_Char(array('null' => true)),
            'address' => new Sprig_Field_Char(array('null' => true,'label'=>'Endereço')),
            'addressnum' => new Sprig_Field_Char(array('null' => true,'label'=>'Número')),
            'district' => new Sprig_Field_Char(array('null' => true,'label'=>'Bairro')),
            'latitude' => new Sprig_Field_Char(array(
	            'null' => true,
	            'rules' => array('numeric')
            )),
            'longitude' => new Sprig_Field_Char(array(
	            'null' => true,
	            'rules' => array('numeric')
            )),
            'added' => new Sprig_Field_Timestamp(array('auto_now_create' => TRUE, 'editable'=>false,'format' => "d.m.Y H:i:s")),
            'updated' => new Sprig_Field_Timestamp(array('auto_now_create' => true, 'editable'=>false,'format' => "d.m.Y H:i:s")),
            /*
             * Tipo de entidade
             * 0: Gerente Linux
             * 1: Agente Windows
             * 2: Agente Windows Mobile

            'type' => new Sprig_Field_Integer(
                array('choices'=>array(0=>'Gerente Linux',
                                       1=>'Agente Windows',
                                       2=>'Agente Windows Mobile'),
                     'default'=>0,
                     'label'=>'Tipo de Entidade'
                )),*/
            /*
             *  Define o estado dessa entidade, valores:
             * 1, ativo normal, aceitando novos cadastros de processos
             * 2, ativo bloqueado, processos rodando, mas nao pode cadastrar novos
             * 0, inativo, nao pode ser usado pra cadastrar novos processos
             * -1, inativo, em processo de exclusao, aguardando outros jobs terminarem
             * -2, inativo, bloqueado, NAO esta em processo de exclusao, so nao pode ter receber novos processos
             */
            'status'=> new Sprig_Field_Integer(
                array('choices'=>
                  array(
	                  0=>'Inativo',
	                  1=>'Ativo',
	                  2=>'Alerta',
	                  3=>'Erro',
	                  -1=>'Bloqueado',
                  ),
	             'default'=>1)),
            'processes_as_source' => new Sprig_Field_HasMany(array('model'=>'Process','foreign_key'=>'source_id')),
            'processes_as_destination' => new Sprig_Field_HasMany(array('model'=>'Process','foreign_key'=>'destination_id'))
        );
    }
}
