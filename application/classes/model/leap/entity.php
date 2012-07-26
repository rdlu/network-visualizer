<?php

class Model_Leap_Entity extends DB_ORM_Model
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
            'isAndroid' => new DB_ORM_Field_Boolean($this, array(
                'label' => 'É um Agente Android',
                'nullable' => false
            )),
            'ipaddress' => new DB_ORM_Field_String($this, array(
                'callback' => 'ipOrHostname',
                'label' => 'Endereço IP',
                'max_length' => 255,
                'nullable' => false,
            )),
            'name' => new DB_ORM_Field_String($this, array(
                'label' => 'Nome da entidade',
                'nullable' => false,
            )),
            'state' => new DB_ORM_Field_String($this, array(
                    'label' => 'Estado',
                    'enum' => Model_Uf::toArray(),
                    'nullable' => false,
                )
            ),
            'city' => new DB_ORM_Field_String($this, array('label' => 'Cidade', 'nullable' => false,)),
            /**
             *  Descricao/Observacoes
             */
            'description' => new DB_ORM_Field_Text($this, array('label' => 'Observações')),
            'zip' => new DB_ORM_Field_String($this),
            'address' => new DB_ORM_Field_String($this, array('label' => 'Endereço')),
            'addressnum' => new DB_ORM_Field_String($this, array('label' => 'Número')),
            'district' => new DB_ORM_Field_String($this, array('label' => 'Bairro')),
            'latitude' => new DB_ORM_Field_String($this, array(
                'callback' => 'coordinate'
            )),
            'longitude' => new DB_ORM_Field_String($this, array(
                'callback' => 'coordinate'
            )),
            /**
             *  Coluna que define o intervalo entre uma rajada e outra
             * Unidade: segundos
             */
            'polling' => new DB_ORM_Field_Integer($this, array(
                'callback' => 'isValidPolling',
                'label' => 'Intervalo de Polling (segundos)'
            )),
            'added' => new DB_ORM_Field_Integer($this, array(
                'default' => date('U'),
                'nullable' => false,
            )),
            'updated' => new DB_ORM_Field_Integer($this, array(
                'default' => date('U'),
                'nullable' => false,
            )),
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
            'status' => new DB_ORM_Field_Integer($this, array(
                'range' => array(-1, 3),
                'control' => 'select',
                'enum' => array(
                    0 => 'Inativo',
                    1 => 'Ativo',
                    2 => 'Alerta',
                    3 => 'Erro',
                    -1 => 'Bloqueado',
                ),
                'default' => 1
            )),
            'processes_as_source' => new Sprig_Field_HasMany(array('model' => 'Process', 'foreign_key' => 'source_id')),
            'processes_as_destination' => new Sprig_Field_HasMany(array('model' => 'Process', 'foreign_key' => 'destination_id'))
        );

        $this->adaptors = array(
            'TimestampAdded' => new DB_ORM_Field_Adaptor_DateTime($this, array(
                'field' => 'added',
                'format' => 'd.m.Y H:i:s'
            )),
            'TimestampUpdated' => new DB_ORM_Field_Adaptor_DateTime($this, array(
                'field' => 'updated',
                'format' => 'd.m.Y H:i:s'
            ))
        );

        $this->relations = array(
            'processes_as_source' => new DB_ORM_Relation_HasMany($this, array(
                'child_key' => array('source_id'),
                'child_model' => 'process',
                'parent_key' => array('id')
            )),
            'processes_as_destination' => new DB_ORM_Relation_HasMany($this, array(
                'child_key' => array('destination_id'),
                'child_model' => 'process',
                'parent_key' => array('id')
            )),
            'destinations' => new DB_ORM_Relation_HasMany($this, array(
                'child_key' => 'id',
                'child_model' => 'entity',
                'parent_key' => 'id',
                'through_model' => 'process',
                'through_keys' => array(
                    array('source_id'),
                    array('destination_id')
                )
            )),
            'sources' => new DB_ORM_Relation_HasMany($this, array(
                'child_key' => 'id',
                'child_model' => 'entity',
                'parent_key' => 'id',
                'through_model' => 'process',
                'through_keys' => array(
                    array('destination_id'),
                    array('source_id')
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
