<?php

class Integrators extends MY_Controller {

    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $name = 'integrators';

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

    public function get_integrators()
    {
        $integrator = new Integrator();
        $this->return_result($integrator->get_integrators());
    }

    public function set_integrator()
    {
        $integrator = new Integrator();
        $this->return_result($integrator->set_integrator());
    }

    public function delete_integrator()
    {
        $integrator = new Integrator();
        $this->return_result($integrator->delete_integrator());
    }

    public function set_integrator_placeholder()
    {
        $placeholder = new Placeholder();
        $this->return_result($placeholder->set_integrator_placeholder());
    }

    public function get_all_integrator_placeholder()
    {
        $placeholder = new Placeholder();
        $this->return_result($placeholder->get_all_integrator_placeholder());
    }

    public function delete_integrator_placeholder()
    {
        $integrator = new Integrator();
        $this->return_result($integrator->delete_integrator_placeholder());
    }
}

?>
