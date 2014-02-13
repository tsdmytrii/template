<?php

class Links extends MY_Controller {

    function __construct() {
        parent::__construct();
    }

    public function check_link(){
        $link = new Link();

        echo $link->check_link();
    }

}

?>
