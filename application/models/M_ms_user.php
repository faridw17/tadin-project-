<?php

class M_ms_user extends CI_Model
{
    private $table = "admin.ms_user";
    private $id = "user_id";

    public function get_total($where)
    {
        $sql = "SELECT
                    count(*) as total
                from
                    admin.ms_user mu
                left join (
                    select
                        user_id,
                        count(*) as total
                    from
                        admin.group_user
                    group by
                        user_id) gu on
                    gu.user_id = mu.user_id
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
                    admin.ms_user mu
                left join (
                    select
                        user_id,
                        count(*) as total
                    from
                        admin.group_user
                    group by
                        user_id) gu on
                    gu.user_id = mu.user_id
                where
                    0 = 0
                    $where
                $order $limit";
        return $this->db->query($sql)->result();
    }

    public function get_akses($id)
    {
        $sql = "SELECT
                    mg.group_id ,
                    mg.group_nama ,
                    case
                        when gu.group_id is null then 0
                        else 1
                    end as akses
                from
                    admin.ms_group mg
                left join admin.group_user gu on
                    gu.group_id = mg.group_id
                    and gu.user_id = $id
                where
                    mg.group_status = true
                    and mg.group_id != 1
                order by
                    mg.group_nama";
        $res = $this->db->query($sql)->result();
        return $res;
    }

    public function delete_akses($user_id)
    {
        $result = $this->db->delete('admin.group_user', [$this->id => $user_id]);
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
        $result = $this->db->insert_batch('admin.group_user', $data);
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
