<button class="btn btn-primary mb-2" onclick="history.back()"><i class="fa fa-arrow-left"></i> Kembali</button>
<button class="btn btn-success mb-2" onclick="window.print()"><i class="fa fa-print"></i> Cetak</button>
<hr>

<div class="container-fluid">
  <!-- Small boxes (Stat box) -->
  <div class="row">
    <div class="col-lg-4 col-6">
      <!-- small box -->
      <div class="small-box bg-info" style=" text-align:left">
        <div class=" inner" style=" padding: 50px">
          <center><i class="fas fa-info-circle" style="font-size: 60px"></i>
            <h3><?= @$alpha ?></h3>
            <p> Alpha</p>
          </center>
        </div>
      </div>
    </div> <!-- ./col -->
    <div class="col-lg-4 col-6">
      <!-- small box -->
      <div class="small-box bg-success" style="text-align:center">
        <div class=" inner" style=" padding: 50px">
          <center>
            <i class="fas fa-info-circle" style="font-size: 60px"></i>
            <h3><?= @$beta ?></h3>
            <p> Beta</p>
          </center>
        </div>
      </div>
    </div> <!-- ./col -->
    <div class="col-lg-4 col-6">
      <!-- small box -->
      <div class="small-box bg-warning" style=" text-align:right">
        <div class=" inner" style="padding: 50px">
          <center>
            <i class=" fas fa-info-circle" style="font-size: 60px"></i>
            <h3><?= @$gamma ?></h3>
            <p> Gamma</p>
          </center>
        </div>
      </div>
    </div>
  </div>
  <hr>
  <div class="table-responsive">
    <table class="table table-hover" id="" width="100%" cellspacing="0">
      <thead>
        <tr>
          <th width="5%">#</th>
          <th>Jenis</th>
          <th>Periode Mulai</th>
          <th>Periode Akhir</th>
          <th width="20%">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php $no = 1;
        foreach ($data as $h) : ?>

          <tr>
            <td><?= $no++ ?></td>
            <td>
              <?=$h->jenis?>
            </td>
            <td><?= $h->periode_mulai ?></td>
            <td><?= $h->periode_akhir ?></td>
            <td align="center">
              <a class="btn btn-primary mb-2 btn-sm" href="<?= base_url() ?>prediksi_mendatang/rincian/<?= $h->h_id ?>"><i class="fa fa-info-circle"></i> Rincian</a>
              <a onclick="deleteConfirm('<?php echo site_url('prediksi_mendatang/delete/' . $h->h_id) ?>')" href="#" class="btn btn-sm btn-danger mb-2"><i class="fas fa fa-trash"></i> Hapus</a>
            </td>
          </tr>

        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <hr>