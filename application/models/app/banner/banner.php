<?php

class Banner extends DataMapper {

    public $table = 'banners';
    public $has_many = array('banner_language', 'banner_image');
    public static $ci;
    public $validation = array(
        array(
            'field' => 'id',
            'label' => 'id',
            'rules' => array('trim', 'numeric', 'max_length' => 5),
        ),
        array(
            'field' => 'top',
            'label' => 'Top',
            'rules' => array('trim', 'max_length' => 5),
        ),
        array(
            'field' => 'left',
            'label' => 'left',
            'rules' => array('trim', 'max_length' => 5),
        ),
        array(
            'field' => 'width',
            'label' => 'width',
            'rules' => array('trim', 'max_length' => 5),
        ),
        array(
            'field' => 'height',
            'label' => 'height',
            'rules' => array('trim', 'max_length' => 5),
        )
    );

    function __construct($id = NULL) {
        parent::__construct($id);
        if (empty(self::$ci)) {
            self::$ci = &get_instance();
        }
    }

    public function set_from_default() {
        return 0;
    }

    public function set_banner() {
        if (self::$ci->access->check_access(__FUNCTION__) == false) {
            return array('result' => false, 'msg' => 403);
        }

        self::$ci->config->load('my_config');

        $result['result'] = false;
        $result['msg'] = false;

        $banner = new Banner();

        if (self::$ci->input->post('id')) {
            $banner->get_by_id(self::$ci->input->post('id'));
        }

        $banner->display = self::$ci->input->post('display') ? self::$ci->input->post('display') : 0;
        $banner->position = self::$ci->input->post('position');
        $banner->date = date(self::$ci->config->item('date') . ' ' . self::$ci->config->item('time'));
        $banner->top = self::$ci->input->post('top');
        $banner->left = self::$ci->input->post('left');
        $banner->width = self::$ci->input->post('width');
        $banner->height = self::$ci->input->post('height');

        if ($banner->save()){
            $banner_lang = new Banner_language();

            $lang_save = $banner_lang->set_banner_lang($banner->id);

            if ($lang_save['msg']) {
                $result['msg'] = $lang_save['msg'];
            }

            $result['result'] = array(
                'baner_id' => $banner->id,
                'baner_lang_id' => $lang_save['result'],
            );
        } else {
            if ($banner->valid) {
                $result['msg'] = 'Banner image insert or update failure';
            } else {
                $result['msg'] = $banner->error->string;
            }
        }

        return $result;
    }

    public function get_banner($ban_id = false) {

        if (self::$ci->access->check_access(__FUNCTION__) == false) {
            return array('result' => false, 'msg' => 403);
        }

        $result['result'] = false;
        $result['msg'] = false;

        $banner_id = self::$ci->input->post('banner_id') ? self::$ci->input->post('banner_id') : $ban_id;

        $banner = new Banner();

        if ($banner_id) {

            $banner->get_by_id($banner_id);

            if ($banner->exists()) {
                $banner_lang = new Banner_language();

                $banner_img = new Banner_image();

                $result['result'] = $banner->to_array();
                $result['result']['lang'] = $banner_lang->get_banner_lang($banner_id);
                $result['result']['img'] = $banner_img->get_img($banner_id);
            } else {
                $result['msg'] = 'There are no such banner.';
            }

        } else {

            $banner->get();

            if ($banner->exists()) {
                foreach ($banner as $key => $b) {
                    $banner_lang = new Banner_language();

                    $banner_img = new Banner_image();

                    $result['result'][$key] = $b->to_array();
                    $result['result'][$key]['lang'] = $banner_lang->get_banner_lang($b->id);
                    $result['result'][$key]['img'] = $banner_img->get_img($b->id);
                }
            } else {
                $result['msg'] = 'There are no banners.';
            }

        }

        return $result;
    }

    public function get_displayed_banners($type = 0) {
        $banner = new Banner();

        $where = array('display' => 1, 'type' => $type);

        $banner->where($where)->get();

        $result = false;

        foreach ($banner as $b) {
            $ban = new Banner();

            $result[] = $ban->get_banner($b->id, true);
        }

        return $result;
    }

    public function delete_banner($banner_id = false) {

        if (self::$ci->access->check_access(__FUNCTION__) == false) {
            return array('result' => false, 'msg' => 403);
        }

        $result['result'] = false;
        $result['msg'] = false;

        $banner_id = self::$ci->input->post('banner_id') ? self::$ci->input->post('banner_id') : $banner_id;

        if ($banner_id) {
            $banner = new Banner();

            $banner->get_by_id($banner_id);

            if ($banner->exists()) {

                $banner_image = new Banner_image();

                $banner_image->delete_img($banner_id);

                $banner_lang = new Banner_language();

                $banner_lang->delete_banner_lang($banner_id);

                if ($banner->delete()) {
                    $result['result'] = true;
                } else {
                    $result['msg'] = 'Error deleting banner.';
                }

            } else {
                $result['msg'] = 'There are no such banner';
            }

        } else {
            $result['msg'] = 'There are no banner id.';
        }


        return $result;
    }

}

?>