<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ms_group extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('m_ms_group');
    }

    public function index()
    {
        $data['title'] = "Master Group";
        return $this->my_theme('v_ms_group', $data);
    }

    public function get_data()
    {
        $columns = array(
            'group_id',
            'group_kode',
            'group_nama',
            'group_status',
            'group_ket',
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
        $iTotalRecords = intval($this->m_ms_group->get_total($where));
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
                    $order .= "" . $columns[intval($this->input->post('order')[$i]['column'])] . " " .
                        ($this->input->post('order')[$i]['dir'] === 'asc' ? 'asc' : 'desc') . ", ";
                }
            }

            $order = substr_replace($order, "", -2);
            if ($order == "ORDER BY") {
                $order = "";
            }
        }
        $data = $this->m_ms_group->get_data($limit, $where, $order, $columns);
        $no   = 1 + $start;
        foreach ($data as $row) {
            $isi = rawurlencode(json_encode($row));
            if ($row->group_status == 't') {
                $status = '<span class="badge badge-success">Aktif</span>';
            } else {
                $status = '<span class="badge badge-danger">Non Aktif</span>';
            }

            $hapus = '&nbsp;<button onclick="set_del(\'' . $row->group_id . '\')" class="btn btn-sm btn-danger " title="Delete">
                        <i class="fa fa-trash"></i>
                    </button>';

            $action = '';

            if ($row->group_id == 1) {
                if ($this->userdata->user_id == 1) {
                    $action .= ' <button onclick="akses(\'' . $row->group_id . '\',\'' . $row->group_nama . '\')" class="btn btn-sm btn-warning font-weight-bold" title="Hak Akses">
                                    <i class="fa fa-cogs"></i>
                                </button>&nbsp;<button onclick="set_val(\'' . $isi . '\')" class="btn btn-sm btn-primary" title="Edit">
                                    <i class="fa fa-pencil-alt"></i>
                                </button>';
                } else {
                    $action .= '';
                }
            } else {
                $action .= ' <button onclick="akses(\'' . $row->group_id . '\',\'' . $row->group_nama . '\')" class="btn btn-sm btn-warning font-weight-bold" title="Hak Akses">
                                <i class="fa fa-cogs"></i>
                            </button>&nbsp;<button onclick="set_val(\'' . $isi . '\')" class="btn btn-sm btn-primary" title="Edit">
                                <i class="fa fa-pencil-alt"></i>
                            </button>' . ($row->group_id > 2 ? $hapus : '');
            }

            $records["data"][] = array(
                $no++,
                $row->group_kode,
                $row->group_nama,
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
            'group_kode' => addslashes($this->input->post('group_kode')),
            'group_nama' => addslashes($this->input->post('group_nama')),
            'group_ket' => addslashes($this->input->post('group_ket')),
            'group_status' => $this->input->post('group_status'),
        ];

        if ($act == 'add') {
            $res = $this->m_ms_group->insert($data);
        } else {
            $id = $this->input->post('group_id');
            $res = $this->m_ms_group->update($id, $data);
        }

        if ($res) {
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
        $res = $this->m_ms_group->delete($id);

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

    public function get_menu()
    {
        $group_id = $this->input->get('group_id');

        $group_id = !empty($group_id) ? $group_id : 0;

        $q = $this->m_ms_group->get_menu($group_id);
        $res = [];

        if (count($q) > 0) {
            foreach ($q as $key => $value) {
                // $value->class = ($value->total_child <= 0 && $value->is_selected == 1) ? 'jstree-checked' : '';
                $res[$key] = [
                    "id" => $value->menu_id,
                    "parent" => $value->menu_parent_id == 0 ? "#" : $value->menu_parent_id,
                    "text" => $value->menu_nama,
                    "state" => [
                        "opened" => true,
                        "selected" => ($value->total_child <= 0 && $value->is_selected == 1) ? true : false,
                        "checked" => ($value->total_child <= 0 && $value->is_selected == 1) ? true : false,
                    ],
                    "li_attr"     => $value,
                    "a_attr"      => $value,
                ];
            }
        }

        echo json_encode($res);
    }

    public function save_akses()
    {
        $group_id = $this->input->post('group_id');
        $menu_id = !empty($this->input->post('menu_id')) ? $this->input->post('menu_id') : [];
        $data = [];

        if (count($menu_id) > 0) {
            foreach ($menu_id as $v) {
                $data[] = [
                    'group_id' => $group_id,
                    'menu_id' => $v
                ];
            }
        }

        $res = $this->m_ms_group->delete_akses($group_id);
        if ($res['status'] && count($data) > 0) {
            $res = $this->m_ms_group->save_akses($data);
        }

        echo json_encode($res);
    }
}
