<?php 
 
class M_users extends CI_Model{
	
	public function rules()
    {
        return [
            ['field' => 'username',
            'label' => 'Username',
            'rules' => 'trim|required',
            'errors' => array(
                'required' => 'Username tidak boleh kosong!',
            )],

            ['field' => 'password',
            'label' => 'Password',
            'rules' => 'required',
            'errors' => array(
                'required' => 'Password tidak boleh kosong!',
            )],
        ];
	}
	
    function cekLogin($tabel, $where){
		return $this->db->get_where($tabel, $where);
    }

    function saveUser($data){
        return $this->db->insert('users',$data);
    }

    function getInfo(){
        return $this->db->query("
            SELECT count(*) jml FROM transaksi union ALL
            SELECT count(*) jml FROM prediksi")->result();
    }
    
    function profile()
    {
        return $this->db->get('admin')->result();
    }

    function graph($inisial)
    {
       $data = $this->db->get_where('peramalan', ['id_inisial'=>$inisial])->result();
       $this->output
		->set_status_header(200)
		->set_content_type('application/json', 'utf-8')
		->set_output(json_encode($data, JSON_PRETTY_PRINT))
		->_display();
		exit;
    }

    function updatePro($data)
    {
        $this->db->where('id', 1);
        $this->db->update('admin', $data);
        return;
    }

    function cekPasswd($passwd)
    {
        return count($this->db->get_where('admin',['password'=>md5($passwd)])->result());
    }

    function updatePass($data)
    {
        $this->db->where('id', 1);
        $this->db->update('admin', $data);
        return;
    }
}