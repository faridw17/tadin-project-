<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends MY_Controller
{
    private $nama_pt;

    public function __construct()
    {
        parent::__construct();
        foreach ($this->setting as  $v) {
            if ($v->setting_nama == 'nama_pt') {
                $this->nama_pt = $v->setting_value;
            }
        }
        $this->load->model('m_dashboard');
    }

    public function index()
    {
        $data['title'] = "Dashboard";
        $data['nama_pt'] = $this->nama_pt;
        return $this->my_theme('v_dashboard', $data);
    }

    public function get_mesin()
    {
        $res = $this->m_dashboard->get_dashboard_mesin();
        echo json_encode($res);
    }

    public function get_grafik()
    {
        $limit = $this->input->post('limit');

        $limit = !empty($limit) ? $limit : 10;

        $res = [];

        $sampelBanyakData = [];
        $sampelBanyakTanggal = [];

        for ($i = 0; $i < $limit; $i++) {
            $sampelBanyakData[$i] = null;
        }

        $tanggal = '';
        $label_x = [];
        $tgl_pertama = date("d-m-Y");

        for ($i = 0; $i < $limit; $i++) {
            $tanggal = date('d-m-Y', strtotime(date('Y-m-d')) - ($limit - $i - 1) * 60 * 60 * 24);
            $sampelBanyakTanggal[$tanggal] = $i;
            $label_x[] = $tanggal;
            if ($i == 0) {
                $tgl_pertama = $tanggal;
            }
        }

        $res['xaxis'] = $label_x;

        $listDevice = $this->m_dashboard->get_dashboard_mesin();

        $res['series'] = [];

        foreach ($listDevice as $key => $value) {
            $res['series'][$key] = [
                'name' => $value->device_nama,
                'data' => $sampelBanyakData,
            ];

            $get_data = $this->m_dashboard->get_line_data($value->device_id, $tgl_pertama);

            foreach ($get_data as $k => $v) {
                $res['series'][$key]['data'][$sampelBanyakTanggal[$v->tanggal]] = floatval($v->jam);
            }
        }

        echo json_encode($res);
    }
}
