<?php

class Statics extends MY_Controller {

    function __construct() {
        parent::__construct();
    }

    public function set_static_component(){
        $static_component = new Static_component();

        $this->return_result($static_component->set_static_component());
    }

    public function get_static_component(){
        $static_component = new Static_component();

        $this->return_result($static_component->get_static_component());
    }

}

?>
