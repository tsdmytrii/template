<?php

class Article_behavior extends DataMapper {

    public $table = 'article_behaviors';
    public $has_one = array('article');
    public static $ci;
    public $validation = array(
        array(
            'field' => 'id',
            'label' => 'id',
            'rules' => array('trim', 'numeric', 'max_length' => 5),
        ),
        array(
            'field' => 'article_id',
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

    public function set_content_behavior($article_id, $mini_block_id) {

        $article_behavior = new Article_behavior();

        if (self::$ci->input->post('behavior_id'))
            $article_behavior->get_by_id(self::$ci->input->post('behavior_id'));
        else{
            $article_behavior->get_by_mini_block_id($mini_block_id);
        }

        $article_behavior->article_id = $article_id;
        $article_behavior->mini_block_id = $mini_block_id;
        $article_behavior->quantity = self::$ci->input->post('quantity');

        $article_behavior->save();

        return $article_behavior->id;

    }

    public function get_content_behavior($mini_block_id){

        $article_behavior = new Article_behavior();

        $article_behavior->get_by_mini_block_id($mini_block_id);

        return $article_behavior->to_array();

    }

    public function delete_content_behavior($mini_block_id){

        $article_behavior = new Article_behavior();

        $article_behavior->get_by_mini_block_id($mini_block_id);

        return $article_behavior->delete();

    }



}

?>