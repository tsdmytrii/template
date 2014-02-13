<?php

class Product_block extends DataMapper {

    public $table = 'product_blocks';
    public $has_many = array('product_block_language', 'product', 'placeholder');
    public static $ci;
    public $validation = array(
        array(
            'field' => 'id',
            'label' => 'id',
            'rules' => array('trim', 'numeric', 'max_length' => 11),
        ),
        array(
            'field' => 'row_quantity',
            'label' => 'Row quantity',
            'rules' => array('trim', 'numeric', 'max_length' => 3),
        ),
        array(
            'field' => 'column_quantity',
            'label' => 'Column quantity',
            'rules' => array('trim', 'numeric', 'max_length' => 2),
        )
    );

    function __construct($id = NULL) {
        parent::__construct($id);
        if (empty(self::$ci)) {
            self::$ci = &get_instance();
        }
    }

    public function get_product_block_by_id($c_id = false) {
        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return 403;

        $product_block = new Product_block();

        $product_block_id = self::$ci->input->post('id') ? self::$ci->input->post('id') : $c_id;

        $product_block->get_by_id($product_block_id);

        $result = $product_block->to_array();

        $product_block_language = new Product_block_language();

        $result['lang'] = $product_block_language->get_product_block_language($product_block->id);

        $product = new Product();

        $product->get_by_related($product_block);

        if ($product->exists()) {

            foreach ($product as $key => $p) {

                $product_lang = new Product_language();

                $result['products'][$key] = $p->to_array(array('id'));
                $result['products'][$key]['lang'] = $product_lang->get_product_language($p->id, 'name, language_id');

            }

        }

        return $result;
    }

    public function set_product_block() {
        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return 403;

        $product_block = new Product_block();

        if (self::$ci->input->post('product_block_id'))
            $product_block->get_by_id(self::$ci->input->post('product_block_id'));

        $product_block->row_quantity = self::$ci->input->post('row_quantity');
        $product_block->column_quantity = self::$ci->input->post('column_quantity');
        $product_block->category = self::$ci->input->post('category');
        $product_block->producer = self::$ci->input->post('producer');
        $product_block->short_description = self::$ci->input->post('short_description');
        $product_block->price = self::$ci->input->post('price');
        $product_block->sale_price = self::$ci->input->post('sale_price');
        $product_block->view = self::$ci->input->post('view');

        if ($product_block->save()) {

            $product_block_lang = new Product_block_language();

            $product_block_lang_id = $product_block_lang->set_product_block_language($product_block->id);

            if ($product_block_lang_id) {

                return array('product_block_id' => $product_block->id, 'lang_id' => $product_block_lang_id);

            } else
                return true;

        }

        else
            return false;
    }

    public function get_all_product_blocks() {
        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return 403;

        $product_block = new Product_block();

        $product_block->get();

        $result = false;

        foreach ($product_block as $key => $m) {

            $product_block_lang = new Product_block_language();

            $result[$key] = $m->to_array();

            $result[$key]['lang'] = $product_block_lang->get_product_block_language($m->id);
        }

        return $result;
    }

    public function delete_product_block($c_id = false) {
        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return 403;

        $product_block = new Product_block();

        $product_block_id = self::$ci->input->post('id') ? self::$ci->input->post('id') : $c_id;

        $product_block->get_by_id($product_block_id);

        $product_block_lang = new Product_block_language();
        $product_block_lang->delete_product_block_language($product_block_id);

        $product = new Product();

        $product->get_by_related($product_block);

        if ($product->exists()) {

            $product_block->delete($product->all);

        }

        $placeholder = new Placeholder();

        $placeholder->get_by_related($product_block);

        if ($placeholder->exists()){

            $product_block->delete($placeholder->all);

        }

        $product_block->delete();

        return $product_block_id;
    }

    public function product_block_autocomplete()
    {
        $product_block_language = new Product_block_language();
        $query = self::$ci->input->post('query');

        $product_block_language->like('name', $query)->get();
        foreach ($product_block_language as $m) {
            $suggestions[] = $m->name;
            $data[] = $m->product_block_id;
        }

        return array('query' => $query, 'suggestions' => $suggestions, 'data' => $data);
    }

    public function get_product_block_by_placeholder($placeholder_id){

        $placeholder = new Placeholder();

        $placeholder->get_by_id($placeholder_id);

        $product_block = new Product_block();

        $product_block->get_by_related($placeholder);

        $result = false;

        foreach ($product_block as $key => $m) {
            $product_block_lang = new Product_block_language();

            $result[$key] = $m->to_array(array('id'));

            $result[$key]['lang'] = $product_block_lang->get_product_block_language($m->id, 'name, language_id');
        }

        return $result;

    }

    /*
     * Product block relations and their functions
     */

    public function set_product_relation() {

        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return 403;

        $product = new Product();

        $product_block = new Product_block();

        $product->get_by_id(self::$ci->input->post('product_id'));

        $product_block->get_by_id(self::$ci->input->post('product_block_id'));

        return $product_block->save($product);

    }

    public function delete_product_relation() {

        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return 403;

        $product = new Product();

        $product_block = new Product_block();

        $product->get_by_id(self::$ci->input->post('product_id'));

        $product_block->get_by_id(self::$ci->input->post('product_block_id'));

        return $product_block->delete($product);
    }

    /*
     * User functions
     */

    public function get_product_block_to_user($placeholder_id = 1) {

        $product_block = new Product_block();

        $product_block->get_by_related_placeholder('id', $placeholder_id);

        $result = false;

        foreach ($product_block as $key => $m) {

            $product = new Product();

            $result[$key] = $m->to_array();

            $result[$key]['product'] = $product->get_products_by_product_block_to_user($m->id);

        }

        return $result;

    }

}

?>