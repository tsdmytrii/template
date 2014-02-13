<?php

class Seo extends DataMapper {

    public $table = 'seoes';
    public $has_many = array('seo_language');
    public static $ci;
    public $validation = array(
        array(
            'field' => 'id',
            'label' => 'id',
            'rules' => array('trim', 'numeric', 'max_length' => 5),
        ),
        array(
            'field' => 'date',
            'label' => 'Date',
            'rules' => array('trim', 'max_length' => 19),
        )
    );

    function __construct($id = NULL) {
        parent::__construct($id);
        if (empty(self::$ci)) {
            self::$ci = &get_instance();
        }
    }

    public function set_from_default() {
        return 0;
    }

    public function set_seo() {

        if (!self::$ci->access->check_access(__FUNCTION__))
            return array('result' => false, 'msg' => 403);

        $result['result'] = false;
        $result['msg'] = false;

        self::$ci->config->load('my_config');

        $seo = new Seo();

        if (self::$ci->input->post('id'))
            $seo->get_by_id(self::$ci->input->post('id'));

        $seo->date = date(self::$ci->config->item('date') . ' ' . self::$ci->config->item('time'));


        if ($seo->save()) {

            $seo_lang = new Seo_language();
            $lang_res = $seo_lang->set_seo_lang($seo->id);

            if ($lang_res['msg'] !== false) {
                $result['msg'] = $lang_res['msg'];
            }

            $result['result'] = $lang_res['result'];

        } else {
            $result['msg'] = 'Error saving seo';
        }

        return $result;

    }

    public function get_seo() {

        if (!self::$ci->access->check_access(__FUNCTION__))
            return array('result' => false, 'msg' => 403);

        $result['result'] = false;
        $result['msg'] = false;

        $seo = new Seo();
        $seo->get();

        if ($seo->exists()) {
            $seo_lang = new Seo_language();

            $result['result'] = $seo->to_array();
            $result['result']['lang'] = $seo_lang->get_seo_lang($seo->id);
        } else {
            $result['msg'] = 'There are no seo entry.';
        }

        return $result;

    }

}

?>