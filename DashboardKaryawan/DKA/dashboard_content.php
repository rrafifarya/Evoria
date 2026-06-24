<?php
/** @var mysqli $conn **/
$totalMahasiswa = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM mahasiswa"));
$totalVenue = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM venue WHERE deleted_at IS NULL"));
$totalKaryawan = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM karyawan"));
$totalPending = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM transaksi_acara WHERE status = 'Pending DKA'"));
$totalDisetujui = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM transaksi_acara WHERE status = 'Disetujui' OR status = 'Selesai'"));
?>

<div class="alert bg-white shadow-sm border-0 py-4 px-4 mt-0 rounded-4" style="border-left: 5px solid #a286f4 !important;">
    <div class="d-flex align-items-center">
        <div class="me-3 fs-3" style="color: #a286f4;">
            <i class="fa-solid fa-circle-user"></i>
        </div>
        <div>
            <h5 class="fw-bold mb-1" style="color: #2d1b4e;">Halo, <?= $_SESSION['nama'] ?? 'Admin DKA' ?>!</h5>
            <span class="text-secondary">Ayoo, Dukung Mahasiswa!</span>
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
                        <i class="fa fa-users" style="color: #2d1b4e; font-size: 20px;"></i>
                    </div>
                    <div>
                        <h6 class="text-secondary mb-0 small">Total Mahasiswa</h6>
                        <h3 class="fw-bold mb-0" style="color: #2d1b4e;"><?= $totalMahasiswa ?></h3>
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
                        <h6 class="text-secondary mb-0 small">Total Venue</h6>
                        <h3 class="fw-bold mb-0" style="color: #2d1b4e;"><?= $totalVenue ?></h3>
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
                        <i class="fa fa-user-tie" style="color: #004085; font-size: 20px;"></i>
                    </div>
                    <div>
                        <h6 class="text-secondary mb-0 small">Total Karyawan</h6>
                        <h3 class="fw-bold mb-0" style="color: #2d1b4e;"><?= $totalKaryawan ?></h3>
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
                        <i class="fa fa-clock" style="color: #856404; font-size: 20px;"></i>
                    </div>
                    <div>
                        <h6 class="text-secondary mb-0 small">Pending DKA</h6>
                        <h3 class="fw-bold mb-0" style="color: #2d1b4e;"><?= $totalPending ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MENU CEPAT -->
<div class="row g-4 mt-3">
    <div class="col-md-4">
        <a href="dashboard.php?page=mahasiswa" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-4 h-100 text-center p-4" style="transition: 0.3s; cursor: pointer;">
                <div class="card-body">
                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
                         style="width: 70px; height: 70px; background: #e8dff5;">
                        <i class="fa fa-users fa-2x" style="color: #2d1b4e;"></i>
                    </div>
                    <h5 class="fw-bold" style="color: #2d1b4e;">Master Mahasiswa</h5>
                    <p class="text-secondary small">Kelola data mahasiswa</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="dashboard.php?page=venue" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-4 h-100 text-center p-4" style="transition: 0.3s; cursor: pointer;">
                <div class="card-body">
                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
                         style="width: 70px; height: 70px; background: #d4edda;">
                        <i class="fa fa-building fa-2x" style="color: #155724;"></i>
                    </div>
                    <h5 class="fw-bold" style="color: #2d1b4e;">Master Venue</h5>
                    <p class="text-secondary small">Kelola data venue</p>
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
                    <p class="text-secondary small">Setujui atau tolak pengajuan acara</p>
                    <?php if ($totalPending > 0): ?>
                        <span class="badge bg-danger rounded-pill"><?= $totalPending ?> Menunggu</span>
                    <?php endif; ?>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="mt-5">
    <h5 class="fw-bold mb-3" style="color: #2d1b4e;">
        <i class="fa fa-calendar-alt me-2" style="color: #a286f4;"></i> 
        Acara Terbaru
    </h5>
    <div class="card p-4 shadow-sm border-0 rounded-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Nama Acara</th>
                        <th>Tanggal</th>
                        <th>Kuota</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $acara = mysqli_query($conn, "SELECT * FROM transaksi_acara ORDER BY id_trsAcara DESC LIMIT 5");
                    if (mysqli_num_rows($acara) > 0):
                        while($row = mysqli_fetch_array($acara)): 
                            $status = $row['status'];
                            $badge_class = '';
                            if($status == 'Pending Kadep' || $status == 'Pending BPH' || $status == 'Pending Sekprodi/Kaprodi' || $status == 'Pending DKA') {
                                $badge_class = 'bg-warning text-dark';
                            } elseif($status == 'Disetujui' || $status == 'Selesai') {
                                $badge_class = 'bg-success';
                            } elseif($status == 'Ditolak') {
                                $badge_class = 'bg-danger';
                            } else {
                                $badge_class = 'bg-secondary';
                            }
                    ?>
                    <tr>
                        <td class="fw-bold" style="color: #2d1b4e;"><?= htmlspecialchars($row['namaTrsAcara']) ?></td>
                        <td>
                            <small>
                                <i class="fa fa-calendar-day me-1"></i>
                                <?= date('d M Y', strtotime($row['tanggal_mulai'])) ?>
                            </small>
                        </td>
                        <td><?= number_format($row['kuota_peserta']) ?> Orang</td>
                        <td class="text-center">
                            <span class="badge <?= $badge_class ?> px-3 py-2 rounded-pill">
                                <?= htmlspecialchars($status) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted py-3">
                            Belum ada acara.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>