<?php

class Components extends MY_Controller {

    function __construct() {
        parent::__construct();
    }

    public function index() {
        $name = 'pages';

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

    /**
     * Components functions
     */

    public function get_components() {
        $component = new Component();

        $this->return_result($component->get_components());
    }

    /**
     * Component types functions
     */

    public function get_display_component_types() {
        $component_type = new Component_type();

        $result = $component_type->get_display_component_types();

        $this->return_result($result);
    }

    /**
     * Menu item functions
     */

    public function set_conect_menu_item() {
        $component = new Component();

        $this->return_result($component->connect_menu_item());
    }

    public function disconect_menu_item() {
        $component = new Component();

        $result = $component->disconect_menu_item();

        $this->return_result($result);
    }

/*
 * Old fnctions
 */

    public function get_component_by_id() {
        $component = new Component();

        $result = $component->get_component_by_id();

        $this->return_result($result);
    }

    public function set_component() {
        $component = new Component();

        $result = $component->set_component();

        $this->return_result($result);
    }

    public function get_componen_types() {
        $component_type = new Component_type();

        $result = $component_type->get_all_component_type();

        $this->return_result($result);
    }

    public function delete_component() {
        $component = new Component();

        $this->return_result($component->delete_component());
    }

    public function component_autocomplete_mini_block() {
        $component = new Component();

        $result = $component->component_autocomplete_mini_block();

        echo json_encode($result);
    }

}

?>
