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
        <h5>Harian</h5>
        <table class="table table-bordered table-striped">
            <tr>
                <th></th>
                <th>MSE Terbaik=<?= @$terbaik['harian-mse']->mse ?></th>
                <th>MAPE Terbaik=<?= @$terbaik['harian-mape']->mape ?></th>
            </tr>
            <tr>
                <th>Alpha</th>
                <td><?= @$terbaik['harian-mse']->alpha ?></td>
                <td><?= @$terbaik['harian-mape']->alpha ?></td>
            </tr>
            <tr>
                <th>Beta</th>
                <td><?= @$terbaik['harian-mse']->beta ?></td>
                <td><?= @$terbaik['harian-mape']->beta ?></td>
            </tr>
            <tr>
                <th>Gamma</th>
                <td><?= @$terbaik['harian-mse']->gamma ?></td>
                <td><?= @$terbaik['harian-mape']->gamma ?></td>
            </tr>
            <tr>
                <th>Aksi</th>
                <td>
                    <?php if (isset($terbaik['harian-mse']->h_id)) : ?>
                        <a class="btn btn-primary" href="<?= base_url() ?>prediksi/rincian/<?= @$terbaik['harian-mse']->h_id ?>"><i class="fa fa-info-circle"></i> Rincian</a>
                    <?php endif ?>
                </td>
                <td>
                    <?php if (isset($terbaik['harian-mape']->h_id)) : ?>
                        <a class="btn btn-primary" href="<?= base_url() ?>prediksi/rincian/<?= @$terbaik['harian-mape']->h_id ?>"><i class="fa fa-info-circle"></i> Rincian</a>
                    <?php endif ?>
                </td>
            </tr>
        </table>
        <hr>
        <h5>Bulanan</h5>
        <table class="table table-bordered table-striped">
            <tr>
                <th></th>
                <th>MSE Terbaik=<?= @$terbaik['bulanan-mse']->mse ?></th>
                <th>MAPE Terbaik=<?= @$terbaik['bulanan-mape']->mape ?></th>
            </tr>
            <tr>
                <th>Alpha</th>
                <td><?= @$terbaik['bulanan-mse']->alpha ?></td>
                <td><?= @$terbaik['bulanan-mape']->alpha ?></td>
            </tr>
            <tr>
                <th>Beta</th>
                <td><?= @$terbaik['bulanan-mse']->beta ?></td>
                <td><?= @$terbaik['bulanan-mape']->beta ?></td>
            </tr>
            <tr>
                <th>Gamma</th>
                <td><?= @$terbaik['bulanan-mse']->gamma ?></td>
                <td><?= @$terbaik['bulanan-mape']->gamma ?></td>
            </tr>
            <tr>
                <th>Aksi</th>
                <td>
                    <?php if (isset($terbaik['bulanan-mse']->h_id)) : ?>
                        <a class="btn btn-primary" href="<?= base_url() ?>prediksi/rincian/<?= @$terbaik['bulanan-mse']->h_id ?>"><i class="fa fa-info-circle"></i> Rincian</a>
                    <?php endif ?>
                </td>
                <td>
                    <?php if (isset($terbaik['bulanan-mape']->h_id)) : ?>
                        <a class="btn btn-primary" href="<?= base_url() ?>prediksi/rincian/<?= @$terbaik['bulanan-mape']->h_id ?>"><i class="fa fa-info-circle"></i> Rincian</a>
                    <?php endif ?>
                </td>
            </tr>
        </table>
        <hr>
        <div class="table-responsive">
            <table class="table table-hover" id="" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th>Jenis</th>
                        <th>Alpha</th>
                        <th>Beta</th>
                        <th>Gamma</th>
                        <th>MSE</th>
                        <th>MAPE</th>
                        <th width="20%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = $nomor_halaman == 1 ? 1 : $nomor_halaman + 1;
                    foreach ($data->result() as $h) : ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $h->jenis ?></td>
                            <td><?= $h->alpha ?></td>
                            <td><?= $h->beta ?></td>
                            <td><?= $h->gamma ?></td>
                            <td><?= $h->mse ?></td>
                            <td><?= $h->mape ?></td>
                            <td align="center">
                                <a class="btn btn-primary mb-2 btn-sm" href="<?= base_url() ?>prediksi/rincian/<?= $h->h_id ?>"><i class="fa fa-info-circle"></i> Rincian</a>
                                <a onclick="deleteConfirm('<?php echo site_url('prediksi/delete/' . $h->h_id) ?>')" href="#" class="btn btn-sm btn-danger mb-2"><i class="fas fa fa-trash"></i> Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?= $pagination ?>
        </div>
    </div>
</div>