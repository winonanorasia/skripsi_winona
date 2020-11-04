<?php defined('BASEPATH') or exit('No direct script access allowed');

class M_prediksi extends CI_Model
{
    private $_table = "hasilprediksi";

    public function getAll()
    {
        return $this->db->order_by('h_id', 'DESC')->get('hasilprediksi')->result();
    }

    public function getById($id)
    {
        return $this->db->get_where('hasilprediksi', ['h_id' => $id])->result()[0];
    }

    public function save($data)
    {
        $this->db->insert($this->_table, $data);
    }

    public function delete($id)
    {
        return $this->db->delete($this->_table, array("h_id" => $id));
    }
    function get_prediksi_list($limit, $start)
    {
        //$query = $this->db->get('hasilprediksi', $limit, $start);
        $query = $this->db->query("SELECT * FROM hasilprediksi WHERE h_id>$start LIMIT $limit");
        return $query;
    }
    function get_prediksi_list1($limit, $start)
    {
        //$query = $this->db->get('hasilprediksi', $limit, $start);
        $query = $this->db->query("SELECT * FROM hasilprediksi WHERE jenis='bulanan' AND h_id>$start LIMIT $limit");
        return $query;
    }
    function get_prediksi_list2($limit, $start)
    {
        //$query = $this->db->get('hasilprediksi', $limit, $start);
        $query = $this->db->query("SELECT * FROM hasilprediksi WHERE jenis='harian' AND h_id>$start LIMIT $limit");
        return $query;
    }
    function get_terbaik()
    {
        return [
            'bulanan-mse' => $this->db->query("SELECT * FROM hasilprediksi WHERE jenis='bulanan' ORDER BY mse LIMIT 1")->first_row(),
            'bulanan-mape' => $this->db->query("SELECT * FROM hasilprediksi WHERE jenis='bulanan' ORDER BY mape LIMIT 1")->first_row(),
            'harian-mse' =>  $this->db->query("SELECT * FROM hasilprediksi WHERE jenis='harian' ORDER BY mse LIMIT 1")->first_row(),
            'harian-mape' =>  $this->db->query("SELECT * FROM hasilprediksi WHERE jenis='harian' ORDER BY mape LIMIT 1")->first_row()
        ];
    }
}
