<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Prediksi Gelombang - Register</title>

  <!-- Custom fonts for this template-->
  <link href="<?php echo base_url('assets/fontawesome-free/css/all.min.css') ?>" rel="stylesheet" type="text/css">

  <!-- Custom styles for this template-->
  <link href="<?php echo base_url('css/sb-admin.css') ?>" rel="stylesheet">

</head>

<body class="bg-dark">

  <div class="container">
    <div class="card card-login mx-auto mt-5">
      <div class="card-header">Register</div>
      <div class="card-body">
        <form method="post" action ="<?php echo base_url("index.php/welcome/index")?>">
          <div class="form-group">
              <input type="text"  name="username" id="inputUsername" class="form-control" placeholder="Username" required="required" autofocus="autofocus">
          </div>
          <div class="form-group">
              <input type="text"  name="email" id="inputemail" class="form-control" placeholder="Email" required="required" autofocus="autofocus">
          </div>
          <div class="form-group">
              <input type="password"  name="password" id="inputPassword" class="form-control" placeholder="Password" required="required">
          </div>
          <div class="form-group">
              <input type="password"  name="password2" id="inputPassword2" class="form-control" placeholder="Confirm Password" required="required">
          </div>
      
          <button class="btn btn-primary btn-block">Register</button>
          <span style="width:50%; text-align:right;  display: inline-block;"><a class="small-text" href="<?php echo base_url('index.php')?>">Batal</a></span>
          
         
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
