<?php

class Attribute extends DataMapper {

    public $db_params = 'default';
    public $table = 'attributes';
    public $has_many = array('attribute_language', 'category', 'attribute_type');
    public static $ci;
    public $validation = array(
        array(
            'field' => 'id',
            'label' => 'Id',
            'rules' => array('trim', 'numeric', 'max_length' => 10),
        ),
        array(
            'field' => 'measurement_type',
            'label' => 'Measurement Type',
            'rules' => array('trim', 'min_length' => 1, 'max_length' => 1),
        ),
        array(
            'field' => 'strict',
            'label' => 'Strict',
            'rules' => array('trim', 'max_length' => 1),
        )
    );

    function __construct($id = NULL) {
        parent::__construct($id);
        if (empty(self::$ci)) {
            self::$ci = &get_instance();
        }
    }

    public function set_attribute() {

        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return 403;

        $attribute = new Attribute();

        if ($attribute_id = self::$ci->input->post('attribute_id'))
            $attribute->get_by_id($attribute_id);

        $attribute->measurement_type = self::$ci->input->post('measurement_type');
        if ($strict = self::$ci->input->post('strict')) {
            $attribute->strict = $strict;
        }

        $influence_type_array = self::$ci->input->post('influence_type');

        foreach ($influence_type_array as $value) {
            $attribute_type = new Attribute_type();
            $attribute_type->where('id', $value)->get();
            $result = $attribute->save($attribute_type);
        }

        if ($attribute->save()) {

            $attribute_language = new Attribute_language();

            $language = $attribute_language->set_attribute_language($attribute->id);

            if ($language) {
                return $attribute->id;
            } else {
                return false;
            }

        } else {
            return false;
        }
    }

    public function get_attribute($attribute_id = false) {
        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return 403;

        $input_id = self::$ci->input->post('attribute_id');
        $attribute_id = $input_id ? $input_id : $attribute_id;

        if ($attribute_id) {

            $attribute = new Attribute();
            $attribute_language = new Attribute_language();
            $attribute_type = new Attribute_type();

            $result = $attribute->get_by_id($attribute_id)->to_array();
            $result['lang'] = $attribute_language->get_attribute_language($attribute_id);
            $result['attribute_type'] = $attribute->attribute_type->get()->all_to_array();

            return $result;

        } else
            return false;
    }

    public function get_all_attribute() {
        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return 403;

        $attribute = new Attribute();
        $result = false;
        $attribute->get();
        foreach ($attribute as $key => $attribute_unit) {

            $attribute_language = new Attribute_language();
            $attribute_language->get_by_related($attribute_unit);

            $attribute_type = new Attribute_type();
            $attribute_type->get_by_related($attribute_unit);

            $result[$key]['attribute_languages'] = $attribute_language->all_to_array(array('name'));
            $result[$key]['attribute_types'] = $attribute_type->all_to_array();
            $result[$key]['attribute'] = $attribute_unit->to_array();
        }

        return $result;
    }

    public function delete_attribute() {
        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return 403;

        $attribute_id = self::$ci->input->post('attribute_id');

        $attribute = new Attribute();
        $attribute->get_by_id($attribute_id);

        $attribute_language = new Attribute_language();
        $attribute_language->where('attribute_id', $attribute->id)->get();
        $attribute_language->delete();

        $attribute_type = new Attribute_type();

        $attribute->delete($attribute_type->all);

        return $attribute->delete() ? array('attribute_id' => $attribute_id) : false;
    }

}