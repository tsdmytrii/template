<?php

class Article_item extends DataMapper {

    public $table = 'article_items';
    public $has_one = array('article');
    public $has_many = array('article_item_language');
    public static $ci;
    public $validation = array(
        array(
            'field' => 'id',
            'label' => 'id',
            'rules' => array('trim', 'numeric', 'max_length' => 11),
        ),
        array(
            'field' => 'article_id',
            'label' => 'Article id',
            'rules' => array('trim', 'required', 'numeric', 'max_length' => 11),
        ),
        array(
            'field' => 'date_time',
            'label' => 'Date time',
            'rules' => array('trim', 'max_length' => 19),
        ),
        array(
            'field' => 'display',
            'label' => 'Display',
            'rules' => array('trim', 'max_length' => 1),
        ),
        array(
            'field' => 'main',
            'label' => 'Main',
            'rules' => array('trim', 'max_length' => 1),
        ),
    );

    function __construct($id = NULL) {
        parent::__construct($id);
        if (empty(self::$ci)) {
            self::$ci = &get_instance();
        }
    }

    /*
     * ------------------------------------------------------------------------- Admin functions
     */

    /*
     * Get functions
     */

    public function get_all_articles($article_id = false) {

        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return array('result' => false, 'msg' => 403);

        $result['result'] = false;
        $result['msg'] = false;

        $article_id = self::$ci->input->post('article_id') ? self::$ci->input->post('article_id') : $article_id;

        if ($article_id != false) {

            $article_item = new Article_item();

            $article_item->order_by('date_time', 'desc');

            if (self::$ci->input->post('limit'))
                $article_item->limit(self::$ci->input->post('limit'));

            if (self::$ci->input->post('offset'))
                $article_item->offset(self::$ci->input->post('offset'));

            $article_item->get_by_article_id($article_id);

            if ($article_item->exists()) {

                $article = false;

                foreach ($article_item as $key => $a) {

                    $article_item_lang = new Article_item_language();

                    $link = new Link();

                    $article[$key] = $a->to_array();

                    $article[$key]['lang'] = $article_item_lang->get_article_item_lang($a->id);

                    $article[$key]['links'] = $link->get_link($a->component_id);
                }

                $article_item->clear();

                $result['result']['data'] = $article;
                $result['result']['articleItemQuant'] = $article_item->get_by_article_id($article_id)->result_count();

                return $result;

            } else {
                $result['msg'] = 'There no such article.';
            }

        } else
             $result['msg'] = 'There no such article. With no article id';

         return $result;
    }

    public function get_article_item($article_item_id = false, $strip_tags = false) {

        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return array('result' => false, 'msg' => 403);

        $result['result'] = false;
        $result['msg'] = false;

        $article_item_id = self::$ci->input->post('article_item_id') ? self::$ci->input->post('article_item_id') : $article_item_id;

        $article_item = new Article_item();
        $article_item->get_by_id($article_item_id);

        if ($article_item->exists()) {
            $article_item_lang = new Article_item_language();

            $link = new Link();

            $result['result'] = $article_item->to_array();
            $result['result']['lang'] = $article_item_lang->get_article_item_lang($article_item_id, $strip_tags);;
            $result['result']['links'] = $link->get_link($article_item->component_id);

        } else {
            $result['msg'] = 'There is no article';
        }

        return $result;
    }

    /*
     * Set functions
     */

    public function set_article_item() {
        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return array('result' => false, 'msg' => 403);

        $result['result'] = false;
        $result['msg'] = false;

        $article_item = new Article_item();

        if (self::$ci->input->post('id'))
            $article_item->get_by_id(self::$ci->input->post('id'));

        $main = self::$ci->input->post('main') ? self::$ci->input->post('main') : 0;

        $article_item->article_id = self::$ci->input->post('article_id');
        $article_item->date_time = self::$ci->input->post('date_time');
        $article_item->display = self::$ci->input->post('display') ? self::$ci->input->post('display') : 0;
        $article_item->main = $main;

        if ($article_item->save()) {

            if (!self::$ci->input->post('id')) {

                $component_type = new Component_type();
                $component_type->get_by_name('articleitem');

                $component = new Component();

                if ($component_type->exists()) {

                    $component_id = $component->create_component($component_type->id, $article_item->id, 0, self::$ci->input->post('title'));

                    $article_item->component_id = $component_id;

                    $article_item->save();

                } else {
                    $result['msg'] = "articleitem component type didn't exist.";
                }

            }

            $link = new Link();
            $link_id = $link->set_article_item_link($article_item->component_id, $article_item->id, self::$ci->input->post('language_id'));

            $article_item_lang = new Article_item_language();
            $langs = $article_item_lang->set_article_item_lang($article_item->id);

            if ($langs['result']) {
                $result['result'] = array('article_item_id' => $article_item->id, 'article_item_lang_id' => $langs['result'], 'link_id' => $link_id);
            } else {
                $result['msg'] = $langs['msg'];
            }

        } else {
            if ($article_item->valid) {
                $result['msg'] = 'Article item insert or update failure';
            } else {
                $result['msg'] = $article_item->error->string;
            }
        }

        return $result;
    }

    public function delete_article_item($a_i_id = false) {

        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return array('result' => false, 'msg' => 403);

        $result['result'] = false;
        $result['msg'] = '';

        $article_item_id = self::$ci->input->post('article_item_id') ? self::$ci->input->post('article_item_id') : $a_i_id;

        $article_item = new Article_item();

        $article_item->get_by_id($article_item_id);

        $article_item_lang = new Article_item_language();
        $delete_lang = $article_item_lang->delete_article_item_lang($article_item_id);
        if ($delete_lang !== true) {
            $result['msg'] .= $delete_lang;
        }

        $link = new Link();
        $delete_link = $link->delete_link($article_item->component_id);

        if ($delete_link === false) {
            $result['msg'] .= 'Error deleting article item link.';
        }

        if ($article_item->delete()) {
            $result['result'] = true;
        } else {
            $result['msg'] .= 'Error deleting article item.';
        }

        if ($result['msg'] == '') {
            $result['msg'] = false;
        }

        return $result;
    }

    public function delete_article_item_by_article($article_id) {
        $article_item = new Article_item();

        $article_item->get_by_article_id($article_id);

        foreach ($article_item as $a) {
            $a->delete_article_item($a->id);
        }

        return true;
    }

    /*
     * ------------------------------------------------------------------------- User functions
     */

    public function get_article_item_for_user($article_item_id = false, $strip_tags = false) {
        $article_item_id = self::$ci->input->post('article_item_id') ? self::$ci->input->post('article_item_id') : $article_item_id;
        $article_item = new Article_item();
        $article_item->get_by_id($article_item_id);

        $article_item_lang = new Article_item_language();
        $article_item_link = new Link();

        $result = $article_item->to_array();
        $result['lang'] = $article_item_lang->get_article_item_lang($article_item_id, $strip_tags);
        $result['link'] = $article_item_link->get_article_link_for_user($article_item_id);

        return $result;
    }

    public function get_article_item_by_behavior($article_id, $component_id, $limit) {
        $article_item = new Article_item();

        $where = array('article_id' => $article_id);

        $article_item->where($where)->order_by('date', 'desc')->order_by('time', 'desc')->limit($limit)->get();

        $result = false;

        foreach ($article_item as $key => $a) {

            $article_item_language = new Article_item_language();
            $article_item_link = new Link();
            $menu_item = new Menu_item();

            $where = array('component_id' => $component_id, 'default_item' => 1);
            $menu_item->where($where)->get();
            $menu_item_id = 0;
            if ($menu_item->id)
                $menu_item_id = $menu_item->id;


            $result[$key] = $a->to_array();
            $result[$key]['lang'] = $article_item_language->get_article_item_lang($a->id);
            $result[$key]['link'] = $article_item_link->get_article_link_for_user($a->id);
            $result[$key]['menu_item'] = $menu_item_id;
        }

        return $result;
    }

}

?>