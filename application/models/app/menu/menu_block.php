<?php

class Menu_block extends DataMapper {

    public $db_params = 'default';
    public $table = 'menu_blocks';
    public $has_many = array('menu_item');
    public static $ci;
    public $validation = array(
        array(
            'field' => 'id',
            'label' => 'id',
            'rules' => array('trim', 'numeric', 'max_length' => 5),
        ),
        array(
            'field' => 'name',
            'label' => 'Name',
            'rules' => array('trim', 'min_length' => 3, 'required', 'max_length' => 100),
        ),
        array(
            'field' => 'role',
            'label' => 'Role',
            'rules' => array('trim', 'min_length' => 3, 'required', 'max_length' => 100),
        ),
    );

    function __construct($id = NULL) {
        parent::__construct($id);
        if (empty(self::$ci)) {
            self::$ci = &get_instance();
        }
    }

    /*
     * --------------------------------ADMIN------------------------------------
     */

    /*
     * Set functions
     */

    public function set_menu_block() {
        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return array('result' => false, 'msg' => 403);

        $menu_block = new Menu_block();

        if (self::$ci->input->post('id')) {
            $menu_block->get_by_id(self::$ci->input->post('id'));
        }

        $menu_block->name = self::$ci->input->post('name');
        $menu_block->role = self::$ci->input->post('role');
        $menu_block->position = self::$ci->input->post('position');

        $result['result'] = false;
        $result['msg'] = false;

        if ($menu_block->save()) {

            $result['result'] = $menu_block->to_array();

        } else {
            if ($menu_block->valid) {
                $result['msg'] = 'insert or update failure';
            } else {
                $result['msg'] = $menu_block->error->string;
            }
        }

        return $result;
    }

    /*
     * Get functions
     */

    public function get_menu_block($menu_block_id = false) {
        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return array('result' => false, 'msg' => 403);

        $menu_block_id = self::$ci->input->post('menu_block_id') ? self::$ci->input->post('menu_block_id') : $menu_block_id;

        $result['result'] = false;
        $result['msg'] = false;

        if ($menu_block_id) {

            $menu_block = new Menu_block();

            $result['result'] = $menu_block->get_by_id($menu_block_id)->to_array();

        }

        return $result;

    }

    public function get_all_menu_blocks() {
        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return array('result' => false, 'msg' => 403);

        $menu_block = new Menu_block();

        $menu_block->order_by("position")->get();

        $result['result'] = $menu_block->exists() ? $menu_block->all_to_array() : false;
        $result['msg'] = false;

        return $result;
    }

    /*
     * Delete Functions
     */

    public function delete_menu_block() {
        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return array('result' => false, 'msg' => 403);

        $menu_block_id = self::$ci->input->post('menu_block_id');

        $menu_block = new Menu_block();

        $menu_block->get_by_id($menu_block_id);

        $result['result'] = false;
        $result['msg'] = false;

        if ($menu_block->delete()) {
            $result['result'] = $menu_block_id;
        }

        return  $result;
    }

}

?>