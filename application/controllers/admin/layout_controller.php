<?php

class Layout_controller extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->library(array('form_validation'));
        $this->session->set_userdata('lang', 2);
    }

    public function index()
    {
        $name = 'mini_blocks';

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

    public function get_mini_block()
    {
        $mini_block = new Mini_block();
        $result = $mini_block->get_mini_block_by_id();
        $this->return_result($result);
    }

    public function get_all_mini_blocks()
    {
        $mini_block = new Mini_block();
        $result = $mini_block->get_all_mini_blocks();
        $this->return_result($result);
    }

    public function set_mini_block()
    {
        $mini_block = new Mini_block();
        $result = $mini_block->set_mini_block();
        $this->return_result($result);
    }

    public function delete_mini_block()
    {
        $mini_block = new Mini_block();
        $result = $mini_block->delete_mini_block();
        $this->return_result($result);
    }

    public function mini_block_autocomplete()
    {
        $miniBlock = new Mini_block();
        $result = $miniBlock->mini_block_autocomplete();
        echo json_encode($result);
    }
}
