<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ms_user extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('m_ms_user');
    }

    public function index()
    {
        $data['title'] = "Master User";
        return $this->my_theme('v_ms_user', $data);
    }

    public function get_data()
    {
        $columns = array(
            'mu.user_id',
            'user_fullname',
            'user_name',
            'user_status',
            'user_email',
            "coalesce(gu.total,0) as total_akses",
        );

        $colSearch = [
            'user_fullname',
            'user_name',
            'user_email',
        ];

        $search = $this->input->post('search')['value'];
        $where = "";
        if (isset($search) && $search != "") {
            $where = "AND (";
            for ($i = 0; $i < count($colSearch); $i++) {
                $where .= " LOWER(" . $colSearch[$i] . ") LIKE LOWER('%" . ($search) . "%') OR ";
            }
            $where = substr_replace($where, "", -3);
            $where .= ')';
        }
        $iTotalRecords = intval($this->m_ms_user->get_total($where));
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
        $data = $this->m_ms_user->get_data($limit, $where, $order, $columns);
        $no   = 1 + $start;
        foreach ($data as $row) {
            $action = "";
            $isi = rawurlencode(json_encode($row));
            if ($row->user_status == 't') {
                $status = '<span class="badge badge-success">Aktif</span>';
            } else {
                $status = '<span class="badge badge-danger">Non Aktif</span>';
            }

            $akses = $row->total_akses > 0 ? 'btn-warning' : 'btn-secondary';

            $hapus = '&nbsp;<button onclick="set_del(\'' . $row->user_id . '\')" class="btn btn-sm btn-danger " title="Delete">
                        <i class="fa fa-trash"></i>
                    </button>';

            if ($row->user_id == 1 && $this->userdata->user_id == 1) {
                $action .= '<button onclick="akses(\'' . $row->user_id . '\',\'' . $row->user_fullname . '\')" class="btn btn-sm ' . $akses . ' font-weight-bold" title="Hak Akses">
                                <i class="fa fa-cogs"></i>
                            </button>&nbsp;<button onclick="set_val(\'' . $isi . '\')" class="btn btn-sm btn-primary" title="Edit">
                                <i class="fa fa-pencil-alt"></i>
                            </button>';
            } else if ($row->user_id == 1 && $this->userdata->user_id != 1) {
                $action .= '';
            } else {
                $action .= '<button onclick="akses(\'' . $row->user_id . '\',\'' . $row->user_fullname . '\')" class="btn btn-sm ' . $akses . ' font-weight-bold" title="Hak Akses">
                                <i class="fa fa-cogs"></i>
                            </button>&nbsp;<button onclick="set_val(\'' . $isi . '\')" class="btn btn-sm btn-primary" title="Edit">
                                <i class="fa fa-pencil-alt"></i>
                            </button>';
                if ($row->user_id != $this->userdata->user_id) {
                    $action .= $hapus;
                }
            }

            $records["data"][] = array(
                $no++,
                $row->user_fullname,
                $row->user_name,
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
        $password = password_hash($this->input->post('password'), PASSWORD_DEFAULT);

        $data = [
            'user_name' => addslashes($this->input->post('user_name')),
            'user_fullname' => addslashes($this->input->post('user_fullname')),
            'user_email' => addslashes($this->input->post('user_email')),
            'user_status' => $this->input->post('user_status'),
        ];

        if ($act == 'add') {
            $data['password'] = $password;
            $res = $this->m_ms_user->insert($data);
        } else {
            if ($this->input->post('is_ganti') == 1) $data['password'] = $password;

            $id = $this->input->post('user_id');
            $res = $this->m_ms_user->update($id, $data);
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
        $res = $this->m_ms_user->delete($id);

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

    public function get_akses()
    {
        $id = $this->input->post('id');
        $res = $this->m_ms_user->get_akses($id);
        echo json_encode($res);
    }

    public function save_akses()
    {
        $user_id = $this->input->post('user_id');
        $group_id = !empty($this->input->post('group_id')) ? $this->input->post('group_id') : [];
        $data = [];

        if (count($group_id) > 0) {
            foreach ($group_id as $v) {
                $data[] = [
                    'user_id' => $user_id,
                    'group_id' => $v
                ];
            }
        }

        $res = $this->m_ms_user->delete_akses($user_id);
        if ($res['status'] && count($data) > 0) {
            $res = $this->m_ms_user->save_akses($data);
        }

        echo json_encode($res);
    }
}
