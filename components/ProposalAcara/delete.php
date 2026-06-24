<?php
/** @var mysqli $conn **/
include '../../Config/koneksi.php';
if (!isset($_SESSION['is_staff_pembuat']) || $_SESSION['is_staff_pembuat'] !== true) {
    echo "<script>
        alert('Akses ditolak! Hanya Staff pembuat acara yang bisa menghapus proposal.');
        window.location='" . ($base_url ?? 'dashboard.php') . "?page=proposal';
    </script>";
    exit();
}

$idMhs = $_SESSION['id_user'] ?? 0;

if (isset($_GET['id'])) {
    $id_acara = mysqli_real_escape_string($conn, $_GET['id']);
    
    // CEK APAKAH ACARA MILIK STAFF INI
    $cek = mysqli_query($conn, "SELECT status FROM transaksi_acara WHERE id_trsAcara = '$id_acara' AND idMhs = '$idMhs'");
    $data = mysqli_fetch_assoc($cek);
    
    if (!$data) {
        echo "<script>
            alert('Acara tidak ditemukan atau bukan milik Anda!');
            window.location='" . ($base_url ?? 'dashboard.php') . "?page=proposal';
        </script>";
        exit();
    }
    $bolehHapus = false;
    $pesanError = '';
    
    $status = $data['status'];
    
    if ($status == 'Draft') {
        $bolehHapus = true;
    } elseif (strpos($status, 'Pending') !== false) {
        $bolehHapus = true;
    } elseif (strpos($status, 'Revisi') !== false) {
        $bolehHapus = true;
    } else {
        $pesanError = 'Acara tidak dapat dihapus karena sudah ' . $status;
    }
    
    if (!$bolehHapus) {
        echo "<script>
            alert('$pesanError!');
            window.location='" . ($base_url ?? 'dashboard.php') . "?page=proposal';
        </script>";
        exit();
    }
    
    $sql = "DELETE FROM transaksi_acara WHERE id_trsAcara = '$id_acara' AND idMhs = '$idMhs'";
    
    if (mysqli_query($conn, $sql)) {
        echo "<script>
            alert('✅ Proposal berhasil dihapus!');
            window.location='" . ($base_url ?? 'dashboard.php') . "?page=proposal';
        </script>";
    } else {
        echo "<script>
            alert('Gagal hapus: " . mysqli_error($conn) . "');
            window.location='" . ($base_url ?? 'dashboard.php') . "?page=proposal';
        </script>";
    }
} else {
    header("Location: " . ($base_url ?? 'dashboard.php') . "?page=proposal");
    exit();
}
?>