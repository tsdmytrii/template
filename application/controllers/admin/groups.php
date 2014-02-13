<?php

class Groups extends MY_Controller {

    function __construct() {
        parent::__construct();
    }

    public function index() {
        $name = 'group';

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

    public function get_group(){

        $group = new Group();

        $result = $group->get_group();

        $this->return_result($result);

    }

    public function get_all_group(){

        $group = new Group();

        $result = $group->get_all_group();

        $this->return_result($result);

    }

    public function set_group(){

        $group = new Group();

        $result = $group->set_group();

        $this->return_result($result);

    }

    public function delete_group(){

        $group = new Group();

        $this->return_result($group->delete_group());

    }

    public function get_component_functions() {

        $group = new Group();

        $this->return_result($group->get_components_and_functions());

    }

    public function set_permissions() {

        $group = new Group();

        $this->return_result($group->set_permissions());

    }

}

?>
