<?php

class Static_component extends DataMapper {

    public $table = 'static_components';
    public $has_many = array('static_component_language', 'link');
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
            'rules' => array('required', 'trim', 'min_length' => 8, 'max_length' => 10),
        )
    );

    function __construct($id = NULL) {
        parent::__construct($id);
        if (empty(self::$ci)) {
            self::$ci = &get_instance();
        }
        self::$ci->load->library(array('plugins/image'));
    }

    public function set_from_default($component_id) {
        $static_component = new Static_component();

        $static_component->date = date('d-m-Y');
        $static_component->component_id = $component_id;

        $static_component->save();

        $static_component_lang = new Static_component_language();

        $static_component_lang->set_from_default($static_component->id, 1);
        $static_component_lang->set_from_default($static_component->id, 2);
        $static_component_lang->set_from_default($static_component->id, 3);

        if ($static_component->id)
            return $static_component->id;
        else
            return false;
    }

    public function set_static_component() {

        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return 403;

        $static_component = new Static_component();

        $static_component_id = self::$ci->input->post('static_component_id');

        $static_component->get_by_id($static_component_id);
        $static_component->date = self::$ci->input->post('date');

        $static_lang = new Static_component_language();

        $link_id = false;

        if (self::$ci->input->post('link')) {

            $menu_item = new Menu_item();

            $link = new Link();

            $link_id = $link->set_static_comp_link($menu_item->get_menu_item_id(self::$ci->input->post('static_component_id'), self::$ci->input->post('component_type_id')), self::$ci->input->post('static_component_id'), self::$ci->input->post('component_type_id'));

        }


        if ($static_component->save()) {
            if ($static_lang->set_static_component_lang($static_component_id))
                return array('link_id' => $link_id);
        } else
            return false;
    }

    public function get_static_component() {

        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return 403;

        $static_component_id = self::$ci->input->post('static_component_id') ? self::$ci->input->post('static_component_id') : false;

        $static_component = new Static_component();
        $static_component->get_by_id($static_component_id);

        $static_component_lang = new Static_component_language();

        $link = new Link();

        $result = $static_component->to_array();
        $result['lang'] = $static_component_lang->get_static_component_lang($static_component_id);
        $result['links'] = $link->get_static_comp_link($static_component_id);

        return $result;
    }

    public function get_static_component_for_user($static_component_id) {
        $static_component_id = self::$ci->input->post('static_component_id') ? self::$ci->input->post('static_component_id') : $static_component_id;       

        $static_component = new Static_component();
        $static_component->get_by_id($static_component_id);

        if (isset($static_component->id)) {
           
            $static_component_lang = new Static_component_language();
            $lang = $static_component_lang->get_static_component_lang($static_component_id);

            $result = $static_component->to_array();
            $result['lang'] = $lang;                                    

            return $result;
        } else {
            return false;
        }
    }

    public function get_static_component_by_id($static_component_id) {

        $static_component = new Static_component();
        $static_component->get_by_id($static_component_id);

        $static_component_lang = new Static_component_language();
        $lang = $static_component_lang->get_static_component_lang($static_component_id);

        $result = $static_component->to_array();
        $result['lang'] = $lang;

        return $result;
    }

    public function delete_static_component($component_id) {

        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return 403;

        $static_component = new Static_component();

        $static_component->get_by_id($component_id);

        $static_component_lang = new Static_component_language();
        $static_component_lang->delete_static_component_lang($component_id);

        $link = new Link();
        $link->delete_static_comp_link($component_id);

        $static_component->delete();

        return array('static_component_id' => $component_id);
    }

    public function search() {

        $menu_item = new Menu_item();

        $menu_item->like('href', 'staticcomp/')->get();
//        $menu_item->get_by_related($component);

        $static_comp_id_array = array();

        foreach ($menu_item as $m) {
            $buf = explode('/', $m->href);
            if (!empty($buf[1]) && $buf[1] !== '')
                $static_comp_id_array[] = $buf[1];
        }

        $search_str = self::$ci->input->post('searchStr');
        $search_str = str_replace('«', '&laquo;', $search_str);
        $search_str = str_replace('»', '&raquo;', $search_str);
        $search_str = str_replace('–', '&ndash;', $search_str);

        $static_component_lang = new Static_component_language();

        $static_component_lang->
                like('title', $search_str)->
                or_like('description_search', $search_str)->
                where(array('language_id' => self::$ci->input->post('lang_id')))->
                where_in('static_component_id', $static_comp_id_array)->
                group_by('title')->
                get();

        $result = false;

        foreach ($static_component_lang as $a) {

            $bufer = $a->to_array(array('id', 'static_component_id', 'title', 'description'));

            $bufer['id'] = intval($bufer['static_component_id']);
            $bufer['description'] = strip_tags($bufer['description']);

            $m_item = new Menu_item();

            $menu_item = $m_item->get_menu_item_by_static_comp_id('staticcomp/' . $a->static_component_id);

            $bufer['href'] = $menu_item['href'];
            $bufer['link'] = $menu_item['link'];
            $bufer['main'] = $menu_item['main'];

            $result[] = $bufer;
        }

        return $result;
    }

    /* -------------------------------------------------------------------------
     * ----------------------- Mini Block Behavior -----------------------------
     * ---------------------------------------------------------------------- */

    public function set_content_behavior($static_component_id, $mini_block_id) {
        $static_component_behavior = new Static_component_behavior();

        return $static_component_behavior->set_content_behavior($static_component_id, $mini_block_id);
    }

    public function get_content_behavior($static_component_id) {
        $static_component_behavior = new Static_component_behavior();

        return $static_component_behavior->get_content_behavior($static_component_id);
    }

    public function delete_content_behavior($mini_block_id) {
        $static_component_behavior = new Static_component_behavior();

        return $static_component_behavior->delete_content_behavior($mini_block_id);
    }

    public function get_content_behavior_to_user($mini_block_id) {

        $static_component_behavior = new Static_component_behavior();
        $static_component_behavior->get_by_mini_block_id($mini_block_id);

        $component = new Component();
        $component->where(array('content_id' => $static_component_behavior->static_component_id, 'component_type_id' => 1))->get();

        $menu_item = new Menu_item();
        $where = array('component_id' => $component->id);
        $menu_item->where($where)->get();

        $menu_item_id = $menu_item->id ? $menu_item->id : 0;

        $static_component = new Static_component();
        $result = $static_component->get_static_component_for_user($static_component_behavior->static_component_id, $menu_item_id);

        $result['quantity'] = $static_component_behavior->quantity;

        return $result;
    }

}

?>