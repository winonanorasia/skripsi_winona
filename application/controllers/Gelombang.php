<?php
defined('BASEPATH') or exit('No direct script access allowed');


class Gelombang extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('login') !== true) {
            redirect(base_url('login'));
        }
        $this->load->model("M_gelombang", 'Gelombang');
    }

    public function index()
    {
        $data["datagelombang"] = $this->Gelombang->getAll();
        $data['page'] = 'gelombang/data';
        $this->load->view("main", $data);
    }

    public function bulanan()
    {
        $data["datagelombang"] = $this->Gelombang->getBulanan();
        $data['page'] = 'gelombang/data-bulanan';
        $this->load->view("main", $data);
    }

    public function create()
    {
        $validation = $this->form_validation;
        $validation->set_rules($this->Gelombang->rules());
        $data['page'] = 'gelombang/create';

        if ($validation->run()) {
            $res = $this->Gelombang->save();
            if ($res) {
                $this->session->set_flashdata('success', 'Berhasil disimpan');
                redirect(base_url('gelombang'));
            }
        }

        $this->load->view('main', $data);
    }

    public function update($id)
    {
        if (!isset($id)) redirect(site_url('admin/datatinggi'));
        $data['page'] = 'gelombang/update';
        $validation = $this->form_validation;
        $validation->set_rules($this->Gelombang->rules());

        if ($validation->run()) {
            $res = $this->Gelombang->update($id);
            if ($res) {
                $this->session->set_flashdata('success', 'Berhasil diupdate');
                redirect(site_url('gelombang'));
            }
        }

        $data["datagelombang"] = $this->Gelombang->getById($id)[0];
        if (!$data["datagelombang"]) {
            show_404();
        }
        $this->load->view("main", $data);
    }

    public function delete($id = null)
    {
        if (!isset($id)) show_404();

        if ($this->Gelombang->delete($id)) {
            $this->session->set_flashdata('success', 'Berhasil dihapus');
            redirect(base_url('gelombang'));
        }
    }

    public function doupload()
    {
        // $config['upload_path']          = './assets/files/upload/';
        // $config['allowed_types']        = 'csv';
        // $config['max_size']             = 500;
        $new_name                       = time() . $_FILES["file"]['name'];
        // $config['file_name']            = $new_name;
        //print_r($_FILES['file']);

        move_uploaded_file($_FILES['file']['tmp_name'], './assets/files/upload/' . $new_name);

        $file = fopen('./assets/files/upload/' . $new_name, 'r');
        $mulai = false;
        while (($row = fgetcsv($file, 1000, ";")) !== FALSE) {
            if ($mulai) $this->db->insert('datagelombang', [
                'd_tanggal' => $row[0],
                'd_tinggi' => str_replace(',', '.', $row[1])
            ]);
            $mulai = true;
        }
        fclose($file);

        //require_once APPPATH . "/third_party/PhpSpreadsheet-1.14.1/src/PhpSpreadsheet/IOFactory.php";

        //$excelReader = \PhpOffice\PhpSpreadsheet\IOFactory::load('./assets/files/upload/' . $new_name);

        //echo $baris_terakhir;

        // $this->load->library('upload', $config);

        // if ($this->upload->do_upload()) {
        //     echo 1;
        // } else {
        //     print_r($this->upload->display_errors());
        // }

        $this->session->set_flashdata('success', 'Berhasil diupload');
        redirect(base_url('gelombang'));
    }
    
}
