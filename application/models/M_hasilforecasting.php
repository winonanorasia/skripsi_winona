<?php defined('BASEPATH') or exit('No direct script access allowed');

class M_hasilforecasting extends CI_Model
{
    private $_table = "hasilforecasting";

    public function getAll()
    {
        return $this->db->order_by('h_id', 'DESC')->get('hasilforecasting')->result();
    }

    public function getById($id)
    {
        return $this->db->get_where('hasilforecasting', ['h_id' => $id])->result()[0];
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
        $query = $this->db->query("SELECT * FROM hasilforecasting WHERE h_id>$start LIMIT $limit");
        return $query;
    }

    function getByJenis($jenis)
    {
        return $this->db->get_where($this->_table, ['jenis' => $jenis])->result();
    }
}
