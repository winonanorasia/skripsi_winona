<?php defined('BASEPATH') or exit('No direct script access allowed');

class M_terbaik extends CI_Model
{
	public function get_terbaik() {
		return $terbaik = [
			'bulanan-mse' => $this->db->query("select * from terbaik where jenis='bulanan' and mse is not null")->first_row(),
			'bulanan-mape' => $this->db->query("select * from terbaik where jenis='bulanan' and mape is not null")->first_row(),
            'harian-mse' =>  $this->db->query("select * from terbaik where jenis='harian' and mse is not null")->first_row(),
			'harian-mape' =>  $this->db->query("select * from terbaik where jenis='harian' and mape is not null")->first_row()
		];
	}
	
	public function perbarui() {		
		$terbaik = [
			'bulanan-mse' => $this->db->query("SELECT * FROM hasilprediksi WHERE jenis='bulanan' ORDER BY mse LIMIT 1")->first_row(),
			'bulanan-mape' => $this->db->query("SELECT * FROM hasilprediksi WHERE jenis='bulanan' ORDER BY mape LIMIT 1")->first_row(),
            'harian-mse' =>  $this->db->query("SELECT * FROM hasilprediksi WHERE jenis='harian' ORDER BY mse LIMIT 1")->first_row(),
			'harian-mape' =>  $this->db->query("SELECT * FROM hasilprediksi WHERE jenis='harian' ORDER BY mape LIMIT 1")->first_row()
		];
		
		//hapus data lama
		$this->db->query("TRUNCATE terbaik");
		
		//bulanan-mse
		$this->db->insert('terbaik', [
			'h_id' => @$terbaik['bulanan-mse']->h_id,
			'jenis' => 'bulanan',
			'alpha' => @$terbaik['bulanan-mse']->alpha,
			'beta' => @$terbaik['bulanan-mse']->beta,
			'gamma' => @$terbaik['bulanan-mse']->gamma,
			'mse' => @$terbaik['bulanan-mse']->mse,
			'mape' => NULL
		]);
		//bulanan-mape
		$this->db->insert('terbaik', [
			'h_id' => @$terbaik['bulanan-mape']->h_id,
			'jenis' => 'bulanan',
			'alpha' => @$terbaik['bulanan-mape']->alpha,
			'beta' => @$terbaik['bulanan-mape']->beta,
			'gamma' => @$terbaik['bulanan-mape']->gamma,
			'mse' => NULL,
			'mape' => @$terbaik['bulanan-mape']->mape
		]);
		
		//harian-mse
		$this->db->insert('terbaik', [
			'h_id' => @$terbaik['harian-mse']->h_id,
			'jenis' => 'harian',
			'alpha' => @$terbaik['harian-mse']->alpha,
			'beta' => @$terbaik['harian-mse']->beta,
			'gamma' => @$terbaik['harian-mse']->gamma,
			'mse' => @$terbaik['harian-mse']->mse,
			'mape' => NULL
		]);
		//harian-mape
		$this->db->insert('terbaik', [
			'h_id' => @$terbaik['harian-mape']->h_id,
			'jenis' => 'harian',
			'alpha' => @$terbaik['harian-mape']->alpha,
			'beta' => @$terbaik['harian-mape']->beta,
			'gamma' => @$terbaik['harian-mape']->gamma,
			'mse' => NULL,
			'mape' => @$terbaik['harian-mape']->mape
		]);
	}
}