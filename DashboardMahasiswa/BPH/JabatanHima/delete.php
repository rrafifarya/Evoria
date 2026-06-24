<?php
include '../../Config/koneksi.php';
/** @var mysqli $conn **/

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Ketua Hima', 'Wakil Ketua Hima', 'Sekretaris Hima', 'Bendahara Hima'])) {
    header("Location: ../../../index.php");
    exit();
}

if (isset($_GET['id_jabatan_hima'])) {
    $id_jabatan_hima = $_GET['id_jabatan_hima'];
    
    $sql = "UPDATE jabatan_hima SET is_active = 0 WHERE id_departemen = '$id_jabatan_hima'";
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Jabatan berhasil dihapus!'); window.location='dashboard.php?page=jabatan';</script>";
    } else {
        echo "<script>alert('Gagal hapus: " . mysqli_error($conn) . "'); window.location='dashboard.php?page=jabatan';</script>";
    }
} else {
    header("Location: dashboard.php?page=jabatan");
}
exit();
?>