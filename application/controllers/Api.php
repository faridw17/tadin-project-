<?php
defined('BASEPATH') or exit('No direct script access allowed');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: POST");

class Api extends CI_Controller
{
    public function __construct()
    {
        $this->load->model('m_api');
    }

    public function insert_mesin()
    {
        $id = $this->input->post('id');
        $jam = $this->input->post('jam');
        $kondisi = $this->input->post('kondisi');

        $data_mesin = [];
        $data_status = [];
        $tgl = date("Y-m-d");

        foreach ($id as $k => $v) {
            $data_mesin[] = [
                "device_id" => $v,
                "jam" => $jam[$k],
                "tanggal" => $tgl,
            ];

            $data_status[] = [
                "device_id" => $v,
                "device_kondisi" => $kondisi[$k],
            ];
        }

        if (count($data_mesin) > 0) {
            $this->m_api->insert_mesin_data($data_mesin);
        }

        if (count($data_status) > 0) {
            $this->m_api->update_status_mesin($data_status);
        }

        return $this->output->set_status_header(201);
    }

    public function insert_test()
    {
        $this->m_api->insert_test(['data' => json_encode($this->input->post())]);
    }
}
