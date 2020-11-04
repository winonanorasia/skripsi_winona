<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('M_users', 'Users');
    }

    public function index()
    {
        $data['title'] = 'Login';
        $this->load->view('auth/login', $data);
    }

    public function login()
    {
        if ($this->session->userdata('login') === true) redirect(base_url());

        $data['page'] = $data['title'] = 'Login';

        $this->form_validation->set_rules($this->Users->rules());
        if ($this->form_validation->run() == TRUE) {
            $where = array(
                'u_username' => $this->input->post('username'),
                'u_password' => md5($this->input->post('password'))
            );
            $result = $this->Users->cekLogin('users', $where)->result();
            if (!empty($result)) {
                $data_session = array(
                    'u_id' => $result[0]->u_id,
                    'u_username' => $result[0]->u_username,
                    'u_email' => $result[0]->u_email,
                    'u_role' => $result[0]->u_role,
                    'login' => true
                );
                $this->session->set_userdata($data_session);
                redirect(base_url());
            } else {
                $data['error'] = 'Username dan Password salah';
                $this->load->view('auth/login', $data);
            }
        } else {
            $this->load->view('auth/login', $data);
        }
    }

    public function register()
    {
        if ($this->session->userdata('login') === true) redirect(base_url());

        $data['page'] = $data['title'] = 'Register';
        $this->form_validation->set_rules($this->Users->rules());
        if ($this->form_validation->run() == TRUE) {
            $user_name = $this->input->post('user_name');
            $user_email = $this->input->post('user_email');
            $user_password = $this->input->post('user_password');
            $where = array(
                'user_email' => $user_email
            );
            $result = $this->Users->cekUser('users', $where)->result();
            if (empty($result)) {
                $data_reg = array(
                    'user_name' => $user_name,
                    'user_email' => $user_email,
                    'user_password' => md5($user_password)
                );
                $this->session->saveUser($data_reg);
                redirect(base_url('login'));
            } else {
                $data['error'] = 'Email telah terdaftar!';
                $this->load->view('auth/register', $data);
            }
        } else {
            $this->load->view('auth/register', $data);
        }
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect(base_url('auth'));
    }
}
