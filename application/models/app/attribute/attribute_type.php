<?php

class Attribute_type extends DataMapper {

    public $table = 'attribute_types';

    public $has_many = array('attribute');

    public static $ci;
    public $validation = array(
        array(
            'field' => 'id',
            'label' => 'id',
            'rules' => array('trim', 'numeric', 'max_length' => 10),
        ),
        array(
            'field' => 'name',
            'label' => 'Attribute ID',
            'rules' => array('trim', 'numeric', 'max_length' => 10),
        )
    );

    function __construct($id = NULL) {
        parent::__construct($id);
        if (empty(self::$ci)) {
            self::$ci = &get_instance();
        }
    }

    public function set_attribute_value() {

        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return 403;

        $attribute_value = new Attribute_value();

        if ($attribute_value_id = self::$ci->input->post('attribute_value_id'))
            $attribute_value->get_by_id($attribute_value_id);

        $attribute_value->attribute_id = self::$ci->input->post('attribute_id');

        if ($attribute_value->save()) {

            $attribute_value_language = new Attribute_value_language();
            $language = $attribute_value_language->set_attribute_value_language($attribute_value->id);

            if ($language) {
                return $attribute_value->id;
            } else {
                return false;
            }

        } else {
            return false;
        }
    }

    public function get_all_attribute_value($attribute_id = false) {

        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return 403;

        $attribute_id_post = self::$ci->input->post('attribute_id');
        $attribute_id = $attribute_id_post ? $attribute_id_post : $attribute_id;

        $attribute_value = new Attribute_value();
        $attribute_value->where('attribute_id', $attribute_id)->get()->all_to_array();

        $result = false;

        foreach ($attribute_value as $key => $attribute_value_unit) {

            $attribute_value_language = new Attribute_value_language();

            $result[$key] = $attribute_value_unit->to_array();
            $result[$key]['lang'] = $attribute_value_language->get_attribute_value_language($attribute_value_unit->id);
        }

        return $result;
    }

    public function delete_attribute_value() {

        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return 403;

        $attribute_value = new Attribute_value();
        $attribute_value_id = self::$ci->input->post('attribute_value_id');
        $attribute_value->get_by_id($attribute_value_id);

        $attribute_value_language = new Attribute_value_language();
        $attribute_value_language->get_by_related($attribute_value);

        if ($attribute_value_language->exists()){
            $attribute_value->delete($attribute_value_language->all);
        }

        return $attribute_value->delete();
    }

}
