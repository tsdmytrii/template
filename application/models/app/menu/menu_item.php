<?php

class Menu_item extends DataMapper {

    public $db_params = 'default';
    public $table = 'menu_items';
    public $has_one = array('component');
    public $has_many = array('menu_block', 'menu_item_language');
    public static $ci;
    public $validation = array(
        array(
            'field' => 'id',
            'label' => 'id',
            'rules' => array('trim', 'numeric', 'max_length' => 5),
        ),
        array(
            'field' => 'component_id',
            'label' => 'Component id',
            'rules' => array('trim', 'numeric', 'max_length' => 5),
        ),
        array(
            'field' => 'menu_block_id',
            'label' => 'Menu block id',
            'rules' => array('trim', 'numeric', 'max_length' => 5),
        ),
        array(
            'field' => 'parent_id',
            'label' => 'Parent id',
            'rules' => array('trim', 'numeric', 'max_length' => 5),
        ),
        array(
            'field' => 'position',
            'label' => 'Position',
            'rules' => array('trim', 'numeric', 'max_length' => 5),
        ),
        array(
            'field' => 'main',
            'label' => 'Main',
            'rules' => array('trim', 'numeric', 'max_length' => 1),
        ),
        array(
            'field' => 'window',
            'label' => 'Window',
            'rules' => array('trim', 'numeric', 'max_length' => 1),
        ),
    );

    function __construct($id = NULL) {
        parent::__construct($id);
        if (empty(self::$ci)) {
            self::$ci = &get_instance();
        }
    }

    /*
     * -------!!!!!!!!!!!!!!------ ADMIN functions ---------!!!!!!!!!!!!!!------
     */

    /*
     * ----------------------- Menu Item SET functions -------------------------
     */

    public function set_menu_item() {
        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return array('result' => false, 'msg' => 403);

        $result['result'] = false;
        $result['msg'] = false;

        $menu_item = new Menu_item();

        if (self::$ci->input->post('id'))
            $menu_item->get_by_id(self::$ci->input->post('id'));

        if (Component::check_multi(self::$ci->input->post('component_type_id'))) {

            $component_id = self::$ci->input->post('component_id') ? self::$ci->input->post('component_id') : 0;

            if ($component_id == 0 && self::$ci->input->post('component_type_id')) {
                $component = new Component();
                $component_id = $component->set_component_from_menu_item($component_id);
            }

            $menu_item->parent_id = self::$ci->input->post('parent_id');
            $menu_item->component_id = $component_id;
            $menu_item->main = intval(self::$ci->input->post('main')) == 1 ? 1 : 0;
            $menu_item->position = self::$ci->input->post('position');
            $menu_item->inner_navigation = self::$ci->input->post('inner_navigation');
            $menu_item->child_inner_navigation = self::$ci->input->post('child_inner_navigation');

            if ($menu_item->save()) {

                $menu_block = new Menu_block();
                $menu_block->get_by_id(self::$ci->input->post('menu_block_id'));

                $menu_item->save($menu_block);

                $m_item_lang = new Menu_item_language();

                $result['result']['menu_item_id'] = $menu_item->id;

                $lang = $m_item_lang->set_menu_item_language($menu_item->id);

                if ($lang['msg']) {
                    $result['msg'] = $lang['msg'];
                }
                $result['result']['menu_item_lang_id'] = $lang['result'];
            } else {
                if ($menu_item->valid) {
                    $result['msg'] = 'insert or update failure';
                } else {
                    $result['msg'] = $menu_item->error->string;
                }
            }
        } else {
            $result['msg'] = 'Such component is registered';
        }

        return $result;
    }

    /*
     * ----------------------- Menu Item GET functions -------------------------
     */

    public function get_menu_item_by_block($menu_bl_id = false) {
        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return array('result' => false, 'msg' => 403);

        $result['result'] = false;
        $result['msg'] = false;

        $menu_block_id = self::$ci->input->post('menu_block_id') ? self::$ci->input->post('menu_block_id') : $menu_bl_id;

        if ($menu_block_id) {

            $menu_block = new Menu_block();

            $menu_block->get_by_id($menu_block_id);

            $menu_item = new Menu_item();

            $menu_item->order_by('position')->get_by_related($menu_block);

            $component_type = new Component_type();
            $component_type->select('id, name')->get();

            if ($menu_item->exists()) {
                foreach ($menu_item as $key => $m_i) {

                    $menu_item_lang = new Menu_item_language();

                    if (intval($m_i->component_id) != 0) {

                        $component = new Component();

                        $component->select('id, component_type_id, content_id')->get_by_id($m_i->component_id);

                        $component_type_id = $component->component_type_id;

                        foreach ($component_type as $c) {
                            if ($c->id == $component_type_id) {
                                $component_type_result = $c->to_array(array('id', 'name'));
                            }
                        }

                    }

                    $result['result'][$key] = $m_i->to_array();
                    $result['result'][$key]['component_type'] = intval($m_i->component_id) != 0 ? $component_type_result : false;
                    $result['result'][$key]['component'] = intval($m_i->component_id) != 0 ? $component->to_array(array('id', 'component_type_id', 'content_id')) : false;
                    $m_lang = $menu_item_lang->get_menu_item_language($m_i->id, '*', false);
                    if ($m_lang['msg']) {
                        $result['msg'] = $m_lang['msg'];
                    }
                    $result['result'][$key]['lang'] = $m_lang['result'];
                }
            } else {
                $result['msg'] = 'There are no menu items';
            }

        }

        return $result;
    }



    public function get_menu_item_for_parent($menu_item_id = '', $get_just_parent = false) {
        $menu_block_id = self::$ci->input->post('menu_block_id');

        $menu_block = new Menu_block();
        $menu_block->get_by_id($menu_block_id);

        $menu_item = new Menu_item();
        $menu_item->get_by_related($menu_block);

        $bufer = false;

        foreach ($menu_item as $m_i) {

            if ($m_i->id != $menu_item_id) {

                $m_i_lang = new Menu_item_language();

                $menu_item_arr = $m_i->to_array();
                $menu_item_arr['lang'] = $m_i_lang->get_menu_item_language($m_i->id);

                $bufer[] = $menu_item_arr;
            }
        }

        if ($get_just_parent) {

            $result = array(
                'parent' => $bufer
            );

        } else {

            $related_menu_item = new Menu_item();

            $result = array(
                'parent' => $bufer,
                'related' => $related_menu_item->get_menu_item_for_related(),
            );

        }

        $component = new Component_type();
        $component->get_by_display('1');
        $result['component'] = $component->all_to_array();

        return $result;
    }

    public function get_menu_item_for_related($menu_item_id = '') {
        $menu_block_id = self::$ci->input->post('menu_block_id');

        $menu_block = new Menu_block();

        $menu_block->where(array('id !=' => $menu_block_id))->get();

        $menu_item_array = false;

        foreach ($menu_block as $key => $m_b) {
            $menu_item = new Menu_item();

            $menu_item->get_by_related($m_b);

            foreach ($menu_item as $m) {
                $menu_item_array[] = $m->to_array();
            }
        }


        $menu_block_current = new Menu_block();
        $menu_block_current->get_by_id($menu_block_id);

        $menu_item_related_obj = new Menu_item();
        $menu_item_related_obj->get_by_related($menu_block_current);
        $menu_item_related = $menu_item_related_obj->all_to_array();

        $result = false;

        if ($menu_item_array !== false) {
            foreach ($menu_item_array as $m_i) {

                $relation = false;

                foreach ($menu_item_related as $key => $m_i_r) {
                    if ($m_i_r['id'] == $m_i['id']) {
                        $relation = true;
                        unset($menu_item_related[$key]);
                    }
                }

                $m_i_lang = new Menu_item_language();

                $result[] = array(
                    'menu_item' => $m_i,
                    'lang' => $m_i_lang->get_menu_item_language($m_i['id']),
                    'relation' => $relation
                );
            }

            return $result;
        } else {
            return false;
        }
    }

    public function get_menu_item() {
        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return array('result' => false, 'msg' => 403);

        $menu_item_id = self::$ci->input->post('menu_item_id');

        $result['result'] = false;
        $result['msg'] = false;

        if ($menu_item_id) {

            $menu_item = new Menu_item();
            $menu_item->get_by_id($menu_item_id);

            if ($menu_item->exists()) {

                $menu_item_lang = new Menu_item_language();

                $component = new Component();
                $component->get_by_id($menu_item->component_id);

                $result['result'] = $menu_item->to_array();

                $result['result']['component'] = $component->exists() ? $component->to_array(array('id', 'component_type_id', 'name')) : false;

                $lang = $menu_item_lang->get_menu_item_language($menu_item_id);

                $result['msg'] = $lang['msg'];
                $result['result']['lang'] = $lang['result'];

            } else {

                $result['msg'] = 'There are no such menu item';

            }

        } else {
            $result['msg'] = 'There are no such menu item';
        }

        return $result;
    }

    public function get_menu_item_by_id($m_i_id) {
        $m_item_id = self::$ci->input->post('menu_item_id') ? self::$ci->input->post('menu_item_id') : $m_i_id;
        $menu_item = new Menu_item();
        $menu_item->get_by_id($m_item_id);

        $menu_item_lang = new Menu_item_language();

        $menu_item_link = new Link();

        $result = $menu_item->to_array();
        $result['lang'] = $menu_item_lang->get_menu_item_language($m_item_id);
        $result['link'] = $menu_item_link->get_link($m_item_id);

        return $result;
    }

    public function get_menu_item_by_static_comp_id($href) {

        $menu_item = new Menu_item();
        $menu_item->get_by_href($href);

        $menu_item_lang = new Menu_item_language();

        $menu_item_link = new Link();

        $result = $menu_item->to_array();
        $result['lang'] = $menu_item_lang->get_menu_item_language($menu_item->id);
        $result['link'] = $menu_item_link->get_link($menu_item->id, true);

        return $result;
    }


    /**
     * @todo Сделать изменения parent_id дочерних пунктов меню, убрать изменение линков.
     */

    public function delete_menu_item($menu_item_id = false) {

        $result['result'] = false;
        $result['msg'] = false;

        $menu_item_id = self::$ci->input->post('menu_item_id') ? self::$ci->input->post('menu_item_id') : $menu_item_id;

        $menu_item = new Menu_item();
        $menu_item->get_by_id($menu_item_id);

        $child_menu_item = new Menu_item();
        $child_menu_item->get_by_parent_id($menu_item_id);

        if ($child_menu_item->exists()) {
            foreach ($child_menu_item as $c) {
                $c->parent_id = 0;
                $c->save();
            }
        }

        $menu_block = new Menu_block();
        $menu_block->get();

        if ( ! $menu_item->delete($menu_block->all)) {
            $result['msg'] = 'Menu item relation to menu block deleting error';
            return $result;
        }

        $menu_item_lang = new Menu_item_language();
        if ( ! $menu_item_lang->delete_menu_item_language($menu_item_id)) {
            $result['msg'] = 'Menu item language deleting error';
            return $result;
        }

        if ($menu_item->delete()) {
            $result['result'] = true;
        } else
            $result['msg'] = 'Menu item deleting error';

        return $result;
    }

    public function minus_menu_item() {
        $menu_item_id = self::$ci->input->post('menu_item_id');
        $menu_block_id = self::$ci->input->post('menu_block_id');

        $menu_item = new Menu_item();
        $menu_item->get_by_id($menu_item_id);

        $menu_block = new Menu_block();
        $menu_block->get_by_id($menu_block_id);

        $menu_block->delete($menu_item);

        return true;
    }

    public function get_menu_item_sitemap() {
        $menu_block = new Menu_block();

        $menu_block->where_in('id', array(3, 4))->get();

        $result = false;

        foreach ($menu_block as $m_b) {
            $menu_item = new Menu_item();

            $menu_item->get_by_related($m_b);

            foreach ($menu_item as $m) {
                $m->menu_item_language->get();

                foreach ($m->menu_item_language as $l) {
                    if ($l->language_id != 3 && $l->language_id != 1) {
                        $m->link->where(array('language_id' => $l->language_id))->get();

                        if (isset($m->link->link)) {
                            $result[] = $m->link->link;
                        } else {
                            if (isset($m->href) && !empty($m->href)) {
                                $result[] = $m->href . '/' . $m->id . '/' . $m->main . '/' . $l->language_id;
                            }
                        }
                    }
                }
            }
        }

        return $result;
    }

    public function get_menu_item_by_component_id($component_id, $lang_id = false) {
        $menu_item = new Menu_item();

        $menu_item->get_by_component_id($component_id);

        $result = false;

        foreach ($menu_item as $key => $m) {

            $result[$key] = $m->to_array();

            $lang = new Language();

            if ($lang_id) {
                $m->menu_item_language->get();

                $lang_arr = $lang->get_langs_as_key_id();

                foreach ($m->menu_item_language as $m_l) {
                    $result[$key]['lang'][$lang_arr[$m_l->language_id]['iso_code']] = $m_l->to_array();
                }

            } else {

                $lang->get_default_lang();

                $m->menu_item_language->where(array('language_id' => $lang->id))->get();

                $result[$key]['lang'] = $m->menu_item_language->to_array();
            }

        }

        return $result;
    }

    public function get_menu_item_autocomplete() {
        $menu_item_language = new Menu_item_language();
        $query = self::$ci->input->post('query');

        $menu_item_language->like('value', $query)->get();
        foreach ($menu_item_language as $m) {
            $suggestions[] = $m->value;
            $data[] = $m->menu_item_id;
        }

        return array('query' => $query, 'suggestions' => $suggestions, 'data' => $data);
    }

    public function get_menu_items_for_user_components($menu_item_id) {

        $result = false;

        if ($menu_item_id !== 0) {

            $menu_item = new Menu_item();

            $menu_item->get_by_id($menu_item_id);

            if (intval($menu_item->child_inner_navigation) == 1) {
                $parent_id = $menu_item->parent_id;

                if ($parent_id !== 0) {

                    $menu_child = $menu_item->get_by_parent_id($parent_id);

                    foreach ($menu_child as $key => $m) {
                        $menu_child_array[$key] = $this->get_menu_item_by_id($m->id);
                        if ($m->id == $menu_item_id) {
                            $menu_child_array[$key]['current'] = true;
                        }
                    }

                    $result['parent'] = $this->get_menu_item_by_id($parent_id);
                    $result['children'] = $menu_child_array;
                } else {

                    $parent_id = $menu_item->id;

                    $result['parent'] = $this->get_menu_item_by_id($parent_id);
                    $result['parent']['current'] = true;

                    $menu_child = $menu_item->get_by_parent_id($parent_id);

                    foreach ($menu_child as $key => $m) {
                        $menu_child_array[$key] = $this->get_menu_item_by_id($m->id);
                    }

                    $result['children'] = $menu_child_array;
                }
            }
        }

        return $result;
    }

    public function get_inner_menu_items() {
        $menu_item = new Menu_item();
        $menu_item->get_by_inner_navigation(1);

        $result = false;

        foreach ($menu_item as $key => $m) {

            $result[$key] = $m->to_array();

            $link = new Link();

            $result[$key]['link'] = $link->get_link($m->id, true);
        }

        return $result;
    }

    /*
     *
     * ---------------------------- Helper functions ---------------------------
     *
     */

    public function get_menu_item_id($content_id, $component_type_id) {

        $menu_item_id = 0;

        $component = new Component();

        $component
                ->where(
                        array(
                            'content_id' => $content_id,
                            'component_type_id' => $component_type_id
                        )
                )
                ->get();

        if ($component->exists()) {

            $menu_item = new Menu_item();

            $menu_item->get_by_component_id($component->id);

            $menu_item_id = $menu_item->exists() ? $menu_item->id : 0;

        }

        return 0;

    }

}

?>