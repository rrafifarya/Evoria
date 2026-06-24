<?php
/** @var mysqli $conn **/
$idMhs = $_SESSION['id_user'] ?? 0;
$queryJabatan = mysqli_query($conn, "SELECT * FROM jabatan_hima WHERE idMhs = '$idMhs' AND is_active = 1");
$dataJabatan = mysqli_fetch_assoc($queryJabatan);
$jabatan = $dataJabatan['nama_jabatan'] ?? '';
$periode = $dataJabatan['periode'] ?? '';

$totalPending = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM transaksi_acara WHERE status = 'Pending BPH'"));
$totalDepartemen = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM departemen"));
$totalJabatan = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM jabatan_hima WHERE is_active = 1"));
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="fw-bold" style="color: #2d1b4e;">Dashboard BPH</h1>
        <p class="text-secondary">Welcome To EVORIA, <?= $_SESSION['nama'] ?? 'BPH' ?>!</p>
    </div>
</div>

<div class="alert bg-white shadow-sm border-0 py-4 px-4 mt-0 rounded-4" style="border-left: 5px solid #a286f4 !important;">
    <div class="d-flex align-items-center">
        <div class="me-3 fs-3" style="color: #a286f4;">
            <i class="fa-solid fa-circle-user"></i>
        </div>
        <div>
            <h5 class="fw-bold mb-1" style="color: #2d1b4e;">
                Halo, <?= $_SESSION['nama'] ?? 'BPH' ?>!
            </h5>
            <div>
                <?php 
                $badgeClass = 'bg-info';
                if($jabatan == 'Ketua Hima') $badgeClass = 'bg-danger';
                elseif($jabatan == 'Wakil Ketua Hima') $badgeClass = 'bg-warning text-dark';
                elseif($jabatan == 'Sekretaris Hima') $badgeClass = 'bg-success';
                elseif($jabatan == 'Bendahara Hima') $badgeClass = 'bg-primary';
                ?>
                <span class="badge <?= $badgeClass ?> px-3 py-2 fs-6">
                    <?= htmlspecialchars($jabatan) ?>
                </span>
                <?php if ($periode): ?>
                    <span class="badge bg-light text-dark ms-2">Periode <?= htmlspecialchars($periode) ?></span>
                <?php endif; ?>
                <span class="text-secondary d-block mt-2">
                    Selamat datang di dashboard BPH. Kelola master data dan approval acara di sini.
                </span>
            </div>
        </div>
    </div>
</div>

<!-- STATISTIK -->
<div class="row g-4 mt-2">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" 
                         style="width: 50px; height: 50px; background: #e8dff5;">
                        <i class="fa fa-calendar" style="color: #2d1b4e; font-size: 20px;"></i>
                    </div>
                    <div>
                        <h6 class="text-secondary mb-0 small">Pending BPH</h6>
                        <h3 class="fw-bold mb-0" style="color: #2d1b4e;"><?= $totalPending ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" 
                         style="width: 50px; height: 50px; background: #d4edda;">
                        <i class="fa fa-building" style="color: #155724; font-size: 20px;"></i>
                    </div>
                    <div>
                        <h6 class="text-secondary mb-0 small">Departemen</h6>
                        <h3 class="fw-bold mb-0" style="color: #2d1b4e;"><?= $totalDepartemen ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" 
                         style="width: 50px; height: 50px; background: #fff3cd;">
                        <i class="fa fa-handshake" style="color: #856404; font-size: 20px;"></i>
                    </div>
                    <div>
                        <h6 class="text-secondary mb-0 small">Sponsor</h6>
                        <h3 class="fw-bold mb-0" style="color: #2d1b4e;"><?= $totalSponsor ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" 
                         style="width: 50px; height: 50px; background: #cce5ff;">
                        <i class="fa fa-user-tag" style="color: #004085; font-size: 20px;"></i>
                    </div>
                    <div>
                        <h6 class="text-secondary mb-0 small">Jabatan Aktif</h6>
                        <h3 class="fw-bold mb-0" style="color: #2d1b4e;"><?= $totalJabatan ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MENU CEPAT -->
<div class="row g-4 mt-3">
    <div class="col-md-4">
        <a href="dashboard.php?page=departemen" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-4 h-100 text-center p-4" style="transition: 0.3s; cursor: pointer;">
                <div class="card-body">
                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
                         style="width: 70px; height: 70px; background: #e8dff5;">
                        <i class="fa fa-building fa-2x" style="color: #2d1b4e;"></i>
                    </div>
                    <h5 class="fw-bold" style="color: #2d1b4e;">Kelola Departemen</h5>
                    <p class="text-secondary small">Tambah/edit departemen</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="dashboard.php?page=sponsor" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-4 h-100 text-center p-4" style="transition: 0.3s; cursor: pointer;">
                <div class="card-body">
                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
                         style="width: 70px; height: 70px; background: #d4edda;">
                        <i class="fa fa-handshake fa-2x" style="color: #155724;"></i>
                    </div>
                    <h5 class="fw-bold" style="color: #2d1b4e;">Kelola Sponsor</h5>
                    <p class="text-secondary small">Tambah/edit sponsor</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="dashboard.php?page=approval" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-4 h-100 text-center p-4" style="transition: 0.3s; cursor: pointer;">
                <div class="card-body">
                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
                         style="width: 70px; height: 70px; background: #fff3cd;">
                        <i class="fa fa-check-double fa-2x" style="color: #856404;"></i>
                    </div>
                    <h5 class="fw-bold" style="color: #2d1b4e;">Approval Acara</h5>
                    <p class="text-secondary small">Setujui atau tolak acara</p>
                    <?php if ($totalPending > 0): ?>
                        <span class="badge bg-danger rounded-pill"><?= $totalPending ?> Menunggu</span>
                    <?php endif; ?>
                </div>
            </div>
        </a>
    </div>
</div>