<?php

class Users extends MY_Controller {

    function __construct() {
        parent::__construct();
    }

    public function index() {

        $name = 'users';

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

    function get_users() {
        $users = new User();

        $this->return_result($users->get_all_user());
    }

    function set_user() {
        $user = new User();

        $this->return_result($user->set_user());

    }

    function get_user() {
        $user = new User();

        $this->return_result($user->get_user($this->input->post('id')));
    }

    function delete_user() {

        $user = new User();

        $this->return_result($user->delete_user());

    }

    function check_email() {
        $user = new User();
        $result = $user->check_email($this->input->post('email'));
        $this->output->set_output($result ? 1 : 0);
    }

    public function get_all_group(){

        $group = new Group();

        $this->return_result($group->get_all_group());

    }
}

?>
