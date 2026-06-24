<?php 
include '../../Style/sidebar_style.php'; 

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
$idMhs = $_SESSION['id_user'] ?? 0;

// CEK APAKAH STAFF PEMBUAT ACARA
$cekStaff = mysqli_query($conn, "SELECT * FROM jabatan_hima WHERE idMhs = '$idMhs' AND is_active = 1 AND nama_jabatan IN ('Staff Departemen', 'Staff_Departemen')");
$isStaffPembuat = mysqli_num_rows($cekStaff) > 0;

// $cekPanitia = mysqli_query($conn, "SELECT id_acara FROM panitia WHERE idMhs = '$idMhs' AND status_seleksi = 'Diterima'");
// $isPanitia = mysqli_num_rows($cekPanitia) > 0;

// $queryCek = "SELECT t.id_trsAcara 
//              FROM peserta p 
//              LEFT JOIN transaksi_acara t ON p.id_acara = t.id_trsAcara 
//              WHERE p.idMhs = '$idMhs' 
//              AND t.tanggal_selesai < NOW() 
//              AND p.sudah_feedback = FALSE";
// $adaAcaraSelesai = mysqli_num_rows(mysqli_query($conn, $queryCek)) > 0;

// NOTIFIKASI
$notifAcaraSaya = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM transaksi_acara WHERE idMhs = '$idMhs' AND status LIKE 'Pending%'"));
?>

<div class="sidebar d-flex flex-column shadow">
    <div class="brand">
        <img src="../../asset/logo.png" alt="EVORIA" style="height: 90px; width 150px: auto;">
    </div>
    
    <div class="text-secondary small px-3 mt-3 mb-1" style="font-size: 0.65rem; letter-spacing: 1px;">MENU UTAMA</div>
    <nav class="nav flex-column">
        <!-- DASHBOARD -->
        <a class="nav-link <?= ($page == 'dashboard') ? 'active' : '' ?>" 
           href="dashboard.php?page=dashboard">
            <i class="fa fa-gauge-high me-2"></i> Dashboard
        </a>
        <?php if ($isStaffPembuat): ?>
            <div class="menu-label">Proposal Acara</div>
            <a class="nav-link <?= ($page == 'proposal' || $page == 'proposal_create' || $page == 'proposal_detail') ? 'active' : '' ?>" 
               href="dashboard.php?page=proposal">
                <i class="fa fa-file-alt me-2"></i> Proposal Acara
                <?php if ($notifAcaraSaya > 0): ?>
                    <span class="badge bg-danger rounded-pill ms-1" style="font-size: 8px;"><?= $notifAcaraSaya ?></span>
                <?php endif; ?>
            </a>
            <div class="menu-label">Pendaftaran</div>
            <a class="nav-link <?= ($page == 'daftar_panitia') ? 'active' : '' ?>" 
               href="dashboard.php?page=daftar_panitia">
                <i class="fa fa-users me-2"></i> Daftar Panitia
                <span class="badge bg-warning text-dark ms-1" style="font-size: 7px;">(Validasi)</span>
            </a>
            <a class="nav-link <?= ($page == 'daftar_peserta') ? 'active' : '' ?>" 
               href="dashboard.php?page=daftar_peserta">
                <i class="fa fa-ticket me-2"></i> Daftar Peserta
                <span class="badge bg-warning text-dark ms-1" style="font-size: 7px;">(Validasi)</span>
            </a>
        <?php else: ?>
            <div class="menu-label">Pendaftaran</div>
            <a class="nav-link <?= ($page == 'daftar_panitia') ? 'active' : '' ?>" 
               href="dashboard.php?page=daftar_panitia">
                <i class="fa fa-users me-2"></i> Daftar Panitia
            </a>
            <a class="nav-link <?= ($page == 'daftar_peserta') ? 'active' : '' ?>" 
               href="dashboard.php?page=daftar_peserta">
                <i class="fa fa-ticket me-2"></i> Daftar Peserta
            </a>
        <?php endif; ?>
    </nav>
    
    <div class="login-as">
        <small>Login sebagai:</small><br>
        <strong><?= $isStaffPembuat ? 'Staff' : 'Mahasiswa' ?></strong>
        <br>
        <small><?= $_SESSION['nama'] ?? '' ?></small>
        <?php if ($isStaffPembuat): ?>
            <br><small style="color: #a286f4;"><i class="fa fa-lock me-1"></i> Pembuat Acara</small>
        <?php endif; ?>
    </div>
</div>