<?php

class Group extends DataMapper {

    public $table = 'groups';
    public $has_many = array('user', 'component_function');
    public static $ci;
    public $validation = array(
        array(
            'field' => 'id',
            'label' => 'id',
            'rules' => array('trim', 'numeric', 'max_length' => 11),
        )
    );

    function __construct($id = NULL) {
        parent::__construct($id);
        if (empty(self::$ci)) {
            self::$ci = &get_instance();
        }
    }

    /*
     * --------------------------------ADMIN------------------------------------
     */

    /*
     * Set functions
     */

    public function set_from_default() {
        return 0;
    }

    public function set_group() {
        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return array('result' => false, 'msg' => 403);

        $group = new Group();

        if (self::$ci->input->post('group_id'))
            $group->get_by_id(self::$ci->input->post('group_id'));

        self::$ci->config->load('my_config');

        $group->name = self::$ci->input->post('name');
        $group->clear_name = self::$ci->input->post('clear_name');
        $group->description = self::$ci->input->post('description');
        $group->admin_access = self::$ci->input->post('admin_access');
        $group->removed = 1;
        $group->date = date($this->config->item('date') . ' ' . $this->config->item('time'));

        $result['result'] = false;
        $result['msg'] = false;

        if ($group->save()) {

            $result['result'] = $group->id;

        } else {
            if ($group->valid) {
                $result['msg'] = 'insert or update failure';
            } else {
                $result['msg'] = $group->error->string;
            }
        }

        return $result;


    }

    /*
     * Get functions
     */

    public function get_group($group_id = false) {
        if (self::$ci->access->check_access(__FUNCTION__) == false) {
            return array('result' => false, 'msg' => 403);
        }

        $group_id = self::$ci->input->post('group_id') ? self::$ci->input->post('group_id') : $group_id;

        $group = new Group();
        $group->get_by_id($group_id);

        $result = array(
            'result' => $group->exists() ? $group->to_array() : false,
            'msg' => false
        );

        return $result;
    }

    public function get_all_group() {

        if (self::$ci->access->check_access(__FUNCTION__) == false) {
            return array('result' => false, 'msg' => 403);
        }

        $group = new Group();

        $group->get();

        $result = array(
            'result' => $group->all_to_array(),
            'msg' => false
        );

        return $result;
    }

    /*
     * Delete Functions
     */

    public function delete_group($group_id = false) {

        if (self::$ci->access->check_access(__FUNCTION__) == false) {
            return array('result' => false, 'msg' => 403);
        }

        $result['result'] = false;
        $result['msg'] = false;

        $group_id = self::$ci->input->post('group_id') ? self::$ci->input->post('group_id') : $group_id;

        $group = new Group();

        $group->get_by_id($group_id);

        $result['result'] = $group->to_array();

        return $result;

        if ($group->exists()) {

            $user = new User();

            $user->get_by_related($group);

            if ($user->exists()) {
                if ( ! $group->delete($user->all)) {
                    $result['msg'] = 'Cannot delete group relation with user.';
                }
            }

            $component_function = new Component_function();

            $component_function->get_by_related($group);

            if ($component_function->exists()) {
                if ( ! $group->delete($component_function->all)) {
                    $result['msg'] = 'Cannot delete group relation with component function.';
                }
            }

            if ($group->delete()) {
                $result['result'] = true;
            } else {
                $result['msg'] = 'Cannot delete group';
            }



        } else {
            $result['msg'] = 'There are no such group.';
        }

        return $result;
    }

    /*
     * Return all component type with functioons use for make relation between groups and component functions
     */

    public function get_components_and_functions() {

        if (self::$ci->access->check_access(__FUNCTION__) == false) {
            return array('result' => false, 'msg' => 403);
        }

        $counter = 0;

        $result = false;

        $component_type = new Component_type();

        $comp_funct_related = new Component_function();

        $component_type->order_by('tab_id', 'desc')->select('id, tab_id, psevdo_name, library')->get();

        $comp_funct_related->select('id')->get_by_related_group('id', self::$ci->input->post('group_id'));

        foreach ($component_type as $c) {

            $component_function = new Component_function();

            $component_function->where_related($c)->get();

            if ($component_function->exists()) {

                $result[$counter] = $c->to_array(array('id', 'tab_id', 'psevdo_name', 'library'));

                foreach ($component_function as $k => $f) {

                    $related = false;

                    foreach ($comp_funct_related as $rel_f) {

                        if ($rel_f->id == $f->id) {

                            $related = true;

                            unset($rel_f);

                            break;
                        }
                    }

                    $result[$counter]['functions'][$k] = $f->to_array();

                    $result[$counter]['functions'][$k]['related'] = $related;
                }


                $counter++;
            }
        }

        return array('result' => $result, 'msg' => false);
    }

    public function set_permissions() {

        $group = new Group();

        $group->get_by_id(self::$ci->input->post('group_id'));

        $component_function = new Component_function();

        $component_function->get_by_related($group);

        if ($component_function->exists()) {

            $group->delete($component_function->all);

            $component_function->clear();
        }

        $component_function->where_in('id', self::$ci->input->post('component_function_id'))->get();

        $result = array(
            'result' => $group->save($component_function->all),
            'msg' => false
        );

        return $result;
    }

    /*
     * ---------------------------------USER------------------------------------
     */
}

?>