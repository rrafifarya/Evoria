<?php
include '../../Config/koneksi.php';
/** @var mysqli $conn **/

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Ketua Hima', 'Wakil Ketua Hima', 'Sekretaris Hima', 'Bendahara Hima'])) {
    header("Location: ../../../index.php");
    exit();
}

if (isset($_POST['simpan'])) {
    $idMhs = $_POST['idMhs'] ?? '';
    $nama_jabatan = $_POST['nama_jabatan'] ?? '';
    $id_departemen = !empty($_POST['id_departemen']) ? $_POST['id_departemen'] : NULL;
    $periode = $_POST['periode'] ?? '';

    $cek = mysqli_query($conn, "SELECT * FROM jabatan_hima WHERE idMhs = '$idMhs' AND is_active = 1");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Mahasiswa sudah memiliki jabatan aktif!'); window.location='dashboard.php?page=jabatan_create';</script>";
        exit();
    }

    $stmt = mysqli_prepare($conn, "INSERT INTO jabatan_hima (idMhs, id_departemen, nama_jabatan, periode, is_active) VALUES (?, ?, ?, ?, 1)");
    mysqli_stmt_bind_param($stmt, "isss", $idMhs, $id_departemen, $nama_jabatan, $periode);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Jabatan berhasil ditambahkan!'); window.location='dashboard.php?page=jabatan';</script>";
        exit();
    } else {
        echo "<script>alert('Gagal menyimpan: " . mysqli_stmt_error($stmt) . "'); window.location='dashboard.php?page=jabatan_create';</script>";
        exit();
    }
}

$mahasiswa = mysqli_query($conn, "SELECT m.* FROM mahasiswa m 
                                  LEFT JOIN jabatan_hima j ON m.idMhs = j.idMhs AND j.is_active = 1 
                                  WHERE j.idMhs IS NULL
                                  ORDER BY m.nama ASC");

$departemen = mysqli_query($conn, "SELECT * FROM departemen ORDER BY namaDepartemen ASC");
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold" style="color: #2d1b4e;">Tambah Jabatan HIMA</h2>
        <p class="text-secondary small">Tambahkan jabatan untuk mahasiswa</p>
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
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Pilih Mahasiswa <span class="text-danger">*</span></label>
                        <select name="idMhs" class="form-select" required>
                            <option value="">-- Pilih Mahasiswa --</option>
                            <?php while($mhs = mysqli_fetch_array($mahasiswa)): ?>
                                <option value="<?= $mhs['idMhs'] ?>">
                                    <?= htmlspecialchars($mhs['nim']) ?> - <?= htmlspecialchars($mhs['nama']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <?php if (mysqli_num_rows($mahasiswa) == 0): ?>
                            <small class="text-danger">Semua mahasiswa sudah memiliki jabatan.</small>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Jabatan <span class="text-danger">*</span></label>
                        <select name="nama_jabatan" class="form-select" required>
                            <option value="">-- Pilih Jabatan --</option>
                            <option value="Ketua Hima">Ketua HIMA</option>           
                            <option value="Wakil Ketua Hima">Wakil Ketua HIMA</option>   
                            <option value="Sekretaris Hima">Sekretaris HIMA</option>     
                            <option value="Bendahara Hima">Bendahara HIMA</option>       
                            <option value="Kepala Departemen">Kepala Departemen (Kadep)</option>
                            <option value="Staff Departemen">Staff Departemen</option>   
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Departemen (Opsional)</label>
                        <select name="id_departemen" class="form-select">
                            <option value="">-- Pilih Departemen --</option>
                            <?php while($dep = mysqli_fetch_array($departemen)): ?>
                                <option value="<?= $dep['id_departemen'] ?>">
                                    <?= htmlspecialchars($dep['namaDepartemen']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <small class="text-muted">Khusus untuk Kadep dan Staff Departemen</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Periode <span class="text-danger">*</span></label>
                        <input type="text" name="periode" class="form-control" placeholder="Contoh: 2024/2025" required>
                    </div>
                    
                    <hr>
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="dashboard.php?page=jabatan" class="btn btn-light border rounded-pill px-4">Batal</a>
                        <button type="submit" name="simpan" class="btn btn-purple rounded-pill px-5">
                            <i class="fa fa-save me-2"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>