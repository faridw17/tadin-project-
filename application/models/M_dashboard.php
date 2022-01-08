<?php

class M_dashboard extends CI_Model
{
    public function get_dashboard_mesin()
    {
        $sql = "SELECT
                    *
                from
                    mesin.ms_device md
                where
                    md.device_status = true
                order by
                    device_kode";

        $res = $this->db->query($sql)->result();
        return $res;
    }

    public function get_label_x($limit = 10)
    {
        $sql = "SELECT
                    *
                from
                    (
                    select
                        distinct dm.tanggal
                    from
                        mesin.data_mesin dm
                    order by
                        dm.tanggal desc
                    limit $limit) label
                order by
                    tanggal asc";

        $result = $this->db->query($sql)->result();

        $res = [];

        foreach ($result as $key => $value) {
            $res[] = $value->tanggal;
        }
        return $res;
    }

    public function get_line_data($device_id, $tgl_pertama)
    {
        $tgl_pertama = date("Y-m-d", strtotime($tgl_pertama));
        $sql = "SELECT
                    *
                from
                    (
                    select
                        date_format(dm.tanggal, '%d-%m-%Y') tanggal,
                        sum(dm.jam) jam
                    from
                        mesin.data_mesin dm
                    where
                        dm.device_id = $device_id
                        and dm.tanggal >= '$tgl_pertama'
                    group by
                        dm.tanggal
                    order by
                        dm.tanggal desc) label
                order by
                    label.tanggal asc";
        $res = $this->db->query($sql)->result();
        return $res;
    }

    public function get_bar_data($where = "")
    {
        # code...
    }
}
