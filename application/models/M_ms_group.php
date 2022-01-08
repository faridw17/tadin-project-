<?php

class M_ms_group extends CI_Model
{
    private $table  = 'admin.ms_group';
    private $id     = 'group_id';

    public function get_total($where)
    {
        $sql = "SELECT
                    count(*) as total
                from
                    admin.ms_group mg
                where
                    0 = 0
                    $where";
        return $this->db->query($sql)->row()->total;
    }

    public function get_data($limit, $where, $order, $columns)
    {
        $slc = implode(',', $columns);
        $sql = "SELECT
                    $slc
                from
                    admin.ms_group mg
                where
                    0 = 0
                    $where
                $order $limit";
        return $this->db->query($sql)->result();
    }

    public function get_menu($group_id = 1)
    {
        $where = " AND mm.menu_id != 4 ";

        $sql = "SELECT
                    mm.menu_id ,
                    concat(mm.menu_nama, case when mm.menu_status = false then ' (NON-AKTIF)' else '' end) as menu_nama,
                    mm.menu_parent_id,
                    case
                        when gm.group_id is null then 0
                        else 1
                    end as is_selected,
                    coalesce(child.total, 0) as total_child
                from
                    admin.ms_menu mm
                left join admin.group_menu gm on
                    gm.menu_id = mm.menu_id
                    and gm.group_id = $group_id
                left join (
                    select
                        count(*) as total,
                        menu_parent_id
                    from
                        admin.ms_menu
                    group by
                        menu_parent_id 
                ) as child on
                    child.menu_parent_id = mm.menu_id
                where
                    0 = 0
                    $where
                order by
                    mm.menu_kode";

        return $this->db->query($sql)->result();
    }

    public function delete_akses($group_id)
    {
        $this->db->where('menu_id !=', 4);
        $this->db->where('group_id', $group_id);
        $result = $this->db->delete('admin.group_menu');
        if ($result) {
            $res = [
                'status' => true,
                'message' => "Berhasil Memperbarui Akses",
            ];
        } else {
            $res = [
                'status' => false,
                'message' => "Gagal Menghapus Akses",
            ];
        }

        return $res;
    }

    public function save_akses($data)
    {
        $result = $this->db->insert_batch('admin.group_menu', $data);
        if ($result) {
            $res = [
                'status' => true,
                'message' => "Berhasil Memperbarui Akses",
            ];
        } else {
            $res = [
                'status' => false,
                'message' => "Gagal Menambahkan Akses",
            ];
        }

        return $res;
    }

    public function insert($data)
    {
        $this->db->insert($this->table, $data);
        if ($this->db->affected_rows() > -1) {
            $res = true;
        } else {
            $res =  false;
        }
        return $res;
    }

    public function update($id, $data)
    {
        $this->db->where($this->id, $id);
        $this->db->update($this->table, $data);
        if ($this->db->affected_rows() > -1) {
            $res = true;
        } else {
            $res =  false;
        }
        return $res;
    }

    public function delete($id)
    {
        $this->db->where($this->id, $id);
        $this->db->delete($this->table);
        if ($this->db->affected_rows() > -1) {
            $res = true;
        } else {
            $res =  false;
        }
        return $res;
    }
}
