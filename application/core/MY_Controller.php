<?php
class MY_Controller extends CI_Input
{
    public function __construct()
    {
        parent::__construct();
    }

    public function my_theme($url, $data)
    {
        $header = [];
        $sidebar = [];
        $topbar = [];
        $footer = [];

        $this->load->view('template/header', $header);
        $this->load->view('template/sidebar', $sidebar);
        $this->load->view('template/topbar', $topbar);
        $this->load->view($url, $data);
        $this->load->view('template/footer', $footer);
    }
}
