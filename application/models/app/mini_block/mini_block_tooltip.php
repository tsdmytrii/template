<?php

class Mini_block_tooltip extends DataMapper {

    public $table = 'mini_block_tooltips';
    public $has_one = array('mini_block');
    public static $directory = './uploads/images';
    public static $ci;
    public $validation = array(
        array(
            'field' => 'id',
            'label' => 'id',
            'rules' => array('trim', 'numeric', 'max_length' => 8)
        ),
        array(
            'field' => 'mini_block_id',
            'label' => 'id',
            'rules' => array('trim', 'numeric', 'max_length' => 8)
        ),
        array(
            'field' => 'name',
            'label' => 'Logo name',
            'rules' => array('trim', 'min_length' => 3, 'max_length' => 100)
        )
    );

    function __construct() {
        parent::__construct();
        if (empty(self::$ci)) {
            self::$ci = &get_instance();
        }
        self::$ci->load->library(array('plugins/image'));
    }

    public function set_img() {
        $mini_block_tooltip = new Mini_block_tooltip();

        $check_mini_block = new Mini_block_tooltip();
        $check_mini_block->get_by_mini_block_id(self::$ci->input->post('mini_block_id'));

        foreach ($check_mini_block as $c) {
            $this->delete_img('', $c->id);
        }

        $result_tooltip = Image::upload_tmp_image(self::$directory, '', 'Filedata');
        $mini_block_tooltip->name = $result_tooltip['filename'];
        $mini_block_tooltip->mini_block_id = self::$ci->input->post('mini_block_id');

        $mini_block_tooltip->save();

        return $mini_block_tooltip->to_array();

    }

    public function get_img($a_id = false) {
        $mini_block_tooltip = new Mini_block_tooltip();

        $mini_block_id = self::$ci->input->post('mini_block_id') ? self::$ci->input->post('mini_block_id') : $a_id;

        $mini_block_tooltip->get_by_mini_block_id($mini_block_id);

        if ($mini_block_tooltip->id) {

            $result = $mini_block_tooltip->to_array();

            return $result;
        } else {
            return false;
        }

    }

    public function delete_img($mini_block_id = '', $img_id = false) {
        $mini_block_tooltip = new Mini_block_tooltip();

        $tooltip_id = self::$ci->input->post('id') ? self::$ci->input->post('id') : $img_id;

        if ($tooltip_id) {

            $mini_block_tooltip->get_by_id($tooltip_id);

            Image::unlink_image(self::$directory . '/' . $mini_block_tooltip->name);

            $mini_block_tooltip->delete();

            return array('img_id' => $tooltip_id);
        } else {
            $mini_block_tooltip->get_by_mini_block_id($mini_block_id);

            foreach ($mini_block_tooltip as $a) {
                if ($a->name !== null && $a->name !== '') {
                    Image::unlink_image(self::$directory . '/' . $a->name);
                    $a->delete();
                }
            }

            return true;
        }
    }

}

?>
