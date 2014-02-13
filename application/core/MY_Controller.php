<?php

class MY_Controller extends CI_Controller {

    function __construct() {
        parent::__construct();

        if ( ! $this->session->userdata('lang')) {
            $lang = new Language();
            $lang->get_default_lang();
            $this->session->set_userdata('lang', $lang->id);
            $this->session->set_userdata('lang_iso', $lang->iso_code);
        }

        //check if user tries to access restricted area
        if ($this->uri->segment(1) == 'admin' && $this->uri->segment(2) != 'login') {

            if ($this->authorized() === FALSE ) {
                $this->request_authorize();
            }

        }

        $this->load->library(array("user_agent"));
    }

    /*
     * Authorization functions
     */

    function authorized() {
        if ($this->ion_auth->logged_in() === TRUE && $this->ion_auth->is_admin() === TRUE)
            return true;
        else
            return false;
    }

    function redirect_to_main() {

        redirect(base_url() . 'admin/menu', 'location', '302');

    }

    function request_authorize() {
        //if user makes xhr, we just send header
        if ($this->input->is_ajax_request() === TRUE) {
            $this->output->set_status_header(401);
            //and clear output
            $this->output->set_output('');
        } else {
            redirect(base_url() . 'admin/login', 'location', 302);
        }
    }

    function authorize() {
        $this->ion_auth->login($this->input->post('login'), $this->input->post('password'));

        $this->redirect_to_main();
    }

    function check_auth() {
        if ($this->session->userdata('logged') === false) {
            redirect(base_url() . 'admin/login', 'location', 302);
        }
    }

    /*
     * Admin functions
     */

    function load_header($page, $title = false) {

        if ($title) {
            $data['layout_title'] = $title;
        } else {
            $data['layout_title'] = $this->get_title($page['lang']['name']);
        }
        $data['current'] = $page;
        $data['pref'] = $this->session->userdata('lang_iso');

        $lang = new Language();
        $langs = $lang->get_all_languages(false);
        $data['language'] = $langs['result'];

        $this->load->view('admin/header', $data);
    }

    function load_footer() {
        $this->load->view('admin/footer');
    }

    function load_menu($current) {
        $tab = new Tab();
        $data['menu'] = $tab->get_all_tabs();
        $user = new User();
        $data['user'] = $user->get_user();
        $data['current'] = $current;
        $this->load->view('admin/menu', $data);
    }

    function load_index() {
        $this->load->view('admin/index');
    }

    function get_title($page) {
        return 'Admin::' . ucfirst($page);
    }

    /*
     * User functions
     */

    function link_redirect($static_link) {

        if ($this->uri->uri_string() == $static_link) {

            $link = new Link();

            $link_for_comp = $link->get_link_by_url($new_link_main);

            if ($link_for_comp) {
                redirect(base_url() . $link_for_comp);
            }
        }

    }

    function is_ajax_request($result, $view, $static_link) {

        if ($this->input->is_ajax_request()) {

            return $this->return_result($result['data']);

        } else {

            $this->link_redirect($static_link);

            $all_data = $this->load_data();

            $result['all_data'] = $all_data['all_data'];
            $result['view'] = $view;
            $result['static_link'] = $static_link;

            $this->load_head($result, $view);

            $result = $result + $all_data;

            $this->load->view('user/content', $result);
        }
    }

    function load_head($data = '', $view = '') {
        $this->load->view('user/components/' . $view . '/head.php', $data);
    }

    function is_cashed_data($cashe_name) {
        if ($this->cache->file->get($cashe_name)) {
            $result = $this->cache->file->get($cashe_name);
        } else {
            $result = false;
        }

        return $result;
    }

    function load_data() {

//        $result['seo'] = $this->is_cashed_data('seo');
//        if ($result['seo'] == false) {
            $seo = new Seo();
            $result['seo'] = $seo->get_seo();
//            $this->cache->file->save('seo', $result['seo'], 5000);
//        }
//
//        $result['head'] = $this->is_cashed_data('head');
//        if ($result['head'] == false) {
            $head = new Menu_item();
            $result['head'] = $head->get_menu(1);
//            $this->cache->file->save('seo', $result['seo'], 5000);
//        }
//
//        $result['component_type'] = $this->is_cashed_data('component_type');
//        if ($result['component_type'] == false) {
            $component_type = new Component_type();
            $result['component_type'] = $component_type->get_display_component_types(array('name', 'client_controller'));
//            $this->cache->file->save('component_type', $result['component_type'], 5000);
//        }
//
//        $result['link'] = $this->is_cashed_data('link');
//        if ($result['link'] == false) {
            $link = new Link();
            $result['link'] = $link->get_link_to_user();
//            $this->cache->file->save('link', $result['link'], 5000);
//        }

        $result['logedIn'] = $this->ion_auth->logged_in();
        $result['mobile'] = $this->isMobile();

        if ($this->session->userdata('activation')) {
            $result['message'] = 'User Activated';
        }
        $this->session->unset_userdata('activation');

        if (isset($_SERVER['HTTP_REFERER']))
            $result['referer'] = $_SERVER['HTTP_REFERER'];
        else
            $result['referer'] = '';

        $all_data = json_encode(array('success' => true, 'data' => $result));

        $result['all_data'] = $all_data;

        return $result;
    }

    function check_link($data, $view, $static_link) {

        if ($data !== false) {

            $this->is_ajax_request($data, $view, $static_link);

        } else {

            $this->show_404_error();

        }

    }

    function show_404_error() {
        $this->output->set_status_header('404');

        $static_component = new Static_component();

        $static_link = 'staticcomp/' . $static_component_id . '/' . $menu_item_id . '/' . $main . '/' . $lang_id;

        $result['data'] = $static_component->get_static_component_for_user($static_component_id, $menu_item_id);
        $result['data']['trilan'] = $this->get_trilan_link($menu_item_id, $lang_id);

        $this->is_ajax_request($result, 'staticcomp', $menu_item_id, $main, $lang_id, $static_link);
    }

    /*
     * Common functions
     */

    function isMobile() {
        $mobile = 0;

        if ($this->agent->is_mobile())
            $mobile = 1;

        return $mobile;
    }

    function return_result($result, $msg = true) {
        if ($msg) {
            $this->output->set_output(json_encode(array(
                'success' => $result['result'] ? true : false,
                'message' => $result['msg'] ? $result['msg'] : false,
                'data' => $result['result'] ? $result['result'] : false
            )));
        } else {
            $this->output->set_output(json_encode(array(
                'success' => $result ? true : false,
                'message' => false,
                'data' => $result ? $result : false
            )));
        }
    }

}

?>
