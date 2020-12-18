<div>
    <h1>Panduan</h1>
    <center>
        <img src="<?php echo base_url(); ?>gelombang2.png" width="400px" height="200px">
    </center>
    <hr>
    <table width="1300px">
        <tr>
            <th>Menu</th>
            <th></th>
            <th>Keterangan</th>
        </tr>
        <tr>
            <th>Dashboard</th>
            <th></th>
            <td>Halaman depan pada menu <?php echo ($this->session->userdata('u_username')) ?></td>
        </tr>
        <tr>
            <th>Data Tinggi Gelombang</th>
            <th></th>
            <td> Kita bisa melakukan aksi membuat, melihat, mengedit, menghapus data tersebut (Dapat diakses oleh Admin saja)</td>
        </tr>
        <tr>
            <th>Prediksi</th>
            <th></th>
            <td> Kita bisa melakukan prediksi dengan melihat perhitungan dan hasil akurasi (Dapat diakses oleh Admin saja)</td>
        </tr>
        <tr>
            <th>Prediksi Mendatang</th>
            <th></th>
            <td> Kita bisa melakukan prediksi mendatang dengan mengambil nilai yang terbaik(Dapat diakses oleh Admin dan Pegawai)</td>
        </tr>
        <tr>
            <th>Cetak</th>
            <th></th>
            <td>Cetak Nilai Prediksi yang keluar (.pdf)</td>
        </tr>
        <tr>
            <th>Logout</th>
            <th>&nbsp &nbsp</th>
            <td>Keluar dari website Prediksi Tinggi Gelombang Signifikan</td>
        </tr>
</div>