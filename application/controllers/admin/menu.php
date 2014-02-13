<?php

class Menu extends MY_Controller {

    function __construct() {
        parent::__construct();
    }

    public function index() {
        $name = 'menu';

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

    /*
     * Menu block functions
     */

    public function set_menu_block() {
        $menu_block = new Menu_block();

        $this->return_result($menu_block->set_menu_block());
    }

    public function get_menu_block() {
        $menu_block = new Menu_block();

        $this->return_result($menu_block->get_menu_block());
    }

    public function get_all_menu_blocks() {
        $menu_block = new Menu_block();

        $this->return_result($menu_block->get_all_menu_blocks());
    }

    public function delete_menu_block() {
        $menu_block = new Menu_block();

        $this->return_result($menu_block->delete_menu_block());
    }

    /*
     * Menu item functions
     */


    public function component_autocomplete() {
        $component = new Component();

        $result = $component->component_autocomplete();

        echo json_encode($result);
    }

/*
 * Old functions
 */





    public function set_menu_item(){
        $menu_item = new Menu_item();

        $result = $menu_item->set_menu_item();

        $this->return_result($result);
    }

    public function get_menu_item(){
        $menu_item = new Menu_item();

        $result = $menu_item->get_menu_item();

        echo $this->return_result($result);
    }

    public function get_menu_item_for_parent(){
        $menu_item = new Menu_item();

        $result = $menu_item->get_menu_item_for_parent();

        echo $this->return_result($result);
    }

    public function get_menu_item_by_block(){
        $menu_item = new Menu_item();

        $result = $menu_item->get_menu_item_by_block();

        $this->return_result($result);
    }

    public function delete_menu_item(){
        $menu_item = new Menu_item();

        $result = $menu_item->delete_menu_item();

        echo $this->return_result($result);
    }

    public function minus_menu_item(){
        $menu_item = new Menu_item();

        $result = $menu_item->minus_menu_item();

        echo $this->return_result($result);
    }

    public function get_navigationoption(){
        $option = new Navigation_option();

        $result = $option->get_navigation_option();

        echo $this->return_result($result);
    }

    public function set_navigationoption(){
        $option = new Navigation_option();

        $result = $option->set_navigation_option();

        echo $this->return_result($result);
    }

    public function menu_item_autocomplete(){
        $menu_item = new Menu_item();

        $result = $menu_item->get_menu_item_autocomplete();

        echo json_encode($result);
    }


}

?>
