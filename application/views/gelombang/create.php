<div class="card mb-3">
    <div class="card-header">
        <a href="<?php echo site_url('gelombang') ?>"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
    <div class="card-body">
        <form action="<?php base_url('gelombang/create') ?>" method="post">
            <div class="form-group">
                <label for="tanggal">TANGGAL*</label>
                <input class="form-control <?php echo form_error('tanggal') ? 'is-invalid' : '' ?>" type="date" name="tanggal" min="0" placeholder="Tanggal" value="<?= set_value('tanggal') ?>" />
                <div class="invalid-feedback">
                    <?php echo form_error('tanggal') ?>
                </div>
            </div>



            <div class="form-group">
                <label for="ketinggian_gelombang">KETINGGIAN GELOMBANG*</label>
                <input type="text" class="form-control <?php echo form_error('ketinggian_gelombang') ? 'is-invalid' : '' ?>" name="ketinggian_gelombang" placeholder="Ketinggian Gelombang" value="<?= set_value('ketinggian_gelombang') ?>" />
                <div class="invalid-feedback">
                    <?php echo form_error('ketinggian_gelombang') ?>
                </div>
            </div>

            <input class="btn btn-success" type="submit" name="btn" value="Save" />
        </form>
    </div>

    <div class="card-footer small text-muted">
        * required fields
    </div>
</div>