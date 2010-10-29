<?php
/**
 * Model Entities: Mapeia as entidades do netmetric (agentes e gerentes)
 * @author Rodrigo Dlugokenski
 */
 
class Model_Entity extends Sprig {

    protected function _init() {
        $this->_fields += array(
            'id' => new Sprig_Field_Auto,
            'name' => new Sprig_Field_Char(array('unique' => true)),
            /**
             *  Endereco IPv4
             */
            'ipaddress' => new Sprig_Field_Char(array('label'=>'Endereço IPv4',
                                                     'min_length'=>7,
                                                     'max_length'=>15,
                                                     'rules' => array('ip'=>array())
                                                )),
            /**
             *  Nome de rede que possa ser resolvido via DNS (Ip Dinâmico)
             */
            'serverName' => new Sprig_Field_Char,
            /** 
             *  Descricao/Observacoes
             */
            'description' => new Sprig_Field_Text,
            'zip' => new Sprig_Field_Char(array('null' => true)),
            'address' => new Sprig_Field_Char(array('null' => true)),
            'addressnum' => new Sprig_Field_Char(array('null' => true)),
            'district' => new Sprig_Field_Char(array('null' => true)),
            'state' => new Sprig_Field_Char(array('null' => true)),
            'latitude' => new Sprig_Field_Char(array('null' => true)),
            'longitude' => new Sprig_Field_Char(array('null' => true)),
            'added' => new Sprig_Field_Timestamp(array('auto_now_create' => true, 'editable'=>false)),
            'updated' => new Sprig_Field_Timestamp(array('auto_now_update' => true,'auto_now_create' => true, 'editable'=>false)),
            /*
             * Tipo de entidade
             * 0: Gerente Linux
             * 1: Agente Windows
             * 2: Agente Windows Mobile
             */
            'type' => new Sprig_Field_Integer(
                array('choices'=>array(0=>'Gerente Linux',
                                       1=>'Agente Windows',
                                       2=>'Agente Windows Mobile'),
                     'default'=>0,
                     'label'=>'Tipo de Entidade'
                )),
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
            'sources' => new Sprig_Field_HasMany(array('model'=>'Process')),
            'destinations' => new Sprig_Field_HasMany(array('model'=>'Process'))
        );
    }
}
