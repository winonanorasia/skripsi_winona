<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Home extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		if ($this->session->userdata('login') !== true) {
			redirect(base_url('login'));
		}
		$this->load->model('M_users', 'Info');
		$this->load->model('M_prediksi', 'Prediksi');
	}

	public function index()
	{
		$data['title'] = 'Home';
		$data['page'] = 'home/overview';
		$data['page1'] = 'home/overview1';
		$this->load->view('main', $data);
	}

	public function profil()
	{
		$data['title'] = 'Home';
		$data['page'] = 'dashboard/profil';
		$this->load->view('main', $data);
	}
}
