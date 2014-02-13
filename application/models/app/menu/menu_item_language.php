<?php

class Menu_item_language extends DataMapper {

    public $table = 'menu_item_languages';
    public $has_one = array('menu_item', 'language');
    public static $ci;
    public $validation = array(
        array(
            'field' => 'language_id',
            'label' => 'Language id',
            'rules' => array('trim', 'required', 'max_length' => 3),
        ),
        array(
            'field' => 'menu_item_id',
            'label' => 'Menu item id',
            'rules' => array('trim', 'required', 'max_length' => 3),
        ),
        array(
            'field' => 'value',
            'label' => 'Value',
            'rules' => array('trim', 'min_length' => 3, 'max_length' => 100),
        )
    );

    function __construct() {
        parent::__construct();
        if (empty(self::$ci)) {
            self::$ci = &get_instance();
        }
    }

    public function set_menu_item_language($menu_item_id) {

        $result['result'] = false;
        $result['msg'] = false;

        $menu_items_language = new Menu_item_language();

        if (self::$ci->input->post('menu_item_lang_id'))
            $menu_items_language->get_by_id(self::$ci->input->post('menu_item_lang_id'));

        $menu_items_language->language_id = self::$ci->input->post('language_id');
        $menu_items_language->menu_item_id = $menu_item_id;
        $menu_items_language->value = self::$ci->input->post('value');

        if ($menu_items_language->save()) {
            $result['result'] = $menu_items_language->id;
        } else {
            if ($menu_items_language->valid) {
                $result['msg'] = 'insert or update failure';
            } else {
                $result['msg'] = $menu_items_language->error->string;
            }
        }

        return $result;

    }

    public function get_menu_item_language($menu_item_id, $fields = "*", $all_langs = true) {

        $menu_items_language = new Menu_item_language();

        if ($fields)
            $menu_items_language->select($fields);

        $language = new Language();

        if ($all_langs == false) {
            $language->get_default_lang();

            $menu_items_language->where('language_id', $language->id);
        }

        $menu_items_language->get_by_menu_item_id($menu_item_id);

        $result['result'] = false;
        $result['msg'] = false;

        if ($menu_items_language->exists()) {

            if ($all_langs) {

                $lang_arr = $language->get_langs_as_key_id();

                foreach ($menu_items_language as $m) {
                    $result['result'][$lang_arr[$m->language_id]['iso_code']] = $m->to_array();
                }

            } else {

                $result['result'] = $menu_items_language->to_array();

            }

        } else {
            $result['msg'] = 'There are no langs in menu item # '.$menu_item_id;
        }

        return $result;
    }

    public function delete_menu_item_language($menu_item_id) {
        $menu_item_lang = new Menu_item_language();

        $menu_item_lang->get_by_menu_item_id($menu_item_id);

        return $menu_item_lang->delete_all();
    }

}

?>
