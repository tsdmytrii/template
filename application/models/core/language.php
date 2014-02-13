<?php

class Language extends DataMapper {

    public $table = 'languages';
    public $has_many = array('menu_item_language');
    public static $ci;
    public $validation = array(
        array(
            'field' => 'id',
            'label' => 'id',
            'rules' => array('trim', 'numeric', 'max_length' => 5),
        ),
        array(
            'field' => 'name',
            'label' => 'Name',
            'rules' => array('required', 'trim', 'max_length' => 200),
        ),
        array(
            'field' => 'iso_code',
            'label' => 'Iso code',
            'rules' => array('required', 'trim', 'max_length' => 3),
        ),
        array(
            'field' => 'position',
            'label' => 'Position',
            'rules' => array('required', 'trim', 'max_length' => 3),
        ),
        array(
            'field' => 'default',
            'label' => 'Default',
            'rules' => array('trim', 'max_length' => 1),
        ),
    );

    function __construct($id = NULL) {
        parent::__construct($id);
        if (empty(self::$ci)) {
            self::$ci = &get_instance();
        }
    }

    public function get_language($language_id = false, $check_access = true) {
        if ($check_access && self::$ci->access->check_access(__FUNCTION__) == false)
            return array('result' => false, 'msg' => 403);

        $result['result'] = false;
        $result['msg'] = false;

        $language = new Language();

        $language_id = self::$ci->input->post('language_id') ? self::$ci->input->post('language_id') : $language_id;

        if ($language_id) {

            $language->get_by_id($language_id);

            if (!$language->exists()) {
                $result['msg'] = 'No such language id';
            }

            $result['result'] = $language->exists() ? $language->to_array() : false;
        }
        else
            $result['msg'] = 'No such language id';

        return $result;
    }

    public function get_all_languages($check_access = true) {
        if ($check_access && self::$ci->access->check_access(__FUNCTION__) == false)
            return array('result' => false, 'msg' => 403);

        $result['result'] = false;
        $result['msg'] = false;

        $language = new Language();

        $language->order_by('position')->get();

        if ($language->exists()) {
            $result['result'] = $language->all_to_array();
        } else {
            $result['msg'] = 'There are no languages';
        }

        return $result;

    }

    public function set_language() {
        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return array('result' => false, 'msg' => 403);

        $result['result'] = false;
        $result['msg'] = false;

        $language = new Language();

        if (self::$ci->input->post('id'))
            $language->get_by_id (self::$ci->input->post('id'));

        if (self::$ci->input->post('default') && intval(self::$ci->input->post('default')) == 1) {

            $lang = new Language();

            $lang->get();

            foreach ($lang as $l) {

                $l->default = 0;

                if ( ! $l->save()) {
                    break;
                }

            }

        }

        $language->name = self::$ci->input->post('name');
        $language->iso_code = self::$ci->input->post('iso_code');
        $language->position = self::$ci->input->post('position');
        $language->default = self::$ci->input->post('default');

        if ($language->save()) {
            $result['result'] = $language->id;
        } else {
            if ($language->valid) {
                $result['msg'] = 'insert or update failure';
            } else {
                $result['msg'] = $language->error->string;
            }
        }

        return $result;
    }

    public function delete_language() {
        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return array('result' => false, 'msg' => 403);

        $result['result'] = false;
        $result['msg'] = false;

        $language_id = self::$ci->input->post('language_id') ? self::$ci->input->post('language_id') : false;

        if ($language_id) {

            $language = new Language();

            $language->get_by_id($language_id);

            $result['result'] = $language->delete();

            if ($result['result'] == false) {
                $result['msg'] = 'error deliting lang';
            }

        } else {
            $result['msg'] = 'no such language find';
        }

        return $result;

    }

    public function get_default_lang() {

        return $this->get_by_default('1')->get();

    }

    public function get_langs_as_key_id() {

        $lang_arr = array();

        $language = new Language();

        $language->get();

        foreach ($language as $l) {

            $lang_arr[$l->id] = $l->to_array();

        }

        return $lang_arr;

    }

}

?>