<div class="card mb-3">
	<div class="card-header">
		<a href="<?php echo site_url('prediksi') ?>"><i class="fas fa-arrow-left"></i> Back</a>
	</div>
	<div class="card-body">
		<form name="formPred" action="<?php echo base_url('prediksi/predik') ?>" method="post"
			onsubmit="return validateForm()">

			<div class="form-group row">
				<div class="col">
					<label>Alpha*</label>
					<input class="form-control <?php echo form_error('alpha') ? 'is-invalid' : '' ?>" type="number"
						name="alpha" min="0" value="<?= set_value('alpha') ?>" step="0.01" />
				</div>
				<div class="col">
					<label>Beta*</label>
					<input class="form-control <?php echo form_error('beta') ? 'is-invalid' : '' ?>" type="number"
						name="beta" min="0" value="<?= set_value('beta') ?>" step="0.01" />
				</div>
				<div class="col">
					<label>Gamma*</label>
					<input class="form-control <?php echo form_error('gamma') ? 'is-invalid' : '' ?>" type="number"
						name="gamma" min="0" value="<?= set_value('gamma') ?>" step="0.01" />
				</div>
			</div>

			<div class="form-group row">
				<div class="col">
					<label>Bulan Mulai</label>
					<select class="form-control" name="blnMulai">
						<option value="01">01</option>
						<option value="02">02</option>
						<option value="03">03</option>
						<option value="04">04</option>
						<option value="05">05</option>
						<option value="06">06</option>
						<option value="07">07</option>
						<option value="08">08</option>
						<option value="09">09</option>
						<option value="10">10</option>
						<option value="11">11</option>
						<option value="12">12</option>
					</select>
				</div>
			</div>

			<div class="form-group row">
				<div class="col">
					<label>Tahun Mulai</label>
					<input class="form-control" name="thnMulai" type="number" />
				</div>
			</div>

			<div class="form-group row">
				<div class="col">
					<label>Bulan Sampai</label>
					<select class="form-control" name="blnSampai">
						<option value="01">01</option>
						<option value="02">02</option>
						<option value="03">03</option>
						<option value="04">04</option>
						<option value="05">05</option>
						<option value="06">06</option>
						<option value="07">07</option>
						<option value="08">08</option>
						<option value="09">09</option>
						<option value="10">10</option>
						<option value="11">11</option>
						<option value="12">12</option>
					</select>
				</div>
			</div>

			<div class="form-group row">
				<div class="col">
					<label>Tahun Sampai</label>
					<input class="form-control" name="thnSampai" type="number" />
				</div>
			</div>

			<input class="btn btn-success" type="submit" name="btn" value="Prediksi" />
		</form>
	</div>

	<div class="card-footer small text-muted">* required fields</div>
</div>
