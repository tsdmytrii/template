<?php

class Access {

    function __construct() {
        $this->ci = &get_instance();
    }

    function check_access($func_name) {

        $id = $this->ci->session->userdata('user_id');

        if ($this->ci->ion_auth->is_admin() === TRUE) {
            return true;
        }

        $group = new Group();

        $group->get_by_related_user('id', $id);

        if ($group->name === 'admin')
            return true;

        $component_function = new Component_function();

        $component_function->where('name', $func_name)->get_by_related($group);

        return $component_function->exists();

    }

    function access_to_user($user_id) {
        $access = true;

        if ($user_id !== $this->ci->session->userdata('user_id'))
            $access = false;

        if ($this->ci->ion_auth->is_admin() || $access === true) {
            return true;
        } else {
            return false;
        }
    }

}

?>
