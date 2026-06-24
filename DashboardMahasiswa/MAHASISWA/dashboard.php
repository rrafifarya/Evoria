<?php 
session_start(); 
include '../../Config/koneksi.php'; 

if($_SESSION['role'] != 'Mahasiswa' && $_SESSION['role'] != 'Staff') {
    header("Location: ../index.php");
    exit();
}

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
$idMhs = $_SESSION['id_user'] ?? 0;
$id_dep = $_SESSION['id_dep'] ?? 0;

// CEK APAKAH STAFF PEMBUAT ACARA
$cekStaff = mysqli_query($conn, "SELECT * FROM jabatan_hima WHERE idMhs = '$idMhs' AND is_active = 1 AND nama_jabatan IN ('Staff Departemen', 'Staff_Departemen')");
$isStaffPembuat = mysqli_num_rows($cekStaff) > 0;

// $cekPanitia = mysqli_query($conn, "SELECT id_acara FROM panitia WHERE idMhs = '$idMhs' AND status_seleksi = 'Diterima'");
// $isPanitia = mysqli_num_rows($cekPanitia) > 0;

// AMBIL DATA JABATAN
$queryJabatan = mysqli_query($conn, "SELECT * FROM jabatan_hima WHERE idMhs = '$idMhs' AND is_active = 1");
$dataJabatan = mysqli_fetch_assoc($queryJabatan);
$jabatan = $dataJabatan['nama_jabatan'] ?? ($isStaffPembuat ? 'Staff' : 'Mahasiswa');
$periode = $dataJabatan['periode'] ?? '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>EVORIA | Dashboard Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            background-color: #f8f9fa; 
            display: flex;
            min-height: 100vh;
        }
        .main-content {
            width: 100%;
            display: flex;
            flex-direction: column;
        }
        .header-mhs {
            background: #2d1b4e;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
        }
        .content-area {
            padding: 30px;
            flex: 1;
        }
        .btn-purple { 
            background-color: #2d1b4e; 
            border: none; 
            color: white; 
        }
        .btn-purple:hover { 
            background-color: #3d2a63; 
            color: white; 
        }
        .bg-purple { 
            background-color: #a286f4; 
        }
        .stat-card {
            transition: transform 0.3s ease;
            cursor: pointer;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        .event-card {
            transition: all 0.3s ease;
            border: 1px solid rgba(0,0,0,0.05);
        }
        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border-color: #a286f4;
        }
        .menu-card {
            transition: all 0.3s ease;
            border: 1px solid rgba(0,0,0,0.05);
        }
        .menu-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
            border-color: #a286f4;
        }
        .menu-card .icon-box {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 15px;
        }
        .disabled-menu {
            opacity: 0.4;
            cursor: not-allowed;
            pointer-events: none;
        }
        .disabled-menu:hover {
            background: transparent !important;
            border-right: none !important;
        }
        .badge-disabled {
            font-size: 7px;
            padding: 2px 8px;
            background: #dc3545;
            color: white;
            border-radius: 10px;
            margin-left: 5px;
        }
        .menu-label-disabled {
            font-size: 9px;
            color: #dc3545;
            text-transform: uppercase;
            padding: 10px 18px 5px;
            letter-spacing: 0.5px;
            font-weight: 600;
            opacity: 0.6;
        }
        .badge-status {
            font-size: 10px;
            padding: 4px 12px;
        }
        .btn-outline-purple {
            background: transparent;
            border: 2px solid #2d1b4e;
            color: #2d1b4e;
        }
        .btn-outline-purple:hover {
            background: #2d1b4e;
            color: white;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="header-mhs">
            <h5 class="mb-0 fw-bold">
            </h5>
            <div>
                <span class="me-3">
                    <i class="fa fa-user me-1"></i> 
                    <?= $_SESSION['nama'] ?? 'Mahasiswa' ?>
                    <?php if ($isStaffPembuat): ?>
                        <span class="badge bg-info ms-2">Staff aselole</span>
                    <?php endif; ?>
                </span>
                <a href="../../logout.php" class="btn btn-danger btn-sm">Keluar</a>
            </div>
        </div>
        <div class="content-area">
            <?php 
                if ($page == 'dashboard') {
                    include 'dashboard_content.php';
                } 
                elseif ($page == 'proposal') {
                    if ($isStaffPembuat) { include '../../components/ProposalAcara/index.php'; } 
                    else { echo '<div class="alert alert-danger">Akses ditolak! Hanya Staff pembuat acara.</div>'; }
                } elseif ($page == 'proposal_create') {
                    if ($isStaffPembuat) { include '../../components/ProposalAcara/create.php'; } 
                    else { echo '<div class="alert alert-danger">Akses ditolak!</div>'; }
                } elseif ($page == 'proposal_detail') {
                    include '../../components/ProposalAcara/detail.php';
                } elseif ($page == 'proposal_detail_penolakan') {
                    include '../../components/ProposalAcara/detail_penolakan.php';
                } elseif ($page == 'proposal_update') {
                    if ($isStaffPembuat) { include '../../components/ProposalAcara/update.php'; } 
                    else { echo '<div class="alert alert-danger">Akses ditolak!</div>'; }
                } elseif ($page == 'proposal_delete') {
                    if ($isStaffPembuat) { include '../../components/ProposalAcara/delete.php'; } 
                    else { echo '<div class="alert alert-danger">Akses ditolak!</div>'; }
                }
                // ========== DAFTAR PANITIA (MAHASISWA BIASA) ==========
                elseif ($page == 'daftar_panitia') {
                    include 'panitia/index.php';
                } elseif ($page == 'daftar_panitia_submit') {
                    include 'panitia/submit.php';
                }
                // ========== DAFTAR PESERTA (MAHASISWA BIASA) ==========
                elseif ($page == 'daftar_peserta') {
                    include 'peserta/index.php';
                } elseif ($page == 'daftar_peserta_submit') {
                    include 'peserta/submit.php';
                }
                // ========== FEEDBACK ==========
                elseif ($page == 'feedback') {
                    include 'feedback/index.php';
                } elseif ($page == 'feedback_create') {
                    include 'feedback/create.php';
                } elseif ($page == 'feedback_detail') {
                    include 'feedback/detail.php';
                } elseif ($page == 'feedback_sertifikat') {
                    include 'feedback/sertifikat.php';
                } elseif ($page == 'feedback_isi') {
                    include 'feedback/isi.php';
                } elseif ($page == 'feedback_peserta') {
                    include 'feedback/peserta.php';
                }
                else {
                    include 'dashboard_content.php';
                }
            ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>