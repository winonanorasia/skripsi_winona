<?php defined('BASEPATH') or exit('No direct script access allowed');

class M_gelombang extends CI_Model
{
    private $_table = "datagelombang";


    public $tanggal;

    public $periode;
    public $ketinggian_gelombang;

    public function rules()
    {
        return [
            [
                'field' => 'tanggal',
                'label' => 'tanggal',
                'rules' => 'required'
            ],

            [
                'field' => 'ketinggian_gelombang',
                'label' => 'ketinggian_gelombang',
                'rules' => 'required'
            ]
        ];
    }

    public function getAll()
    {
        $data = $this->db->get('datagelombang')->result();
        $no = 1;
        foreach ($data as $x) {
            $x->periode = $no;
            $x->d_tinggi = floatval($x->d_tinggi);
            $no++;
        }
        return $data;
    }

    public function getBulanan()
    {
        $data = $this->db->get('datagelombang')->result();
        $data_new = [];
        $tmp = [];
        foreach ($data as $x) {
            $tgl = $x->d_tanggal;
            $bln = explode('-', $tgl)[1];
            $thn = explode('-', $tgl)[0];
            if (in_array($bln . '-' . $thn, array_keys($data_new))) array_push($data_new[$bln . '-' . $thn], $x->d_tinggi);
            else $data_new[$bln . '-' . $thn] = [$x->d_tinggi];
        }
        $no = 1;
        $data_fix = [];
        foreach (array_keys($data_new) as $x) {
            array_push($data_fix, (object)[
                'bulan_tahun' => $x,
                'periode' => $no,
                'tinggi' => array_sum($data_new[$x]) / count($data_new[$x])
            ]);
            $no++;
        }

        return $data_fix;
    }

    public function getByPeriod($where)
    {
        return $this->db
            ->where("d_periode >=", $where['prdMulai'])
            ->where("d_periode <=", $where['prdSampai'])
            ->get($this->_table)->result();
    }

    public function getById($id)
    {
        return $this->db->get_where('datagelombang', ['d_id' => $id])->result();
    }

    public function save()
    {
        $post = $this->input->post();
        $data = array(
            'd_tanggal' => $post["tanggal"],
            'd_tinggi' => $post["ketinggian_gelombang"]
        );
        return $this->db->insert($this->_table, $data);
    }

    public function update($id)
    {
        $post = $this->input->post();
        $data = array(
            'd_tanggal' => $post["tanggal"],
            'd_tinggi' => $post["ketinggian_gelombang"]
        );
        return $this->db->update($this->_table, $data, array('d_id' => $id));
    }

    public function delete($id)
    {
        return $this->db->delete($this->_table, array("d_id" => $id));
    }

    public function getTahun()
    {
        return $this->db->select('DISTINCT(d_tahun)')->from($this->_table)->get()->result();
    }

    public function getBulan($tahun)
    {
        $res =  $this->db->select("d_periode id, concat(d_periode,' - ',d_bulan) ket")
            ->from($this->_table)->where('d_tahun', $tahun)->get()->result();

        $this->output
            ->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($res, JSON_PRETTY_PRINT))
            ->_display();
        exit;
    }
}
