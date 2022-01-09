<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ms_setting extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('m_ms_setting');
    }

    public function index()
    {
        $data['title'] = "Master Setting";
        return $this->my_theme('v_ms_setting', $data);
    }

    public function get_data()
    {
        $columns = array(
            'setting_id',
            'setting_nama',
            'setting_value',
            'setting_ket',
            // 'setting_status',
        );
        $search = $this->input->post('search')['value'];
        $where = "";
        if (isset($search) && $search != "") {
            $where = "AND (";
            for ($i = 0; $i < count($columns); $i++) {
                if ($i == 1 || $i == 2) {
                    $where .= " LOWER(" . $columns[$i] . ") LIKE LOWER('%" . ($search) . "%') OR ";
                }
            }
            $where = substr_replace($where, "", -3);
            $where .= ')';
        }
        $iTotalRecords = intval($this->m_ms_setting->get_total($where));
        $length = intval($this->input->post('length'));
        $length = $length < 0 ? $iTotalRecords : $length;
        $start  = intval($this->input->post('start'));
        $draw      = intval($_REQUEST['draw']);
        $sortCol0 = $this->input->post('order')[0];
        $records = array();
        $records["data"] = array();
        $order = "";
        if (isset($start) && $length != '-1') {
            $limit = "limit " . intval($length) . " offset " . intval($start);
        }

        if (isset($sortCol0)) {
            $order = "ORDER BY  ";
            for ($i = 0; $i < count($this->input->post('order')); $i++) {
                if ($this->input->post('columns')[intval($this->input->post('order')[$i]['column'])]['orderable'] == "true") {
                    $order .= "" . $columns[intval($this->input->post('order')[$i]['column'])] . " " .
                        ($this->input->post('order')[$i]['dir'] === 'asc' ? 'asc' : 'desc') . ", ";
                }
            }

            $order = substr_replace($order, "", -2);
            if ($order == "ORDER BY") {
                $order = "";
            }
        }
        $data = $this->m_ms_setting->get_data($limit, $where, $order, $columns);
        $no   = 1 + $start;
        foreach ($data as $row) {
            $isi = rawurlencode(json_encode($row));
            // if ($row->setting_status == 't') {
            //     $status = '<span class="badge badge-success">Aktif</span>';
            // } else {
            //     $status = '<span class="badge badge-danger">Non Aktif</span>';
            // }

            $action = '<button onclick="set_val(\'' . $isi . '\')" class="btn btn-sm btn-primary" title="Edit">
                            <i class="fa fa-pencil-alt"></i>
                        </button>';

            $records["data"][] = array(
                $no++,
                $row->setting_nama,
                $row->setting_value,
                rawurldecode($row->setting_ket),
                // $status,
                $action,
            );
        }

        $records["draw"] = $draw;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function save()
    {
        $act = $this->input->post('act');

        $data = [
            'setting_nama' => addslashes($this->input->post('setting_nama')),
            'setting_value' => addslashes($this->input->post('setting_value')),
            'setting_ket' => rawurlencode($this->input->post('setting_ket')),
            // 'setting_status' => $this->input->post('setting_status'),
        ];

        if ($act == 'add') {
            $res = $this->m_ms_setting->insert($data);
        } else {
            $id = $this->input->post('setting_id');
            $res = $this->m_ms_setting->update($id, $data);
        }

        if ($res > 0) {
            $response = [
                'status' => true,
                'message' => $act == 'add' ? 'Berhasil menambahkan data!' : 'Berhasil memperbarui data!',
                'title' => 'Success',
            ];
        } else {
            $response = [
                'status' => false,
                'message' => $act == 'add' ? 'Gagal menambahkan data!' : 'Gagal memperbarui data!',
                'title' => 'Error',
            ];
        }

        echo json_encode($response);
    }

    public function hapus()
    {
        $id = $this->input->post('id');
        $res = $this->m_ms_setting->delete($id);

        $response = [
            'status' => false,
            'message' => "Data Gagal dihapus"
        ];

        if ($res) {
            $response = [
                'status' => true,
                'message' => "Data Berhasil dihapus"
            ];
        }

        echo json_encode($response);
    }
}
