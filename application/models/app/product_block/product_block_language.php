<?php

class Product_block_language extends DataMapper {

    public $table = 'product_block_languages';
    public $has_one = array('product_block');
    public static $ci;
    public $validation = array(
        array(
            'field' => 'id',
            'label' => 'id',
            'rules' => array('trim', 'numeric', 'max_length' => 11),
        ),
        array(
            'field' => 'product_block_id',
            'label' => 'Productblock id',
            'rules' => array('trim', 'numeric', 'max_length' => 11),
        ),
        array(
            'field' => 'language_id',
            'label' => 'Language id',
            'rules' => array('trim', 'numeric', 'max_length' => 11),
        ),
        array(
            'field' => 'name',
            'label' => 'Name',
            'rules' => array('trim', 'min_length' => 2, 'max_length' => 200),
        ),
        array(
            'field' => 'description',
            'label' => 'Description',
            'rules' => array('trim', 'min_length' => 2, 'max_length' => 200),
        )
    );

    function __construct() {
        parent::__construct();
        if (empty(self::$ci)) {
            self::$ci = &get_instance();
        }
    }

    public function set_product_block_language($product_block_id) {

        $product_block_lang = new Product_block_language();

        if (self::$ci->input->post('product_block_lang_id') !== ''&& self::$ci->input->post('product_block_lang_id') !== 0)
            $product_block_lang->get_by_id(self::$ci->input->post('product_block_lang_id'));


        $product_block_lang->language_id = self::$ci->input->post('language_id');
        $product_block_lang->product_block_id = $product_block_id;
        $product_block_lang->name = self::$ci->input->post('name');
        $product_block_lang->description = self::$ci->input->post('description');

        if ($product_block_lang->save())
            return $product_block_lang->id;
        else
            return false;

    }

    public function get_product_block_language($product_block_id, $fields = '*') {

        $product_block_lang = new Product_block_language();

        $product_block_lang->select($fields);

        $product_block_lang->get_by_product_block_id($product_block_id);

        $lang = FALSE;

        foreach ($product_block_lang as $c_l) {
            $language = new Language();

            $language->get_by_id($c_l->language_id);

            $lang[$language->iso_code] = $c_l->to_array();
        }

        return $lang;
    }

    public function delete_product_block_language($product_block_id) {
        $product_block_lang = new Product_block_language();

        $product_block_lang->get_by_product_block_id($product_block_id);

        return $product_block_lang->delete_all();
    }

}

?>
