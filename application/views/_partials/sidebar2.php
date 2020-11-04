<!-- Sidebar -->
<ul class="sidebar navbar-nav">
    <li class="nav-item <?php echo $this->uri->segment(1) == '' ? 'active' : '' ?>">
        <a class="nav-link" href="<?php echo base_url() ?>">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
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