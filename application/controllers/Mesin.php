<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mesin extends MY_Controller
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
        $this->load->model('m_mesin');
    }

    public function get_grafik()
    {
        $device_id = $this->input->get('device_id');

        $limit = 10;

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
            $tanggal = date('Y-m-d', strtotime(date('Y-m-d')) - ($limit - $i - 1) * 60 * 60 * 24);
            $sampelBanyakTanggal[$tanggal] = $i;
            $label_x[] = $tanggal;
            if ($i == 0) {
                $tgl_pertama = $tanggal;
            }
        }

        $data = $this->m_mesin->get_data_device($device_id);

        $res['xaxis'] = $label_x;
        $res['series'] = [];

        $res['series'][0] = [
            'name' => $data->device_nama,
            'data' => $sampelBanyakData,
        ];

        $get_data = $this->m_mesin->get_line_data($data->device_id, $tgl_pertama);

        foreach ($get_data as $k => $v) {
            $res['series'][0]['data'][$sampelBanyakTanggal[$v->tanggal]] = floatval($v->jam);
        }

        echo json_encode($res);
    }


    public function detail($device_id)
    {
        $data['device_id'] = $device_id;
        $data['title'] = $this->m_mesin->get_nama($device_id);
        $data['nama_pt'] = $this->nama_pt;
        $data['total_harian'] = $this->m_mesin->get_total_jam($device_id, 'harian');
        $data['total_bulanan'] = $this->m_mesin->get_total_jam($device_id, 'bulanan');
        $data['total_tahunan'] = $this->m_mesin->get_total_jam($device_id, 'tahunan');
        $data['total_all'] = $this->m_mesin->get_total_jam($device_id);
        $data['status'] = $this->m_mesin->get_status($device_id);
        return $this->my_theme('v_mesin', $data);
    }

    public function realtime_detail($device_id)
    {
        $data['total_harian'] = $this->m_mesin->get_total_jam($device_id, 'harian');
        $data['total_bulanan'] = $this->m_mesin->get_total_jam($device_id, 'bulanan');
        $data['total_tahunan'] = $this->m_mesin->get_total_jam($device_id, 'tahunan');
        $data['total_all'] = $this->m_mesin->get_total_jam($device_id);
        $data['status'] = $this->m_mesin->get_status($device_id);
        echo json_encode($data);
    }
}
