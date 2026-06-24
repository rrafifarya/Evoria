<?php
include '../../../Config/koneksi.php';
/** @var mysqli $conn **/

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'DKA') {
    header("Location: ../../../index.php");
    exit();
}

if (isset($_GET['idMhs'])) {
    $idMhs = $_GET['idMhs'];
    
    $cek = mysqli_query($conn, "SELECT * FROM jabatan_hima WHERE idMhs = '$idMhs'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Tidak dapat dihapus! Mahasiswa memiliki jabatan.'); window.location='dashboard.php?page=mahasiswa';</script>";
        exit();
    }
    
    $sql = "DELETE FROM mahasiswa WHERE idMhs = '$idMhs'";
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Data berhasil dihapus!'); window.location='dashboard.php?page=mahasiswa';</script>";
    } else {
        echo "<script>alert('Gagal hapus: " . mysqli_error($conn) . "'); window.location='dashboard.php?page=mahasiswa';</script>";
    }
} else {
    header("Location: dashboard.php?page=mahasiswa");
}
exit();
?>