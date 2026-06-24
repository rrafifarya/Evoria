<?php
include '../../Config/koneksi.php';
/** @var mysqli $conn **/

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'DKA') {
    header("Location: ../../../index.php");
    exit();
}
?>

<!-- CONTENT -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold" style="color: #2d1b4e;">Data Mahasiswa</h2>
        <p class="text-secondary small">Kelola data mahasiswa yang terdaftar di sistem EVORIA</p>
    </div>
    <a href="dashboard.php?page=mahasiswa_create" class="btn btn-purple rounded-pill px-4">
        <i class="fa fa-plus me-2"></i> Tambah Mahasiswa
    </a>
</div>

<div class="card p-4 shadow-sm border-0 rounded-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0" style="color: #2d1b4e;">
            <i class="fa fa-list me-2" style="color: #a286f4;"></i> Daftar Mahasiswa
        </h5>
        <span class="badge bg-purple text-white rounded-pill px-3 py-2">
            Total: <?= mysqli_num_rows(mysqli_query($conn, "SELECT * FROM mahasiswa")) ?> Data
        </span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th>NIM</th>
                    <th>Nama</th>
                    <th>Prodi / Angkatan</th>
                    <th class="text-center" style="width: 150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $m = mysqli_query($conn, "SELECT * FROM mahasiswa ORDER BY idMhs DESC"); 
                if (mysqli_num_rows($m) > 0):
                    while($rm = mysqli_fetch_array($m)): 
                ?>
                <tr>
                    <td class="fw-bold" style="color: #2d1b4e;"><?= htmlspecialchars($rm['nim']) ?></td>
                    <td><?= htmlspecialchars($rm['nama']) ?></td>
                    <td>
                        <span class="badge bg-light text-dark rounded-pill px-3"><?= htmlspecialchars($rm['prodi']) ?></span>
                        <span class="text-muted">-</span>
                        <span class="badge bg-secondary rounded-pill px-3"><?= htmlspecialchars($rm['angkatan']) ?></span>
                    </td>
                    <td class="text-center">
                        <a href="dashboard.php?page=mahasiswa_update&idMhs=<?= $rm['idMhs'] ?>" class="btn btn-outline-primary btn-sm rounded-3 me-1">
                            <i class="fa fa-edit"></i>
                        </a>
                        <a href="dashboard.php?page=mahasiswa_delete&idMhs=<?= $rm['idMhs'] ?>" class="btn btn-outline-danger btn-sm rounded-3" onclick="return confirm('Hapus data ini?');">
                            <i class="fa fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                <tr>
                    <td colspan="4" class="text-center text-muted py-4">
                        <i class="fa fa-inbox d-block fs-2 mb-2"></i>
                        Belum ada data mahasiswa.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>