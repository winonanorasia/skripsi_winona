<?php defined('BASEPATH') or exit('No direct script access allowed');

class M_periode extends CI_Model
{
    private $_table = "hasilforecasting";

    public function getAll()
    {
        return $this->db->order_by('h_id', 'DESC')->get('hasilforecating')->result();
    }

    public function getById($id)
    {
        return $this->db->get_where('hasilforecasting', ['h_id' => $id])->result()[0];
    }

    public function getByJenis($id)
    {
        return $this->db->get_where('hasilforecasting', ['jenis' => $id])->result();
    }

    public function delete($id)
    {
        return $this->db->delete($this->_table, array("h_id" => $id));
    }
}
