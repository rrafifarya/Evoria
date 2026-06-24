<?php
include '../../Config/koneksi.php';
/** @var mysqli $conn **/

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'DKA') {
    header("Location: ../../../index.php");
    exit();
}

if (isset($_POST['update'])) {
    $id_venue = $_POST['id_venue'];
    $namaV = $_POST['namaV'];
    $lokasi = $_POST['lokasi'];
    $kapasitas = $_POST['kapasitas'];

    $cek = mysqli_query($conn, "SELECT namaV FROM venue WHERE namaV = '$namaV' AND id_venue != '$id_venue' AND deleted_at IS NULL");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Nama venue \"$namaV\" sudah digunakan oleh venue lain!'); window.location='dashboard.php?page=venue_update&id_venue=$id_venue';</script>";
        exit();
    }

    $sql = "UPDATE venue SET 
            namaV = '$namaV', 
            lokasi = '$lokasi', 
            kapasitas = '$kapasitas' 
            WHERE id_venue = '$id_venue'";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Data berhasil diperbarui!'); window.location='dashboard.php?page=venue';</script>";
        exit();
    } else {
        echo "<script>alert('Gagal update: " . mysqli_error($conn) . "'); window.location='dashboard.php?page=venue_update&id_venue=$id_venue';</script>";
        exit();
    }
}

if (!isset($_GET['id_venue'])) {
    header("Location: dashboard.php?page=venue");
    exit();
}

$id_venue = mysqli_real_escape_string($conn, $_GET['id_venue']);
$query = "SELECT * FROM venue WHERE id_venue = '$id_venue'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    echo "<script>alert('Data tidak ditemukan!'); window.location='dashboard.php?page=venue';</script>";
    exit();
}

$data = mysqli_fetch_assoc($result);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold" style="color: #2d1b4e;">Edit Data Venue</h2>
        <p class="text-secondary small">Perbarui informasi venue/lokasi</p>
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
                    <input type="hidden" name="id_venue" value="<?= $data['id_venue'] ?>">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Nama Venue <span class="text-danger">*</span></label>
                        <input type="text" name="namaV" class="form-control" value="<?= htmlspecialchars($data['namaV']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Lokasi <span class="text-danger">*</span></label>
                        <input type="text" name="lokasi" class="form-control" value="<?= htmlspecialchars($data['lokasi']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Kapasitas (Orang) <span class="text-danger">*</span></label>
                        <input type="number" name="kapasitas" class="form-control" value="<?= $data['kapasitas'] ?>" min="1" required>
                    </div>
                    <hr>
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="dashboard.php?page=venue" class="btn btn-light border rounded-pill px-4">Batal</a>
                        <button type="submit" name="update" class="btn btn-purple rounded-pill px-5">
                            <i class="fa fa-save me-2"></i> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>