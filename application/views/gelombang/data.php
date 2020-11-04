<!-- DataTables -->
<?php if ($this->session->flashdata('success')) : ?>
  <div class="alert alert-success" role="alert">
    <?php echo $this->session->flashdata('success'); ?>
  </div>
<?php endif; ?>
<div class="card mb-3">
  <div class="card-header">
    <a href="<?php echo site_url('gelombang/create') ?>"><i class="fas fa-plus"></i> Add New</a>
    <a class="float-right" href="#" data-toggle="modal" data-target="#uploadModal""><i class=" fas fa-upload"></i> Upload File</a>
  </div>
  <div class="card-body">

    <div class="table-responsive">
      <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
        <thead>
          <tr>
            <th>NO</th>
            <th>TANGGAL</th>
            <th>PERIODE</th>
            <th>KETINGGIAN GELOMBANG</th>
            <th class="text-center">AKSI</th>
          </tr>
        </thead>
        <tbody>
          <?php $no = 1;
          foreach ($datagelombang as $dg) : ?>
            <tr>
              <td>
                <?php echo $no++ ?>
              </td>
              <td>
                <?php echo $dg->d_tanggal ?>
              </td>
              <td>
                <?php echo $dg->periode ?>
              </td>
              <td>
                <?php echo $dg->d_tinggi ?>
              </td>
              <td align="center">
                <a href="<?php echo base_url('gelombang/update/' . $dg->d_id) ?>" class="btn btn-small"><i class="fas fa-edit"></i></a>

                <a onclick="deleteConfirm('<?php echo base_url('gelombang/delete/' . $dg->d_id) ?>')" href="#!" class="btn btn-small text-danger"><i class="fas fa-trash"></i></a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>