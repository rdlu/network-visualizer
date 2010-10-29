<?php
 /**
 * Model Profile: Mapeia as entidades do netmetric (agentes e gerentes)
 * @author Rodrigo Dlugokenski
 */
class Model_Profile extends Sprig {
    protected function _init() {
        $this->_fields += array(
            'id'=>new Sprig_Field_Auto(),
            'name'=>new Sprig_Field_Char(array('max_lenght'=>32)),
            /**
             *  Coluna que define o intervalo entre uma rajada e outra
             * Unidade: segundos
             */
            'polling'=>new Sprig_Field_Integer(array('rules'=>array('polling'=>array($this)),
                                                    'label'=>'Polling')),
            /*
             *  Coluna que define a quantidade de trens na rajada
             */
            'count'=>new Sprig_Field_Integer(),
            /*
             *  Coluna que define a quantidade de probes em um trem
             */
            'probeCount'=>new Sprig_Field_Integer(),
            /*
             *  Coluna que define o tamanho de um probe
             *  Unidade: bytes
             */
            'probeSize'=>new Sprig_Field_Integer(array('label'=>'Tamanho do probe (bytes)')),
            'gap'=>new Sprig_Field_Integer(),
            /*
             *  Define o tempo de espera antes de considerar cada probe como nao recebido
             *  Unidade: s (segundos)
             */
            'timeout'=>new Sprig_Field_Integer(),
            /*
             *  0 para diffserv (dscp)
             *  1 para tos (tos-dtr+precedence) (RFC-1349)
             */
            'qosType'=>new Sprig_Field_Integer(array('choices'=>array(0=>'DiffServ (DSCP)',1=>'TOS (RFC-1349)'))),
            /*
             *  valor que vai ser preenchido no campo qos do pacote ip, de acordo com o qosType
             */
            'qosValue'=>new Sprig_Field_Integer(array('choices'=>Kohana::config('qos.dscp'))),
            /*
             *  Define o estado desse perfil, valores:
             * 1, ativo normal
             * 0, inativo, nao pode ser usado pra cadastrar novos processos
             * -1, inativo, em processo de exclusao, aguardando outros jobs terminarem
             */
            'status'=>new Sprig_Field_Integer(array('choices'=>array(1=>'Ativo',0=>'Inativo'))),
            /**
             * Relacionamento HasMany com processos
             */
            'processes'=>new Sprig_Field_HasMany(array('model'=>'Process'))
        );
    }
}
