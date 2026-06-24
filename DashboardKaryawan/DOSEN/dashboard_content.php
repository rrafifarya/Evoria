<?php
/** @var mysqli $conn **/
$id_karyawan = $_SESSION['id_user'] ?? 0;
$nama_admin = $_SESSION['nama'] ?? 'Admin';


$totalPending = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM transaksi_acara WHERE status = 'Pending Sekprodi/Kaprodi'"));
$totalDisetujui = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM transaksi_acara WHERE status = 'Disetujui'"));
$totalDitolak = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM transaksi_acara WHERE status = 'Ditolak'"));
$totalAcara = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM transaksi_acara"));
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="fw-bold" style="color: #2d1b4e;">Dashboard Admin</h1>
        <p class="text-secondary">Welcome To EVORIA, <?= $nama_admin ?>!</p>
    </div>
</div>
<div class="alert bg-white shadow-sm border-0 py-4 px-4 mt-0 rounded-4" style="border-left: 5px solid #a286f4 !important;">
    <div class="d-flex align-items-center">
        <div class="me-3 fs-3" style="color: #a286f4;">
            <i class="fa-solid fa-circle-user"></i>
        </div>
        <div>
            <h5 class="fw-bold mb-1" style="color: #2d1b4e;">
                Halo, <?= $nama_admin ?>!
            </h5>
            <div>
                <span class="badge bg-info px-3 py-2 fs-6">Admin (Dosen)</span>
                <span class="text-secondary d-block mt-2">
                    Selamat datang di dashboard Admin. Anda bertugas sebagai Sekretaris Prodi / Kepala Prodi.
                    Silakan lakukan approval acara yang masuk.
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
                        <i class="fa fa-calendar"></i>
                    </div>
                    <div>
                        <h6 class="text-secondary mb-0 small">Total Acara</h6>
                        <h3 class="fw-bold mb-0" style="color: #2d1b4e;"><?= $totalAcara ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 stat-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="stat-icon me-3" style="background: #fff3cd; color: #856404;">
                        <i class="fa fa-clock"></i>
                    </div>
                    <div>
                        <h6 class="text-secondary mb-0 small">Pending Approval</h6>
                        <h3 class="fw-bold mb-0" style="color: #2d1b4e;"><?= $totalPending ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 stat-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="stat-icon me-3" style="background: #d4edda; color: #155724;">
                        <i class="fa fa-check-circle"></i>
                    </div>
                    <div>
                        <h6 class="text-secondary mb-0 small">Disetujui</h6>
                        <h3 class="fw-bold mb-0" style="color: #2d1b4e;"><?= $totalDisetujui ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 stat-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="stat-icon me-3" style="background: #f8d7da; color: #721c24;">
                        <i class="fa fa-times-circle"></i>
                    </div>
                    <div>
                        <h6 class="text-secondary mb-0 small">Ditolak</h6>
                        <h3 class="fw-bold mb-0" style="color: #2d1b4e;"><?= $totalDitolak ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MENU CEPAT -->
<div class="row g-4 mt-3">
    <div class="col-md-6 col-lg-4">
        <a href="dashboard.php?page=approval" class="text-decoration-none">
            <div class="card menu-card h-100 p-4 shadow-sm border-0 rounded-4">
                <div class="card-body d-flex flex-column">
                    <div class="icon-box" style="background: #fff3cd; color: #856404;">
                        <i class="fa fa-check-double"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-3">Approval Acara</h5>
                    <p class="text-secondary mb-4 flex-grow-1">Setujui atau tolak pengajuan acara dari mahasiswa.</p>
                    <div class="d-flex justify-content-between align-items-center mt-auto">
                        <span class="badge bg-danger rounded-pill px-3 py-2">
                            <?= $totalPending ?> Menunggu
                        </span>
                        <span class="btn btn-outline-secondary py-2 px-4 fw-bold" style="color: #2d1b4e; border-color: #2d1b4e;">
                            Kelola <i class="fa fa-arrow-right ms-2"></i>
                        </span>
                    </div>
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
                        <th>Pengaju</th>
                        <th>Departemen</th>
                        <th>Tanggal</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $acara = mysqli_query($conn, "SELECT t.*, m.nama, d.namaDepartemen 
                                                   FROM transaksi_acara t 
                                                   LEFT JOIN mahasiswa m ON t.idMhs = m.idMhs
                                                   LEFT JOIN departemen d ON t.id_departemen = d.id_departemen
                                                   ORDER BY t.id_trsAcara DESC LIMIT 5");
                    if (mysqli_num_rows($acara) > 0):
                        while($row = mysqli_fetch_array($acara)): 
                            $status = $row['status'];
                            $badge_class = '';
                            if(strpos($status, 'Pending') !== false) $badge_class = 'bg-warning text-dark';
                            elseif($status == 'Disetujui') $badge_class = 'bg-success';
                            elseif($status == 'Ditolak') $badge_class = 'bg-danger';
                            else $badge_class = 'bg-secondary';
                    ?>
                    <tr>
                        <td class="fw-bold" style="color: #2d1b4e;"><?= htmlspecialchars($row['namaTrsAcara']) ?></td>
                        <td><?= htmlspecialchars($row['nama']) ?></td>
                        <td><?= htmlspecialchars($row['namaDepartemen'] ?? '-') ?></td>
                        <td>
                            <small>
                                <i class="fa fa-calendar-day me-1"></i>
                                <?= date('d M Y', strtotime($row['tanggal_mulai'])) ?>
                            </small>
                        </td>
                        <td class="text-center">
                            <span class="badge <?= $badge_class ?> px-3 py-2 rounded-pill">
                                <?= htmlspecialchars($status) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-3">
                            Belum ada acara.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>