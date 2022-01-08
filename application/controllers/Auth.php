<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('m_auth');
        $this->load->library('session');
    }

    public function index()
    {
        $userdata = $this->session->userdata('userdata');
        if ($userdata && $userdata->is_login == 1) {
            return redirect()->to(base_url() . 'dashboard');
        }

        $this->load->view('auth/login');
    }

    public function login()
    {
        $res = [
            'status' => false,
            'message' => 'Gagal Login'
        ];

        $username = $this->input->post('username');
        $password = $this->input->post('password');

        $userdata = $this->m_auth->get_user_data($username);

        if ($userdata) {
            if ($userdata->user_status == 't') {
                if (password_verify($password, $userdata->password)) {

                    $setting = $this->m_auth->get_setting();
                    unset($userdata->password);
                    $userdata->is_login = 1;

                    $sess_data = [
                        'setting' => $setting,
                        'userdata' => $userdata,
                    ];

                    $this->session->set_userdata($sess_data);

                    $res = [
                        'status' => true,
                        'message' => 'Berhasil!',
                        'url' => base_url() . 'dashboard',
                    ];
                } else {
                    $res = [
                        'status' => false,
                        'message' => 'Password Salah!'
                    ];
                }
            } else {
                $res = [
                    'status' => false,
                    'message' => 'User Tidak Aktif!'
                ];
            }
        } else {
            $res = [
                'status' => false,
                'message' => 'User Belum Terdaftar!'
            ];
        }

        echo json_encode($res);
    }

    function logout()
    {
        $this->session->sess_destroy();
        return redirect()->to(base_url() . '/auth');
    }
}
