<?php
session_start();
/** @var mysqli $conn **/
include '../../Config/koneksi.php';

$role = $_SESSION['role'] ?? '';
$allowedRoles = ['Kadep', 'Kepala Departemen', 'Ketua Hima', 'Wakil Ketua Hima', 'Sekretaris Hima', 'Bendahara Hima', 'Sekprodi', 'Kaprodi', 'Admin', 'DKA', 'Staff', 'Mahasiswa'];
if (!in_array($role, $allowedRoles)) {
    header("Location: ../../index.php");
    exit();
}

if (!isset($base_url)) {
    $base_url = 'dashboard.php';
}
$isKadep = ($role == 'Kadep' || $role == 'Kepala Departemen');
$isBPH = in_array($role, ['Ketua Hima', 'Wakil Ketua Hima', 'Sekretaris Hima', 'Bendahara Hima']);
$isAdmin = in_array($role, ['Admin', 'Sekprodi', 'Kaprodi']);
$isDKA = ($role == 'DKA');

if (isset($_POST['action']) && isset($_POST['id_acara'])) {
    $id_acara = $_POST['id_acara'];
    $action = $_POST['action'];
    $urutan_tahap = $_POST['urutan_tahap'] ?? 1;
    $catatan = mysqli_real_escape_string($conn, $_POST['catatan'] ?? '');
    
    if ($action == 'reject' && empty($catatan)) {
        echo "<script>alert('Catatan wajib diisi ketika menolak acara!'); window.history.back();</script>";
        exit();
    }
    $cek = mysqli_query($conn, "SELECT status FROM transaksi_acara WHERE id_trsAcara = '$id_acara'");
    $data = mysqli_fetch_assoc($cek);
    if (!$data || strpos($data['status'], 'Pending') === false) {
        echo "<script>alert('Acara sudah diproses!'); window.location='" . $base_url . "?page=approval';</script>";
        exit();
    }
    $id_user = $_SESSION['id_user'] ?? 0;
    $id_karyawan = NULL;
    $idMhs = NULL;
    if (in_array($role, ['Sekprodi', 'Kaprodi', 'Admin', 'DKA'])) {
        $id_karyawan = $id_user;
    } 
    elseif (in_array($role, ['Kadep', 'Kepala Departemen', 'Ketua Hima', 'Wakil Ketua Hima', 'Sekretaris Hima', 'Bendahara Hima', 'Staff', 'Mahasiswa'])) {
        $idMhs = $id_user;
    }

    if ($action == 'approve') {
        $status_approval = 'Disetujui';
        if ($isKadep) {
            $status_acara = 'Pending BPH';
            $pesan = 'Acara disetujui oleh Kadep! Diteruskan ke BPH.';
        } elseif ($isBPH) {
            $status_acara = 'Pending Sekprodi/Kaprodi';
            $pesan = 'Acara disetujui oleh BPH! Diteruskan ke Sekprodi/Kaprodi.';
        } elseif ($isAdmin) {
            $status_acara = 'Pending DKA';
            $pesan = 'Acara disetujui oleh Sekprodi/Kaprodi! Diteruskan ke DKA.';
        } elseif ($isDKA) {
            $status_acara = 'Disetujui'; 
            $pesan = 'Acara FINAL disetujui oleh DKA! Acara siap Dibuat.';
        }
    } else {
        $status_approval = 'Ditolak';
        
        if ($isKadep) {
            $status_acara = 'Revisi Kadep';
            $pesan = 'Acara ditolak oleh Kadep! Silakan revisi.';
        } elseif ($isBPH) {
            $status_acara = 'Revisi BPH';
            $pesan = 'Acara ditolak oleh BPH! Silakan revisi.';
        } elseif ($isAdmin) {
            $status_acara = 'Revisi Sekprodi/Kaprodi';
            $pesan = 'Acara ditolak oleh Sekprodi/Kaprodi! Silakan revisi.';
        } elseif ($isDKA) {
            $status_acara = 'Revisi DKA';
            $pesan = 'Acara ditolak oleh DKA! Silakan revisi.';
        }
    }
    if ($isKadep) {
        $queryKadep = mysqli_query($conn, "SELECT id_departemen FROM jabatan_hima WHERE idMhs = '$id_user' AND is_active = 1 AND (nama_jabatan = 'Kadep' OR nama_jabatan = 'Kepala Departemen')");
        $dataKadep = mysqli_fetch_assoc($queryKadep);
        $id_dep_kadep = $dataKadep['id_departemen'] ?? 0;
        
        $cekAcara = mysqli_query($conn, "SELECT id_departemen FROM transaksi_acara WHERE id_trsAcara = '$id_acara'");
        $dataAcara = mysqli_fetch_assoc($cekAcara);
        if ($dataAcara['id_departemen'] != $id_dep_kadep) {
            echo "<script>alert('Anda tidak berhak approve acara dari departemen lain!'); window.location='" . $base_url . "?page=approval';</script>";
            exit();
        }
    }
    
    $sql_approval = "INSERT INTO approval_acara (id_acara, id_karyawan, idMhs, urutan_tahap, catatan, status) 
                     VALUES (
                         '$id_acara', 
                         " . ($id_karyawan ? "'$id_karyawan'" : "NULL") . ", 
                         " . ($idMhs ? "'$idMhs'" : "NULL") . ", 
                         '$urutan_tahap', 
                         '$catatan', 
                         '$status_approval'
                     )";
    
    if (mysqli_query($conn, $sql_approval)) {
        $sql_update = "UPDATE transaksi_acara SET 
                       status = '$status_acara'
                       WHERE id_trsAcara = '$id_acara'";
        
        if (mysqli_query($conn, $sql_update)) {
            echo "<script>alert('$pesan'); window.location='" . $base_url . "?page=approval';</script>";
        } else {
            echo "<script>alert('Gagal update status: " . mysqli_error($conn) . "'); window.location='" . $base_url . "?page=approval_detail&id=$id_acara';</script>";
        }
    } else {
        echo "<script>alert('Gagal menyimpan approval: " . mysqli_error($conn) . "'); window.location='" . $base_url . "?page=approval_detail&id=$id_acara';</script>";
    }
} else {
    header("Location: " . $base_url . "?page=approval");
}
exit();
?>