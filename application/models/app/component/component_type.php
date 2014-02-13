<?php

class Component_type extends DataMapper {

    public $table = 'component_types';
    public $has_many = array('component', 'component_function');
    public static $ci;
    public $validation = array(
        array(
            'field' => 'id',
            'label' => 'Id',
            'rules' => array('trim', 'numeric', 'max_length' => 11),
        ),
        array(
            'field' => 'tab_id',
            'label' => 'Tab id',
            'rules' => array('trim', 'numeric', 'max_length' => 5),
        ),
        array(
            'field' => 'name',
            'label' => 'Name',
            'rules' => array('trim', 'required', 'min_length' => 3, 'max_length' => 100),
        ),
    );

    function __construct($id = NULL) {
        parent::__construct($id);
        if (empty(self::$ci)) {
            self::$ci = &get_instance();
        }
    }

    /*
     * Set functions
     */

    public function set_from_default() {
        return 0;
    }

    public function set_component_type() {

        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return array('result' => false, 'msg' => 403);

        $component_type = new Component_type();

        if (self::$ci->input->post('id'))
            $component_type->get_by_id (self::$ci->input->post('id'));

        $component_type->tab_id = self::$ci->input->post('tab_id') ? self::$ci->input->post('tab_id') : 0;
        $component_type->name = self::$ci->input->post('name');
        $component_type->psevdo_name = self::$ci->input->post('psevdo_name');
        $component_type->display = self::$ci->input->post('display') && intval(self::$ci->input->post('display')) == 2 ? 0 : 1;
        $component_type->library = self::$ci->input->post('library');
        $component_type->admin_client_controller = self::$ci->input->post('admin_client_controller');
        $component_type->client_controller = self::$ci->input->post('client_controller');
        $component_type->server_controller = self::$ci->input->post('server_controller');
        $component_type->button_panel = self::$ci->input->post('button_panel') ? self::$ci->input->post('button_panel') : 0;
        $component_type->settings = self::$ci->input->post('settings') ? self::$ci->input->post('settings') : 0;
        $component_type->minimise = self::$ci->input->post('minimise') ? self::$ci->input->post('minimise') : 0;
        $component_type->maximise = self::$ci->input->post('maximise') ? self::$ci->input->post('maximise') : 0;
        $component_type->multi = self::$ci->input->post('multi') && intval(self::$ci->input->post('multi')) == 2 ? 0 : 1;

        $result['result'] = false;
        $result['msg'] = false;

        if ($component_type->save()) {

            if ( ! self::$ci->input->post('id')) {

                $component_function = new Component_function();

                $component_function->set_from_default($component_type->to_array());

            }

            $result['result'] = $component_type->id;

        } else {
            if ($component_type->valid) {
                $result['msg'] = 'insert or update failure';
            } else {
                $result['msg'] = $component_type->error->string;
            }
        }

        return $result;

    }

    /*
     * Get functions
     */

    public function get_component_type($component_type_id = false) {

        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return array('result' => false, 'msg' => 403);

        $component_type_id = self::$ci->input->post('component_type_id') ? self::$ci->input->post('component_type_id') : $component_type_id ;

        $result['result'] = false;
        $result['msg'] = false;

        if ($component_type_id !== false) {

            $component_type = new Component_type();

            $component_type->get_by_id($component_type_id);

            $result['result'] = $component_type->exists() ? $component_type->to_array() : false;

        }

        return $result;
    }

    public function get_all_component_type() {

        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return array('result' => false, 'msg' => 403);

        $component_type = new Component_type();

        if (self::$ci->input->post('limit'))
            $component_type->limit(self::$ci->input->post('limit'));

        if (self::$ci->input->post('offset'))
            $component_type->offset(self::$ci->input->post('offset'));

        $component_type->order_by('id', 'desc')->get();

        $result['result'] = false;
        $result['msg'] = false;

        if (self::$ci->input->post('limit') || self::$ci->input->post('offset')) {

            $result['result']['data'] = $component_type->all_to_array();

            $component_type->clear();

            $result['result']['quantity'] = $component_type->count();

        } else
            $result['result'] = $component_type->exists() ? $component_type->all_to_array() : false;

        return $result;
    }

    public function get_component_types_for_nav() {

        $component_type = new Component_type();

        $component_type->get();

        $result['result'] = false;
        $result['msg'] = false;

        foreach ($component_type as $key => $c) {

            $result['result'][$key] = $c->to_array();

            if (intval($c->multi) == 0) {
                $component = new Component();
                $component->get_by_component_type_id($c->id);

                $result['result'][$key]['exist'] = $component->exists();
            }

        }

        return $result;

    }

    public function get_display_component_types() {
        $component_type = new Component_type();

        $component_type->get_by_display('1');

        $result['result'] = $component_type->exists() ? $component_type->all_to_array() : false;
        $result['msg'] = false;

        return $result;
    }

}

?>