<?php

class User extends DataMapper {

    public $table = 'users';
    public $has_many = array('group');
    public static $ci;
    public $validation = array(
        array(
            'field' => 'id',
            'label' => 'id',
            'rules' => array('trim', 'numeric', 'max_length' => 11),
        ),
        array(
            'field' => 'first_name',
            'label' => 'first_name',
            'rules' => array('trim', 'max_length' => 50),
        ),
        array(
            'field' => 'last_name',
            'label' => 'last_name',
            'rules' => array('trim', 'max_length' => 50),
        ),
        array(
            'field' => 'city',
            'label' => 'city',
            'rules' => array('trim', 'max_length' => 50),
        ),
        array(
            'field' => 'address',
            'label' => 'address',
            'rules' => array('trim', 'max_length' => 100),
        ),
        array(
            'field' => 'phone',
            'label' => 'phone',
            'rules' => array('trim', 'max_length' => 40),
        )
    );

    function __construct($id = NULL) {
        parent::__construct($id);
        if (empty(self::$ci)) {
            self::$ci = &get_instance();
        }
    }

    function get_all_user() {

        if (self::$ci->access->check_access(__FUNCTION__) == false) {
            return array('result' => false, 'msg' => 403);
        }

        $result['result'] = false;
        $result['msg'] = false;

        $user = new User();

        $result['result']['quantity'] = $user->count();

        $user->clear();

        if (self::$ci->input->post('limit'))
        {
            $user->limit(self::$ci->input->post('limit'));
        }

        if (self::$ci->input->post('offset'))
        {
            $user->offset(self::$ci->input->post('offset'));
        }

        $user->get();

        if ($user->exists()) {
            $result['result']['users'] = $user->all_to_array();
        } else {
            $result['msg'] = 'There are no users';
        }


        return $result;
    }

    function check_email($email, $from_user = false) {

        if ($from_user) {
            $user_id = self::$ci->session->userdata('user_id');
        } else {
            $user_id = self::$ci->input->post('user_id') ? self::$ci->input->post('user_id') : false;
        }

        $check = self::$ci->ion_auth->email_check($email);

        if ($user_id && $check) {

            $user = new User();

            $user->where(array('id' => $user_id, 'email' => $email))->get();

            return $user->exists() ? false : true;

        } else {

            return $check;

        }

    }

    function set_user() {

        $user_id = self::$ci->input->post('id') ? self::$ci->input->post('id') : 0;

        if ($user_id && !empty($user_id)) {
            return $this->update_user();
        }
        else
            return $this->add_user();
    }

    function add_user() {


        $user_id = self::$ci->ion_auth->register(self::$ci->input->post('username'), self::$ci->input->post('password'), self::$ci->input->post('email'), array(
                'active' => 1,
                'first_name' => (self::$ci->input->post('name')) ? self::$ci->input->post('name') : '',
                'last_name' => (self::$ci->input->post('surname')) ? self::$ci->input->post('surname') : '',
                'phone' => (self::$ci->input->post('phone')) ? self::$ci->input->post('phone') : '',
                'date_active' => date('d.m.Y H:i:s')
            ));

        $result['result'] = $user_id;
        $result['msg'] = false;

        return $result;

    }

    function update_user() {

        $result['result'] = false;
        $result['msg'] = false;

        $user_id = self::$ci->input->post('id') ? self::$ci->input->post('id') : self::$ci->session->userdata('user_id');

        $group_id = self::$ci->input->post('group_id') ? self::$ci->input->post('group_id') : 0;

        $password = self::$ci->input->post('password');

        $data = array(
            'username' => self::$ci->input->post('username'),
            'email' => self::$ci->input->post('email'),
            'active' => 1,
            'first_name' => (self::$ci->input->post('name')) ? self::$ci->input->post('name') : '',
            'last_name' => (self::$ci->input->post('surname')) ? self::$ci->input->post('surname') : '',
            'phone' => (self::$ci->input->post('phone')) ? self::$ci->input->post('phone') : ''
        );

        if ($password && !empty($password)) {
            $data['password'] = $password;
        }

        if ($group_id && $group_id !== 0) {

            $user = new User();
            $user->get_by_id($user_id);

            $group = new Group();

            $group->get_by_related($user);

            if ($group->exists()) {
                $user->delete($group->all);
                $group->clear();
            }

            $group->get_by_id($group_id);

            $user->save($group);

        }

        if (self::$ci->ion_auth->update($user_id, $data)) {
            $result['result'] = $user_id;
        } else {
            $result['msg'] = 'Update error';
        }

        return $result;

    }

    function get_user($user_id = false) {

        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return array('result' => false, 'msg' => 403);

        $result['result'] = false;
        $result['msg'] = false;

        if (!$user_id)
            $user_id = self::$ci->session->userdata('user_id');

        $user = new User();

        $user->get_by_id($user_id);

        if ($user->exists()) {

            $result['result'] = $user->to_array();

            $group = new Group();
            $group->get_by_related_user('id', $user_id);

            $result['result']['group'] = $group->exists() ? $group->to_array() : false;

        } else {
            $result['msg'] = 'There is no user';
        }

        return $result;
    }

    function delete_user() {

        if (self::$ci->access->check_access(__FUNCTION__) == false)
            return array('result' => false, 'msg' => 403);

        $result['result'] = false;
        $result['msg'] = false;

        $user_id = self::$ci->input->post('id');

        $user = new User();

        $user->get_by_id($user_id);

        $group = new Group();

        $group->get_by_related($user);

        if ($group->exists()) {

            $user->delete($group->all);

        }

        if ($user->delete()) {
            $result['result'] = true;
        } else {
            $result['msg'] = 'Error deleting user';
        }

        return $result;

    }

    function set_activation_user($user_id, $prognose_line_id, $status = 1, $type = 1) {

        $user = new User();

        $user->get_by_id($user_id);

        $activation_date = new DateTime($user->date_active);

        if ($status == 1){
            if ($type == 1)
                $activation_date->modify('+1 month');

            $data['chosen_prognose_line'] = $prognose_line_id;
            $data['current_prognose_line'] = $prognose_line_id;
        }
        else{

            $invoice = new Invoice();

            $last_invoice = $invoice->get_last_user_invoice($user_id);

            if ($type == 1)
                $activation_date->modify('-1 month');

            if ($last_invoice !== false) {
                $data['current_prognose_line'] = $last_invoice['prognose_line_id'];
            } else {
                $data['chosen_prognose_line'] = 0;
                $data['current_prognose_line'] = 0;
            }
        }

        $data['date_active'] = $activation_date->format('Y-m-d H:i:s');
        return self::$ci->ion_auth->update($user_id, $data);
    }

    function user_autocomplete() {
        $user = new User();

        $query = self::$ci->input->post('query');

        $user->like('first_name', $query)
                ->or_like('last_name', $query)
                ->or_like('email', $query)
                ->get();

        foreach ($user as $u) {
            $suggestions[] = $u->first_name . ' ' . $u->last_name . ' ' . $u->email;
            $data[] = $u->id;
        }

        return array('query' => $query, 'suggestions' => $suggestions, 'data' => $data);

    }


    /*
     * ------------------------------------------------------------------------- User functions
     */

    public function get_user_info($user_id) {

        if ($user_id) {

            $user = new User();

            $user->get_by_id($user_id);

            return $user->to_array(array('email', 'first_name', 'last_name', 'born_date', 'city', 'address', 'home_numb', 'phone', 'treatment', 'degree'));

        } else {

            return false;

        }

    }

    public function set_user_info() {
        $user = new User();

        $user_id = self::$ci->session->userdata('user_id');

        $user->get_by_id($user_id);

        if ($user->exists()) {

            $user->treatment = self::$ci->input->post('treatment');
            $user->degree = self::$ci->input->post('degree');
            $user->first_name = self::$ci->input->post('name');
            $user->last_name = self::$ci->input->post('surname');
            $user->born_date = self::$ci->input->post('born_date');
            $user->city = self::$ci->input->post('city');
            $user->address = self::$ci->input->post('address');
            $user->phone = self::$ci->input->post('address');

            return $user->save();

        } else {
            return false;
        }
    }

    public function set_user_login_info() {

        $user = new User();

        $user_id = self::$ci->session->userdata('user_id');

        $user->get_by_id($user_id);

        if ($user->exists()) {

            $user->email = self::$ci->input->post('email');
            $user->password = self::$ci->ion_auth->hash_password(self::$ci->input->post('password'), $user->salt);

            return $user->save();

        } else {
            return false;
        }

    }

}

?>