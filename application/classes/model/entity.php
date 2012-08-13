<?php
/**
 * Model Entities: Mapeia as entidades do netmetric (agentes e gerentes)
 * @author Rodrigo Dlugokenski
 */

class Model_Entity extends ORM
{
    protected $_has_many = array(
        'processes_as_source' => array('model' => 'process', 'foreign_key' => 'source_id'),
        'processes_as_destination' => array('model' => 'process', 'foreign_key' => 'destination_id'),
        'destinations' => array(
            'model' => 'entity',
            'through' => 'processes',
            'foreign_key' => 'source_id'),
        'sources' => array(
            'model' => 'entity',
            'through' => 'processes',
            'foreign_key' => 'destination_id'),
    );

    public function filters()
    {
        return array(
            'name' => array(array('trim')),
            'ipaddress' => array(array('trim')),
            'city' => array(array('trim')),
            'longitude' => array(array('trim')),
            'latitude' => array(array('trim'))
        );
    }

    public function rules()
    {
        return array(
            'isAndroid' => array(
                array('not_empty'),
                array('range', array(':value', 0, 1))
            ),
            'ipaddress' => array(
                array('not_empty'),
                array('min_length', array(':value', 7)),
                array('max_length', array(':value', 255)),
                array('ipOrHostname'),
                array(array($this, 'unique'), array('ipaddress', ':value')),
            ),
            'name' => array(
                array('not_empty'),
                array('min_length', array(':value', 3)),
                array('max_length', array(':value', 255)),
                array(array($this, 'unique'), array('name', ':value')),
            ),
            'state' => array(
                array('not_empty'),
                array('key_exists', array(':value', Model_Uf::toArray()))
            ),
            'city' => array(
                array('not_empty'),
                array('min_length', array(':value', 3)),
                array('max_length', array(':value', 255)),
            ),
            'longitude' => array(
                array('coordinate'),
            ),
            'latitude' => array(
                array('coordinate'),
            ),
            'polling' => array(
                array('polling')
            ),
            'status' => array(
                array('range', array(':value', -1, 3))
            )
        );
    }

    protected function inputs()
    {
        return array(
            'isAndroid' => array(
                'id' => 'isAndroid',
                'type' => 'radio',
                'choices' => array('Android', 'Linux'),
                'label' => true,
                'label_position' => 'right'
            ),
            'ipaddress' => array(
                'label_text' => 'Endereço IP: '
            ),
            'description' => array(
                'type' => 'textarea',
                'label_text' => 'Descrição: '
            ),
            'state' => array(
                'type' => 'select',
                'choices' => Model_Uf::toArray(),
                'label_text' => "UF: "
            ),
            'city' => array(
                'label_text' => "Cidade: "
            ),
            'name' => array(
                'label_text' => "Nome da entidade: "
            ),
            'district' => array(
                'label_text' => "Bairro: "
            ),
            'longitude' => array(
                'label_text' => "Longitude: "
            ),
            'latitude' => array(
                'label_text' => "Latitude: "
            ),
            'address' => array(
                'label_text' => "Endereço: "
            ),
            'addressnum' => array(
                'label_text' => "Núm.: "
            ),
        );
    }

    public function input($name, $extraOptions = array())
    {
        $default_values = array(
            'id' => $name,
            'type' => 'input',
            'label' => false,
            'label_position' => 'left',
            'label_attributes' => array(),
            'label_text' => $this->title($name),
            'value' => ''
        );

        $allInputs = $this->inputs();
        $inputs = isset($allInputs[$name]) ? $allInputs[$name] : false;
        $options = ($inputs) ? array_merge($default_values, $inputs) : $default_values;
        $html_options = isset($inputs[$name]) ? array_merge($default_values, $inputs[$name]) : $default_values;
        foreach (array('value', 'type', 'choices', 'label', 'label_position', 'label_attributes', 'label_text') as $opts) {
            unset($html_options[$opts]);
        }

        $html_options = array_merge($html_options, $extraOptions);
        $type = $options['type'];
        try {
            $value = (isset($extraOptions['value'])) ? $options['value'] : $this->$name;
        } catch (Exception $e) {
            $value = '';
        }

        $output = '';
        switch ($type) {
            case 'radio':
                foreach ($options['choices'] as $k => $choice) {
                    $html_options['id'] .= "_$k";
                    $input = Form::radio($name, $k, ($value == $k), $html_options);
                    if ($options['label']) {
                        $label = Form::label($html_options['id'], $choice, $options['label_attributes']);
                        $output .= ($options['label_position'] == 'left') ? $label . $input : $input . $label;
                    } else $output .= $input;
                }
                break;
            case 'input':
                $output = Form::input($name, $value, $html_options);
                if ($options['label']) {
                    $label = Form::label($html_options['id'], $this->title($name), $options['label_attributes']);
                    $output = ($options['label_position'] == 'left') ? $label . $output : $output . $label;
                }
                break;
            case 'checkbox':
                $output = Form::checkbox($name, (bool)$value, $html_options);
                if ($options['label']) {
                    $label = Form::label($html_options['id'], $this->title($name), $options['label_attributes']);
                    $output = ($options['label_position'] == 'left') ? $label . $output : $output . $label;
                }
                break;
            case 'select':
                $output = Form::select($name, $options['choices'], $value, $html_options);
                if ($options['label']) {
                    $label = Form::label($html_options['id'], $this->title($name), $options['label_attributes']);
                    $output = ($options['label_position'] == 'left') ? $label . $output : $output . $label;
                }
                break;
            case 'textarea':
                $output = Form::textarea($name, $value, $html_options);
                break;
        }

        return $output;
    }

    public function save(Validation $validation = NULL)
    {
        if ($this->id == null && !$this->loaded()) {
            $this->added = date('U');
            $this->updated = date('U');
        } else $this->updated = date('U');

        parent::save($validation);
    }

    public function name_available($name)
    {
        // There are simpler ways to do this, but I will use ORM for a while
        return !ORM::factory('entity', array('name' => $name))->loaded();
    }

    public function ipaddress_available($ipaddress)
    {
        // There are simpler ways to do this, but I will use ORM for a while
        return !ORM::factory('entity', array('ipaddress' => $ipaddress))->loaded();
    }

    public function status()
    {
        switch ($this->status()) {
            case 0:
                return 'Inativo';
            case 1:
                return 'Ativo';
            case 2:
                return 'Alerta';
            case 3:
                return 'Erro';
            case -1:
                return 'Bloqueado';
            default:
                throw new Exception("Codigo de erro não reconhecido no Model_Entity");
        }
    }

    public function title($field)
    {
        return __($field);
    }

    public function label($field, $extraAttributes = array())
    {
        $allInputs = $this->inputs();
        $inputs = isset($allInputs[$field]) ? $allInputs[$field] : false;
        $title = (isset($inputs['label_text'])) ? $inputs['label_text'] : $this->title($field);
        return Form::label($field, $title, $extraAttributes);
    }
}
