<?php

class M_api extends CI_Model
{
    public function insert_mesin_data($data)
    {
        $this->db->insert_batch('mesin.data_mesin', $data);
    }

    public function update_status_mesin($data)
    {
        $this->db->update_batch('mesin.ms_device', $data, 'device_id');
    }

    public function insert_test($data)
    {
        $this->db->insert('mesin.test_data', $data);
    }
}
