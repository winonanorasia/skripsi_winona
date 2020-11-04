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
    <br>
    <div class="row">
        <div class="col-lg-4 col-6">
        </div>
    </div>
    <hr>
    <?= $tabel   ?>

  <!-- <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.3"></script> -->
  <script src="<?= base_url('assets/js/chart.js') ?>"></script>
  <!-- <script src="https://cdn.jsdelivr.net/npm/hammerjs@2.0.8/hammer.min.js"></script> -->
  <script src="<?= base_url('assets/js/hammer.min.js') ?>"></script>
  <!-- <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@0.7.5/dist/chartjs-plugin-zoom.min.js"></script> -->
  <script src="<?= base_url('assets/js/chartjs-plugin-zoom.min.js') ?>"></script>

    <canvas id="predcanvas"></canvas>
    <script id="preddata" type="application/json"><?= $data ?></script>
<script>
    

    window.onload = function() {
        var preddata = JSON.parse($('#preddata').html());
        window.predLine = Chart.Line(document.getElementById('predcanvas').getContext('2d'), {
        data: {
          labels: preddata.tanggal,
          datasets: [{
            label: 'Prediksi',
            borderColor: '#11f5',
            backgroundColor: '#11f5',
            fill: false,
            data: preddata.tinggi,
          }]
        },
        options: {
          responsive: true,
          aspectRatio: 2,
          hoverMode: 'index',
          stacked: false,
          title: {
            display: true,
            text: 'Grafik Data Forecasting'
          },
          plugins: {
            zoom: {
              pan: {
                enabled: true,
                mode: 'x'
              },
              zoom: {
                enabled: true,
                mode: 'x',
              }
            }
          },
				scales: {
					xAxes: [{
						display: true,
						scaleLabel: {
							display: true,
							labelString: 'Periode'
						}
					}],
					yAxes: [{
						display: true,
						scaleLabel: {
							display: true,
							labelString: 'Ketinggian Gelombang (m)'
						}
					}]
				}
        }
      });
    };
</script>
