<?php

class Component_types extends MY_Controller {

    function __construct() {
        parent::__construct();
    }

    public function index() {
        $name = 'component_types';

        $tab = new Tab();

        $data['data'] = $tab->get_tab_by_name($name);

        if (!$this->input->is_ajax_request()) {
            $this->load_header($data['data']);
            $this->load->view('admin/toper', $data);
            $this->load_menu($data['data']);
            $this->load_index();
            $this->load_footer();
        }
        else
            $this->output->set_output(json_encode($data['data']));
    }

    public function get_component_type(){
        $component_type = new Component_type();

        $this->return_result($component_type->get_component_type());
    }

    public function set_component_type(){
        $component_type = new Component_type();

        $this->return_result($component_type->set_component_type());
    }

    public function get_all_component_type(){
        $component_type = new Component_type();

        $this->return_result($component_type->get_all_component_type());
    }


    /**
     * get_component_types_for_nav
     * function return array for server navigation
     *
     * @return array
     * @author Andrew Sygyda
     **/

    public function get_component_types_for_nav() {

        $component_type = new Component_type();

        $this->return_result($component_type->get_component_types_for_nav());
    }

    /*
     * COMPONENT TYPE FUNCTION
     */

    public function set_component_function() {

        $component_function = new Component_function();

        $this->return_result($component_function->set_component_function());

    }

    public function get_all_component_function() {

        $component_function = new Component_function();

        $this->return_result($component_function->get_all_component_function());

    }

    public function delete_component_function() {

        $component_function = new Component_function();

        $this->return_result($component_function->delete_component_function());

    }

}

?>
