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
  <?= $hasil['tabel'] ?>
  <canvas id="canvas"></canvas>
  <div class="d-flex">
    <div>Rentang</div>
    <div id="lengthout" class="ml-auto">-</div>
  </div>
  <input type="range" id="length" min="0" max="4" value="4" oninput="update()" class="form-control">
  <div class="d-flex">
    <div>Offset</div>
    <div id="offsetout" class="ml-auto">-</div>
  </div>
  <input type="range" id="offset" min="0" max="1" value="1" oninput="update()" class="form-control" step="0.01">
</div>
<canvas id="predcanvas"></canvas>
<!-- <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.3"></script> -->
<script src="<?= base_url('assets/js/chart.js') ?>"></script>
<!-- <script src="https://cdn.jsdelivr.net/npm/hammerjs@2.0.8/hammer.min.js"></script> -->
<script src="<?= base_url('assets/js/hammer.min.js') ?>"></script>
<!-- <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@0.7.5/dist/chartjs-plugin-zoom.min.js"></script> -->
<script src="<?= base_url('assets/js/chartjs-plugin-zoom.min.js') ?>"></script>
<script>
  var labels = JSON.parse('<?= json_encode($hasil['tanggal'] ?? $hasil['bulan_tahun']) ?>')
  var predicts = JSON.parse('<?= json_encode($hasil['Ft']) ?>')
  var realdata = JSON.parse('<?= json_encode($hasil['tinggi']) ?>')
  // hardcoded. urgh
  var monthly = labels[0].length == 7;
  var startdata = predicts.findIndex(x => x != '');
  var enddata = realdata.length;
  var spanning = monthly ? 12 : 365.25;
  var spans = [
    ['1 Bulan', Math.trunc(spanning / 12)],
    ['3 Bulan', Math.trunc(spanning / 4)],
    ['1 Tahun', Math.trunc(spanning)],
    ['3 Tahun', Math.trunc(spanning * 3)],
    ['Semua', enddata - startdata - 1],
  ];

  window.onload = function() {
    window.myLine = Chart.Line(document.getElementById('canvas').getContext('2d'), {
      data: {
        labels: labels,
        datasets: [{
          label: 'Data Aktual',
          borderColor: '#f115',
          backgroundColor: '#f115',
          fill: false,
          data: realdata,
        }, {
          label: 'Forecasting',
          borderColor: '#11f5',
          backgroundColor: '#11f5',
          fill: false,
          data: predicts,
        }]
      },
      options: {
        responsive: true,
        aspectRatio: 2,
        hoverMode: 'index',
        stacked: false,
        title: {
          display: true,
          text: 'Grafik Data Training'
        },
        type: "time",
        distribution: 'series',
        time: {
          min: labels[0],
          max: labels[labels.length - 1],
          displayFormats: {
            day: 'MMM YY'
          }
        },
        ticks: {
          source: "labels"
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
    window.predLine = Chart.Line(document.getElementById('predcanvas').getContext('2d'), {
      data: {
        labels: labels.slice(enddata),
        datasets: [{
          label: 'Prediksi',
          borderColor: '#11f5',
          backgroundColor: '#11f5',
          fill: false,
          data: predicts.slice(enddata),
        }]
      },
      options: {
        responsive: true,
        aspectRatio: 2,
        hoverMode: 'index',
        stacked: false,
        title: {
          display: true,
          text: 'Grafik Data Forecasing'
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
    update();
  };

  function update() {
    console.log(labels)
    var span = spans[$('#length').val()];
    var length = span[1];
    $('#lengthout').text(span[0]);
    var offset = Math.trunc($('#offset').val() * (enddata - length) / length) * length + startdata;
    var min = offset;
    var max = offset + length + 1;
    console.log(max)
    $('#offsetout').text(`${labels[min]} s/d ${labels[max - 1]}`);
    window.myLine.data.labels = labels.slice(min, max);
    window.myLine.data.datasets[0].data = realdata.slice(min, max);
    window.myLine.data.datasets[1].data = predicts.slice(min, max);
    window.myLine.update();
  }
</script>