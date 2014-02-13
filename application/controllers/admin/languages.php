<?php

class Languages extends MY_Controller {

    function __construct() {
        parent::__construct();
    }

    public function index() {
        $name = 'languages';

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

    public function get_language() {

        $language = new Language();

        $this->return_result($language->get_language());

    }

    public function get_all_languages() {

        $language = new Language();

        $this->return_result($language->get_all_languages());

    }

    public function set_language() {

        $language = new Language();

        $this->return_result($language->set_language());

    }

    public function delete_language() {

        $language = new Language();

        $this->return_result($language->delete_language());

    }


}

?>
