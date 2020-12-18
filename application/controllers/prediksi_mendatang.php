<?php
defined('BASEPATH') or exit('No direct script access allowed');

class prediksi_mendatang extends CI_Controller
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
        $this->load->model('M_periode', 'Periode');
        $this->load->model('M_hasilforecasting', 'HasilForcasting');
    }

    public function index($page = 1)
    {
        $this->load->library('pagination');
        //$data["hasilprediksi"] = $this->Prediksi->getAll();
        $data['page'] = 'hasilprediksi/data_mendatang';

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
        $data['page'] = 'hasilprediksi/create_mendatang';
        //$data['tahun'] = $this->Gelombang->getTahun();
        $this->load->view("main", $data);
    }

    public function delete($id = null)
    {
        if (!isset($id)) show_404();

        if ($this->Periode->delete($id)) {
            $this->session->set_flashdata('success', 'Berhasil dihapus');
            redirect(base_url('prediksi_mendatang'));
        }
    }

    public function predik()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $input = $this->input->post();

            $data['result'] = $this->HoltWinters($input);
            $namafile = 'predikHoltWintersAES_' . date('Ymd') . '-' . $input['alpha'] . '-' . $input['beta'] . '-' . $input['gamma'];

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

    public function create_mendatang()
    {
        $input = $this->input->get();

        if (!empty($input)) {
            $terbaik = $this->Terbaik->get_terbaik();

            $tmp = $this->Prediksi->getById($terbaik[$input['jenis']]->h_id);
            $input['alpha'] = $tmp->alpha;
            $input['beta'] = $tmp->beta;
            $input['gamma'] = $tmp->gamma;

            $data['mse'] = $tmp->mse;
            $data['mape'] = $tmp->mape;

            if ($input['jenis'] == 'harian-mse' | $input['jenis'] == 'harian-mape') {
                $input['data'] = $this->Gelombang->getAll();
                $hasil = $this->prediksi_harian($input);
            } else {
                $input['data'] = $this->Gelombang->getBulanan();
                $hasil = $this->prediksi_bulanan($input);
            }
            $data['hasil'] = $hasil;
            $mulai = count($input['data']) - 1 +  $_GET['periode-mulai'] ?? 0;
            $akhir = count($input['data']) +  $_GET['periode-akhir'] ?? 31;
            $tanggal = array_slice($hasil['tanggal'] ?? $hasil['bulan_tahun'], $mulai, $akhir - $mulai);
            $tinggi = array_slice($hasil['Ft'], count($input['data']));

            $this->HasilForcasting->save([
                'jenis' => $input['jenis'],
                'periode_mulai' => $input['periode-mulai'],
                'periode_akhir' => $input['periode-akhir'],
                'tabel' => $hasil['tabel'],
                'data' => json_encode([
                    'tanggal' => $tanggal,
                    'tinggi' => $tinggi,
                ]),
            ]);
        }

        $data['page'] = 'hasilprediksi/create_mendatang';
        $this->load->view("main", $data);
    }

    private function prediksi_bulanan($input)
    {

        $data = json_decode(json_encode($input['data']), true);
        $res['bulan_tahun'] = array_column($data, 'bulan_tahun');
        $res['periode'] = array_column($data, 'periode');
        $res['tinggi'] = array_column($data, 'tinggi');

        //inisial
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
        $l = 12;
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
                    $res['Ft'][] = round($res['At'][$k - 1] + $res['Tt'][$k - 1] + $res['St-L'][$k], 4);
                    if ($k == count($res['tinggi']) - 1) {
                        for ($i = 1; $i <= $l; $i++) {
                            $res['Ft'][] = round($res['At'][$k] + $res['Tt'][$k] * $i + $res['St-L'][$k + $i], 4);
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
        $l = 12;
        // foreach ($res['tinggi'] as $k => $val) {
        //     if ($k >= $l) {
        //         $res['Xt-Ft'][] = $val - $res['Ft'][$k];
        //         $res['(Et)^2'][] = $res['Xt-Ft'][$k] ** 2;
        //         $res['((Xt-Ft)/Xt)*100'][] = (($val - $res['Ft'][$k]) / $val) * 100;
        //         $res['|((Xt-Ft)/Xt)*100|'][] = abs((($val - $res['Ft'][$k]) / $val) * 100);
        //     } else {
        //         $res['Xt-Ft'][] = '';
        //         $res['(Et)^2'][] = '';
        //         $res['((Xt-Ft)/Xt)*100'][] = '';
        //         $res['|((Xt-Ft)/Xt)*100|'][] = '';
        //     }
        // }

        $index_2019 = array_search('01-2019', $res['bulan_tahun'], TRUE);
        // $res['mse'] = array_sum(array_slice($res['(Et)^2'], $index_2019, 12)) / 12;
        // $res['mape'] = array_sum(array_slice($res['|((Xt-Ft)/Xt)*100|'], $index_2019, 12)) / 12;

        $index_2020 = count($res['bulan_tahun']);

        //menambah tanggal
        $date = new DateTime('01-' . array_slice($res['bulan_tahun'], -1, 1)[0]);
        for ($x = 0; $x < $l; $x++) {
            $date->modify('+1 month');
            array_push($res['bulan_tahun'], $date->format('m-Y'));
            array_push($res['periode'], $x + 1);
        }

        //cetak tabel
        $kolom = ['bulan_tahun', 'periode', 'Ft'];
        $tabel = '';
        // $mse = round($res['mse'], 3);
        // $mape = round($res['mape'], 3);
        // $nilai_mse = "<hr><p><b>MSE: $mse</b></p>";
        // $nilai_mape = "<hr><p><b>MAPE: $mape</b></p>";
        // $tabel = "<hr><p><b>MSE: $mse</b></p>";
        // $tabel .= "<p><b>MAPE: $mape</b></p>";
        $tabel .= '<hr><table id="dataTable" class="table table-striped table-bordered"><thead><tr>';
        foreach ($kolom as $x) {
            $tabel .= "<th>$x</th>";
        }
        $tabel .= '</tr></thead><tbody>';

        $mulai = $_GET['periode-mulai'] ?? 0;
        $akhir = $_GET['periode-akhir'] ?? 0;

        for ($x = 0; $x < count($res['Ft']); $x++) {
            if ($x >= $index_2020 & $res['periode'][$x] >= $mulai & $res['periode'][$x] <= $akhir) {
                $tabel .= '<tr>';
                foreach ($kolom as $y) {
                    $teks = @$res[$y][$x];
                    if (is_float($teks)) $teks = round($teks, 4);
                    if (($y == 'Xt-Ft' | $y == '(Et)^2' | $y == '((Xt-Ft)/Xt)*100' | $y == '|((Xt-Ft)/Xt)*100|') & $x < $index_2019) {
                        $teks = '';
                    }
                    $tabel .= "<td>$teks</td>";
                }
            }
            $tabel .= '</tr>';
        }
        $tabel .= '</tbody></table>';
        // $tabel = array_slice($tabel,2);
        // $tabel = array_slice($, 0, 13);
        $res['tabel'] = $tabel;
        //  echo $res['tabel'];
        // $res['tabel1'] = array_slice($res['tabel'], 13);

        return $res;
    }

    private function prediksi_harian($input)
    {
        $data = json_decode(json_encode($input['data']), true);
        $res['tanggal'] = array_column($data, 'd_tanggal');
        $res['periode'] = array_column($data, 'periode');
        $res['tinggi'] = array_column($data, 'd_tinggi');

        //inisial
        $l = 31;
        for ($i = 0; $i < $l; $i++) {
            $res['Yt'][] = floatval($res['tinggi'][$l + $i]) - floatval($res['tinggi'][$i]);
        }

        $rata2 = array_slice($res['tinggi'], 0, 8);
        $at = array_sum($rata2) / count($rata2);

        foreach ($res['Yt'] as $k => $v) {
            $res['St'][] = floatval($res['tinggi'][$k]) - $at;
        }

        $res['St-L'] = array_merge(array_fill(0, $l, ''), $res['St']);

        // Proses Penghitungan : (At, Tt) -> St -> St-L
        $l = 31;
        // print_r($res['tinggi']);
        // exit();

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
                    $res['Ft'][] = round($res['At'][$k - 1] + $res['Tt'][$k - 1] + $res['St-L'][$k], 4);
                    if ($k == count($res['tinggi']) - 1) {
                        // 
                        for ($i = 1; $i <= $l; $i++) {
                            $res['Ft'][] = round($res['At'][$k] + $res['Tt'][$k] * $i + $res['St-L'][$k + $i], 4);
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
        // $l = 7;
        // foreach ($res['tinggi'] as $k => $val) {
        //     if ($k >= $l) {
        //         $res['Xt-Ft'][] = $val - $res['Ft'][$k];
        //         $res['(Et)^2'][] = $res['Xt-Ft'][$k] ** 2;
        //         $res['((Xt-Ft)/Xt)*100'][] = (($val - $res['Ft'][$k]) / $val) * 100;
        //         $res['|((Xt-Ft)/Xt)*100|'][] = abs((($val - $res['Ft'][$k]) / $val) * 100);
        //     } else {
        //         $res['Xt-Ft'][] = '';
        //         $res['(Et)^2'][] = '';
        //         $res['((Xt-Ft)/Xt)*100'][] = '';
        //         $res['|((Xt-Ft)/Xt)*100|'][] = '';
        //     }
        // }

        $index_2019 = array_search('2019-01-01', $res['tanggal'], TRUE);
        // $res['mse'] = array_sum(array_slice($res['(Et)^2'], $index_2019, 365)) / 365;
        // $res['mape'] = array_sum(array_slice($res['|((Xt-Ft)/Xt)*100|'], $index_2019, 365)) / 365;

        $index_2020 = count($res['tanggal']);

        //menambah tanggal
        $date = new DateTime(array_slice($res['tanggal'], -1, 1)[0]);
        for ($x = 0; $x < $l; $x++) {
            $date->modify('+1 day');
            array_push($res['tanggal'], $date->format('Y-m-d'));
            array_push($res['periode'], $x + 1);
        }

        // cetak tabel
        $kolom = ['tanggal', 'periode', 'Ft'];
        $tabel = '';
        // $mse = round($res['mse'], 3);
        // $mape = round($res['mape'], 3);
        // $tabel = "<hr><p><b>MSE: $mse</b></p>";
        // $tabel .= "<p><b>MAPE: $mape</b></p>";
        $tabel .= '<hr><table id="dataTable" class="table table-striped table-bordered"><thead><tr>';
        foreach ($kolom as $x) {
            $tabel .= "<th>$x</th>";
        }
        $tabel .= '</tr></thead><tbody>';

        $mulai = $_GET['periode-mulai'] ?? 0;
        $akhir = $_GET['periode-akhir'] ?? 0;

        for ($x = 0; $x < count($res['Ft']); $x++) {
            if ($x >= $index_2020 & $res['periode'][$x] >= $mulai & $res['periode'][$x] <= $akhir) {
                $tabel .= '<tr>';
                foreach ($kolom as $y) {
                    $teks = @$res[$y][$x];
                    if (is_float($teks)) $teks = round($teks, 4);
                    if (($y == 'Xt-Ft' | $y == '(Et)^2' | $y == '((Xt-Ft)/Xt)*100' | $y == '|((Xt-Ft)/Xt)*100|') & $x < $index_2019) {
                        $teks = '';
                    }
                    $tabel .= "<td>$teks</td>";
                }
                $tabel .= '</tr>';
            }
        }
        $tabel .= '</tbody></table>';
        $res['tabel'] = $tabel;

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
                        //echo $alpha . '-' . $beta . '-' . $gamma . '|';
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

    public function periode($id)
    {
        if ($id == 1) {
            $tmp = $this->Prediksi->getById($this->Terbaik->get_terbaik()['harian-mse']->h_id);
            $j = 'harian-mse';
        }
        if ($id == 2) {
            $tmp = $this->Prediksi->getById($this->Terbaik->get_terbaik()['harian-mape']->h_id);
            $j = 'harian-mape';
        }
        if ($id == 3) {
            $tmp = $this->Prediksi->getById($this->Terbaik->get_terbaik()['bulanan-mse']->h_id);
            $j = 'bulanan-mse';
        }
        if ($id == 4) {
            $tmp = $this->Prediksi->getById($this->Terbaik->get_terbaik()['bulanan-mape']->h_id);
            $j = 'bulanan-mape';
        }
        //$tmp = $this->Prediksi->getById($id);
        $data['page'] = 'hasilprediksi/periode';
        $data['alpha'] = $tmp->alpha;
        $data['beta'] = $tmp->beta;
        $data['gamma'] = $tmp->gamma;
        $data['data'] = $this->HasilForcasting->getByJenis($j);
        $this->load->view('main', $data);
    }


    public function rincian($id)
    {
        $tmp = $this->HasilForcasting->getById($id);

        $data['tabel'] = $tmp->tabel;
        $data['data'] = $tmp->data;
        $data['page'] = 'hasilprediksi/rincianmendatang';

        $tmp2 = $this->Prediksi->getById($this->Terbaik->get_terbaik()[$tmp->jenis]->h_id);

        $data['alpha'] = $tmp2->alpha;
        $data['beta'] = $tmp2->beta;
        $data['gamma'] = $tmp2->gamma;

        $this->load->view('main', $data);
    }

    // private function HoltWinters($input)
    // {

    //     $data = json_decode(json_encode($input['data']), true);

    //     $res = [];
    //     $res['tahun'] = array_column($data, 'd_tahun');
    //     $res['bulan'] = array_column($data, 'd_bulan');
    //     $res['periode'] = array_column($data, 'd_periode');
    //     $res['tinggi'] = array_column($data, 'd_tinggi');

    //     $res = $this->getInisial($res, $input);
    //     $res = $this->foreCast($res, $input);
    //     $res = $this->getError($res, $input);

    //     $res = $this->prepareData($res, $input);
    //     return $res;
    // }

    // function getInisial($res, $in)
    // {
    //     $l = 7;
    //     for ($i = 0; $i < $l; $i++) {
    //         $res['Yt'][] = floatval($res['tinggi'][$l + $i]) - floatval($res['tinggi'][$i]);
    //     }

    //     $at = array_sum($res['Yt']) / count($res['Yt']);
    //     foreach ($res['Yt'] as $k => $v) {
    //         $res['St'][] = floatval($res['tinggi'][$k]) - $at;
    //     }

    //     $res['St-L'] = array_merge(array_fill(0, $l, ''), $res['St']);

    //     return $res;
    // }

    // function foreCast($res, $in)
    // {
    //     // Proses Penghitungan : (At, Tt) -> St -> St-L
    //     $l = 7;
    //     foreach ($res['tinggi'] as $k => $v) {
    //         if ($k >= $l - 1) {
    //             if ($k == ($l - 1)) {
    //                 $res['At'][] = array_sum($res['Yt']) / count($res['Yt']);
    //                 $res['Tt'][] =  array_sum($res['Yt']) / count($res['Yt']) ** 2;
    //                 $res['Ft+m'][] = '';
    //             } else {
    //                 $res['At'][] = floatval($in['alpha']) * (floatval($res['tinggi'][$k]) - $res['St-L'][$k]) + (1 - floatval($in['alpha'])) * ($res['At'][$k - 1] + $res['Tt'][$k - 1]);
    //                 $res['Tt'][] = floatval($in['beta']) * ($res['At'][$k] - $res['At'][$k - 1]) + (1 - floatval($in['beta'])) * $res['Tt'][$k - 1];
    //                 $res['St'][] = floatval($in['gamma']) * (floatval($res['tinggi'][$k]) - $res['At'][$k]) + (1 - floatval($in['gamma'])) * $res['St-L'][$k];
    //                 $res['St-L'][] = $res['St'][$k];

    //                 // forecaset Ft+m
    //                 $res['Ft+m'][] = $res['At'][$k - 1] + $res['Tt'][$k - 1] + $res['St-L'][$k];
    //                 if ($k == count($res['tinggi']) - 1) {
    //                     for ($i = 1; $i <= $l; $i++) {
    //                         $res['Ft+m'][] = $res['At'][$k] + $res['Tt'][$k] * $i + $res['St-L'][$k + $i];
    //                     }
    //                 }
    //             }
    //         } else {
    //             $res['At'][] = '';
    //             $res['Tt'][] = '';
    //             $res['Ft+m'][] = '';
    //         }
    //     }

    //     return $res;
    // }

    // // function getError($res, $in)
    // {
    //     $l = 7;
    //     foreach ($res['tinggi'] as $k => $val) {
    //         if ($k >= $l) {
    //             $res['Da-Df'][] = $val - $res['Ft+m'][$k];
    //             $res['|Da-Df|'][] = abs($res['Da-Df'][$k]);
    //             $res['(Da-Df)^2'][] = $res['Da-Df'][$k] ** 2;
    //             $res['|(Da-Df):Da|'][] = abs($res['Da-Df'][$k] / $val);
    //         } else {
    //             $res['Da-Df'][] = '';
    //             $res['|Da-Df|'][] = '';
    //             $res['(Da-Df)^2'][] = '';
    //             $res['|(Da-Df):Da|'][] = '';
    //         }
    //     }

    //     $res['mse'] = array_sum(array_slice($res['(Da-Df)^2'], -$l, $l)) / $l;
    //     $res['mape'] = array_sum(array_slice($res['|(Da-Df):Da|'], -$l, $l)) / $l * 100;

    //     return $res;
    // }

    // function prepareData($res, $in)
    // {
    //     $l = 7;
    //     $bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    //     $res['Yt'] = array_merge($res['Yt'], array_fill($l, count($res['tinggi']), ''));
    //     $res['At'] = array_merge($res['At'], array_fill(count($res['At']), $l, ''));
    //     $res['Tt'] = array_merge($res['Tt'], array_fill(count($res['Tt']), $l, ''));
    //     $res['St'] = array_merge($res['St'], array_fill(count($res['St']), $l, ''));

    //     $res['tinggi'] = array_merge($res['tinggi'], array_fill(count($res['tinggi']), $l, ''));
    //     $th = $res['tahun'][count($res['tahun']) - 1];
    //     $bln = array_search($res['bulan'][count($res['bulan']) - 1], $bulan) + 1;
    //     $prd = $res['periode'][count($res['periode']) - 1] + 1;
    //     for ($i = 0; $i < $l; $i++) {
    //         if ($bln < 12) {
    //             $res['bulan'][] = $bulan[$bln];
    //             $res['tahun'][] = "$th";
    //             $bln++;
    //         } else {
    //             $res['bulan'][] = $bulan[0];
    //             $bln = 1;
    //             $th++;
    //             $res['tahun'][] = "$th";
    //         }
    //         $res['periode'][] = "" . $prd++;
    //     }

    //     $res['Da-Df'] = array_merge($res['Da-Df'], array_fill(count($res['Da-Df']), $l, ''));
    //     $res['|Da-Df|'] = array_merge($res['|Da-Df|'], array_fill(count($res['|Da-Df|']), $l, ''));
    //     $res['(Da-Df)^2'] = array_merge($res['(Da-Df)^2'], array_fill(count($res['(Da-Df)^2']), $l, ''));
    //     $res['|(Da-Df):Da|'] =  array_merge($res['|(Da-Df):Da|'], array_fill(count($res['|(Da-Df):Da|']), $l, ''));

    //     $ln = count($res['|Da-Df|']);
    //     $res['Da-Df'][$ln - 12] = 'Total';
    //     $res['|Da-Df|'][$ln - 12] = array_sum(array_slice($res['|Da-Df|'], - ($l + $l), $l));
    //     $res['(Da-Df)^2'][$ln - 12] = array_sum(array_slice($res['(Da-Df)^2'], - ($l + $l), $l));
    //     $res['|(Da-Df):Da|'][$ln - 12] = array_sum(array_slice($res['|(Da-Df):Da|'], - ($l + $l), $l));

    //     return $res;
    // }

    // private function saveToCSV($res, $namafile, $input)
    // {
    //     $write = [];
    //     $no = 1;
    //     foreach ($res['tinggi'] as $k => $val) {
    //         $write[] = [
    //             $no++,
    //             $res['tahun'][$k],
    //             $res['bulan'][$k],
    //             $val,
    //             $res['periode'][$k],
    //             $res['Yt'][$k],
    //             $res['At'][$k],
    //             $res['Tt'][$k],
    //             $res['St-L'][$k],
    //             $res['St'][$k],
    //             $res['Ft+m'][$k],
    //             "",
    //             $res['Da-Df'][$k],
    //             $res['|Da-Df|'][$k],
    //             $res['(Da-Df)^2'][$k],
    //             $res['|(Da-Df):Da|'][$k]
    //         ];
    //     }

    //     $file = fopen("assets/files/prediksi/$namafile.csv", "w");
    //     fputcsv($file, ["No", "Tahun", "Monthly", "Tinggi Gelombang", "Periode", "YL-t-Yt", "At", "Tt", "St-L", "St", "Forecast", "", "Da-Df", "|Da-Df|", "(Da-Df)^2", "|(Da-Df)/Da|"]);
    //     foreach ($write as $line) {
    //         fputcsv($file, $line);
    //     }

    //     fclose($file);
    // }

    // function getDataCSV($namafile)
    // {
    //     $res = [];
    //     if (($h = fopen("assets/files/prediksi/$namafile.csv", "r")) !== FALSE) {
    //         while (($data = fgetcsv($h, 1000, ",")) !== FALSE) {
    //             $data[10] = $data[10] == '' ? 0 : round($data[10], 2);
    //             $res[] = $data;
    //         }

    //         fclose($h);
    //     }
    //     $res = array_slice($res, 1);
    //     return $res;
    // }

    // public function getGraph($id)
    // {
    //     $namafile = $this->Prediksi->getById($id)->h_dokumen;
    //     $data = $this->getDataCSV($namafile);
    //     $this->output
    //         ->set_status_header(200)
    //         ->set_content_type('application/json', 'utf-8')
    //         ->set_output(json_encode($data, JSON_PRETTY_PRINT))
    //         ->_display();
    //     exit;
    // }

    // // public function run() {
    // // 	$res = [];
    // // 	if (($h = fopen("assets/files/percobaan/run_6.csv", "r")) !== FALSE) {
    // // 		while (($data = fgetcsv($h, 1000, ",")) !== FALSE) {
    // // 			$data[0] = floatval($data[0]);
    // // 			$data[1] = floatval($data[1]);
    // // 			$data[2] = floatval($data[2]);
    // // 			$res[] = $data;
    // // 		}

    // // 		fclose($h);
    // // 	}
    // // 	$res = array_slice($res,1);

    // // 	foreach ($res as $key => $val) {
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
