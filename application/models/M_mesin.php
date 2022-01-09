<?php

class M_mesin extends CI_Model
{
    public function get_data_device($device_id)
    {
        $sql = "SELECT * from mesin.ms_device where device_id = $device_id";

        $res = $this->db->query($sql)->row();
        return $res;
    }

    public function get_line_data($device_id, $tgl_pertama)
    {
        $sql = "SELECT
                    *
                from
                    (
                    select
                        dm.tanggal,
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
                    tanggal asc";
        $res = $this->db->query($sql)->result();
        return $res;
    }

    public function get_total_jam($device_id, $jenis = '')
    {
        $where = " AND device_id = $device_id ";

        if ($jenis == 'harian') {
            $where .= " AND tanggal::text = '" . date('Y-m-d') . "' ";
        } else if ($jenis == 'bulanan') {
            $where .= " AND tanggal::text like '" . date('Y-m') . "%' ";
        } else if ($jenis == 'tahunan') {
            $where .= " AND tanggal::text like '" . date('Y') . "%' ";
        }

        $sql = "SELECT
                    sum(jam) as total
                from
                    mesin.data_mesin dm
                where
                    0 = 0
                    $where";

        return number_format($this->db->query($sql)->row()->total, 2, ',', '.');
    }

    public function get_status($device_id)
    {
        return $this->db->get_where('mesin.ms_device', ['device_id' => $device_id])->row()->device_kondisi;
    }

    public function get_nama($device_id)
    {
        return $this->db->get_where('mesin.ms_device', ['device_id' => $device_id])->row()->device_nama;
    }
}
