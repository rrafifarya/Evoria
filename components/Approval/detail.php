<?php
/** @var mysqli $conn **/
include '../../Config/koneksi.php';

if (!isset($base_url)) {
    $base_url = 'dashboard.php';
}

if (!isset($_GET['id'])) {
    header("Location: " . $base_url . "?page=approval");
    exit();
}

$id_acara = mysqli_real_escape_string($conn, $_GET['id']);
$role = $_SESSION['role'] ?? '';
$id_user = $_SESSION['id_user'] ?? 0;

$query = "SELECT t.*, m.nama, m.nim, d.namaDepartemen AS nama_departemen,
          GROUP_CONCAT(v.namaV SEPARATOR ', ') AS semua_venue,
          GROUP_CONCAT(v.lokasi SEPARATOR ', ') AS semua_lokasi,
          GROUP_CONCAT(v.kapasitas SEPARATOR ', ') AS semua_kapasitas
          FROM transaksi_acara t 
          LEFT JOIN mahasiswa m ON t.idMhs = m.idMhs
          LEFT JOIN departemen d ON t.id_departemen = d.id_departemen
          LEFT JOIN detail_venue_acara dv ON t.id_trsAcara = dv.id_trsAcara
          LEFT JOIN venue v ON dv.id_venue = v.id_venue 
          WHERE t.id_trsAcara = '$id_acara'
          GROUP BY t.id_trsAcara";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    echo "<script>alert('Acara tidak ditemukan!'); window.location='" . $base_url . "?page=approval';</script>";
    exit();
}

$data = mysqli_fetch_assoc($result);

$canApprove = false;
$isKadep = ($role == 'Kadep' || $role == 'Kepala Departemen');
$isBPH = in_array($role, ['Ketua Hima', 'Wakil Ketua Hima', 'Sekretaris Hima', 'Bendahara Hima']);
$isAdmin = in_array($role, ['Admin', 'Sekprodi', 'Kaprodi']);
$isDKA = ($role == 'DKA');

if ($isKadep) {
    $queryKadep = mysqli_query($conn, "SELECT id_departemen FROM jabatan_hima WHERE idMhs = '$id_user' AND is_active = 1 AND (nama_jabatan = 'Kadep' OR nama_jabatan = 'Kepala Departemen')");
    $dataKadep = mysqli_fetch_assoc($queryKadep);
    $id_dep_kadep = $dataKadep['id_departemen'] ?? 0;
    if ($data['id_departemen'] == $id_dep_kadep && $data['status'] == 'Pending Kadep') {
        $canApprove = true;
    }
} 
elseif ($isBPH && $data['status'] == 'Pending BPH') {
    $canApprove = true;
} 
elseif ($isAdmin && $data['status'] == 'Pending Sekprodi/Kaprodi') {
    $canApprove = true;
} 
elseif ($isDKA && $data['status'] == 'Pending DKA') {
    $canApprove = true;
}

$historyQuery = "SELECT * FROM approval_acara WHERE id_acara = '$id_acara' ORDER BY urutan_tahap ASC";
$historyResult = mysqli_query($conn, $historyQuery);

$cekUrutan = mysqli_query($conn, "SELECT MAX(urutan_tahap) as max_urutan FROM approval_acara WHERE id_acara = '$id_acara'");
$urutanData = mysqli_fetch_assoc($cekUrutan);
$tahap_selanjutnya = ($urutanData['max_urutan'] ?? 0) + 1;
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold" style="color: #2d1b4e;">Detail Acara</h2>
        <p class="text-secondary small">Review detail acara</p>
    </div>
    <a href="<?= $base_url ?>?page=approval" class="btn btn-secondary btn-sm">
        <i class="fa fa-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h4 class="fw-bold" style="color: #2d1b4e;"><?= htmlspecialchars($data['namaTrsAcara']) ?></h4>
                    <span class="badge bg-warning text-dark rounded-pill px-3 py-2 fs-6">
                        <i class="fa fa-clock me-1"></i> <?= $data['status'] ?>
                    </span>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="bg-light p-3 rounded-3">
                            <small class="text-muted">Pengaju</small>
                            <p class="fw-bold mb-0"><?= htmlspecialchars($data['nama']) ?></p>
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
                            <small class="text-muted">Kuota Peserta</small>
                            <p class="fw-bold mb-0"><?= number_format($data['kuota_peserta']) ?> Orang</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- DESKRIPSI -->
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3" style="color: #2d1b4e;">
                    <i class="fa fa-align-left me-2" style="color: #a286f4;"></i> Deskripsi Acara
                </h5>
                <p class="text-secondary"><?= nl2br(htmlspecialchars($data['deskripsiTrsAcara'])) ?></p>
            </div>
        </div>
        
        <!-- VENUE -->
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3" style="color: #2d1b4e;">
                    <i class="fa fa-map-pin me-2" style="color: #a286f4;"></i> Venue & Lokasi
                </h5>
                <?php if (!empty($data['semua_venue'])): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Nama Venue</th>
                                    <th>Lokasi</th>
                                    <th class="text-center">Kapasitas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $venues = explode(', ', $data['semua_venue']);
                                $lokasis = explode(', ', $data['semua_lokasi']);
                                $kapasitass = explode(', ', $data['semua_kapasitas']);
                                $no = 1;
                                for ($i = 0; $i < count($venues); $i++): 
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><strong><?= htmlspecialchars($venues[$i] ?? '-') ?></strong></td>
                                    <td><?= htmlspecialchars($lokasis[$i] ?? '-') ?></td>
                                    <td class="text-center"><?= number_format($kapasitass[$i] ?? 0) ?> Orang</td>
                                </tr>
                                <?php endfor; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">Belum ada venue yang dipilih</p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- HISTORY APPROVAL -->
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3" style="color: #2d1b4e;">
                    <i class="fa fa-history me-2" style="color: #a286f4;"></i> Riwayat Approval
                    <span class="badge bg-secondary ms-2"><?= mysqli_num_rows($historyResult) ?> Tahap</span>
                </h5>
                
                <?php if (mysqli_num_rows($historyResult) > 0): ?>
                    <div class="timeline">
                        <?php while($history = mysqli_fetch_array($historyResult)): 
                            $badgeClass = ($history['status'] == 'Disetujui') ? 'bg-success' : 
                                         (($history['status'] == 'Ditolak') ? 'bg-danger' : 'bg-warning text-dark');
                            $icon = ($history['status'] == 'Disetujui') ? 'fa-check' : 
                                   (($history['status'] == 'Ditolak') ? 'fa-times' : 'fa-clock');
                            
                            $nama_approver = 'User';
                            $role_approver = '-';
                            if ($history['id_karyawan']) {
                                $qNama = mysqli_query($conn, "SELECT namaKrw, jabatan FROM karyawan WHERE id_karyawan = '{$history['id_karyawan']}'");
                                $n = mysqli_fetch_assoc($qNama);
                                $nama_approver = $n['namaKrw'] ?? 'Karyawan';
                                $role_approver = $n['jabatan'] ?? 'Karyawan';
                            } elseif ($history['idMhs']) {
                                $qNama = mysqli_query($conn, "SELECT m.nama, j.nama_jabatan 
                                                               FROM mahasiswa m 
                                                               LEFT JOIN jabatan_hima j ON m.idMhs = j.idMhs AND j.is_active = 1
                                                               WHERE m.idMhs = '{$history['idMhs']}'");
                                $n = mysqli_fetch_assoc($qNama);
                                $nama_approver = $n['nama'] ?? 'Mahasiswa';
                                $role_approver = $n['nama_jabatan'] ?? 'Mahasiswa';
                            }
                        ?>
                        <div class="timeline-item d-flex mb-3">
                            <div class="timeline-icon me-3">
                                <span class="badge <?= $badgeClass ?> rounded-circle p-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fa <?= $icon ?> text-white"></i>
                                </span>
                            </div>
                            <div class="timeline-content flex-grow-1">
                                <div class="bg-light p-3 rounded-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <strong><?= htmlspecialchars($nama_approver) ?></strong>
                                            <span class="badge bg-secondary ms-2"><?= htmlspecialchars($role_approver) ?></span>
                                            <span class="badge bg-secondary ms-2">Tahap <?= $history['urutan_tahap'] ?></span>
                                            <span class="badge <?= $badgeClass ?> ms-2">
                                                <?= $history['status'] ?>
                                            </span>
                                        </div>
                                        <small class="text-muted">
                                            <?= date('d M Y H:i', strtotime($history['tgl_keputusan'])) ?>
                                        </small>
                                    </div>
                                    <?php if (!empty($history['catatan'])): ?>
                                        <div class="mt-1">
                                            <span class="text-muted small">Catatan: </span>
                                            <span class="small"><?= htmlspecialchars($history['catatan']) ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center py-3">Belum ada riwayat approval</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php if ($canApprove): ?>
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3" style="color: #2d1b4e;">
                    <i class="fa fa-check-double me-2" style="color: #a286f4;"></i> Approval
                </h5>
                
                <form action="<?= $base_url ?>?page=approval_action" method="POST" id="formApproval">
                    <input type="hidden" name="id_acara" value="<?= $data['id_trsAcara'] ?>">
                    <input type="hidden" name="urutan_tahap" value="<?= $tahap_selanjutnya ?>">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Tahap Ke-<?= $tahap_selanjutnya ?></label>
                        <input type="text" class="form-control" value="Approval oleh <?= $_SESSION['nama'] ?? 'User' ?>" disabled>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">
                            Catatan 
                            <span class="text-danger">* (Wajib jika menolak)</span>
                        </label>
                        <textarea name="catatan" id="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan (wajib jika menolak)..."></textarea>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" name="action" value="approve" class="btn btn-success rounded-pill py-2 fw-bold">
                            <i class="fa fa-check me-2"></i> Setujui Acara
                        </button>
                        <button type="submit" name="action" value="reject" class="btn btn-danger rounded-pill py-2 fw-bold" onclick="return confirm('Yakin ingin menolak acara ini?');">
                            <i class="fa fa-times me-2"></i> Tolak Acara
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4 text-center">
                <i class="fa fa-info-circle d-block mb-2" style="font-size: 40px; color: #a286f4;"></i>
                <h5 class="fw-bold" style="color: #2d1b4e;">Tidak Dapat Approve</h5>
                <p class="text-secondary small">
                    <?php 
                    echo "Role: " . $role . "<br>";
                    echo "Status Acara: " . $data['status'] . "<br>";
                    echo "ID Dept Acara: " . ($data['id_departemen'] ?? 'NULL');
                    ?>
                </p>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
.timeline-item {
    position: relative;
}
.timeline-item:not(:last-child)::after {
    content: '';
    position: absolute;
    left: 19px;
    top: 40px;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}
.timeline-icon {
    flex-shrink: 0;
}
</style>

<script>
document.getElementById('formApproval')?.addEventListener('submit', function(e) {
    var catatan = document.getElementById('catatan').value.trim();
    var submitter = document.activeElement;
    var actionValue = submitter.value;
    
    if (actionValue === 'reject' && catatan === '') {
        e.preventDefault();
        alert('⚠️ Catatan wajib diisi ketika menolak acara!');
        document.getElementById('catatan').focus();
        return false;
    }
});
</script>