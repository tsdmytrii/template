<?php

class Mini_block_language extends DataMapper {

    public $table = 'mini_block_languages';
    public $has_one = array('mini_block');
    public static $ci;
    public $validation = array(
        array(
            'field' => 'id',
            'label' => 'id',
            'rules' => array('trim', 'numeric', 'max_length' => 11),
        ),
        array(
            'field' => 'mini_block_id',
            'label' => 'Miniblock id',
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
            'field' => 'button_name',
            'label' => 'Button name',
            'rules' => array('trim', 'min_length' => 2, 'max_length' => 200),
        )
    );

    function __construct() {
        parent::__construct();
        if (empty(self::$ci)) {
            self::$ci = &get_instance();
        }
    }

    public function set_mini_block_language($mini_block_id) {

        $mini_block_lang = new Mini_block_language();

        if (self::$ci->input->post('mini_block_lang_id') !== ''&& self::$ci->input->post('mini_block_lang_id') !== 0)
            $mini_block_lang->get_by_id(self::$ci->input->post('mini_block_lang_id'));


        $mini_block_lang->language_id = self::$ci->input->post('language_id');
        $mini_block_lang->mini_block_id = $mini_block_id;
        $mini_block_lang->name = self::$ci->input->post('name');
        $mini_block_lang->button_name = self::$ci->input->post('button_name');

        if ($mini_block_lang->save())
            return $mini_block_lang->id;
        else
            return false;

    }

    public function get_mini_block_language($mini_block_id) {

        $mini_block_lang = new Mini_block_language();

        $mini_block_lang->get_by_mini_block_id($mini_block_id);

        $lang = FALSE;

        foreach ($mini_block_lang as $c_l) {
            $language = new Language();

            $language->get_by_id($c_l->language_id);

            $lang[$language->iso_code] = $c_l->to_array();
        }

        return $lang;
    }

    public function delete_mini_block_language($mini_block_id) {
        $mini_block_lang = new Mini_block_language();

        $mini_block_lang->get_by_mini_block_id($mini_block_id);

        return $mini_block_lang->delete_all();
    }

}

?>
