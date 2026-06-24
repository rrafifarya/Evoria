<?php
include '../../Config/koneksi.php';
/** @var mysqli $conn **/

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'DKA') {
    header("Location: ../../../index.php");
    exit();
}

if (isset($_POST['update'])) {
    $id_karyawan   = $_POST['id_karyawan'];
    $nid           = $_POST['nid'];
    $namaKrw       = $_POST['namaKrw'];
    $emailKrw      = $_POST['emailKrw'];
    $passwordKrw   = $_POST['passwordKrw'];
    $jabatan       = $_POST['jabatan'];

    $cek = mysqli_query($conn, "SELECT nid FROM karyawan WHERE nid = '$nid' AND id_karyawan != '$id_karyawan'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('NID $nid sudah digunakan oleh karyawan lain!'); window.location='dashboard.php?page=karyawan_update&id_karyawan=$id_karyawan';</script>";
        exit();
    }

    $cekEmail = mysqli_query($conn, "SELECT emailKrw FROM karyawan WHERE emailKrw = '$emailKrw' AND id_karyawan != '$id_karyawan'");
    if (mysqli_num_rows($cekEmail) > 0) {
        echo "<script>alert('Email $emailKrw sudah digunakan oleh karyawan lain!'); window.location='dashboard.php?page=karyawan_update&id_karyawan=$id_karyawan';</script>";
        exit();
    }

    $sql = "UPDATE karyawan SET 
            nid = '$nid',
            namaKrw = '$namaKrw',
            emailKrw = '$emailKrw',
            passwordKrw = '$passwordKrw',
            jabatan = '$jabatan'
            WHERE id_karyawan = '$id_karyawan'";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Data karyawan berhasil diperbarui!'); window.location='dashboard.php?page=karyawan';</script>";
        exit();
    } else {
        echo "<script>alert('Gagal update: " . mysqli_error($conn) . "'); window.location='dashboard.php?page=karyawan_update&id_karyawan=$id_karyawan';</script>";
        exit();
    }
}

// AMBIL DATA KARYAWAN
if (!isset($_GET['id_karyawan'])) {
    header("Location: dashboard.php?page=karyawan");
    exit();
}

$id_karyawan = mysqli_real_escape_string($conn, $_GET['id_karyawan']);
$query = "SELECT * FROM karyawan WHERE id_karyawan = '$id_karyawan'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    echo "<script>alert('Data tidak ditemukan!'); window.location='dashboard.php?page=karyawan';</script>";
    exit();
}

$data = mysqli_fetch_assoc($result);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold" style="color: #2d1b4e;">Edit Data Karyawan</h2>
        <p class="text-secondary small">Perbarui informasi karyawan</p>
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
                    <input type="hidden" name="id_karyawan" value="<?= $data['id_karyawan'] ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-secondary">NID <span class="text-danger">*</span></label>
                            <input type="text" name="nid" class="form-control" value="<?= htmlspecialchars($data['nid']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-secondary">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="namaKrw" class="form-control" value="<?= htmlspecialchars($data['namaKrw']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-secondary">Email <span class="text-danger">*</span></label>
                            <input type="email" name="emailKrw" class="form-control" value="<?= htmlspecialchars($data['emailKrw']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-secondary">Password <span class="text-danger">*</span></label>
                            <input type="password" name="passwordKrw" class="form-control" value="<?= htmlspecialchars($data['passwordKrw']) ?>" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold small text-secondary">Jabatan <span class="text-danger">*</span></label>
                            <select name="jabatan" class="form-select" required>
                                <option value="">-- Pilih Jabatan --</option>
                                <option value="DKA" <?= $data['jabatan'] == 'DKA' ? 'selected' : '' ?>>DKA</option>
                                <option value="Kaprodi" <?= $data['jabatan'] == 'Kaprodi' ? 'selected' : '' ?>>Kepala Prodi</option>
                                <option value="Sekprodi" <?= $data['jabatan'] == 'Sekprodi' ? 'selected' : '' ?>>Sekretaris Prodi</option>
                            </select>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="dashboard.php?page=karyawan" class="btn btn-light border rounded-pill px-4">Batal</a>
                        <button type="submit" name="update" class="btn btn-purple rounded-pill px-5">
                            <i class="fa fa-save me-2"></i> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>