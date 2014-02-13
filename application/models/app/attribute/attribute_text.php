<?php
class Attribute_text extends DataMapper {

    public $table = 'attribute_texts';

    public $has_many = array('category', 'attribute_text_language', 'product', 'attribute_text_language');

    public static $ci;
    public $validation = array(
        array(
            'field' => 'id',
            'label' => 'id',
            'rules' => array('trim', 'numeric', 'max_length' => 10),
        ),
        array(
            'field' => 'attribute_id',
            'label' => 'Attribute ID',
            'rules' => array('trim', 'numeric', 'max_length' => 10),
        ),
        array(
            'field' => 'category_id',
            'label' => 'Category ID',
            'rules' => array('trim', 'numeric', 'max_length' => 10),
        ),
        array(
            'field' => 'product_id',
            'label' => 'Product ID',
            'rules' => array('trim', 'numeric', 'max_length' => 10),
        )
        
    );

    function __construct($id = NULL) {
        parent::__construct($id);
        if (empty(self::$ci)) {
            self::$ci = &get_instance();
        }
    }

    public function set_attribute_text() {

        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return 403;

        $attribute_text = new Attribute_text();

        if ($attribute_text_id = self::$ci->input->post('attribute_text_id'))
            
            $attribute_text->get_by_id($attribute_text_id);

        $attribute_text->attribute_id = self::$ci->input->post('attribute_id');

        if ($attribute_text->save()) {

            $attribute_text_language = new Attribute_text_language();
            $language = $attribute_text_language->set_attribute_text_language($attribute_text->id);

            if ($language) {
                return $attribute_text->id;
            } else {
                return false;
            }

        } else {
            return false;
        }
    }

    public function get_all_attribute_text($attribute_id = false) {

        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return 403;

        $attribute_id_post = self::$ci->input->post('attribute_id');
        $attribute_id = $attribute_id_post ? $attribute_id_post : $attribute_id;

        $attribute_text = new Attribute_text();
        $attribute_text->where('attribute_id', $attribute_id)->get()->all_to_array();

        $result = false;

        foreach ($attribute_text as $key => $attribute_text_unit) {

            $attribute_text_language = new Attribute_text_language();

            $result[$key] = $attribute_text_unit->to_array();
            $result[$key]['lang'] = $attribute_text_language->get_attribute_text_language($attribute_text_unit->id);
        }

        return $result;
    }

    public function delete_attribute_text() {

        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return 403;

        $attribute_text = new Attribute_text();
        $attribute_text_id = self::$ci->input->post('attribute_text_id');
        $attribute_text->get_by_id($attribute_text_id);

        $attribute_text_language = new Attribute_text_language();
        $attribute_text_language->get_by_related($attribute_text);

        if ($attribute_text_language->exists()){
            $attribute_text->delete($attribute_text_language->all);
        }

        return $attribute_text->delete();
    }
    
}
