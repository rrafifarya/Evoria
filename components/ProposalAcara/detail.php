<?php
/** @var mysqli $conn **/
include '../../Config/koneksi.php';

if (!isset($base_url)) {
    $base_url = 'dashboard.php';
}

if (!isset($_GET['id'])) {
    header("Location: " . $base_url . "?page=proposal");
    exit();
}

$id_acara = mysqli_real_escape_string($conn, $_GET['id']);
$idMhs = $_SESSION['id_user'] ?? 0;
$role = $_SESSION['role'] ?? '';
$isStaffPembuat = isset($_SESSION['is_staff_pembuat']) && $_SESSION['is_staff_pembuat'] === true;
$isKadep = ($role == 'Kadep' || $role == 'Kepala Departemen');
$isBPH = in_array($role, ['Ketua Hima', 'Wakil Ketua Hima', 'Sekretaris Hima', 'Bendahara Hima']);
$isAdmin = ($role == 'Sekprodi' || $role == 'Kaprodi' || $role == 'Admin');
$isDKA = ($role == 'DKA');
$isApproval = ($isKadep || $isBPH || $isAdmin || $isDKA);
$query = "SELECT t.*, m.nama as pengaju, m.nim, d.namaDepartemen AS nama_departemen, v.namaV, v.lokasi, v.kapasitas
          FROM transaksi_acara t 
          LEFT JOIN mahasiswa m ON t.idMhs = m.idMhs
          LEFT JOIN departemen d ON t.id_departemen = d.id_departemen
          LEFT JOIN venue v ON t.id_venue = v.id_venue
          WHERE t.id_trsAcara = '$id_acara'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    echo "<script>alert('Acara tidak ditemukan!'); window.location='" . $base_url . "?page=proposal';</script>";
    exit();
}

$data = mysqli_fetch_assoc($result);
$status = $data['status'];
$badgeClass = '';
$badgeIcon = '';
if($status == 'Disetujui') { $badgeClass = 'bg-success'; $badgeIcon = 'fa-check'; }
elseif(strpos($status, 'Pending') !== false) { $badgeClass = 'bg-warning text-dark'; $badgeIcon = 'fa-clock'; }
elseif($status == 'Ditolak') { $badgeClass = 'bg-danger'; $badgeIcon = 'fa-times'; }
elseif(strpos($status, 'Revisi') !== false) { $badgeClass = 'bg-info'; $badgeIcon = 'fa-edit'; }
else { $badgeClass = 'bg-secondary'; $badgeIcon = 'fa-circle'; }

$isActive = (strtotime($data['tanggal_selesai']) >= time());
$isAcaraSaya = ($data['idMhs'] == $idMhs);
$sudahPanitia = false;
$sudahPeserta = false;
$catatanPenolakan = '';
if ($status == 'Ditolak' || strpos($status, 'Revisi') !== false) {
    $qCatatan = mysqli_query($conn, "SELECT catatan FROM approval_acara WHERE id_acara = '$id_acara' AND status IN ('Ditolak', 'Revisi') ORDER BY urutan_tahap DESC LIMIT 1");
    $dataCatatan = mysqli_fetch_assoc($qCatatan);
    $catatanPenolakan = $dataCatatan['catatan'] ?? '';
}
$canEditDelete = ($isStaffPembuat && $isAcaraSaya && (strpos($status, 'Pending') !== false || $status == 'Draft' || strpos($status, 'Revisi') !== false));
$canDaftar = (!$isStaffPembuat && $status == 'Disetujui' && $isActive);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold" style="color: #2d1b4e;">Detail Acara</h2>
        <p class="text-secondary small">Informasi lengkap tentang acara</p>
    </div>
    <a href="javascript:history.back()" class="btn btn-secondary btn-sm">
        <i class="fa fa-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <h3 class="fw-bold" style="color: #2d1b4e;"><?= htmlspecialchars($data['namaTrsAcara']) ?></h3>
                    <span class="badge <?= $badgeClass ?> rounded-pill px-3 py-2">
                        <i class="fa <?= $badgeIcon ?> me-1"></i> <?= $status ?>
                    </span>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="bg-light p-3 rounded-3">
                            <small class="text-muted">Pengaju</small>
                            <p class="fw-bold mb-0"><?= htmlspecialchars($data['pengaju']) ?></p>
                            <small class="text-muted">NIM: <?= htmlspecialchars($data['nim']) ?></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="bg-light p-3 rounded-3">
                            <small class="text-muted">Departemen</small>
                            <p class="fw-bold mb-0"><?= htmlspecialchars($data['nama_departemen'] ?? '-') ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="bg-light p-3 rounded-3">
                            <small class="text-muted">Jenis Acara</small>
                            <p class="fw-bold mb-0"><?= htmlspecialchars($data['jenis_acara']) ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="bg-light p-3 rounded-3">
                            <small class="text-muted">Kuota Peserta</small>
                            <p class="fw-bold mb-0"><?= number_format($data['kuota_peserta']) ?> Orang</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="bg-light p-3 rounded-3">
                            <small class="text-muted">Tanggal Pelaksanaan</small>
                            <p class="fw-bold mb-0">
                                <?= date('d F Y', strtotime($data['tanggal_mulai'])) ?> 
                                <span class="text-muted">s/d</span> 
                                <?= date('d F Y', strtotime($data['tanggal_selesai'])) ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="bg-light p-3 rounded-3">
                            <small class="text-muted">Venue</small>
                            <p class="fw-bold mb-0"><?= htmlspecialchars($data['namaV'] ?? '-') ?></p>
                            <small class="text-muted">Lokasi: <?= htmlspecialchars($data['lokasi'] ?? '-') ?></small> <br>
                            <small class="text-muted">Kapasitas: <?= number_format($data['kapasitas'] ?? 0) ?> Orang</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3" style="color: #2d1b4e;">
                    <i class="fa fa-align-left me-2" style="color: #a286f4;"></i> Deskripsi Acara
                </h5>
                <p class="text-secondary"><?= nl2br(htmlspecialchars($data['deskripsiTrsAcara'])) ?></p>
            </div>
        </div>
        <?php if (!empty($catatanPenolakan)): ?>
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3" style="color: #dc3545;">
                        <i class="fa fa-times-circle me-2"></i> Catatan Penolakan
                    </h5>
                    <div class="p-3 bg-light rounded-3 border-start border-3 border-danger">
                        <p class="mb-0"><?= nl2br(htmlspecialchars($catatanPenolakan)) ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- POSTER -->
        <?php if ($data['poster_url'] && $data['poster_url'] != 'default.jpg'): ?>
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3" style="color: #2d1b4e;">
                    <i class="fa fa-image me-2" style="color: #a286f4;"></i> Poster
                </h5>
                <img src="<?= htmlspecialchars($data['poster_url']) ?>" alt="Poster" class="img-fluid rounded-3" style="max-height: 300px;">
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3" style="color: #2d1b4e;">
                    <i class="fa fa-chart-bar me-2" style="color: #a286f4;"></i> Statistik
                </h5>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-secondary">Kuota</span>
                    <span class="fw-bold"><?= number_format($data['kuota_peserta']) ?></span>
                </div>
                <div class="d-flex justify-content-between py-2 border-top">
                    <span class="text-secondary">Status</span>
                    <span class="badge <?= $badgeClass ?>"><?= $status ?></span>
                </div>
            </div>
        </div>
        <?php if ($isApproval && strpos($status, 'Pending') !== false): ?>
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3" style="color: #2d1b4e;">
                        <i class="fa fa-check-double me-2" style="color: #a286f4;"></i> Approval
                    </h5>
                    <p class="text-secondary small">Lakukan approval untuk acara ini</p>
                    <a href="<?= $base_url ?? 'dashboard.php' ?>?page=approval_detail&id=<?= $id_acara ?>" 
                       class="btn btn-warning w-100 rounded-pill py-2">
                        <i class="fa fa-check-double me-2"></i> Kelola Approval
                    </a>
                </div>
            </div>
            
        <?php elseif ($canEditDelete): ?>
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3" style="color: #2d1b4e;">
                        <i class="fa fa-edit me-2" style="color: #a286f4;"></i> Kelola Proposal
                    </h5>
                    <div class="d-grid gap-2">
                        <?php if (strpos($status, 'Revisi') !== false): ?>
                            <a href="<?= $base_url ?? 'dashboard.php' ?>?page=proposal_update&id=<?= $id_acara ?>" 
                               class="btn btn-warning rounded-pill py-2">
                                <i class="fa fa-edit me-2"></i> Revisi Proposal
                            </a>
                        <?php else: ?>
                            <a href="<?= $base_url ?? 'dashboard.php' ?>?page=proposal_update&id=<?= $id_acara ?>" 
                               class="btn btn-warning rounded-pill py-2">
                                <i class="fa fa-edit me-2"></i> Edit Proposal
                            </a>
                        <?php endif; ?>
                        <a href="<?= $base_url ?? 'dashboard.php' ?>?page=proposal_delete&id=<?= $id_acara ?>" 
                           class="btn btn-danger rounded-pill py-2"
                           onclick="return confirm('Hapus proposal ini?');">
                            <i class="fa fa-trash me-2"></i> Hapus Proposal
                        </a>
                    </div>
                </div>
            </div>
            
        <?php elseif ($status == 'Disetujui' && !$isActive): ?>
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4 text-center">
                    <i class="fa fa-check-circle d-block mb-2" style="font-size: 40px; color: #28a745;"></i>
                    <h5 class="fw-bold" style="color: #2d1b4e;">Acara Telah Selesai</h5>
                    <p class="text-secondary small">Terima kasih telah berpartisipasi!</p>
                </div>
            </div>
            
        <?php elseif ($isStaffPembuat && $isAcaraSaya && $status == 'Disetujui'): ?>
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4 text-center">
                    <i class="fa fa-check-circle d-block mb-2" style="font-size: 40px; color: #28a745;"></i>
                    <h5 class="fw-bold" style="color: #2d1b4e;">✅ Acara Disetujui</h5>
                    <p class="text-secondary small">Proposal Anda sudah disetujui!</p>
                </div>
            </div>
            
        <?php elseif ($status == 'Ditolak'): ?>
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4 text-center">
                    <i class="fa fa-times-circle d-block mb-2" style="font-size: 40px; color: #dc3545;"></i>
                    <h5 class="fw-bold" style="color: #2d1b4e;">Acara Ditolak</h5>
                    <?php if (!empty($catatanPenolakan)): ?>
                        <p class="text-secondary small mt-2">Catatan: <?= nl2br(htmlspecialchars($catatanPenolakan)) ?></p>
                    <?php endif; ?>
                    <a href="<?= $base_url ?? 'dashboard.php' ?>?page=proposal_create" class="btn btn-purple rounded-pill mt-2">
                        <i class="fa fa-plus me-1"></i> Buat Acara Baru
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>