<?php 
include '../../Style/sidebar_style.php'; 
/** @var mysqli $conn **/

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

$idMhs = $_SESSION['id_user'] ?? 0;
$queryJabatan = mysqli_query($conn, "SELECT * FROM jabatan_hima WHERE idMhs = '$idMhs' AND is_active = 1");
$dataJabatan = mysqli_fetch_assoc($queryJabatan);
$jabatan = $dataJabatan['nama_jabatan'] ?? '';
$periode = $dataJabatan['periode'] ?? '';

$id_departemen_kadep = $dataJabatan['id_departemen'] ?? 0;
$pending = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM transaksi_acara WHERE status = 'Pending Kadep' AND id_departemen = '$id_departemen_kadep'"));
?>

<div class="sidebar d-flex flex-column shadow">
    <div class="brand">
        <img src="../../asset/logo.png" alt="EVORIA" style="height: 90px; width: 150px;">
    </div>
    <div class="text-secondary small px-4 mt-4 mb-2">MENU UTAMA</div>
    <nav class="nav flex-column">
        <a class="nav-link <?= ($page == 'dashboard') ? 'active' : '' ?>" 
           href="dashboard.php?page=dashboard">
            <i class="fa fa-gauge-high me-3"></i> Dashboard Kadep
        </a>
        
        <div class="menu-label">Persetujuan</div>
        <a class="nav-link <?= ($page == 'approval') ? 'active' : '' ?>" 
           href="dashboard.php?page=approval">
            <i class="fa fa-check-double me-3"></i> Approval Acara
            <?php if ($pending > 0): ?>
                <span class="badge bg-danger rounded-pill ms-2"><?= $pending ?></span>
            <?php endif; ?>
        </a>
    </nav>
    <div class="login-as">
        <small class="text-secondary">Login sebagai:</small><br>
        <strong><?= htmlspecialchars($jabatan) ?></strong>
        <br>
        <small class="text-secondary"><?= $_SESSION['nama'] ?? '' ?></small>
    </div>
</div>