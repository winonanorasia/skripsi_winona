<?php if ($this->session->flashdata('success')) : ?>
    <div class="alert alert-success" role="alert">
        <?php echo $this->session->flashdata('success'); ?>
    </div>
<?php endif; ?>
<!-- DataTables -->
<div class="card mb-3">
    <div class="card-header">
        <a class="mr-4" href="<?php echo site_url('prediksi_mendatang/create_mendatang') ?>"><i class="fas fa-plus"></i> Prediksi Baru</a>
        <a class="mr-4" href="<?php echo site_url('prediksi_mendatang/perbarui_hasil_terbaik') ?>"><i class="fas fa-sync"></i> Perbarui Hasil Terbaik</a>
        <button class="btn btn-success mb-2" onclick="window.print()"><i class="fa fa-print"></i> Cetak</button>
    </div>

    <div class="card-body">
        <table class="table table-bordered table-striped">

            <tr>
                <th>Prediksi Mendatang Berdasarkan MSE Terbaik (Harian)</th>
                <td>
                    <?php if (isset($terbaik['harian-mse']->h_id)) : ?>
                        <a class="btn btn-primary" href="<?= base_url() ?>prediksi_mendatang/periode/1"><i class="fa fa-info-circle"></i> Rincian</a>
                    <?php endif ?>
                </td>
            </tr>
            <tr>
                <th>Prediksi Mendatang Berdasarkan MAPE Terbaik (Harian)</th>
                <td>
                    <?php if (isset($terbaik['harian-mape']->h_id)) : ?>
                        <a class="btn btn-primary" href="<?= base_url() ?>prediksi_mendatang/periode/2"><i class="fa fa-info-circle"></i> Rincian</a>
                    <?php endif ?>
                </td>
            </tr>
            <tr>
                <th>Prediksi Mendatang Berdasarkan MSE Terbaik (Bulanan)</th>
                <td>
                    <?php if (isset($terbaik['bulanan-mse']->h_id)) : ?>
                        <a class="btn btn-primary" href="<?= base_url() ?>prediksi_mendatang/periode/3"><i class="fa fa-info-circle"></i> Rincian</a>
                    <?php endif ?>
                </td>
            </tr>
            <tr>
                <th>Prediksi Mendatang Berdasarkan MAPE Terbaik (Bulanan)</th>
                <td>
                    <?php if (isset($terbaik['bulanan-mape']->h_id)) : ?>
                        <a class="btn btn-primary" href="<?= base_url() ?>prediksi_mendatang/periode/4"><i class="fa fa-info-circle"></i> Rincian</a>
                    <?php endif ?>
                </td>
            </tr>
        </table>

    </div>
</div>