<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Prediksi extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		if ($this->session->userdata('login') !== true) {
			redirect(base_url('login'));
		}

		$this->load->model("M_Gelombang", "Gelombang");
		$this->load->model('M_prediksi', 'Prediksi');
		$this->load->model('M_terbaik', 'Terbaik');
	}

	public function index($page = 1)
	{
		$this->load->library('pagination');
		//$data["hasilprediksi"] = $this->Prediksi->getAll();
		$data['page'] = 'hasilprediksi/data';

		$jml_data = $this->db->query("explain select count(h_id) from hasilprediksi")->first_row()->rows;

		//konfigurasi pagination
		$config['base_url'] = site_url('prediksi/index'); //site url
		//$config['total_rows'] = $this->db->count_all('hasilprediksi'); //total row
		$config['total_rows'] = $jml_data; //total row
		$config['per_page'] = 20;  //show record per halaman
		$config["uri_segment"] = 3;  // uri parameter
		//$choice = $config["total_rows"] / $config["per_page"];
		$config["num_links"] = 3;

		// Membuat Style pagination untuk BootStrap v4
		$config['first_link']       = 'First';
		$config['last_link']        = 'Last';
		$config['next_link']        = 'Next';
		$config['prev_link']        = 'Prev';
		$config['full_tag_open']    = '<div class="pagging text-center"><nav><ul class="pagination justify-content-center">';
		$config['full_tag_close']   = '</ul></nav></div>';
		$config['num_tag_open']     = '<li class="page-item"><span class="page-link">';
		$config['num_tag_close']    = '</span></li>';
		$config['cur_tag_open']     = '<li class="page-item active"><span class="page-link">';
		$config['cur_tag_close']    = '<span class="sr-only">(current)</span></span></li>';
		$config['next_tag_open']    = '<li class="page-item"><span class="page-link">';
		$config['next_tagl_close']  = '<span aria-hidden="true">&raquo;</span></span></li>';
		$config['prev_tag_open']    = '<li class="page-item"><span class="page-link">';
		$config['prev_tagl_close']  = '</span>Next</li>';
		$config['first_tag_open']   = '<li class="page-item"><span class="page-link">';
		$config['first_tagl_close'] = '</span></li>';
		$config['last_tag_open']    = '<li class="page-item"><span class="page-link">';
		$config['last_tagl_close']  = '</span></li>';

		$this->pagination->initialize($config);
		$data['halaman'] = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;

		//panggil function get_mahasiswa_list yang ada pada mmodel mahasiswa_model. 
		$data['data'] = $this->Prediksi->get_prediksi_list($config["per_page"], $data['halaman']);

		$data['pagination'] = $this->pagination->create_links();

		$data['nomor_halaman'] = $page;

		$data['terbaik'] = $this->Terbaik->get_terbaik();

		$this->load->view("main", $data);
	}

	public function perbarui_hasil_terbaik()
	{
		$this->Terbaik->perbarui();
		redirect(base_url('prediksi'));
	}

	public function createbulanan()
	{
		$data['page'] = 'hasilprediksi/create';
		//$data['tahun'] = $this->Gelombang->getTahun();
		$this->load->view("main", $data);
	}

	public function delete($id = null)
	{
		if (!isset($id)) show_404();

		if ($this->Prediksi->delete($id)) {
			$this->session->set_flashdata('success', 'Berhasil dihapus');
			redirect(base_url('prediksi'));
		}
	}

	public function predik()
	{
		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			$input = $this->input->post();

			$data['result'] = $this->HoltWinters($input);
			$namafile = 'predikHoltWintersAES_' . date('Ymd') . '-' . $input['alpha'] . '-' . $input['beta'] . '-' . $input['gamma'] . '-' . $input['l'];

			$this->saveToCSV($data['result'], $namafile, $input);
			$this->Prediksi->save($namafile, $data['result']);

			$this->session->set_flashdata('success', 'Berhasil diprediksi');
			redirect(site_url("prediksi"));
		} else {
			$this->session->set_flashdata('danger', 'Hasil Prediksi tidak ditemukan');
			redirect(site_url("prediksi"));
		}
	}

	public function detail($id)
	{
		$data['page'] = 'hasilprediksi/detail';
		$data['id'] = $id;
		$data['detail'] = $this->Prediksi->getById($id);
		$data['hasil'] = $this->getDataCSV($data['detail']->h_dokumen);
		$this->load->view('main', $data);
	}

	public function getBulan($thn)
	{
		return $this->Gelombang->getBulan($thn);
	}

	public function create()
	{
		$input = $this->input->post();
		if (!empty($input)) {
			if ($input['jenis'] == 'harian') {
				$input['data'] = $this->Gelombang->getAll();
				$hasil = $this->prediksi_harian($input);
			} else {
				$input['data'] = $this->Gelombang->getBulanan();
				$hasil = $this->prediksi_bulanan($input);
			}
			$data['hasil'] = $hasil;
		}

		$data['page'] = 'hasilprediksi/create';
		//$data['tahun'] = $this->Gelombang->getTahun();
		$this->load->view("main", $data);
	}

	private function prediksi_bulanan($input)
	{
		$data = json_decode(json_encode($input['data']), true);
		$res['bulan_tahun'] = array_column($data, 'bulan_tahun');
		$res['periode'] = array_column($data, 'periode');
		$res['tinggi'] = array_column($data, 'tinggi');

		//inisial
		// $l = $input['l'];
		$l = 12;

		for ($i = 0; $i < $l; $i++) {
			$res['Yt'][] = floatval($res['tinggi'][$l + $i]) - floatval($res['tinggi'][$i]);
		}
		$rata2 = array_slice($res['tinggi'], 0, 13);
		$at = array_sum($rata2) / count($rata2);

		foreach ($res['Yt'] as $k => $v) {
			$res['St'][] = floatval($res['tinggi'][$k]) - $at;
		}

		$res['St-L'] = array_merge(array_fill(0, $l, ''), $res['St']);

		// Proses Penghitungan : (At, Tt) -> St -> St-L
		foreach ($res['tinggi'] as $k => $v) {
			if ($k >= $l - 1) {
				if ($k == ($l - 1)) {
					$res['At'][] = array_sum($rata2) / count($rata2);
					$res['Tt'][] =  array_sum($res['Yt']) / count($res['Yt']) ** 2;
					$res['Ft'][] = '';
				} else {
					$res['At'][] = floatval($input['alpha']) * (floatval($res['tinggi'][$k]) - $res['St-L'][$k]) + (1 - floatval($input['alpha'])) * ($res['At'][$k - 1] + $res['Tt'][$k - 1]);
					$res['Tt'][] = floatval($input['beta']) * ($res['At'][$k] - $res['At'][$k - 1]) + (1 - floatval($input['beta'])) * $res['Tt'][$k - 1];
					$res['St'][] = floatval($input['gamma']) * (floatval($res['tinggi'][$k]) - $res['At'][$k]) + (1 - floatval($input['gamma'])) * $res['St-L'][$k];
					$res['St-L'][] = $res['St'][$k];

					// forecaset Ft

					$res['Ft'][] = $res['At'][$k - 1] + $res['Tt'][$k - 1] + $res['St-L'][$k];
					if ($k == count($res['tinggi']) - 1) {
						for ($i = 1; $i <= $l; $i++) {
							$res['Ft'][] = $res['At'][$k] + $res['Tt'][$k] * $i + $res['St-L'][$k + $i];
						}
					}
				}
			} else {
				$res['At'][] = '';
				$res['Tt'][] = '';
				$res['Ft'][] = '';
			}
		}

		//perhitungan error
		/**
		 * Ft = hasil prediksi
		 * $val = data asli
		 */
		foreach ($res['tinggi'] as $k => $val) {
			if ($k >= $l) {
				$res['Xt-Ft'][] = $val - $res['Ft'][$k];
				$res['(Et)^2'][] = $res['Xt-Ft'][$k] ** 2;
				$res['((Xt-Ft)/Xt)*100'][] = (($val - $res['Ft'][$k]) / $val) * 100;
				$res['|((Xt-Ft)/Xt)*100|'][] = abs((($val - $res['Ft'][$k]) / $val) * 100);
			} else {
				$res['Xt-Ft'][] = '';
				$res['(Et)^2'][] = '';
				$res['((Xt-Ft)/Xt)*100'][] = '';
				$res['|((Xt-Ft)/Xt)*100|'][] = '';
			}
		}

		$index_2019 = array_search('01-2019', $res['bulan_tahun'], TRUE);
		$res['mse'] = array_sum(array_slice($res['(Et)^2'], $index_2019, 12)) / 12;
		$res['mape'] = array_sum(array_slice($res['|((Xt-Ft)/Xt)*100|'], $index_2019, 12)) / 12;

		//menambah tanggal
		$date = new DateTime('01-' . array_slice($res['bulan_tahun'], -1, 1)[0]);
		// echo $res['mape'];
		for ($x = 0; $x < $l; $x++) {
			$date->modify('+1 month');
			array_push($res['bulan_tahun'], $date->format('m-Y'));
			array_push($res['periode'], $x + 1);
		}

		$index_2020 = array_search('01-2020', $res['bulan_tahun'], TRUE);

		//cetak tabel
		$kolom = ['bulan_tahun', 'tinggi', 'periode', 'Yt', 'At', 'Tt', 'St-L', 'St', 'Ft', 'Xt-Ft', '(Et)^2', '((Xt-Ft)/Xt)*100', '|((Xt-Ft)/Xt)*100|'];
		$mse = round($res['mse'], 3);
		$mape = round($res['mape'], 3);
		$tabel = "<hr><p><b>MSE: $mse</b></p>";
		$tabel .= "<p><b>MAPE: $mape</b></p>";
		$tabel .= '<hr><table id="" class="table table-striped table-bordered"><thead><tr>';
		foreach ($kolom as $x) {
			$tabel .= "<th>$x</th>";
		}
		$tabel .= '</tr></thead><tbody>';
		for ($x = 0; $x < count($res['Ft']); $x++) {
			$tabel .= '<tr>';
			foreach ($kolom as $y) {
				$teks = @$res[$y][$x];
				if (is_float($teks)) $teks = round($teks, 2);
				if (($y == 'Xt-Ft' | $y == '(Et)^2' | $y == '((Xt-Ft)/Xt)*100' | $y == '|((Xt-Ft)/Xt)*100|') & $x < $index_2019) {
					$teks = '';
				}
				$tabel .= "<td>$teks</td>";
			}
			$tabel .= '</tr>';
		}

		// $data2020 = array_slice($res["Ft"], $index_2020);
		// print_r($data2020);
		// var_dump(array_slice($res['Ft'], $index_2020));

		// echo "<br>"; 
		// print_r(array_slice($res["Ft"], 0));
		// echo "<br>"; 
		// echo sizeof($res['Ft']);
		// echo "<br>"; 
		// echo sizeof($res['tinggi']);
		// echo "<br>"; 
		// echo sizeof($res['Yt']);

		// for($x = 84; $x < 12; $x++) {
		// echo $res['Ft'][$x];
		// echo "<br>";
		//   }
		// echo $res['Ft'];

		$tabel .= '</tbody></table>';
		// echo array_slice($tabel, $index_2020, 12);
		$res['tabel'] = $tabel;
		// echo $res['tabel'];

		$this->Prediksi->save([
			'jenis' => 'bulanan',
			'alpha' => $input['alpha'],
			'beta' => $input['beta'],
			'gamma' => $input['gamma'],
			'mse' => $res['mse'],
			'mape' => $res['mape']
		]);

		return $res;
	}
	private function prediksi_harian($input)
	{
		$data = json_decode(json_encode($input['data']), true);
		$res['tanggal'] = array_column($data, 'd_tanggal');
		$res['periode'] = array_column($data, 'periode');
		$res['tinggi'] = array_column($data, 'd_tinggi');

		//inisial
		// $l = $input['l'];
		$l = 31;
		for ($i = 0; $i < $l; $i++) {
			$res['Yt'][] = floatval($res['tinggi'][$l + $i]) - floatval($res['tinggi'][$i]);
		}

		$rata2 = array_slice($res['tinggi'], 0, 32);
		$at = array_sum($rata2) / count($rata2);

		foreach ($res['Yt'] as $k => $v) {
			$res['St'][] = floatval($res['tinggi'][$k]) - $at;
		}

		$res['St-L'] = array_merge(array_fill(0, $l, ''), $res['St']);

		// Proses Penghitungan : (At, Tt) -> St -> St-L

		foreach ($res['tinggi'] as $k => $v) {
			if ($k >= $l - 1) {
				if ($k == ($l - 1)) {
					$res['At'][] = array_sum($rata2) / count($rata2);
					$res['Tt'][] =  array_sum($res['Yt']) / count($res['Yt']) ** 2;
					$res['Ft'][] = '';
				} else {
					$res['At'][] = floatval($input['alpha']) * (floatval($res['tinggi'][$k]) - $res['St-L'][$k]) + (1 - floatval($input['alpha'])) * ($res['At'][$k - 1] + $res['Tt'][$k - 1]);
					$res['Tt'][] = floatval($input['beta']) * ($res['At'][$k] - $res['At'][$k - 1]) + (1 - floatval($input['beta'])) * $res['Tt'][$k - 1];
					$res['St'][] = floatval($input['gamma']) * (floatval($res['tinggi'][$k]) - $res['At'][$k]) + (1 - floatval($input['gamma'])) * $res['St-L'][$k];
					$res['St-L'][] = $res['St'][$k];

					// forecaset Ft
					$res['Ft'][] = $res['At'][$k - 1] + $res['Tt'][$k - 1] + $res['St-L'][$k];
					if ($k == count($res['tinggi']) - 1) {
						for ($i = 1; $i <= $l; $i++) {
							$res['Ft'][] = $res['At'][$k] + $res['Tt'][$k] * $i + $res['St-L'][$k + $i];
						}
					}
				}
			} else {
				$res['At'][] = '';
				$res['Tt'][] = '';
				$res['Ft'][] = '';
			}
		}

		//perhitungan error
		/**
		 * Ft = hasil prediksi
		 * $val = data asli
		 */
		foreach ($res['tinggi'] as $k => $val) {
			if ($k >= $l) {
				$res['Xt-Ft'][] = $val - $res['Ft'][$k];
				$res['(Et)^2'][] = $res['Xt-Ft'][$k] ** 2;
				$res['((Xt-Ft)/Xt)*100'][] = (($val - $res['Ft'][$k]) / $val) * 100;
				$res['|((Xt-Ft)/Xt)*100|'][] = abs((($val - $res['Ft'][$k]) / $val) * 100);
			} else {
				$res['Xt-Ft'][] = '';
				$res['(Et)^2'][] = '';
				$res['((Xt-Ft)/Xt)*100'][] = '';
				$res['|((Xt-Ft)/Xt)*100|'][] = '';
			}
		}

		$index_2019 = array_search('2019-01-01', $res['tanggal'], TRUE);
		$res['mse'] = array_sum(array_slice($res['(Et)^2'], $index_2019, 365)) / 365;
		$res['mape'] = array_sum(array_slice($res['|((Xt-Ft)/Xt)*100|'], $index_2019, 365)) / 365;

		//menambah tanggal

		$date = new DateTime(array_slice($res['tanggal'], -1, 1)[0]);
		for ($x = 0; $x < $l; $x++) {
			$date->modify('+1 day');
			array_push($res['tanggal'], $date->format('Y-m-d'));
			array_push($res['periode'], $x + 1);
		}

		$index_2020 = array_search('2020-01-01', $res['tanggal'], TRUE);

		//cetak tabel
		$kolom = ['tanggal', 'tinggi', 'periode', 'Yt', 'At', 'Tt', 'St-L', 'St', 'Ft', 'Xt-Ft', '(Et)^2', '((Xt-Ft)/Xt)*100', '|((Xt-Ft)/Xt)*100|'];
		$mse = round($res['mse'], 3);
		$mape = round($res['mape'], 3);
		$tabel = "<hr><p><b>MSE: $mse</b></p>";
		$tabel .= "<p><b>MAPE: $mape</b></p>";
		$tabel .= '<hr><table id="dataTable" class="table table-striped table-bordered"><thead><tr>';
		foreach ($kolom as $x) {
			$tabel .= "<th>$x</th>";
		}
		$tabel .= '</tr></thead><tbody>';
		for ($x = 0; $x < count($res['Ft']); $x++) {
			$tabel .= '<tr>';
			foreach ($kolom as $y) {
				$teks = @$res[$y][$x];
				if (is_float($teks)) $teks = round($teks, 2);
				if (($y == 'Xt-Ft' | $y == '(Et)^2' | $y == '((Xt-Ft)/Xt)*100' | $y == '|((Xt-Ft)/Xt)*100|') & $x < $index_2019) {
					$teks = '';
				}
				$tabel .= "<td>$teks</td>";
			}
			$tabel .= '</tr>';
		}
		$data2020 = array_slice($res["Ft"], $index_2020);
		//print_r($data2020);
		$tabel .= '</tbody></table>';
		$res['tabel'] = $tabel;

		$this->Prediksi->save([
			'jenis' => 'harian',
			'alpha' => $input['alpha'],
			'beta' => $input['beta'],
			'gamma' => $input['gamma'],
			'mse' => $res['mse'],
			'mape' => $res['mape']
		]);

		return $res;
	}

	public function auto()
	{
		if (!empty($_POST)) {
			//bulanan
			$data_bulanan = $this->Gelombang->getBulanan();
			for ($alpha = 0.1; $alpha < 0.9; $alpha += 0.1) {
				for ($beta = 0.1; $beta < 0.9; $beta += 0.1) {
					for ($gamma = 0.1; $gamma < 0.9; $gamma += 0.1) {
						//echo $alpha . '-' . $beta . '-' . $gamma . '|';
						$input['data'] = $data_bulanan;
						$input['alpha'] = $alpha;
						$input['beta'] = $beta;
						$input['gamma'] = $gamma;
						$this->prediksi_bulanan($input);
					}
				}
			}

			//harian
			$data_harian = $this->Gelombang->getAll();
			for ($alpha = 0.1; $alpha < 0.9; $alpha += 0.1) {
				for ($beta = 0.1; $beta < 0.9; $beta += 0.1) {
					for ($gamma = 0.1; $gamma < 0.9; $gamma += 0.1) {
						// echo $alpha . '-' . $beta . '-' . $gamma . '|';
						$input['data'] = $data_harian;
						$input['alpha'] = $alpha;
						$input['beta'] = $beta;
						$input['gamma'] = $gamma;
						$this->prediksi_harian($input);
					}
				}
			}
		}
		$data['page'] = 'hasilprediksi/auto';
		$this->load->view('main', $data);
	}

	public function rincian($id)
	{
		$tmp = $this->Prediksi->getById($id);
		$data['page'] = 'hasilprediksi/rincian';
		$data['alpha'] = $tmp->alpha;
		$data['beta'] = $tmp->beta;
		$data['gamma'] = $tmp->gamma;
		if ($tmp->jenis == 'harian') {
			$data['data'] = $this->Gelombang->getAll();
			$data['hasil'] = $this->prediksi_harian($data);
		} else {
			$data['data'] = $this->Gelombang->getBulanan();
			$data['hasil'] = $this->prediksi_bulanan($data);
		}
		$this->load->view('main', $data);
	}

	// private function HoltWinters($input)
	// {

	// 	$data = json_decode(json_encode($input['data']), true);

	// 	$res = [];
	// 	$res['tahun'] = array_column($data, 'd_tahun');
	// 	$res['bulan'] = array_column($data, 'd_bulan');
	// 	$res['periode'] = array_column($data, 'd_periode');
	// 	$res['tinggi'] = array_column($data, 'd_tinggi');

	// 	$res = $this->getInisial($res, $input);
	// 	$res = $this->foreCast($res, $input);
	// 	$res = $this->getError($res, $input);

	// 	$res = $this->prepareData($res, $input);
	// 	return $res;
	// }

	// function getInisial($res, $in)
	// {
	// 	$l = 7;
	// 	for ($i = 0; $i < $l; $i++) {
	// 		$res['Yt'][] = floatval($res['tinggi'][$l + $i]) - floatval($res['tinggi'][$i]);
	// 	}

	// 	$at = array_sum($res['Yt']) / count($res['Yt']);
	// 	foreach ($res['Yt'] as $k => $v) {
	// 		$res['St'][] = floatval($res['tinggi'][$k]) - $at;
	// 	}

	// 	$res['St-L'] = array_merge(array_fill(0, $l, ''), $res['St']);

	// 	return $res;
	// }

	// function foreCast($res, $in)
	// {
	// 	// Proses Penghitungan : (At, Tt) -> St -> St-L
	// 	$l = 31;
	// 	foreach ($res['tinggi'] as $k => $v) {
	// 		if ($k >= $l - 1) {
	// 			if ($k == ($l - 1)) {
	// 				$res['At'][] = array_sum($res['Yt']) / count($res['Yt']);
	// 				$res['Tt'][] =  array_sum($res['Yt']) / count($res['Yt']) ** 2;
	// 				$res['Ft+m'][] = '';
	// 			} else {
	// 				$res['At'][] = floatval($in['alpha']) * (floatval($res['tinggi'][$k]) - $res['St-L'][$k]) + (1 - floatval($in['alpha'])) * ($res['At'][$k - 1] + $res['Tt'][$k - 1]);
	// 				$res['Tt'][] = floatval($in['beta']) * ($res['At'][$k] - $res['At'][$k - 1]) + (1 - floatval($in['beta'])) * $res['Tt'][$k - 1];
	// 				$res['St'][] = floatval($in['gamma']) * (floatval($res['tinggi'][$k]) - $res['At'][$k]) + (1 - floatval($in['gamma'])) * $res['St-L'][$k];
	// 				$res['St-L'][] = $res['St'][$k];

	// 				// forecaset Ft+m
	// 				$res['Ft+m'][] = $res['At'][$k - 1] + $res['Tt'][$k - 1] + $res['St-L'][$k];
	// 				if ($k == count($res['tinggi']) - 1) {
	// 					for ($i = 1; $i <= $l; $i++) {
	// 						$res['Ft+m'][] = $res['At'][$k] + $res['Tt'][$k] * $i + $res['St-L'][$k + $i];
	// 					}
	// 				}
	// 			}
	// 		} else {
	// 			$res['At'][] = '';
	// 			$res['Tt'][] = '';
	// 			$res['Ft+m'][] = '';
	// 		}
	// 	}

	// 	return $res;
	// }

	// function getError($res, $in)
	// {
	// 	$l = 7;
	// 	foreach ($res['tinggi'] as $k => $val) {
	// 		if ($k >= $l) {
	// 			$res['Da-Df'][] = $val - $res['Ft+m'][$k];
	// 			$res['|Da-Df|'][] = abs($res['Da-Df'][$k]);
	// 			$res['(Da-Df)^2'][] = $res['Da-Df'][$k] ** 2;
	// 			$res['|(Da-Df):Da|'][] = abs($res['Da-Df'][$k] / $val);
	// 		} else {
	// 			$res['Da-Df'][] = '';
	// 			$res['|Da-Df|'][] = '';
	// 			$res['(Da-Df)^2'][] = '';
	// 			$res['|(Da-Df):Da|'][] = '';
	// 		}
	// 	}

	// 	$res['mse'] = array_sum(array_slice($res['(Da-Df)^2'], -$l, $l)) / $l;
	// 	$res['mape'] = array_sum(array_slice($res['|(Da-Df):Da|'], -$l, $l)) / $l * 100;

	// 	return $res;
	// }

	// function prepareData($res, $in)
	// {
	// 	$l = 7;
	// 	$bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

	// 	$res['Yt'] = array_merge($res['Yt'], array_fill($l, count($res['tinggi']), ''));
	// 	$res['At'] = array_merge($res['At'], array_fill(count($res['At']), $l, ''));
	// 	$res['Tt'] = array_merge($res['Tt'], array_fill(count($res['Tt']), $l, ''));
	// 	$res['St'] = array_merge($res['St'], array_fill(count($res['St']), $l, ''));

	// 	$res['tinggi'] = array_merge($res['tinggi'], array_fill(count($res['tinggi']), $l, ''));
	// 	$th = $res['tahun'][count($res['tahun']) - 1];
	// 	$bln = array_search($res['bulan'][count($res['bulan']) - 1], $bulan) + 1;
	// 	$prd = $res['periode'][count($res['periode']) - 1] + 1;
	// 	for ($i = 0; $i < $l; $i++) {
	// 		if ($bln < 12) {
	// 			$res['bulan'][] = $bulan[$bln];
	// 			$res['tahun'][] = "$th";
	// 			$bln++;
	// 		} else {
	// 			$res['bulan'][] = $bulan[0];
	// 			$bln = 1;
	// 			$th++;
	// 			$res['tahun'][] = "$th";
	// 		}
	// 		$res['periode'][] = "" . $prd++;
	// 	}

	// 	$res['Da-Df'] = array_merge($res['Da-Df'], array_fill(count($res['Da-Df']), $l, ''));
	// 	$res['|Da-Df|'] = array_merge($res['|Da-Df|'], array_fill(count($res['|Da-Df|']), $l, ''));
	// 	$res['(Da-Df)^2'] = array_merge($res['(Da-Df)^2'], array_fill(count($res['(Da-Df)^2']), $l, ''));
	// 	$res['|(Da-Df):Da|'] =  array_merge($res['|(Da-Df):Da|'], array_fill(count($res['|(Da-Df):Da|']), $l, ''));

	// 	$ln = count($res['|Da-Df|']);
	// 	$res['Da-Df'][$ln - 12] = 'Total';
	// 	$res['|Da-Df|'][$ln - 12] = array_sum(array_slice($res['|Da-Df|'], - ($l + $l), $l));
	// 	$res['(Da-Df)^2'][$ln - 12] = array_sum(array_slice($res['(Da-Df)^2'], - ($l + $l), $l));
	// 	$res['|(Da-Df):Da|'][$ln - 12] = array_sum(array_slice($res['|(Da-Df):Da|'], - ($l + $l), $l));

	// 	return $res;
	// }

	// private function saveToCSV($res, $namafile, $input)
	// {
	// 	$write = [];
	// 	$no = 1;
	// 	foreach ($res['tinggi'] as $k => $val) {
	// 		$write[] = [
	// 			$no++,
	// 			$res['tahun'][$k],
	// 			$res['bulan'][$k],
	// 			$val,
	// 			$res['periode'][$k],
	// 			$res['Yt'][$k],
	// 			$res['At'][$k],
	// 			$res['Tt'][$k],
	// 			$res['St-L'][$k],
	// 			$res['St'][$k],
	// 			$res['Ft+m'][$k],
	// 			"",
	// 			$res['Da-Df'][$k],
	// 			$res['|Da-Df|'][$k],
	// 			$res['(Da-Df)^2'][$k],
	// 			$res['|(Da-Df):Da|'][$k]
	// 		];
	// 	}

	// 	$file = fopen("assets/files/prediksi/$namafile.csv", "w");
	// 	fputcsv($file, ["No", "Tahun", "Monthly", "Tinggi Gelombang", "Periode", "YL-t-Yt", "At", "Tt", "St-L", "St", "Forecast", "", "Da-Df", "|Da-Df|", "(Da-Df)^2", "|(Da-Df)/Da|"]);
	// 	foreach ($write as $line) {
	// 		fputcsv($file, $line);
	// 	}

	// 	fclose($file);
	// }

	// function getDataCSV($namafile)
	// {
	// 	$res = [];
	// 	if (($h = fopen("assets/files/prediksi/$namafile.csv", "r")) !== FALSE) {
	// 		while (($data = fgetcsv($h, 1000, ",")) !== FALSE) {
	// 			$data[10] = $data[10] == '' ? 0 : round($data[10], 2);
	// 			$res[] = $data;
	// 		}

	// 		fclose($h);
	// 	}
	// 	$res = array_slice($res, 1);
	// 	return $res;
	// }

	// public function getGraph($id)
	// {
	// 	$namafile = $this->Prediksi->getById($id)->h_dokumen;
	// 	$data = $this->getDataCSV($namafile);
	// 	$this->output
	// 		->set_status_header(200)
	// 		->set_content_type('application/json', 'utf-8')
	// 		->set_output(json_encode($data, JSON_PRETTY_PRINT))
	// 		->_display();
	// 	exit;
	// }

	// public function run() {
	// 	$res = [];
	// 	if (($h = fopen("assets/files/percobaan/run_6.csv", "r")) !== FALSE) {
	// 		while (($data = fgetcsv($h, 1000, ",")) !== FALSE) {
	// 			$data[0] = floatval($data[0]);
	// 			$data[1] = floatval($data[1]);
	// 			$data[2] = floatval($data[2]);
	// 			$res[] = $data;
	// 		}

	// 		fclose($h);
	// 	}
	// 	$res = array_slice($res,1);

	// 	foreach ($res as $key => $val) {
	// 		$_POST = [
	// 			'prdMulai' => 1,
	// 			'prdSampai' => 84,
	// 			'alpha' => $val[0],
	// 			'beta' => $val[1],
	// 			'gamma' => $val[2],
	// 			'jumlah_n' =>12,
	// 			'thnMulai'=>2013,
	// 			'thnSampai'=>2019
	// 		];

	// 		$input= $this->input->post();
	// 		$data['result'] = $this->HoltWinters($input);
	// 		$namafile = 'predikHoltWintersAES_'.date('Ymd').'-'.$input['alpha'].'-'.$input['beta'].'-'.$input['gamma'];

	// 		$this->saveToCSV($data['result'],$namafile,$input);
	// 		$this->Prediksi->save($namafile,$data['result']);
	// 		echo 'Done-'.($key+=1).'<br>';
	// 	}

	// }

}
