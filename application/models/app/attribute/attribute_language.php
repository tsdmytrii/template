<?php

class Attribute_language extends DataMapper {

    public $table = 'attribute_languages';
    public $has_one = array('attribute');
    public static $ci;
    public $validation = array(
        array(
            'field' => 'language_id',
            'label' => 'Language ID',
            'rules' => array('trim', 'required', 'max_length' => 2)
        ),
        array(
            'field' => 'name',
            'label' => 'Name',
            'rules' => array('trim', 'required', 'max_length' => 100)
        ),
        array(
            'field' => 'attribute_id',
            'label' => 'Attribute ID',
            'rules' => array('trim', 'min_length' => 1, 'max_length' => 10),
        )
    );

    function __construct() {
        parent::__construct();
        if (empty(self::$ci)) {
            self::$ci = &get_instance();
        }
    }

    public function set_attribute_language($attribute_id) {

        $attribute_language = new Attribute_language();

        if ($language_id = self::$ci->input->post('attribute_language_id'))
            $attribute_language->get_by_id($language_id);

        $attribute_language->language_id = self::$ci->input->post('language_id');
        $attribute_language->attribute_id = $attribute_id;
        $attribute_language->name = self::$ci->input->post('name');

        if ($attribute_language->save()) {
            return $attribute_language->id;
        } else {
            return false;
        }
    }

    public function get_attribute_language($attribute_id, $fields = '*') {

        $attribute_lang = new Attribute_language();

        if ($fields)
            $attribute_lang->select($fields);

        $attribute_lang->where(array('attribute_id' => $attribute_id));

        $attribute_lang->get();

        $lang = FALSE;

        foreach ($attribute_lang as $c_l) {

            $language = new Language();

            $language->get_by_id($c_l->language_id);

            $lang[$language->iso_code] = $c_l->to_array();
        }

        return $lang;
    }

    public function delete_attribute_lang($attribute_id) {
        $attribute_lang = new Attribute_language();

        $attribute_lang->get_by_attribute_id($attribute_id);

        $attribute_lang->delete_all();
    }

    public function category_autocomplete_attribute() {
        $attr_lang = new Attribute_language();
        $query = self::$ci->input->post('query');

        $attr_lang->like('name', $query)->get();

        foreach ($attr_lang as $a_key => $a) {
            $result = "";
            $suggestions[] = $a->name;

            $attr_val = new Attribute_value();

            $atribute_id = $a->attribute_id;
           
            $attr_val->get_by_attribute_id($atribute_id);
            if ($attr_val) {
                foreach ($attr_val as $key => $a_val) {
                    $a_val_lang = new Attribute_value_language();
                    $a_val_lang->where('attribute_value_id', $a_val->id)->get();
                    $result[$key]['name'] = $a_val_lang->name;
                    $result[$key]['id'] = $a_val_lang->attribute_value_id;
                }
            }

            $data[] = array(
                'attribute_id' => $atribute_id,
                'attr_val' => $result
            );
        }
//        var_dump($a->atribute_id);

        return array('query' => $query, 'suggestions' => $suggestions, 'data' => $data);
    }

}
