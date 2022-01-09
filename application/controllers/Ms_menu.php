<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ms_menu extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('m_ms_menu');
    }

    public function index()
    {
        $data['title'] = "Master Menu";
        return $this->my_theme('v_ms_menu', $data);
    }

    public function get_data()
    {
        $columns = array(
            "mm.menu_id",
            "mm.menu_kode",
            "mm.menu_nama",
            "mm.menu_ikon",
            "mm.menu_url",
            "parent.menu_nama as menu_parent_nama",
            "mm.menu_status",
            "mm.menu_parent_id",
            "coalesce(child.total,0) as total",
        );

        $search = $this->input->post('search')['value'];

        $where = "";

        if (isset($search) && $search != "") {
            $where .= " AND (";
            for ($i = 0; $i < count($columns); $i++) {
                if ($i == 1 || $i == 2 || $i == 3 || $i == 4) {
                    $where .= " LOWER(" . $columns[$i] . ") LIKE LOWER('%" . ($search) . "%') OR ";
                } elseif ($i == 5) {
                    $where .= " LOWER(parent.menu_nama) LIKE LOWER('%" . ($search) . "%') OR ";
                }
            }
            $where = substr_replace($where, "", -3);
            $where .= ')';
        }

        $iTotalRecords = intval($this->m_ms_menu->get_total($where));
        $length = intval($this->input->post('length'));
        $length = $length < 0 ? $iTotalRecords : $length;
        $start  = intval($this->input->post('start'));
        $draw      = intval($_REQUEST['draw']);
        $sortCol0 = $this->input->post('order')[0];
        $records = array();
        $records["data"] = array();
        $order = "";
        if (isset($start) && $length != '-1') {
            $limit = "limit " .  intval($length) . " offset " . intval($start);
        }

        if (isset($sortCol0)) {
            $order = "ORDER BY  ";
            for ($i = 0; $i < count($this->input->post('order')); $i++) {
                if ($this->input->post('columns')[intval($this->input->post('order')[$i]['column'])]['orderable'] == "true") {
                    if (intval($this->input->post('order')[$i]['column']) != 5) {
                        $order .= "" . $columns[intval($this->input->post('order')[$i]['column'])] . " " .
                            ($this->input->post('order')[$i]['dir'] === 'asc' ? 'asc' : 'desc') . ", ";
                    } else {
                        $order .= " parent.menu_nama " .
                            ($this->input->post('order')[$i]['dir'] === 'asc' ? 'asc' : 'desc') . ", ";
                    }
                }
            }

            $order = substr_replace($order, "", -2);
            if ($order == "ORDER BY") {
                $order = "";
            }
        }
        $data = $this->m_ms_menu->get_data($limit, $where, $order, $columns);
        $no   = 1 + $start;
        foreach ($data as $row) {
            $isi = rawurlencode(json_encode($row));
            if ($row->menu_status == 't') {
                $status = '<span class="badge badge-success">Aktif</span>';
            } else {
                $status = '<span class="badge badge-danger">Non Aktif</span>';
            }

            $aksi_hapus = "";

            if ($row->total < 1) {
                $aksi_hapus = ' <button onclick="set_del(\'' . $row->menu_id . '\')" class="btn btn-sm btn-danger " title="Delete">
                                    <i class="fa fa-trash"></i>
                                </button>';
            }

            $action = '';

            if ($row->menu_id <= 4 && $this->userdata->user_id == 1) {
                $action .= '<button onclick="set_val(\'' . $isi . '\')" class="btn btn-sm btn-primary" title="Edit">
                                <i class="fa fa-pencil-alt"></i>
                            </button>';
            } else if ($row->menu_id <= 4 && $this->userdata->user_id != 1) {
                $action .= '';
            } else {
                $action .= '<button onclick="set_val(\'' . $isi . '\')" class="btn btn-sm btn-primary" title="Edit">
                                <i class="fa fa-pencil-alt"></i>
                            </button>' . $aksi_hapus;
            }

            $records["data"][] = array(
                $no++,
                $row->menu_kode,
                $row->menu_nama,
                $row->menu_ikon,
                $row->menu_url,
                !empty($row->menu_parent_nama) ? $row->menu_parent_nama : 'ROOT',
                $status,
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
            'menu_kode' => addslashes($this->input->post('menu_kode')),
            'menu_nama' => addslashes($this->input->post('menu_nama')),
            'menu_url' => addslashes($this->input->post('menu_url')),
            'menu_ikon' => addslashes($this->input->post('menu_ikon')),
            'menu_parent_id' => !empty($this->input->post('menu_parent_id')) ? $this->input->post('menu_parent_id') : 0,
            'menu_status' => $this->input->post('menu_status'),
        ];

        if ($act == 'add') {
            $res = $this->m_ms_menu->insert($data);
        } else {
            $id = $this->input->post('menu_id');
            $res = $this->m_ms_menu->update($id, $data);
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
        $res = $this->m_ms_menu->delete($id);

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

    public function get_parent()
    {
        $res = $this->m_ms_menu->get_parent();

        echo json_encode($res);
    }
}
