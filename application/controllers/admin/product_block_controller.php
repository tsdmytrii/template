<?php

class Product_block_controller extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->library(array('form_validation'/*, 'cimongo/cimongo'*/));
        $this->session->set_userdata('lang', 2);
    }

    public function index()
    {
        $name = 'product_blocks';

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

    public function get_product_block()
    {
        $product_block = new Product_block();
        $result = $product_block->get_product_block_by_id();
        $this->return_result($result);
    }

    public function get_all_product_blocks()
    {
        $product_block = new Product_block();
        $result = $product_block->get_all_product_blocks();
        $this->return_result($result);
    }

    public function set_product_block()
    {
        $product_block = new Product_block();
        $result = $product_block->set_product_block();
        $this->return_result($result);
    }

    public function delete_product_block()
    {
        $product_block = new Product_block();
        $result = $product_block->delete_product_block();
        $this->return_result($result);
    }

    public function product_block_autocomplete()
    {
        $productBlock = new Product_block();
        $result = $productBlock->product_block_autocomplete();
        echo json_encode($result);
    }

    public function set_product_relation()
    {
        $product_block_product_modification = new Product_block_product_modification();
        $result = $product_block_product_modification->set_product_relation();
        $this->return_result($result);
    }

    public function delete_product_relation()
    {
        $product_block_product_modification = new Product_block_product_modification();
        $result = $product_block_product_modification->delete_product_relation();
        $this->return_result($result);
    }

}
