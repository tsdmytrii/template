<?php

class Login extends MY_Controller {

    function __construct() {
        parent::__construct();
    }

    function index() {

        if ($this->authorized() === false) {

            $data['layout_title'] = 'Template admin:: Login';
            $this->load_header(false, $data['layout_title']);
//            $this->load->view('admin/header', $data);
            $this->load->view('admin/login/toper');
            $this->load->view('admin/login/login');

        } else {

            $this->redirect_to_main();

        }

    }

    function process() {
        if ($this->ion_auth->identity_check(($this->input->post('login'))) !== false) {
            $this->authorize();
        } else {
            redirect(base_url() . 'admin/login', 'location', 302);
        }
    }

    function logout() {
        $this->ion_auth->logout();
        redirect(base_url() . 'admin/login', 'refresh');
    }

}

?>
