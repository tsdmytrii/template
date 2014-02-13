<?php

class Banners extends MY_Controller {

    function __construct() {
        parent::__construct();
    }

    public function get_banner(){
        $banner = new Banner();

        $this->return_result($banner->get_banner());
    }

    public function set_banner(){
        $banner = new Banner();

        $this->return_result($banner->set_banner());
    }

    public function get_all_banner(){
        $banner = new Banner();

        $this->return_result($banner->get_banner());
    }

    public function delete_banner(){
        $banner = new Banner();

        $this->return_result($banner->delete_banner());
    }

    public function delete_img_banner() {
        $banner = new Banner_image();

        $this->return_result($banner->delete_img(), false);
    }

}

?>
