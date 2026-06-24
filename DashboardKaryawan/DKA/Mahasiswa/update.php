<?php
include '../../Config/koneksi.php';
/** @var mysqli $conn **/

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'DKA') {
    header("Location: ../../../index.php");
    exit();
}

if (isset($_POST['update'])) {
    $idMhs = $_POST['idMhs'];
    $nim = $_POST['nim'];
    $nama = $_POST['nama'];
    $prodi = $_POST['prodi'];
    $angkatan = $_POST['angkatan'];
    $passwordMhs = $_POST['passwordMhs'];

    $sql = "UPDATE mahasiswa SET 
            nim = '$nim', 
            nama = '$nama', 
            prodi = '$prodi', 
            angkatan = '$angkatan', 
            passwordMhs = '$passwordMhs' 
            WHERE idMhs = '$idMhs'";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Data berhasil diperbarui!'); window.location='dashboard.php?page=mahasiswa';</script>";
        exit();
    } else {
        echo "<script>alert('Gagal update: " . mysqli_error($conn) . "'); window.location='dashboard.php?page=mahasiswa_update&idMhs=$idMhs';</script>";
        exit();
    }
}

if (!isset($_GET['idMhs'])) {
    header("Location: dashboard.php?page=mahasiswa");
    exit();
}

$idMhs = mysqli_real_escape_string($conn, $_GET['idMhs']);
$query = "SELECT * FROM mahasiswa WHERE idMhs = '$idMhs'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    echo "<script>alert('Data tidak ditemukan!'); window.location='dashboard.php?page=mahasiswa';</script>";
    exit();
}

$data = mysqli_fetch_assoc($result);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold" style="color: #2d1b4e;">Edit Data Mahasiswa</h2>
        <p class="text-secondary small">Perbarui informasi mahasiswa</p>
    </div>
    <a href="dashboard.php?page=mahasiswa" class="btn btn-secondary btn-sm">
        <i class="fa fa-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4">
                <form action="" method="POST">
                    <input type="hidden" name="idMhs" value="<?= $data['idMhs'] ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-secondary">NIM</label>
                            <input type="text" name="nim" class="form-control" value="<?= htmlspecialchars($data['nim']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-secondary">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($data['nama']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-secondary">Program Studi</label>
                            <input type="text" name="prodi" class="form-control" value="<?= htmlspecialchars($data['prodi']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-secondary">Angkatan</label>
                            <input type="text" name="angkatan" class="form-control" value="<?= htmlspecialchars($data['angkatan']) ?>" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold small text-secondary">Password</label>
                            <input type="text" name="passwordMhs" class="form-control" value="<?= htmlspecialchars($data['passwordMhs']) ?>" required>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="dashboard.php?page=mahasiswa" class="btn btn-light border rounded-pill px-4">Batal</a>
                        <button type="submit" name="update" class="btn btn-purple rounded-pill px-5">
                            <i class="fa fa-save me-2"></i> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>