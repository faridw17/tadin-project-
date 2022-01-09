<?php

class M_ms_menu extends CI_Model
{
    protected $table    = 'admin.ms_menu';
    protected $id       = 'menu_id';

    public function get_total($where)
    {
        $sql = "SELECT
                    count(*) as total
                from
                    admin.ms_menu mm
                left join admin.ms_menu parent on
                    parent.menu_id = mm.menu_parent_id
                left join (
                    select
                        count(*) as total,
                        menu_parent_id
                    from
                        admin.ms_menu
                    group by
                        menu_parent_id 
                ) child on
                    child.menu_parent_id = mm.menu_id
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
                    admin.ms_menu mm
                left join admin.ms_menu parent on
                    parent.menu_id = mm.menu_parent_id
                left join (
                    select
                        count(*) as total,
                        menu_parent_id
                    from
                        admin.ms_menu
                    group by
                        menu_parent_id 
                ) child on
                    child.menu_parent_id = mm.menu_id
                where
                    0 = 0
                    $where
                $order $limit";
        return $this->db->query($sql)->result();
    }

    public function proses_check($where = '')
    {
        $sql = "SELECT count(*) as total from admin.ms_menu where 0 = 0 $where ";

        $tot = $this->db->query($sql)->row()->total;

        return $tot;
    }

    public function get_parent()
    {
        return $this->db->where('menu_status', 't')->order_by('menu_kode', 'asc')->get('admin.ms_menu')->result();
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
