<?php
defined('BASEPATH') or exit('No direct script access allowed');


class Pengujian extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        if($this->session->userdata('login') !== true){redirect(base_url('login'));}
        
        $this->load->model("M_Gelombang","Gelombang");
        $this->load->model('M_pengujian','Pengujian');
    }

    public function index()
    {
        $data["hasilpengujian"] = $this->Pengujian->getAll();
        $data['page'] = 'hasilpengujian/data';
        $this->load->view("main", $data);
    }

    public function create()
    {
		$data['page'] = 'hasilpengujian/create';
		$data['tahun'] = $this->Gelombang->getTahun();
		$this->load->view("main", $data);
	}

	public function delete($id = null)
    {
        if (!isset($id)) show_404();

        if ($this->Pengujian->delete($id)) {
            $this->session->set_flashdata('success', 'Berhasil dihapus');
            redirect(base_url('pengujian'));
        }
    }
	
	public function uji()
	{
		if ($this->input->server('REQUEST_METHOD') == 'POST'){
			$input = $this->input->post();
			
			$data['result'] = $this->HoltWinters($input);
			$namafile = 'pengujianHoltWintersAES_'.date('Ymd').'-'.$input['alpha'].'-'.$input['beta'].'-'.$input['gamma'];

			$this->saveToCSV($data['result'],$namafile,$input);
			$this->Pengujian->save($namafile,$data['result']);

			$this->session->set_flashdata('success', 'Berhasil diuji');
			redirect(site_url("pengujian"));
		
		}else{
			$this->session->set_flashdata('danger', 'Hasil Pengujian tidak ditemukan');
			redirect(site_url("pengujian"));
		}
	}
    
    public function detail($id)
    {
        $data['page'] = 'hasilpengujian/detail';
		$data['id'] = $id;
		$data['detail'] = $this->Pengujian->getById($id);
		$data['hasil'] = $this->getDataCSV($data['detail']->h_dokumen);
		$this->load->view('main',$data);
	}
	
	public function getBulan($thn)
    {
        return $this->Gelombang->getBulan($thn);
	}

	private function getKFolds($data,$input){
		$fold = $input['jumlah_folds'];
		$tahun = array_values(array_unique($data['tahun']));
		$d = [];
		foreach ($data['tahun'] as $k => $val) {
			$d[$val][] = $data['tinggi'][$k];
		}
		$ctr = count($tahun) - $fold + 1;
		$splits = [];
		for ($i=0; $i <$fold ; $i++) { 
			$splits[] = array_slice($d, 0, $ctr+$i);
		}

		$akurasi_fold = [];
		foreach ($splits as $key => $value) {
			$akurasi_fold[] = array_sum($value[count($value)-1]) / count($value[count($value)-1]);
		}
		$pilih_fold = array_keys($akurasi_fold, min($akurasi_fold))[0];

		// prepare data
		$res['tinggi'] = [];
		foreach ($splits[$pilih_fold] as $key => $value) {
			$res['tinggi'] = array_merge($res['tinggi'],$value);
		}
		$res['tahun'] = array_slice($data['tahun'],0,count($res['tinggi']));
		$res['bulan'] = array_slice($data['bulan'],0,count($res['tinggi']));
		$res['periode'] = array_slice($data['periode'],0,count($res['tinggi']));

		return $res;
	}
	
	private function HoltWinters($input)
	{
		$data = $this->Gelombang->getByPeriod($input);
		$data = json_decode(json_encode($data), true);
		
		$res = [];
		$res['tahun'] = array_column($data, 'd_tahun');
		$res['bulan'] = array_column($data, 'd_bulan');
		$res['periode'] = array_column($data, 'd_periode');
		$res['tinggi'] = array_column($data, 'd_tinggi');

		// proses membagi data kedalam K-fold
		$res = $this->getKFolds($res,$input);

		$res = $this->getInisial($res,$input);
		$res = $this->foreCast($res,$input);
		$res = $this->getError($res,$input);

		$res = $this->prepareData($res,$input);
		return $res;
	}

	function getInisial($res,$in)
	{
		$l = intval($in['jumlah_n']);
		for ($i=0; $i < $l; $i++) { 
			$res['Yt'][] = floatval($res['tinggi'][$l+$i]) - floatval($res['tinggi'][$i]);
		}

		$at = array_sum($res['Yt']) / count($res['Yt']);
		foreach ($res['Yt'] as $k => $v) {
			$res['St'][] = floatval($res['tinggi'][$k]) - $at;
		}

		$res['St-L'] = array_merge(array_fill(0,$l,''), $res['St']);

		return $res;
	}

	function foreCast($res,$in)
	{
		// Proses Penghitungan : (At, Tt) -> St -> St-L
		$l = $in['jumlah_n'];
		foreach ($res['tinggi'] as $k => $v) {
			if($k >= $l-1){
				if($k == ($l-1)){
					$res['At'][] = array_sum($res['Yt'])/count($res['Yt']);
					$res['Tt'][] =  array_sum($res['Yt'])/count($res['Yt'])**2;
					$res['Ft+m'][] ='';
				}else{
					$res['At'][] = floatval($in['alpha']) * ( floatval($res['tinggi'][$k]) - $res['St-L'][$k] ) + (1 - floatval($in['alpha']) ) * ( $res['At'][$k-1] + $res['Tt'][$k-1]);
					$res['Tt'][] = floatval($in['beta']) * ( $res['At'][$k] - $res['At'][$k-1]) + (1 - floatval($in['beta'])) * $res['Tt'][$k-1];
					$res['St'][] = floatval($in['gamma']) * ( floatval($res['tinggi'][$k]) - $res['At'][$k] ) + (1 - floatval($in['gamma'])) * $res['St-L'][$k];
					if($k < count($res['tinggi'])-12){
						$res['St-L'][] = $res['St'][$k];
					}
					
					// forecaset Ft+m
					$res['Ft+m'][] = $res['At'][$k-1] + $res['Tt'][$k-1] + $res['St-L'][$k];
					// if($k == count($res['tinggi'])-1 ){
					// 	for ($i=1; $i <= $l; $i++) { 
					// 		$res['Ft+m'][] = $res['At'][$k] + $res['Tt'][$k] * $i + $res['St-L'][$k+$i];
					// 	}
					// }
				}
				
			}else{
				$res['At'][] = '';
				$res['Tt'][] = '';
				$res['Ft+m'][] ='';
			}
		}
		
		return $res;
	}

	function getError($res,$in)
	{
		$l = $in['jumlah_n'];
		foreach ($res['tinggi'] as $k => $val) {
			if($k >= $l){
				$res['Da-Df'][] = $val - $res['Ft+m'][$k];
				$res['|Da-Df|'][] = abs($res['Da-Df'][$k]);
				$res['(Da-Df)^2'][] = $res['Da-Df'][$k]**2;
				$res['|(Da-Df):Da|'][] = abs($res['Da-Df'][$k]/$val);
			}else{
				$res['Da-Df'][] = '';
				$res['|Da-Df|'][] = '';
				$res['(Da-Df)^2'][] = '';
				$res['|(Da-Df):Da|'][] = '';
			}
		}

		$res['mse'] = array_sum(array_slice($res['(Da-Df)^2'],-$l,$l)) / $l;
		$res['mape'] = array_sum(array_slice($res['|(Da-Df):Da|'],-$l,$l)) / $l * 100;

		return $res;
	}

	function prepareData($res,$in)
	{
		$l = $in['jumlah_n'];
		$bulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
		
		$res['Yt'] = array_merge( $res['Yt'],array_fill($l,count($res['tinggi'])-$l,''));
		
		return $res;
	}

	private function saveToCSV($res, $namafile, $input)
	{
		$write = [];
		$no = 1;
		foreach ($res['tinggi'] as $k => $val) {
			$write[] = [
				$no++,
				$res['tahun'][$k],
				$res['bulan'][$k],
				$val,
				$res['periode'][$k],
				$res['Yt'][$k],
				$res['At'][$k],
				$res['Tt'][$k],
				$res['St-L'][$k],
				$res['St'][$k],
				$res['Ft+m'][$k],
				"",
				$res['Da-Df'][$k],
				$res['|Da-Df|'][$k],
				$res['(Da-Df)^2'][$k],
				$res['|(Da-Df):Da|'][$k]
			];
		}

		$file = fopen("assets/files/pengujian/$namafile.csv","w");
		fputcsv($file, ["No","Tahun","Monthly","Tinggi Gelombang","Periode","YL-t-Yt","At","Tt","St-L","St","Forecast","","Da-Df","|Da-Df|","(Da-Df)^2","|(Da-Df)/Da|"]);
		foreach ($write as $line) {
			fputcsv($file, $line);
		}

		fclose($file);
	}

    function getDataCSV($namafile){
		$res = [];
		if (($h = fopen("assets/files/pengujian/$namafile.csv", "r")) !== FALSE) {
			while (($data = fgetcsv($h, 1000, ",")) !== FALSE) {
				$data[10] = $data[10] == '' ? 0 : round($data[10],2);
				$res[] = $data;
			}

			fclose($h);
		}
		$res = array_slice($res,1);
		return $res;
	}

	public function getGraph($id){
		$namafile = $this->Pengujian->getById($id)->h_dokumen;
		$data = $this->getDataCSV($namafile);
		$this->output
		->set_status_header(200)
		->set_content_type('application/json', 'utf-8')
		->set_output(json_encode($data, JSON_PRETTY_PRINT))
		->_display();
		exit;
	}

}