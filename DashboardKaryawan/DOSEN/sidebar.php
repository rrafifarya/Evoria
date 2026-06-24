<?php 
/** @var mysqli $conn **/
include '../../Style/sidebar_style.php'; 

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

$id_karyawan = $_SESSION['id_user'] ?? 0;
$nama_admin = $_SESSION['nama'] ?? 'Admin';
$pending = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM transaksi_acara WHERE status = 'Pending Sekprodi/Kaprodi'"));
?>

<div class="sidebar d-flex flex-column shadow">
    <div class="brand fs-4">
        <i class="style="color: #a286f4;"></i> EVORIA
    </div>
    
    <div class="text-secondary small px-3 mt-3 mb-1" style="font-size: 0.65rem; letter-spacing: 1px;">MENU UTAMA</div>
    <nav class="nav flex-column">
        <a class="nav-link <?= ($page == 'dashboard') ? 'active' : '' ?>" 
           href="dashboard.php?page=dashboard">
            <i class="fa fa-gauge-high me-2"></i> Dashboard
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
        <strong>Admin (Dosen)</strong>
        <br>
        <small><?= $nama_admin ?></small>
        <br>
        <small style="color: #a286f4; font-size: 0.6rem;">
            <i class="fa fa-graduation-cap me-1"></i> Sekprodi / Kaprodi
        </small>
    </div>
</div>