<?php

class Placeholder extends DataMapper {

    public $table = 'placeholders';
    public $has_one = array('integrator');
    public $has_many = array('placeholder_attribute', 'mini_block', 'product_block');
    public static $ci;
    public $validation = array(
        array(
            'field' => 'id',
            'label' => 'id',
            'rules' => array('trim', 'numeric', 'max_length' => 11),
        ),
        array(
            'field' => 'name',
            'label' => 'Name',
            'rules' => array('required', 'trim', 'max_length' => 200),
        ),
        array(
            'field' => 'description',
            'label' => 'Description',
            'rules' => array('required', 'trim', 'max_length' => 2000),
        ),
        array(
            'field' => 'identificator',
            'label' => 'Identificator',
            'rules' => array('required', 'trim', 'max_length' => 20),
        ),
        array(
            'field' => 'width',
            'label' => 'Width',
            'rules' => array('trim', 'max_length' => 6),
        ),
        array(
            'field' => 'height',
            'label' => 'Height',
            'rules' => array('trim', 'max_length' => 6),
        ),
        array(
            'field' => 'width_param',
            'label' => 'Width percent',
            'rules' => array('required', 'trim', 'max_length' => 3),
        ),
        array(
            'field' => 'height_param',
            'label' => 'Height percent',
            'rules' => array('required', 'trim', 'max_length' => 3),
        ),
        array(
            'field' => 'height_param',
            'label' => 'Height percent',
            'rules' => array('trim', 'max_length' => 300),
        ),
    );

    function __construct($id = NULL) {
        parent::__construct($id);
        if (empty(self::$ci)) {
            self::$ci = &get_instance();
        }
    }

    public function set_from_default() {

        $placeholder = new Placeholder();

        $placeholder->name = 'New placeholder';
        $placeholder->description = 'New placeholder description';

        if ($placeholder->save()) {
            return $placeholder->id;
        }
        else
            return false;
    }

    public function set_placeholder ( $placeholder_id = false )
    {
        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return array('result' => false, 'msg' => 403);

        $result['result'] = false;
        $result['msg'] = false;

        $placeholder = new Placeholder();

        $placeholder_id = self::$ci->input->post('placeholder_id') ? self::$ci->input->post('placeholder_id') : $placeholder_id;

        if ($placeholder_id)
            $placeholder->get_by_id($placeholder_id);

        $placeholder->name = self::$ci->input->post('name');
        $placeholder->description = self::$ci->input->post('description');
        $placeholder->identificator = self::$ci->input->post('identificator');
        $placeholder->position = self::$ci->input->post('position');
        $placeholder->width = self::$ci->input->post('width');
        $placeholder->width_param = self::$ci->input->post('width_param');
        $placeholder->height = self::$ci->input->post('height');
        $placeholder->height_param = self::$ci->input->post('height_param');
        $placeholder->view = self::$ci->input->post('view');
        $placeholder->integrator_id = self::$ci->input->post('integrator_id');

        $this->make_css_file();

        if ($placeholder->save())
            $result['result'] = $placeholder->id;
        else
            $result['msg'] = 'Placeholder was not created.';

        return $result;
    }

    public function get_all_placeholders($integrator_id = false, $get_placeholder_additional_data = false)
    {
        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return array('result' => false, 'msg' => 403);

        $result['result'] = false;
        $result['msg'] = false;

        $placeholder = new Placeholder();

        if ($integrator_id) {
            $placeholder->get_by_integrator_id($integrator_id);
        } else {
            $placeholder->get();
        }

        if ($placeholder->exists())
        {
            $result['result'] = $placeholder->all_to_array();

            if ($get_placeholder_additional_data)
            {
                foreach ($result['result'] as $placeholder_key => $placeholder_value)
                {
                    $result['result'][$placeholder_key] += $this->get_placeholder_additional_data($placeholder_value['id']);
                }
            }

        } else
        {
            $result['msg'] = 'There are no placeholders.';
        }

        return $result;
    }

    public function get_placeholder_additional_data ($placeholder_id)
    {
        $placeholder_attribute = new Placeholder_attribute();
        $result['attributes'] = $placeholder_attribute->get_placeholder_attribute($placeholder_id);

        $mini_block = new Mini_block();
        $result['mini_blocks'] = $mini_block->get_mini_block_by_placeholder($placeholder_id);

        $product_block = new Product_block();
        $result['product_blocks'] = $product_block->get_product_block_by_placeholder($placeholder_id);

        return $result;
    }

    public function get_placeholder($id = false, $integrator_id = false)
    {
        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return array('result' => false, 'msg' => 403);

        $result['result'] = false;
        $result['msg'] = false;

        $placeholder = new Placeholder();
        $placeholder_id = self::$ci->input->post('id') ? self::$ci->input->post('id') : $id;

        if ($placeholder_id != false)
        {
            $result['result'] = $placeholder->get_by_id($placeholder_id)->to_array();
        }

        if ($integrator_id)
        {
            $result['result'] = $placeholder->get_by_integrator_id($integrator_id)->to_array();
            $placeholder_id = $result['id'];
        }

        if ($placeholder_id)
        {
            $result['result'] += $this->get_placeholder_additional_data($placeholder_id);

        } else
        {
            $result['msg'] = 'There is no such placeholder.';
        }

        return $result;
    }

    public function delete_placeholder() {

        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return array('result' => false, 'msg' => 403);

        $placeholder = new Placeholder();

        $placeholder_id = self::$ci->input->post('placeholder_id');

        $placeholder->get_by_id($placeholder_id);

        $placeholder_attribute = new Placeholder_attribute();

        $placeholder_attribute->delete_placeholder_attribute($placeholder->id);

        return $placeholder->delete();
    }

    /*
     * Mini block
     */

    public function set_placeholder_mini_block($placeholder_id = false, $mini_block_id = false)
    {
        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return array('result' => false, 'msg' => 403);

        $result['result'] = false;
        $result['msg'] = false;

        $placeholder_id = self::$ci->input->post('placeholder_id') ? self::$ci->input->post('placeholder_id') : $placeholder_id;
        $mini_block_id = self::$ci->input->post('mini_block_id') ? self::$ci->input->post('mini_block_id') : $mini_block_id;

        if ($placeholder_id && $mini_block_id)
        {
            $placeholder = new Placeholder();
            $placeholder->get_by_id( $placeholder_id );

            $mini_block = new Mini_block();
            $mini_block->get_by_id( $mini_block_id );

            if ($placeholder->exists() && $mini_block->exists())
            {
                if ($save_result = $placeholder->save($mini_block))
                {
                    $result['result'] = $save_result;

                } else
                {
                    $result['msg'] = 'Relation with mini block was not saved.';
                }

            } else
            {
                $result['msg'] = 'There is no such placeholder or mini block.';
            }

        } else
        {
            $result['msg'] = 'Empty placeholder_id or empty mini_block_id was received.';
        }

        return $result;
    }

    public function delete_placeholder_miniblock($placeholder_id = false, $mini_block_id = false)
    {
        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return array('result' => false, 'msg' => 403);

        $result['result'] = false;
        $result['msg'] = false;

        $placeholder_id = self::$ci->input->post('placeholder_id') ? self::$ci->input->post('placeholder_id') : $placeholder_id;
        $mini_block_id = self::$ci->input->post('mini_block_id') ? self::$ci->input->post('mini_block_id') : $mini_block_id;

        if ($placeholder_id && $mini_block_id)
        {
            $placeholder = new Placeholder();
            $placeholder->get_by_id($placeholder_id);

            $mini_block = new Mini_block();
            $mini_block->get_by_id($mini_block_id);

            if ($placeholder->exists() && $mini_block->exists())
            {
                if ($delete_result = $placeholder->delete($mini_block))
                {
                    $result['result'] = $delete_result;

                } else
                {
                    $result['msg'] = 'Relation with mini block was not deleted.';
                }

            } else
            {
                $result['msg'] = 'There is no such placeholder or mini block.';
            }

        } else
        {
            $result['msg'] = 'Relation with mini block was not deleted.';
        }

        return $result;
    }

    public function make_css_file() {

        self::$ci->load->helper('file');

        $placeholder = new Placeholder();

        $placeholder->get();

        $data = '';

        foreach ($placeholder as $p) {
            $placeholder_attr = new Placeholder_attribute();

            $placeholder_attr->get_by_placeholder_id($p->id);

            $data .= '#' . $p->identificator . ' {'."\n";

            if ($p->width_param == 0) {
                $data .= '    width: ' . $p->width . '%;'."\n";
            }

            if ($p->height_param == 0) {
                $data .= '    height: ' . $p->height . '%;'."\n";
            }

            foreach ($placeholder_attr as $attr) {
                $data .= '    ' . $attr->key . ': ' . $attr->value . ';'."\n";
            }

            $data .= '}'."\n";
        }

        return write_file('./js/components/user/core/css/core.css', $data);
    }


    /*
     * Product Block
     */

    public function set_placeholder_product_block($placeholder_id = false, $product_block_id = false)
    {
        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return array('result' => false, 'msg' => 403);

        $result['result'] = false;
        $result['msg'] = false;

        $placeholder_id = self::$ci->input->post('placeholder_id') ? self::$ci->input->post('placeholder_id') : $placeholder_id;
        $product_block_id = self::$ci->input->post('product_block_id') ? self::$ci->input->post('product_block_id') : $product_block_id;

        if ($placeholder_id && $product_block_id)
        {
            $placeholder = new Placeholder();
            $placeholder->get_by_id( $placeholder_id );

            $product_block = new Product_block();
            $product_block->get_by_id( $product_block_id );

            if ($placeholder->exists() && $product_block->exists())
            {
                if ($save_result = $placeholder->save($product_block))
                {
                    $result['result'] = $save_result;

                } else
                {
                    $result['msg'] = 'Relation with product block was not saved.';
                }

            } else
            {
                $result['msg'] = 'There is no such placeholder or product block.';
            }

        } else
        {
            $result['msg'] = 'Empty placeholder_id or empty product_block_id was received.';
        }

        return $result;
    }

    public function delete_placeholder_product_block( $placeholder_id = false, $product_block_id = false )
    {
        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return array('result' => false, 'msg' => 403);

        $result['result'] = false;
        $result['msg'] = false;

        $placeholder_id = self::$ci->input->post('placeholder_id') ? self::$ci->input->post('placeholder_id') : $placeholder_id;
        $product_block_id = self::$ci->input->post('product_block_id') ? self::$ci->input->post('product_block_id') : $product_block_id;

        if ($placeholder_id && $product_block_id)
        {
            $placeholder = new Placeholder();
            $placeholder->get_by_id( $placeholder_id );

            $product_block = new Product_block();
            $product_block->get_by_id( $product_block_id );

            if ($placeholder->exists() && $product_block->exists())
            {
                if ($delete_result = $placeholder->delete($product_block))
                {
                    $result['result'] = $delete_result;

                } else
                {
                    $result['msg'] = 'Relation with product block was not deleted.';
                }

            } else
            {
                $result['msg'] = 'There is no such placeholder or product block.';
            }

        } else
        {
            $result['msg'] = 'Empty placeholder_id or empty product_block_id was received.';
        }

        return $result;
    }

    public function set_integrator_placeholder ()
    {
        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return array('result' => false, 'msg' => 403);

        $result['result'] = false;
        $result['msg'] = false;

        $placeholder = new Placeholder();
        $placeholder->get_by_id(self::$ci->input->post('placeholder_id'));

        $placeholder->integrator_id = self::$ci->input->post('integrator_id');

        if ($placeholder->save())
        {
            $result['result'] = $placeholder->id;

        } else
        {
            if ($placeholder->valid) {
                $result['msg'] = 'Operation failed. Try again.';
            } else {
                $result['msg'] = $placeholder->error->string;
            }
        }

        return $result;
    }

    public function get_placeholders_for_integrator ($integrator_array)
    {
        if (!isset($integrator_array[0])) {
            $integrators[0] = $integrator_array;
        } else {
            $integrators = $integrator_array;
        }

        $result = $integrators;

        foreach ($integrators as $integrator_key => $integrator_item)
        {
            $result[$integrator_key]['placeholders'] = $this->get_all_placeholders($integrator_item['id'], true);
        }

        return $result;
    }

/*    public function delete_integrator_placeholder ()
    {
        include(APPPATH . 'language/'.self::$ci->session->userdata('lang_iso').'/inter_lang.php');

        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return array('result' => false, 'msg' => 403);

        $component_function = new Component_function();

        $component_function_id = self::$ci->input->post('component_function_id') ? self::$ci->input->post('component_function_id') : $component_function_id;

        $component_function->get_by_id($component_function_id);

        $group = new Group();

        $group->get_by_related($component_function);

        if ($group->exists()){
            $component_function->delete($group->all);
        }

        if ($component_function->delete()) {
            $result['result'] = true;
            $result['msg'] = false;
        } else {
            $result['result'] = false;
            $result['msg'] = $localization['comp_type']['remove_func_error'];
        }

        return $result;
    }*/

    /*
     * USER
     */

    public function get_placeholders_for_user() {

        $placeholder = new Placeholder();

        $placeholder->order_by('position')->get();

        $result = false;

        foreach ($placeholder as $key => $p){

            $mini_block = new Mini_block();
            $product_block = new Product_block();

            $result[$key] = $p->to_array();
            $result[$key]['mini_block'] = $mini_block->get_mini_block_to_user($p->id);
            $result[$key]['product_block'] = $product_block->get_product_block_to_user($p->id);

        }

        return $result;

    }

    public function get_placeholder_autocomplete()
    {
        $suggestions = false;
        $data = false;

        $placeholder = new Placeholder();
        $query = self::$ci->input->post('query');

        $placeholder->like('name', $query)->get();
        foreach ($placeholder as $placeholder_item) {
            $suggestions[] = $placeholder_item->name;
            $data[] = $placeholder_item->id;
        }

        return array('query' => $query, 'suggestions' => $suggestions, 'data' => $data);
    }

}