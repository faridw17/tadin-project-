<?php

class APIModel extends CI_Model
{
    public function insert_mesin_data($data)
    {
        $this->db->table('data_mesin')->insertBatch($data);
    }

    public function update_status_mesin($data)
    {
        $this->db->table('ms_device')->updateBatch($data, 'device_id');
    }

    public function insert_test($data)
    {
        $this->db->table('test_data')->insert($data);
    }
}
