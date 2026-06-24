<?php
/** @var mysqli $conn **/
include '../../Config/koneksi.php';
if (!isset($_SESSION['is_staff_pembuat']) || $_SESSION['is_staff_pembuat'] !== true) {
    echo '<div class="alert alert-danger">Akses ditolak! Hanya Staff pembuat acara.</div>';
    return;
}

$idMhs = $_SESSION['id_user'] ?? 0;

if (!isset($_GET['id'])) {
    header("Location: " . ($base_url ?? 'dashboard.php') . "?page=proposal");
    exit();
}

$id_acara = mysqli_real_escape_string($conn, $_GET['id']);
$cek = mysqli_query($conn, "SELECT * FROM transaksi_acara WHERE id_trsAcara = '$id_acara' AND idMhs = '$idMhs'");
if (mysqli_num_rows($cek) == 0) {
    echo "<script>alert('Acara tidak ditemukan atau bukan milik Anda!'); window.location='" . ($base_url ?? 'dashboard.php') . "?page=proposal';</script>";
    exit();
}

$data = mysqli_fetch_assoc($cek);
if (strpos($data['status'], 'Revisi') === false) {
    echo "<script>alert('Acara ini tidak dalam status revisi! Silakan buat acara baru.'); window.location='" . ($base_url ?? 'dashboard.php') . "?page=proposal';</script>";
    exit();
}

if (isset($_POST['update'])) {
    $namaTrsAcara = mysqli_real_escape_string($conn, $_POST['namaTrsAcara']);
    $deskripsiTrsAcara = mysqli_real_escape_string($conn, $_POST['deskripsiTrsAcara']);
    $jenis_acara = $_POST['jenis_acara'] ?? 'Seminar';
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $kuota_peserta = $_POST['kuota_peserta'];
    $id_venue = $_POST['id_venue'] ?? 0;
    $poster_url = !empty($_POST['poster_url']) ? mysqli_real_escape_string($conn, $_POST['poster_url']) : 'default.jpg';
    
    if (empty($namaTrsAcara) || empty($deskripsiTrsAcara) || empty($tanggal_mulai) || empty($tanggal_selesai) || empty($kuota_peserta) || empty($id_venue)) {
        echo "<script>alert('Semua field wajib diisi!'); window.history.back();</script>";
        exit();
    }
    
    if ($tanggal_mulai > $tanggal_selesai) {
        echo "<script>alert('Tanggal mulai tidak boleh lebih besar dari tanggal selesai!'); window.history.back();</script>";
        exit();
    }
    

    $cekVenue = mysqli_query($conn, "SELECT kapasitas FROM venue WHERE id_venue = '$id_venue' AND deleted_at IS NULL");
    $venue = mysqli_fetch_assoc($cekVenue);
    if ($kuota_peserta > $venue['kapasitas']) {
        echo "<script>alert('Kuota peserta ($kuota_peserta) melebihi kapasitas venue (" . $venue['kapasitas'] . ")!'); window.history.back();</script>";
        exit();
    }
    $status_baru = $data['status'];
    $status_baru = str_replace('Revisi ', 'Pending ', $status_baru);
    
    $sql = "UPDATE transaksi_acara SET 
            namaTrsAcara = '$namaTrsAcara',
            deskripsiTrsAcara = '$deskripsiTrsAcara',
            jenis_acara = '$jenis_acara',
            tanggal_mulai = '$tanggal_mulai',
            tanggal_selesai = '$tanggal_selesai',
            kuota_peserta = '$kuota_peserta',
            id_venue = '$id_venue',
            poster_url = '$poster_url',
            status = '$status_baru'
            WHERE id_trsAcara = '$id_acara' AND idMhs = '$idMhs'";
    
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('✅ Revisi berhasil dikirim! Menunggu approval kembali.'); window.location='" . ($base_url ?? 'dashboard.php') . "?page=proposal';</script>";
        exit();
    } else {
        echo "<script>alert('Gagal update: " . mysqli_error($conn) . "');</script>";
    }
}

$venues = mysqli_query($conn, "SELECT * FROM venue WHERE deleted_at IS NULL ORDER BY namaV ASC");
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold" style="color: #2d1b4e;">Revisi Acara</h2>
        <p class="text-secondary small">Perbaiki acara sesuai catatan revisi: <span class="badge bg-danger"><?= $data['status'] ?></span></p>
    </div>
    <a href="<?= $base_url ?? 'dashboard.php' ?>?page=proposal" class="btn btn-secondary btn-sm">
        <i class="fa fa-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4">
                <?php if (!empty($data['catatan_approval'])): ?>
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle me-2"></i>
                        <strong>Catatan Revisi:</strong><br>
                        <?= nl2br(htmlspecialchars($data['catatan_approval'])) ?>
                    </div>
                <?php endif; ?>
                
                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Nama Acara <span class="text-danger">*</span></label>
                        <input type="text" name="namaTrsAcara" class="form-control" value="<?= htmlspecialchars($data['namaTrsAcara']) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Jenis Acara <span class="text-danger">*</span></label>
                        <select name="jenis_acara" class="form-select" required>
                            <option value="Seminar" <?= $data['jenis_acara'] == 'Seminar' ? 'selected' : '' ?>>Seminar</option>
                            <option value="Lomba" <?= $data['jenis_acara'] == 'Lomba' ? 'selected' : '' ?>>Lomba</option>
                            <option value="Perayaan" <?= $data['jenis_acara'] == 'Perayaan' ? 'selected' : '' ?>>Perayaan</option>
                            <option value="Workshop" <?= $data['jenis_acara'] == 'Workshop' ? 'selected' : '' ?>>Workshop</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Deskripsi Acara <span class="text-danger">*</span></label>
                        <textarea name="deskripsiTrsAcara" class="form-control" rows="4" required><?= htmlspecialchars($data['deskripsiTrsAcara']) ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-secondary">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_mulai" class="form-control" value="<?= $data['tanggal_mulai'] ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-secondary">Tanggal Selesai <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_selesai" class="form-control" value="<?= $data['tanggal_selesai'] ?>" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-secondary">Kuota Peserta <span class="text-danger">*</span></label>
                            <input type="number" name="kuota_peserta" class="form-control" value="<?= $data['kuota_peserta'] ?>" min="1" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-secondary">Venue <span class="text-danger">*</span></label>
                            <select name="id_venue" class="form-select" required>
                                <option value="">-- Pilih Venue --</option>
                                <?php while($v = mysqli_fetch_array($venues)): ?>
                                    <option value="<?= $v['id_venue'] ?>" <?= $data['id_venue'] == $v['id_venue'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($v['namaV']) ?> (Kapasitas: <?= number_format($v['kapasitas']) ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Poster URL (Opsional)</label>
                        <input type="text" name="poster_url" class="form-control" value="<?= htmlspecialchars($data['poster_url']) ?>">
                    </div>
                    
                    <hr>
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="<?= $base_url ?? 'dashboard.php' ?>?page=proposal" class="btn btn-light border rounded-pill px-4">Batal</a>
                        <button type="submit" name="update" class="btn btn-success rounded-pill px-5">
                            <i class="fa fa-paper-plane me-2"></i> Kirim Revisi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>