<button class="btn btn-primary mb-2" onclick="history.back()"><i class="fa fa-arrow-left"></i> Kembali</button>
<button class="btn btn-success mb-2" onclick="window.print()"><i class="fa fa-print"></i> Cetak</button>
<hr>
<p>Alpha: <?= @$alpha ?></p>
<p>Beta: <?= @$beta ?></p>
<p>Gamma: <?= @$gamma ?></p>
<?= $hasil['tabel'] ?>
<canvas id="canvas"></canvas>
<details>
<summary>Rincian Grafik</summary>
<div class="row">
  <div class="col-md-6 py-5">
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
  <div class="col-md-6">
    <canvas id="predcanvas"></canvas>
  </div>
</div>
</details>
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.3"></script>
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
    ['Semua', enddata - startdata],
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
        aspectRatio: 4,
        hoverMode: 'index',
        stacked: false,
        title: {
          display: true,
          text: 'Grafik Perbandingan Data Tinggi dan Hasil Prediksi'
        },
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
      }
    });
    update();
  };

  function update() {
    var span = spans[$('#length').val()];
    var length = span[1];
    $('#lengthout').text(span[0]);
    var offset = Math.trunc($('#offset').val() * (enddata - length) / length) * length;
    var min = offset;
    var max = offset + length;
    $('#offsetout').text(`${labels[min]} s/d ${labels[max]}`);
    window.myLine.data.labels = labels.slice(min, max);
    window.myLine.data.datasets[0].data = realdata.slice(min, max);
    window.myLine.data.datasets[1].data = predicts.slice(min, max);
    window.myLine.update();
  }
</script>