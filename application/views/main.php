<!DOCTYPE html>
<html lang="en">

<head>
  <?php $this->load->view("_partials/head.php") ?>
</head>

<body id="page-top">

  <?php $this->load->view("_partials/navbar.php") ?>

  <div id="wrapper">

    <?php
    if ($this->session->userdata('u_role') != 1) {
      $this->load->view("_partials/sidebar2.php");
    } else {
      $this->load->view("_partials/sidebar.php");
    }
    ?> <div id="content-wrapper">

      <div class="container-fluid">

        <!-- Page Content -->
        <?php // MAIN CONTENT DISINI
        if (isset($page) && $page != '') {
          $this->load->view($page);
        } else {
          $this->load->view('home/overview');
        }
        ?>
      </div>
      <!-- /.container-fluid -->

      <!-- Sticky Footer -->
      <?php $this->load->view("_partials/footer.php") ?>

    </div>
    <!-- /.content-wrapper -->

  </div>
  <!-- /#wrapper -->

  <?php $this->load->view("_partials/scrolltop.php") ?>
  <?php $this->load->view("_partials/modal.php") ?>
  <?php $this->load->view("_partials/js.php") ?>


  <script>
    function deleteConfirm(url) {
      $('#btn-delete').attr('href', url);
      $('#deleteModal').modal();
    }
  </script>
</body>

</html>