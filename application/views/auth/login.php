<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Tugas Akhir - Login</title>

  <!-- Custom fonts for this template-->
  <link href="<?php echo base_url('assets/fontawesome-free/css/all.min.css') ?>" rel="stylesheet" type="text/css">

  <!-- Custom styles for this template-->
  <link href="<?php echo base_url('assets/css/sb-admin.css') ?>" rel="stylesheet">
</head>
<style>
  body {
    background-image: url("<?php echo base_url(); ?>wave-4833997_960_720.jpg");
    /* background-size: 1800px 700px; */
    /* background-size: auto; */
    height: 500px;
    background-position: center;
    background-size: cover;
    position: relative;
  }
</style>

<body class="bg-dark">
  <div class="container">
    <center>
      <h1 style="color: #FFF"> TUGAS AKHIR - WINONA NORASIA</h1>
    </center>
    <br>
    <center>
      <h3 style="color: #FFF">PENERAPAN METODE HOLT WINTERS ADDITIVE EXPONENTIAL SMOOTHING DALAM PREDIKSI GELOMBANG AIR LAUT</h3>
    </center>
    <div class="card card-login mx-auto mt-5">
      <div class="card-header">Login</div>
      <div class="card-body">
        <form method="post" action="<?php echo base_url("auth/login") ?>">
          <div class="form-group">
            <div class="form-label-group">
              <input type="text" name="username" id="inputUsername" class="form-control" placeholder="Username" required="required" autofocus="autofocus">
              <label for="inputUsername">Username</label>
            </div>
          </div>
          <div class="form-group">
            <div class="form-label-group">
              <input type="password" name="password" id="inputPassword" class="form-control" placeholder="Password" required="required">
              <label for="inputPassword">Password</label>
            </div>
          </div>
          <div class="form-group text-center">
            <small class="text-center text-danger">
              <?php
              echo validation_errors();
              if (isset($error)) echo $error;
              ?>
            </small>
          </div>
          <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>

      </div>
    </div>
  </div>

  <!-- Bootstrap core JavaScript-->
  <script src="<?php echo base_url('assets/jquery/jquery.min.js') ?>"></script>
  <script src="<?php echo base_url('assets/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>

  <!-- Core plugin JavaScript-->
  <script src="<?php echo base_url('assets/jquery-easing/jquery.easing.min.js') ?>"></script>

</body>

</html>