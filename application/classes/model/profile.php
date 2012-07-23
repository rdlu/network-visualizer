<?php
/**
 * Model Profile: Mapeia as entidades do netmetric (agentes e gerentes)
 * @author Rodrigo Dlugokenski
 */
class Model_Profile extends Sprig
{
    protected function _init()
    {
        $this->_fields += array(
            'id' => new Sprig_Field_Auto(),
            'name' => new Sprig_Field_Char(array('max_lenght' => 32, 'label' => 'Nome do Perfil')),
            /*
             *  Coluna que define a quantidade de trens na rajada
             */
            'count' => new Sprig_Field_Integer(array('label' => 'Número de Vagões')),
            /*
             *  Coluna que define a quantidade de probes em um trem (vagão)
             */
            'probeCount' => new Sprig_Field_Integer(array('label' => 'Número de Probes (por Vagão)')),
            /*
             *  Coluna que define o tamanho de um probe
             *  Unidade: bytes
             */
            'probeSize' => new Sprig_Field_Integer(array('label' => 'Tamanho do probe (bytes)')),
            'gap' => new Sprig_Field_Integer(array('label' => 'Intervalo entre vagões (milisegundos)')),
            /*
             *  Define o tempo de espera antes de considerar cada probe como nao recebido
             *  Unidade: s (segundos)
             */
            'timeout' => new Sprig_Field_Integer(array('label' => 'Tempo de expiração (segundos)')),
            'protocol' => new Sprig_Field_Integer(array('choices' => array(0 => 'UDP', 1 => 'TCP'), 'label' => 'Protocolo')),
            'description' => new Sprig_Field_Text(array('label' => 'Descrição')),
            /*
             *  0 para diffserv (dscp)
             *  1 para tos (tos-dtr+precedence) (RFC-1349)
             */
            'qosType' => new Sprig_Field_Integer(array('label' => 'Tipo de QoS', 'choices' => array(0 => 'DiffServ (DSCP)', 1 => 'TOS (RFC-1349)'))),
            /*
             *  valor que vai ser preenchido no campo qos do pacote ip, de acordo com o qosType
             */
            'qosValue' => new Sprig_Field_Integer(array('label' => 'Valor do QoS', 'choices' => Kohana::$config->load('qos.dscp'))),
            /**
             * Relacionamento HasMany com processos
             */
            'processes' => new Sprig_Field_HasMany(array('model' => 'Process')),
            /**
             * Relacionamento HasMany com Metricas
             */
            'metrics' => new Sprig_Field_HasMany(array('model' => 'Metric', 'label' => __('Métricas'))),
            /*
             *  Define o estado desse perfil, valores:
             * 1, ativo normal
             * 0, inativo, nao pode ser usado pra cadastrar novos processos
             * -1, inativo, em processo de exclusao, aguardando outros jobs terminarem
             */
            'status' => new Sprig_Field_Integer(array('choices' => array(1 => 'Ativo', 0 => 'Inativo'))),
        );
    }

    public function title($field)
    {
        return $this->_fields[$field]->label;
    }
}
