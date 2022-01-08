<?php

class M_auth extends CI_Model
{
    public function get_user_data($username)
    {
        $res = $this->db->query(
            "SELECT * FROM admin.ms_user mu where mu.user_name = '$username'"
        )->row();

        return $res;
    }

    public function get_setting()
    {
        $res = $this->db->query(
            "SELECT * FROM admin.setting where setting_status = true"
        )->result();

        return $res;
    }
}
