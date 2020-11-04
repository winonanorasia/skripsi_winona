<div class="card mb-3">
    <div class="card-header">
        <a href="<?php echo site_url('pengujianhw') ?>"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
    <div class="card-body">
        <form name="formPred" action="<?php echo base_url('pengujianhw/predik') ?>" method="post" onsubmit="return validateForm()">
            <div class="form-group row">
                <div class="col">
                    <label>Alpha*</label>
                    <input class="form-control <?php echo form_error('alpha') ? 'is-invalid':'' ?>"
                        type="number" name="alpha" min="0" value="<?=set_value('alpha')?>" 
                        step="0.01" />
                </div>
                <div class="col">
                    <label >Beta*</label>
                    <input class="form-control <?php echo form_error('beta') ? 'is-invalid':'' ?>"
                        type="number" name="beta" min="0" value="<?=set_value('beta')?>" 
                        step="0.01"/>
                </div>
                <div class="col">
                    <label>Gamma*</label>
                    <input class="form-control <?php echo form_error('gamma') ? 'is-invalid':'' ?>"
                        type="number" name="gamma" min="0" value="<?=set_value('gamma')?>" 
                        step="0.01"/>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-6">
                    <label>Jumlah Peramalan (n)*</label>
                    <input class="form-control <?php echo form_error('jumlah_n') ? 'is-invalid':'' ?>"
                        type="number" name="jumlah_n" min="0" value="<?=set_value('jumlah_n')?>" 
                        />
                </div>
            </div>
            <div class="form-group row">
                <div class="col">
                    <label >Tahun Mulai</label>
                    <select class="form-control" name="thnMulai" onchange="getListPeriode(this.value,1)">
                        <option disabled selected></option>
                        <?php foreach ($tahun as $k => $v) : ?>
                        <option><?=$v->d_tahun?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col">
                    <label >Periode Mulai</label>
                    <select class="form-control" name="prdMulai" id="periode1">
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <div class="col">
                    <label >Tahun Sampai</label>
                    <select class="form-control" name="thnSampai" onchange="getListPeriode(this.value,2)">
                        <option disabled selected></option>
                        <?php foreach ($tahun as $k => $v) : ?>
                        <option><?=$v->d_tahun?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col">
                    <label >Periode Sampai</label>
                    <select class="form-control" name="prdSampai" id="periode2">
                    </select>
                </div>
            </div>

            <input class="btn btn-success" type="submit" name="btn" value="uji" />
        </form>

    </div>

    <div class="card-footer small text-muted">
        * required fields
    </div>
</div>