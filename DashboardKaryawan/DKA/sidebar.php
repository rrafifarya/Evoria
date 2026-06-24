<?php
/** @var mysqli $conn **/
include '../../Style/sidebar_style.php'; 

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// HITUNG PENDING DKA
$pending = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM transaksi_acara WHERE status = 'Pending DKA'"));
?>

<div class="sidebar d-flex flex-column shadow">
    <div class="brand">
        <img src="../../asset/logo.png" alt="EVORIA" style="height: 90px; width: 150px;">
    </div>
    
    
    <div class="text-secondary small px-3 mt-3 mb-1" style="font-size: 0.65rem; letter-spacing: 1px;">MENU UTAMA</div>
    <nav class="nav flex-column">
        <!-- DASHBOARD -->
        <a class="nav-link <?= ($page == 'dashboard') ? 'active' : '' ?>" 
           href="dashboard.php?page=dashboard">
            <i class="fa fa-gauge-high me-2"></i> Dashboard
        </a>
        
        <!-- MASTER DATA -->
        <div class="menu-label">Kelola Data</div>
        <a class="nav-link <?= ($page == 'venue') ? 'active' : '' ?>" 
           href="dashboard.php?page=venue">
            <i class="fa fa-building me-2"></i> Venue
        </a>
        <a class="nav-link <?= ($page == 'mahasiswa') ? 'active' : '' ?>" 
           href="dashboard.php?page=mahasiswa">
            <i class="fa fa-users me-2"></i> Mahasiswa
        </a>
        <a class="nav-link <?= ($page == 'karyawan') ? 'active' : '' ?>" 
           href="dashboard.php?page=karyawan">
            <i class="fa fa-user-shield me-2"></i> Karyawan
        </a>
        
        <!-- PERSETUJUAN -->
        <div class="menu-label">Persetujuan</div>
        <a class="nav-link <?= ($page == 'approval') ? 'active' : '' ?>" 
           href="dashboard.php?page=approval">
            <i class="fa fa-check-double me-2"></i> Approval Acara
            <?php if ($pending > 0): ?>
                <span class="badge bg-danger rounded-pill ms-1"><?= $pending ?></span>
            <?php endif; ?>
        </a>
    </nav>
    
    <div class="login-as">
        <small>Login sebagai:</small><br>
        <strong>DKA</strong>
        <br>
        <small><?= $_SESSION['nama'] ?? '' ?></small>
        <br>
        <small style="color: #a286f4; font-size: 0.6rem;">
            <i class="fa fa-user-shield me-1"></i> Admin
        </small>
    </div>
</div>