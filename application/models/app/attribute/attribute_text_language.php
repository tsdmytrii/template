<?php
class Attribute_text_language extends DataMapper {

    public $table = 'attribute_text_languages';
    public $has_one = array('attribute_text');
    public static $ci;
    public $validation = array(
        array(
            'field' => 'id',
            'label' => 'id',
            'rules' => array('trim', 'numeric', 'max_length' => 10),
        ),
        array(
            'field' => 'attribute_texts_id',
            'label' => 'Attribute texts id',
            'rules' => array('trim', 'numeric', 'max_length' => 10),
        ),
        array(
            'field' => 'name',
            'label' => 'Name',
            'rules' => array('trim', 'max_length' => 10000),
        ),
        array(
            'field' => 'description',
            'label' => 'Description',
            'rules' => array('trim', 'max_length' => 10000),
        )
    );

    function __construct($id = NULL) {
        parent::__construct($id);
        if (empty(self::$ci)) {
            self::$ci = &get_instance();
        }
    }

    public function set_attribute_text_language($attribute_text_id) {

        $attribute_text_language = new Attribute_text_language();

        if ($language_id = self::$ci->input->post('attribute_text_language_id'))
            $attribute_text_language->get_by_id($language_id);

        $attribute_text_language->language_id = self::$ci->input->post('language_id');
        $attribute_text_language->attribute_text_id = $attribute_text_id;
        $attribute_text_language->name = self::$ci->input->post('name');
        $attribute_text_language->description = self::$ci->input->post('description');

        if ($attribute_text_language->save()) {
            return $attribute_text_language->id;
        } else {
            return false;
        }
    }

    public function get_attribute_text_language($attribute_text_id, $fields = '*') {

        $attribute_text_lang = new Attribute_text_language();

        if ($fields)
            $attribute_text_lang->select($fields);

        $attribute_text_lang->where(array('attribute_text_id' => $attribute_text_id));

        $attribute_text_lang->get();

        $lang = FALSE;

        foreach ($attribute_text_lang as $c_l) {

            $language = new Language();
            $language->get_by_id($c_l->language_id);
            $lang[$language->iso_code] = $c_l->to_array();
        }

        return $lang;
    }

    public function delete_attribute_text_language($attribute_text_id) {

        $attribute_text_language = new Attribute_text_language();
        $attribute_text_language->get_by_attribute_text_id($attribute_text_id);
        $attribute_text_language->delete_all();
    }

}
