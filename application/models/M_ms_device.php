<?php

class M_ms_device extends CI_Model
{
    protected $table    = 'mesin.ms_device';
    protected $id       = 'device_id';

    public function get_total($where)
    {
        $sql = "SELECT
                    count(*) as total
                from
                    mesin.ms_device md
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
                    mesin.ms_device md
                where
                    0 = 0
                    $where
                $order $limit";
        return $this->db->query($sql)->result();
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
