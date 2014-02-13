<?php

class Static_component_behavior extends DataMapper {

    public $table = 'static_component_behaviors';
    public $has_one = array('static_component');
    public static $ci;
    public $validation = array(
        array(
            'field' => 'id',
            'label' => 'id',
            'rules' => array('trim', 'numeric', 'max_length' => 5),
        ),
        array(
            'field' => 'static_component_id',
            'label' => 'Component id',
            'rules' => array('trim', 'min_length' => 1, 'max_length' => 11),
        ),
        array(
            'field' => 'mini_block_id',
            'label' => 'Mini block id',
            'rules' => array('trim', 'min_length' => 1, 'max_length' => 11),
        ),
        array(
            'field' => 'quantity',
            'label' => 'Quantity',
            'rules' => array('trim', 'min_length' => 1, 'max_length' => 11),
        )
    );

    function __construct($id = NULL) {
        parent::__construct($id);
        if (empty(self::$ci)) {
            self::$ci = &get_instance();
        }
        self::$ci->load->library(array('plugins/image'));
    }

    public function set_content_behavior($static_component_id, $mini_block_id) {

        $static_component_behavior = new Static_component_behavior();

        if (self::$ci->input->post('behavior_id'))
            $static_component_behavior->get_by_id(self::$ci->input->post('behavior_id'));
        else{
            $static_component_behavior->get_by_mini_block_id($mini_block_id);
        }

        $static_component_behavior->static_component_id = $static_component_id;
        $static_component_behavior->mini_block_id = $mini_block_id;
        $static_component_behavior->quantity = self::$ci->input->post('quantity');

        $static_component_behavior->save();

        return $static_component_behavior->id;

    }

    public function get_content_behavior($mini_block_id){

        $static_component_behavior = new Static_component_behavior();

        $static_component_behavior->get_by_mini_block_id($mini_block_id);

        return $static_component_behavior->to_array();

    }

    public function delete_content_behavior($mini_block_id){

        $static_component_behavior = new Static_component_behavior();

        $static_component_behavior->get_by_mini_block_id($mini_block_id);

        return $static_component_behavior->delete();

    }



}

?>