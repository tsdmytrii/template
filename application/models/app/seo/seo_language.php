<?php

class Seo_language extends DataMapper {

    public $table = 'seo_languages';
    public $has_one = array('seo');
    public static $ci;
    public $validation = array(
        array(
            'field' => 'id',
            'label' => 'id',
            'rules' => array('trim', 'numeric', 'max_length' => 5),
        ),
        array(
            'field' => 'seo_id',
            'label' => 'Seo id',
            'rules' => array('trim', 'numeric', 'max_length' => 5),
        ),
        array(
            'field' => 'title',
            'label' => 'Title',
            'rules' => array('required', 'trim', 'max_length' => 1000),
        ),
        array(
            'field' => 'description',
            'label' => 'Description',
            'rules' => array('required', 'trim', 'max_length' => 5000),
        ),
        array(
            'field' => 'key_words',
            'label' => 'Key words',
            'rules' => array('required', 'trim', 'max_length' => 4000),
        )
    );

    function __construct() {
        parent::__construct();
        if (empty(self::$ci)) {
            self::$ci = &get_instance();
        }
    }

    public function set_seo_lang($seo_id) {

        $result['result'] = false;
        $result['msg'] = false;

        $seo_lang = new Seo_language();

        if (self::$ci->input->post('seo_lang_id') !== '')
            $seo_lang->get_by_id(self::$ci->input->post('seo_lang_id'));

        $seo_lang->language_id = self::$ci->input->post('language_id');
        $seo_lang->seo_id = $seo_id;
        $seo_lang->title = self::$ci->input->post('title');
        $seo_lang->description = self::$ci->input->post('description');
        $seo_lang->key_words = self::$ci->input->post('key_words');

        if ($seo_lang->save())
            $result['result'] = $seo_lang->id;
        else {
            if ($seo_lang->valid) {
                $result['msg'] = 'Seo insert or update failure';
            } else {
                $result['msg'] = $seo_lang->error->string;
            }
        }

        return $result;
    }

    public function get_seo_lang($seo_id) {

        $seo_lang = new Seo_language();

        $seo_lang->get_by_seo_id($seo_id);

        $language = new Language();
        $lang_arr = $language->get_langs_as_key_id();

        $result = FALSE;

        foreach ($seo_lang as $l) {

            $result[$lang_arr[$l->language_id]['iso_code']] = $l->to_array();

        }

        return $result;
    }

}

?>
