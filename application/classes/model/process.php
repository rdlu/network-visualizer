<?php
/**
 * Model Process: Mapeia os processos de medição
 * @author Rodrigo Dlugokenski
 */

class Model_Process extends ORM
{

    protected $_has_many = array(
        'metrics' => array('model' => 'metric', 'through' => 'metrics_processes'),
    );

    protected $_belongs_to = array(
        'thresholdProfile' => array('foreign_key' => 'threshold_id'),
        'profile' => array(),
        'source' => array('model' => 'entity'),
        'destination' => array('model' => 'entity'),
    );

    public function save(Validation $validation = NULL)
    {
        if ($this->id == null && !$this->loaded()) {
            $this->added = date('U');
            $this->updated = date('U');
        } else $this->updated = date('U');

        parent::save($validation);
    }

    public function status()
    {
        switch ($this->status()) {
            case 0:
                return 'Inativo';
            case 1:
                return 'Ativo';
            case 2:
                return 'Bloqueado';
            default:
                throw new Exception("Codigo de erro não reconhecido no Model_Process");
        }

    }
}
