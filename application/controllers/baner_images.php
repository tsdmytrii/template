<?php

class Baner_images extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library(array('upload', 'plugins/image'));
    }

    public function set_img_banner() {

        $banner = new Banner_image();

        $this->return_result($banner->set_img());
    }

    public function delete_img_banner() {
        $banner = new Banner_image();

        $this->return_result($banner->delete_img(), false);
    }
}

?>
