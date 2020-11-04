<?php if ($this->session->flashdata('success')): ?>
    <div class="alert alert-success" role="alert">
        <?php echo $this->session->flashdata('success'); ?>
    </div>
<?php endif; ?>
<!-- DataTables -->
<div class="card mb-3">
    <div class="card-header">
        <a href="<?php echo site_url('pengujian/create') ?>"><i class="fas fa-plus"></i> Pengujian Baru</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                    <th width="5%">#</th>
                    <th>Nama Dokumen</th>
                    <th>Alpha</th>
                    <th>Beta</th>
                    <th>Gamma</th>
                    <th>Periode (n)</th>
                    <th>MSE</th>
                    <th>MAPE</th>
                    <th width="20%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no=1; foreach ($hasilpengujian as $h): ?>
                    <tr>
                        <td><?=$no++?></td>
                        <td>
                            <a href="<?=base_url('pengujian/'.$h->h_id)?>"><?=$h->h_dokumen?></a>
                        </td>
                        <td><?=$h->alpha?></td>
                        <td><?=$h->beta?></td>
                        <td><?=$h->gamma?></td>
                        <td><?=$h->jumlah_n?></td>
                        <td><?=$h->mse?></td>
                        <td><?=$h->mape?></td>
                        <td align="center">
                            <a class="btn btn-success" href="<?=base_url()?>assets/files/pengujian/<?=$h->h_dokumen?>.csv"><i class="fa fa-download"></i></a>
                            <a onclick="deleteConfirm('<?php echo site_url('pengujian/delete/'.$h->h_id) ?>')"
                            href="#" class="btn btn-small text-danger"><i class="fas fa fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>