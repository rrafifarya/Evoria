<?php 
include '../../Style/sidebar_style.php'; 
/** @var mysqli $conn **/ 

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
$idMhs = $_SESSION['id_user'] ?? 0;
$queryJabatan = mysqli_query($conn, "SELECT * FROM jabatan_hima WHERE idMhs = '$idMhs' AND is_active = 1");
$dataJabatan = mysqli_fetch_assoc($queryJabatan);
$jabatan = $dataJabatan['nama_jabatan'] ?? ''; 
$periode = $dataJabatan['periode'] ?? '';
$pending = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM transaksi_acara WHERE status = 'Pending BPH'"));
?>

<div class="sidebar d-flex flex-column shadow">
    <div class="brand">
        <img src="../../asset/logo.png" alt="EVORIA" style="height: 90px; width: 150px;">
    </div>
    
    <div class="text-secondary small px-3 mt-3 mb-1" style="font-size: 0.65rem; letter-spacing: 1px;">MENU UTAMA</div>
    <nav class="nav flex-column">
        <a class="nav-link <?= ($page == 'dashboard') ? 'active' : '' ?>" 
           href="dashboard.php?page=dashboard">
            <i class="fa fa-gauge-high me-2"></i> Dashboard
        </a>
        <div class="menu-label">Data</div>
        <a class="nav-link <?= ($page == 'departemen') ? 'active' : '' ?>" 
           href="dashboard.php?page=departemen">
            <i class="fa fa-building me-2"></i> Departemen
        </a>
        <a class="nav-link <?= ($page == 'sponsor') ? 'active' : '' ?>" 
           href="dashboard.php?page=sponsor">
            <i class="fa fa-handshake me-2"></i> Sponsor
        </a>
        
        <div class="menu-label">Keanggotaan</div>
        <a class="nav-link <?= ($page == 'jabatan') ? 'active' : '' ?>" 
           href="dashboard.php?page=jabatan">
            <i class="fa fa-user-tag me-2"></i> Jabatan HIMA
        </a>
        <a class="nav-link <?= ($page == 'divisipanitia') ? 'active' : '' ?>" 
           href="dashboard.php?page=divisipanitia">
            <i class="fa fa-users-cog me-2"></i> Divisi Panitia
        </a>
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
        <strong><?= htmlspecialchars($jabatan) ?></strong>
        <br>
        <small><?= $_SESSION['nama'] ?? '' ?></small>
        <br>
        <small style="color: #a286f4; font-size: 0.6rem;">
            <i class="fa fa-graduation-cap me-1"></i> BPH
        </small>
    </div>
</div>