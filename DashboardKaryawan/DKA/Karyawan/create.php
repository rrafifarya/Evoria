<?php
include '../../Config/koneksi.php';
/** @var mysqli $conn **/

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'DKA') {
    header("Location: ../../../index.php");
    exit();
}

if (isset($_POST['simpan'])) {
    $nid            = $_POST['nid'] ?? '';
    $namaKrw        = $_POST['namaKrw'] ?? '';
    $emailKrw       = $_POST['emailKrw'] ?? '';
    $passwordKrw    = $_POST['passwordKrw'] ?? '';
    $jabatan        = $_POST['jabatan'] ?? '';

    // CEK DUPLIKAT NID
    $cek = mysqli_query($conn, "SELECT nid FROM karyawan WHERE nid = '$nid'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('NID $nid sudah terdaftar!'); window.location='dashboard.php?page=karyawan_create';</script>";
        exit();
    }

    // CEK DUPLIKAT EMAIL
    $cekEmail = mysqli_query($conn, "SELECT emailKrw FROM karyawan WHERE emailKrw = '$emailKrw'");
    if (mysqli_num_rows($cekEmail) > 0) {
        echo "<script>alert('Email $emailKrw sudah terdaftar!'); window.location='dashboard.php?page=karyawan_create';</script>";
        exit();
    }

    $stmt = mysqli_prepare($conn, "INSERT INTO karyawan (nid, namaKrw, emailKrw, passwordKrw, jabatan) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sssss", $nid, $namaKrw, $emailKrw, $passwordKrw, $jabatan);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Data karyawan berhasil disimpan!'); window.location='dashboard.php?page=karyawan';</script>";
        exit();
    } else {
        echo "<script>alert('Gagal menyimpan: " . mysqli_stmt_error($stmt) . "'); window.location='dashboard.php?page=karyawan_create';</script>";
        exit();
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold" style="color: #2d1b4e;">Tambah Karyawan</h2>
        <p class="text-secondary small">Isi form di bawah untuk menambahkan data karyawan baru</p>
    </div>
    <a href="dashboard.php?page=karyawan" class="btn btn-secondary btn-sm">
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
                            <label class="form-label fw-bold small text-secondary">NID <span class="text-danger">*</span></label>
                            <input type="text" name="nid" class="form-control" placeholder="Contoh: DKA001" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-secondary">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="namaKrw" class="form-control" placeholder="Nama lengkap" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-secondary">Email <span class="text-danger">*</span></label>
                            <input type="email" name="emailKrw" class="form-control" placeholder="Contoh: email@domain.com" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-secondary">Password <span class="text-danger">*</span></label>
                            <input type="password" name="passwordKrw" class="form-control" placeholder="Password" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold small text-secondary">Jabatan <span class="text-danger">*</span></label>
                            <select name="jabatan" class="form-select" required>
                                <option value="">-- Pilih Jabatan --</option>
                                <option value="DKA">DKA</option>
                                <option value="Kaprodi">Kepala Prodi</option>
                                <option value="Sekprodi">Sekretaris Prodi</option>
                            </select>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="dashboard.php?page=karyawan" class="btn btn-light border rounded-pill px-4">Batal</a>
                        <button type="submit" name="simpan" class="btn btn-purple rounded-pill px-5">
                            <i class="fa fa-save me-2"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>