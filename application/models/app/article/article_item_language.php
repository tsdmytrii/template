<?php

class Article_item_language extends DataMapper {

    public $table = 'article_item_languages';
    public $has_one = array('article_item');
    public static $ci;
    public $validation = array(
        array(
            'field' => 'title',
            'label' => 'Title',
            'rules' => array('required', 'trim', 'max_length' => 500),
        ),
        array(
            'field' => 'author',
            'label' => 'Author',
            'rules' => array('required', 'trim', 'max_length' => 100),
        ),
        array(
            'field' => 'description',
            'label' => 'Description',
            'rules' => array('required', 'trim', 'max_length' => 12000),
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
            'label' => 'Seo title',
            'rules' => array('trim', 'max_length' => 4000),
        )
    );

    function __construct() {
        parent::__construct();
        if (empty(self::$ci)) {
            self::$ci = &get_instance();
        }
    }

    public function set_article_item_lang($article_item_id) {

        $result['result'] = false;
        $result['msg'] = false;

        $article_item_lang = new Article_item_language();

        if (self::$ci->input->post('article_lang_id') !== '')
            $article_item_lang->get_by_id(self::$ci->input->post('article_lang_id'));

        $article_item_lang->language_id = self::$ci->input->post('language_id');
        $article_item_lang->article_item_id = $article_item_id;
        $article_item_lang->title = self::$ci->input->post('title');
        $article_item_lang->author = self::$ci->input->post('author');
        $article_item_lang->description = self::$ci->input->post('description');
        $article_item_lang->description_search = strip_tags(self::$ci->input->post('description'));
        $article_item_lang->seo_description = self::$ci->input->post('seo_description');
        $article_item_lang->seo_title = self::$ci->input->post('seo_title');
        $article_item_lang->key_words = self::$ci->input->post('key_words');

        if ($article_item_lang->save())
            $result['result'] = $article_item_lang->id;
        else {
            if ($article_item_lang->valid) {
                $result['msg'] = 'Article item insert or update failure';
            } else {
                $result['msg'] = $article_item_lang->error->string;
            }
        }

        return $result;
    }

    public function get_article_item_lang($article_item_id) {

        $article_item_lang = new Article_item_language();
        $article_item_lang->get_by_article_item_id($article_item_id);

        $language = new Language();
        $lang_arr = $language->get_langs_as_key_id();

        $result = FALSE;

        foreach ($article_item_lang as $a_l) {

            $result[$lang_arr[$a_l->language_id]['iso_code']] = $a_l->to_array();

        }

        return $result;
    }

    public function delete_article_item_lang($article_item_lang_id) {
        $article_item_lang = new Article_item_language();

        $article_item_lang->get_by_article_item_id($article_item_lang_id);

        $result = false;

        if ($article_item_lang->delete_all()) {
            $result = true;
        } else {
            $result = 'Error deleting article item language';
        }

        return $result;
    }

}

?>
