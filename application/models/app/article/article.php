<?php

class Article extends DataMapper {

    public $table = 'articles';
    public $has_many = array('article_item');
    public static $ci;
    public $validation = array(
        array(
            'field' => 'id',
            'label' => 'id',
            'rules' => array('trim', 'numeric', 'max_length' => 11),
        ),
        array(
            'field' => 'component_id',
            'label' => 'Component id',
            'rules' => array('trim', 'max_length' => 11),
        )
    );

    function __construct($id = NULL) {
        parent::__construct($id);
        if (empty(self::$ci)) {
            self::$ci = &get_instance();
        }
    }

    public function set_from_default($component_id) {
        self::$ci->config->load('my_config');

        $article = new Article();

        $article->component_id = $component_id;
        $article->date = date(self::$ci->config->item('date') . ' ' . self::$ci->config->item('time'));

        $article->save();

        $language = new Language();
        $language->get_default_lang();

        $article_lang = new Article_language();
        $article_lang->set_from_default($article->id, $language->id);

        $link = new Link();
        $link->set_article_link($component_id, $article->id, $language->id);

        return $article->id;
    }

    public function get_all_displayed_article($a_id = false, $strip_tags = false, $menu_item_id = 0) {

        $article_id = self::$ci->input->post('article_id') ? self::$ci->input->post('article_id') : $a_id;

        $article = new Article();
        $article->get_by_id($article_id);

        $article_item = new Article_item();
        $where = array('article_id' => $article->id, 'display' => 1);

        $article_item->where($where)->order_by('date', 'desc')->order_by('time', 'desc')->get();

        $result = false;

        if ($menu_item_id == 0) {
            $menu_item = new Menu_item();
            $menu_item->get_by_component_id($article->component_id);

            foreach ($menu_item as $m_i) {

                if ($m_i->default_item == 1)
                    $menu_item_id = $m_i->id;
            }
        }

        $menu_item = new Menu_item();

        if ($menu_item_id == 0)
            $result['menu_item'] = false;
        else
            $result['menu_item'] = $menu_item->get_menu_item_by_id($menu_item_id);

        $result['menu'] = $menu_item->get_menu_items_for_user_components($menu_item_id);
        $result['menu_item_id'] = $menu_item_id;


        $bufer = false;

        foreach ($article_item as $a) {

            $bufer[] = $a->get_article_item_for_user($a->id, $strip_tags);
        }

        $result['data'] = $bufer;

        $article_lang = new Article_language();

        $result['seo'] = $article_lang->get_article_lang($article_id);

        return $result;
    }

    public function get_article_list() {

        if (self::$ci->access->check_access(__FUNCTION__) == false) {
            return array('result' => false, 'msg' => 403);
        }

        $article = new Article();
        $article->get();

        $result = false;

        $menu_item_id = 0;

        foreach ($article as $a) {

            $menu_item = new Menu_item();
            $menu_item->get_by_component_id($a->component_id);

            foreach ($menu_item as $m_i) {
                if ($m_i->default_item == 1)
                    $menu_item_id = $m_i->id;
            }

            $menu_item_lang = new Menu_item_language();
            $bufer = $a->to_array();
            $bufer['lang'] = $menu_item_lang->get_menu_item_language($menu_item_id);

            $result[] = $bufer;
        }

        return $result;
    }

    public function delete_article($article_id = false) {
        if (self::$ci->access->check_access(__FUNCTION__) == false) {
            return array('result' => false, 'msg' => 403);
        }

        $article_id = self::$ci->input->post('article_id') ? self::$ci->input->post('article_id') : $article_id;

        $article = new Article();

        $article->get_by_id($article_id);

        $article_item = new Article_item();
        $article_item->delete_article_item_by_article($article_id);

        $link = new Link();

        $link->delete_article_link($article_id);

        $article->delete();

        return array('article_id' => $article_id);
    }

    public function search() {

//        $component = new Component();
//
//        $component->get_by_component_type_id(7);

        $menu_item = new Menu_item();

        $menu_item->like('href', 'article/')->get();
//        $menu_item->get_by_related($component);

        $article_id_array = array();

        foreach ($menu_item as $m) {
            $buf = explode('/', $m->href);
            if (!empty($buf[1]) && $buf[1] !== '')
                $article_id_array[] = $buf[1];
        }

        $search_str = self::$ci->input->post('searchStr');
        $search_str = str_replace('«', '&laquo;', $search_str);
        $search_str = str_replace('»', '&raquo;', $search_str);
        $search_str = str_replace('–', '&ndash;', $search_str);

        $article_lang = new Article_language();

        $article_lang->
                or_like('description_search', $search_str)->
                where(array('language_id' => self::$ci->input->post('lang_id')))->
                where_in('article_id', $article_id_array)->
                group_by('article_id')->
                get();

        $result = false;

        switch (intval(self::$ci->input->post('lang_id'))) {
            case 1:
                $pref = 'en';
                break;
            case 2:
                $pref = 'ru';
                break;
            case 3:
                $pref = 'ua';
                break;
        }

        foreach ($article_lang as $a) {

            $m_item = new Menu_item();

            $bufer = $a->to_array(array('id', 'article_id', 'description'));

            $bufer['id'] = $bufer['article_id'];

            $bufer['description'] = strip_tags($bufer['description']);

            $menu_item = $m_item->get_menu_item_by_static_comp_id('article/' . $a->article_id);

            $bufer['title'] = $menu_item['lang'][$pref]['value'];

            $bufer['href'] = $menu_item['href'];
            $bufer['link'] = $menu_item['link'];
            $bufer['main'] = $menu_item['main'];

            $result[] = $bufer;
        }

        return $result;
//        return false;
    }

    public function get_article_seo($article_id = false) {

        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return array('result' => false, 'msg' => 403);


        $article_id = self::$ci->input->post('article_id') ? self::$ci->input->post('article_id') : $article_id;

        $result['result'] = false;
        $result['msg'] = false;

        if ($article_id != false) {

            $article = new Article();

            $article->get_by_id($article_id);

            if ($article->exists()) {
                $article_language = new Article_language();

                $link = new Link();

                $result['result']['lang'] = $article_language->get_article_lang($article_id);

                $result['result']['links'] = $link->get_link($article->component_id);
            } else {
                $result['msg'] = 'There are no such article';
            }
        } else {
            $result['msg'] = 'There are no such article';
        }

        return $result;
    }

    /* -------------------------------------------------------------------------
     * ----------------------- Mini Block Behavior -----------------------------
     * ---------------------------------------------------------------------- */

    public function set_content_behavior($article_id, $mini_block_id) {
        $article_behavior = new Article_behavior();

        return $article_behavior->set_content_behavior($article_id, $mini_block_id);
    }

    public function get_content_behavior($article_id) {
        $article_behavior = new Article_behavior();

        return $article_behavior->get_content_behavior($article_id);
    }

    public function delete_content_behavior($mini_block_id) {
        $article_behavior = new Article_behavior();

        return $article_behavior->delete_content_behavior($mini_block_id);
    }

    public function get_content_behavior_to_user($mini_block_id) {

        $article_behavior = new Article_behavior();
        $article_behavior->get_by_mini_block_id($mini_block_id);

        $article = new Article();
        $article->get_by_id($article_behavior->article_id);

        $article_item = new Article_item();
        $result['list'] = $article_item->get_article_item_by_behavior($article_behavior->article_id, $article->component_id, $article_behavior->quantity);

        $menu_item = new Menu_item();

        $where = array('component_id' => $article->component_id, 'default_item' => 1);
        $menu_item->where($where)->get();

        if ($menu_item->id)
            $result['menu_item'] = $menu_item->get_menu_item_by_id($menu_item->id);
        else {
            $result['menu_item'] = false;
            $result['link'] = 'article/' . $article_behavior->article_id . '/' . '0/0/';
        }

        return $result;
    }

}

?>