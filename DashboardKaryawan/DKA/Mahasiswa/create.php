<?php
include '../../Config/koneksi.php';
/** @var mysqli $conn **/

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'DKA') {
    header("Location: ../../../index.php");
    exit();
}

if (isset($_POST['simpan'])) {
    $nim         = $_POST['nim'] ?? '';
    $nama        = $_POST['nama'] ?? '';
    $prodi       = $_POST['prodi'] ?? '';
    $angkatan    = $_POST['angkatan'] ?? '';
    $passwordMhs = $_POST['password'] ?? '';

    $cek = mysqli_query($conn, "SELECT nim FROM mahasiswa WHERE nim = '$nim'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('NIM $nim sudah terdaftar!'); window.location='dashboard.php?page=mahasiswa_create';</script>";
        exit();
    }

    $stmt = mysqli_prepare($conn, "INSERT INTO mahasiswa (nim, nama, prodi, angkatan, passwordMhs) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sssss", $nim, $nama, $prodi, $angkatan, $passwordMhs);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Data berhasil disimpan!'); window.location='dashboard.php?page=mahasiswa';</script>";
        exit();
    } else {
        echo "<script>alert('Gagal menyimpan: " . mysqli_stmt_error($stmt) . "'); window.location='dashboard.php?page=mahasiswa_create';</script>";
        exit();
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold" style="color: #2d1b4e;">Tambah Mahasiswa Baru</h2>
        <p class="text-secondary small">Isi form di bawah untuk menambahkan data mahasiswa</p>
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
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-secondary">NIM</label>
                            <input type="text" name="nim" class="form-control" placeholder="Contoh: 032024001" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-secondary">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" placeholder="Nama lengkap" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-secondary">Program Studi</label>
                            <input type="text" name="prodi" class="form-control" placeholder="Contoh: TRPL" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-secondary">Angkatan</label>
                            <input type="text" name="angkatan" class="form-control" placeholder="Contoh: 2024" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold small text-secondary">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Password awal" required>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="dashboard.php?page=mahasiswa" class="btn btn-light border rounded-pill px-4">Batal</a>
                        <button type="submit" name="simpan" class="btn btn-purple rounded-pill px-5">
                            <i class="fa fa-save me-2"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>