<?php

class Markings extends MY_Controller {

    function __construct() {
        parent::__construct();
    }

    public function index() {
        $name = 'markings';

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

    public function set_marking() {
        $marking = new Marking();

        $this->return_result($marking->set_marking());
    }

    public function get_marking() {
        $marking = new Marking();

        $this->return_result($marking->get_marking());
    }

}

?>
