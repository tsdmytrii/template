<?php
class Attribute_value_language extends DataMapper {

    public $table = 'attribute_value_languages';
    public $has_one = array('attribute_value');
    public static $ci;
    public $validation = array(
        array(
            'field' => 'id',
            'label' => 'id',
            'rules' => array('trim', 'numeric', 'max_length' => 10),
        ),
        array(
            'field' => 'attribute_value_id',
            'label' => 'id',
            'rules' => array('trim', 'numeric', 'max_length' => 10),
        ),
        array(
            'field' => 'name',
            'label' => 'id',
            'rules' => array('trim', 'max_length' => 100),
        ),
        array(
            'field' => 'description',
            'label' => 'id',
            'rules' => array('trim', 'max_length' => 255),
        )
    );

    function __construct($id = NULL) {
        parent::__construct($id);
        if (empty(self::$ci)) {
            self::$ci = &get_instance();
        }
    }

    public function set_attribute_value_language($attribute_value_id) {

        $attribute_value_language = new Attribute_value_language();

        if ($language_id = self::$ci->input->post('attribute_value_language_id'))
            $attribute_value_language->get_by_id($language_id);

        $attribute_value_language->language_id = self::$ci->input->post('language_id');
        $attribute_value_language->attribute_value_id = $attribute_value_id;
        $attribute_value_language->name = self::$ci->input->post('name');
        $attribute_value_language->description = self::$ci->input->post('description');

        if ($attribute_value_language->save()) {
            return $attribute_value_language->id;
        } else {
            return false;
        }
    }

    public function get_attribute_value_language($attribute_value_id, $fields = '*') {

        $attribute_value_lang = new Attribute_value_language();

        if ($fields)
            $attribute_value_lang->select($fields);

        $attribute_value_lang->where(array('attribute_value_id' => $attribute_value_id));

        $attribute_value_lang->get();

        $lang = FALSE;

        foreach ($attribute_value_lang as $c_l) {

            $language = new Language();
            $language->get_by_id($c_l->language_id);
            $lang[$language->iso_code] = $c_l->to_array();
        }

        return $lang;
    }

    public function delete_attribute_value_language($attribute_value_id) {

        $attribute_value_language = new Attribute_value_language();
        $attribute_value_language->get_by_attribute_value_id($attribute_value_id);
        $attribute_value_language->delete_all();
    }

}
