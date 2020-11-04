<?php
class Terminal extends CI_Controller
{

    public function auto_prediksi()
    {
        $this->load->model("M_Gelombang", "Gelombang");
        $this->load->model('M_prediksi', 'Prediksi');
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

        $at = array_sum($res['Yt']) / count($res['Yt']);
        foreach ($res['Yt'] as $k => $v) {
            $res['St'][] = floatval($res['tinggi'][$k]) - $at;
        }

        $res['St-L'] = array_merge(array_fill(0, $l, ''), $res['St']);

        // Proses Penghitungan : (At, Tt) -> St -> St-L
        $l = 12;
        foreach ($res['tinggi'] as $k => $v) {
            if ($k >= $l - 1) {
                if ($k == ($l - 1)) {
                    $res['At'][] = array_sum($res['Yt']) / count($res['Yt']);
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
        $l = 12;
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
        for ($x = 0; $x < $l; $x++) {
            $date->modify('+1 day');
            array_push($res['bulan_tahun'], $date->format('Y-m-d'));
        }

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
        $tabel .= '</tbody></table>';
        $res['tabel'] = $tabel;

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
        $l = 7;
        for ($i = 0; $i < $l; $i++) {
            $res['Yt'][] = floatval($res['tinggi'][$l + $i]) - floatval($res['tinggi'][$i]);
        }

        $at = array_sum($res['Yt']) / count($res['Yt']);
        foreach ($res['Yt'] as $k => $v) {
            $res['St'][] = floatval($res['tinggi'][$k]) - $at;
        }

        $res['St-L'] = array_merge(array_fill(0, $l, ''), $res['St']);

        // Proses Penghitungan : (At, Tt) -> St -> St-L
        $l = 7;
        foreach ($res['tinggi'] as $k => $v) {
            if ($k >= $l - 1) {
                if ($k == ($l - 1)) {
                    $res['At'][] = array_sum($res['Yt']) / count($res['Yt']);
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
        $l = 7;
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
        }

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
}
