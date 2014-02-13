<?php

class Component extends DataMapper {

    public $table = 'components';
    public $has_one = array('component_type');
    public $has_many = array('menu_item', 'link');
    public static $ci;
    public $validation = array(
        array(
            'field' => 'id',
            'label' => 'id',
            'rules' => array('trim', 'numeric', 'max_length' => 5),
        )
    );

    function __construct($id = NULL) {
        parent::__construct($id);
        if (empty(self::$ci)) {
            self::$ci = &get_instance();
        }
    }

    /*
     * SET functions
     */

    public function create_component($component_type_id, $content_id, $display = 1, $name = '') {

        $component = new Component();

        $component->component_type_id = $component_type_id;
        $component->content_id = $content_id;
        $component->display = $display;
        $component->name = $name;

        if ($component->save())
            return $component->id;
        else
            return false;

    }

    public function set_component() {

        self::$ci->config->load('my_config');

        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return array('result' => false, 'msg' => 403);

        $component = new Component();

        $check_component = Component::check_multi(self::$ci->input->post('component_type_id'));

        $result['result'] = false;
        $result['msg'] = $check_component ? false : 'Such component exist';

        if ($check_component) {

            $component_id = self::$ci->input->post('id') ? self::$ci->input->post('id') : false;

            if ($component_id !== false)
                $component->get_by_id($component_id);

            $component->name = self::$ci->input->post('name');
            $component->component_type_id = self::$ci->input->post('component_type_id');
            $component->update_date = date(self::$ci->config->item('date') . ' ' . self::$ci->config->item('time'));

            if ($component->save()) {
                if ($component_id == false) {

                    $content_id = $this->make_component_content(self::$ci->input->post('component_type_id'), $component->id);
                    $component->content_id = $content_id ? $content_id : 0;

                    if ($component->save())
                        $result['result'] = $component->id;
                }
                $result['result'] = $component->id;
            } else {
                if ($component->valid) {
                    $result['msg'] = 'insert or update failure';
                } else {
                    $result['msg'] = $component->error->string;
                }
            }
        }

        return $result;
    }

    public function set_component_from_menu_item($component_id = false) {
        self::$ci->config->load('my_config');

        $component = new Component();

        if ($component_id && $component_id !== false) {
            $component->get_by_id($component_id);
        }

        $component->name = self::$ci->input->post('value');
        $component->component_type_id = self::$ci->input->post('component_type_id');
        $component->update_date = date($this->config->item('date') . ' ' . $this->config->item('time'));

        if ($component->save()) {

            if ( ! $component_id && Component::check_multi(self::$ci->input->post('component_type_id'))) {
                $content_id = $this->make_component_content(self::$ci->input->post('component_type_id'), $component->id);
                $component->content_id = $content_id ? $content_id : 0;

                $component->save();
            }

            return $component->id;
        } else {
            return false;
        }
    }

    public function make_component_content($component_type_id = false, $component_id = false) {

        $result = false;

        if ($component_id) {

            $component_type = new Component_type();
            $component_type->get_by_id($component_type_id);

            $content = new $component_type->library;
            $content_id = $content->set_from_default($component_id);

            $result = $content_id;
        }

        return $result;

    }


    /*
     * GET functions
     */

    public function get_component_by_id($c_id = false)
    {
        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return array('result' => false, 'msg' => 403);

        $component = new Component();

        $component_id = self::$ci->input->post('component_id') ? self::$ci->input->post('component_id') : $c_id;

        $result['result'] = false;
        $result['msg'] = false;

        if ($component_id) {
            $component->get_by_id($component_id);

            if ($component->exists())
                $result['result'] = $component->to_array();
            else
                $result['msg'] = 'No such component';

        } else
            $result['msg'] = 'No such component';


        return $result;
    }

    public function get_components() {
        $result['result'] = false;
        $result['msg'] = false;

        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return array('result' => false, 'msg' => 403);

        $component = new Component();

        $component->where('display', 1);

        $result['result']['iTotalDisplayRecords'] = $component->get_by_related_component_type('tab_id', 0)->result_count();


        if (self::$ci->input->post('iDisplayLength')) {
            $component->limit(self::$ci->input->post('iDisplayLength'));
        }

        if (self::$ci->input->post('iDisplayStart')) {
            $component->offset(self::$ci->input->post('iDisplayStart'));
        }

        if (self::$ci->input->post('sSearch')) {
            $component->like('name', self::$ci->input->post('sSearch'));
        }

        switch (intval(self::$ci->input->post('iSortCol_0'))) {
            case 0:
                $component->order_by('id', self::$ci->input->post('sSortDir_0'));
                break;
            case 1:
                $component->order_by('name', self::$ci->input->post('sSortDir_0'));
                break;
        }

        $component->get_by_related_component_type('tab_id', 0);

        $result['result']['sEcho'] = self::$ci->input->post('sEcho');
        $result['result']['iTotalRecords'] = $component->where('display', 1)->result_count();

        $result['result']['aaData'] = false;

        if ($component->exists()) {
            foreach ($component as $key => $c) {
                $component_type = new Component_type();
                $component_type->get_by_id($c->component_type_id);

                $menu_item = new Menu_item();

                $result['result']['aaData'][$key] = $c->to_array();
                $result['result']['aaData'][$key]['component_type'] = $component_type->to_array();
                $result['result']['aaData'][$key]['menu_item'] = $menu_item->get_menu_item_by_component_id($c->id);
            }
        } else {
            $result['msg'] = 'There are no components yet';
        }

        return $result;
    }

    /*
     * DELETE functions
     */

    public function delete_component($c_id = false) {

        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return array('result' => false, 'msg' => 403);

        $component = new Component();

        $component_id = self::$ci->input->post('component_id') ? self::$ci->input->post('component_id') : $c_id;

        $component->get_by_id($component_id);

        $menu_item = new Menu_item();

        $menu_item->get_by_component_id($component_id);

        if ($menu_item->exists()) {
            foreach ($menu_item as $m) {
                $m->component_id = 0;

                if (!$m->save())
                    return false;
            }
        }

        $component_type = new Component_type();
        $component_type->get_by_id($component->component_type_id);

        $component_function = new Component_function();
        $component_function->where(array('component_type_id' => $component->component_type_id));
        $component_function->like('name', 'delete');
        $component_function->get();

        $delete_function = $component_function->name;

        $content = new $component_type->library;
        $status = $content->$delete_function($component->content_id);

        $result['result'] = false;
        $result['msg'] = false;

        if ( ! $status) {
            $result['msg'] = 'Error delete component content';
        } else {
            $result['result'] = $component->delete();
        }


        return $result;
    }

    /*
     * MENU ITEM functions
     */

    public function connect_menu_item() {
        $result['result'] = false;
        $result['msg'] = false;

        $component = new Component();
        $component_id = self::$ci->input->post('component_id');
        $component->get_by_id($component_id);

        $menu_item = new Menu_item();
        $menu_item->get_by_id(self::$ci->input->post('menu_item_id'));

        $menu_item->component_id = $component_id;

        if ($menu_item->save()) {
            $result['result'] = true;
        } else
            $result['msg'] = 'Can not save menu item.';

        return $result;
    }

    public function disconect_menu_item() {

        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return array('result' => false, 'msg' => 403);

        $result['result'] = false;
        $result['msg'] = false;

        $menu_item = new Menu_item();
        $menu_item->get_by_id(self::$ci->input->post('menu_item_id'));

        if ($menu_item->exists()) {

            $menu_item->component_id = 0;

            if ($menu_item->save()) {
                $result['result'] = true;
            } else {
                $result['msg'] = 'Can not save menu item.';
            }
        } else {
            $result['msg'] = 'No such menu item.';
        }

        return $result;
    }

    /*
     * HELPER functions
     */

    public static function check_multi($component_type_id = false) {

        $result = true;

        $component_type = new Component_type();
        $component_type->get_by_id($component_type_id);


        if ($component_type->exists() && $component_type->multi == 0) {

            $component_type->component->get();

            if ($component_type->component->exists())
                $result = false;
        }

        return $result;

    }

    public function component_autocomplete() {
        $component = new Component();
        $query = self::$ci->input->post('query');

        $component->like('name', $query)->get();

        foreach ($component as $c) {
            $suggestions[] = $c->name;
            $data[] = array(
                'id' => $c->id,
                'component_type_id' => $c->component_type_id
            );
        }

        return array('query' => $query, 'suggestions' => $suggestions, 'data' => $data);
    }

    public function component_autocomplete_mini_block() {
        $component = new Component();
        $query = self::$ci->input->post('query');

        $component->like('name', $query)->get();

        foreach ($component as $c) {
            if ($c->component_type_id == '1' || $c->component_type_id == '7') {
                $suggestions[] = $c->name;
                $data[] = array(
                    'id' => $c->id,
                    'content_id' => $c->content_id,
                    'component_type_id' => $c->component_type_id
                );
            }
        }

        return array('query' => $query, 'suggestions' => $suggestions, 'data' => $data);
    }

}

?>