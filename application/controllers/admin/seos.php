<?php

class Seos extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->library(array('form_validation', 'upload', 'plugins/image'));
    }

    public function index() {
        $name = 'seo';

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

    public function get_seo(){
        $seo = new Seo();

        $result = $seo->get_seo();

        $this->return_result($result);
    }

    public function set_seo(){
        $seo = new Seo();

        $result = $seo->set_seo();

        $this->return_result($result);
    }

}

?>
