<?php 
session_start(); 
include '../../Config/koneksi.php'; 
/** @var mysqli $conn **/

if($_SESSION['role'] != 'Kadep') {
    header("Location: ../index.php");
    exit();
}

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
$base_url = 'dashboard.php';
$status_filter = 'Pending Kadep';

$idMhs = $_SESSION['id_user'] ?? 0;
$queryJabatan = mysqli_query($conn, "SELECT * FROM jabatan_hima WHERE idMhs = '$idMhs' AND is_active = 1");
$dataJabatan = mysqli_fetch_assoc($queryJabatan);
$jabatan = $dataJabatan['nama_jabatan'] ?? '';
$periode = $dataJabatan['periode'] ?? '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>EVORIA | Dashboard Kadep </title>
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
        .header-kadep {
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
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="header-kadep">
            <h5 class="mb-0 fw-bold">
            </h5>
            <div>
                <span class="me-3">
                    <i class="fa fa-user me-1"></i> 
                    <?= $_SESSION['nama'] ?? 'Kadep' ?>
                    <span class="badge bg-danger ms-2">
                        <?= htmlspecialchars($jabatan) ?>
                    </span>
                    <?php if ($periode): ?>
                        <span class="badge bg-light text-dark ms-1"><?= htmlspecialchars($periode) ?></span>
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
                // APPROVAL ACARA
                elseif ($page == 'approval') {
                    include '../../components/Approval/index.php';
                } elseif ($page == 'approval_detail') {
                    include '../../components/Approval/detail.php';
                } elseif ($page == 'approval_action') {
                    include '../../components/Approval/action.php';
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