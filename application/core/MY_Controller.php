<?php
class MY_Controller extends CI_Controller
{
    protected $userdata;
    protected $setting;
    protected $judul_website;
    protected $judul_ikon;

    public function __construct()
    {
        parent::__construct();

        $this->userdata = $this->session->userdata('userdata');
        $this->setting = $this->session->userdata('setting');
        if (!($this->userdata && $this->userdata->is_login == 1)) {
            return redirect()->to(base_url() . '/auth');
        }
        foreach ($this->setting as  $v) {
            if ($v->setting_nama == 'judul_website') {
                $this->judul_website = $v->setting_value;
            } else if ($v->setting_nama == 'judul_ikon') {
                $this->judul_ikon = $v->setting_value;
            }
        }
        $this->load->model('m_umum');
    }

    public function my_theme($url, $data)
    {
        if (!($this->userdata && $this->userdata->is_login == 1)) {
            return redirect()->to(base_url() . 'auth');
        }

        $header['title'] = !empty($data['title']) ? $data['title'] . " | " . $this->judul_website : $this->judul_website;

        $sidebarMenu = $this->m_umum->get_sidebar($this->userdata->user_id, 0);
        $sidebar = [
            'sidebar' => $sidebarMenu,
            'judul' => $this->judul_website,
            'judul_ikon' => $this->judul_ikon,
        ];

        $topbar = [
            'user_fullname' => $this->userdata->user_fullname,
        ];

        $footer = [
            'judul' => $this->judul_website
        ];

        $this->load->view('template/header', $header);
        $this->load->view('template/sidebar', $sidebar);
        $this->load->view('template/topbar', $topbar);
        $this->load->view($url, $data);
        $this->load->view('template/footer', $footer);
    }
}
