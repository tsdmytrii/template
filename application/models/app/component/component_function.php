<?php

class Component_function extends DataMapper {

    public $table = 'component_functions';
    public $has_one = array('component_type');
    public $has_many = array('group');
    public static $ci;
    public $validation = array(
        array(
            'field' => 'id',
            'label' => 'id',
            'rules' => array('trim', 'numeric', 'max_length' => 11),
        ),
        array(
            'field' => 'component_type_id',
            'label' => 'Component type id',
            'rules' => array('trim', 'numeric', 'max_length' => 11),
        ),
        array(
            'field' => 'name',
            'label' => 'Name',
            'rules' => array('trim', 'required', 'min_length' => 3, 'max_length' => 100),
        ),
        array(
            'field' => 'clear_name',
            'label' => 'Clear name',
            'rules' => array('trim', 'min_length' => 3, 'max_length' => 100),
        ),
    );

    function __construct($id = NULL) {
        parent::__construct($id);
        if (empty(self::$ci)) {
            self::$ci = &get_instance();
        }
    }

    /*
     * SET function
     */

    public function set_from_default($component_type_array = false) {

        if ($component_type_array !== false) {

            $function_name[] = 'get_'.strtolower($component_type_array['library']);
            $function_name[] = 'get_all_'.strtolower($component_type_array['library']);
            $function_name[] = 'set_'.strtolower($component_type_array['library']);
            $function_name[] = 'delete_'.strtolower($component_type_array['library']);

            $result = true;

            foreach($function_name as $key => $f) {

                $component_function = new Component_function();

                $component_function->component_type_id = $component_type_array['id'];
                $component_function->name = $f;

                if (!$component_function->save()) {
                    $result = false;
                    break;
                }

            }

            return $result;
        } else
            return false;
    }

    public function set_component_function() {

        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return array('result' => false, 'msg' => 403);

        $component_function = new Component_function();

        if (self::$ci->input->post('id'))
            $component_function->get_by_id (self::$ci->input->post('id'));

        $component_function->component_type_id = self::$ci->input->post('component_type_id');
        $component_function->name = self::$ci->input->post('name');
        $component_function->clear_name = self::$ci->input->post('clear_name');

        $result['result'] = false;
        $result['msg'] = false;

        if ($component_function->save()) {

            $result['result'] = $component_function->id;

        } else {
            if ($component_function->valid) {
                $result['msg'] = 'insert or update failure';
            } else {
                $result['msg'] = $component_function->error->string;
            }
        }

        return $result;

    }

    /*
     * GET function
     */

    public function get_all_component_function($component_type_id = false) {

        include(APPPATH . 'language/'.self::$ci->session->userdata('lang_iso').'/inter_lang.php');

        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return array('result' => false, 'msg' => 403);

        $component_type_id = self::$ci->input->post('component_type_id') ? self::$ci->input->post('component_type_id') : $component_type_id;

        $component_function = new Component_function();

        if ($component_type_id) {

            $component_function->get_by_component_type_id($component_type_id);

        } else {

            $component_function->get();

        }

        $result['result'] = $component_function->exists() ? $component_function->all_to_array() : false;
        $result['msg'] = $component_function->exists() ? false : $localization['comp_type']['empty_functions'];

        return $result;

    }

    /*
     * DELETE function
     */

    public function delete_component_function() {

        include(APPPATH . 'language/'.self::$ci->session->userdata('lang_iso').'/inter_lang.php');

        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return array('result' => false, 'msg' => 403);

        $component_function = new Component_function();

        $component_function_id = self::$ci->input->post('component_function_id') ? self::$ci->input->post('component_function_id') : $component_function_id;

        $component_function->get_by_id($component_function_id);

        $group = new Group();

        $group->get_by_related($component_function);

        if ($group->exists()){
            $component_function->delete($group->all);
        }

        if ($component_function->delete()) {
            $result['result'] = true;
            $result['msg'] = false;
        } else {
            $result['result'] = false;
            $result['msg'] = $localization['comp_type']['remove_func_error'];
        }

        return $result;

    }

}

?>