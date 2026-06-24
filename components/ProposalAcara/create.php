<?php
include '../../Config/koneksi.php';
/** @var mysqli $conn **/

if (!isset($_SESSION['is_staff_pembuat']) || $_SESSION['is_staff_pembuat'] !== true) {
    echo '<div class="alert alert-danger">Akses ditolak! Hanya Staff pembuat acara.</div>';
    return;
}

$idMhs = $_SESSION['id_user'] ?? 0;
$id_departemen = $_SESSION['id_dep'] ?? 0;

if (isset($_POST['simpan'])) {
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
    
    $sql = "INSERT INTO transaksi_acara (id_departemen, idMhs, id_venue, namaTrsAcara, deskripsiTrsAcara, status, tanggal_mulai, tanggal_selesai, kuota_peserta, poster_url, jenis_acara) 
            VALUES ('$id_departemen', '$idMhs', '$id_venue', '$namaTrsAcara', '$deskripsiTrsAcara', 'Pending Kadep', '$tanggal_mulai', '$tanggal_selesai', '$kuota_peserta', '$poster_url', '$jenis_acara')";
    
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('✅ Acara berhasil diajukan! Menunggu approval Kadep.'); window.location='" . ($base_url ?? 'dashboard.php') . "?page=proposal';</script>";
        exit();
    } else {
        echo "<script>alert('Gagal menyimpan: " . mysqli_error($conn) . "');</script>";
    }
}

$venues = mysqli_query($conn, "SELECT * FROM venue WHERE deleted_at IS NULL ORDER BY namaV ASC");
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold" style="color: #2d1b4e;">Buat Acara</h2>
        <p class="text-secondary small">Isi form di bawah untuk mengajukan acara baru</p>
    </div>
    <a href="<?= $base_url ?? 'dashboard.php' ?>?page=proposal" class="btn btn-secondary btn-sm">
        <i class="fa fa-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4">
                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Nama Acara <span class="text-danger">*</span></label>
                        <input type="text" name="namaTrsAcara" class="form-control" placeholder="Masukkan nama acara" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Jenis Acara <span class="text-danger">*</span></label>
                        <select name="jenis_acara" class="form-select" required>
                            <option value="Seminar">Seminar</option>
                            <option value="Lomba">Lomba</option>
                            <option value="Perayaan">Perayaan</option>
                            <option value="Workshop">Workshop</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Deskripsi Acara <span class="text-danger">*</span></label>
                        <textarea name="deskripsiTrsAcara" class="form-control" rows="4" placeholder="Jelaskan detail acara" required></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-secondary">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_mulai" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-secondary">Tanggal Selesai <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_selesai" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-secondary">Kuota Peserta <span class="text-danger">*</span></label>
                            <input type="number" name="kuota_peserta" class="form-control" placeholder="Jumlah kuota" min="1" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-secondary">Venue <span class="text-danger">*</span></label>
                            <select name="id_venue" class="form-select" required>
                                <option value="">-- Pilih Venue --</option>
                                <?php while($v = mysqli_fetch_array($venues)): ?>
                                    <option value="<?= $v['id_venue'] ?>">
                                        <?= htmlspecialchars($v['namaV']) ?> (Kapasitas: <?= number_format($v['kapasitas']) ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Poster URL (Opsional)</label>
                        <input type="text" name="poster_url" class="form-control" placeholder="https://example.com/poster.jpg">
                    </div>
                    
                    <hr>
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="<?= $base_url ?? 'dashboard.php' ?>?page=proposal" class="btn btn-light border rounded-pill px-4">Batal</a>
                        <button type="submit" name="simpan" class="btn btn-purple rounded-pill px-5">
                            <i class="fa fa-paper-plane me-2"></i> Ajukan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>