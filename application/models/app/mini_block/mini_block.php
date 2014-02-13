<?php

class Mini_block extends DataMapper
{
    public $table = 'mini_blocks';
    public $has_one = array('component');
    public $has_many = array('mini_block_language', 'mini_block_image',  'mini_block_tooltip', 'placeholder');
    public static $ci;
    public $validation = array(
        array(
            'field' => 'id',
            'label' => 'id',
            'rules' => array('trim', 'numeric', 'max_length' => 5),
        )
    );

    function __construct($id = NULL) {
        parent::__construct($id);
        if (empty(self::$ci)) {
            self::$ci = &get_instance();
        }
    }

    public function get_mini_block_by_id($c_id = false)
    {
        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return 403;

        $result['result'] = false;
        $result['msg'] = false;

        $mini_block_id = self::$ci->input->post('id') ? self::$ci->input->post('id') : $c_id;

        if ($mini_block_id)
        {
            $mini_block = new Mini_block();
            $mini_block->get_by_id($mini_block_id);

            if ($mini_block->exists())
            {
                $result['result'] = $mini_block->to_array();

                $mini_block_language = new Mini_block_language();

                if ($mini_block->component_id)
                {
                    $component = new Component();
                    $component->get_by_id($mini_block->component_id);

                    $component_type = new Component_type();
                    $component_type->get_by_id($component->component_type_id);

                    $content = new $component_type->library;
                }

                $mini_block_img = new Mini_block_image();

                $mini_block_tooltip = new Mini_block_tooltip();

                $result['result']['lang'] = $mini_block_language->get_mini_block_language($mini_block->id);
                $result['result']['component'] = $mini_block->component_id ? $component->to_array() : false;
                $result['result']['content'] = $mini_block->component_id ? $content->get_content_behavior($mini_block->id) : false;
                $result['result']['bg'] = $mini_block_img->get_img($mini_block->id);
                $result['result']['tooltip'] = $mini_block_tooltip->get_img($mini_block->id);

            } else
            {
                $result['msg'] = 'No mini block with such id='.$mini_block_id.' exists.';
            }

        } else
        {
            $result['msg'] = 'Mini block id is not set.';
        }

        return $result;
    }

    public function set_mini_block($mini_block_id = false, $placeholder_id = false)
    {
        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return 403;

        $result['result'] = false;
        $result['msg'] = false;

        $placeholder_id = self::$ci->input->post('placeholder_id') ? self::$ci->input->post('placeholder_id') : $placeholder_id;
        $mini_block_id = self::$ci->input->post('mini_block_id') ? self::$ci->input->post('mini_block_id') : $mini_block_id;

        $mini_block = new Mini_block();

        if ($mini_block_id)
            $mini_block->get_by_id($mini_block_id);

        $mini_block->position = self::$ci->input->post('position');
        $mini_block->img = self::$ci->input->post('img');
        $mini_block->view = self::$ci->input->post('view');
        $mini_block->component_id = self::$ci->input->post('component_id') ? self::$ci->input->post('component_id') : 0;

        if ($mini_block->save())
        {
            if ($placeholder_id)
            {
                $placeholder = new Placeholder();
                $placeholder->get_by_id($placeholder_id);
                $placeholder->save($mini_block);
            }

            $mini_block_lang = new Mini_block_language();
            $mini_block_lang_id = $mini_block_lang->set_mini_block_language($mini_block->id);

            if (self::$ci->input->post('component_id'))
            {
                $component = new Component();
                $component->get_by_id(self::$ci->input->post('component_id'));

                $component_type = new Component_type();
                $component_type->get_by_id($component->component_type_id);

                $content = new $component_type->library;
                $behavior_id = $content->set_content_behavior($component->content_id, $mini_block->id);

                $result['result'] = array('mini_block_id' => $mini_block->id, 'lang_id' => $mini_block_lang_id, 'behavior_id' => $behavior_id);

            } else
                $result['result'] = true;

        } else
        {
            $result['msg'] = 'Failed to save mini block';
        }

        return $result;
    }

    public function get_all_mini_blocks()
    {
        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return 403;

        $result['result'] = false;
        $result['msg'] = false;
        
        $mini_block = new Mini_block();
        $mini_block->get();

        if ($mini_block->exists())
        {
            foreach ($mini_block as $key => $m)
            {
                $mini_block_lang = new Mini_block_language();
                $result['result'][$key] = $m->to_array();
                $result['result'][$key]['lang'] = $mini_block_lang->get_mini_block_language($m->id);
            }

        } else
        {
            $result['msg'] = 'No mini blocks exist in database.';
        }

        return $result;
    }

    public function delete_mini_block($c_id = false)
    {
        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return 403;

        $result['result'] = false;
        $result['msg'] = false;

        $mini_block_id = self::$ci->input->post('id') ? self::$ci->input->post('id') : $c_id;

        if ($mini_block_id)
        {
            $mini_block = new Mini_block();
            $mini_block->get_by_id($mini_block_id);

            if ($mini_block->exists())
            {
                if ($mini_block->component_id)
                {
                    $component = new Component();
                    $component->get_by_id($mini_block->component_id);

                    $component_type = new Component_type();
                    $component_type->get_by_id($component->component_type_id);

                    $content = new $component_type->library;
                    $content->delete_content_behavior($mini_block->id);
                }

                $mini_block_lang = new Mini_block_language();
                $mini_block_lang->delete_mini_block_language($mini_block_id);

                if ($mini_block->delete())
                {
                    $result['result'] = $mini_block_id;

                } else
                {
                    $result['msg'] = 'Mini block was not deleted.';
                }

            } else
            {
                $result['msg'] = 'No mini block with id='.$mini_block_id.' exist in database.';
            }

        } else
        {
            $result['msg'] = 'Mini block id is not set.';
        }

        return $result;
    }

    public function get_mini_block_to_user($placeholder_id = 1){

        $mini_block = new Mini_block();

        $mini_block->order_by('position')->get_by_related_placeholder('id', $placeholder_id);

        $result = false;

        foreach ($mini_block as $key => $m) {

            $mini_block_language = new Mini_block_language();

            $mini_block_img = new Mini_block_image();

            if ($m->component_id) {
                $component = new Component();
                $component->get_by_id($m->component_id);

                $component_type = new Component_type();
                $component_type->get_by_id($component->component_type_id);

                $content = new $component_type->library;
            }

            $mini_block_tooltip = new Mini_block_tooltip();

            $result[$key] = $m->to_array();
            $result[$key]['component'] = $m->component_id ? $component->to_array() : false;
            $result[$key]['lang'] = $mini_block_language->get_mini_block_language($m->id);
            $result[$key]['content'] = $m->component_id ? $content->get_content_behavior_to_user($m->id) : false;
            $result[$key]['bg'] = $mini_block_img->get_img($m->id);
            $result[$key]['tooltip'] = $mini_block_tooltip->get_img($m->id);

        }

        return $result;

    }

    public function mini_block_autocomplete(){
        $mini_block_language = new Mini_block_language();
        $query = self::$ci->input->post('query');

        $mini_block_language->like('name', $query)->get();
        foreach ($mini_block_language as $m) {
            $suggestions[] = $m->name;
            $data[] = $m->mini_block_id;
        }

        return array('query' => $query, 'suggestions' => $suggestions, 'data' => $data);
    }

    public function get_mini_block_by_placeholder($placeholder_id, $lang_id = false)
    {
        $result = false;

        $placeholder = new Placeholder();
        $placeholder->get_by_id($placeholder_id);

        $mini_block = new Mini_block();
        $mini_block->get_by_related($placeholder);

        foreach ($mini_block as $mini_block_key => $mini_block_item)
        {
            $result['result'][$mini_block_key] = $mini_block_item->to_array();

            $component = new Component();
            $result['result'][$mini_block_key]['component']= $component->get_component_by_id($mini_block->component_id);

            $mini_block_language = new Mini_block_language();
            $result['result'][$mini_block_key]['lang'] = $mini_block_language->get_mini_block_language($mini_block_item->id);
        }

        return $result;
    }

}