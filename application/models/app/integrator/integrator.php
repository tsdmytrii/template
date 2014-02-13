<?php

class Integrator extends DataMapper
{
    public $table = 'integrators';
    public $has_many = array('placeholder');
    public static $ci;
    public $validation = array(
        array(
            'field' => 'id',
            'label' => 'id',
            'rules' => array('trim', 'numeric', 'max_length' => 5),
        ),
        array(
            'field' => 'component_id',
            'label' => 'Component ID',
            'rules' => array('trim', 'numeric', 'max_length' => 5),
        ),
        array(
            'field' => 'name',
            'label' => 'Name',
            'rules' => array('trim','max_length' => 255),
        )
    );

    function __construct($id = NULL)
    {
        parent::__construct($id);
        if (empty(self::$ci)) {
            self::$ci = &get_instance();
        }
    }

    public function get_integrators ($integrator_id = false, $get_placeholders = false)
    {
        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return array('result' => false, 'msg' => 403);

        $result['result'] = false;
        $result['msg'] = false;

        $integrator_id = self::$ci->input->post('id') ? self::$ci->input->post('id') : $integrator_id;

        $integrator = new Integrator();

        if ($integrator_id)
        {
            $integrator->get_by_id($integrator_id);

            if ($integrator->exists()) {
                $result['result'] = $integrator->to_array();
            } else {
                $result['msg'] = 'There is no such integrator.';
            }

        } else
        {
            $integrator->get();

            if ($integrator->exists()) {
                $integrator_array = $integrator->all_to_array();
                if (!isset($integrator_array[0])) {
                    $result['result'][0] = $integrator_array;
                } else {
                    $result['result'] = $integrator_array;
                }
            } else {
                $result['msg'] = 'There are no integrators.';
            }
        }

        if ( $result['result'] && self::$ci->input->post('get_placeholders') || $get_placeholders )
        {
            $placeholder = new Placeholder();
            $result['result'] = $placeholder->get_placeholders_for_integrator($result['result']);
        }

        return $result;
    }

    public function set_integrator ($integrator_id = false)
    {
        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return array('result' => false, 'msg' => 403);

        $result['result'] = false;
        $result['msg'] = false;

        $integrator_id = self::$ci->input->post('id') ? self::$ci->input->post('id') : $integrator_id;

        $integrator = new Integrator();

        if ($integrator_id)
            $integrator->get_by_id($integrator_id);

        $integrator->name = self::$ci->input->post('name');

        if ($integrator->save())
            $result['result'] = $integrator->id;
        else
            $result['msg'] = 'Integrator was not created.';

        return $result;
    }

    public function delete_integrator ($integrator_id = false, $placeholder_id = false, $delete_placeholder_relation_only = false)
    {
        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return array('result' => false, 'msg' => 403);

        $result['result'] = false;
        $result['msg'] = false;

        $integrator_id = self::$ci->input->post('id') ? self::$ci->input->post('id') : $integrator_id;
        $delete_placeholder_relation_only = self::$ci->input->post('delete_placeholder_relation_only') ? self::$ci->input->post('delete_placeholder_relation_only') : $delete_placeholder_relation_only;
        $placeholder_id = self::$ci->input->post('placeholder_id') ? self::$ci->input->post('placeholder_id') : $placeholder_id;

        $integrator = new Integrator();
        $integrator->get_by_id($integrator_id);

        if ($integrator->exists())
        {
            if ($this->unlink_placeholder_type($integrator, $placeholder_id) === true)
            {
                if ($delete_placeholder_relation_only)
                {
                    $result['result'] = true;
                }
            } elseif ($placeholder_id)
            {
                $result['msg'] = 'Cannot delete integrator relation with placeholder.';
            }

            if ($delete_placeholder_relation_only == false)
            {
                if ($integrator->delete())
                {
                    $result['result'] = true;

                } else
                {
                    $result['msg'] = 'Cannot delete integrator';
                }
            }

        } else
        {
            $result['msg'] = 'There is no such integrator.';
        }

        return $result;
    }

    public function unlink_placeholder_type ($integrator, $placeholder_id = false)
    {
        $result = false;
        $placeholder = new Placeholder();

        if ($placeholder_id)
        {
            $placeholder->get_by_id($placeholder_id);
            if ($placeholder->exists())
            {
                $result = true;
                if ( !$integrator->delete($placeholder) )
                {
                    $result = false;
                }
            }

        } else
        {
            $placeholder->get_by_related($integrator);
            if ($placeholder->exists())
            {
                $result = true;
                if ( !$integrator->delete($placeholder->all) )
                {
                    $result = false;
                }
            }
        }

        return $result;
    }


    public function get_placeholders_for_integrator ($integrator_array = false)
    {
        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return array('result' => false, 'msg' => 403);
    }

/*    public function integrator_autocomplete() {
        $component = new Component();
        $query = self::$ci->input->post('query');

        $component->like('name', $query)->get();

        foreach ($component as $c) {
            $suggestions[] = $c->name;
            $data[] = array(
                'id' => $c->id,
                'component_type_id' => $c->component_type_id
            );
        }

        return array('query' => $query, 'suggestions' => $suggestions, 'data' => $data);
    }*/
}