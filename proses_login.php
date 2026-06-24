<?php
session_start();
include 'Config/koneksi.php';

$role_ui = $_POST['role'];
$user = mysqli_real_escape_string($conn, $_POST['username']);
$pass = $_POST['password'];

if ($role_ui == 'Mahasiswa') {
    $sql = "SELECT m.*, j.nama_jabatan, j.id_departemen 
            FROM MAHASISWA m 
            LEFT JOIN JABATAN_HIMA j ON m.idMhs = j.idMhs AND j.is_active = 1
            WHERE m.nim = '$user' AND m.passwordMhs = '$pass'";
    $q = mysqli_query($conn, $sql);
    
    if(mysqli_num_rows($q) > 0) {
        $d = mysqli_fetch_array($q);
        $_SESSION['id_user'] = $d['idMhs'];
        $_SESSION['nama'] = $d['nama'];
        $_SESSION['id_dep'] = $d['id_departemen'];
        $_SESSION['jabatan'] = $d['nama_jabatan'];
        $_SESSION['is_staff_pembuat'] = false;
        
        $jabatan = $d['nama_jabatan'];
        if ($jabatan == 'Staff Departemen' || $jabatan == 'Staff_Departemen') {
            $_SESSION['role'] = 'Staff';
            $_SESSION['is_staff_pembuat'] = true;
            header("Location: DashboardMahasiswa/MAHASISWA/dashboard.php?page=dashboard");
            exit();
        } 
        elseif (in_array($jabatan, ['Ketua Hima', 'Wakil Ketua Hima', 'Sekretaris Hima', 'Bendahara Hima'])) {
            $_SESSION['role'] = $jabatan;
            $_SESSION['is_staff_pembuat'] = false;
            header("Location: DashboardMahasiswa/BPH/dashboard.php?page=dashboard");
            exit();
        } 
        elseif ($jabatan == 'Kepala Departemen') {
            $_SESSION['role'] = 'Kadep';
            $_SESSION['is_staff_pembuat'] = false;
            header("Location: DashboardMahasiswa/KADEP/dashboard.php?page=dashboard");
            exit();
        }
        else {
            $_SESSION['role'] = 'Mahasiswa';
            $_SESSION['is_staff_pembuat'] = false;
            header("Location: DashboardMahasiswa/MAHASISWA/dashboard.php?page=dashboard");
            exit();
        }
    } else { 
        echo "<script>alert('Gagal Login! Periksa NIM dan Password Anda.'); window.location='index.php';</script>"; 
        exit();
    }
} 
else {
    $sql = "SELECT * FROM karyawan WHERE nid = '$user' AND passwordKrw = '$pass'";
    $q = mysqli_query($conn, $sql);
    
    if(mysqli_num_rows($q) > 0) {
        $d = mysqli_fetch_array($q);
        $_SESSION['id_user'] = $d['id_karyawan'];
        $_SESSION['nama'] = $d['namaKrw'];
        $_SESSION['role'] = $d['jabatan'];
        
        if($d['jabatan'] == 'DKA') {
            header("Location: DashboardKaryawan/DKA/dashboard.php?page=dashboard");
            exit();
        } 
        elseif($d['jabatan'] == 'Sekprodi' || $d['jabatan'] == 'Kaprodi') {
            header("Location: DashboardKaryawan/DOSEN/dashboard.php?page=dashboard");
            exit();
        } 
        else {
            header("Location: index.php");
            exit();
        }
    } else { 
        echo "<script>alert('Gagal Login! Periksa NID dan Password Anda.'); window.location='index.php';</script>"; 
        exit();
    }
}
?>