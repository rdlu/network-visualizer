<?php
 /**
 * Model Metric: Mapeia os tipos de metricas do netmetric
 * @author Rodrigo Dlugokenski
 */
class Model_Metric extends Sprig {
    protected function _init() {
        $this->_fields += array(
            'id'=>new Sprig_Field_Auto(),
            'name'=>new Sprig_Field_Char(array('max_lenght'=>20)),
        );
    }
}
