<?php
include '../../Config/koneksi.php';
/** @var mysqli $conn **/

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'DKA') {
    header("Location: ../../../index.php");
    exit();
}

if (isset($_GET['id_karyawan'])) {
    $id_karyawan = $_GET['id_karyawan'];
    
    if ($id_karyawan == $_SESSION['id_user']) {
        echo "<script>alert('Anda tidak dapat menghapus akun sendiri!'); window.location='dashboard.php?page=karyawan';</script>";
        exit();
    }

    $sql = "DELETE FROM karyawan WHERE id_karyawan = '$id_karyawan'";
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Data karyawan berhasil dihapus!'); window.location='dashboard.php?page=karyawan';</script>";
    } else {
        echo "<script>alert('Gagal hapus: " . mysqli_error($conn) . "'); window.location='dashboard.php?page=karyawan';</script>";
    }
} else {
    header("Location: dashboard.php?page=karyawan");
}
exit();
?>