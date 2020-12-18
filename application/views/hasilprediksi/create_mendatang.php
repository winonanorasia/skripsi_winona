<div class="card mb-3">
    <div class="card-header">
        <a href="<?php echo site_url('prediksi_mendatang') ?>"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
    <div class="card-body">
        <form name="formPred" method="get" onsubmit="return validateForm()">
            <div class="form-group row">
                <div class="col">
                    <label>Jenis*</label>
                    <select class="form-control" name="jenis" id="jenis">
                        <option <?= (isset($_GET['jenis']) && $_GET['jenis'] == 'harian-mse') ? 'selected' : '' ?> value="harian-mse">Berdasarkan MSE Terbaik (Harian) </option>
                        <option <?= (isset($_GET['jenis']) && $_GET['jenis'] == 'harian-mape') ? 'selected' : '' ?> value="harian-mape">Berdasarkan MAPE Terbaik (Harian) </option>
                        <option <?= (isset($_GET['jenis']) && $_GET['jenis'] == 'bulanan-mse') ? 'selected' : '' ?> value="bulanan-mse">Berdasarkan MSE Terbaik (Bulanan) </option>
                        <option <?= (isset($_GET['jenis']) && $_GET['jenis'] == 'bulanan-mape') ? 'selected' : '' ?> value="bulanan-mape">Berdasarkan MAPE Terbaik (Bulanan) </option>
                    </select>
                </div>
                <div class="col">
                    <label>Periode Mulai*</label>
                    <input class="form-control <?php echo form_error('periode-mulai') ? 'is-invalid' : '' ?>" type="number" name="periode-mulai" min="1" max="31" value="<?= @$_GET['periode-mulai'] ?>" step="1" />
                </div>
                <div class="col">
                    <label>Periode Akhir*</label>
                    <input class="form-control <?php echo form_error('periode-akhir') ? 'is-invalid' : '' ?>" type="number" name="periode-akhir" min="1" max="31" value="<?= @$_GET['periode-akhir'] ?>" step="1" />
                </div>
            </div>
            <input class="btn btn-success" type="submit" value="Prediksi" />
        </form>

    </div>

    <div class="card-footer small text-muted">
        * required fields
    </div>
</div>
<hr>
<p>MSE: <?= round(@$mse, 4) ?></p>
<p>MAPE: <?= round(@$mape) . "%" ?></p>
<?= @$hasil['tabel'] ?>
<script>

</script>