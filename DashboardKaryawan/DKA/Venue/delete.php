<?php
include '../../Config/koneksi.php';
/** @var mysqli $conn **/

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'DKA') {
    header("Location: ../../../index.php");
    exit();
}

if (isset($_GET['id_venue'])) {
    $id_venue = $_GET['id_venue'];
    $cek = mysqli_query($conn, "SELECT * FROM detail_venue_acara WHERE id_venue = '$id_venue'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Tidak dapat dihapus! Venue sedang digunakan pada acara.'); window.location='dashboard.php?page=venue';</script>";
        exit();
    }
    $sql = "UPDATE venue SET deleted_at = NOW() WHERE id_venue = '$id_venue'";
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Data berhasil dihapus!'); window.location='dashboard.php?page=venue';</script>";
    } else {
        echo "<script>alert('Gagal hapus: " . mysqli_error($conn) . "'); window.location='dashboard.php?page=venue';</script>";
    }
} else {
    header("Location: dashboard.php?page=venue");
}
exit();
?>