<?php
$idMhs = $_SESSION['id_user'] ?? 0;
$id_dep = $_SESSION['id_dep'] ?? 0;

// CEK APAKAH STAFF PEMBUAT ACARA
$cekStaff = mysqli_query($conn, "SELECT * FROM jabatan_hima WHERE idMhs = '$idMhs' AND is_active = 1 AND nama_jabatan IN ('Staff Departemen', 'Staff_Departemen')");
$isStaffPembuat = mysqli_num_rows($cekStaff) > 0;

// STATISTIK - HANYA ACARA DISETUJUI
$totalAcara = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM transaksi_acara WHERE status = 'Disetujui'"));
$totalAcaraSaya = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM transaksi_acara WHERE idMhs = '$idMhs' AND status = 'Disetujui'"));
$totalPending = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM transaksi_acara WHERE status LIKE 'Pending%'"));
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="fw-bold" style="color: #2d1b4e;">Dashboard Mahasiswa</h1>
        <p class="text-secondary">Welcome To EVORIA, <?= $_SESSION['nama'] ?? 'Mahasiswa' ?>!</p>
    </div>
    <?php if ($isStaffPembuat): ?>
        <a href="dashboard.php?page=proposal_create" class="btn btn-purple rounded-pill px-4">
            <i class="fa fa-plus me-2"></i> Buat Acaras Baru
        </a>
    <?php endif; ?>
</div>

<!-- WELCOME -->
<div class="alert bg-white shadow-sm border-0 py-4 px-4 mt-0 rounded-4" style="border-left: 5px solid #a286f4 !important;">
    <div class="d-flex align-items-center">
        <div class="me-3 fs-3" style="color: #a286f4;">
            <i class="fa-solid fa-circle-user"></i>
        </div>
        <div>
            <h5 class="fw-bold mb-1" style="color: #2d1b4e;">
                Halo, <?= $_SESSION['nama'] ?? 'Mahasiswa' ?>!
            </h5>
            <div>
                <span class="badge <?= $isStaffPembuat ? 'bg-info' : 'bg-primary' ?> px-3 py-2 fs-6">
                    <?= $isStaffPembuat ? 'Staff' : 'Mahasiswa' ?>
                </span>
                <span class="text-secondary d-block mt-2">
                    <?php if ($isStaffPembuat): ?>
                        Anda adalah Staff (Pembuat Acara). Anda bisa membuat dan mengelola acara.
                    <?php else: ?>
                        Anda adalah Mahasiswa. Anda bisa mendaftar sebagai panitia atau peserta.
                    <?php endif; ?>
                </span>
            </div>
        </div>
    </div>
</div>

<!-- STATISTIK -->
<div class="row g-4 mt-2">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 stat-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="stat-icon me-3" style="background: #e8dff5; color: #2d1b4e;">
                        <i class="fa fa-calendar-check"></i>
                    </div>
                    <div>
                        <h6 class="text-secondary mb-0 small">Total Acara</h6>
                        <h3 class="fw-bold mb-0" style="color: #2d1b4e;"><?= $totalAcara ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php if ($isStaffPembuat): ?>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 stat-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="stat-icon me-3" style="background: #d4edda; color: #155724;">
                        <i class="fa fa-list"></i>
                    </div>
                    <div>
                        <h6 class="text-secondary mb-0 small">Acara Saya</h6>
                        <h3 class="fw-bold mb-0" style="color: #2d1b4e;"><?= $totalAcaraSaya ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- ========== DAFTAR ACARA (CARD MEMANJANG - 1 PER BARIS) ========== -->
<div class="mt-5">
    <h5 class="fw-bold mb-3" style="color: #2d1b4e;">
        <i class="fa fa-calendar-alt me-2" style="color: #a286f4;"></i> 
        Acara Tersedia
    </h5>

    <?php 
    // HANYA ACARA DENGAN STATUS DISETUJUI
    $queryAcara = "SELECT t.*, m.nama as pengaju, d.namaDepartemen AS nama_departemen, v.namaV
                   FROM transaksi_acara t 
                   LEFT JOIN mahasiswa m ON t.idMhs = m.idMhs
                   LEFT JOIN departemen d ON t.id_departemen = d.id_departemen
                   LEFT JOIN venue v ON t.id_venue = v.id_venue
                   WHERE t.status = 'Disetujui' 
                   ORDER BY t.tanggal_mulai ASC";
    $acara = mysqli_query($conn, $queryAcara);
    
    if (mysqli_num_rows($acara) > 0):
    ?>
    <div class="card p-3 shadow-sm border-0 rounded-4">
        <?php while($row = mysqli_fetch_array($acara)): 
            $isAcaraSaya = ($row['idMhs'] == $idMhs);
            $date = date('d M Y', strtotime($row['tanggal_mulai']));
            $isActive = (strtotime($row['tanggal_selesai']) >= time());
            $status = $row['status'];
        ?>
        <!-- ========== CARD MEMANJANG (1 PER BARIS) ========== -->
        <div class="d-flex justify-content-between align-items-center py-2 <?= (mysqli_num_rows($acara) > 1 && $row != mysqli_fetch_array($acara, MYSQLI_NUM)) ? 'border-bottom' : '' ?>">
            <!-- KIRI: INFO -->
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <!-- Badge Status -->
                <span class="badge bg-success rounded-pill px-3 py-2" style="min-width: 80px;">
                    ✅ Disetujui
                </span>
                
                <!-- Nama Acara -->
                <span class="fw-bold" style="color: #2d1b4e;"><?= htmlspecialchars($row['namaTrsAcara']) ?></span>
                
                <!-- Jenis Acara -->
                <span class="badge bg-light text-dark rounded-pill">
                    <i class="fa fa-tag me-1"></i> <?= htmlspecialchars($row['jenis_acara'] ?? 'Seminar') ?>
                </span>
                
                <!-- Tanggal -->
                <small class="text-muted">
                    <i class="fa fa-calendar-day me-1"></i>
                    <?= $date ?>
                </small>
                
                <!-- Venue -->
                <?php if (!empty($row['namaV'])): ?>
                <small class="text-muted">
                    <i class="fa fa-map-pin me-1"></i>
                    <?= htmlspecialchars($row['namaV']) ?>
                </small>
                <?php endif; ?>
                
                <!-- Label Acara Saya -->
                <?php if ($isAcaraSaya): ?>
                    <span class="badge bg-info">Acara Saya</span>
                <?php endif; ?>
            </div>
            
            <!-- KANAN: TOMBOL -->
            <div class="d-flex gap-2">
                <a href="dashboard.php?page=proposal_detail&id=<?= $row['id_trsAcara'] ?>" 
                   class="btn btn-outline-primary btn-sm">
                    <i class="fa fa-eye"></i> Detail
                </a>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
    <?php else: ?>
    <div class="card p-4 shadow-sm border-0 rounded-4 text-center">
        <div class="py-3">
            <i class="fa fa-calendar-alt d-block mb-2" style="font-size: 32px; color: #a286f4; opacity: 0.5;"></i>
            <p class="text-secondary mb-0">Belum ada acara yang disetujui.</p>
        </div>
    </div>
    <?php endif; ?>
</div>