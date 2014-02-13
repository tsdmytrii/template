<?php

class Article_language extends DataMapper {

    public $table = 'article_languages';
    public $has_one = array('article');
    public static $ci;
    public $validation = array(
        array(
            'field' => 'description',
            'label' => 'Description',
            'rules' => array('trim', 'max_length' => 2000),
        ),
        array(
            'field' => 'description_btm',
            'label' => 'Description bottom',
            'rules' => array('trim', 'max_length' => 4000),
        ),
        array(
            'field' => 'key_words',
            'label' => 'Key words',
            'rules' => array('trim', 'max_length' => 1000),
        ),
        array(
            'field' => 'seo_description',
            'label' => 'Seo description',
            'rules' => array('trim', 'max_length' => 4000),
        ),
        array(
            'field' => 'seo_title',
            'label' => 'Seo description',
            'rules' => array('trim', 'max_length' => 4000),
        )
    );

    function __construct() {
        parent::__construct();
        if (empty(self::$ci)) {
            self::$ci = &get_instance();
        }
    }

    /*
     * SET functions
     */

    public function set_from_default($article_id, $language_id) {

        $article_language = new Article_language();

        $article_language->article_id = $article_id;
        $article_language->description = '';
        $article_language->description_btm = '';
        $article_language->description_search = '';
        $article_language->seo_title = 'New article list seo title';
        $article_language->key_words = 'New article list key words';
        $article_language->seo_description = 'New article list seo description';
        $article_language->language_id = $language_id;

        if ($article_language->save())
            return $article_language->to_array();
        else
            return false;
    }

    public function set_article_lang() {
        if (self::$ci->access->check_access(__FUNCTION__) !== true) {
            return array('result' => false, 'msg' => 403);
        }

        $result['result'] = false;
        $result['msg'] = false;

        $article_lang = new Article_language();

        if (self::$ci->input->post('article_lang_id') !== '')
            $article_lang->id = self::$ci->input->post('article_lang_id');

        $link_id = false;

        if (self::$ci->input->post('link')) {

            $article = new Article();
            $article->get_by_id(self::$ci->input->post('article_id'));

            $link = new Link();
            $link_id = $link->set_article_link($article->component_id, self::$ci->input->post('article_id'), self::$ci->input->post('language_id'));

        }

        $article_lang->language_id = self::$ci->input->post('language_id');
        $article_lang->article_id = self::$ci->input->post('article_id');
        $article_lang->description = self::$ci->input->post('description');
        $article_lang->description_btm = self::$ci->input->post('description_btm');
        $article_lang->description_search = strip_tags(self::$ci->input->post('description')) . ' ' . strip_tags(self::$ci->input->post('description_btm'));

        $article_lang->seo_description = self::$ci->input->post('seo_description');
        $article_lang->seo_title = self::$ci->input->post('seo_title');
        $article_lang->key_words = self::$ci->input->post('key_words');

        if ($article_lang->save()) {
            $result['result'] = array('article_lang_id' => $article_lang->id, 'link_id' => $link_id);
        } else {
            if ($article_lang->valid) {
                $result['msg'] = 'Article insert or update failure';
            } else {
                $result['msg'] = $article_lang->error->string;
            }
        }

        return $result;
    }

    /*
     * GET functions
     */

    public function get_article_lang($article_id) {

        $article_lang = new Article_language();
        $article_lang->get_by_article_id($article_id);

        $language = new Language();
        $lang_arr = $language->get_langs_as_key_id();

        $result = FALSE;

        foreach ($article_lang as $a_l) {

            $result[$lang_arr[$a_l->language_id]['iso_code']] = $a_l->to_array();

        }

        return $result;

    }

    /*
     * DELETE functions
     */

    public function delete_article_lang($article_lang_id) {
        $article_lang = new Article_language();

        $article_lang->get_by_article_id($article_lang_id);

        $article_lang->delete_all();
    }

}

?>
