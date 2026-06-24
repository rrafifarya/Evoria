<?php
/** @var mysqli $conn **/
include '../../Config/koneksi.php';

$idMhs = $_SESSION['id_user'] ?? 0;
$role = $_SESSION['role'] ?? '';
$isStaffPembuat = isset($_SESSION['is_staff_pembuat']) && $_SESSION['is_staff_pembuat'] === true;
$isKadep = ($role == 'Kadep' || $role == 'Kepala Departemen');
$isBPH = in_array($role, ['Ketua Hima', 'Wakil Ketua Hima', 'Sekretaris Hima', 'Bendahara Hima']);
$isAdmin = ($role == 'Sekprodi' || $role == 'Kaprodi' || $role == 'Admin');
$isDKA = ($role == 'DKA');
if ($isStaffPembuat) {
    $query = "SELECT t.*, v.namaV, d.namaDepartemen,
              (SELECT COUNT(*) FROM peserta WHERE id_acara = t.id_trsAcara) as total_peserta,
              (SELECT COUNT(*) FROM panitia WHERE id_acara = t.id_trsAcara AND status_seleksi = 'Diterima') as total_panitia
              FROM transaksi_acara t 
              LEFT JOIN venue v ON t.id_venue = v.id_venue
              LEFT JOIN departemen d ON t.id_departemen = d.id_departemen
              WHERE t.idMhs = '$idMhs'
              ORDER BY t.created_at DESC";
    $title = '📋 Proposal Acara Saya';
    $subtitle = 'Kelola proposal acara yang telah Anda buat';
    $btnText = 'Buat Proposal Baru';
    $showDaftar = false;
    $showKelola = false;
    $showStatus = true;
}
elseif ($isKadep || $isBPH || $isAdmin || $isDKA) {
    $status_filter = '';
    $label_status = '';
    if ($isKadep) { $status_filter = 'Pending Kadep'; $label_status = 'Kadep'; }
    elseif ($isBPH) { $status_filter = 'Pending BPH'; $label_status = 'BPH'; }
    elseif ($isAdmin) { $status_filter = 'Pending Sekprodi/Kaprodi'; $label_status = 'Sekprodi/Kaprodi'; }
    elseif ($isDKA) { $status_filter = 'Pending DKA'; $label_status = 'DKA'; }
    
    $filter_dep = '';
    if ($isKadep) {
        $qDep = mysqli_query($conn, "SELECT id_departemen FROM jabatan_hima WHERE idMhs = '$idMhs' AND is_active = 1 AND (nama_jabatan = 'Kadep' OR nama_jabatan = 'Kepala Departemen')");
        $dDep = mysqli_fetch_assoc($qDep);
        $id_dep_kadep = $dDep['id_departemen'] ?? 0;
        $filter_dep = " AND t.id_departemen = '$id_dep_kadep'";
    }
    
    $query = "SELECT t.*, v.namaV, d.namaDepartemen, m.nama as pengaju
              FROM transaksi_acara t 
              LEFT JOIN venue v ON t.id_venue = v.id_venue
              LEFT JOIN departemen d ON t.id_departemen = d.id_departemen
              LEFT JOIN mahasiswa m ON t.idMhs = m.idMhs
              WHERE t.status = '$status_filter' $filter_dep
              ORDER BY t.tanggal_mulai ASC";
    $title = '✅ Approval Acara (' . $label_status . ')';
    $subtitle = 'Setujui atau tolak pengajuan acara';
    $btnText = null;
    $showDaftar = false;
    $showKelola = true;
    $showStatus = true;
}
else {
    $query = "SELECT t.*, v.namaV, d.namaDepartemen, m.nama as pengaju,
              (SELECT COUNT(*) FROM peserta WHERE id_acara = t.id_trsAcara) as total_peserta,
              (SELECT COUNT(*) FROM panitia WHERE id_acara = t.id_trsAcara AND status_seleksi = 'Diterima') as total_panitia
              FROM transaksi_acara t 
              LEFT JOIN venue v ON t.id_venue = v.id_venue
              LEFT JOIN departemen d ON t.id_departemen = d.id_departemen
              LEFT JOIN mahasiswa m ON t.idMhs = m.idMhs
              WHERE t.status = 'Disetujui'
              ORDER BY t.tanggal_mulai ASC";
    $title = '🎯 Daftar Acara Tersedia';
    $subtitle = 'Daftar acara yang sudah disetujui dan tersedia untuk diikuti';
    $btnText = null;
    $showDaftar = true;
    $showKelola = false;
    $showStatus = true;
}

$acara = mysqli_query($conn, $query);
$totalAcara = mysqli_num_rows($acara);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold" style="color: #2d1b4e;"><?= $title ?></h2>
        <p class="text-secondary small"><?= $subtitle ?></p>
    </div>
    <?php if ($btnText): ?>
        <a href="<?= $base_url ?? 'dashboard.php' ?>?page=proposal_create" class="btn btn-purple rounded-pill px-4">
            <i class="fa fa-plus me-2"></i> <?= $btnText ?>
        </a>
    <?php endif; ?>
</div>

<?php if ($totalAcara > 0): ?>
    <div class="row g-4">
        <?php while($row = mysqli_fetch_array($acara)): 
            $status = $row['status'];
            $badgeClass = '';
            $badgeIcon = '';
            if($status == 'Disetujui') { $badgeClass = 'bg-success'; $badgeIcon = 'fa-check'; }
            elseif(strpos($status, 'Pending') !== false) { $badgeClass = 'bg-warning text-dark'; $badgeIcon = 'fa-clock'; }
            elseif($status == 'Ditolak') { $badgeClass = 'bg-danger'; $badgeIcon = 'fa-times'; }
            else { $badgeClass = 'bg-secondary'; $badgeIcon = 'fa-circle'; }
            
            $isActive = (strtotime($row['tanggal_selesai']) >= time());
            $isAcaraSaya = ($row['idMhs'] == $idMhs);
        ?>
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start">
                        <h5 class="fw-bold" style="color: #2d1b4e;"><?= htmlspecialchars($row['namaTrsAcara']) ?></h5>
                        <span class="badge <?= $badgeClass ?> rounded-pill">
                            <i class="fa <?= $badgeIcon ?> me-1"></i> <?= $status ?>
                        </span>
                    </div>
                    <div class="mt-1">
                        <span class="badge bg-light text-dark rounded-pill">
                            <i class="fa fa-tag me-1"></i> <?= htmlspecialchars($row['jenis_acara'] ?? 'Seminar') ?>
                        </span>
                        <?php if ($isAcaraSaya): ?>
                            <span class="badge bg-info rounded-pill">Acara Saya</span>
                        <?php endif; ?>
                    </div>
                    <p class="text-secondary small mt-2" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                        <?= htmlspecialchars(substr($row['deskripsiTrsAcara'], 0, 80)) ?>...
                    </p>
                    <div class="mt-2">
                        <div class="d-flex align-items-center mb-1">
                            <i class="fa fa-calendar-day me-2 text-muted" style="width: 18px;"></i>
                            <span class="small"><?= date('d M Y', strtotime($row['tanggal_mulai'])) ?></span>
                        </div>
                        <div class="d-flex align-items-center mb-1">
                            <i class="fa fa-map-pin me-2 text-muted" style="width: 18px;"></i>
                            <span class="small">Venue: <?= htmlspecialchars($row['namaV'] ?? '-') ?></span>
                        </div>
                        <?php if (!$isStaffPembuat && !$isKadep && !$isBPH && !$isAdmin && !$isDKA): ?>
                            <div class="d-flex align-items-center">
                                <i class="fa fa-user me-2 text-muted" style="width: 18px;"></i>
                                <span class="small">Pengaju: <strong><?= htmlspecialchars($row['pengaju'] ?? '-') ?></strong></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="mt-3 d-flex flex-wrap gap-2">
                        <a href="<?= $base_url ?? 'dashboard.php' ?>?page=approval_detail&id=<?= $row['id_trsAcara'] ?>" 
                           class="btn btn-outline-primary btn-sm flex-grow-1">
                            <i class="fa fa-eye me-1"></i> Detail
                        </a>
                        <?php if ($showKelola && strpos($status, 'Pending') !== false): ?>
                            <a href="<?= $base_url ?? 'dashboard.php' ?>?page=approval_detail&id=<?= $row['id_trsAcara'] ?>" 
                               class="btn btn-warning btn-sm">
                                <i class="fa fa-check-double me-1"></i> Kelola
                            </a>
                        <?php endif; ?>
                        <?php if ($showDaftar && $status == 'Disetujui' && $isActive): ?>
                            <a href="<?= $base_url ?? 'dashboard.php' ?>?page=daftar_panitia_submit&id_acara=<?= $row['id_trsAcara'] ?>" 
                               class="btn btn-success btn-sm"
                               onclick="return confirm('Yakin ingin mendaftar sebagai panitia?');">
                                <i class="fa fa-users me-1"></i> Panitia
                            </a>
                            <a href="<?= $base_url ?? 'dashboard.php' ?>?page=daftar_peserta_submit&id_acara=<?= $row['id_trsAcara'] ?>" 
                               class="btn btn-primary btn-sm"
                               onclick="return confirm('Yakin ingin mendaftar sebagai peserta?');">
                                <i class="fa fa-ticket me-1"></i> Peserta
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
<?php else: ?>
    <div class="card p-5 shadow-sm border-0 rounded-4 text-center">
        <div class="py-4">
            <i class="fa fa-calendar-alt d-block mb-3" style="font-size: 48px; color: #a286f4; opacity: 0.5;"></i>
            <h5 class="fw-bold" style="color: #2d1b4e;">
                <?php 
                if ($isStaffPembuat) echo '📝 Belum Ada Proposal';
                elseif ($isKadep || $isBPH || $isAdmin || $isDKA) echo '✅ Tidak Ada Acara Menunggu Approval';
                else echo '🎯 Belum Ada Acara Tersedia';
                ?>
            </h5>
            <p class="text-secondary">
                <?php 
                if ($isStaffPembuat) echo 'Anda belum membuat proposal acara. Silakan buat proposal baru.';
                elseif ($isKadep || $isBPH || $isAdmin || $isDKA) echo 'Semua acara sudah diproses. Tidak ada yang menunggu approval.';
                else echo 'Belum ada acara yang tersedia untuk didaftar. Tunggu acara baru ya!';
                ?>
            </p>
            <?php if ($isStaffPembuat): ?>
                <a href="<?= $base_url ?? 'dashboard.php' ?>?page=proposal_create" class="btn btn-purple rounded-pill px-4">
                    <i class="fa fa-plus me-2"></i> Buat Proposal Sekarang
                </a>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>