<!-- Sidebar -->
<ul class="sidebar navbar-nav">

    <li class="nav-item <?php echo $this->uri->segment(1) == '' ? 'active' : '' ?>">
        <a class="nav-link" href="<?php echo base_url() ?>">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <li class="nav-item dropdown <?php echo $this->uri->segment(1) == 'gelombang' ? 'active show' : '' ?>">
        <a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-book-open"></i>
            <span>Data Tinggi</span>
        </a>
        <div class="dropdown-menu <?php echo $this->uri->segment(1) == 'gelombang' ? 'show' : '' ?>" aria-labelledby="pagesDropdown">
            <a class="dropdown-item" href="<?php echo site_url('gelombang') ?>">Lihat Data Harian (D)</a>
            <a class="dropdown-item" href="<?php echo site_url('gelombang/bulanan') ?>">Lihat Data Bulanan</a>
            <a class="dropdown-item" href="<?php echo site_url('gelombang/create') ?>">Input Data</a>
            <!-- <a class="dropdown-item" href="<?php echo site_url('admin/datatinggi/uploadfromexcel') ?>">Upload .xls</a> -->
        </div>
    </li>

    <li class="nav-item dropdown <?php echo $this->uri->segment(1) == 'prediksi' ? 'active' : '' ?>">
        <a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-cog"></i>
            <span>Prediksi</span>
        </a>
        <div class="dropdown-menu" aria-labelledby="pagesDropdown">
            <a class="dropdown-item" href="<?php echo site_url('prediksi') ?>">Hasil Prediksi</a>
            <a class="dropdown-item" href="<?php echo site_url('prediksi/create') ?>">Prediksi Baru</a>
            <a class="dropdown-item" href="<?php echo site_url('prediksi/auto') ?>">Prediksi Otomatis</a>
        </div>
    </li>
    <li class="nav-item dropdown <?php echo $this->uri->segment(1) == 'prediksi_mendatang' ? 'active' : '' ?>">
        <a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-cog"></i>
            <span>Prediksi Mendatang</span>
        </a>
        <div class="dropdown-menu" aria-labelledby="pagesDropdown">
            <a class="dropdown-item" href="<?php echo site_url('prediksi_mendatang') ?>">Hasil Prediksi</a>
            <a class="dropdown-item" href="<?php echo site_url('prediksi_mendatang/create_mendatang') ?>">Prediksi Baru</a>
        </div>
    </li>
</ul>