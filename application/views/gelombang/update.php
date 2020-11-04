<div class="card mb-3">
    <div class="card-header">
        <a href="<?php echo site_url('gelombang') ?>"><i class="fas fa-arrow-left"></i>
            Back</a>
    </div>
    <div class="card-body">
        <form action="<?php site_url('gelombang/update/' . $datagelombang->d_id) ?>" method="post">
            <div class="form-group">
                <label for="Tanggal">TANGGAL*</label>
                <input class="form-control <?php echo form_error('tanggal') ? 'is-invalid' : '' ?>" type="date" name="tanggal" placeholder="Tanggal" value="<?= set_value('tanggal') !== '' ? set_value('tanggal') : $datagelombang->d_tanggal; ?>" />
                <div class="invalid-feedback">
                    <?php echo form_error('tanggal'); ?>
                </div>
            </div>

            <div class="form-group">
                <label for="ketinggian_gelombang">KETINGGIAN GELOMBANG*</label>
                <input class="form-control <?php echo form_error('ketinggian_gelombang') ? 'is-invalid' : '' ?>" type="text" name="ketinggian_gelombang" placeholder="Ketinggian Gelombang" value="<?= set_value('ketinggian_gelombang') !== '' ? set_value('ketinggian_gelombang') : $datagelombang->d_tinggi; ?>" />
                <div class="invalid-feedback">
                    <?php echo form_error('ketinggian_gelombang'); ?>
                </div>
            </div>

            <input class="btn btn-success" type="submit" name="btn" value="Save" />
        </form>

    </div>

    <div class="card-footer small text-muted">
        * required fields
    </div>


</div>