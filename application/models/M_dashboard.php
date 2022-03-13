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

    public function get_line_data($device_id, $tgl_pertama, $tgl_selesai)
    {
        $tgl_pertama = date("Y-m-d", strtotime($tgl_pertama));
        $tgl_selesai = date("Y-m-d", strtotime($tgl_selesai));
        $sql = "SELECT
                    *
                from
                    (
                    select
                        to_char(dm.tanggal, 'dd-mm-yyyy') tanggal,
                        sum(dm.jam) jam
                    from
                        mesin.data_mesin dm
                    where
                        dm.device_id = $device_id
                        and date(dm.tanggal) between '$tgl_pertama' and '$tgl_selesai'
                    group by
                        dm.tanggal
                    order by
                        dm.tanggal desc) label
                order by
                    label.tanggal asc";
        $res = $this->db->query($sql)->result();
        return $res;
    }
}
