<?php
include '../../Config/koneksi.php';
/** @var mysqli $conn **/

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Ketua Hima', 'Wakil Ketua Hima', 'Sekretaris Hima', 'Bendahara Hima'])) {
    header("Location: ../../../index.php");
    exit();
}

if (isset($_POST['update'])) {
    $id_jabatan = $_POST['id_jabatan'];
    $nama_jabatan = $_POST['nama_jabatan'] ?? '';
    $id_departemen = !empty($_POST['id_departemen']) ? $_POST['id_departemen'] : NULL;
    $periode = $_POST['periode'] ?? '';
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    $sql = "UPDATE jabatan_hima SET 
            nama_jabatan = '$nama_jabatan',
            id_departemen = " . ($id_departemen ? "'$id_departemen'" : "NULL") . ",
            periode = '$periode',
            is_active = '$is_active'
            WHERE id_jabatan_hima = '$id_jabatan'";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Jabatan berhasil diperbarui!'); window.location='dashboard.php?page=jabatan';</script>";
        exit();
    } else {
        echo "<script>alert('Gagal update: " . mysqli_error($conn) . "'); window.location='dashboard.php?page=jabatan_update&id=$id_jabatan';</script>";
        exit();
    }
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php?page=jabatan");
    exit();
}

$id_jabatan = mysqli_real_escape_string($conn, $_GET['id']);
$query = "SELECT j.*, m.nim, m.nama FROM jabatan_hima j 
          LEFT JOIN mahasiswa m ON j.idMhs = m.idMhs 
          WHERE j.id_jabatan_hima = '$id_jabatan'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    echo "<script>alert('Data tidak ditemukan!'); window.location='dashboard.php?page=jabatan';</script>";
    exit();
}

$data = mysqli_fetch_assoc($result);
$departemen = mysqli_query($conn, "SELECT * FROM departemen ORDER BY namaDepartemen ASC");
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold" style="color: #2d1b4e;">Edit Jabatan HIMA</h2>
        <p class="text-secondary small">Perbarui jabatan mahasiswa</p>
    </div>
    <a href="dashboard.php?page=jabatan" class="btn btn-secondary btn-sm">
        <i class="fa fa-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4">
                <form action="" method="POST">
                    <input type="hidden" name="id_jabatan" value="<?= $data['id_jabatan_hima'] ?>">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Mahasiswa</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($data['nim']) ?> - <?= htmlspecialchars($data['nama']) ?>" disabled>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Jabatan <span class="text-danger">*</span></label>
                        <select name="nama_jabatan" class="form-select" required>
                            <option value="Ketua Hima" <?= $data['nama_jabatan'] == 'Ketua Hima' ? 'selected' : '' ?>>Ketua HIMA</option>
                            <option value="Wakil Ketua Hima" <?= $data['nama_jabatan'] == 'Wakil Ketua Hima' ? 'selected' : '' ?>>Wakil Ketua HIMA</option>
                            <option value="Sekretaris Hima" <?= $data['nama_jabatan'] == 'Sekretaris Hima' ? 'selected' : '' ?>>Sekretaris HIMA</option>
                            <option value="Bendahara Hima" <?= $data['nama_jabatan'] == 'Bendahara Hima' ? 'selected' : '' ?>>Bendahara HIMA</option>
                            <option value="Kepala Departemen" <?= $data['nama_jabatan'] == 'Kadep' ? 'selected' : '' ?>>Kepala Departemen (Kadep)</option>
                            <option value="Staff Departemen" <?= $data['nama_jabatan'] == 'Staff Departemen' ? 'selected' : '' ?>>Staff Departemen</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Departemen (Opsional)</label>
                        <select name="id_departemen" class="form-select">
                            <option value="">-- Tanpa Departemen --</option>
                            <?php while($dep = mysqli_fetch_array($departemen)): ?>
                                <option value="<?= $dep['id_departemen'] ?>" <?= $data['id_departemen'] == $dep['id_departemen'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($dep['namaDepartemen']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Periode <span class="text-danger">*</span></label>
                        <input type="text" name="periode" class="form-control" value="<?= htmlspecialchars($data['periode']) ?>" required>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_active" class="form-check-input" id="is_active" <?= $data['is_active'] == 1 ? 'checked' : '' ?>>
                        <label class="form-check-label fw-bold small text-secondary" for="is_active">Aktif</label>
                    </div>
                    
                    <hr>
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="dashboard.php?page=jabatan" class="btn btn-light border rounded-pill px-4">Batal</a>
                        <button type="submit" name="update" class="btn btn-purple rounded-pill px-5">
                            <i class="fa fa-save me-2"></i> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>