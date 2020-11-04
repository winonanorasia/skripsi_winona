<?php defined('BASEPATH') or exit('No direct script access allowed');

class M_pengujian extends CI_Model
{
    private $_table = "hasilpengujian";

    public function getAll()
    {
        return $this->db->order_by('h_id','DESC')->get($this->_table)->result();
    }

    public function getById($id)
    {
        return $this->db->get_where($this->_table,['h_id'=>$id])->result()[0];
    }

    public function save($namafile,$data)
    {
        $post = $this->input->post();
        $input = [
            'h_dokumen'=>$namafile,
            'alpha'=>$post['alpha'],
            'beta'=>$post['beta'],
            'gamma'=>$post['gamma'],
            'jumlah_n'=>$post['jumlah_n'],
            'thnMulai'=>$post['thnMulai'],
            'prdMulai'=>$post['prdMulai'],
            'thnSampai'=>$post['thnSampai'],
            'prdSampai'=>$post['prdSampai'],
            'jumlah_folds'=>$post['jumlah_folds'],
            'mse'=>$data['mse'],
            'mape'=>$data['mape'],
            'timestamp' => date('Y-m-d H:i:s')
        ];
        $this->db->insert($this->_table, $input);
    }

    public function delete($id)
    {
        return $this->db->delete($this->_table, array("h_id" => $id));
    }
}