<div class="card mb-3">
    <div class="card-header">
        <a href="<?php echo site_url('prediksi') ?>"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
    <div class="card-body">
        <form name="formPred" method="post" onsubmit="return validateForm()">
            <div class="form-group row">
                <div class="col">
                    <label>Jenis*</label>
                    <select class="form-control" name="jenis" id="jenis">
                        <option <?= (isset($_POST['jenis']) && $_POST['jenis'] == 'harian') ? 'selected' : '' ?> value="harian">Harian</option>
                        <option <?= (isset($_POST['jenis']) && $_POST['jenis'] == 'bulanan') ? 'selected' : '' ?> value="bulanan">Bulanan</option>
                    </select>
                </div>
                <div class="col">
                    <label>Alpha*</label>
                    <input class="form-control <?php echo form_error('alpha') ? 'is-invalid' : '' ?>" type="number" name="alpha" min="0.1" max="0.9" value="<?= set_value('alpha', 0.1) ?>" step="0.1" />
                </div>
                <div class="col">
                    <label>Beta*</label>
                    <input class="form-control <?php echo form_error('beta') ? 'is-invalid' : '' ?>" type="number" name="beta" min="0.1" max="0.9" value="<?= set_value('beta', 0.1) ?>" step="0.1" />
                </div>
                <div class="col">
                    <label>Gamma*</label>
                    <input class="form-control <?php echo form_error('gamma') ? 'is-invalid' : '' ?>" type="number" name="gamma" min="0.1" max="0.9" value="<?= set_value('gamma', 0.1) ?>" step="0.1" />
                </div>
            </div>
            <input class="btn btn-success" type="submit" value="Prediksi" />
        </form>

    </div>

    <div class="card-footer small text-muted">
        * required fields
    </div>
</div>
<?= @$hasil['tabel'] ?>
<script>

</script>