<?php
/** @var mysqli $conn **/
include '../../Config/koneksi.php';

$idMhs = $_SESSION['id_user'] ?? 0;
$role = $_SESSION['role'] ?? '';
$isStaffPembuat = isset($_SESSION['is_staff_pembuat']) && $_SESSION['is_staff_pembuat'] === true;
$isKadep = ($role == 'Kadep');
$isBPH = in_array($role, ['Ketua Hima', 'Wakil Ketua Hima', 'Sekretaris Hima', 'Bendahara Hima']);
$isAdmin = ($role == 'Sekprodi' || $role == 'Kaprodi');
$isDKA = ($role == 'DKA');

if ($isStaffPembuat) {
    $query = "SELECT t.*, v.namaV, d.namaDepartemen
              FROM transaksi_acara t 
              LEFT JOIN venue v ON t.id_venue = v.id_venue
              LEFT JOIN departemen d ON t.id_departemen = d.id_departemen
              WHERE t.idMhs = '$idMhs'
              ORDER BY t.created_at DESC";
    $title = 'Proposal Acara Saya';
    $btnText = 'Buat Proposal Baru';
    $showDaftar = false;
    $showKelola = false;
    
} elseif ($isKadep || $isBPH || $isAdmin || $isDKA) {
    $status_filter = '';
    if ($isKadep) $status_filter = 'Pending Kadep';
    elseif ($isBPH) $status_filter = 'Pending BPH';
    elseif ($isAdmin) $status_filter = 'Pending Sekprodi/Kaprodi';
    elseif ($isDKA) $status_filter = 'Pending DKA';
    
    $filter_dep = '';
    if ($isKadep) {
        $qDep = mysqli_query($conn, "SELECT id_departemen FROM jabatan_hima WHERE idMhs = '$idMhs' AND is_active = 1 AND nama_jabatan = 'Kadep'");
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
    $title = 'Approval Acara';
    $btnText = null;
    $showDaftar = false;
    $showKelola = true;
    
} else {
    $query = "SELECT t.*, v.namaV, d.namaDepartemen, m.nama as pengaju
              FROM transaksi_acara t 
              LEFT JOIN venue v ON t.id_venue = v.id_venue
              LEFT JOIN departemen d ON t.id_departemen = d.id_departemen
              LEFT JOIN mahasiswa m ON t.idMhs = m.idMhs
              WHERE t.status = 'Disetujui'
              ORDER BY t.tanggal_mulai ASC";
    $title = 'Daftar Acara Tersedia';
    $btnText = null;
    $showDaftar = false;  
    $showKelola = false;
}

$acara = mysqli_query($conn, $query);
$totalAcara = mysqli_num_rows($acara);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold" style="color: #2d1b4e;"><?= $title ?></h2>
        <p class="text-secondary small">
            <?php 
            if ($isStaffPembuat) echo 'Kelola proposal acara yang telah Anda buat';
            elseif ($isKadep || $isBPH || $isAdmin || $isDKA) echo 'Setujui atau tolak pengajuan acara';
            else echo 'Daftar acara yang sudah disetujui dan tersedia';
            ?>
        </p>
    </div>
    <?php if ($isStaffPembuat): ?>
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
            if($status == 'Disetujui') $badgeClass = 'bg-success';
            elseif(strpos($status, 'Pending') !== false) $badgeClass = 'bg-warning text-dark';
            elseif($status == 'Ditolak') $badgeClass = 'bg-danger';
            else $badgeClass = 'bg-secondary';
            
            $isActive = (strtotime($row['tanggal_selesai']) >= time());
        ?>
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start">
                        <h5 class="fw-bold" style="color: #2d1b4e;"><?= htmlspecialchars($row['namaTrsAcara']) ?></h5>
                        <span class="badge <?= $badgeClass ?> rounded-pill"><?= $status ?></span>
                    </div>
                    <div class="mt-1">
                        <span class="badge bg-light text-dark rounded-pill">
                            <i class="fa fa-tag me-1"></i> <?= htmlspecialchars($row['jenis_acara']) ?>
                        </span>
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
                        <a href="<?= $base_url ?? 'dashboard.php' ?>?page=proposal_detail&id=<?= $row['id_trsAcara'] ?>" 
                           class="btn btn-outline-primary btn-sm flex-grow-1">
                            <i class="fa fa-eye me-1"></i> Detail
                        </a>
                        <?php if ($showKelola): ?>
                            <a href="<?= $base_url ?? 'dashboard.php' ?>?page=approval_detail&id=<?= $row['id_trsAcara'] ?>" 
                               class="btn btn-warning btn-sm">
                                <i class="fa fa-check-double me-1"></i> Kelola
                            </a>
                        <?php endif; ?>
                        <?php if ($showDaftar && $status == 'Disetujui' && $isActive): ?>
                            <span class="badge bg-secondary d-flex align-items-center px-3">⏳ Belum Tersedia</span>
                        <?php endif; ?>
                    </div>
                    <?php if ($showKelola): ?>
                        <div class="mt-2">
                            <small class="text-muted">
                                <i class="fa fa-user me-1"></i> Pengaju: <?= htmlspecialchars($row['pengaju'] ?? '-') ?>
                            </small>
                        </div>
                    <?php endif; ?>
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
                if ($isStaffPembuat) echo 'Belum Ada Proposal';
                elseif ($isKadep || $isBPH || $isAdmin || $isDKA) echo 'Tidak Ada Acara Menunggu Approval';
                else echo 'Belum Ada Acara Tersedia';
                ?>
            </h5>
            <p class="text-secondary">
                <?php 
                if ($isStaffPembuat) echo 'Anda belum membuat proposal acara.';
                elseif ($isKadep || $isBPH || $isAdmin || $isDKA) echo 'Semua acara sudah diproses.';
                else echo 'Belum ada acara yang tersedia untuk didaftar.';
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