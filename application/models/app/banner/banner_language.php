<?php

class Banner_language extends DataMapper {

    public $table = 'banner_languages';
    public $has_one = array('banner');
    public static $ci;
    public $validation = array(
        array(
            'field' => 'title',
            'label' => 'Title',
            'rules' => array('trim', 'required', 'max_length' => 200),
        ),
        array(
            'field' => 'description',
            'label' => 'Description',
            'rules' => array('trim', 'max_length' => 1000),
        )
    );

    function __construct() {
        parent::__construct();
        if (empty(self::$ci)) {
            self::$ci = &get_instance();
        }
    }

    public function set_banner_lang($banner_id) {

        $result['result'] = false;
        $result['msg'] = false;

        $banner_lang = new Banner_language();

        if (self::$ci->input->post('banner_lang_id') !== ''){
            $banner_lang->get_by_id(self::$ci->input->post('banner_lang_id'));
        }

        $banner_lang->language_id = self::$ci->input->post('language_id');
        $banner_lang->banner_id = $banner_id;
        $banner_lang->title = self::$ci->input->post('title');
        $banner_lang->description = self::$ci->input->post('description');

        if ($banner_lang->save())
            $result['result'] = $banner_lang->id;
        else {
            if ($banner_lang->valid) {
                $result['msg'] = 'Banner insert or update failure';
            } else {
                $result['msg'] = $banner_lang->error->string;
            }
        }
        return $result;
    }

    public function get_banner_lang($banner_id) {

        $banner_lang = new Banner_language();

        $banner_lang->get_by_banner_id($banner_id);

        $language = new Language();
        $lang_arr = $language->get_langs_as_key_id();

        $result = FALSE;

        foreach ($banner_lang as $b_l) {

            $result[$lang_arr[$b_l->language_id]['iso_code']] = $b_l->to_array();

        }

        return $result;
    }

    public function delete_banner_lang($banner_lang_id) {
        $banner_lang = new Banner_language();

        $banner_lang->get_by_banner_id($banner_lang_id);

        return $banner_lang->delete_all();
    }

}

?>
