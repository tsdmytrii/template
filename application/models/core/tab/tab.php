<?php

class Tab extends DataMapper {

    public $table = 'tabs';
    public $has_many = array('tab_components', 'tab_languages', 'component_type');
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
            'rules' => array('required', 'trim', 'max_length' => 5),
        )
    );

    function __construct($id = NULL) {
        parent::__construct($id);
        if (empty(self::$ci)) {
            self::$ci = &get_instance();
        }
    }

    public function get_tab_by_name($name) {

        $tab = new Tab();

        $tab->where(array('name' => $name))->get();

        $result = $tab->to_array();

        $tab_lang = new Tab_language();

        $result['lang'] = $tab_lang->get_tab_name($tab->id, self::$ci->session->userdata('lang'));

        return $result;
    }

    public function get_all_tabs() {

        $lang_id = self::$ci->session->userdata('lang');

        $user = self::$ci->ion_auth->user()->row();

        $group = new Group();
        $group->where_related_user('id', $user->id)->get();

        $tab = new Tab();

        $result = false;

        if ($group->name === 'admin') {

            $tab->order_by('position')->get();

            foreach ($tab as $key => $t) {
                $result[$key] = $t->to_array();
                $tab_lang = new Tab_language();
                $result[$key]['lang'] = $tab_lang->get_tab_name($t->id, $lang_id);
                $result[$key]['child'] = $this->check_child_tabs($t->id, $tab);
            }

            return $result;

        }

        $component_function = new Component_function();
        $component_function->get_by_related($group);

        if ($component_function->exists()) {

            $component_type_array = array();

            foreach ($component_function as $c) {
                $component_type_array[] = $c->component_type_id;
            }

            $component_type = new Component_type();

            $component_type->where_in('id', $component_type_array)->get();

            $tab_ids = array();

            foreach ($component_type as $c) {
                if ($c->tab_id !== 0)
                    $tab_ids[] = $c->tab_id;
            }

            array_unique($tab_ids);

            $tab->where_in('id', $tab_ids)->get();

            $tab_array = array();

            foreach ($tab as $t) {
                if ($t->parent_id !== 0) {
                    $tab_array[] = $t->id;
                    $tab_array[] = $t->parent_id;
                }
            }

            $tab->clear();

            array_unique($tab_array);

            $tab->order_by('position')->where_in('id', $tab_array)->get();

            foreach ($tab as $key => $t) {
                $result[$key] = $t->to_array();
                $tab_lang = new Tab_language();
                $result[$key]['lang'] = $tab_lang->get_tab_name($t->id, $lang_id);
                $result[$key]['child'] = $this->check_child_tabs($t->id, $tab);
            }

            return $result;

        } else {

            return false;

        }

    }

    public function check_child_tabs($tab_id, $all_tabs) {

        $result = false;

        foreach ($all_tabs as $t) {

            if (intval($t->parent_id) == intval($tab_id))
                $result = true;
        }

        return $result;

    }


}

?>