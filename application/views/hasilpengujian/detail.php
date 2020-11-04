<!-- DataTables -->
<div class="card mb-3">
    <div class="card-header">
        <a href="<?php echo site_url('pengujian') ?>"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table width="100%" cellspacing="0">
                <tr>
                    <td width="20%">Nama Dokumen</td>
                    <td width="1%">:</td>
                    <td><?=$detail->h_dokumen?></td>
                </tr>
                <tr>
                    <td>Jumlah Fold</td>
                    <td width="1%">:</td>
                    <td><?=$detail->jumlah_folds?></td>
                </tr>
                <tr>
                    <td>Alpha</td>
                    <td width="1%">:</td>
                    <td><?=$detail->alpha?></td>
                </tr>
                <tr>
                    <td>Beta</td>
                    <td width="1%">:</td>
                    <td><?=$detail->beta?></td>
                </tr>
                <tr>
                    <td>Gamma</td>
                    <td width="1%">:</td>
                    <td><?=$detail->gamma?></td>
                </tr>
                <tr>
                    <td>MSE</td>
                    <td width="1%">:</td>
                    <td><?=$detail->mse?></td>
                </tr>
                <tr>
                    <td>MAPE</td>
                    <td width="1%">:</td>
                    <td><?=$detail->mape?></td>
                </tr>
            </table>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <div class="table-responsive">
        <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Tahun</th>
                        <th>Montly</th>
                        <th>Tinggi Gel</th>
                        <th>Periode</th>
                        <th>YL+t - Yt</th>
                        <th>At</th>
                        <th>Tt</th>
                        <th>St-L</th>
                        <th>St</th>
                        <th>Forecast</th>
                        <th></th>
                        <th>Da-Df</th>
                        <th>|Da-Df|</th>
                        <th>(Da-Df)^2</th>
                        <th>|(Da-Df)/Da|</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no=1; foreach ($hasil as $h): ?>
                    <tr>
                        <td><?=$h[1]?></td>
                        <td><?=$h[2]?></td>
                        <td><?=$h[3]?></td>
                        <td><?=$h[4]?></td>
                        <td><?=$h[5]?></td>
                        <td><?=$h[6]?></td>
                        <td><?=$h[7]?></td>
                        <td><?=$h[8]?></td>
                        <td><?=$h[9]?></td>
                        <td><?=$h[10]?></td>
                        <td><?=$h[11]?></td>
                        <td><?=$h[12]?></td>
                        <td><?=$h[13]?></td>
                        <td><?=$h[14]?></td>
                        <td><?=$h[15]?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <script>
            var id_uji = <?=$detail->h_id?>;
            var url= '<?=base_url()?>';
        </script>
        <div class="row">
            <div class="col">
                <div id="line_chart" style="height:280px;"></div>
            </div>
        </div>
    </div>
</div>