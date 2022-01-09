<?php

class M_ms_setting extends CI_Model
{
    private $table    = 'admin.setting';
    private $id       = 'setting_id';

    public function get_total($where)
    {
        $sql = "SELECT
                    count(*) as total
                from
                    admin.setting s
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
                    admin.setting s
                where
                    0 = 0
                    $where
                $order $limit";
        return $this->db->query($sql)->result();
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
}
