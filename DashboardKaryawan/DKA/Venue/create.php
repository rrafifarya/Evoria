<?php
include '../../Config/koneksi.php';
/** @var mysqli $conn **/

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'DKA') {
    header("Location: ../../../index.php");
    exit();
}

if (isset($_POST['simpan'])) {
    $namaV = $_POST['namaV'] ?? '';
    $lokasi = $_POST['lokasi'] ?? '';
    $kapasitas = $_POST['kapasitas'] ?? '';

    // Cek duplikat nama venue
    $cek = mysqli_query($conn, "SELECT namaV FROM venue WHERE namaV = '$namaV' AND deleted_at IS NULL");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Nama venue \"$namaV\" sudah terdaftar!'); window.location='dashboard.php?page=venue_create';</script>";
        exit();
    }

    $stmt = mysqli_prepare($conn, "INSERT INTO venue (namaV, lokasi, kapasitas) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sss", $namaV, $lokasi, $kapasitas);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Data venue berhasil disimpan!'); window.location='dashboard.php?page=venue';</script>";
        exit();
    } else {
        echo "<script>alert('Gagal menyimpan: " . mysqli_stmt_error($stmt) . "'); window.location='dashboard.php?page=venue_create';</script>";
        exit();
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold" style="color: #2d1b4e;">Tambah Venue Baru</h2>
        <p class="text-secondary small">Isi form di bawah untuk menambahkan venue/lokasi baru</p>
    </div>
    <a href="dashboard.php?page=venue" class="btn btn-secondary btn-sm">
        <i class="fa fa-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4">
                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Nama Venue <span class="text-danger">*</span></label>
                        <input type="text" name="namaV" class="form-control" placeholder="Contoh: Auditorium Kampus" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Lokasi <span class="text-danger">*</span></label>
                        <input type="text" name="lokasi" class="form-control" placeholder="Contoh: Gedung Utama Lantai 3" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Kapasitas (Orang) <span class="text-danger">*</span></label>
                        <input type="number" name="kapasitas" class="form-control" placeholder="Contoh: 100" min="1" required>
                    </div>
                    <hr>
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="dashboard.php?page=venue" class="btn btn-light border rounded-pill px-4">Batal</a>
                        <button type="submit" name="simpan" class="btn btn-purple rounded-pill px-5">
                            <i class="fa fa-save me-2"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>