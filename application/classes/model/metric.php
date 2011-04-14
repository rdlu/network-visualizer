<?php
 /**
 * Model Metric: Mapeia os tipos de metricas do netmetric
 * @author Rodrigo Dlugokenski
 */
class Model_Metric extends Sprig {
	protected function _init() {
		$this->_fields += array(
			'id' => new Sprig_Field_Auto(),
			'name' => new Sprig_Field_Char(array('max_lenght' => 20)),
			'desc' => new Sprig_Field_Char(array('max_lenght' => 50)),
			'profile' => new Sprig_Field_BelongsTo(array('model' => 'Profile', 'label' => __('Perfis'))),
			'processes' => new Sprig_Field_ManyToMany(array('model' => 'Metric', 'through'=>'metrics_processes')),
			'order' => new Sprig_Field_Integer(),
			'reverse' => new Sprig_Field_Boolean()
		);
	}
}
